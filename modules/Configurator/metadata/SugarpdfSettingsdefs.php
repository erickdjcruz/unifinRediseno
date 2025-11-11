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

use Sugarcrm\Sugarcrm\Security\Crypto\Blowfish;

require_once 'include/Sugarpdf/sugarpdf_config.php';

$SugarpdfSettings = [
    'sugarpdf_pdf_title' => [
        'label' => $mod_strings['PDF_TITLE'],
        'info_label' => '',
        'value' => PDF_TITLE,
        'class' => 'basic',
        'type' => 'text',
    ],
    'sugarpdf_pdf_subject' => [
        'label' => $mod_strings['PDF_SUBJECT'],
        'info_label' => '',
        'value' => PDF_SUBJECT,
        'class' => 'basic',
        'type' => 'text',
    ],
    'sugarpdf_pdf_author' => [
        'label' => $mod_strings['PDF_AUTHOR'],
        'info_label' => '',
        'value' => PDF_AUTHOR,
        'class' => 'basic',
        'type' => 'text',
        'required' => 'true',
    ],
    'sugarpdf_pdf_keywords' => [
        'label' => $mod_strings['PDF_KEYWORDS'],
        'info_label' => $mod_strings['PDF_KEYWORDS_INFO'],
        'value' => PDF_KEYWORDS,
        'class' => 'basic',
        'type' => 'text',
    ],
    'sugarpdf_pdf_small_header_logo' => [
        'label' => $mod_strings['PDF_SMALL_HEADER_LOGO'],
        'info_label' => $mod_strings['PDF_SMALL_HEADER_LOGO_INFO'],
        'value' => PDF_SMALL_HEADER_LOGO,
        'path' => K_PATH_CUSTOM_IMAGES . PDF_SMALL_HEADER_LOGO,
        'class' => 'logo',
        'type' => 'image',
    ],
    'new_small_header_logo' => [
        'label' => $mod_strings['PDF_NEW_SMALL_HEADER_LOGO'],
        'info_label' => $mod_strings['PDF_NEW_SMALL_HEADER_LOGO_INFO'],
        'value' => '',
        'class' => 'logo',
        'type' => 'file',
    ],
];

// Use the OOB directory for images if there is no image in the custom directory
$small_logo = $SugarpdfSettings['sugarpdf_pdf_small_header_logo']['path'];
if (@getimagesize($small_logo) === false) {
    $SugarpdfSettings['sugarpdf_pdf_small_header_logo']['path'] = K_PATH_IMAGES . $SugarpdfSettings['sugarpdf_pdf_small_header_logo']['value'];
}
