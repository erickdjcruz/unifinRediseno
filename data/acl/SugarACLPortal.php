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


use Sugarcrm\Sugarcrm\Portal\Factory as PortalFactory;

/**
 * Static ACL implementation - ACLs defined per-module
 * Uses ACLController and ACLAction
 */
class SugarACLPortal extends SugarACLStatic
{
    protected $modulesToDenyPortalList = [
        'Users',
        'Employees',
        'OAuthTokens',
        'EmailAddresses',
        'Tags',
        'SugarFavorites',
        'Audit',
        'Teams',
    ];

    /**
     * Determines if a portal user "owns" a record
     * @param SugarBean $bean
     * @return bool
     */
    protected function isPortalOwner(SugarBean $bean)
    {
        // wherever possible, check if the current contact is the creator and therefore owner of the record
        if (empty($bean->id) || $bean->new_with_id) {
            // New record, they are the owner.
            $bean->portal_owner = true;
        }
        // Cache portal owner on bean so that we aren't loading Contacts for each ACL check
        // Performance Bug58133
        if (!isset($bean->portal_owner)) {
            $moduleName = $bean->getModuleName();
            switch ($moduleName) {
                case 'Contacts':
                    $bean->portal_owner = $bean->id === PortalFactory::getInstance('Session')->getContactId();
                    break;
                    // Cases & Bugs work the same way, so handily enough we can share the code.
                case 'Cases':
                case 'Bugs':
                    $bean->load_relationship('contacts');
                    $rows = $bean->contacts->query([
                        'where' => [
                            // query adds the prefix so we don't need contact.id
                            'lhs_field' => 'id',
                            'operator' => '=',
                            'rhs_value' => \DBManagerFactory::getInstance()->quote(PortalFactory::getInstance('Session')->getContactId()),
                        ],
                    ]);
                    $bean->portal_owner = safeCount($rows['rows']) > 0;
                    break;
                case 'Notes':
                    $bean->portal_owner = $bean->contact_id === PortalFactory::getInstance('Session')->getContactId();
                    break;
                default:
                    // Unless we know how to find the "owner", they can't own it.
                    $bean->portal_owner = false;
            }
        }
        return $bean->portal_owner;
    }

    /**
     * Handles the special access controls of the portal system
     * primarily disabling editing of records while allowing for record creation
     *
     * @param string $module
     * @param string $action
     * @param array $context THIS IS MODIFIED, owner_override is modified
     *  it is set according to if the portal user is the "owner" of this object
     * @return bool|null
     */
    protected function portalAccess($module, $action, &$context)
    {
        // Leave this set to null to let the decision be handled by the parent
        $accessGranted = null;

        if (PortalFactory::getInstance('Session')->isActive()) {
            $bean = $context['bean'] ?? BeanFactory::newBean($module);
            if (!$bean) {
                // There is no bean, without a bean portal ACL's wont work
                // So for security we will deny the request
                return false;
            }

            $context['owner_override'] = $this->isPortalOwner($bean);

            if (isset(self::$action_translate[$action])) {
                $action = self::$action_translate[$action];
            }

            // Only allow users to create records, never edit, for everything but Contacts
            if ($bean->module_name !== 'Contacts') {
                if ($action === 'edit' && !empty($bean->id) && !$bean->new_with_id) {
                    return false;
                }
            } else {
                // Can't create new Contacts
                if ($action === 'edit' && (empty($bean->id) || $bean->new_with_id)) {
                    return false;
                }
            }
            // Allow users to delete, edit, and view notes they own
            // to facilitate adding/removing attachments from a Note
            if ($bean->module_name === 'Notes' && $context['owner_override']) {
                return true;
            }
        }

        return $accessGranted;
    }

    public static $action_translate = [
        'listview' => 'list',
        'index' => 'list',
        'popupeditview' => 'edit',
        'editview' => 'edit',
        'detail' => 'view',
        'detailview' => 'view',
        'save' => 'edit',
        'create' => 'edit',
    ];

    /**
     * Check access to fields
     * @param string $module
     * @param string $action
     * @param array $context
     * @return bool
     */
    protected function fieldACL($module, $action, $context)
    {
        $accessGranted = $this->portalAccess($module, $action, $context);

        // Handle file and image type field checking here, specifically for creates
        if ($accessGranted === false && $action === 'create') {
            $bean = $context['bean'] ?? null;

            // If there is a bean, and a field name and defs for that fieldname...
            if ($bean && isset($context['field']) && isset($bean->field_defs[$context['field']])) {
                $field = $context['field'];
                $def = $bean->field_defs[$field];

                // If the field type is an image or file
                if (isset($def['type']) && ($def['type'] === 'image' || $def['type'] === 'file')) {
                    // And the value for this field in the bean is empty, it is
                    // a create, which should make accessGranted = null
                    if (empty($bean->$field)) {
                        $accessGranted = null;
                    }
                }
            }
        }

        if (!isset($accessGranted)) {
            $module = ($module === 'Categories') ? 'KBContents' : $module;
            $accessGranted = parent::fieldACL($module, $action, $context);
        }

        return $accessGranted;
    }

    /**
     * Check bean ACLs
     * @param string $module
     * @param string $action
     * @param array $context
     * @return bool
     */
    protected function beanACL($module, $action, $context)
    {
        $accessGranted = $this->portalAccess($module, $action, $context);

        if (!isset($accessGranted)) {
            $module = ($module === 'Categories') ? 'KBContents' : $module;

            // block listviews for the list of modules defined in modulesToDenyPortalList
            if ($action === 'list' && safeInArray($module, $this->modulesToDenyPortalList)) {
                return false;
            }

            $accessGranted = parent::beanACL($module, $action, $context);
        }

        return $accessGranted;
    }
}
