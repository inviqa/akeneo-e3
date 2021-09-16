<?php

declare(strict_types=1);

namespace AkeneoE3\Application;

use AkeneoE3\Application\Expression\ExpressionEvaluator;
use AkeneoE3\Domain\Action;
use AkeneoE3\Application\Action as Actions;
use AkeneoE3\Domain\Profile\TransformProfile;
use LogicException;

final class ActionFactory
{
    private ExpressionEvaluator $expressionLanguage;

    public function __construct(ExpressionEvaluator $expressionLanguage)
    {
        $this->expressionLanguage = $expressionLanguage;
    }

    public function create(string $type, array $options): Action
    {
        switch ($type) {
            case 'set':
                $action = new Actions\Set($this->expressionLanguage, $options);
                break;
            case 'add':
                $action = new Actions\Add($this->expressionLanguage, $options);
                break;
            case 'remove':
                $action = new Actions\Remove($this->expressionLanguage, $options);
                break;
            case 'duplicate':
                $action = new Actions\Duplicate($this->expressionLanguage, $options);
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
