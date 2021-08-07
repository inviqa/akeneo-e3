<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Transform\Event;

use AkeneoEtl\Domain\Resource;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class BeforeTransformEvent extends Event
{
    private int $index;
    private int $total;

    private \AkeneoEtl\Domain\Resource $resource;

    private DateTimeInterface $dateTime;

    private function __construct(int $index, int $total, Resource $resource)
    {
        $this->index = $index;
        $this->total = $total;
        $this->resource = $resource;
        $this->dateTime = new DateTimeImmutable();
    }

    public static function create(int $index, int $total, Resource $resource): self
    {
        return new self($index, $total, $resource);
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getResource(): \AkeneoEtl\Domain\Resource
    {
        return $this->resource;
    }

    public function getDateTime(): DateTimeInterface
    {
        return $this->dateTime;
    }
}
