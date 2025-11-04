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
 * Description:  Defines the English language pack for the base application.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('modules/Bkl_Backlog_Compromiso/Bkl_Backlog_Compromiso.php');

class Bkl_Backlog_CompromisoDashlet extends DashletGeneric { 
    public function __construct($id, $def = null)
    {
		global $current_user, $app_strings;
		require('modules/Bkl_Backlog_Compromiso/metadata/dashletviewdefs.php');

        parent::__construct($id, $def);

        if(empty($def['title'])) $this->title = translate('LBL_HOMEPAGE_TITLE', 'Bkl_Backlog_Compromiso');

        $this->searchFields = $dashletData['Bkl_Backlog_CompromisoDashlet']['searchFields'];
        $this->columns = $dashletData['Bkl_Backlog_CompromisoDashlet']['columns'];

        $this->seedBean = new Bkl_Backlog_Compromiso();        
    }
}