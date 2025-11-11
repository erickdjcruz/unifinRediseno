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

namespace Sugarcrm\Sugarcrm\Denormalization\Relate;

use Administration;
use ModuleInstaller;
use SugarAutoLoader;

final class HookInstaller
{
    private const HOOK_FILE = __DIR__ . '/files/denorm_field_hook.php';

    /** @var array */
    protected $moduleNames;

    public function __construct(array $moduleNames)
    {
        $this->moduleNames = $moduleNames;
    }

    public function create(): void
    {
        foreach ($this->moduleNames as $moduleName) {
            copy(self::HOOK_FILE, $this->getLogicHookPath($moduleName));
        }
        $this->rebuildCache();
    }

    public function remove(): void
    {
        foreach ($this->moduleNames as $moduleName) {
            $file = $this->getLogicHookPath($moduleName);
            if (is_file($file)) {
                unlink($file);
            }
        }
        $this->rebuildCache();
    }

    protected function rebuildCache()
    {
        $moduleInstallerClass = SugarAutoLoader::customClass('ModuleInstaller');
        /** @var ModuleInstaller $mi */
        $mi = new $moduleInstallerClass();
        $mi->silent = true;
        $mi->rebuild_extensions($this->moduleNames, ['logichooks']);
    }

    protected function getLogicHookPath(string $moduleName): string
    {
        $dir = SugarAutoLoader::normalizeFilePath("custom/Extension/modules/{$moduleName}/Ext/LogicHooks");
        SugarAutoLoader::ensureDir($dir);

        return "$dir/denorm_field_hook.php";
    }
}
