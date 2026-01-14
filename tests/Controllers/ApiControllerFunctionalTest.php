<?php

declare(strict_types=1);

namespace Hirtz\Location\Tests\Controllers;

use Hirtz\Location\Test\Fixtures\Traits\LocationFixtureTrait;
use Hirtz\Location\Test\TestCase;
use Hirtz\Skeleton\Test\Traits\FunctionalTestTrait;

class ApiControllerFunctionalTest extends TestCase
{
    use FunctionalTestTrait;
    use LocationFixtureTrait;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testIndex(): void
    {
        $this->open('api/location/index.json');
        self::assertResponseIsSuccessful();

        $data = $this->getJsonResponseData();

        self::assertCount(4, $data);
        self::assertArrayHasKey('type', $data[0]);
    }

    public function testIndexWithTypeSlug(): void
    {
        $this->open('api/location/test.json');
        self::assertResponseIsSuccessful();

        $data = $this->getJsonResponseData();

        self::assertCount(2, $data);
        self::assertArrayHasKey('type', $data[0]);
    }
}
