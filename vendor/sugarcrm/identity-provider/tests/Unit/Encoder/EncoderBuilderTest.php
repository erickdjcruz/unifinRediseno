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

namespace Sugarcrm\IdentityProvider\Tests\Unit\Encoder;

use Sugarcrm\IdentityProvider\Encoder\BCryptLegacyMD5PasswordEncoder;
use Sugarcrm\IdentityProvider\Encoder\BCryptPasswordEncoder;
use Sugarcrm\IdentityProvider\Encoder\CryptLegacyMD5PasswordEncoder;
use Sugarcrm\IdentityProvider\Encoder\CryptLegacyMD5StrictPasswordEncoder;
use Sugarcrm\IdentityProvider\Encoder\CryptPasswordEncoder;
use Sugarcrm\IdentityProvider\Encoder\EncoderBuilder;

class EncoderBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function buildEncoderDataProvider()
    {
        return [
            'default' => [BCryptPasswordEncoder::class, [], false, false],
            'blowfish' => [BCryptPasswordEncoder::class, ['passwordHash' => ['backend' => 'native']], false, false],
            'sha2' => [
                CryptPasswordEncoder::class,
                ['passwordHash' => ['backend' => 'sha2', 'algo' => 'CRYPT_SHA256']],
                false,
                false,
            ],
            'blowfish_md5' => [
                BCryptLegacyMD5PasswordEncoder::class,
                ['passwordHash' => ['backend' => 'native']],
                true,
                false,
            ],
            'sha2_md5' => [
                CryptLegacyMD5PasswordEncoder::class,
                ['passwordHash' => ['backend' => 'sha2', 'algo' => 'CRYPT_SHA256']],
                true,
                false,
            ],
            'blowfish_md5_strict' => [
                BCryptLegacyMD5PasswordEncoder::class,
                ['passwordHash' => ['backend' => 'native']],
                true,
                true,
            ],
            'sha2_md5_strict' => [
                CryptLegacyMD5StrictPasswordEncoder::class,
                ['passwordHash' => ['backend' => 'sha2', 'algo' => 'CRYPT_SHA256']],
                true,
                true,
            ],
        ];
    }

    /**
     * @dataProvider buildEncoderDataProvider
     */
    public function testBuildEncoder($expectedEncoderType, $config, $legacy_md5_support, $strict_verify)
    {
        $encoderBuilder = new EncoderBuilder();
        $this->assertInstanceOf($expectedEncoderType, $encoderBuilder->buildEncoder($config, $legacy_md5_support, $strict_verify));
    }
}
