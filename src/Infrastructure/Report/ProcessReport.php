<?php

namespace AkeneoEtl\Infrastructure\Report;

use AkeneoEtl\Domain\Resource\Resource;
use AkeneoEtl\Domain\Transform\TransformResult;
use AkeneoEtl\Domain\Load\LoadResult;

class ProcessReport
{
    private int $total = 0;
    private int $transformedCount = 0;
    private int $transformFailedCount = 0;

    private int $loadedCount = 0;
    private int $loadFailedCount = 0;

    private array $transformErrorSummary = [];
    private array $loadErrorSummary = [];


    public function total(): int
    {
        return $this->total;
    }

    public function add(Resource $fromCode)
    {
        $this->total++;
    }

    public function addTransformResult(TransformResult\TransformResult $result): void
    {
        if ($result instanceof TransformResult\Transformed) {
            $this->transformedCount++;

            return;
        }

        if ($result instanceof TransformResult\Failed) {
            $this->transformFailedCount++;

            $error = $result->getError();
            $currentCount = $this->transformErrorSummary[$error] ?? 0;
            $this->transformErrorSummary[$error] = $currentCount + 1;
        }
    }


    public function addLoadResult(LoadResult\LoadResult $result): void
    {
        if ($result instanceof LoadResult\Loaded) {
            $this->loadedCount++;

            return;
        }

        if ($result instanceof LoadResult\Failed) {
            $this->loadFailedCount++;

            $error = $result->getError();
            $currentCount = $this->loadErrorSummary[$error] ?? 0;
            $this->loadErrorSummary[$error] = $currentCount + 1;
        }
    }

    public function transformedCount(): int
    {
        return $this->transformedCount;
    }

    public function transformFailedCount(): int
    {
        return $this->transformFailedCount;
    }

    public function transformErrorSummary(): array
    {
        return $this->transformErrorSummary;
    }

    public function loadedCount(): int
    {
        return $this->loadedCount;
    }

    public function loadFailedCount(): int
    {
        return $this->loadFailedCount;
    }

    public function loadErrorSummary(): array
    {
        return $this->loadErrorSummary;
    }
}
