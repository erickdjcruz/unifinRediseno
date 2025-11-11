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

namespace Sugarcrm\Sugarcrm\Dashboards;

class DashletValidatorFactory
{
    private static $instance;

    /**
     * constructor
     */
    private function __construct()
    {
        // private constructor to prevent instantiation from outside the class
    }

    /**
     * @static
     *
     * @return DashletValidatorFactory
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Validate a dashlet against its meta
     *
     * @param mixed $dashletMeta
     * @return boolean
     */
    public function validate($dashletMeta)
    {
        $validator = $this->buildValidator($dashletMeta);

        if ($validator) {
            return $validator->validate($dashletMeta);
        }

        return true;
    }

    /**
     * Validate a dashlet's fields against its meta
     *
     * @param mixed $dashletMeta
     * @param mixed $field
     * @return boolean
     */
    public function validateField($dashletMeta, $field)
    {
        $validator = $this->buildValidator($dashletMeta);

        if ($validator) {
            return $validator->validateField($dashletMeta, $field);
        }

        return true;
    }

    /**
     * Create the Dashlet Validator
     *
     * @param mixed $dashletMeta
     *
     * @return object|false
     */
    protected function buildValidator($dashletMeta)
    {
        $dashletType = $dashletMeta->view->type ?? null;

        if (!isset($dashletType)) {
            return false;
        }

        $validatorClassName = $this->generateValidatorClassName(kebabToCamel($dashletType));

        if (class_exists($validatorClassName)) {
            return new $validatorClassName();
        }

        return false;
    }

    /**
     * Build and return the validator class name
     *
     * @param string $dashletType
     * @return string
     */
    private function generateValidatorClassName($dashletType)
    {
        return 'Sugarcrm\Sugarcrm\Dashboards\Validators\\' . $dashletType . 'Validator';
    }
}
