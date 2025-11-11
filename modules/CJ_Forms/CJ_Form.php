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

use Sugarcrm\Sugarcrm\Util\Uuid;

class CJ_Form extends Basic
{
    public const TRIGGER_EVENT_COMPLETED = 'completed';
    public const TRIGGER_EVENT_IN_PROGRESS = 'in_progress';
    public const TRIGGER_EVENT_NOT_APPLICABLE = 'not_applicable';

    public const ACTION_TYPE_VIEW_RECORD = 'view_record';
    public const ACTION_TYPE_CREATE_RECORD = 'create_record';
    public const ACTION_TYPE_UPDATE_RECORD = 'update_record';

    public const ACTION_TRIGGER_AUTOMATIC_CREATE = 'automatic_create';
    public const ACTION_TRIGGER_MANUAL_CREATE = 'manual_create';
    public const ACTION_TRIGGER_AUTOMATIC_UPDATE = 'automatic_update';
    public const ACTION_TRIGGER_MANUAL_UPDATE = 'manual_update';

    public const MAIN_TRIGGER_EVENT_SG_To_SA = 'smart_guide_to_sugar_action';
    public const MAIN_TRIGGER_EVENT_SA_To_SG = 'sugar_action_to_smart_guide';

    public const TABLE_NAME = 'cj_forms';

    public $disable_row_level_security = false;
    public $new_schema = true;
    public $module_dir = 'CJ_Forms';
    public $object_name = 'CJ_Form';
    public $table_name = 'cj_forms';
    public $importable = true;

    public $id;
    public $name;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $modified_by_name;
    public $created_by;
    public $created_by_name;
    public $description;
    public $deleted;
    public $created_by_link;
    public $modified_user_link;
    public $activities;
    public $following;
    public $following_link;
    public $my_favorite;
    public $favorite_link;
    public $tag;
    public $tag_link;
    public $commentlog;
    public $commentlog_link;
    public $locked_fields;
    public $locked_fields_link;
    public $team_id;
    public $team_set_id;
    public $acl_team_set_id;
    public $team_count;
    public $team_name;
    public $acl_team_names;
    public $team_link;
    public $team_count_link;
    public $teams;
    public $trigger_event;
    public $action_type;
    public $parent_module;
    public $relationship;
    public $parent_id;
    public $parent_type;
    public $activity_template_link;
    public $dri_workflow_template_id;
    public $dri_workflow_template_name;
    public $dri_workflow_template_link;
    public $activity_module;
    public $active;

