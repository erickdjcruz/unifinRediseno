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
 * Class SugarUpgradeUpdateCaseInteractionsDashlet
 */
class SugarUpgradeUpdateCaseInteractionsDashlet extends UpgradeScript
{
    public $order = 7560;
    public $type = self::UPGRADE_DB;

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        if (version_compare($this->from_version, '10.2.0', '<')) {
            $this->addMessageCreateAction('c290ef46-7606-11e9-9129-f218983a1c3e');
        } else {
            $this->log('No changes to be made to case interactions dashboard.');
        }
    }

    /**
     * Gets a dashboard bean if one can be found and loaded for the ID
     * @param string $id The ID of the dashboard bean to get
     * @return SugarBean if found, null otherwise
     */
    protected function getDashboardBean(string $id): ?SugarBean
    {
        // Read the Dashoard we want to modify, wrapped inside a try catch because
        // some beans have before retrieve hooks in place that throw exceptions
        try {
            $dashboard = BeanFactory::retrieveBean('Dashboards', $id);
            if (!$dashboard) {
                $this->log("Could not retrieve dashboard id $id");
                return null;
            }

            return $dashboard;
        } catch (Exception $e) {
            $this->log("Could not collect dashboard id $id");
            return null;
        }
    }

    /**
     * Returns the metadata of dashlet message create link.
     *
     * @return {Object}
     */
    protected function getMessageCreateLinkMeta()
    {
        return [
            'type' => 'dashletaction',
            'action' => 'createRecord',
            'params' => [
                'link' => 'messages',
                'module' => 'Messages',
            ],
            'label' => 'LBL_CREATE_MESSAGE',
            'icon' => 'sicon-comment-lg',
            'acl_action' => 'create',
            'acl_module' => 'Messages',
        ];
    }

    /**
     * Determines if a metadata component object needs to have message create link
     * @param stdClass $buttons
     * @return boolean
     */
    protected function needsMessageCreateLink($buttons): bool
    {
        foreach ($buttons as $button) {
            if ($button->label === 'LBL_CREATE_MESSAGE') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $id
     */
    protected function addMessageCreateAction($id)
    {
        if (($dashboard = $this->getDashboardBean($id)) === null) {
            return;
        }

        $meta = json_decode($dashboard->metadata);
        $rows = $meta->components[0]->rows ?? [];

        // Iterate dashlets and find any activity-timeline dashlets
        $hasChanges = false;
        foreach ($rows as $row) {
            // Each row will contain a dashlet
            foreach ($row as $d) {
                if (isset($d->view->type) && $d->view->type === 'activity-timeline') {
                    // Now we have an activity timelines dashlet. The next step is to check if it has
                    // the dashlet "Create Message" action and if not, add it.
                    if (isset($d->view->custom_toolbar) && isset($d->view->custom_toolbar->buttons)) {
                        foreach ($d->view->custom_toolbar->buttons as $buttons) {
                            if (isset($buttons->buttons) && $this->needsMessageCreateLink($buttons->buttons)) {
                                $hasChanges = true;
                                array_push($buttons->buttons, $this->getMessageCreateLinkMeta());
                            }
                        }
                    }
                }
            }
        }

        // If we have added the message create link to the metadata, save it.
        if ($hasChanges) {
            $dashboard->metadata = json_encode($meta);
            $dashboard->save();
            $this->log("Updated dashboard $id.");
        }
    }
}
