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

    public function transform(Resource $resource): \AkeneoEtl\Domain\Resource
    {
        $transformingResource = clone $resource;

        foreach ($this->actions as $action) {
            try {
                $action->execute($transformingResource);
            } catch (Exception $e) {
                // @todo: skip if configured to skip exceptions
                throw($e);
            }
        }

        return $transformingResource;
    }
}
