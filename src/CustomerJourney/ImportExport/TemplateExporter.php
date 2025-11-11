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

namespace Sugarcrm\Sugarcrm\CustomerJourney\ImportExport;

class TemplateExporter
{
    /**
     * @var array
     */
    private static $fieldDenyList = [
        'deleted',
        'date_entered',
        'date_modified',
        'modified_user_id',
        'created_by',
        'my_favorite',
        'favorite_link',
        'following',
        'following_link',
        'modified_by_name',
        'created_by_name',
        'doc_owner',
        'user_favorites',
        'created_by_link',
        'modified_user_link',
        'activities',
        'team_count',
        'team_link',
        'team_name',
        'team_count_link',
        'teams',
        'copied_template_id',
        'copied_template_name',
        'copied_template_link',
        'leads',
        'accounts',
        'contacts',
        'cases',
        'bugs',
        'opportunities',
        'contracts',
        'copies',
        'dri_workflows',
        'tasks',
        'dri_subworkflows',
        'tag',
        'tag_link',
        'locked_fields',
        'locked_fields_link',
        'dri_workflow_template_link',
        'dri_subworkflow_template_link',
        'dri_workflow_template_link',
        'calls',
        'meetings',
        'active',
    ];

    /**
     * @var array
     */
    private static $links = [
        'dri_subworkflow_templates',
        'dri_workflow_task_templates',
        'web_hooks',
        'forms',
    ];

    /**
     * Export DRI_Workflow_Template record of specific id
     *
     * @param string $id
     * @return array
     */
    public function exportId($id)
    {
        $template = \DRI_Workflow_Template::getById($id);
        return $this->export($template);
    }

    /**
     * Export SugarBean record as an array
     *
     * @param \SugarBean $bean
     * @return array
     */
    public function export(\SugarBean $bean)
    {
        $data = $bean->toArray();

        // remove fields in denylist
        $data = array_diff_key($data, array_flip(self::$fieldDenyList));

        // export blocked by in old field as well to be compatible with older versions
        // start - keeping this code to ensure backward compatibility with versions older than 4.5.0
        if ($bean instanceof \DRI_Workflow_Task_Template && $bean->hasBlockedBy()) {
            $blockedBy = $bean->getBlockedBy();
            $blockedBy = array_shift($blockedBy);

            if ($blockedBy instanceof \DRI_Workflow_Task_Template) {
                $data['blocked_by_id'] = $blockedBy->id;
                $data['blocked_by_name'] = $blockedBy->name;
            }
        }
        // end - keeping this code to ensure backward compatibility with versions older than 4.5.0

        foreach ($data as $link => $value) {
            if (isset($bean->field_defs[$link]) &&
                $bean->field_defs[$link]['type'] === 'link' &&
                in_array($link, self::$links, true)
            ) {
                $bean->load_relationship($link);

                $data[$link] = [];

                foreach ($bean->$link->getBeans() as $related) {
                    if (!($bean->module_dir === 'DRI_Workflow_Templates' &&
                        ($link === 'forms' || $link === 'dri_workflow_task_templates'))
                    ) {
                        $data[$link][$related->id] = $this->export($related);
                    }
                }
            }
        }

        return $data;
    }
}
