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

use Sugarcrm\Sugarcrm\Audit\EventRepository;
use Sugarcrm\Sugarcrm\DependencyInjection\Container;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Config;
use Sugarcrm\Sugarcrm\Audit\Formatter\Subject;
use Sugarcrm\Sugarcrm\Security\Subject\Formatter as SubjectFormatter;

class ModuleApi extends SugarApi
{
    /** @var RelateRecordApi */
    protected $relateRecordApi;
    private $aclCheckOptions = ['source' => 'module_api'];

    /**
     * A list of fields for which we disallow update through updateRecord
     *
     * @var array
     */
    protected $disabledUpdateFields = [
        'deleted',
        'created_by',
    ];

    /**
     * is IDM mode auth provider enabled?
     * @var bool
     */
    protected $isIDMModeEnabled;

    /**
     * What modules will be filtered if IDM mode is enabled?
     * @var array
     */
    protected $idmModeDisabledModules;

    /**
     * constructor
     */
    public function __construct()
    {
        $idpConfig = new Config(\SugarConfig::getInstance());
        $this->isIDMModeEnabled = $idpConfig->isIDMModeEnabled();
        $this->idmModeDisabledModules = $idpConfig->getIDMModeDisabledModules();
    }

    public function registerApiRest()
    {
        return [
            'create' => [
                'reqType' => 'POST',
                'path' => ['<module>'],
                'pathVars' => ['module'],
                'method' => 'createRecord',
                'shortHelp' => 'This method creates a new record of the specified type',
                'longHelp' => 'include/api/help/module_post_help.html',
            ],
            'retrieve' => [
                'reqType' => 'GET',
                'path' => ['<module>', '?'],
                'pathVars' => ['module', 'record'],
                'method' => 'retrieveRecord',
                'shortHelp' => 'Returns a single record',
                'longHelp' => 'include/api/help/module_record_get_help.html',
            ],
            'update' => [
                'reqType' => 'PUT',
                'path' => ['<module>', '?'],
                'pathVars' => ['module', 'record'],
                'method' => 'updateRecord',
                'shortHelp' => 'This method updates a record of the specified type',
                'longHelp' => 'include/api/help/module_record_put_help.html',
            ],
            'delete' => [
                'reqType' => 'DELETE',
                'path' => ['<module>', '?'],
                'pathVars' => ['module', 'record'],
                'method' => 'deleteRecord',
                'shortHelp' => 'This method deletes a record of the specified type',
                'longHelp' => 'include/api/help/module_record_delete_help.html',
            ],
            'favorite' => [
                'reqType' => 'PUT',
                'path' => ['<module>', '?', 'favorite'],
                'pathVars' => ['module', 'record', 'favorite'],
                'method' => 'setFavorite',
                'shortHelp' => 'This method sets a record of the specified type as a favorite',
                'longHelp' => 'include/api/help/module_record_favorite_put_help.html',
            ],
            'deleteFavorite' => [
                'reqType' => 'DELETE',
                'path' => ['<module>', '?', 'favorite'],
                'pathVars' => ['module', 'record', 'favorite'],
                'method' => 'unsetFavorite',
                'shortHelp' => 'This method unsets a record of the specified type as a favorite',
                'longHelp' => 'include/api/help/module_record_favorite_delete_help.html',
            ],
            'unfavorite' => [
                'reqType' => 'PUT',
                'path' => ['<module>', '?', 'unfavorite'],
                'pathVars' => ['module', 'record', 'unfavorite'],
                'method' => 'unsetFavorite',
                'shortHelp' => 'This method unsets a record of the specified type as a favorite',
                'longHelp' => 'include/api/help/module_record_favorite_delete_help.html',
            ],
            'enum' => [
                'reqType' => 'GET',
                'path' => ['<module>', 'enum', '?'],
                'pathVars' => ['module', 'enum', 'field'],
                'method' => 'getEnumValues',
                'shortHelp' => 'This method returns enum values for a specified field',
                'longHelp' => 'include/api/help/module_enum_get_help.html',
            ],
            'pii' => [
                'reqType' => 'GET',
                'path' => ['<module>', '?', 'pii'],
                'pathVars' => ['module', 'record', 'pii'],
                'minVersion' => '11.1',
                'method' => 'getPiiFields',
                'shortHelp' => 'Returns pii fields',
                'longHelp' => 'include/api/help/module_record_pii_help.html',
            ],
        ];
    }

