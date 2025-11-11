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

use Sugarcrm\Sugarcrm\CustomerJourney\ImportExport\TemplateImporter;

$db = DBManagerFactory::getInstance();

if ($db->tableExists('dri_workflow_templates')) {
    try {
        // ensure send_invites column exists in dri_workflow_task_templates table
        $columns = $db->get_columns('dri_workflow_task_templates');

        if (!isset($columns['send_invites'])) {
            $def = [
                'name' => 'send_invites',
                'type' => 'varchar',
                'len' => 255,
                'default' => 'none',
            ];
            $sql = $db->addColumnSQL('dri_workflow_task_templates', [$def]);
            $db->query($sql, true);
        }

        $GLOBALS['log']->info('CJ Install: Importing templates ...');

        $importer = new TemplateImporter();
        $importer->importAll();
    } catch (\Exception $e) {
        $GLOBALS['log']->info('CJ Install: Templates are not imported due to some error ...');
    }
}
