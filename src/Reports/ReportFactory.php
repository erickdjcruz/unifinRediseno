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

namespace Sugarcrm\Sugarcrm\Reports;

use Sugarcrm\Sugarcrm\Reports\Constants\ReportType;
use Sugarcrm\Sugarcrm\Reports\Types\Reporter;
use Sugarcrm\Sugarcrm\Reports\Types\Matrix;
use Sugarcrm\Sugarcrm\Reports\Types\RowsAndColumns;
use Sugarcrm\Sugarcrm\Reports\Types\Summary;
use Sugarcrm\Sugarcrm\Reports\Types\SummaryDetails;

class ReportFactory
{
    public static function getReport($type, $data, bool $ignoreBuildReportDef = false)
    {
        switch ($type) {
            case ReportType::MATRIX:
                return new Matrix($data, $ignoreBuildReportDef);
            case ReportType::ROWSANDCOLUMNS:
                return new RowsAndColumns($data, $ignoreBuildReportDef);
            case ReportType::SUMMARY:
                return new Summary($data, $ignoreBuildReportDef);
            case ReportType::SUMMARYDETAILS:
                return new SummaryDetails($data, $ignoreBuildReportDef);
            default:
                return new Reporter($data, $ignoreBuildReportDef);
        }
    }
}
