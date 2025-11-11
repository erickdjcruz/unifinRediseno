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

/**
 * API for Config Maps.
 */
// @codingStandardsIgnoreLine
class MapsConfigApi extends ConfigApi
{
    /**
     * @return array
     */
    public function registerApiRest()
    {
        return [
            'setConfig' => [
                'reqType' => ['POST'],
                'path' => ['Administration', 'maps', 'config', '?'],
                'pathVars' => ['', '', '', 'category'],
                'method' => 'setConfig',
                'shortHelp' => 'Sets configuration for a category',
                'longHelp' => 'include/api/help/administration_config_post_help.html',
                'exceptions' => ['SugarApiExceptionNotAuthorized'],
                'ignoreSystemStatusError' => true,
                'minVersion' => '11.16',
            ],
        ];
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
        if (!hasMapsLicense()) {
            throw new SugarApiExceptionNotAuthorized(translate('LBL_MAPS_NO_LICENSE_ACCESS'));
        }

        //keep previous values locally
        $previousConfig = $this->getConfig($api, ['category' => 'maps']);

        $previousModules = $previousConfig['maps_enabled_modules'];
        $newModules = $args['maps_enabled_modules'];

        $removedModules = array_diff($previousModules, $newModules);
        $addedModules = array_diff($newModules, $previousModules);

        $this->manageMapsDistanceField($addedModules, $removedModules);

        $modulesToBeRefreshed = array_merge($removedModules, $addedModules);

        $result = parent::setConfig($api, $args);

        $this->refreshCacheSections($modulesToBeRefreshed);

        return $result;
    }

    /**
     * Gets metadata from a file.
     *
     * @param string $type The type of the file (custom, base..)
     * @param string $moduleName target module name
     * @return array
     */
    public function getMetadataFromFile(string $type, string $moduleName): array
    {
        $viewdefs = [];

        $file = $this->getFilterMetadataFilename($type, $moduleName);

        try {
            require FileLoader::validateFilePath($file);
        } catch (\RuntimeException $e) {
            throw new SugarApiExceptionError('LBL_MAPS_INVALID_MODULE', [$moduleName]);
        }


        return $viewdefs;
    }

    /**
     * Refresh cache
     *
     * @param array $modulesToBeRefreshed
     * @return void
     */
    protected function refreshCacheSections(array $modulesToBeRefreshed)
    {
        MetaDataManager::refreshModulesCache($modulesToBeRefreshed);
        MetaDataManager::refreshSectionCache('config');
    }

    /**
     * getFilterMetadataFilename function
     *
     * @param string $type The type of the file (custom, base..)
     * @param string $moduleName target module name
     * @return string
     */
    protected function getFilterMetadataFilename(string $type, string $moduleName): string
    {
        $path = MetaDataFiles::getPath($type) . "modules/{$moduleName}/clients/base/filters/default/default.php";

        return $path;
    }

    /**
     * manageMapsDistanceField add/remove maps distance field for search view
     *
     * @param array $addedModules
     * @param array $removedModules
     * @return void
     */
    private function manageMapsDistanceField(array $addedModules, array $removedModules)
    {
        $field = [
            'fieldName' => '$distance',
            'fieldDef' => [
                'name' => '$distance',
                'vname' => 'LBL_MAPS_DISTANCE',
                'type' => 'maps-distance',
                'source' => 'non-db',
                'merge_filter' => 'enabled',
                'licenseFilter' => ['MAPS'],
            ],
        ];

        foreach ($removedModules as $removedModule) {
            $this->deployDefaultFilterMetadata($removedModule, $field, false);
        }

        foreach ($addedModules as $addedModule) {
            $this->deployDefaultFilterMetadata($addedModule, $field, true);
        }
    }

    /**
     * Deploy updated metadata
     *
     * @param string $module
     * @param array $field
     * @param bool $enabled
     * @return void
     */
    private function deployDefaultFilterMetadata(string $module, array $field, bool $enabled)
    {
        $metadataFile = $this->getFilterMetadataFilename('base', $module);

        $viewdefs = $this->getMetadataFromFile('base', $module);

        if ($enabled) {
            $viewdefs = $this->addMapField($viewdefs, $field, $module);
        } else {
            $viewdefs = $this->removeMapField($viewdefs, $field, $module);
        }

        write_array_to_file(
            "viewdefs['{$module}']['base']['filter']['default']",
            $viewdefs[$module]['base']['filter']['default'],
            $metadataFile
        );
    }

    /**
     * Add field to default fields
     *
     * @param array $viewdefs
     * @param array $field
     * @param string $module
     * @return array
     */
    private function addMapField(array $viewdefs, array $field, string $module): array
    {
        $fieldName = $field['fieldName'];
        $fieldDef = $field['fieldDef'];

        if (!array_key_exists($module, $viewdefs) ||
            !array_key_exists('base', $viewdefs[$module]) ||
            !array_key_exists('filter', $viewdefs[$module]['base']) ||
            !array_key_exists('default', $viewdefs[$module]['base']['filter']) ||
            !array_key_exists('fields', $viewdefs[$module]['base']['filter']['default'])) {
            throw new SugarApiExceptionError('LBL_MAPS_INVALID_MODULE', [$module]);
        }

        $defaultFields = $viewdefs[$module]['base']['filter']['default']['fields'];

        $defaultFields[$fieldName] = $fieldDef;

        $viewdefs[$module]['base']['filter']['default']['fields'] = $defaultFields;

        return $viewdefs;
    }

    /**
     * Remove field from default fields
     *
     * @param array $viewdefs
     * @param array $field
     * @param string $module
     * @return array
     */
    private function removeMapField(array $viewdefs, array $field, string $module): array
    {
        $fieldName = $field['fieldName'];

        if (!array_key_exists($module, $viewdefs) ||
            !array_key_exists('base', $viewdefs[$module]) ||
            !array_key_exists('filter', $viewdefs[$module]['base']) ||
            !array_key_exists('default', $viewdefs[$module]['base']['filter']) ||
            !array_key_exists('fields', $viewdefs[$module]['base']['filter']['default'])) {
            throw new SugarApiExceptionError('LBL_MAPS_INVALID_MODULE', [$module]);
        }

        unset($viewdefs[$module]['base']['filter']['default']['fields'][$fieldName]);

        return $viewdefs;
    }
}
