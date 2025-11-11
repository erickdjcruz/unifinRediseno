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

namespace Sugarcrm\Sugarcrm\IdentityProvider\Authentication;

use OneLogin\Saml2\Constants;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use Sugarcrm\IdentityProvider\Srn;
use Sugarcrm\IdentityProvider\STS\EndpointInterface;
use Sugarcrm\IdentityProvider\STS\EndpointService;

use function GuzzleHttp\default_user_agent;

/**
 * Configuration glue for IdM
 */
class Config
{
    public const LDAP_ENCRYPTION_NONE = 'none';
    public const LDAP_ENCRYPTION_SSL = 'ssl';
    public const LDAP_ENCRYPTION_TLS = 'tls';
    public const IDM_MODE_KEY = 'idm_mode';

    /**
     * list of key names with values in array format
     * @var array
     */
    protected $idmArrayTypeAttributes = [
        'http_client',
        'cloudConsoleRoutes',
        'caching',
        'requestedOAuthScopes',

        // @deprecated: allowedSAs will be removed in the future versions.
        'allowedSAs',
    ];

    /**
     * @var \SugarConfig
     */
    protected $sugarConfig;

    /**
     * @var \Administration
     */
    protected $ldapSettings;

    /**
     * @var \Configurator
     */
    protected $configurator;

    /**
     * idm settings
     * @var \Administration
     */
    protected $idmSettings;

    /**
     * idm settings
     * @var Srn\Manager
     */
    protected $srnManager;

    /**
     * catalog_urls for different regions
     * @var array
     */
    protected $catalogURLs = [
        'us-west-2' => 'https://appcatalog-us-west-2.service.sugarcrm.com',
        'eu-west-1' => 'https://appcatalog-eu-west-1.service.sugarcrm.com',
        'ap-southeast-2' => 'https://appcatalog-ap-southeast-2.service.sugarcrm.com',
        'eu-central-1' => 'https://appcatalog-eu-central-1.service.sugarcrm.com',
        'ca-central-1' => 'https://appcatalog-ca-central-1.service.sugarcrm.com',
        'ap-southeast-1' => 'https://appcatalog-ap-southeast-1.service.sugarcrm.com',
        'eu-west-2' => 'https://appcatalog-eu-west-2.service.sugarcrm.com',
    ];

    /**
     * @param \SugarConfig $sugarConfig
     */
    public function __construct(\SugarConfig $sugarConfig)
    {
        $this->sugarConfig = $sugarConfig;
    }

    /**
     * get settings either from SugarConfig or from Database,
     * idm_mode.* config are stored in Database's config table.
     *
     * @param string $key full qualified name, such as idm_mode.enabled
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (strncmp($key, self::IDM_MODE_KEY, strlen(self::IDM_MODE_KEY)) !== 0) {
            return $this->sugarConfig->get($key, $default);
        } elseif ($key === self::IDM_MODE_KEY) {
            // get all idm config from db
            $result = [];
            foreach ($this->getIdmSettings()->settings as $idmKey => $value) {
                if (strncmp($idmKey, self::IDM_MODE_KEY, strlen(self::IDM_MODE_KEY)) === 0) {
                    $idmKey = preg_replace('/' . self::IDM_MODE_KEY . '_' . '/', '', $idmKey, 1);
                    if (!empty($idmKey)) {
                        $result[$idmKey] = $value;
                    }
                }
            }
            return $result ?? $default;
        } else {
            // construct key for db config setting
            [$idmMode, $idmKey] = explode('.', $key, 2);
            $dbConfigKey = $idmMode . '_' . $idmKey;
            if (isset($this->getIdmSettings()->settings[$dbConfigKey])) {
                return $this->getIdmSettings()->settings[$dbConfigKey];
            }
            return $default;
        }
    }

    /**
     * to get idm_mode's attribute value for a given key
     * @param string $key key name for an attribute
     * @param mixed $default
     */
    protected function getIdmSettingsByKey(string $key, $default = null)
    {
        return $this->get(self::IDM_MODE_KEY . '.' . $key, $default);
    }

    /**
     * check if value is array for a given key
     * @param string $key
     * @return bool
     */
    protected function isArrayTypeValue(string $key): bool
    {
        return in_array($key, $this->idmArrayTypeAttributes);
    }

