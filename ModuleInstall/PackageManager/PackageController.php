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

require_once 'ModuleInstall/PackageManager/PackageManager.php';

use Sugarcrm\Sugarcrm\Security\InputValidation\InputValidation;
use Sugarcrm\Sugarcrm\Security\InputValidation\Request;
use Sugarcrm\Sugarcrm\PackageManager\PackageManager as UpgradedPackageManager;

class PackageController
{
    /**
     * @var PackageManager
     */
    public $packageManager;

    /**
     * @var Request
     */
    private $inputValidator;

    /**
     * @var UpgradedPackageManager
     */
    private $upgradedPackageManager;

    /**
     * Constructor: this class is called from the the ajax call and handles invoking the correct
     * functionality on the server.
     */
    public function __construct()
    {
        $this->packageManager = new PackageManager();
        $this->inputValidator = InputValidation::getService();
        $this->upgradedPackageManager = new UpgradedPackageManager();
    }

    /**
     * return packages
     * @throws SugarQueryException
     */
    public function getPackagesInStaging(): void
    {
        $this->sendJsonOutput(['packages' => $this->packageManager->getPackagesInStaging()]);
    }

    /**
     * Get package file path from $_REQUEST['file'] and remove all package related files.
     * Directly return JSON to client
     */
    public function remove(): void
    {
        $result = false;
        $id = $this->inputValidator->getValidInputRequest('file');
        if (!empty($id)) {
            /** @var UpgradeHistory $upgradeHistory */
            $upgradeHistory = BeanFactory::retrieveBean('UpgradeHistory', $id);
            if (!empty($upgradeHistory->id)) {
                try {
                    $this->upgradedPackageManager->deletePackage($upgradeHistory);
                    $result = true;
                } catch (Exception $e) {
                    $result = false;
                }
            }
        }
        $this->sendJsonOutput(['result' => $result]);
        return;
    }

    /**
     * Sends output in a JSON format
     *
     * @param mixed $output
     */
    protected function sendJsonOutput($output)
    {
        header('Content-Type: application/json');
        echo JSON::encode($output);
    }
}
