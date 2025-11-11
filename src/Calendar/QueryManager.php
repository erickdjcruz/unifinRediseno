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

namespace Sugarcrm\Sugarcrm\Calendar;

/**
 * Used for default functionality of retrieving events from a module, most use cases
 */
class QueryManager extends \FilterApi
{
    /**
     * @var mixed|array<string, mixed>
     */
    public $params;
    /**
     * @var \SugarBean|null|mixed
     */
    public $targetBean;
    /**
     * @var \Sugarcrm\Sugarcrm\Calendar\CalendarFilterManager|mixed
     */
    public $filterManager;
    /**
     * @var mixed[]|mixed
     */
    public $defaultFilters;
    /**
     * @var \LoggerManager|mixed
     */
    public $logger;
    /**
     * @var \SugarQuery|mixed
     */
    public $query;
    public $calendarBean = null;

    /**
     * Set up manager
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $targetModule = $options['calendarBean']->calendar_module;
        $this->params = $options['params'];
        $this->calendarBean = $options['calendarBean'];
        $this->targetBean = \BeanFactory::newBean($targetModule);

        $this->filterManager = new CalendarFilterManager();
        $this->defaultFilters = $this->filterManager->getDefaultFilters($targetModule);

        if (!empty($options['userId'])) {
            self::$current_user = \BeanFactory::retrieveBean('Users', $options['userId']);
        }

        $this->logger = \LoggerManager::getLogger();
    }

    /**
     * Set up query to execute for a calendar configuration
     *
     * @return void
     */
    public function setupQuery(): void
    {
        $this->query = new \SugarQuery();

        $this->setQuerySelect();
        $this->setQueryFrom();
        $this->setQueryFilters();

        $this->logger->debug('QueryManagger: Calendar query ' . $this->extractSql());
    }

    /**
     * Raw events as they are in db
     *
     * @return array
     */
    public function executeQuery(): array
    {
        return $this->query->execute();
    }

    /**
     * Extract sql for debugging purposes
     *
     * @return string
     */
    public function extractSql(): string
    {
        $c = $this->query->compile();
        $sql = $c->getSQL();
        $parameters = $c->getParameters();
        foreach ($parameters as $idx => $parameterValue) {
            $sql = $this->strReplaceFirst('?', "'{$parameterValue}'", $sql);
        }
        return $sql;
    }

    /**
     * Replace first ocurrecence from string
     *
     * @param String $from
     * @param String $to
     * @param String $content
     * @return string
     */
    public function strReplaceFirst(string $from, string $to, string $content): string
    {
        $from = '/' . preg_quote($from, '/') . '/';

        return preg_replace($from, $to, $content, 1);
    }

    /**
     * Sets SugarQuery `SELECT`.
     *
     * @return void
     */
    protected function setQuerySelect(): void
    {
        $fieldsListUsedByCalendar = $this->extractFieldsUsedForSelect();

        foreach ($fieldsListUsedByCalendar as $fieldName) {
            $preparedField = $this->prepareDbFieldForQuery($fieldName);
            $this->query->select->fieldRaw($preparedField, $fieldName);
        }
    }

    /**
     * Formats db field so to be correctly used within the SugarQuery object.
     *
     * @param string $fieldName
     *
     * @return string
     */
    public function prepareDbFieldForQuery(string $fieldName): string
    {
        $fieldSource = $this->getFieldProperty($this->targetBean->module_dir, $fieldName, 'source');

        $alias = $this->targetBean->getTableName();

        if ($fieldSource === 'custom_fields') {
            $alias = $this->targetBean->get_custom_table_name();
        }

        return $alias . '.' . $fieldName;
    }

    /**
     * Returns the value for the given field property.
     *
     * @param string $module
     * @param string $fieldName
     * @param string $fieldProp
     *
     * @return string
     */
    public function getFieldProperty(string $module, string $fieldName, string $fieldProp): string
    {
        $bean = \BeanFactory::newBean($module);
        $fieldDef = $bean->getFieldDefinition($fieldName);

        if (empty($fieldDef) || empty($fieldProp)) {
            return '';
        }

        if (isset($fieldDef[$fieldProp])) {
            $prop = $fieldDef[$fieldProp];
            return $prop;
        }

        return '';
    }

