<?php

namespace AkeneoEtl\Application;

use AkeneoEtl\Domain\Action;
use AkeneoEtl\Domain\Hook\ActionTraceHook;
use AkeneoEtl\Domain\Hook\EmptyHooks;
use AkeneoEtl\Domain\Transformer;
use Exception;

class SequentialTransformer implements Transformer
{
    /**
     * @var iterable|Action[]
     */
    private iterable $actions;

    private ActionTraceHook $traceHook;

    public function __construct(iterable $actions, ActionTraceHook $traceHook = null)
    {
        $this->actions = $actions;
        $this->traceHook = $traceHook ?? new EmptyHooks();
    }

    public function transform(array $item): ?array
    {
        $patch = [];
        $actionsExecuted = 0;

        foreach ($this->actions as $action) {
            try {
                $transformationResult = $action->execute($item, $this->traceHook);
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

        $patch['identifier'] = $item['identifier'];

        return $patch;
    }
}
