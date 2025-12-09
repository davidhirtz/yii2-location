<?php

declare(strict_types=1);

namespace Hirtz\Location\models;

use davidhirtz\yii2\datetime\DateTime;
use Hirtz\Location\modules\ModuleTrait;
use Hirtz\Skeleton\Behaviors\BlameableBehavior;
use Hirtz\Skeleton\Behaviors\TimestampBehavior;
use Hirtz\Skeleton\Behaviors\TrailBehavior;
use Hirtz\Skeleton\Db\ActiveQuery;
use Hirtz\Skeleton\Db\ActiveRecord;
use Hirtz\Skeleton\Models\Traits\UpdatedByUserTrait;
use Hirtz\Skeleton\Validators\UniqueValidator;
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

    #[\Override]
    public function behaviors(): array
    {
        return [
            ...parent::behaviors(),
            'TrailBehavior' => TrailBehavior::class,
        ];
    }

    #[\Override]
    public function rules(): array
    {
        return [...parent::rules(), [
            ['location_id'],
            $this->validateLocationId(...),
        ], [
            ['tag_id'],
            $this->validateTagId(...),
        ], [
            ['location_id'],
            UniqueValidator::class,
            'targetAttribute' => ['location_id', 'tag_id'],
        ]];
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

    #[\Override]
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

    #[\Override]
    public function afterSave($insert, $changedAttributes): void
    {
        if ($insert) {
            if (!$this->getIsBatch()) {
                $this->updateLocationTagIds();

            }
            $this->updateTagLocationCount();
        }

        static::getModule()->invalidatePageCache();

        parent::afterSave($insert, $changedAttributes);
    }

    #[\Override]
    public function afterDelete(): void
    {
        if (!$this->location->isDeleted() && !$this->getIsBatch()) {
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

    public function populateTagRelation(Tag $tag): void
    {
        $this->populateRelation('tag', $tag);
        $this->tag_id = $tag->id;
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

    #[\Override]
    public function attributeLabels(): array
    {
        return [...parent::attributeLabels(), 'location_id' => Yii::t('location', 'Location'), 'tag_id' => Yii::t('location', 'Tag'), 'updated_at' => Yii::t('location', 'Added')];
    }

    #[\Override]
    public function formName(): string
    {
        return 'LocationTag';
    }

    #[\Override]
    public static function tableName(): string
    {
        return static::getModule()->getTableName('location_tag');
    }
}
