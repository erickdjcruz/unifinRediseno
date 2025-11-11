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
 * API Handler for Portal Theme Config
 */
class PortalthemeConfigApiHandler extends ConfigApiHandler
{
    /**
     * Mapping of variables defined in styleguide
     * @var array
     */
    public static $STYLEGUIDE_VARIABLES = [
        'portaltheme_button_color' => 'PrimaryButton',
        'portaltheme_text_link_color' => 'LinkColor',
    ];

    /**
     * @inheritdoc
     */
    public function setConfig(ServiceBase $api, array $args)
    {
        $args['platform'] = $args['platform'] ?? 'portal';
        $result = parent::setConfig($api, $args);
        $this->setStyleguideConfig($api, $args);
        $this->clearCache();
        return $result;
    }

    /**
     * Update styleguide configs
     *
     * @param ServiceBase $api
     * @param array $args
     */
    protected function setStyleguideConfig($api, $args)
    {
        $themeApi = $this->getThemeApi();
        $themeArgs = [
            'platform' => 'portal',
            'themeName' => 'default',
        ];
        foreach (self::$STYLEGUIDE_VARIABLES as $key => $value) {
            if (!empty($args[$key])) {
                $themeArgs[$value] = $args[$key];
            }
        }
        $themeApi->updateCustomTheme($api, $themeArgs);
    }

    /**
     * Get ThemeApi instance
     * @return ThemeApi
     */
    protected function getThemeApi()
    {
        return new ThemeApi();
    }
}
