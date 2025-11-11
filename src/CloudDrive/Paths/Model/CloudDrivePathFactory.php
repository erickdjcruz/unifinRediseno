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

namespace Sugarcrm\Sugarcrm\CloudDrive\Paths\Model;

use Sugarcrm\Sugarcrm\CloudDrive\Constants\DriveType;
use Sugarcrm\Sugarcrm\CloudDrive\Paths\Model\Types\CloudDrivePathBase;
use Sugarcrm\Sugarcrm\CloudDrive\Paths\Model\Types\CloudDrivePathDropbox;
use Sugarcrm\Sugarcrm\CloudDrive\Paths\Model\Types\CloudDrivePathGoogleDrive;
use Sugarcrm\Sugarcrm\CloudDrive\Paths\Model\Types\CloudDrivePathOnedrive;
use Sugarcrm\Sugarcrm\CloudDrive\Paths\Model\Types\CloudDrivePathSharepoint;

class CloudDrivePathFactory
{
    /**
     * Get the Path Model
     *
     * @param string $type
     * @return CloudDrivePathBase
     */
    public static function getPathModel(string $type)
    {
        switch ($type) {
            case DriveType::SHAREPOINT:
                return new CloudDrivePathSharepoint(['type' => $type,]);
            case DriveType::ONEDRIVE:
                return new CloudDrivePathOnedrive(['type' => $type,]);
            case DriveType::DROPBOX:
                return new CloudDrivePathDropbox(['type' => $type,]);
            case DriveType::GOOGLE:
                return new CloudDrivePathGoogleDrive(['type' => $type,]);
            default:
                return new CloudDrivePathBase(['type' => $type,]);
        }
    }
}
