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
 * Used to evaluate related expressions on the front end for arbitrary (possibly unsaved) records.
 */
class RelatedValueApi extends SugarApi
{
    /**
     * Rest Api Registration Method
     *
     * @return array
     */
    public function registerApiRest()
    {
        $parentApi = [
            'related_value_deprecated' => [
                'reqType' => 'GET',
                'path' => ['ExpressionEngine', '?', 'related'],
                'pathVars' => ['', 'record', ''],
                'method' => 'deprecatedGetRelatedValues',
                'shortHelp' => 'Retrieve the Chart data for the given data in the Forecast Module (deprecated)',
                'longHelp' => 'modules/Forecasts/clients/base/api/help/ForecastChartApi.html',
                'maxVersion' => '12',
            ],
            'related_value' => [
                'reqType' => 'POST',
                'path' => ['ExpressionEngine', '?', 'related'],
                'pathVars' => ['', 'record', ''],
                'method' => 'getRelatedValues',
                'shortHelp' => 'Retrieves the related fields for a given module record',
                'longHelp' => 'modules/ExpressionEngine/clients/base/api/help/related_value_api_post_help.html',
                'minVersion' => '11.8',
            ],
        ];
        return $parentApi;
    }

    /**
     * Extends the functionality of getRelatedValues to produce a warning for
     * the deprecated GET ExpressionEngine/:record/related endpoint
     */
    public function deprecatedGetRelatedValues(ServiceBase $api, array $args)
    {
        LoggerManager::getLogger()->deprecated(
            'GET ExpressionEngine/:record/related is deprecated as of 10.0. 
            Use POST ExpressionEngine/:record/related instead.'
        );
        return $this->getRelatedValues($api, $args);
    }

