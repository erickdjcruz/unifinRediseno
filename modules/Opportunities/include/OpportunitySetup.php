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
 * Class OpportunitySetup
 */
abstract class OpportunitySetup
{
    /**
     * @var mixed[]
     */
    protected $reportchange;
    /**
     * @var Opportunity
     */
    protected $bean;

    /**
     * A flag to indicate if it's from upgrade
     *
     * @var boolean
     */
    protected $isUpgrade = false;

    /**
     * Field Vardef setup
     *
     * @var array
     */
    protected $field_vardef_setup = [];

    /**
     * Where is the applications extension folder at
     *
     * @var string
     */
    protected $appExtFolder = 'custom/Extension/application/Ext';

    /**
     * Opportunity Extension Folder
     *
     * @var string
     */
    protected $moduleExtFolder = 'custom/Extension/modules/Opportunities/Ext';

    /**
     * RevenueLineItem Extension Folder
     *
     * @var string
     */
    protected $rliModuleExtFolder = 'custom/Extension/modules/RevenueLineItems/Ext';

    /**
     * Dupe Check Extension File Name
     *
     * @var string
     */
    protected $dupeCheckExtFile = 'dupe_check.ext.php';

    /**
     * RevenueLineItem Module Extension unhide file name
     *
     * @var string
     */
    protected $rliModuleExtFile = 'rli_unhide.ext.php';

    /**
     * RevenueLineItem Module Extension vardef dictionary change
     *
     * @var string
     */
    protected $rliModuleExtVardefFile = 'rli_vardef.ext.php';

    /**
     * What is the file name for the extends to disable the stock Opportunity Dependencies
     *
     * @var string
     */
    protected $oppModuleDependencyFile = 'opp_disable_dep.ext.php';

    /**
     * Where should we put the studio file at for RevenueLineItems
     *
     * @var string
     */
    protected $rliStudioFile = 'custom/modules/RevenueLineItems/metadata/studio.php';

    /**
     * Account Extension Folder
     *
     * @var string
     */
    protected $accModuleExtFolder = 'custom/Extension/modules/Accounts/Ext';

    /**
     * Account Module Extension vardef dictionary change
     *
     * @var string
     */
    protected $accModuleExtVardefFile = 'acc_vardef.ext.php';

    /**
     * Forecast field defs
     * @var array
     */
    protected $commitStageViewDef = [
        'name' => 'commit_stage',
        'type' => 'enum-cascade',
        'disable_field' => 'closed_won_revenue_line_items',
        'disable_positive' => true,
        'related_fields' => [
            'probability',
            'closed_won_revenue_line_items',
        ],
    ];

    public function __construct()
    {
        $this->bean = BeanFactory::newBean('Opportunities');
    }

    /**
     * Set the flag to indicate if it's in upgrade.
     * @param boolean $isUpgrade the flag value
     */
    public function setIsUpgrade($isUpgrade = false)
    {
        $this->isUpgrade = $isUpgrade;
    }

    /**
     * Process the field vardefs as setup by the extending classes
     */
    public function processFields()
    {
        // get the get_widget helper and the StandardField Helper
        SugarAutoLoader::load('modules/DynamicFields/FieldCases.php');
        SugarAutoLoader::load('modules/ModuleBuilder/parsers/StandardField.php');

        foreach ($this->field_vardef_setup as $field => $new_defs) {
            // get the field defs
            $field_defs = $this->bean->getFieldDefinition($field);

            $diff = [];
            foreach ($new_defs as $k => $v) {
                if (!isset($field_defs[$k])) {
                    switch ($k) {
                        case 'massupdate':
                        case 'studio':
                        case 'reportable':
                        case 'workflow':
                            if (!$v) {
                                $diff[$k] = $v;
                            }
                            break;
                        default:
                            if ($v) {
                                $diff[$k] = $v;
                            }
                    }
                } elseif ($field_defs[$k] != $v) {
                    $diff[$k] = $v;
                }
            }
            if (empty($diff)) {
                continue;
            }

            // the TemplateCurrency has a default of 0, but out OOB files, they are required
            // to not have a default value of 0, so it's set to null here
            if ($field_defs['type'] === 'currency' && !isset($field_defs['default'])) {
                $diff['default'] = null;
            }

            $this->updateFieldVardef($field, $diff);
        }
    }

