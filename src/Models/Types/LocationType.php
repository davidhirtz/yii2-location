<?php

declare(strict_types=1);

namespace Hirtz\Location\Models\Types;

use Hirtz\Location\Controllers\ApiController;
use Hirtz\Skeleton\Models\Types\Type;

class LocationType extends Type
{
    protected ?string $slug = null;

    /**
     * @param string|null $slug what {@see ApiController::findTypeBySlug()} matches, so the type has a URL of its own
     */
    public function slug(?string $slug): static
    {
        $this->slug = $slug;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }
}
