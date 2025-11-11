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

use PhpParser\Error;
use PhpParser\ParserFactory;
use Sugarcrm\Sugarcrm\DependencyInjection\Container;
use Sugarcrm\Sugarcrm\FeatureToggle\FeatureFlag;
use Sugarcrm\Sugarcrm\FeatureToggle\Features\EnhancedModuleChecks;
use Sugarcrm\Sugarcrm\FeatureToggle\Features\StrictIncludes;
use Sugarcrm\Sugarcrm\FeatureToggle\Features\StrictManifestChecks;
use Sugarcrm\Sugarcrm\FeatureToggle\Features\TranslateMLPCode;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\BlacklistVisitor;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\CodeScanner;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\DynamicNameVisitor;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\IncludesVisitor;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\PhpCodeDetected;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\ManifestScanner;
use Sugarcrm\Sugarcrm\Security\Validator\Constraints\DropdownList as ConstraintsDropdownList;
use Sugarcrm\Sugarcrm\Security\Validator\Validator;
use Sugarcrm\Sugarcrm\Util\Files\FileLoader;
use Sugarcrm\Sugarcrm\Entitlements\SubscriptionManager;

class ModuleScanner
{
    /**
     * @var HealthCheckScanner
     */
    private $healthCheck;

    private $manifestMap = [
        'pre_execute' => 'pre_execute',
        'install_mkdirs' => 'mkdir',
        'install_copy' => 'copy',
        'install_images' => 'image_dir',
        'install_menus' => 'menu',
        'install_userpage' => 'user_page',
        'install_administration' => 'administration',
        'install_connectors' => 'connectors',
        'install_vardefs' => 'vardefs',
        'install_layoutdefs' => 'layoutdefs',
        'install_layoutfields' => 'layoutfields',
        'install_relationships' => 'relationships',
        'install_languages' => 'language',
        'install_logichooks' => 'logic_hooks',
        'post_execute' => 'post_execute',

    ];

    /**
     * config settings
     * @var array
     */
    private $config = [];
    private $config_hash;

    private $blackListExempt = [];
    private $classBlackListExempt = [];

