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
 * Class Subscription
 *
 * sugar subscription object, it parses raw subscription data and provides APIs to access those data
 */
class Subscription
{
    /**
     * license keys or types
     */
    public const SUGAR_SELL_KEY = 'SUGAR_SELL';
    public const SUGAR_SELL_ESSENTIALS_KEY = 'SUGAR_SELL_ESSENTIALS';
    public const SUGAR_SELL_BUNDLE_KEY = 'SUGAR_SELL_BUNDLE';
    public const SUGAR_SELL_PREMIER_BUNDLE_KEY = 'SUGAR_SELL_PREMIER_BUNDLE';
    public const SUGAR_SELL_PREMIER_KEY = 'SELL_PREMIER';
    public const SUGAR_SELL_ADVANCED_BUNDLE_KEY = 'SUGAR_SELL_ADVANCED_BUNDLE';
    public const SUGAR_SERVE_KEY = 'SUGAR_SERVE';
    public const SUGAR_BASIC_KEY = 'CURRENT';
    public const SUGAR_HINT_KEY = 'HINT';
    public const SUGAR_AUTOMATE_KEY = 'AUTOMATE';
    public const SUGAR_PREDICT_ADVANCED_KEY = 'PREDICT_ADVANCED';
    public const SUGAR_PREDICT_PREMIER_KEY = 'PREDICT_PREMIER';
    public const SUGAR_CONNECT_KEY = 'CONNECT';
    public const SUGAR_DISCOVER_KEY = 'DISCOVER';
    public const SUGAR_MAPS_KEY = 'MAPS';
    public const SUGAR_ADVANCEDFORECAST_KEY = 'ADVANCEDFORECAST';
    public const SUGAR_SERVE_ADDON_PACKAGE_KEY = 'SERVE_ADDON_PACKAGE';

    /**
     * unknown type
     */
    public const UNKNOWN_TYPE = 'UNKNOWN';

    /**
     * Mango CRM keys,
     * please keep the order of this list, the order of this list will be used in displaying user's license type
     */
    public const MANGO_KEYS = [
        Subscription::SUGAR_SELL_PREMIER_BUNDLE_KEY,
        Subscription::SUGAR_SELL_PREMIER_KEY,
        Subscription::SUGAR_SELL_ADVANCED_BUNDLE_KEY,
        Subscription::SUGAR_SELL_ESSENTIALS_KEY,
        Subscription::SUGAR_SELL_BUNDLE_KEY,
        Subscription::SUGAR_SELL_KEY,
        Subscription::SUGAR_SERVE_KEY,
        Subscription::SUGAR_BASIC_KEY,
    ];

    /**
     * Bundle keys, the bundle will contain other products
     */
    public const BUNDLE_KEYS = [
        Subscription::SUGAR_SELL_ESSENTIALS_KEY,
        Subscription::SUGAR_SELL_BUNDLE_KEY,
        Subscription::SUGAR_SELL_PREMIER_BUNDLE_KEY,
        Subscription::SUGAR_SELL_PREMIER_KEY,
        Subscription::SUGAR_SELL_ADVANCED_BUNDLE_KEY,
    ];

    /**
     * current supported SELL keys
     */
    public const SELL_KEYS = [
        self::SUGAR_SELL_KEY,
        self::SUGAR_SELL_ESSENTIALS_KEY,
        self::SUGAR_SELL_BUNDLE_KEY,
        self::SUGAR_SELL_PREMIER_BUNDLE_KEY,
        self::SUGAR_SELL_PREMIER_KEY,
        self::SUGAR_SELL_ADVANCED_BUNDLE_KEY,
    ];

