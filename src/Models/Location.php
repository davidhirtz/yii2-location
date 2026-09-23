<?php

declare(strict_types=1);

namespace Hirtz\Location\Models;

use Closure;
use Hirtz\Location\Models\Collections\TagCollection;
use Hirtz\Location\Models\Queries\LocationQuery;
use Hirtz\Location\Models\Queries\TagQuery;
use Hirtz\Location\Models\Types\LocationType;
use Hirtz\Location\Modules\ModuleTrait;
use Hirtz\Location\Validators\CoordinateValidator;
use Hirtz\Skeleton\Models\Breadcrumb;
use Hirtz\Skeleton\Behaviors\BlameableBehavior;
use Hirtz\Skeleton\Behaviors\TimestampBehavior;
use Hirtz\Skeleton\Behaviors\TrailBehavior;
use Hirtz\Skeleton\Db\ActiveRecord;
use Hirtz\Skeleton\Helpers\ArrayHelper;
use Hirtz\Skeleton\Helpers\CountryList;
use Hirtz\Skeleton\Models\Interfaces\CustomAttributeInterface;
use Hirtz\Skeleton\Models\Interfaces\DraftStatusAttributeInterface;
use Hirtz\Skeleton\Models\Interfaces\I18nAttributeInterface;
use Hirtz\Skeleton\Models\Interfaces\SearchableInterface;
use Hirtz\Skeleton\Models\Interfaces\TrailModelInterface;
use Hirtz\Skeleton\Models\Interfaces\TranslationInterface;
use Hirtz\Skeleton\Models\Interfaces\TypeAttributeInterface;
use Hirtz\Skeleton\Models\Traits\AdminModelTrait;
use Hirtz\Skeleton\Models\Traits\CustomAttributesTrait;
use Hirtz\Skeleton\Models\Traits\DraftStatusAttributeTrait;
use Hirtz\Skeleton\Models\Traits\I18nAttributesTrait;
use Hirtz\Skeleton\Models\Traits\SearchableTrait;
use Hirtz\Skeleton\Models\Traits\TrailModelTrait;
use Hirtz\Skeleton\Models\Traits\TranslationTrait;
use Hirtz\Skeleton\Models\Traits\TypeAttributeTrait;
use Hirtz\Skeleton\Models\Traits\UpdatedByUserTrait;
use Hirtz\Skeleton\Validators\DynamicRangeValidator;
use Hirtz\Skeleton\Web\User as WebUser;
use Override;
use Yii;
use davidhirtz\yii2\datetime\DateTime;
use davidhirtz\yii2\datetime\DateTimeBehavior;
use yii\db\ActiveQuery;

/**
 * @property int $id
 * @property string $name
 * @property string|null $formatted_address
 * @property float|null $lat
 * @property float|null $lng
 * @property string|null $street
 * @property string|null $house_number
 * @property string|null $locality
 * @property string|null $postal_code
 * @property string|null $district
 * @property string|null $state
 * @property string|null $country_code
 * @property string|null $provider_id
 * @property list<int>|null $tag_ids
 * @property int $tag_count
 * @property DateTime|null $updated_at
 * @property DateTime $created_at
 *
 * @property-read LocationTag[] $locationTags {@see static::getLocationTags()}
 * @property-read LocationTag|null $locationTag {@see static::getLocationTag()}
 * @property-read Tag[] $tags {@see static::getTags()}
 */
