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

use Sugarcrm\Sugarcrm\Security\Validator\Constraints\File;
use Sugarcrm\Sugarcrm\Security\Validator\Validator;
use Sugarcrm\Sugarcrm\Security\InputValidation\Exception\ViolationException;

class MailApi extends ModuleApi
{
    /*-- API Argument Constants --*/
    public const EMAIL_CONFIG = 'email_config';
    public const FROM_ADDRESS = 'from_address';
    public const TO_ADDRESSES = 'to_addresses';
    public const CC_ADDRESSES = 'cc_addresses';
    public const BCC_ADDRESSES = 'bcc_addresses';
    public const ATTACHMENTS = 'attachments';
    public const TEAMS = 'teams';
    public const RELATED = 'related';
    public const SUBJECT = 'subject';
    public const HTML_BODY = 'html_body';
    public const TEXT_BODY = 'text_body';
    public const STATUS = 'status';
    public const DATE_SENT = 'date_sent';
    public const ASSIGNED_USER_ID = 'assigned_user_id';

    /*-- API Fields with default values --*/
    public static $fields = [
        self::EMAIL_CONFIG => '',
        self::FROM_ADDRESS => '',
        self::TO_ADDRESSES => [],
        self::CC_ADDRESSES => [],
        self::BCC_ADDRESSES => [],
        self::ATTACHMENTS => [],
        self::TEAMS => [],
        self::RELATED => [],
        self::SUBJECT => '',
        self::HTML_BODY => '',
        self::TEXT_BODY => '',
        self::STATUS => '',
        self::DATE_SENT => '',
        self::ASSIGNED_USER_ID => '',
    ];

    /*-- Supported API Status values --*/
    private static $apiStatusValues = [
        'draft', // draft
        'ready', // ready to be sent
        'archive', // archived
    ];

    /*-- Supported API Attachment Type values --*/
    private static $apiAttachmentTypes = [
        'document',
        'template',
        'upload',
    ];

    private $emailRecipientsService;

    /**
     * {@inheritdoc}
     */
    public function registerApiRest()
    {
        $api = [
            'createMail' => [
                'reqType' => 'POST',
                'path' => ['Mail'],
                'pathVars' => [''],
                'method' => 'createMail',
                'shortHelp' => 'Create Mail Item',
                'longHelp' => 'modules/Emails/clients/base/api/help/mail_post_help.html',
            ],
            'archiveMail' => [
                'reqType' => 'POST',
                'path' => ['Mail', 'archive'],
                'pathVars' => [''],
                'method' => 'archiveMail',
                'shortHelp' => 'Archive Mail Item',
                'longHelp' => 'modules/Emails/clients/base/api/help/mail_archive_help.html',
            ],
            'recipientLookup' => [
                'reqType' => 'POST',
                'path' => ['Mail', 'recipients', 'lookup'],
                'pathVars' => [''],
                'method' => 'recipientLookup',
                'shortHelp' => 'Lookup Email Recipient Info',
                'longHelp' => 'modules/Emails/clients/base/api/help/mail_recipients_lookup_post_help.html',
            ],
            'listRecipients' => [
                'reqType' => 'GET',
                'path' => ['Mail', 'recipients', 'find'],
                'pathVars' => [''],
                'method' => 'findRecipients',
                'shortHelp' => 'Search For Email Recipients',
                'longHelp' => 'modules/Emails/clients/base/api/help/mail_recipients_find_get_help.html',
            ],
            'validateEmailAddresses' => [
                'reqType' => 'POST',
                'path' => ['Mail', 'address', 'validate'],
                'pathVars' => [''],
                'method' => 'validateEmailAddresses',
                'shortHelp' => 'Validate One Or More Email Address',
                'longHelp' => 'modules/Emails/clients/base/api/help/mail_address_validate_post_help.html',
            ],
            'saveAttachment' => [
                'reqType' => 'POST',
                'path' => ['Mail', 'attachment'],
                'pathVars' => ['', ''],
                'method' => 'saveAttachment',
                'rawPostContents' => true,
                'shortHelp' => 'Saves a mail attachment.',
                'longHelp' => 'modules/Emails/clients/base/api/help/mail_attachment_post_help.html',
            ],
            'removeAttachment' => [
                'reqType' => 'DELETE',
                'path' => ['Mail', 'attachment', '?'],
                'pathVars' => ['', '', 'file_guid'],
                'method' => 'removeAttachment',
                'rawPostContents' => true,
                'shortHelp' => 'Removes a mail attachment',
                'longHelp' => 'modules/Emails/clients/base/api/help/mail_attachment_record_delete_help.html',
            ],
            'clearUserCache' => [
                'reqType' => 'DELETE',
                'path' => ['Mail', 'attachment', 'cache'],
                'pathVars' => ['', '', ''],
                'method' => 'clearUserCache',
                'rawPostContents' => true,
                'shortHelp' => 'Clears the user\'s attachment cache directory',
                'longHelp' => 'modules/Emails/clients/base/api/help/mail_attachment_cache_delete_help.html',
            ],
        ];

        return $api;
    }

