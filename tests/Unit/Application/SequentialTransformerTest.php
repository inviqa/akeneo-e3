<?php

namespace AkeneoE3\Tests\Unit\Application;

use AkeneoE3\Application\SequentialTransformer;
use AkeneoE3\Domain\Action;
use AkeneoE3\Domain\Resource\Property;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Resource\AuditableResource;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class SequentialTransformerTest extends TestCase
{
    public function test_it_transforms()
    {
        $transform = new SequentialTransformer([new FakeAction()]);
        $resource = AuditableResource::fromArray(['identifier' => 123], 'product');

        $transform->transform($resource);

        Assert::assertEquals([
            'identifier' => 123,
            'fake' => '!',
        ], $resource->toArray(false));
    }

    public function test_it_throws_an_exception_if_action_fails()
    {
        $this->expectException(RuntimeException::class);

        $transform = new SequentialTransformer([new FailAction()]);

        $transform->transform(AuditableResource::fromArray(['identifier' => 123], 'product'));
    }

    public function test_it_returns_an_empty_changeset_no_actions_executed()
    {
        $transform = new SequentialTransformer([new NullAction()]);
        $resource = AuditableResource::fromArray(['identifier' => 123], 'product');

        $transform->transform($resource);

        Assert::assertFalse($resource->isChanged());
        Assert::assertEquals([
            'identifier' => 123,
        ], $resource->toArray(false));
    }
}

class FakeAction implements Action
{
    public function execute(Resource $resource): void
    {
        $resource->set(Property::create('fake'), '!');
    }
}

class FailAction implements Action
{
    public function execute(Resource $resource): void
    {
        throw new RuntimeException('Ooops!');
    }
}

class NullAction implements Action
{
    public function execute(Resource $resource): void
    {
    }
}
