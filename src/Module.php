<?php

declare(strict_types=1);

namespace Hirtz\Location;

use Hirtz\Skeleton\Filters\PageCache;
use Yii;
use yii\caching\CacheInterface;
use yii\caching\TagDependency;

class Module extends \Hirtz\Skeleton\Base\Module
{
    /**
     * @var bool whether to enable the API route.
     */
    public bool $enableApiRoutes = true;

    /**
     * @var bool whether to enable location tags.
     */
    public bool $enableTags = false;

    public ?int $tagCachedQueryDuration = 60;

    public function invalidatePageCache(): void
    {
        if ($cache = $this->getCache()) {
            TagDependency::invalidate($cache, PageCache::TAG_DEPENDENCY_KEY);
        }
    }

    public function getCache(): ?CacheInterface
    {
        return Yii::$app->getCache();
    }
}
