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

class ext_eapm_microsoft extends source
{
    //@codingStandardsIgnoreStart
    protected $_required_config_fields = [
        'oauth2_client_id',
        'oauth2_client_secret',
        'oauth2_single_tenant_id',
    ];
    //@codingStandardsIgnoreEnd

    /**
     * The name of checkbox field and all the related fields inside the container block
     */
    protected array $visibilityCheckBoxConfigForFields = [
        'oauth2_single_tenant_enabled' => [
            'oauth2_single_tenant_id',
        ],
    ];

    /**
     * Overrides parent __construct to set new variable defaults
     */
    public function __construct()
    {
        parent::__construct();
        $this->_enable_in_wizard = false;
        $this->_enable_in_hover = false;
        $this->_has_testing_enabled = false;
    }

    /**
     * getItem is not used by this connector
     */
    public function getItem($args = [], $module = null)
    {
    }

    /**
     * getList is not used by this connector
     */
    public function getList($args = [], $module = null)
    {
    }

    /**
     * @inheritDoc
     */
    public function isRequiredConfigFieldsSet()
    {
        //Check if required fields are set
        foreach ($this->_required_config_fields as $field) {
            // skip checkbox
            if (isset($this->visibilityCheckBoxConfigForFields[$field])) {
                continue;
            }
            // skip if related checkbox is turned off
            foreach ($this->visibilityCheckBoxConfigForFields as $checkBoxField => $checkBoxFields) {
                if (safeInArray($field, $checkBoxFields) && empty($this->_config['properties'][$checkBoxField])) {
                    continue(2);
                }
            }

            if (empty($this->_config['properties'][$field])) {
                return false;
            }
        }
        return true;
    }
}
