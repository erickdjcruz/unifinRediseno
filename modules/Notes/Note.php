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


// Note is used to store customer information.
class Note extends SugarBean
{
    // Stored fields
    public $id;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $assigned_user_id;
    public $created_by;
    public $created_by_name;
    public $modified_by_name;
    public $description;
    public $name;
    public $filename;
    // handle to an upload_file object
    // used in emails
    public $file;
    public $embed_flag; // inline image flag
    public $note_parent_id;
    public $parent_type;
    public $parent_id;
    public $contact_id;
    public $portal_flag;
    public $team_id;

    public $parent_name;
    public $contact_name;
    public $contact_phone;
    public $contact_email;
    public $file_mime_type;
    public $file_ext;
    public $file_source;
    public $file_size;
    public $attachment_flag;
    public $module_dir = 'Notes';
    public $default_note_name_dom = ['Meeting notes', 'Reminder'];
    public $table_name = 'notes';
    public $new_schema = true;
    public $object_name = 'Note';
    public $importable = true;

    public $entry_source = 'internal';

    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = [
        'contact_name',
        'contact_phone',
        'contact_email',
        'parent_name',
        'first_name',
        'last_name',
    ];

    /**
     * Assignment notification emails are not sent when the note is an email attachment
     *
     * {@inheritdoc}
     */
    public function send_assignment_notifications($notify_user, $admin)
    {
        if (empty($this->email_id)) {
            parent::send_assignment_notifications($notify_user, $admin);
        }
    }

    /**
     * Populate the contact id field for new record if it's empty
     */
    protected function setContactId()
    {
        // supported parent module/field pair
        $supportedModules = [
            'Cases' => 'primary_contact_id',
        ];

        // If we are creating a new Note and the parent module is supported
        // and contact_id is not already set, then set contact_id
        if (!$this->isUpdate()
            && empty($this->contact_id)
            && !empty($this->parent_type)
            && !empty($supportedModules[$this->parent_type])
            && !empty($this->parent_id)) {
            $parentBean = BeanFactory::retrieveBean($this->parent_type, $this->parent_id);
            if (!empty($parentBean->{$supportedModules[$this->parent_type]})) {
                $this->contact_id = $parentBean->{$supportedModules[$this->parent_type]};
            }
        }
    }

    /**
     * Get and save file size if note represents and email file attachment
     *
     * {@inheritdoc}
     */
    public function save($check_notify = false)
    {
        $file = $this->getUploadId();
        $filePath = '';

        if (empty($file)) {
            // If a file has been uploaded but the note does not yet have an ID, then the upload has not yet been
            // confirmed. The file can be found at the temporary location.
            if ($this->file instanceof UploadFile) {
                $filePath = $this->file->get_temp_file_location();
            }
        } else {
            $filePath = "upload://{$file}";
        }

        if (file_exists($filePath)) {
            $this->file_mime_type = get_file_mime_type($filePath, 'application/octet-stream');
            $this->file_ext = pathinfo((string)$this->filename, PATHINFO_EXTENSION);
            $this->file_size = filesize($filePath);
            if (!$this->isUpdate()) {
                // new attachment-only note
                $this->attachment_flag = true;
                $check_notify = false;
            }
        }

        $stateChanges = $this->getStateChanges();

        $this->setContactId();
        $id = parent::save($check_notify);

        // executes Team update flow for all nested attachments if necessary
        if (!$this->attachment_flag && $this->load_relationship('attachments')) {
            $sensitiveFields = [
                'team_id' => true,
                'team_set_id' => true,
                'acl_team_set_id' => true,
                'assigned_user_id' => true,
            ];
            $teamListIsLong = safeCount($this->teams->_teamList) > 1;
            if ($teamListIsLong || !empty(array_intersect_key($stateChanges, $sensitiveFields))) {
                // update DB values
                $this->updateAttachmentTeams($this->attachments, $this->id);

                // update already loaded beans
                $this->synchronizeTeamsRecursively();
            }
        }

        return $id;
    }

    /**
     * Set teams and assigned user to be the same as parent's for an attachment note
     *
     * @param SugarBean $parentBean
     * @param bool $save
     */
    public function setAttachmentTeams(SugarBean $parentBean, bool $save = true)
    {
        if ($this->team_set_id !== $parentBean->team_set_id || $this->assigned_user_id !== $parentBean->assigned_user_id) {
            $this->team_id = $parentBean->team_id ?? null;
            $this->team_set_id = $parentBean->team_set_id ?? null;
            $this->acl_team_set_id = $parentBean->acl_team_set_id ?? null;
            $this->assigned_user_id = $parentBean->assigned_user_id ?? null;
            if ($save) {
                $this->save();
            }
        }
    }

