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


$GLOBALS['studioDefs']['ProjectTask'] = [
    'LBL_DETAILVIEW' => [
        'template' => 'xtpl',
        'template_file' => 'modules/ProjectTask/DetailView.html',
        'php_file' => 'modules/ProjectTask/DetailView.php',
        'type' => 'DetailView',
    ],
    'LBL_EDITVIEW' => [
        'template' => 'xtpl',
        'template_file' => 'modules/ProjectTask/EditView.html',
        'php_file' => 'modules/ProjectTask/EditView.php',
        'type' => 'EditView',
    ],
    'LBL_LISTVIEW' => [
        'template' => 'listview',
        'meta_file' => 'modules/ProjectTask/listviewdefs.php',
        'type' => 'ListView',
    ],
    'LBL_SEARCHFORM' => [
        'template' => 'xtpl',
        'template_file' => 'modules/ProjectTask/SearchForm.html',
        'php_file' => 'modules/ProjectTask/ListView.php',
        'type' => 'SearchForm',
    ],

];
