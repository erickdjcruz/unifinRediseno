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

declare(strict_types=1);

namespace Sugarcrm\Sugarcrm\FeatureToggle\Features;

use Sugarcrm\Sugarcrm\FeatureToggle\Feature;

class EnhancedModuleChecks implements Feature
{
    public static function getName(): string
    {
        return 'enableEnhancedModuleChecks';
    }

    public static function getDescription(): string
    {
        return <<<'TEXT'
Defines whether the curl_*, socket_*, and stream_* functions should be allowed in MLPs.

Note: The default value for this configuration is "false" for Sugar version 12.1.0. 
The default value for Sugar versions 12.2.0 and higher is "true".
Instances running on Sugar's cloud environment will have this setting enforced as true.
TEXT;
    }

    public static function isEnabledIn(string $version): bool
    {
        return version_compare($version, '12.2.0', '>=');
    }

    public static function isToggleableIn(string $version): bool
    {
        return version_compare($version, '13.0.0', '<');
    }
}
