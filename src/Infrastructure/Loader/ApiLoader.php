<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Loader;

use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Akeneo\Pim\ApiClient\Api\Operation\UpsertableResourceListInterface;
use AkeneoEtl\Domain\Load\LoadResult\Failed;
use AkeneoEtl\Domain\Load\LoadResult\Loaded;
use AkeneoEtl\Domain\Load\LoadResult\LoadResult;
use AkeneoEtl\Domain\Loader;
use AkeneoEtl\Domain\Profile\EtlProfile;
use AkeneoEtl\Domain\Profile\LoadProfile;
use AkeneoEtl\Domain\Resource\Resource;
use AkeneoEtl\Infrastructure\Api\ApiSelector;
use LogicException;
use Traversable;

final class ApiLoader implements Loader
{
    private UpsertableResourceListInterface $api;

    private int $batchSize = 100;

    /**
     * @var array|Resource[]
     */
    private array $buffer = [];

    private string $codeFieldName = '';

    private string $resourceType = '';

    private LoadProfile $profile;

    private AkeneoPimClientInterface $client;

    private bool $isUpdateMode;

    public function __construct(LoadProfile $loadProfile, AkeneoPimClientInterface $client)
    {
        $this->profile = $loadProfile;
        $this->client = $client;
        $this->isUpdateMode = $this->profile->getMode() === EtlProfile::MODE_UPDATE;
    }

    public function load(Resource $resource): array
    {
        if ($this->codeFieldName === '') {
            $this->codeFieldName = $resource->getCodeOrIdentifierFieldName();
            $this->resourceType = $resource->getResourceType();

            $apiSelector = new ApiSelector();
            $this->api = $apiSelector->getApi($this->client, $this->resourceType);
        }

        if ($this->isUpdateMode === true && $resource->getOrigin() === null) {
            throw new LogicException('Resource must have origin for the update mode.');
        }

        if ($resource->isChanged() === false) {
            return [];
        }

        $id = $resource->getCodeOrIdentifier();
        $this->buffer[$id] = $resource;

        if (count($this->buffer) >= $this->batchSize) {
            return $this->loadBatch();
        }

        return [];
    }

    public function finish(): array
    {
        return $this->loadBatch();
    }

    /**
     * @return array|LoadResult[]
     */
    private function loadBatch(): array
    {
        if (count($this->buffer) === 0) {
            return [];
        }


        $isUpdateMode = $this->isUpdateMode;

        $list = array_map(
            function (Resource $resource) use ($isUpdateMode) {
                if ($isUpdateMode === true && $resource->getOrigin() !== null) {
                    $patch = $resource->diff($resource->getOrigin());

                    return $patch->toArray();
                }

                return $resource->toArray();
            },
            $this->buffer
        );

        $response = $this->api->upsertList($list);

        return $this->processResponse($response);
    }

    private function processResponse(Traversable $result): array
    {
        $loadResults = [];

        foreach ($result as $line) {
            // In some cases when a PATCH body is invalid,
            // Akeneo PHP client returns a response with null
            // instead of throwing an HTTP exception.
            if ($line === null) {
                throw new LogicException('Akeneo API error by PATCH: please check your rules.');
            }

            $code = $line[$this->codeFieldName];
            $resource = $this->buffer[$code];

            if ($line['status_code'] !== 422) {
                $loadResults[] = Loaded::create($resource);
                continue;
            }

            $error = $this->getErrorMessage($line);
            $loadResults[] = Failed::create($resource, $error);
        }

        $this->buffer = [];

        return $loadResults;
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
