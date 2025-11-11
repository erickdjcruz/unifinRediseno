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
use Sugarcrm\Sugarcrm\Security\Csrf\CsrfAuthenticator;
use Sugarcrm\Sugarcrm\PackageManager\Entity\PackageManifest;
use Sugarcrm\Sugarcrm\PackageManager\File\PackageZipFile;
use Sugarcrm\Sugarcrm\PackageManager\PackageManager;

require_once 'include/utils.php';

MlpLogger::replaceDefault();

$historyId = InputValidation::getService()->getValidInputRequest('install_file');
if (empty($historyId)) {
    sugar_die(htmlspecialchars(translate('ERR_UW_NO_PACKAGE_FILE', 'Administration'), ENT_COMPAT));
}

$upgradeHistory = BeanFactory::retrieveBean('UpgradeHistory', $historyId);
if (empty($upgradeHistory->id)) {
    sugar_die(htmlspecialchars(translate('ERR_UW_NO_PACKAGE_FILE', 'Administration'), ENT_COMPAT));
}
$packageManager = new PackageManager();
try {
    $historyManifest = $upgradeHistory->getPackageManifest();
    $packageZipFile = new PackageZipFile($upgradeHistory->getFileName(), $packageManager->getBaseTempDir());
    $packageZipFile->extractPackage();
} catch (SugarException $e) {
    sugar_die($e->getMessage());
}

$mode = InputValidation::getService()->getValidInputRequest(
    'mode',
    [
        'Assert\Choice' => [
            'choices' => [
                'Install',
                'Uninstall',
                'Disable',
                'Enable',
            ],
        ],
    ],
    ''
);
if (empty($mode)) {
    sugar_die(htmlspecialchars(translate('LBL_UPGRADE_WIZARD_NO_MODE_SPEC', 'Administration'), ENT_COMPAT));
}

$installType = $historyManifest->getPackageType();
$csrfFieldName = CsrfAuthenticator::FORM_TOKEN_FIELD;
$csrfToken = htmlspecialchars(CsrfAuthenticator::getInstance()->getFormToken(), ENT_QUOTES, 'UTF-8');

$license = $readme = '';
$licenseFile = $packageZipFile->getPackageDir() . DIRECTORY_SEPARATOR . 'LICENSE.txt';
$readmeFile = $packageZipFile->getPackageDir() . DIRECTORY_SEPARATOR . 'README.txt';
$isPackageTypeModule = $installType === PackageManifest::PACKAGE_TYPE_MODULE;
if ($isPackageTypeModule && ($mode === 'Install' || $mode === 'Enable') && file_exists($licenseFile)) {
    $licenseContent = htmlspecialchars(file_get_contents($licenseFile), ENT_COMPAT);
    $moduleLicenseLabel = htmlspecialchars(translate('LBL_LICENSE', 'Administration'), ENT_COMPAT);
    $moduleReadLicenseLabel = htmlspecialchars(translate('LBL_MODULE_LICENSE', 'Administration'), ENT_COMPAT);
    $moduleLicenseAcceptLabel = htmlspecialchars(translate('LBL_ACCEPT', 'Administration'), ENT_COMPAT);
    $moduleLicenseDenyLabel = htmlspecialchars(translate('LBL_DENY', 'Administration'), ENT_COMPAT);
    $moduleLicenseError = htmlspecialchars(translate('ERR_UW_ACCEPT_LICENSE', 'Administration'), ENT_COMPAT);
    if (!empty($licenseContent)) {
        $license = <<<HTML
<div id="uw-license-block" style="text-align:left; width: 50%; margin-top: 15px;">
    <h2>$moduleLicenseLabel</h2>
    <h3>$moduleReadLicenseLabel</h3>
    <textarea style="display: block; margin: 15px; height: 500px; width: 700px;" readonly>$licenseContent</textarea>
    <div id="module-license-error" style="display: none; color: red;">$moduleLicenseError</div>
    <div style="display: block">
        <div style="display: inline-block; width: 25%">
            <lable for="radio_license_agreement_accept">$moduleLicenseAcceptLabel</lable>
            <input type="radio" id="radio_license_agreement_accept" name="radio_license_agreement" value="accept">
        </div>
        <div style="display: inline-block; width: 25%">
            <lable for="radio_license_agreement_deny">$moduleLicenseDenyLabel</lable>
            <input type="radio" id="radio_license_agreement_deny" name="radio_license_agreement" value="reject" checked>
        </div>            
    </div>
</div>
HTML;
        $readmeContent = $historyManifest->getManifestValue('readme');
        if (empty($readmeContent) && file_exists($readmeFile)) {
            $readmeContent = file_get_contents($readmeFile);
        }
        if (!empty($readmeContent)) {
            $readmeContent = htmlspecialchars($readmeContent, ENT_COMPAT);
            $readmeLabel = htmlspecialchars(translate('LBL_README', 'Administration'), ENT_COMPAT);
            $showMoreLabel = htmlspecialchars(translate('LBL_SHOW_MORE'), ENT_COMPAT);
            $showLessLabel = htmlspecialchars(translate('LBL_SHOW_LESS'), ENT_COMPAT);
            $readme = <<<HTML
<div id="uw-readme-block" style="text-align:left; width: 50%; margin-top: 15px;">
    <div style="display: block">
        <div style="display: inline-block; width: 25%">
            <h2>$readmeLabel</h2>
        </div>
        <div style="display: inline-block; text-align: left; width: 15%">
            <a id="readme-toggle-link" href="#" data-toggle-text="$showLessLabel">$showMoreLabel</a>
        </div>
    </div>    
    <div id="readme-content-block" style="display: none;">
        <textarea style="display: block; margin: 15px; height: 500px; width: 700px;" readonly>$readmeContent</textarea>
    </div>
</div>
HTML;
        }
    }
}

