<?php

namespace AkeneoE3\Domain;

use AkeneoE3\Domain\Exception\TransformException;
use AkeneoE3\Domain\Resource\TransformableResource;
use AkeneoE3\Domain\Result\Transform\Failed;
use AkeneoE3\Domain\Result\Transform\Transformed;
use AkeneoE3\Domain\Result\Transform\TransformResult;

class Transformer
{
    /**
     * @var iterable|Action[]
     */
    private iterable $actions;

    public function __construct(iterable $actions)
    {
        $this->actions = $actions;
    }

    /**
     * @param TransformableResource[] $resources
     *
     * @return TransformResult[]
     */
    public function transform(iterable $resources): iterable
    {
        foreach ($resources as $resource) {
            $result = Transformed::create($resource);

            try {
                foreach ($this->actions as $action) {
                    $action->execute($resource);
                }
            } catch (TransformException $e) {
                $result = Failed::create($resource, $e->getMessage());

                if ($e->canBeSkipped() === false) {
                    throw $e;
                }
            }
            yield $result;
        }
    }
}
