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

use Elastica\Bulk\Action\AbstractDocument;
use Elastica\Document;

/**
 * DeleteDocument with type
 * Adapter class for \Elastica\DeleteDocument
 *
 */
class DeleteDocumentWithSingleType extends DeleteDocumentWithType
{
    /**
     * @return $this
     */
    public function setDocument(Document $document): AbstractDocument
    {
        parent::setDocument($document);
        if ($document instanceof \Sugarcrm\Sugarcrm\Elasticsearch\Adapter\Document) {
            $metadata = $this->_getMetadata($document);
            $metadata = array_merge($metadata, ['_type' => MappingWithSingleType::DEFAULT_TYPE_NAME]);
            $this->setMetadata($metadata);
        }

        return $this;
    }
}
