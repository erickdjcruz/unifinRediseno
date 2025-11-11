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

class ExternalUser extends Person
{
    public $module_dir = 'ExternalUsers';
    public $module_name = 'ExternalUsers';
    public $object_name = 'ExternalUser';
    public $table_name = 'external_users';

    /**
     * @inheritDoc
     */
    public function isACLRoleEditable()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     * @see Person::mark_deleted()
     */
    public function mark_deleted($id)
    {
        if (!empty($this->parent_type) && !empty($this->parent_id)) {
            $parentBean = BeanFactory::getBean($this->parent_type, $this->parent_id);
            if ($parentBean) {
                $parentBean->external_user_id = null;
                $parentBean->save();
            }
        }
        parent::mark_deleted($id);
    }
}
