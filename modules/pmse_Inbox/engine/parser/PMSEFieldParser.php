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

use Sugarcrm\Sugarcrm\ProcessManager;
use Sugarcrm\Sugarcrm\ProcessManager\Registry;

/**
 * Class that analyzes the data type of a bean
 * getting the value of this field according to the data type
 * if there is a date data type used the classes TimeDate()
 *
 */
class PMSEFieldParser extends PMSEAbstractDataParser implements PMSEDataParserInterface
{
    /**
     * @var mixed|\TimeDate|object
     */
    public $timeDate;
    /**
     * Object Bean
     * @var object
     */
    private $evaluatedBean;

    /**
     * Related bean to the evaluated bean
     * @deprecated since version 7.11.0.0
     * @var SugarBean
     */
    protected $relatedBean;

    /**
     * Related beans to the evaluated bean
     * @var array
     */
    protected $relatedBeans;

    /**
     * Lists modules Bean
     * @var array
     */
    private $beanList;
    private $currentUser;
    private $pmseRelatedModule;

    /**
     * List of token parse methods
     * @var array
     */
    public $tokenMethods = [
        'current_user' => 'parseCurrentUser',
        'supervisor' => 'parseSupervisor',
        'owner' => 'parseOwner',
    ];

    /**
     * Getting the PMSERelatedModule object
     * @return object
     */
    private function getRelatedModuleObject()
    {
        if (!isset($this->pmseRelatedModule)) {
            $this->pmseRelatedModule = ProcessManager\Factory::getPMSEObject('PMSERelatedModule');
        }
        return $this->pmseRelatedModule;
    }

    /**
     * gets the bean list
     * @return array
     * @codeCoverageIgnore
     */
    public function getBeanList()
    {
        return $this->beanList;
    }

    /**
     * sets the bean list
     * @param array $beanList
     */
    public function setBeanList($beanList)
    {
        $this->beanList = $beanList;
    }

    /**
     * gets the bean
     * @return object
     * @codeCoverageIgnore
     */
    public function getEvaluatedBean()
    {
        return $this->evaluatedBean;
    }

    /**
     * sets the bean
     * @param object $evaluatedBean
     */
    public function setEvaluatedBean($evaluatedBean)
    {
        $this->evaluatedBean = $evaluatedBean;
        $this->relatedBean = null;
        $this->relatedBeans = [];
    }

    /**
     * sets the current user
     * @param object $currentUser
     * @codeCoverageIgnore
     */
    public function setCurrentUser($currentUser)
    {
        $this->currentUser = $currentUser;
    }

    /**
     * get the class TimeDate()
     * @return object
     * @codeCoverageIgnore
     */
    public function getTimeDate()
    {
        if (!isset($this->timeDate) || empty($this->timeDate)) {
            $this->timeDate = new TimeDate();
        }
        return $this->timeDate;
    }

    /**
     * set the class TimeDate()
     * @param object $timeDate
     * @codeCoverageIgnore
     */
    public function setTimeDate($timeDate)
    {
        $this->timeDate = $timeDate;
    }

    /**
     * Parser token incorporando el tipo de dato, en el caso de tipo de dato date, datetime se usa la clase TimeDate
     * @param object $criteriaToken token to be parsed
     * @param array $params
     * @return object
     * @global object $current_user cuurrent user
     */
    public function parseCriteriaToken($criteriaToken, $params = [])
    {
        if ($criteriaToken->expType === 'VARIABLE') {
            $criteriaToken = $this->parseVariable($criteriaToken, $params);
        } else {
            $criteriaToken = $this->parseCriteria($criteriaToken, $params);
        }
        return $criteriaToken;
    }

