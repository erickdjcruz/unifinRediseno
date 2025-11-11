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

use Sugarcrm\Sugarcrm\Portal\Factory as PortalFactory;

class MetaDataManagerPortal extends MetaDataManager
{
    /**
     * Names of logo configs
     * @var array
     */
    public static $LOGO_CONFIGS = [
        'portaltheme_navigation_bar_logo_image_url' => 'logomarkURL',
        'portaltheme_login_page_image_url' => 'logoURL',
    ];

    /**
     * Find all modules with Portal metadata
     *
     * @param boolean $filtered Ignored
     * @return array List of Portal module names
     */
    protected function getModules($filtered = true)
    {
        $modules = [];
        foreach (SugarAutoLoader::getDirFiles('modules', true) as $mdir) {
            // do we have a core or custom portal directory for the module
            if (SugarAutoLoader::existingCustomOne($mdir . '/clients/portal/')) {
                // strip modules/ from name
                $mname = substr($mdir, 8);
                $modules[] = $mname;
            }
        }
        $modules[] = 'Users';
        $modules[] = 'Filters';

        // If you don't have Serve, you don't get Dashboards in your Portal
        if (PortalFactory::getInstance('Settings')->isServe()) {
            $modules[] = 'Dashboards';
        }

        return $modules;
    }

    /**
     * Gets the full module list of Portal.
     * Returns the same module list as `getModules`.
     *
     * @return array List of Portal module names
     */
    public function getFullModuleList($filtered = false)
    {
        return $this->getModules();
    }

    /**
     * Gets the moduleTabMap array to allow clients to decide which menu element
     * a module should live in for non-module modules
     *
     * @return array
     */
    public function getModuleTabMap()
    {
        $map = $GLOBALS['moduleTabMap'];
        $map['Search'] = 'Home';
        return $map;
    }

    /**
     * Gets configs
     *
     * @return array
     */
    protected function getConfigs()
    {
        global $sugar_config;

        $admin = new Administration();
        $admin->retrieveSettings(false, true);
        $configs = $admin->getConfigForModule('portal', 'support');

        $configs['smtpServerSet'] = false;
        if (!empty(BeanFactory::getBean('OutboundEmail')->getSystemMailerSettings()->mail_smtpserver)) {
            $configs['smtpServerSet'] = true;
        }
        $configs['passwordsetting'] = $sugar_config['passwordsetting'];

        $configs['isServe'] = $admin->isLicensedForServe();

        // Get AWS admin for Serve
        if ($admin->isLicensedForServe()) {
            foreach ($admin->settings as $key => $value) {
                if (substr($key, 0, 4) === 'aws_') {
                    // Format the key for these configs correctly
                    $configs[$this->translateConfigProperty($key)] = $value;
                }
            }
        }

        // Theme settings
        foreach ($admin->settings as $key => $value) {
            if (strpos($key, 'portaltheme_') === 0) {
                if (!empty(self::$LOGO_CONFIGS[$key])) {
                    $configs[self::$LOGO_CONFIGS[$key]] = $value;
                } else {
                    $configs[$this->translateConfigProperty(substr($key, 12))] = $value;
                }
            }
        }

        return $configs;
    }


    /**
     * Fills in additional app list strings data as needed by the client
     *
     * @param array $public Public app list strings
     * @param array $main Core app list strings
     * @return array
     */
    protected function fillInAppListStrings(array $public, array $main)
    {
        $public['countries_dom'] = $main['countries_dom'];
        $public['state_dom'] = $main['state_dom'];

        return $public;
    }

    /**
     * Gets list of modules that are displayed in the navigation bar
     *
     * @return array The list of module names
     */
    public function getTabList($filter = true)
    {
        $controller = new TabController();
        return $controller->getPortalTabs();
    }

    /**
     * Gets the module list for the current user
     * Returns the same module list as `getTabList`.
     *
     * In the future, there may be a UI to allow user to configure visible
     * modules in his `Profile` section.
     *
     * @return array The list of modules for portal
     */
    public function getUserModuleList()
    {
        return $this->getTabList();
    }

    /**
     * Retrieves the portal logo if defined, otherwise the company logo url
     *
     * @return string url of the portal logo
     */
    public function getLogoUrl()
    {
        global $sugar_config;
        $config = $this->getConfigs();
        if (!empty($config['logoURL'])) {
            return $config['logoURL'];
        } else {
            $themeObject = SugarThemeRegistry::current();
            return $sugar_config['site_url'] . '/' . $themeObject->getImageURL('company_logo.png', true, true);
        }
    }

    /**
     * Load Portal specific metadata (heavily pruned to only show modules enabled for Portal)
     * @param array $args
     * @param MetaDataContextInterface $context
     * @return array Portal metadata
     */
    protected function loadMetadata($args, MetaDataContextInterface $context)
    {
        $data = parent::loadMetadata($args, $context);

        if (!empty($data['modules'])) {
            foreach ($data['modules'] as $modKey => $modMeta) {
                if (!empty($modMeta['isBwcEnabled'])) {
                    // portal has no concept of bwc so get rid of it
                    unset($data['modules'][$modKey]['isBwcEnabled']);
                }
            }

            // Serve-only restrictions
            if (isset($data['modules']['Cases']) && !PortalFactory::getInstance('Settings')->isServe()) {
                $contentSearchViews = ['contentsearch-footer', 'contentsearch-results', 'contentsearchdashlet'];
                $contentSearchLayouts = ['contentsearch-dropdown'];
                foreach ($contentSearchViews as $view) {
                    if (isset($data['modules']['Cases']['views'][$view])) {
                        unset($data['modules']['Cases']['views'][$view]);
                    }
                }
                foreach ($contentSearchLayouts as $layout) {
                    if (isset($data['modules']['Cases']['layouts'][$layout])) {
                        unset($data['modules']['Cases']['layouts'][$layout]);
                    }
                }
            }
        }

        // Rehash the hash
        $data['_hash'] = $this->hashChunk($data);
        return $data;
    }

    /**
     * Not supported.
     * {@inheritDoc}
     * @see MetaDataManager::getModuleDropdownViews()
     */
    public function getModuleDropdownViews($moduleName, MetaDataContextInterface $context = null)
    {
        return [];
    }
}
