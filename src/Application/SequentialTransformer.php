<?php

declare(strict_types=1);

namespace AkeneoEtl\Application;

use AkeneoEtl\Domain\Action;
use AkeneoEtl\Domain\Resource;
use AkeneoEtl\Domain\Transformer;

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

    public function transform(Resource $resource): \AkeneoEtl\Domain\Resource
    {
        $transformingResource = clone $resource;

        foreach ($this->actions as $action) {
            $action->execute($transformingResource);
        }

        return $transformingResource;
    }
}
