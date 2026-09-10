<?php

declare(strict_types=1);

namespace Hirtz\Location\Models;

use Hirtz\Skeleton\I18n\Lang;
use davidhirtz\yii2\datetime\DateTime;
use davidhirtz\yii2\datetime\DateTimeBehavior;
use Hirtz\Location\Models\Collections\TagCollection;
use Hirtz\Location\Models\Queries\LocationQuery;
use Hirtz\Location\Models\Queries\TagQuery;
use Hirtz\Location\Modules\ModuleTrait;
use Hirtz\Location\Validators\CoordinateValidator;
use Hirtz\Skeleton\Behaviors\BlameableBehavior;
use Hirtz\Skeleton\Behaviors\TimestampBehavior;
use Hirtz\Skeleton\Behaviors\TrailBehavior;
use yii\db\ActiveQuery;
use Hirtz\Skeleton\Db\ActiveRecord;
use Hirtz\Skeleton\Helpers\ArrayHelper;
use Hirtz\Skeleton\Helpers\CountryList;
use Hirtz\Skeleton\Models\Interfaces\DraftStatusAttributeInterface;
use Hirtz\Skeleton\Models\Interfaces\I18nAttributeInterface;
use Hirtz\Skeleton\Models\Interfaces\TrailModelInterface;
use Hirtz\Skeleton\Models\Interfaces\TypeAttributeInterface;
use Hirtz\Skeleton\Models\Traits\DraftStatusAttributeTrait;
use Hirtz\Skeleton\Models\Traits\I18nAttributesTrait;
use Hirtz\Skeleton\Models\Traits\TrailModelTrait;
use Hirtz\Skeleton\Models\Traits\TypeAttributeTrait;
use Hirtz\Skeleton\Models\Traits\UpdatedByUserTrait;
use Hirtz\Skeleton\Validators\DynamicRangeValidator;
use Override;
use Yii;

/**
 * @property int $id
 * @property int $status
 * @property int $type
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
 * @property array|null $tag_ids
 * @property int $tag_count
 * @property DateTime|null $updated_at
 * @property DateTime $created_at
 *
 * @property-read LocationTag[] $locationTags {@see static::getLocationTags()}
 * @property-read LocationTag|null $locationTag {@see static::getLocationTag()}
 * @property-read Tag[] $tags {@see static::getTags()}
 */
class Location extends ActiveRecord implements
    DraftStatusAttributeInterface,
    I18nAttributeInterface,
    TrailModelInterface,
    TypeAttributeInterface
{
    use DraftStatusAttributeTrait;
    use I18nAttributesTrait;
    use ModuleTrait;
    use TrailModelTrait;
    use TypeAttributeTrait;
    use UpdatedByUserTrait;

    public const string AUTH_LOCATION_CREATE = 'locationCreate';
    public const string AUTH_LOCATION_DELETE = 'locationDelete';
    public const string AUTH_LOCATION_UPDATE = 'locationUpdate';

    #[Override]
    public function behaviors(): array
    {
        return [
            ...parent::behaviors(),
            'DateTimeBehavior' => DateTimeBehavior::class,
            'TrailBehavior' => TrailBehavior::class,
        ];
    }

    #[Override]
    public function fields(): array
    {
        return array_filter([
            'name',
            count(static::getTypes()) > 1 ? 'type' : null,
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

    #[Override]
    public static function find(): LocationQuery
    {
        return Yii::createObject(LocationQuery::class, [static::class]);
    }

    public function recalculateTagIds(): static
    {
        $tagIds = $this->getLocationTags()->select('tag_id')->column();
        $this->tag_ids = $tagIds ?: null;
        $this->tag_count = count($tagIds);

        return $this;
    }

    public function getCountryName(): ?string
    {
        return $this->country_code ? CountryList::getName($this->country_code) : null;
    }

    public function getTagNames(): array
    {
        return $this->tag_count
            ? ArrayHelper::getColumn(TagCollection::getByLocation($this), $this->getI18nAttributeName('name'), false)
            : [];
    }

    /**
     * @noinspection PhpUnused
     */
    public function getTrailAttributes(): array
    {
        return array_diff($this->attributes(), [
            'tag_ids',
            'tag_count',
            'updated_by_user_id',
            'updated_at',
            'created_at',
        ]);
    }

    /**
     * @noinspection PhpUnused
     */
    public function getTrailModelName(): string
    {
        if ($this->id) {
            return $this->getI18nAttribute('name') ?: Lang::t('skeleton', 'COMMON_MODEL_ID', [
                'model' => $this->getTrailModelType(),
                'id' => $this->id,
            ]);
        }

        return $this->getTrailModelType();
    }

    public function getTrailModelType(): string
    {
        return Lang::t('location', 'COMMON_LOCATION');
    }

    public function getAdminRoute(): array
    {
        return ['/admin/location/location/update', 'id' => $this->id];
    }

    /**
     * @noinspection PhpUnused
     */
    public function getTrailModelAdminRoute(): array|false
    {
        return $this->id ? $this->getAdminRoute() : false;
    }

    public function getCountryCodes(): array
    {
        return array_keys(CountryList::getNames());
    }

    public function hasTagsEnabled(): bool
    {
        return static::getModule()->enableTags;
    }

    #[Override]
    public function attributeLabels(): array
    {
        return [
            ...parent::attributeLabels(),
            'name' => Lang::t('location', 'LOCATION_NAME_LABEL'),
            'formatted_address' => Lang::t('location', 'LOCATION_FORMATTED_ADDRESS_LABEL'),
            'street' => Lang::t('location', 'LOCATION_STREET_LABEL'),
            'house_number' => Lang::t('location', 'LOCATION_HOUSE_NUMBER_LABEL'),
            'locality' => Lang::t('location', 'LOCATION_LOCALITY_LABEL'),
            'postal_code' => Lang::t('location', 'LOCATION_POSTAL_CODE_LABEL'),
            'district' => Lang::t('location', 'LOCATION_DISTRICT_LABEL'),
            'state' => Lang::t('location', 'LOCATION_STATE_LABEL'),
            'country_code' => Lang::t('location', 'LOCATION_COUNTRY_CODE_LABEL'),
            'lat' => Lang::t('location', 'LOCATION_LAT_LABEL'),
            'lng' => Lang::t('location', 'LOCATION_LNG_LABEL'),
            'provider_id' => Lang::t('location', 'LOCATION_PROVIDER_ID_LABEL'),
            'tag_count' => Lang::t('location', 'LOCATION_TAG_COUNT_LABEL'),
        ];
    }

    #[Override]
    public function formName(): string
    {
        return 'Location';
    }

    #[Override]
    public static function tableName(): string
    {
        return '{{%location}}';
    }
}
