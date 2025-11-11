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
 * Update Docmerge urls
 */
class SugarUpgradeDocMergeServiceUrls extends UpgradeScript
{
    public $order = 9999;

    /**
     * @var string
     */
    private $logPrefix;

    public function __construct($upgrader)
    {
        parent::__construct($upgrader);
        $this->logPrefix = 'DocMergeUpgradeScript: ';
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (version_compare($this->from_version, '12.2.0', '>=')) {
            return;
        }
        $this->updateConfig();
    }

    /**
     * Update the config with the proper service urls
     * @return void
     */
    private function updateConfig()
    {
        $this->upgrader->config['document_merge'] = [
            'max_retries' => 3,
            'service_urls' => [
                'default' => 'https://document-merge-us-west-2-prod.service.sugarcrm.com',
                'us-west-2' => 'https://document-merge-us-west-2-prod.service.sugarcrm.com',
                'ap-southeast-2' => 'https://document-merge-ap-se-2-prod.service.sugarcrm.com',
                'eu-central-1' => 'https://document-merge-eu-central-1-prod.service.sugarcrm.com',
                'ca-central-1' => 'https://document-merge-ca-central-1-prod.service.sugarcrm.com',
                'ap-southeast-1' => 'https://document-merge-ap-se-1-prod.service.sugarcrm.com',
                'eu-west-2' => 'https://document-merge-eu-west-2-prod.service.sugarcrm.com',
            ],
        ];
    }
}
