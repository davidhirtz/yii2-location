<?php

declare(strict_types=1);

namespace Hirtz\Location\Models\Collections;

use Hirtz\Location\Models\Location;
use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\ModuleTrait;
use Yii;
use yii\caching\TagDependency;

/**
 * @template T of Tag
 */
class TagCollection
{
    use ModuleTrait;

    public const CACHE_KEY = 'tag-collection';

    protected static ?array $tags = null;

    /**
     * @return array<int, T>
     */
    public static function getAll(bool $refresh = false): array
    {
        if (null === static::$tags || $refresh) {
            $dependency = new TagDependency(['tags' => static::CACHE_KEY]);
            $duration = static::getModule()->tagCachedQueryDuration;

            static::$tags = (null !== $duration)
                ? Tag::getDb()->cache(static::findAll(...), $duration, $dependency)
                : static::findAll();
        }

        return static::$tags;
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
            ->withTranslations()
            ->whereStatus()
            ->indexBy('id')
            ->all();
    }

    public static function invalidateCache(): void
    {
        if (null !== static::getModule()->tagCachedQueryDuration) {
            TagDependency::invalidate(Yii::$app->getCache(), static::CACHE_KEY);
        }

        self::reset();
    }

    /**
     * The static outlives the application; `Bootstrap` resets it, so a test's application does not start with the
     * tags of the one before.
     */
    public static function reset(): void
    {
        self::$tags = null;
    }
}
