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

class SugarUpgradeUpdateDashboardMetadataPagination extends UpgradeScript
{
    public $order = 9501;
    public $type = self::UPGRADE_DB;

    /**
     * @throws SugarQueryException
     */
    public function run()
    {
        if (version_compare($this->from_version, '13.3.0', '>=')) {
            return;
        }

        $this->log('Updating Pagination for Service console dashboard ...');
        $consoleIDs = [
            'c108bb4a-775a-11e9-b570-f218983a1c3e', // Service Console
            'da438c86-df5e-11e9-9801-3c15c2c53980', // Renewals Console
        ];

        $bean = BeanFactory::newBean('Dashboards');
        $query = new SugarQuery();
        $query->select(['id', 'name', 'metadata']);
        $query->from($bean);
        $query->where()->in('id', $consoleIDs);
        $rows = $query->execute();

        foreach ($rows as $row) {
            $metadata = json_decode($row['metadata'], true);

            switch ($row['id']) {
                // Service console
                case 'c108bb4a-775a-11e9-b570-f218983a1c3e':
                    if (version_compare($this->from_version, '13.1.0', '>=')) {
                        if ($this->updatePaginationCssClass($metadata, [1])) {
                            $this->doUpdate($metadata, $row);
                        }
                    } elseif ($this->updateServiceConsoleMeta($metadata)) {
                        $this->doUpdate($metadata, $row);
                    }
                    break;

                    // Renewals console
                case 'da438c86-df5e-11e9-9801-3c15c2c53980':
                    if (version_compare($this->from_version, '13.1.0', '>=')) {
                        if ($this->updatePaginationCssClass($metadata, [1, 2])) {
                            $this->doUpdate($metadata, $row);
                        }
                    } elseif ($this->updateRenewalsConsoleMeta($metadata)) {
                        $this->doUpdate($metadata, $row);
                    }
                    break;
            }
        }

        $this->log('Service console Pagination metadata was updated!');
    }

    /**
     * Update css class
     * @param $metadata
     * @param array $tabNumbers
     * @return bool
     */
    private function updatePaginationCssClass(&$metadata, $tabNumbers): bool
    {
        $updated = false;

        foreach ($tabNumbers as $tabNumber) {
            if (isset($metadata['tabs'][$tabNumber]['components'])) {
                foreach ($metadata['tabs'][$tabNumber]['components'] as $i => $component) {
                    if (isset($component['view']['name']) && $component['view']['name'] === 'multi-line-list-pagination') {
                        $metadata['tabs'][$tabNumber]['components'][$i]['view']['css_class'] =
                            'flex-table-pagination absolute bg-[--primary-content-background] w-full z-30';
                        $updated = true;
                        break;
                    }
                }
            }
        }

        return $updated;
    }

    /**
     * Update the Service console dashboard metadata
     * @param $metadata
     * @return bool
     */
    private function updateServiceConsoleMeta(&$metadata): bool
    {
        if (isset($metadata['tabs'][1]['components'])) {
            $metadata['tabs'][1]['components'][] = [
                'context' => [
                    'module' => 'Cases',
                ],
                'view' => [
                    'name' => 'multi-line-list-pagination',
                    'css_class' => 'flex-table-pagination absolute bg-[--primary-content-background] w-full z-30',
                ],
            ];

            return true;
        }

        return false;
    }

    /**
     * Update the Renewals console dashboard metadata
     * @param $metadata
     * @return bool
     */
    private function updateRenewalsConsoleMeta(&$metadata): bool
    {
        $updated = false;

        if (isset($metadata['tabs'][1]['components'])) {
            $metadata['tabs'][1]['components'][] = [
                'context' => [
                    'module' => 'Accounts',
                ],
                'view' => [
                    'name' => 'multi-line-list-pagination',
                    'css_class' => 'flex-table-pagination absolute bg-[--primary-content-background] w-full z-30',
                ],
            ];

            $updated = true;
        }

        if (isset($metadata['tabs'][2]['components'])) {
            $metadata['tabs'][2]['components'][] = [
                'context' => [
                    'module' => 'Opportunities',
                ],
                'view' => [
                    'name' => 'multi-line-list-pagination',
                    'css_class' => 'flex-table-pagination absolute bg-[--primary-content-background] w-full z-30',
                ],
            ];

            $updated = true;
        }

        return $updated;
    }

    /**
     * @param $metadata
     * @param $row
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    private function doUpdate($metadata, $row): void
    {
        $query = 'UPDATE dashboards SET metadata = ? WHERE id = ?';
        $this->db->getConnection()->executeUpdate(
            $query,
            [json_encode($metadata), $row['id']],
            [\Doctrine\DBAL\ParameterType::STRING, \Doctrine\DBAL\ParameterType::STRING]
        );
        $this->log("Pagination metadata was updated for dashboard name = {$row['name']}");
    }
}
