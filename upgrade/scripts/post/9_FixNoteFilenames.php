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
 * Fix issues with Note filenames containing newline characters
 */
class SugarUpgradeFixNoteFilenames extends UpgradeScript
{
    public $order = 9000;
    public $type = self::UPGRADE_DB;

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $this->log('Fixing Note filenames');
        if ($this->shouldRun()) {
            $this->executeQuery();
        }
        $this->log('Done fixing Note filenames');
    }

    /**
     * Determines if this upgrader should run
     *
     * @return bool true if the upgrader should run
     */
    protected function shouldRun()
    {
        return version_compare($this->from_version, '11.1.0', '<');
    }

    /**
     * Removes newline characters from the name and filename columns of all
     * Notes that contain them
     *
     * @throws Doctrine\DBAL\Exception
     */
    protected function executeQuery()
    {
        // Get the Notes table name
        $noteBean = BeanFactory::newBean('Notes');
        $table = $noteBean->getTableName();

        // Get the SQL to replace any newlines or carriage returns in the column
        // values
        $nameWithRemovedNewlinesAndReturns = $this->getSqlToRemoveNewlinesAndReturns('name');
        $filenameWithRemovedNewlinesAndReturns = $this->getSqlToRemoveNewlinesAndReturns('filename');

        // Get the SQL to filter on only rows that will be affected
        $nameContainsNewline = $this->db->getLikeSQL('name', "%\n%");
        $nameContainsCarriageReturn = $this->db->getLikeSQL('name', "%\r%");
        $filenameContainsNewline = $this->db->getLikeSQL('filename', "%\n%");
        $filenameContainsCarriageReturn = $this->db->getLikeSQL('filename', "%\r%");

        // Execute the update
        $qb = $this->db->getConnection()->createQueryBuilder();
        $qb->update($table)
            ->set('name', $nameWithRemovedNewlinesAndReturns)
            ->set('filename', $filenameWithRemovedNewlinesAndReturns)
            ->where($nameContainsNewline)
            ->orWhere($nameContainsCarriageReturn)
            ->orWhere($filenameContainsNewline)
            ->orWhere($filenameContainsCarriageReturn)
            ->execute();
    }

    /**
     * Returns the SQL to remove any newline or carriage return characters from
     * a column of a table
     *
     * @param string $column the name of the column
     * @return string the SQL to remove the newline and carriage return characters
     */
    protected function getSqlToRemoveNewlinesAndReturns(string $column)
    {
        // Remove the newlines in the column, then remove the resulting string's
        // carriage returns. This is to ensure that both characters are removed
        // regardless of their position or proximity to each other
        return 'REPLACE(' .
            'REPLACE(' .
            $column . ',' . $this->db->quoted("\n") . ',' . $this->db->quoted('') . '),' .
            $this->db->quoted("\r") . ',' .
            $this->db->quoted('') .
            ')';
    }
}
