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

use Sugarcrm\IdentityProvider\Authentication\Token\OIDC\OIDCCodeToken;

/**
 * @coversDefaultClass \Sugarcrm\IdentityProvider\Authentication\Token\OIDC\OIDCCodeToken
 */
class OIDCCodeTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::getCredentials
     */
    public function testCredentials(): void
    {
        $code = 'code';
        $token = new OIDCCodeToken($code);
        $this->assertEquals($code, $token->getCredentials());
    }
}
