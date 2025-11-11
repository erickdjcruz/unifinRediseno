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

namespace Sugarcrm\Sugarcrm\Util;

trait MemoryUsageRecorderTrait
{
    /**
     * current memory usage in kb
     * @var int
     */
    private $currentMemoryUsageInRecorder = 0;

    /**
     * start to record
     */
    public function startRecord(): void
    {
        $this->currentMemoryUsageInRecorder = $this->getMemoryUsage();
    }

    /**
     * stop record, return the delta
     * @param int $currntUsage
     * @return int
     */
    public function stopRecord(int &$currntUsage): int
    {
        $delta = $this->getMemoryUsage() - $this->currentMemoryUsageInRecorder;
        $currntUsage = $this->getMemoryUsage();
        $this->currentMemoryUsageInRecorder = $currntUsage;
        return $delta;
    }

    /**
     * check memory usage vs memory limit set in php' ini file, the return value is in %
     * @return int
     */
    public function checkMemoryUsageVsLimit(): int
    {
        $memoryLimit = $this->getMemoryUsageLimitFromIni();
        if ($memoryLimit === -1) {
            return 0;
        }
        if ($memoryLimit < 1) {
            return 100;
        }
        return intdiv($this->getMemoryUsage() * 100, $memoryLimit);
    }

    /**
     * get memory usage
     * @return int
     */
    protected function getMemoryUsage(): int
    {
        return intdiv(memory_get_usage(), 1024);
    }

    /**
     * read ini for 'memory_limit' and return in kb
     * @return int
     */
    protected function getMemoryUsageLimitFromIni(): int
    {
        $memory_limit = ini_get('memory_limit');
        if (empty($memory_limit) || $memory_limit === '-1') {
            return -1;
        }
        preg_match('/^\s*([0-9.]+)\s*([KMGTPE])B?\s*$/i', $memory_limit, $matches);
        $num = 0;
        if (isset($matches[1])) {
            $num = (float)$matches[1];
        }
        if (isset($matches[2])) {
            switch (strtoupper($matches[2])) {
                case 'G':
                    $num = $num * 1024 * 1024;
                    break;
                case 'M':
                    $num = $num * 1024;
                    break;
            }
        }
        return intval($num);
    }
}
