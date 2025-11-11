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

use Sugarcrm\Sugarcrm\Util\Files\FilePhpEntriesConverter;

class SugarUpgradeEncodeUploadFiles extends UpgradeScript
{
    public function run()
    {
        $settings = Administration::getSettings('upgrade', true);

        if (!empty($settings->settings['upgrade_skip_files_encoding'])) {
            $GLOBALS['log']->info(self::class . ': Encoding of files was skipped.');

            return;
        }

        if (is_windows()) {
            $GLOBALS['log']->info(self::class . ': Encoding of files was started for Windows.');

            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(UploadStream::getDir()));
            $fileConverter = new FilePhpEntriesConverter();

            /** @var SplFileInfo $fileInfo */
            foreach ($iterator as $fileInfo) {
                if ($fileInfo->isDir()) {
                    continue;
                }

                $path = $fileInfo->getPathname();

                try {
                    //We can skip file from converting process. It won't influence on further Sugar work
                    $encPath = $fileConverter->convert($path);
                } catch (SugarException $exception) {
                    $GLOBALS['log']->fatal(self::class . ': ' . $exception->getMessage());

                    continue;
                }

                rename($encPath, $path);
            }
        } else {
            $GLOBALS['log']->info(self::class . ': Encoding of files was started for Unix.');

            $encodeFrom = '<?';
            $encodeTo = '~!#@\&';
            $dir = UploadStream::getDir();

            $command = "grep -a -m 1 -r -l '$encodeFrom' $dir | xargs -i^ sed -i 's+$encodeFrom+$encodeTo+g' ^";

            exec($command);
        }

        $GLOBALS['log']->info(self::class . ': Encoding of files was finished.');
    }
}
