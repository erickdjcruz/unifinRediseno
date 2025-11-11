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

namespace Sugarcrm\Sugarcrm\ProductDefinition\Config\Cache;

class DbCache implements CacheInterface
{
    /**
     * Product definition refresh interval in hours
     * It means how many hours system will wait before refresh product definition
     */
    public const REFRESH_INTERVAL = 24;

    public const TABLE_NAME = 'product_definition';

    /**
     * @var \DBManager
     */
    protected $db;

    /**
     * DbCache constructor.
     */
    public function __construct()
    {
        $this->db = \DBManagerFactory::getInstance();
    }

    /**
     * @inheritDoc
     */
    public function get(): ?string
    {
        $data = $this->db->getConnection()
            ->createQueryBuilder()
            ->select('data')
            ->from(static::TABLE_NAME)
            ->setMaxResults(1)
            ->execute()
            ->fetchOne();

        return $data === false ? null : $data;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function set(string $data)
    {
        $this->db->commit();

        $conn = $this->db->getConnection();
        $conn->delete(static::TABLE_NAME, [1 => 1]);
        $conn->insert(static::TABLE_NAME, ['data' => $data]);

        $this->db->commit();
    }
}
