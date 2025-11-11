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

class StrictManifestChecks implements Feature
{
    public static function getName(): string
    {
        return 'enableStringManifestChecks';
    }

    public static function getDescription(): string
    {
        return <<<'TEXT'
If enabled, forbids usage of any functions or methods inside MLP's manifest.php file, 
also requires $manifest and $installdefs variables to be defined, no other variables are allowed  
TEXT;
    }

    public static function isEnabledIn(string $version): bool
    {
        return version_compare($version, '13.3.0', '>=');
    }

    public static function isToggleableIn(string $version): bool
    {
        return version_compare($version, '14.1.0', '<');
    }
}
