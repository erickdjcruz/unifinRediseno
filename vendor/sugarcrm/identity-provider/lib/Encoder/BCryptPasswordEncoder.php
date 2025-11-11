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

use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;
use Symfony\Component\Security\Core\Encoder\LegacyEncoderTrait;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\SelfSaltingEncoderInterface;

class BCryptPasswordEncoder implements PasswordEncoderInterface, SelfSaltingEncoderInterface
{
    use LegacyEncoderTrait;

    public function __construct(int $cost = null)
    {
        $this->hasher = new NativePasswordHasher(null, null, $cost, \PASSWORD_BCRYPT);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $encoded An encoded password
     * @param string $raw     A raw password
     * @param string $salt    Salt parameter is ignored for SHA-2 as it's stored directly in the hash
     */
    public function isPasswordValid(string $encoded, string $raw, ?string $salt): bool
    {
        return $this->hasher->verify($encoded, $raw, $salt) || $this->hasher->verify($encoded, md5($raw), $salt);
    }
}
