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

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Sugarcrm\Sugarcrm\DependencyInjection\Container;
use Sugarcrm\Sugarcrm\Entitlements\SubscriptionManager;

class Administration extends SugarBean
{
    public $settings;
    public $table_name = 'config';
    public $object_name = 'Administration';
    public $new_schema = true;
    public $module_dir = 'Administration';
    public $config_categories = [
        // 'mail', // cn: moved to include/OutboundEmail
        'disclosure', // appended to all outbound emails
        'notify',
        'system',
        'portal',
        'proxy',
        'massemailer',
        'ldap',
        'captcha',
        'honeypot',
        'sugarpdf',
        'base',
        'license',
        'csp',
    ];
    public $disable_custom_fields = true;
    public $checkbox_fields = [
        'notify_send_by_default',
        'mail_smtpauth_req',
        'notify_on',
        'tweettocase_on',
        'skypeout_on',
        'system_mailmerge_on',
        'proxy_auth',
        'proxy_on',
        'system_ldap_enabled',
        'captcha_on',
        'honeypot_on',
    ];
    public $disable_row_level_security = true;
    public static $passwordPlaceholder = '::PASSWORD::';

    /**
     * List of default values needed for Pendo analytics
     *
     * @var array
     */
    protected $analyticsDefaults = [
        'si_id' => 'unknown_si_id',
        'si_name' => 'unknown_si_name',
        'si_type' => 'unknown_si_type',
        'si_license_current' => false,
        'si_license_serve' => false,
        'si_license_sell' => false,
        'si_tier' => 'unknown_si_tier',
        'si_customer_since' => 'unknown_si_customer_since',
        'si_sic_code' => 'unknown_si_sic_code',
        'si_employees_no' => 'unknown_si_employees_no',
        'si_managing_team' => 'unknown_si_managing_team',
        'si_partner_name' => 'unknown_si_partner_name',
        'si_partner_type' => 'unknown_si_partner_type',
        'si_account_record' => 'unknown_si_account_record',
        'si_customer_region' => 'unknown_si_customer_region',
        'si_billing_country' => 'unknown_si_billing_country',
        'si_billing_state' => 'unknown_si_billing_state',
        'si_billing_city' => 'unknown_si_billing_city',
        'si_postal_code' => 'unknown_si_postal_code',
        'si_cloud_instance' => 'unknown_si_cloud_instance',
        'si_usage_designation' => 'unknown_si_usage_designation',
        'si_no_of_licenses' => 'unknown_si_no_of_licenses',
        'si_cloud_region' => 'unknown_si_cloud_region',
        'si_upgrade_frequency' => 'unknown_si_upgrade_frequency',
        'si_db_size' => 'unknown_si_db_size',
        'si_file_system_size' => 'unknown_si_file_system_size',
        'si_sum_size' => 'unknown_si_sum_size',
        'si_rli_enabled' => 'unknown_rli_enabled',
        'si_forecasts_is_setup' => 'unknown_forcasts_is_setup',
        'si_product_list' => 'unknown_product_list',
        'portal_active' => 'unknown_portal_activated',
    ];

    /**
     * Additional properties to add to the analytic account data
     *
     * @var array
     */
    protected $addedAnalyticAccountData = [
        'activity_streams_enabled' => 'activity_streams_enabled',
        'editable_preview_enabled' => 'preview_edit',
        'list_view_items_per_page' => 'list_max_entries_per_page',
        'subpanel_items_per_page' => 'list_max_entries_per_subpanel',
        'lead_conversion_options' => 'lead_conv_activity_opt',
        'system_default_currency_code' => 'default_currency_iso4217',
        'system_default_language' => 'default_language',
    ];

