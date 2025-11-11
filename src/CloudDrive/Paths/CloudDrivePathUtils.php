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

namespace Sugarcrm\Sugarcrm\CloudDrive\Paths;

use BeanFactory;
use SugarBean;
use Sugarcrm\Sugarcrm\CloudDrive\DriveFacade;
use SugarQuery;

trait CloudDrivePathUtils
{
     /**
     * Gets the list of record paths
     *
     * @param array $args
     *
     * @return array
     */
    protected function getDrivePathsUtils(array $args): array
    {
        $sugarQuery = new SugarQuery();
        $sugarQuery->from(BeanFactory::newBean('CloudDrivePaths'), ['team_security' => false]);
        $sugarQuery->where()->equals('type', $args['type']);
        $sugarQuery->where()->equals('deleted', 0);

        if ($args['module']) {
            $sugarQuery->where()->equals('path_module', $args['module']);
        }
        if (isset($args['recordId']) && $args['recordId']) {
            $sugarQuery->where()->equals('record_id', $args['recordId']);
        }

        $result = $sugarQuery->execute();

        return $result;
    }

    /**
     * Find folder root
     *
     * @param string $type
     * @return null|SugarBean
     * @throws SugarQueryException
     * @throws SugarApiExceptionNotFound
     */
    protected function findRoot(string $type): ?SugarBean
    {
        $sugarQuery = new SugarQuery();
        $sugarQuery->from(\BeanFactory::newBean('CloudDrivePaths'), ['team_security' => false]);
        $sugarQuery->where()->equals('type', $type);
        $sugarQuery->where()->equals('is_root', 1);
        $sugarQuery->where()->equals('deleted', 0);
        $result = $sugarQuery->execute();

        if (safeCount($result) > 0) {
            $pathData = $result[0];
            $driveBean = BeanFactory::retrieveBean('CloudDrivePaths', $pathData['id']);

            return $driveBean;
        }
        return null;
    }


    /**
     * Retrieve paths for a module
     *
     * @param array $options
     * @return array
     */
    protected function getPaths(array $options): array
    {
        //get paths for this module
        $recordPaths = $this->getDrivePathsUtils($options);
        $modulePaths = $this->getDrivePathsUtils([
            'type' => $options['type'],
            'module' => $options['module'],
        ]);
        $paths = safeCount($recordPaths) > 0 ? $recordPaths : $modulePaths;

        return $paths;
    }

    /**
     * Parse path and replace variables
     *
     * @param string $path
     * @param SugarBean $record
     * @return null|array
     */
    protected function parsePath(string $path, SugarBean $record): ?array
    {
        /**
         * We handle 2 types of paths here:
         * 1. a json formatted path - it means we already know folder ids
         * 2. just a string - 'path1/path2/{$name}' - this was added manually by the user
         */
        $decodedPath = json_decode($path, true);

        if (($decodedPath === false || is_null($decodedPath)) && is_string($path)) {
            $path = trim($path, '/');
            $path = explode('/', $path);
            foreach ($path as $index => $pathSubName) {
                $pathItem = ['name' => $pathSubName];
                $path[$index] = $pathItem;
            }
            $decodedPath = $path;
        }

        foreach ($decodedPath as $index => $pathItem) {
            $pattern = '/\$\w+/';
            preg_match_all($pattern, $pathItem['name'], $matches);

            foreach ($matches[0] as $field) {
                $field = ltrim($field, $field[0]); //remove the dollar sign $fieldValue
                $fieldValue = $record->{$field};
                $field = '$' . $field;
                $pathItem['name'] = str_replace($field, $fieldValue, $pathItem['name']);
            }
            $decodedPath[$index] = $pathItem;
        }
        return $decodedPath;
    }

