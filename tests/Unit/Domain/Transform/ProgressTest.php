<?php

namespace AkeneoEtl\Tests\Unit\Domain\Transform;

use PHPUnit\Framework\TestCase;
use AkeneoEtl\Domain\Transform\Progress;

class ProgressTest extends TestCase
{
    public function test_it_should_be_created()
    {
        $progress = Progress::create(100);

        $this->assertEquals(0, $progress->current());
        $this->assertEquals(100, $progress->total());
    }

    public function test_it_should_advance()
    {
        $progress = Progress::create(100);
        $progress->advance();
        $progress->advance();

        $this->assertEquals(2, $progress->current());
        $this->assertEquals(100, $progress->total());
    }
}
