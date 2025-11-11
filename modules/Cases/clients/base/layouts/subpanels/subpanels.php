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
$viewdefs['Cases']['base']['layout']['subpanels'] = [
    'components' => [
        [
            'layout' => 'subpanel',
            'label' => 'LBL_CALLS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'calls',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_MEETINGS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'meetings',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_TASKS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'tasks',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_NOTES_SUBPANEL_TITLE',
            'context' => [
                'link' => 'notes',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_DOCUMENTS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'documents',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_CONTACTS_SUBPANEL_TITLE',
            'override_paneltop_view' => 'panel-top-for-cases',
            'override_subpanel_list_view' => 'subpanel-for-cases',
            'context' => [
                'link' => 'contacts',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_BUGS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'bugs',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_PROJECTS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'project',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_EMAILS_SUBPANEL_TITLE',
            'override_subpanel_list_view' => 'subpanel-for-cases-archived-emails',
            'context' => [
                'link' => 'archived_emails',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_KBCONTENTS_SUBPANEL_TITLE',
            'override_paneltop_view' => 'panel-top-for-cases',
            'context' => [
                'link' => 'kbcontents',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_CHANGETIMERS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'changetimers',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_PURCHASES_SUBPANEL_TITLE',
            'context' => [
                'link' => 'purchases',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_MESSAGES_SUBPANEL_TITLE',
            'context' => [
                'link' => 'messages',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_ESCALATIONS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'escalations',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_EXTERNAL_USERS_SUBPANEL_TITLE',
            'override_paneltop_view' => 'panel-top-for-externalusers',
            'context' => [
                'link' => 'external_users',
            ],
        ],
    ],
];
