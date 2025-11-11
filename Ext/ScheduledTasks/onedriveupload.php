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

use Sugarcrm\Sugarcrm\Security\HttpClient\ExternalResourceClient;
use Sugarcrm\Sugarcrm\CloudDrive\Drives\OneDrive;

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

if (!class_exists('OneDriveUploadJob')) {
    class OneDriveUploadJob implements RunnableSchedulerJob
    {
        /**
         * the job
         *
         * @var SchedulersJob
         */
        protected $job;

        /**
         * Set job
         *
         * @param SchedulersJob $job
         */
        public function setJob(SchedulersJob $job)
        {
            $this->job = $job;
        }

        /**
         * Job execution function
         *
         * @param string $data
         * @return true
         */
        public function run($data)
        {
            global $current_user;
            $oneDriveClient = new OneDrive();

            $this->job->runnable_ran = true;
            $this->job->runnable_data = $data;
            $data = unserialize(base64_decode($data), ['allowed_classes' => false]);
            $uploadUrl = $data['uploadUrl'];
            $filePath = $data['filePath'];
            $client = $oneDriveClient->getClient();
            $fileName = $data['fileName'];
            $fileSize = $data['fileSize'];
            $fragSize = 320 * 1024;
            $numFrags = ceil($fileSize / $fragSize);
            $bytesRemaining = $fileSize;
            $i = 0;

            while ($i < $numFrags) {
                $chunkSize = $fragSize;
                $numBytes = $fragSize;
                $start = $i * $fragSize;
                $end = $i * $fragSize + $chunkSize - 1;
                $offset = $i * $fragSize;

                if ($bytesRemaining < $chunkSize) {
                    $chunkSize = $bytesRemaining;
                    $numBytes = $bytesRemaining;
                    $end = $fileSize - 1;
                }

                $data = $oneDriveClient->getFileChunk($filePath, $chunkSize, $offset);
                $contentRange = " bytes {$start}-{$end}/{$fileSize}";
                $headers = [
                    'Content-Length' => $numBytes,
                    'Content-Range' => $contentRange,
                ];

                $request = $client->createRequest('PUT', $uploadUrl);
                $request->addHeaders($headers);
                $request->attachBody($data);

                try {
                    $request->execute();
                } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                    $errorMessage = json_decode($e->getResponse()->getBody(true));
                    $GLOBALS['log']->fatal($errorMessage->error->message);
                    return false;
                }

                $bytesRemaining = $bytesRemaining - $chunkSize;
                $i++;
            }

            $notification = BeanFactory::newBean('Notifications');
            $notification->name = translate('LBL_MICROSOFT_UPLOAD_COMPLETE');
            $notification->description = $fileName . translate('LBL_MICROSOFT_UPLOAD_COMPLETE_DESCRIPTION');
            $notification->assigned_user_id = $current_user->id;
            $notification->severity = 'information';
            $notification->save();

            return true;
        }
    }
}
