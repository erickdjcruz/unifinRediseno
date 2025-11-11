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

namespace Sugarcrm\Sugarcrm\CustomerJourney\ImportExport;

class TemplateImporter
{
    /**
     * @var array
     */
    private static $links = [
        'dri_subworkflow_templates',
        'dri_workflow_task_templates',
        'web_hooks',
        'forms',
    ];

    /**
     * @var bool
     */
    private $output = false;

    /**
     * @var string
     */
    private $directory = 'install/CustomerJourney/data';

    /**
     * @var bool
     */
    private $purge = false;

    /**
     * @var bool
     */
    private $dry = false;

    /**
     * @var bool
     */
    private $verbose = false;

    /**
     * Keeps track of records saved in the import and makes
     * sure moved records are not deleted when syncing links
     *
     * @var array
     */
    private $saved = [];

    /**
     * @param mixed $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * @param string $directory
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
    }

    /**
     * @param boolean $purge
     */
    public function setPurge($purge)
    {
        $this->purge = $purge;
    }

    /**
     * @param boolean $dry
     */
    public function setDry($dry)
    {
        $this->dry = $dry;
    }

    /**
     * @param boolean $verbose
     */
    public function setVerbose($verbose)
    {
        $this->verbose = $verbose;
    }

    /**
     * Delete record whose id does not match ids array
     *
     * @param array $ids
     */
    private function purge(array $ids)
    {
        $query = new \SugarQuery();
        $query->from(new \DRI_Workflow_Template());
        $query->select('id');
        $query->where()->notIn('id', $ids);

        foreach ($query->execute() as $row) {
            $journey = \BeanFactory::retrieveBean('DRI_Workflow_Template', $row['id']);

            if ($journey) {
                $this->log("Deleting {$journey->module_dir} with id {$journey->id}");

                if (!$this->dry) {
                    $journey->mark_deleted($journey->id);
                }
            }
        }
    }

    /**
     * Log message at info level
     *
     * @param string $message
     */
    private function log($message)
    {
        $GLOBALS['log']->info($message);

        if (is_object($this->output)) {
            $this->output->writeln($message);
        } elseif ($this->output === true) {
            $message = htmlspecialchars($message);
            $lineBreak = php_sapi_name() === 'cli' ? '\n' : '<br/>';
            echo $message . $lineBreak;
        }
    }

    /**
     * Provide list of record Ids
     *
     * @return array
     * @throws \SugarQueryException
     */
    public function listIds()
    {
        return $this->loadFile(sprintf('%s/all.php', $this->directory));
    }

    /**
     * Upload file content and provide it after importing
     *
     * @param string $file
     * @return \DRI_Workflow_Template
     * @throws \SugarApiExceptionMissingParameter
     */
    public function importUpload($file)
    {
        $content = $this->getUploadedContent($file);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \SugarApiExceptionMissingParameter('ERROR_UPLOAD_FAILED');
        }

