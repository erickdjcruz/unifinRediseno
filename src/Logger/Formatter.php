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

namespace Sugarcrm\Sugarcrm\Logger;

use Monolog\Formatter\LineFormatter;

/**
 * SugarLogger-compatible log formatter
 */
class Formatter extends LineFormatter
{
    /**
     * Constructor
     *
     * @param string $dateFormat Date format
     */
    public function __construct(string $dateFormat)
    {
        parent::__construct(null, $dateFormat);
    }

    /**
     * {@inheritdoc}
     */
    public function format(array $record): string
    {
        global $current_user;

        if (!empty($current_user->id)) {
            $userId = $current_user->id;
        } else {
            $userId = '-none-';
        }

        // mute deprecation
        $time = @strftime($this->dateFormat);
        return $time
            . ' '
            . '[' . getmypid() . ']'
            . '[' . $userId . ']'
            . '[' . strtoupper($record['level_name']) . ']'
            . ' '
            . $this->stringify($record['message'])
            . "\n";
    }

    /**
     * {@inheritdoc}
     */
    protected function replaceNewlines($str): string
    {
        if ($this->allowInlineLineBreaks) {
            return $str;
        }

        return str_replace(["\r", "\n"], ['\r', '\n'], $str);
    }
}
