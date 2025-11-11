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
 *
 * HealthCheck
 */
class HealthCheck extends Basic
{
    public const CACHE_DIR = 'healthcheck';

    public $module_dir = 'HealthCheck';
    public $object_name = 'HealthCheck';
    public $table_name = 'healthcheck';

    /**
     *
     * Perform healthcheck
     * @param Scanner $scanner
     * @return HealthCheck
     */
    public function run(HealthCheckScanner $scanner)
    {
        // log file setup
        $cacheDir = sugar_cached(self::CACHE_DIR);
        sugar_mkdir($cacheDir);
        $this->logfile = 'healthcheck-' . time() . '.log';
        $scanner->setLogFile($cacheDir . '/' . $this->logfile);

        try {
            $logMeta = $scanner->scan();
            $this->logmeta = json_encode($logMeta);
            $this->bucket = $scanner->getStatus();
            $this->flag = $scanner->getFlag();
        } catch (Exception $e) {
            $GLOBALS['log']->fatal('Error executing Health Check: ' . $e->getMessage());
            $this->error = $e->getMessage();
        }
        if (!in_array($this->bucket, ['H'])) {
            $this->save();
        }
        return $this;
    }

    /**
     *
     * Get the most recent healtcheck run
     *
     * @return HealthCheck|null
     */
    public function getLastRun()
    {
        $sql = 'SELECT id FROM healthcheck WHERE deleted = 0 ORDER BY date_entered DESC';
        $id = $this->db->getOne($sql, false, 'Error fetching most recent healtcheck record');
        if ($id) {
            return $this->retrieve($id);
        }
        return null;
    }

    /**
     *
     * Return full path for log file
     */
    public function getLogFileName()
    {
        if (!empty($this->logfile)) {
            return sugar_cached(self::CACHE_DIR) . '/' . $this->logfile;
        }
    }

    /**
     *
     * @see Basic::get_summary_text()
     */
    public function get_summary_text()
    {
        return '';
    }
}
