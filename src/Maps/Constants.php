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

namespace Sugarcrm\Sugarcrm\Maps;

class Constants
{
    public const GEOCODE_MODULE = 'Geocode';
    public const GEOCODE_SCHEDULER_MODULE = 'GeocodeJob';
    public const GEOCODE_SCHEDULER_STATUS_QUEUED = 'QUEUED';
    public const GEOCODE_SCHEDULER_STATUS_REQUEUE = 'REQUEUE';
    public const GEOCODE_SCHEDULER_STATUS_FAILED = 'FAILED';
    public const GEOCODE_SCHEDULER_STATUS_COMPLETED = 'COMPLETED';
    public const GEOCODE_SCHEDULER_STATUS_NOT_FOUND = 'NOT_FOUND';
    public const MIN_CHARS_NR_FOR_VALID_ADDRESS = 4;
}