    /**
     * Updates a single field's vardefs with the given properties
     *
     * @param string $field
     * @param array $properties
     */
    public function updateFieldVardef(string $field, array $properties)
    {
        $fieldDef = $this->bean->getFieldDefinition($field);
        $fieldTemplate = get_widget($fieldDef['type']);
        $fieldTemplate->populateFromRow(array_merge($fieldDef, $properties));
        $fieldTemplate->module = $this->bean;
        $field = new StandardField($this->bean->module_name);
        $field->setup($this->bean);

        // StandardField considers only the attributes which can be edited in Studio,
        // while the "studio" attribute is not one of them. we need to change the vardef map temporarily here,
        // because changing it permanently will make the "studio" attribute always overridden with empty value,
        // after the field has been saved in Studio
        if (!isset($f->vardef_map['studio'])) {
            $fieldTemplate->vardef_map['studio'] = 'studio';
        }
        if (!isset($f->vardef_map['convertToBase'])) {
            $fieldTemplate->vardef_map['convertToBase'] = 'convertToBase';
        }

        $fieldTemplate->save($field);
    }

    /**
     * Convert the Opportunity Module to be Using Opps w/ RLIs or Opps w/o RLI's
     *
     * @return mixed
     */
    public function doMetadataConvert()
    {

        MetaDataManager::enableCacheRefreshQueue();
        // process the fields so we have all the vardefs changes first
        $this->processFields();

        // fix the dupe check as it changes the vardefs as well
        $this->fixOpportunityModule();

        // hide RLI related fields from massupdate
        $this->fixProductsModule();

        // hide RLI related fields in Account module
        $this->fixAccountModule();

        $this->fixLeadConvertView();

        // r&r the opp module
        $this->runRepairAndRebuild(
            [
                'Opportunities',
                'Products',
                'Forecasts',
                'Accounts',
                'Leads',
            ]
        );

        // regenerate the Opportunity Vardefs
        VardefManager::loadVardef(
            $this->bean->getModuleName(),
            $this->bean->object_name,
            true,
            ['bean' => $this->bean]
        );

        $this->bean->clearLoadedDef($this->bean->object_name);

        $this->bean = BeanFactory::newBean('Opportunities');

        $rnr_modules = $this->fixRevenueLineItemModule();
        SugarBean::clearLoadedDef('RevenueLineItem');

        // hide/show reports
        $this->handleReports();

        if ($this->isUpgrade === false) {
            // lets fix the workflows module
            $this->processWorkFlows();
        }

        // r&r the rli + related modules
        $this->runRepairAndRebuild($rnr_modules);

        MetaDataManager::disableCacheRefreshQueue();

        register_shutdown_function(['SugarAutoLoader', 'buildCache']);
    }

    /**
     * Utility method to run repair and rebuild on a set of modules.
     *
     * @param array $modules The list of modules
     */
    private function runRepairAndRebuild(array $modules = ['Opportunities'])
    {
        SugarAutoLoader::load('modules/Administration/QuickRepairAndRebuild.php');
        $rac = new RepairAndClear();
        $rac->show_output = false;
        $rac->module_list = $modules;
        $rac->clearVardefs();
        $rac->clearMetadataAPICache();
        $rac->rebuildExtensions($modules);
    }

    /**
     * Fix the module Filters
     *
     * @param array $fieldMap The list of fields to add or remove from the filter.
     */
    protected function fixFilter(array $fieldMap)
    {
        /* @var $filterDefParser SidecarFilterLayoutMetaDataParser */
        $filterDefParser = ParserFactory::getParser(MB_BASICSEARCH, 'Opportunities', null, null, 'base');

        foreach ($fieldMap as $field => $add) {
            if ($add === true) {
                $filterDefParser->addField($field);
            } else {
                $filterDefParser->removeField($field);
            }
        }

        $filterDefParser->handleSave(false, false);
    }


