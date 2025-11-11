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


class ViewSelectRelatedField extends SugarView
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
    public $vars = ['tmodule', 'selLink'];

    public function __construct()
    {

        parent::__construct();
        foreach ($this->vars as $var) {
            if (!isset($_REQUEST[$var])) {
                sugar_die("Required paramter $var not set in ViewRelFields");
            }
            $this->$var = $_REQUEST[$var];
        }
        $mb = new ModuleBuilder();
        $this->package = empty($_REQUEST['package']) || $_REQUEST['package'] == 'studio' ? '' : $mb->getPackage($_REQUEST['package']);
    }

    public function display()
    {
        $rmodules = [];
        $links = FormulaHelper::getLinksForModule($this->tmodule, $this->package);
        $rfields = [];
        foreach ($links as $lname => $link) {
            $rmodules[$lname] = $link['label'];
        }

        //Preload the related fields from the first relationship
        if (!empty($links)) {
            $link = $links[$this->selLink] ?? $links[array_key_first($links)];
            $rfields = FormulaHelper::getRelatableFieldsForLink($link, $this->package);
        }

        $this->ss->assign('rmodules', $rmodules);
        $this->ss->assign('rfields', $rfields);
        $this->ss->assign('tmodule', $this->tmodule);
        $this->ss->assign('selLink', $this->selLink);
        $this->ss->assign('rollup_types', [
            'rollupSum' => 'Sum',
            'rollupMin' => 'Minimum',
            'rollupMax' => 'Maximum',
            'rollupAverage' => 'Average',
        ]);
        $this->ss->display('modules/ExpressionEngine/tpls/selectRelatedField.tpl');
    }
}