    // Bug 56717 - adding hbs extension to the whitelist - rgonzalez
    private $validExt = ['png', 'gif', 'jpg', 'css', 'js', 'php', 'txt', 'html', 'htm', 'tpl', 'pdf', 'md5', 'xml', 'hbs', 'less', 'wsdl'];
    private $classBlackList = [
        // Class names specified here must be in lowercase as the implementation
        // of the tokenizer converts all tokens to lowercase.
        'reflection',
        'reflectionclass',
        'reflectionzendextension',
        'reflectionextension',
        'reflectionfunction',
        'reflectionfunctionabstract',
        'reflectionmethod',
        'reflectionobject',
        'reflectionparameter',
        'reflectionproperty',
        'reflector',
        'reflectionexception',
        'lua',
        'ziparchive',
        'splfileinfo',
        'splfileobject',
        'sugarautoloader',
        'sugarmin',
        'sugarcronparalleljobs',
        'sugarcronjobs',
        'symfony\component\security\core\authentication\token\abstracttoken',
        'symfony\component\security\core\authentication\token\remembermetoken',
        'symfony\component\expressionlanguage\serializedparsedexpression',
        'pdo',
    ];
    private $blackList = [
        'popen',
        'proc_open',
        'error_log',
        'escapeshellarg',
        'escapeshellcmd',
        'proc_close',
        'proc_get_status',
        'proc_nice',
        'passthru',
        'clearstatcache',
        'disk_free_space',
        'disk_total_space',
        'diskfreespace',
        'dir',
        'fclose',
        'feof',
        'fflush',
        'fgetc',
        'fgetcsv',
        'fgets',
        'fgetss',
        'file_exists',
        'file_get_contents',
        'filesize',
        'filetype',
        'flock',
        'fnmatch',
        'fpassthru',
        'fputcsv',
        'fputs',
        'fread',
        'fscanf',
        'fseek',
        'fstat',
        'ftell',
        'ftruncate',
        'fwrite',
        'glob',
        'is_dir',
        'is_file',
        'is_link',
        'is_readable',
        'is_uploaded_file',
        'opendir',
        'parse_ini_string',
        'pathinfo',
        'pclose',
        'readfile',
        'readlink',
        'realpath_cache_get',
        'realpath_cache_size',
        'realpath',
        'rewind',
        'readdir',
        'readline_add_history',
        'readline_write_history',
        'set_file_buffer',
        'tmpfile',
        'umask',
        'ini_set',
        'set_time_limit',
        'eval',
        'exec',
        'system',
        'shell_exec',
        'passthru',
        'chgrp',
        'chmod',
        'chown',
        'file_put_contents',
        'file',
        'fileatime',
        'filectime',
        'filegroup',
        'fileinode',
        'filemtime',
        'fileowner',
        'fileperms',
        'fopen',
        'is_executable',
        'is_writable',
        'is_writeable',
        'lchgrp',
        'lchown',
        'linkinfo',
        'lstat',
        'mkdir',
        'mkdir_recursive',
        'parse_ini_file',
        'rmdir',
        'rmdir_recursive',
        'stat',
        'tempnam',
        'touch',
        'unlink',
        'getimagesize',
        'call_user_func',
        'call_user_func_array',
        'create_function',
        'session_save_path',


        //mutliple files per function call
        'copy',
        'copy_recursive',
        'link',
        'rename',
        'symlink',
        'move_uploaded_file',
        'chdir',
        'chroot',
        'create_cache_directory',
        'mk_temp_dir',
        'write_array_to_file',
        'write_array_to_file_as_key_value_pair',
        'create_custom_directory',
        'sugar_rename',
        'sugar_chown',
        'sugar_fopen',
        'sugar_mkdir',
        'sugar_file_put_contents',
        'sugar_file_put_contents_atomic',
        'sugar_chgrp',
        'sugar_chmod',
        'sugar_touch',

        // Functions that have callbacks can circumvent our security measures.
        // List retrieved through PHP's XML documentation, and running the
        // following script in the reference directory:

        // grep -R callable . | grep -v \.svn | grep methodparam | cut -d: -f1 | sort -u | cut -d"." -f2 | sed 's/\-/\_/g' | cut -d"/" -f4

        // AMQPQueue
        'consume',

        // PHP internal - arrays
        'array_diff_uassoc',
        'array_diff_ukey',
        'array_filter',
        'array_intersect_uassoc',
        'array_intersect_ukey',
        'array_map',
        'array_reduce',
        'array_udiff_assoc',
        'array_udiff_uassoc',
        'array_udiff',
        'array_uintersect_assoc',
        'array_uintersect_uassoc',
        'array_uintersect',
        'array_walk_recursive',
        'array_walk',
        'uasort',
        'uksort',
        'usort',

        // EIO functions that accept callbacks.
        'eio_busy',
        'eio_chmod',
        'eio_chown',
        'eio_close',
        'eio_custom',
        'eio_dup2',
        'eio_fallocate',
        'eio_fchmod',
        'eio_fchown',
        'eio_fdatasync',
        'eio_fstat',
        'eio_fstatvfs',
        'eio_fsync',
        'eio_ftruncate',
        'eio_futime',
        'eio_grp',
        'eio_link',
        'eio_lstat',
        'eio_mkdir',
        'eio_mknod',
        'eio_nop',
        'eio_open',
        'eio_read',
        'eio_readahead',
        'eio_readdir',
        'eio_readlink',
        'eio_realpath',
        'eio_rename',
        'eio_rmdir',
        'eio_sendfile',
        'eio_stat',
        'eio_statvfs',
        'eio_symlink',
        'eio_sync_file_range',
        'eio_sync',
        'eio_syncfs',
        'eio_truncate',
        'eio_unlink',
        'eio_utime',
        'eio_write',

        // PHP internal - error functions
        'set_error_handler',
        'set_exception_handler',

        // Forms Data Format functions
        'fdf_enum_values',

        // PHP internal - function handling
        'call_user_func_array',
        'call_user_func',
        'forward_static_call_array',
        'forward_static_call',
        'register_shutdown_function',
        'register_tick_function',

        // Gearman
        'setclientcallback',
        'setcompletecallback',
        'setdatacallback',
        'setexceptioncallback',
        'setfailcallback',
        'setstatuscallback',
        'setwarningcallback',
        'setworkloadcallback',
        'addfunction',

        // Firebird/InterBase
        'ibase_set_event_handler',

        // LDAP
        'ldap_set_rebind_proc',

        // LibXML
        'libxml_set_external_entity_loader',

        // Mailparse functions
        'mailparse_msg_extract_part_file',
        'mailparse_msg_extract_part',
        'mailparse_msg_extract_whole_part_file',

        // Memcache(d) functions
        'addserver',
        'setserverparams',
        'get',
        'getbykey',
        'getdelayed',
        'getdelayedbykey',

        // MySQLi
        'set_local_infile_handler',

        // PHP internal - network functions
        'header_register_callback',

        // Newt
        'newt_entry_set_filter',
        'newt_set_suspend_callback',

        // OAuth
        'consumerhandler',
        'timestampnoncehandler',
        'tokenhandler',

        // PHP internal - output control
        'ob_start',

        // PHP internal - PCNTL
        'pcntl_signal',

        // PHP internal - PCRE
        'preg_replace_callback',

        // SQLite
        'sqlitecreateaggregate',
        'sqlitecreatefunction',
        'sqlite_create_aggregate',
        'sqlite_create_function',

        // RarArchive
        'open',

        // Readline
        'readline_callback_handler_install',
        'readline_completion_function',

        // PHP internal - session handling
        'session_set_save_handler',

        // PHP internal - SPL
        'construct',
        'iterator_apply',
        'spl_autoload_register',

        // Sybase
        'sybase_set_message_handler',

        // PHP internal - variable handling
        'is_callable',

        // XML Parser
        'xml_set_character_data_handler',
        'xml_set_default_handler',
        'xml_set_element_handler',
        'xml_set_end_namespace_decl_handler',
        'xml_set_external_entity_ref_handler',
        'xml_set_notation_decl_handler',
        'xml_set_processing_instruction_handler',
        'xml_set_start_namespace_decl_handler',
        'xml_set_unparsed_entity_decl_handler',
        'simplexml_load_file',
        'simplexml_load_string',

        // unzip
        'unzip',
        'unzip_file',

        // sugar vulnerable functions, need to be lower case
        'getfunctionvalue',
        'save_custom_app_list_strings_contents',
    ];
    private $unsafeHttpClientFunctions = [
        // curl
        'curl_copy_handle',
        'curl_exec',
        'curl_file_create',
        'curl_init',
        'curl_multi_add_handle',
        'curl_multi_exec',
        'curl_multi_getcontent',
        'curl_multi_info_read',
        'curl_multi_init',
        'curl_multi_remove_handle',
        'curl_multi_select',
        'curl_multi_setopt',
        'curl_setopt_array',
        'curl_setopt',
        'curl_share_init',
        'curl_share_setopt',
        'curl_share_strerror',
        //sockets
        'socket_accept',
        'socket_addrinfo_bind',
        'socket_addrinfo_connect',
        'socket_addrinfo_explain',
        'socket_addrinfo_lookup',
        'socket_bind',
        'socket_clear_error',
        'socket_close',
        'socket_cmsg_space',
        'socket_connect',
        'socket_create_listen',
        'socket_create_pair',
        'socket_create',
        'socket_export_stream',
        'socket_get_option',
        'socket_getopt',
        'socket_getpeername',
        'socket_getsockname',
        'socket_import_stream',
        'socket_last_error',
        'socket_listen',
        'socket_read',
        'socket_recv',
        'socket_recvfrom',
        'socket_recvmsg',
        'socket_select',
        'socket_send',
        'socket_sendmsg',
        'socket_sendto',
        'socket_set_block',
        'socket_set_nonblock',
        'socket_set_option',
        'socket_setopt',
        'socket_shutdown',
        'socket_write',
        'fsockopen',
        'pfsockopen',
        // streams
        'stream_bucket_append',
        'stream_bucket_make_writeable',
        'stream_bucket_new',
        'stream_bucket_prepend',
        'stream_context_create',
        'stream_context_get_default',
        'stream_context_get_options',
        'stream_context_get_params',
        'stream_context_set_default',
        'stream_context_set_option',
        'stream_context_set_params',
        'stream_copy_to_stream',
        'stream_filter_append',
        'stream_filter_prepend',
        'stream_filter_register',
        'stream_filter_remove',
        'stream_get_contents',
        'stream_get_filters',
        'stream_get_line',
        'stream_get_meta_data',
        'stream_get_transports',
        'stream_get_wrappers',
        'stream_is_local',
        'stream_isatty',
        'stream_notification_callback',
        'stream_register_wrapper',
        'stream_resolve_include_path',
        'stream_select',
        'stream_set_blocking',
        'stream_set_chunk_size',
        'stream_set_read_buffer',
        'stream_set_timeout',
        'stream_set_write_buffer',
        'stream_socket_accept',
        'stream_socket_client',
        'stream_socket_enable_crypto',
        'stream_socket_get_name',
        'stream_socket_pair',
        'stream_socket_recvfrom',
        'stream_socket_sendto',
        'stream_socket_server',
        'stream_socket_shutdown',
        'stream_supports_lock',
        'stream_wrapper_register',
        'stream_wrapper_restore',
        'stream_wrapper_unregister',
    ];