    /**
     * Add and Remove fields from the Record View
     *
     * @param array $fieldMap
     */
    protected function fixRecordView(array $fieldMap)
    {
        SugarAutoLoader::load('modules/Opportunities/include/OpportunityViews.php');
        $view = new OpportunityViews();
        $view->processBaseRecordLayout($fieldMap);
        $view->processMobileRecordLayout($fieldMap);
        $view->processPreviewLayout($fieldMap);
    }

    /**
     * Add and Remove fields from all the list views
     *
     * @param array $fieldMap
     */
    protected function fixListViews(array $fieldMap)
    {
        SugarAutoLoader::load('modules/Opportunities/include/OpportunityViews.php');
        $view = new OpportunityViews();

        $modules = $view->processListViews($fieldMap);

        // run repair and rebuild for all the modules that were touched
        $this->runRepairAndRebuild($modules);
    }

    /**
     * Ensure forecast-related fields are in the correct state
     * @param null $enabledOverride if set to true or false, used instead of checking forecast settings
     *                              to see if fields should be added/removed
     */
    public function fixForecastFields($enabledOverride = null)
    {
        $recordViews = [
            'base' => [
                MB_RECORDVIEW,
                MB_RECORDDASHLETVIEW,
                MB_PREVIEWVIEW,
            ],
            'mobile' => [
                MB_WIRELESSDETAILVIEW,
                MB_WIRELESSEDITVIEW,
            ],
        ];

        $listViews = [
            'base' => [
                MB_SIDECARPOPUPVIEW,
                MB_SIDECARDUPECHECKVIEW,
                MB_LISTVIEW,
            ],
            'mobile' => [
                MB_WIRELESSLISTVIEW,
            ],
        ];

        $shouldAddFields = $this->shouldAddForecastFields($enabledOverride);

        foreach ($recordViews as $client => $views) {
            foreach ($views as $view) {
                $this->fixRecordForecastFields($shouldAddFields, $view, $client);
            }
        }
        foreach ($listViews as $client => $views) {
            foreach ($views as $view) {
                $this->fixListForecastFields($shouldAddFields, $view, $client);
            }
        }

        $this->fixFilter([
            'commit_stage' => $shouldAddFields,
        ]);

        $this->runRepairAndRebuild(['Opportunities']);
    }

    /**
     * Helper function to check if forecast fields need to be fixed
     * @param $enabledOverride
     * @return bool
     */
    private function shouldAddForecastFields($enabledOverride)
    {
        return $enabledOverride ?? $this->isForecastSetup();
    }

    /**
     * Fixes the forecast field for record views
     * @param $shouldAddFields
     * @param $view
     * @param $client
     */
    protected function fixRecordForecastFields($shouldAddFields, $view, $client)
    {
        $parser = ParserFactory::getParser($view, 'Opportunities', null, null, $client);

        // Check the available fields to ensure we don't add commit_stage more than once.
        $availableFieldNames = array_column($parser->getAvailableFields(), 'name');
        if ($shouldAddFields && in_array('commit_stage', $availableFieldNames)) {
            $parser->additionalFieldDefs['commit_stage'] = $this->commitStageViewDef;
            $parser->addField($this->commitStageViewDef);
            $parser->handleSave(false, true);
        } elseif (!$shouldAddFields) {
            $parser->removeField('commit_stage');
            $parser->handleSave(false, true);
        }
    }

    /**
     * Fixes the forecast field for list views
     * @param $view
     * @param $client
     */
    protected function fixListForecastFields($shouldAddFields, $view, $client)
    {
        $parser = ParserFactory::getParser($view, 'Opportunities', null, null, $client);

        // List view parsers only allow one instance of a field to be added at a time, and don't report entirely
        // accurate results with getAvailableFields() - just skip checking that for these views.
        if ($shouldAddFields) {
            $parser->addField('commit_stage', $this->commitStageViewDef);
        } else {
            $parser->removeField('commit_stage');
        }
        $parser->handleSave(false, true);
    }