switch ($mode) {
    case 'Install':
        $actionLabel = htmlspecialchars(translate('LBL_UW_PATCH_READY', 'Administration'), ENT_COMPAT);
        if ($installType === PackageManifest::PACKAGE_TYPE_LANGPACK) {
            $actionLabel = htmlspecialchars(translate('LBL_UW_LANGPACK_READY', 'Administration'), ENT_COMPAT);
        }
        break;
    case 'Uninstall':
        $actionLabel = htmlspecialchars(translate('LBL_UW_UNINSTALL_READY', 'Administration'), ENT_COMPAT);
        if ($installType === PackageManifest::PACKAGE_TYPE_LANGPACK) {
            $actionLabel = htmlspecialchars(translate('LBL_UW_LANGPACK_READY', 'Administration'), ENT_COMPAT);
        } elseif ($installType !== PackageManifest::PACKAGE_TYPE_MODULE) {
            $actionLabel = htmlspecialchars(translate('LBL_UW_FILES_REMOVED', 'Administration'), ENT_COMPAT);
        }
        break;
    case 'Disable':
        $actionLabel = htmlspecialchars(translate('LBL_UW_DISABLE_READY', 'Administration'), ENT_COMPAT);
        if ($installType === PackageManifest::PACKAGE_TYPE_LANGPACK) {
            $actionLabel = htmlspecialchars(translate('LBL_UW_LANGPACK_READY_DISABLE', 'Administration'), ENT_COMPAT);
        }
        break;
    case 'Enable':
        $actionLabel = htmlspecialchars(translate('LBL_UW_ENABLE_READY', 'Administration'), ENT_COMPAT);
        if ($installType === PackageManifest::PACKAGE_TYPE_LANGPACK) {
            $actionLabel = htmlspecialchars(translate('LBL_UW_LANGPACK_READY_ENABLE', 'Administration'), ENT_COMPAT);
        }
        break;
}

