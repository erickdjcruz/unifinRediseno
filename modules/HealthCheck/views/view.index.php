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
 *
 * Index main view
 *
 */
class ViewIndex extends SugarView
{
    /**
     *
     * Constructor
     *
     * @see SugarView::__construct()
     */
    public function __construct($bean = null, $view_object_map = [])
    {
        $this->suppressDisplayErrors = true;

        $this->options['show_title'] = false;
        $this->options['show_header'] = false;
        $this->options['show_javascript'] = false;
        $this->options['show_subpanels'] = false;
        $this->options['show_search'] = false;

        parent::__construct($bean, $view_object_map);
    }

    /**
     *
     * @see SugarView::display()
     */
    public function display()
    {
        $this->ss->display('modules/HealthCheck/tpls/index.tpl');
    }
}
