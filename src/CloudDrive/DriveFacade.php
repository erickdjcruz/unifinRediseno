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

class DriveFacade implements DriveInterface
{
    /**
     * @var \Sugarcrm\Sugarcrm\CloudDrive\Drives\GoogleDrive|\Sugarcrm\Sugarcrm\CloudDrive\Drives\OneDrive|\Sugarcrm\Sugarcrm\CloudDrive\Dropbox|void|mixed
     */
    public $drive;

    /**
     * @constructor
     *
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->drive = DriveFactory::getDrive($type);
    }

    /**
     * Get the drive
     *
     * @return Drive
     */
    public function getDrive(): Drive
    {
        return $this->drive;
    }

    /**
     * Sets the facade's drive
     *
     * @param mixed $drive
     * @return void
     */
    public function setDrive($drive): void
    {
        $this->drive = $drive;
    }

    /**
     * Get a list of folders
     *
     * @param array $options
     */
    public function listFolders(array $options)
    {
        return $this->drive->listFolders($options);
    }

    /**
     * Create a folder on drive
     *
     * @param array $options
     * @return null|array
     */
    public function createFolder(array $options): ?array
    {
        return $this->drive->createFolder($options);
    }

    /**
     * Retrieve a list of files from drive
     *
     * @param array $options
     * @return mixed
     */
    public function listFiles(array $options)
    {
        return $this->drive->listFiles($options);
    }

    /**
     * Download file from drive
     *
     * @param array $options
     * @return mixed
     */
    public function downloadFile(array $options)
    {
        return $this->drive->downloadFile($options);
    }

    /**
     * upload a file to drive
     *
     * @param array $options
     * @return array
     */
    public function uploadFile(array $options): ?array
    {
        if (isset($options['largeFile']) && $options['largeFile']) {
            return $this->drive->uploadLargeFile($options);
        }

        return $this->drive->uploadFile($options);
    }

    /**
     * delete a file from drive
     *
     * @param array $options
     */
    public function deleteFile(array $options)
    {
        return $this->drive->deleteFile($options);
    }

    /**
     * Gets a shared link from drive
     *
     * @param array $options
     */
    public function getSharedLink(array $options)
    {
        return $this->drive->getSharedLink($options);
    }

    /**
     * Get file data from drive
     *
     * @param array $options
     * @return mixed
     */
    public function getFile(array $options)
    {
        return $this->drive->getFile($options);
    }

    /**
     * Get the extension of a file
     *
     * @param array $options
     * @return string
     */
    public function getFileExtension(array $options)
    {
        return $this->drive->getFileExtension($options);
    }

    /**
     * Get the usable mime type of a file
     *
     * @param array $options
     * @return string
     */
    public function getUsableMimeType(array $options)
    {
        return $this->drive->getUsableMimeType($options);
    }

    /**
     * Gets the drive client
     *
     * @param array $options
     * @return array
     */
    public function getClient(array $options)
    {
        return $this->drive->getClient($options);
    }
}
