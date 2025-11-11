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
 * Description: Controller for the Import module
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 ********************************************************************************/

require_once 'modules/Import/Forms.php';

use Sugarcrm\Sugarcrm\Security\InputValidation\InputValidation;
use Sugarcrm\Sugarcrm\Security\InputValidation\Request;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\IdmModeLimitationTrait;

class ImportController extends SugarController
{
    use IdmModeLimitationTrait;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $importModule;

    public function __construct()
    {
        $this->request = InputValidation::getService();
    }

    /**
     * @see SugarController::loadBean()
     */
    public function loadBean()
    {
        global $mod_strings;

        if (!isset($_REQUEST['import_module'])) {
            return; // there is no module to load
        }

        $this->importModule = $_REQUEST['import_module'];

        $this->bean = BeanFactory::newBean($this->importModule);
        if ($this->bean) {
            if (!$this->bean->importable) {
                $this->bean = false;
            } elseif ($_REQUEST['import_module'] == 'Users' && !is_admin($GLOBALS['current_user'])) {
                $this->bean = false;
            } elseif ($this->bean->bean_implements('ACL')) {
                if (!ACLController::checkAccess($this->bean->module_dir, 'import', true)) {
                    ACLController::displayNoAccess();
                    sugar_cleanup(true);
                }
            }
        }

        if (!$this->bean && $this->importModule != 'Administration') {
            $_REQUEST['message'] = $mod_strings['LBL_ERROR_IMPORTS_NOT_SET_UP'];
            $this->view = 'error';
            if (!isset($_REQUEST['import_map_id']) && !isset($_REQUEST['delete_map_id'])) {
                $this->_processed = true;
            }
        } else {
            $GLOBALS['FOCUS'] = $this->bean;
        }
    }

    public function action_index()
    {
        $this->action_Step1();
    }

    public function action_mapping()
    {
        global $mod_strings, $current_user;
        $results = ['message' => ''];
        // handle publishing and deleting import maps
        if (isset($_REQUEST['delete_map_id'])) {
            $import_map = BeanFactory::deleteBean('Import_1', $_REQUEST['delete_map_id']);
        }

        if (isset($_REQUEST['publish'])) {
            $import_map = BeanFactory::getBean('Import_1', $_REQUEST['import_map_id'], ['encode' => false]);

            if ($_REQUEST['publish'] == 'yes') {
                $result = $import_map->mark_published($current_user->id, true);
                if (!$result) {
                    $results['message'] = $mod_strings['LBL_ERROR_UNABLE_TO_PUBLISH'];
                }
            } elseif ($_REQUEST['publish'] == 'no') {
                // if you don't own this importmap, you do now, unless you have a map by the same name
                $result = $import_map->mark_published($current_user->id, false);
                if (!$result) {
                    $results['message'] = $mod_strings['LBL_ERROR_UNABLE_TO_UNPUBLISH'];
                }
            }
        }

        echo json_encode($results);
        sugar_cleanup(true);
    }

    public function action_RefreshMapping()
    {
        global $mod_strings;
        $v = new ImportViewConfirm();
        $fileName = $this->request->getValidInputRequest('importFile', null, '');
        $delim = $v->getDelimiterValue();
        $enclosure = $_REQUEST['qualif'];
        $enclosure = html_entity_decode($enclosure, ENT_QUOTES);
        $hasHeader = isset($_REQUEST['header']) && !empty($_REQUEST['header']) ? true : false;

        $importFile = new ImportFile($fileName, $delim, $enclosure, false);
        $importFile->setHeaderRow($hasHeader);
        $rows = $v->getSampleSet($importFile);

        $ss = new Sugar_Smarty();
        $ss->assign('SAMPLE_ROWS', $rows);
        $ss->assign('HAS_HEADER', $hasHeader);
        $ss->assign('column_count', $v->getMaxColumnsInSampleSet($rows));
        $ss->assign('MOD', $mod_strings);
        $ss->display('modules/Import/tpls/confirm_table.tpl');
        sugar_cleanup(true);
    }

    public function action_RefreshTable()
    {
        $offset = $this->request->getValidInputRequest('offset', [
            'Assert\Type' => ['type' => 'numeric'],
            'Assert\Range' => ['min' => 0],
        ], 0);

        $tableID = $this->request->getValidInputRequest('tableID', ['Assert\Choice' => ['choices' => ['errors', 'dup']]]);

        $has_header = $_REQUEST['has_header'] == 'on' ? true : false;
        if ($tableID == 'dup') {
            $tableFilename = ImportCacheFiles::getDuplicateFileDisplayName();
        } else {
            $tableFilename = ImportCacheFiles::getErrorRecordsFileName();
        }

        $if = new ImportFile($tableFilename, ',', '"', false, false);
        $if->setHeaderRow($has_header);
        $lv = new ImportListView($if, ['offset' => $offset], $tableID);
        $lv->display(false);

        sugar_cleanup(true);
    }

    public function action_Step1()
    {
        $fromAdminView = $_REQUEST['from_admin_wizard'] ?? false;
        if ($this->importModule == 'Administration' || $fromAdminView
            || $this->bean instanceof Person
        ) {
            if ($this->isLimitedForModuleInIdmMode($this->importModule) && !$fromAdminView) {
                $this->view = 'step2';
            } else {
                $this->view = 'step1';
            }
        } else {
            $this->view = 'step2';
        }
    }

    public function action_Step2()
    {
        $this->view = 'step2';
    }

    public function action_Confirm()
    {
        $this->view = 'confirm';
    }

    public function action_Step3()
    {
        $this->view = 'step3';
    }

    public function action_DupCheck()
    {
        $this->view = 'dupcheck';
    }

    public function action_Step4()
    {
        $this->view = 'step4';
    }

    public function action_Last()
    {
        $this->view = 'last';
    }

    public function action_Undo()
    {
        $this->view = 'undo';
    }

    public function action_Error()
    {
        $this->view = 'error';
    }

    public function action_ExtStep1()
    {
        $this->view = 'extStep1';
    }

    public function action_Extdupcheck()
    {
        $this->view = 'extdupcheck';
    }

    public function action_Extimport()
    {
        $this->view = 'extimport';
    }

    public function action_GetControl()
    {
        $module = $this->request->getValidInputRequest('import_module', 'Assert\Mvc\ModuleName');
        $fieldName = $this->request->getValidInputRequest('field_name');
        echo getControl($module, $fieldName, null, '', ['idName' => "default_value_$fieldName"]);
        exit;
    }

    public function action_AuthenticatedSources()
    {
        $this->view = 'authenticatedsources';
    }

    public function action_RevokeAccess()
    {
        $this->view = 'revokeaccess';
    }

    /**
     * show disabled page
     */
    protected function showDisabledPage()
    {
        global $mod_strings;
        $ss = new Sugar_Smarty();
        $ss->assign('MOD', $mod_strings);
        $ss->display('modules/Import/tpls/disabled.tpl');
        sugar_cleanup(true);
    }
}
