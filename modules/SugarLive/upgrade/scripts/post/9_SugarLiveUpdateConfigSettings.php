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

class SugarUpgradeSugarLiveUpdateConfigSettings extends UpgradeScript
{
    public $order = 9100;
    public $type = self::UPGRADE_CUSTOM;

    /**
     * @var SugarApi
     */
    private $adminApi = null;

    /**
     * @var SugarBean
     */
    private $adminBean = null;

    /**
     * @throws SugarApiExceptionNotFound
     */
    public function run()
    {
        if ($this->shouldUpdateSugarLiveConfig()) {
            $settings = $this->getAdminSettings();
            $ccpUrl = $settings['aws_connect_url'];

            if (empty($ccpUrl)) {
                $instanceName = $settings['aws_connect_instance_name'];

                if (!empty($instanceName)) {
                    $url = $this->getCCPUrl($instanceName);
                    $this->saveCCPUrl($url);
                }
                // do nothing if the instance name is empty
            } else {
                // update the CSP if CCP URL is already populated
                $this->saveToCSP($ccpUrl);
            }
        }
    }

    /**
     * Determine if we should update the SugarLive config settings
     *
     * @return bool true if should update, false otherwise
     */
    public function shouldUpdateSugarLiveConfig(): bool
    {
        return $this->toFlavor('ent') && version_compare($this->from_version, '11.0.0', '<');
    }

    /**
     * Get (and cache) the admin api
     *
     * @return AdministrationApi
     */
    public function getAdministrationApi(): AdministrationApi
    {
        if (is_null($this->adminApi)) {
            $this->adminApi = new \AdministrationApi();
        }

        return $this->adminApi;
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
     * Get the admin settings for the aws category
     *
     * @return array the aws admin settings
     * @throws SugarApiExceptionNotFound
     */
    public function getAdminSettings(): array
    {
        $bean = $this->getAdminBean();
        return $bean->retrieveSettings('aws')->settings;
    }

    /**
     * Generate the CCP URL given the instance name. This assumes the user is
     * connecting to AWS Connect via awsapps.com domain
     *
     * @param string $instanceName
     * @return string the CCP URL
     */
    public function getCCPUrl(string $instanceName): string
    {
        return "https://{$instanceName}.awsapps.com/connect/ccp-v2";
    }

    /**
     * Save the specified CCP URL to admin settings
     *
     * @param string $ccpUrl
     * @throws SugarApiExceptionNotFound
     */
    public function saveCCPUrl(string $ccpUrl)
    {
        $bean = $this->getAdminBean();
        $bean->saveSetting('aws', 'connect_url', $ccpUrl);

        $this->saveToCSP($ccpUrl);
    }

    /**
     * Update CSP using specified CCP URL
     *
     * @param string $ccpUrl
     */
    public function saveToCSP(string $ccpUrl)
    {
        $api = $this->getAdministrationApi();
        $api->updateAWSDomainsOnCSP(
            [
                'aws_connect_url' => $ccpUrl,
            ],
            [],
        );
    }
}
