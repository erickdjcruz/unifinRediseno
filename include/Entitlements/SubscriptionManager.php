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
use Administration;
use Sugarcrm\Sugarcrm\Util\Arrays\ArrayFunctions\ArrayFunctions;

/**
 * Class SubscriptionManager
 *
 * Sugar subscription manager:
 * It can talk to license server to download subscription data and save to DB.config table
 * It will not talk to license server unless license is modified
 *
 */
class SubscriptionManager
{
    protected $subscriptionRestApiEndPoint = 'rest/subscription/';

    /**
     * internal subscription data
     * @var subscription
     */
    protected $subscription;

    /**
     * subscription or license id
     * @string
     */
    protected $licenseKey;

    /**
     * system subscription keys
     * @var array
     */
    protected $systemSubscriptionKeys = [];
    protected $systemSubscriptionKeysAllLevels = [];

    /**
     * old system subscription keys
     * @var array
     */
    protected $oldSystemSubscriptionKeys = [];

    /**
     * array of license types which exceed the limit
     * @var array
     */
    protected $exceededLimitTypes = [];

    /**
     * flag to check limits
     */
    protected $hasCheckedLimit = false;

    /**
     * instance
     * @var subscriptionmanager
     */
    protected static $instance;

    /**
     * flag to ignore do metadata diff
     * @var bool
     */
    protected $ignoreMedatdataDiff = false;

    /**
     * fix license in process
     * @var bool
     */
    protected $fixLicenseTypeInProcess = false;

    /**
     * timeout for the request
     */
    public const REQUEST_TIMEOUT = 10;

    /**
     * no data from license server, using default setting
     */
    public const USE_DEFAULT_SETTING = 'use_default';

    /**
     * no public ctor
     * subscriptionmanager constructor.
     */
    private function __construct()
    {
    }

    /**
     * singleton implementation
     * @return subscriptionmanager
     */
    public static function instance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * get instance of http client for license server
     * @return \sugarlicensing
     */
    protected function getSugarLicensingClient()
    {
        return new \SugarLicensing();
    }

    /**
     * get subscription, either go to db or license server to get subscription content
     *
     * @param null|string $licenseKey
     * @return null|Subscription
     */
    protected function getSubscription(?string $licenseKey): ?Subscription
    {
        if (empty($licenseKey)) {
            return null;
        }

        if (!empty($this->subscription) && $this->licenseKey === $licenseKey) {
            return $this->subscription;
        }

        $this->subscription = null;
        $content = $this->getSubscriptionContent($licenseKey, true);
        $this->subscription = new Subscription($content);
        $this->licenseKey = $licenseKey;

        return $this->subscription;
    }

    /**
     * get content of subscription, if $useDb is false, it will ignore database and retrieve directly from license server
     *
     * @param string $licenseKey license key
     * @param bool $useDb if false, it will ignore local DB and retrieve data directly from license server
     * @return bool|string
     */
    protected function getSubscriptionContent(string $licenseKey, bool $useDb)
    {
        $admin = \BeanFactory::newBean('Administration');
        $admin->retrieveSettings('license');
        $data = null;
        if (isset($admin->settings['license_subscription'])) {
            $data = $admin->settings['license_subscription'];
            if (is_array($data)) {
                $data = json_encode($admin->settings['license_subscription']);
                if (!empty($data)) {
                    $this->setOldSubscriptionKeys($data);
                }
            }
        }

        if ($useDb && !empty($data)) {
            if ($data === self::USE_DEFAULT_SETTING) {
                return false;
            }
            return $data;
        }

        if ($data === self::USE_DEFAULT_SETTING) {
            $data = false;
        }
        // go to license server to retrieve data
        $endpoint = $this->subscriptionRestApiEndPoint . $licenseKey;
        $subscriptionClient = $this->getSugarLicensingClient();
        $GLOBALS['log']->info('download new sunscription data');
        $response = $subscriptionClient->request($endpoint, [], false, self::REQUEST_TIMEOUT);

        // try to parse and valid the content
        $this->subscription = new Subscription($response);
        $subscriptionClient = null;

        if (empty($this->subscription)) {
            // something is wrong
            return '';
        }

        if ($response !== false) {
            // save to config table
            $admin->saveSetting('license', 'subscription', $response);
        } else {
            $admin->saveSetting('license', 'subscription', self::USE_DEFAULT_SETTING);
        }
        $admin->saveSetting('license', 'subscription_downloaded', '');

        // refresh metadata cache if not in installation stage and subscription has changed
        if ((!(isset($GLOBALS['installing'])) || $GLOBALS['installing'] != true)
            && !$this->ignoreMedatdataDiff
            && !($data === false && $response === false)) {
            $data = empty($data) ? '' : $data;
            $needUpdate = !empty($this->getInvalidUsersLicenseTypes());
            if ($this->isSubscriptionChanged($data) || $needUpdate) {
                if (!empty($data) || $needUpdate) {
                    // don't need to do it at first time since it was taken cared by performanceSetup
                    $this->updateUsersLicenseTypesAfterSubscriptionChanges($needUpdate);
                }
                $this->refreshMetadataCache();
            }
        }
        return $response;
    }

