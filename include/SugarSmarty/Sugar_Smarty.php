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


if (!defined('SUGAR_SMARTY_DIR')) {
    define('SUGAR_SMARTY_DIR', sugar_cached('smarty/'));
}

/**
 * Smarty wrapper for Sugar
 * @api
 */
class Sugar_Smarty extends Smarty
{
    protected static $_plugins_dir;
    /**
     * Allows {$foo} where foo is unset.
     * @var bool
     */
    public $allowUndefinedVars = true;

    /**
     * Allows {$foo.bar} where bar is unset and {$foo.bar1.bar2} where either bar1 or bar2 is unset.
     * @var bool
     */
    public $allowUndefinedArrayKeys = true;

    /**
     * Allows passing null to non-nullable parameters of standard PHP functions: strlen(null), htmlspecialchars(null),
     * this behaviour was deprecated in PHP 8.1 and raises deprecation warnings. Remove when Smarty upgraded to v4.1+
     * @var bool
     */
    public $allowPassingNullToParameters = true;

    /**
     * Allows usage of strftime function, which was deprecated in PHP 8.1 and raises deprecation warnings.
     * Remove when Smarty upgraded to v4.1+
     * @var bool
     */
    public $allowStrftime = true;

    private $previousErrorHandler = null;

    public function __construct()
    {
        parent::__construct();
        $this->error_reporting = error_reporting() & ~E_NOTICE;
        if (!file_exists(SUGAR_SMARTY_DIR)) {
            mkdir_recursive(SUGAR_SMARTY_DIR, true);
        }
        if (!file_exists(SUGAR_SMARTY_DIR . 'templates_c')) {
            mkdir_recursive(SUGAR_SMARTY_DIR . 'templates_c', true);
        }
        if (!file_exists(SUGAR_SMARTY_DIR . 'configs')) {
            mkdir_recursive(SUGAR_SMARTY_DIR . 'configs', true);
        }
        if (!file_exists(SUGAR_SMARTY_DIR . 'cache')) {
            mkdir_recursive(SUGAR_SMARTY_DIR . 'cache', true);
        }

        $this->addTemplateDirectories();
        $this->setCompileDir(SUGAR_SMARTY_DIR . 'templates_c');
        $this->setCacheDir(SUGAR_SMARTY_DIR . 'cache');
        $this->setConfigDir(SUGAR_SMARTY_DIR . 'configs');

        // Smarty will create subdirectories under the compiled templates and cache directories
        $this->use_sub_dirs = true;

        if (empty(self::$_plugins_dir)) {
            self::$_plugins_dir = [];
            if (file_exists('custom/include/SugarSmarty/plugins')) {
                self::$_plugins_dir[] = 'custom/include/SugarSmarty/plugins';
            }
            if (file_exists('custom/vendor/smarty/smarty/libs/plugins')) {
                self::$_plugins_dir[] = 'custom/vendor/smarty/smarty/libs/plugins';
            }
            if (file_exists('custom/vendor/Smarty/plugins')) {
                self::$_plugins_dir[] = 'custom/vendor/Smarty/plugins';
            }
            self::$_plugins_dir[] = 'include/SugarSmarty/plugins';
            self::$_plugins_dir[] = 'vendor/smarty/smarty/libs/plugins';
        }
        $this->plugins_dir = self::$_plugins_dir;

        $this->assign('VERSION_MARK', getVersionedPath(''));
    }

    public function mutingErrorHandler($errno, $errstr, $errfile, $errline, $errcontext = [])
    {
        $smartyDirs = [
            realpath(SUGAR_PATH . DIRECTORY_SEPARATOR . SUGAR_SMARTY_DIR),
            realpath(SMARTY_DIR),
        ];
        foreach (self::$_plugins_dir as $pluginsDir) {
            $smartyDir = realpath($pluginsDir);
            if (!empty($smartyDir)) {
                $smartyDirs[] = $smartyDir;
            }
        }
        $isSmartyRelated = false;
        foreach ($smartyDirs as $smartyDir) {
            if (!strncmp($errfile, $smartyDir, strlen($smartyDir))
            ) {
                $isSmartyRelated = true;
            }
        }

        if ($isSmartyRelated && $this->allowUndefinedVars && $errstr === 'Attempt to read property "value" on null') {
            return; // suppresses this error
        }
        if ($isSmartyRelated && $this->allowUndefinedArrayKeys && preg_match(
            '/^(Undefined array key|Trying to access array offset on)/',
            $errstr
        )) {
            return;// suppresses this error
        }
        if ($isSmartyRelated && $this->allowPassingNullToParameters
            && preg_match('/Passing null to parameter #.+? is deprecated/', $errstr)
        ) {
            return; // suppresses this error
        }
        if ($isSmartyRelated && $this->allowStrftime
            && preg_match('/^Function strftime\(\) is deprecated/', $errstr)
        ) {
            return; // suppresses this error
        }

        // pass to next error handler if this error did not occur inside SMARTY related dir
        // or the error was within smarty but masked to be ignored
        if ($errno && $errno & error_reporting()) {
            if ($this->previousErrorHandler) {
                return call_user_func(
                    $this->previousErrorHandler,
                    $errno,
                    $errstr,
                    $errfile,
                    $errline,
                    $errcontext
                );
            } else {
                return false;
            }
        }
    }

