<?php

declare(strict_types=1);

namespace AkeneoEtl\Application;

use AkeneoEtl\Domain\Action;
use AkeneoEtl\Application\Action as Actions;
use AkeneoEtl\Domain\Profile\TransformProfile;
use LogicException;
use AkeneoEtl\Application\Expression\ExpressionLanguage;

final class ActionFactory
{
    public function create(string $type, array $options): Action
    {
        switch ($type) {
            case 'set':
                $action = new Actions\Set(new ExpressionLanguage(), $options);
                break;
            case 'add':
                $action = new Actions\Add(new ExpressionLanguage(), $options);
                break;
            case 'remove':
                $action = new Actions\Remove(new ExpressionLanguage(), $options);
                break;
            default:
                throw new LogicException(sprintf('No registered action with the name %s', $type));
        }

        return $action;
    }

    /**
     * @return array|Action[]
     */
    public function createActions(TransformProfile $transformProfile): array
    {
        $actions = [];
        foreach ($transformProfile->getActions() as $actionOptions) {
            // @todo: throw exception if no type

            $type = $actionOptions['type'];
            unset($actionOptions['type']);
            $actions[] = $this->create($type, $actionOptions);
        }

        return $actions;
    }
}
