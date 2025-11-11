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
 * The ViewdefManager is a service designed to read and write viewdef files.
 */

namespace Sugarcrm\Sugarcrm\MetaData;

class ViewdefManager
{
    /**
     * Entrypoint to find the proper viewdef for a module/platform/view and load it for manipulation
     *
     * @param string $platform
     * @param string $module
     * @param string $view
     * @param bool $loadBase flag to load the base config instead of the custom one.
     * @param bool $isLayout flag to load a layout instead of a view.
     * @return array viewdefs
     */
    public function loadViewdef($platform, $module, $view, $loadBase = false, $isLayout = false)
    {
        $type = $isLayout ? 'layout' : 'view';

        $paths = $this->getClientFiles([$platform], $type, $module);
        $path = $this->findModuleViewdef($paths, $module, $view, $loadBase);

        if ($path === null) {
            return [];
        }

        //return viewdefs loaded in loadDef
        $viewdef = $this->loadDef($path['path']);

        //make sure the path we want exists in the loaded file
        if (isset($viewdef[$module][$platform][$type][$view])) {
            return $viewdef[$module][$platform][$type][$view];
        }

        return [];
    }

    /**
     * Saves the viewdef out to the appropriate location
     *
     * @param string $viewdef
     * @param string $module
     * @param string $platform
     * @param string $view
     * @param bool $isLayout flag to load a layout instead of a view.
     */
    public function saveViewdef($viewdef, $module, $platform, $view, $isLayout = false)
    {
        $type = $isLayout ? 'layout' : 'view';

        $path = "custom/modules/{$module}/clients/{$platform}/{$type}s/{$view}/{$view}.php";
        $varname = "viewdefs['{$module}']['{$platform}']['{$type}']['{$view}']";

        if (!file_exists($path)) {
            sugar_touch($path);
        }

        write_array_to_file($varname, $viewdef, $path);
    }

    /**
     * Abstracts loading the viewdef
     *
     * @param string $path
     * @return array viewdefs
     */
    protected function loadDef($path)
    {
        $viewdefs = [];
        if (!empty($path)) {
            require $path;
        }

        return $viewdefs;
    }

    /**
     * abstracts away the static call to MetaDataFiles::getClientFiles
     *
     * @param string[] $platform
     * @param string $type
     * @param string $module
     * @return array
     */
    protected function getClientFiles($platform, $type, $module)
    {
        return \MetaDataFiles::getClientFiles($platform, $type, $module);
    }

    /**
     * Sorts through the output of MetaDataFiles::getClientFiles to find the proper definition file for a viewdef,
     * starting with custom first and working down from there.
     *
     * @param array<string,array<string,mixed>> $paths Output from calling {@link MetaDataFiles::getClientFiles}
     * @param string $module Quotes, Opportunities, etc
     * @param string $view record, edit, detail, etc
     * @param bool $preferBase Flag to load base value over custom if base exists
     *
     * @return array<string,mixed>|null
     */
    public function findModuleViewdef($paths, $module, $view, $preferBase = false)
    {
        $returnPath = null;

        foreach ($paths as $path) {
            // make sure this is the view we're looking for,
            // and we're in the right module (sometimes not),
            // and the path is not an extension file
            if ($path['file'] == $view . '.php' &&
                strpos($path['path'], $module) !== false &&
                strpos($path['path'], '.ext.') === false
            ) {
                $returnPath = $path;
                // look for the custom def first, then load the default if we can't find a custom
                // unless $preferBase is specified
                if (strpos($path['path'], 'custom') !== false && !$preferBase) {
                    $returnPath = $path;
                    break;
                }
            }
        }

        return $returnPath;
    }
}
