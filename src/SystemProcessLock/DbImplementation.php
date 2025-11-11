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

namespace Sugarcrm\Sugarcrm\SystemProcessLock;

use DateInterval;
use DateTime;
use DBManagerFactory;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use LoggerManager;
use Throwable;
use TimeDate;

/**
 * Process lock/unlock database implementation
 *
 * @package Sugarcrm\Sugarcrm\DbImplementation
 */
class DbImplementation
{
    private const TABLE = 'system_process_lock';
    private $connection;
    public $isAvailable = false;

    public function __construct()
    {
        $dbManagerFactory = DBManagerFactory::getInstance();
        if ($dbManagerFactory) {
            $this->connection = $dbManagerFactory->getConnection();
            $this->isAvailable = $dbManagerFactory->tableExists(self::TABLE);
        }
    }

    public function lock(string $uniqueId, string $additionalKey, int $timeoutSeconds): bool
    {
        $expirationTimestamp = (new DateTime())->add(new DateInterval(sprintf('PT%dS', $timeoutSeconds)));
        $timeDate = new TimeDate();

        $data = [
            'unique_id' => $uniqueId,
            'additional_key' => $additionalKey,
            'date_expires' => $timeDate->asDb($expirationTimestamp),
        ];
        try {
            @$this->connection->insert(self::TABLE, $data);
            return true;
        } catch (UniqueConstraintViolationException $e) {
            return false;
        } catch (Throwable $e) {
            LoggerManager::getLogger()->fatal($e);
            return true;
        }
    }

    public function unlock(string $uniqueId, string $additionalKey): void
    {
        $conditions = [
            'unique_id' => $uniqueId,
            'additional_key' => $additionalKey,
        ];
        try {
            $this->connection->delete(self::TABLE, $conditions);
        } catch (\Exception $e) {
            if (DBManagerFactory::getInstance()->tableExists(self::TABLE)) {
                throw $e;
            }
        }
    }

    public function processTimedOutLocks(): void
    {
        $timeDate = new TimeDate();

        $data = [
            $timeDate->asDb(new DateTime()),
        ];
        try {
            $this->connection->executeStatement(
                sprintf('DELETE FROM %s WHERE date_expires <= ?', self::TABLE),
                $data
            );
        } catch (\Exception $e) {
            if (DBManagerFactory::getInstance()->tableExists(self::TABLE)) {
                throw $e;
            }
        }
    }
}
