<?php

namespace davidhirtz\yii2\location\models;

use davidhirtz\yii2\datetime\DateTime;
use davidhirtz\yii2\location\modules\ModuleTrait;
use davidhirtz\yii2\skeleton\behaviors\BlameableBehavior;
use davidhirtz\yii2\skeleton\behaviors\TimestampBehavior;
use davidhirtz\yii2\skeleton\behaviors\TrailBehavior;
use davidhirtz\yii2\skeleton\db\ActiveQuery;
use davidhirtz\yii2\skeleton\db\ActiveRecord;
use davidhirtz\yii2\skeleton\models\traits\UpdatedByUserTrait;
use davidhirtz\yii2\skeleton\validators\UniqueValidator;
use Yii;

/**
 * @property int $location_id
 * @property int $tag_id
 * @property int|null $updated_by_user_id
 * @property DateTime|null $updated_at
 *
 * @property-read Tag|null $tag {@see static::getTag()}
 * @property-read Location|null $location {@see static::getLocation()}
 *
 * @mixin TrailBehavior
 */
class LocationTag extends ActiveRecord
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
                ['location_id'],
                $this->validateLocationId(...),
            ],
            [
                ['tag_id'],
                $this->validateTagId(...),
            ],
            [
                ['location_id'],
                UniqueValidator::class,
                'targetAttribute' => ['location_id', 'tag_id'],
            ],
        ]);
    }

    protected function validateLocationId(): void
    {
        if (!$this->location?->hasTagsEnabled()) {
            $this->addInvalidAttributeError('location_id');
        }
    }

    protected function validateTagId(): void
    {
        if (!$this->tag?->hasTagsEnabled()) {
            $this->addInvalidAttributeError('tag_id');
        }
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

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes): void
    {
        if ($insert) {
            $this->updateLocationTagIds();
            $this->updateTagLocationCount();
        }

        static::getModule()->invalidatePageCache();

        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete(): void
    {
        if (!$this->location->isDeleted()) {
            $this->updateLocationTagIds();
        }

        if (!$this->tag->isDeleted()) {
            $this->updateTagLocationCount();
        }

        static::getModule()->invalidatePageCache();

        parent::afterDelete();
    }

    public function getLocation(): ActiveQuery
    {
        return $this->hasOne(Location::class, ['id' => 'location_id']);
    }

    public function getTag(): ActiveQuery
    {
        return $this->hasOne(Tag::class, ['id' => 'tag_id']);
    }

    public function populateLocationRelation(Location $location): void
    {
        $this->populateRelation('location', $location);
        $this->location_id = $location->id;
    }

    public function updateLocationTagIds(): bool|int
    {
        return $this->location->recalculateTagIds()->update();
    }

    public function updateTagLocationCount(): bool|int
    {
        return $this->tag->recalculateLocationCount()->update();
    }

    /**
     * @noinspection PhpUnused
     */
    public function getTrailParents(): array
    {
        return [$this->location, $this->tag];
    }

    /**
     * @noinspection PhpUnused
     */
    public function getTrailModelName(): string
    {
        return Yii::t('location', 'Location–Tag');
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
            'tag_id' => Yii::t('location', 'Tag'),
            'updated_at' => Yii::t('location', 'Added'),
        ]);
    }

    public function formName(): string
    {
        return 'LocationTag';
    }

    public static function tableName(): string
    {
        return static::getModule()->getTableName('location_tag');
    }
}