    /**
     * current supported keys
     */
    public const SUPPORTED_KEYS = [
        self::SUGAR_BASIC_KEY,
        self::SUGAR_SELL_KEY,
        self::SUGAR_SERVE_KEY,
        self::SUGAR_HINT_KEY,
        self::SUGAR_AUTOMATE_KEY,
        // new in Mango 12.0
        self::SUGAR_SELL_ESSENTIALS_KEY,
        self::SUGAR_SELL_BUNDLE_KEY,
        self::SUGAR_SELL_PREMIER_BUNDLE_KEY,
        self::SUGAR_SELL_ADVANCED_BUNDLE_KEY,
        self::SUGAR_PREDICT_ADVANCED_KEY,
        self::SUGAR_PREDICT_PREMIER_KEY,
        self::SUGAR_CONNECT_KEY,
        self::SUGAR_DISCOVER_KEY,
        self::SUGAR_MAPS_KEY,
        self::SUGAR_ADVANCEDFORECAST_KEY,
        // new in 14.0.0
        self::SUGAR_SELL_PREMIER_KEY,
    ];

    /**
     * mapping product codes to internal keys
     */
    public const PRODUCT_CODE_MAPPING = [
        'ENT' => self::SUGAR_BASIC_KEY,
        'PRO' => self::SUGAR_BASIC_KEY,
        'ULT' => self::SUGAR_BASIC_KEY,
        'SELL' => self::SUGAR_SELL_KEY,
        'SERVE' => self::SUGAR_SERVE_KEY,
        'HINT' => self::SUGAR_HINT_KEY,
        'AUTOMATE' => self::SUGAR_AUTOMATE_KEY,
        'PREDICT_ADVANCED' => self::SUGAR_PREDICT_ADVANCED_KEY,
        'PREDICT_PREMIER' => self::SUGAR_PREDICT_PREMIER_KEY,
        'CONNECT' => self::SUGAR_CONNECT_KEY,
        'DISCOVER' => self::SUGAR_DISCOVER_KEY,
        'MAPS' => self::SUGAR_MAPS_KEY,
        'ADVANCED_FORECAST' => self::SUGAR_ADVANCEDFORECAST_KEY,
        'SERVE_ADDON_PACKAGE' => self::SUGAR_SERVE_ADDON_PACKAGE_KEY,
    ];

    /**
     * mapping for edition
     */
    public const SELL_EDITIONS_MAPPING = [
        'SELL_PREMIER' => self::SUGAR_SELL_PREMIER_BUNDLE_KEY,
        'SELL_ADVANCED' => self::SUGAR_SELL_ADVANCED_BUNDLE_KEY,
        'SELL_ESSENTIALS' => self::SUGAR_SELL_ESSENTIALS_KEY,
        'OTHERS' => self::SUGAR_SELL_BUNDLE_KEY,
    ];

    /**
     * visible Non CRM products in user's licese types' list
     */
    public const VISIBLE_NONCRM_PRODUCTS = [
        Subscription::SUGAR_HINT_KEY,
        Subscription::SUGAR_MAPS_KEY,
        Subscription::SUGAR_AUTOMATE_KEY,
    ];

    /**
     * bundled producted
     */
    public const SELL_PREMIER_INCLUDED_PRODUCTS = [
        Subscription::SUGAR_HINT_KEY,
        Subscription::SUGAR_MAPS_KEY,
        Subscription::SUGAR_AUTOMATE_KEY,
    ];

    /**
     * non product keys, like package key
     */
    public const NON_PRODUCT_KEYS = [
        self::SUGAR_SERVE_ADDON_PACKAGE_KEY,
    ];

    /**
     * internal data
     * @var array
     */
    protected $data = [];

    /**
     * parsed subscription data
     * @var array
     */
    protected $subscriptions = [];
    protected $expiredSubscriptions = [];

    /**
     * @var array of Addons
     */
    protected $addons = [];

    /**
     * use default value from license config
     * @var bool
     */
    protected $useDefault = false;

    /**
     * private Subscription constructor.
     * @param mixed $jsonData
     */
    public function __construct($jsonData)
    {
        if ($jsonData === false || $jsonData === '') {
            $this->useDefault = true;
            $this->subscriptions[self::SUGAR_BASIC_KEY] = $this->getDefaultSubscription();
        } else {
            $this->parse($jsonData);
        }
    }

