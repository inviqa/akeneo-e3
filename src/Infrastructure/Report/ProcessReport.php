<?php

namespace AkeneoE3\Infrastructure\Report;

use AkeneoE3\Domain\Result\Write;
use AkeneoE3\Domain\Result\Transform;

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

    public function add(Write\WriteResult $result): void
    {
        $this->total++;

        if ($result instanceof Write\TransformFailed) {
            $this->addTransformResult($result->getTransformResult());

            return;
        }

        if ($result instanceof Write\Loaded) {
            $this->loadedCount++;

            return;
        }

        if ($result instanceof Write\Failed) {
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

    private function addTransformResult(Transform\TransformResult $result): void
    {
        if (!$result instanceof Transform\Failed) {
            return;
        }

        $this->transformFailedCount++;

        $error = $result->getError();
        $currentCount = $this->transformErrorSummary[$error] ?? 0;
        $this->transformErrorSummary[$error] = $currentCount + 1;
    }
}
