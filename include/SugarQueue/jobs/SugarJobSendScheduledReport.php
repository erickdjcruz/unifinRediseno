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

use Sugarcrm\Sugarcrm\Reports\ReportFactory;
use Sugarcrm\Sugarcrm\Reports\Schedules\ReportSchedules;
use Sugarcrm\Sugarcrm\Reports\Schedules\ReportSchedulesHelper;
use Sugarcrm\Sugarcrm\Util\Uuid;

/**
 * Class to run a job which should submit report to a single user and schedule next run time
 */
class SugarJobSendScheduledReport implements RunnableSchedulerJob
{
    /**
     * @var SchedulersJob
     */
    protected $job;

    /**
     * @param SchedulersJob $job
     */
    public function setJob(SchedulersJob $job)
    {
        $this->job = $job;
    }

    /**
     * @param $data
     * @return bool
     */
    public function run($data)
    {
        $reportFilename = null;
        $csvFileName = null;
        global $current_user;
        global $current_language;
        global $locale;

        $this->job->runnable_ran = true;
        $this->job->runnable_data = $data;

        $report_schedule_id = $data;

        $dataTableHtml = '';
        $chartImage = '';

        $reportSchedule = new ReportSchedule();
        $scheduleInfo = $reportSchedule->getInfo($report_schedule_id);

        $GLOBALS['log']->debug('-----> in Reports foreach() loop');

        $savedReport = BeanFactory::getBean('Reports', $scheduleInfo['report_id'], ['use_cache' => false]);
        if (!$savedReport || !$savedReport->id || !$savedReport->ACLAccess('view')) {
            $GLOBALS['log']->error('ScheduleReport: User ' . $current_user->id . ' can not access report id ' . $scheduleInfo['report_id']);
            $this->job->succeedJob();
            return true;
        }

        $GLOBALS['log']->debug('-----> Generating Reporter');
        $reporter = new Report(from_html($savedReport->content));

        $reporter->is_saved_report = true;
        $reporter->isScheduledReport = true;
        $reporter->saved_report = $savedReport;
        $reporter->saved_report_id = $savedReport->id;

        $mod_strings = return_module_language($current_language, 'Reports');

        // prevent invalid report from being processed
        if (!$reporter->is_definition_valid()) {
            $invalidFields = $reporter->get_invalid_fields();
            $args = [$scheduleInfo['report_id'], implode(', ', $invalidFields)];
            $message = string_format($mod_strings['ERR_REPORT_INVALID'], $args);

            $GLOBALS['log']->fatal("-----> {$message}");

            $reportOwner = BeanFactory::retrieveBean('Users', $savedReport->assigned_user_id);
            if ($reportOwner) {
                $reportsUtils = new ReportsUtilities();
                try {
                    $reportsUtils->sendNotificationOfInvalidReport($reportOwner, $message);
                } catch (MailerException $me) {
                    //@todo consider logging the error at the very least
                }
            }

            $this->job->failJob('Report field definition is invalid');
            return false;
        } else {
            // default to PDF
            $fileType = $scheduleInfo['file_type'] ?: 'PDF';

            $embedChart = false;
            $embedDataTable = false;
            $embedReport = $scheduleInfo['embed_report'];

            if (($embedReport === 'Chart' || $embedReport === 'Chart and Data Table') &&
                (!$reporter->chart_type || $reporter->chart_type !== 'none')) {
                $embedChart = true;
            }

            if ($embedReport === 'Data Table' || $embedReport === 'Chart and Data Table') {
                $embedDataTable = true;
            }

            if ($embedChart) {
                $fileType = '';
                $chartImage = $this->embedChart($reporter);
            }

            if ($embedDataTable) {
                $data = $this->embedDataTable($reporter);
                $fileType = $data['fileType'];
                $dataTableHtml = $data['dataTableHtml'];
            }

            $GLOBALS['log']->debug('-----> Reporter settings attributes');
            $reporter->layout_manager->setAttribute('no_sort', 1);

            $GLOBALS['log']->debug('-----> Reporter Handling PDF output');
            $filesToUnlink = [];

            if (!empty($fileType)) {
                if ($fileType == 'PDF') {
                    require_once 'modules/Reports/templates/templates_tcpdf.php';
                    $reportFilename = template_handle_pdf($reporter, false);
                    $filesToUnlink[] = $reportFilename;
                } elseif ($fileType == 'CSV') {
                    require_once 'modules/Reports/templates/templates_export.php';
                    $csvFileName = template_handle_export($reporter, false);
                    $filesToUnlink[] = $csvFileName;
                } else { // both PDF and CSV
                    require_once 'modules/Reports/templates/templates_tcpdf.php';
                    $reportFilename = template_handle_pdf($reporter, false);
                    require_once 'modules/Reports/templates/templates_export.php';
                    $csvFileName = template_handle_export($reporter, false);
                    $filesToUnlink[] = $reportFilename;
                    $filesToUnlink[] = $csvFileName;
                }
            }

            // get the recipient's data...

            // first get all email addresses known for this recipient
            $recipientEmailAddresses = [$current_user->email1, $current_user->email2];
            $recipientEmailAddresses = array_filter($recipientEmailAddresses);

            // then retrieve first non-empty email address
            $recipientEmailAddress = array_shift($recipientEmailAddresses);

            // get the recipient name that accompanies the email address
            $recipientName = $locale->formatName($current_user);

            try {
                $GLOBALS['log']->debug('-----> Generating Mailer');
                $mailer = MailerFactory::getSystemDefaultMailer();
                $timedate = TimeDate::getInstance();
                $reportTime = $timedate->getNow();
                $reportTime = $timedate->asUser($reportTime) . ' ' . $reportTime->format('T');
                $reportName = empty($savedReport->name) ? 'Report' : $savedReport->name;

                // add the recipient
                $mailer->addRecipientsTo(new EmailIdentity($recipientEmailAddress, $recipientName));

                // attach the report
                $charsToRemove = ["\r", "\n"];
                // remove these characters from the attachment name
                $attachmentName = str_replace($charsToRemove, '', $reportName . ' ' . $reportTime);
                // replace spaces with the underscores
                if (!empty($fileType)) {
                    if ($fileType == 'PDF') {
                        $this->attachFile($fileType, $mailer, $attachmentName, $reportFilename);
                    } elseif ($fileType == 'CSV') {
                        $this->attachFile($fileType, $mailer, $attachmentName, $csvFileName);
                    } else {
                        $this->attachFile('PDF', $mailer, $attachmentName, $reportFilename);
                        $this->attachFile('CSV', $mailer, $attachmentName, $csvFileName);
                    }
                }

                $emailConfig = SugarConfig::getInstance()->get('emailTemplate');
                $templateID = $emailConfig['‌ReportSchedule'] ?? '';

                // Pull the email template if it exists
                $emailTemplate = BeanFactory::getBean('EmailTemplates', $templateID);

                if (!empty($emailTemplate) && $emailTemplate->id) {
                    $siteUrl = SugarConfig::getInstance()->get('site_url');
                    $variables = [
                        '$site_url' => !empty($siteUrl) ? $siteUrl : '',
                        '$report_id' => !empty($savedReport->id) ? $savedReport->id : '',
                        '$assigned_user' => !empty($recipientName) ? $recipientName : '',
                        '$report_name' => !empty($reportName) ? $reportName : '',
                        '$report_time' => !empty($reportTime) ? $reportTime : '',
                    ];
                    $subject = str_replace(array_keys($variables), array_values($variables), $emailTemplate->subject);
                    $emailBody = $dataTableHtml || $chartImage ? $emailTemplate->body_html : $emailTemplate->body;
                    $body = str_replace(array_keys($variables), array_values($variables), $emailBody);
                } else {
                    // set the subject of the email
                    $subject = $mod_strings['LBL_SUBJECT_SCHEDULED_REPORT'] . $reportName .
                        $mod_strings['LBL_SUBJECT_AS_OF'] . $reportTime;

                    // set the body of the email
                    $body = $mod_strings['LBL_HELLO'];

                    if ($recipientName != '') {
                        $body .= " {$recipientName}";
                    }

                    $body .= ',<br><br>' .
                        $mod_strings['LBL_SCHEDULED_REPORT_MSG_INTRO'] .
                        '<br><br>' .
                        $mod_strings['LBL_SCHEDULED_REPORT_MSG_BODY1'] .
                        $reportName . '<br><br>' .
                        $mod_strings['LBL_SCHEDULED_REPORT_MSG_BODY2'] .
                        $reportTime .
                        '<br><br>';
                }

                if ($chartImage) {
                    $fileName = Uuid::uuid4() . '.png';
                    $processChartImg = str_replace('data:image/png;base64,', '', $chartImage);

                    //upload chart image locally
                    $uploadFile = new UploadFile();
                    $uploadFile->set_for_soap($fileName, base64_decode($processChartImg));
                    $uploadFile->final_move($fileName);

                    $fileLocation = $uploadFile->get_upload_path($fileName);
                    $mimeType = $uploadFile->getMimeSoap($fileName);

                    $embedImage = new EmbeddedImage($fileName, $fileLocation, $fileName, Encoding::Base64, $mimeType);
                    $mailer->addAttachment($embedImage);

                    $body .= "<img src='cid:$fileName'/><br>";

                    $filesToUnlink[] = $fileLocation;
                }

                if ($dataTableHtml) {
                    $body .= $dataTableHtml;
                }

                $textOnly = EmailFormatter::isTextOnly($body);
                if ($textOnly) {
                    $mailer->setTextBody($body);
                } else {
                    $textBody = strip_tags(br2nl($body)); // need to create the plain-text part
                    $mailer->setTextBody($textBody);
                    $mailer->setHtmlBody($body);
                }
                $mailer->setSubject($subject);

                $GLOBALS['log']->debug("-----> Sending PDF via Email to [ {$recipientEmailAddress} ]");
                $mailer->send();

                $GLOBALS['log']->debug('-----> Send successful');
                $reportSchedule->update_next_run_time(
                    $report_schedule_id,
                    $scheduleInfo['next_run'],
                    $scheduleInfo['time_interval']
                );
            } catch (MailerException $me) {
                switch ($me->getCode()) {
                    case MailerException::InvalidEmailAddress:
                        $GLOBALS['log']->info("No email address for {$recipientName}");
                        break;
                    default:
                        $GLOBALS['log']->fatal('Mail error: ' . $me->getMessage());
                        break;
                }
            }

            $GLOBALS['log']->debug('-----> Removing temporary files');
            foreach ($filesToUnlink as $file) {
                unlink($file);
            }

            $this->job->succeedJob();

            return true;
        }
    }

