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

use Microsoft\Graph\Model\DriveItem as ModelDriveItem;

class DriveItem
{
    public $id;
    public $name;
    public $driveId;
    public $isFolder;
    public $isGoodForDocuSign;
    public $parents;
    public $shared;
    public $owners;
    public $iconLink;
    public $webViewLink;
    public $dateModified;
    public $downloadUrl; //works for onedrive

    /**
     * Maps a model tp DriveItem
     *
     * @param ModelDriveItem $data
     * @return DriveItem
     */
    public static function fromOneDrive(ModelDriveItem $data): DriveItem
    {
        $item = new self([
            'id' => $data->getId(),
            'name' => $data->getName(),
        ]);

        if ($data->getRemoteItem()) {
            $data = $data->getRemoteItem();
            $data->setShared(true);
        }
        $parentReference = $data->getParentReference();

        if (!is_null($parentReference)) {
            $item->setDriveId($parentReference->getDriveId());
            $item->setParents($parentReference->getId());
        }
        $data->getFolder() ? $item->setFolder(true) : $item->setFolder(false);

        $item::isGoodForDocuSign($data, $item->name) ? $item->setGoodForDocuSign(true) : $item->setGoodForDocuSign(false);

        $createBy = $data->getCreatedBy();
        $user = null;
        if ($createBy) {
            $user = $createBy->getUser();
        }

        $item->setOwners([$user]);
        $item->setWebViewLink($data->getWebUrl());
        if (!is_null($data->getLastModifiedDateTime())) {
            $item->setDateModified($data->getLastModifiedDateTime()->format('c'));
        }
        $item->setDownloadUrl($item->downloadUrl);

        return $item;
    }

    /**
     * Maps a model tp DriveItem
     *
     * @param ModelDriveItem $data
     * @return DriveItem
     */
    public static function fromSharepoint(ModelDriveItem $data): DriveItem
    {
        $name = $data->getName();
        $allProps = $data->getProperties();
        $driveType = array_key_exists('driveType', $allProps) ? $allProps['driveType'] : null;

        if (array_key_exists('displayName', $allProps)) {
            $name = $allProps['displayName'];
        }

        $item = new self([
            'id' => $data->getId(),
            'name' => $name,
        ]);

        if ($data->getRemoteItem()) {
            $data = $data->getRemoteItem();
            $data->setShared(true);
        }
        $parentReference = $data->getParentReference();

        if (!is_null($parentReference)) {
            $item->setDriveId($parentReference->getDriveId());
            $item->setParents($parentReference->getId());
        }

        $data->getFolder() || isset($allProps['siteCollection']) ? $item->setFolder(true) : $item->setFolder(false);
        if ($driveType === 'documentLibrary') {
            $item->setFolder(true);
        }

        $item::isGoodForDocuSign($data, $item->name) ? $item->setGoodForDocuSign(true) : $item->setGoodForDocuSign(false);

        $createBy = $data->getCreatedBy();
        $user = null;
        if ($createBy) {
            $user = $createBy->getUser();
        }

        $item->setOwners([$user]);
        $item->setWebViewLink($data->getWebUrl());
        if (!is_null($data->getLastModifiedDateTime())) {
            $item->setDateModified($data->getLastModifiedDateTime()->format('c'));
        }
        $item->setDownloadUrl($item->downloadUrl);

        return $item;
    }

    /**
     * Map google item to DriveItem
     *
     * @param mixed $data
     * @return DriveItem
     */
    public static function fromGoogleDrive($data): DriveItem
    {
        $item = new self($data);
        $data->mimeType === 'application/vnd.google-apps.folder' ? $item->setFolder(true) : $item->setFolder(false);

        $item::isGoodForDocuSign($data, $item->name) ? $item->setGoodForDocuSign(true) : $item->setGoodForDocuSign(false);

        $item->setDateModified($data->modifiedTime);
        return $item;
    }

