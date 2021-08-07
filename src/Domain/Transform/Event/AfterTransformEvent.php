<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Transform\Event;

use AkeneoEtl\Domain\Resource;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class AfterTransformEvent extends Event
{
    private int $index;
    private int $total;

    private ?Resource $after;

    private ?Resource $before;

    private DateTimeInterface $dateTime;

    private function __construct(int $index, int $total, ?Resource $after, ?Resource $before)
    {
        $this->index = $index;
        $this->total = $total;
        $this->after = $after;
        $this->before = $before;
        $this->dateTime = new DateTimeImmutable();
    }

    public static function create(int $index, int $total, ?Resource $after, ?Resource $before): self
    {
        return new self($index, $total, $after, $before);
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getAfter(): ?\AkeneoEtl\Domain\Resource
    {
        return $this->after;
    }

    public function getBefore(): ?\AkeneoEtl\Domain\Resource
    {
        return $this->before;
    }

    public function getDateTime(): DateTimeInterface
    {
        return $this->dateTime;
    }
}
