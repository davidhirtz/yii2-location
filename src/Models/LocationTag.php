<?php

declare(strict_types=1);

namespace Hirtz\Location\Models;

use davidhirtz\yii2\datetime\DateTime;
use Hirtz\Location\Modules\ModuleTrait;
use Hirtz\Skeleton\Behaviors\BlameableBehavior;
use Hirtz\Skeleton\Behaviors\TimestampBehavior;
use Hirtz\Skeleton\Behaviors\TrailBehavior;
use Hirtz\Skeleton\Models\Traits\AdminModelTrait;
use yii\db\ActiveQuery;
use Hirtz\Skeleton\Db\ActiveRecord;
use Hirtz\Skeleton\Models\Interfaces\TrailModelInterface;
use Hirtz\Skeleton\Models\Traits\TrailModelTrait;
use Hirtz\Skeleton\Models\Traits\UpdatedByUserTrait;
use Hirtz\Skeleton\Validators\UniqueValidator;
use Override;
use Yii;

/**
 * @property int $location_id
 * @property int $tag_id
 * @property DateTime|null $updated_at
 *
 * @property-read Tag|null $tag {@see static::getTag()}
 * @property-read Location|null $location {@see static::getLocation()}
 *
 * @mixin TrailBehavior
 */
class LocationTag extends ActiveRecord implements TrailModelInterface
{
    use AdminModelTrait;
    use ModuleTrait;
    use TrailModelTrait;
    use UpdatedByUserTrait;

    #[Override]
    public function behaviors(): array
    {
        return [
            ...parent::behaviors(),
            'TrailBehavior' => TrailBehavior::class,
        ];
    }

    #[Override]
    public function rules(): array
    {
        return [
            ...parent::rules(), [
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
        ];
    }

    protected function validateLocationId(): void
    {
        if (!$this->location?->allowsTags()) {
            $this->addInvalidAttributeError('location_id');
        }
    }

    protected function validateTagId(): void
    {
        if (!$this->tag?->allowsTags()) {
            $this->addInvalidAttributeError('tag_id');
        }
    }

    #[Override]
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

    /**
     * @param array<string, mixed> $changedAttributes
     */
    #[Override]
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

    #[Override]
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

    /**
     * @return ActiveQuery<Location>
     */
    public function getLocation(): ActiveQuery
    {
        return $this->hasOne(Location::class, ['id' => 'location_id']);
    }

    /**
     * @return ActiveQuery<Tag>
     */
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

    public function updateLocationTagIds(): int
    {
        return $this->location->updateTagIds();
    }

    public function updateTagLocationCount(): int
    {
        return $this->tag->updateLocationCount();
    }

    /**
     * @return list<Location|Tag>
     * @noinspection PhpUnused
     */
    public function getTrailParents(): array
    {
        return [$this->location, $this->tag];
    }

    /**
     * @noinspection PhpUnused
     */
    public function getAdminRoute(): array|false
    {
        return false;
    }

    public function getPermissionName(): string
    {
        return Location::AUTH_LOCATION;
    }

    public function getAdminName(): string
    {
        return Yii::t('location', 'LOCATION_TAG_LOCATION_TAG');
    }

    /**
     * @noinspection PhpUnused
     */
    public function getAdminType(): string
    {
        return Yii::t('skeleton', 'COMMON_RELATION');
    }

    #[Override]
    public function attributeLabels(): array
    {
        return [...parent::attributeLabels(), 'location_id' => Yii::t('location', 'LOCATION_TAG_LOCATION_ID_LABEL'), 'tag_id' => Yii::t('location', 'LOCATION_TAG_TAG_ID_LABEL'), 'updated_at' => Yii::t('location', 'LOCATION_TAG_UPDATED_AT_LABEL')];
    }

    #[Override]
    public function formName(): string
    {
        return 'LocationTag';
    }

    #[Override]
    public static function tableName(): string
    {
        return '{{%location_tag}}';
    }
}
