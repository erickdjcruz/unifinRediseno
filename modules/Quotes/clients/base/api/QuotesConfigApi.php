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

use Sugarcrm\Sugarcrm\MetaData\ViewdefManager;

class QuotesConfigApi extends ConfigModuleApi
{
    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function registerApiRest()
    {
        return
            [
                'quotesConfigGet' => [
                    'reqType' => 'GET',
                    'path' => ['Quotes', 'config'],
                    'pathVars' => ['module', ''],
                    'minVersion' => '11.3',
                    'method' => 'config',
                    'shortHelp' => 'Retrieves the config settings for a given module',
                    'longHelp' => 'modules/Quotes/clients/base/api/help/quotes_module_config_get_help.html',
                ],
                'quotesConfigCreate' => [
                    'reqType' => 'POST',
                    'path' => ['Quotes', 'config'],
                    'pathVars' => ['module', ''],
                    'minVersion' => '11.3',
                    'method' => 'configSave',
                    'shortHelp' => 'Save the config settings for the Quotes Module',
                    'longHelp' => 'modules/Quotes/clients/base/api/help/quotes_module_config_post_help.html',
                ],
            ];
    }

    /**
     * {@inheritdoc}
     */
    public function config(ServiceBase $api, array $args)
    {
        $viewdefManager = $this->getViewdefManager();

        $quotesConfig = parent::config($api, $args);
        $quotesConfig['dependentFields'] = $this->getDependentFields();
        $quotesConfig['relatedFields'] = $this->getRelatedFieldsMap();

        $parser = ParserFactory::getParser(
            MB_LISTVIEW,
            'Products',
            null,
            null,
            'base'
        );

        $defaultDiscountAmt = [
            'name' => 'discount_field',
            'type' => 'fieldset',
            'css_class' => 'discount-field quote-discount-percent',
            'label' => 'LBL_DISCOUNT_AMOUNT',
            'sortable' => false,
            'fields' => [
                [
                    'name' => 'discount_amount',
                    'label' => 'LBL_DISCOUNT_AMOUNT',
                    'type' => 'discount-amount',
                    'discountFieldName' => 'discount_select',
                    'related_fields' => [
                        'currency_id',
                    ],
                    'convertToBase' => true,
                    'base_rate_field' => 'base_rate',
                    'showTransactionalAmount' => true,
                ],
                [
                    'type' => 'discount-select',
                    'name' => 'discount_select',
                    'options' => [],
                ],
            ],
        ];

        $quotesConfig['defaultWorksheetColumns'] = $viewdefManager->loadViewdef('base', 'Products', 'quote-data-group-list', true);

        $quotesConfig['productsFields'] = array_merge(
            $parser->getAvailableFields(),
            $parser->getDefaultFields(),
            ['discount_field' => $defaultDiscountAmt]
        );

        $parser = ParserFactory::getParser(
            MB_LISTVIEW,
            'Quotes',
            null,
            null,
            'base'
        );
        $quotesConfig['quotesFields'] = array_merge($parser->getAvailableFields(), $parser->getDefaultFields());

        return $quotesConfig;
    }

    /**
     * Quotes Override since we have custom logic that needs to be ran
     *
     * {@inheritdoc}
     */
    public function configSave(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['worksheet_columns', 'worksheet_columns_related_fields']);
        $settings = parent::configSave($api, $args);
        $this->applyWorksheetColumnsConfig();
        $this->applySummaryColumnsConfig();
        $this->applyFooterRowsConfig();
        $this->saveMobileWorksheetColumnConfig($args);

        MetaDataManager::refreshModulesCache(['Quotes', 'Products']);

