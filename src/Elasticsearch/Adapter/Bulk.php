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

use Elastica\Bulk as BaseBulk;
use Elastica\Bulk\Action;
use Elastica\Bulk\Action\AbstractDocument as AbstractDocumentAction;
use Elastica\Document;
use Sugarcrm\Sugarcrm\Elasticsearch\Index\IndexManager;

/**
 *
 * Adapter class for \Elastica\Bulk
 *
 */
class Bulk extends BaseBulk
{
    /**
     * @param Document $document
     * @param string|null $opType
     * @return $this
     */
    public function addDocument(Document $document, ?string $opType = null): BaseBulk
    {
        $action = AbstractDocumentAction::create($document, $opType);
        $action = $this->getAction($action);

        return $this->addAction($action);
    }

    /**
     * factory, to get the right action for the version
     * @return AbstractDocumentAction
     */
    protected function getAction(AbstractDocumentAction $action, ?string $opType = null): AbstractDocumentAction
    {
        $esVersion = $this->getServerVersion();
        if (!version_compare($esVersion, '7.0', '<')) {
            return $action;
        }

        if (version_compare($esVersion, '6.0', '<') || !IndexManager::isOneIndexEnabled()) {
            // ES 5.x or no 'one index per instance' is enabled
            if (is_null($opType) || $opType === Action::OP_TYPE_INDEX) {
                return new IndexDocumentWithType($action->getDocument());
            } elseif ($opType === Action::OP_TYPE_DELETE) {
                return new DeleteDocumentWithType($action->getDocument());
            }
        } else {
            // ES 6.x AND  'one index per instance' is enabled
            if (is_null($opType) || $opType === Action::OP_TYPE_INDEX) {
                return new IndexDocumentWithSingleType($action->getDocument());
            } elseif ($opType === Action::OP_TYPE_DELETE) {
                return new DeleteDocumentWithSingleType($action->getDocument());
            }
        }

        return $action;
    }

    /**
     * get the version of elastic server
     * @return string
     * @throws \Exception
     */
    protected function getServerVersion(): string
    {
        return $this->_client->getElasticServerVersion();
    }
}
