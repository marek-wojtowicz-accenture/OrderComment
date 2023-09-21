<?php

declare(strict_types=1);

namespace Pg\OrderComment\Test\Unit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Pg\OrderComment\Model\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    private $scopeConfigMock;

    private $config;

    protected function setUp(): void
    {
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->config = new Config($this->scopeConfigMock);
    }

    public function testGetCommentText(): void
    {
        $storeId = 1;
        $expectedCommentText = 'Special Comment';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                Config::XML_PATH_COMMENT_TEXT,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
            ->willReturn($expectedCommentText);

        $actualCommentText = $this->config->getCommentText($storeId);
        $this->assertEquals($expectedCommentText, $actualCommentText);
    }
}
