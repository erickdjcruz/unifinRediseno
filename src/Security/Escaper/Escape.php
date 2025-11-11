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

namespace Sugarcrm\Sugarcrm\Security\Escaper;

use Laminas\Escaper\Escaper;

class Escape
{
    private static ?Escaper $escaper = null;

    private static function init(): void
    {
        if (!self::$escaper) {
            self::$escaper = new Escaper('utf-8');
        }
    }

    public static function html(string $html): string
    {
        self::init();

        return self::$escaper->escapeHtml($html);
    }

    public static function htmlAttr(string $htmlAttr): string
    {
        self::init();

        return self::$escaper->escapeHtmlAttr($htmlAttr);
    }

    public static function js(string $js): string
    {
        self::init();

        return self::$escaper->escapeJs($js);
    }

    public static function css(string $css): string
    {
        self::init();

        return self::$escaper->escapeCss($css);
    }

    public static function url(string $url): string
    {
        self::init();

        return self::$escaper->escapeUrl($url);
    }
}
