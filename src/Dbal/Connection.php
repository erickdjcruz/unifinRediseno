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

namespace Sugarcrm\Sugarcrm\Dbal;

use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Connection as BaseConnection;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Result;
use LoggerManager;
use Sugarcrm\Sugarcrm\Dbal\Query\QueryBuilder;

/**
 * {@inheritDoc}
 */
class Connection extends BaseConnection
{
    /**
     * {@inheritDoc}
     *
     * @return \Sugarcrm\Sugarcrm\Dbal\Query\QueryBuilder
     */
    public function createQueryBuilder()
    {
        return new QueryBuilder($this);
    }

    /**
     * {@inheritDoc}
     */
    public function executeQuery($query, array $params = [], $types = [], QueryCacheProfile $qcp = null): Result
    {
        try {
            return parent::executeQuery($query, $params, $types, $qcp);
        } catch (DBALException $e) {
            $this->logException($e);
            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function executeUpdate($query, array $params = [], array $types = []): int
    {
        try {
            return parent::executeStatement($query, $params, $types);
        } catch (DBALException $e) {
            $this->logException($e);
            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function executeStatement($query, array $params = [], array $types = []): int
    {
        try {
            return parent::executeStatement($query, $params, $types);
        } catch (DBALException\UniqueConstraintViolationException $e) {
            throw $e;
        } catch (DBALException $e) {
            $this->logException($e);
            throw $e;
        }
    }

    /**
     * Logs DBAL exception
     *
     * @param DBALException $e Exception
     */
    protected function logException(DBALException $e)
    {
        LoggerManager::getLogger()->fatal($this->formatExceptionMessage($e));
    }

    /**
     * @param DBALException $e
     * @return string
     */
    protected function formatExceptionMessage(DBALException $e): string
    {
        $message = $e->getMessage();
        if ($e instanceof DBALException\DriverException && $e->getQuery() !== null) {
            $message .= '; Query: ' . $e->getQuery()->getSQL();
            $params = $e->getQuery()->getParams();
            if (safeCount($params) > 0) {
                $message .= '; Params: ' . var_export($params, true);
            }
        }
        return $message;
    }
}