    /**
     * List of mapped data needed from SI for Pendo analytics
     *
     * @var array
     */
    protected $analyticsFieldMap = [
        'account_id' => [
            'name' => 'si_id',
            'default' => 'unknown_si_id',
            'label' => 'Account ID',
        ],
        'account_name' => [
            'name' => 'si_name',
            'default' => 'unknown_account_name',
            'label' => 'Account Name',
        ],
        'account_type' => [
            'name' => 'si_type',
            'default' => 'unknown_si_type',
            'label' => 'Account Type',
        ],
        'license_current' => [
            'name' => 'si_license_current',
            'default' => false,
            'label' => 'License Current',
        ],
        'license_serve' => [
            'name' => 'si_license_serve',
            'default' => false,
            'label' => 'License Serve',
        ],
        'license_sell' => [
            'name' => 'si_license_sell',
            'default' => false,
            'label' => 'License Sell',
        ],
        'account_tier' => [
            'name' => 'si_tier',
            'default' => 'unknown_si_tier',
            'label' => 'Account Tier',
        ],
        'customer_since' => [
            'name' => 'si_customer_since',
            'default' => 'unknown_si_customer_since',
            'label' => 'Customer Since',
        ],
        'sic_code' => [
            'name' => 'si_sic_code',
            'default' => 'unknown_si_sic_code',
            'label' => 'SIC Code',
        ],
        'employees_no' => [
            'name' => 'si_employees_no',
            'default' => 'unknown_si_employees_no',
            'label' => 'Number of Employees',
        ],
        'account_managing_team' => [
            'name' => 'si_managing_team',
            'default' => 'unknown_si_managing_team',
            'label' => 'Managing Team',
        ],
        'account_partner_name' => [
            'name' => 'si_partner_name',
            'default' => 'unknown_si_partner_name',
            'label' => 'Partner Name',
        ],
        'partner_type_c' => [
            'name' => 'si_partner_type',
            'default' => 'unknown_si_partner_type',
            'label' => 'Partner Type',
        ],
        'account_record' => [
            'name' => 'si_account_record',
            'default' => 'unknown_si_account_record',
            'label' => 'Account Record Link',
        ],
        'customer_region' => [
            'name' => 'si_customer_region',
            'default' => 'unknown_si_customer_region',
            'label' => 'Customer Region',
        ],
        'billing_country' => [
            'name' => 'si_billing_country',
            'default' => 'unknown_si_billing_country',
            'label' => 'Billing Country',
        ],
        'billing_state' => [
            'name' => 'si_billing_state',
            'default' => 'unknown_si_billing_state',
            'label' => 'Billing State',
        ],
        'billing_city' => [
            'name' => 'si_billing_city',
            'default' => 'unknown_si_billing_state',
            'label' => 'Billing City',
        ],
        'postal_code' => [
            'name' => 'si_postal_code',
            'default' => 'unknown_si_postal_code',
            'label' => 'Postal Code',
        ],
        'cloud_instance' => [
            'name' => 'si_cloud_instance',
            'default' => 'unknown_si_cloud_instance',
            'label' => 'Cloud Instance',
        ],
        'usage_designation' => [
            'name' => 'si_usage_designation',
            'default' => 'unknown_si_usage_designation',
            'label' => 'Usage Designation',
        ],
        'no_of_licenses' => [
            'name' => 'si_no_of_licenses',
            'default' => 'unknown_si_no_of_licenses',
            'label' => 'Postal Code',
        ],
        'cloud_region' => [
            'name' => 'si_cloud_region',
            'default' => 'unknown_si_cloud_region',
            'label' => 'Cloud Region',
        ],
        'upgrade_frequency' => [
            'name' => 'si_upgrade_frequency',
            'default' => 'unknown_si_upgrade_frequency',
            'label' => 'Upgrade Frequency',
        ],
        'db_size' => [
            'name' => 'si_db_size',
            'default' => 'unknown_si_db_size',
            'label' => 'Database Size',
        ],
        'file_system_size' => [
            'name' => 'si_upgrade_frequency',
            'default' => 'unknown_si_file_system_size',
            'label' => 'File System Size',
        ],
        'total_sum_size' => [
            'name' => 'si_sum_size',
            'default' => 'unknown_si_sum_size',
            'label' => 'Total Size',
        ],
    ];

    public function __construct()
    {
        parent::__construct();

        $this->setupCustomFields('Administration');
        $this->disable_row_level_security = true;
    }

