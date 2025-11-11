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

namespace Sugarcrm\Sugarcrm\DocumentMerge;

use InvalidArgumentException;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Sugarcrm\Sugarcrm\DocumentMerge\Client\Http\Client;
use Sugarcrm\Sugarcrm\DocumentMerge\Configuration as ServiceConfig;

class Service
{
    private $url;

    public $config;
    public $client;
    public $instance;

    public function __construct()
    {
        $this->config = new ServiceConfig\Configuration(\SugarConfig::getInstance());
        $this->url = $this->config->getServiceURL();
        $this->client = new Client($this->url, $this->config->getMaxRetries());
    }

    /**
     * Makes the call to the merge service
     *
     * App.api.call('create', 'rest/v10/DocumentMerge/merge', {'mergeType': 'merge', 'useRevision': 1,
     *                      'documentName': 'Test', 'templateId':'7f177a9c-992b-11eb-972f-0242ac140008',
     *                      'recordId':'139382f2-8657-11eb-b2a9-0242ac140003', 'recordModule': 'Accounts',
     *                      'metadataHash':'123456789', 'userPrefsHash': '123456789',
     *                      'mergeRequestId':'814cee72-976f-11eb-8954-0242ac140008', 'parentId': '',
     *                      'parentModule': '', 'maxRelate':40})
     *
     * options = [
     *  'params' => [
     *      type => 'convert'
     *          // merge/multimerge/multipdfmerge/labelsgenerate/excel,
     *      use_revision => 1
     *      file_name => {$document_name}
     *      current_user_id => {$current_user_id}
     *      instance_url => {$instance_url}
     *      unique_key => {$unique_key}
     *      document_id => {$document_id} //the template upload id
     *      record_id => {$record_id}
     *      record_module => {$record_module}
     *      metadata_hash => {$metadata_hash}
     *      user_prefs_hash => {$user_prefs_hash}
     *      mergeRequestId => {$merge_request_id}
     *      model_ids => [$id1, $id2] //used for multimerge
     *      current_user_name => {$current_user_name},
     *      dynamicFields => [] // dynamic input
     *      parent_id => {$parent_id} //used for subpanel merge
     *      parent_module => {$parent_module} //used for subpanel merge
     *      max_relate_records_retrieve => {20} //used to limit records retrieval in collections
     *  ],
     *  service => 'convert-service'
     *          //merge-service/multimerge-service/generate-labels-service/excel-merge-service/multiconvert-service
     *  action => 'convert'
     *          //merge/multimerge/multiconvert/labelsgenerate
     *  version => '1'
     * ]
     *
     * @param array $options
     * @return ResponseInterface
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function merge(array $options): ResponseInterface
    {
        $params = array_merge($options, [
            'instance_url' => $this->config->getSystemUrl(),
            'unique_key' => $this->config->getSystemKey(),
        ]);

        return $this->client->call('POST', $params);
    }
}
