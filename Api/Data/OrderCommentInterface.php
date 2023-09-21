<?php

declare(strict_types=1);

namespace Pg\OrderComment\Api\Data;

interface OrderCommentInterface
{
    public const COMMENT = 'comment';

    public function getComment(): ?string;

    public function setComment(?string $comment): void;
}
