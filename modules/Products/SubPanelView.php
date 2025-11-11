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
 * Description:
 ********************************************************************************/
class SubPanelViewProducts
{
    public $products_list = null;
    public $hideNewButton = false;
    public $hideSelectButton = false;
    public $focus;

    public function setFocus(&$value)
    {
        $this->focus = (object)$value;
    }


    public function setProductsList(&$value)
    {
        $this->products_list = $value;
    }

    public function setHideNewButton($value)
    {
        $this->hideNewButton = $value;
    }

    public function setHideSelectButton($value)
    {
        $this->hideSelectButton = $value;
    }

    public function getHeaderText($action, $currentModule)
    {
        global $app_strings;
        global $locale;
        $button = "<table cellspacing='0' cellpadding='1' border='0'><form border='0' action='index.php' method='post' name='form' id='form'>\n";
        $button .= "<input type='hidden' name='module' value='Products'>\n";
        $button .= "<input type='hidden' name='return_module' value='" . $currentModule . "'>\n";
        $button .= "<input type='hidden' name='return_action' value='" . $action . "'>\n";
        $button .= "<input type='hidden' name='return_id' value='" . $this->focus->id . "'>\n";
        $button .= "<input type='hidden' name='action'>\n";
        $button .= '<tr>';
        if (!$this->hideNewButton) {
            if ($currentModule == 'Accounts') {
                $button .= "<input type='hidden' name='account_name' value='" . urlencode($this->focus->name) . "'>\n";
                $button .= "<input type='hidden' name='account_id' value='" . $this->focus->id . "'>\n";
            } elseif ($currentModule == 'Contacts') {
                $button .= "<input type='hidden' name='account_name' value='" . urlencode($this->focus->account_name) . "'>\n";
                $button .= "<input type='hidden' name='account_id' value='" . $this->focus->account_id . "'>\n";
                $focus_name = $locale->formatName($this->focus);
                $button .= "<input type='hidden' name='contact_name' value='" . urlencode($focus_name) . "'>\n";
                $button .= "<input type='hidden' name='contact_id' value='" . $this->focus->id . "'>\n";
            }
            $button .= "<td><input title='" . $app_strings['LBL_NEW_BUTTON_TITLE'] . "' class='button' onclick=\"this.form.action.value='EditView'\" type='submit' name='button' value='  " . $app_strings['LBL_NEW_BUTTON_LABEL'] . "  '></td>\n";
        }
        if (!$this->hideSelectButton) {
            if ($currentModule == 'Accounts') {
                // TODO: will this ever get called?  accounts detailview does not have a select button for products subpanel
                $button .= "<td><input title='" . $app_strings['LBL_SELECT_BUTTON_TITLE'] . "' accessyKey='" . $app_strings['LBL_SELECT_BUTTON_KEY'] . "' type='button' class='button' value='  " . $app_strings['LBL_SELECT_BUTTON_LABEL'] . "  ' name='button' LANGUAGE=javascript onclick='window.open(\"index.php?module=Products&action=Popup&html=Popup_picker&form=DetailView&form_submit=true\",\"new\",\"width=600,height=400,resizable=1,scrollbars=1\");'></td>\n";
            } else {
                ///////////////////////////////////////
                ///
                /// SETUP PARENT POPUP

                $popup_request_data = [
                    'call_back_function' => 'set_return_and_save',
                    'form_name' => 'DetailView',
                    'field_to_name_array' => [
                        'id' => 'related_product_id',
                    ],
                ];

                $json = getJSONobj();
                $encoded_popup_request_data = $json->encode($popup_request_data);

                //
                ///////////////////////////////////////

                $button .= "<td><input title='" . $app_strings['LBL_SELECT_BUTTON_TITLE']
                    . "' accessyKey='" . $app_strings['LBL_SELECT_BUTTON_KEY']
                    . "' type='button' class='button' value='  " . $app_strings['LBL_SELECT_BUTTON_LABEL']
                    . "  ' name='button' onclick='open_popup(\"Products\", 600, 400, \"&account_id={$this->focus->account_id}&account_name=" . urlencode($this->focus->account_name) . "\", false, true, {$encoded_popup_request_data});'>\n";
                //              ."  ' name='button' onclick='window.open(\"index.php?module=Products&action=Popup&html=Popup_picker&form=DetailView&form_submit=true&query=true&account_id=".$this->focus->account_id."&account_name=".urlencode($this->focus->account_name)."\",\"new\",\"width=600,height=400,resizable=1,scrollbars=1\");'></td>\n";
            }
        }

        $button .= "</tr></form></table>\n";
        return $button;
    }
}
