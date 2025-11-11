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

namespace Sugarcrm\Sugarcrm\DocumentMerge\Client;

use Psr\Http\Message\ResponseInterface;

interface Client
{
    /**
     * DocumentMerge Api interface
     *
     * @param string $method HTTP Method
     * @param array $options The data to send to document merging api.
     *
     * @return void
     */
    public function call(string $method, array $options): ResponseInterface;
}
