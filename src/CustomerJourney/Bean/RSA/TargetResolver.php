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

namespace Sugarcrm\Sugarcrm\CustomerJourney\Bean\RSA;

class TargetResolver
{
    /**
     * @var \CJ_Form
     */
    private $action;

    /**
     * RelationshipFinder constructor.
     * @param \CJ_Form $action
     */
    public function __construct(\CJ_Form $action)
    {
        $this->action = $action;
    }

    /**
     * Return the complete information against a Target record
     *
     * @param \SugarBean $parent
     * @param \SugarBean $target
     * @return array
     */
    public function resolve(\SugarBean $parent, \SugarBean $target)
    {
        $linkName = null;
        $module = null;
        $allBeans = null;

        $rels = json_decode($this->action->relationship, true);

        foreach ($rels as $rel) {
            $module = $rel['module'];

            if ($rel['relationship'] === 'self') {
                $allBeans[0] = $target;
                break;
            }

            $linkName = $rel['relationship'];
            $target->load_relationship($linkName);

            /** @var \Link2 $link */
            $link = $target->{$linkName};

            if (empty($link)) {
                throw new \SugarApiExceptionError("Unable to load link: {$linkName}");
            }

            $beans = $link->getBeans();

            if (!empty($beans)) {
                $parent = $target;
                $allBeans = $beans;
                $target = array_shift($beans);
            } else {
                $parent = $target;
                $target = \BeanFactory::newBean($module);
            }
        }

        return [
            'parent' => $parent,
            'target' => $target,
            'linkName' => $linkName,
            'module' => $module,
            'allBeans' => $allBeans,
        ];
    }
}