    /**
     * Adds the list of directories where templates could be stored
     */
    public function addTemplateDirectories()
    {
        // Add the base directory
        $this->addTemplateDir('.');

        // Get the current theme, and add its template directory
        $currentTheme = SugarThemeRegistry::current();
        $this->addTemplateDir($currentTheme->getTemplatePath(), 'theme');

        // Themes can define parent themes, so climb up the parent tree and add the theme's ancestors' template
        // directories as well. This way, if a theme doesn't define a template, it will inherit the template from an
        // ancestor theme
        while (isset($currentTheme->parentTheme)) {
            $parentTheme = SugarThemeRegistry::get($currentTheme->parentTheme);
            if ($parentTheme instanceof SugarTheme) {
                $this->addTemplateDir($parentTheme->getTemplatePath());
                $currentTheme = $parentTheme;
            } else {
                break;
            }
        }
    }

    /**
     * Fetch template or custom double
     * @param string $resource_name
     * @param string $cache_id
     * @param string $compile_id
     * @param boolean $display
     * @see Smarty::fetch()
     */
    public function fetchCustom($resource_name, $cache_id = null, $compile_id = null, $display = false)
    {
        return $this->fetch(SugarAutoLoader::existingCustomOne($resource_name), $cache_id, $compile_id, $display);
    }

    /**
     * Display template or custom double
     * @param string $resource_name
     * @param string $cache_id
     * @param string $compile_id
     * @see Smarty::display()
     */
    public function displayCustom($resource_name, $cache_id = null, $compile_id = null)
    {
        return $this->display(SugarAutoLoader::existingCustomOne($resource_name), $cache_id, $compile_id);
    }

    /**
     * assigns a Smarty variable and also assign to a new smarty object
     *
     * @param Smarty $smartyTpl
     * @param array|string $tpl_var the template variable name(s)
     * @param mixed $value the value to assign
     * @param boolean $nocache if true any output of this variable will be not cached
     *
     * @return Smarty_Internal_Data current Smarty_Internal_Data (or Smarty or Smarty_Internal_Template) instance for
     *                              chaining
     */
    public function assignAndCopy($smartyTpl, $tpl_var, $value = null, $nocache = false)
    {
        $this->assign($tpl_var, $value, $nocache);
        if (!empty($smartyTpl)) {
            $smartyTpl->assign($tpl_var, $value, $nocache);
        }
    }

