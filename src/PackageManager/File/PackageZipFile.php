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

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use SugarConfig;
use Sugarcrm\Sugarcrm\DependencyInjection\Container;
use Sugarcrm\Sugarcrm\FeatureToggle\FeatureFlag;
use Sugarcrm\Sugarcrm\FeatureToggle\Features\TranslateMLPCode;
use Sugarcrm\Sugarcrm\PackageManager\Exception\NoPackageFileException;
use Sugarcrm\Sugarcrm\PackageManager\Exception\NoUploadFileException;
use Sugarcrm\Sugarcrm\PackageManager\Exception\PackageConvertingException;
use Sugarcrm\Sugarcrm\PackageManager\Exception\PackageExistsException;
use Sugarcrm\Sugarcrm\PackageManager\Exception\UnableExtractFileException;
use Sugarcrm\Sugarcrm\PackageManager\Exception\NoPackageManifestFileException;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\SweetTranslator;
use Sugarcrm\Sugarcrm\Util\Files\FileLoader;
use Sugarcrm\Sugarcrm\Util\Files\FilePhpEntriesConverter;
use SugarException;
use ZipArchive;
use RuntimeException;

class PackageZipFile
{
    /**
     * package pre/post action scripts
     */
    public const PRE_INSTALL_FILE = 'scripts/pre_install.php';
    public const POST_INSTALL_FILE = 'scripts/post_install.php';
    public const PRE_UNINSTALL_FILE = 'scripts/pre_uninstall.php';
    public const POST_UNINSTALL_FILE = 'scripts/post_uninstall.php';

    /**
     * script list
     */
    public const PACKAGE_SCRIPT_LIST = [
        self::PRE_INSTALL_FILE,
        self::POST_INSTALL_FILE,
        self::PRE_UNINSTALL_FILE,
        self::POST_UNINSTALL_FILE,
    ];

    /**
     * function name in scripts
     */
    public const PACKAGE_SCRIPTS_FUNCTION = [
        self::PRE_INSTALL_FILE => 'pre_install',
        self::POST_INSTALL_FILE => 'post_install',
        self::PRE_UNINSTALL_FILE => 'pre_uninstall',
        self::POST_UNINSTALL_FILE => 'post_uninstall',
    ];

    /**
     * manifest file name in package zip file
     */
    public const PACKAGE_MANIFEST_FILE_NAME = 'manifest.php';

    /**
     * old sugar creates package meta files to improve performance
     */
    public const PACKAGE_METADATA_FILE_ADDONS = ['manifest', 'icon'];
    public const PACKAGE_METADATA_MD5_FILE_EXT = 'md5';

    /**
     * @var string
     */
    private $relativeZipFilePath;

    /**
     * @var string
     */
    private $zipFile;

    /**
     * @var string
     */
    private $basePackagesDir;

    /**
     * @var string
     */
    private $packageDir;

    /**
     * @var string
     */
    private $manifestFile;

    /**
     * @var bool
     */
    private $isFullPackageExtracted = false;

    /**
     * @var FilePhpEntriesConverter
     */
    private $fileConverter;

    /**
     * PackageZipFile constructor.
     * @param string $zipFile
     * @param string $basePackagesDir
     * @throws NoPackageFileException
     */
    public function __construct(string $zipFile, string $basePackagesDir)
    {
        $this->basePackagesDir = $basePackagesDir;
        $this->relativeZipFilePath = $zipFile;
        $this->fileConverter = new FilePhpEntriesConverter();

        try {
            $zipFile = $this->validateFilePath($zipFile);
        } catch (RuntimeException $e) {
            $exception = new NoPackageFileException();
            $exception->setErrorDescription($e->getMessage());
            throw $exception;
        }

        if (strpos($zipFile, \UploadStream::getDir()) !== false) {
            try {
                //We should abort the process in case if file converter returns SugarException
                $this->zipFile = $this->fileConverter->revert($zipFile);

                register_shutdown_function([$this, 'unlinkZip']);
            } catch (SugarException $e) {
                $exception = new PackageConvertingException();
                $exception->setErrorDescription($e->getMessage());
                throw $exception;
            }
        } else {
            $this->zipFile = $zipFile;
        }
    }

    /**
     * @return string
     */
    public function getRelativeZipFilePath(): string
    {
        return $this->relativeZipFilePath;
    }

    /**
     * return package dir
     * @return string
     */
    public function getPackageDir(): string
    {
        return $this->packageDir;
    }

    /**
     * extract package to package dir
     * @throws UnableExtractFileException
     */
    public function extractPackage(): void
    {
        if ($this->isFullPackageExtracted) {
            return;
        }
        if (!$this->packageDir) {
            $this->createPackageDir();
        }
        $archive = $this->openZipArchive();
        $result = $archive->extractTo($this->packageDir);
        if ($result !== true) {
            throw new UnableExtractFileException('ERR_UW_UNABLE_EXTRACT_FILE', [intval($result), $archive->status]);
        }

        if (SugarConfig::getInstance()->get('moduleInstaller.packageScan', false)) {
            $features = Container::getInstance()->get(FeatureFlag::class);
            if ($features->isEnabled(TranslateMLPCode::getName())) {
                $this->translateCode();
            }
        }

        $this->isFullPackageExtracted = true;
    }

