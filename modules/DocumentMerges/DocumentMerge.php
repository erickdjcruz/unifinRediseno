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

class DocumentMerge extends Basic
{
    public $id;
    public $name;
    public $date_entered;
    public $created_by;
    public $created_by_name;
    public $table_name = 'document_merges';
    public $object_name = 'DocumentMerge';
    public $module_dir = 'DocumentMerges';
    public $new_schema = true;

    /**
     * Make sure the bean implements ACL so it can be modified from Roles
     *
     * @param string $interface
     */
    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }
}
