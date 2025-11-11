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

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\NodeVisitor\ParentConnectingVisitor;
use PhpParser\ParserFactory;
use Sugarcrm\Sugarcrm\modules\HealthCheck\Scanner\Checks\Dbal as DbalHealthCheck;
use Sugarcrm\Sugarcrm\modules\HealthCheck\Scanner\Checks\PasswordHashAlgo as PasswordHashAlgoCheck;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\ForbiddenStatementVisitor;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\MethodSignatureVisitor;

require_once __DIR__ . '/ScannerMeta.php';

/**
 *
 * HealthCheck Scanner
 *
 */
class HealthCheckScanner
{
    /**
     * @var array<string, mixed>|mixed|mixed[]|array<class-string<\HealthCheck>, mixed>
     */
    public $beanList;
    /**
     * @var array<string, mixed>|mixed|mixed[]
     */
    public $objectList;
    /**
     * @var mixed[]
     */
    public $bwcModulesHash;
    /**
     * @var mixed[]
     */
    public $beanFiles;
    public const VERSION_FILE = 'version.json';

    // failure status
    public const FAIL = 99;

    /**
     * Constant for failure of unserialization because of data issues
     */
    public const UNSERIALIZE_FAIL_DATA = 1;

    /**
     * Constant for failure of unserialization because of object or class references
     */
    public const UNSERIALIZE_FAIL_OBJECTS = 2;

    /**
     *
     * @var HealthCheckScannerMeta
     */
    protected $meta;

    /**
     *
     * @var string Directory of the instance to scan
     */
    protected $instance;

    /**
     *
     * DB connection to Sugar
     * @var DBManager
     */
    protected $db;

    /**
     *
     * @var string Log filename
     */
    protected $logfile;

    /**
     *
     * @var integer Verbose log level (0, 1, 2)
     */
    protected $verbose = 0;

    /**
     *
     * @var integer Exit status (FIXME: do we need this here ?)
     */
    protected $exit_status = 0;

    /**
     *
     * @var string FIXME: input Zac
     */
    protected $ping_url = 'http://sortinghat-sugarcrm.rhcloud.com/feedback';

    /**
     * List of packages with compatible versions to check. This is driven by the
     * package-checklist.php contents located in the same directory as this file.
     *
     * @var array
     */
    protected $packages = [];

    /**
     * @var array List of modules which excluded from table check.
     */
    protected $excludeModules = [
        'Audit',
        'Connectors',
        'DynamicFields',
        'MergeRecords',
    ];

    /**
     * @var array List of unsupported modules.
     */
    protected $unsupportedModules = [
        'Feeds',
        'iFrames',
    ];

    /**
     *
     * Instance status (bucket)
     * @var string
     */
    protected $status = HealthCheckScannerMeta::VANILLA;

    /**
     *
     * @var int
     */
    protected $flag = HealthCheckScannerMeta::FLAG_GREEN;

    /**
     *
     * @var array
     */
    protected $status_log = [];

    /**
     * @var resource
     */
    protected $fp;

    /**
     * metadata log
     *
     * @var array
     */
    protected $logMeta = [];

    /**
     * Health Check module properties
     *
     * @var array
     */
    protected $healthCheckModule = [
        'bean' => 'HealthCheck',
        'file' => 'modules/HealthCheck/HealthCheck.php',
        'md5' => './modules/HealthCheck/HealthCheck.php',
    ];


    /**
     * Ignored files
     *
     * @var array
     */
    protected $ignoredFiles = [
        'custom/Extension/modules/Administration/Ext/Administration/upgrader2.php',
        'custom/Extension/modules/Administration/Ext/Administration/healthcheck.php',
    ];

    /**
     * Array of files which will not be scanned for output
     * @var array
     */
    protected $ignoreOutputCheckFiles = [
        'modules/Connectors/connectors/sources/ext/rest/insideview/InsideViewLogicHook.php',
        'modules/Connectors/connectors/sources/ext/rest/inbox25/InboxViewLogicHook.php',
    ];

    /**
     * Array of files which will be ignored if missing in 7.x
     * @var array
     */
    protected $ignoreMissingCustomFiles = [
        'modules/Connectors/connectors/sources/ext/rest/insideview/InsideViewLogicHook.php',
        'modules/Connectors/connectors/sources/ext/rest/inbox25/InboxViewLogicHook.php',
        'modules/Contacts/SugarFeeds/ContactFeed.php',
        'modules/Leads/SugarFeeds/LeadFeed.php',
        'modules/SugarFeed/SugarFeed.php',
        'modules/Cases/SugarFeeds/CaseFeed.php',
        'modules/Opportunities/SugarFeeds/OppFeed.php',
    ];

    protected $ignoredTplPatterns = [
        '~/LinkedinParserSettings\.tpl$~i',
        '~/PipelineByObjectivesConfigure\.tpl$~i',
        '~/zendeskTickets/.*\.tpl$~i',
        '~/zendesk-dashlet/.*\.tpl$~i',
        '~/Forecasts/Chart\.tpl$~i',
        '~/modules/Relationships/editFields\.tpl$~i',
        '~/swagger/settings/settings\.tpl$~i',
        '~Address/ro_RO\.EditView\.tpl$~i',
        '~Address/sk_SK\.EditView\.tpl$~i',
    ];

    /**
     * If Scanner founds some number of files and is going to report them, it's better to report them in bunches.
     * This field defines an appropriate bunch size.
     * @see CRYS-554
     *
     * @var int
     */
    protected $numberOfFilesToReport = 5;

    /**
     * Upgrader object
     * @var UpgradeDriver
     */
    protected $upgrader = null;


    /**
     * Dirs that are moved to vendor
     * @var array
     */
    protected $removed_directories = [
        'include/HTMLPurifier',
        'include/HTTP_WebDAV_Server',
        'include/Smarty',
        'XTemplate',
        'Zend',
        'include/lessphp',
        'include/oauth2-php',
        'include/tcpdf',
        'include/ytree',
        'include/SugarSearchEngine/Elastic/Elastica',
    ];
    /**
     * dirs or files that have been deleted
     * @var array
     */
    protected $removed_files = [
        'include/Smarty/plugins/function.sugar_help.php',
        // Removed in Sugar 7.8
        'modules/pmse_Inbox/engine/Crypt.php',
        'modules/pmse_Inbox/engine/PMSEAccessManager.php',
        'modules/pmse_Inbox/engine/PMSELicenseManager.php',
        'modules/pmse_Project/pmse_BpmAccessManagement',
        'modules/Notifications/controller.php',
    ];

    /**
     * Specific files that should be excluded from SH include check
     * @var array
     */
    protected $specificSugarFiles = [
        'include/Smarty/plugins/function.sugar_action_menu.php',
    ];

    protected $excludedScanDirectories = [
        'backup',
        'tmp',
        'temp',
    ];
    protected $filesToFix = [];

    protected $specificSugarFilesToFix = [];

    protected $sessionUsages = [];

    protected $deletedFilesReferenced = [];

    /**
     * regex'es for removed code
     * @var array
     */
    protected $deprecatedPHPCodePatterns = [
        '/[^\w]SugarSession[^\w]/i' => 'deprecatedCodeSugarSession', //report id
        '/[^\w]SugarAuthenticate[^\w]/i' => 'deprecatedAuthN',
        '/[^\w]SugarAuthenticateUser[^\w]/i' => 'deprecatedAuthN',
        '/[^\w]LDAPAuthenticate[^\w]/i' => 'deprecatedAuthN',
        '/[^\w]LDAPAuthenticateUser[^\w]/i' => 'deprecatedAuthN',
        '/[^\w]SAMLAuthenticate[^\w]/i' => 'deprecatedAuthN',
        '/[^\w]SAMLAuthenticateUser[^\w]/i' => 'deprecatedAuthN',
        '/[^\w]SAMLRequestRegistry[^\w]/i' => 'deprecatedAuthN',
        '/\bMonolog\\\\Handler\\\\(HipChatHandler|SlackbotHandler|RavenHandler)\b/i' => 'incompatibleMonologCustomization',
        '/\bZend_Mime(\b|_Exception|_Decode|_Message|_Part)/i' => 'removedZendComponent',
        '/\b(SugarUploadS3|Zend_Service_|Zend_Gdata\b|Zend_Gdata_|Zend_Version)/i' => 'deprecatedZendComponent',
    ];

    protected $deprecatedJsAPIPatterns = [
        // Removed in 7.8
        '/(_\.bindAll)\s*\([\s\w]+[^,][\s\w]+\)/U' => 'useOfUnderscoreBindAll',
        '/\.(setDefaultAttributes)\W/' => 'removedSidecarAPI_Bean',
        '/\.([gs]etDefaultAttribute)\W/' => 'removedSidecarAPI_Bean_fixable',
        '/\.(removeDefaultAttribute)\W/' => 'removedSidecarAPI_Bean',
        '/([Aa]pp\.date\.(compareDates|isDate(After|Before|On|Between)))\s*\(/' => 'removedSidecarAPI_app_date',
        // Removed in 7.9
        '/([Aa]pp\.view\.invokeParent)/' => 'useOfAppViewInvokeParent',
        // Removed in 7.10
        '/(resetLoadFlag\((true|false))/' => 'removedSidecarAPI_Context',
        '/(resetLoadFlag\.call\(\w+,\s?(true|false))/' => 'removedSidecarAPI_Context',
        '/(resetLoadFlag\.apply\(\w+,\s?\[(true|false))/' => 'removedSidecarAPI_Context',
        '/(metadata\.getField\([\'"])/' => 'useOfMetadataGetFieldOldSignature',
        // Deprecated in 7.10
        '/\.(initButtons)/' => 'useOfInitButtons',
    ];

    protected $deprecatedHBSPatterns = [];

    // Deprecated patterns that could be in PHP, JS, or HBS files
    protected $deprecatedGenericPatterns = [
        '/(([\'"\s\.]|\b)fa-\w*?[\'"\s\.])/' => 'deprecatedFontAwesomeIcons',
    ];

    // Deprecated color variables found in LESS files
    protected $deprecatedLESSColorVariables = [
        "/[^\w]@grayDarker[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@grayDark[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@grayDarkish[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@gray[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@grayLight[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@grayLightish[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@grayMid[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@grayLighter[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@grayActive[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@slate[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@steel[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@rock[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@stone[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@lime[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@night[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@darkIndigo[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@indigo[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@lightIndigo[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@btnBackgroundHighlight[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@commentLogDashletTimeColor[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@commentLogDashletNoCommentsAvailable[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@commentLogDashletItemHeaderColor[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@dashletTabBorderColor[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@hrBorder[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@linkMore[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@loadingColor[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@navbarInverseBackground[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@navbarInverseBackgroundHighlight[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@navbarInverseBorder[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@navbarInverseText[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@navbarInverseLinkColor[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@navbarInverseLinkColorHover[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@navbarInverseLinkColorActive[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@navbarInverseLinkBackgroundHover[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@navbarInverseLinkBackgroundActive[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@navbarInverseSearchBackground[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@navbarInverseSearchBackgroundFocus[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@navbarInverseSearchBorder[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@navbarInverseBrandColor[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@panelBorderColor[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@panelInactiveHoverColor[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@previewDataBackground[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@previewFieldSeparator[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@previewHeaderBackground[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@select2LinkColorHover[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@tabBackgroundActive[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@tabBackgroundInactive[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@tableBorderColor[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@tableBorder[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@tableHeader[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@tableRowHighlighted[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@tableHeaderHighlighted[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@tableTbodyRowBorderColor[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@tableTheadRowBorderColor[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@tableFixedColumnsBorderColor[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@errorAlertText[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@infoAlertText[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@pendingAlertText[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@successAlertText[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@warningAlertText[^\w]/" => 'deprecatedLESSColorVariables',
        "/[^\w]@wellBackground[^\w]/" => 'deprecatedLESSColorVariables',
    ];

    //Removed in 7.8
    protected $removedSidecarFiles = [
        'clients/base/views/news',
        'clients/base/layouts/subpanels-create/subpanels-create',
        'clients/base/views/history-summary-preview-header',
        'clients/base/views/passwordmodal',
        'clients/portal/views/passwordmodal',
        'modules/ACLRoles/clients/base/layouts/records',
        'modules/Administration/clients/base/layouts/records',
        'modules/Calendar/clients/base/layouts/records',
        'modules/Campaigns/clients/base/layouts/records',
        'modules/Contracts/clients/base/layouts/records',
        'modules/ContractTypes/clients/base/layouts/records',
        'modules/KBContents/clients/base/views/prefilteredlist',
        'modules/Quotes/clients/base/views/panel-top-for-accounts',
        'modules/RevenueLineItems/clients/base/views/subpanel-list-with-massupdate',
    ];

    //Removed in 7.8
    protected $removedSidecarClasses = [
        'NewsView',
        'SubpanelsCreateLayout',
        'HistorySummaryPreviewHeaderView',
        'PasswordmodalView',
        'PortalPasswordmodalView',
        'ACLRolesRecordsLayout',
        'AdministrationRecordsLayout',
        'CalendarRecordsLayout',
        'CampaignsRecordsLayout',
        'ContractsRecordsLayout',
        'ContractTypesRecordsLayout',
        'KBContentsPrefilteredlistView',
        'QuotesPanelTopForAccountsView',
        'RevenueLineItemsSubpanelListWithMassupdateView',
    ];

    protected $filesWithDeprecatedCode = [];

    protected $filesWithIncompatibleInheritance = [];

    protected $filesWithOutdatedDbal = [];

    /**
     * Array of warnings per upgrade method that is used for tracking possible
     * failures
     * @var array
     */
    protected $unserializeFailureWarnings = [];

    /**
     * Listing of unserialization failure warnings
     * @var array
     */
    protected $unserializeFailureReasons = [
        self::UNSERIALIZE_FAIL_DATA => 'LBL_PA_UNSERIALIZE_DATA_FAILURE',
        self::UNSERIALIZE_FAIL_OBJECTS => 'LBL_PA_UNSERIALIZE_OBJECT_FAILURE',
    ];

    /**
     * Listing of methods that are run during the PA unserialize upgrade. At a
     * minimum this requires a task name that maps to a table name and columns.
     * @var array
     */
    protected $unserializeTasks = [
        'lockedVariables' => [
            'table' => 'pmse_bpm_process_definition',
            'cols' => ['pro_locked_variables'],
        ],
        'dynamicFormTable' => [
            'table' => 'pmse_bpm_dynamic_forms',
            'cols' => ['dyn_view_defs'],
            'functions' => ['base64_decode'],
            'decode' => false,
        ],
        'bpmFlowTable' => [
            'table' => 'pmse_bpm_flow',
            'cols' => ['cas_adhoc_actions'],
        ],
    ];

    /**
     * Methods to run as part of the SugarBPM invalid field check
     * @var array
     */
    protected $invalidFieldUseMethods = [
        'checkActionsForInvalidFields',
        'checkBusinesRulesForInvalidFields',
    ];

    /**
     * List of fields blacklisted for SugarBPM as of 7.6.2
     * @var array
     */
    protected $blacklistedPAFields = [
        'ALL' => [
            'deleted',
            'mkto_id',
            'parent_type',
            'user_name',
            'user_hash',
            'portal_app',
            'portal_active',
            'portal_name',
            'password',
            'is_admin',
        ],
        'BR' => [
            'duration_hours',
            'duration_minutes',
            'repeat_type',
            'created_by',
            'modified_user_id',
            'date_entered',
            'date_modified',
        ],
        'BRR' => [],
        'CF' => [
            'created_by',
            'modified_user_id',
            'date_entered',
            'date_modified',
        ],
    ];

    /**
     * PA special fields
     * @var array
     */
    protected $whitelistedPAFields = [
        'ALL' => ['created_by', 'modified_user_id'],
        'BR' => ['assigned_user_id', 'email1', 'outlook_id'],
        'BRR' => ['assigned_user_id', 'email1', 'outlook_id'],
        'ET' => ['email1'],
        'AC' => ['assigned_user_id', 'likely_case', 'worst_case', 'best_case', 'teams'],
        'CF' => ['assigned_user_id', 'likely_case', 'worst_case', 'best_case', 'teams'],
        'RR' => [],
    ];

    /**
     * List of field types that are blacklisted throughout SugarBPM
     * @var array
     */
    protected $blacklistedPAFieldTypes = ['image', 'password', 'file'];

    /**
     * List of validation types needed when checking SugarBPM fields
     * @var array
     */
    protected $processFieldValidationTypes = [
        'ADD_RELATED_RECORD' => 'AC',
        'CHANGE_FIELD' => 'CF',
        'BUSINESS_RULE' => 'BR',
    ];

    /**
     * List of business rule validation types
     * @var array
     */
    protected $businessRuleTypes = [
        'BRR' => 'conditions',
        'BR' => 'conclusions',
    ];

    /**
     * Stack of invalid SugarBPM fields used in context, and their counts
     * @var array
     */
    protected $invalidPAFields = [];

    /**
     * @var array
     */
    protected $md5_files = [];

    /**
     * @return string[]
     */
    protected $inheritanceRules = [
        \Sugarcrm\Sugarcrm\Logger\Formatter::class => ['format' => ['return' => 'string'], 'formatBatch' => ['return' => 'string']],
        \Sugarcrm\Sugarcrm\Logger\Formatter\BackwardCompatibleFormatter::class => ['format' => ['return' => 'string'], 'formatBatch' => ['return' => 'string']],
        \Monolog\Formatter\FlowdockFormatter::class => ['format' => ['return' => 'array'], 'formatBatch' => ['return' => 'array']],
        \Monolog\Formatter\FluentdFormatter::class => ['format' => ['return' => 'string'], 'formatBatch' => ['return' => 'string']],
        \Monolog\Formatter\GelfMessageFormatter::class => ['format' => ['return' => 'Gelf\Message'], 'formatBatch' => ['return' => 'Gelf\Message']],
        \Monolog\Formatter\HtmlFormatter::class => ['format' => ['return' => 'string'], 'formatBatch' => ['return' => 'string']],
        \Monolog\Formatter\JsonFormatter::class => ['format' => ['return' => 'string'], 'formatBatch' => ['return' => 'string']],
        \Monolog\Formatter\LineFormatter::class => ['format' => ['return' => 'string'], 'formatBatch' => ['return' => 'string']],
        \Monolog\Formatter\LogglyFormatter::class => ['format' => ['return' => 'string'], 'formatBatch' => ['return' => 'string']],
        \Monolog\Formatter\LogstashFormatter::class => ['format' => ['return' => 'string'], 'formatBatch' => ['return' => 'string']],
        \Monolog\Formatter\MongoDBFormatter::class => ['format' => ['return' => 'array'], 'formatBatch' => ['return' => 'array']],
        \Monolog\Formatter\ScalarFormatter::class => ['format' => ['return' => 'array'], 'formatBatch' => ['return' => 'array']],
        \Monolog\Formatter\WildfireFormatter::class => ['format' => ['return' => 'string'], 'formatBatch' => ['return' => 'string']],
    ];

    /**
     *
     * Ctor setup
     * @return void
     */
    public function __construct()
    {
        $this->meta = HealthCheckScannerMeta::getInstance();
        if (!class_exists('LoggerManager')) {
            $this->logfile = 'healthcheck-' . time() . '.log';
        }
    }

    public function getIgnoredFiles(): array
    {
        return $this->ignoredFiles;
    }

    public function getIgnoreOutputCheckFiles(): array
    {
        return $this->ignoreOutputCheckFiles;
    }

    /**
     * Set point to UpgradeDriver object
     * @param UpgradeDriver $upgrader
     */
    public function setUpgrader(UpgradeDriver $upgrader)
    {
        $this->upgrader = $upgrader;
    }