    /**
     * Used by the dependency manager to pre-load all the related fields required
     * to load an entire view.
     */
    public function getRelatedValues(ServiceBase $api, array $args)
    {
        $ret = [];
        if (empty($args['module']) || empty($args['fields'])) {
            return $ret;
        }
        if (is_array($args['fields'])) {
            $fields = $args['fields'];
        } else {
            $fields = json_decode(html_entity_decode($args['fields'], ENT_COMPAT), true);
        }
        $focus = $this->loadBean($api, $args);
        foreach ($fields as $rfDef) {
            if (!isset($rfDef['link']) || !isset($rfDef['type'])) {
                continue;
            }
            $link = $rfDef['link'];
            $type = $rfDef['type'];
            $rField = '';
            if (!isset($ret[$link])) {
                $ret[$link] = [];
            }
            if (empty($ret[$link][$type])) {
                $ret[$link][$type] = [];
            }
            // count formulas don't have a relate attribute
            if (isset($rfDef['relate'])) {
                $rField = $rfDef['relate'];
            }

            // Switch the type to the correct name
            if ($type == 'rollupAvg') {
                $type = 'rollupAve';
            } elseif ($type == 'rollupCurrencySum') {
                $type = 'rollupSum';
            }

            switch ($type) {
                //The Related function is used for pulling a sing field from a related record
                case 'related':
                    //Default it to a blank value
                    $ret[$link]['related'][$rfDef['relate']] = '';

                    //If we have neither a focus id nor a related record id, we can't retrieve anything
                    $relBean = null;
                    if (empty($rfDef['relId']) || empty($rfDef['relModule'])) {
                        //If the relationship is invalid, just move onto another field
                        if (!$focus->load_relationship($link)) {
                            break;
                        }

                        $beans = $focus->$link->getBeansForSugarLogic();
                        //No related beans means no value
                        if (empty($beans)) {
                            break;
                        }
                        //Grab the first bean on the list
                        reset($beans);
                        $relBean = current($beans);
                    } else {
                        $relBean = BeanFactory::getBean($rfDef['relModule'], $rfDef['relId']);
                    }
                    //If we found a bean and the current user has access to the related field, grab a value from it
                    if (!empty($relBean) && ACLField::hasAccess($rfDef['relate'], $relBean->module_dir, $GLOBALS['current_user']->id, true)) {
                        $validFields = FormulaHelper::cleanFields($relBean->field_defs, false, true, true);
                        if (isset($validFields[$rfDef['relate']])) {
                            $ret[$link]['relId'] = $relBean->id;
                            $ret[$link]['related'][$rfDef['relate']] =
                                FormulaHelper::getFieldValue($relBean, $rfDef['relate']);
                        }
                    }

                    break;
                case 'count':
                    if ($focus->load_relationship($link)) {
                        $ret[$link][$type] = safeCount($focus->$link->get());
                    } else {
                        $ret[$link][$type] = 0;
                    }
                    break;
                case 'rollupSum':
                case 'rollupAve':
                case 'rollupMin':
                case 'rollupMax':
                    //If we are going to calculate one rollup, calculate all the rollups since there is so little cost
                    if ($focus->load_relationship($link)) {
                        $relBeans = $focus->$link->getBeansForSugarLogic();
                        $sum = 0;
                        $count = 0;
                        $min = false;
                        $max = false;
                        $values = [];
                        if (!empty($relBeans)) {
                            //Check if the related record vardef has banned this field from formulas
                            $relBean = reset($relBeans);
                            $validFields = FormulaHelper::cleanFields($relBean->field_defs, false, true, true);
                            if (!isset($validFields[$rField])) {
                                $ret[$link][$type][$rField] = 0;
                                break;
                            }
                        }

                        $isCurrency = null;

                        foreach ($relBeans as $bean) {
                            if (isset($bean->$rField) && is_numeric($bean->$rField) &&
                                //ensure the user can access the fields we are using.
                                ACLField::hasAccess($rField, $bean->module_dir, $GLOBALS['current_user']->id, true)
                            ) {
                                if (is_null($isCurrency)) {
                                    $isCurrency = $this->isFieldCurrency($bean, $rField);
                                }

                                $count++;

                                $value = $bean->$rField;
                                if ($isCurrency) {
                                    $value = SugarCurrency::convertWithRate($value, $bean->base_rate);
                                }

                                $sum = SugarMath::init($sum)->add($value)->result();
                                if ($min === false || floatval($value) < floatval($min)) {
                                    $min = $value;
                                }
                                if ($max === false || floatval($value) > floatval($max)) {
                                    $max = $value;
                                }
                                $values[$bean->id] = $value;
                            }
                        }
                        if ($type == 'rollupSum') {
                            $ret[$link][$type][$rField] = $sum;
                            $ret[$link][$type][$rField . '_values'] = $values;
                        }
                        if ($type == 'rollupAve') {
                            $ret[$link][$type][$rField] = $count == 0 ? 0 : SugarMath::init($sum)->div($count)->result();
                            $ret[$link][$type][$rField . '_values'] = $values;
                        }
                        if ($type == 'rollupMin') {
                            $ret[$link][$type][$rField] = $min;
                            $ret[$link][$type][$rField . '_values'] = $values;
                        }
                        if ($type == 'rollupMax') {
                            $ret[$link][$type][$rField] = $max;
                            $ret[$link][$type][$rField . '_values'] = $values;
                        }
                    } else {
                        $ret[$link][$type][$rField] = 0;
                    }
                    break;
                case 'countConditional':
                    $sum = 0;
                    $values = [];

                    if ($focus->load_relationship($link)) {
                        $condition_values = Parser::evaluate($rfDef['condition_expr'])->evaluate();
                        $relBeans = $focus->$link->getBeansForSugarLogic();

                        foreach ($relBeans as $bean) {
                            if (safeInArray($bean->{$rfDef['condition_field']}, $condition_values)) {
                                $sum++;
                                $values[$bean->id] = true;
                            }
                        }
                    }
                    // for countConditional, we use the target field, since there can have more than one
                    // on the same record.
                    if (isset($rfDef['target'])) {
                        $ret[$link][$type][$rfDef['target']] = $sum;
                        $ret[$link][$type][$rfDef['target'] . '_values'] = $values;
                    } else {
                        $ret[$link][$type] = $sum;
                    }
                    break;
                case 'rollupConditionalSum':
                    $ret[$link][$type][$rField] = '0';
                    $values = [];

                    if ($focus->load_relationship($link)) {
                        if (preg_match('/^[a-zA-Z0-9_\-$]+\(.*\)$/', $rfDef['condition_expr'])) {
                            $condition_values = Parser::evaluate($rfDef['condition_expr'])->evaluate();
                        } else {
                            $condition_values = [$rfDef['condition_expr']];
                        }
                        $toRate = $focus->base_rate ?? null;
                        $relBeans = $focus->$link->getBeansForSugarLogic();
                        $sum = '0';
                        $isCurrency = null;
                        foreach ($relBeans as $bean) {
                            if (!empty($bean->$rField) && is_numeric($bean->$rField) &&
                                //ensure the user can access the fields we are using.
                                ACLField::hasAccess($rField, $bean->module_dir, $GLOBALS['current_user']->id, true)
                            ) {
                                if (safeInArray($bean->{$rfDef['condition_field']}, $condition_values)) {
                                    if (is_null($isCurrency)) {
                                        $isCurrency = $this->isFieldCurrency($bean, $rField);
                                    }
                                    $value = $bean->$rField;
                                    if ($isCurrency) {
                                        $value = SugarCurrency::convertWithRate($value, $bean->base_rate, $toRate);
                                    }
                                    $sum = SugarMath::init($sum)->add(
                                        $value
                                    )->result();
                                    $values[$bean->id] = $value;
                                }
                            }
                        }
                        $ret[$link][$type][$rField] = $sum;
                        // To avoid the values getting overridden in case of different categories within the condition
                        if (!empty($ret[$link][$type][$rField . '_values'])) {
                            $ret[$link][$type][$rField . '_values'] = array_merge($ret[$link][$type][$rField . '_values'], $values);
                        } else {
                            $ret[$link][$type][$rField . '_values'] = $values;
                        }
                    }
                    break;
                case 'rollupConditionalMinDate':
                    // This function is similar to maxRelatedDate, so rather than
                    // copying its code, set a flag to indicate this is a
                    // rollupConditionalMinDate and fall through
                    $isRollupMinDate = true;

                    // Parse the conditions
                    $conditions = [];
                    foreach ($rfDef['conditionFields'] as $index => $conditionField) {
                        if (is_array($rfDef['conditionValues'][$index])) {
                            $conditions[$conditionField] = $rfDef['conditionValues'][$index];
                        } else {
                            $conditions[$conditionField] = [$rfDef['conditionValues'][$index]];
                        }
                    }
                    // Fall through
                    // no break
                case 'maxRelatedDate':
                    $ret[$link][$type][$rField] = '';
                    if ($focus->load_relationship($link)) {
                        $td = TimeDate::getInstance();
                        $isTimestamp = true;
                        $resDate = 0;
                        $relBeans = $focus->$link->getBeansForSugarLogic();
                        $valueMap = [];
                        foreach ($relBeans as $bean) {
                            // If this is a rollupConditionalMinDate, make sure the bean conditions hold
                            if (!empty($isRollupMinDate) && !empty($conditions) && is_array($conditions)) {
                                foreach ($conditions as $conField => $conValues) {
                                    if (!safeInArray($bean->$conField, $conValues)) {
                                        continue 2;
                                    }
                                }
                            }
                            if (ACLField::hasAccess($rField, $bean->module_dir, $GLOBALS['current_user']->id, true)
                            ) {
                                // we have to use the fetched_row as it's still in db format
                                // where as the $bean->$relfield is formatted into the users format.
                                if (isset($bean->fetched_row[$rField])) {
                                    $value = $bean->fetched_row[$rField];
                                } elseif (isset($bean->$rField)) {
                                    if (is_int($bean->$rField)) {
                                        // if we have a timestamp field, just set the value
                                        $value = $bean->relfield;
                                    } elseif (empty($bean->$rField)) {
                                        continue;
                                    } else {
                                        // more than likely this is a date field, so try and un-format based on the users preferences
                                        // we pass false to asDbDate as we want the value that would be stored in the DB
                                        $value = $td->fromString($bean->$rField)->asDbDate(false);
                                    }
                                } else {
                                    continue;
                                }

                                $valueMap[$bean->id] = $value;

                                //if it isn't a timestamp, mark the flag as such and convert it for comparison
                                if (!is_int($value)) {
                                    $isTimestamp = false;
                                    $value = strtotime($value);
                                }

                                // Do the proper comparison depending on whether we are looking for a min or max date
                                if (!empty($isRollupMinDate)) {
                                    if ($resDate === 0 || $value < $resDate) {
                                        $resDate = $value;
                                    }
                                } else {
                                    if ($resDate < $value) {
                                        $resDate = $value;
                                    }
                                }
                            }
                        }

                        //if nothing was done, return an empty string
                        if ($resDate == 0 && $isTimestamp) {
                            $resDate = '';
                        } elseif ($isTimestamp === false) {
                            $date = new DateTime();
                            $date->setTimestamp($resDate);

                            $resDate = $date->format('Y-m-d');
                        }


                        $ret[$link][$type][$rField] = $resDate;
                        $ret[$link][$type][$rField . '_values'] = $valueMap;
                    }
                    break;
            }
        }

        return $ret;
    }

    /**
     * Test if the current field is a currency field
     *
     * @param SugarBean $bean The Bean to which the Field Belongs
     * @param string $field The name of the field
     * @return bool
     */
    protected function isFieldCurrency(SugarBean $bean, $field)
    {
        $def = $bean->getFieldDefinition($field);
        // start by just using the type in the def
        $def_type = $def['type'];
        // but if custom_type is set, use it, when it's not set and dbType is, use dbType
        if (isset($def['custom_type']) && !empty($def['custom_type'])) {
            $def_type = $def['custom_type'];
        } elseif (isset($def['dbType']) && !empty($def['dbType'])) {
            $def_type = $def['dbType'];
        }
        // always lower case the type just to make sure.
        return (strtolower($def_type) === 'currency');
    }
}
