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

class SugarUpgradeAllowEncodeUploadedFiles extends UpgradeScript
{
    public function run()
    {
        UploadStream::register();
        $filters = stream_get_filters();

        $skip = in_array('encodeFilter', $filters, true);

        $settings = Administration::getSettings('upgrade', true);
        $settings->saveSetting('upgrade', 'skip_files_encoding', $skip);

        $GLOBALS['log']->info(self::class . ': Should encoding of files be skipped? ' . ($skip ? 'yes' : 'no'));
    }
}
