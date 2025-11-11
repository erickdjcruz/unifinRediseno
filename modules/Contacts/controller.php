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

class ContactsController extends SugarController
{
    public function action_Popup()
    {
        if (!empty($_REQUEST['html']) && $_REQUEST['html'] == 'mail_merge') {
            $this->view = 'mailmergepopup';
        } else {
            $this->view = 'popup';
        }
    }

    public function action_ValidPortalUsername()
    {
        $this->view = 'validportalusername';
    }

    public function action_RetrieveEmail()
    {
        $this->view = 'retrieveemail';
    }

    public function action_ContactAddressPopup()
    {
        $this->view = 'contactaddresspopup';
    }

    public function action_CloseContactAddressPopup()
    {
        $this->view = 'closecontactaddresspopup';
    }
}
