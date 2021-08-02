<?php

namespace AkeneoEtl\Domain;

use Closure;
use Exception;

class Transformer
{
    /**
     * @var iterable|TransformerStep[]
     */
    private iterable $steps;

    public function __construct(iterable $transformerSteps)
    {
        $this->steps = $transformerSteps;
    }

    public function transform(array $item, Closure $traceCallBack): ?array
    {
        $patch = [];
        $stepsExecuted = 0;

        foreach ($this->steps as $step) {
            try {
                $transformationResult = $step->transform($item, $traceCallBack);
            } catch (Exception $e) {
                // @todo: skip if configured to skip exceptions
                throw($e);
            }

            // @todo: or SkipException? NonProcessableItemException?
            if ($transformationResult === null) {
                continue;
            }

            $stepsExecuted++;
            $patch = array_merge_recursive($patch, $transformationResult);
        }

        // if no changes - skip
        if ($stepsExecuted === 0) {
            return null;
        }

        $patch['identifier'] = $item['identifier'];

        return $patch;
    }
}
