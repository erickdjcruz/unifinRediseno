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

/**
 * Copy guests from all existing Messages to use the new invitee Contact relationship.
 *
 * The previous relationship is left intact, only copied
 */
class SugarUpgradeMigrateMessageGuests extends UpgradeScript
{
    public $order = 7500;
    public $type = self::UPGRADE_ALL;

    private $inviteeField = [
        'name' => 'invitees',
        'type' => 'participants',
        'label' => 'LBL_INVITEES',
        'fields' => [
            'name',
            'accept_status_messages',
            'picture',
            'email',
        ],
        'related_fields' => [
            'date_start',
            'date_end',
        ],
        'max_num' => 20,
        'span' => 12,
    ];

    private $layoutMap = [
        'record' => MB_RECORDVIEW,
        'recorddashlet' => MB_RECORDDASHLETVIEW,
        'preview' => MB_PREVIEWVIEW,
    ];

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (version_compare($this->from_version, '11.1', '<')) {
            $this->log('Migrating guests relationship for Messages');
            $this->migrateRelationship();

            foreach (['record', 'recorddashlet', 'preview'] as $layout) {
                if ($this->layoutRequiresUpdate($layout)) {
                    $this->log('Updating Messages layout: ' . $layout);
                    $this->updateLayout($layout);
                } else {
                    $this->log('Skipping updating Messages layout: ' . $layout);
                }
            }
        }
    }

    /**
     * Copies the existing guest relationship over to the new invitees-linked relationship.
     *
     * We need to touch every Message that has an assigned user and/or has a contact. The invitees list
     * automatically adds the assigned user, and we need to make sure that behavior is kept.
     */
    private function migrateRelationship()
    {
        // Get all Messages that either have a guest or have an assigned user
        $q = new SugarQuery();
        $q->select(['id']);
        $q->from(BeanFactory::newBean('Messages'));
        $q->where()->queryOr()
            ->isNotEmpty('assigned_user_id')
            ->isNotEmpty('contact_id');
        $results = $q->execute();

        $ids = array_column($results, 'id');

        if (empty($ids)) {
            $this->log('No Messages to fix');
            return;
        }

        $this->log('Found Messages to fix: ' . sizeof($ids));

        foreach ($ids as $id) {
            $message = BeanFactory::retrieveBean('Messages', $id);

            if (!empty($message->contact_id)) {
                if ($message->load_relationship('invitee_contacts')) {
                    $message->invitee_contacts->add($message->contact_id);
                }
            }

            // Saving automatically adds the assigned user to the invitees
            $message->save();
        }
    }

    /**
     * Checks if we need to update the metadata for the given layout. We need to if the user has customized it.
     * @param string $layout The layout to check
     * @return bool
     */
    private function layoutRequiresUpdate($layout)
    {
        return file_exists('custom/modules/Messages/clients/base/views/' . $layout . '/' . $layout . '.php');
    }

    /**
     * Performs the layout update, adding the invitees field to the end of the metadata
     * @param string $layout
     */
    private function updateLayout($layout)
    {
        $parser = ParserFactory::getParser($this->layoutMap[$layout], 'Messages', null, null, 'base');

        $availableFieldNames = array_column($parser->getAvailableFields(), 'name');
        if (in_array('invitees', $availableFieldNames)) {
            $parser->addField($this->inviteeField);
            $parser->handleSave(false, true);
        } else {
            $this->log('Invitees field already added for layout: ' . $layout);
        }

        $this->fixInviteeFieldWidth($layout);
    }

    /**
     * Extends the invitee field to the full width
     * @param $layout
     */
    private function fixInviteeFieldWidth($layout)
    {
        $impl = new DeployedMetaDataImplementation($this->layoutMap[$layout], 'Messages', 'base', []);

        // This should always be the second panel, but search to be sure
        $viewdef = $impl->getViewdefs();
        $panels = $viewdef['base']['view'][$layout]['panels'];
        $panelIdx = array_search('panel_body', array_column($panels, 'name'));
        $panelFields = $panels[$panelIdx]['fields'];

        // Again, this should always be the last field, but it doesn't hurt to double check
        $fieldIdx = -1;
        foreach ($panelFields as $idx => $field) {
            if (is_array($field) && !empty($field['name']) && $field['name'] === 'invitees') {
                $fieldIdx = $idx;
                break;
            }
        }

        $viewdef['base']['view'][$layout]['panels'][$panelIdx]['fields'][$fieldIdx]['span'] = 12;
        $impl->deploy($viewdef);
    }
}