    private $methodsBlackList = [
        'setlevel',
        'put' => ['sugarautoloader'],
        'unlink' => ['sugarautoloader'],
        'minify' => ['sugarmin'],
    ];

    private array $effectiveDenyLists = [];
    /**
     * @var CodeScanner
     */
    private $codeScanner;

    /**
     * @var ManifestScanner
     */
    private $manifestScanner;

    protected $installdefs;

    public function printToWiki()
    {
        echo "'''Default Extensions'''<br>";
        foreach ($this->validExt as $b) {
            echo '#' . $b . '<br>';
        }
        echo "'''Default Deny Listed Functions'''<br>";
        foreach ($this->blackList as $b) {
            echo '#' . $b . '<br>';
        }
    }

    public function __construct()
    {
        $params = [
            'blackListExempt' => 'MODULE_INSTALLER_PACKAGE_SCAN_BLACK_LIST_EXEMPT',
            'blackList' => 'MODULE_INSTALLER_PACKAGE_SCAN_BLACK_LIST',
            'classBlackListExempt' => 'MODULE_INSTALLER_PACKAGE_SCAN_CLASS_BLACK_LIST_EXEMPT',
            'classBlackList' => 'MODULE_INSTALLER_PACKAGE_SCAN_CLASS_BLACK_LIST',
            'validExt' => 'MODULE_INSTALLER_PACKAGE_SCAN_VALID_EXT',
            'methodsBlackList' => 'MODULE_INSTALLER_PACKAGE_SCAN_METHOD_LIST',
        ];

        $disableConfigOverride = defined('MODULE_INSTALLER_DISABLE_CONFIG_OVERRIDE')
            && MODULE_INSTALLER_DISABLE_CONFIG_OVERRIDE;

        $disableDefineOverride = defined('MODULE_INSTALLER_DISABLE_DEFINE_OVERRIDE')
            && MODULE_INSTALLER_DISABLE_DEFINE_OVERRIDE;

        if (!$disableConfigOverride && !empty($GLOBALS['sugar_config']['moduleInstaller'])) {
            $this->config = $GLOBALS['sugar_config']['moduleInstaller'];
        }

        foreach ($params as $param => $constName) {
            if (!$disableConfigOverride && isset($this->config[$param]) && is_array($this->config[$param])) {
                $this->{$param} = array_merge($this->{$param}, $this->config[$param]);
            }

            if (!$disableDefineOverride && defined($constName)) {
                $value = constant($constName);
                $value = explode(',', $value);
                $value = array_map('trim', $value);
                $value = array_filter($value, 'strlen');
                $this->{$param} = array_merge($this->{$param}, $value);
            }
        }
        $classesBlackList = array_diff($this->classBlackList, $this->classBlackListExempt);
        $features = Container::getInstance()->get(FeatureFlag::class);
        if ($features->isEnabled(EnhancedModuleChecks::getName())) {
            $this->blackList = array_merge($this->blackList, $this->unsafeHttpClientFunctions);
        }
        $functionsBlackList = array_diff($this->blackList, $this->blackListExempt);
        $this->effectiveDenyLists = [
            'classes' => $classesBlackList,
            'functions' => $functionsBlackList,
            'methods' => $this->methodsBlackList,
        ];

        $codeScanner = new CodeScanner();
        if (!$features->isEnabled(TranslateMLPCode::getName())) {
            $codeScanner->registerVisitor(new DynamicNameVisitor());
        }
        if ($features->isEnabled(StrictIncludes::getName())) {
            $codeScanner->registerVisitor(new IncludesVisitor());
        }
        $codeScanner->registerVisitor(
            new BlacklistVisitor($classesBlackList, $functionsBlackList, $this->methodsBlackList)
        );
        $this->codeScanner = $codeScanner;
        if ($features->isEnabled(StrictManifestChecks::getName())) {
            $this->manifestScanner = new ManifestScanner();
        }
    }

