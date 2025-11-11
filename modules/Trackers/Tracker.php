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

use Doctrine\DBAL\Connection;

class Tracker extends SugarBean
{
    public $module_dir = 'Trackers';
    public $table_name = 'tracker';
    public $object_name = 'Tracker';
    public $acltype = 'Tracker';
    public $acl_category = 'Trackers';
    public $disable_custom_fields = true;
    public $disable_row_level_security = true;
    public $column_fields = [
        'id',
        'monitor_id',
        'user_id',
        'module_name',
        'item_id',
        'item_summary',
        'date_modified',
        'action',
        'session_id',
        'visible',
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /*
     * Return the most recently viewed items for this user.
     * The number of items to return is specified in sugar_config['history_max_viewed']
     * @param uid user_id
     * @param mixed module_name Optional - return only items from this module, a string of the module or array of modules
     * @return array list
     */
    public function get_recently_viewed($user_id, $modules = '')
    {
        if (empty($_SESSION['breadCrumbs'])) {
            $breadCrumb = new BreadCrumbStack($user_id, $modules);
            $_SESSION['breadCrumbs'] = $breadCrumb;
            $GLOBALS['log']->info(string_format($GLOBALS['app_strings']['LBL_BREADCRUMBSTACK_CREATED'], [$user_id]));
        } else {
            $breadCrumb = $_SESSION['breadCrumbs'];

            if (!empty($modules)) {
                $history_max_viewed = 10;
            } elseif (!empty($GLOBALS['sugar_config']['history_max_viewed'])) {
                $history_max_viewed = $GLOBALS['sugar_config']['history_max_viewed'];
            } else {
                $history_max_viewed = 50;
            }

            $conn = $this->db->getConnection();
            $qbSubquery = $conn->createQueryBuilder();
            $expr = $qbSubquery->expr();
            $qbSubquery->select('MAX(id) AS id')
                ->from($this->table_name)
                ->where($expr->eq('user_id', $qbSubquery->createPositionalParameter($user_id)))
                ->andWhere($expr->eq('deleted', 0))
                ->andWhere($expr->eq('visible', 1));

            if (!empty($modules)) {
                $qbSubquery->andWhere($expr->in(
                    'module_name',
                    $qbSubquery->createPositionalParameter((array)$modules, Connection::PARAM_STR_ARRAY)
                ));
            }

            $qb = $conn->createQueryBuilder();
            $qb->select(['id', 'item_id', 'item_summary', 'module_name'])
                ->from($this->table_name)
                ->setMaxResults($history_max_viewed)
                ->where('id = (' . $qb->importSubQuery($qbSubquery) . ')');

            $stmt = $qb->execute();

            while ($row = $stmt->fetchAssociative()) {
                $breadCrumb->push($row);
            }
        }

        $list = $breadCrumb->getBreadCrumbList($modules);
        $GLOBALS['log']->info('Tracker: retrieving ' . safeCount($list) . ' items');
        return $list;
    }

    public function makeInvisibleForAll($item_id)
    {
        $builder = $this->db->getConnection()->createQueryBuilder();
        $query = $builder->update($this->table_name)
            ->set('visible', 0)
            ->where($builder->expr()->eq('item_id', $builder->createPositionalParameter($item_id)))
            ->andWhere($builder->expr()->eq('visible', 1));
        $query->execute();
        if (!empty($_SESSION['breadCrumbs']) && $_SESSION['breadCrumbs'] instanceof BreadCrumbStack) {
            $breadCrumbs = $_SESSION['breadCrumbs'];
            $breadCrumbs->popItem($item_id);
        }
    }

    /**
     * create_tables
     * Override this method to insert ACLActions for the tracker beans
     *
     */
    public function create_tables()
    {
        $path = 'modules/Trackers/config.php';
        if (defined('TEMPLATE_URL')) {
            $path = SugarTemplateUtilities::getFilePath($path);
        }
        require $path;
        foreach ($tracker_config as $key => $configEntry) {
            if (isset($configEntry['bean']) && $configEntry['bean'] != 'Tracker') {
                $bean = BeanFactory::newBeanByName($configEntry['bean']);
                if ($bean->bean_implements('ACL')) {
                    ACLAction::addActions($bean->getACLCategory(), $configEntry['bean']);
                }
            }
        }
        parent::create_tables();
    }

    /**
     * bean_implements
     * Override method to support ACL roles
     */
    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getModuleName()
    {
        return 'Trackers';
    }
}
