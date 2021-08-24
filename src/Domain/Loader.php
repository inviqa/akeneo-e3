<?php

namespace AkeneoE3\Domain;

use AkeneoE3\Domain\Result\Transform\Failed;
use AkeneoE3\Domain\Result\Transform\TransformResult;
use AkeneoE3\Domain\Result\Write\WriteResult;
use AkeneoE3\Domain\Result\Write\Skipped;
use AkeneoE3\Domain\Result\Write\TransformFailed;
use AkeneoE3\Domain\Profile\EtlProfile;
use AkeneoE3\Domain\Profile\LoadProfile;
use AkeneoE3\Domain\Repository\PersistRepository;

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
        $patch = !$this->profile->isDuplicateMode();

        foreach ($transformResults as $transformResult) {
            $resource = $transformResult->getResource();

            if ($transformResult instanceof Failed) {
                yield TransformFailed::create($resource, $transformResult);

                continue;
            }

            if ($resource->isChanged() === false && $this->profile->getUploadMode() === EtlProfile::MODE_UPDATE) {
                yield Skipped::create($resource);

                continue;
            }

            yield from $this->repository->persist($resource, $patch);
        }

        yield from $this->repository->flush($patch);
    }
}