    /**
     * Map dropbox item to DriveItem
     *
     * @param mixed $data
     * @return DriveItem
     */
    public static function fromDropboxDrive($data): DriveItem
    {
        $item = new self([
            'id' => array_key_exists('id', $data) ? $data['id'] : $data['shared_folder_id'],
            'name' => $data['name'],
        ]);

        if (isset($data['shared_folder_id'])) {
            $item->setShared(true);
        }

        if (array_key_exists('id', $data)) {
            isset($data['.tag']) && $data['.tag'] === 'folder' ? $item->setFolder(true) : $item->setFolder(false);
        } else {
             isset($data['shared_folder_id']) ? $item->setFolder(true) : $item->setFolder(false);
        }

        $item::isGoodForDocuSign($data, $item->name) ? $item->setGoodForDocuSign(true) : $item->setGoodForDocuSign(false);

        if (isset($data['client_modified']) && !is_null($data['client_modified'])) {
            $dateModified = new \DateTime($data['client_modified']);
            $item->setDateModified($dateModified->format('r'));
        }
        if (isset($data['time_invited'])) {
            $dateModified = new \DateTime($data['time_invited']);
            $item->setDateModified($dateModified->format('r'));
        }
        if (isset($data['preview_url'])) {
            $item->setWebViewLink($data['preview_url']);
        }

        return $item;
    }

    /**
     * Check if file is good to be send to DocuSign
     *
     * @param Object $data
     * @param String $fileName
     * @return bool
     */
    protected static function isGoodForDocuSign($data, $fileName): bool
    {
        if (isset($data->mimeType) && (
            $data->mimeType === 'application/pdf' ||
                $data->mimeType === 'application/msword' ||
                $data->mimeType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ||
                $data->mimeType === 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        )) {
            return true;
        }

        $allowedExtensionsForDS = ['doc', 'docx', 'pdf', 'xls', 'xlsx', 'ppt', 'pptx', 'png', 'jpg', 'jpeg'];

        foreach ($allowedExtensionsForDS as $extension) {
            $interestingCharNr = strlen($extension) + 1;
            if (substr(strtolower($fileName), $interestingCharNr * -1) === ".{$extension}") {
                return true;
            }
        }

        return false;
    }

    /**
     * @constructor
     *
     * @param string $type
     */
    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->shared = $data['shared'] ?? null;
        $this->parents = $data['parents'] ?? null;
        $this->owners = $data['owners'] ?? null;
        $this->iconLink = $data['iconLink'] ?? null;
        $this->webViewLink = $data['webViewLink'] ?? null;
    }

    /**
     * Setter for drive id;
     *
     * @param string $id
     * @return void
     */
    public function setDriveId(string $id)
    {
        $this->driveId = $id;
    }

    /**
     * Used only for onedrive
     * Set if item is folder
     *
     * @param bool $isFolder
     */
    public function setFolder(bool $isFolder)
    {
        $this->isFolder = $isFolder;
    }

    /**
     * Set if item can be send to DocuSign
     *
     * @param bool $isGoodForDocuSign
     */
    public function setGoodForDocuSign(bool $isGoodForDocuSign)
    {
        $this->isGoodForDocuSign = $isGoodForDocuSign;
    }

    /**
     * Set parents
     *
     * @param string $parentId
     */
    public function setParents(?string $parentId)
    {
        $this->parents = [$parentId];
    }

    /**
     * Set shared
     * @param bool $isShared
     */
    public function setShared(bool $isShared)
    {
        $this->shared = $isShared;
    }

    /**
     * Set owners
     *
     * @param array $owners
     */
    public function setOwners(?array $owners)
    {
        $this->owners = $owners;
    }

    /**
     * Set owners
     *
     * @param string $link
     */
    public function setWebViewLink(?string $link)
    {
        $this->webViewLink = $link;
    }

    /**
     * Set date modified
     *
     * @param string $dateModified
     */
    public function setDateModified(?string $dateModified)
    {
        $this->dateModified = $dateModified;
    }

    /**
     * Set download url
     *
     * @param string $link
     */
    public function setDownloadUrl(?string $link)
    {
        $this->downloadUrl = $link;
    }
}
