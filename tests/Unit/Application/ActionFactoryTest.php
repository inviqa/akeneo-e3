<?php

namespace AkeneoEtl\Tests\Unit\Application;

use AkeneoEtl\Application\ActionFactory;
use AkeneoEtl\Domain\Action;
use LogicException;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ActionFactoryTest extends TestCase
{
    public function test_it_creates_a_action()
    {
        $factory = new ActionFactory(new EventDispatcher());
        $action = $factory->create('copy-all', []);

        Assert::assertInstanceOf(Action::class, $action);
    }

    public function test_it_throws_an_exception_if_action_not_registered()
    {
        $this->expectException(LogicException::class);

        $factory = new ActionFactory(new EventDispatcher());
        $factory->create('??? unknown ???', []);
    }
}