    /**
     * Gets the correct working SugarBean
     * @param stdClass $criteriaToken
     * @return SugarBean
     */
    protected function getWorkingBean($criteriaToken)
    {
        $bean = $this->evaluatedBean;
        // We need an expModule to do the rest of the checking
        // So if it is absent, send back the evaluated bean
        if (!isset($criteriaToken->expModule)) {
            return $bean;
        }

        // Get the module for the criteria
        $criteriaMod = BeanFactory::getBeanClass($criteriaToken->expModule);
        // If there is no criteria module then it is a link
        // If we have a criteria module, but it is not the evaluated bean,
        // it is a link
        if ($criteriaMod === false || !$bean instanceof $criteriaMod) {
            $rels = $this->getRelatedBean($criteriaToken->expModule);
            $bean = array_shift($rels);
        }

        // Bean will now either be the evaluated bean, or the correct related bean
        // or a false
        return $bean;
    }

    /**
     * Check if the expRel is one of relationship change condition and check if event was trigger with
     * one of the appropriate after_relationship hooks
     * @param string $expRel
     * @param string $event
     * @return bool
     */
    public function checkRelationshipChange($expRel = '', $event = '')
    {
        switch ($expRel) {
            case 'Added':
                return ($event === 'after_relationship_add');
            case 'Removed':
                return ($event === 'after_relationship_delete');
            case 'AddedOrRemoved':
                return ($event === 'after_relationship_add' || $event === 'after_relationship_delete');
            default:
                return true;
        }
    }

    /**
     * Takes in a criteria object and updates it as needed
     * @param stdClass $criteriaToken Object made from definitions
     * @param array $params
     * @return stdClass
     */
    public function parseCriteria($criteriaToken, $params = [])
    {
        $tokenArray = [
            $criteriaToken->expModule,
            $criteriaToken->expField,
            $criteriaToken->expOperator,
        ];

        $criteriaToken->currentValue = $this->parseTokenValue($tokenArray, $params);
        $criteriaToken->expValue = $this->setExpValueFromCriteria($criteriaToken);

        if (isset($criteriaToken->expField)) {
            // Get the proper bean to use for eval
            $bean = $this->getWorkingBean($criteriaToken);

            // If there is no working bean send back the criteria token as is
            if (!$bean) {
                return $criteriaToken;
            }

            // Check the criteria field type and set data as needed
            $type = '';
            if (isset($bean->field_defs[$criteriaToken->expField]['type'])) {
                $type = $bean->field_defs[$criteriaToken->expField]['type'];
            }
            $dates = ['date', 'datetime', 'datetimecombo'];
            if (in_array($type, $dates)) {
                $criteriaToken->expSubtype = 'date';
            } elseif ($type === 'currency') {
                $criteriaToken->expValue = $this->setCurrentValueIfCurrency($criteriaToken, $bean);
            }
        }

        return $criteriaToken;
    }

    /**
     * Parse the token using a new function to parse variable tokens
     * @param type $criteriaToken
     * @param type $params
     * @return type
     * @global object $current_user
     */
    public function parseVariable($criteriaToken, $params = [])
    {
        $tokenArray = [
            $criteriaToken->expModule ?? null,
            $criteriaToken->expValue ?? null,
            $criteriaToken->expOperator ?? null,
        ];
        $tokenValue = $this->parseTokenValue($tokenArray);
        $tokenValue = $tokenValue[0];
        if ($criteriaToken->expSubtype == 'Currency') {
            $value = json_decode($tokenValue);
            // in some use cases, value can be numeric instead of array
            // so need to handle currency_id and amount accordingly
            if (!empty($value['currency_id'])) {
                $criteriaToken->expField = $value['currency_id'];
            } elseif (isset($this->evaluatedBean->currency_id)) {
                $criteriaToken->expField = $this->evaluatedBean->currency_id;
            }
            if (!empty($value['amount'])) {
                $criteriaToken->currentValue = $value['amount'];
            } else {
                $criteriaToken->currentValue = $tokenValue;
            }
        } else {
            $criteriaToken->currentValue = $tokenValue;
        }
        if (isset($criteriaToken->expField)) {
            if (isset($this->evaluatedBean->field_defs[$criteriaToken->expField])) {
                if ($this->evaluatedBean->field_defs[$criteriaToken->expField]['type'] == 'date') {
                    $criteriaToken->expSubtype = 'date';
                } elseif ($this->evaluatedBean->field_defs[$criteriaToken->expField]['type'] == 'datetime'
                    || $this->evaluatedBean->field_defs[$criteriaToken->expField]['type'] == 'datetimecombo'
                ) {
                    $criteriaToken->expSubtype = 'date';
                }
            }
        }
        $criteriaToken->expValue = $criteriaToken->currentValue;
        return $criteriaToken;
    }