    /**
     * Sets SugarQuery `FROM`.
     *
     * @return void
     */
    public function setQueryFrom(): void
    {
        $this->query->from($this->targetBean);

        /**
         * We manually join the target bean custom table with the base table.
         * This is a fix implemented for the case when currency_id field is found in the target bean custom table.
         * Somehow, when SugarQuery is compiled and therefore executed, the raw SQL is wrongly created,
         * because it first joins the currencies table, which, according to the above case (currency_id is custom field)
         * should be linked to the target bean custom table but that's generating the error,
         * because the join with the target bean custom table is made in the end.
         *
         * EG of wrong query:
         *
         * SELECT
         *     ...
         * FROM
         *     accounts
         * LEFT JOIN
         *     currencies
         * ON
         *     currencies.id = accounts_cstm.currency_id
         * LEFT JOIN
         *     accounts_cstm
         * ON
         *     accounts_cstm.id_c = accounts.id
         */
        $this->query->joinCustomTable($this->targetBean);
    }

    /**
     * Applies filter to the global SugarQuery.
     *
     * @return void
     *
     * @throws \Exception
     * @throws \Error
     */
    public function setQueryFilters(): void
    {
        global $db;

        $module = $this->calendarBean->calendar_module;

        if (trim($this->calendarBean->calendar_filter) !== '') {
            $calendarFilter = json_decode($this->calendarBean->calendar_filter, true);

            if (!empty($calendarFilter)) {
                $filterId = $calendarFilter['filterId'];
                $filterDef = $calendarFilter['filterDef'];

                if (isset($filterId) && !empty($filterId)) {
                    if (safeInArray($filterId, $this->defaultFilters) === true) {
                        //sometime filters come as a simple dictionary - like 'My Meetings' filter.
                        //Others are arrays of dictionaries
                        $filterDefKeys = array_keys($filterDef);
                        $allFilterKeysAreNumeric = $this->allKeysAreNumeric($filterDefKeys);
                        if ($allFilterKeysAreNumeric === false) {
                            $filterDef = [$filterDef];
                        }
                    } else {
                        $filterDef = $this->filterManager->getFilterDefinition($module, $filterId);
                    }
                }

                self::addFilters($filterDef, $this->query->where(), $this->query);
            }
        }

        $tmpTimeDate = \TimeDate::getInstance();

        //format to GMT values for sql queries
        $viewStart = $this->params['startDate'];
        $viewStartDT = new \DateTime($viewStart);
        $viewStart = $tmpTimeDate->asDb($viewStartDT);

        $viewEnd = $this->params['endDate'];
        $viewEndDT = new \DateTime($viewEnd);
        $viewEnd = $tmpTimeDate->asDb($viewEndDT);

        if ($this->calendarBean->intervalType === \Calendar::$INTERVAL_TIME) {
            $eventStart = $this->calendarBean->event_start;
            $eventEnd = $this->calendarBean->event_end;

            $intervalConditions = $this->query->where()->queryOr();

            // event is anywhere inside the start and end dates of the view
            $case = $intervalConditions->queryAnd();
            $case->condition($eventStart, '>=', $viewStart);
            $case->condition($eventStart, '<=', $viewEnd);
            $case->condition($eventEnd, '>=', $viewStart);
            $case->condition($eventEnd, '<=', $viewEnd);

            // event started before view start and ends somewhere inside start/end  of the view
            $case = $intervalConditions->queryAnd();
            $case->condition($eventStart, '<', $viewStart);
            $case->condition($eventEnd, '>=', $viewStart);
            $case->condition($eventEnd, '<=', $viewEnd);

            // event starts somewhere inside start/end of the view and it ends after the view end
            $case = $intervalConditions->queryAnd();
            $case->condition($eventStart, '>=', $viewStart);
            $case->condition($eventStart, '<=', $viewEnd);
            $case->condition($eventEnd, '>', $viewEnd);

            // event starts before start view and ends after end view
            $case = $intervalConditions->queryAnd();
            $case->condition($eventStart, '<', $viewStart);
            $case->condition($eventEnd, '>', $viewEnd);
        } elseif ($this->calendarBean->intervalType === \Calendar::$INTERVAL_DURATION) {
            $eventStart = $this->calendarBean->event_start;
            if (!isset($this->targetBean->field_defs[$eventStart])) {
                $this->logger->error(
                    sprintf('QueryManager Calendar could not parse the event_start as a valid field - %s', $eventStart)
                );
                return;
            }
            if ($module == 'Calls' || $module == 'Meetings') {
                $eventEnd = 'date_end';
            } else {
                $eventEndFormula = $this->generateDbDateEndFormula($this->calendarBean);
                if ($eventEndFormula == '') {
                    $this->logger->error(
                        sprintf('QueryManager Calendar could not generate db date end formula for module %s', $module)
                    );
                }
                $eventEnd = $eventEndFormula;
            }

            $intervalConditions = $this->query->where()->queryOr();

            $viewStart = $db->quoted($viewStart);
            $viewEnd = $db->quoted($viewEnd);

            // event is anywhere inside the start and end dates of the view
            $case = $intervalConditions->queryAnd();
            $queryRaw = <<<SQL
    {$eventStart} >= $viewStart
AND {$eventStart} <= $viewEnd
AND {$eventEnd} >= $viewStart
AND {$eventEnd} <= $viewEnd
SQL;
            $case->addRaw($queryRaw);

            // event started before view start and ends somewhere inside start/end  of the view
            $case = $intervalConditions->queryAnd();
            $queryRaw = <<<SQL
    {$eventStart} < $viewStart
AND {$eventEnd} >= $viewStart
AND {$eventEnd} <= $viewEnd
SQL;
            $case->addRaw($queryRaw);

            // event starts somewhere inside start/end of the view and it ends after the view end
            $case = $intervalConditions->queryAnd();
            $queryRaw = <<<SQL
    {$eventStart} >= $viewStart
AND {$eventStart} <= $viewEnd
AND {$eventEnd} > $viewEnd
SQL;
            $case->addRaw($queryRaw);
            // event starts before start view and ends after end view
            $case = $intervalConditions->queryAnd();
            $queryRaw = <<<SQL
    {$eventStart} < $viewStart
AND {$eventEnd} > $viewEnd
SQL;
            $case->addRaw($queryRaw);
        }
    }

