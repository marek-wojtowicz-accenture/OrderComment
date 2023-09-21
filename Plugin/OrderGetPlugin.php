<?php

declare(strict_types=1);

namespace Pg\OrderComment\Plugin;

use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Pg\OrderComment\Api\Data\OrderCommentInterface;
use Pg\OrderComment\Api\Data\OrderCommentInterfaceFactory;

class OrderGetPlugin
{
    public function __construct(
        protected OrderExtensionFactory $orderExtensionFactory,
        protected OrderCommentInterfaceFactory $orderCommentFactory
    ) {
    }

    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order): OrderInterface
    {
        $this->setOrderExtensionAttributes($order);
        return $order;
    }

    private function setOrderExtensionAttributes(OrderInterface $order): void
    {
        $commentObject = $this->prepareOrderComment($order);
        $extensionAttributes = $this->getOrCreateExtensionAttributes($order);
        $extensionAttributes->setComment($commentObject);
        $order->setExtensionAttributes($extensionAttributes);
    }

    private function prepareOrderComment(OrderInterface $order): OrderCommentInterface
    {
        $commentText = $order->getData(OrderCommentInterface::COMMENT);
        $commentObject = $this->orderCommentFactory->create();
        $commentObject->setComment($commentText);
        return $commentObject;
    }

    private function getOrCreateExtensionAttributes(OrderInterface $order): OrderExtensionInterface
    {
        $extensionAttributes = $order->getExtensionAttributes();
        return $extensionAttributes ? $extensionAttributes : $this->orderExtensionFactory->create();
    }
}
