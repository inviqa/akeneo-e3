<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Load;

use AkeneoEtl\Domain\Resource;

final class LoadError
{
    private string $message;

    private \AkeneoEtl\Domain\Resource $resource;

    public function __construct(string $message, Resource $resource)
    {
        $this->message = $message;
        $this->resource = $resource;
    }

    public static function create(string $message, Resource $resource): self
    {
        return new self($message, $resource);
    }

    public function getIdentifier(): string
    {
        return $this->resource->getCodeOrIdentifier() ?? '';
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
