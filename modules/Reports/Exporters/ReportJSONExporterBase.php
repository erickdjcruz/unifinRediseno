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
declare(strict_types=1);

namespace Sugarcrm\Sugarcrm\modules\Reports\Exporters;

require_once 'include/export_utils.php'; // for user defined delimiter

/**
 * Class ReportJSONExporterBase
 * @package Sugarcrm\Sugarcrm\modules\Reports\Exporters
 */
abstract class ReportJSONExporterBase implements ReportExporterInterface
{
    /**
     * @var \Report
     */
    protected $reporter;

    public function __construct(\Report $reporter)
    {
        $this->reporter = $reporter;
    }

    /**
     * Concrete implementations must define their own export methodology
     *
     * @return string
     */
    abstract public function export(): string;

    /**
     * Run the corresponding query to get the data
     * Concrete implementations must define their own query method
     */
    abstract protected function runQuery();

    /**
     * To prepare the export
     */
    protected function prepareExport()
    {
        $this->reporter->do_export = true;
        $this->reporter->_load_currency();
        $this->runQuery();
    }
}
