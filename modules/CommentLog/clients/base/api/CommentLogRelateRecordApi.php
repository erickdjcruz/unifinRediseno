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

use Sugarcrm\Sugarcrm\SearchEngine\SearchEngine;

/**
 * Extends RelateRecordApi for CommentLog specific work
 *
 * Class CommentLogRelateRecordApi
 */
class CommentLogRelateRecordApi extends RelateRecordApi
{
    /** {@inheritDoc} */
    public function registerApiRest()
    {
        return [
            'addComment' => [
                'reqType' => 'POST',
                'path' => ['<module>', '?', 'link', 'commentlog_link'],
                'pathVars' => ['module', 'record', '', 'link_name'],
                'method' => 'addComment',
                'shortHelp' => 'Add a comment to this module\'s record',
                'longHelp' => 'include/api/help/module_record_link_link_name_post_help.html',
            ],
        ];
    }

    /**
     * Adds a comment to a module's record and indexes it.
     *
     * @param ServiceBase $api The API class of the request.
     * @param array $args The arguments array passed in from the API.
     * @return array Two elements, 'record' which is the formatted version of $primaryBean, and 'related_record' which is the formatted version of $relatedBean
     * @throws SugarApiExceptionError In case the module API has improper interface
     */
    public function addComment(ServiceBase $api, array $args)
    {
        $result = $this->createRelatedRecord($api, $args);
        $bean = $this->loadBean($api, $args);
        $this->indexComment($bean);
        return $result;
    }

    /**
     * Indexes comment added to a bean.
     *
     * @param SugarBean $bean The parent record
     */
    protected function indexComment(SugarBean $bean)
    {
        // 'CommentLog' module itself is not enabled for elastic search
        // but $bean and $bean->commentlog should be enabled
        SearchEngine::getInstance()->indexBean($bean);
    }
}