    public function getEffectiveDenyLists(): array
    {
        return $this->effectiveDenyLists;
    }

    private $issues = [];
    private $pathToModule = '';
    private $baseDir = '';

    /**
     *returns a list of issues
     */
    public function getIssues()
    {
        return $this->issues;
    }

    /**
     *returns true or false if any issues were found
     */
    public function hasIssues()
    {
        return !empty($this->issues);
    }

    /**
     *Ensures that a file has a valid extension
     */
    public function isValidExtension($file)
    {
        $file = strtolower($file);
        $pi = pathinfo($file);

        // because of SC-3079, LICENSE doesn't have an extension any more, so if the base name is LICENSE
        // let it pass
        if ($pi['basename'] === 'license') {
            return true;
        }

        //make sure they don't override the files.md5
        if (empty($pi['extension']) || $pi['basename'] == 'files.md5') {
            return false;
        }
        return in_array($pi['extension'], $this->validExt);
    }

    public function isConfigFile($file)
    {
        $real = realpath($file);
        if ($real == realpath('config.php')) {
            return true;
        }
        if (file_exists('config_override.php') && $real == realpath('config_override.php')) {
            return true;
        }
        return false;
    }

    /**
     * check if it is a vardef file
     * @param string $file
     * @return bool
     */
    protected function isVardefFile($file)
    {
        if ($this->isVardefsFileNameOrDir($file)) {
            return true;
        }

        // check manifest file
        if (isset($this->installdefs['vardefs'])) {
            foreach ($this->installdefs['vardefs'] as $pack) {
                $pack['from'] = str_replace('<basepath>', '', $pack['from']);
                if ($pack['from'] == str_replace($this->baseDir, '', $file)) {
                    return true;
                }
            }
        }

        // check distination
        if (isset($this->installdefs['copy'])) {
            foreach ($this->installdefs['copy'] as $pack) {
                $pack['from'] = str_replace('<basepath>', '', $pack['from']);
                if ($pack['from'] == str_replace($this->baseDir, '', $file)) {
                    // check target file or dir
                    if (isset($pack['to'])) {
                        return $this->isVardefsFileNameOrDir($pack['to']);
                    }
                }
            }
        }

        return false;
    }

    /**
     * check if vardefs file name and parent dir
     * @param $file
     * @return bool
     */
    protected function isVardefsFileNameOrDir($file)
    {
        $fileInfo = pathinfo($file);
        if ($fileInfo['basename'] === 'vardefs.php' || $fileInfo['basename'] === 'vardefs.ext.php') {
            return true;
        }

        // check if parent dir is 'Vardefs'
        if (basename($fileInfo['dirname']) === 'Vardefs') {
            return true;
        }

        return false;
    }

