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

// functionality is restricted for regular users
if (!is_admin($GLOBALS['current_user'])) {
    sugar_die($GLOBALS['app_strings']['ERR_NOT_ADMIN']);
}

global $sugar_config;
$json = getJSONobj();

$returnArray = [];

$upload_ok = false;
$hasFile = false;
$isDarkModeLogo = false;

if (isset($_FILES['file_1'])) {
    $upload_path = Configurator::COMPANY_LOGO_UPLOAD_PATH;
    $upload = new UploadFile('file_1');
    $hasFile = true;
} elseif (isset($_FILES['file_dark'])) {
    $isDarkModeLogo = true;
    $upload_path = Configurator::COMPANY_LOGO_UPLOAD_PATH_DARK;
    $upload = new UploadFile('file_dark');
    $hasFile = true;
}

if ($hasFile) {
    if ($upload->confirm_upload()) {
        $upload_dir = dirname($upload_path);
        UploadStream::ensureDir($upload_dir);

        $tempPath = $_FILES[$upload->field_name]['tmp_name'];
        if (verify_uploaded_image($tempPath) && $upload->final_move($upload_path)) {
            $upload_ok = true;
        }
    }
}

if (!$upload_ok) {
    $returnArray['data'] = 'not_recognize';
    echo $json->encode($returnArray);
    sugar_cleanup(true);
}
if (file_exists($upload_path) && is_file($upload_path)) {
    $logoFileName = $isDarkModeLogo ? 'logo_dark.png' : 'logo.png';
    $returnArray['url'] = "cache/images/{$logoFileName}?nocache=" . time();
    if (!verify_uploaded_image($upload_path)) {
        $returnArray['data'] = 'other';
    } else {
        $img_size = getimagesize($upload_path);
        $filetype = $img_size['mime'];
        $test = $img_size[0] / $img_size[1];
        if ($test > 10 || $test < 1) {
            $returnArray['data'] = 'size';
        }
        sugar_mkdir(sugar_cached('images'));
        copy($upload_path, sugar_cached("images/{$logoFileName}"));
    }
    if (!empty($returnArray['data'])) {
        echo $json->encode($returnArray);
    } else {
        $returnArray['data'] = 'ok';
        echo $json->encode($returnArray);
    }
} else {
    $returnArray['data'] = 'file_error';
    echo $json->encode($returnArray);
}
sugar_cleanup(true);
