<?php

declare(strict_types=1);

namespace Pg\OrderComment\Model\Data;

use Pg\OrderComment\Api\Data\OrderCommentInterface;

class OrderComment implements OrderCommentInterface
{
    private ?string $comment = null;

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }
}
