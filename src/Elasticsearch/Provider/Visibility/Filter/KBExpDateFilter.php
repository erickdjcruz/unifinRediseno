<?php

declare(strict_types=1);
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

namespace Sugarcrm\Sugarcrm\Elasticsearch\Provider\Visibility\Filter;

use Sugarcrm\Sugarcrm\Elasticsearch\Mapping\Mapping;

/**
 *
 * Knowledge Base expiration date filter.
 *
 */
class KBExpDateFilter implements FilterInterface
{
    use FilterTrait;

    /**
     * {@inheritdoc}
     */
    public function buildFilter(array $options = [])
    {
        $field = $options['module'] . Mapping::PREFIX_SEP . 'exp_date.kbvis';

        // range of date
        $range = new \Elastica\Query\Range();
        $range->addField($field, $options['range']);

        // empty date
        $dateEmptyFilter = new \Elastica\Query\BoolQuery();
        $dateEmptyFilter->addMustNot(new \Elastica\Query\Exists($field));

        // When neither must or filter presents then at least one of the should queries must match a document
        // for it to match the bool query.
        // In this case, it will be a match if range matches or exp_date is empty
        $filter = new \Elastica\Query\BoolQuery();
        $filter->addShould($range);
        $filter->addShould($dateEmptyFilter); // empty exp_date

        return $filter;
    }
}
