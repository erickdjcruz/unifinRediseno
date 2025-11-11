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

use Sugarcrm\Sugarcrm\AccessControl\AccessControlManager;

/*********************************************************************************
 * Description: .
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
class DataArchiver extends SugarBean
{
    // Stored fields
    public $filter_module_name;
    public $name;
    public $active;
    public $process_tyoe;
    public $filter_def;

    public const PROCESS_TYPE_ARCHIVE = 'archive';
    public const PROCESS_TYPE_DELETE = 'delete';

    public $object_name = 'DataArchiver';
    public $table_name = 'data_archivers';

    public $module_dir = 'DataArchiver';
    public $module_name = 'DataArchiver';

    /**
     * Returns the module list that will populate the module-select field
     * @return array
     */
    public function getArchiveModuleList()
    {
        $modules = [];
        global $beanList;
        global $dictionary;
        foreach ($beanList as $module => $object) {
            $object = BeanFactory::getObjectName($module);
            VardefManager::loadVardef($module, $object);
            if (empty($dictionary[$object]['fields'])) {
                continue;
            }
            // Default true for archive property plus check in case someone mistakenly set the property to true manually
            // Also check to make sure the module has a valid bean associated with it
            if ((!isset($dictionary[$object]['archive']) || $dictionary[$object]['archive'] !== false) && BeanFactory::getBeanClass($module)) {
                // Check ACL access for module under given license. Ignore invisible modules.
                if (!safeInArray($module, $GLOBALS['modInvisList'])) {
                    if (!AccessControlManager::instance()->allowModuleAccess($module)) {
                        continue;
                    }
                }
                $modules[$module] = $module;
            }
        }
        asort($modules);
        return $modules;
    }

    /**
     * Gets an appropriate process type
     * @param string $type The requested process type
     * @return string
     */
    public static function getProcessType(string $type = ''): string
    {
        if ($type !== DataArchiver::PROCESS_TYPE_DELETE) {
            $type = DataArchiver::PROCESS_TYPE_ARCHIVE;
        }

        return $type;
    }

    /**
     * Provides the dropdown list elements needed for the process type. This is
     * a system type indicator so it should not be editable in the dropdownlist editor,
     * thus it is wrapped in a function. However, the values should be localizable
     * hence the use of labels.
     * @return array
     */
    public function getProcessTypes()
    {
        return [
            static::PROCESS_TYPE_ARCHIVE => translate('LBL_PROCESS_TYPE_ARCHIVE', 'DataArchiver'),
            static::PROCESS_TYPE_DELETE => translate('LBL_PROCESS_TYPE_DELETE', 'DataArchiver'),
        ];
    }
}
