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
 * Migrates fieds in Hint package, previously in cstm tables, to base tables
 */
class SugarUpgradeMigrateHintMLPFields extends UpgradeScript
{
    public $order = 9000;
    public $type = self::UPGRADE_DB;

    protected $accountsMapping = [
        'hint_account_size_c' => 'hint_account_size',
        'hint_account_industry_c' => 'hint_account_industry',
        'hint_account_location_c' => 'hint_account_location',
        'hint_account_industry_tags_c' => 'hint_account_industry_tags',
        'hint_account_founded_year_c' => 'hint_account_founded_year',
        'hint_account_facebook_handle_c' => 'hint_account_facebook_handle',
        'hint_account_logo_c' => 'hint_account_logo',
        'hint_account_pic_c' => 'hint_account_pic',
        'hint_account_naics_code_lbl_c' => 'hint_account_naics_code_lbl',
        'hint_account_fiscal_year_end_c' => 'hint_account_fiscal_year_end',
    ];

    protected $leadsMapping = [
        'hint_account_size_c' => 'hint_account_size',
        'hint_account_industry_c' => 'hint_account_industry',
        'hint_account_location_c' => 'hint_account_location',
        'hint_account_description_c' => 'hint_account_description',
        'hint_job_2_c' => 'hint_job_2',
        'hint_education_c' => 'hint_education',
        'hint_education_2_c' => 'hint_education_2',
        'hint_facebook_c' => 'hint_facebook',
        'hint_twitter_c' => 'hint_twitter',
        'hint_industry_tags_c' => 'hint_industry_tags',
        'hint_account_founded_year_c' => 'hint_account_founded_year',
        'hint_account_facebook_handle_c' => 'hint_account_facebook_handle',
        'hint_account_twitter_handle_c' => 'hint_account_twitter_handle',
        'hint_account_logo_c' => 'hint_account_logo',
        'hint_contact_pic_c' => 'hint_contact_pic',
        'hint_photo_c' => 'hint_photo',
        'hint_phone_1_c' => 'hint_phone_1',
        'hint_phone_2_c' => 'hint_phone_2',
        'hint_account_naics_code_lbl_c' => 'hint_account_naics_code_lbl',
        'hint_account_sic_code_label_c' => 'hint_account_sic_code_label',
        'hint_account_fiscal_year_end_c' => 'hint_account_fiscal_year_end',
        'hint_account_annual_revenue_c' => 'hint_account_annual_revenue',
        'hint_account_website_c' => 'hint_account_website',
    ];

    protected $contactsMapping = [
        'hint_account_size_c' => 'hint_account_size',
        'hint_account_industry_c' => 'hint_account_industry',
        'hint_account_location_c' => 'hint_account_location',
        'hint_account_description_c' => 'hint_account_description',
        'hint_job_2_c' => 'hint_job_2',
        'hint_education_c' => 'hint_education',
        'hint_education_2_c' => 'hint_education_2',
        'hint_facebook_c' => 'hint_facebook',
        'hint_twitter_c' => 'hint_twitter',
        'hint_industry_tags_c' => 'hint_industry_tags',
        'hint_account_founded_year_c' => 'hint_account_founded_year',
        'hint_account_facebook_handle_c' => 'hint_account_facebook_handle',
        'hint_account_twitter_handle_c' => 'hint_account_twitter_handle',
        'hint_account_logo_c' => 'hint_account_logo',
        'hint_contact_pic_c' => 'hint_contact_pic',
        'hint_photo_c' => 'hint_photo',
        'hint_phone_1_c' => 'hint_phone_1',
        'hint_phone_2_c' => 'hint_phone_2',
        'hint_account_website_c' => 'hint_account_website',
        'hint_account_naics_code_lbl_c' => 'hint_account_naics_code_lbl',
        'hint_account_sic_code_label_c' => 'hint_account_sic_code_label',
        'hint_account_fiscal_year_end_c' => 'hint_account_fiscal_year_end',
        'hint_account_annual_revenue_c' => 'hint_account_annual_revenue',
    ];

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        if ($this->shouldRun()) {
            $this->log('Migrating Hint MLP fields');

            $this->accountsMapping = $this->updateAvailableFields($this->accountsMapping, 'Accounts');
            $this->executeMigration('Accounts', $this->accountsMapping);

            $this->leadsMapping = $this->updateAvailableFields($this->leadsMapping, 'Leads');
            $this->executeMigration('Leads', $this->leadsMapping);

            $this->contactsMapping = $this->updateAvailableFields($this->contactsMapping, 'Contacts');
            $this->executeMigration('Contacts', $this->contactsMapping);

            $this->log('Done migrating Hint');
        } else {
            $this->log('Skipping migrating Hint');
        }
    }

    /**
     * Determines if this upgrader should run
     *
     * @return bool true if the upgrader should run
     */
    protected function shouldRun()
    {
        if (version_compare($this->from_version, '12.0.0', '>=')) {
            return false;
        }

        $accountSeed = BeanFactory::newBean('Accounts');
        $hasCustomFields = $accountSeed->hasCustomFields();
        if ($hasCustomFields) {
            $cstmTable = $accountSeed->get_custom_table_name();
            $cols = $this->db->get_columns($cstmTable);
            $colsNames = array_keys($cols);
            if (in_array('hint_account_size_c', $colsNames) || in_array('hint_account_facebook_handle_c', $colsNames)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Update available fields
     *
     * In case of old package being installed, make sure to skip newer fields
     *
     * @param String $fields
     * @param String $module
     * @return array
     */
    protected function updateAvailableFields($fields, $module)
    {
        $seed = BeanFactory::newBean($module);
        $cstmTable = $seed->get_custom_table_name();
        $cols = $this->db->get_columns($cstmTable);
        $colsNames = array_keys($cols);

        $newFields = [];
        foreach ($fields as $oldFieldName => $newFieldName) {
            if (in_array($oldFieldName, $colsNames)) {
                $newFields[$oldFieldName] = $newFieldName;
            }
        }
        return $newFields;
    }

    /**
     * Migrates fieds in Hint package, previously in cstm table, to base table
     *
     * @param String $module
     * @param array $mapping
     * @throws Doctrine\DBAL\Exception
     */
    protected function executeMigration($module, $mapping)
    {
        $bean = BeanFactory::newBean($module);
        $table = $bean->getTableName();
        $customTable = $bean->get_custom_table_name();

        // Get cstm values
        $qb = $this->db->getConnection()->createQueryBuilder();

        $selectFields = array_keys($mapping);
        array_unshift($selectFields, 'id_c');

        $qb->select($selectFields);
        $qb->from($customTable);

        //only select records that have at least a value to migrate
        $or = $qb->expr()->orx();
        foreach ($mapping as $oldFieldName => $newFieldName) {
            $or->add($qb->expr()->isNotNull($oldFieldName));
        }
        $qb->where($or);
        $result = $qb->execute();

        // Copy the values
        while ($row = $result->fetchAssociative()) {
            $qb = $this->db->getConnection()->createQueryBuilder();
            $whereId = $qb->expr()->eq('id', $this->db->quoted($row['id_c']));

            $qb->update($table);

            $atLeastOneColumnIsSet = false;
            foreach ($mapping as $oldFieldName => $newFieldName) {
                if (!is_null($row[$oldFieldName])) {
                    $qb->set($newFieldName, $this->db->quoted($row[$oldFieldName]));
                    $atLeastOneColumnIsSet = true;
                }
            }

            $qb->where($whereId);

            if ($atLeastOneColumnIsSet) {
                $qb->execute();
            }
        }
    }
}
