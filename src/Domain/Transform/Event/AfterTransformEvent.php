<?php

declare(strict_types=1);

namespace AkeneoE3\Domain\Transform\Event;

use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Transform\Progress;
use AkeneoE3\Domain\Transform\TransformResult\TransformResult;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class AfterTransformEvent extends Event
{
    private Progress $progress;

    private Resource $resource;

    private DateTimeInterface $dateTime;

    private TransformResult $transformResult;

    private function __construct(Progress $progress, Resource $resource, TransformResult $transformResult)
    {
        $this->progress = $progress;
        $this->resource = $resource;
        $this->transformResult = $transformResult;
        $this->dateTime = new DateTimeImmutable();
    }

    public static function create(Progress $progress, Resource $resource, TransformResult $transformResult): self
    {
        return new self($progress, $resource, $transformResult);
    }

    public function getProgress(): Progress
    {
        return $this->progress;
    }

    public function getResource(): \AkeneoE3\Domain\Resource\Resource
    {
        return $this->resource;
    }

    public function getDateTime(): DateTimeInterface
    {
        return $this->dateTime;
    }

    public function getTransformResult(): TransformResult
    {
        return $this->transformResult;
    }
}