    /**
     * Refresh the metadata cache for a given list of modules
     *
     * @param array $modules Which modules to refresh, if left empty it wil only do `Opportunities`
     */
    protected function refreshMetadataCache(array $modules = [])
    {
        // if empty, default it to Opportunities
        if (empty($modules)) {
            $modules[] = $this->bean->module_name;
        }
        MetaDataManager::refreshModulesCache($modules);
    }

    /**
     * Utility Method to know if forecasts is setup or not
     *
     * @return bool
     */
    protected function isForecastSetup()
    {
        $settings = Forecast::getSettings();

        return ($settings['is_setup'] == 1);
    }

    /**
     * Reset the forecast data.
     *
     * @param string $forecast_by What are we going to be forecasting by now
     */
    protected function resetForecastData($forecast_by)
    {
        $admin = BeanFactory::newBean('Administration');
        $admin->saveSetting('Forecasts', 'forecast_by', $forecast_by, 'base');

        SugarAutoLoader::load('modules/Forecasts/include/ForecastReset.php');
        $forecast_reset = new ForecastReset();
        $forecast_reset->truncateForecastData();
        //No need to clear or rebuild the cache here, Opp settings will clear/rebuild at the end of its process
        $forecast_reset->setDefaultWorksheetColumns($forecast_by, false);

        // reload the settings
        Forecast::getSettings(true);
    }

    /**
     * Hide or show the navigation tab.
     *
     * @param bool $show Should we show the tab or not, defaults to `true`
     */
    protected function setRevenueLineItemModuleTab($show = true)
    {
        $this->setRevenueLineItemTab($show);
        // for ths one, we have to reverse show, since if we want to show it, it needs not be in the list
        // and if we want to hide it, it needs to not be in the list
        $this->setConfigSetting('hide_subpanels', 'revenuelineitems', !$show);

        sugar_cache_clear('admin_settings_cache');
    }

    protected function setRevenueLineItemTab($show)
    {
        SugarAutoLoader::load('modules/MySettings/TabController.php');
        $newTB = new TabController();

        //grab the existing system tabs
        $tabs = $newTB->get_system_tabs();

        if ($show) {
            // if this is in the upgrade and RevenueLineItem is disabled in the tab before the upgrade,
            // it should not be enabled in the tab.
            if (!$this->isUpgrade || isset($tabs['RevenueLineItems'])) {
                $tabs['RevenueLineItems'] = 'RevenueLineItems';
            }
        } else {
            unset($tabs['RevenueLineItems']);
        }

        //now assign the modules to system tabs
        $newTB->set_system_tabs($tabs);
    }

    /**
     * @param $setting
     * @param $value
     * @param bool $show
     */
    protected function setConfigSetting($setting, $value, $show = true)
    {
        $db = DBManagerFactory::getInstance();
        $sql = <<<SQL
SELECT value FROM config
WHERE category = 'MySettings'
AND name = ?
AND (platform = 'base' OR platform IS NULL OR platform = '')
SQL;

        $result = $db->getConnection()->executeQuery($sql, [$setting]);

        foreach ($result->iterateAssociative() as $row) {
            $tabArray = unserialize(base64_decode($row['value']), ['allowed_classes' => false]);

            // in the setup, this might not be set yet.
            if (is_array($tabArray)) {
                // find the key
                $key = array_search($value, $tabArray);
                if ($key === false && $show === true) {
                    $tabArray[] = $value;
                } elseif ($key !== false & $show === false) {
                    unset($tabArray[$key]);
                }

                $sql = <<<SQL
UPDATE config
SET value = ?
WHERE category = 'MySettings'
AND name = ?
AND (platform = 'base' OR platform IS NULL OR platform = '')
SQL;

                $db->getConnection()
                    ->executeUpdate(
                        $sql,
                        [
                            base64_encode(serialize($tabArray)),
                            $setting,
                        ]
                    );
            }
        }
    }