    /**
     * @param String $type
     * @param Object $mailer
     * @param String $attachmentName
     * @param String $filename
     */
    protected function attachFile(string $type, object $mailer, string $attachmentName, string $filename)
    {
        $typeLower = strtolower($type);
        $attachmentName = str_replace(' ', '_', "{$attachmentName}.{$typeLower}");
        $attachment = new Attachment($filename, $attachmentName, Encoding::Base64, "application/{$typeLower}");
        $mailer->addAttachment($attachment);
    }

    /**
     * Retrieves the html for data table
     *
     * @param Report $reporter
     * @return string[]
     */
    private function embedDataTable(Report $reporter)
    {
        $fileType = '';

        $scheduledReport = new ReportSchedules($reporter);
        $dataTableHtml = $scheduledReport->getDataTableHtml();

        if ($dataTableHtml === 'exceeded') {
            $fileType = 'CSV';
            $dataTableHtml = '';
        }

        return [
            'fileType' => $fileType,
            'dataTableHtml' => $dataTableHtml,
        ];
    }

    /**
     * Gets the chart data
     *
     * @param Report $reporter
     * @return null|string
     */
    private function embedChart(Report $reporter): ?string
    {
        $helper = new ReportSchedulesHelper();
        $sugarConfig = \SugarConfig::getInstance();
        $siteUrl = $sugarConfig->get('site_url');

        $reporter = ReportFactory::getReport('summary', ['record' => $reporter->saved_report_id]);
        $chartConfig = $reporter->buildChartConfig();

        $width = 800;
        $height = 600;

        $chartConfig['width'] = $width;
        $chartConfig['height'] = $height;

        $chartConfig['url'] = $siteUrl;

        if (!empty($chartConfig['data']) && !empty($chartConfig['type']) && !empty($chartConfig['data']['datasets'])) {
            $response = $helper->retrieveChartData($chartConfig);
            $response = $response->getBody()->getContents();
            $response = json_decode($response);

            if (isset($response->success) && $response->success === true && isset($response->chart)) {
                return $response->chart;
            } else {
                global $log;

                $message = $response->message;
                $log->fatal("Chart Service: {$message}");
            }
        }

        return null;
    }
}
