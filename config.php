<?php
// created: 2025-11-11 16:19:19
$sugar_config = array (
  'activity_streams' => 
  array (
    'erasure_job_limit' => 5,
    'erasure_job_delay' => 0,
  ),
  'activity_streams_enabled' => false,
  'additional_http_referer' => 
  array (
    0 => 'account-d.docusign.com',
    1 => 'account.docusign.com',
    2 => 'dropbox.com',
    3 => 'api.dropboxapi.com',
  ),
  'additional_js_config' => 
  array (
  ),
  'admin_access_control' => false,
  'admin_export_only' => false,
  'allow_freeze_first_column' => true,
  'allowed_link_schemes' => 
  array (
    0 => 'http',
    1 => 'https',
  ),
  'analytics' => 
  array (
    'enabled' => true,
    'connector' => 'Pendo',
    'id' => '1dd345e9-b638-4bd2-7bfb-147a937d4728',
  ),
  'aws_connect' => 
  array (
    'allow_list_domains' => 
    array (
      0 => '*.amazonaws.com',
      1 => '*.static.connect.aws',
    ),
    'portal_allow_list_domains' => 
    array (
      0 => 'wss://*.amazonaws.com',
    ),
  ),
  'cache_dir' => 'cache/',
  'calculate_response_time' => true,
  'calendar' => 
  array (
    'default_view' => 'week',
    'show_calls_by_default' => true,
    'show_tasks_by_default' => true,
    'editview_width' => 990,
    'editview_height' => 465,
    'day_timestep' => 15,
    'week_timestep' => 30,
    'items_draggable' => true,
    'items_resizable' => true,
    'enable_repeat' => true,
    'max_repeat_count' => 1000,
  ),
  'catalog_enabled' => false,
  'catalog_url' => 'https://appcatalog.service.sugarcrm.com',
  'chartEngine' => 'chartjs',
  'chart_service' => 
  array (
    'max_retries' => 3,
    'service_urls' => 
    array (
      'default' => 'https://chart-service-usw2.service.sugarcrm.com',
      'us-west-2' => 'https://chart-service-usw2.service.sugarcrm.com',
      'ap-southeast-2' => 'https://chart-service-apse2.service.sugarcrm.com',
      'eu-central-1' => 'https://chart-service-euc1.service.sugarcrm.com',
      'ca-central-1' => 'https://chart-service-cac1.service.sugarcrm.com',
      'ap-southeast-1' => 'https://chart-service-apse1.service.sugarcrm.com',
      'eu-west-2' => 'https://chart-service-euw2.service.sugarcrm.com',
    ),
  ),
  'clear_resolved_date' => true,
  'collapse_subpanels' => false,
  'commentlog' => 
  array (
    'maxchars' => 500,
  ),
  'create_default_user' => false,
  'cron' => 
  array (
    'max_cron_jobs' => 25,
    'max_cron_runtime' => 1800,
    'min_cron_interval' => 30,
  ),
  'currency' => '',
  'currency_create_in_preferred' => false,
  'customer_journey' => 
  array (
    'enabled_modules' => 'Accounts,Contacts,Leads,Opportunities,Cases',
    'recordview_display_settings' => 
    array (
      'Accounts' => 'panel_bottom',
      'Contacts' => 'panel_bottom',
      'Leads' => 'panel_bottom',
      'Opportunities' => 'panel_bottom',
      'Cases' => 'panel_bottom',
    ),
    'grace_period_start_date' => '0000-00-00',
    'sugar_action_batch_chunk' => '20',
  ),
  'dashlet_display_row_options' => 
  array (
    0 => '1',
    1 => '3',
    2 => '5',
    3 => '10',
  ),
  'date_formats' => 
  array (
    'Y-m-d' => '2010-12-23',
    'm-d-Y' => '12-23-2010',
    'd-m-Y' => '23-12-2010',
    'Y/m/d' => '2010/12/23',
    'm/d/Y' => '12/23/2010',
    'd/m/Y' => '23/12/2010',
    'Y.m.d' => '2010.12.23',
    'd.m.Y' => '23.12.2010',
    'm.d.Y' => '12.23.2010',
  ),
  'datef' => 'm/d/Y',
  'dbconfig' => 
  array (
    'db_host_name' => 'localhost',
    'db_host_instance' => 'SQLEXPRESS',
    'db_user_name' => 'root',
    'db_password' => 'root',
    'db_name' => 'catorce',
    'db_type' => 'mysql',
    'db_port' => '',
    'db_manager' => 'MysqliManager',
  ),
  'dbconfigoption' => 
  array (
    'persistent' => false,
    'autofree' => false,
    'debug' => 0,
    'ssl' => false,
    'collation' => 'utf8mb4_general_ci',
  ),
  'default_action' => 'index',
  'default_background_image' => 'include/images/login-background.png',
  'default_charset' => 'UTF-8',
  'default_currencies' => 
  array (
    'AUD' => 
    array (
      'name' => 'Australian Dollars',
      'iso4217' => 'AUD',
      'symbol' => '$',
    ),
    'BRL' => 
    array (
      'name' => 'Brazilian Reais',
      'iso4217' => 'BRL',
      'symbol' => 'R$',
    ),
    'GBP' => 
    array (
      'name' => 'British Pounds',
      'iso4217' => 'GBP',
      'symbol' => '£',
    ),
    'CAD' => 
    array (
      'name' => 'Canadian Dollars',
      'iso4217' => 'CAD',
      'symbol' => '$',
    ),
    'CNY' => 
    array (
      'name' => 'Chinese Yuan',
      'iso4217' => 'CNY',
      'symbol' => '￥',
    ),
    'EUR' => 
    array (
      'name' => 'Euro',
      'iso4217' => 'EUR',
      'symbol' => '€',
    ),
    'HKD' => 
    array (
      'name' => 'Hong Kong Dollars',
      'iso4217' => 'HKD',
      'symbol' => '$',
    ),
    'INR' => 
    array (
      'name' => 'Indian Rupees',
      'iso4217' => 'INR',
      'symbol' => '₨',
    ),
    'KRW' => 
    array (
      'name' => 'Korean Won',
      'iso4217' => 'KRW',
      'symbol' => '₩',
    ),
    'YEN' => 
    array (
      'name' => 'Japanese Yen',
      'iso4217' => 'JPY',
      'symbol' => '¥',
    ),
    'MXM' => 
    array (
      'name' => 'Mexican Pesos',
      'iso4217' => 'MXM',
      'symbol' => '$',
    ),
    'SGD' => 
    array (
      'name' => 'Singaporean Dollars',
      'iso4217' => 'SGD',
      'symbol' => '$',
    ),
    'CHF' => 
    array (
      'name' => 'Swiss Franc',
      'iso4217' => 'CHF',
      'symbol' => 'SFr.',
    ),
    'THB' => 
    array (
      'name' => 'Thai Baht',
      'iso4217' => 'THB',
      'symbol' => '฿',
    ),
    'USD' => 
    array (
      'name' => 'US Dollars',
      'iso4217' => 'USD',
      'symbol' => '$',
    ),
  ),
  'default_currency_iso4217' => 'USD',
  'default_currency_name' => 'US Dollars',
  'default_currency_show_preferred' => false,
  'default_currency_significant_digits' => 2,
  'default_currency_symbol' => '$',
  'default_date_format' => 'm/d/Y',
  'default_decimal_seperator' => '.',
  'default_email_charset' => 'UTF-8',
  'default_email_client' => 'sugar',
  'default_email_editor' => 'html',
  'default_export_charset' => 'UTF-8',
  'default_language' => 'en_us',
  'default_locale_name_format' => 's f l',
  'default_max_pinned_modules' => 4,
  'default_max_tabs' => '7',
  'default_module' => 'Home',
  'default_navigation_paradigm' => 'gm',
  'default_number_grouping_seperator' => ',',
  'default_password' => '',
  'default_permissions' => 
  array (
    'dir_mode' => 1528,
    'file_mode' => 432,
    'user' => '',
    'group' => '',
  ),
  'default_subpanel_links' => false,
  'default_subpanel_tabs' => false,
  'default_swap_last_viewed' => false,
  'default_swap_shortcuts' => false,
  'default_theme' => 'RacerX',
  'default_time_format' => 'h:ia',
  'default_user_is_admin' => false,
  'default_user_name' => '',
  'demoData' => 'no',
  'diagnostic_file_max_lifetime' => 604800,
  'disable_convert_lead' => false,
  'disable_export' => false,
  'disable_persistent_connections' => 'false',
  'display_email_template_variable_chooser' => false,
  'display_inbound_email_buttons' => false,
  'document_merge' => 
  array (
    'max_retries' => 3,
    'service_urls' => 
    array (
      'default' => 'https://document-merge-us-west-2-prod.service.sugarcrm.com',
      'us-west-2' => 'https://document-merge-us-west-2-prod.service.sugarcrm.com',
      'ap-southeast-2' => 'https://document-merge-ap-se-2-prod.service.sugarcrm.com',
      'eu-central-1' => 'https://document-merge-eu-central-1-prod.service.sugarcrm.com',
      'ca-central-1' => 'https://document-merge-ca-central-1-prod.service.sugarcrm.com',
      'ap-southeast-1' => 'https://document-merge-ap-se-1-prod.service.sugarcrm.com',
      'eu-west-2' => 'https://document-merge-eu-west-2-prod.service.sugarcrm.com',
    ),
  ),
  'dump_slow_queries' => false,
  'email_address_separator' => ',',
  'email_default_client' => 'sugar',
  'email_default_editor' => 'html',
  'email_mailer_timelimit' => 2,
  'email_mailer_timeout' => 2,
  'enable_link_to_drawer' => true,
  'enable_mobile_redirect' => true,
  'error_number_of_cycles' => '10',
  'export_delimiter' => ',',
  'export_excel_compatible' => false,
  'freeze_list_headers' => true,
  'full_text_engine' => 
  array (
    'Elastic' => 
    array (
      'host' => 'localhost',
      'port' => '9200',
    ),
  ),
  'gcs_client' => 
  array (
    'service_urls' => 
    array (
      'default' => 'https://gcs-api-us-west-2-prod.service.sugarcrm.com',
      'us-west-2' => 'https://gcs-api-us-west-2-prod.service.sugarcrm.com',
      'ap-southeast-2' => 'https://gcs-api-apse2-prod.service.sugarcrm.com',
      'eu-central-1' => 'https://gcs-api-euc1-prod.service.sugarcrm.com',
      'ca-central-1' => 'https://gcs-api-cac1-prod.service.sugarcrm.com',
      'ap-southeast-1' => 'https://gcs-api-apse1-prod.service.sugarcrm.com',
      'eu-west-2' => 'https://gcs-api-euw2-prod.service.sugarcrm.com',
    ),
  ),
  'generic_search' => 
  array (
    'modules' => 
    array (
      0 => 'KBContents',
    ),
  ),
  'hide_history_contacts_emails' => 
  array (
    'Cases' => false,
    'Accounts' => false,
    'Opportunities' => true,
  ),
  'history_max_viewed' => 50,
  'host_designation' => 'production',
  'host_environment' => 'on-premise',
  'host_name' => 'localhost',
  'import_max_execution_time' => 3600,
  'import_max_records_per_file' => 100,
  'import_max_records_total_limit' => '',
  'installer_locked' => true,
  'jobs' => 
  array (
    'min_retry_interval' => 30,
    'max_retries' => 5,
    'timeout' => 3600,
  ),
  'js_custom_version' => '',
  'js_lang_version' => 1,
  'languages' => 
  array (
    'en_us' => 'English (US)',
    'bg_BG' => 'Български',
    'cs_CZ' => 'Česky',
    'da_DK' => 'Dansk',
    'de_DE' => 'Deutsch',
    'el_EL' => 'Ελληνικά',
    'es_ES' => 'Español',
    'fr_FR' => 'Français',
    'he_IL' => 'עברית',
    'hu_HU' => 'Magyar',
    'hr_HR' => 'Hrvatski',
    'it_it' => 'Italiano',
    'lt_LT' => 'Lietuvių',
    'ja_JP' => '日本語',
    'ko_KR' => '한국어',
    'lv_LV' => 'Latviešu',
    'nb_NO' => 'Norsk',
    'nl_NL' => 'Nederlands',
    'pl_PL' => 'Polski',
    'pt_PT' => 'Português',
    'ro_RO' => 'Română',
    'ru_RU' => 'Русский',
    'sv_SE' => 'Svenska',
    'th_TH' => 'ไทย',
    'tr_TR' => 'Türkçe',
    'zh_TW' => '繁體中文',
    'zh_CN' => '简体中文',
    'pt_BR' => 'Português Brasileiro',
    'ca_ES' => 'Català',
    'en_UK' => 'English (UK)',
    'sr_RS' => 'Српски',
    'sk_SK' => 'Slovenčina',
    'sq_AL' => 'Shqip',
    'et_EE' => 'Eesti',
    'es_LA' => 'Español (Latinoamérica)',
    'fi_FI' => 'Suomi',
    'ar_SA' => 'العربية',
    'uk_UA' => 'Українська',
  ),
  'large_scale_test' => false,
  'lead_conv_activity_opt' => 'donothing',
  'list_max_entries_per_page' => 20,
  'list_max_entries_per_subpanel' => 5,
  'lock_default_user_name' => false,
  'lock_homepage' => false,
  'lock_subpanels' => false,
  'log_dir' => '.',
  'log_file' => 'sugarcrm.log',
  'log_memory_usage' => false,
  'logger' => 
  array (
    'level' => 'fatal',
    'file' => 
    array (
      'name' => 'sugarcrm',
      'dateFormat' => '%c',
      'maxSize' => '10MB',
      'maxLogs' => 10,
      'suffix' => '',
    ),
  ),
  'login_page' => 
  array (
    'marketing_extras_content' => 
    array (
      'url' => 'https://www.sugarcrm.com/product-login-page-service/',
      'static_url' => 'include/MarketingExtras/StaticMarketingContent/static.html',
      'connect_timeout_ms' => 150,
      'timeout_ms' => 300,
    ),
  ),
  'map_key' => 
  array (
    'bing' => 'AuIEmeKGjvNV037yKiYGV0B3cxqNmx6FWnode0Hj8tjQPjK1zJP-7GAESccoCDUA',
  ),
  'marketing_extras_url' => 'https://marketing.sugarcrm.com/content',
  'mass_actions' => 
  array (
  ),
  'max_aggregate_email_attachments_bytes' => 10000000,
  'max_dashlets_homepage' => '15',
  'max_record_fetch_size' => 1000,
  'max_record_link_fetch_size' => 5000,
  'merge_duplicates' => 
  array (
    'merge_relate_fetch_concurrency' => 2,
    'merge_relate_fetch_timeout' => 90000,
    'merge_relate_fetch_limit' => 20,
    'merge_relate_update_concurrency' => 4,
    'merge_relate_update_timeout' => 90000,
    'merge_relate_max_attempt' => 3,
  ),
  'name_formats' => 
  array (
    's f l' => 's f l',
    'f l' => 'f l',
    's l' => 's l',
    'l, s f' => 'l, s f',
    'l, f' => 'l, f',
    's l, f' => 's l, f',
    'l s f' => 'l s f',
    'l f s' => 'l f s',
  ),
  'new_email_addresses_opted_out' => false,
  'oauth_token_expiry' => 0,
  'oauth_token_life' => 86400,
  'passwordsetting' => 
  array (
    'minpwdlength' => 6,
    'maxpwdlength' => '',
    'oneupper' => true,
    'onelower' => true,
    'onenumber' => true,
    'onespecial' => '',
    'SystemGeneratedPasswordON' => false,
    'lostpasswordtmpl' => '2ce065bc-bf1a-11f0-ac2f-8c8590aaa72b',
    'customregex' => '',
    'regexcomment' => '',
    'forgotpasswordON' => true,
    'linkexpiration' => true,
    'linkexpirationtime' => 24,
    'linkexpirationtype' => 60,
    'userexpiration' => '0',
    'userexpirationtime' => '',
    'userexpirationtype' => '1',
    'userexpirationlogin' => '',
    'systexpiration' => 1,
    'systexpirationtime' => 7,
    'systexpirationtype' => '0',
    'systexpirationlogin' => '',
    'lockoutexpiration' => '0',
    'lockoutexpirationtime' => '',
    'lockoutexpirationtype' => '1',
    'lockoutexpirationlogin' => '',
  ),
  'pdf_file_max_lifetime' => 86400,
  'pmse_settings_default' => 
  array (
    'logger_level' => 'critical',
    'error_timeout' => '40',
  ),
  'portal_view' => 'single_user',
  'preview_edit' => true,
  'processes_auto_save_interval' => 30000,
  'processes_auto_validate_on_autosave' => true,
  'processes_auto_validate_on_import' => true,
  'product_definition' => 
  array (
    'type' => 'Http',
    'options' => 
    array (
      'base_uri' => 'https://updates.sugarcrm.com/spds/',
    ),
  ),
  'push_notification' => 
  array (
    'enabled' => false,
  ),
  'reports_complexity_display' => 
  array (
    'simplified' => 30000,
    'export' => 100000,
  ),
  'require_accounts' => true,
  'resource_management' => 
  array (
    'special_query_limit' => 50000,
    'special_query_modules' => 
    array (
      0 => 'Reports',
      1 => 'Export',
      2 => 'Import',
      3 => 'Administration',
      4 => 'Sync',
    ),
    'default_limit' => 1000,
  ),
  'roleBasedViews' => true,
  'rss_cache_time' => '10800',
  'save_query' => 'all',
  'search_wildcard_char' => '%',
  'search_wildcard_infront' => false,
  'security' => 
  array (
    'private_ips' => 
    array (
      0 => '10.0.0.0|10.255.255.255',
      1 => '172.16.0.0|172.31.255.255',
      2 => '192.168.0.0|192.168.255.255',
      3 => '169.254.0.0|169.254.255.255',
      4 => '127.0.0.0|127.255.255.255',
    ),
  ),
  'session_dir' => '',
  'showDetailData' => true,
  'showThemePicker' => true,
  'site_url' => 'http://localhost:8888/fourt',
  'slow_query_time_msec' => '100',
  'smtp_mailer_debug' => 0,
  'snip_url' => 'https://ease.sugarcrm.com/',
  'sugar_max_int' => 2147483647,
  'sugar_min_int' => -2147483648,
  'sugar_push' => 
  array (
    'max_retries' => 2,
    'service_urls' => 
    array (
      'default' => 'https://sugarpush-%s-prod.service.sugarcrm.com',
      'us-west-2' => 'https://sugarpush-us-west-2-prod.service.sugarcrm.com',
    ),
  ),
  'sugar_version' => '14.0.0',
  'team_based_acl' => 
  array (
    'enabled' => false,
    'enabled_modules' => 
    array (
    ),
  ),
  'time_formats' => 
  array (
    'H:i' => '23:00',
    'h:ia' => '11:00pm',
    'h:iA' => '11:00PM',
    'h:i a' => '11:00 pm',
    'h:i A' => '11:00 PM',
    'H.i' => '23.00',
    'h.ia' => '11.00pm',
    'h.iA' => '11.00PM',
    'h.i a' => '11.00 pm',
    'h.i A' => '11.00 PM',
  ),
  'timef' => 'H:i',
  'tmp_dir' => 'cache/xml/',
  'tmp_file_max_lifetime' => 86400,
  'tracker_max_display_length' => 30,
  'translation_string_prefix' => false,
  'unique_key' => 'ca640762941bf68c30121cc6fe9c193d',
  'upload_badext' => 
  array (
    0 => 'php',
    1 => 'php3',
    2 => 'php4',
    3 => 'php5',
    4 => 'pl',
    5 => 'cgi',
    6 => 'py',
    7 => 'asp',
    8 => 'cfm',
    9 => 'js',
    10 => 'vbs',
    11 => 'html',
    12 => 'htm',
  ),
  'upload_dir' => 'upload/',
  'upload_maxsize' => 30000000,
  'use_common_ml_dir' => false,
  'use_real_names' => true,
  'use_sprites' => true,
  'vcal_time' => '2',
  'verify_client_ip' => false,
  'wl_list_max_entries_per_page' => 10,
  'wl_list_max_entries_per_subpanel' => 3,
  'emailTemplate' => 
  array (
    'Meeting' => '2cd9b0aa-bf1a-11f0-885c-8c8590aaa72b',
    'Call' => '2cdba54a-bf1a-11f0-8d0e-8c8590aaa72b',
    '‌ReportSchedule' => '2cdd1448-bf1a-11f0-93f3-8c8590aaa72b',
    'CommentLogMention' => '2cde7c20-bf1a-11f0-92c4-8c8590aaa72b',
  ),
  'portalpasswordsetting' => 
  array (
    'lostpasswordtmpl' => 'f29f5864-bc90-11e9-82c9-a45e60e684a5',
    'resetpasswordtmpl' => '12d9843e-bed9-11e9-8cec-6003089fe26e',
  ),
);