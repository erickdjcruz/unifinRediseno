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

/**
 * Configurator class around `$sugar_config`
 */
class Configurator
{
    /**
     * @var array
     */
    public $config;

    public $override = '';
    public $errors = ['main' => ''];
    public $logger = null;
    public $previous_sugar_override_config_array = [];
    public $useAuthenticationClass = false;

    public const COMPANY_LOGO_UPLOAD_PATH = 'upload://tmp_logo_company_upload/logo.png';
    public const COMPANY_LOGO_UPLOAD_PATH_DARK = 'upload://tmp_logo_company_upload/logo_dark.png';
    /**
     * List of allowed undefined `$sugar_config` keys to be set
     * @var array
     */
    protected $allowUndefined = [
        'stack_trace_errors',
        'export_delimiter',
        'use_real_names',
        'developerMode',
        'default_module_favicon',
        'authenticationClass',
        'SAML_loginurl',
        'SAML_idp_entityId',
        'SAML_issuer',
        'SAML_X509Cert',
        'SAML_SLO',
        'SAML_SAME_WINDOW',
        'SAML_provisionUser',
        'SAML_request_signing_method',
        'SAML_request_signing_cert_name',
        'SAML_request_signing_pkey',
        'SAML_request_signing_pkey_name',
        'SAML_request_signing_x509',
        'SAML_sign_authn',
        'SAML_sign_logout_request',
        'SAML_sign_logout_response',
        'dashlet_auto_refresh_min',
        'show_download_tab',
        'enable_action_menu',
        'offlineEnabled',
        'noPrivateTeamUpdate',
    ];

    /**
     * List of keys allowed to be accepted through POST. If this list is empty
     * then no filtering will happen when populating this object from POST.
     * @var array
     */
    protected $allowKeys = [];

    /**
     * List of POST keys to be ignored when populating from POST
     * @var array
     */
    protected $ignoreKeys = [
        'action',
        'module',
        'save',
    ];

    /**
     * Ctor
     */
    public function __construct()
    {
        $this->logger = LoggerManager::getLogger();
        $this->loadConfig();
    }

    /**
     * Load `$sugar_config`
     */
    public function loadConfig()
    {
        global $sugar_config;
        $this->config = $sugar_config;
    }

    /**
     * Setter for allowKeys
     * @param array $allowKeys
     */
    public function setAllowKeys(array $allowKeys)
    {
        $this->allowKeys = $allowKeys;
    }

    /**
     * Populate $sugar_config from POST. If no list of allowed keys is passed
     * in then no filtering happens and basically exposes every available
     * $sugar_config settings to be changed if already present or if allowed
     * as an undefined key.
     */
    public function populateFromPost()
    {

        $sugarConfig = SugarConfig::getInstance();
        $filterAllowKeys = empty($this->allowKeys) ? false : true;

        foreach ($_POST as $key => $value) {
            // Skip ignored keys silently
            if (in_array($key, $this->ignoreKeys)) {
                continue;
            }

            // If filtering is requested do so
            if ($filterAllowKeys && !in_array($key, $this->allowKeys)) {
                $GLOBALS['log']->debug("Skip unallowed key '$key' from POST");
                continue;
            }

            // Validate logger file name
            if ($key === 'logger_file_name' && strcmp(trim($value), '') == 0) {
                $GLOBALS['log']->error('Invalid log file name: Log file name should not blank.');
                continue;
            }

            // Validate logger file max size
            if ($key === 'logger_file_maxSize' && strcmp(trim($value), '') == 0) {
                $GLOBALS['log']->error('Invalid log file max size: Log file max size should not be blank.');
                continue;
            }

            // Validate logger file max logs
            if ($key === 'logger_file_maxLogs' && $value <= 0) {
                $GLOBALS['log']->error('Invalid maximum number of logs: should be 1 or greater.');
                continue;
            }

            // We can set the value directly if key exists or if allowed as undefined
            if (isset($this->config[$key]) || in_array($key, $this->allowUndefined)) {
                // compensate booleans as strings
                if (strcmp("$value", 'true') === 0) {
                    $value = true;
                }
                if (strcmp("$value", 'false') === 0) {
                    $value = false;
                }

                // set config key
                $this->config[$key] = $value;
            } elseif ($key === 'document_merge_service_urls_default') {
                $serviceUrl = $this->config['document_merge']['service_urls'][$value];
                $this->config['document_merge']['service_urls']['default'] = $serviceUrl;
            } else {
                // Check if key exists by looking up value for multidimensional
                // keys. This is pretty sketchy as some newer keys can have
                // underscores.
                if ($sugarConfig->get(str_replace('_', '.', $key)) !== null) {
                    setDeepArrayValue($this->config, $key, $value);
                } else {
                    $GLOBALS['log']->debug("Skipping unknown config key '$key' from POST");
                }
            }
        }
    }

