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

namespace Sugarcrm\IdentityProvider\Encoder;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * Encoder implementing php's crypt() hashing.
 * Supports legacy SOAP integrations with MD5 passwords
 */
class CryptLegacyMD5PasswordEncoder extends CryptPasswordEncoder
{
    /**
     * {@inheritdoc}
     */
    public function encodePassword($raw, $salt)
    {
        return parent::encodePassword(md5($raw), $salt);
    }
}
