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
 * Delete files inside the shadow folder that are copies of the files that were deleted from
 * Sugar 7, Sugar 6, or even Sugar 5
 */
class SugarUpgradeCloudCleanup extends UpgradeScript
{
    private const FILES_TO_DELETE = [
        'include/FCKeditor/editor/filemanager/browser/default/connectors/php/config.php',
        'include/FCKeditor/editor/filemanager/upload/php/config.php',
        'include/javascript/tiny_mce/plugins/spellchecker/config.php',
        'include/language/*_*.lang.php',
        'include/SubPanel/SubPanelTilesTabs.php',
        'include/SugarObjects/templates/basic/Dashlets/Dashlet/m-n-Dashlet.php',
        'include/SugarObjects/templates/company/config.php',
        'include/tcpdf/config/tcpdf_config.php',
        'install/lang.config.php',
        'modules/Administration/System.php',
        'modules/Administration/views/view.themesettings.php',
        'modules/Connectors/connectors/sources/ext/rest/dnb/config.php',
        'modules/Connectors/connectors/sources/ext/rest/linkedin/config.php',
        'modules/Connectors/connectors/sources/ext/rest/zoominfocompany/config.php',
        'modules/Connectors/connectors/sources/ext/rest/zoominfoperson/config.php',
        'modules/Connectors/connectors/sources/ext/soap/hoovers/config.php',
        'modules/DCEClients/dce_config.php',
        'modules/disabled/*',
        'modules/Disabled/*',
        'modules/EditCustomFields/*',
        'modules/EmailMan/config.php',
        'modules/Emails/views/view.classic.config.php',
        'modules/Feeds/Feed.php',
        'modules/Forecasts/clients/base/layouts/config/config.php',
        'modules/ForecastSchedule/*',
        'modules/iFrames/*',
        'modules/Import/config.php',
        'modules/Import/ImportMap.php',
        'modules/Import/ImportStep4.php',
        'modules/KBDocumentRevisions/KBDocumentRevision.php',
        'modules/KBDocuments/EditView.php',
        'modules/KBTags/*',
        'modules/MergeRecords/MergeRecord.php',
        'modules/Studio/config.php',
        'modules/Studio/wizards/ManageBackups.php',
        'modules/SugarFeed/SugarFeed.php',
        'modules/Sync/config.php',
        'modules/Temp/*',
        'modules/temp/*',
        'modules/Users/UserSignature.php',
        'portal/include/language/*_*.lang.php',
        'portal/sugar_version.php',
    ];
    
    public $order = 7000;
    public $type = self::UPGRADE_CUSTOM;

    public function run()
    {
        if (version_compare($this->from_version, '15.0.0', '>=')) {
            return;
        }
        if (!defined('SHADOW_INSTANCE_DIR')) {
            return;
        }

        $filesToDelete = self::FILES_TO_DELETE;
        foreach ($filesToDelete as $pattern) {
            $files = glob($pattern);
            $this->deleteCustomerFiles($files);
        }
    }

    private function deleteCustomerFiles($files): void
    {
        if (!is_iterable($files)) {
            return;
        }
        foreach ($files as $file) {
            $realpath = realpath($file);
            $this->deleteCustomerFile($realpath);
        }
    }

    private function deleteCustomerFile($file): void
    {
        if (is_writable($file) && $this->isCustomerFile($file)) {
            $this->log('Deleting ' . $file);
            unlink($file);
        }
    }

    private function isCustomerFile($file): bool
    {
        return str_starts_with($file, (string)SHADOW_INSTANCE_DIR);
    }
}
