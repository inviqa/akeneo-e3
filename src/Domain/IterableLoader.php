<?php

namespace AkeneoE3\Domain;

use AkeneoE3\Domain\Load\LoadResult\LoadResult;
use AkeneoE3\Domain\Load\LoadResult\Skipped;
use AkeneoE3\Domain\Load\LoadResult\TransformFailed;
use AkeneoE3\Domain\Profile\EtlProfile;
use AkeneoE3\Domain\Profile\LoadProfile;
use AkeneoE3\Domain\Repository\WriteRepository;
use AkeneoE3\Domain\Transform\TransformResult\Failed;
use AkeneoE3\Domain\Transform\TransformResult\TransformResult;

class IterableLoader
{
    private WriteRepository $repository;

    private LoadProfile $profile;

    public function __construct(WriteRepository $repository, LoadProfile $profile)
    {
        $this->repository = $repository;
        $this->profile = $profile;
    }

    /**
     * @param TransformResult[] $transformResults
     *
     * @returns LoadResult[]
     */
    public function load(iterable $transformResults): iterable
    {
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

            yield from $this->repository->persist($resource, !$this->profile->isDuplicateMode());
        }

        yield from $this->repository->flush(!$this->profile->isDuplicateMode());
    }
}
