<?php

namespace AkeneoEtl\Tests\Unit\Domain;

use AkeneoEtl\Domain\Transformer;
use AkeneoEtl\Domain\TransformerStep;
use Closure;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class TransformerTest extends TestCase
{
    public function test_it_transforms()
    {
        $transform = new Transformer([new TransformerFakeStep()]);

        $result = $transform->transform(['identifier' => 123], null);

        Assert::assertEquals([
            'data' => 'fake',
            'identifier' => 123,
        ], $result);
    }

    public function test_it_throws_an_exception_if_step_fails()
    {
        $this->expectException(RuntimeException::class);

        $transform = new Transformer([new TransformerFailingStep()]);

        $transform->transform(['identifier' => 123], null);
    }


    public function test_it_returns_null_if_no_steps_executed()
    {
        $transform = new Transformer([new TransformerNullStep()]);

        $result = $transform->transform(['identifier' => 123], null);

        Assert::assertNull($result);
    }
}

class TransformerFakeStep implements TransformerStep
{

    public function getType(): string
    {
        return 'fake';
    }

    public function transform(array $item, Closure $traceCallback = null): ?array
    {
        return ['data' => 'fake'];
    }
}

class TransformerFailingStep implements TransformerStep
{
    public function getType(): string
    {
        return 'fake';
    }

    public function transform(array $item, Closure $traceCallback = null): ?array
    {
        throw new RuntimeException('Ooops!');
    }
}

class TransformerNullStep implements TransformerStep
{
    public function getType(): string
    {
        return 'fake';
    }

    public function transform(array $item, Closure $traceCallback = null): ?array
    {
        return null;
    }
}