    /**
     * Fast implementation of team update flow in case a parent Note changes
     */
    public function updateAttachmentTeams(Link2 $attachmentLink, string $id): void
    {
        $updateChunkSize = 100;
        $rel = $attachmentLink->getRelationshipObject();
        $conn = DBManagerFactory::getConnection();
        $attachmentIds = $conn->fetchFirstColumn(
            sprintf('SELECT id FROM %s WHERE %s = ? AND deleted = ?', $rel->getRelationshipTable(), $rel->rhs_key),
            [$id, 0]
        );

        $attachmentChunks = array_chunk($attachmentIds, $updateChunkSize);
        foreach ($attachmentChunks as $attachmentChunk) {
            $builder = $conn->createQueryBuilder();
            $builder->update($rel->getRelationshipTable())
                ->set('team_id', ':team_id')
                ->set('team_set_id', ':team_set_id')
                ->set('acl_team_set_id', ':acl_team_set_id')
                ->set('assigned_user_id', ':assigned_user_id')
                ->where($builder->expr()->in('id', ':ids'))
                ->setParameter('team_id', $this->team_id)
                ->setParameter('team_set_id', $this->team_set_id)
                ->setParameter('acl_team_set_id', $this->acl_team_set_id)
                ->setParameter('assigned_user_id', $this->assigned_user_id)
                ->setParameter('ids', $attachmentChunk, $conn::PARAM_STR_ARRAY);

            $affectedRows = $builder->executeStatement();

            if ($affectedRows === 0) {
                continue;
            }

            // update nested attachments
            foreach ($attachmentChunk as $attachmentId) {
                $this->updateAttachmentTeams($attachmentLink, $attachmentId);
            }
        }
    }

    /**
     * Team update flow in case a parent Note changes: updates Beans which previously were loaded in memory
     */
    public function synchronizeTeamsRecursively(): void
    {
        $this->load_relationship('attachments');
        foreach ($this->attachments->beans as $attachment) {
            $attachment->setAttachmentTeams($this, false);
            $attachment->synchronizeTeamsRecursively();
        }
    }

