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

namespace Sugarcrm\Sugarcrm\Maps\Engine\Geocode;

use SugarAutoLoader;
use Sugarcrm\Sugarcrm\Maps\Container as BaseContainer;
use Sugarcrm\Sugarcrm\Maps\Engine\Geocode\Geocode;
use Sugarcrm\Sugarcrm\Maps\Engine\Geocode\Constants;
use Sugarcrm\Sugarcrm\Maps\Engine\Geocode\Geocoder;

class Container extends BaseContainer
{
    /**
     * @var Container
     */
    protected static $instance;

    private function __construct()
    {
        parent::__construct(self::class, Geocode::class, Constants::class, Geocoder::class);
    }

    /**
     * Create new container object. Use self::getInstance unless you know
     * what you are doing.
     *
     * @return Container
     */
    public static function create(): Container
    {
        /*
         * Until system wide bundle support is possible in the framework we
         * rely on the ability of using the /custom framework to customize
         * this service container. See `self::getInstance`.
         */
        $class = SugarAutoLoader::customClass(self::class);
        return new $class();
    }

    /**
     * Factory getting the service container instance.
     *
     * @return Container
     */
    public static function getInstance(): Container
    {
        if (empty(self::$instance)) {
            self::$instance = self::create();
        }

        return self::$instance;
    }
}
