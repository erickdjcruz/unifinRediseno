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

use Sugarcrm\Sugarcrm\CSP\ContentSecurityPolicy;
use Sugarcrm\Sugarcrm\CSP\Directive;

/**
 * Handler for Config API
 */
class CspConfigApiHandler extends ConfigApiHandler
{
    /**
     * @inheritdoc
     */
    public function getConfig(ServiceBase $api, array $args)
    {
        $admin = BeanFactory::getBean('Administration');
        return $admin->retrieveSettings('csp', true)->settings;
    }

    /**
     * @inheritdoc
     */
    public function setConfig(ServiceBase $api, array $args)
    {
        $prefix = 'csp_';
        $directives = [];
        foreach ($args as $key => $value) {
            if (substr($key, 0, 4) === $prefix) {
                // convert values like csp_default_src to default-src
                $cspDirective = substr($key, 4);
                $cspDirective = str_replace('_', '-', $cspDirective);

                if (trim($value) === '') {
                    $directives[] = Directive::createWithEmptySource($cspDirective);
                } else {
                    $directives[] = Directive::create($cspDirective, $value);
                }

                if (!empty($directives)) {
                    $csp = ContentSecurityPolicy::fromDirectivesList(...$directives);
                    $csp->saveToSettings($api->platform);
                }
            }
        }
        return $this->getConfig($api, $args);
    }
}
