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

namespace Sugarcrm\Sugarcrm\AccessControl;

// This section of code is a portion of the code referred
// to as Critical Control Software under the End User
// License Agreement.  Neither the Company nor the Users
// may modify any portion of the Critical Control Software.

use Sugarcrm\Sugarcrm\ProductDefinition\Config\Config as ProductDefinitionConfig;

/**
 * Class AccessConfigurator, this class offers APIs to retrieve data from access_config.json
 *
 * @package Sugarcrm\Sugarcrm\AccessControl
 */
class AccessConfigurator
{
    /**
     * access control configuration file.
     */
    public const ACCESS_CONFIG_FILE = 'access_config.json';

    /**
     * instance
     * @var AccessConfigurator
     */
    protected static $instance;

    /**
     * inaccessible modules
     * @var array
     */
    protected $inaccessibleModules = [];

    /**
     * inaccessible records
     * @var array
     */
    protected $inaccessibleRecords = [];

    /**
     * access configuration data, cached in memory for session
     * @var array
     */
    protected $data = [];

    /**
     * private ctor
     * AccessConfigurator constructor
     */
    private function __construct()
    {
    }

    /**
     * Singleton implementation
     * @return AccessConfigurator
     */
    public static function instance()
    {
        if (empty(self::$instance)) {
            self::$instance = new AccessConfigurator();
        }

        return self::$instance;
    }

    /**
     *
     * get access controlled list
     * @param string $key
     * @param bool $useCache
     * @return array|mixed
     */
    public function getAccessControlledList(string $key, bool $useCache = true)
    {
        $cacheKey = 'access_config_data';

        if (empty($this->data)) {
            if ($useCache) {
                // try cache first
                $this->data = sugar_cache_retrieve($cacheKey);
                if (!empty($this->data)) {
                    if (isset($this->data[$key])) {
                        return $this->data[$key];
                    }
                    return [];
                }
            }

            $this->data = $this->loadAccessConfig();
            if ($useCache) {
                sugar_cache_put($cacheKey, $this->data);
            }
        }

        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return [];
    }

    /**
     * get access controlled list by license types
     *
     * @param array $types
     * @param bool $useCache
     * @return array|mixed
     */
    public function getNotAccessibleModuleListByLicenseTypes(array $types, bool $useCache = true)
    {
        if (empty($types)) {
            return [];
        }

        if (!empty($this->inaccessibleModules)) {
            return $this->inaccessibleModules;
        }

        $controlledList = $this->getAccessControlledList(AccessControlManager::MODULES_KEY, $useCache);

        $notAccessibleList = [];

        // find out inaccessible modules
        if (!empty($controlledList)) {
            foreach ($controlledList as $module => $allowedTypes) {
                if (empty(array_intersect($types, $allowedTypes))) {
                    $notAccessibleList[$module] = true;
                }
            }
        }

        $this->inaccessibleModules = $notAccessibleList;
        return $notAccessibleList;
    }

    /**
     * get inaccessable records list by license types
     *
     * @param array $types
     * @param bool $useCache
     * @return array|mixed
     */
    public function getNotAccessibleRecordListByLicenseTypes(array $types, bool $useCache = true)
    {
        if (empty($types)) {
            return [];
        }

        if (!empty($this->inaccessibleRecords)) {
            return $this->inaccessibleRecords;
        }

        $controlledList = $this->getAccessControlledList(AccessControlManager::RECORDS_KEY, $useCache);

        $notAccessibleList = [];

        // find out inaccessible records
        if (!empty($controlledList)) {
            foreach ($controlledList as $module => $records) {
                $notAccessibleList[$module] = [];
                foreach ($records as $id => $allowedTypes) {
                    if (empty(array_intersect($types, $allowedTypes))) {
                        $notAccessibleList[$module][] = $id;
                    }
                }
            }
        }

        $this->inaccessibleRecords = $notAccessibleList;
        return $notAccessibleList;
    }

    /**
     * load access config from disk
     *
     * @return array|mixed
     * @throws \Exception
     */
    protected function loadAccessConfig()
    {
        return (new ProductDefinitionConfig(\SugarConfig::getInstance()))->getProductDefinition();
    }
}
//END REQUIRED CODE DO NOT MODIFY
