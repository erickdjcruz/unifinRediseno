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

use SugarBean;
use User;

class AccessRulesManager
{
    private static $instance;

    /**
     * Rules list
     *
     * Available rules:
     *  configExport
     *  viewRights
     *  accessFieldsRights
     *  exportRights
     *
     * @var array
     */
    protected $rulesList = [
        'viewRights',
        'accessFieldsRights',
    ];

    /**
     * @var User
     */
    public $user;

    private function __construct()
    {
        global $current_user;
        $this->user = $current_user;
    }

    /**
     * @static
     *
     * @return AccessRulesManager
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Validate rules
     *
     * @param SavedReport $bean
     * @return boolean
     */
    public function validate($bean)
    {
        $isValid = true;
        if ($this->user->isAdmin()) {
            return $isValid;
        }

        while ($ruleName = array_shift($this->rulesList)) {
            $rule = RuleFactory::getRule($ruleName, $this->user);

            $isValid = $rule->validate($bean);

            if (!$isValid) {
                break;
            }
        }


        return $isValid;
    }

    /**
     * Set rules
     *
     * @param array $rules
     * @return AccessRulesManager
     */
    public function setRules($rules)
    {
        $this->rulesList = $rules;

        return $this;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return AccessRulesManager
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
}