    /**
     *
     * Log message
     * @param string $msg Log message
     * @param string $tag Log level
     * @return string formatted log message
     */
    protected function log(string $msg, string $tag = 'INFO')
    {
        if (null === $this->logfile && empty($this->fp)) {
            $fmsg = sprintf('[Scanner] [%s] %s', $tag, $msg);
            $tagsToLevelMap = [
                'STATUS' => 'fatal',
                'ERROR' => 'error',
                'BUCKET' => 'warn',
                'INFO' => 'info',
            ];
            $level = $tagsToLevelMap[$tag] ?? 'info';
            LoggerManager::getLogger()->$level($fmsg);
        } else {
            $fmsg = sprintf("[%s] %s %s\n", date('c'), $tag, $msg);

            if (empty($this->fp)) {
                $this->fp = @fopen($this->logfile, 'a+');
            }
            if (empty($this->fp)) {
                throw new RuntimeException("Cannot open logfile: $this->logfile");
            }
            fwrite($this->fp, $fmsg);
        }
        return $fmsg;
    }

    /**
     *
     * Script failure
     * @param string $msg
     * @return false
     */
    public function fail($msg)
    {
        $this->exit_status = self::FAIL;
        $this->updateStatus('scriptFailure', $msg);
        $this->log($msg, 'ERROR');
        return false;
    }

    /**
     *
     * Add reason to stats log
     * @param integer $status Bucket code
     * @param integer $code Scan id
     * @param string $reason Reason log
     * @return void
     */
    protected function logReason($status, $code, $reason)
    {
        $this->status_log[$status][] = [
            'code' => $code,
            'reason' => $reason,
        ];
    }

    /**
     *
     * If current status is lower that this, raise it
     * @param id|string $id Scan id or report id
     * @param mixed
     */
    public function updateStatus()
    {
        $params = func_get_args();

        $id = array_shift($params);

        $scanMeta = $this->meta->getMetaFromReportId($id, $params);

        // load default failure if no metadata can be found for given $id
        if ($scanMeta === false) {
            $scanMeta = $this->meta->getMetaFromReportId('unknownFailure');
        }

        $status = $scanMeta['bucket'];
        $code = $scanMeta['id'];
        $report = $scanMeta['report'];
        $this->logMeta[] = $scanMeta;
        $issueNo = $this->safeCount($this->logMeta);

        $reason = "[Issue $issueNo][$report][$code][" . vsprintf($scanMeta['log'], $params) . ']';

        $this->log($reason, 'CHECK-' . $status);
        $this->logReason($status, $code, $reason);


        if ($status > $this->status) {
            $this->log("===> Status changed to $status", 'STATUS');
            $this->status = $status;
        }

        /*
         * Every scan code can have a separate flag apart from the actual
         * bucket. This has only meaning for the health check module.
         *
         * @see HealthCheckScannerMeta::$defaultFlagMap
         */
        if ($scanMeta['flag'] > $this->flag) {
            $this->flag = $scanMeta['flag'];
        }
    }

    /**
     * @return array
     */
    public function getLogMeta()
    {
        return $this->logMeta;
    }

    /**
     *
     * Setter logfile
     * @param string $fileName
     */
    public function setLogFile($fileName)
    {
        // function_exists check needed for upgrade path 11.0 -> 12.2, because the function does not exists in 11.0
        if (function_exists('check_file_name') && !check_file_name($fileName)) {
            throw new \Exception('Path traversal attack vector detected');
        }

        $this->logfile = $fileName;
    }

    /**
     *
     * Getter logfile
     * @return string
     */
    public function getLogFile()
    {
        return $this->logfile;
    }

    /**
     *
     * Setter fp
     * @param $fp
     */
    public function setLogFilePointer($fp)
    {
        $this->fp = $fp;
    }

    /**
     *
     * Check if flag is green
     * @return boolean
     */
    public function isFlagGreen()
    {
        return $this->flag == HealthCheckScannerMeta::FLAG_GREEN;
    }

    /**
     *
     * Check if flag is yello
     * @return boolean
     */
    public function isFlagYellow()
    {
        return $this->flag == HealthCheckScannerMeta::FLAG_YELLOW;
    }

    /**
     *
     * Check if flag is red
     * @return boolean
     */
    public function isFlagRed()
    {
        return $this->flag == HealthCheckScannerMeta::FLAG_RED;
    }

    /**
     *
     * Setter verbose level
     * @param integer $level
     */
    public function setVerboseLevel($level)
    {
        $this->verbose = $level;
    }


    /**
     *
     * Setter instance directory
     * @param string $directory
     */
    public function setInstanceDir($directory)
    {
        $this->instance = $directory;
    }

    /**
     *
     * Getter status (verdict)
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Getter verbose (output)
     * @return int
     */
    public function getVerbose()
    {
        return $this->verbose;
    }

    /**
     *
     * Getter flag
     * @return integer
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     *
     * Getter status_log
     * @return array
     */
    public function getStatusLog()
    {
        return $this->status_log;
    }

    /**
     * Method detects current version and flavor of installed SugarCRM and returns them
     *
     * @return array (version, flavor)
     */
    public function getVersionAndFlavor()
    {
        $sugar_version = '9.9.9';
        $sugar_flavor = 'unknown';
        include 'sugar_version.php';
        return [$sugar_version, $sugar_flavor];
    }

    /**
     * @return PackageManager
     */
    public function getPackageManager()
    {
        require_once 'ModuleInstall/PackageManager/PackageManager.php';
        return new PackageManager();
    }

    /**
     *
     */
    public function scan(): array
    {
        $flags = E_ALL & ~E_STRICT;
        if (defined('E_DEPRECATED')) {
            $flags = $flags & ~E_DEPRECATED;
        }
        set_error_handler([$this, 'scriptErrorHandler'], $flags);
        $upgraderVersionInfo = $this->getVersion();
        $this->log(vsprintf("HealthCheck v.%s (build %s) starting scanning $this->instance", $upgraderVersionInfo));
        if (!$this->init()) {
            return $this->logMeta;
        }

        [$sugar_version, $sugar_flavor] = $this->getVersionAndFlavor();
        $this->log("Instance version: $sugar_version");
        $this->log("Instance flavor: $sugar_flavor");

        try {
            if ($this->upgrader) {
                $manifest = $this->getPackageManifest();

                if (version_compare($upgraderVersionInfo[0], HealthCheckScannerMeta::ALLOWED_UPGRADER_VERSION, '<')) {
                    $this->updateStatus('unsupportedUpgrader');
                    $this->log(
                        'Unsupported Upgrader version. Please install the appropriate SugarUpgradeWizardPrereq package'
                    );
                    return $this->logMeta;
                }

                $manifestFlavor = !empty($manifest['flavor']) ? $manifest['flavor'] : '';
                $manifestVersion = $manifest['version'];
                if ((!version_compare($sugar_version, $manifestVersion) && !strcasecmp($sugar_flavor, $manifestFlavor)) ||
                    version_compare($sugar_version, $manifestVersion, '>')
                ) {
                    $this->updateStatus('alreadyUpgraded');
                    $this->log('Instance already upgraded to ' . $manifestVersion);
                    return $this->logMeta;
                }
            }

            if ($GLOBALS['sugar_config']['site_url']) {
                $this->ping(['instance' => $GLOBALS['sugar_config']['site_url'], 'version' => $sugar_version]);
            }

            $this->checkDbDriver();

            $this->listUpgrades();
            $this->checkPackages();
            $this->scanCustomDir();

            foreach ($this->getModuleList() as $module) {
                $this->log("Checking module $module");
                $this->scanModule($module);
            }

            //Now that we have catalogued all the bad files in custom, log them by category.
            $this->updateCustomDirScanStatus();

            // Check global hooks
            $this->log('Checking global hooks');
            $hook_files = [];
            $this->extractHooks('custom/modules/logic_hooks.php', $hook_files, true);
            $this->extractHooks('custom/application/Ext/LogicHooks/logichooks.ext.php', $hook_files, true);
            foreach ($hook_files as $hookname => $hooks) {
                foreach ($hooks as $hook_data) {
                    $this->log("Checking global hook $hookname:{$hook_data[1]}");
                    $this->checkFileForOutput($hook_data[2], $hook_data[3]);
                }
            }

            // Check the Elastic Search Customization
            $this->checkCustomElastic();

            if (version_compare($sugar_version, '11.2.0.0', '<')
                // This check uses older Smarty version which is not compatible with PHP8.2. It should be ok, because
                // upgrade paths available for Sugar older than 11.2 does not include PHP8.2 compatible Sugar versions
                && version_compare(PHP_VERSION, '8.2.0', '<')
            ) {
                $this->checkSmartyTemplatesSyntax();
            }

            $this->checkSmartyTemplatesModifierDeprecationErrors();

            // TODO: custom dashlets
            if ($GLOBALS['sugar_config']['site_url']) {
                $this->ping(['instance' => $GLOBALS['sugar_config']['site_url'], 'verdict' => $this->status]);
            }
        } catch (\Error $error) {
            $this->reportPhpError(E_ERROR, $error->getMessage(), $error->getFile(), $error->getLine());
        }

        $this->checkPasswordHashAlgo();

        $this->finishScan();
        return $this->logMeta;
    }