    /**
     * Scans a directory and calls on scan file for each file
     * @param string $path path of the directory to be scanned
     * @param string $sugarFileAllowed whether should allow to override core files
     **/
    public function scanDir($path, $sugarFileAllowed = true)
    {
        static $startPath = '';
        if (empty($startPath)) {
            $startPath = $path;
        }
        if (!is_dir($path)) {
            return false;
        }
        $d = dir($path);
        while ($e = $d->read()) {
            $next = $path . '/' . $e;
            if (is_dir($next)) {
                if (empty($e) || $e == '.' || $e == '..') {
                    continue;
                }
                $this->scanDir($next, $sugarFileAllowed);
            } else {
                $issues = $this->scanFile($next, $sugarFileAllowed);
                $nextFileContents = file_get_contents($next);

                if ($this->isLanguageFile($next)) {
                    $this->checkLanguageFileKeysValidity($next);
                }

                // scan vardef file
                if ($this->isVardefFile($next)) {
                    $this->scanVardefFile($next);
                }

                if ($this->isCreateActionsFile($next)) {
                    $this->healthCheck->updateStatus('hasCustomCreateActions', $next);
                }

                if ($this->isSidecarJSFile($next)) {
                    $this->healthCheck->scanFileForDeprecatedJSCode($next, $nextFileContents);
                }

                if ($this->isSidecarHBSFile($next)) {
                    $this->healthCheck->scanFileForDeprecatedHBSCode($next, $nextFileContents);
                }

                if ($this->isCustomLESSFile($next)) {
                    $this->healthCheck->scanFileForDeprecatedLESSColorVariables($next, $nextFileContents);
                }

                if ($this->isExtensionPhpFile($next)) {
                    $this->healthCheck->scanForOutputConstructs($nextFileContents, $next, true);
                }

                if ($this->isHookMetaFile($next)) {
                    $hook_files = [];
                    $this->healthCheck->extractHooks($next, $hook_files, true);
                    foreach ($hook_files as $hookname => $hooks) {
                        foreach ($hooks as $hook_data) {
                            $this->healthCheck->scanFileForDeprecatedCode($hook_data[2], file_get_contents($hook_data[2]));
                        }
                    }
                }

                if ($this->isPhpFile($next)) {
                    $this->healthCheck->scanFileForInvalidReferences($next, $nextFileContents);
                    $this->healthCheck->scanFileForSessionArrayReferences($next, $nextFileContents);
                    $this->healthCheck->scanFileForDeprecatedCode($next, $nextFileContents);
                }
            }
        }
        return true;
    }

    /**
     * Tells whether the filename is a Logic Hook meta file
     * @param string $file Path to the file
     * @return bool
     */
    protected function isHookMetaFile(string $file): bool
    {
        if (false !== strpos($file, 'custom/modules/logic_hooks.php')) {
            return true;
        }
        if (false !== strpos($file, 'custom/application/Ext/LogicHooks/logichooks.ext.php')) {
            return true;
        }
        return false;
    }

    /**
     * Tells whether the filename is an extension-related PHP file
     * @param string $file Path to the file
     * @return bool
     */
    protected function isExtensionPhpFile(string $file): bool
    {
        if (in_array($file, $this->healthCheck->getIgnoredFiles()) || in_array($file, $this->healthCheck->getIgnoreOutputCheckFiles())) {
            return false;
        }
        if (!preg_match('~custom/Extension/modules/([a-z][a-z0-9_\-]*)/Ext~is', $file)) {
            return false;
        }
        return $this->isPhpFile($file);
    }


    /**
     * Tells whether the filename is a PHP file
     * @param string $file Path to the file
     * @return bool
     */
    protected function isPhpFile(string $file): bool
    {
        return 'php' === pathinfo($file, PATHINFO_EXTENSION);
    }

    /**
     * Tells whether the filename is a file related to create actions
     * @param string $file Path to the file
     * @return bool
     */
    protected function isCreateActionsFile(string $file): bool
    {
        return (bool)preg_match('~create-actions\.(php|js|hbs)$~', basename($file));
    }

    /**
     * Tells whether the filename is a sidecar JS file
     * @param string $file Path to the file
     * @return bool
     */
    protected function isSidecarJSFile(string $file): bool
    {
        return (bool)preg_match('~clients/.*?/(layouts|views|fields)/.*?/.*?\.js$~', $file);
    }

    /**
     * Tells whether the filename is a sidecar HBS file
     * @param string $file Path to the file
     * @return bool
     */
    protected function isSidecarHBSFile(string $file): bool
    {
        return (bool)preg_match('~clients/.*?/(layouts|views|fields)/.*?/.*?\.hbs$~', $file);
    }

    /**
     * Determines whether the filename is a custom LESS file
     * @param string $file Path to the file
     * @return bool
     */
    protected function isCustomLESSFile(string $file): bool
    {
        return (bool)preg_match('~custom/themes/.*?.less$~', $file);
    }

    /**
     * Checks if a file is a language file from manifest installdefs
     * @param string $file path to file
     * @return bool true if file is a language file from manifest.
     */
    private function isLanguageFile($file)
    {
        $isLanguageFile = false;
        if (!isset($this->installdefs['language'])) {
            return $isLanguageFile;
        }
        foreach ($this->installdefs['language'] as $pack) {
            $pack['from'] = str_replace('<basepath>', '', $pack['from']);
            if ($pack['from'] == str_replace($this->baseDir, '', $file)) {
                $isLanguageFile = true;
                break;
            }
        }
        return $isLanguageFile;
    }

    /**
     * Checks language file keys validity (starts with letter and contains only letters, numbers and underscore)
     * @param $file string path to file
     * @return array issues.
     */
    private function checkLanguageFileKeysValidity($file)
    {
        include $file;
        if (!empty($app_list_strings) && is_array($app_list_strings)) {
            $constraint = new ConstraintsDropdownList();
            $violations = Validator::getService()->validate($app_list_strings, $constraint);
            if ($violations->count() > 0) {
                foreach ($violations as $violation) {
                    $this->issues['file'][$file][] = $violation->getMessage();
                }
                return $this->issues['file'][$file];
            }
        }
        return [];
    }

