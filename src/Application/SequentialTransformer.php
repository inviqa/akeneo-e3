<?php

declare(strict_types=1);

namespace AkeneoEtl\Application;

use AkeneoEtl\Domain\Action;
use AkeneoEtl\Domain\Resource;
use AkeneoEtl\Domain\Transformer;
use Exception;
use LogicException;
use Symfony\Component\ExpressionLanguage\SyntaxError;

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

        foreach ($this->actions as $actionId => $action) {
            try {
                $action->execute($transformingResource);
            } catch (SyntaxError $e) {
                throw $e;
            } catch (LogicException $e) {
                // @todo: stop if configured to stop internal exceptions
                // @todo: trigger onTransformerError
                print $e->getMessage().PHP_EOL;
            } catch (Exception $e) {
                throw $e;
            }
        }

        return $transformingResource;
    }
}
