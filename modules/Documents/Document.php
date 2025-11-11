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


// User is used to store Forecast information.
class Document extends SugarBean
{
    public $id;
    public $document_name;
    public $description;
    public $category_id;
    public $subcategory_id;
    public $status_id;
    public $status;
    public $created_by;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $assigned_user_id;
    public $team_id;
    public $active_date;
    public $exp_date;
    public $document_revision_id;
    public $filename;
    public $doc_type;

    public $img_name;
    public $img_name_bare;
    public $related_doc_id;
    public $related_doc_name;
    public $related_doc_rev_id;
    public $related_doc_rev_number;
    public $is_template;
    public $template_type;

    //additional fields.
    public $revision;
    public $last_rev_create_date;
    public $last_rev_created_by;
    public $last_rev_created_name;
    public $file_url;
    public $file_url_noimage;

    public $table_name = 'documents';
    public $object_name = 'Document';
    public $user_preferences;

    public $encodeFields = [];

    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = ['revision'];

    public $new_schema = true;
    public $module_dir = 'Documents';

    public $relationship_fields = [
        'contract_id' => 'contracts',
    ];


    public function __construct()
    {
        parent::__construct();
        $this->setupCustomFields('Documents'); //parameter is module name
        $this->disable_row_level_security = false;
    }

    /**
     * {@inheritDoc}
     */
    public function populateFromRow(array $row, $convert = false, $getMoreData = true)
    {
        $row = parent::populateFromRow($row, $convert);

        if (!empty($this->document_name) && empty($this->name)) {
            $this->name = $this->document_name;
        }

        return $row;
    }

    /**
     * Create a revision bean for current document
     * @return DocumentRevision
     */
    public function createRevisionBean()
    {
        $Revision = BeanFactory::newBean('DocumentRevisions');
        //save revision.
        $Revision->in_workflow = true;
        $Revision->not_use_rel_in_req = true;
        $Revision->new_rel_id = $this->id;
        $Revision->new_rel_relname = 'Documents';
        $Revision->change_log = translate('DEF_CREATE_LOG', 'Documents');
        $Revision->revision = $this->revision;
        $Revision->document_id = $this->id;
        $Revision->filename = $this->filename;

        if (isset($this->file_ext)) {
            $Revision->file_ext = $this->file_ext;
        }

        if (isset($this->file_mime_type)) {
            $Revision->file_mime_type = $this->file_mime_type;
        }

        $Revision->doc_type = $this->doc_type;
        if (isset($this->doc_id)) {
            $Revision->doc_id = $this->doc_id;
        }
        if (isset($this->doc_url)) {
            $Revision->doc_url = $this->doc_url;
        }

        $Revision->id = create_guid();
        $Revision->new_with_id = true;
        return $Revision;
    }

    public function save($check_notify = false)
    {
        $save_revision = [];
        if (empty($this->document_name) && !empty($this->name)) {
            $this->document_name = $this->name;
        }

        if (empty($this->doc_type)) {
            $this->doc_type = 'Sugar';
        }
        if (empty($this->id) || $this->new_with_id) {
            if (empty($this->id)) {
                $this->id = create_guid();
                $this->new_with_id = true;
            }

            if (isset($_REQUEST) && isset($_REQUEST['filename_duplicateBeanId'])) {
                $isDuplicate = true;
            } else {
                $isDuplicate = false;
            }

            $Revision = $this->createRevisionBean();

            $createRevision = false;
            //Move file saved during populatefrompost to match the revision id rather than document id
            if (isset($this->filename) && file_exists("upload://{$this->id}")) {
                rename("upload://{$this->id}", "upload://{$Revision->id}");
                $createRevision = true;
            } elseif ($isDuplicate && (empty($this->doc_type) || $this->doc_type == 'Sugar')) {
                // Looks like we need to duplicate a file, this is tricky
                $oldDocument = BeanFactory::getBean('Documents', $_REQUEST['duplicateId']);
                $old_name = "upload://{$oldDocument->document_revision_id}";
                $new_name = "upload://{$Revision->id}";
                $GLOBALS['log']->debug("Attempting to copy from $old_name to $new_name");
                copy($old_name, $new_name);
                $createRevision = true;
            }

            // For external documents, we just need to make sure we have a doc_id
            if (!empty($this->doc_id) && $this->doc_type != 'Sugar') {
                $createRevision = true;
            }

            if ($createRevision) {
                $Revision->save();
                //update document with latest revision id
                $this->process_save_dates = false; //make sure that conversion does not happen again.
                $this->document_revision_id = $Revision->id;
            }


            //set relationship field values if contract_id is passed (via subpanel create)
            if (!empty($_POST['contract_id'])) {
                $save_revision['document_revision_id'] = $this->document_revision_id;
                $this->load_relationship('contracts');
                $this->contracts->add($_POST['contract_id'], $save_revision);
            }

            if ((isset($_POST['load_signed_id']) and !empty($_POST['load_signed_id']))) {
                $query = 'update linked_documents set deleted=1 where id=' . $this->db->quoted($_POST['load_signed_id']);
                $this->db->query($query);
            }
        }

        return parent::save($check_notify);
    }

    public function get_summary_text()
    {
        return "$this->document_name";
    }

    public function is_authenticated()
    {
        return $this->authenticated;
    }

    public function fill_in_additional_list_fields()
    {
        $this->fill_in_additional_detail_fields();
    }

