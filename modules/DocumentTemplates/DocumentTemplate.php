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

class DocumentTemplate extends SugarBean
{
    public $id;
    public $date_entered;
    public $created_by;
    public $filename;
    public $file_mime_type;
    public $file_size;
    public $file_ext;
    public $created_by_name;

    public $table_name = 'document_templates';
    public $object_name = 'DocumentTemplate';
    public $module_dir = 'DocumentTemplates';
    public $new_schema = true;

    public $name;

    /**
     * {@inheritdoc}
     */
    public function save($check_notify = false)
    {
        $file = $this->id;
        $filePath = '';

        if (empty($file)) {
            // If a file has been uploaded but the note does not yet have an ID, then the upload has not yet been
            // confirmed. The file can be found at the temporary location.
            if ($this->file instanceof UploadFile) {
                $filePath = $this->file->get_temp_file_location();
            }
        } else {
            $filePath = 'upload://' . $file;
        }

        if (file_exists($filePath)) {
            $this->file_mime_type = get_file_mime_type($filePath, 'application/octet-stream');
            $this->file_ext = pathinfo($this->filename, PATHINFO_EXTENSION);
            $this->file_size = filesize($filePath);
        }

        return parent::save($check_notify);
    }

    /**
     * Method to delete an attachment
     *
     * @param string $isduplicate
     * @return bool
     */
    public function deleteAttachment($isduplicate = 'false')
    {
        $removeFile = null;
        if ($this->ACLAccess('edit')) {
            if ($isduplicate == 'true') {
                return true;
            }
            $removeFile = "upload://{$this->id}";
        }
        if (file_exists($removeFile)) {
            if (!unlink($removeFile)) {
                $GLOBALS['log']->error("*** Could not unlink() file: [ {$removeFile} ]");
            } else {
                $this->uploadfile = '';
                $this->filename = '';
                $this->file_mime_type = '';
                $this->file_ext = '';
                $this->save();
                return true;
            }
        } else {
            $this->uploadfile = '';
            $this->filename = '';
            $this->file_mime_type = '';
            $this->file_ext = '';
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Make sure the bean implements ACL so it can be modified from Roles
     *
     * @param string $interface
     */
    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }
}
