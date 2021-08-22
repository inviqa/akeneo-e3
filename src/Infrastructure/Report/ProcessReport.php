<?php

namespace AkeneoE3\Infrastructure\Report;

use AkeneoE3\Domain\Load\LoadResult;
use AkeneoE3\Domain\Transform\TransformResult;

class ProcessReport
{
    private int $total = 0;
    private int $transformFailedCount = 0;

    private int $loadedCount = 0;
    private int $loadFailedCount = 0;

    private array $transformErrorSummary = [];
    private array $loadErrorSummary = [];


    public function total(): int
    {
        return $this->total;
    }

    public function add(LoadResult\LoadResult $result): void
    {
        $this->total++;

        if ($result instanceof LoadResult\TransformFailed) {
            $this->addTransformResult($result->getTransformResult());

            return;
        }

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

    private function addTransformResult(TransformResult\TransformResult $result): void
    {
        if (!$result instanceof TransformResult\Failed) {
            return;
        }

        $this->transformFailedCount++;

        $error = $result->getError();
        $currentCount = $this->transformErrorSummary[$error] ?? 0;
        $this->transformErrorSummary[$error] = $currentCount + 1;
    }
}
