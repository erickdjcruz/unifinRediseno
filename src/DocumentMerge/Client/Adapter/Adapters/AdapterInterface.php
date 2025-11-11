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

namespace Sugarcrm\Sugarcrm\DocumentMerge\Client\Adapter\Adapters;

/**
 * The interface for the data adapters
 * @package Sugarcrm\Sugarcrm\DocumentMerge\Client\Adapter\Adapters
 */
interface AdapterInterface
{
    /**
     * here we build the payload data
     * @return array
     */
    public function getData(): array;
}
