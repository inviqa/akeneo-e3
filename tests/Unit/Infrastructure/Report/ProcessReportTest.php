<?php

namespace AkeneoE3\Tests\Unit\Infrastructure\Report;

use AkeneoE3\Domain\Resource\AuditableResource;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Domain\Transform\TransformResult;
use AkeneoE3\Domain\Load\LoadResult;
use AkeneoE3\Infrastructure\Report\ProcessReport;
use PHPUnit\Framework\TestCase;

class ProcessReportTest extends TestCase
{
    public function test_it_is_empty_in_the_beginning()
    {
        $report = new ProcessReport();

        $this->assertEquals(0, $report->total());
        $this->assertEquals(0, $report->transformFailedCount());
        $this->assertCount(0, $report->transformErrorSummary());

        $this->assertEquals(0, $report->loadedCount());
        $this->assertEquals(0, $report->loadFailedCount());
        $this->assertCount(0, $report->loadErrorSummary());
    }

    public function test_it_return_total_amount_of_processed_resources()
    {
        $report = new ProcessReport();
        $resource = AuditableResource::fromCode('123', ResourceType::create('product'));

        $report->add(LoadResult\Loaded::create($resource));
        $report->add(LoadResult\Loaded::create($resource));

        $this->assertEquals(2, $report->total());
    }

    public function test_it_returns_transform_stats()
    {
        $report = new ProcessReport();
        $resource = AuditableResource::fromCode('123', ResourceType::create('product'));

        $report->add(LoadResult\TransformFailed::create($resource, TransformResult\Failed::create($resource, 'error1')));
        $report->add(LoadResult\TransformFailed::create($resource, TransformResult\Failed::create($resource, 'error1')));
        $report->add(LoadResult\TransformFailed::create($resource, TransformResult\Failed::create($resource, 'error2')));

        $this->assertEquals(3, $report->transformFailedCount());

        $this->assertEquals([
            'error1' => 2,
            'error2' => 1,
        ], $report->transformErrorSummary());
    }

    public function test_it_returns_load_stats()
    {
        $report = new ProcessReport();

        $resource = AuditableResource::fromCode('123', ResourceType::create('product'));

        $report->add(LoadResult\Loaded::create($resource));
        $report->add(LoadResult\Loaded::create($resource));
        $report->add(LoadResult\Failed::create($resource, 'error1'));
        $report->add(LoadResult\Failed::create($resource, 'error2'));
        $report->add(LoadResult\Failed::create($resource, 'error2'));

        $this->assertEquals(2, $report->loadedCount());
        $this->assertEquals(3, $report->loadFailedCount());

        $this->assertEquals([
            'error1' => 1,
            'error2' => 2,
        ], $report->loadErrorSummary());
    }
}
