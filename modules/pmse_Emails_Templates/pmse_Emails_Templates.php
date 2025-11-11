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


class pmse_Emails_Templates extends pmse_Emails_Templates_sugar
{
    protected array $fillable = ['base_module'];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Clean string from potential XSS problems
     * @param string $content
     * @param bool $encoded
     * @return string
     */
    public function cleanContent($content, $encoded = false)
    {
        $clearContent = parent::cleanContent($content, $encoded);
        return static::restoreRecordLinks($clearContent);
    }

    /**
     * Replace codes of braces with symbols
     * @param string $html
     * @return string
     */
    public static function restoreRecordLinks($html)
    {
        preg_match_all('/%7B::(.*?)::%7D/', (string)$html, $match);
        foreach ($match[1] as $value) {
            $html = str_replace('%7B::' . $value . '::%7D', '{::' . $value . '::}', $html);
        }

        return $html;
    }
}
