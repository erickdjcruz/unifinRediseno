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

namespace Sugarcrm\IdentityProvider\Tests\Unit\Mango;

use Sugarcrm\IdentityProvider\Mango\LocaleMapping;

class LocaleMappingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function mapProvider(): array
    {
        return [
            'empty' => ['', null],
            'regular' => ['de-DE', 'de_DE'],
            'short' => ['de', 'de_DE'],
            'custom_en' => ['en-US', 'en_us'],
            'custom_it' => ['it-IT', 'it_it'],
            'custom_it_short' => ['it', 'it_it'],
            'custom_it_weird' => ['it-iT', 'it_it'],
        ];
    }

    /**
     * @param array $source
     * @param array $expectedMapping
     *
     * @dataProvider mapProvider
     *
     * @covers ::map
     */
    public function testMap(string $locale, $expectedMangoLocale): void
    {
        $result = LocaleMapping::map($locale);
        $this->assertEquals($expectedMangoLocale, $result);
    }
}
