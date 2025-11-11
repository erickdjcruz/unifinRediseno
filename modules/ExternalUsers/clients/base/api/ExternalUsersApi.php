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

class ExternalUsersApi extends ModuleApi
{
    public function registerApiRest()
    {
        return [
            'create' => [
                'reqType' => 'POST',
                'path' => ['ExternalUsers'],
                'pathVars' => ['module'],
                'method' => 'createRecord',
                'shortHelp' => 'This method creates a new record of the specified type',
                'longHelp' => 'include/api/help/module_post_help.html',
                'minVersion' => '11.8',
            ],
            'update' => [
                'reqType' => 'PUT',
                'path' => ['ExternalUsers', '?'],
                'pathVars' => ['module', 'record'],
                'method' => 'updateRecord',
                'shortHelp' => 'This method updates a record of the specified type',
                'longHelp' => 'include/api/help/module_record_put_help.html',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function createRecord(ServiceBase $api, array $args)
    {
        $duplicateCheckApi = new DuplicateCheckApi();
        $result = $duplicateCheckApi->checkForDuplicates($api, $args);
        if (!empty($result['records'])) {
            throw new SugarApiExceptionInvalidParameter('Duplicates detected');
        }
        $data = parent::createRecord($api, $args);
        if (!empty($data['id'])) {
            if (!empty($args['parent_type']) && !empty($args['parent_id'])) {
                $person = BeanFactory::getBean($args['parent_type'], $args['parent_id']);
                if (!empty($person->id)) {
                    $person->external_user_id = $data['id'];
                    $person->save();
                }
            }
        }
        return $data;
    }

    /**
     * {@inheritDoc}
     * @see SugarApi::updateBean()
     */
    protected function updateBean(SugarBean $bean, ServiceBase $api, array $args)
    {
        $parentType = $bean->parent_type;
        $parentId = $bean->parent_id;
        $id = parent::updateBean($bean, $api, $args);
        if (!empty($bean->parent_id) && $bean->parent_id !== $parentId) {
            $parentBean = BeanFactory::getBean($bean->parent_type, $bean->parent_id);
            if ($parentBean) {
                $parentBean->external_user_id = $id;
                $parentBean->save();
            }
        }
        if (!empty($parentId) && $parentId !== $bean->parent_id) {
            $parentBean = BeanFactory::getBean($parentType, $parentId);
            if ($parentBean) {
                $parentBean->external_user_id = null;
                $parentBean->save();
            }
        }
        return $id;
    }
}
