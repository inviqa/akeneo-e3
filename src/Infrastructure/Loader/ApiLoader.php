<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Loader;

use Akeneo\Pim\ApiClient\Api\Operation\UpsertableResourceListInterface;
use AkeneoEtl\Domain\Hook\LoaderError;
use AkeneoEtl\Domain\Hook\LoaderErrorHook;
use AkeneoEtl\Domain\Loader;
use AkeneoEtl\Domain\Resource;
use Exception;
use Traversable;

final class ApiLoader implements Loader
{
    private UpsertableResourceListInterface $api;

    private int $batchSize;

    private array $buffer = [];

    private LoaderErrorHook $onError;

    public function __construct(UpsertableResourceListInterface $api, LoaderErrorHook $onError, int $batchSize = 100)
    {
        $this->api = $api;
        $this->batchSize = $batchSize;
        $this->onError = $onError;
    }

    public function queue(Resource $resource): void
    {
        $id = $resource->getCodeOrIdentifier();
        $this->buffer[$id] = $resource->toArray();

        if (count($this->buffer) >= $this->batchSize) {
            $this->load();
        }
    }

    public function load(): void
    {
        if (count($this->buffer) === 0) {
            return;
        }

        $response = $this->api->upsertList($this->buffer);
        $this->processResponse($response);

        $this->buffer = [];
    }

    private function processResponse(Traversable $result): void
    {
        foreach ($result as $line) {
            if ($line['status_code'] === 422) {
                $initialItem = $this->buffer[$line['identifier']];

                try {
                    $this->onError->onLoaderError($initialItem, new LoaderError($line));
                } catch (Exception $e) {
                    // @todo: log exceptions
                }
            }
        }
    }
}
