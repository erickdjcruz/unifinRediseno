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

class MetricsApi extends FilterApi
{
    /**
     * @return array
     */
    public function registerApiRest()
    {
        return [
            'getVisibleMetrics' => [
                'reqType' => 'GET',
                'path' => ['Metrics', 'visible'],
                'pathVars' => ['module', ''],
                'method' => 'getVisibleMetrics',
                'shortHelp' => 'This method will return all metrics user has configured as visible.',
                'minVersion' => '11.18',
            ],
            'getHiddenMetrics' => [
                'reqType' => 'GET',
                'path' => ['Metrics', 'hidden'],
                'pathVars' => ['module', ''],
                'method' => 'getHiddenMetrics',
                'shortHelp' => 'This method will return all metrics user hide.',
                'minVersion' => '11.18',
            ],
            'organizeMetrics' => [
                'reqType' => 'POST',
                'path' => ['Metrics', 'config'],
                'pathVars' => ['module', ''],
                'method' => 'configSave',
                'shortHelp' => 'This method will return metrics.',
                'minVersion' => '11.18',
            ],
            'restoreAdminDefaults' => [
                'reqType' => 'GET',
                'path' => ['Metrics', 'restore-defaults'],
                'pathVars' => ['module', ''],
                'method' => 'restoreAdminDefaults',
                'shortHelp' => 'This method will return metrics.',
                'minVersion' => '11.18',
            ],
        ];
    }

    /**
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionInvalidParameter
     */
    public function getVisibleMetrics(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['metric_context', 'metric_module']);
        $userPreference = new UserPreference($GLOBALS['current_user']);
        $userMetrics = json_decode($userPreference->getPreference('user_metrics'), true) ?? [];
        $userMetrics = $userMetrics[$args['metric_context'] . $args['metric_module']] ?? [];
        $metrics = $this->getMetrics($api, $args);
        $visibleMetrics = [];
        if (empty($userMetrics)) {
            // all are visible by default
            foreach ($metrics['records'] as $key => $val) {
                $visibleMetrics[] = $val;
            }
        } else {
            $allMetrics = [];
            foreach ($metrics['records'] as $key => $val) {
                $allMetrics[$val['id']] = $val;
            }
            $visibleList = array_intersect($userMetrics['visible_list'], array_keys($allMetrics));
            // find metrics not in visible_list and hidden_list
            $newMetrics = array_diff(array_keys($allMetrics), $userMetrics['visible_list'], $userMetrics['hidden_list']);
            // add new metrics to the end of visible_list
            $visibleList = array_merge($visibleList, $newMetrics);
            foreach ($visibleList as $metricId) {
                $visibleMetrics[] = $allMetrics[$metricId];
            }
        }
        foreach ($visibleMetrics as &$visibleMetric) {
            $visibleMetric['filter_def'] = $visibleMetric['filter_def'] ?? [];
        }
        return $visibleMetrics;
    }

    /**
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionInvalidParameter
     */
    public function getHiddenMetrics(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['metric_context', 'metric_module']);
        $userPreference = new UserPreference($GLOBALS['current_user']);
        $userMetrics = json_decode($userPreference->getPreference('user_metrics'), true) ?? [];
        $userMetrics = $userMetrics[$args['metric_context'] . $args['metric_module']] ?? [];
        $metrics = $this->getMetrics($api, $args);
        $hiddenMetrics = [];
        if (!empty($userMetrics)) {
            foreach ($metrics['records'] as $key => $val) {
                if (safeInArray($val['id'], $userMetrics['hidden_list'])) {
                    $hiddenMetrics[] = $val;
                }
            }
        }
        return $hiddenMetrics;
    }

    /**
     * @param ServiceBase $api
     * @param array $args
     * @throws SugarApiExceptionNotAuthorized
     */
    public function configSave(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, [
            'metric_context',
            'metric_module',
            'visible_list',
            'hidden_list',
        ]);
        $userPreference = new UserPreference($GLOBALS['current_user']);
        $userMetrics = json_decode($userPreference->getPreference('user_metrics'), true) ?? [];
        $userMetrics[$args['metric_context'] . $args['metric_module']] = [
            'visible_list' => $args['visible_list'],
            'hidden_list' => $args['hidden_list'],
        ];
        $userPreference->setPreference('user_metrics', json_encode($userMetrics));
        $userPreference->savePreferencesToDB(true);
    }

    /**
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionInvalidParameter
     */
    public function restoreAdminDefaults(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['metric_context', 'metric_module']);
        $metrics = $this->getMetrics($api, $args);
        $visibleMetrics = [];
        // all are visible by default
        foreach ($metrics['records'] as $key => $val) {
            $visibleMetrics[] = $val;
        }

        return $visibleMetrics;
    }

    /**
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionInvalidParameter
     */
    private function getMetrics(ServiceBase $api, array $args)
    {
        $args['filter'] = [
            ['metric_context' => ['$equals' => $args['metric_context']]],
            ['metric_module' => ['$equals' => $args['metric_module']]],
            ['status' => ['$equals' => 'Active']],
        ];
        $args['module'] = 'Metrics';
        if (empty($args['fields'])) {
            $args['fields'] = 'id,name';
        }
        return $this->filterList($api, $args);
    }
}