        return $settings;
    }

    /**
     * Gets a list of all fields and their related and locked dependencies
     *
     * @return array
     */
    protected function getRelatedFieldsMap()
    {
        $fieldVardefs = [
            'Products' => VardefManager::getFieldDefs('Products'),
            'Quotes' => VardefManager::getFieldDefs('Quotes'),
        ];

        $productsFieldNames = array_keys($fieldVardefs['Products']);
        $quotesFieldNames = array_keys($fieldVardefs['Quotes']);

        $resultArray = array_merge($this->getAllFieldDependencies(
            'Products',
            $productsFieldNames,
            $fieldVardefs
        ), $this->getAllFieldDependencies(
            'Quotes',
            $quotesFieldNames,
            $fieldVardefs
        ));

        return $resultArray;
    }

    /**
     * Finds all dependencies on a field by field name
     *
     * @param string $moduleName The name of the module for the fieldDef we're parsing
     * @param array $fieldNames The names of all the fields to loop over and parse
     * @param array $fieldDefs The collection of all field definitions
     * @return array The map of all field dependencies
     */
    protected function getAllFieldDependencies(string $moduleName, array $fieldNames, array $fieldDefs)
    {
        $retFields = [];
        foreach ($fieldNames as $fieldName) {
            $fieldDef = $fieldDefs[$moduleName][$fieldName];

            if (isset($fieldDef['formula'])) {
                // check variables links
                $fieldsInFormula = Parser::getFieldsFromExpression($fieldDef['formula']);

                if (safeCount($fieldsInFormula)) {
                    $matches = array_unique($fieldsInFormula);

                    foreach ($matches as $lockedField) {
                        if ($fieldName === $lockedField) {
                            continue;
                        }

                        if (!isset($retFields[$moduleName][$fieldName])) {
                            $retFields[$moduleName][$fieldName] = [];
                        }

                        if (!isset($retFields[$moduleName][$fieldName]['locked'])) {
                            $retFields[$moduleName][$fieldName]['locked'] = [];
                        }

                        if (!isset($retFields[$moduleName][$fieldName]['locked'][$lockedField])) {
                            $retFields[$moduleName][$fieldName]['locked'][$lockedField] = [
                                'module' => $moduleName,
                                'field' => $fieldName,
                                'reason' => 'formula',
                            ];
                        }
                    }
                }
            }

            if (isset($fieldDef['related_fields'])) {
                foreach ($fieldDef['related_fields'] as $relatedField) {
                    if (!isset($retFields[$moduleName][$fieldName])) {
                        $retFields[$moduleName][$fieldName] = [];
                    }

                    if (!isset($retFields[$moduleName][$fieldName]['related'])) {
                        $retFields[$moduleName][$fieldName]['related'] = [];
                    }

                    if (!isset($retFields[$moduleName][$fieldName]['related'][$relatedField])) {
                        $retFields[$moduleName][$fieldName]['related'][$relatedField] = [
                            'module' => $moduleName,
                            'field' => $fieldName,
                            'reason' => 'related_fields',
                        ];
                    }
                }
            }
        }

        return $retFields;
    }

    /**
     * Gets the field dependencies list
     *
     * @return array
     */
    protected function getDependentFields()
    {
        $mm = MetaDataManager::getManager();

        $quotesFieldNames = array_merge(
            $mm->getModuleViewFields('Quotes', 'record'),
            $mm->getModuleViewFields('Quotes', 'quote-data-grand-totals-header'),
            $mm->getModuleViewFields('Quotes', 'quote-data-grand-totals-footer')
        );
        $pbFieldNames = array_merge(
            $mm->getModuleViewFields('ProductBundles', 'quote-data-group-header'),
            $mm->getModuleViewFields('ProductBundles', 'quote-data-group-footer')
        );
        $productsFieldNames = $mm->getModuleViewFields('Products', 'quote-data-group-list');

        $fieldVardefs = [
            'Quotes' => VardefManager::getFieldDefs('Quotes'),
            'ProductBundles' => VardefManager::getFieldDefs('ProductBundles'),
            'Products' => VardefManager::getFieldDefs('Products'),
        ];

        $qFields = $this->getDependenciesFromFields(
            'Quotes',
            $quotesFieldNames,
            $fieldVardefs
        );

        $pbFields = $this->getDependenciesFromFields(
            'ProductBundles',
            $pbFieldNames,
            $fieldVardefs
        );
        $pFields = $this->getDependenciesFromFields(
            'Products',
            $productsFieldNames,
            $fieldVardefs
        );

        $retFields = [
            'Quotes' => [],
            'ProductBundles' => [],
            'Products' => [],
        ];

        $retFields['Quotes'] = array_merge(
            $qFields['Quotes'] ?? [],
            $pbFields['Quotes'] ?? [],
            $pFields['Quotes'] ?? []
        );

        $retFields['ProductBundles'] = array_merge(
            $qFields['ProductBundles'] ?? [],
            $pbFields['ProductBundles'] ?? [],
            $pFields['ProductBundles'] ?? []
        );

        $retFields['Products'] = array_merge(
            $qFields['Products'] ?? [],
            $pbFields['Products'] ?? [],
            $pFields['Products'] ?? []
        );

        return $retFields;
    }

    /**
     * Gets the dependencies from a specific field and recurses any fields in it's field dependencies list
     *
     * @param string $moduleName The name of the module for the fieldDef we're parsing
     * @param array $fieldNames The names of all the fields to loop over and parse
     * @param array $fieldDefs The collection of all field definitions
     * @param array $retFields The map of all field dependencies we're returning
     */
    protected function getDependenciesFromFields(string $moduleName, array $fieldNames, array $fieldDefs)
    {
        $retFields = [];
        foreach ($fieldNames as $fieldName) {
            if (isset($fieldDefs[$moduleName][$fieldName])) {
                $fieldDef = $fieldDefs[$moduleName][$fieldName];

                if (isset($fieldDef['formula'])) {
                    $this->parseFieldFormula($moduleName, $fieldDef, $fieldDefs, $retFields);
                }

                if (isset($fieldDef['related_fields'])) {
                    $this->parseFieldRelatedFields($moduleName, $fieldDef, $fieldDefs, $retFields);
                }
            }
        }
        return $retFields;
    }

    /**
     * Parses a fieldDef's formula to pull out any variables or link fields
     *
     * @param string $moduleName The name of the module for the fieldDef we're parsing
     * @param array $fieldDef The Field definition to parse
     * @param array $fieldDefs The collection of all field definitions
     * @param array $retFields The map of all field dependencies we're returning
     */
    protected function parseFieldFormula(string $moduleName, array $fieldDef, array $fieldDefs, array &$retFields)
    {
        $matches = [];
        $fieldName = $fieldDef['name'];

        // check ProductBundles links
        $expr = Parser::evaluate($fieldDef['formula'], $this);
        $fields = Parser::getFormulaRelateFields($expr, 'product_bundles');

        //$fields = Parser::getFormulaRelateFields($fieldDef['formula']);
        if (safeCount($fields)) {
            foreach ($fields as $lockedField) {
                if (!isset($retFields['ProductBundles'][$lockedField])) {
                    $retFields['ProductBundles'][$lockedField] = [];
                }

                if (!isset($retFields['ProductBundles'][$lockedField]['locked'])) {
                    $retFields['ProductBundles'][$lockedField]['locked'] = [];
                }

                if (!isset($retFields['ProductBundles'][$lockedField]['locked'][$fieldName])) {
                    $retFields['ProductBundles'][$lockedField]['locked'][$fieldName] = [
                        'module' => $moduleName,
                        'field' => $fieldName,
                        'reason' => 'rollup',
                    ];
                }

                $rollupField = $fieldDefs['ProductBundles'][$lockedField];
                if (isset($rollupField['formula'])) {
                    $this->parseFieldFormula(
                        'ProductBundles',
                        $rollupField,
                        $fieldDefs,
                        $retFields
                    );
                }
                if (isset($rollupField['related_fields'])) {
                    $this->parseFieldRelatedFields(
                        'ProductBundles',
                        $rollupField,
                        $fieldDefs,
                        $retFields
                    );
                }
            }
        }

        // check Products links
        $expr = Parser::evaluate($fieldDef['formula'], $this);
        $fields = Parser::getFormulaRelateFields($expr, 'products');

        if (safeCount($fields)) {
            foreach ($fields as $lockedField) {
                if (!isset($retFields['Products'][$lockedField])) {
                    $retFields['Products'][$lockedField] = [];
                }

                if (!isset($retFields['Products'][$lockedField]['locked'])) {
                    $retFields['Products'][$lockedField]['locked'] = [];
                }

                if (!isset($retFields['Products'][$lockedField]['locked'][$fieldName])) {
                    $retFields['Products'][$lockedField]['locked'][$fieldName] = [
                        'module' => $moduleName,
                        'field' => $fieldName,
                        'reason' => 'rollup',
                    ];
                }

                $rollupField = $fieldDefs['Products'][$lockedField];
                if (isset($rollupField['formula'])) {
                    $this->parseFieldFormula(
                        'Products',
                        $rollupField,
                        $fieldDefs,
                        $retFields
                    );
                }
                if (isset($rollupField['related_fields'])) {
                    $this->parseFieldRelatedFields(
                        'Products',
                        $rollupField,
                        $fieldDefs,
                        $retFields
                    );
                }
            }
        }

        // check variables links
        $fieldsInFormula = Parser::getFieldsFromExpression($fieldDef['formula']);

        if (safeCount($fieldsInFormula)) {
            $matches = array_unique($fieldsInFormula);

            foreach ($matches as $lockedField) {
                if ($lockedField === 'products' ||
                    $lockedField === 'product_bundles' ||
                    $fieldName === $lockedField) {
                    continue;
                }

                if (!isset($retFields[$moduleName][$lockedField])) {
                    $retFields[$moduleName][$lockedField] = [];
                }

                if (!isset($retFields[$moduleName][$lockedField]['locked'])) {
                    $retFields[$moduleName][$lockedField]['locked'] = [];
                }

                if (!isset($retFields[$moduleName][$lockedField]['locked'][$fieldName])) {
                    $retFields[$moduleName][$lockedField]['locked'][$fieldName] = [
                        'module' => $moduleName,
                        'field' => $fieldName,
                        'reason' => 'formula',
                    ];
                }

                $rollupField = $fieldDefs[$moduleName][$lockedField];
                if (isset($rollupField['formula']) && $fieldName !== $lockedField) {
                    $this->parseFieldFormula(
                        $moduleName,
                        $rollupField,
                        $fieldDefs,
                        $retFields
                    );
                }
                if (isset($rollupField['related_fields']) && $fieldName !== $lockedField) {
                    $this->parseFieldRelatedFields($moduleName, $rollupField, $fieldDefs, $retFields);
                }
            }
        }
    }

    /**
     * Gets any related fields from a fieldDef
     *
     * @param string $moduleName The name of the module for the fieldDef we're parsing
     * @param array $fieldDef The Field definition to parse
     * @param array $fieldDefs The collection of all field definitions
     * @param array $retFields The map of all field dependencies we're returning
     */
    protected function parseFieldRelatedFields(string $moduleName, array $fieldDef, array $fieldDefs, array &$retFields)
    {
        $fieldName = $fieldDef['name'];

        foreach ($fieldDef['related_fields'] as $relatedField) {
            if (!isset($retFields[$moduleName][$relatedField])) {
                $retFields[$moduleName][$relatedField] = [];
            }

            if (!isset($retFields[$moduleName][$relatedField]['related'])) {
                $retFields[$moduleName][$relatedField]['related'] = [];
            }

            if (!isset($retFields[$moduleName][$relatedField]['related'][$fieldName])) {
                $retFields[$moduleName][$relatedField]['related'][$fieldName] = [
                    'module' => $moduleName,
                    'field' => $fieldName,
                    'reason' => 'related_fields',
                ];
            }
        }
    }

    /**
     * Applies the saved Quotes config.
     * This might be necessary to run independently of the config api for cases like updating the
     * quote record view in studio -- that would remove any related_fields updates we made here.
     *
     * @throws SugarApiExceptionInvalidParameter
     */
    public function applyWorksheetColumnsConfig()
    {
        // Grab the Quotes config, and verify that it contains worksheet columns
        // settings
        $settings = $this->getSettings();
        if (!array_key_exists('worksheet_columns', $settings) || !is_array($settings['worksheet_columns'])) {
            throw new \SugarApiExceptionInvalidParameter($GLOBALS['app_strings']['EXCEPTION_MISSING_WORKSHEET_COLUMNS']);
        } elseif (!array_key_exists('worksheet_columns_related_fields', $settings) ||
            !is_array($settings['worksheet_columns_related_fields'])) {
            throw new \SugarApiExceptionInvalidParameter($GLOBALS['app_strings']['EXCEPTION_MISSING_WORKSHEET_COLUMNS_RELATED_FIELDS']);
        }

        // Apply the worksheet columns to the various viewdefs that use them
        $viewdefManager = $this->getViewdefManager();
        $this->applyWorksheetColumnsToQuoteDataGroupListView($viewdefManager, $settings);
        $this->applyWorksheetColumnsToRecordViews($viewdefManager, $settings);
    }

    /**
     * Updates the quote-data-group-list view with the configured worksheet
     * columns fields
     *
     * @param ViewdefManager $viewdefManager used to interface with viewdefs (read/save)
     * @param array $settings the array of Quotes config settings
     */
    protected function applyWorksheetColumnsToQuoteDataGroupListView(ViewdefManager $viewdefManager, array $settings)
    {
        // Load the quote-data-group-list viewdef. If it does not contain the
        // key we need to update, then load from base
        $qlidatagrouplistdef = $viewdefManager->loadViewdef('base', 'Products', 'quote-data-group-list', false);
        if (!isset($qlidatagrouplistdef['panels'][0]['fields'])) {
            $qlidatagrouplistdef = $viewdefManager->loadViewdef('base', 'Products', 'quote-data-group-list', true);
        }

        // Update the viewdef metadata with the configured worksheet_columns fields
        $qlidatagrouplistdef['panels'][0]['fields'] = $settings['worksheet_columns'];
        $viewdefManager->saveViewdef($qlidatagrouplistdef, 'Products', 'base', 'quote-data-group-list');
    }

    /**
     * Updates Quotes record-style views (the base record view as well as mobile
     * record-like views) to include the configured worksheet columns fields in
     * the list of product_bundle_items fields
     *
     * @param ViewdefManager $viewdefManager used to interface with viewdefs (read/save)
     * @param array $settings the array of Quotes config settings
     */
    protected function applyWorksheetColumnsToRecordViews(ViewdefManager $viewdefManager, array $settings)
    {
        // Build the list of the new worksheet columns field names. Include the
        // configured fields and their related/dependent fields
        $columnNames = array_column($settings['worksheet_columns'], 'name');
        if (in_array('line_num', $columnNames)) {
            $columnNames = array_diff($columnNames, ['line_num']);
        }
        $worksheetColumns = array_merge($columnNames, $settings['worksheet_columns_related_fields']);

        // Write the worksheet columns to the base Quotes record view
        $this->writeWorksheetColumnsToRecordViewDef($viewdefManager, $worksheetColumns, 'base', 'Quotes', 'record');

        // Write the worksheet columns to the mobile Quotes detail and edit views
        // Prevent the campaign_name field as Campaigns is not available on mobile
        $mobileWorksheetColumns = array_diff($worksheetColumns, ['campaign_name']);
        $this->writeWorksheetColumnsToRecordViewDef($viewdefManager, $mobileWorksheetColumns, 'mobile', 'Quotes', 'detail');
        $this->writeWorksheetColumnsToRecordViewDef($viewdefManager, $mobileWorksheetColumns, 'mobile', 'Quotes', 'edit');
    }

    /**
     * Updates a record-style view that uses worksheet columns with a new
     * list of worksheet columns fields
     *
     * @param ViewdefManager $viewdefManager used to interface with viewdefs (read/save)
     * @param array $worksheetColumns the new list of worksheet columns
     * @param string $platform the platform of the viewdef to update
     * @param string $module the module of the viewdef to update
     * @param string $view the view name of the viewdef to update
     */
    protected function writeWorksheetColumnsToRecordViewDef(
        ViewdefManager $viewdefManager,
        array          $worksheetColumns,
        string         $platform,
        string         $module,
        string         $view
    ) {

        // Load the viewDef. If it does not contain the key we need to update,
        // then load from base/core
        $viewDef = $viewdefManager->loadViewdef($platform, $module, $view, false);
        if (!isset($viewDef['panels'][0]['fields'][1]['related_fields'][0]['fields'])) {
            $viewDef = $viewdefManager->loadViewdef($platform, $module, $view, true);
        }

        // Now that we know the related_fields[0]['fields'] exists, we need to
        // search that array for the array def for the product_bundle_items field
        $fieldsIndex = 0;
        foreach ($viewDef['panels'][0]['fields'][1]['related_fields'][0]['fields'] as $field) {
            if (!is_array($field)) {
                $fieldsIndex++;
                continue;
            } else {
                if (array_key_exists('name', $field) &&
                    $field['name'] == 'product_bundle_items' &&
                    array_key_exists('fields', $field)) {
                    $viewDef['panels'][0]['fields'][1]['related_fields'][0]['fields'][$fieldsIndex]['fields'] =
                        $worksheetColumns;
                }
                break;
            }
        }

        // Write the updated viewdef to the file system
        $viewdefManager->saveViewdef($viewDef, $module, $platform, $view);
    }

    /**
     * Updates the Quotes worksheet_columns mobile config property to remove the
     * unwanted `campaign_name` field.
     * @param array $data Worksheet columns data from config
     * @return void
     */
    public function saveMobileWorksheetColumnConfig(array $data): void
    {
        $worksheet_columns = $data['worksheet_columns'];

        // Get the key for the data array to see if there is a campaign_name field added
        $key = array_search('campaign_name', array_column($worksheet_columns, 'name'));
        if ($key !== false) {
            // Remove the campaign field from the fields array
            array_splice($worksheet_columns, $key, 1);

            // Save the modified fields in the mobile config
            $adminBean = BeanFactory::newBean('Administration');
            $adminBean->saveSetting('Quotes', 'worksheet_columns', $worksheet_columns, 'mobile');
        }
    }

    /**
     * Applies the saved Quotes config for Summary columns sheet.
     * This might be necessary to run independently of the config api for cases like updating the
     * quote record view in studio -- that would remove any related_fields updates we made here.
     *
     * @throws SugarApiExceptionInvalidParameter
     */
    public function applySummaryColumnsConfig()
    {
        $viewdefManager = $this->getViewdefManager();
        $settings = $this->getSettings();

        if (!array_key_exists('summary_columns', $settings) || !is_array($settings['summary_columns'])) {
            throw new \SugarApiExceptionInvalidParameter($GLOBALS['app_strings']['EXCEPTION_MISSING_SUMMARY_COLUMNS']);
        }

        if (!array_key_exists('summary_columns_related_fields', $settings) ||
            !is_array($settings['summary_columns_related_fields'])) {
            throw new \SugarApiExceptionInvalidParameter($GLOBALS['app_strings']['EXCEPTION_MISSING_SUMMARY_COLUMNS_RELATED_FIELDS']);
        }

        //update products c/b/v/quote-data-grand-totals-header with new fields for summary_columns
        //load viewdefs
        $qlidatagrouplistdef = $viewdefManager->loadViewdef('base', 'Quotes', 'quote-data-grand-totals-header');

        //check to see if the key we need to update exists in the loaded viewdef, if not, load the base.
        if (!isset($qlidatagrouplistdef['panels'][0]['fields'])) {
            $qlidatagrouplistdef = $viewdefManager->loadViewdef('base', 'Quotes', 'quote-data-grand-totals-header', true);
        }

        foreach ($settings['summary_columns'] as $key => $summaryField) {
            if ($summaryField['type'] === 'varchar') {
                unset($settings['summary_columns'][$key]['type']);
            }

            if (isset($summaryField['css_class']) && !empty($summaryField['css_class'])) {
                continue;
            } else {
                $settings['summary_columns'][$key]['css_class'] = 'quote-totals-row-item';
            }
        }

        $qlidatagrouplistdef['panels'][0]['fields'] = $settings['summary_columns'];
        $viewdefManager->saveViewdef($qlidatagrouplistdef, 'Quotes', 'base', 'quote-data-grand-totals-header');
    }

    /**
     * Applies the saved Quotes config for Grand Totals Footer rows.
     *
     * @throws SugarApiExceptionInvalidParameter
     */
    public function applyFooterRowsConfig()
    {
        $viewdefManager = $this->getViewdefManager();
        $settings = $this->getSettings();

        if (!array_key_exists('footer_rows', $settings) || !is_array($settings['footer_rows'])) {
            throw new \SugarApiExceptionInvalidParameter($GLOBALS['app_strings']['EXCEPTION_MISSING_FOOTER_ROWS']);
        }

        if (!array_key_exists('footer_rows_related_fields', $settings) ||
            !is_array($settings['footer_rows_related_fields'])) {
            throw new \SugarApiExceptionInvalidParameter($GLOBALS['app_strings']['EXCEPTION_MISSING_FOOTER_ROWS_RELATED_FIELDS']);
        }

        //update products c/b/v/quote-data-grand-totals-footer with new fields for footer_rows
        //load viewdefs
        $quoteDataGroupListDef = $viewdefManager->loadViewdef('base', 'Quotes', 'quote-data-grand-totals-footer');

        //check to see if the key we need to update exists in the loaded viewdef, if not, load the base.
        if (!isset($quoteDataGroupListDef['panels'][0]['fields'])) {
            $quoteDataGroupListDef = $viewdefManager->loadViewdef('base', 'Quotes', 'quote-data-grand-totals-footer', true);
        }

        $quoteDataGroupListDef['panels'][0]['fields'] = $settings['footer_rows'];
        $viewdefManager->saveViewdef($quoteDataGroupListDef, 'Quotes', 'base', 'quote-data-grand-totals-footer');
    }

    /**
     * abstraction for getting a new instance of ViewdefManager
     *
     * @return Sugarcrm\Sugarcrm\MetaData\ViewdefManager
     */
    protected function getViewdefManager()
    {
        return new ViewdefManager();
    }

    /**
     * abstraction of retreiving settings for the quotes module.
     *
     * @return array settings
     */
    protected function getSettings()
    {
        $admin = BeanFactory::newBean('Administration');
        return $admin->getConfigForModule('Quotes');
    }
}
