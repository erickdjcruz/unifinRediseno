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

use Sugarcrm\Sugarcrm\modules\Reports\Exporters\ReportExporter;

require_once 'include/export_utils.php';

/**
 * @param Report $reporter Report object
 * @param bool $stream Streaming back to the client
 * @return string|void Return file name to caller if not streaming
 */
function template_handle_export(Report &$reporter, bool $stream = true)
{
    ini_set('zlib.output_compression', 'Off');
    $reporter->plain_text_output = true;
    //disable paging so we get all results in one pass
    $reporter->enable_paging = false;

    $exporter = new ReportExporter($reporter);
    $content = $exporter->export();

    global $locale, $sugar_config;

    $transContent = $GLOBALS['locale']->translateCharset(
        $content,
        'UTF-8',
        $GLOBALS['locale']->getExportCharset(),
        false,
        true
    );

    if ($stream) {
        ob_clean();
        header('Pragma: cache');
        header('Content-type: text/plain; charset=' . $locale->getExportCharset());
        header('Content-Disposition: attachment; filename=Reports.csv');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . TimeDate::httpTime());
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Content-Length: ' . mb_strlen($transContent, '8bit'));
    }
    if (!empty($sugar_config['export_excel_compatible'])) {
        if ($stream) {
            print $transContent;
        } else {
            return writeToCSVFile($transContent, $reporter->name);
        }
    } else {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if ($locale->getExportCharset() == 'UTF-8' &&
            !preg_match('/macintosh|mac os x|mac_powerpc/i', $user_agent)) {
            $BOM = "\xEF\xBB\xBF";
        } else {
            $BOM = ''; // Mac Excel does not support utf-8
        }
        if ($stream) {
            print $BOM . $transContent;
        } else {
            return writeToCSVFile($BOM . $transContent, $reporter->name);
        }
    }
}

/**
 * Writes content to cache file
 *
 * @param String $content
 * @param String $reportName
 * @return string
 */
function writeToCSVFile(string $content, string $reportName)
{
    // This mimics what pdf does
    create_cache_directory('csv');
    $filenamestamp = '_' . date(translate('LBL_PDF_TIMESTAMP', 'Reports'), time());
    $cr = [' ', "\r", "\n", '/'];
    $filename = str_replace($cr, '_', $reportName . $filenamestamp . '.csv');
    $cachefile = sugar_cached('csv/') . basename($filename);
    $fp = sugar_fopen($cachefile, 'w');
    fwrite($fp, $content);
    fclose($fp);
    return $cachefile;
}
