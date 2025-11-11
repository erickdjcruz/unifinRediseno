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

namespace Sugarcrm\Sugarcrm\PackageManager\Exception;

use ModuleScanner;

class ModuleScannerException extends InvalidPackageException
{
    /**
     * @var ModuleScanner
     */
    protected $moduleScanner;

    /**
     * @return ModuleScanner
     */
    public function getModuleScanner(): ModuleScanner
    {
        return $this->moduleScanner;
    }

    /**
     * @param ModuleScanner $moduleScanner
     * @return $this
     */
    public function setModuleScanner(ModuleScanner $moduleScanner): ModuleScannerException
    {
        $this->moduleScanner = $moduleScanner;
        return $this;
    }
}
