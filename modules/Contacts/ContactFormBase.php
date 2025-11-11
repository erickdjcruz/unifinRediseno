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
 * Description:  Base form for contact
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/


use Sugarcrm\Sugarcrm\Security\InputValidation\InputValidation;

class ContactFormBase extends PersonFormBase
{
    public $bean;
    public $moduleName = 'Contacts';
    public $objectName = 'Contact';

    /**
     * getDuplicateQuery
     *
     * This function returns the SQL String used for initial duplicate Contacts check
     *
     * @param $focus sugarbean
     * @param $prefix String value of prefix that may be present in $_POST variables
     * @return string The query that should be used for the initial duplicate lookup check
     * @see checkForDuplicates (method), ContactFormBase.php, LeadFormBase.php, ProspectFormBase.php
     */
    public function getDuplicateQuery($focus, $prefix = '')
    {
        $db = DBManagerFactory::getInstance();
        $query = 'SELECT contacts.id, contacts.first_name, contacts.last_name, contacts.title FROM contacts ';

        // Bug #46427 : Records from other Teams shown on Potential Duplicate Contacts screen during Lead Conversion
        // add team security
        if (!empty($focus) && !$focus->disable_row_level_security) {
            $focus->add_team_security_where_clause($query);
        }

        $query .= ' where contacts.deleted = 0 AND ';

        if (isset($_POST[$prefix . 'first_name']) && strlen($_POST[$prefix . 'first_name']) != 0 && isset($_POST[$prefix . 'last_name']) && strlen($_POST[$prefix . 'last_name']) != 0) {
            $query .= sprintf(
                ' contacts.first_name LIKE %s AND contacts.last_name = %s',
                $db->quoted($_POST[$prefix . 'first_name'] . '%'),
                $db->quoted($_POST[$prefix . 'last_name'])
            );
        } else {
            $query .= sprintf(
                ' contacts.last_name = %s',
                $db->quoted($_POST[$prefix . 'last_name'])
            );
        }

        if (!empty($_POST[$prefix . 'record'])) {
            $query .= sprintf(
                ' AND contacts.id != %s',
                $db->quoted($_POST[$prefix . 'record'])
            );
        }

        return $query;
    }

    public function getFormBody($prefix, $mod = '', $formname = '')
    {
        if (!ACLController::checkAccess('Contacts', 'edit', true)) {
            return '';
        }
        global $mod_strings;
        $temp_strings = $mod_strings;
        if (!empty($mod)) {
            global $current_language;
            $mod_strings = return_module_language($current_language, $mod);
        }
        global $app_strings;
        global $current_user;
        $lbl_required_symbol = $app_strings['LBL_REQUIRED_SYMBOL'];
        $lbl_first_name = $mod_strings['LBL_FIRST_NAME'];
        $lbl_last_name = $mod_strings['LBL_LAST_NAME'];
        $lbl_phone = $mod_strings['LBL_PHONE'];
        $user_id = $current_user->id;
        $lbl_email_address = $mod_strings['LBL_EMAIL_ADDRESS'];
        if ($formname == 'EmailEditView') {
            $form = <<<EOQ
		<input type="hidden" name="{$prefix}record" value="">
		<input type="hidden" name="{$prefix}email2" value="">
		<input type="hidden" name="{$prefix}phone_work" value="">
		<input type="hidden" name="{$prefix}assigned_user_id" value='{$user_id}'>
		$lbl_first_name<br>
		<input name="{$prefix}first_name" type="text" value="" size=10><br>
		$lbl_last_name&nbsp;<span class="required">$lbl_required_symbol</span><br>
		<input name='{$prefix}last_name' type="text" value="" size=10><br>
		$lbl_email_address&nbsp;<span class="required">$lbl_required_symbol</span><br>
		<input name='{$prefix}email1' type="text" value=""><br><br>

EOQ;
        } else {
            $form = <<<EOQ
		<input type="hidden" name="{$prefix}record" value="">
		<input type="hidden" name="{$prefix}email2" value="">
		<input type="hidden" name="{$prefix}assigned_user_id" value='{$user_id}'>
		$lbl_first_name<br>
		<input name="{$prefix}first_name" type="text" value=""><br>
		$lbl_last_name&nbsp;<span class="required">$lbl_required_symbol</span><br>
		<input name='{$prefix}last_name' type="text" value=""><br>
		$lbl_phone<br>
		<input name='{$prefix}phone_work' type="text" value=""><br>
		$lbl_email_address<br>
		<input name='{$prefix}email1' type="text" value=""><br><br>

EOQ;
        }


        $javascript = new javascript();
        $javascript->setFormName($formname);
        $javascript->setSugarBean($this->getContact());
        $javascript->addField('email1', 'false', $prefix);
        $javascript->addRequiredFields($prefix);

        $form .= $javascript->getScript();
        $mod_strings = $temp_strings;
        return $form;
    }

