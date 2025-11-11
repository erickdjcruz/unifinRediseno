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

namespace Sugarcrm\Sugarcrm\CloudDrive\Paths\Model\Types;

use CloudDrivePath;
use Doctrine\DBAL\Exception;
use Sugarcrm\Sugarcrm\CloudDrive\Paths\Model\Types\CloudDrivePathBase;
use SugarQueryException;

class CloudDrivePathOnedrive extends CloudDrivePathBase
{
    /**
     * Get the onedrive path
     *
     * @param array $options
     * @return array
     */
    public function getDrivePath(array $options): array
    {
        $result = ['root' => 'root'];
        $paths = [];
        $path = $this->findRoot($options['type']);

        if ($options['layoutName'] === 'record') {
            $paths = $this->getPaths($options);
        }

        if (safeCount($paths) > 0) {
            $path = $paths[0];
        }

        if (is_array($path)) {
            $result = [
                'root' => isset($path['folder_id']) ? $path['folder_id'] : 'root',
                'driveId' => isset($path['drive_id']) ? $path['drive_id'] : null,
                'siteId' => isset($path['site_id']) ? $path['site_id'] : null,
                'path' => isset($path['path']) ? $path['path'] : null,
                'isShared' => isset($path['is_shared']) ? $path['is_shared'] : null,
            ];
        }

        if ($path instanceof CloudDrivePath) {
            $result = [
                'root' => isset($path->folder_id) ? $path->folder_id : 'root',
                'driveId' => isset($path->drive_id) ? $path->drive_id : null,
                'siteId' => isset($path->site_id) ? $path->site_id : null,
                'path' => isset($path->path) ? $path->path : null,
                'isShared' => isset($path->is_shared) ? $path->is_shared : null,
            ];
        }

        return $result;
    }
}
