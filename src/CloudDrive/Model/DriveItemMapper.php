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

namespace Sugarcrm\Sugarcrm\CloudDrive\Model;

use Sugarcrm\Sugarcrm\CloudDrive\Constants\DriveType;

class DriveItemMapper
{
    public $data;
    /**
     * @var string|mixed
     */
    public $type;

    /**
     * @constructor
     * @param mixed $data
     * @param string $type
     */
    public function __construct($data, string $type)
    {
        $this->data = $data;
        $this->type = $type;
    }

    /**
     * Returns an object fo DriveItem type
     *
     * @param $data
     * @param string $type
     * @return null|DriveItem
     */
    public function mapToDriveItem($data = null, ?string $type = null): ?DriveItem
    {
        $data = $data ?? $this->data;
        $type = $type ?? $this->type;

        if ($type === DriveType::GOOGLE) {
            return DriveItem::fromGoogleDrive($data);
        } elseif ($type === DriveType::ONEDRIVE) {
            return DriveItem::fromOneDrive($data);
        } elseif ($type === DriveType::DROPBOX) {
            return DriveItem::fromDropboxDrive($data);
        } elseif ($type === DriveType::SHAREPOINT) {
            return DriveItem::fromSharepoint($data);
        }

        return null;
    }

    /**
     * Parses a response to array
     *
     * @param array $data
     * @param string $type
     * @return null|array
     */
    public function mapToArray($data = null, ?string $type = null): ?array
    {
        $data = $data ?? $this->data;
        $type = $type ?? $this->type;

        $result = [];
        foreach ($data as $model) {
            $result[] = $this->mapToDriveItem($model, $type);
        }
        return $result;
    }
}
