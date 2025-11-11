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
 * Fix issues with the focus drawer dashboards
 */
class SugarUpgradeFixFocusDrawerDashboards extends UpgradeScript
{
    public $order = 7560;
    public $type = self::UPGRADE_DB;

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $this->log('Fixing focus drawer dashboards...');
        if (version_compare($this->from_version, '11.0.0', '<')) {
            $this->log('Fixing contracts related records dashlet');
            $this->findAndFixContractsDashlet();
            $this->log('Fixing tags favorite list view dashlet');
            $this->findAndFixTagsDashlet();
        }
        $this->log('Done fixing focus drawer dashboards');
    }

    /**
     * Finds which dashlet on the contracts dashboard needs to be fixed
     * @throws SugarQueryException
     */
    private function findAndFixContractsDashlet()
    {
        $dashboard = $this->getFocusDashboardForModule('Contracts');
        if (!$dashboard) {
            return;
        }

        $metadata = json_decode($dashboard['metadata'], true);

        // If a user has made changes:
        if (!empty($metadata['dashlets'])) {
            foreach ($metadata['dashlets'] as $dashletIdx => $dashlet) {
                if (empty($dashlet['view']) || $dashlet['view']['type'] !== 'dashablerecord') {
                    continue;
                }

                $metadata['dashlets'][$dashletIdx] = $this->fixContractsDashlet($dashlet);
            }
        }

        // If a user has not made changes:
        if (!empty($metadata['components'])) {
            $metadata['components'][0]['rows'][1][1] = $this->fixContractsDashlet(
                $metadata['components'][0]['rows'][1][1],
            );
        }

        $this->saveDashboard($dashboard['id'], $metadata);
    }

    /**
     * Fixes the contracts related records dashlet
     * @param $dashlet
     * @return array
     */
    private function fixContractsDashlet($dashlet)
    {
        $tabs = $dashlet['view']['tabs'];
        foreach ($tabs as $tabIdx => $tab) {
            if (in_array($tab['link'], ['contracts_documents', 'notes', 'contacts', 'quotes']) &&
                (empty($tab['fields']) || $tab['label'] === 'LBL_MODULE_NAME_SINGULAR')
            ) {
                $this->log('Fixing contracts dashlet tab: ' . $tab['link']);
                $dashlet['view']['tabs'][$tabIdx] = $this->fixContractsDashletTab($tab);
            }
        }

        return $dashlet;
    }

    /**
     * Fix a contracts dashlet tab by adding the correct fields for the link
     * @param $tab
     * @return array
     */
    private function fixContractsDashletTab($tab)
    {
        $fields = [
            'notes' => [
                'name',
                'contact_name',
                'parent_name',
                'created_by_name',
                'date_modified',
                'date_entered',
            ],
            'contracts_documents' => [
                'document_name',
                'filename',
                'category_id',
                'doc_type',
                'status_id',
                'active_date',
            ],
            'quotes' => [
                'quote_num',
                'name',
                'billing_account_name',
                'quote_stage',
                'total',
                'total_usdollar',
                'date_quote_expected_closed',
                'assigned_user_name',
                'date_modified',
                'date_entered',
            ],
            'contacts' => [
                'name',
                'title',
                'account_name',
                'email',
                'phone_mobile',
                'phone_work',
                'phone_other',
                'assistant_phone',
                'assigned_user_name',
                'date_modified',
                'date_entered',
            ],
        ];
        $tab['fields'] = $fields[$tab['link']];
        $tab['type'] = 'list';
        if (empty($tab['limit'])) {
            $tab['limit'] = 5;
        }
        $tab['label'] = 'LBL_MODULE_NAME';
        unset($tab['active']);
        return $tab;
    }

    /**
     * Finds which dashlet on the tags focus drawer dashboard needs to be fixed
     * @throws SugarQueryException
     */
    private function findAndFixTagsDashlet()
    {
        $dashboard = $this->getFocusDashboardForModule('Tags');
        if (!$dashboard) {
            return;
        }

        $metadata = json_decode($dashboard['metadata'], true);

        // If a user has made changes:
        if (!empty($metadata['dashlets'])) {
            foreach ($metadata['dashlets'] as $dashletIdx => $dashlet) {
                if (empty($dashlet['view']) ||
                    $dashlet['view']['type'] !== 'dashablelist' ||
                    $dashlet['view']['module'] !== 'Tasks' ||
                    $dashlet['view']['label'] !== 'LBL_MY_FAVORITE_TASKS'
                ) {
                    continue;
                }

                $metadata['dashlets'][$dashletIdx] = $this->fixTagsDashlet($dashlet);
            }
        }

        // If a user has not made changes:
        if (!empty($metadata['components'])) {
            $metadata['components'][0]['rows'][0][1] = $this->fixTagsDashlet(
                $metadata['components'][0]['rows'][0][1],
            );
        }

        $this->saveDashboard($dashboard['id'], $metadata);
    }

    /**
     * Fixes the favorite tags dashlet
     * @param $dashlet
     * @return array
     */
    private function fixTagsDashlet($dashlet)
    {
        $dashlet['view'] = [
            'label' => 'LBL_MY_FAVORITE_TAGS',
            'type' => 'dashablelist',
            'display_columns' => [
                'name',
                'created_by_name',
                'assigned_user_name',
                'date_modified',
                'date_entered',
            ],
            'module' => 'Tags',
            'filter_id' => 'favorites',
        ];
        $dashlet['context'] = [
            'module' => 'Tags',
        ];
        return $dashlet;
    }

    /**
     * Saves the dashboard metadata for the given id
     * @param $id
     * @param $metadata
     */
    private function saveDashboard($id, $metadata)
    {
        $bean = BeanFactory::retrieveBean('Dashboards', $id);
        $bean->metadata = json_encode($metadata);
        $bean->save();
    }

    /**
     * Gets the focus dashboard metadata for the given module
     * @param $module
     * @return array|null
     * @throws SugarQueryException
     */
    private function getFocusDashboardForModule($module)
    {
        $dashboardBean = BeanFactory::newBean('Dashboards');

        $query = new SugarQuery();
        $query->select(['id', 'metadata']);
        $query->from($dashboardBean);
        $query->where()
            ->queryAnd()
            ->equals('dashboard_module', $module)
            ->equals('view_name', 'focus');

        $results = $query->execute();

        if (!empty($results)) {
            return $results[0];
        } else {
            return null;
        }
    }
}