    /**
     * Gets the related bean to the evaluated bean, if one is set
     * @param string $link The link name to get the related bean from
     * @return array SugarBeans
     */
    public function getRelatedBean($link)
    {
        if (empty($this->relatedBeans[$link])) {
            // There are times when the process bean is not the bean needed for
            // evaluations
            $bean = $this->getBeanForEvaluation();

            // Get and set the related bean since we don't have it yet
            $this->relatedBeans[$link] = $this->getRelatedModuleObject()->getRelatedModuleBeans($bean, $link);
        }

        return $this->relatedBeans[$link];
    }

    /**
     * Get the bean due to which the relationship change was triggered
     * @param string $link The link name to get the related bean from
     * @return array
     */
    public function getRelationshipChangeBean($link)
    {
        if (empty($this->relatedBeans[$link])) {
            $args = PMSEBaseValidator::getLogicHookArgs();
            $expRel = $this->criteriaToken->expRel ?? '';
            $event = $args['event'] ?? '';
            if (((isset($args['link']) && $link === $args['link']) || (isset($args['module']) && $args['module'] === $link)) &&
                $this->checkRelationshipChange($expRel, $event)) {
                $relBean = BeanFactory::retrieveBean($args['related_module'], $args['related_id']);
                if (!is_null($relBean)) {
                    return [$relBean];
                }
            }
        }
        return [];
    }

    /**
     * parser a token for a field element, is this: bool or custom fields
     * @param array $token field contains a parser
     * @return array of field values, in the case of a currency type it returns a serialized array with the amount and
     * the currency id.
     */
    public function parseTokenValue($token, $params = [])
    {
        $values = [];

        if (!empty($token)) {
            // This logic is a fairly bad assumption, but works in most cases. The
            // assumption is that a link name won't be in the bean list so try to load
            // a related bean instead.
            $expRel = $this->criteriaToken->expRel ?? '';
            $relationshipChange = in_array($expRel, ['Added', 'Removed', 'AddedOrRemoved']);
            if ($relationshipChange) {
                $beans = $this->getRelationshipChangeBean($token[0]);
                $bean = reset($beans);
                if (!empty($bean)) {
                    PMSEEngineUtils::setRegistry($bean, false);
                }
            } elseif (!isset($this->beanList[$token[0]]) && empty($params['useEvaluatedBean'])) {
                // Get the related bean instead
                $beans = $this->getRelatedBean($token[0]);
                // Required for wait event timer and business rules to get business center
                // currently we only support one related bean at one time
                // business rule conditions may require multiple beans at the same time, which is not supported yet
                $bean = reset($beans);
                if (!empty($bean)) {
                    PMSEEngineUtils::setRegistry($bean, false);
                }
            } else {
                $beans = [$this->evaluatedBean];
            }

            if (is_array($beans)) {
                $field = $token[1];
                foreach ($beans as $bean) {
                    if (isset($token[2]) && in_array($token[2], ['changes', 'changes_from', 'changes_to'])) {
                        // Get the bean data changes
                        $bdc = empty($bean->dataChanges) ? [] : $bean->dataChanges;

                        // Get the registry key of changs for this bean
                        $key = 'bean-data-changes-' . $bean->id;

                        // Get the registry data changes and merge the bean data changes into it
                        $rdc = Registry\Registry::getInstance()->get($key, []);
                        $dataChanges = array_merge($rdc, $bdc);

                        // Handle what is changing and to which
                        if (isset($dataChanges[$field])) {
                            if ($token[2] == 'changes_from') {
                                $value = $dataChanges[$field]['before'];
                            } else {
                                $value = $dataChanges[$field]['after'];
                            }
                            // The value coming from $bean->dataChanges can be null.
                            // This happens to text fields (text, textarea, url, etc), date fields (date, datetime)
                            // and dropdown. The number fields (int, float, decimal) seem to have default 0, not null.
                            // But we should not use null because null indicates no changes.
                            // See the comment below inside the else block
                            if ($value === null) {
                                $value = '';
                            }
                        } else {
                            // used as a flag that means no changes
                            $value = null;
                        }
                    } else {
                        $value = $this->getRelatedModuleObject()->getFieldValue($bean, $field);
                    }
                    $values[] = $value;
                }
            }
        }

        return $values;
    }