    /**
     * This method returns the aclCheckOptions
     * @return array
     */
    public function getACLOptions() : array
    {
        return $this->aclCheckOptions;
    }

    /**
     * This method returns the pii fields of a given record
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function getPiiFields(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'record']);

        $bean = $this->loadBean($api, $args, 'view');
        //get the list of pii fields
        $piiFields = array_keys($bean->getFieldDefinitions('pii', [true]));
        $filter = $this->getFieldsFromArgs($api, $args);
        if (safeCount($filter) > 0) {
            $piiFields = array_intersect($piiFields, $filter);
        }
        $args['fields'] = $piiFields;

        $data = $this->formatBeanAfterSave($api, $args, $bean);

        $eventRepo = Container::getInstance()->get(EventRepository::class);
        $events = $this->formatSourceSubject(
            $eventRepo->getLatestBeanEvents(
                $bean,
                $piiFields
            )
        ) ?? [];

        $fields = [];

        $eventsByField = array_combine(array_column($events, 'field_name'), $events);
        foreach ($piiFields as $field) {
            if ($field !== 'email' && isset($data[$field])) {
                $fields[] = $this->mergeFieldWithEvent($field, $data[$field], $eventsByField[$field] ?? null);
            }
        }

        if (safeInArray('email', $piiFields)) {
            $fields = array_merge($fields, $this->mergeEmailFieldsWithEvents($data['email'] ?? null, $events));
        }

        $return = ['fields' => $fields, '_acl' => $data['_acl'],];
        if (isset($data['_erased_fields'])) {
            $return['_erased_fields'] = $data['_erased_fields'];
        }

        return $return;
    }

    private function mergeFieldWithEvent($field, $value, $event)
    {
        global $timedate;
        $item = [
            'field_name' => $field,
            'value' => $value,
            'date_modified' => null,
            'event_type' => null,
            'source' => null,
        ];

        if ($event !== null) {
            $dateModified = $timedate->asIso(
                $timedate->fromDbType($event['date_created'], 'datetime')
            );
            $item = array_merge(
                $item,
                [
                    'date_modified' => $dateModified,
                    'event_type' => $event['type'],
                    'source' => $event['source'],
                ]
            );
        }
        return $item;
    }

    private function mergeEmailFieldsWithEvents($emails, $events)
    {
        if (empty($emails)) {
            return [[
                'field_name' => 'email',
                'value' => null,
                'date_modified' => null,
                'event_type' => null,
                'source' => null,
            ]];
        }

        $emailEvents = array_filter($events ?? [], function ($v) {
            return $v['field_name'] === 'email';
        });
        $emailEventsById = array_combine(array_column($emailEvents, 'after_value_string'), $emailEvents);
        $fields = [];
        foreach ($emails as $email) {
            $value = [
                'id' => $email['email_address_id'],
                'email_address' => $email['email_address'],
                'opt_out' => (bool)$email['opt_out'],
                'invalid_email' => (bool)$email['invalid_email'],
                'primary_address' => (bool)$email['primary_address'],
            ];
            $fields[] = $this->mergeFieldWithEvent(
                'email',
                $value,
                $emailEventsById[$email['email_address_id']] ?? null
            );
        }

        return $fields;
    }

    private function formatSourceSubject($rows)
    {
        return (new Subject(Container::getInstance()->get(SubjectFormatter::class)))->formatRows($rows);
    }

    /**
     * This method returns the dropdown options of a given field
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function getEnumValues(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'field']);

        $bean = BeanFactory::newBean($args['module']);

        if (!isset($bean->field_defs[$args['field']])) {
            throw new SugarApiExceptionNotFound('field not found');
        }

        $vardef = $bean->field_defs[$args['field']];

        $value = null;
        $cache_age = 0;

        if (isset($vardef['function'])) {
            $cache_age = 60;
        } else {
            $cache_age = 3600;
        }
        $value = getOptionsFromVardef($vardef);
        if ($value === false) {
            throw new SugarApiExceptionNotFound('options not found');
        }
        // If a particular field has an option list that is expensive to calculate and/or rarely changes,
        // set the cache_setting property on the vardef to the age in seconds you want browsers to wait before refreshing
        if (isset($vardef['cache_setting'])) {
            $cache_age = $vardef['cache_setting'];
        }
        generateEtagHeader(md5(serialize($value)), $cache_age);
        return $value;
    }

    /**
     * Creates new record of the given module and returns its formatted representation
     *
     * @param ServiceBase $api
     * @param array $args API arguments
     *
     * @return array Formatted representation of the bean
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotAuthorized
     */
    public function createRecord(ServiceBase $api, array $args)
    {
        $bean = $this->createBean($api, $args);
        $data = $this->formatBeanAfterSave($api, $args, $bean);

        return $data;
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
        $this->requireArgs($args, ['module']);

        // Users can be created only in cloud console for IDM mode.
        if (safeInArray($args['module'], $this->idmModeDisabledModules)
            && $this->isIDMModeEnabled()
            && empty($args['skip_idm_mode_restrictions'])) {
            throw new SugarApiExceptionNotAuthorized();
        }

        $bean = BeanFactory::newBeanFromArgs($args);

        if (empty($bean)) {
            throw new SugarApiExceptionMissingParameter('Invalid module or missing required field(s)');
        }

        // TODO: When the create ACL goes in to effect, add it here.
        if (!$bean->ACLAccess('save', $this->aclCheckOptions)) {
            // No create access so we construct an error message and throw the exception
            $moduleName = null;
            if (isset($args['module'])) {
                $failed_module_strings = return_module_language($GLOBALS['current_language'], $args['module']);
                $moduleName = $failed_module_strings['LBL_MODULE_NAME'];
            }
            $args = null;
            if (!empty($moduleName)) {
                $args = ['moduleName' => $moduleName];
            }
            throw new SugarApiExceptionNotAuthorized('EXCEPTION_CREATE_MODULE_NOT_AUTHORIZED', $args);
        }

        if (!empty($args['id'])) {
            // Check if record already exists
            if (BeanFactory::getBean(
                $args['module'],
                $args['id'],
                ['strict_retrieve' => true, 'disable_row_level_security' => true]
            )) {
                throw new SugarApiExceptionInvalidParameter(
                    'Record already exists: ' . $args['id'] . ' in module: ' . $args['module']
                );
            }
        } else {
            $args['id'] = create_guid();
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
        // FIXME Moving temporary files will be handled better in BR-2059.
        $this->moveTemporaryFiles($args, $bean);

        $relateArgs = $this->getRelatedRecordArguments($bean, $args, 'add');
        $this->linkRelatedRecords($api, $bean, $relateArgs, 'create', 'view');

        $relateArgs = $this->getRelatedRecordArguments($bean, $args, 'create');
        $this->createRelatedRecords($api, $bean, $relateArgs);

        // finally save parent bean
        $this->saveBean($bean, $api, $args);

        $args['record'] = $bean->id;

        $this->processAfterCreateOperations($args, $bean);

        return $this->reloadBean($api, $args);
    }

    public function updateRecord(ServiceBase $api, array $args)
    {
        foreach ($this->disabledUpdateFields as $field) {
            if (isset($args[$field])) {
                unset($args[$field]);
            }
        }

        $api->action = 'view';
        $this->requireArgs($args, ['module', 'record']);

        $bean = $this->loadBean($api, $args, 'save', $this->aclCheckOptions);
        $api->action = 'save';

        // If we uploaded files during the record update, move them from
        // the temporary folder to the configured upload folder.
        // FIXME Moving temporary files will be handled better in BR-2059.
        $this->moveTemporaryFiles($args, $bean);
        $this->updateBean($bean, $api, $args);

        $this->updateRelatedRecords($api, $bean, $args);

        return $this->getLoadedAndFormattedBean($api, $args);
    }

    /**
     * Link and unlink any related records
     *
     * @param ServiceBase $api
     * @param SugarBean $bean
     * @param array $args API arguments
     * @throws SugarApiExceptionInvalidParameter
     */
    public function updateRelatedRecords(ServiceBase $api, SugarBean $bean, array $args)
    {
        $relateArgs = $this->getRelatedRecordArguments($bean, $args, 'delete');
        $this->unlinkRelatedRecords($api, $bean, $relateArgs);

        $relateArgs = $this->getRelatedRecordArguments($bean, $args, 'add');
        $this->linkRelatedRecords($api, $bean, $relateArgs);

        $relateArgs = $this->getRelatedRecordArguments($bean, $args, 'create');
        $this->createRelatedRecords($api, $bean, $relateArgs);
    }

    public function retrieveRecord(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'record']);

