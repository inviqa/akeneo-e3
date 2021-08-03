<?php

namespace AkeneoEtl\Application;

use AkeneoEtl\Domain\Action;
use AkeneoEtl\Application\Action as Actions;
use AkeneoEtl\Domain\Hook\ActionTraceHook;
use AkeneoEtl\Domain\Hook\ActionTraceHookAware;
use AkeneoEtl\Domain\Profile\TransformProfile;
use LogicException;
use AkeneoEtl\Application\Expression\ExpressionLanguage;

class ActionFactory
{
    public function create(string $type, array $options, ActionTraceHook $traceHook = null): Action
    {
        // if deps needed, then clone from registry
        $action = null;

        switch ($type) {
            case 'set':
                $action = new Actions\Set(new ExpressionLanguage(), $options);
                break;
            case 'copy-all':
                $action = new Actions\CopyAll($options);
                break;
            default:
                throw new LogicException(sprintf('No registered action with the name %s', $type));
        }

        if ($traceHook !== null && $action instanceof ActionTraceHookAware) {
            $action->setHook($traceHook);
        }

        return $action;
    }

    /**
     * @return array|Action[]
     */
    public function createActions(TransformProfile $transformProfile, ActionTraceHook $traceHook): array
    {
        $actions = [];
        foreach ($transformProfile->getActions() as $actionData) {
            // @todo: throw exception if no type

            $type = $actionData['type'];
            $actions[] = $this->create($type, $actionData, $traceHook);
        }

        return $actions;
    }
}
