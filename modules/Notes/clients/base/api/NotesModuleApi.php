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

class NotesModuleApi extends ModuleApi
{
    /**
     * Registers the create route for Note to guarantee that {@link NotesModuleApi::createBean()} is used
     * in place of {@link ModuleApi::createBean()}.
     *
     * {@inheritdoc}
     */
    public function registerApiRest()
    {
        return [
            'create' => [
                'reqType' => 'POST',
                'path' => ['Notes'],
                'pathVars' => ['module'],
                'method' => 'createRecord',
                'shortHelp' => 'This method creates a new Note record',
                'exceptions' => [
                    'SugarApiExceptionInvalidParameter',
                    'SugarApiExceptionMissingParameter',
                    'SugarApiExceptionNotAuthorized',
                    'SugarApiExceptionNotFound',
                ],
            ],
        ];
    }

    /**
     * Creates new bean of the given module
     *
     * @param ServiceBase $api
     * @param array $args API arguments
     * @param array $additionalProperties Additional properties to be set on the bean
     *
     * @return SugarBean
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotAuthorized
     */
    public function createBean(ServiceBase $api, array $args, array $additionalProperties = [])
    {
        $api->action = 'save';

        // Users can be created only in cloud console for IDM mode.
        if (safeInArray('Notes', $this->idmModeDisabledModules)
            && $this->isIDMModeEnabled()
            && empty($args['skip_idm_mode_restrictions'])) {
            throw new SugarApiExceptionNotAuthorized();
        }

        $bean = BeanFactory::newBeanFromArgs($args);

        if (empty($bean)) {
            throw new SugarApiExceptionMissingParameter('Invalid module or missing required field(s)');
        }

        if (!$bean->ACLAccess('save', $this->getACLOptions())) {
            // No create access so we construct an error message and throw the exception
            $failed_module_strings = return_module_language($GLOBALS['current_language'], 'Notes');
            $args = ['moduleName' => $failed_module_strings['LBL_MODULE_NAME']];
            throw new SugarApiExceptionNotAuthorized('EXCEPTION_CREATE_MODULE_NOT_AUTHORIZED', $args);
        }

        if (!empty($args['id'])) {
            // Check if record already exists
            if (BeanFactory::getBean(
                'Notes',
                $args['id'],
                ['strict_retrieve' => true, 'disable_row_level_security' => true]
            )) {
                throw new SugarApiExceptionInvalidParameter(
                    'Record already exists: ' . $args['id'] . ' in module: ' . 'Notes'
                );
            }
        } else {
            $args['id'] = Uuid::uuid1();
        }

        $bean->id = $args['id'];
        $bean->new_with_id = true;
        $bean->in_save = true;

        $additionalProperties['additional_rel_values'] = $this->getRelatedFields($args, $bean);

        // register newly created bean so that it could be accessible by related beans before it's saved
        BeanFactory::registerBean($bean);

        foreach ($additionalProperties as $property => $value) {
            $bean->$property = $value;
        }

        // populate parent bean before saving related ones
        $this->populateBean($bean, $api, $args);

        // If we uploaded files during the record creation, move them from
        // the temporary folder to the configured upload folder.
        $this->moveTemporaryFiles($args, $bean);

        $relateArgs = $this->getRelatedRecordArguments($bean, $args, 'create');
        $this->createRelatedRecords($api, $bean, $relateArgs);

        // finally save parent bean
        $this->saveBean($bean, $api, $args);

        $args['record'] = $bean->id;

        $this->processAfterCreateOperations($args, $bean);

        return $this->reloadBean($api, $args);
    }
}