    /**
     * update license tpye after license entitlement changes, we only check the top level changes
     * @param bool $forceToUpdate force to update license types
     * @throws SugarApiExceptionNotFound
     * @throws SugarQueryException
     */
    public function updateUsersLicenseTypesAfterSubscriptionChanges(bool $forceToUpdate = false)
    {
        global $current_user;
        if (empty($current_user) || !is_admin($current_user)) {
            $GLOBALS['log']->fatal('must be admin to do license type update!');
            return;
        }

        $current_user->updateUsersLicenseTypesAfterSubscriptionChanges([], [], $forceToUpdate);
    }

    /**
     * get Mango only keys
     * @param array|null keys
     * @return array
     */
    public function getMangoKeys(?array $keys): array
    {
        if (empty($keys)) {
            return [];
        }

        $retKeys = [];
        foreach ($keys as $key => $value) {
            if (Subscription::isMangoKey($key)) {
                $retKeys[$key] = $value;
            }
        }
        return $retKeys;
    }

    /**
     * API to check subscription by key
     * @param string $key
     * @param bool $includeImplied to include inplied, such as $key='SELL' for a bundle
     * @return bool
     */
    public function hasSubscription(string $key, bool $includeImplied = true): bool
    {
        $subscriptions = $this->getAllSystemSubscriptionKeys();
        if (!empty($subscriptions[$key])) {
            return true;
        }

        if ($includeImplied) {
            $parentKeys = $this->getSubscriptionKeysContains($key);
            foreach ($parentKeys as $parentKey) {
                if (!empty($subscriptions[$parentKey])) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * check if the entitlement contains hint
     * @return bool
     */
    public function hasHintInTopLevel(): bool
    {
        return $this->getTopLevelSystemSubscriptionKeys()[Subscription::SUGAR_HINT_KEY] ?? false;
    }

    /**
     * check if there is any entitlement changes, it'll ignore any expiration date and quantity changes
     * @param string $oldSubscrptionData old subscription data
     * @param bool $checkMangoOnly only check changes for mango license types
     * @return bool
     */
    protected function isSubscriptionChanged(string $oldSubscrptionData, bool $checkMangoOnly = false): bool
    {
        if (empty($oldSubscrptionData)) {
            $this->oldSystemSubscriptionKeys = [];
            return true;
        }
        $this->setOldSubscriptionKeys($oldSubscrptionData);
        $this->ignoreMedatdataDiff = true;
        $currentKeys = $this->getTopLevelSystemSubscriptionKeys();
        $this->ignoreMedatdataDiff = false;

        $newAddedKeys = array_diff_key($currentKeys, $this->getOldSystemSubscriptionKeys());
        $removedKeys = array_diff_key($this->getOldSystemSubscriptionKeys(), $currentKeys);
        if ($checkMangoOnly) {
            $newAddedKeys = $this->getMangoKeys($newAddedKeys);
            $removedKeys = $this->getMangoKeys($removedKeys);
        }
        return !empty($newAddedKeys) || !empty($removedKeys);
    }

    /**
     * set old subscription keys
     *
     * @param string $oldSubscrptionData
     */
    protected function setOldSubscriptionKeys(string $oldSubscrptionData): void
    {
        if (empty($oldSubscrptionData)) {
            $this->oldSystemSubscriptionKeys = [];
            return;
        }
        $oldSubscrptionData = new Subscription($oldSubscrptionData);
        $this->oldSystemSubscriptionKeys = $oldSubscrptionData->getTopLevelSubscriptionKeys();
    }

    /**
     * get Old system subscription keys
     * @return array
     */
    public function getOldSystemSubscriptionKeys(): array
    {
        return !empty($this->oldSystemSubscriptionKeys) ? $this->oldSystemSubscriptionKeys : $this->systemSubscriptionKeys;
    }

    /**
     * refresh metadata cache
     *
     */
    protected function refreshMetadataCache()
    {
        \MetaDataManager::refreshCache();
    }

    /**
     * get total number of Mango users, doesn't include hint
     * @return int
     */
    public function getTotalNumberOfMangoUsers(): int
    {
        $total = 0;
        foreach ($this->getSystemSubscriptions() as $key => $subscripion) {
            if (Subscription::isMangoKey($key)) {
                $total += $subscripion['quantity'];
            }
        }
        return $total;
    }

    /**
     * check if this is single license type entitmenet
     * @return bool
     */
    public function isSingleMangoTypeEntitlement(): bool
    {
        $total = 0;
        foreach ($this->getSystemSubscriptions() as $key => $subscripion) {
            if (Subscription::isMangoKey($key)) {
                $total += 1;
            }
        }
        if ($total <= 1) {
            return true;
        }
        return false;
    }

    /**
     * get license key
     * @return string/null
     */
    public function getLicenseKey()
    {
        if (!empty($this->licenseKey)) {
            return $this->licenseKey;
        }

        $admin = \Administration::getSettings('license');
        if (isset($admin->settings['license_key'])) {
            return $admin->settings['license_key'];
        }
        return null;
    }

    /**
     * set a new license key, it will trigger to access license server to download new subscription content and save to db
     * @param null|string $licenseKey
     */
    public function downloadSubscriptionContent(?string $licenseKey)
    {
        if (empty($licenseKey)) {
            $this->licenseKey = null;
            return;
        }

        // reset internal data
        $this->subscription = null;
        $this->systemSubscriptionKeys = [];
        $this->licenseKey = $licenseKey;

        // need to go to license server to get subscription data
        $this->getSubscriptionContent($licenseKey, false);
    }

    /**
     * get list of subscriptions
     * @return array
     */
    public function getSystemSubscriptions(): array
    {
        $licenseKey = $this->getLicenseKey();
        if (empty($licenseKey)) {
            return [];
        }
        $subscription = $this->getSubscription($licenseKey);
        return !empty($subscription) ? $subscription->getSubscriptions() : [];
    }

    /**
     * get subscription keys
     * @param bool $getAll only retrive top level if true
     * @return array
     */
    protected function getSystemSubscriptionKeys(bool $getAll): array
    {
        if (!$getAll && !empty($this->systemSubscriptionKeys)) {
            return $this->systemSubscriptionKeys;
        }

        if ($getAll && !empty($this->systemSubscriptionKeysAllLevels)) {
            return $this->systemSubscriptionKeysAllLevels;
        }

        $licenseKey = $this->getLicenseKey();
        if (empty($licenseKey)) {
            return [];
        }
        $subscription = $this->getSubscription($licenseKey);
        if (empty($subscription)) {
            return [];
        }

        if (!$getAll) {
            // top level only
            $subscriptionKeys = $subscription->getTopLevelSubscriptionKeys();
            $this->systemSubscriptionKeys = $subscriptionKeys;
        } else {
            $subscriptionKeys = $subscription->getAllSubscriptionKeys();
            $this->systemSubscriptionKeysAllLevels = $subscriptionKeys;
        }
        return $subscriptionKeys;
    }

    /**
     * get all system subscription keys
     * @return array
     */
    public function getAllSystemSubscriptionKeys(): array
    {
        return $this->getSystemSubscriptionKeys(true);
    }

    /**
     * get top level system subscription keys only
     * @return array
     */
    public function getTopLevelSystemSubscriptionKeys(): array
    {
        return $this->getSystemSubscriptionKeys(false);
    }

    /**
     * get subscription by key
     * @param string $key the key to search
     * @return array
     */
    public function getSystemSubscriptionByKey(string $key): array
    {
        $licenseKey = $this->getLicenseKey();
        if (empty($licenseKey)) {
            return [];
        }
        $subscription = $this->getSubscription($licenseKey);
        if (empty($subscription)) {
            return [];
        }
        return $subscription->getSubscriptionByKey($key);
    }

    /**
     * get bundled subscriptions by key, not including itself
     * @param string $key
     * @return array
     */
    public function getBundledSubscriptionsByKey(string $key): array
    {
        if (!Subscription::isBundleKey($key)) {
            return [];
        }

        $bundlesSubscription = $this->getSystemSubscriptionByKey($key);
        return $bundlesSubscription[Addon::BUNDLED_PRODUCTS_KEY] ?? [];
    }

    /**
     * get customer product name for a product key, such as SUGAR_SELL, SUGAR_SERVE etc
     * @param string $key
     * @return string
     */
    public function getCustomerProductNameByKey(string $key): string
    {
        $subscription = $this->getSystemSubscriptionByKey($key);
        return $subscription['customer_product_name'] ?? '';
    }

    /**
     * get subscription keys in value-sorted array
     * @return array
     */
    public function getSystemSubscriptionKeysInSortedValueArray(): array
    {
        // only need top level CRM product keys
        $results = array_keys($this->getTopLevelSystemSubscriptionKeys());
        $this->sortSubscriptionKeys($results);
        return $results;
    }

    /**
     * get valid subscription seats by type
     * @param string $type
     * @return int
     */
    public function getSystemSubscriptionSeatsByType(string $type): int
    {
        $systemSubscriptions = $this->getSystemSubscriptions();
        if (isset($systemSubscriptions[$type])) {
            return $systemSubscriptions[$type]['quantity'] ?? 0;
        }
        return 0;
    }

    /**
     * get all subscription seats by types
     * @return array
     */
    public function getSystemSubscriptionSeats(): array
    {
        $systemSubscriptions = $this->getSystemSubscriptions();
        $results = [];
        foreach ($systemSubscriptions as $key => $value) {
            $results[$key] = $value['quantity'];
        }

        return $results;
    }

    /**
     * get mango keys, ignore non-crm keys, such as HINT, CONNECT etc.
     * @return array
     */
    public function getSystemCRMKeys(): array
    {
        return $this->getMangoKeys($this->getSystemSubscriptions());
    }

    /**
     * get user's subscriptions, it compares system subscriptions with user's license type
     * @param null|\User $user
     * @param bool $getAll , only fetch top level, no bundled level product returned if $shallow is true
     * @return array
     */
    protected function getUserSubscriptions(?\User $user, bool $getAll): array
    {
        if (empty($user)) {
            return [];
        }
        // get all top level system subscriptions
        $systemSubscriptionKeys = $this->getTopLevelSystemSubscriptionKeys();

        if (empty($systemSubscriptionKeys)) {
            return [];
        }

        $userSubscriptions = [];
        $userLicenseTypes = $user->getTopLevelLicenseTypes();
        // one prod subscription, license type = current or empty will be using current product
        if (safeCount($systemSubscriptionKeys) === 1) {
            if (empty($userLicenseTypes)) {
                // never assigned before
                $userSubscriptions = array_keys($systemSubscriptionKeys);
            } else {
                // check if user has current license type
                foreach ($userLicenseTypes as $type) {
                    if (Subscription::SUGAR_BASIC_KEY === $type) {
                        $userSubscriptions = array_keys($systemSubscriptionKeys);
                    }
                }
            }

            if ($getAll) {
                // get all sub levels
                if (isset($userSubscriptions[0]) && Subscription::isBundleKey($userSubscriptions[0])) {
                    $subs = $this->getSystemSubscriptionByKey($userSubscriptions[0]);
                    $foundSubs = array_keys($subs[Addon::BUNDLED_PRODUCTS_KEY] ?? []);
                    if (!empty($foundSubs)) {
                        $userSubscriptions = array_unique(array_merge($userSubscriptions, $foundSubs) ?? []);
                    }
                }
            }
            if (!empty($userSubscriptions)) {
                return $userSubscriptions;
            }
        }

        // pick up a license type
        if (empty($userLicenseTypes)) {
            // never assigned before, pick up one based on the order in getAllSupportedProducts()
            $userLicenseTypes = [$this->getUserDefaultLicenseType()];
        }

        // loop through the license keys
        $isSellPremier = safeInArray(Subscription::SUGAR_SELL_PREMIER_KEY, $userLicenseTypes);
        foreach ($userLicenseTypes as $type) {
            if (isset($systemSubscriptionKeys[$type])) {
                $userSubscriptions[] = $type;
                if ($getAll && Subscription::isBundleKey($type)) {
                    $subs = $this->getSystemSubscriptionByKey($type);
                    $foundSubs = array_keys($subs[Addon::BUNDLED_PRODUCTS_KEY] ?? []);
                    if (!empty($foundSubs)) {
                        $userSubscriptions = array_unique(array_merge($userSubscriptions, $foundSubs));
                    }
                }
            } elseif ($type === Subscription::SUGAR_SELL_PREMIER_KEY && isset($systemSubscriptionKeys[Subscription::SUGAR_SELL_PREMIER_BUNDLE_KEY])) {
                $userSubscriptions[] = $type;
            } elseif ($isSellPremier && isset($systemSubscriptionKeys[Subscription::SUGAR_SELL_PREMIER_BUNDLE_KEY])) {
                $subs = $this->getSystemSubscriptionByKey(Subscription::SUGAR_SELL_PREMIER_BUNDLE_KEY);
                $foundSubs = array_keys($subs[Addon::BUNDLED_PRODUCTS_KEY] ?? []);
                if (safeInArray($type, $foundSubs)) {
                    $userSubscriptions[] = $type;
                }
            }
        }

        // assign admin user to default license type, otherwise, an ENT user will get blank license types
        if (empty($userSubscriptions) && is_admin($user)) {
            return [$this->getUserDefaultLicenseType()];
        }

        $this->sortSubscriptionKeys($userSubscriptions);
        return $userSubscriptions;
    }

    /**
     * get a user's all valid subscriptions
     * @param \User|null $user
     * @return array|string[]
     */
    public function getAllUserSubscriptions(?\User $user): array
    {
        return $this->getUserSubscriptions($user, true);
    }

    /**
     * get user's top level subscription
     * @param \User|null $user
     * @return array|string[]
     */
    public function getTopLevelUserSubscriptions(?\User $user): array
    {
        return $this->getUserSubscriptions($user, false);
    }

    /**
     * get user's invalid subscriptions, it compares system subscriptions with user's license type.
     * Top level only, since we only store the top level license types in DB
     *
     * @param null|\User $user
     * @return array
     */
    public function getUserInvalidSubscriptions(?\User $user): array
    {
        if (empty($user) || empty($user->id)) {
            return [];
        }
        // get top level system subscriptions
        $systemSubscriptionKeys = $this->getTopLevelSystemSubscriptionKeys();

        $userLicenseTypes = $user->getTopLevelLicenseTypes();
        if (empty($systemSubscriptionKeys)) {
            return $userLicenseTypes;
        }

        if (empty($userLicenseTypes)) {
            return [];
        }

        $invalidTypes = [];
        foreach ($userLicenseTypes as $type) {
            if (!isset($systemSubscriptionKeys[$type])) {
                $invalidTypes[] = $type;
            }
        }
        return $invalidTypes;
    }

    /**
     * Get user's license types which either exceed limit or  is invalid
     * @param null|\User $user
     * @return array
     */
    public function getUserExceededAndInvalidLicenseTypes(?\User $user): array
    {
        if (empty($user)) {
            return [];
        }

        $license_seats_needed = 0;
        $exceededLicenseTypes = $this->getSystemLicenseTypesExceededLimit($license_seats_needed);

        $userLicenseTypesOverLimit = [];
        // check current user's license types against $exceededLicenseTypes
        $userLicenseTypes = $this->getTopLevelUserSubscriptions($user);
        if (!empty($userLicenseTypes)) {
            foreach ($userLicenseTypes as $type) {
                if (isset($exceededLicenseTypes[$type])) {
                    $userLicenseTypesOverLimit[] = $type;
                }
            }
        }

        // merge with invalid types
        $invalidLicenseTypes = $this->getUserInvalidSubscriptions($user);
        foreach ($invalidLicenseTypes as $type) {
            $userLicenseTypesOverLimit[] = $type;
        }
        return $userLicenseTypesOverLimit;
    }

    /**
     * all supported types, keep the order
     * @return array
     */
    public function getAllSupportedProducts()
    {
        // The order of this array determines the default license type if user's license_type is empty
        return [
            Subscription::SUGAR_BASIC_KEY,
            Subscription::SUGAR_SERVE_KEY,
            Subscription::SUGAR_SELL_KEY,
            Subscription::SUGAR_SELL_ESSENTIALS_KEY,
            Subscription::SUGAR_SELL_BUNDLE_KEY,
            Subscription::SUGAR_SELL_ADVANCED_BUNDLE_KEY,
            Subscription::SUGAR_SELL_PREMIER_BUNDLE_KEY,
            Subscription::SUGAR_SELL_PREMIER_KEY,
            Subscription::SUGAR_HINT_KEY,
            Subscription::SUGAR_AUTOMATE_KEY,
            Subscription::SUGAR_DISCOVER_KEY,
            Subscription::SUGAR_CONNECT_KEY,
            Subscription::SUGAR_PREDICT_ADVANCED_KEY,
            Subscription::SUGAR_PREDICT_PREMIER_KEY,
            Subscription::SUGAR_MAPS_KEY,
        ];
    }

    /**
     * get list of product keys contains $key feature
     * @param string $key
     * @return array|string[]
     */
    public function getSubscriptionKeysContains(string $key): array
    {
        switch ($key) {
            case (Subscription::SUGAR_SELL_KEY):
                return [
                    Subscription::SUGAR_SELL_KEY,
                    Subscription::SUGAR_SELL_ESSENTIALS_KEY,
                    Subscription::SUGAR_SELL_PREMIER_BUNDLE_KEY,
                    Subscription::SUGAR_SELL_PREMIER_KEY,
                    Subscription::SUGAR_SELL_ADVANCED_BUNDLE_KEY,
                    Subscription::SUGAR_SELL_BUNDLE_KEY,
                ];
            case (Subscription::SUGAR_ADVANCEDFORECAST_KEY):
                return [
                    Subscription::SUGAR_SELL_PREMIER_KEY,
                ];
            default:
                return [$key];
        }
    }

    /**
     * get default license type, must be a Mango product type, Hint can't be used as default key
     *
     * @return string
     */
    public function getUserDefaultLicenseType(): string
    {
        // Warning to Dev: if modifying logic here, you must notify MTS team!
        // MTS team needs to do corresponding changes on their user reports
        $systemSubscriptionKeys = $this->getTopLevelSystemSubscriptionKeys();
        $allProducts = $this->getAllSupportedProducts();
        foreach ($allProducts as $type) {
            if (isset($systemSubscriptionKeys[$type]) && Subscription::isMangoKey($type)) {
                // The first valid key in AllSupportedProducts array will be the default license type
                return $type;
            }
        }
        return '';
    }

    /**
     * sort keys
     * @param array $keys
     */
    protected function sortSubscriptionKeys(array &$keys)
    {
        sort($keys);
    }

    /**
     *
     * get all subsets of system subscriptions
     *
     * @return array
     */
    public function getAllSubsetsOfSystemSubscriptions(): array
    {
        $systemSubscriptions = $this->getSystemSubscriptionKeysInSortedValueArray();
        $allSubsets = ArrayFunctions::powerSet($systemSubscriptions);

        $subsets = [];
        foreach ($allSubsets as $subset) {
            if (!empty($subset)) {
                $this->sortSubscriptionKeys($subset);
                $subsets[] = $subset;
            }
        }
        return $subsets;
    }

    /**
     * convert keys to a string
     * @param array|null $keys
     * @return mixed|string
     */
    public function getUserLicenseTypesInString(?\User $user)
    {
        if (empty($user)) {
            return '';
        }

        $userSubscriptions = $this->getTopLevelUserSubscriptions($user);

        if (empty($userSubscriptions)) {
            return '';
        }

        return implode('_', $userSubscriptions);
    }


    /**
     * get number of users exceed limit by license type
     * @param int $license_seats_needed total number of license needed
     * @param bool $ignoreCache ignore previous check, redo DB access
     * @return array
     */
    public function getSystemLicenseTypesExceededLimit(int &$license_seats_needed, bool $ignoreCache = false): array
    {
        if ($this->hasCheckedLimit && !$ignoreCache) {
            return $this->exceededLimitTypes;
        }

        $this->hasCheckedLimit = true;

        $sysSubscriptions = $this->getSystemSubscriptions();
        $exceededLimitTypes = [];

        // no subscription
        if (empty($sysSubscriptions)) {
            $exceededLimitTypes[Subscription::SUGAR_BASIC_KEY] = 1;
            $license_seats_needed = 1;
            $this->exceededLimitTypes = $exceededLimitTypes;
            return $exceededLimitTypes;
        }

        $userCountByType = $this->getSystemUserCountByLicenseTypes();
        $sellPremierCount = $userCountByType[Subscription::SUGAR_SELL_PREMIER_KEY] ?? 0;
        foreach ($userCountByType as $licenseType => $count) {
            if ($count > 0) {
                if (isset($sysSubscriptions[$licenseType])) {
                    if ($licenseType === Subscription::SUGAR_SELL_PREMIER_BUNDLE_KEY) {
                        $userCountByType[$licenseType] = $userCountByType[$licenseType] ?? 0 + $sellPremierCount;
                    }
                    if ($userCountByType[$licenseType] > $sysSubscriptions[$licenseType]['quantity']) {
                        $exceededLimitTypes[$licenseType] = $count - $sysSubscriptions[$licenseType]['quantity'];
                        $license_seats_needed += $exceededLimitTypes[$licenseType];
                    }
                } else {
                    // license expired or switched
                    $exceededLimitTypes[$licenseType] = $count;
                    $license_seats_needed += $count;
                }
            }
        }

        $this->exceededLimitTypes = $exceededLimitTypes;
        return $exceededLimitTypes;
    }

    /**
     * Check system types for free seats for user and return array of exceeded types
     * It should be used for user management only.
     * @param \User $user
     * @return array
     */
    public function getUserExceededLicenseTypes(\User $user): array
    {
        // ignore support users
        if (\User::isSupportUser($user)) {
            return [];
        }
        $usedSeats = $this->getSystemUserCountByLicenseTypes();
        $allowedSeats = $this->getSystemSubscriptions();
        $userTypes = $this->getTopLevelUserSubscriptions($user);

        if (empty($allowedSeats)) {
            return $userTypes;
        }

        return array_filter($userTypes, function ($type) use ($allowedSeats, $usedSeats) {
            if (empty($allowedSeats[$type]) || $allowedSeats[$type]['quantity'] - $usedSeats[$type] <= 0) {
                return true;
            }
            return false;
        });
    }

    /**
     * Get system active users by license types
     * @return array
     */
    public function getSystemUserCountByLicenseTypes(): array
    {
        global $db;
        $query = 'SELECT license_type from users WHERE ' . \User::getLicensedUsersWhere();
        $result = $db->query($query, true, 'Error filling in user array: ');
        $supportedTypes = $this->getAllSupportedProducts();
        $userCountByType = [];
        foreach ($supportedTypes as $type) {
            $userCountByType[$type] = 0;
        }
        $userCountByType[Subscription::UNKNOWN_TYPE] = 0;

        $foundUnknownType = false;
        $unknownTypes = '';
        while (($row = $db->fetchByAssoc($result, false)) != null) {
            if (empty($row['license_type'])) {
                $type = $this->getUserDefaultLicenseType();
                if (empty($type) || !in_array($type, $supportedTypes)) {
                    if (!empty($type)) {
                        $unknownTypes .= $type . ' ';
                    }
                    $foundUnknownType = true;
                } else {
                    $userCountByType[$type] += 1;
                }
            } else {
                $types = json_decode($row['license_type'], true);
                if (!is_array($types)) {
                    $GLOBALS['log']->fatal('invalid license_type format: ' . $row['license_type']);
                } else {
                    foreach ($types as $type) {
                        if (empty($type)) {
                            $type = $this->getUserDefaultLicenseType();
                        }
                        if (!in_array($type, $supportedTypes)) {
                            $foundUnknownType = true;
                            $unknownTypes .= $type . ' ';
                            $userCountByType[Subscription::UNKNOWN_TYPE] += 1;
                        } elseif (safeInArray(Subscription::SUGAR_SELL_PREMIER_KEY, $types)) {
                            if (!safeInArray($type, Subscription::SELL_PREMIER_INCLUDED_PRODUCTS)) {
                                // exclude premier bundled product
                                if ($type === Subscription::SUGAR_SELL_PREMIER_KEY) {
                                    $type = Subscription::SUGAR_SELL_PREMIER_BUNDLE_KEY;
                                }
                                $userCountByType[$type] += 1;
                            }
                        } else {
                            $userCountByType[$type] += 1;
                        }
                    }
                }
            }
        }

        if ($foundUnknownType) {
            // don't know what to do, skip for now
            $GLOBALS['log']->fatal('Found unknown type: ' . $unknownTypes);
        }

        return $userCountByType;
    }

    /**
     * get invalid license types for all users
     * @return array
     */
    public function getInvalidUsersLicenseTypes(): array
    {
        $invalidLicenseTypes = [];
        $userLicenseTypes = $this->getSystemUserCountByLicenseTypes();
        $topLevelTypes = $this->getTopLevelSystemSubscriptionKeys();
        foreach ($userLicenseTypes as $type => $count) {
            if ($count > 0 && !isset($topLevelTypes[$type])) {
                // invalid key for current subscription
                $invalidLicenseTypes[] = $type;
            }
        }
        return $invalidLicenseTypes;
    }

    /**
     * From a list of subscription keys, gets all implied keys as well
     * Ex:
     * [Subscription::SUGAR_SELL_BUNDLE_KEY] -> [Subscription::SUGAR_SELL_KEY, Subscription::SUGAR_SELL_BUNDLE_KEY]
     *
     * @param array $keys
     * @return array
     */
    public function getAllImpliedSubscriptions(array $keys): array
    {
        $subscriptions = [];
        foreach ($keys as $key) {
            if (Subscription::isBundleKey($key)) {
                foreach (Subscription::getBundledKeys($key) as $bundledKey) {
                    // get all sub keys for this bundle, implied subscriptions
                    $subscriptions[] = $bundledKey;
                }
            } else {
                $subscriptions[] = $key;
            }
        }

        return array_values(array_unique($subscriptions));
    }

    /**
     * A part of license polling mechanism. This makes subscription updates to appear quickly on app instances without
     * need of manual revalidation.
     * 1. Check update of subscription details on license server and save them locally. This action should
     *      be performed periodically with a bit of time randomization to avoid load spikes on license server
     * @param bool $forceToUpdate force to update license types
     * @return bool whether a change exited and was applied successfully
     * @see \Sugarcrm\Sugarcrm\Entitlements\SubscriptionPrefetcher
     * 2. (this method) Install updated subscription data, also update License Type for users if needed. This action
     *      requires admin privileges, e.g. running from cron job
     *
     */
    public function applyDownloadedLicense(bool $forceToUpdate = false): bool
    {
        /** @var Administration $admin */
        $admin = \BeanFactory::newBean('Administration');
        $admin->retrieveSettings('license');

        $downloadedSubscription = $admin->settings['license_subscription_downloaded'] ?? '';
        if (empty($downloadedSubscription) && !$forceToUpdate) {
            return false;
        }
        if (!empty($downloadedSubscription)) {
            if (is_array($downloadedSubscription)) {
                $downloadedSubscription = json_encode($downloadedSubscription);
            }
            $this->subscription = new Subscription($downloadedSubscription);
            $this->systemSubscriptionKeys = [];
            $this->systemSubscriptionKeysAllLevels = [];
            $this->oldSystemSubscriptionKeys = [];

            $admin->saveSetting('license', 'subscription', $downloadedSubscription);
            $admin->saveSetting('license', 'subscription_downloaded', '');
        }

        $oldSubscriptionData = $admin->settings['license_subscription'] ?? '';
        if (!is_string($oldSubscriptionData)) {
            $oldSubscriptionData = json_encode($oldSubscriptionData);
        }

        $needUpdate = $forceToUpdate || !empty($this->getInvalidUsersLicenseTypes());
        if (!($GLOBALS['installing'] ?? false) && ($this->isSubscriptionChanged($oldSubscriptionData) || $needUpdate)) {
            $this->updateUsersLicenseTypesAfterSubscriptionChanges($needUpdate);
            $this->refreshMetadataCache();
        }
        return true;
    }

    /**
     * get Fix License Process State
     * @return bool
     */
    public function getFixLicenseProcessState(): bool
    {
        return $this->fixLicenseTypeInProcess;
    }

    /**
     * set Fix License Process State
     * @param bool $state
     */
    public function setFixLicenseProcessState(bool $state): void
    {
        $this->fixLicenseTypeInProcess = true;
    }
}
//END REQUIRED CODE DO NOT MODIFY
