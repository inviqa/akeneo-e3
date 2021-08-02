<?php

namespace AkeneoEtl\Tests\Unit\Domain;

use AkeneoEtl\Domain\Transformer;
use AkeneoEtl\Domain\Action;
use Closure;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class TransformerTest extends TestCase
{
    public function test_it_transforms()
    {
        $transform = new Transformer([new FakeAction()]);

        $result = $transform->transform(['identifier' => 123], null);

        Assert::assertEquals([
            'data' => 'fake',
            'identifier' => 123,
        ], $result);
    }

    public function test_it_throws_an_exception_if_action_fails()
    {
        $this->expectException(RuntimeException::class);

        $transform = new Transformer([new FailAction()]);

        $transform->transform(['identifier' => 123], null);
    }

    public function test_it_returns_null_if_no_actions_executed()
    {
        $transform = new Transformer([new NullAction()]);

        $result = $transform->transform(['identifier' => 123], null);

        Assert::assertNull($result);
    }
}

class FakeAction implements Action
{
    public function getType(): string
    {
        return 'fake';
    }

    public function execute(array $item, Closure $traceCallback = null): ?array
    {
        return ['data' => 'fake'];
    }
}

class FailAction implements Action
{
    public function getType(): string
    {
        return 'fail';
    }

    public function execute(array $item, Closure $traceCallback = null): ?array
    {
        throw new RuntimeException('Ooops!');
    }
}

class NullAction implements Action
{
    public function getType(): string
    {
        return 'null';
    }

    public function execute(array $item, Closure $traceCallback = null): ?array
    {
        return null;
    }
}

