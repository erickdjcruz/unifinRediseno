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


class ConfiguratorController extends SugarController
{
    /**
     * List of allowed $sugar_config keys to be changed
     * by `self::action_saveconfig`.
     * @var array
     */
    protected $allowKeysSaveConfig = [
        'list_max_entries_per_page',
        'list_max_entries_per_subpanel',
        'collapse_subpanels',
        'calculate_response_time',
        'freeze_list_headers',
        'allow_freeze_first_column',
        'default_module_favicon',
        'use_real_names',
        'show_download_tab',
        'lead_conv_activity_opt',
        'enable_action_menu',
        'lock_subpanels',
        'preview_edit',
        'verify_client_ip',
        'log_memory_usage',
        'dump_slow_queries',
        'slow_query_time_msec',
        'upload_maxsize',
        'stack_trace_errors',
        'developerMode',
        'vcal_time',
        'import_max_records_total_limit',
        'noPrivateTeamUpdate',

        // logger settings
        'logger_file_name',
        'logger_file_suffix',
        'logger_file_maxSize',
        'logger_file_dateFormat',
        'logger_level',
        'logger_file_maxLogs',

        'activity_streams_enabled',

        'commentlog_maxchars',
        'allowed_link_schemes',

        'catalog_enabled',
        'catalog_url',
        // SugarBPM settings
        'processes_auto_validate_on_import',
        'processes_auto_validate_on_autosave',
        'processes_auto_save_interval',
        'error_number_of_cycles',

        //Doc Merge
        'document_merge_service_urls_default',
    ];


    public function action_listview()
    {
        global $current_user;
        if (!is_admin($current_user)) {
            sugar_die($GLOBALS['app_strings']['ERR_NOT_ADMIN']);
        }
        $this->view = 'edit';
    }

    public function action_saveadminwizard()
    {
        global $current_user;
        if (!is_admin($current_user)) {
            sugar_die($GLOBALS['app_strings']['ERR_NOT_ADMIN']);
        }
        $focus = Administration::getSettings();
        $focus->saveConfig();

        $configurator = new Configurator();
        $configurator->populateFromPost();
        $configurator->handleOverride();
        $configurator->saveConfig();

        // Bug 37310 - Delete any existing currency that matches the one we've just set the default to during the admin wizard
        $currency = BeanFactory::newBean('Currencies');
        $currency->retrieve_id_by_name($_REQUEST['default_currency_name']);
        if (!empty($currency->id)
            && $currency->symbol == $_REQUEST['default_currency_symbol']
            && $currency->iso4217 == $_REQUEST['default_currency_iso4217']) {
            $currency->deleted = 1;
            $currency->save();
        }

        SugarApplication::redirect('index.php?module=Users&action=Wizard&skipwelcome=1');
    }

    /**
     * savconfig action
     */
    public function action_saveconfig()
    {
        global $current_user;
        if (!is_admin($current_user)) {
            sugar_die($GLOBALS['app_strings']['ERR_NOT_ADMIN']);
        }

        $allowKeys = $this->allowKeysSaveConfig;

        // Filter logger_* keys if logger is not visible
        if (!SugarConfig::getInstance()->get('logger_visible', true)) {
            $allowKeys = array_filter($allowKeys, function ($value) {
                return (strpos($value, 'logger_') === 0) ? false : true;
            });
        }
        // Remove developerMode key if developerMode is not visible
        if (!SugarConfig::getInstance()->get('developer_mode_visible', true)) {
            $allowKeys = array_filter($allowKeys, function ($value) {
                return $value !== 'developerMode';
            });
        }

        $configurator = new Configurator();
        $configurator->setAllowKeys($allowKeys);

        $focus = BeanFactory::newBean('Administration');
        $focus->saveConfig();

        $configurator->saveConfig();

        // Clear the Contacts file b/c portal flag affects rendering
        if (file_exists($cachedfile = sugar_cached('modules/Contacts/EditView.tpl'))) {
            unlink($cachedfile);
        }

        echo '<script type="text/javascript">';
        echo 'parent && parent.SUGAR && parent.SUGAR.App && parent.SUGAR.App.sync();';
        echo 'parent.SUGAR.App.router.navigate("#Administration", {trigger: true})';
        echo '</script>';
        exit();
    }

    public function action_detail()
    {
        global $current_user;
        if (!is_admin($current_user)) {
            sugar_die($GLOBALS['app_strings']['ERR_NOT_ADMIN']);
        }
        $this->view = 'edit';
    }

    /**
     * Define correct view for action
     */
    public function action_historyContactsEmails()
    {
        $this->view = 'historyContactsEmails';
    }

    /**
     * Generates custom field_defs for selected fields
     */
    public function action_historyContactsEmailsSave()
    {
        require_once 'include/formbase.php';

        if (!empty($_POST['modules']) && is_array($_POST['modules'])) {
            $modules = [];
            foreach ($_POST['modules'] as $moduleName => $enabled) {
                $bean = BeanFactory::newBean($moduleName);

                if (!($bean instanceof SugarBean)) {
                    continue;
                }
                if (empty($bean->field_defs)) {
                    continue;
                }

                // these are the specific modules we care about
                if (!in_array($moduleName, ['Opportunities', 'Accounts', 'Cases'])) {
                    continue;
                }

                $bean->load_relationships();
                foreach ($bean->get_linked_fields() as $fieldName => $fieldDef) {
                    if ($bean->$fieldName->getRelatedModuleName() == 'Contacts') {
                        $modules[$moduleName] = !$enabled;
                        break;
                    }
                }
            }

            $configurator = new Configurator();
            $configurator->config['hide_history_contacts_emails'] = $modules;
            $configurator->handleOverride();
        }

        SugarApplication::redirect(buildRedirectURL('', 'Administration'));
    }
}
