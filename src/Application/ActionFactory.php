<?php

declare(strict_types=1);

namespace AkeneoE3\Application;

use AkeneoE3\Domain\Action;
use AkeneoE3\Application\Action as Actions;
use AkeneoE3\Domain\Profile\TransformProfile;
use LogicException;
use AkeneoE3\Application\Expression\ExpressionLanguage;

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
            case 'duplicate':
                $action = new Actions\Duplicate($options);
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
