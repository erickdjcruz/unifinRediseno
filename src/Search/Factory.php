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

namespace Sugarcrm\Sugarcrm\Search;

class Factory
{
    /**
     * @var array
     */
    protected static $instances;

    /**
     * Returns a singleton instance of the object passed as a parameter to the method
     *
     * @param String $className
     *
     * @return 'Sugarcrm\\Sugarcrm\\Search\\' . $className
     */
    public static function getInstance(string $className)
    {
        if (empty($className)) {
            return false;
        }

        if (!empty(self::$instances[$className])) {
            return self::$instances[$className];
        }

        $classFullName = \SugarAutoLoader::customClass('Sugarcrm\\Sugarcrm\\Search\\' . $className);
        self::$instances[$className] = new $classFullName();
        return self::$instances[$className];
    }
}
