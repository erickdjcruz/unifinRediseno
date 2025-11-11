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
 * Installs the new PDF templates for Quotes and Invoices with service information
 */
class SugarUpgradeAddServicesPDFs extends UpgradeScript
{
    public $order = 9000;
    public $type = self::UPGRADE_DB;
    public $version = '9.3.0';

    public function run()
    {
        // Only install the new PDF templates if needed
        if ($this->isNeeded()) {
            // Set up the variables needed to create the templates
            $logoUrl = './themes/default/images/pdf_logo.jpg';
            if (defined('PDF_HEADER_LOGO')) {
                $logoUrl = K_PATH_CUSTOM_IMAGES . PDF_HEADER_LOGO;
                $imsize = @getimagesize($logoUrl);
                if ($imsize === false) {
                    // Encode spaces on filename
                    $logoUrl = str_replace(' ', '%20', $logoUrl);
                    $imsize = @getimagesize($logoUrl);
                    if ($imsize === false) {
                        $logoUrl = K_PATH_IMAGES . PDF_HEADER_LOGO;
                    }
                }
                $logoUrl = './' . $logoUrl;
            }
            $modStringSrc = return_module_language($GLOBALS['current_language'], 'PdfManager');
            $ss = new Sugar_Smarty();
            $ss->assign('logoUrl', $logoUrl);
            $ss->assign('MOD', $modStringSrc);
            $ss->assign('withServices', true);

            // Create the "Quote (with services)" template
            $pdfTemplate = new PdfManager();
            $pdfTemplate->base_module = 'Quotes';
            $pdfTemplate->name = $modStringSrc['LBL_TPL_QUOTE_SERVICES_NAME'];
            $pdfTemplate->description = $modStringSrc['LBL_TPL_QUOTE_SERVICES_DESCRIPTION'];
            $pdfTemplate->body_html = to_html($ss->fetch('modules/PdfManager/tpls/templateQuote.tpl'));
            $pdfTemplate->template_name = $modStringSrc['LBL_TPL_QUOTE_SERVICES_TEMPLATE_NAME'];
            $pdfTemplate->author = PDF_AUTHOR;
            $pdfTemplate->title = PDF_TITLE;
            $pdfTemplate->subject = PDF_SUBJECT;
            $pdfTemplate->keywords = PDF_KEYWORDS;
            $pdfTemplate->published = 'yes';
            $pdfTemplate->deleted = 0;
            $pdfTemplate->team_id = 1;
            $pdfTemplate->save();

            // Create the "Invoice (with services)" template
            $pdfTemplate = new PdfManager();
            $pdfTemplate->base_module = 'Quotes';
            $pdfTemplate->name = $modStringSrc['LBL_TPL_INVOICE_SERVICES_NAME'];
            $pdfTemplate->description = $modStringSrc['LBL_TPL_INVOICE_SERVICES_DESCRIPTION'];
            $pdfTemplate->body_html = to_html($ss->fetch('modules/PdfManager/tpls/templateInvoice.tpl'));
            $pdfTemplate->template_name = $modStringSrc['LBL_TPL_INVOICE_SERVICES_TEMPLATE_NAME'];
            $pdfTemplate->author = PDF_AUTHOR;
            $pdfTemplate->title = PDF_TITLE;
            $pdfTemplate->subject = PDF_SUBJECT;
            $pdfTemplate->keywords = PDF_KEYWORDS;
            $pdfTemplate->published = 'yes';
            $pdfTemplate->deleted = 0;
            $pdfTemplate->team_id = 1;
            $pdfTemplate->save();
        }
    }

    /**
     * Helper function to determine if this upgrade is necessary
     * @return bool true if necessary; false otherwise
     */
    private function isNeeded()
    {
        $isConversion = !$this->fromFlavor('ent') && $this->toFlavor('ent');
        $isBelow93Ent = $this->toFlavor('ent') && version_compare($this->from_version, '9.3.0', '<');
        return $isConversion || $isBelow93Ent;
    }
}