    public function safeAttachmentName()
    {
        global $sugar_config;

        // get position of last "." in file name
        $file_ext_beg = strrpos($this->filename, '.');
        $file_ext = '';

        // get file extension
        if ($file_ext_beg !== false) {
            $file_ext = substr($this->filename, $file_ext_beg + 1);
        }

        // check to see if this is a file with extension located in "badext"
        foreach ($sugar_config['upload_badext'] as $badExt) {
            if (strtolower($file_ext) == strtolower($badExt)) {
                // if found, then append with .txt and break out of lookup
                $this->name = $this->name . '.txt';
                $this->file_mime_type = 'text/';
                $this->filename = $this->filename . '.txt';
                break; // no need to look for more
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @uses UploadFile::unlink_file() to delete the file as well. The file is only deleted if {@link Note::$upload_id}
     * is empty.
     */
    public function mark_deleted($id)
    {
        if (empty($this->upload_id)) {
            UploadFile::unlink_file($id);
        }

        // delete note
        parent::mark_deleted($id);
    }

    /**
     * Removes the file from the filesystem and clears the file metadata from the record.
     *
     * @param string $isduplicate
     * @param boolean $save
     * @return bool
     */
    public function deleteAttachment($isduplicate = 'false', $save = true)
    {
        if (!$this->ACLAccess('edit')) {
            return false;
        }

        if ($isduplicate == 'true') {
            return true;
        }

        // Only attempt to delete the file if there isn't an upload_id. When there is an upload_id, we just clear the
        // file metadata from the record. When there isn't an upload_id, we attempt to delete the file and clear the
        // file metadata.
        if (empty($this->upload_id) && !UploadFile::unlink_file($this->id)) {
            return false;
        }

        $this->filename = '';
        $this->file_ext = '';
        $this->file_mime_type = '';
        $this->file = '';
        $this->file_size = '';
        $this->file_source = '';
        $this->email_type = '';
        $this->email_id = '';
        $this->upload_id = '';
        if ($save) {
            $this->save();
        }
        return true;
    }

    public function get_summary_text()
    {
        return "$this->name";
    }

    public function fill_in_additional_list_fields()
    {
        $this->fill_in_additional_detail_fields();
    }

    public function fill_in_additional_detail_fields()
    {
        parent::fill_in_additional_detail_fields();

        if (!empty($this->contact_id)) {
            $emailAddress = BeanFactory::newBean('EmailAddresses');
            $this->contact_email = $emailAddress->getPrimaryAddress(false, $this->contact_id, 'Contacts');
        }
    }

    public function get_list_view_data($filter_fields = [])
    {
        $note_fields = $this->get_list_view_array();
        global $app_list_strings, $focus, $action, $currentModule, $mod_strings, $sugar_config;

        if (isset($this->parent_type)) {
            $note_fields['PARENT_MODULE'] = $this->parent_type;
        }

        if (!empty($this->filename)) {
            if (file_exists("upload://{$this->id}")) {
                $note_fields['FILENAME'] = $this->filename;
                $note_fields['FILE_URL'] = UploadFile::get_upload_url($this);
            }
        }
        if (isset($this->contact_id) && $this->contact_id != '') {
            $contact = BeanFactory::getBean('Contacts', $this->contact_id);
            if (isset($contact->id)) {
                $this->contact_name = $contact->full_name;
            }
        }
        if (isset($this->contact_name)) {
            $note_fields['CONTACT_NAME'] = $this->contact_name;
        }

        global $current_language;
        $mod_strings = return_module_language($current_language, 'Notes');
        $note_fields['STATUS'] = $mod_strings['LBL_NOTE_STATUS'];

        return $note_fields;
    }

    /**
     * Assigns message variables to email template
     *
     * @param XTemplate $xtpl Email template
     * @param Note $note Source note
     *
     * @return XTemplate
     */
    public function set_notification_body(XTemplate $xtpl, Note $note)
    {
        $xtpl->assign('NOTE_SUBJECT', $note->name);
        $xtpl->assign('NOTE_DESCRIPTION', $note->description);
        return $xtpl;
    }

    public function listviewACLHelper()
    {
        $array_assign = parent::listviewACLHelper();
        $is_owner = false;
        if (!empty($this->parent_name)) {
            if (!empty($this->parent_name_owner)) {
                global $current_user;
                $is_owner = $current_user->id == $this->parent_name_owner;
            }
        }

        if (!ACLController::moduleSupportsACL($this->parent_type) ||
            ACLController::checkAccess($this->parent_type, 'view', $is_owner)
        ) {
            $array_assign['PARENT'] = 'a';
        } else {
            $array_assign['PARENT'] = 'span';
        }

        $is_owner = false;
        if (!empty($this->contact_name)) {
            if (!empty($this->contact_name_owner)) {
                global $current_user;
                $is_owner = $current_user->id == $this->contact_name_owner;
            }
        }

        if (ACLController::checkAccess('Contacts', 'view', $is_owner)) {
            $array_assign['CONTACT'] = 'a';
        } else {
            $array_assign['CONTACT'] = 'span';
        }

        return $array_assign;
    }

    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }

    /**
     * Gets the file id under upload/ dir for this note.
     * @return string
     */
    public function getUploadId()
    {
        return !empty($this->upload_id) ? $this->upload_id : $this->id;
    }

    /**
     * Provides the dropdown list elements needed for the `entry_source`. This is
     * a system status so it should not be editable in the dropdownlist editor,
     * thus it is wrapped in a function. However, the values should be localizable
     * hence the use of labels.
     * @return array
     */
    public function getSourceTypes()
    {
        return [
            'external' => translate('LBL_SOURCE_EXTERNAL', 'Notes'),
            'internal' => translate('LBL_SOURCE_INTERNAL', 'Notes'),
        ];
    }

    /**
     * Verifies if this note has an attachment
     * @return bool
     */
    public function hasAttachment(): bool
    {
        return !empty($this->filename) && !empty($this->id) && file_exists('upload://' . $this->id);
    }

    /**
     * Gets this note's attachment information
     * @return array
     */
    public function getAttachment(): array
    {
        if ($this->hasAttachment()) {
            $mimeType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), 'upload://' . $this->id);
            return [
                'id' => $this->id,
                'filename' => $this->filename,
                'name' => $this->filename,
                'isImage' => strpos($mimeType, 'image') !== false,
            ];
        }
        return [];
    }

    /**
     * Checks if its the right parent relationship
     * @inheritdoc
     * @param string $typeField The parent field type
     * @param SugarRelationship $rel The parent relationship
     * @return bool
     */
    protected function checkParentRelationship(string $typeField, SugarRelationship $rel): bool
    {
        $relColumns = $rel->getRelationshipRoleColumns();
        // check if attachment_flag matches
        $attachmentFlagMatch = !isset($relColumns['attachment_flag']) ||
            (($relColumns['attachment_flag'] === 1 && !empty($this->attachment_flag)) ||
                ($relColumns['attachment_flag'] === 0 && empty($this->attachment_flag)));
        return isset($relColumns[$typeField]) && $attachmentFlagMatch;
    }
}