    /**
     * Add or Remove the RevenueLineItems Module to the Parent Type dropdown List
     *
     * @param bool $add Defaults to `true`
     */
    protected function setRevenueLineItemInParentRelateDropDown($add = true)
    {
        $rli = BeanFactory::newBean('RevenueLineItems');
        $all_languages = get_languages();
        $old_request = $_REQUEST;

        // What lists need updating
        $listsToUpdate = [
            'moduleList',
            'parent_type_display',
            'record_type_display_notes',
            'record_type_display',
        ];

        // load the Dropdown parser so it can easily be saved
        SugarAutoLoader::load('modules/ModuleBuilder/parsers/ParserFactory.php');
        /**
         * @var ParserDropDown $dd_parser
         */
        $dd_parser = ParserFactory::getParser('dropdown');

        foreach ($all_languages as $current_lang => $current_lang_name) {
            // get the default app_list_strings and the default language for Revenue Line Items
            $app_list_stings = return_app_list_strings_language($current_lang);
            $module_lang = return_module_language($current_lang, 'RevenueLineItems');

            foreach ($listsToUpdate as $list_key) {
                $list = $app_list_stings[$list_key];
                $hasRLI = isset($list[$rli->module_name]);

                if ($add && (!$hasRLI || $list[$rli->module_name] !== $module_lang['LBL_MODULE_NAME'])) {
                    // get the translated value
                    $list[$rli->module_name] = $module_lang['LBL_MODULE_NAME'];
                    $GLOBALS['app_list_strings'][$list_key][$rli->module_name] = $module_lang['LBL_MODULE_NAME'];
                } elseif (!$add && $hasRLI) {
                    unset($GLOBALS['app_list_strings'][$list_key][$rli->module_name]);
                    unset($list[$rli->module_name]);
                } else {
                    // nothing changed, we can continue
                    continue;
                }

                // the parser need all the values to be in their own array with the key first then the value
                $new_list = [];
                foreach ($list as $k => $v) {
                    $new_list[] = [$k, $v];
                }

                $params = [
                    'dropdown_name' => $list_key,
                    'dropdown_lang' => $current_lang,
                    'list_value' => json_encode($new_list),
                    'view_package' => 'studio',
                    'use_push' => ($list_key == 'moduleList'),
                    'skipSaveExemptDropdowns' => true,
                    'skip_sync' => true,
                ];
                // for some reason, the ParserDropDown class uses $_REQUEST vs getting it from what
                // was passed in.
                $_REQUEST['view_package'] = 'studio';
                $_REQUEST['dropdown_lang'] = $current_lang;

                //Save, but wait on clearing/rebuilding the cache until after we have updated all the languages
                $dd_parser->saveDropDown($params, false);

                // clean up the request object
                unset($_REQUEST['dropdown_lang']);
                unset($_REQUEST['view_package']);
            }
        }

        $dd_parser->finalize($all_languages);

        $_REQUEST = $old_request;
    }

    protected function toggleRevenueLineItemQuickCreate($enable = false)
    {
        SugarAutoLoader::load('modules/Administration/views/view.configureshortcutbar.php');
        $cscb = new ViewConfigureshortcutbar();

        $modules = $cscb->getQuickCreateModules();

        $enModules = [];
        foreach ($modules['enabled'] as $module => $def) {
            $enModules[$module] = $def['order'];
        }

        $hasRLI = isset($enModules['RevenueLineItems']);
        if ($enable === true && $hasRLI === false) {
            // if it's upgrade, RLI must be disabled and $hasRLI is false. Hence it won't be enabled.
            if (!$this->isUpgrade) {
                $enModules['RevenueLineItems'] = safeCount($enModules);
            }
        } elseif ($enable === false && $hasRLI === true) {
            unset($enModules['RevenueLineItems']);
        } else {
            return;
        }

        $cscb->saveChangesToQuickCreateMetadata($modules['enabled'], $modules['disabled'], $enModules);
    }

