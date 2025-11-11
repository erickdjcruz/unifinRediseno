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
/*********************************************************************************
 * Description: TODO:  To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
// User is used to store Forecast information.
class DocumentRevision extends SugarBean
{
    public $id;
    public $document_id;
    public $doc_id;
    public $doc_type;
    public $doc_url;
    public $date_entered;
    public $created_by;
    public $filename;
    public $file_mime_type;
    public $file_size;
    public $revision;
    public $change_log;
    public $document_name;
    public $latest_revision;
    public $file_url;
    public $file_ext;
    public $created_by_name;

    public $img_name;
    public $img_name_bare;

    public $table_name = 'document_revisions';
    public $object_name = 'DocumentRevision';
    public $module_dir = 'DocumentRevisions';
    public $new_schema = true;
    public $latest_revision_id;

    public $name;

    /*var $column_fields = Array("id"
        ,"document_id"
        ,"date_entered"
        ,"created_by"
        ,"filename"
        ,"file_mime_type"
        ,"revision"
        ,"change_log"
        ,"file_ext"
        );
*/
    public $encodeFields = [];

    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = [''];

    // This is the list of fields that are in the lists.
    public $list_fields = ['id'
        , 'document_id'
        , 'date_entered'
        , 'created_by'
        , 'filename'
        , 'file_mime_type'
        , 'revision'
        , 'file_url'
        , 'change_log'
        , 'file_ext'
        , 'created_by_name',
    ];

    public $required_fields = ['revision'];


    public function __construct()
    {
        parent::__construct();
        $this->setupCustomFields('DocumentRevisions');  //parameter is module name
        $this->disable_row_level_security = true; //no direct access to this module.
    }

    public function save($check_notify = false)
    {
        $filePath = "upload://{$this->id}";
        $this->file_size = file_exists($filePath) ? filesize($filePath) : 0;

        // For Document Revisions that have a file uploaded to the Sugar file
        // system and are set as external API document types, upload them to the
        // external API
        if (!empty($this->doc_type) && $this->doc_type !== 'Sugar' && file_exists($filePath)) {
            $this->syncWithExternalApi();
        }

        $saveRet = parent::save($check_notify);

        //update documents table. (not through save, because it causes a loop)
        // If we don't have a document_id, find it.
        if (empty($this->document_id)) {
            $query = "SELECT document_id FROM document_revisions WHERE id = '" . $this->db->quote($this->id) . "'";
            $ret = $this->db->query($query, true);
            $row = $this->db->fetchByAssoc($ret);
            $this->document_id = $row['document_id'];
        }
        $query = "UPDATE documents set document_revision_id='" . $this->db->quote($this->id) . "', doc_type='" . $this->db->quote($this->doc_type) . "', doc_url='" . $this->db->quote($this->doc_url) . "', doc_id='" . $this->db->quote($this->doc_id) . "' where id = '" . $this->db->quote($this->document_id) . "'";
        $this->db->query($query, true);

        return $saveRet;
    }

    /**
     * Syncs a file uploaded to Sugar's filesystem to the external API indicated
     * by the doc_type of the DocumentRevision.
     *
     * The effect of this is that the file will be uploaded to the external API,
     * and this DocumentRevision will store a link to the URL of the external
     * resource, rather than a link to the file on the filesystem. If successful,
     * the original file will be deleted from the filesystem.
     */
    protected function syncWithExternalApi()
    {
        $upload_file = new UploadFile('filename_file');
        $upload_file->upload_doc(
            $this,
            $this->id,
            $this->doc_type,
            $this->filename,
            $this->file_mime_type
        );
    }

    public function get_summary_text()
    {
        return "$this->filename";
    }

    public function retrieve($id = '-1', $encode = false, $deleted = true)
    {
        $ret = parent::retrieve($id, $encode, $deleted);

        return $ret;
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
        parent::fill_in_additional_detail_fields();

        if (empty($this->id) && empty($this->document_id) && isset($_REQUEST['return_id']) && !empty($_REQUEST['return_id'])) {
            $this->document_id = $_REQUEST['return_id'];
        }

        //find the document name and current version.
        $query = 'SELECT document_name, revision, document_revision_id 
                  FROM documents, document_revisions WHERE documents.id = ? 
                  AND document_revisions.id = documents.document_revision_id';
        $conn = $this->db->getConnection();
        $stmt = $conn->executeQuery($query, [$this->document_id]);
        $row = $stmt->fetchAssociative();
        if ($row != null) {
            $this->document_name = $row['document_name'];
            $this->name = $this->document_name;
            $this->latest_revision = $row['revision'];
            $this->latest_revision_id = $row['document_revision_id'];

            if (empty($this->revision)) {
                $this->revision = (int)$this->latest_revision + 1;
            }
        }
    }

    /**
     * Returns a filename based off of the logical (Sugar-side) Document name and combined with the revision. Tailor
     * this to needs created by email RFCs, filesystem name conventions, charset conventions etc.
     * @param string revId Revision ID if not latest
     * @return string formatted name
     */
    public function getDocumentRevisionNameForDisplay($revId = '')
    {
        global $sugar_config;
        global $current_language;

        $localLabels = return_module_language($current_language, 'DocumentRevisions');

        // prep - get source Document
        $document = BeanFactory::newBean('Documents');

        // use passed revision ID
        if (!empty($revId)) {
            $tempDoc = BeanFactory::getBean('DocumentRevisions', $revId);
        } else {
            $tempDoc = $this;
        }

        // get logical name
        $document->retrieve($tempDoc->document_id);
        $logicalName = $document->document_name;

        // get revision string
        $revString = '';
        if (!empty($tempDoc->revision)) {
            $revString = "-{$localLabels['LBL_REVISION']}_{$tempDoc->revision}";
        }

        // get extension
        $realFilename = $tempDoc->filename;
        $fileExtension_beg = strrpos($realFilename, '.');
        $fileExtension = '';

        if ($fileExtension_beg > 0) {
            $fileExtension = substr($realFilename, $fileExtension_beg + 1);
        }
        //check to see if this is a file with extension located in "badext"
        foreach ($sugar_config['upload_badext'] as $badExt) {
            if (strtolower($fileExtension) == strtolower($badExt)) {
                //if found, then append with .txt to filename and break out of lookup
                //this will make sure that the file goes out with right extension, but is stored
                //as a text in db.
                $fileExtension .= '.txt';
                break; // no need to look for more
            }
        }
        $fileExtension = '.' . $fileExtension;

        $return = $logicalName . $revString . $fileExtension;

        // apply RFC limitations here
        if (mb_strlen($return) > 1024) {
            // do something if we find a real RFC issue
        }

        return $return;
    }


    public function list_view_parse_additional_sections(&$list_form)
    {
        return $list_form;
    }

    public function get_list_view_data($filter_fields = [])
    {
        $forecast_fields = [];
        $revision_fields = $this->get_list_view_array();

        $forecast_fields['FILE_URL'] = $this->file_url;
        return $revision_fields;
    }

    public static function get_document_revisions($doc_id)
    {
        $return_array = [];
        if (empty($doc_id)) {
            return $return_array;
        }

        $db = DBManagerFactory::getInstance();
        $result = $db->getConnection()
            ->executeQuery(
                'SELECT id, revision FROM document_revisions WHERE document_id=? AND deleted=0',
                [$doc_id]
            );
        foreach ($result->iterateAssociative() as $row) {
            $return_array[$row['id']] = $row['revision'];
        }
        return $return_array;
    }

    /**
     * {@inheritdoc}
     *
     * @uses UploadFile::unlink_file()
     */
    public function mark_deleted($id)
    {
        UploadFile::unlink_file($id);
        parent::mark_deleted($id);
    }
}

require_once 'modules/Documents/DocumentExternalApiDropDown.php';
