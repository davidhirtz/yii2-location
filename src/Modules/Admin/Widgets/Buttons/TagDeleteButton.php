<?php

declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Buttons;

use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\Admin\Controllers\TagController;
use Hirtz\Skeleton\Widgets\Buttons\DeleteButton;

/**
 * @see TagController::actionDelete()
 *
 * @extends DeleteButton<Tag>
 */
class TagDeleteButton extends DeleteButton
{
    public function isVisible(): bool
    {
        return parent::isVisible()
            && $this->webuser->can(Tag::AUTH_TAG_DELETE, ['tag' => $this->model]);
    }
}
