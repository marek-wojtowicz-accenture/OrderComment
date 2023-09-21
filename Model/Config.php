<?php

declare(strict_types=1);

namespace Pg\OrderComment\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Pg\OrderComment\Api\ConfigInterface;

class Config implements ConfigInterface
{
    public const XML_PATH_COMMENT_TEXT = 'sales/order_comment/order_comment_field';

    public function __construct(private ScopeConfigInterface $scopeConfig)
    {
    }

    public function getCommentText(?int $storeId = null): string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_COMMENT_TEXT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
