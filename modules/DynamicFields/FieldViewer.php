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

class FieldViewer
{
    /**
     * @var \Sugar_Smarty|mixed
     */
    public $ss;
    public static $fieldNameBlacklist = [
        'date_entered', 'date_modified', 'modified_user_id', 'created_by', 'deleted',
    ];

    public static $fieldTypeBlacklist = [
        'password',
    ];

    public static $fieldNameNoRequired = [
        'date_entered', 'date_modified',
    ];

    public static $massUpdateWhitelist = [
        'bool', 'int', 'date', 'datetime', 'datetimecombo', 'decimal', 'float', 'enum',
        'multienum', 'radioenum', 'varchar', 'relate', 'encrypt', 'iframe', 'url', 'phone',
        'text', 'toggle',
    ];

    public static $calculationVisibleLimitedModules = ['Users'];
    public static $calculationVisibleLimitedTypes = [
        'int', 'float', 'decimal', 'discount', 'currency', 'bool', 'varchar', 'name',
        'phone', 'text', 'url', 'encrypt', 'enum', 'radioenum', 'fullname', 'date',
        'datetime', 'datetimecombo', 'service-enddate', 'multienum',
    ];
    public static $calculationVisibleDisallowedFields = [
        'deleted', 'email1',
    ];

    public function __construct()
    {
        $this->ss = new Sugar_Smarty();
    }

    public function getLayout($vardef)
    {
        if (empty($vardef['type'])) {
            $vardef['type'] = 'varchar';
        }
        $mod = return_module_language($GLOBALS['current_language'], 'DynamicFields');
        $this->ss->assign('vardef', $vardef);
        $this->ss->assign('MOD', $mod);
        $this->ss->assign('APP', $GLOBALS['app_strings']);
        //Only display range search option if in Studio, not ModuleBuilder
        $this->ss->assign('range_search_option_enabled', empty($_REQUEST['view_package']));

        if ((isset($vardef['name']) && in_array($vardef['name'], self::$fieldNameBlacklist))
            || (isset($vardef['type']) && in_array($vardef['type'], self::$fieldTypeBlacklist))) {
            $this->ss->assign('hideDuplicatable', 'true');
        }

        if ($fieldRangeValue = DynamicField::getFieldRangeValueByType($vardef['type'])) {
            $this->ss->assign('field_range_value', $fieldRangeValue);
        }

        if ((isset($vardef['name']) && in_array($vardef['name'], self::$fieldNameNoRequired))) {
            $this->ss->assign('hideRequired', true);
        } else {
            $this->ss->assign('hideRequired', false);
        }

        $this->ss->assign('hideMassUpdate', !in_array($vardef['type'], self::$massUpdateWhitelist));

        if ($this->ss->get_template_vars('is_relationship_field')) {
            $this->ss->assign('hideReportable', true);
            $this->ss->assign('hideReadOnly', true);

            if ($this->ss->get_template_vars('is_one_to_one_field')) {
                $this->ss->assign('hideMassUpdate', true);
            }

            // For Account Name fields, the required option is controlled by the require_accounts config.
            if (isset($GLOBALS['sugar_config']['require_accounts']) && $vardef['name'] === 'account_name') {
                $this->ss->assign('hideRequired', true);
            }
        }

        $module = $this->ss->get_template_vars('module');
        $hasStudio = isset($vardef['studio']) && is_array($vardef['studio']);
        $hideForFormula = $hasStudio && isset($vardef['studio']['formula']) && isFalsy($vardef['studio']['formula']);
        $hideForRelated = $hasStudio && isset($vardef['studio']['related']) && isFalsy($vardef['studio']['related']);
        if (!empty($module) && !empty($module->name) &&
            in_array($module->name, self::$calculationVisibleLimitedModules) &&
            in_array($vardef['type'], self::$calculationVisibleLimitedTypes) &&
            !in_array($vardef['name'], self::$calculationVisibleDisallowedFields) &&
            !$hideForFormula && !$hideForRelated
        ) {
            $this->ss->assign('showCalculationVisible', true);
        } else {
            $this->ss->assign('showCalculationVisible', false);
        }

        $GLOBALS['log']->debug('FieldViewer.php->getLayout() = ' . $vardef['type']);
        switch ($vardef['type']) {
            case 'address':
                return $this->ss->fetch('modules/DynamicFields/templates/Fields/Forms/address.tpl');
            case 'bool':
                return $this->ss->fetch('modules/DynamicFields/templates/Fields/Forms/bool.tpl');
            case 'int':
                return $this->ss->fetch('modules/DynamicFields/templates/Fields/Forms/int.tpl');
            case 'float':
                return $this->ss->fetch('modules/DynamicFields/templates/Fields/Forms/float.tpl');
            case 'decimal':
                return $this->ss->fetch('modules/DynamicFields/templates/Fields/Forms/float.tpl');
            case 'date':
                require_once 'modules/DynamicFields/templates/Fields/Forms/date.php';
                return get_body($this->ss, $vardef);
            case 'datetimecombo':
            case 'datetime':
                require_once 'modules/DynamicFields/templates/Fields/Forms/datetimecombo.php';
                return get_body($this->ss, $vardef);
            case 'enum':
                require_once 'modules/DynamicFields/templates/Fields/Forms/enum2.php';
                return get_body($this->ss, $vardef);
            case 'multienum':
                require_once 'modules/DynamicFields/templates/Fields/Forms/multienum.php';
                return get_body($this->ss, $vardef);
            case 'radioenum':
                require_once 'modules/DynamicFields/templates/Fields/Forms/radioenum.php';
                return get_body($this->ss, $vardef);
            case 'html':
                require_once 'modules/DynamicFields/templates/Fields/Forms/html.php';
                return get_body($this->ss, $vardef);
            case 'currency':
                return $this->ss->fetch('modules/DynamicFields/templates/Fields/Forms/currency.tpl');
            case 'relate':
                require_once 'modules/DynamicFields/templates/Fields/Forms/relate.php';
                return get_body($this->ss, $vardef);
            case 'parent':
                require_once 'modules/DynamicFields/templates/Fields/Forms/parent.php';
                return get_body($this->ss, $vardef);
            case 'text':
                return $this->ss->fetch('modules/DynamicFields/templates/Fields/Forms/text.tpl');
            case 'encrypt':
                require_once 'modules/DynamicFields/templates/Fields/Forms/encrypt.php';
                return get_body($this->ss, $vardef);
            case 'iframe':
                require_once 'modules/DynamicFields/templates/Fields/Forms/iframe.php';
                return get_body($this->ss, $vardef);
            case 'url':
                require_once 'modules/DynamicFields/templates/Fields/Forms/url.php';
                return get_body($this->ss, $vardef);
            case 'phone':
                require_once 'modules/DynamicFields/templates/Fields/Forms/phone.php';
                return get_body($this->ss, $vardef);
            case 'pricing-formula':
                require_once 'modules/DynamicFields/templates/Fields/Forms/enum2.php';
                return get_body($this->ss, $vardef);
            case 'autoincrement':
                require_once 'modules/DynamicFields/templates/Fields/Forms/autoincrement.php';
                return get_body($this->ss, $vardef);
            default:
                if (SugarAutoLoader::requireWithCustom('modules/DynamicFields/templates/Fields/Forms/' . $vardef['type'] . '.php')) {
                    return get_body($this->ss, $vardef);
                } else {
                    return $this->ss->fetch('modules/DynamicFields/templates/Fields/Forms/varchar.tpl');
                }
        }
    }
}