    /**
     * Initialize a record bean
     *
     * @param array $options
     * @return null|SugarBean
     */
    protected function getRecord(array $options): ?SugarBean
    {
        $module = $options['module'];
        $recordId =  array_key_exists('recordId', $options) ? $options['recordId'] : null ;

        if ($recordId) {
            $record = BeanFactory::retrieveBean($module, $recordId);
        } else {
            $record = BeanFactory::newBean($module);
        }
        return $record;
    }

    /**
     * Retrieves a file from drive
     *
     * @param array $options
     * @return false|array|DriveFile
     */
    protected function getFileUtils(array $options)
    {
        $driveFacade = $this->getDrive($options['type']);
        return $driveFacade->getFile($options);
    }


    /**
     * Get Drive facade
     *
     * @param string $type
     * @return DriveFacade
     */
    protected function getDrive(string $type)
    {
        return new DriveFacade($type);
    }

      /**
     * Try to find a path that matches our path
     *
     * @param array $path
     * @param array $pathFolders
     * @param array $currentFolder
     * @param string $type
     * @return null|array
     */
    protected function approximatePath(
        array       $path,
        array       $currentFolder,
        string      $type
    ): ?array {

        $count = safeCount($path);

        for ($index = $count - 1; $index >= 0; $index--) {
            $folderParent = ($index === 1 && ($path[$index - 1]['name'] === 'My files' || $path[$index - 1]['name'] === 'Shared')) ?
                'root' : $path[$index - 1]['name'];
            $parentId = $path[$index - 1]['folderId'];
            $folderName = ($index === 0 && ($path[$index]['name'] === 'My files' || $path[$index]['name'] === 'Shared')) ? 'root' : $path[$index]['name'];

            $driveId = $currentFolder['is_shared'] ? $currentFolder['drive_id'] : null;
            $data = $this->listFoldersUtils([
                'folderName' => $folderName,
                'folderParent' => $folderParent,
                'sharedWithMe' => $currentFolder['is_shared'],
                'parentId' => $parentId,
                'type' => $type,
                'driveId' => $driveId,
            ]);

            if (!$data['success']) {
                return [
                    'success' => false,
                    'message' => $data['message'],
                ];
            }

            if (is_array($data['files']) && safeCount($data['files']) > 0) {
                $driveItem = $data['files'][0];
                $root = false;
                $nextPageToken = false;
                $parentId = $driveItem->id;
                $driveId = $driveItem->driveId;

                if ($index === $count - 1) {
                    $root = $driveItem->id;
                    $parentId = $driveItem->parents[0];
                    $nextPageToken = $data['nextPageToken'];
                    $path[$index - 1]['driveId'] = $driveItem->driveId;
                    $path[$index - 1]['folderId'] = $parentId;
                }

                $path[$index]['driveId'] = $driveItem->driveId;
                $path[$index]['folderId'] = $driveItem->id;
                $path[$index]['sharedWithMe'] = $currentFolder['is_shared'];

                if ($path[0]['name'] === 'My files' || $path[0]['name'] === 'Shared') {
                    $path[0]['folderId'] = 'root';
                }

                return [
                    'root' => $root,
                    'path' => $path,
                    'pathCreateIndex' => $index + 1,
                    'nextPageToken' => $nextPageToken,
                    'parentId' => $parentId,
                    'isShared' => $currentFolder['is_shared'],
                    'driveId' => $driveId,
                ];
            }

            if ($data['parentId']) {
                // should go here on google
                $path[$index - 1]['folderId'] = $data['parentId'];
                return [
                    'root' => false,
                    'path' => $path,
                    'pathCreateIndex' => $index,
                    'nextPageToken' => null,
                    'parentId' => $data['parentId'],
                    'isShared' => $currentFolder['is_shared'],
                    'driveId' => null,
                ];
            }
        }
        return null;
    }

    /**
     * List folders in a drive
     *
     * @param ServiceBase $api
     * @param array $args
     * @return FileList|bool
     */
    protected function listFoldersUtils(array $args)
    {
        $driveFacade = $this->getDrive($args['type']);
        return $driveFacade->listFolders($args);
    }
}