    /**
     * init idm settings
     */
    public function getIdmSettings()
    {
        if (!$this->idmSettings) {
            $this->idmSettings = \Administration::getSettings(self::IDM_MODE_KEY);
        }
        return $this->idmSettings;
    }

    /**
     * refresh idm settings
     */
    protected function refreshIdmSettings()
    {
        $this->idmSettings = \Administration::getSettings(self::IDM_MODE_KEY);
    }

    /**
     * Builds proper configuration suitable for SAMLAuthenticationProvider
     *
     * @return array
     */
    public function getSAMLConfig()
    {
        $defaultConfig = $this->getSAMLDefaultConfig();
        return array_replace_recursive($defaultConfig, $this->get('SAML', [])); //update with values from config
    }

    /**
     * Gets IDM mode configuration
     * @return array
     */
    public function getIDMModeConfig()
    {
        if (!$this->isIDMModeEnabled()) {
            return [];
        }

        $stsUrl = rtrim($this->getIdmSettingsByKey('stsUrl', '/ '));
        $idpUrl = rtrim($this->getIdmSettingsByKey('idpUrl', '/ '));
        $stsKeySetId = $this->getIdmSettingsByKey('stsKeySetId');
        $urlKeys = $stsKeySetId ? $stsUrl . '/keys/' . $stsKeySetId : null;

        $endpointService = new EndpointService(['host' => $stsUrl]);

        $tid = $this->getIdmSettingsByKey('tid', '');
        $idmModeConfig = [
            'tid' => $tid,
            'clientId' => $this->getIdmSettingsByKey('clientId'),
            'clientSecret' => $this->getIdmSettingsByKey('clientSecret'),
            'stsUrl' => $stsUrl,
            'idpUrl' => $idpUrl,
            'redirectUri' => rtrim($this->get('site_url'), '/') . '/?module=Users&action=OAuth2CodeExchange',
            'urlAuthorize' => $endpointService->getOAuth2Endpoint(EndpointInterface::AUTH_ENDPOINT),
            'urlAccessToken' => $endpointService->getOAuth2Endpoint(EndpointInterface::TOKEN_ENDPOINT),
            'urlResourceOwnerDetails' => $endpointService->getOAuth2Endpoint(EndpointInterface::INTROSPECT_ENDPOINT),
            'urlUserInfo' => $endpointService->getUserInfoEndpoint(),
            'urlRevokeToken' => $endpointService->getOAuth2Endpoint(EndpointInterface::REVOCATION_ENDPOINT),
            'http_client' => $this->getIdmSettingsByKey('http_client', []),
            'cloudConsoleUrl' => $this->getIdmSettingsByKey('cloudConsoleUrl', ''),
            'cloudConsoleRoutes' => $this->getIdmSettingsByKey('cloudConsoleRoutes', []),
            'profileUrls' => $this->getIdmSettingsByKey('profileUrls', ['changePassword' => $idpUrl . '/password/change']),
            'caching' => array_replace_recursive($this->getIDMModeDefaultCachingConfig(), $this->getIdmSettingsByKey('caching') ?? []),
            'crmOAuthScope' => $this->getIdmSettingsByKey('crmOAuthScope', ''),
            'requestedOAuthScopes' => $this->getIdmSettingsByKey('requestedOAuthScopes', []),
            // @deprecated: allowedSAs will be removed in the future versions.
            'allowedSAs' => $this->getIdmSettingsByKey('allowedSAs', []),
            'serviceAccountPermissions' => $this->getIdmSettingsByKey(
                'serviceAccountPermissions',
                $this->getDefaultServiceAccountPermissions($tid)
            ),
        ];

        $idmModeConfig['http_client']['headers']['User-Agent'] = $this->get(
            'site_url',
            $_SERVER['HTTP_HOST'] ?? default_user_agent()
        );

        if ($stsKeySetId) {
            $idmModeConfig['keySetId'] = $stsKeySetId;
            $idmModeConfig['urlKeys'] = $urlKeys;
        }

        if ($discoveryUrl = $this->getIdmSettingsByKey('discoveryUrl', $this->getDefaultDiscoveryUrl($tid))) {
            $idmModeConfig['discoveryUrl'] = $discoveryUrl;
        }

        return $idmModeConfig;
    }