    /**
     * Check keys are numeric
     *
     * @param Array $calendarFilterDefKeys
     * @return bool
     */
    public function allKeysAreNumeric(array $calendarFilterDefKeys): bool
    {
        $allKeysAreIdxs = true;
        foreach ($calendarFilterDefKeys as $keyIdx => $keyVal) {
            if (is_numeric($keyVal) === false) {
                $allKeysAreIdxs = false;
            }
        }
        return $allKeysAreIdxs;
    }

    /**
     * Generates a formula for end date based on start date and duration
     *
     * @param SugarBean $calendarBean
     * @return String
     */
    protected function generateDbDateEndFormula(\SugarBean $calendarBean): string
    {
        $eventStart = $calendarBean->event_start;

        if (!isset($this->targetBean->field_defs[$eventStart])) {
            return '';
        }
        $formula = $eventStart;

        if (!empty($calendarBean->duration_minutes) &&
            isset($this->targetBean->field_defs[$calendarBean->duration_minutes])) {
            $durationMinutesField = $this->prepareDbFieldForQuery($calendarBean->duration_minutes);
            $formula = "DATE_ADD({$formula}, INTERVAL {$durationMinutesField} MINUTE)";
        }

        if (!empty($calendarBean->duration_hours) &&
            isset($this->targetBean->field_defs[$calendarBean->duration_hours])) {
            $durationHoursField = $this->prepareDbFieldForQuery($calendarBean->duration_hours);
            $formula = "DATE_ADD({$formula}, INTERVAL {$durationHoursField} HOUR)";
        }

        if (!empty($calendarBean->duration_days) &&
            isset($this->targetBean->field_defs[$calendarBean->duration_days])) {
            $durationDaysField = $this->prepareDbFieldForQuery($calendarBean->duration_days);
            $formula = "DATE_ADD({$formula}, INTERVAL {$durationDaysField} DAY)";
        }
        return $formula;
    }