class Location extends ActiveRecord implements
    CustomAttributeInterface,
    DraftStatusAttributeInterface,
    I18nAttributeInterface,
    SearchableInterface,
    TrailModelInterface,
    TranslationInterface,
    TypeAttributeInterface
{
    use AdminModelTrait;
    use CustomAttributesTrait;
    use DraftStatusAttributeTrait;
    use I18nAttributesTrait;
    use SearchableTrait;
    use TranslationTrait;
    use ModuleTrait;
    use TrailModelTrait;
    use TypeAttributeTrait;
    use UpdatedByUserTrait;

    public const string AUTH_LOCATION = 'location';

    #[Override]
    public function behaviors(): array
    {
        return [
            ...parent::behaviors(),
            'DateTimeBehavior' => DateTimeBehavior::class,
            'TrailBehavior' => TrailBehavior::class,
        ];
    }

    /**
     * @return array<int|string, string|Closure(self): mixed>
     */
    #[Override]
    public function fields(): array
    {
        return array_filter([
            'name',
            count(static::getTypeDefinitions()) > 1 ? 'type' : null,
            'formatted_address',
            'tags' => static::getModule()->enableTags ? fn (self $location) => $location->getTagNames() : null,
            'lat',
            'lng',
        ]);
    }

    #[Override]
    public function rules(): array
    {
        return [
            ...parent::rules(),
            [
                ['status', 'type'],
                DynamicRangeValidator::class,
            ],
            [
                ['name', 'formatted_address', 'street', 'house_number', 'locality', 'postal_code', 'district', 'state'],
                'string',
                'max' => 255,
            ],
            [
                ['provider_id'],
                'string',
            ],
            [
                ['lat', 'lng'],
                CoordinateValidator::class,
            ],
            [
                ['lat'],
                'number',
                'min' => -90,
                'max' => 90,
            ],
            [
                ['lng'],
                'number',
                'min' => -180,
                'max' => 180,
            ],
            [
                ['country_code'],
                'in',
                'range' => $this->getCountryCodes(),
            ],
            [
                ['tag_ids'],
                'each',
                'rule' => ['integer'],
            ],
        ];
    }

    #[Override]
    public function beforeSave($insert): bool
    {
        $this->attachBehaviors([
            'BlameableBehavior' => BlameableBehavior::class,
            'TimestampBehavior' => TimestampBehavior::class,
        ]);

        return parent::beforeSave($insert);
    }

    /**
     * @param array<string, mixed> $changedAttributes
     */
    #[Override]
    public function afterSave($insert, $changedAttributes): void
    {
        static::getModule()->invalidatePageCache();
        parent::afterSave($insert, $changedAttributes);
    }

    #[Override]
    public function afterDelete(): void
    {
        if ($this->tag_count) {
            foreach ($this->locationTags as $locationTag) {
                $locationTag->delete();
            }
        }

        static::getModule()->invalidatePageCache();
        parent::afterDelete();
    }

    public function getTags(): TagQuery
    {
        /** @var TagQuery $query */
        $query = $this->hasMany(Tag::class, ['id' => 'tag_id'])
            ->via('locationTags');

        return $query;
    }

    /**
     * @return ActiveQuery<LocationTag>
     */
    public function getLocationTag(): ActiveQuery
    {
        return $this->hasOne(LocationTag::class, ['location_id' => 'id'])
            ->inverseOf('location');
    }

    /**
     * @return ActiveQuery<LocationTag>
     */
    public function getLocationTags(): ActiveQuery
    {
        return $this->hasMany(LocationTag::class, ['location_id' => 'id'])
            ->inverseOf('location');
    }

    /**
     * @return LocationQuery<static>
     */
    #[Override]
    public static function find(): LocationQuery
    {
        return Yii::createObject(LocationQuery::class, [static::class]);
    }

    public function updateTagIds(): int
    {
        $tagIds = $this->getLocationTags()->select('tag_id')->column();

        return $this->updateDenormalizedAttributes([
            'tag_ids' => $tagIds ? array_values(array_map(intval(...), $tagIds)) : null,
            'tag_count' => count($tagIds),
        ]);
    }

    public function getCountryName(): ?string
    {
        return $this->country_code ? CountryList::getName($this->country_code) : null;
    }

    /**
     * @return list<string>
     */
    public function getTagNames(): array
    {
        return $this->tag_count
            ? array_values(ArrayHelper::getColumn(TagCollection::getByLocation($this), $this->getI18nAttributeName('name'), false))
            : [];
    }

    /**
     * @return list<string>
     * @noinspection PhpUnused
     */
    public function getTrailAttributes(): array
    {
        return array_values(array_diff($this->attributes(), [
            $this->getCustomAttributesColumn(),
            'tag_ids',
            'tag_count',
            'updated_by_user_id',
            'updated_at',
            'created_at',
        ]));
    }

    public function getAdminType(): string
    {
        return Yii::t('location', 'COMMON_LOCATION');
    }

    #[Override]
    public static function getTypeClass(): string
    {
        return LocationType::class;
    }

    #[Override]
    public function getType(): ?LocationType
    {
        /** @var LocationType|null */
        return static::findType(static::normalizeTypeValue($this->type ?? null));
    }

    public function getAdminRoute(): array|false
    {
        return $this->id ? ['/admin/location/location/update', 'id' => $this->id] : false;
    }

    public function getAdminIndexBreadcrumb(): Breadcrumb
    {
        return new Breadcrumb(Yii::t('location', 'COMMON_LOCATIONS'), ['/admin/location/']);
    }

    public function getPermissionName(): string
    {
        return self::AUTH_LOCATION;
    }

    public function getSearchAttributes(): array
    {
        return ['name', 'formatted_address', 'street', 'locality', 'postal_code', 'district', 'state'];
    }

    public function getSearchWeight(): float
    {
        return 0.6;
    }

    protected function isSearchResultVisible(): bool
    {
        return WebUser::current()?->can(static::AUTH_LOCATION) ?? false;
    }

    /**
     * @return list<string>
     */
    public function getCountryCodes(): array
    {
        return array_keys(CountryList::getNames());
    }

    public function allowsTags(): bool
    {
        return static::getModule()->enableTags && ($this->getType()?->allowsTags() ?? true);
    }

    #[Override]
    public function attributeLabels(): array
    {
        return [
            ...parent::attributeLabels(),
            'name' => Yii::t('location', 'LOCATION_NAME_LABEL'),
            'formatted_address' => Yii::t('location', 'LOCATION_FORMATTED_ADDRESS_LABEL'),
            'street' => Yii::t('location', 'LOCATION_STREET_LABEL'),
            'house_number' => Yii::t('location', 'LOCATION_HOUSE_NUMBER_LABEL'),
            'locality' => Yii::t('location', 'LOCATION_LOCALITY_LABEL'),
            'postal_code' => Yii::t('location', 'LOCATION_POSTAL_CODE_LABEL'),
            'district' => Yii::t('location', 'LOCATION_DISTRICT_LABEL'),
            'state' => Yii::t('location', 'LOCATION_STATE_LABEL'),
            'country_code' => Yii::t('location', 'LOCATION_COUNTRY_CODE_LABEL'),
            'lat' => Yii::t('location', 'LOCATION_LAT_LABEL'),
            'lng' => Yii::t('location', 'LOCATION_LNG_LABEL'),
            'provider_id' => Yii::t('location', 'LOCATION_PROVIDER_ID_LABEL'),
            'tag_count' => Yii::t('location', 'LOCATION_TAG_COUNT_LABEL'),
        ];
    }

    #[Override]
    public function formName(): string
    {
        return 'Location';
    }

    public function getTranslationModelClass(): string
    {
        return self::class;
    }

    #[Override]
    public static function tableName(): string
    {
        return '{{%location}}';
    }
}
