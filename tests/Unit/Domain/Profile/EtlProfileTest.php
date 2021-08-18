<?php

namespace AkeneoEtl\Tests\Unit\Domain\Profile;

use AkeneoEtl\Domain\Profile\EtlProfile;
use LogicException;
use PHPUnit\Framework\TestCase;

class EtlProfileTest extends TestCase
{
    public function test_it_can_be_created_from_array()
    {
        $condition = [
            'field' => 'name',
            'operator' => '=',
            'value' => 'me',
        ];

        $action = [
            'type' => 'set',
            'field' => 'family',
            'value' => 'pet',
        ];

        $profile = EtlProfile::fromArray([
            'upload-type' => 'dry-run',
            'conditions' => [$condition],
            'actions' => [$action]
        ]);

        $this->assertEquals(true, $profile->isDryRun());
        $this->assertEquals([$condition], $profile->getConditions());
        $this->assertEquals([$action], $profile->getActions());
    }

    public function test_it_throws_an_exception_by_creation_from_invalid_data()
    {
        $this->expectException(LogicException::class);

        EtlProfile::fromArray([
            'type' => 'run',
            'unknown' => [],
        ]);
    }

    public function test_it_allows_to_dynamically_set_dry_run_list_of_codes()
    {
        $condition = [
            'field' => 'name',
            'operator' => '=',
            'value' => 'me',
        ];

        $action = [
            'type' => 'set',
            'field' => 'family',
            'value' => 'pet',
        ];

        $profile = EtlProfile::fromArray([
            'conditions' => [$condition],
            'actions' => [$action]
        ]);

        $profile->setDryRunCodes(['123', '456', 'abc']);

        $this->assertEquals(['123', '456', 'abc'], $profile->getDryRunCodes());
        $this->assertTrue($profile->isDryRun());
    }
}