    public function retrieveSettings($category = false, $clean = false)
    {
        // declare a cache for all settings
        $settings_cache = sugar_cache_retrieve('admin_settings_cache');

        if ($clean) {
            $settings_cache = [];
        }

        // Check for a cache hit
        if (!empty($settings_cache)) {
            $this->settings = $settings_cache;
            if (!empty($this->settings[$category])) {
                return $this;
            }
        }

        $conn = $this->db->getConnection();
        $builder = $conn->createQueryBuilder();
        $query = $builder
            ->select('category', 'name', 'value', 'platform')
            ->from($this->table_name);
        if ($category) {
            $query->where('category = ' . $builder->createPositionalParameter($category));
        }

        $stmt = $query->execute();
        while ($row = $stmt->fetchAssociative()) {
            $key = $row['category'] . '_' . $row['name'];
            // There can be settings that have the same `category`, the same
            // `name` but a different platform. We are going to prevent the
            // settings from non `base` platforms (ie `mobile` or `portal`) from
            // overriding `base` settings.

            // TODO: deprecate this method for a method that can select settings
            // per platform
            if (empty($row['platform'])) {
                $row['platform'] = 'base';
            }
            if (isset($this->settings[$key]) && $row['platform'] !== 'base') {
                // Don't hold this setting because it's already set
                continue;
            }
            if ($key == 'ldap_admin_password' || $key == 'proxy_password') {
                $this->settings[$key] = $this->decrypt_after_retrieve($row['value']);
            } else {
                $this->settings[$key] = $this->decodeConfigVar($row['value']);
            }
        }
        $this->settings[$category] = true;

        // We only need this if we are requesting all categories or the mail category
        if ($category === false || $category === 'mail') {
            // outbound email settings
            $oe = new OutboundEmail();

            if ($oe->getSystemMailerSettings(false)) {
                foreach ($oe->field_defs as $name => $value) {
                    // Only set the value if the key starts with "mail_" or is oauth2 field.
                    if ($name === 'eapm_id' || $name === 'authorized_account' || strpos($name, 'mail_') === 0) {
                        $this->settings[$name] = $oe->$name;
                    }
                }
            }
        }

        // At this point, we have built a new array that should be cached.
        sugar_cache_put('admin_settings_cache', $this->settings);
        return $this;
    }

    public function saveConfig()
    {
        // outbound email settings
        $oe = new OutboundEmail();
        $oe = $oe->getSystemMailerSettings();
        $proxyVisible = Container::getInstance()->get(SugarConfig::class)->get('proxy_visible', true);

        foreach ($_POST as $key => $val) {
            $prefix = $this->get_config_prefix($key);
            if (safeInArray($prefix[0], $this->config_categories)) {
                if ($prefix[0] === 'proxy' && !$proxyVisible) {
                    continue;
                }
                if (($key === 'proxy_username' || $key === 'proxy_password')
                    && (empty($_POST['proxy_on']) || empty($_POST['proxy_auth']))) {
                    $val = '';
                }
                if ($prefix[0] === 'proxy' && $prefix[1] === 'on') {
                    if (empty($_POST['proxy_host']) || empty($_POST['proxy_port'])) {
                        $val = '';
                    }
                }
                if (is_array($val)) {
                    $val = implode(',', $val);
                }
                $this->saveSetting($prefix[0], $prefix[1], $val);
            }

            // Only store the value if the key starts with "mail_".
            if (strpos($key, 'mail_') === 0 && array_key_exists($key, $oe->field_defs)) {
                $oe->$key = $val;
            }

            // Keep the name and email address of the system account in sync with the configs notify_fromname and
            // notify_fromaddress.
            if ($key === 'notify_fromname') {
                $oe->name = $val;
            } elseif ($key === 'notify_fromaddress') {
                $sea = new SugarEmailAddress();
                $oe->email_address_id = $sea->getEmailGUID($val);
                $oe->email_address = $val;
            } elseif ($key === 'eapm_id' || $key === 'authorized_account') {
                $oe->$key = $val;
            }
        }

        //saving outbound email from here is probably redundant, adding a check to make sure
        //smtpserver name is set.
        if (!empty($oe->mail_smtpserver)) {
            $oe->saveSystem();
        }

        $this->retrieveSettings(false, true);
    }

