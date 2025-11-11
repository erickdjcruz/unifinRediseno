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

class UserDownloadsHideOpiWpiPlugins implements Feature
{
    public static function getName(): string
    {
        return 'UserDownloadsHideOpiWpiPlugins';
    }

    public static function getDescription(): string
    {
        return 'Flag whether show or hide OPI/WPI Plugins on User - DetailView - Downloads Tab';
    }

    public static function isEnabledIn(string $version): bool
    {
        return version_compare($version, '13.2.0', '>=');
    }

    public static function isToggleableIn(string $version): bool
    {
        return version_compare($version, '15.0.0', '<');
    }
}
