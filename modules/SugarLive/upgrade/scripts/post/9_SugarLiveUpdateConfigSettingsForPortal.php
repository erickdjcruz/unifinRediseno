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

class SugarUpgradeSugarLiveUpdateConfigSettingsForPortal extends UpgradeScript
{
    public $order = 9100;
    public $type = self::UPGRADE_CUSTOM;

    /**
     * @var SugarBean
     */
    private $adminBean = null;

    /**
     * @var AwsConfigApiHandler
     */
    private $awsConfigApi = null;

    /**
     * @throws SugarApiExceptionNotFound
     */
    public function run()
    {
        if ($this->shouldUpdateCSPForPortal()) {
            $settings = $this->getAdminSettings();
            $isPortalChatEnabled = $settings['aws_connect_enable_portal_chat'];

            if ($isPortalChatEnabled) {
                $this->saveToCSP(
                    [],
                    [
                        'wss://*.amazonaws.com',
                    ]
                );
            }
        }
    }

    /**
     * Determine if we should update CSP for Portal
     *
     * @return bool
     */
    public function shouldUpdateCSPForPortal(): bool
    {
        return $this->toFlavor('ent') && version_compare($this->from_version, '11.2.0', '<');
    }

    /**
     * Get (and cache) the admin bean
     *
     * @return SugarBean
     * @throws SugarApiExceptionNotFound
     */
    public function getAdminBean(): SugarBean
    {
        if (is_null($this->adminBean)) {
            $this->adminBean = BeanFactory::getBean('Administration');
        }

        return $this->adminBean;
    }

    /**
     * Get (and cache) the aws config api
     *
     * @return AwsConfigApiHandler
     */
    public function getAwsConfigApi(): AwsConfigApiHandler
    {
        if (is_null($this->awsConfigApi)) {
            $this->awsConfigApi = new \AwsConfigApiHandler();
        }

        return $this->awsConfigApi;
    }

    /**
     * Get the admin settings for the aws category
     *
     * @return array
     * @throws SugarApiExceptionNotFound
     */
    public function getAdminSettings(): array
    {
        $bean = $this->getAdminBean();
        return $bean->retrieveSettings('aws')->settings;
    }

    /**
     * Invoke the aws config api's saveToCSP method to save new CSP values
     *
     * @param $domainsToRemove
     * @param $domainsToAppend
     */
    public function saveToCSP($domainsToRemove, $domainsToAppend)
    {
        $api = $this->getAwsConfigApi();
        $api->saveToCSP($domainsToRemove, $domainsToAppend);
    }
}