    /**
     * Save a setting
     *
     * @param string $category Category for the config value
     * @param string $key Key for the config value
     * @param string|array $value Value of the config param
     * @param string $platform Which platform this belongs to (API use only, If platform is empty it will not be returned in the API calls)
     * @return int Number of records Returned
     */
    public function saveSetting($category, $key, $value, $platform = '')
    {
        // platform is always lower case
        $platform = strtolower((string)$platform);
        $conn = $this->db->getConnection();
        $builder = $conn->createQueryBuilder();
        $query = $builder
            ->select('NULL')
            ->from($this->table_name)
            ->where($this->getConfigWhere($builder, $category, $key, $platform))
            ->setMaxResults(1);
        $result = $query->execute()->fetchOne();
        if (is_array($value)) {
            $value = json_encode($value);
        }
        if ($category . '_' . $key == 'ldap_admin_password' || $category . '_' . $key == 'proxy_password') {
            $value = $this->encrpyt_before_save($value);
        }
        $builder = $conn->createQueryBuilder();
        if ($result === false) {
            $query = $builder
                ->insert($this->table_name)
                ->values([
                    'category' => $builder->createPositionalParameter($category),
                    'name' => $builder->createPositionalParameter($key),
                    'platform' => $builder->createPositionalParameter($platform),
                    'value' => $builder->createPositionalParameter($value),
                ]);
        } else {
            $query = $builder
                ->update($this->table_name)
                ->set('value', $builder->createPositionalParameter($value))
                ->where($this->getConfigWhere($builder, $category, $key, $platform));
        }
        $result = $query->execute();
        sugar_cache_clear('admin_settings_cache');
        // check to see if category is a module
        if (!empty($platform)) {
            // we have an api call so lets clear out the cache for the module + platform
            global $moduleList;
            // FIXME TY-839 'portal' should be the platform, not category
            if (safeInArray($category, $moduleList) || $category == 'portal') {
                $cache_key = 'ModuleConfig-' . $category;
                if ($platform !== 'base') {
                    $cache_key .= $platform;
                } else {
                    // Since the base config for a module has changed, and the
                    // module config for other platforms extends from the base
                    // module config, we should clear the cache value for all
                    // platforms. That way, they will properly refetch the next
                    // time getConfigForModule is called for them
                    $availablePlatforms = MetaDataManager::getPlatformList();
                    foreach ($availablePlatforms as $availablePlatform) {
                        sugar_cache_clear($cache_key . $availablePlatform);
                    }
                }
                sugar_cache_clear($cache_key);
            }
        }

        // need to update subscription info
        if ($category === 'license' && $key === 'key') {
            // This section of code is a portion of the code referred
            // to as Critical Control Software under the End User
            // License Agreement.  Neither the Company nor the Users
            // may modify any portion of the Critical Control Software.
            SubscriptionManager::instance()->downloadSubscriptionContent($value);
            //END REQUIRED CODE DO NOT MODIFY
        }
        return $result;
    }

    /**
     * Builds WHERE for the given configuration parameter
     *
     * @param QueryBuilder $builder Query builder
     * @param string $category Config parameter category
     * @param string $name Config parameter name
     * @param string $platform Platform name
     * @return CompositeExpression
     */
    protected function getConfigWhere(QueryBuilder $builder, $category, $name, $platform)
    {
        $where = $builder->expr()->and(
            $builder->expr()->eq('category', $builder->createPositionalParameter($category)),
            $builder->expr()->eq('name', $builder->createPositionalParameter($name))
        );

        $wherePlatform = $builder->expr()->eq('platform', $builder->createPositionalParameter($platform));
        if (empty($platform)) {
            $wherePlatform = $builder->expr()->or(
                $wherePlatform,
                $builder->expr()->isNull('platform')
            );
        }

        return $where->with($wherePlatform);
    }

    /**
     * Return the config for a specific module.
     *
     * @param string $module The module we are wanting to get the config for
     * @param string $platform The platform we want to get the data back for
     * @param boolean $clean Get clean copy of module config
     * @return array
     */
    public function getConfigForModule($module, $platform = 'base', $clean = false)
    {
        // platform is always lower case
        $platform = strtolower($platform);

        $cache_key = 'ModuleConfig-' . $module;
        if ($platform != 'base') {
            $cache_key .= $platform;
        }

        if ($clean) {
            sugar_cache_clear($cache_key);
        } else {
            // try and see if there is a cache for this
            $moduleConfig = sugar_cache_retrieve($cache_key);

            if (!empty($moduleConfig)) {
                return $moduleConfig;
            }
        }

        $sql = 'SELECT name, value FROM config WHERE category = ?';
        if ($platform != 'base') {
            // if the platform is not base, we need to order it so the platform we are looking for overrides any base values
            $sql .= " AND platform IN ('base', ?) ORDER BY CASE WHEN platform = 'base' THEN 0 ELSE 1 END";
        } else {
            $sql .= ' AND platform = ?';
        }

        $conn = $this->db->getConnection();
        $result = $conn->executeQuery($sql, [$module, $platform]);

        $moduleConfig = [];
        while ($row = $result->fetchAssociative()) {
            $moduleConfig[$row['name']] = $this->decodeConfigVar($row['value']);
        }

        if (!empty($moduleConfig)) {
            sugar_cache_put($cache_key, $moduleConfig);
        }

        return $moduleConfig;
    }

    public function get_config_prefix($str)
    {
        return [substr($str, 0, strpos($str, '_')), substr($str, strpos($str, '_') + 1)];
    }

