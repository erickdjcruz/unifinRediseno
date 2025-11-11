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

use Sugarcrm\Sugarcrm\CustomerJourney\Bean\WebHook\Request;

class CJ_WebHook extends Basic
{
    public const REQUEST_METHOD_GET = 'GET';
    public const REQUEST_METHOD_POST = 'POST';
    public const REQUEST_METHOD_PUT = 'PUT';
    public const REQUEST_METHOD_PATCH = 'PATCH';
    public const REQUEST_METHOD_DELETE = 'DELETE';
    public const TRIGGER_EVENT_BEFORE_CREATE = 'before_create';
    public const TRIGGER_EVENT_AFTER_CREATE = 'after_create';
    public const TRIGGER_EVENT_BEFORE_DELETE = 'before_delete';
    public const TRIGGER_EVENT_AFTER_DELETE = 'after_delete';
    public const TRIGGER_EVENT_BEFORE_IN_PROGRESS = 'before_in_progress';
    public const TRIGGER_EVENT_AFTER_IN_PROGRESS = 'after_in_progress';
    public const TRIGGER_EVENT_BEFORE_COMPLETED = 'before_completed';
    public const TRIGGER_EVENT_AFTER_COMPLETED = 'after_completed';
    public const TRIGGER_EVENT_BEFORE_NOT_APPLICABLE = 'before_not_applicable';
    public const TRIGGER_EVENT_AFTER_NOT_APPLICABLE = 'after_not_applicable';
    public const REQUEST_FORMAT_JSON = 'json';
    public const REQUEST_FORMAT_HTTP_QUERY = 'http_query';
    public const REQUEST_BODY_JOURNEY = 'journey_body';
    public const REQUEST_BODY_CUSTOM = 'custom_body';
    public const RESPONSE_FORMAT_JSON = 'json';
    public const RESPONSE_FORMAT_HTTP_QUERY = 'http_query';
    public const RESPONSE_FORMAT_TEXT = 'text';

    public $disable_row_level_security = false;
    public $new_schema = true;
    public $module_dir = 'CJ_WebHooks';
    public $object_name = 'CJ_WebHook';
    public $table_name = 'cj_web_hooks';
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
    public $url;
    public $request_method;
    public $request_format;
    public $request_body;
    public $custom_post_body;
    public $response_format;
    public $trigger_event;
    public $error_message_path;
    public $headers;
    public $ignore_errors;
    public $parent_type;
    public $parent_id;
    public $parent_name;
    public $sort_order;

    /**
     * Send the webhooks
     *
     * @param SugarBean $parent
     * @param string $triggerEvent
     * @param array $data
     * @throws SugarApiException
     * @throws SugarQueryException
     */
    public static function send(\SugarBean $parent, string $triggerEvent, array $data)
    {
        $GLOBALS['log']->debug("Sending web hook: {$parent->module_dir}:$triggerEvent:{$parent->id}");

        $results = self::getWebHooksByParent($parent->module_dir, $triggerEvent, $parent->id);

        foreach ($results as $result) {
            /** @var \CJ_WebHook $webHook */
            $webHook = BeanFactory::retrieveBean('CJ_WebHooks', $result['id']);

            if ($webHook) {
                $data['trigger_event'] = $triggerEvent;
                $data['web_hook_id'] = $webHook->id;
                $data['current_user_id'] = $GLOBALS['current_user']->id;
                $request = new Request($webHook);
                $request->send($data);
            }
        }
    }

    /**
     * Copy the webhooks
     *
     * @param SugarBean $parent
     * @param SugarBean $parentBase
     */
    public static function copyWebHooks(SugarBean $parent, SugarBean $parentBase)
    {
        $parentBase->load_relationship('web_hooks');
        foreach ($parentBase->web_hooks->getBeans() as $webHookBase) {
            /** @var \CJ_WebHook $webHook */
            $webHook = clone $webHookBase;
            $webHook->id = \Sugarcrm\Sugarcrm\Util\Uuid::uuid4();
            $webHook->new_with_id = true;
            $webHook->parent_id = $parent->id;
            $webHook->parent_name = $parent->name;
            $webHook->parent_type = $parent->module_dir;
            $webHook->save();
            BeanFactory::unregisterBean($webHook);
        }
    }

    /**
     * Delete the webhooks
     *
     * @param SugarBean $parent
     */
    public static function deleteWebHooks(SugarBean $parent)
    {
        $parent->load_relationship('web_hooks');
        foreach ($parent->web_hooks->getBeans() as $webHook) {
            /** @var \CJ_WebHook $webHook */
            $webHook->mark_deleted($webHook->id);
        }
    }

    /**
     * Get the webhooks by parent
     *
     * @param string $parentType
     * @param string $parentId
     * @param string $triggerEvent
     * @return array
     * @throws SugarQueryException
     */
    private static function getWebHooksByParent(string $parentType, string $triggerEvent, $parentId = '')
    {
        $key = self::getWebHooksByParentCacheKey($parentType, $parentId, $triggerEvent);
        $results = sugar_cache_retrieve($key);

        if (is_array($results)) {
            return $results;
        }

        $query = new SugarQuery();
        $query->from(BeanFactory::newBean('CJ_WebHooks'));
        $query->select('id');
        $query->orderBy('sort_order', 'asc');
        $query->where()
            ->equals('active', true)
            ->equals('trigger_event', $triggerEvent)
            ->equals('parent_type', $parentType)
            ->equals('parent_id', $parentId);

        $results = $query->execute();
        $keyExpireTimeOut = 0;
        sugar_cache_put($key, $results, $keyExpireTimeOut);

        return $results;
    }

    /**
     * Get the webhooks by parent cache key
     *
     * @param $parentType
     * @param $parentId
     * @param $triggerEvent
     * @return string
     */
    private static function getWebHooksByParentCacheKey($parentType, $parentId, $triggerEvent)
    {
        return "CJ_WebHook::getWebHooksByParent[$parentType][$triggerEvent][$parentId]";
    }

    /**
     * Clear the webhooks cache keys
     *
     * @param string $parentType
     * @param string $parentId
     * @param string $triggerEvent
     * @return string
     */
    private static function clearWebHooksByParentCache($parentType, $parentId, $triggerEvent)
    {
        $key = self::getWebHooksByParentCacheKey($parentType, $parentId, $triggerEvent);
        $GLOBALS['log']->debug("Clearing CJ Web Hook cache key: $key");
        sugar_cache_clear($key);
    }

    /**
     * {@inheritdoc}
     **/
    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function save($check_notify = false)
    {
        $return = parent::save($check_notify);

        self::clearWebHooksByParentCache(
            $this->parent_type,
            $this->parent_id,
            $this->trigger_event
        );

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function mark_deleted($id)
    {
        parent::mark_deleted($id);

        self::clearWebHooksByParentCache(
            $this->parent_type,
            $this->parent_id,
            $this->trigger_event
        );
    }
}
