<?php

declare(strict_types=1);

namespace Pg\OrderComment\Test\Unit\Model;

use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Pg\OrderComment\Api\ConfigInterface;
use Pg\OrderComment\Api\Data\OrderCommentInterface;
use Pg\OrderComment\Api\Data\OrderCommentInterfaceFactory;
use Pg\OrderComment\Model\OrderProcessor;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class OrderProcessorTest extends TestCase
{
    private $configMock;
    private $orderRepositoryMock;
    private $orderExtensionFactoryMock;
    private $orderCommentFactoryMock;
    private $loggerMock;

    private $orderProcessor;

    protected function setUp(): void
    {
        $this->configMock = $this->createMock(ConfigInterface::class);
        $this->orderCommentFactoryMock = $this->createMock(OrderCommentInterfaceFactory::class);
        $this->orderExtensionFactoryMock = $this->createMock(OrderExtensionFactory::class);
        $this->orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->orderProcessor = new OrderProcessor(
            $this->configMock,
            $this->orderCommentFactoryMock,
            $this->orderExtensionFactoryMock,
            $this->orderRepositoryMock,
            $this->loggerMock
        );
    }

    public function testProcessOrder(): void
    {
        $orderId = 123;
        $storeId = 1;
        $commentText = 'Special Comment';

        $orderMock = $this->createMock(OrderInterface::class);
        $orderCommentMock = $this->createMock(OrderCommentInterface::class);
        $extensionAttributesMock = $this->createMock(OrderExtensionInterface::class);

        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->with($orderId)
            ->willReturn($orderMock);

        $this->configMock->expects($this->once())
            ->method('getCommentText')
            ->with($storeId)
            ->willReturn($commentText);

        $orderMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);

        $this->orderCommentFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($orderCommentMock);

        $orderCommentMock->expects($this->once())
            ->method('setComment')
            ->with($commentText);

        $orderMock->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn(null);

        $this->orderExtensionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($extensionAttributesMock);

        $extensionAttributesMock->expects($this->once())
            ->method('setComment')
            ->with($orderCommentMock);

        $orderMock->expects($this->once())
            ->method('setExtensionAttributes')
            ->with($extensionAttributesMock);

        $this->orderRepositoryMock->expects($this->once())
            ->method('save')
            ->with($orderMock);

        $this->orderProcessor->processOrder($orderId);
    }
}
