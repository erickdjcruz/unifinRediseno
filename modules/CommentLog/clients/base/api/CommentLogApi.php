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


/**
 * Extends ModuleApi for CommentLog specific work
 *
 * Class CommentLogApi
 */
class CommentLogApi extends ModuleApi
{
    private $methodMaps = [
        'POST' => 'Create',
        'GET' => 'Read',
    ];

    public function registerApiRest()
    {
        return [
            'create' => [
                'reqType' => 'POST',
                'path' => ['CommentLog'],
                'pathVars' => ['module'],
                'method' => 'accessBlocker',
                'shortHelp' => 'This method is not available',
            ],
            'retrieve' => [
                'reqType' => 'GET',
                'path' => ['CommentLog', '?'],
                'pathVars' => ['module', 'record'],
                'method' => 'retrieveRecord',
                'shortHelp' => 'Returns a single parent record of the given commentlog',
                'longHelp' => 'include/api/help/module_record_get_help.html',
            ],
            'read' => [
                'reqType' => 'GET',
                'path' => ['CommentLog'],
                'pathVars' => ['module'],
                'method' => 'accessBlocker',
                'shortHelp' => 'This method is not available',
            ],
        ];
    }

    /**
     * Tells user this method is not there
     * @throws SugarApiExceptionNoMethod
     */
    public function accessBlocker(ServiceBase $api, array $args)
    {
        $method = $api->getRequest()->getMethod();
        $message = sprintf(
            'The %s action is not supported by this endpoint',
            $this->methodMaps[$method] ?? 'requested'
        );
        throw new SugarApiExceptionNoMethod($message);
    }

    /**
     * Retrieves the parent record of this commentlog instead
     * @throws SugarApiExceptionInvalidParameter When id for the given commentlog is not found
     * @throws SugarApiExceptionRequestMethodFailure Somehow the relationship won't load
     * @throws SugarApiExceptionInvalidParameter Can't find any parent record related to this commentlog
     */
    public function retrieveRecord(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'record']);

        $bean = BeanFactory::retrieveBean('CommentLog', $args['record']);
        if ($bean === null) {
            // can't find the CommentLog
            throw new SugarApiExceptionInvalidParameter();
        }

        $parent = $bean->getParentRecord();
        if (!isset($parent['module'], $parent['record'])) {
            throw new SugarApiExceptionInvalidParameter();
        }

        return parent::retrieveRecord($api, $parent);
    }
}
