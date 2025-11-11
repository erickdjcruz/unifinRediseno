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

use DownloadFileApi;
use RestService;

class Drive
{
    /**
     * Gets the file path of a document
     *
     * @param SugarBean $bean
     * @return string
     */
    public function getFilePath(\SugarBean $bean): ?string
    {
        $api = new RestService();
        $download = new DownloadFileApi($api);
        $fileInfo = $download->getFileInfo($bean, 'filename');
        return $fileInfo['path'];
    }

    /**
     * Get a chunk from a file
     *
     * @param string $path
     * @param int $chunkSize
     * @param int $offset
     *
     * @return null|bool|string
     */
    public static function getFileChunk(string $path, int $chunkSize, int $offset)
    {
        if ($stream = fopen($path, 'r')) {
            $data = stream_get_contents($stream, $chunkSize, $offset);
            fclose($stream);

            return $data;
        }

        return null;
    }
}
