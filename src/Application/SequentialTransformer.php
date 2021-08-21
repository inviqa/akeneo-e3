<?php

declare(strict_types=1);

namespace AkeneoE3\Application;

use AkeneoE3\Domain\Action;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Transformer;

final class SequentialTransformer implements Transformer
{
    /**
     * @var iterable|Action[]
     */
    private iterable $actions;

    public function __construct(iterable $actions)
    {
        $this->actions = $actions;
    }

    public function transform(Resource $resource): void
    {
        foreach ($this->actions as $action) {
            $action->execute($resource);
        }
    }
}
