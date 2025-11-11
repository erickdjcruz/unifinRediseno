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

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

/**
 * Builds encoder based on application settings
 * @see Mango/sugarcrm/src/Security/Password/Hash.php
 */
class EncoderBuilder
{
    const DEFAULT_BACKEND = 'native';
    const BACKEND_SHA2 = 'sha2';

    const DEFAULT_BCRYPT_COST = 10;

    /**
     * Get proper encoder according to the config. Config parameters names are identical to Mango's.
     *
     * @param array $config Configuration
     * @param bool  $legacy_md5_support Should we use legacy MD5 passwords\
     * @param bool  $strict_verify If false we'll try both plain text and MD5 hashed password for verification
     *
     * @return PasswordEncoderInterface
     */
    public function buildEncoder(array $config, bool $legacy_md5_support, bool $strict_verify = false): PasswordEncoderInterface
    {
        $backend = isset($config['passwordHash']['backend'])
            ? $config['passwordHash']['backend']
            : self::DEFAULT_BACKEND;
        $algo = isset($config['passwordHash']['algo'])
            ? $config['passwordHash']['algo']
            : null;
        $options = isset($config['passwordHash']['options'])
            ? $config['passwordHash']['options']
            : [];

        switch ($backend) {
            case self::BACKEND_SHA2:
                if ($legacy_md5_support) {
                    if ($strict_verify) {
                        $encoder = new CryptLegacyMD5StrictPasswordEncoder(
                            $algo,
                            isset($options['rounds']) ? $options['rounds'] : CryptPasswordEncoder::DEFAULT_ITERATIONS
                        );
                    } else {
                        $encoder = new CryptLegacyMD5PasswordEncoder(
                            $algo,
                            isset($options['rounds']) ? $options['rounds'] : CryptPasswordEncoder::DEFAULT_ITERATIONS
                        );
                    }
                } else {
                    $encoder = new CryptPasswordEncoder(
                        $algo,
                        isset($options['rounds']) ? $options['rounds'] : CryptPasswordEncoder::DEFAULT_ITERATIONS
                    );
                }
                break;
            case self::DEFAULT_BACKEND:
            default:
                switch ($algo) {
                    case null:
                    case PASSWORD_DEFAULT:
                    case PASSWORD_BCRYPT:
                        if ($legacy_md5_support) {
                            $encoder = new BCryptLegacyMD5PasswordEncoder(
                                $options['cost'] ?? self::DEFAULT_BCRYPT_COST
                            );
                        } else {
                            $encoder = new BCryptPasswordEncoder(
                                $options['cost'] ?? self::DEFAULT_BCRYPT_COST
                            );
                        }
                        break;
                }
                break;
        }
        return $encoder;
    }
}
