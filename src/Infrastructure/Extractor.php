<?php

namespace App\Infrastructure;

use Akeneo\Pim\ApiClient\Api\Operation\ListableResourceInterface;
use App\Domain\Extractor as DomainExtractor;
use Generator;

class Extractor implements DomainExtractor
{
    private ListableResourceInterface $api;

    /**
     * @var array
     */
    private array $query;

    public function __construct(ListableResourceInterface $api, array $query)
    {
        $this->api = $api;
        $this->query = $query;
    }

    public function count(): int
    {
        return $this->api
            ->listPerPage(1, true, $this->query)
            ->getCount();
    }

    public function extract(): Generator
    {
        $cursor = $this->api->all(100, $this->query);

        foreach ($cursor as $product) {
            yield $product;
        }
    }
}
