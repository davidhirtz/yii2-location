<?php

namespace davidhirtz\yii2\location;

use davidhirtz\yii2\skeleton\filters\PageCache;
use davidhirtz\yii2\skeleton\modules\ModuleTrait;
use Yii;
use yii\caching\CacheInterface;
use yii\caching\TagDependency;

class Module extends \davidhirtz\yii2\skeleton\base\Module
{
    use ModuleTrait;

    /**
     * @var bool whether to enable location tags.
     */
    public bool $enableTags = true;

    /**
     * @var int|null
     */
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
