<?php

namespace davidhirtz\yii2\location\models\collections;

use davidhirtz\yii2\location\models\Location;
use davidhirtz\yii2\location\models\Tag;
use davidhirtz\yii2\location\modules\ModuleTrait;
use Yii;
use yii\caching\TagDependency;

/**
 * @template T of Tag
 */
class TagCollection
{
    use ModuleTrait;

    public const CACHE_KEY = 'tag-collection';

    protected static ?array $_tags = null;

    /**
     * @return array<int, T>
     */
    public static function getAll(bool $refresh = false): array
    {
        if (null === static::$_tags || $refresh) {
            $dependency = new TagDependency(['tags' => static::CACHE_KEY]);
            $duration = static::getModule()->tagCachedQueryDuration;

            static::$_tags = $duration !== false
                ? Yii::$app->getDb()->cache(static::findAll(...), $duration, $dependency)
                : static::findAll();
        }

        return static::$_tags;
    }

    /**
     * @return array<int, T>
     * @noinspection PhpUnused
     */
    public static function getByLocation(Location $location): array
    {
        return array_filter(static::getAll(), fn (Tag $tag) => $tag->hasTagsEnabled()
            && $location->tag_ids
            && in_array($tag->id, $location->tag_ids));
    }

    /**
     * @return array<int, T>
     */
    public static function findAll(): array
    {
        return Tag::find()
            ->replaceI18nAttributes()
            ->whereStatus()
            ->indexBy('id')
            ->all();
    }

    public static function invalidateCache(): void
    {
        if (static::getModule()->tagCachedQueryDuration !== false) {
            TagDependency::invalidate(Yii::$app->getCache(), static::CACHE_KEY);
        }
    }
}
