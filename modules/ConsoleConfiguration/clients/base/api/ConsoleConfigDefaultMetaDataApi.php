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

use Sugarcrm\Sugarcrm\Security\Validator\ConstraintBuilder;
use Sugarcrm\Sugarcrm\Security\Validator\Validator;

class ConsoleConfigDefaultMetaDataApi extends SugarApi
{
    /**
     * @var null
     */
    protected $validator = null;

    /**
     * @var null
     */
    protected $moduleNameConstraints = null;

    /**
     * @return array
     */
    public function registerApiRest()
    {
        return [
            'getMetadata' => [
                'reqType' => 'GET',
                'path' => ['ConsoleConfiguration', 'default-metadata'],
                'pathVars' => ['module', ''],
                'method' => 'getDefaultMetadata',
                'shortHelp' => 'This method will return the original metadata for the module.',
                'longHelp' => 'modules/ConsoleConfiguration/clients/base/api/help/default_metadata.html',
                'minVersion' => '11.9',
            ],
        ];
    }

    /**
     * @param string $module
     * @return bool
     */
    protected function isValidModule(string $module): bool
    {
        if (empty($this->validator)) {
            $this->buildModuleNameValidator();
        }
        $errors = $this->validator->validate($module, $this->moduleNameConstraints);
        return safeCount($errors) == 0;
    }

    /**
     * To build a validator and constraint
     */
    protected function buildModuleNameValidator()
    {
        $this->validator = Validator::getService();
        $contraintBuilder = new ConstraintBuilder();
        $this->moduleNameConstraints = $contraintBuilder->build(['Assert\Bean\ModuleName',]);
    }

    /**
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionInvalidParameter
     */
    public function getDefaultMetadata(ServiceBase $api, array $args)
    {
        $type = $args['type'];
        $name = $args['name'];
        $modules = explode(',', $args['modules']);

        $allowedTypes = ['filter', 'layout', 'menu', 'view'];
        if (!in_array($type, $allowedTypes)) {
            throw new SugarApiExceptionInvalidParameter('Invalid type: ' . $type);
        }

        $platform = $args['platform'] ?? 'base';

        $ret = [];
        foreach ($modules as $mod) {
            if (!$this->isValidModule($mod)) {
                throw new SugarApiExceptionInvalidParameter('Invalid module: ' . $mod);
            }

            $filename = "modules/{$mod}/clients/{$platform}/{$type}s/{$name}/{$name}.php";

            if (!file_exists($filename)) {
                $ret[$mod] = [];
                continue;
            }

            $viewdefs = [];
            require $filename;
            $ret[$mod] = $viewdefs[$mod][$platform][$type][$name];
        }

        return $ret;
    }
}
