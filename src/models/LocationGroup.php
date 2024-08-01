<?php

namespace davidhirtz\yii2\location\models;

use davidhirtz\yii2\datetime\DateTime;
use davidhirtz\yii2\location\modules\ModuleTrait;
use davidhirtz\yii2\skeleton\behaviors\BlameableBehavior;
use davidhirtz\yii2\skeleton\behaviors\TimestampBehavior;
use davidhirtz\yii2\skeleton\behaviors\TrailBehavior;
use davidhirtz\yii2\skeleton\db\ActiveRecord;
use davidhirtz\yii2\skeleton\models\traits\UpdatedByUserTrait;
use davidhirtz\yii2\skeleton\validators\RelationValidator;
use Yii;

/**
 * @property int $location_id
 * @property int $group_id
 * @property int $position
 * @property int|null $updated_by_user_id
 * @property DateTime|null $updated_at
 *
 * @property-read Group $group {@see static::getGroup()}
 * @property-read Location $location {@see static::getLocation()}
 *
 * @mixin TrailBehavior
 */
class LocationGroup extends ActiveRecord
{
    use ModuleTrait;
    use UpdatedByUserTrait;

    public function behaviors(): array
    {
        return [
            ...parent::behaviors(),
            'TrailBehavior' => TrailBehavior::class,
        ];
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [
                ['group_id'],
                RelationValidator::class,
                'required' => true,
            ],
            [
                ['location_id'],
                RelationValidator::class,
                'required' => true,
            ],
            [
                ['location_id'],
                'unique',
                'targetAttribute' => ['location_id', 'group_id'],
            ],
        ]);
    }

    public function beforeSave($insert): bool
    {
        $this->attachBehaviors([
            'BlameableBehavior' => BlameableBehavior::class,
            'TimestampBehavior' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => null,
            ],
        ]);

        $this->position ??= $this->getMaxPosition() + 1;

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes): void
    {
        if ($insert) {
            $this->updateLocationGroupIds();
            $this->updateGroupLocationCount();
        }

        static::getModule()->invalidatePageCache();

        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete(): void
    {
        if (!$this->location->isDeleted()) {
            $this->updateLocationGroupIds();
        }

        if (!$this->group->isDeleted()) {
            $this->updateGroupLocationCount();
        }

        static::getModule()->invalidatePageCache();

        parent::afterDelete();
    }

    public function updateLocationGroupIds(): bool|int
    {
        return $this->location->recalculateGroupIds()->update();
    }

    public function updateGroupLocationCount(): bool|int
    {
        return $this->group->recalculateLocationCount()->update();
    }

    public function getMaxPosition(): int
    {
        return (int)static::find()->where(['group_id' => $this->group_id])->max('[[position]]');
    }

    /**
     * @noinspection PhpUnused
     */
    public function getTrailParents(): array
    {
        return [$this->location, $this->group];
    }

    /**
     * @noinspection PhpUnused
     */
    public function getTrailModelName(): string
    {
        return Yii::t('location', 'Location–Group');
    }

    /**
     * @noinspection PhpUnused
     */
    public function getTrailModelType(): string
    {
        return Yii::t('skeleton', 'Relation');
    }

    public function attributeLabels(): array
    {
        return array_merge(parent::attributeLabels(), [
            'location_id' => Yii::t('location', 'Location'),
            'group_id' => Yii::t('location', 'Group'),
            'updated_at' => Yii::t('location', 'Added'),
        ]);
    }

    public function formName(): string
    {
        return 'LocationGroup';
    }

    public static function tableName(): string
    {
        return static::getModule()->getTableName('location_group');
    }
}