    /**
     * @param string $interface
     * @return boolean
     */
    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }

        return false;
    }

    /**
     * Get the enum values for the relationships
     *
     * @deprecated
     * @return array
     */
    public static function getRelationshipEnumValues()
    {
        $values = array_merge(
            ['' => ''],
            self::addValuesForModule('Tasks', 'Tasks', 'Tasks', ''),
            self::addValuesForModule('Meetings', 'Meetings', 'Meetings', ''),
            self::addValuesForModule('Calls', 'Calls', 'Calls', '')
        );

        ksort($values);
        return $values;
    }

    /**
     * Set the values for the module
     * @param string $module
     * @param string $prefix
     * @param string $prefixName
     * @param string $skipLink
     * @param integer $depth
     * @deprecated
     * @return array
     */
    public static function addValuesForModule(
        string $module,
        string $prefix,
        string $prefixName,
        string $skipLink,
        int    $depth = 0
    ) {

        $GLOBALS['log']->fatal('loading $module');
        $bean = BeanFactory::newBean($module);
        $fieldsArr = [
            'created_by_link',
            'modified_user_link',
            'activities',
            'activities_users',
            'activities_teams',
            'comments',
            'teams',
            'team_link',
            'team_count_link',
            'email_attachment_for',
            'assigned_user_link',
            'current_cj_activity_at',
            'current_activity_call',
            'current_activity_meeting',
            'current_activity_task',
            'current_stage_at',
            'dri_workflow_template_link',
            'dri_subworkflow_template_link',
            'cj_activity_tpl_link',
            'archived_emails',
            'blocked_by_link',
        ];
        $values = [];

        if (!$bean) {
            return $values;
        }

        foreach ($bean->getFieldDefinitions() as $def) {
            if (isset($def['type']) &&
                isset($def['module']) &&
                isset($def['vname']) &&
                isset($def['name']) &&
                $def['type'] === 'link' &&
                !in_array($def['name'], $fieldsArr)) {
                $key = "$prefix:{$def['name']}";

                $vname = translate($def['vname'], $module);
                $name = "$prefixName › $vname ({$def['name']})";

                $values[$key] = $name;

                if ($depth < 2) {
                    $values = array_merge(
                        $values,
                        self::addValuesForModule($def['module'], $key, $name, $skipLink, $depth + 1)
                    );
                }
            }
        }
        return $values;
    }

    /**
     * {@inheritDoc}
     */
    public function save($check_notify = false)
    {
        $this->validateUniqueTriggerEvent();

        /**
         * This attribute should be empty so that if the Parent Type will change in Flex Relate Field then it should create
         * the form according to the selected Parent Type
         */
        $this->new_rel_relname = '';
        return parent::save($check_notify);
    }

    /**
     * Copy the forms
     *
     * @param DRI_Workflow_Template $journeyTemplate
     * @param SugarBean $parent
     * @param SugarBean $parentBase
     */
    public static function copyForms(DRI_Workflow_Template $journeyTemplate, SugarBean $parent, SugarBean $parentBase)
    {
        $parentBase->load_relationship('forms');
        foreach ($parentBase->forms->getBeans() as $formBase) {
            /** @var \CJ_Form $form */
            $form = clone $formBase;
            $form->id = \Sugarcrm\Sugarcrm\Util\Uuid::uuid4();
            $form->new_with_id = true;
            $form->dri_workflow_template_id = $journeyTemplate->id;
            // Set Smart Guide Template in case of Sugar Action to Smart Guide
            if ($form->main_trigger_type === 'sugar_action_to_smart_guide') {
                $form->smart_guide_template_id = $journeyTemplate->id;
            }
            $form->parent_id = $parent->id;
            $form->parent_name = $parent->name;
            $form->parent_type = $parent->module_dir;
            $form->save();
            BeanFactory::unregisterBean($form);
        }
    }

    /**
     * Validates the trigger event
     * return null
     * @throws SugarApiExceptionInvalidParameter
     */
    private function validateUniqueTriggerEvent()
    {
        if (empty($this->main_trigger_type) ||
            $this->main_trigger_type == 'sugar_action_to_smart_guide' ||
            !$this->active ||
            $this->action_trigger_type === self::ACTION_TRIGGER_AUTOMATIC_CREATE ||
            $this->action_trigger_type === self::ACTION_TRIGGER_AUTOMATIC_UPDATE) {
            return;
        }

        $query = new SugarQuery();
        $query->from(BeanFactory::newBean('CJ_Forms'));
        $query->select('id');
        $query->where()
            ->equals('active', true)
            ->equals('parent_id', $this->parent_id)
            ->equals('parent_type', $this->parent_type)
            ->equals('trigger_event', $this->trigger_event);

        $query->where()->queryOr()
            ->equals('action_trigger_type', self::ACTION_TRIGGER_MANUAL_CREATE)
            ->equals('action_trigger_type', self::ACTION_TRIGGER_MANUAL_UPDATE)
            ->isNull('action_trigger_type');

        if (!empty($this->id)) {
            $query->where()->notEquals('id', $this->id);
        }

        $results = $query->execute();

        if (safeCount($results) > 0) {
            $bean = BeanFactory::getBean('CJ_Forms', $results[0]['id']);
            $templateName = $bean->parent_name ?? 'Undefined parent';
            $event = translate('cj_forms_trigger_event_list', 'CJ_Forms', $this->trigger_event);
            $message = sprintf(translate('LBL_DUPLICATE_TRIGGER_EVENT_FOUND_ERROR', 'CJ_Forms'), $templateName, $event);
            throw new SugarApiExceptionInvalidParameter($message);
        }
    }

    /**
     * Sets the values from the populate_fields to the bean indexes
     *
     * @param \SugarBean $target
     * @param \SugarBean $targetTemplate
     */
    public static function setTargetValues(\SugarBean $target, \SugarBean $targetTemplate)
    {
        if (!empty($targetTemplate->populate_fields)) {
            $populateFields = json_decode($targetTemplate->populate_fields, true);
            foreach ($populateFields as $key => $pf) {
                if (!empty($pf['id']) && !empty($pf['actualFieldName'])) {
                    if ($pf['type'] === 'tag') {
                        self::setTagValues($target, $pf);
                    } elseif (is_array($pf['value'])) {
                        $target->{$pf['actualFieldName']} = encodeMultienumValue($pf['value']);
                    } elseif (($pf['type'] === 'date' || $pf['type'] === 'datetimecombo') && !empty($pf['childFieldsData'])) {
                        self::setPopulateFieldTargetValues($target, $targetTemplate, $pf);
                    } elseif ($pf['type'] === 'relate') {
                        if (!empty($pf['actual_id_name'])) {
                            $target->{$pf['actual_id_name']} = $pf['id_value'];
                        }
                        $target->{$pf['actualFieldName']} = $pf['value'];
                    } elseif ($pf['type'] === 'currency') {
                        if (!empty($pf['id_name'])) {
                            $target->{$pf['id_name']} = $pf['id_value'];
                        }
                        $target->{$pf['actualFieldName']} = $pf['value'];
                    } else {
                        $target->{$pf['actualFieldName']} = $pf['value'];
                    }
                }
            }
        }
    }

    /**
     * Sets the values from the populate_fields to the bean tag field
     *
     * @param \SugarBean $target
     * @param array $pf populate_field - tag
     */
    private static function setTagValues($target, $pf)
    {
        if ($target->load_relationship('tag_link')) {
            if (!isset($target->id)) {
                $target->new_with_id = true;
                $target->id = Uuid::uuid4();
            }
            $sft = new SugarFieldTag('tag');
            $rel = [];
            $tags = [];
            foreach ($pf['value'] as $tag) {
                $rel[] = $tagBean = $sft->getTagBean($tag);
                $tags[$tagBean->name_lower] = $tagBean->name;
            }

            $sft->addTagsToBean($target, $rel, 'tag_link', $tags);
        }
    }

    private static function setPopulateFieldTargetValues(\SugarBean $target, \SugarBean $targetTemplate, $populateField)
    {
        $selectiveType = $populateField['childFieldsData']['selective_date']['value'];
        if ($selectiveType === 'relative') {
            // current time in UTC
            $timedate = new \TimeDate();
            $currentTime = $timedate->getNow();
            $intDateValue = $populateField['childFieldsData']['int_date']['value'];
            $relativeDateValue = $populateField['childFieldsData']['relative_date']['value'];
            $date = $intDateValue . ' ' . $relativeDateValue;
            $date = $intDateValue >= 0 ? '+' . $date : $date;

            $currentTime->modify($date);
            $target->{$populateField['actualFieldName']} = ($populateField['type'] === 'datetimecombo') ?
                $currentTime->asDb() :
                $currentTime->asDbDate();
        } elseif ($selectiveType === 'fixed') {
            if ($populateField['type'] === 'datetimecombo') {
                $timedate = new \TimeDate();
                $currentTime = $timedate->fromString($populateField['childFieldsData']['main_date']['value']);
                $target->{$populateField['actualFieldName']} = $currentTime->asDb();
            } elseif ($populateField['type'] === 'date') {
                $target->{$populateField['actualFieldName']} = $populateField['childFieldsData']['main_date']['value'];
            }
        }
    }
}
