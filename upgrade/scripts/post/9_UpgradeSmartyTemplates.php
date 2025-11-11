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
 * Update all packages
 */
class SugarUpgradeUpgradeSmartyTemplates extends UpgradeScript
{
    public $order = 9999;
    /**
     * @var string
     */
    private $logPrefix;

    public function __construct($upgrader)
    {
        parent::__construct($upgrader);
        $this->logPrefix = 'SMARTY_UPGRADE: ';
    }

    public function run()
    {
        if (version_compare($this->from_version, '11.2.0', '>=')) {
            return;
        }
        $this->overrideTemplates();
    }

    protected function overrideTemplates()
    {
        if (!file_exists('./_smarty3_')) {
            return;
        }
        $recursiveIteratorIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('./_smarty3_'));
        foreach ($recursiveIteratorIterator as $curFile) {
            if ($recursiveIteratorIterator->isDot()) {
                continue;
            }
            if (false === strpos($curFile, '/_smarty3_/db/')) {
                [, $pathToTemplate] = explode('/_smarty3_/', $curFile);
                copy($curFile, $pathToTemplate);
            } else {
                $id = basename($curFile);
                $pdfManager = BeanFactory::getBean('PdfManager', $id);
                $pdfManager->body_html = file_get_contents($curFile);
                $pdfManager->save();
            }
        }
        rmdir_recursive('./_smarty3_');
    }
}
