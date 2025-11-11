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

namespace Sugarcrm\IdentityProvider\Mango;

/**
 * Converts RFC locale to the format used by Mango
 */
class LocaleMapping
{
    /**
     * Non-standard mappings
     * @var array
     */
    protected static $nonStandardMappings = [
        'en_us' => 'en_us',
        'it_it' => 'it_it',
    ];

    /**
     * Converts RFC locale to the format used by Mango
     *
     * @param string $locale RFC compliant locale
     *
     * @return Locale in Mango format
     */
    public static function map(string $locale) :?string
    {
        if (empty($locale)) {
            return null;
        }

        $localeParts = explode('-', $locale);

        if (count($localeParts) === 1) {
            $localeParts[1] = strtoupper($localeParts[0]);
        }

        $mangoLocale = implode('_', $localeParts);

        if (array_key_exists(strtolower($mangoLocale), self::$nonStandardMappings)) {
            $mangoLocale = self::$nonStandardMappings[strtolower($mangoLocale)];
        }
        return $mangoLocale;
    }
}