    /**
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @see EmailsApi::createRecord()
     * @deprecated POST /Mail has been deprecated and will not be available after v11. Use POST /Emails instead.
     */
    public function createMail(ServiceBase $api, array $args)
    {
        LoggerManager::getLogger()->deprecated(
            'POST /Mail has been deprecated and will not be available after v11. Use POST /Emails instead.'
        );

        return $this->handleMail($api, $args);
    }

    /**
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionRequestMethodFailure
     * @throws SugarApiExceptionInvalidParameter
     * @deprecated PUT /Mail/:record has been deprecated and will not be available after v11. Use PUT /Emails/:record
     * instead.
     * @see EmailsApi::updateRecord()
     */
    public function updateMail(ServiceBase $api, array $args)
    {
        LoggerManager::getLogger()->deprecated(
            'PUT /Mail/:record has been deprecated and will not be available after v11. Use PUT /Emails/:record ' .
            'instead.'
        );

        $email = new Email();

        if (isset($args['email_id']) && !empty($args['email_id'])) {
            if ((!$email->retrieve($args['email_id'])) || ($email->id != $args['email_id'])) {
                throw new SugarApiExceptionMissingParameter();
            }

            if ($email->state !== Email::STATE_DRAFT) {
                throw new SugarApiExceptionRequestMethodFailure();
            }
        } else {
            throw new SugarApiExceptionInvalidParameter();
        }

        return $this->handleMail($api, $args);
    }

    /**
     * Archive email.
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @see EmailsApi::createRecord()
     * @deprecated POST /Mail/archive has been deprecated and will not be available after v11. Use POST /Emails with
     * `{"state": "Archived"}` instead.
     */
    public function archiveMail(ServiceBase $api, array $args)
    {
        LoggerManager::getLogger()->deprecated(
            'POST /Mail/archive has been deprecated and will not be available after v11. Use POST /Emails with ' .
            '{"state": "Archived"} instead.'
        );

        // Perform Front End argument validation per the Mail API architecture
        // Non-compliant arguments will result in an Invalid Parameter Exception Thrown
        $this->validateArguments($args);
        $mailRecord = $this->initMailRecord($args);
        return $mailRecord->archive();
    }

    /**
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionRequestMethodFailure
     * @see MailApi::createMail()
     * @see MailApi::updateMail()
     * @deprecated This method is no longer used and is not recommended.
     */
    protected function handleMail(ServiceBase $api, array $args)
    {
        LoggerManager::getLogger()->deprecated('MailApi::handleMail() has been deprecated.');

        // Perform Front End argument validation per the Mail API architecture
        // Non-compliant arguments will result in an Invalid Parameter Exception Thrown
        $this->validateArguments($args);

        $mailRecord = $this->initMailRecord($args);

        try {
            if ($args[self::STATUS] == 'ready') {
                $response = $mailRecord->send(); // send immediately
            } else {
                $response = $mailRecord->saveAsDraft(); // save as draft
            }
        } catch (MailerException $e) {
            $eMessage = $e->getUserFriendlyMessage();
            if (isset($GLOBALS['log'])) {
                $GLOBALS['log']->error("MailApi: Request Failed - Message: {$eMessage}");
            }
            throw new SugarApiExceptionRequestMethodFailure($eMessage, null, 'Emails');
        }

        return $response;
    }

