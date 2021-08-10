<?php

namespace AkeneoEtl\Domain\Transform\Event;

use AkeneoEtl\Domain\Resource\Resource;
use Symfony\Contracts\EventDispatcher\Event;

class TransformErrorEvent extends Event
{
    private string $message;

    private \AkeneoEtl\Domain\Resource\Resource $resource;

    public function __construct(Resource $resource, string $message)
    {
        $this->resource = $resource;
        $this->message = $message;
    }

    public static function create(Resource $resource, string $message): self
    {
        return new self($resource, $message);
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getResource(): \AkeneoEtl\Domain\Resource\Resource
    {
        return $this->resource;
    }
}