    public function fill_in_additional_detail_fields()
    {
        global $app_list_strings;
        global $current_language;
        global $img_name;
        global $img_name_bare;
        global $timedate;

        parent::fill_in_additional_detail_fields();

        $mod_strings = return_module_language($current_language, 'Documents');
        $revisions = $this->get_linked_beans('latest_document_revision_link', 'DocumentRevision');

        if (!empty($revisions)) {
            $latestRevision = $revisions[0];
            if (isset($this->document_name)) {
                $this->name = $this->document_name;
            }
            $this->filename = $latestRevision->filename;

            $this->revision = $latestRevision->revision;

            //image is selected based on the extension name <ext>_icon_inline, extension is stored in document_revisions.
            //if file is not found then default image file will be used.

            if (!empty($latestRevision->file_ext)) {
                $img_name = SugarThemeRegistry::current()->getImageURL(
                    strtolower($latestRevision->file_ext) . '_image_inline.gif'
                );
                $img_name_bare = strtolower($latestRevision->file_ext) . '_image_inline';
            }
            $this->last_rev_created_name = $latestRevision->created_by_name;
            $this->last_rev_create_date = $timedate->to_display_date_time(
                $this->db->fromConvert($latestRevision->date_entered, 'datetime')
            );
            $this->last_rev_mime_type = $latestRevision->file_mime_type;
        }

        //set default file name.
        if (!empty($img_name) && file_exists($img_name)) {
            $img_name = $img_name_bare;
        } else {
            $img_name = 'def_image_inline'; //todo change the default image.
        }
        if ($this->ACLAccess('DetailView')) {
            $params = [
                'entryPoint' => 'download',
                'type' => 'Documents',
                'id' => $this->document_revision_id,
            ];

            if (!empty($this->doc_type) && $this->doc_type != 'Sugar' && !empty($this->doc_url)) {
                $imgTag = SugarThemeRegistry::current()->getImage(
                    $this->doc_type . '_image_inline',
                    'border="0"',
                    null,
                    null,
                    '.png',
                    $mod_strings['LBL_LIST_VIEW_DOCUMENT']
                );
                $file_url = sprintf(
                    '<a href="%s" target="_blank">%s</a>',
                    htmlspecialchars($this->doc_url, ENT_QUOTES, 'UTF-8'),
                    $imgTag
                );
            } else {
                $href = 'index.php?' . http_build_query($params);
                $imgTag = SugarThemeRegistry::current()->getImage(
                    $img_name,
                    'border="0"',
                    null,
                    null,
                    '.gif',
                    $mod_strings['LBL_LIST_VIEW_DOCUMENT']
                );
                $file_url = sprintf(
                    '<a href="%s" target="_blank">%s</a>',
                    htmlspecialchars($href, ENT_QUOTES, 'UTF-8'),
                    $imgTag
                );
            }

            $this->file_url = $file_url;
            $this->file_url_noimage = 'index.php?' . http_build_query($params);
        } else {
            $this->file_url = '';
            $this->file_url_noimage = '';
        }

        if (!empty($this->status_id)) {
            $this->status = $app_list_strings['document_status_dom'][$this->status_id];
        }
    }

    public function list_view_parse_additional_sections(&$list_form)
    {
        return $list_form;
    }

    public function get_list_view_data($filter_fields = [])
    {
        global $current_language;
        $app_list_strings = return_app_list_strings_language($current_language);

        $document_fields = $this->get_list_view_array();

        $this->fill_in_additional_list_fields();


        $document_fields['FILENAME'] = $this->filename;
        $document_fields['FILE_URL'] = $this->file_url;
        $document_fields['FILE_URL_NOIMAGE'] = $this->file_url_noimage;
        $document_fields['LAST_REV_CREATED_BY'] = $this->last_rev_created_name;

        $category_id_key = $this->field_defs['category_id']['options'] ?? 'document_category_dom';

        $document_fields['CATEGORY_ID'] = empty($this->category_id) ? '' :
            $app_list_strings[$category_id_key][$this->category_id];

        $subcategory_id_key = $this->field_defs['subcategory_id']['options'] ?? 'document_subcategory_dom';

        $document_fields['SUBCATEGORY_ID'] = empty($this->subcategory_id) ? '' :
            $app_list_strings[$subcategory_id_key][$this->subcategory_id];

        $document_fields['NAME'] = $this->document_name;
        $document_fields['DOCUMENT_NAME_JAVASCRIPT'] = $GLOBALS['db']->quote($document_fields['DOCUMENT_NAME']);
        return $document_fields;
    }


    /**
     * mark_relationships_deleted
     *
     * Override method from SugarBean to handle deleting relationships associated with a Document.  This method will
     * remove DocumentRevision relationships and then optionally delete Contracts depending on the version.
     *
     * @param $id String The record id of the Document instance
     */
    public function mark_relationships_deleted($id)
    {
        $this->load_relationships();
        $revisions = $this->get_linked_beans('revisions', 'DocumentRevision');

        if (!empty($revisions) && is_array($revisions)) {
            foreach ($revisions as $key => $version) {
                UploadFile::unlink_file($version->id, $version->filename);
                //mark the version deleted.
                $version->mark_deleted($version->id);
            }
        }

        //Remove the contracts relationships
        $this->load_relationship('contracts');
        if (!empty($this->contracts)) {
            $this->contracts->delete($id);
        }
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
     * Document specific file name getter
     *
     * @return string
     */
    public function getFileName()
    {
        if (empty($this->id)) {
            return '';
        }

        // Documents store their file information in DocumentRevisions
        $revision = BeanFactory::getBean('DocumentRevisions', $this->id);

        // Check if the id was for a revision
        if (!empty($revision)) {
            return $revision->filename;
        } else {
            // The id is not a revision id, try the actual document revision id
            $revision = BeanFactory::getBean('DocumentRevisions', $this->document_revision_id);

            if ($revision) {
                return $revision->filename;
            }
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public function getRecordName()
    {
        return isset($this->document_name) ? trim($this->document_name) : '';
    }
}

require_once 'modules/Documents/DocumentExternalApiDropDown.php';
