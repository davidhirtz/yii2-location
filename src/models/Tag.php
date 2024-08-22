<?php

namespace davidhirtz\yii2\location\models;

use davidhirtz\yii2\datetime\DateTime;
use davidhirtz\yii2\datetime\DateTimeBehavior;
use davidhirtz\yii2\location\models\collections\TagCollection;
use davidhirtz\yii2\location\models\queries\LocationQuery;
use davidhirtz\yii2\location\models\queries\TagQuery;
use davidhirtz\yii2\location\modules\ModuleTrait;
use davidhirtz\yii2\skeleton\behaviors\BlameableBehavior;
use davidhirtz\yii2\skeleton\behaviors\TimestampBehavior;
use davidhirtz\yii2\skeleton\behaviors\TrailBehavior;
use davidhirtz\yii2\skeleton\db\ActiveQuery;
use davidhirtz\yii2\skeleton\db\ActiveRecord;
use davidhirtz\yii2\skeleton\models\interfaces\DraftStatusAttributeInterface;
use davidhirtz\yii2\skeleton\models\interfaces\TypeAttributeInterface;
use davidhirtz\yii2\skeleton\models\traits\DraftStatusAttributeTrait;
use davidhirtz\yii2\skeleton\models\traits\I18nAttributesTrait;
use davidhirtz\yii2\skeleton\models\traits\TypeAttributeTrait;
use davidhirtz\yii2\skeleton\models\traits\UpdatedByUserTrait;
use davidhirtz\yii2\skeleton\validators\DynamicRangeValidator;
use davidhirtz\yii2\skeleton\validators\UniqueValidator;
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
class Tag extends ActiveRecord implements DraftStatusAttributeInterface, TypeAttributeInterface
{
    use DraftStatusAttributeTrait;
    use I18nAttributesTrait;
    use ModuleTrait;
    use TypeAttributeTrait;
    use UpdatedByUserTrait;

    public const AUTH_TAG_CREATE = 'tagCreate';
    public const AUTH_TAG_DELETE = 'tagDelete';
    public const AUTH_TAG_UPDATE = 'tagUpdate';

    public function behaviors(): array
    {
        return [
            ...parent::behaviors(),
            'DateTimeBehavior' => DateTimeBehavior::class,
            'TrailBehavior' => TrailBehavior::class,
        ];
    }

    public function fields(): array
    {
        return [
            'name',
            'formatted_address',
            'lat',
            'lng',
        ];
    }

    public function rules(): array
    {
        return [
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

    public function beforeSave($insert): bool
    {
        $this->attachBehaviors([
            'BlameableBehavior' => BlameableBehavior::class,
            'TimestampBehavior' => TimestampBehavior::class,
        ]);

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes): void
    {
        $this->invalidateCache();
        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete(): void
    {
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

    public function getLocationTag(): ActiveQuery
    {
        return $this->hasOne(LocationTag::class, ['tag_id' => 'id'])
            ->inverseOf('tag');
    }

    public function getLocationTags(): ActiveQuery
    {
        return $this->hasMany(LocationTag::class, ['tag_id' => 'id'])
            ->inverseOf('tag');
    }

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
            return $this->getI18nAttribute('name') ?: Yii::t('skeleton', '{model} #{id}', [
                'model' => $this->getTrailModelType(),
                'id' => $this->id,
            ]);
        }

        return $this->getTrailModelType();
    }

    public function getTrailModelType(): string
    {
        return Yii::t('location', 'Tag');
    }

    public function getAdminRoute(): array
    {
        return ['/admin/tag/update', 'id' => $this->id];
    }

    /**
     * @noinspection PhpUnused
     */
    public function getTrailModelAdminRoute(): array|false
    {
        return $this->id ? $this->getAdminRoute() : false;
    }

    public function hasTagsEnabled(): bool
    {
        return static::getModule()->enableTags;
    }

    public function attributeLabels(): array
    {
        return [
            ...parent::attributeLabels(),
            'name' => Yii::t('location', 'Name'),
            'location_count' => Yii::t('location', 'Locations'),
        ];
    }

    public function formName(): string
    {
        return 'Tag';
    }

    public static function tableName(): string
    {
        return static::getModule()->getTableName('tag');
    }
}