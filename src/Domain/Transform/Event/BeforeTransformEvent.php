<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Transform\Event;

use AkeneoEtl\Domain\Resource\Resource;
use AkeneoEtl\Domain\Transform\Progress;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class BeforeTransformEvent extends Event
{
    private Progress $progress;

    private \AkeneoEtl\Domain\Resource\Resource $resource;

    private DateTimeInterface $dateTime;

    private function __construct(Progress $progress, Resource $resource)
    {
        $this->resource = $resource;
        $this->dateTime = new DateTimeImmutable();
        $this->progress = $progress;
    }

    public static function create(Progress $progress, Resource $resource): self
    {
        return new self($progress, $resource);
    }

    public function getProgress(): Progress
    {
        return $this->progress;
    }

    public function getResource(): \AkeneoEtl\Domain\Resource\Resource
    {
        return $this->resource;
    }

    public function getDateTime(): DateTimeInterface
    {
        return $this->dateTime;
    }
}
