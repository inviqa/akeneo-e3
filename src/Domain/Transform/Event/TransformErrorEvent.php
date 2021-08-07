<?php

namespace AkeneoEtl\Domain\Transform\Event;

use Symfony\Contracts\EventDispatcher\Event;

class TransformErrorEvent extends Event
{
    private string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public static function create(string $message): self
    {
        return new self($message);
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