    /**
     * This endpoint accepts an array of one or more recipients and tries to resolve unsupplied arguments.
     * EmailRecipientsService::lookup contains the lookup and resolution rules.
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @deprecated POST /Mail/recipients/lookup has been deprecated and will not be available after v11.
     */
    public function recipientLookup(ServiceBase $api, array $args)
    {
        LoggerManager::getLogger()->deprecated(
            'POST /Mail/recipients/lookup has been deprecated and will not be available after v11.'
        );

        $recipients = $args;
        unset($recipients['__sugar_url']);

        $emailRecipientsService = $this->getEmailRecipientsService();

        $result = [];
        foreach ($recipients as $recipient) {
            $result[] = $emailRecipientsService->lookup($recipient);
        }

        return $result;
    }

    /**
     * Finds recipients that match the search term.
     *
     * Arguments:
     *    q           - search string
     *    module_list -  one of the keys from $modules
     *    order_by    -  columns to sort by (one or more of $sortableColumns) with direction
     *                   ex.: name:asc,id:desc (will sort by last_name ASC and then id DESC)
     *    offset      -  offset of first record to return
     *    max_num     -  maximum records to return
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function findRecipients(ServiceBase $api, array $args)
    {
        if (ini_get('max_execution_time') > 0 && ini_get('max_execution_time') < 300) {
            ini_set('max_execution_time', 300);
        }
        $term = (isset($args['q'])) ? trim($args['q']) : '';
        $offset = 0;
        $limit = (!empty($args['max_num'])) ? (int)$args['max_num'] : 20;
        $orderBy = [];

        if (!empty($args['offset'])) {
            if ($args['offset'] === 'end') {
                $offset = 'end';
            } else {
                $offset = (int)$args['offset'];
            }
        }

        $modules = [
            'users' => 'users',
            'accounts' => 'accounts',
            'contacts' => 'contacts',
            'leads' => 'leads',
            'prospects' => 'prospects',
            'all' => 'LBL_DROPDOWN_LIST_ALL',
        ];
        $module = $modules['all'];

        if (!empty($args['module_list'])) {
            $moduleList = strtolower($args['module_list']);

            if (array_key_exists($moduleList, $modules)) {
                $module = $modules[$moduleList];
            }
        }

        if (!empty($args['order_by'])) {
            $orderBys = explode(',', $args['order_by']);

            foreach ($orderBys as $sortBy) {
                $column = $sortBy;
                $direction = 'ASC';

                if (strpos($sortBy, ':')) {
                    // it has a :, it's specifying ASC / DESC
                    [$column, $direction] = explode(':', $sortBy);

                    if (strtolower($direction) == 'desc') {
                        $direction = 'DESC';
                    } else {
                        $direction = 'ASC';
                    }
                }

                // only add column once to the order-by clause
                if (empty($orderBy[$column])) {
                    $orderBy[$column] = $direction;
                }
            }
        }

        $records = [];
        $nextOffset = -1;

        if ($offset !== 'end') {
            $emailRecipientsService = $this->getEmailRecipientsService();
            $records = $emailRecipientsService->find($term, $module, $orderBy, $limit + 1, $offset);
            $totalRecords = safeCount($records);
            if ($totalRecords > $limit) {
                // means there are more records in DB than limit specified
                $nextOffset = $offset + $limit;
                array_pop($records);
            }

            $apiHelpers = [];
            $retrieveOptions = [];
            if (!empty($args['erased_fields'])) {
                $retrieveOptions = ['erased_fields' => true, 'encode' => false, 'use_cache' => false];
            }
            foreach ($records as $idx => $record) {
                $bean = $this->getBeanFromServiceRecord($record, $retrieveOptions);
                if (!isset($apiHelpers[$record['_module']])) {
                    $apiHelpers[$record['_module']] = ApiHelper::getHelper($api, $bean);
                }
                if (isset($bean->erased_fields)) {
                    $records[$idx]['_erased_fields'] = $bean->erased_fields;
                }
                $records[$idx]['_acl'] = $apiHelpers[$record['_module']]->getBeanAcl($bean, array_keys($record));
            }
        }

        return [
            'next_offset' => $nextOffset,
            'records' => $records,
        ];
    }

    /**
     * Gets a bean from a record provided by the EmailRecipientsService find()
     * method. Gets the bean using list view permissions.
     *
     * @param array $record
     * @param array $retrieveOptions
     *
     * @returns SugarBean
     */
    protected function getBeanFromServiceRecord(array $record, array $retrieveOptions = []): SugarBean
    {
        $seed = BeanFactory::newBean($record['_module']);
        if (!$seed->ACLAccess('list')) {
            throw new SugarApiExceptionNotAuthorized('No access to view records for module: ' . $record['_module']);
        }
        $q = new SugarQuery(DBManagerFactory::getInstance('listviews'));
        $q->from($seed, $retrieveOptions);
        $q->where()->equals('id', $record['id']);
        $beans = $seed->fetchFromQuery($q);

        $bean = array_shift($beans);
        if (is_null($bean)) {
            throw new SugarApiExceptionError('Record not found in fetch: ' . $record['_module'] . ':' . $record['id']);
        }
        return $bean;
    }