    /**
     * @param string $tid
     * @return string
     */
    protected function getDefaultDiscoveryUrl(string $tid): string
    {
        $srn = Srn\Converter::fromString($tid);
        switch ($srn->getPartition()) {
            case 'stage':
                return 'https://discovery-stage.service.sugarcrm.com/';
            case 'cloud':
                return 'https://discovery.service.sugarcrm.com/';
        }
        return '';
    }

    /**
     * @param string $tid
     * @return array
     */
    protected function getDefaultServiceAccountPermissions(string $tid): array
    {
        $srn = Srn\Converter::fromString($tid);
        return [
            sprintf('srn:%s:iam:::permission:crm.sa', $srn->getPartition()),
            sprintf('srn:%s:iam:::permission:tenant.crm.sa', $srn->getPartition()),
        ];
    }

    /**
     * Builds URL for Cloud Console navigation.
     *
     * If you provide key of the pre-configured Cloud Console URI it takes it's value from the config.
     *
     * Additionally you can pass a list of params to specify concrete action and/or resource,
     * e.g. ['users', 'user-id', 'permissions'] will give you 'users/user-id'/permissions' URI.
     *
     * @param string $pathKey
     * @param array $parts
     * @param string $userId
     * @return string
     */
    public function buildCloudConsoleUrl($pathKey, $parts = [], string $userId = '')
    {
        $config = $this->getIDMModeConfig();
        $serverUrl = rtrim($config['cloudConsoleUrl'], '/');
        $additional = [];

        if (array_key_exists($pathKey, $config['cloudConsoleRoutes'])) {
            $additional[] = trim($config['cloudConsoleRoutes'][$pathKey], '/');
        }

        $additional = array_merge($additional, array_map('urlencode', $parts));

        $query = ['tenant_hint' => $config['tid']];
        if ($userId !== '') {
            $tenantSrn = Srn\Converter::fromString($config['tid']);
            $userSrn = $this->getSrnManager()->createUserSrn($tenantSrn->getTenantId(), $userId);
            $query['user_hint'] = Srn\Converter::toString($userSrn);
        }
        return join('/', array_merge([$serverUrl], $additional)) . '?' . http_build_query($query);
    }

    /**
     * Create Srn\Manager
     *
     * @return Srn\Manager
     */
    protected function getSrnManager(): Srn\Manager
    {
        if (is_null($this->srnManager)) {
            $tenantSrn = Srn\Converter::fromString($this->getIDMModeConfig()['tid']);
            $this->srnManager = new Srn\Manager([
                'partition' => $tenantSrn->getPartition(),
                'region' => $tenantSrn->getRegion(),
            ]);
        }
        return $this->srnManager;
    }

    /**
     * Checks IDM mode is enabled
     * @return bool
     */
    public function isIDMModeEnabled(): bool
    {
        return (bool)$this->getIdmSettingsByKey('enabled', false);
    }

    /**
     * Determine if a bean (and action over bean) is considered a special case.
     * Thus possibly denoting that IDM-rules are not applicable for it.
     *
     * @param \SugarBean $bean
     * @param array $request the PHP $_REQUEST superglobal
     * @return bool
     */
    public function isSpecialBeanAction(\SugarBean $bean, array $request): bool
    {
        // Group and Portal Users are not a IdM domain entities and are special Users in terms of SugarCRM
        $creation = empty($bean->id) && in_array(strtolower($request['usertype'] ?? ''), ['portal', 'group']);
        $isPortalOrGroupUser = $bean->module_name == 'Users' && ($bean->is_group || $bean->portal_only || $creation);
        $isEmployee = ($bean->module_name == 'Employees') && !$bean->canBeAuthenticated();
        return $isPortalOrGroupUser || $isEmployee;
    }

    /**
     * get Idm Mode data from source, without any calculated fields
     * @return mixed
     */
    protected function getIdmModeData()
    {
        return $this->get(Config::IDM_MODE_KEY, []);
    }

