<?php

namespace AkeneoEtl\Tests\Unit\Domain\Profile;

use AkeneoEtl\Domain\Profile\ExtractProfile;
use LogicException;
use PHPUnit\Framework\TestCase;

class ExtractProfileTest extends TestCase
{
    public function test_it_can_be_created_from_array()
    {
        $condition = [
            'field' => 'name',
            'operator' => '=',
            'value' => 'me',
        ];

        $profile = ExtractProfile::fromArray([
            'conditions' => [$condition]
        ]);

        $this->assertEquals([$condition], $profile->getConditions());
    }

    public function test_it_throws_an_exception_by_creation_from_invalid_data()
    {
        $condition = [
            'field' => 'name',
            'value' => 'me',
        ];

        $this->expectException(LogicException::class);

        ExtractProfile::fromArray([
            'conditions' => [$condition]
        ]);
    }
}
