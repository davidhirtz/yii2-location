<?php

namespace davidhirtz\yii2\location\modules\admin\widgets\forms;

use davidhirtz\yii2\location\modules\admin\assets\AutocompleteAssetBundle;
use Yii;
use yii\widgets\InputWidget;

class AutocompleteInputWidget extends InputWidget
{
    /**
     * @var string|false|null containing label of the input field. This makes it easier for related packages to change
     * the label without having to override the entire widget. Set to `false` to disable the label.
     */
    public string|false|null $label = null;

    public function init(): void
    {
        if ($this->label !== null) {
            $this->field->label($this->label);
        }

        $this->options['id'] ??= 'autocomplete';
        $this->options['placeholder'] ??= Yii::t('location', 'Search for a location ...');
        $this->options['autocomplete'] ??= 'off';

        $this->registerClientScript();

        parent::init();
    }

    public function run(): string
    {
        return $this->renderInputHtml($this->model?->{$this->attribute} ? 'text' : 'search');
    }

    protected function registerClientScript(): void
    {
        AutocompleteAssetBundle::register(Yii::$app->getView());
    }
}