    /**
     * Get the full config table as an associative array
     *
     * @return array
     */
    public function getAllSettings()
    {
        $conn = $this->db->getConnection();
        $builder = $conn->createQueryBuilder();
        $query = $builder
            ->select('category', 'name', 'value', 'platform')
            ->from($this->table_name);
        $stmt = $query->execute();

        $return = [];
        while ($row = $stmt->fetchAssociative()) {
            $row['value'] = $this->decodeConfigVar($row['value']);
            $return[] = $row;
        }

        return $return;
    }

    /**
     * @param string $var
     * @return string
     * decode the config var
     */
    protected function decodeConfigVar($var)
    {
        // make sure the value is not null and the length is greater than 0
        if (!is_null($var) && strlen($var) > 0) {
            // if it looks like a JSON string then lets run the json_decode on it
            if ($var[0] == '{' || $var[0] == '[') {
                $decoded = json_decode($var, true);
                // if we didn't get a json error, then put the decoded value as the value we want to return
                if (json_last_error() == JSON_ERROR_NONE) {
                    $var = $decoded;
                }
            } elseif (is_numeric($var) && ctype_digit($var)) {
                // if it's a numeric value and all the string only contains digits, the convert it to an integer
                $var = intval($var);
            }
        }
        return $var;
    }

    /**
     * Return Administration object with filled in settings
     * @param string|false $category
     * @param bool $clean
     * @return Administration
     */
    public static function getSettings($category = false, $clean = false)
    {
        $admin = BeanFactory::newBean('Administration');
        $admin->retrieveSettings($category, $clean);
        return $admin;
    }

    /**
     * Check if the Bean Implements anything special
     * @param $interface
     * @return bool
     */
    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }

    /**
     * Adds defaults to the server array if certain properties are missing
     * @param array $server The server info from metadata
     * @return array
     */
    public function getUpdatedAnalyticData(array $server = [])
    {
        $config = Container::getInstance()->get(SugarConfig::class);
        $return = [];

        // Add in additional account items before handling defaults
        foreach ($this->addedAnalyticAccountData as $k => $v) {
            $return[$k] = $config->get($v) ?? 'unknown_' . $k;
        }

        // Now handle defaults
        foreach ($this->analyticsDefaults as $k => $v) {
            $return[$k] = $server[$k] ?? $v;
        }

        return $return;
    }

    /**
     * Updates server information from metadata with additional analytics server
     * data
     * @param array $data Original payload of server information
     * @param array $settings Configuration for the instance
     * @return array
     */
    public function getUpdatedAnalyticServerInfo(array $data = [], array $settings = [])
    {

        // Needed for Pendo analytics
        if (!empty($settings['site_id'])) {
            $data['site_id'] = $settings['site_id'];
        }

        if (isset($settings['Opportunities_opps_view_by'])) {
            $data['si_rli_enabled'] = $settings['Opportunities_opps_view_by'] === 'RevenueLineItems';
        } else {
            LoggerManager::getLogger()->error('Unable to get Opportunities_opps_view_by from system config.');
            $data['si_rli_enabled'] = 'unknown_rli_enabled';
        }

        if (isset($settings['Forecasts_is_setup'])) {
            $data['si_forecasts_is_setup'] = $settings['Forecasts_is_setup'] === 1;
        } else {
            LoggerManager::getLogger()->error('Unable to get Forecasts_is_setup from system config.');
            $data['si_forecasts_is_setup'] = 'unknown_forcasts_is_setup';
        }

        if (!empty($settings['license_subscription']['subscription'])) {
            $s = $settings['license_subscription']['subscription'];

            // Add in products from license server
            if (!empty($s['addons']) && is_array($s['addons'])) {
                $products = [];
                foreach ($s['addons'] as $addon) {
                    if (!empty($addon['product_name'])) {
                        $products[] = $addon['product_name'];
                    }
                }

                if (!empty($products)) {
                    $data['si_product_list'] = implode(', ', $products);
                } else {
                    LoggerManager::getLogger()->error('Unable to get product list from license server.');
                    $data['si_product_list'] = 'unknown_product_list';
                }
            }

            // Now add in other default mapped values
            foreach ($this->analyticsFieldMap as $k => $v) {
                if (empty($s[$k])) {
                    $data[$v['name']] = $v['default'];
                    LoggerManager::getLogger()->error(
                        sprintf(
                            'Unable to get %s from license server.',
                            $v['label']
                        )
                    );
                } else {
                    $data[$v['name']] = $s[$k];
                }
            }
        }

        return $data;
    }
}
