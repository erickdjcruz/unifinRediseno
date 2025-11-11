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
 * Replace Notes' old attachment field with the new one in subpanels
 */
class SugarUpgradeFixNotesSubpanels extends UpgradeScript
{
    public $order = 9200;
    public $type = self::UPGRADE_CUSTOM;

    public function run()
    {
        if (version_compare($this->from_version, '11.3.0', '>=')) {
            return;
        }

        $path = 'custom/modules/Notes/clients/base/views/subpanel-for-*/subpanel-for-*.php';
        foreach (glob($path) as $scanFile) {
            $viewdefs = [];
            include $scanFile;
            $keyNames = [];
            $viewdefsWrite = $viewdefs;

            // collect key names for array path "$viewdefs['Notes']['base']['view']['subpanel-for-*-notes']"
            for ($i = 0; $i < 4; $i++) {
                if (1 != count($viewdefsWrite)) {
                    break;
                }
                $keyNames[] = array_key_first($viewdefsWrite);
                $viewdefsWrite = $viewdefsWrite[key($viewdefsWrite)];
            }

            $hasNewField = false;
            $oldFieldIndex = null;
            $newFieldName = 'attachment_list';

            foreach ($viewdefsWrite['panels'][0]['fields'] as $idx => $field) {
                if ($field['name'] === $newFieldName) {
                    $hasNewField = true;
                    break;
                } elseif ($field['name'] === 'filename') {
                    $oldFieldIndex = $idx;
                }
            }

            // To replace the name and label of the old attachment field with the new one, if and only if
            // 1. old field is found ($oldFieldIndex is set), and
            // 2. new field is not found
            if (!$hasNewField && isset($oldFieldIndex)) {
                $viewdefsWrite['panels'][0]['fields'][$oldFieldIndex]['name'] = $newFieldName;
                $viewdefsWrite['panels'][0]['fields'][$oldFieldIndex]['label'] = 'LBL_ATTACHMENTS';

                $out = "<?php\n// created: ' . date('Y-m-d H:i:s')\n";
                $out .= override_value_to_string_recursive($keyNames, 'viewdefs', $viewdefsWrite);

                sugar_file_put_contents_atomic($scanFile, $out);
            }
        }
    }
}