    public function getForm($prefix, $mod = '')
    {
        if (!ACLController::checkAccess('Contacts', 'edit', true)) {
            return '';
        }
        if (!empty($mod)) {
            global $current_language;
            $mod_strings = return_module_language($current_language, $mod);
        } else {
            global $mod_strings;
        }
        global $app_strings;

        $lbl_save_button_title = $app_strings['LBL_SAVE_BUTTON_TITLE'];
        $lbl_save_button_key = $app_strings['LBL_SAVE_BUTTON_KEY'];
        $lbl_save_button_label = $app_strings['LBL_SAVE_BUTTON_LABEL'];


        $the_form = get_left_form_header($mod_strings['LBL_NEW_FORM_TITLE']);
        $the_form .= <<<EOQ

		<form name="{$prefix}ContactSave" onSubmit="return check_form('{$prefix}ContactSave')" method="POST" action="index.php">
			<input type="hidden" name="{$prefix}module" value="Contacts">
			<input type="hidden" name="{$prefix}action" value="Save">
EOQ;
        $the_form .= $this->getFormBody($prefix, 'Contacts', "{$prefix}ContactSave");
        $the_form .= <<<EOQ
		<input title="$lbl_save_button_title" accessKey="$lbl_save_button_key" class="button" type="submit" name="{$prefix}button" value="  $lbl_save_button_label  " >
		</form>

EOQ;
        $the_form .= get_left_form_footer();
        $the_form .= get_validate_record_js();

        return $the_form;
    }

// FIXME TY-986: decide if/how we're going to deprecate this
    public function handleSave($prefix, $redirect = true, $useRequired = false)
    {
        global $log;
        $log->deprecated('ContactFormBase::handleSave() is deprecated since 7.0.0.');

        global $theme, $current_user;

        require_once 'include/formbase.php';

        global $timedate;

        $focus = $this->getContact();

        if ($useRequired && !checkRequired($prefix, array_keys($focus->required_fields))) {
            return null;
        }

        if (!empty($_POST[$prefix . 'new_reports_to_id'])) {
            $focus->retrieve($_POST[$prefix . 'new_reports_to_id']);
            $focus->reports_to_id = $_POST[$prefix . 'record'];
        } else {
            $focus = populateFromPost($prefix, $focus);
            $oldPassword = null;

            if (isset($focus->id)) {
                $contact = BeanFactory::getBean('Contacts', $focus->id);
                $oldPassword = $contact->portal_password;
            }

            // update password
            if (!empty($focus->portal_password) && $focus->portal_password != $oldPassword && $focus->portal_password != 'value_setvalue_setvalue_set') {
                $focus->portal_password = User::getPasswordHash($focus->portal_password);
                // clear password
            } elseif (empty($focus->portal_password)) {
                $focus->portal_password = null;
                // keep existing password
            } else {
                $focus->portal_password = $oldPassword;
            }

            if (!isset($_POST[$prefix . 'email_opt_out'])) {
                $focus->email_opt_out = 0;
            }
            if (!isset($_POST[$prefix . 'do_not_call'])) {
                $focus->do_not_call = 0;
            }
        }
        if (!$focus->ACLAccess('Save')) {
            ACLController::displayNoAccess(true);
            sugar_cleanup(true);
        }
        if ($_REQUEST['action'] != 'BusinessCard' && $_REQUEST['action'] != 'ConvertProspect') {
            if (isset($_POST[$prefix . 'sync_contact'])) {
                $focus->sync_contact = $_POST[$prefix . 'sync_contact'];
            }
        }

        if (isset($GLOBALS['check_notify'])) {
            $check_notify = $GLOBALS['check_notify'];
        } else {
            $check_notify = false;
        }

        $post = InputValidation::getService();
        $record = $post->getValidInputPost('record', 'Assert\Guid');
        $dupCheck = $post->getValidInputPost('dup_checked');
        $inboundEmailId = $post->getValidInputPost('inbound_email_id', 'Assert\Guid');
        $relateTo = $post->getValidInputPost('relate_to', 'Assert\ComponentName');
        $relateId = $post->getValidInputPost('relate_id', 'Assert\Guid');

        if (!empty($record) && !empty($dupCheck)) {
            $duplicateContacts = $this->checkForDuplicates($prefix);
            if (isset($duplicateContacts)) {
                $location = 'module=Contacts&action=ShowDuplicates';
                $get = '';
                if (!empty($inboundEmailId)) {
                    $get .= '&inbound_email_id=' . $inboundEmailId;
                }
                // Bug 25311 - Add special handling for when the form specifies many-to-many relationships
                if (!empty($relateTo)) {
                    $get .= '&Contactsrelate_to=' . $relateTo;
                }
                if (!empty($relateId)) {
                    $get .= '&Contactsrelate_id=' . $relateId;
                }

                //add all of the post fields to redirect get string
                foreach ($focus->column_fields as $field) {
                    if (!empty($focus->$field) && !is_object($focus->$field)) {
                        $get .= "&Contacts$field=" . urlencode($focus->$field);
                    }
                }

                foreach ($focus->additional_column_fields as $field) {
                    if (!empty($focus->$field)) {
                        $get .= "&Contacts$field=" . urlencode($focus->$field);
                    }
                }

                if ($focus->hasCustomFields()) {
                    foreach ($focus->field_defs as $name => $field) {
                        if (!empty($field['source']) && $field['source'] == 'custom_fields') {
                            $get .= "&Contacts$name=" . urlencode($focus->$name);
                        }
                    }
                }


                $emailAddress = BeanFactory::newBean('EmailAddresses');
                $get .= $emailAddress->getFormBaseURL($focus);

                $get .= get_teams_url('Contacts');

                //create list of suspected duplicate contact id's in redirect get string
                $i = 0;
                foreach ($duplicateContacts as $contact) {
                    $get .= "&duplicate[$i]=" . $contact['id'];
                    $i++;
                }

                //add return_module, return_action, and return_id to redirect get string
                $urlData = ['return_module' => 'Contacts', 'return_action' => ''];
                foreach (['return_module', 'return_action', 'return_id', 'popup', 'create', 'start'] as $var) {
                    if (!empty($_POST[$var])) {
                        $urlData[$var] = $_POST[$var];
                    }
                }
                $get .= '&' . http_build_query($urlData);
                $_SESSION['SHOW_DUPLICATES'] = $get;

                //now redirect the post to modules/Contacts/ShowDuplicates.php
                if (!empty($_POST['is_ajax_call']) && $_POST['is_ajax_call'] == '1') {
                    ob_clean();
                    $json = getJSONobj();
                    echo $json->encode(['status' => 'dupe', 'get' => $location]);
                } elseif (!empty($_REQUEST['ajax_load'])) {
                    echo "<script>SUGAR.ajaxUI.loadContent('index.php?$location');</script>";
                } else {
                    if (!empty($_POST['to_pdf'])) {
                        $location .= '&to_pdf=' . urlencode($_POST['to_pdf']);
                    }
                    header("Location: index.php?$location");
                }
                return null;
            }
        }


        ///////////////////////////////////////////////////////////////////////////////
        ////	INBOUND EMAIL HANDLING
        ///////////////////////////////////////////////////////////////////////////////
        if (isset($_REQUEST['inbound_email_id']) && !empty($_REQUEST['inbound_email_id'])) {
            // fake this case like it's already saved.
            $focus->save($check_notify);

            $email = BeanFactory::getBean('Emails', $_REQUEST['inbound_email_id']);
            $email->parent_type = 'Contacts';
            $email->parent_id = $focus->id;
            $email->assigned_user_id = $current_user->id;
            $email->status = 'read';
            $email->save();
            $email->load_relationship('contacts');
            $email->contacts->add($focus->id);

            header('Location: index.php?&module=Emails&action=EditView&type=out&inbound_email_id=' . $_REQUEST['inbound_email_id'] . '&parent_id=' . $email->parent_id . '&parent_type=' . $email->parent_type . '&start=' . $_REQUEST['start'] . '&assigned_user_id=' . $current_user->id);
            exit();
        }
        ////	END INBOUND EMAIL HANDLING
        ///////////////////////////////////////////////////////////////////////////////

        $focus->save($check_notify);
        $return_id = $focus->id;

        $GLOBALS['log']->debug('Saved record with id of ' . $return_id);

        if ($redirect && !empty($_POST['is_ajax_call']) && $_POST['is_ajax_call'] == '1') {
            $json = getJSONobj();
            echo $json->encode(['status' => 'success',
                'get' => '']);
            $trackerManager = TrackerManager::getInstance();
            $timeStamp = TimeDate::getInstance()->nowDb();
            if ($monitor = $trackerManager->getMonitor('tracker')) {
                $monitor->setValue('team_id', $GLOBALS['current_user']->getPrivateTeamID());
                $monitor->setValue('action', 'detailview');
                $monitor->setValue('user_id', $GLOBALS['current_user']->id);
                $monitor->setValue('module_name', 'Contacts');
                $monitor->setValue('date_modified', $timeStamp);
                $monitor->setValue('visible', 1);

                if (!empty($this->bean->id)) {
                    $monitor->setValue('item_id', $return_id);
                    $monitor->setValue('item_summary', $focus->get_summary_text());
                }
                $trackerManager->saveMonitor($monitor, true, true);
            }
            return null;
        }

        if ($redirect && isset($_POST['popup']) && $_POST['popup'] == 'true') {
            $urlData = ['query' => true, 'first_name' => $focus->first_name, 'last_name' => $focus->last_name,
                'module' => 'Accounts', 'action' => 'Popup'];
            if (!empty($_POST['return_module'])) {
                $urlData['module'] = $_POST['return_module'];
            }
            if (!empty($_POST['return_action'])) {
                $urlData['action'] = $_POST['return_action'];
            }
            foreach (['return_id', 'popup', 'create', 'to_pdf'] as $var) {
                if (!empty($_POST[$var])) {
                    $urlData[$var] = $_POST[$var];
                }
            }
            header('Location: index.php?' . http_build_query($urlData));
            return;
        }

        if ($redirect) {
            $this->handleRedirect($return_id);
        } else {
            return $focus;
        }
    }