    /**
     * Enable or disable IDM mode
     *
     * @param false|array $config
     */
    public function setIDMMode($config, $refreshCache = true): void
    {
        // get IDM config data from source
        $oldConfig = $this->getIdmModeData();
        if (is_array($config)) {
            ksort($config);
            ksort($oldConfig);
            if ($config == $oldConfig) {
                // same config, don't need to do anything
                return;
            }
        }

        if ($config === false || !is_array($config)) {
            if (!$this->isIDMModeEnabled()) {
                // was idm off, do nothing
                return;
            }
            $this->getIdmSettings()->saveSetting(self::IDM_MODE_KEY, 'enabled', false);
            $this->toggleCatalog(false);
            $this->setPushNotification(false);
        } else {
            foreach ($config as $key => $value) {
                if ((is_array($value) && !$this->isArrayTypeValue($key))
                    || (!is_array($value) && $this->isArrayTypeValue($key))) {
                    if (!empty($GLOBALS['log'])) {
                        $GLOBALS['log']->error("value for key=$key is unexpected in array!");
                    }
                }
                $this->getIdmSettings()->saveSetting(self::IDM_MODE_KEY, $key, $value);
            }
            $this->toggleCatalog(true);
            $this->setPushNotification(true);
        }

        $this->refreshIdmSettings();
        if ($refreshCache) {
            $this->refreshCache();
        }
    }


    /**
     * Enable or disable push notification.
     *
     * @param bool $state Set $sugar_config['push_notification']['enabled'] to this value in config_override.php
     */
    protected function setPushNotification(bool $state)
    {
        $configurator = new \Configurator();
        if ($configurator->config['push_notification']['enabled'] === $state) {
            return;
        }
        if (empty($configurator->config['push_notification']['service_provider']) ||
            $configurator->config['push_notification']['service_provider'] === 'SugarPush') {
            $configurator->config['push_notification']['enabled'] = $state;
            if ($state && empty($configurator->config['push_notification']['service_provider'])) {
                $configurator->config['push_notification']['service_provider'] = 'SugarPush';
            }
            $configurator->handleOverride();
            $configurator->clearCache();
            $this->sugarConfig->clearCache();
        }
    }

    /**
     * Toggle the catalog_enabled config property and set catalog_url if enabled
     * @param bool $toggle Set catalog_enabled to this value in config_override.php
     */
    public function toggleCatalog(bool $toggle)
    {
        $configurator = new \Configurator();
        if ($configurator->config['catalog_enabled'] == $toggle) {
            return;
        }
        if ($toggle) {
            // set catalog_url only if idmMode is changed from 'disabled' to 'enabled'
            $catalogURL = $this->getCatalogURL();
            if (!$catalogURL) {
                return;
            }
            $configurator->config['catalog_enabled'] = $toggle;
            $configurator->config['catalog_url'] = $catalogURL;
        } else {
            $configurator->config['catalog_enabled'] = $toggle;
        }
        $configurator->handleOverride();
        $configurator->clearCache();
        $this->sugarConfig->clearCache();
    }

    /**
     * Get catalog_url
     * @return string
     */
    public function getCatalogURL(): string
    {
        $catalogURL = '';
        $config = $this->getIDMModeConfig();
        if (!empty($config['tid'])) {
            $tenantSrn = Srn\Converter::fromString($config['tid']);
            if ($tenantSrn) {
                $region = $tenantSrn->getRegion();
                $catalogURL = $this->catalogURLs[$region] ?? '';
            }
        }
        return $catalogURL;
    }

    /**
     * Refresh config cache
     */
    protected function refreshCache(): void
    {
        $repairAndClear = new \RepairAndClear();
        $repairAndClear->repairAndClearAll(['clearAll'], ['Employees', 'Users'], false, false, false);

        \MetaDataManager::refreshSectionCache(\MetaDataManager::MM_CONFIG);
    }


    /**
     * return disabled modules in IDM mode
     * @return array
     */
    public function getIDMModeDisabledModules()
    {
        return ['Users', 'Employees'];
    }

    /**
     * return IDM mode disabled fields
     * @return array
     */
    public function getIDMModeDisabledFields(array $exceptionNames = []) : array
    {
        return array_filter($this->getUserVardef(), function ($def) use ($exceptionNames) {
            return (!empty($def['idm_mode_disabled']) && !in_array($def['name'], $exceptionNames) &&
                (
                    $def['name'] != 'license_type' ||
                    ($def['name'] === 'license_type' && $this->getUserLicenseTypeIdmModeLock())
                )
            );
        });
    }

