<?php

namespace AkeneoE3\Domain;

use AkeneoE3\Domain\Resource\WritableResource;
use AkeneoE3\Domain\Result\Transform\Failed;
use AkeneoE3\Domain\Result\Transform\TransformResult;
use AkeneoE3\Domain\Result\Write\WriteResult;
use AkeneoE3\Domain\Result\Write\Skipped;
use AkeneoE3\Domain\Result\Write\TransformFailed;
use AkeneoE3\Domain\Profile\LoadProfile;
use AkeneoE3\Domain\Repository\PersistRepository;
use LogicException;

class Loader
{
    private PersistRepository $repository;

    private LoadProfile $profile;

    public function __construct(PersistRepository $repository, LoadProfile $profile)
    {
        $this->repository = $repository;
        $this->profile = $profile;
    }

    /**
     * @param TransformResult[] $transformResults
     *
     * @returns WriteResult[]
     */
    public function load(iterable $transformResults): iterable
    {
        foreach ($transformResults as $transformResult) {
            $resource = $transformResult->getResource();

            if (!$resource instanceof WritableResource) {
                throw new LogicException('Resource must support WritableResource');
            }

            if ($transformResult instanceof Failed) {
                yield TransformFailed::create($resource, $transformResult);

                continue;
            }

            if ($resource->isChanged() === false) {
                yield Skipped::create($resource);

                continue;
            }

            yield from $this->repository->persist($resource);
        }

        yield from $this->repository->flush();
    }
}