    /**
     * Perform Audit Validation on Input Arguments and normalize
     *
     * @param array $args
     * @see MailApi::archiveMail()
     * @see MailApi::handleMail()
     * @deprecated This method is no longer used and is not recommended.
     */
    public function validateArguments(array &$args)
    {
        LoggerManager::getLogger()->deprecated('MailApi::validateArguments() has been deprecated.');

        $bean = BeanFactory::getBean('Emails');
        $relatedToOptions = $bean->field_defs['parent_name']['options'];
        $relatedToModules = array_keys($GLOBALS['app_list_strings'][$relatedToOptions]);

        /*--- Validate status value ---*/
        if (empty($args[self::STATUS]) || !in_array($args[self::STATUS], self::$apiStatusValues)) {
            $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_VALUE', [self::STATUS]);
        }

        /*--- Validate Mail Configuration ---*/
        if ($args[self::STATUS] === 'ready' && empty($args[self::EMAIL_CONFIG])) {
            $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_VALUE', [self::EMAIL_CONFIG]);
        }

        /*--- Validate FROM_ADDRESS if 'archive' ---*/
        if ($args[self::STATUS] === 'archive') {
            if (empty($args[self::FROM_ADDRESS]) || !is_string($args[self::FROM_ADDRESS])) {
                $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_VALUE', [self::FROM_ADDRESS]);
            }
            $fromAddress = empty($args[self::FROM_ADDRESS]) ? '' : trim($args[self::FROM_ADDRESS]);
            if (empty($fromAddress)) {
                $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_VALUE', [self::FROM_ADDRESS]);
            }
        }

        /*--- Validate DATE_SENT if 'archive' ---*/
        if ($args[self::STATUS] === 'archive') {
            if (empty($args[self::DATE_SENT]) || !is_string($args[self::DATE_SENT])) {
                $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_VALUE', [self::DATE_SENT]);
            }
            $dateSent = empty($args[self::DATE_SENT]) ? '' : trim($args[self::DATE_SENT]);
            if (empty($dateSent)) {
                $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_VALUE', [self::DATE_SENT]);
            }
        }

        /*--- Validate ASSIGNED_USER_ID if 'archive' - Argument is Optional - so can be empty string ---*/
        if ($args[self::STATUS] === 'archive') {
            if (isset($args[self::ASSIGNED_USER_ID]) && !is_string($args[self::ASSIGNED_USER_ID])) {
                $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_FORMAT', [self::ASSIGNED_USER_ID]);
            }
        }

        /*--- Validate TO Recipients ---*/
        $isRequired = $args[self::STATUS] === 'archive' ? true : false;
        $this->validateRecipients($args, self::TO_ADDRESSES, $isRequired);

        /*--- Validate CC Recipients ---*/
        $this->validateRecipients($args, self::CC_ADDRESSES);

        /*--- Validate BCC Recipients ---*/
        $this->validateRecipients($args, self::BCC_ADDRESSES);

        /*--- Validate Attachments ---*/
        if (isset($args[self::ATTACHMENTS])) {
            if (!is_array($args[self::ATTACHMENTS])) {
                $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_FORMAT', [self::ATTACHMENTS]);
            }
            foreach ($args[self::ATTACHMENTS] as $attachment) {
                if (!is_array($attachment)) {
                    $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_FORMAT', [self::ATTACHMENTS]);
                }
                if (empty($attachment['type']) || !in_array($attachment['type'], self::$apiAttachmentTypes)) {
                    $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_FIELD', [self::ATTACHMENTS, 'type']);
                }
                if (empty($attachment['id']) || !is_string($attachment['id'])) {
                    $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_FIELD', [self::ATTACHMENTS, 'id']);
                }
                if ($attachment['type'] == 'upload' && empty($attachment['name'])) {
                    $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_FIELD', [self::ATTACHMENTS, 'name']);
                }
            }
        }

        /*--- Validate Teams ---*/
        if (isset($args[self::TEAMS])) {
            if (!is_array($args[self::TEAMS])) {
                $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_FORMAT', [self::TEAMS]);
            }
            /* Primary is REQUIRED if Teams supplied */
            if (!isset($args[self::TEAMS]['primary']) || !is_string(
                $args[self::TEAMS]['primary']
            ) || empty($args[self::TEAMS]['primary'])
            ) {
                $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_FIELD', [self::TEAMS, 'primary']);
            }
            if (isset($args[self::TEAMS]['others'])) {
                if (!is_array($args[self::TEAMS]['others'])) {
                    $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_FIELD', [self::TEAMS, 'others']);
                }
                foreach ($args[self::TEAMS]['others'] as $otherTeam) {
                    if (!is_string($otherTeam) || empty($otherTeam)) {
                        $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_FIELD', [self::TEAMS, 'others']);
                    }
                }
            }
        }

        /*--- Validate Related ---*/
        if (isset($args[self::RELATED])) {
            if (!is_array($args[self::RELATED])) {
                $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_FORMAT', [self::RELATED]);
            }
            if (!empty($args[self::RELATED])) {
                if (empty($args[self::RELATED]['id']) || !is_string($args[self::RELATED]['id'])) {
                    $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_FIELD', [self::RELATED, 'id']);
                }
                if (empty($args[self::RELATED]['type']) || !is_string($args[self::RELATED]['type'])) {
                    $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_FIELD', [self::RELATED, 'type']);
                }
                if (!in_array($args[self::RELATED]['type'], $relatedToModules)) {
                    $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_FIELD', [self::RELATED, 'type']);
                }
            }
        }

        /*--- Validate Subject ---*/
        if (isset($args[self::SUBJECT]) && !is_string($args[self::SUBJECT])) {
            $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_FORMAT', [self::SUBJECT]);
        }

        if ($args[self::STATUS] === 'archive') {
            $subject = empty($args[self::SUBJECT]) ? '' : trim($args[self::SUBJECT]);
            if (empty($subject)) {
                $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_VALUE', [self::SUBJECT]);
            }
        }

        /*--- Validate html_body ---*/
        if (isset($args[self::HTML_BODY]) && !is_string($args[self::HTML_BODY])) {
            $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_FORMAT', [self::HTML_BODY]);
        }

        /*--- Validate text_body ---*/
        if (isset($args[self::TEXT_BODY]) && !is_string($args[self::TEXT_BODY])) {
            $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_FORMAT', [self::TEXT_BODY]);
        }

        /*--- Initialize any Unprovided Arguments to their Defaults ---*/
        foreach (self::$fields as $k => $v) {
            if (!isset($args[$k])) {
                $args[$k] = $v;
            }
        }

        /*--- If Sending Mail, make sure there is at least One Recipient specified --*/
        if (($args[self::STATUS] !== 'draft') &&
            empty($args[self::TO_ADDRESSES]) &&
            empty($args[self::CC_ADDRESSES]) &&
            empty($args[self::BCC_ADDRESSES])
        ) {
            $this->invalidParameter('LBL_MAILAPI_NO_RECIPIENTS');
        }
    }

