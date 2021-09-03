<?php

namespace AkeneoE3\Tests\Unit\Application\Action;

use AkeneoE3\Application\Action\DuplicateOptions;
use LogicException;
use PHPUnit\Framework\TestCase;

class DuplicateOptionsTest extends TestCase
{
    public function test_resolve_options()
    {
        $options = DuplicateOptions::fromArray([
            'include_fields' => ['a'],
        ]);

        $this->assertEquals(['a'], $options->getIncludeFieldNames());
        $this->assertEquals([], $options->getExcludeFieldNames());
    }

    public function test_fail_if_included_and_excluded_fields_are_set_in_the_same_time()
    {
        $this->expectException(LogicException::class);

        DuplicateOptions::fromArray([
            'include_fields' => ['a'],
            'exclude_fields' => ['a'],
        ]);
    }
}
