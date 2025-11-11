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

namespace Sugarcrm\IdentityProvider\Tests\Unit\Authentication\Token\OIDC;

use Sugarcrm\IdentityProvider\Authentication\Provider\Providers;
use Sugarcrm\IdentityProvider\Authentication\Token\OIDC\OIDCCodeToken;
use Sugarcrm\IdentityProvider\Authentication\Token\OIDC\ResultToken;

/**
 * @coversDefaultClass \Sugarcrm\IdentityProvider\Authentication\Token\OIDC\ResultToken
 */
class ResultTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::getCredentials
     */
    public function testGetCredentials(): void
    {
        $attributes = ['attr1' => 'a1', 'attr2' => 'a2'];
        $sourceToken = new OIDCCodeToken('code');
        $sourceToken->setAttributes($attributes);
        $resultToken = new ResultToken($sourceToken->getCredentials(), $sourceToken->getAttributes());
        $this->assertEquals('code', $resultToken->getCredentials());
        $this->assertEquals($attributes, $resultToken->getAttributes());
    }

    /**
     * @covers ::getProviderKey
     */
    public function testGetProviderKey(): void
    {
        $resultToken = new ResultToken('code', []);
        $this->assertEquals(Providers::PROVIDER_KEY_OIDC, $resultToken->getProviderKey());
    }
}
