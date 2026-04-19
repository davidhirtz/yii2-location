<?php
declare(strict_types=1);

namespace Hirtz\Location\Modules\Admin\Widgets\Navs;

use Hirtz\Skeleton\Widgets\Navs\ActionDropdown;
use Hirtz\Skeleton\Widgets\Traits\ModelTrait;
use Hirtz\Location\Models\Tag;
use Hirtz\Location\Modules\Admin\Widgets\Buttons\TagDeleteButton;
use Override;
use Stringable;

class TagActionDropdown extends ActionDropdown
{
    /**
     * @use ModelTrait<Tag>
     */
    use ModelTrait;

    #[Override]
    protected function configure(): void
    {
        $this->addItem($this->getTagDeleteButton());
        parent::configure();
    }

    protected function getTagDeleteButton(): ?Stringable
    {
        return TagDeleteButton::make()
            ->model($this->model);
    }
}