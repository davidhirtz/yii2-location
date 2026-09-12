<?php

declare(strict_types=1);

namespace Hirtz\Location\Models;

use davidhirtz\yii2\datetime\DateTime;
use davidhirtz\yii2\datetime\DateTimeBehavior;
use Hirtz\Location\Models\Collections\TagCollection;
use Hirtz\Location\Models\Queries\LocationQuery;
use Hirtz\Location\Models\Queries\TagQuery;
use Hirtz\Location\Modules\ModuleTrait;
use Hirtz\Skeleton\Behaviors\BlameableBehavior;
use Hirtz\Skeleton\Behaviors\TimestampBehavior;
use Hirtz\Skeleton\Behaviors\TrailBehavior;
use yii\db\ActiveQuery;
use Hirtz\Skeleton\Db\ActiveRecord;
use Hirtz\Skeleton\Models\Interfaces\AdminRouteInterface;
use Hirtz\Skeleton\Models\Interfaces\DraftStatusAttributeInterface;
use Hirtz\Skeleton\Models\Interfaces\I18nAttributeInterface;
use Hirtz\Skeleton\Models\Interfaces\SearchableInterface;
use Hirtz\Skeleton\Models\Interfaces\CustomAttributeInterface;
use Hirtz\Skeleton\Models\Interfaces\TrailModelInterface;
use Hirtz\Skeleton\Models\Interfaces\TranslationInterface;
use Hirtz\Skeleton\Models\Interfaces\TypeAttributeInterface;
use Hirtz\Skeleton\Models\Traits\CustomAttributesTrait;
use Hirtz\Skeleton\Models\Traits\DraftStatusAttributeTrait;
use Hirtz\Skeleton\Models\Traits\I18nAttributesTrait;
use Hirtz\Skeleton\Models\Traits\SearchableTrait;
use Hirtz\Skeleton\Models\Traits\TrailModelTrait;
use Hirtz\Skeleton\Models\Traits\TranslationTrait;
use Hirtz\Skeleton\Models\Traits\TypeAttributeTrait;
use Hirtz\Skeleton\Models\Traits\UpdatedByUserTrait;
use Hirtz\Skeleton\Validators\DynamicRangeValidator;
use Hirtz\Skeleton\Validators\UniqueValidator;
use Override;
use Yii;

/**
 * @property int $id
 * @property string $name
 * @property int $location_count
 * @property DateTime|null $updated_at
 * @property DateTime $created_at
 *
 * @property-read LocationTag[] $locationTags {@see static::getLocationTags()}
 * @property-read LocationTag|null $locationTag {@see static::getLocationTag()}
 * @property-read Location[] $locations {@see static::getLocations()}
 */
class Tag extends ActiveRecord implements
    AdminRouteInterface,
    CustomAttributeInterface,
    DraftStatusAttributeInterface,
    I18nAttributeInterface,
    SearchableInterface,
    TrailModelInterface,
    TranslationInterface,
    TypeAttributeInterface
{
    use CustomAttributesTrait;
    use DraftStatusAttributeTrait;
    use I18nAttributesTrait;
    use SearchableTrait;
    use TranslationTrait;
    use ModuleTrait;
    use TypeAttributeTrait;
    use TrailModelTrait;
    use UpdatedByUserTrait;

    public const string AUTH_TAG_CREATE = 'tagCreate';
    public const string AUTH_TAG_DELETE = 'tagDelete';
    public const string AUTH_TAG_UPDATE = 'tagUpdate';

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
        return [
            'name',
            'formatted_address',
            'lat',
            'lng',
        ];
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
                ['name'],
                'required',
            ],
            [
                ['name'],
                'string',
                'max' => 255,
            ],
            [
                ['name'],
                UniqueValidator::class,
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
        $this->invalidateCache();
        parent::afterSave($insert, $changedAttributes);
    }

    #[Override]
    public function afterDelete(): void
    {
        foreach ($this->locationTags as $locationTag) {
            $locationTag->delete();
        }

        $this->invalidateCache();
        parent::afterDelete();
    }

    public function getLocations(): LocationQuery
    {
        /** @var LocationQuery $query */
        $query = $this->hasMany(Location::class, ['id' => 'location_id'])
            ->via('locationTags');

        return $query;
    }

    /**
     * @return ActiveQuery<LocationTag>
     */
    public function getLocationTag(): ActiveQuery
    {
        return $this->hasOne(LocationTag::class, ['tag_id' => 'id'])
            ->inverseOf('tag');
    }

    /**
     * @return ActiveQuery<LocationTag>
     */
    public function getLocationTags(): ActiveQuery
    {
        return $this->hasMany(LocationTag::class, ['tag_id' => 'id'])
            ->inverseOf('tag');
    }

    #[Override]
    public static function find(): TagQuery
    {
        return Yii::createObject(TagQuery::class, [static::class]);
    }

    public function invalidateCache(): void
    {
        static::getModule()->invalidatePageCache();
        TagCollection::invalidateCache();
    }

    public function recalculateLocationCount(): static
    {
        $this->location_count = (int)$this->getLocationTags()->count();
        return $this;
    }

    /**
     * @noinspection PhpUnused
     */
    public function getTrailAttributes(): array
    {
        return array_diff($this->attributes(), [
            $this->getCustomAttributesColumn(),
            'location_count',
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
            return $this->getI18nAttribute('name') ?: Yii::t('skeleton', 'COMMON_MODEL_ID', [
                'model' => $this->getTrailModelType(),
                'id' => $this->id,
            ]);
        }

        return $this->getTrailModelType();
    }

    public function getTrailModelType(): string
    {
        return Yii::t('location', 'COMMON_TAG');
    }

    public function getAdminRoute(): array|false
    {
        return $this->id ? ['/admin/location/tag/update', 'id' => $this->id] : false;
    }

    public function getSearchAttributes(): array
    {
        return ['name'];
    }

    public function getSearchWeight(): float
    {
        return 0.5;
    }

    public function isSearchable(): bool
    {
        return $this->hasTagsEnabled();
    }

    /**
     * The flag gates the hit as well as the write, so turning tags off hides the rows a rebuild has not
     * removed yet.
     */
    protected function isSearchResultVisible(): bool
    {
        return $this->hasTagsEnabled()
            && Yii::$app->has('user')
            && Yii::$app->getUser()->can(static::AUTH_TAG_UPDATE);
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
            'name' => Yii::t('location', 'TAG_NAME_LABEL'),
            'location_count' => Yii::t('location', 'TAG_LOCATION_COUNT_LABEL'),
        ];
    }

    #[Override]
    public function formName(): string
    {
        return 'Tag';
    }

    public function getTranslationModelClass(): string
    {
        return self::class;
    }

    #[Override]
    public static function tableName(): string
    {
        return '{{%tag}}';
    }
}
