<?php

declare(strict_types=1);

namespace Hirtz\Location\Test;

use Override;

class TestCase extends \Hirtz\Skeleton\Test\TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->config ??= require(__DIR__ . '/../../tests/config.php');
        parent::setUp();
    }
}
