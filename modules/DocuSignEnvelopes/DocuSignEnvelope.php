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

class DocuSignEnvelope extends Basic
{
    public $module_dir = 'DocuSignEnvelopes';
    public $object_name = 'DocuSignEnvelope';
    public $table_name = 'docusign_envelopes';
    public $module_name = 'DocuSignEnvelopes';

    public $id;
    public $name;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $modified_by_name;
    public $created_by;
    public $created_by_name;
    public $completed_document;
    public $user_favorites;
    public $description;
    public $deleted;
    public $created_by_link;
    public $modified_user_link;
    public $activities;
    public $following;
    public $following_link;
    public $my_favorite;
    public $favorite_link;
    public $status;
    public $envelope_id;
    public $parent_name;
    public $parent_id;
    public $parent_type;
    public $last_audit;
    public $team_id;
    public $team_set_id;
    public $team_count;
    public $team_name;
    public $team_link;
    public $team_count_link;
    public $teams;
    public $assigned_user_id;
    public $assigned_user_name;
    public $assigned_user_link;
    public $tag;

    public $importable = true;

    public $cloud_service_type;
    public $driveId;
    public $cloud_path;

    /**
     * @inheritDoc
     */
    public function bean_implements($interface): bool
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }

        return false;
    }
}
