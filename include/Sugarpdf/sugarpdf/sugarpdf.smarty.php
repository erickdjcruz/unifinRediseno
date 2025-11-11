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
 * This is an helper class to generate PDF using smarty template.
 * You have to extend this class, set the templateLocation to your smarty template
 * location and assign the Smarty variables ($this->ss->assign()) in the overriden
 * preDisplay method (don't forget to call the parent).
 *
 * @author bsoufflet
 *
 */
class SugarpdfSmarty extends Sugarpdf
{
    /**
     *
     * @var String
     */
    protected $templateLocation = '';
    /**
     * The Sugar_Smarty object
     * @var Sugar_Smarty
     */
    protected $ss;
    /**
     * These 5 variables are use for the writeHTML method.
     * @see vendor/tcpdf/tcpdf.php writeHTML()
     */
    protected $smartyLn = true;
    protected $smartyFill = false;
    protected $smartyReseth = false;
    protected $smartyCell = false;
    protected $smartyAlign = '';

    public function preDisplay()
    {
        parent::preDisplay();
        $this->print_header = false;
        $this->print_footer = false;
        $this->initSmartyInstance();
    }

    public function display()
    {
        //turn off all error reporting so that PHP warnings don't munge the PDF code
        error_reporting(E_ALL);
        set_time_limit(1800);

        //Create new page
        $this->AddPage();
        $this->SetFont(PDF_FONT_NAME_MAIN, '', 8);

        if (!empty($this->templateLocation)) {
            $str = $this->ss->fetch($this->templateLocation);
            $this->writeHTML($str, $this->smartyLn, $this->smartyFill, $this->smartyReseth, $this->smartyCell, $this->smartyAlign);
        } else {
            $this->Error('The class SugarpdfSmarty has to be extended and you have to set a location for the Smarty template.');
        }
    }

    /**
     * Init the Sugar_Smarty object.
     */
    private function initSmartyInstance()
    {
        if (!($this->ss instanceof Sugar_Smarty)) {
            $this->ss = new Sugar_Smarty();
            $securityPolicy = new Smarty_Security($this->ss);
            // forbid all static calls
            $securityPolicy->static_classes = [null];
            // explicitly allow list of PHP functions (equals to defaults):
            $securityPolicy->php_functions = ['isset', 'empty', 'count', 'sizeof', 'in_array', 'is_array', 'time',];
            $securityPolicy->allow_super_globals = false;
            $securityPolicy->allow_constants = false;
            // disable all stream wrappers
            $securityPolicy->streams = null;
            // 'math' internally uses eval()
            $securityPolicy->disabled_tags = ['eval', 'fetch', 'include_php', 'math', 'php',];
            /**
             * Disable 'template_object' to prevent tricks like:  {$name=$smarty.template_object->disableSecurity()} {include "file://etc/passwd"}
             * Disable 'current_dir' to prevent filesystem info leakage
             */
            $securityPolicy->disabled_special_smarty_vars = ['template_object', 'current_dir',];
            if (defined('SUGAR_SHADOW_PATH')) {
                $securityPolicy->secure_dir[] = SUGAR_SHADOW_PATH;
            }
            $this->ss->enableSecurity($securityPolicy);

            $this->ss->assign('MOD', $GLOBALS['mod_strings']);
            $this->ss->assign('APP', $GLOBALS['app_strings']);
        }
    }

    /*
     * @see TCPDF::Image()
     */
    public function Image($file, $x = '', $y = '', $w = 0, $h = 0, $type = '', $link = '', $align = '', $resize = false, $dpi = 300, $palign = '', $ismask = false, $imgmask = false, $border = 0, $fitbox = false)
    {
        // strip query string from the image if it exists in the file name, tcpdf doesn't like 'query strings as part of the image file name
        //a sample image string is:  http://d2owqhhe2x3j50.cloudfront.net/images/sugar7/home/concept1_1.png?version=3.0.3

        //grab the base name and search for the '?' character
        $fileinfo = pathinfo($file);
        $q_pos = strpos($fileinfo['basename'], '?');

        if (!empty($fileinfo['basename']) && $q_pos !== false) {
            //split the file name and reassemble
            $splitName = substr($fileinfo['basename'], 0, $q_pos);
            $file = $fileinfo['dirname'] . '/' . $splitName;
        }

        return parent::Image($file, $x, $y, $w, $h, $type, $link, $align, $resize, $dpi, $palign, $ismask, $imgmask, $border, $fitbox);
    }
}
