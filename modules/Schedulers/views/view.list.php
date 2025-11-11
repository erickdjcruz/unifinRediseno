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


class SchedulersViewList extends ViewList
{
    public function display()
    {
        parent::display();
        $this->seed->displayCronInstructions();
    }

    /**
     * process list view
     */
    public function listViewProcess()
    {
        if (!is_array($this->params)) {
            $this->params = [];
        }
        if (empty($this->params['custom_where'])) {
            $this->params['custom_where'] = '';
        }

        $this->params['custom_where'] .= sprintf(" AND %s.system_job <> '1'", $this->bean->getTableName());
        if (!hasHintLicense()) {
            $this->params['custom_where'] .= sprintf(" AND %s.name <> 'Hint Init Job'", $this->bean->getTableName());
            $this->params['custom_where'] .= sprintf(" AND %s.name <> 'Hint Register Config Job'", $this->bean->getTableName());
            $this->params['custom_where'] .= sprintf(" AND %s.name <> 'Hint Seats Job'", $this->bean->getTableName());
            $this->params['custom_where'] .= sprintf(" AND %s.name <> 'Hint User Init Job'", $this->bean->getTableName());
            $this->params['custom_where'] .= sprintf(" AND %s.name <> 'Hint News Job'", $this->bean->getTableName());
        }
        return parent::listViewProcess();
    }
}
