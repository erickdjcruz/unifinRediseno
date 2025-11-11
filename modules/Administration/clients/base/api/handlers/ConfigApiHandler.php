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
 * Base Handler for Config API. Create a custom handler by extending this one if needed.
 */
class ConfigApiHandler
{
    /**
     * Gets configuration details for a category
     *
     * @param ServiceBase $api The RestService object
     * @param array $args Arguments passed to the service
     * @return array
     */
    public function getConfig(ServiceBase $api, array $args)
    {
        $admin = BeanFactory::getBean('Administration');
        return $admin->retrieveSettings($args['category'], true)->settings;
    }

    /**
     * Saves new configuration details for a category and returns updated config
     *
     * @param ServiceBase $api The RestService object
     * @param array $args Arguments passed to the service
     * @return array
     */
    public function setConfig(ServiceBase $api, array $args)
    {
        $admin = BeanFactory::getBean('Administration');
        $category = $args['category'];
        $prefix = $category . '_';

        foreach ($args as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                $admin->saveSetting($category, str_replace($prefix, '', $key), $value, $api->platform);
            }
        }

        return $admin->retrieveSettings($category, true)->settings;
    }

    /**
     * Clears required metadata cache
     */
    protected function clearCache(): void
    {
        \MetaDataManager::refreshSectionCache(\MetaDataManager::MM_CONFIG);
    }
}
