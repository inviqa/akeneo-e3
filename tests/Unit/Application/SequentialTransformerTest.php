<?php

namespace AkeneoEtl\Tests\Unit\Application;

use AkeneoEtl\Application\SequentialTransformer;
use AkeneoEtl\Domain\Hook\ActionTraceHook;
use AkeneoEtl\Domain\Action;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class SequentialTransformerTest extends TestCase
{
    public function test_it_transforms()
    {
        $transform = new SequentialTransformer([new FakeAction()]);

        $result = $transform->transform(['identifier' => 123]);

        Assert::assertEquals([
            'data' => 'fake',
            'identifier' => 123,
        ], $result);
    }

    public function test_it_throws_an_exception_if_action_fails()
    {
        $this->expectException(RuntimeException::class);

        $transform = new SequentialTransformer([new FailAction()]);

        $transform->transform(['identifier' => 123]);
    }

    public function test_it_returns_null_if_no_actions_executed()
    {
        $transform = new SequentialTransformer([new NullAction()]);

        $result = $transform->transform(['identifier' => 123]);

        Assert::assertNull($result);
    }
}

class FakeAction implements Action
{
    public function getType(): string
    {
        return 'fake';
    }

    public function execute(array $item, ActionTraceHook $tracer = null): ?array
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

    public function execute(array $item, ActionTraceHook $tracer = null): ?array
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

    public function execute(array $item, ActionTraceHook $tracer = null): ?array
    {
        return null;
    }
}
