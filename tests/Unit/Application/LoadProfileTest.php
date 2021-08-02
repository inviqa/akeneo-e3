<?php

namespace AkeneoEtl\Tests\Unit\Application;

use AkeneoEtl\Domain\Profile\LoadProfile;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class LoadProfileTest extends TestCase
{
    public function test_profile_is_created_from_array()
    {
        $profile = LoadProfile::fromArray([
            'type' => 'dry-run',
        ]);

        Assert::assertEquals(true, $profile->isDryRun());
    }
}
