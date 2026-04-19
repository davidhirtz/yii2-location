<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Buttons;

use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\Admin\Controllers\TagController;
use Hirtz\Skeleton\Widgets\Buttons\DeleteButton;
use Hirtz\Skeleton\Widgets\Traits\ModelTrait;

/**
 * @see TagController::actionDelete()
 */
class TagDeleteButton extends DeleteButton
{
    /**
     * @use ModelTrait<Tag>
     */
    use ModelTrait;

    public function isVisible(): bool
    {
        return parent::isVisible()
            && $this->webuser->can(Tag::AUTH_TAG_DELETE, ['tag' => $this->model]);
    }
}
