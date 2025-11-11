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

use SugarOAuth2Server;

/**
 * base clas for data adapters
 * @package Sugarcrm\Sugarcrm\DocumentMerge\Client\Adapter\Adapters
 */
class BaseDataAdapter implements AdapterInterface
{
    public const MAX_RETRIEVE = 20;

    /**
     * The client id for the sudo token
     *
     * @var string
     */
    public const CLIENT_ID = 'sugar';

    /**
     * The platform for the sudo token
     *
     * @var string
     */
    public const PLATFORM = 'dms';

    /**
     * This data will be processed in order to be
     * returned in a data model accepted by the merge service
     * @var array
     */
    protected $data;

    private $payload;

    /**
     * @constructor
     * @param array $options
     * @return void
     */
    public function __construct(array $options)
    {
        $this->data = $options;
        $this->payload = [];
    }

    /**
     * Build and return the data
     *
     * @return array
     */
    public function getData(): array
    {
        global $current_user;
        $metaManager = new \MetaDataManager();

        /**
         * Example api call
         *
         * App.api.call('create', 'rest/v10/DocumentMerge/merge', {'mergeType': 'merge', 'useRevision': 1,
         * 'documentName': 'saddsad', 'templateId':'139382f2-8657-11eb-b2a9-0242ac140003',
         * 'recordId':'139382f2-8657-11eb-b2a9-0242ac140003', 'recordModule': 'Accounts', 'metadataHash':'32424', '
         * userPrefsHash': '232424', 'mergeRequestId':'139382f2-8657-11eb-b2a9-0242ac140003', 'parentId': '234324',
         * 'parentModule': 'dsada', 'maxRelate':40})
         */
        $params = [];
        $params['mergeType'] = $this->data['mergeType'];
        $params['use_revision'] = $this->data['useRevision'];
        $params['file_name'] = $this->data['templateName'];
        $params['document_id'] = $this->data['templateId'];
        $params['record_id'] = $this->data['recordId'];
        $params['record_module'] = $this->data['recordModule'];
        $params['mergeRequestId'] = $this->data['mergeRequestId'];
        $params['parent_id'] = $this->data['parentId'];
        $params['parent_module'] = $this->data['parentModule'];
        $params['max_relate_records_retrieve'] = $this->data['maxRelate'];

        $params['metadata_hash'] = $metaManager->getMetadataHash();
        $params['user_prefs_hash'] = $current_user->getUserMDHash();

        // we need the current session_id to revert back to
        $sessionId = session_id();

        /**
         * Get a sudo token to be used on the DocumentMerge server
         * This changes the current session
         */
        $outhServer = SugarOAuth2Server::getOAuth2Server();
        $sudoToken = $outhServer->getSudoToken($current_user->user_name, self::CLIENT_ID, self::PLATFORM);

        // close the current sudo token
        session_write_close();

        // revert to the old session
        session_id($sessionId);
        session_start();

        if (is_array($sudoToken)) {
            $params['token'] = $sudoToken;
        }

        $this->payload = $params;

        return $this->payload;
    }
}