    /**
     * Given a file it will open it's contents and check if it is a PHP file (not safe to just rely on extensions) if it finds <?php tags it will use the tokenizer to scan the file
     * $var()  and ` are always prevented then whatever is in the blacklist.
     * It will also ensure that all files are of valid extension types
     * @param string $file file to be scanned
     * @param string $sugarFileAllowed whether should allow to override core files
     *
     */
    public function scanFile($file, $sugarFileAllowed = true)
    {
        $issues = [];
        if (!$this->isValidExtension($file)) {
            $issues[] = translate('ML_INVALID_EXT');
            $this->issues['file'][$file] = $issues;
            return $issues;
        }
        if ($this->isConfigFile($file)) {
            $issues[] = translate('ML_OVERRIDE_CORE_FILES');
            $this->issues['file'][$file] = $issues;
            return $issues;
        }
        if (!$sugarFileAllowed) {
            $baseDir = $this->baseDir;
            if (!empty($this->baseDir) && substr($this->baseDir, -1) != '/') {
                $baseDir = $this->baseDir . '/';
            }
            $fileNoBase = str_replace($baseDir, '', $file);
            if ($this->sugarFileExists($fileNoBase)) {
                $issues[] = translate('ML_OVERRIDE_CORE_FILES');
                $this->issues['file'][$file] = $issues;
                return $issues;
            }
        }
        $contents = file_get_contents($file);
        if (!$this->isPhpFile($file) && (str_contains($contents, '<?php') || str_contains($contents, '<?='))) {
            $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
            try {
                $parser->parse($contents);
                $issues[] = (new PhpCodeDetected($file))->getMessage();
            } catch (\Throwable $error) {
            }
        } else {
            $issues = $this->scanCode($contents);
        }

        if (safeCount($issues) > 0) {
            $this->issues['file'][$file] = $issues;
        }

        return $issues;
    }

    /**
     * scan vardef file, make sure the function used are not in denylist
     * @param $file
     * @return array
     */
    protected function scanVardefFile($file)
    {
        $issues = [];
        if (!$this->isVardefFile($file)) {
            return $issues;
        }

        $dictionary = [];
        include $file;

        if (empty($dictionary)) {
            return $issues;
        }
        foreach ($dictionary as $module => $vardefs) {
            // check 'function' attribute
            if (isset($vardefs['fields'])) {
                foreach ($vardefs['fields'] as $field => $def) {
                    $functions = [];
                    if (isset($def['function_name'])) {
                        if (is_array($def['function_name'])) {
                            $functions = $def['function_name'];
                        } else {
                            $functions[] = [$def['function_name']];
                        }
                    } elseif (isset($def['function'])) {
                        if (is_string($def['function'])) {
                            $functions = [$def['function']];
                        } else {
                            if (!empty($def['function']['name'])) {
                                if (is_array($def['function']['name'])) {
                                    $functions = $def['function']['name'];
                                } else {
                                    $functions = [$def['function']['name']];
                                }
                            }
                        }
                        if (!empty($def['function']['include'])) {
                            if (!check_file_name($def['function']['include'])) {
                                $issues[] = translate('ML_INVALID_INCLUDE') . ' ' . $def['function']['include'];
                            }
                        }
                    }
                    if (!empty($functions)) {
                        foreach ($functions as $function) {
                            if (is_string($function)) {
                                $canonicalFunctionName = ltrim(strtolower($function), '\\');
                                if (!in_array($canonicalFunctionName, $this->blackListExempt)
                                    && in_array($canonicalFunctionName, $this->blackList)
                                ) {
                                    $issues[] = translate('ML_INVALID_FUNCTION') . ' ' . $function . '()';
                                }
                            } else {
                                // wrong format
                                $issues[] = translate('ML_INVALID_FUNCTION') . ' ' . print_r($function, true);
                            }
                        }
                    }
                }
            }
        }

        if (safeCount($issues) > 0) {
            $this->issues['file'][$file] = $issues;
        }
        return $issues;
    }

    /**
     * checks files.md5 file to see if the file is from sugar
     * ONLY WORKS ON FILES
     *
     * @param string $path
     * @return bool
     */
    public function sugarFileExists($path)
    {
        $md5_string = null;
        static $md5 = [];
        if (empty($md5) && file_exists('files.md5')) {
            include 'files.md5';
            $md5 = $md5_string;
        }
        if ($path[0] != '.' || $path[1] != '/') {
            $path = './' . $path;
        }
        if (isset($md5[$path])) {
            return true;
        }

        return false;
    }

    /**
     * Normalize a path to not contain dots & multiple slashes
     *
     * @param string $path
     * @return string false
     */
    public function normalizePath($path)
    {
        if (DIRECTORY_SEPARATOR != '/') {
            // convert to / for OSes that use other separators
            $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        }
        $res = [];
        foreach (explode('/', $path) as $component) {
            if (empty($component)) {
                continue;
            }
            if ($component == '.') {
                continue;
            }
            if ($component == '..') {
                // this is not allowed, bail
                return false;
            }
            $res[] = $component;
        }

        return join('/', $res);
    }

