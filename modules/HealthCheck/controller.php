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

require_once 'modules/HealthCheck/HealthCheckHelper.php';


/**
 *
 * HealthCheck Controller
 *
 */
class HealthCheckController extends SugarController
{
    /**
     * @see SugarController::$action_remap
     * @var array
     */
    protected $action_remap = [];

    /**
     * Default action "index"
     */
    public function action_index()
    {
        $this->view = 'index';
    }

    /**
     * Execute scan - returns json data
     */
    public function action_scan()
    {
        $this->view = 'ajax';

        // initialize scanner
        $scanner = $this->getHelper()->getScanner('web');
        $scanner->setInstanceDir(__DIR__ . '/../..');

        $hc = $this->bean->run($scanner);
        if (!empty($hc->error)) {
            echo json_encode(['error' => $hc->error]);
        } else {
            // logmeta is already json encoded
            echo $hc->logmeta;
        }

        if ($this->getHelper()->pingHeartbeat(['bucket' => $hc->bucket, 'flag' => $hc->flag])) {
            $GLOBALS['log']->info('HealthCheck: Heartbeat server has been pinged successfully.');
        } else {
            $GLOBALS['log']->error('HealthCheck: Unable to ping Heartbeat server.');
        }
    }

    /**
     *
     * Export log file from last run
     */
    public function action_export()
    {
        $this->view = 'ajax';

        $hc = $this->bean->getLastRun();
        if ($hc) {
            $file = $hc->getLogFileName();
            if ($file && file_exists($file)) {
                $this->streamFileToBrowser($file);
            }
        }
        sugar_cleanup(true);
    }

    /**
     *
     * Confirm action, will redirect to UpgradeWizard
     */
    public function action_confirm()
    {
        $this->view = 'ajax';
        $url = SugarConfig::getInstance()->get('site_url');
        $redirect = "{$url}/UpgradeWizard.php";
        $hc = $this->bean->getLastRun();
        if ($hc) {
            $redirect .= "?confirm_id={$hc->id}";
        }
        $this->set_redirect($redirect);
        $this->redirect();
    }

    /**
     *
     * Stream given file to browser
     * @param string $file Filename full path
     */
    protected function streamFileToBrowser($file)
    {
        header('Content-Type: application/text');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
    }

    /**
     * @return HealthCheckHelper
     */
    protected function getHelper()
    {
        require_once 'include/SugarSystemInfo/SugarSystemInfo.php';
        require_once 'include/SugarHeartbeat/SugarHeartbeatClient.php';
        if (file_exists('modules/HealthCheck/HealthCheckClient.php')) {
            require_once 'modules/HealthCheck/HealthCheckClient.php';
        }
        return HealthCheckHelper::getInstance();
    }
}
