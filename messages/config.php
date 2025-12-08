<?php

declare(strict_types=1);

/**
 * This is the configuration for generating message translations
 * for the Yii framework. It is used by the 'yii message' command.
 */

$config = require Yii::getAlias('@skeleton/../messages/config.php');

return [
    ...$config,
    'sourcePath' => __DIR__ . '/../src/',
    'messagePath' => __DIR__,
    'ignoreCategories' => ['yii', 'skeleton'],
];
