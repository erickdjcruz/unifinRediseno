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

namespace Sugarcrm\Sugarcrm\Util\Files;

class FilePhpEntriesConverter
{
    /**
     * @param string $path
     * @return string
     */
    public function convert(string $path): string
    {
        $convertedFilePath = tempnam(sys_get_temp_dir(), 'enc_');

        $fp = fopen($path, 'r');

        if (!is_resource($fp)) {
            throw new \SugarException("File $path cannot be opened.");
        }

        $convertedFp = fopen($convertedFilePath, 'w');

        if (!is_resource($convertedFp)) {
            fclose($fp);
            unlink($convertedFilePath);

            throw new \SugarException("File $convertedFilePath cannot be opened.");
        }

        stream_filter_append($convertedFp, 'encodeFilter');
        stream_copy_to_stream($fp, $convertedFp);

        fclose($convertedFp);
        fclose($fp);

        return $convertedFilePath;
    }

    /**
     * @param string $path
     * @return string
     */
    public function revert(string $path): string
    {
        $revertedFilePath = tempnam(sys_get_temp_dir(), 'dec_');

        $fp = fopen($path, 'r');

        if (!is_resource($fp)) {
            throw new \SugarException("File $path cannot be opened.");
        }

        $revertedFp = fopen($revertedFilePath, 'w');

        if (!is_resource($revertedFp)) {
            fclose($fp);
            unlink($revertedFilePath);

            throw new \SugarException("File $revertedFilePath cannot be opened.");
        }

        stream_filter_append($revertedFp, 'decodeFilter');
        stream_copy_to_stream($fp, $revertedFp);

        fclose($revertedFp);
        fclose($fp);

        return $revertedFilePath;
    }
}
