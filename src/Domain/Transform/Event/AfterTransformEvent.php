<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Transform\Event;

use AkeneoEtl\Domain\Resource;
use AkeneoEtl\Domain\Transform\Progress;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class AfterTransformEvent extends Event
{
    private Progress $progress;

    private ?Resource $after;

    private ?Resource $before;

    private DateTimeInterface $dateTime;

    private function __construct(Progress $progress, ?Resource $after, ?Resource $before)
    {
        $this->progress = $progress;
        $this->after = $after;
        $this->before = $before;
        $this->dateTime = new DateTimeImmutable();
    }

    public static function create(Progress $progress, ?Resource $after, ?Resource $before): self
    {
        return new self($progress, $after, $before);
    }

    public function getProgress(): Progress
    {
        return $this->progress;
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
