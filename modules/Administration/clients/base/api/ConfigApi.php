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
 * API for Config Framework.
 */
class ConfigApi extends AdministrationApi
{
    /**
     * @return array
     */
    public function registerApiRest()
    {
        return [
            'getConfig' => [
                'reqType' => ['GET'],
                'path' => ['Administration', 'config', '?'],
                'pathVars' => ['', '', 'category'],
                'method' => 'getConfig',
                'shortHelp' => 'Gets configuration for a category',
                'longHelp' => 'include/api/help/administration_config_get_help.html',
                'exceptions' => ['SugarApiExceptionNotAuthorized'],
                'ignoreSystemStatusError' => true,
                'minVersion' => '11.13',
            ],
            'setConfig' => [
                'reqType' => ['POST'],
                'path' => ['Administration', 'config', '?'],
                'pathVars' => ['', '', 'category'],
                'method' => 'setConfig',
                'shortHelp' => 'Sets configuration for a category',
                'longHelp' => 'include/api/help/administration_config_post_help.html',
                'exceptions' => ['SugarApiExceptionNotAuthorized'],
                'ignoreSystemStatusError' => true,
                'minVersion' => '11.13',
            ],
        ];
    }

    /**
     * Gets configuration details for a category
     *
     * @param ServiceBase $api The RestService object
     * @param array $args Arguments passed to the service
     * @return array
     */
    public function getConfig(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['category']);
        $this->ensureAdminUser();
        $handler = $this->getHandler($args['category']);
        return $handler->getConfig($api, $args);
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
        $this->requireArgs($args, ['category']);
        $this->ensureAdminUser();
        $handler = $this->getHandler($args['category']);
        return $handler->setConfig($api, $args);
    }

    /**
     * Gets api handler.
     * @param string $category
     * @return object
     */
    protected function getHandler(string $category)
    {
        $handlerClass = ucfirst($category) . 'ConfigApiHandler';
        if (!class_exists($handlerClass)) {
            $handlerClass = 'ConfigApiHandler';
        }
        return new $handlerClass();
    }
}