    /**
     * Validate Recipient List
     *
     * @param array $args
     * @param string $argName
     * @param bool $isRequired
     * @see MailApi::validateArguments()
     * @deprecated This method is no longer used and is not recommended.
     */
    protected function validateRecipients(array $args, $argName, $isRequired = false)
    {
        LoggerManager::getLogger()->deprecated('MailApi::validateRecipients() has been deprecated.');

        $recipientCount = 0;
        if (isset($args[$argName])) {
            if (!is_array($args[$argName])) {
                $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_FORMAT', [$argName]);
            }
            foreach ($args[$argName] as $recipient) {
                if (!is_array($recipient)) {
                    $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_FORMAT', [$argName]);
                }
                if (empty($recipient['email'])) {
                    $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_FIELD', [$argName, 'email']);
                }
                if (!is_string($recipient['email'])) {
                    $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_FIELD', [$argName, 'email']);
                }
                $recipientCount++;
            }
        }
        if ($isRequired && $recipientCount == 0) {
            $this->invalidParameter('LBL_MAILAPI_INVALID_ARGUMENT_VALUE', [$argName]);
        }
    }

    /**
     * Log Audit Errors and Throw Appropriate Exception
     *
     * @param string $message
     * @param null|array $msgArgs
     * @throws SugarApiExceptionInvalidParameter
     * @deprecated This method is no longer used and is not recommended.
     * @see MailApi::validateArguments()
     * @see MailApi::validateRecipients()
     */
    protected function invalidParameter($message, $msgArgs = null)
    {
        LoggerManager::getLogger()->deprecated('MailApi::invalidParameter() has been deprecated.');

        throw new SugarApiExceptionInvalidParameter($message, $msgArgs, 'Emails');
    }

