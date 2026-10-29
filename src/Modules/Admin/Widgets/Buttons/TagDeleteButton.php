<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Buttons;

use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\Admin\Controllers\TagController;
use Hirtz\Skeleton\Widgets\Buttons\DeleteButton;
use Yii;

/**
 * @see TagController::actionDelete()
 *
 * @extends DeleteButton<Tag>
 */
class TagDeleteButton extends DeleteButton
{
    #[\Override]
    public function isVisible(): bool
    {
        return parent::isVisible()
            && $this->webuser->can(Tag::AUTH_TAG);
    }

    #[\Override]
    protected function configure(): void
    {
        $this->label ??= Yii::t('location', 'TAG_DELETE_BUTTON');
        $this->title ??= Yii::t('location', 'TAG_DELETE_CONFIRM');

        parent::configure();
    }
}