    /**
     * Process WorkFlows
     *
     * This will mark any WorkFlows based on the Opportunity Module as Inactive so they don't run and potentially blow
     * up after the convert.
     *
     * @throws SugarQueryException
     */
    protected function processWorkFlows()
    {
        $this->markWorkFlowsWithOppActionShellsInactive();
        $this->markWorkFlowsWithOppTriggerShellsInactive();

        // mark all WorkFlows with their base of opportunities as status '0' (Inactive)
        /* @var $workFlow WorkFlow */
        $workFlow = BeanFactory::newBean('WorkFlow');
        $sq = new SugarQuery();
        $sq->select(['id']);
        $sq->from($workFlow);
        $sq->where()
            ->equals('status', 1)
            ->equals('base_module', $this->bean->module_name);

        $rows = $sq->execute();

        // now mark all the WorkFlows that were found as In-Active (status = 0)
        foreach ($rows as $row) {
            $workFlow->retrieve($row['id']);
            $workFlow->status = 0;
            $workFlow->save(false);
            $workFlow->write_workflow();
        }
    }

    /**
     * Find any Action Shells for the Opportunity Module and mark it's related workflow inactive
     *
     * @throws SugarQueryException
     */
    private function markWorkFlowsWithOppActionShellsInactive()
    {
        // get the action shells
        $actionShells = BeanFactory::newBean('WorkFlowActionShells');

        $sq = new SugarQuery();
        $sq->select(['id', 'parent_id']);
        $sq->from($actionShells);
        $sq->where()
            ->queryOr()
            ->equals('rel_module', 'opportunities')
            ->equals('action_module', 'opportunities');

        $rows = $sq->execute();

        foreach ($rows as $row) {
            $actionShells->retrieve($row['id']);
            $workflow = $actionShells->get_workflow_object();
            $workflow->status = 0;
            $workflow->save();
            $workflow->write_workflow();
        }
    }

    /**
     * Find any Trigger Shells for the Opportunity Module and Mark it's related workflow inactive
     *
     * @throws SugarQueryException
     */
    private function markWorkFlowsWithOppTriggerShellsInactive()
    {
        // get the action shells
        $triggerShells = BeanFactory::newBean('WorkFlowTriggerShells');

        $sq = new SugarQuery();
        $sq->select(['id', 'parent_id']);
        $sq->from($triggerShells);
        $sq->where()
            ->equals('rel_module', 'opportunities');

        $rows = $sq->execute();

        foreach ($rows as $row) {
            $triggerShells->retrieve($row['id']);
            $workflow = $triggerShells->get_workflow_object();
            $workflow->status = 0;
            $workflow->save();
            $workflow->write_workflow();
        }
    }

    protected function toggleRevenueLineItemsLinkInWorkFlows($show = false)
    {
        // make sure all the links are visible in workflows
        /* @var $rli_bean = RevenueLineItem */
        $rli_bean = BeanFactory::newBean('RevenueLineItems');
        $rli_links = $rli_bean->get_linked_fields();

        $rnr_modules = [];

        foreach ($rli_links as $name => $link) {
            if ($rli_bean->load_relationship($name) && $rli_bean->$name instanceof Link2) {
                $bean = BeanFactory::newBean($rli_bean->$name->getRelatedModuleName());
                $rel_name = $rli_bean->$name->getRelatedModuleLinkName();

                // if for some reason we didn't find a rli_name on the other side of the link
                // we should just ignore it
                if (empty($rel_name)) {
                    continue;
                }

                $file = 'rli_link_workflow.php';
                $folder = "custom/Extension/modules/{$bean->module_dir}/Ext";

                SugarAutoLoader::ensureDir($folder . '/Vardefs');

                if ($show === true) {
                    $file_contents = <<<EOL
<?php
\$dictionary['{$bean->object_name}']['fields']['{$rel_name}']['workflow'] = true;
EOL;

                    sugar_file_put_contents($folder . '/Vardefs/' . $file, $file_contents);
                } else {
                    if (file_exists($folder . '/Vardefs/' . $file)) {
                        // since we don't what to show it, just remove the file as it defaults
                        // to false out of the box.
                        unlink($folder . '/Vardefs/' . $file);
                    }
                }

                $rnr_modules[] = $bean->module_name;
            }
        }

        return $rnr_modules;
    }

