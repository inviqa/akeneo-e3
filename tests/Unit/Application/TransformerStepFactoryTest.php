<?php

namespace AkeneoEtl\Tests\Unit\Application;

use AkeneoEtl\Application\TransformerStepFactory;
use AkeneoEtl\Domain\TransformerStep;
use LogicException;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class TransformerStepFactoryTest extends TestCase
{
    public function test_it_creates_a_step()
    {
        $factory = new TransformerStepFactory();
        $step = $factory->create('set', []);

        Assert::assertInstanceOf(TransformerStep::class, $step);
    }

    public function test_it_throws_an_exception_if_step_not_registered()
    {
        $this->expectException(LogicException::class);

        $factory = new TransformerStepFactory();
        $factory->create('??? unknown ???', []);
    }
}
