<?php

declare(strict_types=1);

namespace AkeneoEtl\Application;

use AkeneoEtl\Domain\Action;
use AkeneoEtl\Application\Action as Actions;
use AkeneoEtl\Domain\Profile\TransformProfile;
use AkeneoEtl\Domain\Transform\Event\BeforeTransformEvent;
use LogicException;
use AkeneoEtl\Application\Expression\ExpressionLanguage;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ActionFactory
{
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $eventDispatcher->addListener(
            BeforeTransformEvent::class,
            function (BeforeTransformEvent $event) {
                CurrentResourceHolder::$current = $event->getResource();
            }
        );
    }

    public function create(string $type, array $options): Action
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
