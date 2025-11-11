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

class DashboardDefaultMetadataApi extends SugarApi
{
    /**
     * Set up the endpoints
     *
     * @return array
     */
    public function registerApiRest()
    {
        return [
            'restoreDefaultMetadataForTabbedDashboard' => [
                'reqType' => 'PUT',
                'path' => ['Dashboards', '?', 'restore-tab-metadata'],
                'pathVars' => ['module', 'id', ''],
                'method' => 'restoreDefaultMetadataForTabbedDashboard',
                'shortHelp' => 'This method updates a tab on a dashboard to the default metadata',
                'longHelp' => 'modules/Dashboards/clients/base/api/help/dashboard_default_metadata.html',
                'minVersion' => '11.12',
            ],
            'restoreMetadata' => [
                'reqType' => 'PUT',
                'path' => ['Dashboards', '?', 'restore-metadata'],
                'pathVars' => ['module', 'id', ''],
                'method' => 'restoreMetadata',
                'shortHelp' => 'This method restores a dashboard to the default metadata',
                'longHelp' => 'modules/Dashboards/clients/base/api/help/restore_metadata.html',
                'minVersion' => '11.13',
            ],
        ];
    }

    /**
     * Restore a tab on a dashboard to the default metadata
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionRequestMethodFailure
     */
    public function restoreDefaultMetadataForTabbedDashboard(ServiceBase $api, array $args): array
    {
        $this->requireArgs($args, ['dashboard', 'tab_index']);

        $tabIndex = $args['tab_index'];
        $dashboard = BeanFactory::retrieveBean($args['module'], $args['id']);

        if (empty($dashboard)) {
            throw new SugarApiExceptionRequestMethodFailure('Failed to retrieve dashboard bean');
        }

        $defaultMetadata = $this->getDefaultMetadata($args);
        $result = $this->setMetadataForTab($dashboard, intval($tabIndex), $defaultMetadata);

        if (!$result) {
            throw new SugarApiExceptionRequestMethodFailure('Failed to save dashboard metadata');
        }

        return $this->formatBean($api, $args, $dashboard);
    }

    /**
     * Restore dashboard to the default metadata
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionRequestMethodFailure
     */
    public function restoreMetadata(ServiceBase $api, array $args): array
    {
        $this->requireArgs($args, ['dashboard_module', 'dashboard']);

        $dashboard = BeanFactory::retrieveBean('Dashboards', $args['id']);
        if (empty($dashboard)) {
            throw new SugarApiExceptionRequestMethodFailure('Failed to retrieve dashboard bean');
        }

        $defaultMetadata = $this->getDefaultMetadata($args);
        $dashboard->metadata = json_encode($defaultMetadata);
        $dashboard->save();

        return $this->formatBean($api, $args, $dashboard);
    }

    /**
     * Set the dashboard bean's metadata to the specified metadata on
     * the specified tab and save
     *
     * @param SugarBean $dashboard the dashboard bean
     * @param int $tabIndex the tab index to set
     * @param array $metadata the metadata to set
     * @return bool true if new metadata was saved, false otherwise
     */
    public function setMetadataForTab(SugarBean $dashboard, int $tabIndex, array $metadata): bool
    {
        if (empty($dashboard) || empty($metadata)) {
            return false;
        }

        // decode dashboard metadata to an associative array
        $dashboardMetadata = json_decode($dashboard->metadata, true);

        $oldTabs = &$dashboardMetadata['tabs'];
        $newTabs = &$metadata['tabs'];

        if (!isset($oldTabs, $newTabs)) {
            return false;
        }

        // set to default metadata
        // if metadata has dashlets defined
        if (isset($newTabs[$tabIndex]['dashlets'])) {
            $oldTabs[$tabIndex]['dashlets'] = $newTabs[$tabIndex]['dashlets'];
        } else {
            // Older console metadata has components instead dashlets
            // so we copy complete tab metadata
            $oldTabs[$tabIndex] = $newTabs[$tabIndex];
        }

        // re-encode and save
        $dashboard->metadata = json_encode($dashboardMetadata);
        $dashboard->save();

        return true;
    }

    /**
     * Get the default dashboard metadata from file
     *
     * @param array $args
     * @return array the metadata
     */
    public function getDefaultMetadata(array $args): array
    {
        $metadata = [];
        $filename = $this->getFilename($args);

        if (!file_exists($filename)) {
            return $metadata;
        }

        // get the content and load into system
        $contents = require $filename;

        $metadata = $contents['metadata'];
        return $metadata;
    }

    /**
     * Helper function to get the module name for the default dashboard file
     *
     * @param array $args
     * @return string the module name
     */
    public function getModuleForFilename(array $args): string
    {
        // dashboard_module takes precedence as dashboards may not be stored in module/Dashboards
        return $args['dashboard_module'] ?? $args['module'];
    }

    /**
     * Get the filename (with full path) to the default dashboard file
     *
     * @param array $args
     * @return string the filename
     */
    public function getFilename(array $args): string
    {
        $module = $this->getModuleForFilename($args);
        $dashboard = $args['dashboard'];
        return "modules/{$module}/dashboards/{$dashboard}/{$dashboard}.php";
    }
}
