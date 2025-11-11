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

namespace Sugarcrm\Sugarcrm\SugarConnect\Bean;

/**
 * @deprecated Will be removed in the next release.
 */
class Event extends SugarBean
{
    /**
     * Determines how to handle bean events for Meetings and Calls.
     *
     * @param \SugarBean $bean The bean that was changed.
     * @param string $event The type of event.
     * @param array $args Additional arguments.
     *
     * @return void
     */
    public function publish(\SugarBean $bean, string $event, array $args): void
    {
        // Don't announce changes to events that belong to a series.
        if (!empty($bean->repeat_type)) {
            return;
        }

        $relEvents = [
            'after_relationship_add',
            'after_relationship_update',
            'after_relationship_delete',
        ];

        if (in_array($event, $relEvents)) {
            // We only care about changes to attendees.
            if (!in_array($args['link'], ['contacts', 'leads', 'users'])) {
                return;
            }
        }

        parent::publish($bean, $event, $args);
    }
}
