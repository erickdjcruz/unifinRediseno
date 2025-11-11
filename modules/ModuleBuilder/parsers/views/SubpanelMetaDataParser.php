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

use Sugarcrm\Sugarcrm\Security\InputValidation\InputValidation;
use Sugarcrm\Sugarcrm\Security\InputValidation\Request;

require_once 'modules/ModuleBuilder/parsers/constants.php';

class SubpanelMetaDataParser extends ListLayoutMetaDataParser
{
    //@codingStandardsIgnoreStart
    public $_invisibleFields;
    //@codingStandardsIgnoreEnd
    // Columns is used by the view to construct the listview - each column is built by calling the named function
    public $columns = ['LBL_DEFAULT' => 'getDefaultFields', 'LBL_HIDDEN' => 'getAvailableFields'];
    protected $labelIdentifier = 'vname'; // labels in the subpanel defs are tagged 'vname' =>

    /**
     * @var Request
     */
    protected $request;

    /*
     * Constructor
     * Must set:
     * $this->columns   Array of 'Column LBL'=>function_to_retrieve_fields_for_this_column() - expected by the view
     *
     * @param string subpanelName   The name of this subpanel
     * @param string moduleName     The name of the module to which this subpanel belongs
     * @param string packageName    If not empty, the name of the package to which this subpanel belongs
     */
    public function __construct($subpanelName, $moduleName, $packageName = '')
    {
        $GLOBALS ['log']->debug(get_class($this) . ': __construct()');
        $this->request = InputValidation::getService();

        // TODO: check the implementations
        if (empty($packageName)) {
            $this->implementation = new DeployedSubpanelImplementation($subpanelName, $moduleName);
            //$this->originalViewDef = $this->implementation->getOriginalDefs ();
        } else {
            $this->implementation = new UndeployedSubpanelImplementation($subpanelName, $moduleName, $packageName);
        }

        $this->_viewdefs = array_change_key_case($this->implementation->getViewdefs()); // force to lower case so don't have problems with case mismatches later
        $this->_fielddefs = $this->implementation->getFielddefs();
        $this->_standardizeFieldLabels($this->_fielddefs);
        $GLOBALS['log']->debug(get_class($this) . '->__construct(): viewdefs = ' . print_r($this->_viewdefs, true));
        $GLOBALS['log']->debug(get_class($this) . '->__construct(): viewdefs = ' . print_r($this->_viewdefs, true));
        $this->_invisibleFields = $this->findInvisibleFields($this->_viewdefs);

        $GLOBALS['log']->debug(get_class($this) . '->__construct(): invisibleFields = ' . print_r($this->_invisibleFields, true));
    }

    /**
     * @inheritDoc
     */
    public function handleSave($populate = true, $clearCache = true)
    {
        if ($populate) {
            $this->_populateFromRequest();
            if (isset($_REQUEST['subpanel_title']) && isset($_REQUEST['subpanel_title_key'])) {
                $authenticatedUserLanguage = !empty($_SESSION['authenticated_user_language']) ? $_SESSION['authenticated_user_language'] : false;
                $selected_lang = !empty($_REQUEST['selected_lang']) ? $_REQUEST['selected_lang'] : $authenticatedUserLanguage;
                if (empty($selected_lang)) {
                    $selected_lang = $GLOBALS['sugar_config']['default_language'];
                }
                $labelParser = new ParserLabel($_REQUEST['view_module'], $_REQUEST ['view_package'] ?? null);
                $viewModule = $this->request->getValidInputRequest('view_module', 'Assert\ComponentName');
                $labelParser->addLabels($selected_lang, [$_REQUEST['subpanel_title_key'] => remove_xss(from_html($_REQUEST['subpanel_title']))], $viewModule);
            }
        }
        // Bug 46291 - Missing widget_class for edit_button and remove_button
        foreach ($this->_viewdefs as $key => $def) {
            if (isset($this->_fielddefs [$key] ['widget_class'])) {
                $this->_viewdefs [$key] ['widget_class'] = $this->_fielddefs [$key] ['widget_class'];
            }
        }
        $defs = $this->restoreInvisibleFields($this->_invisibleFields, $this->_viewdefs); // unlike our parent, do not force the field names back to upper case
        $defs = $this->makeRelateFieldsAsLink($defs);
        $this->implementation->deploy($defs);
    }

    /**
     * Return a list of the default fields for a subpanel
     * TODO: have this return just a list of fields, without definitions
     * @return array    List of default fields as an array, where key = value = <field name>
     */
    public function getDefaultFields()
    {
        $defaultFields = [];
        foreach ($this->_viewdefs as $key => $def) {
            if (empty($def ['usage']) || strcmp($def ['usage'], 'query_only') == 1) {
                $defaultFields [strtolower($key)] = $this->_viewdefs [$key];
            }
        }

        return $defaultFields;
    }

    /*
     * Find the query_only fields in the viewdefs
     * Query_only fields are used by the MVC to generate the subpanel but are not editable - they must be maintained in the layout
     * @param viewdefs The viewdefs to be searched for invisible fields
     * @return Array of invisible fields, ready to be provided to $this->restoreInvisibleFields
     */
    public function findInvisibleFields($viewdefs)
    {
        $invisibleFields = [];
        foreach ($viewdefs as $name => $def) {
            if (isset($def ['usage']) && ($def ['usage'] == 'query_only')) {
                $invisibleFields [$name] = $def;
            }
        }
        return $invisibleFields;
    }

    public function restoreInvisibleFields($invisibleFields, $viewdefs)
    {
        foreach ($invisibleFields as $name => $def) {
            $viewdefs [$name] = $def;
        }
        return $viewdefs;
    }

    public static function _trimFieldDefs(array $def)
    {
        $listDef = parent::_trimFieldDefs($def);
        if (isset($listDef ['label'])) {
            $listDef ['vname'] = $listDef ['label'];
            unset($listDef ['label']);
        }
        return $listDef;
    }

    /**
     * makeRelateFieldsAsLink
     * This method will go through the subpanel definition entries being saved and then apply formatting to any that are
     * relate field so that a link to the related record may be shown in the subpanel code.  This is done by adding the
     * widget_class, target_module and target_record_key deltas to the related subpanel definition entry.
     *
     * @param Array of subpanel definitions to possibly alter
     * @return $defs Array of formatted subpanel definition entries to include any relate field attributes for Subpanels
     */
    protected function makeRelateFieldsAsLink($defs)
    {
        foreach ($defs as $index => $fieldData) {
            // These checks need to pass in some way, shape or form in order to
            // make this relatable
            $typeCheck = isset($fieldData['type']) && $fieldData['type'] == 'relate';
            $linkCheck = isset($fieldData['link']) && self::isTrue($fieldData['link']);
            $reqsCheck = isset($this->_fielddefs[$index]['module']) || !empty($this->_moduleName);
            $reqsCheck = $reqsCheck && isset($this->_fielddefs[$index]['id_name']);

            if (($typeCheck || $linkCheck) && $reqsCheck) {
                $defs[$index]['widget_class'] = 'SubPanelDetailViewLink';
                $defs[$index]['target_module'] = $this->_fielddefs[$index]['module'] ?? $this->_moduleName;
                $defs[$index]['target_record_key'] = $this->_fielddefs[$index]['id_name'];
            }
        }

        return $defs;
    }
}
