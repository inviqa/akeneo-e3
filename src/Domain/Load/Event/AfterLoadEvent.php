<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Load\Event;

use AkeneoEtl\Domain\Load\LoadResult\LoadResult;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class AfterLoadEvent extends Event
{
    private DateTimeInterface $dateTime;

    /**
     * @var array|LoadResult[]
     */
    private array $loadResults;

    /**
     * @param array|LoadResult[] $loadResults
     */
    private function __construct(array $loadResults)
    {
        $this->loadResults = $loadResults;
        $this->dateTime = new DateTimeImmutable();
    }

    /**
     * @param array|LoadResult[] $loadResults
     */
    public static function create(array $loadResults): self
    {
        return new self($loadResults);
    }

    /**
     * @return array|LoadResult[]
     */
    public function getLoadResults(): array
    {
        return $this->loadResults;
    }

    public function getDateTime(): DateTimeInterface
    {
        return $this->dateTime;
    }
}
