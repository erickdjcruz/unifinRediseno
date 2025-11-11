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

namespace Sugarcrm\Sugarcrm\CloudDrive;

use Sugarcrm\Sugarcrm\CloudDrive\Constants\DriveType;
use Sugarcrm\Sugarcrm\CloudDrive\Drives\GoogleDrive;
use Sugarcrm\Sugarcrm\CloudDrive\Drives\OneDrive;
use Sugarcrm\Sugarcrm\CloudDrive\Drives\DropboxDrive;
use Sugarcrm\Sugarcrm\CloudDrive\Drives\SharepointDrive;

class DriveFactory
{
    /**
     * Get the correct drive type
     *
     * @param string $type
     * @return GoogleDrive|OneDrive|Dropbox|void
     */
    public static function getDrive(string $type)
    {
        switch ($type) {
            case DriveType::GOOGLE:
                return new GoogleDrive();
            case DriveType::ONEDRIVE:
                return new OneDrive();
            case DriveType::DROPBOX:
                return new DropboxDrive();
            case DriveType::SHAREPOINT:
                return new SharepointDrive();
        }
    }
}
