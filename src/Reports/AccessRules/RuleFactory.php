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

namespace Sugarcrm\Sugarcrm\Reports\AccessRules;

use User;
use SugarAutoLoader;

class RuleFactory
{
    private function __construct()
    {
    }

    /**
     * @static
     *
     * @param string $rule
     * @param User $user
     * @return mixed
     */
    public static function getRule(string $rule, $user)
    {
        if (!($user instanceof User)) {
            global $current_user;
            $user = $current_user;
        }
        $ruleClassName = self::generateRuleClassName($rule);

        return new $ruleClassName($user);
    }

    /**
     * Return the rule class name
     *
     * @static
     *
     * @param string $rule
     * @return string
     */
    private static function generateRuleClassName(string $rule): string
    {
        $rule = ucfirst($rule);

        SugarAutoLoader::requireWithCustom("src/Reports/AccessRules/Rules/{$rule}Rule.php");

        $className = "Sugarcrm\Sugarcrm\Reports\AccessRules\Rules\\{$rule}Rule";

        return SugarAutoLoader::customClass($className);
    }
}