    /**
     * Returns list of fields that are needed on calendar
     * These includes start/end dates but also other fields like those used in templates and dependent fields.
     *
     * @return array An array of fields
     */
    public function extractFieldsUsedForSelect(): array
    {
        global $dictionary;

        $fieldsToSelect = ['id', 'assigned_user_id'];

        $objectName = \BeanFactory::getObjectName($this->calendarBean->calendar_module);

        $allFieldsWeAskFor = [
            'event_start',
            'event_end',
            'duration_minutes',
            'duration_hours',
            'duration_days',
            'subject',
        ];

        foreach ($allFieldsWeAskFor as $fieldToCheck) {
            if (!empty($this->calendarBean->{$fieldToCheck})) {
                $fieldsToSelect[] = $this->calendarBean->{$fieldToCheck};
            }
        }

        $templateFields = [
            'event_tooltip_template',
            'day_event_template',
            'week_event_template',
            'month_event_template',
            'agenda_event_template',
            'timeline_event_template',
            'schedulermonth_event_template',
            'ical_event_template',
        ];

        foreach ($templateFields as $templateField) {
            $tooltipEventTemplateFields = $this->parseTargetBody($this->calendarBean->{$templateField});
            $fieldsToSelect = array_merge($fieldsToSelect, $tooltipEventTemplateFields);
        }

        $tempFieldsToSelectList = [];
        //add dependend fields. used for fields like full_name
        foreach ($fieldsToSelect as $fieldToSelect) {
            $tempFieldsToSelectList[] = $fieldToSelect;
            if (isset($dictionary[$objectName]['fields'][$fieldToSelect]['fields']) &&
                is_array($dictionary[$objectName]['fields'][$fieldToSelect]['fields'])) {
                $fields = $dictionary[$objectName]['fields'][$fieldToSelect]['fields'];
                foreach ($fields as $depFieldIdx => $dependentField) {
                    $tempFieldsToSelectList[] = $dependentField;
                }
            }
        }

        //add id_name values to list for relates
        $fieldsToAdd = [];
        foreach ($tempFieldsToSelectList as $fieldToSelect) {
            $fieldToSelectDefinition = $dictionary[$objectName]['fields'][$fieldToSelect];
            if ($fieldToSelectDefinition['type'] === 'relate') {
                $fieldsToAdd[] = $fieldToSelectDefinition['id_name'];
            }
        }

        foreach ($fieldsToAdd as $fieldToAdd) {
            $tempFieldsToSelectList[] = $fieldToAdd;
        }

        //add dblclick record id
        $dblClickAction = explode(':', $this->calendarBean->dblclick_event);
        if (is_array($dblClickAction) && safeCount($dblClickAction) === 3) {
            $dblClickModule = $dblClickAction[1];
            $dblClickId = $dblClickAction[2];
            if ($dblClickModule !== 'self') {
                $tempFieldsToSelectList[] = $dblClickId;
            }
        }

        //test fields against vardefs in order to make sure sql will not fail because of a wrong field name given
        $fieldsToSelect = array_unique($tempFieldsToSelectList);
        $tempFieldsToSelectList = [];
        foreach ($fieldsToSelect as $fieldToSelect) {
            if ((
                array_key_exists($fieldToSelect, $dictionary[$objectName]['fields']) === true
                    && (array_key_exists('source', $dictionary[$objectName]['fields'][$fieldToSelect]) === false
                        || $dictionary[$objectName]['fields'][$fieldToSelect]['source'] === 'custom_fields')
            ) || $fieldToSelect === 'current_user_id') {
                $tempFieldsToSelectList[] = $fieldToSelect;
            }
        }
        $fieldsToSelect = $tempFieldsToSelectList;

        $this->logger->debug('QueryManager Calendar extractFieldsUsedForSelect ' . var_export($fieldsToSelect, true));

        return $fieldsToSelect;
    }

    /**
     * Parse target body
     *
     * @param string $target_body
     * @return array
     */
    public function parseTargetBody(string $target_body): array
    {
        $component_array = [];

        preg_match_all('/(({::)[^>]*?)(.*?)((::})[^>]*?)/', $target_body, $matches, PREG_SET_ORDER);

        foreach ($matches as $val) {
            if (is_array($val) && safeCount($val) > 3) {
                $matched_component_core = $val[3];
                $component_array[] = $matched_component_core;
            }
        }
        return $component_array;
    }
}