    /**
     * return package manifest file path
     * @return string
     * @throws NoPackageManifestFileException|UnableExtractFileException
     */
    public function getPackageManifestFile(): string
    {
        if ($this->manifestFile) {
            return $this->manifestFile;
        }
        if (!$this->isFullPackageExtracted) {
            $archive = $this->openZipArchive();
            if (!$this->packageDir) {
                $this->createPackageDir();
            }
            $result = $archive->extractTo($this->packageDir, self::PACKAGE_MANIFEST_FILE_NAME);
            if ($result !== true) {
                throw new NoPackageManifestFileException();
            }
        }
        $manifestFile = $this->packageDir . DIRECTORY_SEPARATOR . self::PACKAGE_MANIFEST_FILE_NAME;

        try {
            $manifestFile = $this->validateFilePath($manifestFile);
        } catch (RuntimeException $e) {
            $exception = new NoPackageManifestFileException();
            $exception->setErrorDescription($e->getMessage());
            throw $exception;
        }
        $this->manifestFile = $manifestFile;
        return $this->manifestFile;
    }

    /**
     * clean up after self
     */
    public function deletePackageDir(): void
    {
        if (file_exists($this->packageDir)) {
            rmdir_recursive($this->packageDir);
        }
    }

    /**
     * copy file to destination
     * @param $destination
     * @throws PackageExistsException
     */
    public function copyZipFileTo($destination)
    {
        if (file_exists($destination)) {
            throw new PackageExistsException();
        }
        copy($this->zipFile, $destination);
    }

    /**
     * copy manifest file
     * @param string $destination
     * @throws NoPackageManifestFileException
     * @throws UnableExtractFileException
     * @throws PackageExistsException
     */
    public function copyManifestFileTo(string $destination): void
    {
        if (file_exists($destination)) {
            throw new PackageExistsException();
        }
        copy($this->getPackageManifestFile(), $destination);
    }

    /**
     * create temp package dir and register shutdown function to delete it
     */
    protected function createPackageDir()
    {
        if (!file_exists($this->basePackagesDir)) {
            sugar_mkdir($this->basePackagesDir, null, true);
        }
        $this->packageDir = mk_temp_dir($this->basePackagesDir);
        register_shutdown_function([$this, 'deletePackageDir']);
    }

    /**
     * @return ZipArchive
     * @throws UnableExtractFileException
     */
    protected function openZipArchive(): ZipArchive
    {
        $archive = new ZipArchive();
        $result = $archive->open($this->zipFile);
        if ($result !== true) {
            throw new UnableExtractFileException('ERR_UW_UNABLE_EXTRACT_FILE', [intval($result), $archive->status]);
        }
        return $archive;
    }

    /**
     * @param string $path
     * @return string
     * @throws RuntimeException
     */
    protected function validateFilePath(string $path): string
    {
        return FileLoader::validateFilePath($path, true);
    }

    /**
     * old sugar creates package meta files to improve performance.
     * remove package files with all possible metadata.
     */
    public function removeSelfWithMetadata()
    {
        unlink($this->zipFile);
        $fileTemplate = pathinfo($this->zipFile, PATHINFO_DIRNAME)
            . DIRECTORY_SEPARATOR . pathinfo($this->zipFile, PATHINFO_FILENAME);
        foreach (self::PACKAGE_METADATA_FILE_ADDONS as $addon) {
            $file = sprintf('%s-%s.php', $fileTemplate, $addon);
            if (file_exists($file)) {
                unlink($file);
            }
        }
        $md5File = $this->zipFile . '.' . self::PACKAGE_METADATA_MD5_FILE_EXT;
        if (file_exists($md5File)) {
            unlink($md5File);
        }
    }

    /**
     * include pre\post scripts to support old package compatibility
     * @param string $script
     * @param bool $silent
     */
    public function runPackageScript(string $script, bool $silent): void
    {
        if (!$this->isFullPackageExtracted || !in_array($script, self::PACKAGE_SCRIPT_LIST)) {
            return;
        }
        $scriptFile = $this->getPackageDir() . DIRECTORY_SEPARATOR . $script;
        try {
            $scriptFile = $this->validateFilePath($scriptFile);
        } catch (RuntimeException $e) {
            return;
        }
        if (!file_exists($scriptFile)) {
            return;
        }
        include $scriptFile;

        $funcName = self::PACKAGE_SCRIPTS_FUNCTION[$script];
        if (function_exists($funcName)) {
            if ($silent) {
                ob_start();
            }
            $funcName();
            if ($silent) {
                ob_get_clean();
            }
        }
    }

    public function unlinkZip(): void
    {
        unlink($this->zipFile);
    }

    /**
     * @return void
     */
    protected function translateCode(): void
    {
        $recursiveIterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $this->packageDir,
                FilesystemIterator::SKIP_DOTS | FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO
            )
        );
        $regex = '/^.+\.php$/i';
        foreach (new RegexIterator($recursiveIterator, $regex) as $phpFile) {
            $code = file_get_contents($phpFile);
            $translatedCode = SweetTranslator::translate($code);
            if (false === sugar_file_put_contents($phpFile, $translatedCode)) {
                throw new \RuntimeException("Failed to write translated code into $phpFile");
            }
        }
    }
}
