<?php

namespace App\Infrastructure\Loader;

use Akeneo\Pim\ApiClient\Api\Operation\UpsertableResourceListInterface;
use App\Domain\Loader;
use Closure;
use Exception;
use Traversable;

class ApiLoader implements Loader
{
    private UpsertableResourceListInterface $api;

    private Closure $errorCallback;

    private int $batchSize;

    private array $buffer = [];

    public function __construct(UpsertableResourceListInterface $api, Closure $errorCallback, $batchSize = 100)
    {
        $this->api = $api;
        $this->errorCallback = $errorCallback;
        $this->batchSize = $batchSize;
    }

    public function addToBatch(array $item, bool $flush = false): void
    {
        $id = $item['identifier'];
        $this->buffer[$id] = $item;

        if ($flush === true || count($this->buffer) >= $this->batchSize) {
            $this->flushBatch();
        }
    }

    public function flushBatch(): void
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
        $errorCallbackClosure = $this->errorCallback;

        foreach ($result as $line) {
            if ($line['status_code'] === 422) {
                $initialItem = $this->buffer[$line['identifier']];

                try {
                    $errorCallbackClosure($initialItem, new LoaderError($line));
                } catch (Exception $e) {
                    // @todo: log exceptions
                }
            }
        }
    }
}
