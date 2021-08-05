<?php

namespace AkeneoEtl\Tests\Unit\Application;

use AkeneoEtl\Application\SequentialTransformer;
use AkeneoEtl\Domain\Action;
use AkeneoEtl\Domain\Field;
use AkeneoEtl\Domain\Resource;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class SequentialTransformerTest extends TestCase
{
    public function test_it_transforms()
    {
        $transform = new SequentialTransformer([new FakeAction()]);

        $result = $transform->transform(Resource::fromArray(['identifier' => 123], 'product'));

        Assert::assertEquals([
            'identifier' => 123,
            'fake' => '!',
        ], $result);
    }

    public function test_it_throws_an_exception_if_action_fails()
    {
        $this->expectException(RuntimeException::class);

        $transform = new SequentialTransformer([new FailAction()]);

        $transform->transform(Resource::fromArray(['identifier' => 123], 'product'));
    }

    public function test_it_returns_null_if_no_actions_executed()
    {
        $transform = new SequentialTransformer([new NullAction()]);

        $result = $transform->transform(Resource::fromArray(['identifier' => 123], 'product'));

        Assert::assertNull($result);
    }
}

class FakeAction implements Action
{
    public function getType(): string
    {
        return 'fake';
    }

    public function execute(Resource $resource): void
    {
        $resource->set(Field::create('fake', []), '!', false);
    }
}

class FailAction implements Action
{
    public function getType(): string
    {
        return 'fail';
    }

    public function execute(Resource $resource): void
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

    public function execute(Resource $resource): void
    {
    }
}
