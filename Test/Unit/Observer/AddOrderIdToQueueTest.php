<?php

declare(strict_types=1);

namespace Pg\OrderComment\Test\Unit\Observer;

use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Sales\Model\Order;
use Pg\OrderComment\Observer\AddOrderIdToQueue;
use PHPUnit\Framework\TestCase;

class AddOrderIdToQueueTest extends TestCase
{
    private $publisherMock;

    private $observerInstance;

    protected function setUp(): void
    {
        $this->publisherMock = $this->createMock(PublisherInterface::class);

        $this->observerInstance = new AddOrderIdToQueue($this->publisherMock);
    }

    public function testExecutePublishesOrderId(): void
    {
        $orderId = 1;
        $orderMock = $this->createMock(Order::class);
        $eventMock = $this->getMockBuilder(Event::class)
            ->disableOriginalConstructor()
            ->addMethods(['getOrder'])
            ->getMock();

        $orderMock->expects($this->any())
            ->method('getId')
            ->willReturn($orderId);

        $eventMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($orderMock);

        $observerMock = $this->createMock(Observer::class);
        $observerMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);

        $this->publisherMock->expects($this->once())
            ->method('publish')
            ->with(AddOrderIdToQueue::TOPIC_NAME, $orderId);

        $this->observerInstance->execute($observerMock);
    }

    public function testExecuteDoesNotPublishWhenNoOrderId(): void
    {
        $orderMock = $this->createMock(Order::class);
        $eventMock = $this->getMockBuilder(Event::class)
            ->disableOriginalConstructor()
            ->addMethods(['getOrder'])
            ->getMock();

        $orderMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);

        $eventMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($orderMock);

        $observerMock = $this->createMock(Observer::class);
        $observerMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);

        $this->publisherMock->expects($this->never())
            ->method('publish');

        $this->observerInstance->execute($observerMock);
    }
}