    /**
     * fetches a rendered Smarty template
     *
     * @param string $template the resource handle of the template file or template object
     * @param mixed $cache_id cache id to be used with this template
     * @param mixed $compile_id compile id to be used with this template
     * @param object $parent next higher level of Smarty variables
     *
     * @return string rendered template output
     * @throws SmartyException
     * @throws Exception
     */
    public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null)
    {
        $savedPrevious = $this->previousErrorHandler;
        $this->previousErrorHandler = set_error_handler([$this, 'mutingErrorHandler']);
        try {
            return parent::fetch($template, $cache_id, $compile_id, $parent);
        } finally {
            restore_error_handler();
            $this->previousErrorHandler = $savedPrevious;
        }
    }

    /**
     * displays a Smarty template
     *
     * @param string $template the resource handle of the template file or template object
     * @param mixed $cache_id cache id to be used with this template
     * @param mixed $compile_id compile id to be used with this template
     * @param object $parent next higher level of Smarty variables
     *
     * @throws \Exception
     * @throws \SmartyException
     */
    public function display($template = null, $cache_id = null, $compile_id = null, $parent = null)
    {
        $savedPrevious = $this->previousErrorHandler;
        $this->previousErrorHandler = set_error_handler([$this, 'mutingErrorHandler']);
        try {
            parent::display($template, $cache_id, $compile_id, $parent);
        } finally {
            restore_error_handler();
            $this->previousErrorHandler = $savedPrevious;
        }
    }

    /**
     * wrapper for assign_by_ref
     *
     * @param string $tpl_var the template variable name
     * @param mixed  &$value the referenced value to assign
     */
    public function assign_by_ref($tpl_var, &$value)
    {
        $this->assignByRef($tpl_var, $value);
    }

    /**
     * wrapper for append_by_ref
     *
     * @param string $tpl_var the template variable name
     * @param mixed   &$value the referenced value to append
     * @param boolean $merge flag if array elements shall be merged
     */
    public function append_by_ref($tpl_var, &$value, $merge = false)
    {
        $this->appendByRef($tpl_var, $value, $merge);
    }

    /**
     * clear the given assigned template variable.
     *
     * @param string $tpl_var the template variable to clear
     */
    public function clear_assign($tpl_var)
    {
        $this->clearAssign($tpl_var);
    }

    /**
     * Registers custom function to be used in templates
     *
     * @param string $function the name of the template function
     * @param string $function_impl the name of the PHP function to register
     * @param bool $cacheable
     * @param mixed $cache_attrs
     */
    public function register_function($function, $function_impl, $cacheable = true, $cache_attrs = null)
    {
        $this->registerPlugin('function', $function, $function_impl, $cacheable, $cache_attrs);
    }

    /**
     * Unregisters custom function
     *
     * @param string $function name of template function
     */
    public function unregister_function($function)
    {
        $this->unregisterPlugin('function', $function);
    }

    /**
     * Registers object to be used in templates
     *
     * @param string $object name of template object
     * @param object $object_impl the referenced PHP object to register
     * @param array $allowed list of allowed methods (empty = all)
     * @param boolean $smarty_args smarty argument format, else traditional
     * @param array $block_methods list of methods that are block format
     *
     * @throws SmartyException
     * @internal param array $block_functs list of methods that are block format
     */
    public function register_object($object, $object_impl, $allowed = [], $smarty_args = true, $block_methods = [])
    {
        settype($allowed, 'array');
        settype($smarty_args, 'boolean');
        $this->registerObject($object, $object_impl, $allowed, $smarty_args, $block_methods);
    }

    /**
     * Unregisters object
     *
     * @param string $object name of template object
     */
    public function unregister_object($object)
    {
        $this->unregisterObject($object);
    }

    /**
     * Registers block function to be used in templates
     *
     * @param string $block name of template block
     * @param string $block_impl PHP function to register
     * @param bool $cacheable
     * @param mixed $cache_attrs
     */
    public function register_block($block, $block_impl, $cacheable = true, $cache_attrs = null)
    {
        $this->registerPlugin('block', $block, $block_impl, $cacheable, $cache_attrs);
    }

    /**
     * Unregisters block function
     *
     * @param string $block name of template function
     */
    public function unregister_block($block)
    {
        $this->unregisterPlugin('block', $block);
    }

    /**
     * Registers compiler function
     *
     * @param string $function name of template function
     * @param string $function_impl name of PHP function to register
     * @param bool $cacheable
     */
    public function register_compiler_function($function, $function_impl, $cacheable = true)
    {
        $this->registerPlugin('compiler', $function, $function_impl, $cacheable);
    }

    /**
     * Unregisters compiler function
     *
     * @param string $function name of template function
     */
    public function unregister_compiler_function($function)
    {
        $this->unregisterPlugin('compiler', $function);
    }

    /**
     * Registers modifier to be used in templates
     *
     * @param string $modifier name of template modifier
     * @param string $modifier_impl name of PHP function to register
     */
    public function register_modifier($modifier, $modifier_impl)
    {
        $this->registerPlugin('modifier', $modifier, $modifier_impl);
    }

    /**
     * Unregisters modifier
     *
     * @param string $modifier name of template modifier
     */
    public function unregister_modifier($modifier)
    {
        $this->unregisterPlugin('modifier', $modifier);
    }

    /**
     * Registers a resource to fetch a template
     *
     * @param string $type name of resource
     * @param array $functions array of functions to handle resource
     */
    public function register_resource($type, $functions)
    {
        $this->registerResource($type, $functions);
    }

    /**
     * Unregisters a resource
     *
     * @param string $type name of resource
     */
    public function unregister_resource($type)
    {
        $this->unregisterResource($type);
    }

    /**
     * Registers a prefilter function to apply
     * to a template before compiling
     *
     * @param callable $function
     */
    public function register_prefilter($function)
    {
        $this->registerFilter('pre', $function);
    }

    /**
     * Unregisters a prefilter function
     *
     * @param callable $function
     */
    public function unregister_prefilter($function)
    {
        $this->unregisterFilter('pre', $function);
    }

    /**
     * Registers a postfilter function to apply
     * to a compiled template after compilation
     *
     * @param callable $function
     */
    public function register_postfilter($function)
    {
        $this->registerFilter('post', $function);
    }

    /**
     * Unregisters a postfilter function
     *
     * @param callable $function
     */
    public function unregister_postfilter($function)
    {
        $this->unregisterFilter('post', $function);
    }

    /**
     * Registers an output filter function to apply
     * to a template output
     *
     * @param callable $function
     */
    public function register_outputfilter($function)
    {
        $this->registerFilter('output', $function);
    }

    /**
     * Unregisters an outputfilter function
     *
     * @param callable $function
     */
    public function unregister_outputfilter($function)
    {
        $this->unregisterFilter('output', $function);
    }

    /**
     * load a filter of specified type and name
     *
     * @param string $type filter type
     * @param string $name filter name
     */
    public function load_filter($type, $name)
    {
        $this->loadFilter($type, $name);
    }

    /**
     * clear cached content for the given template and cache id
     *
     * @param string $tpl_file name of template file
     * @param string $cache_id name of cache_id
     * @param string $compile_id name of compile_id
     * @param string $exp_time expiration time
     *
     * @return boolean
     */
    public function clear_cache($tpl_file = null, $cache_id = null, $compile_id = null, $exp_time = null)
    {
        return $this->clearCache($tpl_file, $cache_id, $compile_id, $exp_time);
    }

    /**
     * clear the entire contents of cache (all templates)
     *
     * @param string $exp_time expire time
     *
     * @return boolean
     */
    public function clear_all_cache($exp_time = null)
    {
        return $this->clearCache(null, null, null, $exp_time);
    }

    /**
     * test to see if valid cache exists for this template
     *
     * @param string $tpl_file name of template file
     * @param string $cache_id
     * @param string $compile_id
     *
     * @return boolean
     */
    public function is_cached($tpl_file, $cache_id = null, $compile_id = null)
    {
        return $this->isCached($tpl_file, $cache_id, $compile_id);
    }

    /**
     * clear all the assigned template variables.
     */
    public function clear_all_assign()
    {
        $this->clearAllAssign();
    }

    /**
     * clears compiled version of specified template resource,
     * or all compiled template files if one is not specified.
     * This function is for advanced use only, not normally needed.
     *
     * @param string $tpl_file
     * @param string $compile_id
     * @param string $exp_time
     *
     * @return boolean results of {@link smarty_core_rm_auto()}
     */
    public function clear_compiled_tpl($tpl_file = null, $compile_id = null, $exp_time = null)
    {
        return $this->clearCompiledTemplate($tpl_file, $compile_id, $exp_time);
    }

    /**
     * Checks whether requested template exists.
     *
     * @param string $tpl_file
     *
     * @return boolean
     */
    public function template_exists($tpl_file)
    {
        return $this->templateExists($tpl_file);
    }

    /**
     * Returns an array containing template variables
     *
     * @param string $name
     *
     * @return array
     */
    public function get_template_vars($name = null)
    {
        return $this->getTemplateVars($name);
    }

    /**
     * Returns an array containing config variables
     *
     * @param string $name
     *
     * @return array
     */
    public function get_config_vars($name = null)
    {
        return $this->getConfigVars($name);
    }

    /**
     * load configuration values
     *
     * @param string $file
     * @param string $section
     * @param string $scope
     */
    public function config_load($file, $section = null, $scope = 'global')
    {
        $this->ConfigLoad($file, $section, $scope);
    }

    /**
     * return a reference to a registered object
     *
     * @param string $name
     *
     * @return object
     */
    public function get_registered_object($name)
    {
        return $this->getRegisteredObject($name);
    }

    /**
     * clear configuration values
     *
     * @param string $var
     */
    public function clear_config($var = null)
    {
        $this->clearConfig($var);
    }

    /**
     * trigger Smarty error
     *
     * @param string $error_msg
     * @param integer $error_type
     */
    public function trigger_error($error_msg, $error_type = E_USER_WARNING)
    {
        trigger_error("Smarty error: $error_msg", $error_type);
    }
}
