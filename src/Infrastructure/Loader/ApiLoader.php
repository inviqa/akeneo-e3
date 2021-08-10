<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Loader;

use Akeneo\Pim\ApiClient\Api\Operation\UpsertableResourceListInterface;
use AkeneoEtl\Domain\Exception\LoadException;
use AkeneoEtl\Domain\Load\LoadError;
use AkeneoEtl\Domain\Loader;
use AkeneoEtl\Domain\Resource\Resource;
use Traversable;

final class ApiLoader implements Loader
{
    private UpsertableResourceListInterface $api;

    private int $batchSize;

    private array $buffer = [];

    private string $codeFieldName = '';

    private string $resourceType = '';

    public function __construct(UpsertableResourceListInterface $api, int $batchSize = 100)
    {
        $this->api = $api;
        $this->batchSize = $batchSize;
    }

    /**
     * @throws \AkeneoEtl\Domain\Exception\LoadException
     */
    public function load(Resource $resource): void
    {
        if ($this->codeFieldName === '') {
            $this->codeFieldName = $resource->getCodeOrIdentifierFieldName();
            $this->resourceType = $resource->getResourceType();
        }

        $id = $resource->getCodeOrIdentifier();
        $this->buffer[$id] = $resource->toArray();

        if (count($this->buffer) >= $this->batchSize) {
            $this->finish();
        }
    }

    /**
     * @throws \AkeneoEtl\Domain\Exception\LoadException
     */
    public function finish(): void
    {
        $this->loadBatch();
    }

    /**
     * @throws \AkeneoEtl\Domain\Exception\LoadException
     */
    private function loadBatch(): void
    {
        if (count($this->buffer) === 0) {
            return;
        }

        $response = $this->api->upsertList($this->buffer);
        $this->processResponse($response);

        $this->buffer = [];
    }

    /**
     * @throws \AkeneoEtl\Domain\Exception\LoadException
     */
    private function processResponse(Traversable $result): void
    {
        $errors = [];

        foreach ($result as $line) {
            if ($line['status_code'] === 422) {
                $code = $line[$this->codeFieldName] ?? '';
                $initialItem = $this->buffer[$code] ?? [];

                $errors[] = LoadError::create(
                    $this->getErrorMessage($line),
                    Resource::fromArray($initialItem, $this->resourceType)
                );
            }
        }

        if (count($errors) > 0) {
            throw new LoadException('Batch upload errors', $errors);
        }
    }

    public function getErrorMessage(array $response): string
    {
        $messages = [$response['message'] ?? ''];

        foreach ($response['errors'] ?? [] as $error) {
            $messages[] = sprintf(
                ' - property: %s, attribute: %s, %s',
                $error['property'] ?? '?',
                $error['attribute'] ?? '?',
                $error['message'] ?? '?',
            );
        }

        return implode(PHP_EOL, $messages);
    }
}
