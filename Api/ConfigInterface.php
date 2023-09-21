<?php

declare(strict_types=1);

namespace Pg\OrderComment\Api;

interface ConfigInterface
{
    public function getCommentText(?int $storeId = null): string;
}
