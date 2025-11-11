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

use Sugarcrm\IdentityProvider\Encoder\CryptLegacyMD5StrictPasswordEncoder;

class CryptLegacyMD5StrictPasswordEncoderTest extends \PHPUnit_Framework_TestCase
{
    public function isPasswordValidDataProvider()
    {
        return [
            'sha256_plain_text_is_not_valid' => [
                false,
                'CRYPT_SHA256',
                '$5$rounds=5000$1234567812345678$s4SFagv7XHmUocBTR44o5tOUhpzRYKkWE46J7OUv9N.',
                'secret',
                '1234567812345678'
            ],
            'sha256_md5_valid' => [
                true,
                'CRYPT_SHA256',
                '$5$rounds=5000$1234567812345678$s4SFagv7XHmUocBTR44o5tOUhpzRYKkWE46J7OUv9N.',
                md5('secret'),
                '1234567812345678'
            ],
        ];
    }

    /**
     * @dataProvider isPasswordValidDataProvider
     */
    public function testIsPasswordValid($expected, $algo, $hash, $secret, $salt)
    {
        $encoder = new CryptLegacyMD5StrictPasswordEncoder($algo);
        $this->assertEquals($expected, $encoder->isPasswordValid($hash, $secret, $salt));
    }
}
