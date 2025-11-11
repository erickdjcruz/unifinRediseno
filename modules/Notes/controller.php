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


class NotesController extends SugarController
{
    /**
     * Overriding parent method to prevent Uploads handling
     * @return void
     */
    public function pre_save()
    {
        trigger_error('DEPRECATED', E_USER_ERROR);
    }

    public function action_save()
    {
        trigger_error('DEPRECATED', E_USER_ERROR);
    }

    public function action_editview()
    {
        $this->view = 'edit';
        $GLOBALS['view'] = $this->view;
        if (!empty($_REQUEST['deleteAttachment'])) {
            ob_clean();
            echo $this->bean->deleteAttachment($_REQUEST['isDuplicate']) ? 'true' : 'false';
            sugar_cleanup(true);
        }
    }
}
