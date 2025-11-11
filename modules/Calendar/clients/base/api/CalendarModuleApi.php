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

class CalendarModuleApi extends ModuleApi
{
    /**
     * Deny fields list
     *
     * @var array
     */
    public $denyFields = ['date_entered', 'date_modified'];

    /**
     * Register api rest
     *
     * {@inheritdoc}
     */
    public function registerApiRest()
    {
        return [
            'create' => [
                'reqType' => 'POST',
                'path' => ['Calendar'],
                'pathVars' => ['module'],
                'method' => 'createRecord',
                'shortHelp' => 'This method creates a new record of the specified type',
                'longHelp' => 'include/api/help/module_post_help.html',
            ],
            'update' => [
                'reqType' => 'PUT',
                'path' => ['Calendar', '?'],
                'pathVars' => ['module', 'record'],
                'method' => 'updateRecord',
                'shortHelp' => 'This method updates a record of the specified type',
                'longHelp' => 'include/api/help/module_record_put_help.html',
            ],
        ];
    }

    /**
     * Creates a calendar record
     *
     * {@inheritdoc}
     */
    public function createRecord(ServiceBase $api, array $args)
    {
        $this->validateFields($args);

        return parent::createRecord($api, $args);
    }

    /**
     * Updates the calendar record
     *
     * {@inheritdoc}
     */
    public function updateRecord(ServiceBase $api, array $args)
    {
        $this->validateFields($args);

        return parent::updateRecord($api, $args);
    }

    /**
     * Validate fields
     *
     * Make sure fields received are valid
     *
     * @param Array $args
     */
    public function validateFields($args)
    {
        if (in_array($args['event_start'], $this->denyFields)) {
            throw new SugarApiExceptionInvalidParameter(
                translate('LBL_CALENDAR_START_DATE_INVALID', 'Calendar'),
            );
        }

        if (isset($args['event_end']) && in_array($args['event_end'], $this->denyFields)) {
            throw new SugarApiExceptionInvalidParameter(
                translate('LBL_CALENDAR_END_DATE_INVALID', 'Calendar'),
            );
        }
    }
}
