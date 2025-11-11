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
 * Will make 2 sets of changes:
 * 1. Swaps the side-drawer type to one which has capability to switch the drawer dashboard to edit mode.
 * 2. Will re-add the remove buttons to the activity timeline dashlets for the above dashboards.
 * The changes are necessary to be able to render the console dashboard multi line list side drawer dashboards
 * in edit mode.
 * This script is also supposed to take into account and client made modifications.
 */
class SugarUpgradeEnableConsoleDashboardEdits extends UpgradeScript
{
    public $order = 7560;
    public $type = self::UPGRADE_DB;

    private $consoleIDs = [
        'c108bb4a-775a-11e9-b570-f218983a1c3e', // Service Console
        'da438c86-df5e-11e9-9801-3c15c2c53980', // Renewals Console
    ];
    private $multiLineDashBoardIDs = [
        'c290ef46-7606-11e9-9129-f218983a1c3e', // Cases
        'd8f610a0-e950-11e9-81b4-2a2ae2dbcce4', // Accounts
        '069a1142-61bf-473f-8014-faca9aaf43cf', // Opportunities
    ];

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $isUpgradeTo1010 = version_compare($this->from_version, '10.1.0', '<');
        if ($isUpgradeTo1010 && $this->toFlavor('ent')) {
            // Consoles...
            foreach ($this->consoleIDs as $id) {
                $this->changeSideDrawerType($id);
            }

            // Multiline list views...
            foreach ($this->multiLineDashBoardIDs as $id) {
                $this->addDashletRemoveButton($id);
            }

            $this->log('Console dashboards update process complete.');
        } else {
            $this->log('No changes to be made to console dashboards.');
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
                $this->log("Could not retrieve console component id $id");
                return null;
            }

            return $dashboard;
        } catch (Exception $e) {
            $this->log("Could not collect console component id $id");
            return null;
        }
    }

    /**
     * On console dashboards changes the side drawer type to one that supports dashboard editing.
     *
     * @param {String} $id The id of a dashboard.
     */
    public function changeSideDrawerType($id)
    {
        if (($dashboard = $this->getDashboardBean($id)) === null) {
            return;
        }

        $meta = json_decode($dashboard->metadata);

        // Iterate dashlets and find any tabs with side drawer layouts.
        // When found change the type to console-side-drawer.
        $hasChanges = false;
        foreach ($meta->tabs as $tab) {
            foreach ($tab->components as $component) {
                if (isset($component->layout->type) && $component->layout->type === 'side-drawer') {
                    $hasChanges = true;
                    $component->layout->type = 'console-side-drawer';
                }
            }
        }

        // If we have changed the side drawer type, save it.
        if ($hasChanges) {
            $dashboard->metadata = json_encode($meta);
            $dashboard->save();
            $this->log("Updated dashboard $id from side-drawer type to console-side-drawer.");
        } else {
            $this->log("No changes made to drawer type for dashboard $id.");
        }
    }

    /**
     * Returns the metadata of dashlet remove button.
     *
     * @return {Object}
     */
    public function getRemoveDashletButtonMeta()
    {
        $removeButton = new StdClass();
        $removeButton->type = 'dashletaction';
        $removeButton->action = 'removeClicked';
        $removeButton->label = 'LBL_DASHLET_REMOVE_LABEL';
        $removeButton->name = 'remove_button';
        return $removeButton;
    }

    /**
     * Determines if a metadata component object needs to have buttons added
     * @param stdClass $buttons
     * @return boolean
     */
    protected function needsButton($buttons): bool
    {
        foreach ($buttons as $button) {
            if ($button->action === 'removeClicked') {
                return false;
            }
        }

        return true;
    }

    /**
     * In previous versions activity timeline dashlet on console dashboards was missing the remove.
     * In order to fully support the editing of the console dashboards multi line list view side drawer dashboard's
     * edit mode, we add back the remove buttons to all instances of activity timelines dashlets.
     */
    public function addDashletRemoveButton($id)
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
                    // the dashlet remove button and if not, add it.
                    if (isset($d->view->custom_toolbar) && isset($d->view->custom_toolbar->buttons)) {
                        foreach ($d->view->custom_toolbar->buttons as $buttons) {
                            if (isset($buttons->dropdown_buttons) && $this->needsButton($buttons->dropdown_buttons)) {
                                $hasChanges = true;
                                array_push($buttons->dropdown_buttons, $this->getRemoveDashletButtonMeta());
                            }
                        }
                    }
                }
            }
        }

        // If we have added the remove button to the metadata, save it.
        if ($hasChanges) {
            $dashboard->metadata = json_encode($meta);
            $dashboard->save();
            $this->log("Updated dashboard $id so activity-timeline dashlet can be removed.");
        } else {
            $this->log("No changes made to dashlets for dashboard $id.");
        }
    }
}
