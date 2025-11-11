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

/**
 * Include the Tab Controller since we will need this
 */

/**
 * Add new modules to the megamenu (tab controller)
 */
class SugarUpgradeAddNewModulesToMegamenu extends UpgradeScript
{
    public $order = 4001;
    public $type = self::UPGRADE_DB;

    /**
     * Array of arrays of defs for all new modules. Each array must define at
     * least a modules array. Additionally, the def array can define one of the
     * following attributes:
     *  - fromFlavor
     *  - toFlavor
     *  - fromVersion (this MUST be an array which specifies a version and evaluator)
     *  - toVersion (this MUST be an array which specifies a version and evaluator)
     *
     * These attributes will be checked to confirm that the upgrade criteria are
     * met before adding the modules to the tab controller.
     *
     * @var array
     */
    public $newModuleDefs = [
        // Upgrade from 7.5.X and below to 7.6+ on ent
        [
            'name' => 'PMSE Modules',
            'toFlavor' => ['ent', 'ult'],
            'fromVersion' => ['7.6.0', '<'],
            'modules' => [
                'pmse_Project',
                'pmse_Inbox',
                'pmse_Business_Rules',
                'pmse_Emails_Templates',
            ],
        ],
        // Conversion from CORP or PRO to ENT or ULT on 7.7+
        [
            'name' => 'PMSE Modules',
            'fromFlavor' => ['corp', 'pro'],
            'toFlavor' => ['ent', 'ult'],
            'fromVersion' => ['7.7', '>='],
            'modules' => [
                'pmse_Project',
                'pmse_Inbox',
                'pmse_Business_Rules',
                'pmse_Emails_Templates',
            ],
        ],
        // 9.1.0 new Business Center module
        [
            'name' => 'Business Centers Module',
            'fromVersion' => ['9.1.0', '<'],
            'toFlavor' => ['ent'],
            'modules' => ['BusinessCenters'],
            'forceVisible' => true,
        ],
        // 9.1.0 new Business Center module (pro->ent conversion)
        [
            'name' => 'Business Centers Module',
            'fromFlavor' => ['pro'],
            'toFlavor' => ['ent'],
            'fromVersion' => ['9.1.0', '>='],
            'modules' => ['BusinessCenters'],
            'forceVisible' => true,
        ],
        // 10.1.0 new Purchases and Purchased Line Items Modules
        [
            'name' => 'Purchases and Purchased Line Items Modules',
            'fromVersion' => ['10.1.0', '<'],
            'toFlavor' => ['ent'],
            'modules' => ['Purchases', 'PurchasedLineItems'],
            'forceVisible' => true,
        ],
        // 10.1.0 new Purchases and Purchased Line Items Modules (pro->ent conversion)
        [
            'name' => 'Purchases and Purchased Line Items Modules',
            'fromFlavor' => ['pro'],
            'toFlavor' => ['ent'],
            'fromVersion' => ['10.1.0', '>='],
            'modules' => ['Purchases', 'PurchasedLineItems'],
            'forceVisible' => true,
        ],
        // 11.2.0 new Escalations Module
        [
            'name' => 'Escalations Module',
            'fromVersion' => ['11.2.0', '<'],
            'toFlavor' => ['ent'],
            'modules' => ['Escalations'],
            'forceVisible' => true,
        ],
        // 12.3.0 new Smart Guide and Smart Guide Stages Modules
        [
            'name' => 'Smart Guide and Smart Guide Stages Modules',
            'fromVersion' => ['12.3.0', '<'],
            'toFlavor' => ['ent'],
            'modules' => ['DRI_Workflows', 'DRI_SubWorkflows'],
            'forceVisible' => true,
        ],
    ];

    public function run()
    {
        // Get the tab controller object
        $tc = $this->getTabController();

        // Get the existing tabs
        $tabs = $this->getExistingTabs($tc);

        foreach ($this->newModuleDefs as $def) {
            $this->log('Checking module set ' . $def['name']);

            // Build our boolean criteria check
            $check = $this->buildCheckCriteria($def);

            // If we are good to go, then go
            if (!$check) {
                $this->log('Skipping module set ' . $def['name']);
                continue;
            }

            // Get the newly added modules mixed with existing tabs
            $tabs = $this->getNewTabsList($tabs, $def);

            // Get the log message
            $logMessage = $this->getMessageToLog($def);

            // Log it and be done
            $this->log($logMessage);
        }

        // Save the module list
        $this->saveModifiedTabs($tc, $tabs[0]);
    }

