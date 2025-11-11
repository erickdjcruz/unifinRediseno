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

use Sugarcrm\Sugarcrm\Util\Files\FileLoader;
use Sugarcrm\Sugarcrm\Security\Validator\ConstraintBuilder;
use Sugarcrm\Sugarcrm\Security\Validator\Validator;

class SugarLiveConfigModuleApi extends ConsoleConfigModuleApi
{
    /**
     * Setup the endpoint that belong to this API EndPoint
     *
     * @return array
     */
    public function registerApiRest()
    {
        return [
            'configCreate' => [
                'reqType' => 'POST',
                'path' => ['SugarLive', 'config'],
                'pathVars' => ['module', ''],
                'method' => 'configSave',
                'shortHelp' => 'Creates the config entries for the SugarLive module',
                'longHelp' => 'modules/SugarLive/clients/base/api/help/module_config_post_help.html',
                'minVersion' => '11.12',
            ],
            'configUpdate' => [
                'reqType' => 'PUT',
                'path' => ['SugarLive', 'config'],
                'pathVars' => ['module', ''],
                'method' => 'configSave',
                'shortHelp' => 'Creates the config entries for the SugarLive module',
                'longHelp' => 'modules/SugarLive/clients/base/api/help/module_config_post_help.html',
                'minVersion' => '11.12',
            ],
        ];
    }

    /**
     * Save function for the config settings.
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionNotAuthorized
     */
    public function configSave(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module']);

        // save meta file to custom directory
        $this->configSaveMetaFiles($args, 'omnichannel-detail');

        // Refresh view metadata now that they have changed
        MetaDataManager::refreshSectionCache(MetaDataManager::MM_VIEWS);

        return [];
    }
}
