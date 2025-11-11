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
 * Remove duplicate email addresses and fix up references to removed records.
 */
class SugarUpgradeFixupEmailAddressDuplication extends UpgradeScript
{
    /**
     * {@inheritdoc}
     */
    public $order = 7100;

    /**
     * {@inheritdoc}
     */
    public $type = self::UPGRADE_CUSTOM;

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (version_compare($this->from_version, '9.1.0', '>=')) {
            return;
        }

        $dupEmailAddresses = [];

        // Find any duplicate email addresses
        $eaBean = BeanFactory::newBean('EmailAddresses');

        $q = new SugarQuery();
        $q->select(['email_address_caps'])->fieldRaw('COUNT(email_address_caps)', 'emcount');
        $q->from($eaBean, ['alias' => 'ea']);
        $q->groupBy('email_address_caps');
        if (safeCount($rows = $q->execute()) > 0) {
            foreach ($rows as $row) {
                if ($row['emcount'] > 1) { // is Duplicate?
                    // Get id as well as the opt_out and invalid_email flag settings
                    $q2 = new SugarQuery();
                    $q2->from($eaBean)->where()->equals('email_address_caps', $row['email_address_caps']);
                    $matches = $eaBean->fetchFromQuery($q2);

                    $eaInfo = [
                        'opt_out' => 0,
                        'invalid_email' => 0,
                        'email_addresses' => [],
                    ];
                    foreach ($matches as $match) {
                        // Use most conservative strategy - true always wins
                        if (boolval($match->opt_out)) {
                            $eaInfo['opt_out'] = 1;
                        }
                        if (boolval($match->invalid_email)) {
                            $eaInfo['invalid_email'] = 1;
                        }
                        $eaInfo['email_addresses'][$match->id] = $match->email_address;
                    }
                    $dupEmailAddresses[$row['email_address_caps']] = $eaInfo;
                }
            }
        }

        if (!empty($dupEmailAddresses)) {
            foreach ($dupEmailAddresses as $eaCaps => $eaInfo) {
                $i = 0;
                $survivorEmailAddressId = '';
                foreach ($eaInfo['email_addresses'] as $emailAddressId => $emailAddress) {
                    if ($i === 0) {
                        $survivorEmailAddressId = $emailAddressId;
                        $this->updateEmailAddressesRecord(
                            $emailAddressId,
                            $eaInfo['opt_out'],
                            $eaInfo['invalid_email']
                        );
                    } else {
                        $this->performUpdate(
                            'email_addr_bean_rel',
                            'email_address_id',
                            $emailAddressId,
                            $survivorEmailAddressId
                        );
                        $this->performUpdate(
                            'emails_email_addr_rel',
                            'email_address_id',
                            $emailAddressId,
                            $survivorEmailAddressId
                        );
                        $this->performUpdate(
                            'outbound_email',
                            'email_address_id',
                            $emailAddressId,
                            $survivorEmailAddressId
                        );
                        $this->performUpdate(
                            'outbound_email',
                            'reply_to_email_address_id',
                            $emailAddressId,
                            $survivorEmailAddressId
                        );
                        $this->deleteEmailAddressesRecord($emailAddressId);
                    }
                    $i++;
                }
            }
        }

        // Remove any duplicates from email_addr_bean_rel table
        /** @var \Sugarcrm\Sugarcrm\Dbal\Query\QueryBuilder $qb */
        $qb = $GLOBALS['db']->getConnection()->createQueryBuilder();
        $qb->select('email_address_id', 'bean_module', 'bean_id', 'COUNT(id) AS eabr_count');
        $qb->from('email_addr_bean_rel');
        $qb->where('deleted = 0');
        $qb->groupBy('email_address_id', 'bean_module', 'bean_id');
        $qb->having('COUNT(id) > 1');
        $res = $qb->execute();

        $dups = [];
        while ($row = $res->fetchAssociative()) {
            $dups[] = $row;
        }

        if (!empty($dups)) {
            foreach ($dups as $dup) {
                // Preserve one record and delete the rest. The one we will opt to preserve will be the first record
                // that has the primary_address field set if any, otherwise it will be that last record encountered
                $qb = DBManagerFactory::getConnection()->createQueryBuilder();
                $qb->select('id', 'primary_address');
                $qb->from('email_addr_bean_rel', 'eabr');
                $qb->add(
                    'where',
                    $qb->expr()->and(
                        $qb->expr()->eq('eabr.email_address_id', '?'),
                        $qb->expr()->eq('eabr.bean_module', '?'),
                        $qb->expr()->eq('eabr.bean_id', '?'),
                        $qb->expr()->eq('eabr.deleted', 0)
                    )
                );
                $qb->setParameter(0, $dup['email_address_id']);
                $qb->setParameter(1, $dup['bean_module']);
                $qb->setParameter(2, $dup['bean_id']);
                $res = $qb->execute();

                $eabrCount = $dup['eabr_count'];
                $kept = false;
                while ($row = $res->fetchAssociative()) {
                    if (!$kept && ($row['primary_address'] == 1 || $eabrCount == 1)) {
                        $kept = true;
                    } else {
                        $this->deleteEmailAddrBeanRelRecord($row['id']);
                    }
                    $eabrCount--;
                }
            }
        }
    }

    /**
     * Delete the supplied email_addresses record and log it
     * @param string $id
     */
    protected function deleteEmailAddressesRecord($id)
    {
        $sql = "DELETE FROM email_addresses WHERE id='{$id}' AND deleted=0";
        $this->log('Fixup_EmailAddress_Duplication::deleteEmailAddressesRecord: Query: ' . $sql);

        $this->db->query($sql);
    }

    /**
     * Delete the supplied email_addr_bean_rel record and log it
     * @param string $id
     */
    protected function deleteEmailAddrBeanRelRecord($id)
    {
        $sql = "DELETE FROM email_addr_bean_rel WHERE id='{$id}' AND deleted=0";
        $this->log('Fixup_EmailAddress_Duplication::deleteEmailAddressesRecord: Query: ' . $sql);

        $this->db->query($sql);
    }

    /**
     * Update the flags for the supplied email_addresses record
     * @param string $id
     * @param int $optOut
     * @param int $invalidEmail
     */
    protected function updateEmailAddressesRecord($id, $optOut, $invalidEmail)
    {
        $sql = 'UPDATE email_addresses' .
            " SET opt_out={$optOut}, invalid_email={$invalidEmail} WHERE id='{$id}' AND deleted=0";

        $this->log('Fixup_EmailAddress_Duplication::updateEmailAddressesRecord: Query: ' . $sql);
        $this->db->query($sql);
    }

    /**
     * Update the email address id for the supplied table and column
     * @param string $tableName Table name
     * @param string $column Column name
     * @param string $fromEmailAddressId Current id value
     * @param string $toEmailAddressId New id value
     */
    protected function performUpdate($tableName, $column, $fromEmailAddressId, $toEmailAddressId)
    {
        $sql = "UPDATE {$tableName}" .
            " SET {$column}='{$toEmailAddressId}' WHERE {$column}='{$fromEmailAddressId}' AND deleted=0";

        $this->log('Fixup_EmailAddress_Duplication::performUpdate: Query: ' . $sql);
        $this->db->query($sql);
    }
}
