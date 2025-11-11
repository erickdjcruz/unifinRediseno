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

namespace Sugarcrm\Sugarcrm\Visibility\Portal;

class Context
{
    /** @var string */
    public $contactId;
    /**
     * @var \SugarBean
     */
    protected $bean;

    /**
     * @var array
     */
    protected $relationshipLinks;

    /**
     * @param string $contactId
     * @param array $links
     */
    public function __construct(string $contactId, array $links = [])
    {
        $this->contactId = $contactId;
        $this->relationshipLinks = $links;
    }

    /**
     * @return \SugarBean
     */
    public function getBean()
    {
        return $this->bean;
    }

    /**
     * @param \SugarBean $bean
     */
    public function setBean(\SugarBean $bean)
    {
        $this->bean = $bean;
    }

    /**
     * @return string
     */
    public function getAccountsRelationshipLink()
    {
        return $this->getLinkFieldName('Accounts');
    }

    /**
     * @return string
     */
    public function getContactsRelationshipLink()
    {
        return $this->getLinkFieldName('Contacts');
    }

    /**
     * @param string $moduleName
     *
     * @return string
     */
    protected function getLinkFieldName(string $moduleName)
    {
        $bean = $this->getBean();
        if (!empty($bean) && !empty($this->relationshipLinks[$moduleName])) {
            $fieldDef = $bean->getFieldDefinition($this->relationshipLinks[$moduleName]);
            if (!empty($fieldDef) && !empty($fieldDef['type']) && $fieldDef['type'] === 'link') {
                return $this->relationshipLinks[$moduleName];
            }
        }

        return '';
    }
}
