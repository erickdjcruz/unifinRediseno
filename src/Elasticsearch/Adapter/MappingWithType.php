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

namespace Sugarcrm\Sugarcrm\Elasticsearch\Adapter;

use Elastica\Index;
use Elastica\Response;
use Elasticsearch\Endpoints\Indices\PutMapping;

/**
 * this is the Mapping class for old elatic verson which supports type
 * Adapter class for \Elastica\Mapping
 *
 */
class MappingWithType extends Mapping
{
    /**
     * overwrite the default behavior
     * Submits the mapping and sends it to the server.
     *
     * @param Index $index the index to send the mappings to
     * @param array $query Query string parameters to send with mapping
     *
     * @return Response Response object
     */
    public function send(Index $index, array $query = []): Response
    {
        $endpoint = new PutMapping();
        // setup type
        $endpoint->setType($this->type);
        $endpoint->setBody($this->toArray());
        $endpoint->setParams($query);
        return $index->requestEndpoint($endpoint);
    }
}
