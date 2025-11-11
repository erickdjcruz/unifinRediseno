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


use Sugarcrm\Sugarcrm\Security\ValueObjects\PlatformName;

class ThemeApi extends SugarApi
{
    public function registerApiRest()
    {
        return [
            'getCSSURL' => [
                'reqType' => 'GET',
                'path' => ['css'],
                'pathVars' => [''],
                'method' => 'getCSSURLs',
                'shortHelp' => 'Get (or generate) the css files for a platform and a theme',
                'longHelp' => 'include/api/help/css_get_help.html',
                'noLoginRequired' => true,
            ],
            'previewCSS' => [
                'reqType' => 'GET',
                'path' => ['css', 'preview'],
                'pathVars' => ['', ''],
                'method' => 'previewCSS',
                'shortHelp' => 'Compile the css for a platform and a theme just as a preview',
                'longHelp' => 'include/api/help/css_preview_get_help.html',
                'noLoginRequired' => true,
                'rawReply' => true,
            ],
            'getCustomThemeVars' => [
                'reqType' => 'GET',
                'path' => ['theme'],
                'pathVars' => [''],
                'method' => 'getCustomThemeVars',
                'shortHelp' => 'Get the customizable variables of a custom theme',
                'longHelp' => 'include/api/help/theme_get_help.html',
                'noLoginRequired' => true,
            ],
            'updateCustomTheme' => [
                'reqType' => 'POST',
                'path' => ['theme'],
                'pathVars' => [''],
                'method' => 'updateCustomTheme',
                'shortHelp' => 'Update the customizable variables of a custom theme',
                'longHelp' => 'include/api/help/theme_post_help.html',
            ],
        ];
    }

    /**
     * Get (or generate) the css files for a platform and a theme
     *
     * @param ServiceBase $api
     * @param array $args
     *
     * @return array Locations of CSS Files
     */
    public function getCSSURLs(ServiceBase $api, array $args)
    {
        $theme = $this->newSidecarTheme($args);
        // Otherwise we just return the CSS Url so the application can load the CSS file.
        // getCSSURL method takes care of generating CSS file(s) if it doesn't exist in cache.
        return ['url' => array_values($theme->getCSSURL())];
    }

    /**
     * Compile the css for a platform and a theme just as a preview
     *
     * @param ServiceBase $api
     * @param array $args
     *
     * @return string Plaintext css
     */
    public function previewCSS(ServiceBase $api, array $args)
    {
        // If `preview` is defined, it means that the call was made by the Theme Editor in Studio so we want to return
        // plain text/css
        $theme = $this->newSidecarTheme($args);
        $minify = isset($args['min']);
        $theme->loadVariables();
        $theme->setVariables($args);
        $theme->setVariable('baseUrl', '"../../styleguide/assets"');

        $api->setHeader('Content-type', 'text/css');
        $css = $theme->previewCss($minify);
        $api->setHeader('Content-Length', strlen($css));

        echo $css;
        return;
    }

    /**
     * Get the customizable variables of a custom theme
     *
     * @param ServiceBase $api
     * @param array $args
     *
     * @return array Collection of objects {"name": varName, "value": value}
     */
    public function getCustomThemeVars(ServiceBase $api, array $args)
    {
        $theme = $this->newSidecarTheme($args);
        $output = [];
        $variablesByType = $theme->getThemeVariables();
        foreach ($variablesByType as $type => $variables) {
            foreach ($variables as $lessVar => $lessValue) {
                $output[$type][] = ['name' => $lessVar, 'value' => $lessValue];
            }
        }
        return $output;
    }

    /**
     * Updates variables.less with the values given in the request.
     *
     * @param ServiceBase $api
     * @param array $args
     *
     * @return array Locations of CSS files
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionMissingParameter
     */
    public function updateCustomTheme(ServiceBase $api, array $args)
    {
        if (!$api->user->isAdmin()) {
            throw new SugarApiExceptionNotAuthorized();
        }

        if (empty($args)) {
            throw new SugarApiExceptionMissingParameter('Missing colors');
        }

        if (isset($args['platform'])) {
            $platformName = PlatformName::fromString($args['platform']);
        } else {
            $platformName = PlatformName::base();
        }
        $theme = $this->newSidecarTheme($args);
        // if reset=true is passed
        if (!empty($args['reset'])) {
            $theme->saveThemeVariables($args['reset']);
        } else {
            // else
            $theme->loadVariables();
            // Override the custom variables.less with the given vars
            $variables = array_diff_key($args, ['platform' => 0, 'themeName' => 0, 'reset' => 0]);
            $theme->setVariables($variables);
            $theme->saveThemeVariables();
        }

        // saves the CSS file URL in the portal settings
        $urls = $theme->getCSSURL();
        foreach ($urls as $key => $url) {
            $urls[$key] = $GLOBALS['sugar_config']['site_url'] . '/' . $url;
        }

        $system_config = new Administration();
        $system_config->saveSetting($platformName->value(), 'css', $urls);

        return $urls;
    }

    /**
     * @param array $args
     * @return SidecarTheme
     */
    protected function newSidecarTheme(array $args): SidecarTheme
    {
        if (isset($args['platform'])) {
            $platformName = PlatformName::fromString($args['platform']);
        } else {
            $platformName = PlatformName::base();
        }
        return new SidecarTheme($platformName);
    }
}
