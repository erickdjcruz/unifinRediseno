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

namespace Sugarcrm\Sugarcrm\CustomerJourney\Bean;

/**
 * This class parse the data for webhooks mainly
 */
class ParseData
{
    /**
     * Function extracts the variables from data
     * @param string $data
     * @return array Array of variables
     */
    public static function parseVariables($data): array
    {
        $result = [];
        $index = 0;
        $switch = false;

        for ($i = 0; $i < strlen($data); $i++) {
            if ($data[$i] === '{') {
                $index = $i;
                $switch = true;
            } elseif ($data[$i] === '}' && $switch === true) {
                $switch = false;
                array_push($result, [substr($data, $index, $i - $index + 1)]);
            }
        }

        return $result;
    }

    /**
     * Replace the variables with there respective values
     * @param array $info_list
     * @param string $text
     * @return string $text
     */
    public static function replaceVariablesWithValues($info_list, $text): string
    {
        if (empty($info_list)) {
            return $text;
        }

        foreach ($info_list as $key => $value) {
            if (!empty($value)) {
                $text = str_replace($value['original_variable'], $value['field_value'], $text);
            }
        }
        return $text;
    }
}