    /**
     *
     * Used to retrieve the field defs for a Users module
     *
     * @return array
     */
    protected function getUserVardef()
    {
        return \VardefManager::getFieldDefs('Users');
    }

    /**
     * Get default config for php-saml library
     *
     * @return array
     */
    protected function getSAMLDefaultConfig()
    {
        $isSPPrivateKeyCertSet = (bool)$this->get('SAML_request_signing_pkey')
            && (bool)$this->get('SAML_request_signing_x509');
        $siteUrl = rtrim((string)$this->get('site_url'), '/');
        $acsUrl = sprintf('%s/index.php?module=Users&action=Authenticate', $siteUrl);
        $sloUrl = sprintf('%s/index.php?module=Users&action=Logout', $siteUrl);
        $idpSsoUrl = htmlspecialchars_decode((string)$this->get('SAML_loginurl'), ENT_QUOTES);
        $idpSloUrl = htmlspecialchars_decode((string)$this->get('SAML_SLO'), ENT_QUOTES);
        return [
            'strict' => false,
            'debug' => false,
            'sp' => [
                'entityId' => htmlspecialchars_decode($this->get('SAML_issuer', 'php-saml'), ENT_QUOTES) ?: 'php-saml',
                'assertionConsumerService' => [
                    'url' => $acsUrl,
                    'binding' => Constants::BINDING_HTTP_POST,
                ],
                'singleLogoutService' => [
                    'url' => $sloUrl,
                    'binding' => Constants::BINDING_HTTP_REDIRECT,
                ],
                'NameIDFormat' => Constants::NAMEID_EMAIL_ADDRESS,
                'x509cert' => $this->get('SAML_request_signing_x509', ''),
                'privateKey' => $this->get('SAML_request_signing_pkey', ''),
                'provisionUser' => $this->get('SAML_provisionUser', true),
            ],

            'idp' => [
                'entityId' => htmlspecialchars_decode($this->get('SAML_idp_entityId', $idpSsoUrl), ENT_QUOTES),
                'singleSignOnService' => [
                    'url' => $idpSsoUrl,
                    'binding' => Constants::BINDING_HTTP_REDIRECT,
                ],
                'singleLogoutService' => [
                    'url' => $idpSloUrl,
                    'binding' => Constants::BINDING_HTTP_REDIRECT,
                ],
                'x509cert' => $this->get('SAML_X509Cert'),
            ],

            'security' => [
                'authnRequestsSigned' => $isSPPrivateKeyCertSet && $this->get('SAML_sign_authn', false),
                'logoutRequestSigned' => $isSPPrivateKeyCertSet && $this->get('SAML_sign_logout_request', false),
                'logoutResponseSigned' => $isSPPrivateKeyCertSet && $this->get('SAML_sign_logout_response', false),
                'signatureAlgorithm' => $this->get('SAML_request_signing_method', XMLSecurityKey::RSA_SHA256),
                'validateRequestId' => $this->get('saml.validate_request_id', false),
            ],
        ];
    }

    /**
     * returns mapped mango ldap config
     * @return array
     */
    public function getLdapConfig()
    {
        if (!$this->isLdapEnabled()) {
            return [];
        }

        // make sure host is in symfony/ldap format
        $host = $this->getLdapSetting('ldap_hostname', '127.0.0.1');
        $encryption = $this->getLdapSetting('ldap_encryption', self::LDAP_ENCRYPTION_NONE);
        if (strpos((string)$host, 'ldaps://') === 0 && $encryption != self::LDAP_ENCRYPTION_TLS) {
            $host = substr($host, strlen('ldaps://'));
            $encryption = self::LDAP_ENCRYPTION_SSL;
        }

        $ldap = [
            'adapter_config' => [
                'host' => $host,
                'port' => $this->getLdapSetting('ldap_port', 389),
                'options' => [
                    'network_timeout' => 60,
                    'timelimit' => 60,
                ],
                'encryption' => $encryption,
            ],
            'adapter_connection_protocol_version' => 3,
            'baseDn' => $this->getLdapSetting('ldap_base_dn', ''),
            'uidKey' => $this->getLdapSetting('ldap_login_attr', ''),
            'filter' => $this->buildLdapSearchFilter(),
            'dnString' => '',
            'entryAttribute' => $this->getLdapSetting('ldap_bind_attr'),
            'autoCreateUser' => $this->getLdapSetting('ldap_auto_create_users', false),
        ];
        if (!empty($this->getLdapSetting('ldap_authentication'))) {
            $ldap['searchDn'] = $this->getLdapSetting('ldap_admin_user');
            $ldap['searchPassword'] = $this->getLdapSetting('ldap_admin_password');
        }

        if (!empty($this->getLdapSetting('ldap_group'))) {
            $ldap['groupMembership'] = true;
            $ldap['groupDn'] = sprintf(
                '%s,%s',
                $this->getLdapSetting('ldap_group_name'),
                $this->getLdapSetting('ldap_group_dn')
            );
            $ldap['groupAttribute'] = $this->getLdapSetting('ldap_group_attr');
            $ldap['userUniqueAttribute'] = $this->getLdapSetting('ldap_group_user_attr');
            $ldap['includeUserDN'] = (bool)$this->getLdapSetting('ldap_group_attr_req_dn', false);
        }

        return array_merge($this->getLdapDefaultConfig(), $ldap);
    }

