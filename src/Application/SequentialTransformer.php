<?php

namespace AkeneoEtl\Application;

use AkeneoEtl\Domain\Action;
use AkeneoEtl\Domain\Resource;
use AkeneoEtl\Domain\Transformer;
use Exception;

class SequentialTransformer implements Transformer
{
    /**
     * @var iterable|Action[]
     */
    private iterable $actions;

    public function __construct(iterable $actions)
    {
        $this->actions = $actions;
    }

    public function transform(Resource $resource): ?array
    {
        $patch = [];
        $actionsExecuted = 0;

        foreach ($this->actions as $action) {
            try {
                $transformationResult = $action->execute($resource);
            } catch (Exception $e) {
                // @todo: skip if configured to skip exceptions
                throw($e);
            }

            // @todo: or SkipException? NonProcessableItemException?
            if ($transformationResult === null) {
                continue;
            }

            $actionsExecuted++;
            $patch = array_merge_recursive($patch, $transformationResult);
        }

        // if no changes - skip
        if ($actionsExecuted === 0) {
            return null;
        }

        // @todo: store to 'code' if not product
        $patch['identifier'] = $resource->getCodeOrIdentifier();

        return $patch;
    }
}
