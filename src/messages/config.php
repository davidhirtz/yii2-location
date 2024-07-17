<?php
/**
 * This is the configuration for generating message translations
 * for the Yii framework. It is used by the 'yii message' command.
 */

$config = require Yii::getAlias('@skeleton/messages/config.php');

return [
    ...$config,
    'sourcePath' => dirname(__DIR__),
    'messagePath' => __DIR__,
    'ignoreCategories' => ['yii', 'skeleton'],
];
