<?php

declare(strict_types=1);

namespace Hirtz\Location\Tests\Mcp;

use Hirtz\Location\Models\Location;
use Hirtz\Location\Test\TestCase;
use Hirtz\Skeleton\Mcp\Mcp;
use Hirtz\Skeleton\Mcp\Tools\CreateRecordTool;
use Hirtz\Skeleton\Test\Traits\UserFixtureTrait;

class McpResourcesTest extends TestCase
{
    use UserFixtureTrait;

    public function testALocationIsCreated(): void
    {
        $this->getWebUser()->setIdentity($this->getUserFromFixture('owner'));

        $result = (new CreateRecordTool(Mcp::getComponent()))->call([
            'resource' => 'location',
            'attributes' => ['name' => 'Café Ąb 東京', 'locality' => 'München', 'country_code' => 'DE'],
        ]);

        self::assertFalse($result->isError, json_encode($result->structuredContent, JSON_UNESCAPED_UNICODE) ?: '');
        self::assertSame('München', Location::findOne($result->structuredContent['id'] ?? 0)?->locality);
    }
}
