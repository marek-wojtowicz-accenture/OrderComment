<?php

declare(strict_types=1);

namespace Pg\OrderComment\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\MessageQueue\PublisherInterface;

class AddOrderIdToQueue implements ObserverInterface
{
    public const TOPIC_NAME = 'pg.order.place.after';

    public function __construct(private PublisherInterface $publisher)
    {
    }

    public function execute(Observer $observer): void
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        if ($order && $order->getId()) {
            $this->publisher->publish(self::TOPIC_NAME, $order->getId());
        }
    }
}
