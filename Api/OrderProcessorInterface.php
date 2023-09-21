<?php

declare(strict_types=1);

namespace Pg\OrderComment\Api;

interface OrderProcessorInterface
{
    public function processOrder(int $orderId): void;
}
