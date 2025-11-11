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

global $theme, $mod_strings;

$listViewDefs['Campaigns'] = [
    'NAME' => [
        'width' => '20',
        'label' => 'LBL_LIST_CAMPAIGN_NAME',
        'link' => true,
        'default' => true],
    'STATUS' => [
        'width' => '10',
        'label' => 'LBL_LIST_STATUS',
        'default' => true],
    'CAMPAIGN_TYPE' => [
        'width' => '10',
        'label' => 'LBL_LIST_TYPE',
        'default' => true],
    'END_DATE' => [
        'width' => '10',
        'label' => 'LBL_LIST_END_DATE',
        'default' => true],

    'TEAM_NAME' => [
        'width' => '15',
        'label' => 'LBL_LIST_TEAM',
        'default' => false],
    'ASSIGNED_USER_NAME' => [
        'width' => '8',
        'label' => 'LBL_LIST_ASSIGNED_USER',
        'module' => 'Employees',
        'id' => 'ASSIGNED_USER_ID',
        'default' => true],
    'TRACK_CAMPAIGN' => [
        'width' => '1',
        'label' => '&nbsp;',
        'link' => true,
        'customCode' => ' <a title="{$TRACK_CAMPAIGN_TITLE}" href="index.php?action=TrackDetailView&module=Campaigns&record={$ID}"><!--not_in_theme!--><img border="0" src="{$TRACK_CAMPAIGN_IMAGE}" alt="{$TRACK_VIEW_ALT_TEXT}"></a> ',
        'default' => true,
        'studio' => false,
        'nowrap' => true,
        'sortable' => false],
    'LAUNCH_WIZARD' => [
        'width' => '1',
        'label' => '&nbsp;',
        'link' => true,
        'customCode' => '{$WIZARD_BUTTON}',
        'default' => true,
        'studio' => false,
        'nowrap' => true,
        'sortable' => false],
    'DATE_ENTERED' => [
        'width' => '10',
        'label' => 'LBL_DATE_ENTERED',
        'default' => true],
];