    /**
     * Builds a conditional evaluation for checking if the criteria for the upgrade
     * is met based on the defs of the new module(s)
     *
     * @param Array $def New module def
     * @return boolean
     */
    public function buildCheckCriteria(array $def)
    {
        // First check is to ensure the modules array
        if (!isset($def['modules'])) {
            return false;
        }

        // Initialize our criteria to true, since what is required is already
        // passed
        $check = true;

        // Handle the froms and tos
        if (isset($def['fromFlavor'])) {
            $check = $this->getFromFlavorCheck($check, $def['fromFlavor']);
        }

        if (isset($def['toFlavor'])) {
            $check = $this->getToFlavorCheck($check, $def['toFlavor']);
        }

        // From and To version criteria MUST specify a version and evaluator
        if (isset($def['fromVersion'])) {
            $check = $check && version_compare($this->from_version, $def['fromVersion'][0], $def['fromVersion'][1]);
        }

        if (isset($def['toVersion'])) {
            $check = $check && version_compare($this->to_version, $def['toVersion'][0], $def['toVersion'][1]);
        }

        return $check;
    }

    /**
     * Wrapper to get a FROM flavor check
     * @param boolean $check Current state of the check
     * @param string|array $flavor The definition for the flavor
     * @return boolean
     */
    protected function getFromFlavorCheck($check, $flavor)
    {
        return $this->getFlavorCheck($check, $flavor, 'from');
    }

    /**
     * Wrapper to get a TO flavor check
     * @param boolean $check Current state of the check
     * @param string|array $flavor The definition for the flavor
     * @return boolean
     */
    protected function getToFlavorCheck($check, $flavor)
    {
        return $this->getFlavorCheck($check, $flavor, 'to');
    }

    /**
     * Checks flavor definitions for whether or not to run
     * @param boolean $check Current state of the check
     * @param string|array $flavor The definition for the flavor
     * @param string $type Either 'to' or 'from'
     * @return boolean
     */
    protected function getFlavorCheck($check, $flavor, $type)
    {
        $checkFlavor = null;
        if ($type === 'from' || $type === 'to') {
            // Set our check method based on type
            $method = $type . 'Flavor';

            // For an array of flavors, loop and check
            if (is_array($flavor)) {
                foreach ($flavor as $flav) {
                    // If this is the first time through, set the evaluation
                    if (!isset($checkFlavor)) {
                        $checkFlavor = $this->$method($flav);
                    } else {
                        // Otherwise, combine the evaluation
                        $checkFlavor = $checkFlavor || $this->$method($flav);
                    }
                }

                $check = $check && $checkFlavor;
            } else {
                $check = $check && $this->$method($flavor);
            }
        }

        return $check;
    }

    /**
     * Gets a new TabController object, encapsulated into its own method for
     * testability.
     *
     * @return TabController
     */
    public function getTabController()
    {
        return new TabController();
    }

    /**
     * Gets the current setup of modules in the megamenu
     * @param TabController $tc The TabController object
     * @return array
     */
    public function getExistingTabs(TabController $tc)
    {
        // Get all the tabs - enabled and disabled
        return $tc->get_tabs_system();
    }

    /**
     * Gets the tabs list with new modules added to it.
     * @param array $tabs Current list of modules in the megamenu
     * @param array $def Defs that contain a modules array to add
     * @return array The list of tabs.
     */
    public function getNewTabsList(array $tabs, array $def)
    {
        // Add in our new modules
        foreach ($def['modules'] as $m) {
            if (isset($def['forceVisible']) && $def['forceVisible']) {
                // add to the enabled tab list and remove from the disabled tab list
                $tabs[0][$m] = $m;
                if (isset($tabs[1][$m])) {
                    unset($tabs[1][$m]);
                }
            } elseif (!isset($tabs[0][$m]) && !isset($tabs[1][$m])) {
                $tabs[0][$m] = $m;
            }
        }

        return $tabs;
    }

    /**
     * Saves the modified tab list
     *
     * @param TabController $tc TabController object
     * @param Array $tabs Array of new modules to be saved to the tab list
     */
    protected function saveModifiedTabs(TabController $tc, array $tabs)
    {
        $tc->set_system_tabs($tabs);
    }

    /**
     * Gets a log message based on the def
     *
     * @param array $def New modules array def
     * @return string
     */
    public function getMessageToLog($def)
    {
        // Build a log message, sort of abstract at first...
        $logMessage = 'Megamenu module list updated';

        // But if there is a name for this addition, a little less abstract
        if (isset($def['name'])) {
            $logMessage .= " with $def[name]";
        }

        return $logMessage;
    }
}
