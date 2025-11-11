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

use Sugarcrm\Sugarcrm\Security\InputValidation\InputValidation;

if (!isset($_GET['DynamicAction']) || ($_GET['DynamicAction'] !== 'saveImage') ||
    !isset($_POST['chart_id']) || !isset($_POST['imageStr'])) {
    return;
}

$chart_id = InputValidation::getService()->getValidInputRequest('chart_id', 'Assert\Guid');
$priv_guid = $GLOBALS['current_user']->getUserPrivGuid();

/* allowed file_extension => mime_type */
$allowed_mime_types = [
    'jpg' => 'image/jpeg',
    'png' => 'image/png',
];

[$mtype, $image] = explode(',', $_POST['imageStr']);

/* we got from client something likes data:image/png;base64 */
$mtype_processed = [];
foreach ($allowed_mime_types as $ext => $mt) {
    $mtype_processed['data:' . $mt . ';base64'] = $ext;
}

/* check mime type. */
if (!array_key_exists($mtype, $mtype_processed)) {
    throw new \RuntimeException('Invalide MIME type');
}

/* build file name  */
$file_extension = $mtype_processed[$mtype];
$file_name = $priv_guid . '_' . $chart_id . '_saved_chart.' . $file_extension;
$filepath = sugar_cached('images/' . $file_name);

/* process image */
$image = str_replace(' ', '+', $image);
$image = base64_decode($image);

/* check image size */
if (strlen($image) > $sugar_config['upload_maxsize']) {
    throw new \RuntimeException(sprintf('File %s is too big', $file_name));
}

/* upload file to cache/image */
if (!sugar_mkdir(sugar_cached('images'), 0777, true)) {
    throw new \RuntimeException(sprintf("Can't create directory '%s'", sugar_cached('images')));
}

$tmpFile = tempnam(sugar_cached('images'), 'charts');

if (false === file_put_contents($tmpFile, $image)) {
    throw new \RuntimeException(sprintf("Can't write data into '%s'", $tmpFile));
}
if (!verify_uploaded_image($tmpFile)) {
    unlink($tmpFile);
    throw new \RuntimeException('Uploaded file is not a valid image');
}
if (!rename($tmpFile, $filepath)) {
    throw new \RuntimeException("Can't rename tmp file '%s' to '%s'", $tmpFile, $filepath);
}
