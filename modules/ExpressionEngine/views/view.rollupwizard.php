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


class ViewRollupWizard extends SugarView
{
    /**
     * @var string|\MBPackage
     */
    public $package;
    /**
     * @var mixed[]|mixed
     */
    public $selLink;
    public $tmodule;
    public $vars = ['tmodule', 'selLink', 'type'];

    public function __construct()
    {
        parent::__construct();
        foreach ($this->vars as $var) {
            if (!isset($_REQUEST[$var])) {
                sugar_die("Required paramter $var not set in Rollup Wizard");
            }
            $this->$var = $_REQUEST[$var];
        }
        $mb = new ModuleBuilder();
        $this->package = empty($_REQUEST['package']) || $_REQUEST['package'] == 'studio' ? '' : $mb->getPackage($_REQUEST['package']);
    }

    /**
     * Display the view
     */
    public function display()
    {
        $valid_links = [];
        $links = FormulaHelper::getLinksForModule($this->tmodule, $this->package);

        // loop over all the $links and if we don't have any fields, don't pass it down
        $current_fields = [];

        //Preload the related fields from the first relationship
        if (!empty($links)) {
            $selected_link = $links[$this->selLink] ?? $links[array_key_first($links)];
            foreach ($links as $link_key => $link) {
                $rfields = FormulaHelper::getRelatableFieldsForLink($link, $this->package, ['number']);
                if (!empty($rfields)) {
                    $valid_links[$link_key] = $link['label'];
                    if ($link === $selected_link || empty($current_fields)) {
                        $current_fields = $rfields;
                    }
                }
            }
        }

        $this->ss->assign('rmodules', $valid_links);
        $this->ss->assign('rfields', $current_fields);
        $this->ss->assign('tmodule', $this->tmodule);
        $this->ss->assign('selLink', $this->selLink);

        $this->ss->assign('rollup_types', [
            'Sum' => 'Sum', 'Ave' => 'Average', 'Min' => 'Minimum', 'Max' => 'Maximum',
        ]);
        $this->ss->assign('rollupType', $this->type);

        $this->ss->display('modules/ExpressionEngine/tpls/rollupWizard.tpl');
    }
}