    /**
     * Creates a valid LDAP filter string based on configuration.
     *
     * @return string
     */
    protected function buildLdapSearchFilter()
    {
        $defaultFilter = '({uid_key}={username})';
        $loginFilter = $this->getLdapSetting('ldap_login_filter', '');
        if ($loginFilter) {
            $loginFilter = '(' . trim($loginFilter, " ()\t\n\r\0\x0B") . ')';
            return '(&' . $defaultFilter . $loginFilter . ')';
        }
        return $defaultFilter;
    }

    /**
     * Is LDAP enabled?
     * @return bool
     */
    protected function isLdapEnabled()
    {
        $system = \Administration::getSettings('system');
        return !empty($system->settings['system_ldap_enabled']);
    }

    /**
     * return settings value from mango ldap settings
     * @param $key
     * @param null $default
     * @return mixed
     */
    protected function getLdapSetting($key, $default = null)
    {
        if (!$this->ldapSettings) {
            $this->ldapSettings = \Administration::getSettings('ldap');
        }
        if (isset($this->ldapSettings->settings[$key])) {
            return trim(htmlspecialchars_decode($this->ldapSettings->settings[$key], ENT_COMPAT)) ?: $default;
        }

        return $default;
    }

    /**
     * return default config for ldap
     * @return array
     */
    protected function getLdapDefaultConfig()
    {
        return [
            'user' => [
                'mapping' => [
                    'givenName' => 'first_name',
                    'sn' => 'last_name',
                    'mail' => 'email1',
                    'telephoneNumber' => 'phone_work',
                    'facsimileTelephoneNumber' => 'phone_fax',
                    'mobile' => 'phone_mobile',
                    'street' => 'address_street',
                    'l' => 'address_city',
                    'st' => 'address_state',
                    'postalCode' => 'address_postalcode',
                    'c' => 'address_country',
                ],
            ],
        ];
    }

    /**
     * Get default IdM mode caching settings
     *
     * @return array
     */
    protected function getIDMModeDefaultCachingConfig(): array
    {
        return [
            'ttl' => [
                'introspectToken' => 10, // 10 seconds for introspecting user token
                'userInfo' => 10, // 10 seconds for requesting user info
                'keySet' => 24 * 60 * 60, // 24 hours for requesting keySet for Mango client
                'discovery' => 24 * 60 * 60, // 24 hours for requesting Discovery data
                'authz' => 15 * 60, // 15 minutes for requesting AuthZ data
                'remoteIdpResponseParsed' => 60, // 1 minute for remote IDP Auth response
            ],
        ];
    }

    /**
     * check config settings for license type locker
     * @return bool
     */
    public function getUserLicenseTypeIdmModeLock(): bool
    {
        return !empty(\Administration::getSettings('FeatureFlags')->settings['FeatureFlags_idm_mode_licensing']);
    }

    /**
     * check config settings Multifactor authentication
     * @return bool
     */
    public function isMultiFactorAuthenticationEnabled(): bool
    {
        return !empty(\Administration::getSettings('FeatureFlags')->settings['FeatureFlags_idm_mode_mfa_enabled']);
    }
}