        $bean = $this->loadBean($api, $args, 'view');

        // formatBean is soft on view so that creates without view access will still work
        if (!$bean->ACLAccess('view', $this->aclCheckOptions)) {
            throw new SugarApiExceptionNotAuthorized('SUGAR_API_EXCEPTION_RECORD_NOT_AUTHORIZED', ['view']);
        }

        $api->action = 'view';
        $data = $this->formatBean($api, $args, $bean);

        return $data;
    }

    public function deleteRecord(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'record']);

        // Users can be deleted only in cloud console for IDM mode.
        if (safeInArray($args['module'], $this->idmModeDisabledModules)
            && $this->isIDMModeEnabled()
            && empty($args['skip_idm_mode_restrictions'])) {
            throw new SugarApiExceptionNotAuthorized();
        }

        $bean = $this->loadBean($api, $args, 'delete', $this->aclCheckOptions);
        $bean->mark_deleted($args['record']);

        return ['id' => $bean->id];
    }

    public function setFavorite(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'record']);
        $bean = $this->loadBean($api, $args, 'view');

        if (!$bean->ACLAccess('view', $this->aclCheckOptions)) {
            // No create access so we construct an error message and throw the exception
            $moduleName = null;
            if (isset($args['module'])) {
                $failed_module_strings = return_module_language($GLOBALS['current_language'], $args['module']);
                $moduleName = $failed_module_strings['LBL_MODULE_NAME'];
            }
            $args = null;
            if (!empty($moduleName)) {
                $args = ['moduleName' => $moduleName];
            }
            throw new SugarApiExceptionNotAuthorized('EXCEPTION_FAVORITE_MODULE_NOT_AUTHORIZED', $args);
        }

        $this->toggleFavorites($bean, true);
        $bean = BeanFactory::getBean($bean->module_dir, $bean->id, ['use_cache' => false]);
        $api->action = 'view';
        $data = $this->formatBean($api, $args, $bean);
        return $data;
    }

    public function unsetFavorite(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'record']);
        $bean = $this->loadBean($api, $args, 'view');

        if (!$bean->ACLAccess('view', $this->aclCheckOptions)) {
            // No create access so we construct an error message and throw the exception
            $moduleName = null;
            if (isset($args['module'])) {
                $failed_module_strings = return_module_language($GLOBALS['current_language'], $args['module']);
                $moduleName = $failed_module_strings['LBL_MODULE_NAME'];
            }
            $args = null;
            if (!empty($moduleName)) {
                $args = ['moduleName' => $moduleName];
            }
            throw new SugarApiExceptionNotAuthorized('EXCEPTION_FAVORITE_MODULE_NOT_AUTHORIZED', $args);
        }

        $this->toggleFavorites($bean, false);
        $bean = BeanFactory::getBean($bean->module_dir, $bean->id, ['use_cache' => false]);
        $api->action = 'view';
        $data = $this->formatBean($api, $args, $bean);
        return $data;
    }

    /**
     * Gets an array of additional related fields, to be set during bean relationship save
     *
     * @param array $args The request arguments.
     * @param SugarBean $bean The bean associated.
     * @return array Array of additional related fields
     */
    protected function getRelatedFields(array $args, SugarBean $bean)
    {
        $additional_rel_fields = [];

        foreach ($bean->field_defs as $fieldName => $fieldDef) {
            if (isset($fieldDef['rname_link']) && !empty($args[$fieldDef['name']])) {
                $additional_rel_fields[$fieldDef['rname_link']] = $args[$fieldDef['name']];
            }
        }
        return $additional_rel_fields;
    }

    /**
     * Moves temporary files associated with the bean from the temporary folder
     * to the upload folder.
     *
     * @param array $args The request arguments.
     * @param SugarBean $bean The bean associated with the file.
     * @throws SugarApiExceptionInvalidParameter If the file mime types differ
     *   from $imageFileMimeTypes.
     */
    protected function moveTemporaryFiles(array $args, SugarBean $bean)
    {

        $fileFields = $bean->getFieldDefinitions('type', ['file', 'image']);
        $sfh = new SugarFieldHandler();
        // FIXME This path should be changed with BR-1955.
        $basepath = UploadStream::path('upload://tmp/');

        foreach ($fileFields as $fieldName => $def) {
            if (empty($args[$fieldName . '_guid'])) {
                continue;
            }
            $this->verifyFieldAccess($bean, $fieldName);
            $filepath = $basepath . $args[$fieldName . '_guid'];

            if (!is_file($filepath)) {
                if (isset($bean->$fieldName)) {
                    $bean->$fieldName = null;
                }
                continue;
            }

            if ($def['type'] === 'image') {
                $filename = $args[$fieldName . '_guid'];
                $bean->$fieldName = $filename;
            } else {
                // FIXME Image verification and mime type updating
                // should not be duplicated from SugarFieldFile.
                // SC-3338 is tracking this.
                $filename = $bean->id;
                $mimeType = get_file_mime_type($filepath, 'application/octet-stream');
                $sf = $sfh->getSugarField($def['type']);
                $extension = pathinfo($bean->$fieldName, PATHINFO_EXTENSION);

                if (safeInArray($mimeType, $sf::$imageFileMimeTypes) &&
                    !verify_image_file($filepath)
                ) {
                    throw new SugarApiExceptionInvalidParameter(string_format(
                        $GLOBALS['app_strings']['LBL_UPLOAD_IMAGE_FILE_NOT_SUPPORTED'],
                        [$extension]
                    ));
                }

                $bean->file_mime_type = $mimeType;
                $bean->file_ext = $extension;
            }

            // FIXME BR-1956 will address having multiple files
            // associated with a record.
            $from = UploadStream::STREAM_NAME . '://tmp/' . $args[$fieldName . '_guid'];
            $to = UploadStream::STREAM_NAME . '://' . $filename;
            UploadStream::move_temp_file($from, $to);
        }
    }

    /**
     * Shared method from create and update process that handles records that
     * might not pass visibility checks. This method assumes the API has validated
     * the authorization to create/edit records prior to this point.
     *
     * @param ServiceBase $api The service object
     * @param array $args Request arguments
     * @return array Array of formatted fields
     */
    protected function getLoadedAndFormattedBean(ServiceBase $api, array $args)
    {
        $bean = $this->reloadBean($api, $args);
        $data = $this->formatBeanAfterSave($api, $args, $bean);

        return $data;
    }

    /**
     * Reloads the bean defined by arguments bypassing cache
     *
     * @param ServiceBase $api
     * @param array $args API arguments
     *
     * @return SugarBean
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionNotFound
     */
    protected function reloadBean(ServiceBase $api, array $args)
    {
        // Load the bean fresh to ensure the cache entry from the create process
        // doesn't get in the way of visibility checks
        return $this->loadBean($api, $args, 'view', ['use_cache' => false]);
    }

    /**
     * Formats the bean which was previously saved with admission that the bean may be not accessible
     * to the current user anymore
     *
     * @param ServiceBase $api
     * @param array $args API arguments
     * @param SugarBean $bean The saved bean
     *
     * @return array Formatted representation of the bean
     */
    protected function formatBeanAfterSave(ServiceBase $api, array $args, SugarBean $bean)
    {
        $api->action = 'view';
        $data = $this->formatBean($api, $args, $bean, [
            'display_acl' => true,
        ]);

        return $data;
    }

    /**
     * Process all after create operations:
     * copy_rel_from - Copies relationships from a specified record. The relationship that should be copied is specified
     *                 in the vardef.
     *
     * @param array $args
     * @param SugarBean $bean
     */
    protected function processAfterCreateOperations(array $args, SugarBean $bean)
    {
        $this->requireArgs($args, ['module']);

        global $dictionary;
        $afterCreateKey = 'after_create';
        $copyRelationshipsFromKey = 'copy_rel_from';
        $module = $args['module'];
        $objectName = BeanFactory::getObjectName($module);

        if (array_key_exists($afterCreateKey, $args)
            && array_key_exists($copyRelationshipsFromKey, $args[$afterCreateKey])
            && array_key_exists($afterCreateKey, $dictionary[$objectName])
            && array_key_exists($copyRelationshipsFromKey, $dictionary[$objectName][$afterCreateKey])
        ) {
            $relationshipsToCopy = $dictionary[$objectName][$afterCreateKey][$copyRelationshipsFromKey];
            $beanCopiedFrom = BeanFactory::getBean($module, $args[$afterCreateKey][$copyRelationshipsFromKey]);

            foreach ($relationshipsToCopy as $linkName) {
                $bean->load_relationship($linkName);
                $beanCopiedFrom->load_relationship($linkName);

                $beanCopiedFrom->$linkName->getBeans();
                $bean->$linkName->add($beanCopiedFrom->$linkName->beans);
            }
        }
    }

    /**
     * Returns arguments for RelateRecordApi for the given action
     *
     * @param SugarBean $bean Primary bean
     * @param array $args This API arguments
     * @param string $action Related record action.
     *
     * @return array
     * @throws SugarApiExceptionInvalidParameter
     */
    protected function getRelatedRecordArguments(SugarBean $bean, array $args, $action)
    {
        $arguments = [];
        foreach ($bean->getFieldDefinitions() as $field => $definition) {
            if (!isset($definition['type']) || $definition['type'] != 'link') {
                continue;
            }
            if (!isset($args[$field])) {
                continue;
            }
            if (!is_array($args[$field])) {
                throw new SugarApiExceptionInvalidParameter(
                    sprintf(
                        'Link field must contain array of actions, %s given',
                        gettype($field)
                    )
                );
            }
            if (!isset($args[$field][$action])) {
                continue;
            }

            $data = $args[$field][$action];

            if (!is_array($data)) {
                throw new SugarApiExceptionInvalidParameter(
                    sprintf(
                        'Link action data must be array, %s given',
                        gettype($data)
                    )
                );
            }

            $arguments[$field] = $data;
        }

        return $arguments;
    }

    /**
     * Links related records to the given bean
     *
     * @param ServiceBase $service
     * @param SugarBean $bean Primary bean
     * @param array $ids Related record IDs
     * @param string $securityTypeLocal What ACL to check on the near side of the link
     * @param string $securityTypeRemote What ACL to check on the far side of the link
     *
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarApiExceptionNotFound
     */
    protected function linkRelatedRecords(
        ServiceBase $service,
        SugarBean   $bean,
        array       $ids,
        $securityTypeLocal = 'view',
        $securityTypeRemote = 'view'
    ) {

        $api = $this->getRelateRecordApi();
        foreach ($ids as $linkName => $items) {
            if (!empty($items)) {
                $api->createRelatedLinks($service, [
                    'module' => $bean->module_name,
                    'record' => $bean->id,
                    'link_name' => $linkName,
                    'ids' => $items,
                ], $securityTypeLocal, $securityTypeRemote);
            }
        }
    }

    /**
     * Unlinks related records from the given bean
     *
     * @param ServiceBase $service
     * @param SugarBean $bean Primary bean
     * @param array $ids Related record IDs
     *
     * @throws SugarApiExceptionNotFound
     */
    protected function unlinkRelatedRecords(ServiceBase $service, SugarBean $bean, array $ids)
    {
        $api = $this->getRelateRecordApi();
        foreach ($ids as $linkName => $items) {
            foreach ($items as $id) {
                $api->deleteRelatedLink($service, [
                    'module' => $bean->module_name,
                    'record' => $bean->id,
                    'link_name' => $linkName,
                    'remote_id' => $id,
                ]);
            }
        }
    }

    /**
     * Creates related records for the given bean
     *
     * @param ServiceBase $service
     * @param SugarBean $bean Primary bean
     * @param array $data New record data
     */
    protected function createRelatedRecords(ServiceBase $service, SugarBean $bean, array $data)
    {
        $api = $this->getRelateRecordApi();
        foreach ($data as $linkName => $records) {
            foreach ($records as $record) {
                $api->createRelatedBean(
                    $service,
                    array_merge($record, [
                        'module' => $bean->module_name,
                        'record' => $bean->id,
                        'link_name' => $linkName,
                    ]),
                    $bean
                );
            }
        }
    }

    /**
     * Lazily loads RelateRecord API
     *
     * @return RelateRecordApi
     */
    protected function getRelateRecordApi()
    {
        if (!$this->relateRecordApi) {
            $this->relateRecordApi = new RelateRecordApi();
        }

        return $this->relateRecordApi;
    }

    /**
     * Is IDM mode enabled?
     * @return bool
     */
    protected function isIDMModeEnabled()
    {
        return $this->isIDMModeEnabled;
    }
}