$removeTables = $historyManifest->getManifestValue('remove_tables');
$removeTablesContent = '';
if ($removeTables === 'prompt' && $mode === 'Uninstall') {
    $removeTablesLabel = htmlspecialchars(translate('ML_LBL_REMOVE_TABLES', 'Administration'), ENT_COMPAT);
    $doNotRemoveTablesLabel = htmlspecialchars(translate('ML_LBL_DO_NOT_REMOVE_TABLES', 'Administration'), ENT_COMPAT);

    $removeTablesContent = <<<HTML
<div style="width: 50%; display: block; margin-top: 15px;">
    <div style="display: block">
        <div style="display: inline-block; width: 25%">
            <lable for="remove_tables_true">$removeTablesLabel</lable>'
            <input type="radio" id="remove_tables_true" name="remove_tables" value="true" checked>
        </div>
        <div style="display: inline-block; width: 25%">
            <lable for="remove_tables_false">$doNotRemoveTablesLabel</lable>
            <input type="radio" id="remove_tables_false" name="remove_tables" value="false">
        </div>            
    </div>
</div>
HTML;
}
$overwriteFilesContent = '';
if ($mode === 'Disable' || $mode === 'Enable') {
    $copyFiles = $historyManifest->getInstallDefsValue('copy');
    if (!empty($copyFiles)) {
        $overwriteFilesLabel = htmlspecialchars(translate('LBL_OVERWRITE_FILES', 'Administration'), ENT_COMPAT);
        $doNotOverwriteFilesLabel = htmlspecialchars(translate('LBL_DO_OVERWRITE_FILES', 'Administration'), ENT_COMPAT);

        $overwriteFilesContent = <<<HTML
<div style="width: 50%; display: block; margin-top: 15px;">
    <div style="display: inline-block; width: 25%">
        <h3>$overwriteFilesLabel</h3>
    </div>
    <div style="display: block">
        <div style="display: inline-block; width: 25%">
            <lable for="radio_overwrite_files">$overwriteFilesLabel</lable>
            <input type='radio' id='radio_overwrite_files' name='radio_overwrite' value='overwrite'>
        </div>
        <div style="display: inline-block; width: 25%">
            <lable for="radio_do_not_overwrite_files">$doNotOverwriteFilesLabel</lable>
            <input type='radio' id='radio_do_not_overwrite_files' name='radio_overwrite' value='do_not_overwrite' checked>
        </div>            
    </div>
</div>
HTML;
    }
}

$commitButtonLabel = htmlspecialchars(translate('LBL_ML_COMMIT', 'Administration'), ENT_COMPAT);
$cancelButtonLabel = htmlspecialchars(translate('LBL_ML_CANCEL', 'Administration'), ENT_COMPAT);

echo <<<HTML
<div style="display:block; width: 100%;">
    <form action="index.php?module=Administration&view=module&action=UpgradeWizard_commit" method="post" id="uw-prepare-form">
        <input type="hidden" name="mode" value="$mode" />
        <input type="hidden" name="$csrfFieldName" value="$csrfToken" />
        <input type="hidden" name="package_id"  value="{$upgradeHistory->id}" />
        $license
        $readme
        <div style="width: 50%; display: block; margin-top: 15px;"><h2>$actionLabel</h2></div>
        $removeTablesContent
        $overwriteFilesContent
        <div style="width: 50%; display: block; margin-top: 15px;">
            <input disabled="disabled" type=submit value="$commitButtonLabel" class="button" id="uw-prepare-form-submit-button" />
            <input type=button value="$cancelButtonLabel" class="button" onClick="location.href='index.php?module=Administration&action=UpgradeWizard&view=module';"/>
        </div>
    </form>
</div>