    public function handleOverride()
    {
        global $sugar_config, $sugar_version;
        $sc = SugarConfig::getInstance();
        [$oldConfig, $overrideArray] = $this->readOverride();
        $this->previous_sugar_override_config_array = $overrideArray;
        $diffArray = deepArrayDiff($this->config, $sugar_config);
        $overrideArray = sugarArrayMergeRecursive($overrideArray, $diffArray);

        if (isset($overrideArray['authenticationClass']) && empty($overrideArray['authenticationClass'])) {
            unset($overrideArray['authenticationClass']);
        }

        $overideString = "<?php\n/***CONFIGURATOR***/\n";

        $GLOBALS['sugar_config'] = $this->config;

        //print_r($overrideArray);
        //Bug#53013: Clean the tpl cache if action menu style has been changed.
        if (isset($overrideArray['enable_action_menu']) &&
            (!isset($this->previous_sugar_override_config_array['enable_action_menu']) ||
                $overrideArray['enable_action_menu'] != $this->previous_sugar_override_config_array['enable_action_menu'])
        ) {
            $repair = new RepairAndClear();
            $repair->module_list = [];
            $repair->clearTpls();
        }

        foreach ($overrideArray as $key => $val) {
            if (in_array($key, $this->allowUndefined) || isset($sugar_config[$key])) {
                if (is_string($val) && strcmp($val, 'true') == 0) {
                    $val = true;
                    $this->config[$key] = $val;
                }
                if (is_string($val) && strcmp($val, 'false') == 0) {
                    $val = false;
                    $this->config[$key] = false;
                }
            }
            $overideString .= override_value_to_string_recursive2('sugar_config', $key, $val, true, $oldConfig);
        }
        $overideString .= '/***CONFIGURATOR***/';

        $this->saveOverride($overideString);
        if (isset($this->config['logger']['level']) && $this->logger) {
            $this->logger->setLevel($this->config['logger']['level']);
        }
    }

    //bug #27947 , if previous $sugar_config['stack_trace_errors'] is true and now we disable it , we should clear all the cache.
    public function clearCache()
    {
        global $sugar_config, $sugar_version;

        $sections = [
            MetaDataManager::MM_CONFIG,
            MetaDataManager::MM_SERVERINFO,
        ];

        [$oldConfig, $currentConfigArray] = $this->readOverride();
        foreach ($currentConfigArray as $key => $val) {
            if (in_array($key, $this->allowUndefined) || isset($sugar_config[$key])) {
                if (empty($val)) {
                    if (!empty($this->previous_sugar_override_config_array['stack_trace_errors']) && $key == 'stack_trace_errors') {
                        TemplateHandler::clearAll();
                        return;
                    }
                }
            }
        }

        //Module metadata needs to be refreshed if Activity Stream system setting is changed
        if ((
            isset($currentConfigArray['activity_streams_enabled'])
                && (!isset($oldConfig['activity_streams_enabled']) ||
                    $currentConfigArray['activity_streams_enabled'] != $oldConfig['activity_streams_enabled'])
        ) ||
            (isset($oldConfig['activity_streams_enabled']) && !isset($currentConfigArray['activity_streams_enabled']))
        ) {
            $sections[] = MetaDataManager::MM_MODULES;
        }

        if ($sugar_config['activity_streams_enabled']) {
            Activity::enable();
        } else {
            Activity::disable();
        }

        $this->updateMetadataCache($sections);
    }

    /**
     * Refreshes the metadata cache for the specified sections.
     *
     * @param array $sections The sections that need to be refreshed.
     */
    protected function updateMetadataCache($sections)
    {
        MetaDataManager::refreshSectionCache($sections);
    }

