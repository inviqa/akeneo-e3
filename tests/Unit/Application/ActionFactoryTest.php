<?php

namespace AkeneoE3\Tests\Unit\Application;

use AkeneoE3\Application\ActionFactory;
use AkeneoE3\Domain\Action;
use LogicException;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ActionFactoryTest extends TestCase
{
    public function test_it_creates_a_action()
    {
        $factory = new ActionFactory();
        $action = $factory->create('set', [
            'field' => 'name',
            'value' => 'nemo',
        ]);

        Assert::assertInstanceOf(Action::class, $action);
    }

    public function test_it_throws_an_exception_if_action_not_registered()
    {
        $this->expectException(LogicException::class);

        $factory = new ActionFactory();
        $factory->create('??? unknown ???', []);
    }
}
