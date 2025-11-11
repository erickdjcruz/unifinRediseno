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

// Handle input validation up front
use Sugarcrm\Sugarcrm\Security\InputValidation\InputValidation;

$request = InputValidation::getService();
$record = $request->getValidInputPost('record');
$relate_id = $request->getValidInputRequest('relate_id');
$return_module = $request->getValidInputRequest('return_module');
$duplicateId = $request->getValidInputRequest('duplicateId', null, '');
$return_action = $request->getValidInputPost('return_action', null, '');
$return_id = $request->getValidInputPost('return_id', null, '');


require_once 'include/formbase.php';

$focus = BeanFactory::newBean('Holidays');
global $current_user;

$focus->disable_row_level_security = true;
$focus->retrieve($record);

$focus = populateFromPost('', $focus);

if ($focus->id != $relate_id) {
    if ($return_module === 'Users') {
        $focus->person_id = $relate_id;
        $focus->person_type = 'Users';
    } else {
        $focus->related_module = $return_module;
        $focus->related_module_id = $relate_id;
    }
}

if (!$focus->id && !empty($duplicateId)) {
    $original_focus = BeanFactory::newBean('Holidays');
    $original_focus->retrieve($duplicateId);

    $focus->person_id = $original_focus->person_id;
    $focus->person_type = $original_focus->person_type;
    $focus->related_module = $original_focus->related_module;
    $focus->related_module_id = $original_focus->related_module_id;
}

$check_notify = false;

$focus->save($check_notify);

// Added specifically for Business Center Holidays
if ($focus->related_module !== 'Project') {
    // Load up the relationship for this bean and save it
    $focus->load_relationship('business_holidays');
    $focus->business_holidays->add($focus->related_module_id);
}
$return_id = $focus->id;

if ($return_module === '') {
    $return_module = 'Holidays';
}

if ($return_action === '') {
    $return_action = 'DetailView';
}


$GLOBALS['log']->debug('Saved record with id of ' . $return_id);

handleRedirect($return_id, 'Holidays');
