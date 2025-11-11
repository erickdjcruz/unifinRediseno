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

namespace Sugarcrm\Sugarcrm\CustomerJourney\LogicHooks;

/**
 * All general hooks registered here for CJ
 */
class GeneralHooks
{
    /**
     * Return the before or after bean value form the
     * arguments array against different version
     * of sugarcrm
     *
     * @param array $arguments
     * @param string $fieldName
     * @return mixed
     */
    public static function getBeanValueFromArgs($arguments, $fieldName, $beforeOrAfter)
    {
        if (empty($beforeOrAfter) || empty($fieldName) || !in_array($beforeOrAfter, ['before', 'after'])) {
            return '';
        }

        $fieldValue = '';
        if (version_compare($GLOBALS['sugar_config']['sugar_version'], '10.2', '>=')) {
            if (isset($arguments['stateChanges'][$fieldName])) {
                $fieldValue = $arguments['stateChanges'][$fieldName][$beforeOrAfter];
            }
        } else {
            if (isset($arguments['dataChanges'][$fieldName])) {
                $fieldValue = $arguments['dataChanges'][$fieldName][$beforeOrAfter];
            }
        }
        return $fieldValue;
    }
}
