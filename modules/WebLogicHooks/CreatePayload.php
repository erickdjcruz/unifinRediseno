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

class CreatePayload
{
    public const TYPE_CHANGELIST = 'changelist';
    public const TYPE_BOOL = 'bool';
    public const TYPE_STRING = 'string';

    /** @var WebLogicHook */
    private $current_hook;

    /** @var array fields from regular hook arguments which will be passed to webhook */
    private $eventFields = [
        'after_save' => [
            'isUpdate' => self::TYPE_BOOL,
            'dataChanges' => self::TYPE_CHANGELIST,
            'stateChanges' => self::TYPE_CHANGELIST,
        ],
        'after_delete' => [
            'id' => self::TYPE_STRING,
        ],
        'after_relationship_add' => [
            'id' => self::TYPE_STRING,
            'related_id' => self::TYPE_STRING,
            'name' => self::TYPE_STRING,
            'related_name' => self::TYPE_STRING,
            'module' => self::TYPE_STRING,
            'related_module' => self::TYPE_STRING,
            'link' => self::TYPE_STRING,
            'relationship' => self::TYPE_STRING,
        ],
        'after_relationship_delete' => [
            'id' => self::TYPE_STRING,
            'related_id' => self::TYPE_STRING,
            'name' => self::TYPE_STRING,
            'related_name' => self::TYPE_STRING,
            'module' => self::TYPE_STRING,
            'related_module' => self::TYPE_STRING,
            'link' => self::TYPE_STRING,
            'relationship' => self::TYPE_STRING,
        ],
        'after_login' => [],
        'after_logout' => [],
        'after_login_failed' => [],
    ];

    public function __construct(WebLogicHook $hook)
    {
        $this->current_hook = $hook;
    }

    /**
     * Convert linked beans to array using toArray() function
     * Remove nested beans
     *
     * @param Link2 $l
     * @return array
     */
    private function link2ToArray(Link2 $l): array
    {
        return array_map(
            function (SugarBean $bean) {
                /* convert bean to array and remove nested beans */
                return array_filter(
                    $bean->toArray(),
                    function ($value) {
                        return !($value instanceof Link2);
                    }
                );
            },
            $l->getBeans()
        );
    }

    /**
     * Decode html tags in data
     *
     * @param array $data
     * @return array
     */
    private function decodeHTML($data)
    {
        $returnData = [];

        $db = DBManagerFactory::getInstance();
        foreach ($data as $key => $value) {
            $returnData[$key] = $db->decodeHTML($value);
            if (is_array($value)) {
                $returnData[$key] = $this->decodeHTML($value);
            }
        }

        return $returnData;
    }

    private function convertChangeList(array $data): array
    {
        /* All linked beans convert to array */
        $tmp = array_map(
            function (array $field) {
                if ($field['data_type'] === 'link') {
                    if ($field['before'] instanceof Link2) {
                        $field['before'] = $this->link2ToArray($field['before']);
                    }
                    if ($field['after'] instanceof Link2) {
                        $field['after'] = $this->link2ToArray($field['after']);
                    }
                };
                return $field;
            },
            $data
        );
        return $this->decodeHTML($tmp);
    }

    /**
     * Create data to be serialized in JSON format
     *
     * @param SugarBean $bean
     * @param string $event
     * @param array $arguments
     * @return array
     */
    public function getPayload(SugarBean $bean, string $event, array $arguments): array
    {
        global $current_user;

        $data = [];
        $sfh = new SugarFieldHandler();

        $result = [];
        $result['bean'] = get_class($bean);

        if (isset($bean->id)) {
            $data['id'] = $bean->id;
        }

        if (!SugarACL::moduleSupportsACL($bean->webhook_target_module) || $bean->ACLAccess('detail')) {
            $fieldList = $bean->field_defs;

            $this->current_hook->ACLFilterFieldList($fieldList, ['bean' => $bean]);

            $service = new RestService();
            $service->user = $current_user;
            foreach ($fieldList as $fieldName => $properties) {
                $fieldType = !empty($properties['custom_type']) ? $properties['custom_type'] : $properties['type'];
                $field = $sfh->getSugarField($fieldType);
                if ('link' !== $fieldType && !empty($field) && (isset($bean->$fieldName) || 'relate' === $fieldType)) {
                    $field->apiFormatField($data, $bean, [], $fieldName, $properties, [], $service);
                }
            }
        }

        $result['data'] = $this->decodeHTML($data);
        $result['event'] = $event;

        foreach (($this->eventFields[$event] ?? []) as $fieldName => $fieldType) {
            if (isset($arguments[$fieldName])) {
                switch ($fieldType) {
                    case self::TYPE_CHANGELIST:
                        $result[$fieldName] = $this->convertChangeList($arguments[$fieldName]);
                        break;
                    case self::TYPE_BOOL:
                    case self::TYPE_STRING:
                    default:
                        $result[$fieldName] = $arguments[$fieldName];
                }
            }
        }

        return $result;
    }
}
