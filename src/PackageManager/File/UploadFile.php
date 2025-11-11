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

namespace Sugarcrm\Sugarcrm\PackageManager\File;

use Sugarcrm\Sugarcrm\PackageManager\Exception\InvalidPackageException;
use Sugarcrm\Sugarcrm\PackageManager\Exception\NoUploadFileException;
use Sugarcrm\Sugarcrm\PackageManager\Exception\WrongPackageExtensionException;
use Sugarcrm\Sugarcrm\PackageManager\Exception\PackageManagerException;
use UploadFile as BaseUploadFile;
use UploadStream;

/**
 * Create upload file facade, extend functionality, improve error handling
 */
final class UploadFile
{
    /**
     * @var BaseUploadFile
     */
    private $uploadFile;

    /**
     * @var string
     */
    private $storedFileName;

    /**
     * @var bool
     */
    private $isUploaded = false;

    /**
     * @var bool
     */
    private $isMoved = false;

    /**
     * Create upload file handler
     * @param BaseUploadFile $uploadFile
     * @throws WrongPackageExtensionException
     * @throws PackageManagerException
     */
    public function __construct(BaseUploadFile $uploadFile)
    {
        $this->uploadFile = $uploadFile;
        if (!$this->uploadFile->confirm_upload()) {
            throw (new NoUploadFileException())->setErrorDescription($this->uploadFile->getErrorMessage());
        }

        $this->storedFileName = $this->uploadFile->get_stored_file_name();

        if (strtolower(pathinfo($this->storedFileName, PATHINFO_EXTENSION)) !== 'zip') {
            throw new WrongPackageExtensionException();
        }

        $this->isUploaded = true;
        register_shutdown_function([$this, 'cleanTempFiles']);
    }

    /**
     * clean up upload files
     */
    public function cleanTempFiles(): void
    {
        $tempFileName = $this->uploadFile->get_temp_file_location();
        if (is_uploaded_file($tempFileName) && file_exists($tempFileName)) {
            unlink($tempFileName);
        }
    }

    /**
     * return stored file name
     * @return string
     */
    public function getStoredFileName(): string
    {
        return $this->storedFileName;
    }

    /**
     * return relative file path
     * @return string
     * @throws NoUploadFileException
     */
    public function getPath(): string
    {
        if (!$this->isUploaded || !$this->isMoved) {
            throw new NoUploadFileException();
        }
        return UploadStream::getDir() . '/' . $this->storedFileName;
    }

    /**
     * move uploaded file in to destination folder
     * @throws NoUploadFileException
     * @throws PackageManagerException
     */
    public function moveToUpload()
    {
        if ($this->isMoved) {
            return;
        }
        if (!$this->isUploaded) {
            throw new NoUploadFileException();
        }
        if (!$this->uploadFile->final_move($this->storedFileName)) {
            throw (new InvalidPackageException())->setErrorDescription($this->uploadFile->getErrorMessage());
        }
        $this->isMoved = true;
        register_shutdown_function([$this, 'cleanUploadedFiles']);
    }

    /**
     * clean up upload files
     */
    public function cleanUploadedFiles(): void
    {
        try {
            $streamFilePath = sprintf('%s://%s', UploadStream::STREAM_NAME, $this->storedFileName);
            unlink($streamFilePath);
        } catch (\Exception $e) {
        }
    }
}