    /**
     * parse the raw subscription data
     * @param string $jsonData
     */
    protected function parse(string $jsonData)
    {
        $decodedData = json_decode($jsonData, true);
        if ($decodedData === null) {
            throw new SubscriptionException('Invalid subscription json data');
        }

        if (empty($decodedData['subscription'])) {
            return;
        }

        foreach ($decodedData['subscription'] as $key => $value) {
            if ($key === 'addons' && safeCount($decodedData['subscription'][$key]) > 0) {
                foreach ($decodedData['subscription'][$key] as $addonId => $addonData) {
                    $this->addons[$addonId] = new Addon($addonId, $addonData);
                }
            } else {
                $this->data[$key] = $value;
            }
        }
        $this->data['addons'] = $this->addons;
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
     * to get subscriptions
     * only gets the subscriptions with quantity > 0 and not expired.
     *
     * return in array format
     * [
     *      'quantity' => ...,
     *      'expiration_date' => ...,
     * ];
     * @return array
     */
    public function getSubscriptions(): array
    {
        if (!empty($this->subscriptions) || $this->useDefault) {
            return $this->subscriptions;
        }

        if (empty($this->data)) {
            return [];
        }

        if (!empty($this->error)) {
            $GLOBALS['log']->fatal('there is an error in license server response: ' . $this->error);
            return [];
        }

        $subscriptions = $this->getSubscriptionFromAddons($this->addons);
        $this->subscriptions = $subscriptions;

        return $subscriptions;
    }

    /**
     * parse the list of addons.
     * @param array $addons
     * @param bool $isTopLevel
     * @param mixed $expDate
     * @param mixed $sttDate
     *
     * @return array
     */
    protected function getSubscriptionFromAddons(array $addons, bool $isTopLevel = true, $expDate = '', $sttDate = ''): array
    {
        $subscriptions = [];
        // will skip top level of the subscription
        // initiate quantity count
        foreach (self::SUPPORTED_KEYS as $type) {
            $subscriptions[$type]['quantity'] = 0;
        }

        // check addons, only interested in 'SELL', 'SERVE' and Legacy product codes such as 'ENT', 'PRO' and 'ULT'.
        // also need to check bundles
        // ignore any other addons for now
        foreach ($addons as $addon) {
            $quantity = (int)$addon->quantity;
            $expirationDate = $addon->expiration_date;
            if (empty($expirationDate)) {
                $expirationDate = $expDate;
            }

            $startDate = $addon->start_date_c;
            if (empty($startDate)) {
                $startDate = $sttDate;
            }
            if (($quantity > 0)
                && ((empty($expirationDate) && !$isTopLevel) || (!empty($expirationDate) && is_numeric($expirationDate) && $expirationDate > time()))
                && (empty($startDate) || !empty($startDate) && is_numeric($startDate) && $startDate < time())) {
                // using product code to find out subscription types
                $productCode = $addon->product_code_c;
                if (!empty($productCode) && !empty(self::PRODUCT_CODE_MAPPING[strtoupper($productCode)])) {
                    $key = self::PRODUCT_CODE_MAPPING[strtoupper($productCode)];
                    $bundlesSubscriptions = [];
                    if ($key === self::SUGAR_SELL_KEY) {
                        // reassign to bundle key based on edition
                        $edition = $addon->product_edition_c;
                        if (!empty($edition) && isset(self::SELL_EDITIONS_MAPPING[strtoupper($edition)])) {
                            $key = self::SELL_EDITIONS_MAPPING[strtoupper($edition)];
                        }
                        if ($addon->hasBundledProducts()) {
                            if (!empty($edition)) {
                                $key = self::SELL_EDITIONS_MAPPING[strtoupper($edition)] ?? self::SUGAR_SELL_BUNDLE_KEY;
                            } else {
                                $key = self::SUGAR_SELL_BUNDLE_KEY;
                            }
                            // get bundles
                            $bundles = $addon->getBundledProducts();
                            $bundlesSubscriptions = $this->getSubscriptionFromAddons($bundles, false, $expirationDate, $startDate);
                        }
                    }

                    if (self::isProductKey($key)) {
                        // calculate the expiration date, using the min date as expiration date
                        if (!empty($subscriptions[$key]['expiration_date'])
                            && $expirationDate > $subscriptions[$key]['expiration_date']
                        ) {
                            $expirationDate = $subscriptions[$key]['expiration_date'];
                        }

                        $customerProductName = $addon->customer_product_name_c;
                        $subscriptions[$key] = [
                            'quantity' => $subscriptions[$key]['quantity'] + $quantity,
                            'expiration_date' => $expirationDate,
                            'start_date' => $startDate,
                            'customer_product_name' => $customerProductName,
                            // this may not be needed since PM has promised 1 bundle product for a license key
                            Addon::BUNDLED_PRODUCTS_KEY => array_merge($subscriptions[$key][Addon::BUNDLED_PRODUCTS_KEY] ?? [], $bundlesSubscriptions),
                        ];
                    } elseif ($addon->hasBundledProducts()) {
                        // get bundles
                        $bundles = $addon->getBundledProducts();
                        $bundlesSubscriptions = $this->getSubscriptionFromAddons($bundles, false, $expirationDate, $startDate);
                        foreach ($bundlesSubscriptions as $subKey => $sub) {
                            if ($subscriptions[$subKey]['quantity'] === 0) {
                                $subscriptions[$subKey] = $sub;
                            } else {
                                // merge two addon together
                                if (!empty($subscriptions[$subKey]['expiration_date'])
                                    && !empty($sub['expiration_date'])
                                    && $sub['expiration_date'] < $subscriptions[$subKey]['expiration_date']) {
                                    $subscriptions[$subKey]['expiration_date'] = $sub['expiration_date'];
                                }
                                $subscriptions[$subKey]['quantity'] += $sub['quantity'];
                                $subscriptions[$subKey]['customer_product_name'] = $subscriptions[$subKey]['customer_product_name'] ?? ($sub[$subKey]['customer_product_name'] ?? '');
                            }
                        }
                    }
                }
            }
        }

        return $this->removeZeroQuantityKeys($subscriptions);
    }

    /**
     * remove zero quntity Keys
     * @param array $subscriptions
     * @return array
     */
    protected function removeZeroQuantityKeys(array $subscriptions): array
    {
        // remove 0 quantity keys
        foreach ($subscriptions as $key => $value) {
            if ($value['quantity'] === 0) {
                unset($subscriptions[$key]);
            }
        }

        return $subscriptions;
    }

    /**
     * to get all subscriptions, including expired and not yet started
     *
     * return in array format
     * [
     *      'quantity' => ...,
     *      'expiration_date' => ...,
     * ];
     *
     * @return array
     */
    public function getExpiredSubscriptions(): array
    {
        if (!empty($this->expiredSubscriptions)) {
            return $this->expiredSubscriptions;
        }
        $subscriptions = [];
        if (empty($this->data)) {
            return [];
        }

        if (!empty($this->error)) {
            $GLOBALS['log']->fatal('there is an error in license server response: ' . $this->error);
            return [];
        }

        // will skip top level of the subscription
        // initiate quantity count
        foreach (self::SUPPORTED_KEYS as $type) {
            $subscriptions[$type]['quantity'] = 0;
        }

        // check addons, only interested in 'SELL', 'SELL' BUNDLES, 'SERVE' and Legacy product codes such as 'ENT', 'PRO' and 'ULT'.
        // ignore any other addons for now
        foreach ($this->addons as $addon) {
            $quantity = (int)$addon->quantity;
            $expirationDate = $addon->expiration_date;
            $startDate = $addon->start_date_c;

            if ($quantity === 0 || !isset($expirationDate) || $expirationDate >= time()) {
                continue;
            }

            $customerProductName = $addon->customer_product_name_c;
            // using product code to find out subscription types
            $productCode = $addon->product_code_c;
            if (!empty($productCode) && !empty(self::PRODUCT_CODE_MAPPING[strtoupper($productCode)])) {
                $key = self::PRODUCT_CODE_MAPPING[strtoupper($productCode)];
                $subscriptions[$key] = [
                    'quantity' => $subscriptions[$key]['quantity'] + $quantity,
                    'expiration_date' => $expirationDate,
                    'start_date' => $startDate,
                    'customer_product_name' => $customerProductName,
                ];
            }
        }

        // remove 0 quantity keys
        $this->expiredSubscriptions = array_filter($subscriptions, static function (array $item) {
            return $item['quantity'] !== 0;
        });

        return $this->expiredSubscriptions;
    }

    /**
     * get keys for subscriptions
     *
     * @param bool $getAll to get all subscription keys if it is true
     * need to take care of ENT, PRO, etc
     */
    public function getSubscriptionKeys(bool $getAll = true): array
    {
        $subscriptions = $this->getSubscriptions();
        if (empty($subscriptions)) {
            return [];
        }

        $keys = [];
        foreach ($subscriptions as $key => $value) {
            if (!in_array($key, $this->getAddonProducts())) {
                $keys[self::SUGAR_BASIC_KEY] = true;
            } else {
                $keys[$key] = true;
                if ($getAll && self::isBundleKey($key) && !empty($value[Addon::BUNDLED_PRODUCTS_KEY]) && is_array($value[Addon::BUNDLED_PRODUCTS_KEY])) {
                    foreach ($value[Addon::BUNDLED_PRODUCTS_KEY] as $bundledKey => $bundledItem) {
                        if (!empty($bundledItem)) {
                            $keys[$bundledKey] = true;
                        }
                    }
                }
            }
        }
        return $keys;
    }

    /**
     * get all subscription keys
     * @return array
     */
    public function getAllSubscriptionKeys(): array
    {
        return $this->getSubscriptionKeys(true);
    }

    /**
     * get top level subscription keys only
     * @return array
     */
    public function getTopLevelSubscriptionKeys(): array
    {
        return $this->getSubscriptionKeys(false);
    }

    /**
     * get subscription(s) for a key
     *
     * @param string $key key to search
     * need to take care of ENT, PRO, etc
     */
    public function getSubscriptionByKey(?string $key): array
    {
        $subscriptions = $this->getSubscriptions();
        if (empty($subscriptions)) {
            return [];
        }

        if (!empty($subscriptions[$key])) {
            return $subscriptions[$key];
        }

        foreach ($subscriptions as $subKey => $value) {
            if (self::isBundleKey($subKey) && !empty($value[Addon::BUNDLED_PRODUCTS_KEY]) && is_array($value[Addon::BUNDLED_PRODUCTS_KEY])) {
                foreach ($value[Addon::BUNDLED_PRODUCTS_KEY] as $bundledKey => $bundledItem) {
                    if ($bundledKey === $key && !empty($bundledItem)) {
                        return $bundledItem;
                    }
                }
            }
        }
        return [];
    }

    /**
     * get current supported addon products,
     * @return array
     */
    public function getAddonProducts(): array
    {
        return [
            Subscription::SUGAR_SELL_KEY,
            Subscription::SUGAR_SERVE_KEY,
            Subscription::SUGAR_HINT_KEY,
            Subscription::SUGAR_AUTOMATE_KEY,
            Subscription::SUGAR_PREDICT_ADVANCED_KEY,
            Subscription::SUGAR_PREDICT_PREMIER_KEY,
            Subscription::SUGAR_CONNECT_KEY,
            Subscription::SUGAR_DISCOVER_KEY,
            Subscription::SUGAR_MAPS_KEY,
            Subscription::SUGAR_ADVANCEDFORECAST_KEY,
            Subscription::SUGAR_SELL_ESSENTIALS_KEY,
            Subscription::SUGAR_SELL_BUNDLE_KEY,
            Subscription::SUGAR_SELL_ADVANCED_BUNDLE_KEY,
            Subscription::SUGAR_SELL_PREMIER_BUNDLE_KEY,
        ];
    }


    /**
     * check if a key is Mango key
     * @return array
     */
    public static function isMangoKey(?string $key): bool
    {
        return in_array($key, self::MANGO_KEYS);
    }

    /**
     * check is bundle key
     * @param string|null $key
     * @return bool
     */
    public static function isBundleKey(?string $key): bool
    {
        return in_array($key, self::BUNDLE_KEYS);
    }

    /**
     * check if a key is product key
     * @param string|null $key
     * @return bool
     */
    public static function isProductKey(?string $key): bool
    {
        if (empty($key)) {
            return false;
        }

        return in_array($key, self::SUPPORTED_KEYS);
    }

    /**
     * get all CRM keys for a bundle, for instance SUGAR_SELL_BUNDLE_KEY will return [SUGAR_SELL_KEY, SUGAR_SELL_BUNDLE_KEY]
     *
     * @param string $key
     * @return string[]
     */
    public static function getBundledKeys(string $key): array
    {
        switch ($key) {
            case (self::SUGAR_SELL_ESSENTIALS_KEY):
            case (self::SUGAR_SELL_BUNDLE_KEY):
            case (self::SUGAR_SELL_PREMIER_BUNDLE_KEY):
            case (self::SUGAR_SELL_ADVANCED_BUNDLE_KEY):
            case (self::SUGAR_SELL_PREMIER_KEY):
                return [self::SUGAR_SELL_KEY, $key];
            default:
                return [];
        }
    }

    /**
     * get the first SELL key from a license types array
     * @param array $licenseTypes license types array
     * @return string
     */
    public static function getSellKey(array $licenseTypes): string
    {
        foreach (Subscription::SELL_KEYS as $key) {
            if (in_array($key, $licenseTypes)) {
                return $key;
            }
        }
        return '';
    }

    /**
     * get default subscription in case of offline, client is not able to download from license server
     * @return array
     */
    protected function getDefaultSubscription(): array
    {

        $expiredDate = $this->getLicenseSettingByKey('license_expire_date', '+12 months');
        if (strtotime($expiredDate) - time() < 0) {
            if (!empty($GLOBALS['log'])) {
                $GLOBALS['log']->fatal('license was expired at ' . $expiredDate);
            }
            return [];
        }

        return [
            'quantity' => $this->getLicenseSettingByKey('license_users', 1),
            'expiration_date' => strtotime($expiredDate),
            'start_date' => null,
            'customer_product_name' => 'SugarCRM',
            Addon::BUNDLED_PRODUCTS_KEY => [],
        ];
    }

    /**
     * get license setting values, it will take the default value if it is during installation
     * @param string $key
     * @param $defaultValue
     */
    protected function getLicenseSettingByKey(string $key, $defaultValue)
    {
        if (isset($GLOBALS['installing']) && $GLOBALS['installing'] === true) {
            return $defaultValue;
        }

        if (!isset($GLOBALS['license'])) {
            loadLicense(true);
        }

        if (!empty($GLOBALS['license']->settings[$key])) {
            return $GLOBALS['license']->settings[$key];
        }
        return $defaultValue;
    }

    /**
     * order license type
     * @param array|null $licTypes user's license type
     * @return array
     */
    public static function getOrderedLicenseTypes(?array $licTypes): array
    {
        if (empty($licTypes)) {
            return [];
        }

        $retLicenseTypes = [];
        // order by Mango keys first, tehn non-crm keys
        foreach ([Subscription::MANGO_KEYS, self::VISIBLE_NONCRM_PRODUCTS] as $keys) {
            foreach ($keys as $key) {
                if (in_array($key, $licTypes)) {
                    $retLicenseTypes[] = $key;
                }
            }
        }
        return $retLicenseTypes;
    }
}
//END REQUIRED CODE DO NOT MODIFY
