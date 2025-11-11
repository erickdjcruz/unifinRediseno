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


$listViewDefs['EmailMan'] = [
    'CAMPAIGN_NAME' => [
        'width' => '10',
        'label' => 'LBL_LIST_CAMPAIGN',
        'link' => true,
        'customCode' => '<a href="index.php?module=Campaigns&action=DetailView&record={$CAMPAIGN_ID}">{$CAMPAIGN_NAME}</a>',
        'default' => true],
    'RECIPIENT_NAME' => [
        'sortable' => false,
        'width' => '10',
        'label' => 'LBL_LIST_RECIPIENT_NAME',
        'customCode' => '<a href="index.php?module={$RELATED_TYPE}&action=DetailView&record={$RELATED_ID}">{$RECIPIENT_NAME}</a>',
        'default' => true],
    'RECIPIENT_EMAIL' => [
        'sortable' => false,
        'width' => '10',
        'label' => 'LBL_LIST_RECIPIENT_EMAIL',
        'customCode' => '{$EMAIL_LINK}{$RECIPIENT_EMAIL}</a>',
        'default' => true],
    'MESSAGE_NAME' => [
        'sortable' => false,
        'width' => '10',
        'label' => 'LBL_LIST_MESSAGE_NAME',
        'customCode' => '<a href="index.php?module=EmailMarketing&action=DetailView&record={$MARKETING_ID}">{$MESSAGE_NAME}</a>',
        'default' => true],
    'SEND_DATE_TIME' => [
        'width' => '10',
        'label' => 'LBL_LIST_SEND_DATE_TIME',
        'default' => true],
    'SEND_ATTEMPTS' => [
        'width' => '10',
        'label' => 'LBL_LIST_SEND_ATTEMPTS',
        'default' => true],
    'IN_QUEUE' => [
        'width' => '10',
        'label' => 'LBL_LIST_IN_QUEUE',
        'default' => true],
];
