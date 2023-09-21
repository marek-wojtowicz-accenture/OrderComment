<?php

declare(strict_types=1);

namespace Pg\OrderComment\Model;

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Pg\OrderComment\Api\ConfigInterface;
use Pg\OrderComment\Api\Data\OrderCommentInterface;
use Pg\OrderComment\Api\Data\OrderCommentInterfaceFactory;
use Pg\OrderComment\Api\OrderProcessorInterface;
use Psr\Log\LoggerInterface;

class OrderProcessor implements OrderProcessorInterface
{
    public function __construct(
        private ConfigInterface $config,
        private OrderCommentInterfaceFactory $orderCommentFactory,
        private OrderExtensionFactory $orderExtensionFactory,
        private OrderRepositoryInterface $orderRepository,
        private LoggerInterface $logger
    ) {
    }

    public function processOrder(int $orderId): void
    {
        try {
            $order = $this->orderRepository->get($orderId);
            $commentObject = $this->prepareOrderComment($order);
            $this->setOrderExtensionAttributes($order, $commentObject);
            $this->orderRepository->save($order);
        } catch (\Exception $e) {
            $this->logger->critical('Error processing order: ' . $e->getMessage());
        }
    }

    private function prepareOrderComment(OrderInterface $order): OrderCommentInterface
    {
        $commentText = $this->config->getCommentText((int)$order->getStoreId());
        $commentObject = $this->orderCommentFactory->create();
        $commentObject->setComment($commentText);
        return $commentObject;
    }

    private function setOrderExtensionAttributes(OrderInterface $order, OrderCommentInterface $commentObject): void
    {
        $extensionAttributes = $order->getExtensionAttributes();
        $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->orderExtensionFactory->create();
        $extensionAttributes->setComment($commentObject);
        $order->setExtensionAttributes($extensionAttributes);
    }
}
