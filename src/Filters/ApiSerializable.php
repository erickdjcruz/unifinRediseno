<?php

declare(strict_types=1);
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

namespace Sugarcrm\Sugarcrm\Filters;

use ServiceBase;

/**
 * ApiSerializable defines an interface for whole or parts of a filter definition to
 * be formatted for an API client or database.
 */
interface ApiSerializable
{
    /**
     * Returns a filter definition formatted for an API client.
     *
     * @param ServiceBase $api Provides the API context.
     *
     * @return mixed An array representing an entire filter definition or a segment
     * of a filter definition, or a string representing a field's filter value.
     */
    public function apiSerialize(ServiceBase $api);

    /**
     * Returns a filter definition formatted for persistance in a database.
     *
     * @param ServiceBase $api Provides the API context.
     *
     * @return mixed An array representing an entire filter definition or a segment
     * of a filter definition, or a string representing a field's filter value.
     */
    public function apiUnserialize(ServiceBase $api);
}
