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

namespace Sugarcrm\Sugarcrm\Hint;

use Sugarcrm\Sugarcrm\CSP\ContentSecurityPolicy;
use Sugarcrm\Sugarcrm\CSP\Directive;

class ContentSecurityPolicyWrapper
{
    /**
     * Add to default csp
     *
     * @param string $suffix
     */
    public static function addToDefaultCsp($suffix)
    {
        $csp = ContentSecurityPolicy::fromAdministrationSettings()->withAddedDefaults();
        $directive = Directive::create('default-src', $suffix);
        $csp->appendDirective($directive);
        $csp->saveToSettings();
    }
}
