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


class PersonFilterApi extends FilterApi
{
    /**
     * @var bool|mixed
     */
    public $useOnlyActiveUsers;

    protected $filterPortalUsers;

    public function registerApiRest()
    {
        return [
            'UserSearch' => [
                'reqType' => 'GET',
                'path' => ['Users'],
                'jsonParams' => ['filter'],
                'pathVars' => ['module_list'],
                'method' => 'filterList',
                'shortHelp' => 'Search User records',
                'longHelp' => 'include/api/help/module_filter_get_help.html',
                'exceptions' => [
                    // Thrown in filterList and filterListSetup
                    'SugarApiExceptionInvalidParameter',
                    // Thrown in filterListSetup and parseArguments
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiExceptionError',
                ],
            ],
            'EmployeeSearch' => [
                'reqType' => 'GET',
                'path' => ['Employees'],
                'jsonParams' => ['filter'],
                'pathVars' => ['module_list'],
                'method' => 'filterList',
                'shortHelp' => 'Search Employee records',
                'longHelp' => 'include/api/help/module_filter_get_help.html',
                'exceptions' => [
                    // Thrown in filterList and filterListSetup
                    'SugarApiExceptionInvalidParameter',
                    // Thrown in filterListSetup and parseArguments
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiExceptionError',
                ],
            ],
        ];
    }

    /**
     * {@inheritDoc}
     *
     * If $args['q'] is provided, run a global search instead of filtering.
     * Also applies default filters depending on what module is passed.
     *
     * @param ServiceBase $api The REST API object.
     * @param array $args REST API arguments.
     * @param string $acl Which type of ACL to check.
     * @return array The REST response as a PHP array.
     * @throws SugarApiExceptionError If retrieving a predefined filter failed.
     * @throws SugarApiExceptionInvalidParameter If any arguments are invalid.
     * @throws SugarApiExceptionNotAuthorized If we lack ACL access.
     */
    public function filterList(ServiceBase $api, array $args, $acl = 'list')
    {
        $this->filterPortalUsers = safeInArray($args['module_list'], ['Users', 'Employees']) &&
            !empty($args['filterPortal']);
        $this->useOnlyActiveUsers = safeInArray($args['module_list'], ['Users', 'Employees']) &&
            !empty($args['filterInactive']);

        if (!empty($args['q'])) {
            $this->useOnlyActiveUsers = $this->useOnlyActiveUsers && empty($options['fieldFilters']['status']);
            return $this->globalSearch($api, $args);
        }

        $this->useOnlyActiveUsers = $this->useOnlyActiveUsers && empty(array_column($args['filter'] ?? [], 'status'));

        $args['module'] = $args['module_list'];

        $api->action = 'list';
        [$args, $q, $options, $seed] = $this->filterListSetup($api, $args);

        $this->getCustomWhereForModule($args['module_list'], $q);

        if (($options['id_query'] ?? null) instanceof SugarQuery) {
            $this->addCustomWhereToIdQuery((string)$args['module_list'], $options['id_query']);
        }

        return $this->runQuery($api, $args, $q, $options, $seed);
    }

    /**
     * This function is the global search
     * @param ServiceBase $api The API class of the request
     * @param array $args The arguments array passed in from the API
     * @return array result set
     */
    public function globalSearch(ServiceBase $api, array $args)
    {
        $api->action = 'list';
        // This is required to keep the loadFromRow() function in the bean from making our day harder than it already is.
        $GLOBALS['disable_date_format'] = true;
        $search = new UnifiedSearchApi();
        $options = $search->parseSearchOptions($api, $args);

        // In case we want to filter on user status
        $options['custom_where'] = $this->getCustomWhereForModule($args['module_list']);

        $searchEngine = new SugarSpot();
        $options['resortResults'] = true;
        $recordSet = $search->globalSearchSpot($api, $args, $searchEngine, $options);

        return $recordSet;
    }

    /**
     * Gets the proper query where clause to use to prevent special user types from
     * being returned in the result
     *
     * @param string $module The name of the module we are looking for
     * @return string
     */
    protected function getCustomWhereForModule($module, $query = null)
    {
        if ($query instanceof SugarQuery) {
            if ($module == 'Employees') {
                $query->where()->equals('employee_status', 'Active')->equals('show_on_employees', '1');
                return;
            }

            // Filter out portal type Users if necessary
            if ($this->filterPortalUsers) {
                $query->where()->equals('portal_only', '0');
            }

            // Filter out inactive Users if necessary
            if ($this->useOnlyActiveUsers) {
                $query->where()->equals('status', 'Active');
            }

            return;
        }

        if ($module == 'Employees') {
            return "users.employee_status = 'Active' AND users.show_on_employees = 1";
        }

        $filter = '';

        // Filter out portal type Users if necessary
        if ($this->filterPortalUsers) {
            $filter .= "users.portal_only = 0";
        }

        // Filter out inactive Users if necessary
        if ($this->useOnlyActiveUsers) {
            if (!empty($filter)) {
                $filter .= ' AND ';
            }
            $filter .= "users.status = 'Active'";
        }

        return $filter;
    }

    /**
     * Checks the filter array to see if status is a field being filtered on. If
     * it isn't then returns true, as the default is to filter users based on
     * status = 'Active'
     * @param array $filter Filter definition
     * @return boolean
     * @deprecated As of 13.3 this is no longer used
     */
    protected function useOnlyActiveUsers(array $filter = []): bool
    {
        return empty(array_column($filter, 'status'));
    }

    /**
     * Adds Custom Where part like what will be added inside ::getCustomWhereForModule method
     */
    protected function addCustomWhereToIdQuery(string $module, SugarQuery $query): void
    {
        // everything is similar to ::getCustomWhereForModule

        if ($module === 'Employees') {
            return;
        }

        // Filter out portal type Users if necessary
        if ($this->filterPortalUsers) {
            $query->where()->equals('portal_only', '0');
        }

        // Filter out inactive Users if necessary
        if ($this->useOnlyActiveUsers) {
            $query->where()->equals('status', 'Active');
        }
    }
}