    /**
     * Instantiate and initialize the MaiRecord from the incoming api arguments
     *
     * @param array $args
     * @return MailRecord
     * @see MailApi::handleMail()
     * @deprecated This method is no longer used and is not recommended.
     * @see MailApi::archiveMail()
     */
    protected function initMailRecord(array $args)
    {
        LoggerManager::getLogger()->deprecated('MailApi::initMailRecord() has been deprecated.');

        $mailRecord = new MailRecord();
        $mailRecord->mailConfig = $args[self::EMAIL_CONFIG];
        $mailRecord->toAddresses = $args[self::TO_ADDRESSES];
        $mailRecord->ccAddresses = $args[self::CC_ADDRESSES];
        $mailRecord->bccAddresses = $args[self::BCC_ADDRESSES];
        $mailRecord->attachments = $args[self::ATTACHMENTS];
        $mailRecord->teams = $args[self::TEAMS];
        $mailRecord->related = $args[self::RELATED];
        $mailRecord->subject = $args[self::SUBJECT];
        $mailRecord->html_body = $args[self::HTML_BODY];
        $mailRecord->text_body = $args[self::TEXT_BODY];
        $mailRecord->fromAddress = $args[self::FROM_ADDRESS];
        $mailRecord->assigned_user_id = $args[self::ASSIGNED_USER_ID];

        if (!empty($args[self::DATE_SENT])) {
            $date = TimeDate::getInstance()->fromIso($args[self::DATE_SENT]);
            $mailRecord->date_sent = $date->asDb();
        }

        return $mailRecord;
    }

