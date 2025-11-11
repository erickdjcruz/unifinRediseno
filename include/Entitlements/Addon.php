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

namespace Sugarcrm\Sugarcrm\Entitlements;

// This section of code is a portion of the code referred
// to as Critical Control Software under the End User
// License Agreement.  Neither the Company nor the Users
// may modify any portion of the Critical Control Software.
use Sugarcrm\Sugarcrm\inc\Entitlements\Exception\SubscriptionException;

/**
 * Class Addon
 *
 * addon part of Sugar subscription
 */
class Addon
{
    /**
     * data
     * @var array
     */
    protected $data = [];

    /**
     * list of attrubutes for calculation
     * @var string[]
     */
    public const ATTRIBUTES =
        [
            'quantity',
            'start_date_c',
            'expiration_date',
        ];

    /**
     * attribute name for bundled products in license data
     */
    public const BUNDLED_PRODUCTS_KEY = 'bundled_products';

    /**
     * ctor
     * @param string $id
     * @param array $data
     * @throws \Exception
     */
    public function __construct(string $id, array $data)
    {
        $this->parse($id, $data);
    }

    /**
     * parse the Addon section
     * @param string $id
     * @param array $data
     * @throws \Exception
     */
    protected function parse(string $id, array $data)
    {
        if (empty($id)) {
            throw new SubscriptionException('No subscription Id in json data');
        }

        $this->data['id'] = $id;
        if (empty($data)) {
            return;
        }

        // get other fields first, such as start_date, quantity, expiration_date
        foreach ($data as $key => $value) {
            if ($key === self::BUNDLED_PRODUCTS_KEY && is_array($value)) {
                continue;
            } else {
                $this->data[$key] = $value;
            }
        }

        // handle bundled products
        $bundledProducts = isset($data[self::BUNDLED_PRODUCTS_KEY]) && is_array($data[self::BUNDLED_PRODUCTS_KEY]) ? $data[self::BUNDLED_PRODUCTS_KEY] : [];
        $this->data[self::BUNDLED_PRODUCTS_KEY] = [];
        foreach ($bundledProducts as $bundledId => $itemData) {
            $bundled = new Addon($bundledId, $itemData);
            foreach (self::ATTRIBUTES as $field) {
                if (isset($this->data[$field])) {
                    $bundled->setValue($field, $this->data[$field]);
                }
            }
            $this->data[self::BUNDLED_PRODUCTS_KEY][] = $bundled;
        }
    }

    /**
     * access method
     * @param string $name
     * @return mixed|null
     */
    public function __get(string $name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
        return null;
    }

    /**
     * setter, only set value if the value is not present
     * @param string $name
     * @param mixed $value
     * @param bool $overwrite overwrite flag
     */
    public function setValue(string $name, $value, bool $overwrite = false)
    {
        if (!isset($this->data[$name]) || $overwrite) {
            $this->data[$name] = $value;
        }
    }

    /**
     * to get bundled products
     * @return array
     */
    public function getBundledProducts(): array
    {
        return $this->data[self::BUNDLED_PRODUCTS_KEY] ?? [];
    }

    /**
     * check if it has bundle products
     * @return bool
     */
    public function hasBundledProducts(): bool
    {
        return safeCount($this->getBundledProducts()) > 0;
    }

    /**
     * check if bundle is valid
     * @return bool
     */
    public function isValidBundle(): bool
    {
        $bundles = $this->getBundledProducts();
        if (empty($bundles)) {
            return true;
        }

        // check quantity
        foreach ($bundles as $product) {
            if ($this->quantity != $product->quantity) {
                if (!empty($GLOBALS['log'])) {
                    $GLOBALS['log']->error('bundled product has different quantity!');
                }
                return false;
            }
        }
        return true;
    }
}
//END REQUIRED CODE DO NOT MODIFY