        return $this->importData($data);
    }

    /**
     * Provide file data after parsing
     *
     * @param string $file
     * @return array
     * @throws \SugarApiExceptionMissingParameter
     */
    public function parseUpload($file)
    {
        $content = $this->getUploadedContent($file);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \SugarApiExceptionMissingParameter('ERROR_UPLOAD_FAILED');
        }

        return $data;
    }

    /**
     * Function to get a data for file uploaded
     *
     * @param string $file
     * @return string
     */
    private function getUploadedContent($file)
    {
        require_once 'include/upload_file.php';
        $uploadFile = new \UploadFile($file);

        // confirm upload
        if (!$uploadFile->confirm_upload()) {
            throw new \SugarApiExceptionMissingParameter('ERROR_UPLOAD_FAILED');
        }

        return $uploadFile->get_file_contents();
    }

    /**
     * Import template with specific id
     *
     * @param string $id
     * @throws \Exception
     */
    public function import($id)
    {
        $file = sprintf('%s/%s.php', $this->directory, $id);
        $this->log("Importing template with id $id");
        $data = $this->loadFile($file);
        $this->importData($data, $id);
    }

    /**
     * Import data as a DRI_Workflow_Template record
     *
     * @param array $data
     * @param string|null $id
     * @return \DRI_Workflow_Template
     */
    private function importData($data, $id = null)
    {
        $this->saved = [];

        if (null === $id) {
            $id = $data['id'];
        }

        /** @var \DRI_Workflow_Template $journey */
        $journey = $this->findRecord('DRI_Workflow_Templates', $id);

        // If the Smart Guide is deleted, don't import and return as it is
        if ($journey->deleted == 1) {
            return $journey;
        }

        // we don't want deactivated default templates to become active again
        if (isset($data['active'])) {
            unset($data['active']);
        }

        $this->syncRecord($journey, $data);

        return $journey;
    }

    /**
     * Sync related records of provided link
     *
     * @param \SugarBean $bean
     * @param string $link
     * @param array $records
     */
    private function syncLink(\SugarBean $bean, $link, array $records)
    {
        $bean->load_relationship($link);
        $current = $bean->$link->getBeans();

        foreach ($records as $id => $data) {
            $record = $this->findRecord($bean->$link->getRelatedModuleName(), $id);

            // If the record is deleted, don't import and skip to the next one
            if ($record->deleted == 1) {
                continue;
            }

            if (isset($current[$id])) {
                unset($current[$id]);
            }

            $this->syncRecord($record, $data);
        }

        $this->deleteRecords($current);
    }

    /**
     * Populate data in given bean
     *
     * @param \SugarBean $bean
     * @param array $data
     * @return bool
     */
    private function populateData(\SugarBean $bean, array $data)
    {
        $changes = false;

        // import blocked by from old format into new one if not exported from new version
        if ($bean instanceof \DRI_Workflow_Task_Template &&
            !empty($data['blocked_by_id']) &&
            empty($data['blocked_by'])
        ) {
            $data['blocked_by'] = json_encode([$data['blocked_by_id']]);
        }

        foreach ($data as $fieldName => $value) {
            if (isset($bean->field_defs[$fieldName]) && $bean->field_defs[$fieldName]['type'] === 'link') {
                continue;
            }

            if ($bean->$fieldName !== $value) {
                if ($this->verbose) {
                    $message = sprintf(
                        'updating field %s to \'%s\' on record with id %s in module %s, previous value: \'%s\'',
                        $fieldName,
                        $value,
                        $bean->id,
                        $bean->module_dir,
                        $bean->$fieldName
                    );

                    $this->log($message);
                }

                $bean->$fieldName = $value;
                $changes = true;
            }
        }

        return $changes;
    }

    /**
     * Save bean and push bean id in saved array
     *
     * @param \SugarBean $bean
     * @param bool $changes
     */
    private function saveRecord(\SugarBean $bean, $changes)
    {
        if ($bean->new_with_id || !empty($bean->fetched_row['deleted'])) {
            $fetchedRowDeleted = false;
            if (is_array($bean->fetched_row) && isset($bean->fetched_row['deleted'])) {
                $fetchedRowDeleted = $bean->fetched_row['deleted'];
            }

            $this->log("creating {$bean->module_dir}");
            $this->log("(new: {$bean->new_with_id}, deleted: {$fetchedRowDeleted}) with id {$bean->id}");

            if (!$this->dry) {
                $bean->save();
            }
        } elseif ($changes) {
            $this->log("updating {$bean->module_dir} with id {$bean->id}");

            if (!$this->dry) {
                $bean->save();
            }
        } else {
            $this->log("{$bean->module_dir} with id {$bean->id} is already synchronized");
        }

        $this->saved[] = $bean->id;
    }

    /**
     * Find record of specific id in provided module
     *
     * @param string $moduleName
     * @param string $id
     * @return \SugarBean
     */
    private function findRecord($moduleName, $id)
    {
        $this->log("retrieving {$moduleName} with id {$id}");
        $bean = \BeanFactory::retrieveBean($moduleName, $id, [], false);

        if (null === $bean) {
            $this->log("creating new bean {$moduleName} with id {$id}");
            $bean = \BeanFactory::newBean($moduleName);
            $bean->id = $id;
            $bean->new_with_id = true;
        }

        return $bean;
    }

    /**
     * Delete all the records of provided array
     *
     * @param \SugarBean[] $records
     */
    private function deleteRecords(array $records)
    {
        foreach ($records as $record) {
            if (!in_array($record->id, $this->saved, true)) {
                $this->log("deleting {$record->module_dir} with id {$record->id}");

                if (!$this->dry) {
                    $record->mark_deleted($record->id);
                }
            }
        }
    }

    /**
     * Load file from provided file path
     *
     * @param string $file
     * @return array
     * @throws \Exception
     */
    private function loadFile($file)
    {
        return require \SugarAutoLoader::existingCustomOne($file);
    }

    /**
     * Sync bean according to provided data
     *
     * @param \SugarBean $bean
     * @param array $data
     */
    private function syncRecord(\SugarBean $bean, array $data)
    {
        $changes = $this->populateData($bean, $data);

        $this->saveRecord($bean, $changes);

        foreach ($data as $fieldName => $value) {
            if (isset($bean->field_defs[$fieldName]) &&
                $bean->field_defs[$fieldName]['type'] === 'link' &&
                in_array($fieldName, self::$links, true)
            ) {
                $this->syncLink($bean, $fieldName, $value);
            }
        }
    }

    /**
     * import all the records
     */
    public function importAll()
    {
        $ids = $this->listIds();

        if (safeCount($ids) > 0) {
            foreach ($ids as $id) {
                $this->import($id);
            }
        } else {
            $this->log('no templates to import');
        }

        if ($this->purge) {
            $this->purge($ids);
        }
    }
}
