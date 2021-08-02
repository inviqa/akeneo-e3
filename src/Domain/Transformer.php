<?php

namespace AkeneoEtl\Domain;

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

    public function transform(array $item): array
    {
        $patch = [];

        foreach ($this->steps as $step) {
            try {
                $transformationResult = $step->transform($item);
            } catch (Exception $e) {
                throw($e);
            }

            // @todo: or SkipException? NonProcessableItemException?
            if ($transformationResult === null) {
                continue;
            }

            $patch = array_merge_recursive($patch, $transformationResult);
        }

        $patch['identifier'] = $item['identifier'];

        return $patch;
    }
}
