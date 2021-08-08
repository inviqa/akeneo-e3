<?php

namespace AkeneoEtl\Tests\Unit\Domain\Profile;

use AkeneoEtl\Domain\Profile\ConnectionProfile;
use LogicException;
use PHPUnit\Framework\TestCase;

class ConnectionProfileTest extends TestCase
{
    public function test_it_can_be_created_from_array()
    {
        $connection = [
            'host' => 'http://ziggy',
            'clientId' => 'id',
            'clientSecret' => 'secret',
            'userName' => 'ziggy',
            'userPassword' => 'oH~My^zÏ99y/',
        ];

        $profile = ConnectionProfile::fromArray($connection);

        $this->assertEquals('http://ziggy', $profile->getHost());
        $this->assertEquals('id', $profile->getClientId());
        $this->assertEquals('secret', $profile->getClientSecret());
        $this->assertEquals('ziggy', $profile->getUserName());
        $this->assertEquals('oH~My^zÏ99y/', $profile->getUserPassword());
    }

    public function test_it_throws_an_exception_by_creation_from_invalid_data()
    {
        $connection = [
            'host' => 'http://ziggy',
            'id' => 'id',
        ];

        $this->expectException(LogicException::class);

        ConnectionProfile::fromArray($connection);
    }
}