    /**
     * converts a string {:: future :: Users :: id ::} to an array ('future','Users','id')
     * @param string $token @example {:: future :: Users :: id ::}
     * @return array
     */
    public function decomposeToken($token)
    {
        $response = [];
        $tokenArray = explode('::', $token);
        foreach ($tokenArray as $key => $value) {
            if ($value != '{' && $value != '}' && !empty($value)) {
                $response[] = $value;
            }
        }
        return $response;
    }

    /**
     * Checks to see if there is a parser method for this token
     *
     * @param object $token Criteria token object
     * @return boolean True if there is a method for this token criteria
     */
    public function hasParseMethod($token)
    {
        return !empty($token->expValue) && !is_array($token->expValue)
            && isset($this->tokenMethods[$token->expValue])
            && method_exists($this, $this->tokenMethods[$token->expValue]);
    }

    /**
     * Parses the token value for a User field element
     * @param object $token field contains a parser
     * @return string field value
     */
    public function setExpValueFromCriteria($token)
    {
        if ($this->hasParseMethod($token)) {
            $method = $this->tokenMethods[$token->expValue];
            return $this::$method($token);
        }

        return $token->expValue;
    }

    /**
     * Parse the token value for Currency
     * @param $token
     * @return float
     */
    public function setCurrentValueIfCurrency($token, SugarBean $bean = null)
    {
        // If there is no bean presented use the evaluated bean
        if ($bean === null) {
            $bean = $this->evaluatedBean;
        }

        // Get the default currency ID if we don't have one set
        $expCurrency = empty($token->expCurrency) ? '-99' : $token->expCurrency;

        // Get the currency from the bean
        $defCurrency = SugarCurrency::getCurrency($bean);

        // Convert the currency to a value
        $amount = SugarCurrency::convertAmount((float)$token->expValue, $expCurrency, $defCurrency->id);

        return $amount;
    }

    /**
     * Gets current user id or criteria token expected value
     *
     * @param object $token field contains a parser
     * @return string field value
     */
    public function parseCurrentUser($token)
    {
        return !empty($this->currentUser->id) ?
            $this->currentUser->id : $token->expValue;
    }

    /**
     * Gets assigned user id or criteria token expected value
     *
     * @param object $token field contains a parser
     * @return string field value
     */
    public function parseOwner($token)
    {
        return !empty($this->evaluatedBean->assigned_user_id) ?
            $this->evaluatedBean->assigned_user_id : $token->expValue;
    }

    /**
     * Gets reports to id or criteria token expected value
     *
     * @param object $token field contains a parser
     * @return string field value
     */
    public function parseSupervisor($token)
    {
        return !empty($this->currentUser->reports_to_id) ?
            $this->currentUser->reports_to_id : $token->expValue;
    }

    /**
     * Gets the proper bean to work on for evaluation
     * @return SugarBean
     */
    protected function getBeanForEvaluation()
    {
        // This is simply a shortcut to make lines shorter
        $ct = $this->criteriaToken;

        // These items are set onto the token in {{@see PMSEBaseValidator::updateRelateCriteria}}
        if (!empty($ct->expBeanModule) && !empty($ct->expBeanId)) {
            if (isset($ct->expLinkName) && $ct->expLinkName === $ct->expModule) {
                $bean = BeanFactory::retrieveBean($ct->expBeanModule, $ct->expBeanId);
                if ($bean) {
                    return $bean;
                }
            }
        }

        return $this->evaluatedBean;
    }
}