    /**
     * Cleanup the Unified Search Files
     */
    protected function cleanupUnifiedSearchCache()
    {
        // since we changed the unified search setting remove the cache file
        $file = sugar_cached('modules/unified_search_modules.php');
        if (file_exists($file)) {
            unlink($file);
        }
        // remove the unified search display settings
        $file = 'custom/modules/unified_search_modules_display.php';
        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * Show/hide reports based on mode.
     */
    protected function handleReports()
    {
        require_once 'modules/Reports/SeedReports.php';

        $db = DBManagerFactory::getInstance();

        $func = function ($item) use ($db) {
            return ($db->quoted($item));
        };

        $hide = !empty($this->reportchange['hide']) ? array_map($func, $this->reportchange['hide']) : [];

        if (!empty($hide)) {
            $sql = 'UPDATE saved_reports SET deleted = 1 WHERE name IN (' . implode(',', $hide) . ') AND deleted = 0';
            $db->query($sql);
        }

        if (!empty($this->reportchange['show'])) {
            create_default_reports(false, $this->reportchange['show']);
        }

        if (!empty($this->reportchange['redefine'])) {
            $default_reports_mapped = [];

            $default_reports = array_merge(
                get_sales_marketing_reports(),
                get_customer_service_reports(),
                get_data_privacy_reports(),
                get_admin_reports()
            );
            foreach ($default_reports as $row) {
                $default_reports_mapped[$row[1]] = $row;
            }

            foreach ($this->reportchange['redefine'] as $key => $value) {
                if (empty($value) && isset($default_reports_mapped[$key])) {
                    $value = $default_reports_mapped[$key][2];
                }

                $query = 'UPDATE saved_reports SET content = ? WHERE name = ? AND date_entered = date_modified';
                $db->getConnection()->executeStatement($query, [$value, $key]);
            }
        }
    }

    /**
     * Handle updating the field in the Products Module
     */
    protected function fixProductsModuleField($field, $attribute, $value)
    {
        $products = BeanFactory::newBean('Products');
        $field_defs = $products->getFieldDefinition($field);

        // get the get_widget helper and the StandardField Helper
        SugarAutoLoader::load('modules/DynamicFields/FieldCases.php');
        SugarAutoLoader::load('modules/ModuleBuilder/parsers/StandardField.php');

        $f = get_widget($field_defs['type']);
        $f->populateFromRow(array_merge($field_defs, [$attribute => $value]));

        // now lets save, since these are OOB field, we use StandardField
        $df = new StandardField($products->module_name);
        $df->setup($products);
        $f->module = $products;

        // StandardField considers only the attributes which can be edited in Studio,
        // while the "studio" attribute is not one of them. we need to change the vardef map temporarily here,
        // because changing it permanently will make the "studio" attribute always overridden with empty value,
        // after the field has been saved in Studio
        if (!isset($f->vardef_map['studio'])) {
            $f->vardef_map['studio'] = 'studio';
        }
        if (!isset($f->vardef_map['convertToBase'])) {
            $f->vardef_map['convertToBase'] = 'convertToBase';
        }

        $f->save($df);
    }

    abstract public function doDataConvert();

    abstract protected function fixRevenueLineItemModule();

    /**
     * Any Custom Logic for the Opportunity Module
     */
    abstract protected function fixOpportunityModule();

    /**
     * Fix Account module.
     */
    abstract protected function fixAccountModule();

    /**
     * Fix Lead Convert views
     */
    abstract protected function fixLeadConvertView();
}
