<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Loader\Api;

use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Akeneo\Pim\ApiClient\Api\Operation\UpsertableResourceListInterface;
use AkeneoE3\Domain\Load\LoadResult\Failed;
use AkeneoE3\Domain\Load\LoadResult\Loaded;
use AkeneoE3\Domain\Load\LoadResult\LoadResult;
use AkeneoE3\Domain\Loader;
use AkeneoE3\Domain\Profile\EtlProfile;
use AkeneoE3\Domain\Profile\LoadProfile;
use AkeneoE3\Domain\Resource\AuditableResource;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Infrastructure\Api\ApiSelector;
use LogicException;
use Traversable;

abstract class BaseBatchLoader implements Loader
{
    private int $batchSize = 100;

    /**
     * @var array|Resource[]
     */
    private array $buffer = [];

    private string $codeFieldName = '';

    private string $resourceType = '';

    private LoadProfile $profile;

    private bool $isUpdateMode;

    public function __construct(LoadProfile $loadProfile)
    {
        $this->profile = $loadProfile;
        $this->isUpdateMode = $this->profile->getUploadMode() === EtlProfile::MODE_UPDATE;
    }

    public function load(Resource $resource): array
    {
        if ($this->codeFieldName === '') {
            $this->codeFieldName = $resource->getCodeFieldName();
            $this->resourceType = $resource->getResourceType();
        }

        if ($resource->isChanged() === false) {
            return [];
        }

        $id = $resource->getCode();
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
     * @param array|Resource[] $list
     *
     * @return array
     */
    abstract protected function upsertList(array $list): iterable;

    /**
     * @return array|LoadResult[]
     */
    private function loadBatch(): array
    {
        if (count($this->buffer) === 0) {
            return [];
        }

        $response = $this->upsertList($this->buffer);

        return $this->processResponse($response);
    }

    private function processResponse(iterable $result): array
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

    private function getErrorMessage(array $response): string
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

    /**
     * @param array|Resource[] $buffer
     *
     * @return array
     */
    protected function prepareBufferToUpsert(array $buffer): array
    {
        $isUpdateMode = $this->isUpdateMode;

        $list = array_map(
            function (Resource $resource) use ($isUpdateMode) {
                if ($isUpdateMode === true && $resource instanceof AuditableResource) {
                    return $resource->changes()->toArray();
                }

                return $resource->toArray();
            },
            $buffer
        );

        return $list;
    }
}
