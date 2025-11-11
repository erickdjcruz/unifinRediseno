<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

namespace Sugarcrm\IdentityProvider\Tests\Unit\Authentication\UserProvider;

use Sugarcrm\IdentityProvider\Authentication\UserProvider\OIDCUserProvider;
use Sugarcrm\IdentityProvider\Authentication\User;

/**
 * @coversDefaultClass \Sugarcrm\IdentityProvider\Authentication\UserProvider\OIDCUserProvider
 */
class OIDCUserProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::supportsClass
     */
    public function testSupportsClass(): void
    {
        $provider = new OIDCUserProvider();
        $this->assertTrue($provider->supportsClass(User::class));
    }

    /**
     * @covers ::loadUserByUsername
     */
    public function testLoadUserByUsername(): void
    {
        $provider = new OIDCUserProvider();
        $user = $provider->loadUserByIdentifier('username');
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('username', $user->getUserIdentifier());
    }

    /**
     * @covers ::refreshUser
     */
    public function testRefreshUser(): void
    {
        $provider = new OIDCUserProvider();
        $user = new User('username');
        $user = $provider->refreshUser($user);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('username', $user->getUserIdentifier());
    }
}
