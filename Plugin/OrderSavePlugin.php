<?php

declare(strict_types=1);

namespace Pg\OrderComment\Plugin;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Pg\OrderComment\Api\Data\OrderCommentInterface;

class OrderSavePlugin
{
    public function beforeSave(OrderRepositoryInterface $subject, OrderInterface $order): array
    {
        $this->updateOrderDataWithExtensionAttribute($order);
        return [$order];
    }

    private function updateOrderDataWithExtensionAttribute(OrderInterface $order): void
    {
        $extensionAttributes = $order->getExtensionAttributes();
        if ($extensionAttributes && $extensionAttributes->getComment()) {
            $order->setData(OrderCommentInterface::COMMENT, $extensionAttributes->getComment()->getComment());
        }
    }
}
