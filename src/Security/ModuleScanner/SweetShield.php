<?php

declare(strict_types=1);
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

namespace Sugarcrm\Sugarcrm\Security\ModuleScanner;

final class SweetShield
{
    public const SWEET_PREFIX = 'sooo_sweeet_';
    public const SWEET_INTERFACE = '\\' . SweetInterface::class;
    private static array $denyLists;

    /**
     * Checks file name for null bytes, directory traversal, absolute path and stream wrappers.
     * Throws if checks do not pass
     * @param string $filename Filename
     * @return string Valid filename
     * @throws \RuntimeException
     */
    public static function validPath(string $filename): string
    {
        if (!check_file_name($filename)) {
            throw new \RuntimeException("Trying to include invalid file: $filename");
        }
        return $filename;
    }

    /**
     * Calls method of the object if it's allowed, throws otherwise
     * @param $object
     * @param string $method
     * @param ...$params
     * @return mixed
     * @throws \RuntimeException
     */
    public static function callMethod($object, string $method, ...$params)
    {
        if (!self::isAllowedMethod($object, $method)) { // checks against denylist
            $error = sprintf('Code attempted to call denylisted method "%s::%s"', is_object($object) ? get_class($object) : (string)$object, $method);
            throw new \RuntimeException($error);
        }
        return call_user_func_array([$object, $method], $params);
    }

    /**
     * Calls function if it's allowed, throws otherwise
     * @param string $function
     * @param ...$params
     * @return mixed
     * @throws \RuntimeException
     */
    public static function callFunction(string $function, ...$params)
    {
        if (!self::isAllowedFunction($function)) {// checks against denylist
            if (is_callable(['self', 'safe_' . $function])) {// safe alternative for internal function
                return call_user_func_array(['self', 'safe_' . $function], $params);
            } elseif (!in_array(strtolower(self::SWEET_PREFIX . $function), get_defined_functions()['user'])) {
                $error = sprintf('Code attempted to call denylisted function "%s"', $function);
                throw new \RuntimeException($error);
            } else {
                return call_user_func_array(self::SWEET_PREFIX . $function, $params);
            }
        }
        return call_user_func_array($function, $params);
    }

    /**
     * Checks if the function is allowed to be called
     */
    public static function isAllowedFunction(string $function): bool
    {
        $deniedFunctions = self::getDenyList('functions');
        return !in_array(strtolower($function), $deniedFunctions);
    }

    /**
     * Checks if the function is built-in or a custom one
     * @param string $function
     * @return bool
     */
    public static function isInternalFunction(string $function): bool
    {
        return in_array($function, get_defined_functions()['internal']);
    }

    /**
     * Checks if the method is allowed to be called
     * @param $obj
     * @param string $method
     * @return bool
     */
    public static function isAllowedMethod($obj, string $method): bool
    {
        $class = is_object($obj) ? get_class($obj) : (string)$obj;

        // Allow all classes defined in MLP
        if (is_subclass_of($class, self::SWEET_INTERFACE)) {
            return true;
        }

        if (in_array($class, self::getDenyList('classes'))) {
            return false;
        }

        $deniedMethods = self::getDenyList('methods');
        if (in_array($method, $deniedMethods)) {//denied for all classes
            return false;
        }

        if (isset($deniedMethods[$method]) && $deniedMethods[$method] === $class) {
            return false;
        }
        return true;
    }

    /**
     * Safe variant of unserialize, enforces ['allowed_classes' => false] to be passed as an option
     * @param string $data
     * @param array $options
     * @return mixed
     */
    private static function safe_unserialize(string $data, array $options = [])
    {
        $options['allowed_classes'] = false;
        return unserialize($data, $options);
    }

    /**
     * Safe alternative for file_get_contents(), enforces usage of custom stream wrapper
     * @param string $filename
     * @param bool $use_include_path
     * @param $context
     * @param int $offset
     * @param int|null $length
     * @return false|string
     */
    private static function safe_file_get_contents(string $filename, bool $use_include_path = false, $context = null, int $offset = 0, ?int $length = null)
    {
        if (substr($filename, 0, 9) !== 'upload://') {
            $filename = "upload://$filename";
        }
        //Disable include_path and ignore $context
        return file_get_contents($filename, false, null, $offset, $length);
    }

    /**
     * Returns denylist of functions, methods, and classes
     * @param string $listName
     * @return array
     */
    private static function getDenyList(string $listName): array
    {
        if (!isset(self::$denyLists)) {
            self::$denyLists = (new \ModuleScanner())->getEffectiveDenyLists();
        }
        return self::$denyLists[$listName];
    }
}
