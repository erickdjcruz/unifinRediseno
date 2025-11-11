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
 * Deletes onedrive paths that don't have a folderId
 */
class SugarUpgradeCloudDriveOnedrivePathsSanitize extends UpgradeScript
{

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (version_compare($this->from_version, '13.2.0', '<=')) {
            $this->log('Deleting CloudDrive paths with no folder id');
            $this->deleteCloudDriveOnedrivePaths();
            $this->log('Done deleting CloudDrive paths with no folder id');
        }
    }

    /**
     * Delete all onedrive paths with no folder id
     */
    private function deleteCloudDriveOnedrivePaths()
    {
        $conn = $GLOBALS['db']->getConnection();
        $query = <<<SQL
            UPDATE cloud_drive_paths SET deleted = 1 WHERE folder_id IS NULL AND type = ?
        SQL;
        $conn->executeQuery($query, ['onedrive']);
    }
}
