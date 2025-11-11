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
use Elastica\Mapping as BaseMapping;
use Elastica\Response;

/**
 *
 * Adapter class for \Elastica\Mapping
 *
 */
class Mapping extends BaseMapping
{
    /**
     * type name
     * @var string
     */
    protected $type = null;

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
        return parent::send($index, $query);
    }

    /**
     * Returns mapping type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * set mapping type.
     *
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }
}