    public function handleRedirect($return_id)
    {
        $return_module = InputValidation::getService()->getValidInputPost(
            'return_module',
            'Assert\Mvc\ModuleName',
            'Contacts'
        ) ?: 'Contacts';

        $return_action = InputValidation::getService()->getValidInputPost(
            'return_action',
            '',
            'DetailView'
        ) ?: 'DetailView';

        $return_id = InputValidation::getService()->getValidInputPost(
            'return_id',
            'Assert\Guid',
            ''
        ) ?: $return_id;

        if ($_REQUEST['return_module'] === 'Emails') {
            $return_action = InputValidation::getService()->getValidInputRequest('return_action', '');
        } // if we create a new record "Save", we want to redirect to the DetailView
        elseif ($_REQUEST['action'] === 'Save' && $return_module !== 'Home') {
            $return_action = 'DetailView';
        }

        //eggsurplus Bug 23816: maintain VCR after an edit/save. If it is a duplicate then don't worry about it. The offset is now worthless.
        $queryData = [
            'action' => $return_action,
            'module' => $return_module,
            'record' => $return_id,
        ];
        if (isset($_REQUEST['offset']) && empty($_REQUEST['duplicateSave'])) {
            $queryData['offset'] = $_REQUEST['offset'];
        }
        $redirect_url = 'index.php?' . http_build_query($queryData);

        if (!empty($_REQUEST['ajax_load'])) {
            echo '<script>SUGAR.ajaxUI.loadContent(' . json_encode($redirect_url, JSON_HEX_TAG) . ');</script>';
        } else {
            header('Location: ' . $redirect_url);
        }
    }

    /**
     * @return Contact
     */
    protected function getContact()
    {
        return BeanFactory::newBean('Contacts');
    }
}
