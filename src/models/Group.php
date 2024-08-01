<?php

namespace davidhirtz\yii2\location\models;

use davidhirtz\yii2\datetime\DateTime;
use davidhirtz\yii2\datetime\DateTimeBehavior;
use davidhirtz\yii2\location\modules\ModuleTrait;
use davidhirtz\yii2\location\validators\CoordinateValidator;
use davidhirtz\yii2\skeleton\behaviors\BlameableBehavior;
use davidhirtz\yii2\skeleton\behaviors\TimestampBehavior;
use davidhirtz\yii2\skeleton\behaviors\TrailBehavior;
use davidhirtz\yii2\skeleton\db\ActiveQuery;
use davidhirtz\yii2\skeleton\db\ActiveRecord;
use davidhirtz\yii2\skeleton\models\traits\DraftStatusAttributeTrait;
use davidhirtz\yii2\skeleton\models\traits\I18nAttributesTrait;
use davidhirtz\yii2\skeleton\models\traits\TypeAttributeTrait;
use davidhirtz\yii2\skeleton\models\traits\UpdatedByUserTrait;
use davidhirtz\yii2\skeleton\validators\DynamicRangeValidator;
use Yii;

/**
 * @property int $id
 * @property string $name
 * @property int $location_count
 * @property DateTime|null $updated_at
 * @property DateTime $created_at
 *
 * @property-read LocationGroup[] $locationGroups {@see static::getLocationGroups()}
 * @property-read LocationGroup $locationGroup {@see static::getLocationGroup()}
 * @property-read Location[] $locations {@see static::getLocations()}
 */
class Group extends ActiveRecord
{
    use DraftStatusAttributeTrait;
    use I18nAttributesTrait;
    use ModuleTrait;
    use TypeAttributeTrait;
    use UpdatedByUserTrait;

    public const AUTH_GROUP_CREATE = 'groupCreate';
    public const AUTH_GROUP_DELETE = 'groupDelete';
    public const AUTH_GROUP_UPDATE = 'groupUpdate';

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
                'string',
                'max' => 255,
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
        static::getModule()->invalidatePageCache();
        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete(): void
    {
        static::getModule()->invalidatePageCache();
        parent::afterDelete();
    }

    public function getLocations(): ActiveQuery
    {
        return $this->hasMany(Location::class, ['id' => 'location_id'])
            ->via('locationGroups');
    }

    public function getLocationGroup(): ActiveQuery
    {
        return $this->hasOne(LocationGroup::class, ['group_id' => 'id'])
            ->inverseOf('group');
    }

    public function getLocationGroups(): ActiveQuery
    {
        return $this->hasMany(LocationGroup::class, ['group_id' => 'id'])
            ->inverseOf('group');
    }

    public function recalculateLocationCount(): static
    {
        $this->location_count = (int)$this->getEntryGroups()->count();
        return $this;
    }
    
    /**
     * @noinspection PhpUnused
     */
    public function getTrailAttributes(): array
    {
        return array_diff($this->attributes(), [
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
        return Yii::t('location', 'Group');
    }

    public function getAdminRoute(): array
    {
        return ['/admin/group/update', 'id' => $this->id];
    }

    /**
     * @noinspection PhpUnused
     */
    public function getTrailModelAdminRoute(): array|false
    {
        return $this->id ? $this->getAdminRoute() : false;
    }

    public function attributeLabels(): array
    {
        return [
            ...parent::attributeLabels(),
            'name' => Yii::t('location', 'Name'),
        ];
    }

    public function formName(): string
    {
        return 'Group';
    }

    public static function tableName(): string
    {
        return static::getModule()->getTableName('group');
    }
}