    public function saveConfig()
    {
        $this->saveImages();
        $this->populateFromPost();
        $this->handleOverride();
        $this->clearCache();
    }

    /**
     * Read config & config override, and return old config and their difference
     * @return array[old config, difference in configs]
     */
    protected function readOverride()
    {
        $sugar_config = [];
        if (is_readable('config.php')) {
            include 'config.php';
        }
        $old_config = $sugar_config;
        if (file_exists('config_override.php')) {
            if (!is_readable('config_override.php')) {
                $GLOBALS['log']->fatal('Unable to read the config_override.php file. Check the file permissions');
            } else {
                include 'config_override.php';
            }
        }
        return [$old_config, deepArrayDiff($sugar_config, $old_config)];
    }

    public function saveOverride($override)
    {
        require_once 'install/install_utils.php';

        if (sugar_file_put_contents_atomic('config_override.php', $override) === false) {
            $GLOBALS['log']->fatal('Unable to write to the config_override.php file. Check the php_errors for detail');
        }
    }

    public function overrideClearDuplicates($array_name, $key)
    {
        if (!empty($this->override)) {
            $pattern = '/.*CONFIGURATOR[^\$]*\$' . $array_name . '\[\'' . $key . '\'\][\ ]*=[\ ]*[^;]*;\n/';
            $this->override = preg_replace($pattern, '', $this->override);
        } else {
            $this->override = "<?php\n\n?>";
        }
    }

    public function replaceOverride($array_name, $key, $value)
    {
        $GLOBALS[$array_name][$key] = $value;
        $this->overrideClearDuplicates($array_name, $key);
        $new_entry = '/***CONFIGURATOR***/' . override_value_to_string($array_name, $key, $value);
        $this->override = str_replace('?>', "$new_entry\n?>", $this->override);
    }

    private function saveImages()
    {
        $saveLightModeLogo = !empty($_POST['commit_company_logo']);
        $saveDarkModeLogo = !empty($_POST['commit_company_logo_dark']);
        if ($saveLightModeLogo || $saveDarkModeLogo) {
            $this->commitCompanyLogo($saveLightModeLogo, $saveDarkModeLogo);
        }
    }

    public function checkTempImage($path)
    {
        if (!verify_uploaded_image($path)) {
            $GLOBALS['log']->fatal("A user ({$GLOBALS['current_user']->id}) attempted to use an invalid file for the logo - {$path}");
            sugar_die('Invalid File Type');
        }
        return $path;
    }

    /**
     * Commits images uploaded by the user as the company logo for default theme
     *
     * @param boolean $saveLightModeLogo If we need to update the light mode logo
     * @param boolean $saveDarkModeLogo If we need to update the dark mode logo
     */
    private function commitCompanyLogo($saveLightModeLogo, $saveDarkModeLogo): void
    {
        $path = self::COMPANY_LOGO_UPLOAD_PATH;
        $pathDark = self::COMPANY_LOGO_UPLOAD_PATH_DARK;
        if (($saveLightModeLogo && !file_exists($path)) || ($saveDarkModeLogo && !file_exists($pathDark))) {
            return;
        }

        $cacheUpdate = [];
        $imgPath = SugarThemeRegistry::current()->getDefaultImagePath();

        if ($saveLightModeLogo) {
            $logo = create_custom_directory($imgPath . '/company_logo.png');
            copy($path, $logo);
            sugar_cache_clear('company_logo_attributes');
            $cacheUpdate[] = MetaDataManager::MM_LOGOURL;
            SugarThemeRegistry::current()->clearImageCache('company_logo.png');
        }

        if ($saveDarkModeLogo) {
            $logoDark = create_custom_directory($imgPath . '/company_logo_dark.png');
            copy($pathDark, $logoDark);
            sugar_cache_clear('company_logo_attributes_dark');
            $cacheUpdate[] = MetaDataManager::MM_LOGOURLDARK;
            SugarThemeRegistry::current()->clearImageCache('company_logo_dark.png');
        }

        SugarThemeRegistry::clearAllCaches();
        $this->updateMetadataCache($cacheUpdate);
    }


    /**
     * Add error message
     * @param string errstr Error message
     */
    public function addError($errstr)
    {
        $this->errors['main'] .= $errstr . '<br>';
    }
}
