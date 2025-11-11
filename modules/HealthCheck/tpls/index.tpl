{*
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
*}

<link rel="stylesheet" href="{sugar_getjspath file='styleguide/assets/css/upgrade.css'}"/>
<script src='{sugar_getjspath file='include/javascript/jquery/jquery-min.js'}'></script>
<script src='{sugar_getjspath file='include/javascript/phpjs/get_html_translation_table.js'}'></script>
<script src='{sugar_getjspath file='include/javascript/phpjs/htmlentities.js'}'></script>

<div class="upgrade">
    <div id="alerts" class="alert-top">
        <div class="alert-wrapper">
            <div class="alert alert-danger alert-block" data-flag="3">
                <button class="btn btn-link btn-invisible close" data-action="close">
                    <i class="sicon sicon-close"></i>
                </button>
                <strong>Error</strong>
                The health check didn't pass. Please correct the issue and <a href="index.php?module=HealthCheck">restart</a>
                or <a href="mailto:support@sugarcrm.com">contact support</a>.
            </div>
            <div class="alert alert-success alert-block" data-flag="1">
                <button class="btn btn-link btn-invisible close" data-action="close">
                    <i class="sicon sicon-close"></i>
                </button>
                <strong>Success</strong>
                You have passed the health check.
            </div>
            <div class="alert alert-warning alert-block" data-flag="2">
                <button class="btn btn-link btn-invisible close" data-action="close">
                    <i class="sicon sicon-close"></i>
                </button>
                <strong>Warning</strong>
                Adjustments to your system will be automatically applied, please take a note.
            </div>
            <div class="alert alert-success alert-block" data-send="ok">
                <button class="btn btn-link btn-invisible close" data-action="close">
                    <i class="sicon sicon-close"></i>
                </button>
                <strong>Success</strong>
                Log was sent successfully.
            </div>
            <div class="alert alert-danger alert-block" data-send="error">
                <button class="btn btn-link btn-invisible close" data-action="close">
                    <i class="sicon sicon-close"></i>
                </button>
                <strong>Error</strong>
                Unable to send log to Sugar. Please make sure you've internet connection.
            </div>
        </div>
    </div>
    <div class="modal" data-step="1">
        <div class="modal-header modal-header-upgrade row-fluid">
            <span class="step-circle">
                <span>1</span>
            </span>

            <div class="upgrade-title span7">
                <h3>Sugar Health Check</h3>
                <span></span>
            </div>
            <div class="progress-section span5 pull-right">
                <span><img src="themes/default/images/company_logo.png" alt="SugarCRM" class="logo"></span>

                <div class="progress progress-success">
                    <div class="bar in-progress" style="width: 33%;"></div>
                </div>
            </div>
        </div>
        <div class="modal-body record">
            <div class="row-fluid">
                <h1>Upgrading</h1>

                <p>To ensure a successful upgrade this health check wizard will scan your current SugarCRM
                    instance and will generate a full report of any incompatible customizations. This report
                    will explain which changes will be performed to your instance during an upgrade. In case
                    of any incompatible issues which cannot be automatically resolved by the upgrade wizard,
                    this health check tool will report what needs to be addressed.</p>

                <p>If not all prerequisites pass, an upgrade to Sugar 7 will not be possible.</p>

            </div>
        </div>
        <div class="modal-footer">
          <span sfuuid="25" class="detail">
            <a class="btn btn-invisible btn-link" href="index.php">Cancel</a>
            <a class="btn btn-primary" href="#2" name="next_button">Next</a>
          </span>
        </div>
    </div>
    <div class="modal" data-step="2">
        <div class="modal-header modal-header-upgrade row-fluid">
            <span class="step-circle">
                <span>2</span>
            </span>

            <div class="upgrade-title span7">
                <h3>Sugar Health Check</h3>
                <span>Review health check results</span>
            </div>
            <div class="progress-section span5 pull-right">
                <span><img src="themes/default/images/company_logo.png" alt="SugarCRM" class="logo"></span>

                <div class="progress progress-success">
                    <div class="bar in-progress" style="width: 66%;"></div>
                </div>
            </div>
        </div>
        <div class="modal-body record">
            <div class="row-fluid" id="healthcheck">
                <h1><i class="sicon sicon-settings sicon-is-spinning"></i>Performing health check. Please wait...</h1>
            </div>
        </div>
        <div class="modal-footer">
          <span sfuuid="25" class="detail">
            <a class="btn btn-invisible btn-link" href="index.php">Cancel</a>
            <a class="btn btn-invisible btn-link send-logs" href="javascript:void(0);">Send Log to Sugar</a>
            <a class="btn btn-invisible btn-link" href="index.php?module=HealthCheck&action=export">Export Log</a>
              {if isset($smarty.get.referrer)}
                  <a class="btn btn-primary disabled" href="index.php?module=HealthCheck&action=confirm"
                     name="next_button">Confirm</a>


                                                          {else}


                  <a class="btn btn-primary" href="#Administration"
                     name="next_button">Back to Admin Page</a>
              {/if}
          </span>
        </div>
    </div>
