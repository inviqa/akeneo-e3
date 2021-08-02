<?php

namespace AkeneoEtl\Tests\Unit\Application;

use AkeneoEtl\Domain\EtlLoadProfile;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class EtlLoadProfileTest extends TestCase
{
    public function test_profile_is_created_from_array()
    {
        $profile = EtlLoadProfile::fromArray([
            'type' => 'dry-run',
        ]);

        Assert::assertEquals(true, $profile->isDryRun());
    }
}