    /**
     * @param string $manifestPath
     * @return array
     */
    public function strictManifestScan(string $manifestPath): array
    {
        if (isset($this->manifestScanner)) {
            $issues = $this->manifestScanner->scan(file_get_contents($manifestPath));
            foreach ($issues as $issue) {
                $this->issues['manifest'][$manifestPath][] = $issue->getMessage();
            }
            return $issues;
        }
        return [];
    }

    /**
     *This function will scan the Manifest for disabled actions specified in $GLOBALS['sugar_config']['moduleInstaller']['disableActions']
     *if $GLOBALS['sugar_config']['moduleInstaller']['disableRestrictedCopy'] is set to false or not set it will call on scanCopy to ensure that it is not overriding files
     */
    public function scanManifest($manifestPath)
    {
        $issues = [];
        if (!file_exists($manifestPath)) {
            $this->issues['manifest'][$manifestPath] = translate('ML_NO_MANIFEST');
            return false;
        }
        if (isset($this->manifestScanner)) {
            if ($issues = $this->strictManifestScan($manifestPath)) {
                return false;
            }
        } else {
            $fileIssues = $this->scanFile($manifestPath);
            //if the manifest contains malicious code do not open it
            if (!empty($fileIssues)) {
                return $fileIssues;
            }
        }
        $this->lockConfig();
        [$manifest, $installdefs] = MSLoadManifest($manifestPath);
        $this->installdefs = $installdefs;
        $fileIssues = $this->checkConfig($manifestPath);
        if (!empty($fileIssues)) {
            return $fileIssues;
        }

        //scan for disabled actions
        if (isset($this->config['disableActions'])) {
            foreach ($this->config['disableActions'] as $action) {
                if (isset($installdefs[$this->manifestMap[$action]])) {
                    $issues[] = translate('ML_INVALID_ACTION_IN_MANIFEST') . $this->manifestMap[$action];
                }
            }
        }

        // now lets scan for files that will override our files
        if (isset($installdefs['copy'])) {
            foreach ($installdefs['copy'] as $copy) {
                if (!isset($copy['from'])) {
                    continue;
                }
                $from = $this->normalizePath($copy['from']);
                if ($from === false) {
                    $this->issues['copy'][$copy['from']] = translate('ML_PATH_MAY_NOT_CONTAIN') . ' ".." -' . $copy['from'];
                    continue;
                }
                if (strpos($from, '<basepath>') !== 0 || !check_file_name($from)) {
                    $this->issues['copy'][$copy['from']] = 'Incorrect format for copied files was provided.';
                    continue;
                }
                $from = str_replace('<basepath>', $this->pathToModule, $from);
                $to = $this->normalizePath($copy['to']);
                if ($to === false) {
                    $this->issues['copy'][$copy['to']] = translate('ML_PATH_MAY_NOT_CONTAIN') . ' ".." -' . $copy['to'];
                    continue;
                }
                if ($to === '') {
                    $to = '.';
                }
                $this->scanCopy($from, clean_path($to));
            }
        }
        if (safeCount($issues) > 0) {
            $this->issues['manifest'][$manifestPath] = $issues;
        }
    }

    /**
     * Takes in where the file will is specified to be copied from and to
     * and ensures that there is no official sugar file there.
     * If the file exists it will check
     * against the MD5 file list to see if Sugar Created the file
     * @param string $from source filename
     * @param string $to destination filename
     */
    public function scanCopy($from, $to)
    {
        if (is_dir($from)) {
            $d = dir($from);
            while ($e = $d->read()) {
                if ($e == '.' || $e == '..') {
                    continue;
                }
                $this->scanCopy($from . '/' . $e, $to . '/' . $e);
            }
            return;
        }

        $pathinfo = pathinfo($to);

        // if $to is a dir and $from is a file then make $to a full file path as well
        if ((is_dir($to) || empty($pathinfo['extension'])) && is_file($from)) {
            $to = rtrim($to, '/') . '/' . basename($from);
        }

        if (!$this->isValidExtension($to)) {
            $this->issues['copy'][$to] = translate('ML_INVALID_EXT');
        }
        if (empty($this->config['disableRestrictedCopy']) && file_exists($to)) {
            // if the $to is a file and it is found in sugarFileExists then don't allow overriding it
            if (is_file($to) && $this->sugarFileExists($to)) {
                $this->issues['copy'][$from] = translate('ML_OVERRIDE_CORE_FILES') . '(' . $to . ')';
            }
            if ($this->isConfigFile($to)) {
                $this->issues['copy'][$from] = translate('ML_CONFIG_OVERRIDE');
            }
        }
    }


