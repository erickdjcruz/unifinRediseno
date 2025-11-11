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

use Elastica\Request;
use Sugarcrm\Sugarcrm\SearchEngine\SearchEngine;
use Sugarcrm\Sugarcrm\SearchEngine\Engine\Elastic;

/**
 * Upgrade script to schedule a full FTS index.
 */
class SugarUpgradeRunFTSIndex extends UpgradeScript
{
    public $order = 9610;
    public $type = self::UPGRADE_CUSTOM;

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        if (version_compare($this->from_version, '11.0.0', '>=')) {
            return;
        }

        $needFullIndex = false;
        if (version_compare($this->from_version, '10.3.0', '<')
        ) {
            $needFullIndex = true;
        }

        if (!$needFullIndex) {
            if ($this->isOneIndexEnabled()) {
                // check ES server version
                $esVersion = $this->getEsVersion();
                if (version_compare($esVersion, '6.0', '>=')) {
                    $needFullIndex = true;
                }
            }
        }
        if ($needFullIndex) {
            try {
                SearchEngine::getInstance()->scheduleIndexing([], true);
                $this->log('scheduling full FTS!');
            } catch (Exception $e) {
                $this->log('SugarUpgradeRunFTSIndex: scheduling FTS reindex got exceptions!');
            }
        }
    }

    /**
     * @return string elasticsearch version
     * have to use raw Elastic Client's request method, not method in mango version.
     */
    protected function getEsVersion(): string
    {
        $esVersion = null;
        $engine = SearchEngine::getInstance()->getEngine();
        if ($engine instanceof Elastic) {
            try {
                $result = $engine->getContainer()->client->request('', Request::GET);
                if ($result->isOk()) {
                    $data = $result->getData();
                    $esVersion = $data['version']['number'] ?? '0';
                }
            } catch (Exception $e) {
                $this->log('getEsVersion: get ES version got exceptions: ' . $e->getMessage());
            }
        }
        return $esVersion;
    }

    /**
     * get one index setting 'enable_one_index' from config, default is false, i.e., one index per module,
     * this keeps ES 6.x Elastic indexing strategy consistent
     *
     * @return bool
     */
    protected function isOneIndexEnabled(): bool
    {
        return $GLOBALS['sugar_config']['enable_one_index'] ?? false;
    }
}