    /**
     * Validates email addresses. The return value is an array of key-value pairs where the keys are the email
     * addresses and the values are booleans indicating whether or not the email address is valid.
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiException
     * @deprecated POST /Mail/address/validate has been deprecated and will not be available after v11.
     */
    public function validateEmailAddresses(ServiceBase $api, array $args)
    {
        LoggerManager::getLogger()->deprecated(
            'POST /Mail/address/validate has been deprecated and will not be available after v11.'
        );

        $validatedEmailAddresses = [];
        unset($args['__sugar_url']);
        if (!is_array($args)) {
            throw new SugarApiExceptionInvalidParameter('Invalid argument: cannot validate');
        }
        if (empty($args)) {
            throw new SugarApiExceptionMissingParameter('Missing email address(es) to validate');
        }
        $emailAddresses = $args;
        foreach ($emailAddresses as $emailAddress) {
            $validatedEmailAddresses[$emailAddress] = SugarEmailAddress::isValidEmail($emailAddress);
        }
        return $validatedEmailAddresses;
    }

    /**
     * @return EmailRecipientsService
     * @see MailApi::findRecipients()
     * @see MailApi::recipientLookup()
     */
    protected function getEmailRecipientsService()
    {
        if (!($this->emailRecipientsService instanceof EmailRecipientsService)) {
            $this->emailRecipientsService = new EmailRecipientsService();
        }

        return $this->emailRecipientsService;
    }

    /**
     * Saves an email attachment using the POST method
     *
     * @param ServiceBase $api The service base
     * @param array $args Arguments array built by the service base
     * @return array metadata about the attachment including name, guid, and nameForDisplay
     * @deprecated POST /Mail/attachment has been deprecated and will not be available after v11. Use POST
     * /Notes/temp/file/filename to upload an attachment and POST /Emails/:record/link/attachments to link it to an
     * email.
     */
    public function saveAttachment(ServiceBase $api, array $args)
    {
        LoggerManager::getLogger()->deprecated(
            'POST /Mail/attachment has been deprecated and will not be available after v11. Use POST ' .
            '/Notes/temp/file/filename to upload an attachment and POST /Emails/:record/link/attachments to link it ' .
            'to an email.'
        );

        $this->checkPostRequestBody();
        $email = $this->getEmailBean();
        $email->email2init();
        $metadata = $email->email2saveAttachment();
        return $metadata;
    }

    /**
     * Removes an email attachment
     *
     * @param ServiceBase $api The service base
     * @param array $args The request args
     * @return bool
     * @throws SugarApiExceptionRequestMethodFailure
     * @deprecated DELETE /Mail/attachment/:id has been deprecated and will not be available after v11. Use DELETE
     * /Notes/:record/file/filename to delete an uploaded file from the filesystem. Use DELETE
     * /Emails/:record/link/attachments/:remote_id to remove an attachment from an email. Note that removing an
     * attachment from an email will also delete it from the filesystem.
     */
    public function removeAttachment(ServiceBase $api, array $args)
    {
        LoggerManager::getLogger()->deprecated(
            'DELETE /Mail/attachment/:id has been deprecated and will not be available after v11. Use DELETE ' .
            '/Notes/:id/file/filename to delete a file from the filesystem. Use DELETE ' .
            '/Emails/:id/link/attachments/:remote_id to remove an attachment from an email. Note that removing an ' .
            'attachment from an email will also delete it from the filesystem.'
        );

        $email = $this->getEmailBean();
        $email->email2init();
        $fileGUID = $args['file_guid'];
        $fileName = $email->et->userCacheDir . '/' . $fileGUID;
        $filePath = clean_path($fileName);
        $fileConstraint = new File([
            'baseDirs' => [realpath($email->et->userCacheDir)],
        ]);
        $violations = Validator::getService()->validate($filePath, $fileConstraint);
        if ($violations->count()) {
            throw new ViolationException('Invalid mail attachment file path', $violations);
        }

        unlink($filePath);
        return true;
    }

    /**
     * Returns a new Email bean, used for testing purposes
     *
     * @return Email
     * @see MailApi::saveAttachment()
     * @see MailApi::removeAttachment()
     * @deprecated This method is no longer used and is not recommended.
     */
    protected function getEmailBean()
    {
        return new Email();
    }
}
