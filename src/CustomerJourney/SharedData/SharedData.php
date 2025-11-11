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

namespace Sugarcrm\Sugarcrm\CustomerJourney\SharedData;

/**
 * This class is here to provide functions for
 * shared data management as key value pair.
 *
 * Example:
 * Shared data against a particular journey, like
 * User Ids storage in tasks/stage completion and
 * then send the notification at the end.
 */
class SharedData
{
    /**
     * Shared variable to compile and share data between classes
     */
    private static $data = [];

    /**
     * Sets a value againt a key provided
     *
     * @param string $key
     * @param array $value
     */
    public function setData($key, $value)
    {
        if (empty($key)) {
            return false;
        }

        self::$data[$key] = $value;
    }

    /**
     * Gets a value againt a key provided
     *
     * @param string $key
     * @return array
     */
    public function getData($key)
    {
        if (!isset(self::$data[$key])) {
            return [];
        }

        return self::$data[$key];
    }

    /**
     * Clears the data against the provided key
     *
     * @param string $key
     * @return boolean
     */
    public function resetData($key)
    {
        if (empty($key)) {
            return false;
        }

        unset(self::$data[$key]);
        return (isset(self::$data[$key])) ? false : true;
    }
}
