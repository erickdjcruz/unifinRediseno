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
$viewdefs['DRI_Workflow_Templates']['base']['view']['template-import-headerpane'] = [
    'template' => 'headerpane',
    'title' => 'LBL_IMPORT',
    'buttons' => [
        [
            'name' => 'project_cancel_button',
            'type' => 'button',
            'label' => 'LBL_CANCEL_BUTTON_LABEL',
            'css_class' => 'btn-invisible btn-link',
        ],
        [
            'name' => 'project_finish_button',
            'type' => 'button',
            'label' => 'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_BUTTON_LABEL',
            'acl_action' => 'create',
            'css_class' => 'btn-primary',
        ],
        [
            'name' => 'sidebar_toggle',
            'type' => 'sidebartoggle',
        ],
    ],
];