<script>
    (function() {
        let form = document.getElementById('uw-prepare-form');
        let readmeDiv = document.getElementById('uw-readme-block');
        
        form.onsubmit = function() {
            let licenseDiv = document.getElementById('uw-license-block');
            if (typeof(licenseDiv) !== 'undefined' && licenseDiv !== null) {
                if (document.getElementById("radio_license_agreement_deny").checked) {
                    document.getElementById('module-license-error').style.display = 'block';
                    setTimeout(function() {
                        document.getElementById('module-license-error').style.display = 'none';
                    }, 5000);
                    return false;
                }
            }
            
            if (form.elements.mode.value === 'Install') {
                asyncInstallationStart(form.elements.package_id.value);
                return false;
            }
            
            form.submit();
            return false;
        }
        
        if (typeof(readmeDiv) !== 'undefined' && readmeDiv !== null) {
            let showMoreLink = document.getElementById('readme-toggle-link');
            let readmeContent = document.getElementById('readme-content-block');
            
            showMoreLink.onclick = function() {
                let toggleText = showMoreLink.getAttribute('data-toggle-text');
                showMoreLink.setAttribute('data-toggle-text', showMoreLink.textContent);
                showMoreLink.textContent = toggleText;
                
                if (readmeContent.style.display == "none") {
                    readmeContent.style.display = 'block';
                } else {
                    readmeContent.style.display = 'none';
                }
            }
        }
        
        document.getElementById('uw-prepare-form-submit-button').disabled = false;
        
        // async installation UI
        let packageId;
        let installationProcessDone;
        let awaitPostInstall;
        let awaitTimedOutProcess;
        let progressMessageEl = null;
        let progressMessageLogStat = {error: 0, message: 0, is_staging: false};
        let installationProgressTimer;
        let progressMessage = function(flag) {
            if (flag) {
                let template = app.template.getView('module-loader.process', 'Administration');
                progressMessageEl = $('<div></div>');
                progressMessageEl.html(template({id: 'package-install-progress-bar'}));
                app.\$rootEl.find('.alert-top').append(progressMessageEl);
            } else if (progressMessageEl) {
                progressMessageEl.remove();
            }
        }
        
        let navigateToInitialPage = function() {
            if (window.top) {
                const urlPieces = [location.protocol, '//', location.host, location.pathname];
                window.top.location.href = urlPieces.join('') 
                    + '#bwc/index.php?module=Administration&action=UpgradeWizard&view=module&reloadMetadata=true';
            }
        }
        
        let errorMessage = function(title, messages) {
            progressMessage(false);
            app.alert.show('module_loader_error', {
                level: 'error',
                title: title || app.lang.get('ERR_INTERNAL_ERR_MSG'),
                messages: messages || ['ERR_HTTP_500_TEXT_LINE1', 'ERR_HTTP_500_TEXT_LINE2']
            });
        }
        
        let installedSuccessfully = function() {
            progressMessageEl.find('#ml-installing-lbl').toggle();
            progressMessageEl.find('#ml-installing-success-lbl').toggle();
            progressMessageEl.find('.alert-info').removeClass('alert-info').addClass('alert-success');
            progressMessageEl.find('button').toggle();
            progressMessageEl.find('.progress .bar').css('width', '100%');
            progressMessageEl.find('h5').html('100%');
        }

        let isInstallationFailed = false;
        let installationError = function(error) {
            if (isInstallationFailed) {
                return;
            }
            isInstallationFailed = true;

            progressMessageEl.find('pre').append("\\n" + '<b style="color: red">' + error + '</b>')
                .scrollTop(function() { return this.scrollHeight; });
            progressMessageEl.find('#ml-installing-lbl').toggle();
            progressMessageEl.find('#ml-installing-error-lbl').toggle();
            progressMessageEl.find('.alert-info').removeClass('alert-info').addClass('alert-danger');
            progressMessageEl.find('button').toggle();
            progressMessageEl.find('.sicon-chevron-down').parent().find('a').click();
        }
        
        let checkInstallationProgress = function() {
            if (awaitPostInstall) {
                return;
            }
            let attributes = {
                id: packageId
            };
            let url = app.api.buildURL('Administration/packages', 'installation-status', attributes);
            let errorCounter = 0;
            let dotRendered = false;
            $.ajax({
              url: url,
              cache: false,
              dataType: "json"
            }).done(function(progress) {
                let ml = progressMessageEl.find('pre');
                
                if (progress.message) {
                    for (let ts in progress.message) {
                        ts = parseInt(ts, 10);
                        if (ts > progressMessageLogStat.message) {
                            progressMessageLogStat.message = ts;
                            if (dotRendered) {
                                ml.append("\\n");
                            }
                            ml.append(progress.message[ts] + "\\n")
                                .scrollTop(function() { return this.scrollHeight; })
                        }
                    }
                }
                if (progress.error) {
                    installationError(progress.error);
                    navigateToInitialPage();
                    return;
                }
                
                if (progress.error_page) {
                    setTimeout(function() {
                        progressMessage(false);
                        if (installationProcessDone) {
                            document.write(installationProcessDone);
                        } else {
                            errorMessage();
                            navigateToInitialPage();
                        }
                    }, 500);
                    return;
                }
                
                if (!progress.is_staged && progress.is_done) {
                    installedSuccessfully();
                    navigateToInitialPage();
                    return;
                } else if (progress.is_done) {
                    if (!progressMessageLogStat.is_staging) {
                        progressMessageLogStat.is_staging = true;
                        ml.append("checking the package ...");
                        ml.scrollTop(function() { return this.scrollHeight; });
                    } else {
                        dotRendered = true;
                        ml.append(".");
                    }
                } else {
                    let c = parseInt(progress.current_step, 10);
                    let t = parseInt(progress.total_steps, 10);
                    let p = Math.floor((c / t) * 100);
                    if (!isNaN(p) && t > 0) {
                        p = p === 100 ? 99 : p;
                        progressMessageEl.find('.progress .bar').css('width', p + '%');
                        progressMessageEl.find('h5').html(p + '%');
                    }
                    
                    if (progress.await_post_install && !installationProcessDone && !awaitTimedOutProcess) {
                        awaitPostInstall = true;
                        ml.append("package post-install execution...");
                        ml.scrollTop(function() { return this.scrollHeight; });
                        return;
                    }
                }
                
                if (installationProgressTimer) {
                    setInstallationProgressCheck(true);
                    if (installationProcessDone) {
                        installationProgressTimer = null;
                    }
                } else if (installationProcessDone) {
                    installationError(app.lang.get('LBL_ML_INSTALLATION_FATAL', 'Administration'));
                    navigateToInitialPage();
                }
            })
            .fail(function() {
                errorCounter++;
                if (errorCounter < 10 && installationProgressTimer) {
                    awaitPostInstall = false;
                    setInstallationProgressCheck(true);
                } else {
                    errorMessage();
                    navigateToInitialPage();
                }
            });
        }
        let setInstallationProgressCheck = function(flag) {
            if (flag) {
                setInstallationProgressCheck(false);
                installationProgressTimer = setTimeout(checkInstallationProgress.bind(this), 1000);
            } else if (installationProgressTimer) {
                clearTimeout(installationProgressTimer);
            }
        }
        
        let asyncInstallationStart = function(pkgId) {
            packageId = pkgId;
            installationProcessDone = false;
            progressMessage(true);
            
            let url = [
                '?module=Administration',
                'view=module',
                'action=UpgradeWizard_commit'
            ].join('&');
            
            let data = {};
            $(form).serializeArray().map(function(x) {data[x.name] = x.value;});
            
            app.api.call('create', url, null, {
                success: function(html) {
                    installationProcessDone = html;
                    if (awaitPostInstall) {
                        awaitPostInstall = false;
                        checkInstallationProgress();
                    }
                },
                error: function(response) {
                    awaitPostInstall = false;
                    if (response && response.status === 504) {
                        awaitTimedOutProcess = true;
                        let ml = progressMessageEl.find('pre');
                        ml.append(
                            "\\nResponse timeout!\\nThe process will continue. It it takes too long - please " 
                            + "contact with Administrator\\n"
                            ).scrollTop(function() { return this.scrollHeight; });
                        checkInstallationProgress();
                        return;
                    }
                    
                    setInstallationProgressCheck(false);
                    installationProgressTimer = null;
                    checkInstallationProgress();
                }
            }, {
                dataType: 'text',
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                processData: true,
                data: data
            });
            
            setInstallationProgressCheck(true);
        }
    })();
</script>
HTML;
