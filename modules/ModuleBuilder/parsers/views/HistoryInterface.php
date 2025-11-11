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

interface HistoryInterface
{
    /*
     * Get the most recent item in the history
     * @return Id of the first item
     */
    public function getFirst();

    /*
     * Get the next oldest item in the history
     * @return Id of the next item
     */
    public function getNext();

    /*
     * Get the nth item in the history (where the zeroeth record is the most recent)
     * @return Id of the nth item
     */
    public function getNth($n);

    /*
     * Restore the historical layout identified by timestamp
     * @return Timestamp if successful, null if failure (if the file could not be copied for some reason)
     */
    public function restoreByTimestamp($timestamp);

    /*
     * Undo the restore - revert back to the layout before the restore
     */
    public function undoRestore();

    /*
     * Add an item to the history
     * @return String   An timestamp for this newly added item
     */
    public function append($path);
}
