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

class ConsoleConfigModuleApi extends ConfigModuleApi
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    protected $validator = null;

    /**
     * @var \Symfony\Component\Validator\Constraint[]
     */
    protected $moduleNameConstraints = null;

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
                'path' => ['ConsoleConfiguration', 'config'],
                'pathVars' => ['module', ''],
                'method' => 'configSave',
                'shortHelp' => 'Creates the config entries for the ConsoleConfiguration module',
                'longHelp' => 'modules/ConsoleConfiguration/clients/base/api/help/module_config_post_help.html',
                'minVersion' => '11.9',
            ],
            'configUpdate' => [
                'reqType' => 'PUT',
                'path' => ['ConsoleConfiguration', 'config'],
                'pathVars' => ['module', ''],
                'method' => 'configSave',
                'shortHelp' => 'Creates the config entries for the ConsoleConfiguration module',
                'longHelp' => 'modules/ConsoleConfiguration/clients/base/api/help/module_config_post_help.html',
                'minVersion' => '11.9',
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
        $this->configSaveMetaFiles($args);

        // save labels
        $this->configSaveLabels($args);

        unset($args['viewdefs']);
        unset($args['labels']);

        // call parent method to save other config settings into config table
        return parent::configSave($api, $args);
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
     * @param array $args
     * @param string $viewName
     * @throws SugarApiExceptionInvalidParameter
     */
    protected function configSaveMetaFiles(array $args, string $viewName = 'multi-line-list')
    {
        if (empty($args['viewdefs'])) {
            return;
        }
        foreach ($args['viewdefs'] as $mod => $defs) {
            if (!$this->isValidModule($mod)) {
                throw new SugarApiExceptionInvalidParameter('Invalid module: ' . $mod);
            }
            $filename = 'custom/modules/' . $mod . '/clients/base/views/' . $viewName . '/' . $viewName . '.php';
            if ($this->saveToFile($filename, $defs, $mod)) {
                MetaDataFiles::clearModuleClientCache($mod, 'view');
                TemplateHandler::clearCache($mod);
            }
        }
    }

    /**
     * @param $filename
     * @param $defs
     * @param $module
     * @param string $viewVariable
     * @return boolean
     */
    protected function saveToFile($filename, $defs, $module, $viewVariable = 'viewdefs'): bool
    {
        if (file_exists($filename)) {
            $filename = FileLoader::validateFilePath($filename);
            unlink($filename);
        }

        mkdir_recursive(dirname($filename));

        if (write_array_to_file($viewVariable . "['$module']", $defs, $filename) === false) {
            $GLOBALS['log']->fatal(get_class($this) . ": could not write new $viewVariable file " . $filename);
            return false;
        }
        return true;
    }

    /**
     * @param array $args
     * @throws SugarApiExceptionInvalidParameter
     */
    protected function configSaveLabels(array $args)
    {
        global $current_language;

        if (empty($args['labels'])) {
            return;
        }

        foreach ($args['labels'] as $mod => $labels) {
            if (!$this->isValidModule($mod)) {
                throw new SugarApiExceptionInvalidParameter('Invalid module: ' . $mod);
            }
            $labelsToSave = [];
            foreach ($labels as $label) {
                if ($mod && !empty($label['label']) && !empty($label['labelValue'])) {
                    // ParserLabel expects this format
                    $labelsToSave['label_' . $label['label']] = $label['labelValue'];
                }
            }
            $parser = new ParserLabel($mod);
            $parser->handleSave($labelsToSave, $current_language);

            // Clear the language cache to make sure the view picks up the latest
            $cache_key = LanguageManager::getLanguageCacheKey($mod, $current_language);
            sugar_cache_clear($cache_key);
        }
        MetaDataManager::refreshSectionCache(MetaDataManager::MM_LABELS);
        MetaDataManager::refreshSectionCache(MetaDataManager::MM_ORDEREDLABELS);
    }
}
