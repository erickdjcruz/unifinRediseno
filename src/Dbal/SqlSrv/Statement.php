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

namespace Sugarcrm\Sugarcrm\Dbal\SqlSrv;

use Doctrine\DBAL\Driver\Middleware\AbstractStatementMiddleware;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\ParameterType;

/**
 * SQL Server statement
 *
 * Explicitly cast numeric values to string since it's important for SQL Server,
 * but Doctrine DBAL doesn't pass the "string" binding type to the DB driver
 *
 * until we get rid of numeric IDs, we have to cast integers to strings in order to avoid
 * string to integer conversion errors on the database side
 *
 * @link https://github.com/doctrine/dbal/issues/2369
 * @link https://msdn.microsoft.com/en-us/library/ms190309.aspx
 */
class Statement extends AbstractStatementMiddleware
{
    /**
     * {@inheritdoc}
     */
    public function bindValue($param, $value, $type = ParameterType::STRING)
    {
        if (is_int($value)) {
            $value = (string)$value;
        }
        return parent::bindValue($param, $value, $type);
    }

    /**
     * @inheritDoc
     */
    public function bindParam($param, &$variable, $type = ParameterType::STRING, $length = null)
    {
        $value = is_int($variable) ? (string)$variable : $variable;
        return parent::bindParam($param, $value, $type, $length);
    }

    /**
     * @inheritDoc
     */
    public function execute($params = null): Result
    {
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                if (is_int($value)) {
                    $params[$key] = (string)$value;
                }
            }
        }
        return parent::execute($params);
    }
}