    /**
     *Main external function that takes in a path to a package and then scans
     *that package's manifest for disabled actions and then it scans the PHP files
     *for restricted function calls
     * @param string $path path of the package to be scanned
     * @param string $sugarFileAllowed whether should allow to override core files
     *
     */
    public function scanPackage($path, $sugarFileAllowed = true)
    {
        $manifest = [];
        $this->baseDir = $path;
        $this->pathToModule = $path;
        $this->scanManifest($path . '/manifest.php');
        if (safeCount($this->issues)) {
            return;
        }
        if (empty($this->config['disableFileScan'])) {
            /**
             * @var array $manifest
             */
            require $path . '/manifest.php';
            $packageName = $manifest['name'] ?? 'unknown';
            require_once 'modules/HealthCheck/Scanner/Scanner.php';
            $this->healthCheck = new HealthCheckScanner();
            $this->healthCheck->initPackageScan();
            ob_start();
            $this->scanDir($path, $sugarFileAllowed);
            ob_end_clean();
            $this->healthCheck->finishScan();
            foreach ($this->healthCheck->getLogMeta() as $entry) {
                $this->issues['healthcheck'][$packageName][] = "[{$entry['bucket']}][{$entry['flag_label']}] {$entry['descr']}: {$entry['title']}";
            }
        }
    }

    /**
     * Formatted issues by type and file and return array.
     * @return array
     */
    public function getFormattedIssues(): array
    {
        $out = [];
        foreach ($this->issues as $type => $issuesByType) {
            foreach ($issuesByType as $file => $issuesByFile) {
                $file = str_replace($this->pathToModule . '/', '', $file);

                if (!is_array($issuesByFile)) {
                    $issuesByFile = [$issuesByFile];
                }
                foreach ($issuesByFile as $key => $issueText) {
                    $words = explode(' ', $issueText);
                    $words[0] = translate($words[0], 'Administration');
                    $issuesByFile[$key] = implode(' ', $words);
                }

                $out[$type][$file] = $issuesByFile;
            }
        }

        return $out;
    }

    /**
     *This function will take all issues of the current instance and print them to the screen
     **/
    public function displayIssues($package = 'Package')
    {
        global $sugar_version, $sugar_flavor, $current_user;

        $productCodes = $current_user->getProductCodes();
        $productCodes = urlencode(implode(',', $productCodes));
        echo '<h2>' . str_replace('{PACKAGE}', $package, translate('ML_PACKAGE_SCANNING')) .
            '</h2><BR><h2 class="error">' . translate('ML_INSTALLATION_FAILED') . '</h2><br><p>' .
            str_replace('{PACKAGE}', $package, translate('ML_PACKAGE_NOT_CONFIRM')) .
            '</p><ul><li>' . translate('ML_OBTAIN_NEW_PACKAGE') . '<li>' . translate('ML_RELAX_LOCAL') .
            '</ul></p><br>' .
            ' <a href="https://www.sugarcrm.com/crm/product_doc.php?module=FailPackageScan&version=' .
            $sugar_version . '&edtion=' . $sugar_flavor . '&products=' . $productCodes .
            '" target="_blank">' . translate('ML_PKG_SCAN_GUIDE') . '</a>' . '<br><br>';


        foreach ($this->issues as $type => $issues) {
            echo '<div class="error"><h2>' . ucfirst($type) . ' ' . translate('ML_ISSUES') . '</h2> </div>';
            echo '<div id="details' . $type . '" >';
            foreach ($issues as $file => $issue) {
                $file = str_replace($this->pathToModule . '/', '', $file);
                echo '<div style="position:relative;left:10px"><b>' . $file . '</b></div><div style="position:relative;left:20px">';
                if (is_array($issue)) {
                    foreach ($issue as $i) {
                        echo "$i<br>";
                    }
                } else {
                    echo "$issue<br>";
                }
                echo '</div>';
            }
            echo '</div>';
        }
        echo "<br><input class='button' onclick='document.location.href=\"index.php?module=Administration&action=UpgradeWizard&view=module\"' type='button' value=\"" . translate('LBL_UW_BTN_BACK_TO_MOD_LOADER') . '" />';
    }

    /**
     * Lock config settings
     */
    public function lockConfig()
    {
        if (empty($this->config_hash)) {
            $this->config_hash = md5(serialize($GLOBALS['sugar_config']));
        }
    }

    /**
     * Check if config was modified. Return
     * @param string $file
     * @return array Errors if something wrong, false if no problems
     */
    public function checkConfig($file)
    {
        $config_hash_after = md5(serialize($GLOBALS['sugar_config']));
        if ($config_hash_after != $this->config_hash) {
            $this->issues['file'][$file] = [translate('ML_CONFIG_OVERRIDE')];
            return $this->issues;
        }
        return false;
    }

    protected function scanCode(string $code): array
    {
        $issueMessages = [];
        foreach ($this->codeScanner->scan($code) as $issue) {
            $issueMessages[] = $issue->getMessage();
        }
        return $issueMessages;
    }

    public function scanArchive(string $path): void
    {
        $zip = new ZipArchive();
        if ($zip->open($path) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $fileName = $zip->getNameIndex($i);
                $fp = $zip->getStream($fileName);
                if ($fp === false) {
                    throw new RuntimeException('Failed to read data from ' . $fileName);
                }
                $contents = stream_get_contents($fp);
                fclose($fp);
                $issues = $this->scanCode($contents);
                if (safeCount($issues) > 0) {
                    $this->issues['file'][$fileName] = $issues;
                }
            }
        }
    }
}

/**
 * Load manifest file
 * Outside of the class to isolate the context
 * @param string $manifest_file
 * @return array
 */
function MSLoadManifest($manifest_file)
{
    include FileLoader::validateFilePath($manifest_file, true);
    return [$manifest, $installdefs];
}