    protected function checkSmartyTemplatesSyntax()
    {
        if (!in_array('phar', stream_get_wrappers())) {
            stream_wrapper_restore('phar');
        }
        if (file_exists(dirname(__DIR__) . '/smarty.phar')) {
            require_once dirname(__DIR__) . '/smarty.phar';
        }
        if (!class_exists('Smarty3')) {
            return;
        }
        $sql = "SELECT * FROM pdfmanager WHERE deleted='0'";
        $converter = new \SmartyConverter();
        $converter::muteExpectedErrors();
        $templates = DBManagerFactory::getConnection()->fetchAllAssociative($sql);
        $dbErrors = [];
        foreach ($templates as $template) {
            $dbErrors = $converter->scanDatabaseTpl($template);
            if (!$this->safeCount($dbErrors)) {
                $this->updateStatus('smartyCustomPdf', $template['name']);
            } else {
                $this->updateStatus('smartyOutdatedCustomPdf', $template['name'], implode(', ', $dbErrors));
                $this->log('Errors: ' . implode(', ', $dbErrors), 'ERROR');
            }
        }
        if (!is_dir('./custom') && !is_dir('./modules')) {
            $converter::unmuteExpectedErrors();
            return;
        }

        $iterator = new AppendIterator();
        if (is_dir('./custom')) {
            $customRecursiveIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('./custom'));
            $customRegexIterator = new RegexIterator($customRecursiveIterator, '/^.+\.tpl$/i', RecursiveRegexIterator::GET_MATCH);
            $iterator->append($customRegexIterator);
        }
        if (is_dir('./modules')) {
            $modulesRecursiveIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('./modules'));
            $modulesRegexIterator = new RegexIterator($modulesRecursiveIterator, '/^.+\.tpl$/i', RecursiveRegexIterator::GET_MATCH);
            $iterator->append($modulesRegexIterator);
        }
        $errors = [];
        foreach ($iterator as $match) {
            $curFile = $match[0];
            if (defined('SUGAR_SHADOW_TEMPLATEPATH')) {
                $realName = realpath(str_replace(SHADOW_INSTANCE_DIR . '/', '', $curFile));
                if (0 === strpos($realName, (string) SUGAR_SHADOW_TEMPLATEPATH)) {
                    continue;
                }
            } elseif (isset($this->md5_files[str_replace('\\', '/', $curFile)])) {// Stock file
                continue;
            }
            if ($this->isIgnoredTplPattern($curFile)) {
                $this->updateStatus('smartyUnsupportedSyntax', $curFile);
                continue;
            }
            $errors = $converter->scanFilesystemTpl($curFile);
            if (!$this->safeCount($errors)) {
                $this->updateStatus('smartyCustomization', $curFile);
            } else {
                $this->updateStatus('smartyOutdatedCustomization', $curFile);
                $this->log('Errors: ' . implode(', ', $errors), 'ERROR');
            }
        }
        $cacheDir = sugar_cached('smarty3/');
        if (file_exists($cacheDir)) {
            rmdir_recursive($cacheDir);
        }
        if ($this->safeCount($errors)) {
            rmdir_recursive('./_smarty3_');
        }
        $converter::unmuteExpectedErrors();
    }

    /**
     * @param string $filename
     * @return bool
     */
    private function isIgnoredTplPattern(string $filename): bool
    {
        foreach ($this->ignoredTplPatterns as $pattern) {
            if (preg_match($pattern, $filename)) {
                return true;
            }
        }
        return false;
    }

    protected function checkSmartyTemplatesModifierDeprecationErrors()
    {
        $sql = "SELECT * FROM pdfmanager WHERE deleted='0'";
        $templates = DBManagerFactory::getConnection()->fetchAllAssociative($sql);

        foreach ($templates as $template) {
            $oldErrorReporting = error_reporting();
            error_reporting(E_USER_DEPRECATED);
            $deprecationError = $this->getSmartyTemplateDeprecationError((string) $template['body_html']);
            error_reporting($oldErrorReporting);
            if ($deprecationError !== null) {
                $this->updateStatus('smarty4ModifierDeprecation', $template['name'], $deprecationError);
                $this->log('Errors: ' . $deprecationError, 'ERROR');
            }
        }

        if (!is_dir('./custom') && !is_dir('./modules')) {
            return;
        }

        $iterator = new AppendIterator();
        if (is_dir('./custom')) {
            $customRecursiveIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('./custom'));
            $customRegexIterator = new RegexIterator($customRecursiveIterator, '/^.+\.tpl$/i', RecursiveRegexIterator::GET_MATCH);
            $iterator->append($customRegexIterator);
        }
        if (is_dir('./modules')) {
            $modulesRecursiveIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('./modules'));
            $modulesRegexIterator = new RegexIterator($modulesRecursiveIterator, '/^.+\.tpl$/i', RecursiveRegexIterator::GET_MATCH);
            $iterator->append($modulesRegexIterator);
        }
        foreach ($iterator as $match) {
            $curFile = $match[0];
            if (defined('SUGAR_SHADOW_TEMPLATEPATH')) {
                $realName = realpath(str_replace(SHADOW_INSTANCE_DIR . '/', '', $curFile));
                if (0 === strpos($realName, SUGAR_SHADOW_TEMPLATEPATH)) {
                    continue;
                }
            } elseif (isset($this->md5_files[str_replace('\\', '/', $curFile)])) {// Stock file
                continue;
            }
            $oldErrorReporting = error_reporting();
            error_reporting(E_USER_DEPRECATED);
            $deprecationError = $this->getSmartyTemplateDeprecationError((string) file_get_contents($curFile));
            error_reporting($oldErrorReporting);
            if ($deprecationError !== null) {
                $this->updateStatus('smarty4ModifierDeprecation', $curFile, $deprecationError);
                $this->log('Errors: ' . $deprecationError, 'ERROR');
            }
        }
    }

    protected function getSmartyTemplateDeprecationError(string $tpl): ?string
    {
        $ss = new Sugar_Smarty();
        $ss->force_compile = true;
        ob_start();
        try {
            error_clear_last();
            $ss->display('string:' . $tpl);
        } catch (Throwable $e) {
            return null;
        } finally {
            ob_end_clean();
        }

        $lastError = error_get_last();
        if (strstr($lastError['message'] ?? '', 'as a modifier is deprecated and will be removed ')) {
            error_clear_last();
            return $lastError['message'];
        }
        return null;
    }

    public function initPackageScan()
    {
        $flags = E_ALL & ~E_STRICT;
        if (defined('E_DEPRECATED')) {
            $flags = $flags & ~E_DEPRECATED;
        }
        set_error_handler([$this, 'scriptErrorHandler'], $flags);
        $this->init();
        return $this->logMeta;
    }

    public function finishScan()
    {
        $this->log("VERDICT: {$this->status}", 'STATUS');
        ksort($this->status_log);
        foreach ($this->status_log as $status => $items) {
            $this->log("=> $status: " . $this->safeCount($items) . ' total', 'BUCKET');
            foreach ($items as $item) {
                $this->log(sprintf('=> %s: %s', $status, $item['reason']), 'BUCKET');
            }
        }
        restore_error_handler();
    }

    /**
     * return list of installed packages by type.
     * @TODO use UpgradeHistory->getInstalledPackagesByType after Sugar 10.1.0 upgrade
     * @param string $type
     * @return array|SugarBean[]
     * @throws SugarQueryException
     */
    protected function getInstalledPackagesByType(string $type): array
    {
        $history = new UpgradeHistory();
        $query = new SugarQuery();
        $query->from($history);
        $query->where()->equals('status', 'installed');
        $query->where()->equals('type', $type);
        return $history->fetchFromQuery($query);
    }

    /**
     * Checks SugarBPM locked fields.
     * If any field group is partially locked, it will notify with a Bucket F red flag.
     */
    protected function checkPALockedFields()
    {
        // Make sure we need to run this first
        [, $flavor] = $this->getVersionAndFlavor();

        // only run this for ENT or ULT flavors
        if (in_array(strtolower($flavor), ['ent', 'ult'])) {
            $warnings = $this->checkLockedFieldGroups();
            foreach ($warnings as $warning) {
                $this->updateStatus('invalidAWFLockedFieldGroup', $warning['group'], $warning['pd'], $warning['module'], $warning['fields']);
            }
        }
    }

    /**
     * Checks whether there is any field group that is only partially locked.
     * Used by HealthCheck to ensure that upgraded process definitions will function
     * properly concerning locked fields.
     * @return array List of warnings
     */
    protected function checkLockedFieldGroups()
    {
        $durationModules = ['Calls', 'Meetings'];
        $durationFields = ['duration_hours', 'duration_minutes', 'date_end'];
        $durationGroup = 'duration';

        $warnings = [];

        // Add a logger for this step
        $this->log('Checking for any field group that is only partially locked.');

        // Build the query and run it
        $sql = 'SELECT id, name, pro_module, pro_locked_variables'
            . ' FROM pmse_bpm_process_definition'
            . ' WHERE deleted = 0';
        $result = $this->db->query($sql);

        // Loop and check now, making sure to send a false flag to fetchByAssoc
        // to ensure that the data in the row does not get html encoded on fetch
        while ($row = $this->db->fetchByAssoc($result, false)) {
            $lockedFields = json_decode(html_entity_decode($row['pro_locked_variables'], ENT_COMPAT));
            if ($lockedFields) {
                $bean = BeanFactory::newBean($row['pro_module']);
                $checkDuration = in_array($row['pro_module'], $durationModules);
                // tally the locked fields in each group
                $locked = [];
                foreach ($lockedFields as $lockedField) {
                    $def = $bean->field_defs[$lockedField];
                    if (isset($def['group'])) {
                        if (isset($locked[$def['group']])) {
                            $locked[$def['group']][] = $lockedField;
                        } else {
                            $locked[$def['group']] = [$lockedField];
                        }
                    } else {
                        // if a locked field does not belong to any group, default itself as the locked group
                        if (!empty($locked[$lockedField])) {
                            $locked[$lockedField] = array_merge($locked[$lockedField], [$lockedField]);
                        } else {
                            $locked[$lockedField] = [$lockedField];
                        }
                    }
                    if ($checkDuration && in_array($lockedField, $durationFields)) {
                        if (isset($locked[$durationGroup])) {
                            $locked[$durationGroup][] = $lockedField;
                        } else {
                            $locked[$durationGroup] = [$lockedField];
                        }
                    }
                }
                // tally the number of fields in each group
                foreach ($locked as $group => $fields) {
                    if ($checkDuration && $group == $durationGroup) {
                        $total = $this->safeCount($durationFields);
                    } else {
                        $total = 0;
                        foreach ($bean->field_defs as $def) {
                            // if field name happens to be the name of a group, it is by default in that group
                            if ($def['name'] == $group || (isset($def['group']) && $def['group'] == $group)) {
                                $this->log($group . ' => ' . $def['name']);
                                $total++;
                            }
                        }
                    }
                    if ($total > $this->safeCount($fields)) {
                        // Add this failure to the stack
                        $warnings[] = [
                            'fields' => implode(',', $fields),
                            'group' => $group,
                            'pd' => $row['name'],
                            'module' => $row['pro_module'],
                        ];
                    }
                }
            }
        }

        return $warnings;
    }


    /**
     * Checks whether the unserialization of PHP serialized data will actually
     * work during an upgrade. Used by HealthCheck to ensure that an upgrade will
     * actually succeed when it comes to SugarBPM conversion of serialized
     * data to json data.
     * @return array List of warnings
     */
    protected function checkUnserializationFailures()
    {
        foreach ($this->unserializeTasks as $data) {
            $this->handleUnserializeCheck($data);
        }

        return $this->getUnserializeFailureWarnings();
    }

    /**
     * Triggers the actual unserialize check on the data
     * @param array $data Collection of properties used to check serialized data
     */
    protected function handleUnserializeCheck($data)
    {
        // Define our table
        $table = $data['table'];

        // Define the column(s) we will be working with.
        // This is always an array.
        $cols = $data['cols'];

        // Builds a simple list of selectable columns
        $selectCols = implode(',', $cols);

        // Add a logger for this step
        $this->log(
            sprintf(
                "Checking unserialization of column(s) '%s' in the '%s' table...",
                $selectCols,
                $table
            )
        );

        // Builds a list of not empty SQL bits.
        $whereCols = $updateCols = [];
        foreach ($cols as $col) {
            $whereCols[] = $this->getNotEmptyFieldSQL($col);
        }

        $whereNotEmpty = implode(' AND ', $whereCols);

        // Build the query and run it
        $select = 'SELECT id, %s FROM %s WHERE %s';
        $sql = sprintf($select, $selectCols, $table, $whereNotEmpty);
        $result = $this->db->query($sql);

        // Loop and check now, making sure to send a false flag to fetchByAssoc
        // to ensure that the data in the row does not get html encoded on fetch
        while ($row = $this->db->fetchByAssoc($result, false)) {
            foreach ($cols as $col) {
                // Isolate the actual data to be handled
                $string = $row[$col];

                // If there are functions to apply to this data, do that now
                if (isset($data['functions'])) {
                    foreach ($data['functions'] as $function) {
                        $string = $function($string);
                    }
                }

                // If, for some reason, the string to be checked is empty, there
                // is nothing to do.
                if (empty($string)) {
                    continue;
                }

                // Get our decode flag from the properties
                $decode = !isset($data['decode']) || $data['decode'] === true;

                // Do the actual check now
                $reason = $this->checkSerializedData($string, $decode);

                // If there was a failure reason, add it to the stack of reasons
                if ($reason) {
                    $msg = $this->getPALogMessage($table, $col, $row['id'], $string, $reason);

                    // Log this so we know what failed and why
                    $this->log($msg);

                    // Add this failure to the stack
                    $this->addUnserializeFailureWarning($table, $col, $reason);
                }
            }
        }
    }

    /**
     * Get a not empty field where clause for a column. Done in a wrapper method
     * because Oracle does things a little different.
     * @param string $col The name of the column to build the SQL on
     * @return string
     */
    protected function getNotEmptyFieldSQL($col)
    {
        // Oracle cannot handle empty string comparisons, so this one will be a
        // NULL check only
        if ($this->db instanceof OracleManager) {
            $return = "$col IS NOT NULL";
        } else {
            $quoted = $this->db->quoted('');
            $return = "($col != $quoted AND $col IS NOT NULL)";
        }

        return $return;
    }

    /**
     * Gets a parsed log message string for SugarBPM Unserialization issues
     * @param string $table The table that contains the data for this failure
     * @param string $col The column that contains the data for this failure
     * @param string $id The id of the failed data
     * @param string $string The actual failed data
     * @param int $reason The failure reason code
     * @return string
     */
    protected function getPALogMessage($table, $col, $id, $string, $reason)
    {
        // Build a log string for parsing into the log
        $logString = "UNSERIALIZATION FAILURE:\nTable: %s\nColumn: %s\nID: %s";
        $logString .= "\n-----\n%s\n-----\nReason: %s";

        // Translate the reason code
        $reason = $this->getUnserializeFailureReason($reason);
        return sprintf($logString, $table, $col, $id, $string, $reason);
    }

    /**
     * Checks an input to see if there are unserialization issues with it
     * @param string $input Serialized data
     * @param boolean $decode Whether to html entity decode the input
     * @return int
     */
    protected function checkSerializedData($input, $decode = true)
    {
        // Basic good return
        $reason = 0;
        if ($this->serializationHasObjectRefs($input)) {
            // This is an easy check, and tells us right away why we wouldn't be
            // able to unserialize
            $reason = self::UNSERIALIZE_FAIL_OBJECTS;
        } else {
            // Since we need to work on html decoded data, get that now
            $decoded = $decode ? html_entity_decode($input, ENT_COMPAT) : $input;

            // Now try to unserialize, suppressing errors in case of bad data
            $unserialized = @unserialize($decoded, ['allowed_classes' => false]);

            // If this failed, it is either an encoding issue or just bad data
            if ($unserialized === false) {
                // If the secondary unserializer returns a false, then we have
                // bad data that cannot be manipulated into something unserializable
                if ($this->secondaryUnserialize($decoded) === false) {
                    $reason = self::UNSERIALIZE_FAIL_DATA;
                }
            }
        }

        return $reason;
    }

    /**
     * Handles a second level of unserialization in case the first one didn't
     * work. This happens in cases where data may have been serialized in one
     * encoding charset but is being unserialized in another.
     * @param string $input Serialized data
     * @param boolean $decode Whether to html entity decode the input
     * @return boolean
     */
    protected function secondaryUnserialize($string)
    {
        // For reference, please see the following links...
        //http://magp.ie/2014/08/13/php-unserialize-string-after-non-utf8-characters-stripped-out/
        //https://dzone.com/articles/mulit-byte-unserialize
        //http://stackoverflow.com/questions/2853454/php-unserialize-fails-with-non-encoded-characters
        $string = preg_replace_callback(
            '!s:(\d+):"(.*?)";!s',
            function ($matches) {
                if (isset($matches[2])) {
                    return 's:' . strlen($matches[2]) . ':"' . $matches[2] . '";';
                }
            },
            $string
        );

        // Use error suppression to prevent erroneous output
        return @unserialize($string, ['allowed_classes' => false]) !== false;
    }

    /**
     * Checks whether the $value contains object or class references, but not
     * objects of type stdClass
     * @param string $value Serialized value of any type
     * @return boolean
     */
    protected function serializationHasObjectRefs($value)
    {
        // Remove all references to stdClass objects
        $cleared = str_replace('O:8:"stdClass"', '', $value);

        // Now use the same logic as the unserialize validator
        preg_match('/[oc]:[^:]*\d+:/i', $cleared, $matches);
        return $this->safeCount($matches) > 0;
    }

    /**
     * Adds a preflight warning to the list of warnings
     * @param string $table The table that the warning was thrown on
     * @param string $col The column the warning was thrown on
     * @param int $reason The reason code for the warning
     */
    protected function addUnserializeFailureWarning($table, $col, $reason)
    {
        if (!isset($this->unserializeFailureWarnings[$table][$col][$reason])) {
            $this->unserializeFailureWarnings[$table][$col][$reason] = 0;
        }

        $this->unserializeFailureWarnings[$table][$col][$reason]++;
    }

    /**
     * Gets a semi formatted list of data for use in parsing the healthcheck
     * scan
     * @return array
     */
    protected function getUnserializeFailureWarnings()
    {
        // Order is important, so make it count... col... table.. reason
        $return = [];
        foreach ($this->unserializeFailureWarnings as $table => $cols) {
            foreach ($cols as $col => $reasons) {
                foreach ($reasons as $reason => $count) {
                    $return[] = [
                        'count' => $count,
                        'col' => $col,
                        'table' => $table,
                        'reason' => $this->getUnserializeFailureReason($reason),
                    ];
                }
            }
        }

        return $return;
    }

    /**
     * Gets a translated failure reason
     * @param int $reason Reason code
     * @return string
     */
    protected function getUnserializeFailureReason($reason)
    {
        if ($reason && array_key_exists($reason, $this->unserializeFailureReasons)) {
            return $this->meta->getModString($this->unserializeFailureReasons[$reason]);
        }

        return "Could not determine reason (Reason Code: $reason)";
    }

    /**
     * Checks for invalid field use across PA modules (as needed) and returns a
     * list of warnings based on the scan.
     * @return array
     */
    protected function checkPAInvalidFieldUse()
    {
        foreach ($this->invalidFieldUseMethods as $method) {
            $this->$method();
        }

        return $this->getInvalidFieldUseWarnings();
    }

    /**
     * @param string $contents
     * @param string $phpfile
     * @param bool $processOutput
     */
    public function scanForOutputConstructs(string $contents, string $phpfile, bool $processOutput): void
    {
        $sePattern = <<<ENDP
if\s*\(\s*!\s*defined\s*\(\s*'sugarEntry'\s*\)\s*(\|\|\s*!\s*sugarEntry\s*)?\)\s*{?\s*die\s*\(\s*'Not A Valid Entry Point'\s*\)\s*;\s*}?
ENDP;
        $contents = preg_replace("#$sePattern#i", '', $contents);

        $tokens = token_get_all($contents);
        $tokens = array_filter($tokens, [$this, 'ignoreWhitespace']);
        $tokens = array_values($tokens);
        foreach ($tokens as $index => $token) {
            if (is_array($token)) {
                if ($token[0] == T_INLINE_HTML) {
                    $inlineHTMLStatus = (strlen(trim($token[1])) != 0) ? 'inlineHtml' : 'inlineHtmlSpacing';
                    $args = [$inlineHTMLStatus, $phpfile, $token[2]];
                } elseif ($processOutput && $token[0] == T_ECHO) {
                    $args = ['foundEcho', $phpfile, $token[2]];
                } elseif ($processOutput && $token[0] == T_PRINT) {
                    $args = ['foundPrint', $phpfile, $token[2]];
                } elseif ($token[0] == T_EXIT) {
                    $args = ['foundDieExit', $phpfile, $token[2]];
                } elseif ($processOutput && $token[0] == T_STRING && $token[1] == 'print_r' && $this->checkPrintR($index, $tokens)) {
                    $args = ['foundPrintR', $phpfile, $token[2]];
                } elseif ($processOutput && $token[0] == T_STRING && $token[1] == 'var_dump') {
                    $args = ['foundVarDump', $phpfile, $token[2]];
                } elseif ($token[0] == T_STRING && strpos($token[1], 'ob_') === 0) {
                    $args = ['inlineHtml', $token[1], $phpfile, $token[2]];
                } else {
                    continue;
                }
                call_user_func_array([$this, 'updateStatus'], $args);
            }
        }
    }

    /**
     * Checks all process definition actions for valid fields. If any field in
     * the def is invalid, this will log that and the scanner will fail with
     * Bucket F failures.
     */
    protected function checkActionsForInvalidFields()
    {
        // Decorate our SQL so that each DB can parse this properly
        $afmNotEmptyString = $this->getNotEmptyFieldSQL('ad.act_field_module');
        $afNotEmptyString = $this->getNotEmptyFieldSQL('ad.act_fields');

        // build the SQL now
        $sql = "SELECT
                    /* Activity definition fields - the basis for what we are doing */
                    ad.id id, ad.name action_name,
                    ad.act_fields action_fields, ad.act_field_module action_module,
                    /* Process Definition fields - needed for target module */
                    pd.pro_module target_module,
                    /* Project fields - needed for logging the PD name */
                    p.name process_name,
                    /* Activity fields - needed for activity type validation */
                    a.act_task_type action_task_type, a.act_script_type action_script_type
                FROM
                    pmse_bpm_activity_definition ad
                    INNER JOIN
                        pmse_bpmn_activity a ON ad.id = a.id
                    INNER JOIN
                        pmse_bpm_process_definition pd ON ad.pro_id = pd.id
                    INNER JOIN
                        pmse_project p ON pd.prj_id = p.id
                WHERE
                    $afmNotEmptyString
                    AND $afNotEmptyString
                    AND a.act_task_type = 'SCRIPTTASK'
                    AND pd.deleted = 0
                    AND ad.deleted = 0
                    AND p.deleted =0";

        $result = $this->db->query($sql);

        // Always send false as the second argument to fetch so that we do not
        // get bitten by encoded data
        while ($row = $this->db->fetchByAssoc($result, false)) {
            $validationType = $this->getValidationType($row['action_script_type']);
            $this->scanActionsForInvalidFields($row, $row['target_module'], $validationType);
        }
    }

    /**
     * Gets a validation type code if one exists for $key
     * @param string $key The script type to check against
     * @return string
     */
    protected function getValidationType($key)
    {
        return $this->processFieldValidationTypes[$key] ?? '';
    }

    /**
     * Scans action definition data for invalid fields
     * @param array $element An activity element from an import
     * @param string $module The module to get fields to validate from
     * @param string $type The type of field validation to apply
     * @return string
     */
    protected function scanActionsForInvalidFields(array $element, $module, $type = '')
    {
        if (!empty($element['action_module'])) {
            // This gets the correct bean for testing the action
            $bean = $this->getProperProcessBean($module, $element['action_module']);

            // Get the field data array for this action
            $fieldData = json_decode($element['action_fields'], true);

            // In some cases $fieldData comes back null, so we need to check
            // if it is actually an array before trying to use it as one
            if (is_array($fieldData)) {
                foreach ($fieldData as $fieldDef) {
                    $field = $fieldDef['field'];
                    if (isset($bean->field_defs[$field])) {
                        if (!$this->isValidPAField($bean->field_defs[$field], $type)) {
                            // Get the message to log
                            $msg = $this->getInvalidPAActionFieldLogMessage(
                                $bean->module_dir,
                                $field,
                                $element['action_script_type'],
                                $element['process_name'],
                                $element['action_name'],
                                $element['id']
                            );

                            // Log the message
                            $this->log($msg);

                            // Add the failure to the stack
                            $this->addInvalidPAFieldWarning('Actions');
                        }
                    } else {
                        // A field that is not found on the module is also a
                        // problem
                        $msg = $this->getInvalidPANotFoundFieldLogMessage(
                            $bean->module_dir,
                            $field,
                            $element['action_script_type'],
                            $element['process_name'],
                            $element['id']
                        );

                        // Log the message
                        $this->log($msg);

                        // Add the failure to the stack
                        $this->addInvalidPAFieldWarning('Actions');
                    }
                }
            }
        }
    }

    /**
     * Gets the proper module for a field validation check
     * @param string $tModule Target module name
     * @param string $aModule Related module name
     * @return string
     */
    protected function getProperProcessModule($tModule, $aModule)
    {
        return $this->getProperProcessBean($tModule, $aModule, true);
    }

    /**
     * Gets the proper bean for a field validation check
     * @param string $tModule Target module name
     * @param string $aModule Action module name
     * @param boolean $asModule If true, sends back just the module name
     * @return SugarBean
     */
    protected function getProperProcessBean($tModule, $aModule, $asModule = false)
    {
        // If there is a field on the target module that matches the action module
        // but is different from the target module...
        if ($tModule != $aModule) {
            // Start with the target module bean
            $bean = BeanFactory::newBean($tModule);

            // See if there is a corresponding field for this module
            if (isset($bean->field_defs[$aModule])) {
                // If we have a link field load the relationship for it
                if ($bean->field_defs[$aModule]['type'] === 'link') {
                    // Load the relationship for the action module
                    $bean->load_relationship($aModule);

                    // If the relationship loaded, get the related bean for it
                    if ($bean->$aModule) {
                        $rModule = $bean->$aModule->getRelatedModuleName();

                        // If we want just the module, send that back
                        if ($asModule) {
                            return $rModule;
                        }

                        return BeanFactory::newBean($rModule);
                    } else {
                        $this->log("Could not load relationship for link field $aModule on {$bean->module_dir}");
                    }
                } elseif (isset($bean->field_defs[$aModule]['module'])) {
                    // If we are a relate field, see if we have a module on that def
                    $rModule = $bean->field_defs[$aModule]['module'];

                    // If we want just the module, send that back
                    if ($asModule) {
                        return $rModule;
                    }

                    return BeanFactory::newBean($rModule);
                }
            }
        }

        // If we want just the module, send that back, otherwise return the bean
        // for the target module
        if ($asModule) {
            return $tModule;
        }

        // If we had a bean, send it back
        if (isset($bean)) {
            return $bean;
        }

        // If we didn't make it into the conditional, build the bean here
        return BeanFactory::newBean($tModule);
    }

    /**
     * Gets the message to log when an invalid field is discovered
     * @param string $module The module that this field is on
     * @param string $field The field that is invalid
     * @param string $action The action type that is being validated
     * @param string $processName The name of the process definition this is on
     * @param string $actionName The name of the action element that contains the field
     * @param string $id The id of the action
     * @return string
     */
    protected function getInvalidPAActionFieldLogMessage($module, $field, $action, $processName, $actionName, $id)
    {
        $msg = "-----\n%s->%s field is not a valid action field for the %s action type.\n";
        $msg .= "Process Definition Name: %s\nAction Name: %s\n";
        $msg .= "Table: pmse_bpm_activity_definition\nColumn: act_fields\nID: %s\n-----";

        return sprintf(
            $msg,
            $module,
            $field,
            $action,
            $processName,
            $actionName,
            $id
        );
    }

    /**
     * Gets the message to log when an invalid field is discovered
     * @param string $module The module that this field is on
     * @param string $field The field that is invalid
     * @param string $action The action type that is being validated
     * @param string $processName The name of the process definition this is on
     * @param string $id The id of the action
     * @return string
     */
    protected function getInvalidPANotFoundFieldLogMessage($module, $field, $processName, $actionName, $id)
    {
        $msg = "-----\n%s was not found as a field on the %s module.\n";
        $msg .= "Process Definition Name: %s\nAction Name: %s\n";
        $msg .= "Table: pmse_bpm_activity_definition\nColumn: act_fields\nID: %s\n-----";

        return sprintf(
            $msg,
            $field,
            $module,
            $processName,
            $actionName,
            $id
        );
    }

    /**
     * Field validator that simulates isValidField in PMSEEngineUtils.
     * @param array $def Field def
     * @param string $type Type of action validation
     * @return boolean
     */
    protected function isValidPAField($def, $type = '')
    {
        // Empty name attribute is always a false
        if (empty($def['name'])) {
            return false;
        }

        if (($return = $this->isValidPAFieldByVardef($def)) !== null) {
            return $return;
        }

        if ($this->isWhitelistedPAField($def['name'], $type)) {
            return true;
        }

        if ($this->isBlacklistedPAField($def['name'], $type)) {
            return false;
        }

        if ($this->isNonDBField($def)) {
            return false;
        }

        if ($this->isBlacklistedPAFieldType($def)) {
            return false;
        }

        if ($this->isCalculatedField($def, $type)) {
            return false;
        }

        if ($this->isReadonlyField($def, $type)) {
            return false;
        }

        if (($return = $this->isValidStudioField($def)) !== null) {
            return $return;
        }

        return $this->isValidFieldBySourceAndType($def);
    }

    /**
     * Checks a vardef for processes directive. If founds, calculates it and
     * returns it. Otherwise returns null.
     * @param array $def The vardef to check
     * @return boolean True|false if evaluated, null if not
     */
    protected function isValidPAFieldByVardef($def)
    {
        // The class that will be used to check function evaluation, if it exists
        $utilClass = 'PMSEEngineUtils';

        // First things first... if we are explicitly directed to do something
        // based on the vardefs, do that thing first
        if (isset($def['processes'])) {
            // If a field is explicitly marked for processes, handle it
            if (is_bool($def['processes'])) {
                return $def['processes'];
            }

            // If the marker is a string or an array, it is mapped to a method
            if (is_string($def['processes'])) {
                $def['processes'] = [$def['processes']];
            }

            // For a field validation list, run through until you hit a false,
            // otherwise let the rest of the validation processes run
            foreach ($def['processes'] as $method) {
                // Only try to use the util class if it exists, without autoloading
                // it to prevent issues
                if (class_exists($utilClass, false) && method_exists($utilClass, $method)) {
                    if ($utilClass::$method() === false) {
                        return false;
                    }
                }
            }
        }

        // Nothing to do, return null to let consumers know this wasn't used
        return null;
    }

    /**
     * Checks the whitelist to force PA field validity
     * @param string $field The field name to check
     * @param string $type The validation type code
     * @return boolean
     */
    protected function isWhitelistedPAField($field, $type)
    {
        $list = $this->whitelistedPAFields['ALL'];
        if ($type && array_key_exists($type, $this->whitelistedPAFields)) {
            $list = array_merge($list, $this->whitelistedPAFields[$type]);
        }

        return $this->safeInArray($field, $list);
    }

    /**
     * Checks the field blacklist for validity
     * @param string $field The name of the field to check
     * @param string $type The validation type code
     * @return boolean
     */
    protected function isBlacklistedPAField($field, $type = '')
    {
        $list = $this->blacklistedPAFields['ALL'];
        if ($type && array_key_exists($type, $this->blacklistedPAFields)) {
            $list = array_merge($list, $this->blacklistedPAFields[$type]);
        }

        return $this->safeInArray($field, $list);
    }

    /**
     * Checks if a field is non-db source
     * @param array $def Field def
     * @return boolean
     */
    protected function isNonDBField($def)
    {
        return isset($def['source']) && $def['source'] === 'non-db';
    }

    /**
     * Checks the field blacklist for validity
     * @param array $def The vardef to check
     * @return boolean
     */
    protected function isBlacklistedPAFieldType($def)
    {
        return isset($def['type']) && in_array($def['type'], $this->blacklistedPAFieldTypes);
    }

    /**
     * Checks whether a field is calculated and is a write type validation
     * @param array $def Field def
     * @param string $type The validation type code
     * @return boolean
     */
    protected function isCalculatedField($def, $type)
    {
        return in_array($type, ['AC', 'CF', 'BR']) && isset($def['formula']);
    }

    /**
     * Checks whether a field is readonly
     * @param array $def Field def
     * @param string $type The validation type code
     * @return boolean
     */
    protected function isReadonlyField($def, $type)
    {
        return in_array($type, ['RR', 'AC', 'CF', 'BR']) && isset($def['readonly']);
    }

    /**
     * Checks if a field is valid based on the studio attribute
     * @param array $def Field def
     * @return boolean
     */
    protected function isValidStudioField($def)
    {
        // We only do something here is there is a studio attribute
        if (isset($def['studio'])) {
            // If the studio attribue is an array, check editField and required
            // If either of those is truthy, this is a valid field
            if (is_array($def ['studio'])) {
                if (!empty($def['studio']['editField']) || !empty($def['studio']['required'])) {
                    return true;
                }
            } else {
                // If the studio attribute is a string and set to visible, then
                // it is valid
                if ($def['studio'] === 'visible') {
                    return true;
                }

                // If the studio attribute is falsey then it is not valid
                if (empty($def['studio']) || $def['studio'] === 'hidden' || $def['studio'] === 'false') {
                    return false;
                }
            }
        }

        // No studio directive, return null
        return null;
    }

    /**
     * Checks a vardef for a proper type to make a field valid for edits
     * @param array $def Field def
     * @return boolean
     */
    protected function isValidFieldBySourceAndType($def)
    {
        // The basics here are simple... if this is a DB field or a custom field
        // and it is not an ID type, it is valid for edits
        if (empty($def['source']) || $def['source'] === 'db' || $def['source'] === 'custom_fields') {
            if ($def['type'] !== 'id' && (empty($def['dbType']) || $def['dbType'] !== 'id')) {
                return true;
            }
        }

        // Otherwise it is not
        return false;
    }

    /**
     * Checks all business rules in the system for valid fields. If any field is
     * invalid for READ or WRITE ops, this method will log that and the scan will
     * fail with a Bucket F report.
     */
    protected function checkBusinesRulesForInvalidFields()
    {
        // Decorate our SQL so that each DB can parse this properly
        $defNotEmptyString = $this->getNotEmptyFieldSQL('br.rst_source_definition');

        // Build up the query that is needed for this process
        $sql = "SELECT
                    br.id id, br.name name, br.rst_source_definition definition,
                    br.rst_module module
                FROM
                    pmse_business_rules br
                WHERE
                    $defNotEmptyString
                    AND br.deleted = 0";

        $result = $this->db->query($sql);

        // Always send false as the second argument to fetch so that we do not
        // get bitten by encoded data
        while ($row = $this->db->fetchByAssoc($result, false)) {
            $data = $this->getParsedBusinessRuleData($row['definition']);
            foreach ($data['scan'] as $type => $rows) {
                foreach ($rows as $row) {
                    $bean = BeanFactory::newBean($row['module']);
                    $field = $row['field'];
                    if ($bean) {
                        if (isset($bean->field_defs[$field])) {
                            if (!$this->isValidPAField($bean->field_defs[$field], $type)) {
                                // Get the business rule type
                                $brType = $this->getBusinessRuleValidationType($type);

                                // Get the message to log
                                $msg = $this->getInvalidBusinessRuleFieldLogMessage(
                                    $row['module'],
                                    $field,
                                    $brType,
                                    $data['name'],
                                    $data['id']
                                );

                                // Log the message
                                $this->log($msg);

                                // Add the failure to the stack
                                $this->addInvalidPAFieldWarning('Business Rules');
                            }
                        } else {
                            // A field not found is also a problem
                            $msg = $this->getInvalidPABRNotFoundFieldLogMessage(
                                $row['module'],
                                $field,
                                $data['name'],
                                $data['id']
                            );

                            // Log the message
                            $this->log($msg);

                            // Add the failure to the stack
                            $this->addInvalidPAFieldWarning('Business Rules');
                        }
                    }
                }
            }
        }
    }

    /**
     * Adds an invalid field type count increment
     * @param string $type The type of element that contains the invalid field
     */
    protected function addInvalidPAFieldWarning($type)
    {
        if (!isset($this->invalidPAFields[$type])) {
            $this->invalidPAFields[$type] = 0;
        }

        $this->invalidPAFields[$type]++;
    }

    /**
     * Gets a properly formatted list of invalid field use types and counts
     * @return array
     */
    protected function getInvalidFieldUseWarnings()
    {
        $return = [];
        foreach ($this->invalidPAFields as $type => $count) {
            $return[] = [
                'type' => $type,
                'count' => $count,
            ];
        }

        return $return;
    }

    /**
     * Gets a business rule validation type based on a shortcode key
     * @param string $key The shortcode key for this business rule element type
     * @return string
     */
    protected function getBusinessRuleValidationType($key)
    {
        return $this->businessRuleTypes[$key];
    }

    /**
     * Gets the message to log when an invalid field is discovered
     * @param string $module The module that this field is on
     * @param string $field The field that is invalid
     * @param string $type The business rule type (condition or conclusion)
     * @param string $name The name of the business rule this is on
     * @param string $id The id of the business rule
     * @return string
     */
    protected function getInvalidBusinessRuleFieldLogMessage($module, $field, $type, $name, $id)
    {
        $msg = "-----\n%s->%s field is not a valid business rule %s field.\n";
        $msg .= "Business Rule Name: %s\nID: %s\n-----";

        return sprintf(
            $msg,
            $module,
            $field,
            $type,
            $name,
            $id
        );
    }

    /**
     * Gets the message to log when an invalid field is discovered
     * @param string $module The module that this field is on
     * @param string $field The field that is invalid
     * @param string $name The name of the business rule this is on
     * @param string $id The id of the business rule
     * @return string
     */
    protected function getInvalidPABRNotFoundFieldLogMessage($module, $field, $name, $id)
    {
        $msg = "-----\n%s was not found as a field on the %s module.\n";
        $msg .= "Business Rule Name: %s\nID: %s\n-----";

        return sprintf(
            $msg,
            $field,
            $module,
            $name,
            $id
        );
    }

    /**
     * Parses a business rule definition to get relevent information for validation
     * @param string $def JSON encoded string of definition data
     * @return array
     */
    protected function getParsedBusinessRuleData($def)
    {
        // Start with decoding the json string
        $data = json_decode($def, true);

        // Now set the return, using some parts of the definition that we need
        $return = [
            'id' => $data['id'],
            'name' => $data['name'],
            'module' => $data['base_module'],
            'scan' => [
                'BR' => [],
                'BRR' => [],
            ],
        ];

        // We are going to need the bean for the target module, so get that
        $bean = BeanFactory::newBean($data['base_module']);

        // Since we are going to be checking read and write ops, we need to
        // make sure we have both of these values
        if (isset($data['columns'], $data['ruleset'])) {
            // Conditions on columns are an easy fetch, so collect those now
            foreach ($data['columns']['conditions'] as $row) {
                // Get the module in question
                $module = $this->getProperProcessModule($data['base_module'], $row['module']);

                // When it comes times to check this later, it makes no sense
                // to check the same combination of module:field more than once
                $key = $this->getBusinessRuleKey($module, $row['field']);

                // Create a scannable row of business rule condition fields
                // by module
                $return['scan']['BRR'][$key] = [
                    'module' => $module,
                    'field' => $row['field'],
                ];
            }

            // Handle the rulesets, or what is actually checked and returned
            // and written
            foreach ($data['ruleset'] as $ruleset) {
                // Start with the conditions of each ruleset
                if (isset($ruleset['conditions']) && is_array($ruleset['conditions'])) {
                    foreach ($ruleset['conditions'] as $condition) {
                        // Again, get the module we need
                        $module = $this->getProperProcessModule($data['base_module'], $condition['variable_module']);

                        // Again, make a key
                        $key = $this->getBusinessRuleKey($module, $condition['variable_name']);

                        // Add this module:field to the stack of READ ops checks
                        $return['scan']['BRR'][$key] = [
                            'module' => $module,
                            'field' => $condition['variable_name'],
                        ];
                    }
                }

                // Now handle conclusions... these are write ops
                if (isset($ruleset['conclusions']) && is_array($ruleset['conclusions'])) {
                    foreach ($ruleset['conclusions'] as $conclusion) {
                        // These are return value types and need no checking
                        if ($conclusion['conclusion_type'] === 'return') {
                            continue;
                        }

                        // Create a key for this like the others
                        $key = $this->getBusinessRuleKey($data['base_module'], $conclusion['conclusion_value']);

                        // Add this module:field to the stack of WRITE ops checks
                        $return['scan']['BR'][$key] = [
                            'module' => $data['base_module'],
                            'field' => $conclusion['conclusion_value'],
                        ];
                    }
                }
            }
        }

        return $return;
    }

    /**
     * Gets an array index used for stacking invalid fields
     * @param string $module The module for this field
     * @param string $field The field name
     * @return string
     */
    protected function getBusinessRuleKey($module, $field)
    {
        return "$module:$field";
    }


    /**
     * Loads up the package check list
     * @return array
     * @see Scanner::checkPackages()
     */
    protected function loadPackageChecklist()
    {
        if (empty($this->packages)) {
            $this->packages = require __DIR__ . '/package-checklist.php';
        }

        return $this->packages;
    }

    /**
     * Checks for unsupported installed packages.
     */
    protected function checkPackages()
    {
        $this->loadPackageChecklist();

        $this->log('Checking packages');
        $pm = $this->getPackageManager();
        $packages = $pm->getinstalledPackages();
        foreach ($packages as $pack) {
            if ($pack['enabled'] == 'DISABLED') {
                if (isset($this->packages[$pack['name']]['checkDisabled']) &&
                    $this->packages[$pack['name']]['checkDisabled'] === false
                ) {
                    $this->log("Disabled package {$pack['name']} (version {$pack['version']}) detected");
                    continue;
                }
            }

            $this->log("Package {$pack['name']} (version {$pack['version']}) detected");
            if (array_key_exists($pack['name'], $this->packages)) {
                $incompatible = false;
                foreach ($this->packages[$pack['name']] as $req) {
                    if (!is_array($req)) {
                        continue;
                    }
                    if (empty($req['version'])) {
                        $incompatible = true;
                    } elseif ($req['version'] == '*' || $this->versionLessThan($pack['version'], $req['version'])) {
                        $incompatible = true;
                    }
                    if (!empty($req['author'])) {
                        $uh = new UpgradeHistory();
                        $uh->retrieve_by_string_fields(
                            ['name' => $pack['name'], 'version' => $pack['version']],
                            true,
                            false
                        );
                        $manifest = unserialize(base64_decode($uh->manifest), ['allowed_classes' => false]);
                        if (empty($manifest)) {
                            break;
                        }
                        $manifest = $manifest['manifest'];
                        $scp = strcasecmp($manifest['author'], $req['author']);
                        $incompatible = $incompatible && ($req['author'] == '*' || empty($scp));
                    }

                    if (!empty($req['path']) && is_dir($req['path']) &&
                        is_callable(['SugarAutoLoader', 'addDirectory'])
                    ) {
                        SugarAutoLoader::addDirectory($req['path']);
                    }

                    if ($incompatible) {
                        break;
                    }
                }
                if ($incompatible) {
                    $this->updateStatus('incompatIntegration' . (!empty($req['__MetaSuffix']) ? $req['__MetaSuffix'] : ''), $pack['name'], $pack['version']);
                }
            }
        }
    }

    /**
     * Check if $table_name property in bean match table parameter in module/vardefs.php
     * @param $module
     * @return bool
     */
    protected function checkTableName($module)
    {
        $object = $this->getObjectName($module);

        VardefManager::loadVardef($module, $object);
        if (empty($GLOBALS['dictionary'][$object]['table'])) {
            $this->log("Failed to load vardefs for $module:$object");
            return false;
        }

        $seed = BeanFactory::newBean($module);
        if (empty($seed)) {
            $this->log("Failed to instantiate bean for $module, not checking table");
            return false;
        }

        if ($GLOBALS['dictionary'][$object]['table'] !== $seed->getTableName()) {
            $this->updateStatus('badVardefsTableName', $module, $module);
        }
    }

    /**
     * Log upgrades registered for the instance
     */
    protected function listUpgrades()
    {
        $ulist = $this->getInstalledPackagesByType('patch');
        if (empty($ulist)) {
            return;
        }
        foreach ($ulist as $urecord) {
            $this->log("Detected patch: {$urecord->name} version {$urecord->version} status {$urecord->status}");
        }
    }

    /**
     * Dump Scanner issues to log and optional stdout
     */
    public function dumpMeta()
    {
        $this->log('*** START HEALTHCHECK ISSUES ***');
        foreach ($this->getLogMeta() as $key => $entry) {
            $issueNo = $key + 1;
            $this->log(
                " => {$entry['bucket']}: [Issue {$issueNo}][{$entry['flag_label']}][{$entry['report']}][{$entry['id']}][{$entry['title']}] {$entry['descr']}"
            );
        }
        $this->log('*** END HEALTHCHECK ISSUES ***');
    }

    /**
     * Searching line number of value
     * @param string $file File to search in
     * @param string $pattern Value to search
     * @param string optional $directory
     * @return array
     */
    protected function getLineNumberOfPattern($file, $pattern, $directory = '')
    {
        $foundInfo = [];

        $fileContentsLined = file($file);
        $linesFound = preg_grep('/' . preg_quote($pattern, '/') . '/', $fileContentsLined);

        if ($this->safeCount($linesFound) > 0) {
            foreach ($linesFound as $linePosition => $lineContent) {
                $foundInfo['line'] = ((int)$linePosition + 1);
                $foundInfo['directory'] = $directory;
            }
        }
        return $foundInfo;
    }

    /**
     * Scans the custom directory for possible code level incompatability.
     */
    protected function scanCustomDir()
    {
        $this->checkCreateActions();
        $this->checkSidecarJSFiles();
        $this->checkSidecarTemplateFiles();
        $this->checkCustomLessFiles(['custom_themes_dir' => 'themes']);
        $this->log('Checking custom directory for no longer valid code');
        $files = $this->getPhpFiles('custom/');
        foreach ($files as $name => $file) {
            // check for any occurrence of the directories and flag them
            $fileContents = file_get_contents($file);
            $this->scanFileForInvalidReferences($file, $fileContents);
            $this->scanFileForSessionArrayReferences($file, $fileContents);
            $this->scanFileForDeprecatedCode($file, $fileContents);
            $this->scanFileForIncompatibleInheritance($file, $fileContents);
            $this->scanFileForOutdatedDbalUsage($file, $fileContents);
        }
    }

    protected function checkPasswordHashAlgo()
    {
        // cannot put in src/ and rely on autoloader, because autoloader works in context of checked instance, not HC
        require_once __DIR__ . '/Checks/PasswordHashAlgo.php';

        $check = new PasswordHashAlgoCheck();

        if (!$check->check($this->upgrader)) {
            if ($check->isError) {
                $this->updateStatus(
                    'unsupportedConfigParameterPasswordHash',
                    'passwordHash',
                );
            } else {
                $this->updateStatus(
                    'deprecatedConfigParameterPasswordHash',
                    'passwordHash',
                );
            }
        }
    }

    /**
     * This method checks for directories/files that have been moved/removed that are referenced
     * in custom code
     */
    public function scanFileForInvalidReferences($file, $fileContents)
    {
        if (preg_match_all(
            "#(\b(include|require|require_once|include_once)\b[\s('\"]*(.*?);)#",
            $fileContents,
            $m
        )) {
            $vendorFileFound = false;
            $includedVendors = [];
            foreach ($m[1] as $value) {
                foreach ($this->removed_directories as $directory) {
                    if (preg_match(
                        "#(include|require|require_once|include_once)[\s('\"]*({$directory})#",
                        $value
                    ) > 0
                    ) {
                        foreach ($this->specificSugarFiles as $specificSugarFile) {
                            if (preg_match(
                                "#(include|require|require_once|include_once)[\s('\"]*(\b{$specificSugarFile}\b)#",
                                $value
                            ) > 0
                            ) {
                                if (empty($this->specificSugarFilesToFix[$specificSugarFile][$file])) {
                                    $fileInfo = $this->getLineNumberOfPattern($file, $value, $directory);
                                    if ($fileInfo) {
                                        $this->specificSugarFilesToFix[$specificSugarFile][$file] = $fileInfo;
                                    }
                                }
                                break 2;
                            }
                        }

                        $foundVendor = $this->getLineNumberOfPattern($file, $value, $directory);
                        if (!empty($foundVendor)) {
                            $vendorFileFound = true;
                            $includedVendors[] = $foundVendor;
                            break;
                        }
                    }
                }
            }
            if ($vendorFileFound) {
                $this->filesToFix[] = [
                    'file' => $file,
                    'vendors' => $includedVendors,
                ];
            }
            foreach ($this->removed_files as $deletedFile) {
                if (preg_match(
                    "#(include|require|require_once|include_once)[\s('\"]*({$deletedFile})#",
                    $fileContents
                ) > 0
                ) {
                    $this->log("Found $deletedFile in $file");
                    $this->deletedFilesReferenced[] = $file;
                }
            }
        }
    }

    /**
     * This function checks for usage of $_SESSION with known bad array functions. $_SESSION will now be an instance
     * of TrackableArray that will not be compatible. The ArrayFunctions class contains compatible
     * versions of some of these functions.
     *
     * @param $file
     * @param $fileContents
     */
    public function scanFileForSessionArrayReferences($file, $fileContents)
    {
        $array_functions = [
            'array_change_key_case', 'array_chunk', 'array_column', 'array_combine', 'array_count_values',
            'array_diff_assoc', 'array_diff_key', 'array_diff_uassoc', 'array_diff_ukey', 'array_diff',
            'array_fill_keys', 'array_fill', 'array_filter', 'array_flip', 'array_intersect_assoc',
            'array_intersect_key', 'array_intersect_uassoc', 'array_intersect_ukey', 'array_intersect',
            'array_key_exists', 'array_keys', 'array_map', 'array_merge_recursive', 'array_merge', 'array_multisort',
            'array_pad', 'array_pop', 'array_product', 'array_push', 'array_rand', 'array_reduce',
            'array_replace_recursive', 'array_replace', 'array_reverse', 'array_search', 'array_shift', 'array_slice',
            'array_splice', 'array_sum', 'array_udiff_assoc', 'array_udiff_uassoc', 'array_udiff',
            'array_uintersect_assoc', 'array_uintersect_uassoc', 'array_uintersect', 'array_unique', 'array_unshift',
            'array_values', 'array_walk_recursive', 'array_walk', 'array', 'arsort', 'asort', 'in_array', 'is_array',
            'key_exists', 'krsort', 'ksort', 'natcasesort', 'natsort', 'rsort', 'sort', 'uasort', 'uksort', 'usort',
        ];
        if (preg_match_all(
            '/(\w*(array|sort)[\w\s]*)\([^)]*\$_SESSION/',
            $fileContents,
            $m
        )) {
            foreach ($m[1] as $func) {
                if (in_array($func, $array_functions)) {
                    $this->sessionUsages[$file][] = $func;
                }
            }
        }
    }

    /**
     * Gets all relevant deprecated code patterns
     * @param $typeSpecificPatterns
     * @return array
     */
    protected function getDeprecatedPatterns($typeSpecificPatterns)
    {
        return array_merge($typeSpecificPatterns, $this->deprecatedGenericPatterns);
    }

    /**
     * Checks that we don't use classes deprecated/removed in sugar API
     * @param string $file
     * @param string $fileContents
     */
    public function scanFileForDeprecatedCode($file, $fileContents)
    {
        foreach ($this->getDeprecatedPatterns($this->deprecatedPHPCodePatterns) as $pattern => $reportId) {
            if (preg_match($pattern, $fileContents)) {
                $this->filesWithDeprecatedCode[$reportId][] = $file;
            }
        }
    }

    /**
     * Check that customizations don't use known backward-incompatible interfaces
     *
     * @param $file
     * @param $fileContents
     * @return void
     */
    public function scanFileForIncompatibleInheritance($file, $fileContents): void
    {
        // check for usage of Monolog classes, changed in Sugar 12.3.0
        [$version,] = $this->getVersionAndFlavor();
        if (version_compare($version, '12.3.0', '>=')) {
            return;
        }

        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        try {
            $stmts = $parser->parse($fileContents);
        } catch (\PhpParser\Error $error) {
            $this->updateStatus('phpError', 'E_UNKNOWN', $error->getMessage(), $file, $error->getLine());
            return;
        }

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor(new ParentConnectingVisitor());
        $methodSignatureVisitor = $this->getMethodSignatureVisitor($this->inheritanceRules);
        $traverser->addVisitor($methodSignatureVisitor);
        $traverser->traverse($stmts);

        $issues = $methodSignatureVisitor->getIssues();
        if ($this->safeCount($issues) > 0) {
            $this->filesWithIncompatibleInheritance['incompatibleMonologCustomization'][] = $file;
        }
    }

    public function scanFileForOutdatedDbalUsage($file, $fileContents): void
    {
        // 1. cannot put in src/ and rely on autoloader, because autoloader works in context of checked instance, not HC
        // 2. cannot include at the beginning of the file, because at that point autoloader not yet registered, but we
        // need it to load PHPParser classes
        require_once __DIR__ . '/Checks/Dbal.php';

        $check = new DbalHealthCheck();
        $issues = $check->check($fileContents);
        foreach ($issues as $issue) {
            $message = $issue->getMessage();
            $line = $issue->getLine();
            $this->filesWithOutdatedDbal['outdatedDbalUsage'][] = $file . ':' . $line . ': ' . $message;
        }
    }

    protected function updateCustomDirScanStatus()
    {
        if (!empty($this->filesToFix)) {
            $files_to_fix = '';
            foreach ($this->filesToFix as $fileToFix) {
                $files_to_fix .= "{$fileToFix['file']} has the following vendor inclusions: " . PHP_EOL;
                foreach ($fileToFix['vendors'] as $vendor) {
                    $files_to_fix .= " '{$vendor['directory']}' found in line {$vendor['line']}" . PHP_EOL;
                }
            }
            $this->updateStatus('vendorFilesInclusion', $files_to_fix);
        }
        if (!empty($this->deletedFilesReferenced)) {
            $this->updateStatus('deletedFilesReferenced', $this->deletedFilesReferenced);
        }
        if (!empty($this->specificSugarFilesToFix)) {
            $specificFiles = '';
            foreach ($this->specificSugarFilesToFix as $fileToFix => $filesInfo) {
                $specificFiles .= "'$fileToFix' in: " . PHP_EOL;
                foreach ($filesInfo as $file => $info) {
                    $specificFiles .= " '$file' file in line {$info['line']}" . PHP_EOL;
                }
            }
            $this->updateStatus('sugarSpecificFilesInclusion', $specificFiles);
        }
        if (!empty($this->sessionUsages)) {
            $filesWithSession = '';
            foreach ($this->sessionUsages as $file => $func) {
                $arrayFunctions = implode(', ', $func);
                $filesWithSession .= "'$file' using \$_SESSION with array function '$arrayFunctions'. " . PHP_EOL;
            }
            $this->updateStatus('arraySessionUsage', $filesWithSession);
        }
        foreach ($this->filesWithDeprecatedCode as $reportId => $files) {
            $this->updateStatus($reportId, array_unique($files));
        }
        foreach ($this->filesWithIncompatibleInheritance as $reportId => $files) {
            $this->updateStatus($reportId, array_unique($files));
        }
        foreach ($this->filesWithOutdatedDbal as $reportId => $files) {
            $this->updateStatus($reportId, array_unique($files));
        }
    }


    /**
     * Scan individual module
     * @param string $module
     * @return boolean Was it a real module?
     */
    protected function scanModule($module)
    {
        if (empty($this->beanList[$module])) {
            // absent from module list, not an actual module
            // TODO: we may still want to check for extensions here?
            // TODO: check for view defs for modules not in BeanList?
            $this->log("$module is not in Bean List, may be not a real module");
            return false;
        }

        if (in_array($module, $this->unsupportedModules)) {
            $this->updateStatus('incompatModule', $module);
            return;
        }
        // TODO: check if module table is OK
        if (!in_array($module, $this->excludeModules)) {
            $this->checkTableName($module);
        }

        $isNewModule = false;
        if ($this->isNewModule($module)) {
            $this->updateStatus('notStockModule', $module);
            // not a stock module, check if it's working at least with BWC
            $this->checkMBModule($module);
            $isNewModule = true;
        } else {
            $this->checkStockModule($module);
        }
        $options = [
            'module' => $module,
            'isNewModule' => $isNewModule,
        ];
        $this->checkCreateActions($options);
        $this->checkSidecarJSFiles($options);
        $this->checkSidecarTemplateFiles($options);

        /*
         * Module specific checks
         */
        switch ($module) {
            case 'KBContents':
                $this->runKBContentsModuleScanner();
                break;
        }
    }

    /**
     * Check if KBContents module is ok.
     */
    private function runKBContentsModuleScanner()
    {
        $sugar_version = '';
        include 'sugar_version.php';
        // only if we are updating 7.7
        if (!(version_compare($sugar_version, '7.7', '>=') && version_compare($sugar_version, '7.8a', '<'))) {
            // no checks needed
            return;
        }

        $customRecordViewFileName = 'custom/modules/KBContents/clients/base/views/record/record.php';
        // check if custom file exists
        if (!file_exists($customRecordViewFileName)) {
            // no warnings
            return;
        }

        $viewdefs = [];
        require $customRecordViewFileName;
        // search for htmleditable_tinymce field
        foreach ($viewdefs['KBContents']['base']['view']['record']['panels'] as $panel) {
            if (!isset($panel['fields'])) {
                continue;
            }
            foreach ($panel['fields'] as $fieldSets) {
                if (!isset($fieldSets['fields'])) {
                    continue;
                }
                foreach ($fieldSets['fields'] as $field) {
                    if ($field['type'] == 'htmleditable_tinymce') {
                        // customization exists
                        if (!empty($field['tinyConfig'])) {
                            /*
                             * update health check status
                             */
                            $this->updateStatus('customTinyMCEConfig', $customRecordViewFileName);
                            return;
                        }
                    }
                }
            }
        }
    }

    /**
     * Get name of the object
     * @param string $module
     * @return string|null
     */
    protected function getObjectName($module)
    {
        if (!empty($this->objectList[$module])) {
            return $this->objectList[$module];
        }
        if (!empty($this->beanList[$module])) {
            return $this->beanList[$module];
        }
        return null;
    }

    /**
     * Check if there is a create-actions customization on this instance. By
     * default, checks the clients/ folder for these customizations.
     *
     * @param array $options {
     *     Optional hash that defines which module folders should be scanned for
     *     create-actions components. If passed, the clients/ folder will not
     *     be scanned.
     *
     * @type string $module The module to scan the custom/$module/clients/*
     *       directory with.
     * @type boolean $isNewModule `true` to scan both the
     *       custom/$module/clients/* and modules/$module/clients/* directories.
     *       If `false` or no value passed, only the custom/$module/clients/*
     *       directory will be scanned.
     * }
     */
    protected function checkCreateActions($options = [])
    {
        $files = [];
        $createActionsPath = 'clients' . DIRECTORY_SEPARATOR .
            '*' . DIRECTORY_SEPARATOR .
            '{layouts,views}' . DIRECTORY_SEPARATOR .
            'create-actions' . DIRECTORY_SEPARATOR .
            'create-actions.*';

        if (!empty($options['module'])) {
            $this->log("Checking for customized create-actions components in custom/modules/{$options['module']}");
            $files = glob(
                'custom' . DIRECTORY_SEPARATOR .
                'modules' . DIRECTORY_SEPARATOR .
                $options['module'] . DIRECTORY_SEPARATOR .
                $createActionsPath,
                GLOB_BRACE
            );

            if (!empty($options['isNewModule'])) {
                $this->log("Checking for customized create-actions components in modules/{$options['module']}");
                $files = array_merge($files, glob(
                    'modules' . DIRECTORY_SEPARATOR .
                    $options['module'] . DIRECTORY_SEPARATOR .
                    $createActionsPath,
                    GLOB_BRACE
                ));
            }
        } else {
            $this->log('Checking for customized create-actions components in custom/clients');
            $files = glob(
                'custom' . DIRECTORY_SEPARATOR .
                $createActionsPath,
                GLOB_BRACE
            );
        }

        if (!empty($files)) {
            $formatted = implode(', ', $files);
            $this->log('Found custom create-actions components');
            $this->updateStatus('hasCustomCreateActions', $formatted);
        }
    }

    /**
     * Check sidecar javascript files for deprecated code and removed files
     * @param array $options
     */
    protected function checkSidecarJSFiles($options = [])
    {
        $files = $this->getSidecarFiles('js', $options);

        if (!empty($files)) {
            foreach ($files as $file) {
                $this->scanFileForDeprecatedJSCode($file, file_get_contents($file));
            }

            foreach ($this->removedSidecarFiles as $deletedFile) {
                if (in_array($deletedFile, $files)) {
                    $this->log("found deleted file: $deletedFile");
                    $this->deletedFilesReferenced[] = $deletedFile;
                }
            }
        }
    }

    /**
     * Check sidecar template files for deprecated code
     * @param array $options
     */
    protected function checkSidecarTemplateFiles($options = [])
    {
        $files = $this->getSidecarFiles('hbs', $options);

        if (!empty($files)) {
            foreach ($files as $file) {
                $this->scanFileForDeprecatedHBSCode($file, file_get_contents($file));
            }
        }
    }

    /**
     * Gets the string to match sidecar files of the given extension
     * @param $ext string extension to search for (ex: 'js', 'hbs')
     * @return string
     */
    protected function getSidecarPathForExtension($ext)
    {
        return 'clients' . DIRECTORY_SEPARATOR .
            '*' . DIRECTORY_SEPARATOR .
            '{layouts,views,fields}' . DIRECTORY_SEPARATOR .
            '*' . DIRECTORY_SEPARATOR .
            '*.' . $ext;
    }

    /**
     * Gets the string to match LESS files in a certain custom directory.
     * @param $themeDir string defines the path relative to custom/ where the LESS files will be. Ex. "themes" will
     * search in custom/themes.
     * @return string
     */
    protected function getPathForCustomLESSFile(string $themeDir): string
    {
        return $themeDir . DIRECTORY_SEPARATOR . '*.less';
    }

    /**
     * Check custom LESS files for deprecated color variables.
     * @param array $options
     * @return void
     */
    protected function checkCustomLessFiles(array $options = [])
    {
        $files = $this->getSidecarFiles('less', $options);

        foreach ($files as $file) {
            $this->scanFileForDeprecatedLESSColorVariables($file, file_get_contents($file));
        }
    }

    /**
     * Gets sidecar files of the given extension. If the custom_themes_dir option is present, getCustomLESSFiles is
     * given priority.
     * @param $ext
     * @param $options
     * @return array
     */
    protected function getSidecarFiles($ext, $options)
    {
        if (!empty($options['custom_themes_dir'])) {
            return $this->getCustomLESSFiles($options['custom_themes_dir']);
        }

        $path = $this->getSidecarPathForExtension($ext);
        $extLabel = sugarStrToUpper($ext);

        if (!empty($options['module'])) {
            $this->log("Checking for deprecated/removed Sidecar {$extLabel} in custom/modules/{$options['module']}");
            $files = glob(
                'custom' . DIRECTORY_SEPARATOR .
                'modules' . DIRECTORY_SEPARATOR .
                $options['module'] . DIRECTORY_SEPARATOR .
                $path,
                GLOB_BRACE
            );

            if (!empty($options['isNewModule'])) {
                $this->log("Checking for deprecated/removed Sidecar {$extLabel} in modules/{$options['module']}");
                $files = array_merge($files, glob(
                    'modules' . DIRECTORY_SEPARATOR .
                    $options['module'] . DIRECTORY_SEPARATOR .
                    $path,
                    GLOB_BRACE
                ));
            }
        } else {
            $this->log("Checking for deprecated/removed Sidecar {$extLabel} in custom/clients");
            $files = glob(
                'custom' . DIRECTORY_SEPARATOR .
                $path,
                GLOB_BRACE
            );
        }

        return $files;
    }

    /**
     * Returns the custom LESS files present at the custom_themes_dir location.
     * @param $customThemesDir string defines a path for where the custom themes directory is.
     * @return array|false
     */
    protected function getCustomLESSFiles(string $customThemesDir)
    {
        $this->log("Checking for deprecated/removed LESS variables in custom/$customThemesDir");
        return glob(
            'custom' . DIRECTORY_SEPARATOR . $this->getPathForCustomLESSFile($customThemesDir),
            GLOB_BRACE
        );
    }

    /**
     * Checks that we don't use classes deprecated/removed in sugar API
     * @param string $file
     * @param string $fileContents
     */
    public function scanFileForDeprecatedJSCode($file, $fileContents)
    {
        foreach ($this->getDeprecatedPatterns($this->deprecatedJsAPIPatterns) as $pattern => $reportId) {
            $matches = [];
            if ($val = preg_match($pattern, $fileContents, $matches)) {
                $this->log("Found $matches[1] in $file");
                $this->filesWithDeprecatedCode[$reportId][] = $file;
            }
        }
        foreach ($this->removedSidecarClasses as $className) {
            if (preg_match('/\s+extendsFrom:\s*[\'"]' . $className . '[\'"]/', $fileContents)) {
                $this->log("Found $className in $file");
                $this->filesWithDeprecatedCode['extendsFromRemovedSidecarClass'][] = $file;
            }
        }
    }

    /**
     * Checks HBS template files for deprecated code
     * @param string $file
     * @param string $fileContents
     */
    public function scanFileForDeprecatedHBSCode($file, $fileContents)
    {
        foreach ($this->getDeprecatedPatterns($this->deprecatedHBSPatterns) as $pattern => $reportId) {
            $matches = [];
            if ($val = preg_match($pattern, $fileContents, $matches)) {
                $this->log("Found $matches[1] in $file");
                $this->filesWithDeprecatedCode[$reportId][] = $file;
            }
        }
    }

    /**
     * Check LESS files for deprecated color variables
     * @param $file
     * @param $fileContents
     * @return void
     */
    public function scanFileForDeprecatedLESSColorVariables($file, $fileContents)
    {
        foreach ($this->getDeprecatedPatterns($this->deprecatedLESSColorVariables) as $pattern => $reportId) {
            $matches = [];
            if ($val = preg_match($pattern, $fileContents, $matches)) {
                $this->log("Found $matches[0] in $file");
                $this->filesWithDeprecatedCode[$reportId][] = $file;
            }
        }
    }

    /**
     * Do checks for ModuleBuilder modules
     * @param string $module
     */
    protected function checkMBModule($module)
    {
        if (!empty($this->newModules[$module])) {
            // we have a name clash
            $this->updateStatus('sameModuleName', $module);
        }

        // Check if ModuleBuilder module needs to be run as BWC
        // Checks from 6_ScanModules. 'isSidecarModule' is added later - @see CRYS-1068.
        $bwc = false;
        if (!$this->isMBModule($module) && !$this->isSidecarModule($module)) {
            $bwc = true;
            $this->updateStatus('toBeRunAsBWC', $module);
        } else {
            $this->log("$module is upgradeable MB module");
        }

        $objectName = $this->getObjectName($module);
        // check for subpanels since BWC subpanels can be used in non-BWC modules
        $defs = $this->getPhpFiles("modules/$module/metadata/subpanels");
        if (!empty($defs) && !empty($this->beanList[$module])) {
            foreach ($defs as $deffile) {
                $this->checkListFields($deffile, 'subpanel_layout', 'list_fields', $module, $objectName);
            }
        }

        $defs = $this->getPhpFiles("custom/modules/$module/metadata/subpanels");
        if (!empty($defs) && !empty($this->beanList[$module])) {
            $this->log("$module has custom subpanels");
            foreach ($defs as $deffile) {
                $this->checkCustomCode($deffile, 'subpanel_layout', "modules/$module/metadata/" . basename($deffile));
                $this->checkListFields($deffile, 'subpanel_layout', 'list_fields', $module, $objectName);
            }
        }

        // check for output in logic hooks
        // if there is some, we'd need to put it to custom
        // since upgrader does not handle it, we have to manually BWC the module
        $this->checkHooks($module, HealthCheckScannerMeta::CUSTOM, $bwc);
    }

    /**
     * Check if stock module is a BWC module
     * @param string $module
     */
    protected function isStockBWCModule($module)
    {
        return isset($this->bwcModulesHash[$module]);
    }

    /**
     * Var names for various viewdefs
     * Isn't it fun that we use so many differen ones?
     * @var array
     */
    protected $vardefnames = [
        'SearchFields.php' => 'searchFields',
        'listviewdefs.php' => 'listViewDefs',
        'popupdefs.php' => 'popupMeta',
        'searchdefs.php' => 'searchdefs',
        'subpaneldefs.php' => 'layout_defs',
        'wireless.subpaneldefs.php' => 'layout_defs',

    ];

    /**
     * Check stock module for customizations not compatible with 7
     * @param string $module
     */
    protected function checkStockModule($module)
    {
        $bwc = $this->isStockBWCModule($module);

        $history = $this->getPhpFiles("custom/history/modules/$module");
        if (!empty($history)) {
            $this->updateStatus('hasStudioHistory', $module);
        }

        $objectName = $this->getObjectName($module);

        // check vardefs for HTML and bad names
        if (!$bwc && $objectName) {
            $this->checkVardefs($module, $objectName, true, HealthCheckScannerMeta::CUSTOM);
        }

        // Check for extension files
        $extfiles = $this->getPhpFiles("custom/Extension/modules/$module/Ext");
        if (!empty($extfiles)) {
            $this->updateStatus('hasExtensions', $module, $extfiles);
        }
        // skip check for output for bwc module
        if (!$bwc) {
            foreach ($extfiles as $phpfile) {
                $this->checkFileForOutput($phpfile);
            }
        }

        // Check custom vardefs
        $defs = $this->getPhpFiles("custom/Extension/modules/$module/Ext/Vardefs");
        if (!empty($defs)) {
            $this->updateStatus('hasCustomVardefs', $module);
            foreach ($defs as $deffile) {
                $this->checkCustomCode($deffile, 'dictionary', "modules/$module/vardefs.php");
                $this->checkForOtherModuleDefinition($deffile, 'dictionary', $objectName);
            }
        }

        // check layout defs
        $defs = $this->getPhpFiles("custom/Extension/modules/$module/Ext/Layoutdefs");
        if (!empty($defs)) {
            $this->updateStatus('hasCustomLayoutdefs', $module);
            foreach ($defs as $deffile) {
                $this->checkCustomCode($deffile, 'layout_defs', "modules/$module/metadata/subpaneldefs.php");
                $this->checkSubpanelLayoutDefs($module, $objectName, $deffile);
            }
        }

        // check custom viewdefs
        $defs = array_filter(
            $this->getPhpFiles("custom/modules/$module/metadata"),
            [$this, 'filterViewDefs']
        );

        if ($module == 'Connectors') {
            $pos = array_search('custom/modules/Connectors/metadata/connectors.php', $defs);
            if ($pos !== false) {
                unset($defs[$pos]);
                // TODO: any checks for connectors.php?
            }
            $pos = array_search('custom/modules/Connectors/metadata/display_config.php', $defs);
            if ($pos !== false) {
                unset($defs[$pos]);
                // TODO: any checks for display_config.php?
            }
        }

        // check viewdefs
        if (!empty($defs)) {
            $this->updateStatus('hasCustomViewdefs', $module);
            foreach ($defs as $deffile) {
                if (strpos($deffile, '/subpanels/') !== false) {
                    // special case for subpanels, since subpanels are special
                    $base = basename(dirname($deffile)) . '/' . basename($deffile);
                    $defsname = 'subpanel_layout';
                } else {
                    $base = basename($deffile);
                    if (!empty($this->vardefnames[$base])) {
                        $defsname = $this->vardefnames[$base];
                    } else {
                        $defsname = 'viewdefs';
                    }
                }
                if (!$bwc) {
                    $this->checkCustomCode($deffile, $defsname, "modules/$module/metadata/$base", $history);
                }
                // For stock modules, check subpanels and also list views for non-bwc modules
                if ($defsname == 'subpanel_layout') {
                    // checking also BWC since Sugar 7 module can have subpanel for BWC module
                    $this->checkListFields($deffile, $defsname, 'list_fields', $module, $objectName);
                }
            }
        }

        if (!$bwc) {
            // check for custom views
            $defs = array_filter(
                $this->getPhpFiles("custom/modules/$module/views"),
                [$this, 'sideQuickCreateFilter']
            );
            if (!empty($defs)) {
                $this->updateStatus('hasCustomViews', $module, $defs);
            }
            $defs = array_filter(
                $this->getPhpFiles("modules/$module/views"),
                [$this, 'sideQuickCreateMD5Filter']
            );
            if (!empty($defs)) {
                $this->updateStatus('hasCustomViewsModDir', $module, $defs);
            }

            // Check custom extensions which aren't Studio
            $badExts = [
                'ActionViewMap',
                'ActionFileMap',
                'ActionReMap',
                'EntryPointRegistry',
                'FileAccessControlMap',
                'WirelessModuleRegistry',
                'JSGroupings',
            ];
            $badExts = array_flip($badExts);
            foreach ($this->glob("custom/modules/$module/Ext/*") as $extdir) {
                if (isset($badExts[basename($extdir)])) {
                    $extfiles = glob("$extdir/*");
                    foreach ($extfiles as $k => $file) {
                        if ($this->isEmptyFile($file)) {
                            unset($extfiles[$k]);
                        }
                    }
                    if (!empty($extfiles)) {
                        $this->updateStatus('extensionDir', $extdir);
                    }
                }
            }
        }
        // check logic hooks for module
        $this->checkHooks($module, $bwc ? HealthCheckScannerMeta::CUSTOM : HealthCheckScannerMeta::MANUAL, $bwc);
    }

    /**
     * Callback for filter viewdefs.
     * @param array $file
     * @return bool
     */
    public function filterViewDefs($file)
    {
        $filesToExclude = [
            'quickcreatedefs.php', // CRYS-426 - exclude quickcreatedefs.php
            'wireless.editviewdefs.php',
            'wireless.detailviewdefs.php',
            'wireless.listviewdefs.php',
            'convertdefs.php',     // CRYS-536 - exclude */Leads/metadata/convertdefs.php
        ];
        return !in_array(basename($file), $filesToExclude);
    }

    /**
     * Callback to filter metadata file based on it's name.
     * @param string $file
     * @return bool
     */
    public function sideQuickCreateFilter($file)
    {
        return basename($file) != 'view.sidequickcreate.php';
    }

    /**
     * Callback to filter metadata file based on it's name and MD5 hash.
     * @param string $file
     * @return bool
     */
    public function sideQuickCreateMD5Filter($file)
    {
        $result = $this->sideQuickCreateFilter($file);
        return $result && !$this->isStockFile($file);
    }

    /**
     * Callback to filter module name from it's path.
     * @param string $file
     * @return string
     */
    public function cutOffModuleFromName($file)
    {
        return substr($file, 8); /* cut off modules/ */
    }

    /**
     * Make sure glob always returns array
     *
     * @param $pattern
     * @return array
     */
    protected function glob($pattern)
    {
        $dirs = glob($pattern);
        return ($dirs ?: []);
    }

    /**
     * Types that are BLOBs in the DB
     * @var array
     */
    protected $blob_types = ['text', 'longtext', 'multienum', 'html', 'blob', 'longblob'];

    /**
     * Check if any original vardef changed type
     * @param string $module
     * @param string $object
     */
    protected function checkVardefTypeChange($module, $object)
    {
        if (!file_exists("modules/$module/vardefs.php")) {
            // can't find original vardefs, don't mess with it
            return;
        }
        $full_vardefs = $GLOBALS['dictionary'][$object];
        unset($GLOBALS['dictionary'][$object]);
        global $dictionary;
        include "modules/$module/vardefs.php";
        // load only original vardefs
        if (!empty($GLOBALS['dictionary'][$object])) {
            $original_vardefs = $GLOBALS['dictionary'][$object];
        } else {
            return;
        }
        // return vardefs back to old state
        $GLOBALS['dictionary'][$object] = $full_vardefs;
        $original_vardefs['fields'] = (is_array($original_vardefs['fields'])) ? $original_vardefs['fields'] : [];
        foreach ($original_vardefs['fields'] as $name => $def) {
            if (empty($def['type']) || empty($def['name'])) {
                continue;
            }
            if (!empty($def['source']) && $def['source'] != 'db') {
                continue;
            }
            if (empty($full_vardefs['fields'][$name])) {
                continue;
            }
            $real_type = $this->db->getFieldType($full_vardefs['fields'][$name]);
            $original_type = $this->db->getFieldType($def);
            if (empty($real_type)) {
                // If we can't find the type, this is some serious breakage
                $this->updateStatus('fieldTypeMissing', $module, $name);
                continue;
            }
            if (!in_array($real_type, $this->blob_types)) {
                // Per ENGRD-263, we are only interested in changes to blob type
                continue;
            }
            if (!in_array($original_type, $this->blob_types)) {
                // We have changed from non-blob type to blob type, not good
                $this->updateStatus('typeChange', $module, $name, $original_type, $real_type);
            }
        }
    }

    /**
     * Load definition of certain var from file
     * @param string $deffile
     * @param string $varname
     * @return array
     */
    protected function loadFromFile($deffile, $varname)
    {
        if (!file_exists($deffile)) {
            return [];
        }
        $l = new FileLoaderWrapper();
        $res = $l->loadFile($deffile, $varname);
        if (is_null($res)) {
            $this->log("Weird, loaded $deffile but no $varname there");
            return [];
        }
        if ($res === false) {
            $this->updateStatus('thisUsage', $deffile);
        }
        return $res;
    }

    /**
     * Check if compatible DB driver is used
     */
    protected function checkDbDriver()
    {
        if ($this->db->variant == 'mysql') {
            $this->updateStatus('unsupportedDbDriver', 'mysql', 'mysqli');
        } elseif ($this->db->variant == 'mssql' || $this->db->variant == 'freetds') {
            $this->updateStatus('unsupportedDbDriver', 'mssql', 'sqlsrv');
        }
    }

    /**
     * Check if files of Elastic customization exist.
     * Files include:
     * custom/include/SugarSearchEngine/Elastic/SugarSearchEngineElastic.php
     * custom/include/SugarSearchEngine/Elastic/SugarSearchEngineElasticMapping.php
     * custom/include/SugarSearchEngine/Elastic/SugarSearchEngineElasticIndexStrategyXXX.php
     * custom/include/SugarSearchEngine/Elastic/Facets/FacetYYY.php
     * custom/include/SugarSearchEngine/SugarSearchEngineQueueManager.php
     */
    protected function checkCustomElastic()
    {
        $this->log('Checking the files of Elastic Search customization');
        $baseDir = 'custom/include/SugarSearchEngine/';
        $fileNames = [
            'Elastic/SugarSearchEngineElastic.php',
            'Elastic/SugarSearchEngineElasticMapping.php',
            'Elastic/SugarSearchEngineElasticIndexStrategy*.php',
            'Elastic/Facets/Facet*.php',
            'SugarSearchEngineQueueManager.php',
        ];
        $files = [];
        foreach ($fileNames as $fileName) {
            $files = array_merge($files, glob($baseDir . $fileName));
        }
        if (!empty($files)) {
            $this->updateStatus('foundCustomElastic', $files);
        }
    }

    /**
     * Look for custom code in array of defs
     * @param array $path path through the defs so far
     * @param array $defs Defs to be checked
     */
    protected function lookupCustomCode($path, $defs, $codes)
    {
        foreach ($defs as $key => $value) {
            if ($key === 'customCode' && !empty($value)) {
                $codes[$value][] = $path;
            } elseif (is_array($value)) {
                $codes = $this->lookupCustomCode($path . $key . ':', $value, $codes);
            }
        }
        return $codes;
    }

    /**
     * Checking if module contains the definition of another module
     * @param $file - vardefs file path
     * @param $variable - variable to get from vardefs
     * @param $object - module object name
     */
    protected function checkForOtherModuleDefinition($file, $variable, $object)
    {
        $this->log("Checking $file for other module definition");
        $definition = $this->loadFromFile($file, $variable);

        if (empty($definition)) {
            return;
        }
        $flippedModules = array_merge(array_flip($this->beanList), array_flip($this->objectList));
        $moduleName = $flippedModules[$object];

        foreach ($definition as $key => $data) {
            if ($key !== $object) {
                $foundName = $flippedModules[$key] ?? $key;
                $this->updateStatus('foundOtherModuleVardefs', $moduleName, $foundName, $file);
            }
        }
    }

    /**
     * Check defs for customCode entries
     * @param string $deffile Filename for definitions file
     * @param string $varname Variable to get defs from
     * @param string $original Original defs file
     * @param array $history Studio history files
     */
    protected function checkCustomCode($deffile, $varname, $original, $history = [])
    {
        $this->log("Checking $deffile for custom code");
        $defs = $this->loadFromFile($deffile, $varname);
        if (empty($defs)) {
            return;
        }

        $origdefs = $this->loadFromFile($original, $varname);

        $defs_code = $this->lookupCustomCode('', $defs, []);
        $orig_code = $this->lookupCustomCode('', $origdefs, []);
        $foundCustomCode = [];
        foreach ($defs_code as $code => $places) {
            if (!isset($orig_code[$code])) {
                $foundCustomCode[$code] = $places;
            }
        }

        // We found something, do more precise check through all available history
        if (!empty($foundCustomCode) && !empty($history)) {
            $historyFiles = [];
            foreach ($history as $key => $file) {
                if (strpos(basename($file), basename($deffile, '.php')) !== false) {
                    $historyFiles[$key] = $file;
                }
            }

            $allHistoryCode = [];
            foreach ($historyFiles as $file) {
                //for history files check internal functions and replace them with random names CRYS-498
                $replacedNames = [];
                $tmpName = tempnam(sys_get_temp_dir(), $file);
                if ($tmpName && is_writable($tmpName) && file_exists($file)) {
                    $tmpContents = file_get_contents($file);
                    $matches = [];
                    if (preg_match_all('/function\s+(\w+)\s*\(/', $tmpContents, $matches) && isset($matches[1])) {
                        $tmpMatch = [];
                        foreach ($matches[1] as $key => $value) {
                            $tmpMatch[$key] = $replacedNames[] = $value . md5($tmpName);
                        }
                        $tmpContents = str_replace($matches[1], $tmpMatch, $tmpContents);

                        if (file_put_contents($tmpName, $tmpContents)) {
                            $file = $tmpName;
                        }
                    }
                }

                $historyDefs = $this->loadFromFile($file, $varname);
                // Make sure we got initial values, but not replaced with md5 ones
                $historyDefs = json_decode(str_replace($replacedNames, $matches[1], json_encode($historyDefs)), true);

                if ($tmpName) {
                    @unlink($tmpName);
                }

                $historyCode = $this->lookupCustomCode('', $historyDefs, []);
                $allHistoryCode = array_merge($allHistoryCode, $historyCode);
            }

            $foundCustomCode = array_diff_key($foundCustomCode, $allHistoryCode);
        }

        // finally output status, if there is any
        foreach ($foundCustomCode as $code => $places) {
            $this->updateStatus('foundCustomCode', $code, $places, $deffile);
        }
    }

    /**
     * Check if the link name is valid
     * @param string $module
     * @param string $object
     * @param string $link Link name
     * @return boolean
     */
    protected function isValidLink($module, $object, $link)
    {
        if (empty($GLOBALS['dictionary'][$object]['fields'])) {
            VardefManager::loadVardef($module, $object);
        }
        if (empty($GLOBALS['dictionary'][$object]['fields'])) {
            // weird, we could not load vardefs for this link
            $this->log("Failed to load vardefs for $module:$object");
            return false;
        }
        if (empty($GLOBALS['dictionary'][$object]['fields'][$link]) ||
            empty($GLOBALS['dictionary'][$object]['fields'][$link]['type']) ||
            $GLOBALS['dictionary'][$object]['fields'][$link]['type'] != 'link'
        ) {
            return false;
        }
        return true;
    }

    /**
     * Check subpanel defs
     * @param string $module Module for subpanel
     * @param string $deffile Filename for definitions file
     */
    protected function checkSubpanelLayoutDefs($module, $object, $deffile)
    {
        $layoutDefs = $this->loadFromFile($deffile, 'layout_defs');
        // get defs regardless of the module_name since it can be plural or singular, but we don't care here
        if (!$layoutDefs) {
            return;
        }
        $defs = $layoutDefs[key($layoutDefs)];
        if (empty($defs['subpanel_setup'])) {
            return;
        }
        $this->log("Checking subpanel file $deffile");
        // check 'get_subpanel_data' contains not applicable in Sidecar 'function:...' value
        foreach ($defs['subpanel_setup'] as $panel) {
            if (!empty($panel['module']) && ($panel['module'] == 'Activities' || $panel['module'] == 'History')
                && isset($panel['collection_list'])
            ) {
                // skip activities/history, upgrader will take care of them
                continue;
            }

            // check subpanel module. This param should refer to existing module
            if (!empty($panel['module']) && empty($this->beanList[$panel['module']])) {
                $this->updateStatus('subpanelLinkNonExistModule', $panel['module'], $deffile);
            }

            if (!empty($panel['get_subpanel_data']) && strpos($panel['get_subpanel_data'], 'function:') !== false) {
                $this->updateStatus('subPanelWithFunction', $deffile);
            }
            if (!empty($panel['get_subpanel_data']) && !$this->isValidLink(
                $module,
                $object,
                $panel['get_subpanel_data']
            )
            ) {
                $this->updateStatus('badSubpanelLink', $panel['get_subpanel_data'], $deffile);
            }
        }
    }

    protected $knownWidgetClasses = [
        'SubPanelDetailViewLink',
        'SubPanelEmailLink',
        'SubPanelEditButton',
        'SubPanelRemoveButton',
        'SubPanelIcon',
        'SubPanelDeleteButton',
    ];

    /**
     * Check list view type metadata for bad fields
     * @param string $deffile Filename for definitions file
     * @param string $varname Variable to get defs from
     * @param string $subvarname Section in defs where list fields are stored
     * @param string $module Module name
     * @param string $object Object name
     * @param string $status Status to set if something is wrong
     */
    protected function checkListFields($deffile, $varname, $subvarname, $module, $object)
    {
        if (!$object) {
            return true;
        }

        $this->log("Checking $deffile for bad list fields");

        if (empty($GLOBALS['dictionary'][$object])) {
            VardefManager::loadVardef($module, $object);
        }

        if (empty($GLOBALS['dictionary'][$object]['fields'])) {
            // weird module, no fields, skip
            return true;
        }
        $vardefs = $GLOBALS['dictionary'][$object]['fields'];

        $defs = $this->loadFromFile($deffile, $varname);
        if (empty($defs)) {
            return true;
        }
        if ($subvarname) {
            if (empty($defs[$subvarname])) {
                return true;
            }
            $defs = $defs[$subvarname];
        }
        foreach ($defs as $key => $data) {
            if (!empty($data['usage'])) {
                // it's a query field, skip it, converter will take care of them
                continue;
            }
            $key = strtolower($key);
            if (!empty($data['widget_class']) && !in_array($data['widget_class'], $this->knownWidgetClasses)) {
                if (!file_exists("include/generic/SugarWidgets/SugarWidget{$data['widget_class']}.php")) {
                    $this->updateStatus('unknownWidgetClass', $data['widget_class'], $key, $module, $deffile);
                }
            }
            // Unknown fields handled by CRYS-36, so no more checks here
        }
    }

    /**
     * Check logic hooks for module
     * @param string $module
     * @param string $status
     * @param bool $bwc
     */
    protected function checkHooks($module, $status = HealthCheckScannerMeta::MANUAL, $bwc = false)
    {
        $this->log("Checking hooks for $module");
        $hook_files = [];
        $this->extractHooks("custom/modules/$module/logic_hooks.php", $hook_files);
        $this->extractHooks("custom/modules/$module/Ext/LogicHooks/logichooks.ext.php", $hook_files);

        foreach ($hook_files as $hookname => $hooks) {
            foreach ($hooks as $hook_data) {
                $hookDescription = (!empty($hook_data[1])) ? $hook_data[1] : '';
                $this->log("Checking module hook $hookname: $hookDescription");
                if (!$this->isValidHookFile($hook_data)) {
                    $this->updateStatus('badHookFile', $hookname, '');
                } elseif (!$bwc) {
                    $this->checkFileForOutput($hook_data[2], $hook_data[3]);
                }
            }
        }
    }

    /**
     * Test if hook definition uses a valid file
     *
     * @param array $hookDefinition
     * @return bool
     */
    protected function isValidHookFile(array $hookDefinition) : bool
    {
        $file = $hookDefinition[2] ?? '';
        $class = $hookDefinition[3] ?? '';

        if (empty($file)) {
            if (!empty($class) && class_exists($class)) {
                return true;
            }

            return false;
        }

        return file_exists($file);
    }

    /**
     * Get list of existing modules
     * @return array
     */
    protected function getModuleList()
    {
        $this->loadModulesList();
        $this->setupHealthCheckModule();

        return array_map(
            [$this, 'cutOffModuleFromName'],
            glob('modules/*', GLOB_ONLYDIR)
        );
    }

    protected function loadModulesList()
    {
        $beanList = $beanFiles = $objectList = [];
        require 'include/modules.php';
        $this->beanList = $beanList;
        $this->beanFiles = $beanFiles;
        $this->objectList = $objectList;
    }

    /**
     * Make scanner ignore health check module
     */
    public function setupHealthCheckModule()
    {
        $this->beanList['HealthCheck'] = $this->healthCheckModule['bean'];
        $this->beanFiles['HealthCheck'] = $this->healthCheckModule['file'];
        $this->md5_files[$this->healthCheckModule['md5']] = md5('HealthCheck');
    }

    /**
     * Initialize instance environment
     * @return bool False means this instance is messed up
     */
    protected function init()
    {
        $this->db = DBManagerFactory::getInstance();

        $md5_string = [];
        if (!file_exists('files.md5')) {
            return $this->fail('files.md5 not found');
        }

        require 'files.md5';
        $this->md5_files = $md5_string;
        $this->bwcModulesHash = array_flip($this->bwcModules);

        // turn on AdminWork
        if (class_exists(\Sugarcrm\Sugarcrm\AccessControl\AccessControlManager::class)) {
            Sugarcrm\Sugarcrm\AccessControl\AccessControlManager::instance()->setAdminWork(true, true);
        }

        return true;
    }

    /**
     * Is $module a new module or standard Sugar module?
     * @param string $module
     * @return boolean $module is new?
     */
    protected function isNewModule($module)
    {
        $object = $this->beanList[$module];
        if (empty($this->beanFiles[$object])) {
            // no bean file - check directly
            foreach ($this->glob("modules/$module/*") as $file) {
                // if any file from this dir mentioned in md5 - not a new module
                if ($this->isStockFile($file)) {
                    return false;
                }
            }
            return true;
        }

        if (!$this->isStockFile($this->beanFiles[$object])) {
            // no mention of the bean in files.md5 - new module
            return true;
        }

        return false;
    }

    protected function scanCustomPhpFiles(array $callbacks)
    {
        $this->log('Scanning custom PHP files');

        $files = $this->getPhpFiles('.');

        foreach ($files as $file) {
            $file = substr($file, 2);
            if ($this->isStockFile($file)) {
                continue;
            }

            if (strpos($file, 'cache/') === 0) {
                continue;
            }

            if (strpos($file, 'upload/') === 0) {
                continue;
            }

            // ignore old backups
            if (strpos($file, '/.pre_500/') !== false) {
                continue;
            }

            // ignore files which should have been automatically removed by previous upgrades but still sometimes exist
            if (in_array($file, [
                'include/SugarPDF.php',
                'modules/Opportunities/views/view.sidequickcreate.php',
                'include/database/MssqlManager2.php',
                'include/database/MssqlHelper2.php',
                'include/FCKeditor_Sugar/FCKeditor_Sugar.php',
                'include/database/OracleHelper.php',
                'modules/Documents/DocumentTreeView.php',
            ])) {
                continue;
            }

            $contents = file_get_contents($file);

            foreach ($callbacks as $callback) {
                $callback($file, $contents);
            }
        }
    }

    private function findUnsupportedApiUsages($file, $contents)
    {
        $methods = [
            'compilesql' => true,
            'deletesql' => true,
            'joinraw' => true,
            'insertsql' => true,
            'pquery' => true,
            'preparedquery' => true,
            'preparequery' => true,
            'preparestatement' => true,
            'preparetypedata' => true,
            'retrievesql' => true,
            'updatesql' => true,
        ];

        $properties = [
            'field_name_map' => true,
        ];

        $tokens = token_get_all($contents);

        $isInObjectOperator = false;

        foreach ($tokens as $token) {
            if (!is_array($token) || $token[0] == T_WHITESPACE) {
                continue;
            }

            if ($token[0] == T_OBJECT_OPERATOR) {
                $isInObjectOperator = true;
                continue;
            }

            if ($isInObjectOperator && $token[0] == T_STRING) {
                if (isset($methods[strtolower($token[1])])) {
                    $this->updateStatus('unsupportedMethodCall', $token[1], $file, $token[2]);
                } elseif (isset($properties[$token[1]])) {
                    $this->updateStatus('unsupportedPropertyAccess', $token[1], $file, $token[2]);
                }
            }

            $isInObjectOperator = false;
        }
    }

    public function getResultCode()
    {
        if ($this->exit_status == self::FAIL) {
            return self::FAIL;
        }
        return ord($this->status) - ord(HealthCheckScannerMeta::VANILLA);
    }

    /**
     * Scan directory and build the list of PHP files it contains
     * @param string $path
     * @return array Files data
     */
    protected function getPhpFiles($path)
    {
        $data = [];
        if (!is_dir($path)) {
            return [];
        }
        $path = rtrim($path, '/') . '/';
        $iter = new DirectoryIterator('./' . $path);
        foreach ($iter as $item) {
            if ($item->isDot()) {
                continue;
            }

            $filename = $item->getFilename();
            if (strpos($filename, '.suback.php') !== false || strpos($filename, '_backup') !== false) {
                // we'll ignore .suback files, they are old upgrade backups
                continue;
            }

            $extension = explode('.', $filename);
            $extension = $this->safeCount($extension) >= 2 ? $extension[$this->safeCount($extension) - 1] : $extension[0];
            if ($item->isDir() && in_array($filename, $this->excludedScanDirectories)) {
                continue;
            } elseif ($item->isDir()) {
                if (strtolower($filename) == 'disable' || strtolower($filename) == 'disabled') {
                    // skip disable dirs
                    continue;
                }
                $data = array_merge($data, $this->getPhpFiles($path . $filename . '/'));
            } elseif (!preg_match('/php(_\d+)?\b/', $extension)) {
                // we need only php and php Studio-history (.php_{timestamp} extension) files
                continue;
            } elseif (!in_array($path . $filename, $this->ignoredFiles)) {
                $data[] = $path . $filename;
            }
        }

        return $data;
    }

    /**
     * Extract hook filenames from logic hook file and put them into hook files list
     * @param string $hookfile
     * @param array &$hooks_array
     * @param bool $detectAfterUiHooks should we log after_ui_footer & after_ui_frame hooks if they are present in file
     */
    public function extractHooks($hookfile, &$hooks_array, $detectAfterUiHooks = false)
    {
        $hook_array = [];
        if (!is_readable($hookfile)) {
            return;
        }
        ob_start();
        include $hookfile;
        ob_end_clean();
        if (empty($hook_array)) {
            return;
        }
        if ($detectAfterUiHooks && !empty($hook_array['after_ui_footer'])) {
            $this->updateStatus('logicHookAfterUIFooter', $hookfile);
        }
        if ($detectAfterUiHooks && !empty($hook_array['after_ui_frame'])) {
            $this->updateStatus('logicHookAfterUIFrame', $hookfile);
        }
        foreach ($hook_array as $hooks) {
            foreach ($hooks as $hook) {
                $hookFileLocation = (!empty($hook[2])) ? $hook[2] : '';
                if (!$hookFileLocation) {
                    $hookFileLocation = $this->getHookFilePath($hook[3]);
                }
                if (!file_exists($hookFileLocation) && !in_array($hookFileLocation, $this->ignoreMissingCustomFiles)) {
                    // putting it as custom since LogicHook checks file_exists
                    $this->updateStatus('badHookFile', $hookfile, $hookFileLocation);
                }
            }
        }
        $hooks_array = array_merge($hooks_array, $hook_array);
    }

    /**
     * Check PHP file for output constructs.
     * Set $status if it happens.
     * @param string $phpfile
     */
    protected function checkFileForOutput($phpfile, $namespace = null)
    {
        if (!$phpfile && $namespace) {
            $phpfile = $this->getHookFilePath($namespace);
        }

        if (in_array($phpfile, $this->ignoreMissingCustomFiles)) {
            [$sugar_version, $sugar_flavor] = $this->getVersionAndFlavor();
            if (version_compare($sugar_version, '7.0', '>')) {
                return;
            }
        }
        if (!file_exists($phpfile)) {
            $this->updateStatus('missingCustomFile', $phpfile);
            return;
        }
        $contents = file_get_contents($phpfile);
        if ($this->isStockFile($phpfile) && $this->areContentsIntact($phpfile)) {
            // this is our file, no need to check
            return;
        }
        $processOutput = !in_array($phpfile, $this->ignoreOutputCheckFiles);

        // remove sugarEntry check
        $this->scanForOutputConstructs($contents, $phpfile, $processOutput);
    }

    /**
     * Returns false if $item is T_WHITESPACE token.
     * @param $item
     * @return bool
     * @see \HealthCheckScanner::checkFileForOutput
     */
    protected function ignoreWhitespace($item)
    {
        return !(is_array($item) && $item[0] == T_WHITESPACE);
    }

    /**
     * Checking PHP file content and returning true if there was no code found.
     *
     * @param string $file path to file
     * @return bool is file empty or not
     */
    protected function isEmptyFile($file)
    {
        $content = file_get_contents($file);
        if (empty($content)) {
            return true;
        }
        $tokens = token_get_all($content);
        foreach ($tokens as $token) {
            switch ($token[0]) {
                case T_CLOSE_TAG:
                case T_COMMENT:
                case T_DOC_COMMENT:
                case T_OPEN_TAG:
                case T_WHITESPACE:
                    break;
                default:
                    return false;
            }
        }
        return true;
    }

    /**
     * Checks if print_r has the second parameter as 'true', according to:
     * When this parameter is set to TRUE, print_r() will return the information rather than print it.
     * We cannot check if the second parameter is actually true
     * in cases when the second parameter is a variable i.e. print_r($foo, $bar).
     * We blindly assume that if second parameter is passed then it is true.
     * Continue to scan, if has.
     * @param $index int index to start traversing $tokens at
     * @param $tokens array of tokens from token_get_all
     * @return bool
     */
    protected function checkPrintR($index, $tokens)
    {
        $curlyBracketsCount = 0;
        $found = false;
        $count = $this->safeCount($tokens);
        for ($i = $index + 1; $i < $count; $i++) {
            if ($tokens[$i] === '(') {
                $curlyBracketsCount += 1;
            } else {
                if ($tokens[$i] === ')') {
                    if ($curlyBracketsCount === 1 && !$found) {
                        return true;
                    }
                    $curlyBracketsCount -= 1;
                } else {
                    if ($tokens[$i] === ',' && $curlyBracketsCount === 1) {
                        $next = $tokens[$i + 1];
                        return (is_array($next) && $next[1] === 'false');
                    }
                }
            }
        }
        return false;
    }


    /**
     * PHP error handler, to log PHP errors
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param string $errline
     */
    public function scriptErrorHandler($errno, $errstr, $errfile, $errline)
    {
        // error was suppressed with the @-operator or does not correspond to current error level
        if (!(error_reporting() & $errno)) {
            return false;
        }
        return $this->reportPhpError($errno, $errstr, $errfile, $errline);
    }

    public $names = [
        'Gryffindor',
        'Hufflepuff',
        'Ravenclaw',
        'Slytherin',
        'Death Eater',
        'Voldemort',
        'Dumbledore',
    ];

    /* Copypaste from 6_ScanModules */

    /**
     * Is this a pure ModuleBuilder module?
     * @param string $module_dir
     * @return boolean
     */
    protected function isMBModule($module_name)
    {
        $module_dir = "modules/$module_name";
        if (empty($this->beanList[$module_name])) {
            // if this is not a deployed one, don't bother
            return false;
        }
        $bean = $this->beanList[$module_name];
        if (empty($this->beanFiles[$bean])) {
            return false;
        }

        // bad vardefs means no conversion to Sugar 7
        $this->checkVardefs($module_name, $bean, false, HealthCheckScannerMeta::STUDIO_MB_BWC);

        $mbFiles = ['Dashlets', 'Menu.php', 'language', 'metadata', 'vardefs.php', 'clients', 'workflow'];
        $mbFiles[] = basename($this->beanFiles[$bean]);
        $mbFiles[] = pathinfo($this->beanFiles[$bean], PATHINFO_FILENAME) . '_sugar.php';

        // to make checks faster
        $mbFiles = array_flip($mbFiles);

        $hook_files = [];
        $this->extractHooks("custom/$module_dir/logic_hooks.php", $hook_files);
        $this->extractHooks("custom/$module_dir/Ext/LogicHooks/logichooks.ext.php", $hook_files);
        $hook_files_list = [];
        foreach ($hook_files as $hookname => $hooks) {
            foreach ($hooks as $hook_data) {
                if (empty($hook_data[2])) {
                    $this->updateStatus('badHookFile', $hookname, '');
                } else {
                    $hook_files_list[] = $hook_data[2];
                }
            }
        }
        $hook_files = array_unique($hook_files_list);

        $unknownMBModuleFiles = [];
        // For now, the check is just checking if we have any files
        // in the directory that we do not recognize. If we do, we
        // put the module in BC.
        foreach ($this->glob("$module_dir/*") as $file) {
            if (in_array($file, $hook_files)) {
                // logic hook files are OK
                continue;
            }
            if (basename($file) == 'views') {
                // check views separately because of file template that has view.edit.php
                if (!$this->checkViewsDir("$module_dir/views")) {
                    $this->updateStatus('unknownFileViews', $module_name);
                    return false;
                } else {
                    continue;
                }
            }
            if (basename($file) == 'Forms.php') {
                if (filesize($file) > 0) {
                    $this->updateStatus('nonEmptyFormFile', $file, $module_name);
                    return false;
                }
                continue;
            }
            if (!isset($mbFiles[basename($file)])) {
                // unknown file, not MB module
                if ($this->safeCount($unknownMBModuleFiles) > $this->numberOfFilesToReport) {
                    break;
                }
                $unknownMBModuleFiles[] = $file;
            }
        }
        // files that are OK for custom:
        $mbFiles['Ext'] = true;
        $mbFiles['logic_hooks.php'] = true;

        // now check custom/ for unknown files
        foreach ($this->glob("custom/$module_dir/*") as $file) {
            if (in_array($file, $hook_files)) {
                // logic hook files are OK
                continue;
            }
            if (!isset($mbFiles[basename($file)])) {
                // unknown file, not MB module
                if ($this->safeCount($unknownMBModuleFiles) > $this->numberOfFilesToReport) {
                    break;
                }
                $unknownMBModuleFiles[] = $file;
            }
        }

        if (!empty($unknownMBModuleFiles)) {
            $filesToReport = array_slice($unknownMBModuleFiles, 0, $this->numberOfFilesToReport);
            $moreMessage = ($this->safeCount($unknownMBModuleFiles) > $this->numberOfFilesToReport) ? PHP_EOL . 'and there are more...' : '';
            $this->updateStatus('isNotMBModule', $filesToReport, $moreMessage, $module_name);
            return false;
        }

        $badExts = [
            'ActionViewMap',
            'ActionFileMap',
            'ActionReMap',
            'EntryPointRegistry',
            'FileAccessControlMap',
            'WirelessModuleRegistry',
        ];
        $badExts = array_flip($badExts);
        // Check Ext for any "dangerous" extensions
        $return = true;
        foreach ($this->glob("custom/$module_dir/Ext/*") as $extdir) {
            if (isset($badExts[basename($extdir)])) {
                $extfiles = glob("$extdir/*");
                foreach ($extfiles as $k => $file) {
                    if ($this->isEmptyFile($file)) {
                        unset($extfiles[$k]);
                    }
                }
                if (!empty($extfiles)) {
                    $this->updateStatus('extensionDirDetected', $extdir, $module_name);
                    $return = false;
                }
            }
        }

        return $return;
    }

    /**
     * Check if a module has Sidecar characteristics.
     * @param string $module name of the module.
     * @return bool
     */
    protected function isSidecarModule($module)
    {
        $directoriesToCheck = [
            "$module/clients/base",
            "custom/$module/clients/base",
        ];

        foreach ($directoriesToCheck as $dir) {
            if (file_exists($dir)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if views dir was created by file template
     * @param string $view_dir
     * @param string $status Status to assign if check fails
     * @return boolean
     */
    protected function checkViewsDir($view_dir)
    {
        foreach ($this->glob("$view_dir/*") as $file) {
            // for now we allow only view.edit.php
            if (basename($file) != 'view.edit.php') {
                $this->updateStatus('unknownFile', $view_dir, $file);
                return false;
            }
            $data = file_get_contents($file);
            // start with first {
            $data = substr($data, strpos($data, '{'));
            // drop function names
            $data = preg_replace('/function\s[<>_\w]+/', '', $data);
            // drop whitespace
            $data = preg_replace('/\s+/', '', $data);
            /* File data is:
             * {(){if(isset($this->bean->id)){$this->ss->assign("FILE_OR_HIDDEN","hidden");if(empty($_REQUEST['isDuplicate'])||$_REQUEST['isDuplicate']=='false'){$this->ss->assign("DISABLED","disabled");}}else{$this->ss->assign("FILE_OR_HIDDEN","file");}parent::display();}}
             * md5 is: 794b5f58a557c243ddea04382996891f
             * or
             * File data is:
             * {(){parent::ViewEdit();}(){if(isset($this->bean->id)){$this->ss->assign("FILE_OR_HIDDEN","hidden");if(empty($_REQUEST['isDuplicate'])||$_REQUEST['isDuplicate']=='false'){$this->ss->assign("DISABLED","disabled");}}else{$this->ss->assign("FILE_OR_HIDDEN","file");}parent::display();}}?>
             * md5 is: c8251f6b50e3e814135c936f6b5292eb
             */
            $md5 = md5($data);
            if (($md5 !== '794b5f58a557c243ddea04382996891f') && ($md5 !== 'c8251f6b50e3e814135c936f6b5292eb')) {
                $this->updateStatus('badMd5', $file);
                return false;
            }
        }
        return true;
    }

    /**
     * List of modules with messed-up vardefs
     * For our eternal shame, these vardefs are broken in existing installs
     * Only non-BWC modules here, since BWC ones aren't checked for vardefs
     * @var array
     */
    protected $bad_vardefs = [
        'Forecasts' => ['closed_count'],
        'ForecastOpportunities' => ['description'],
        'Quotas' => ['assigned_user_id'],
        'ProductTemplates' => ['assigned_user_link'],
        'Calls' => ['contact_id'],
        'Meetings' => ['contact_id'],
        'KBDocuments' => [
            'case_name',
            'keywords',
            'modified_user_name',
        ],
        'KBContents' => ['created_by_link', 'modified_user_link'],
        'KBDocumentRevisions' => ['document_revisions'],
        'KBTags' => [
            'created_by_name',
        ],
    ];

    /**
     * Check that all fields in array exist
     * @param string $key Origin field
     * @param array $fields List of fields to check
     * @param array $fieldDefs Vardefs
     * @param array $status Status array to store errors
     * @param string $module Module name
     */
    protected function checkFields(string $key, array $fields, array $fieldDefs, string $custom, string $module): void
    {
        foreach ($fields as $subField) {
            if (empty($fieldDefs[$subField])) {
                $this->updateStatus('badVardefsSubfields' . $custom, $key, $subField, $module);
            }
        }
    }

    /**
     * @var array List of fields that can use html function in vardefs.
     * These fields are allowed to use in stock and non-stock modules.
     */
    protected $templateFields = [
        'email' => true,
        'email1' => true,
        'email2' => true,
        'currency_id' => true,
        'currency_name' => true,
        'currency_symbol' => true,
    ];

    /**
     * Check vardefs for module
     * @param string $module
     * @param string $object
     * @param bool $stock Is this a stock module?
     * @return boolean|array true if vardefs OK, list of reasons if module needs to be BWCed
     */
    protected function checkVardefs($module, $object, $stock = false, $status = HealthCheckScannerMeta::STUDIO_MB_BWC)
    {
        $custom = '';
        if ($status == HealthCheckScannerMeta::CUSTOM) {
            $custom = 'Custom';
        }

        if ($module == 'DynamicFields') {
            // this one is an odd one
            return true;
        }
        $this->log("Checking vardefs for $module");
        VardefManager::loadVardef($module, $object);
        if (empty($GLOBALS['dictionary'][$object]['fields'])) {
            $this->log("Failed to load vardefs for $module:$object");
            return true;
        }
        $seed = BeanFactory::newBean($module);
        if (empty($seed)) {
            $this->log("Failed to instantiate bean for $module, not checking vardefs");
            return true;
        }

        $fieldDefs = $GLOBALS['dictionary'][$object]['fields'];

        // get names of 'stock' fields, that are defined in original vardefs.php
        $stockFieldsDef = $this->loadFromFile("modules/$module/vardefs.php", 'dictionary');
        if (!empty($stockFieldsDef[$seed->object_name]['fields']) && is_array($stockFieldsDef[$seed->object_name]['fields'])) {
            $stockFieldsDef = $stockFieldsDef[$seed->object_name]['fields'];
        } else {
            $stockFieldsDef = [];
        }
        $stockFields = array_keys($stockFieldsDef);

        foreach ($fieldDefs as $key => $value) {
            if (!empty($this->bad_vardefs[$module]) && $this->safeInArray($key, $this->bad_vardefs[$module])) {
                continue;
            }
            if (empty($value['name']) || $key != $value['name']) {
                if (empty($stockFieldsDef[$key]) || $stockFieldsDef[$key] != $value) {
                    $nameValue = (!empty($value['name'])) ? $value['name'] : '';
                    $this->updateStatus('badVardefsKey', $key, $nameValue, $module);
                    continue;
                }
            }

            // Check "name" field type, @see CRYS-130
            if ($key == 'name' && $value['type'] != 'name') {
                // Assume those types are valid, cause they used in stock modules
                $validNameTypes = ['id', 'fullname', 'varchar'];
                if (!in_array($value['type'], $validNameTypes)) {
                    $this->updateStatus('badVardefsName', $value['type'], $module);
                    continue;
                }
            }

            if ($key == 'team_name') {
                if (empty($value['module'])) {
                    $this->updateStatus('badVardefsRelate', $key, $module);
                }
                // this field is really weird, let's leave it alone for now
                continue;
            }

            if (!empty($value['function']['returns']) &&    // there is function in vardefs
                $value['function']['returns'] == 'html' &&  // that returns html
                !isset($this->templateFields[$key]) &&      // and field isn't in white-list
                (!$stock || !in_array(
                    $key,
                    $stockFields
                ))  // and it is non-stock module or it is stock module but field is non-stock
            ) {
                $this->updateStatus('vardefHtmlFunctionName' . $custom, $value['function']['name'], $module, $key);
            }

            if (!empty($value['type'])) {
                switch ($value['type']) {
                    case 'date':
                    case 'datetime':
                    case 'time':
                        if (!empty($value['display_default']) && preg_match('/^\-.+\-$/', $value['display_default'])) {
                            $this->updateStatus('vardefIncorrectDisplayDefault', $key, $module);
                        }
                        break;
                    case 'enum':
                    case 'multienum':
                        if (!empty($value['function']['returns']) && $value['function']['returns'] == 'html') {
                            // found html functional field
                            $this->updateStatus('vardefHtmlFunction' . $custom, $key);
                        }

                        // Check option-list multienum fields
                        if ($value['type'] == 'multienum'
                            && !empty($value['options'])
                            && !empty($GLOBALS['app_list_strings'][$value['options']])
                        ) {
                            $optionKeys = array_keys($GLOBALS['app_list_strings'][$value['options']]);
                            // Strip all valid characters in dropdown keys - a-zA-Z0-9. and spaces
                            $result = preg_replace('/[\w\d\s\.,\(\)]/', '', $optionKeys);

                            // Get unique chars
                            $result = count_chars(implode('', $result), 3);

                            if ($result) {
                                $this->updateStatus('badVardefsMultienum', $value['name'], $value['options'], $result, $module);
                            }
                        }

                        break;
                    case 'link':
                        if (isset($value['link_file'], $value['link_class']) && file_exists($value['link_file']) && !class_exists($value['link_class'])) {
                            require_once $value['link_file'];
                            if (class_exists($value['link_class'])) {
                                $this->updateStatus('badVardefsClassAutoloading', $key, $module);
                            }
                            break;
                        }
                        $seed->load_relationship($key);
                        if (empty($seed->$key)) {
                            $this->updateStatus('badVardefsLink', $key, $module);
                        }
                        break;
                    case 'relate':
                        if (!empty($value['link'])) {
                            $lname = $value['link'];
                            if (empty($fieldDefs[$lname])) {
                                ;
                                $this->updateStatus('badVardefsKey', $key, $lname, $module);
                                break;
                            }
                            $seed->load_relationship($lname);
                            if (empty($seed->$lname)) {
                                $this->updateStatus('badVardefsRelate', $key, $module);
                                break;
                            }
                            $relatedModuleName = $seed->$lname->getRelatedModuleName();
                            if (empty($relatedModuleName)) {
                                break;
                            }
                            $relatedBean = BeanFactory::newBean($relatedModuleName);
                            if (empty($relatedBean)) {
                                break;
                            }
                        }
                        if (!$this->hasValidLinkAndModule($value)
                            && (empty($stockFieldsDef[$key])
                                || $this->hasValidLinkAndModule($stockFieldsDef[$key]))) {
                            $this->updateStatus('badVardefsRelate', $key, $module);
                        }
                        break;
                }
            }

            if (empty($value['source']) || $value['source'] == 'db' || $value['source'] == 'custom_fields') {
                // check fields
                if (isset($value['fields'])) {
                    $this->checkFields($key, $value['fields'], $fieldDefs, $custom, $module);
                }
                // check db_concat_fields
                if (isset($value['db_concat_fields'])) {
                    $this->checkFields($key, $value['db_concat_fields'], $fieldDefs, $custom, $module);
                }
                // check sort_on
                if (!empty($value['sort_on'])) {
                    if (is_array($value['sort_on'])) {
                        $sort = $value['sort_on'];
                    } else {
                        $sort = [$value['sort_on']];
                    }
                    $this->checkFields($key, $sort, $fieldDefs, $custom, $module);
                }
            }
        }

        // check if we have any type changes for vardefs, BR-1427
        $this->checkVardefTypeChange($module, $object);
    }

    /* END of copypaste from 6_ScanModules */

    private function hasValidLinkAndModule($value)
    {
        return !((empty($value['link_type'])
                || $value['link_type'] != 'relationship_info')
            && empty($value['module']));
    }

    /**
     * Ping feedback url
     * @param array $data
     */
    protected function ping($data)
    {
        $url = $this->ping_url . '?' . http_build_query($data);
        $curlHandler = curl_init($url);
        if ($curlHandler !== false) {
            curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curlHandler, CURLOPT_FOLLOWLOCATION, true);
            curl_exec($curlHandler);
            curl_close($curlHandler);
        }
    }

    /**
     * List of standard BWC modules
     * @var array
     */
    protected $bwcModules = [
        'ACLFields',
        'ACLRoles',
        'ACLActions',
        'Administration',
        'Audit',
        'Calendar',
        'CampaignLog',
        'Campaigns',
        'CampaignTrackers',
        'Charts',
        'Configurator',
        'Contracts',
        'ContractTypes',
        'Connectors',
        'Currencies',
        'CustomQueries',
        'DataSets',
        'DocumentRevisions',
        'Documents',
        'EAPM',
        'EmailAddresses',
        'EmailMarketing',
        'EmailMan',
        'EmailTemplates',
        'Employees',
        'Exports',
        'Expressions',
        'Groups',
        'History',
        'Holidays',
        'iCals',
        'Import',
        'InboundEmail',
        'KBOLDContents',
        'KBOLDDocuments',
        'KBOLDDocumentRevisions',
        'KBOLDTags',
        'KBOLDDocumentKBOLDTags',
        'KBOLDContents',
        'Manufacturers',
        'MergeRecords',
        'ModuleBuilder',
        'MySettings',
        'OAuthKeys',
        'OptimisticLock',
        'OutboundEmailConfiguration',
        'PdfManager',
        'ProductBundleNotes',
        'ProductBundles',
        'ProductTypes',
        'Project',
        'ProjectTask',
        'QueryBuilder',
        'Relationships',
        'Releases',
        'ReportMaker',
        'Reports',
        'SavedSearch',
        'Schedulers',
        'SchedulersJobs',
        'Shippers',
        'SNIP',
        'Studio',
        'SugarFavorites',
        'TaxRates',
        'Teams',
        'TeamMemberships',
        'TeamSets',
        'TeamSetModules',
        'TeamNotices',
        'TimePeriods',
        'Trackers',
        'TrackerSessions',
        'TrackerPerfs',
        'TrackerQueries',
        'UserPreferences',
        'Users',
        'vCals',
        'vCards',
        'Versions',
        'WorkFlow',
        'WorkFlowActions',
        'WorkFlowActionShells',
        'WorkFlowAlerts',
        'WorkFlowAlertShells',
        'WorkFlowTriggerShells',
        'HealthCheck',
    ];

    /**
     * List of modules we have added in Sugar7
     * @var array
     */
    protected $newModules = [
        'Comments' => 'Comments',
        'Filters' => 'Filters',
        'RevenueLineItems' => 'Revenue Line Items',
        'Styleguide' => 'Styleguide',
        'Subscriptions' => 'Subscriptions',
        'UserSignatures' => 'Email Signatures',
        'WebLogicHooks' => 'Web Logic Hooks',
        'Words' => 'Words',
    ];

    /**
     * Retrieve version.json file path
     * @return null|string
     */
    protected function getVersionFile()
    {
        $versionFile = null;
        if (file_exists(__DIR__ . '/' . self::VERSION_FILE)) {
            $versionFile = __DIR__ . '/' . self::VERSION_FILE;
        } elseif ($this->upgrader && isset($this->upgrader->context['upgrader_dir']) &&
            file_exists($this->upgrader->context['upgrader_dir'] . '/' . self::VERSION_FILE)
        ) {
            $versionFile = $this->upgrader->context['upgrader_dir'] . '/' . self::VERSION_FILE;
        }
        return $versionFile;
    }

    /**
     * Get upgrade package manifest from upgrader context
     * @return array
     */
    public function getPackageManifest()
    {

        /**
         * Our upgrade drivers (web,cli, and shadow) differ slightly in what context is available during the healthcheck
         * process.  Cli and shadow both have access to the new UpgradeDriver and utilize it when initiated.  Web upgrader
         * must use the previous WebUpgrade/UpgradeDriver during the healthcheck stage.  This means the new Scanner
         * is tightly coupled with the previous upgrade driver and therefore care must be taken when modifying existing
         * APIs.  The algorithm to deduce how to load the manifest is:
         *
         *      1.  If our upgrade driver has a public method available entitled getManifest use that implementation (7.6.1 >)
         *      2.  If the getManifest method on the upgrade driver is not callable indicating an old version of the
         *          upgrade driver then use the old mechanism for retrieving the package manifest. (Pre-7.6.10)
         *      3.  If none of these methods work return nothing (indicating the health check will fail.
         *
         * We can skip step #2 and only go with step #1 once we only support upgrades from 7.6.1 going forward.
         *
         */
        if (is_callable([$this->upgrader, 'getManifest'])) {
            return $this->upgrader->getManifest();
        } elseif (!empty($this->upgrader->context['extract_dir'])) {
            $fileReader = new FileLoaderWrapper();
            $manifest = $fileReader->loadFile($this->upgrader->context['extract_dir'] . '/manifest.php', 'manifest');
            return !empty($manifest) ? $manifest : [];
        }
        return [];
    }

    /**
     * Returns array that contains build and version
     */
    public function getVersion()
    {
        global $sugar_version, $sugar_build;
        $version = [
            'version' => 'N/A',
            'build' => 'N/A',
        ];
        $versionFile = $this->getVersionFile();
        if ($versionFile) {
            $json = file_get_contents($versionFile);
            $data = json_decode($json, true);
            $version = array_merge($version, $data);
        } elseif ($sugar_version && $sugar_build) {
            $version = array_merge(
                $version,
                [
                    'version' => $sugar_version,
                    'build' => $sugar_build,
                ]
            );
        } elseif (file_exists('sugar_version.php')) {
            if (!defined('sugarEntry')) {
                define('sugarEntry', true);
            }
            include 'sugar_version.php';
            $version = array_merge(
                $version,
                [
                    'version' => $sugar_version,
                    'build' => $sugar_build,
                ]
            );
        }
        return [$version['version'], $version['build']];
    }

    /**
     * Get hook file path by class namespace
     * @param string $namespace Fully class namespace
     * @return string
     */
    protected function getHookFilePath($nameSpace)
    {
        try {
            $reflectionClass = new \ReflectionClass($nameSpace);
        } catch (\ReflectionException $e) {
            $this->log("Scanner: Could not use ReflectionClass with {$nameSpace}");
            return '';
        }
        return $reflectionClass->getFileName();
    }

    /**
     * Checks if the file is a stock SugarCRM file
     *
     * @param string $path File path
     * @return bool
     */
    protected function isStockFile($path)
    {
        return isset($this->md5_files['./' . $path]);
    }

    /**
     * Checks if the file contents are intact
     *
     * @param string $path File path
     * @return bool
     */
    protected function areContentsIntact($path)
    {
        if (!$this->isStockFile($path)) {
            return false;
        }

        $contents = file_get_contents($path);
        return $this->md5_files['./' . $path] === md5($contents);
    }

    /**
     * @param $version1
     * @param $version2
     * @return bool|int
     */
    private function versionLessThan($version1, $version2)
    {
        $version1 = ltrim($version1, 'v');
        $version2 = ltrim($version2, 'v');
        return version_compare($version1, $version2, '<');
    }

    /**
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param string $errline
     * @return false|void
     */
    private function reportPhpError(int $errno, string $errstr, string $errfile, string $errline)
    {
        // ignore redis initialization error when running healthcheck on 8.1
        if (basename($errfile) === 'Redis.php') {
            return false;
        }

        // ignore entryPoint session error when running healthcheck on php7.3+
        if (basename($errfile) === 'entryPoint.php'
            && !empty($errstr)
            && strpos($errstr, 'session_set_save_handler') !== false
        ) {
            return false;
        }

        // ignore errors in smarty cache
        if (false !== strpos($errfile, (string) sugar_cached('smarty3/'))) {
            return false;
        }

        switch ($errno) {
            case 1:
                $e_type = 'E_ERROR';
                break;
            case 2:
                $e_type = 'E_WARNING';
                break;
            case 4:
                $e_type = 'E_PARSE';
                break;
            case 8:
                $e_type = 'E_NOTICE';
                break;
            case 16:
                $e_type = 'E_CORE_ERROR';
                break;
            case 32:
                $e_type = 'E_CORE_WARNING';
                break;
            case 64:
                $e_type = 'E_COMPILE_ERROR';
                break;
            case 128:
                $e_type = 'E_COMPILE_WARNING';
                break;
            case 256:
                $e_type = 'E_USER_ERROR';
                break;
            case 512:
                $e_type = 'E_USER_WARNING';
                break;
            case 1024:
                $e_type = 'E_USER_NOTICE';
                break;
            case 2048:
                $e_type = 'E_STRICT';
                break;
            case 4096:
                $e_type = 'E_RECOVERABLE_ERROR';
                break;
            case 8192:
                $e_type = 'E_DEPRECATED';
                break;
            case 16384:
                if (basename($errfile) === 'smarty_internal_compile_private_modifier.php') {
                    @trigger_error($errstr, E_USER_DEPRECATED);
                    return;
                }
                $e_type = 'E_USER_DEPRECATED';
                break;
            case 30719:
                $e_type = 'E_ALL';
                break;
            default:
                $e_type = 'E_UNKNOWN';
                break;
        }
        $this->updateStatus('phpError', $e_type, $errstr, $errfile, $errline);
    }

    private function getMethodSignatureVisitor(array $rules): NodeVisitor
    {
        if (class_exists(MethodSignatureVisitor::class)) {
            return new MethodSignatureVisitor($rules);
        }
        // copy of MethodSignatureVisitor class, which is unavailable in HealthCheck context during upgrade to 12.3
        return new class ($rules) extends ForbiddenStatementVisitor {
            private $rules = [];

            public function __construct(array $rules)
            {
                $this->rules = $rules;
            }

            public function enterNode(Node $node)
            {
                if (count($this->rules) === 0) {
                    return;
                }
                if (!$node instanceof Node\Stmt\ClassMethod) {
                    return;
                }
                $class = $node->getAttribute('parent');
                if (!$class instanceof Node\Stmt\Class_ || $class->extends === null || !isset($this->rules[$class->extends->toString()])) {
                    return;
                }
                $rule = $this->rules[$class->extends->toString()][$node->name->toString()] ?? [];
                if (isset($rule['return']) && (!isset($node->returnType) || $node->returnType->toString() !== $rule['return'])) {
                    $this->issues[] = '';
                }
            }
        };
    }

    private function safeCount($potentiallyCountable): int
    {
        if (function_exists('safeCount')) {
            return safeCount($potentiallyCountable);
        }
        if (is_countable($potentiallyCountable)) {
            return count($potentiallyCountable);
        }
        LoggerManager::getLogger()->warn('count() called on non-countable argument: ' . PHP_EOL . (new Exception())->getTraceAsString());
        return 0;
    }

    private function safeInArray($needle, $haystack, bool $strict = false) : bool
    {
        if (!is_array($haystack)) {
            return false;
        }
        return in_array($needle, $haystack, $strict);
    }
}

/**
 * Class that ignores everything, needs for loading
 * metadata with code
 */
class BlackHole implements ArrayAccess, Countable, Iterator
{
    protected $called;

    /**
     * Fields to be stubbed.
     * @var array
     */
    protected $stubFields = [];

    /**
     * Methods to be stubbed.
     * @var array
     */
    protected $stubMethods = [];

    /**
     * You can set fields and methods to be stubbed when __get() or __call() are triggered on a BlackHole.
     * @param array $fields list of fields (name => value)
     * @param array $methods list of methods (name => returnValue)
     */
    public function __construct($fields = [], $methods = [])
    {
        $this->stubFields = $fields;
        $this->stubMethods = $methods;
    }

    public function __get($v)
    {
        $this->called = true;
        return array_key_exists($v, $this->stubFields) ? $this->stubFields[$v] : $this;
    }

    public function __call($n, $a)
    {
        $this->called = true;
        return array_key_exists($n, $this->stubMethods) ? $this->stubMethods[$n] : $this;
    }

    public function __toString(): string
    {
        return '';
    }

    public function offsetExists($offset): bool
    {
        return false;
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this;
    }

    public function offsetSet($offset, $value): void
    {
        // Nothing to do
    }

    public function offsetUnset($offset): void
    {
        // Nothing to do
    }

    public function count(): int
    {
        return 0;
    }

    public function __invoke()
    {
        $this->called = true;
        return $this;
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this;
    }

    public function next(): void
    {
        // Nothing to do
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        return null;
    }

    public function valid(): bool
    {
        return false;
    }

    public function rewind(): void
    {
        // Nothing to do
    }
}

/**
 * Stub class for loading files
 * Needed because we can not override $this but some data files use $this
 * @param string $deffile Definitions file
 * @param string $varname Variable to load
 * @return null if no variable, false on error, otherwise value of $varname in file
 */
class FileLoaderWrapper extends BlackHole
{
    private $nullUser;

    private $currentUser;

    public function __construct()
    {
        parent::__construct();

        $this->nullUser = new BlackHole(
            [
                'id' => null,
            ],
            [
                'getPreference' => null,
            ]
        );
    }

    public function __get($v)
    {
        $this->called = true;
        return $this;
    }

    public function __call($n, $a)
    {
        $this->called = true;
        return $this;
    }

    public function loadFile($deffile, $varname)
    {
        global $current_user;

        $this->currentUser = $current_user;
        $current_user = $this->nullUser;

        ob_start();
        @include $deffile;
        ob_end_clean();

        $current_user = $this->currentUser;
        $this->currentUser = null;

        if ($this->called) {
            return false;
        }
        if (empty(${$varname})) {
            return null;
        }
        return ${$varname};
    }
}
