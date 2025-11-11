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
$entry_point_registry = [
    'emailImage' => ['file' => 'modules/EmailMan/EmailImage.php', 'auth' => false],
    'download' => ['file' => 'download.php', 'auth' => true],
    'export' => ['file' => 'export.php', 'auth' => true],
    'export_dataset' => ['file' => 'export_dataset.php', 'auth' => true],
    'Changenewpassword' => ['file' => 'modules/Users/Changenewpassword.php', 'auth' => false],
    'GeneratePassword' => ['file' => 'modules/Users/GeneratePassword.php', 'auth' => false],
    'vCard' => ['file' => 'vCard.php', 'auth' => true],
    'pdf' => ['file' => 'pdf.php', 'auth' => true],
    'minify' => ['file' => 'jssource/minify.php', 'auth' => true],
    'json_server' => ['file' => 'json_server.php', 'auth' => true],
    'get_url' => ['file' => 'get_url.php', 'auth' => true],
    'HandleAjaxCall' => ['file' => 'HandleAjaxCall.php', 'auth' => true],
    'TreeData' => ['file' => 'TreeData.php', 'auth' => true],
    'image' => ['file' => 'modules/Campaigns/image.php', 'auth' => false],
    'campaign_trackerv2' => ['file' => 'modules/Campaigns/Tracker.php', 'auth' => false],
    'WebToLeadCapture' => ['file' => 'modules/Campaigns/WebToLeadCapture.php', 'auth' => false],
    'removeme' => ['file' => 'modules/Campaigns/RemoveMe.php', 'auth' => false],
    'acceptDecline' => ['file' => 'modules/Contacts/AcceptDecline.php', 'auth' => false],
    'process_queue' => ['file' => 'process_queue.php', 'auth' => true],
    'process_workflow' => ['file' => 'process_workflow.php', 'auth' => true],
    'zipatcher' => ['file' => 'zipatcher.php', 'auth' => true],
    'mm_get_doc' => ['file' => 'modules/MailMerge/get_doc.php', 'auth' => true],
    'getImage' => ['file' => 'include/SugarTheme/getImage.php', 'auth' => false],
    'DetailUserRole' => ['file' => 'modules/ACLRoles/DetailUserRole.php', 'auth' => true],
    'getYUIComboFile' => ['file' => 'include/javascript/getYUIComboFile.php', 'auth' => false],
    'UploadFileCheck' => ['file' => 'modules/Configurator/UploadFileCheck.php', 'auth' => true],
    'tinymce_spellchecker_rpc' => ['file' => 'include/javascript/tiny_mce/plugins/spellchecker/rpc.php', 'auth' => true],
    'jslang' => ['file' => 'include/language/getJSLanguage.php', 'auth' => true],
    'ConfirmEmailAddress' => ['file' => 'modules/EmailAddresses/Confirm.php', 'auth' => false],
];