</div>
    <script>
        (function () {

            function doHealthCheck() {
                var flagToIcon = [, 'sicon-check-circle color_green', 'sicon-ellipsis color_yellow', 'sicon-warning-circle'],
                        $healthcheck = $("#healthcheck");
                $.ajax('index.php?module=HealthCheck&action=scan', {
                    dataType: 'json',
                    success: function (data) {
                        if (data.length == 0) {
                            data = [
                                { flag: 1, descr: "Your instance is ready for upgrade!", title: "Success", displayNumber: false }
                            ];
                        }
                        data = data.sort(_sortByBucket);
                        $healthcheck.html("");
                        for (var i = 0; i < data.length; i++) {
                            var item = data[i];
                            var displayNumber = typeof data[i].displayNumber == 'undefined' ? true : data[i].displayNumber;
                            var html = ["<h1><i class='fa ", flagToIcon[parseInt(item.flag)], "'></i> "];
                            if (displayNumber) {
                                html.push(i + 1, ". ");
                            }
                            html.push(htmlentities(item.title, 'ENT_NOQUOTES'), "</h1><p>", htmlentities(item.descr, 'ENT_NOQUOTES'));
                            if (data[i].kb) {
                                html.push("<a target='_blank' href='", data[i].kb, "'> Learn more...</a>");
                            }
                            html.push("</p>");
                            $healthcheck.append(html.join(""));
                        }
                        $healthcheck.parent().scrollTop($healthcheck.height());
                        var flag = parseInt(data[data.length - 1].flag);
                        _displayAlert(flag);
                        if (flag < 3) {
                            $('.btn.btn-primary.disabled').removeClass('disabled');
                        }
                    },
                    error: function () {
                        var html = ["<h1><i class='fa ", flagToIcon[parseInt(3)],
                            "'></i> Unexpected error occurred!</h1><p>We've encountered an unexpected error during health check procedure. Please <a href='mailto:support@sugarcrm.com'>contact support</a>.</p>"];
                        $healthcheck.html(html.join(""));
                        _displayAlert(3);
                    }
                });
            }

            function _sortByBucket(item1, item2) {
                if (item1.bucket > item2.bucket) return 1;
                if (item1.bucket < item2.bucket) return -1;
                return 0;
            }

            function _displayAlert(flag) {
                $('#alerts [data-flag=' + flag + ']').show();
            }

            function sendLogs() {
                $.ajax('index.php?module=HealthCheck&action=send', {
                    dataType: 'json',
                    success: function (data) {
                        $('[data-send=' + data.status + ']').show();
                    },
                    error: function () {
                        $('[data-send="error"]').show();
                    }
                });
            }


            function showNextStep() {
                var nextStep = currentStep + 1;
                if (nextStep <= maxSteps) {
                    $('[data-step="' + currentStep + '"]').hide();
                    $('[data-step="' + nextStep + '"]').show();
                    currentStep = nextStep;
                }
                return false;
            }

            var currentStep = 0,
                    maxSteps = 2,
                    hashStep = parseInt(window.location.hash);

            if (hashStep > currentStep) {
                currentStep = hashStep - 1;
            }

            $('.close').on('click', function () {
                $(this).parents('.alert').hide();
            });

            $('[data-step="1"] a[name="next_button"]').on('click', showNextStep).on('click', doHealthCheck);
            $('.send-logs').on('click', sendLogs);

            showNextStep();
        })();
    </script>

