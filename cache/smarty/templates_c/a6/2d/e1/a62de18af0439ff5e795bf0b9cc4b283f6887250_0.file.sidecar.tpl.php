<?php
/* Smarty version 4.3.1, created on 2025-11-11 16:32:24
  from '/Applications/MAMP/htdocs/fourt/include/MVC/View/tpls/sidecar.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_691365182a5e05_68202540',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a62de18af0439ff5e795bf0b9cc4b283f6887250' => 
    array (
      0 => '/Applications/MAMP/htdocs/fourt/include/MVC/View/tpls/sidecar.tpl',
      1 => 1711552720,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_691365182a5e05_68202540 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/Applications/MAMP/htdocs/fourt/include/SugarSmarty/plugins/function.sugar_getjspath.php','function'=>'smarty_function_sugar_getjspath',),1=>array('file'=>'/Applications/MAMP/htdocs/fourt/include/SugarSmarty/plugins/function.sugar_getstylesheet.php','function'=>'smarty_function_sugar_getstylesheet',),2=>array('file'=>'/Applications/MAMP/htdocs/fourt/include/SugarSmarty/plugins/function.sugar_getscript.php','function'=>'smarty_function_sugar_getscript',),));
?>

<!DOCTYPE HTML>
<html class="no-js">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
        <meta charset="UTF-8">
        <title>SugarCRM</title>

                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['css_url']->value, 'url');
$_smarty_tpl->tpl_vars['url']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['url']->value) {
$_smarty_tpl->tpl_vars['url']->do_else = false;
?>
            <link rel="preload" as="style" href="<?php echo smarty_function_sugar_getjspath(array('file'=>$_smarty_tpl->tpl_vars['url']->value),$_smarty_tpl);?>
">
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        <link rel="preload" as="style" href="styleguide/assets/css/loading.css">

        <link rel="shortcut icon" type="image/png" href="<?php echo smarty_function_sugar_getjspath(array('file'=>'include/images/sugar-favicon.png'),$_smarty_tpl);?>
">
        <!-- CSS -->
                <?php echo smarty_function_sugar_getstylesheet(array('file'=>"styleguide/assets/css/build.tailwind.css",'type'=>"text/css"),$_smarty_tpl);?>

        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['css_url']->value, 'url');
$_smarty_tpl->tpl_vars['url']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['url']->value) {
$_smarty_tpl->tpl_vars['url']->do_else = false;
?>
            <link rel="stylesheet" href="<?php echo smarty_function_sugar_getjspath(array('file'=>$_smarty_tpl->tpl_vars['url']->value),$_smarty_tpl);?>
">
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        <link rel="stylesheet" href="styleguide/assets/css/loading.css" type="text/css">
        <link rel="stylesheet" href="styleguide/assets/css/gridstack.css" type="text/css">
        <link rel="stylesheet" href="styleguide/assets/css/gridstack-extra.css" type="text/css">
        <?php echo smarty_function_sugar_getscript(array('file'=>"include/javascript/modernizr.js"),$_smarty_tpl);?>

    </head>
    <body>
        <div id="sugarcrm">
            <div id="sidecar">
                <div id="alerts" class="alert-top">
                    <div class="alert-wrapper">
                        <div class="alert alert-process rounded-md shadow-lg bg-alert-background mb-2">
                            <strong>
                                <div class="loading">
                                    <?php echo $_smarty_tpl->tpl_vars['LBL_LOADING']->value;?>
<i class="l1">&#46;</i><i class="l2">&#46;</i><i class="l3">&#46;</i>
                                </div>
                            </strong>
                            <button class="close btn btn-invisible" onclick="$('.alert-process').hide();"><i class="sicon sicon-close"></i></button>
                        </div>
                    </div>
                    <noscript>
                        <div class="alert-top">
                            <div class="alert alert-danger">
                                <strong><?php echo $_smarty_tpl->tpl_vars['LBL_ENABLE_JAVASCRIPT']->value;?>
</strong>
                            </div>
                        </div>
                    </noscript>
                </div>
                <div id="impersonation-banner"></div>
                <nav role="navigation">
                    <div id="sidebar-nav"></div>
                </nav>
                <header>
                    <div id="header-nav"></div>
                </header>
                <main>
                    <div id="content"></div>
                </main>
                <div id="sweetspot"></div>
                <div id="drawers"></div>
                <div id="side-drawer"></div>
            </div>
        </div>
        <!-- App Scripts -->
        <?php echo smarty_function_sugar_getscript(array('file'=>"sidecar/minified/sidecar.min.js"),$_smarty_tpl);?>

        <?php echo '<script'; ?>
 src='<?php echo smarty_function_sugar_getjspath(array('file'=>$_smarty_tpl->tpl_vars['sugarSidecarPath']->value),$_smarty_tpl);?>
'><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src='<?php echo smarty_function_sugar_getjspath(array('file'=>$_smarty_tpl->tpl_vars['SLFunctionsPath']->value),$_smarty_tpl);?>
'><?php echo '</script'; ?>
>
        <!-- <?php echo '<script'; ?>
 src='<?php echo smarty_function_sugar_getjspath(array('file'=>'sidecar/minified/sugar.min.js'),$_smarty_tpl);?>
'><?php echo '</script'; ?>
> -->
        <?php echo '<script'; ?>
 src='<?php echo smarty_function_sugar_getjspath(array('file'=>($_smarty_tpl->tpl_vars['configFile']->value).('?hash=$configHash')),$_smarty_tpl);?>
'><?php echo '</script'; ?>
>
        <?php echo smarty_function_sugar_getscript(array('file'=>"cache/include/javascript/sugar_grp7.min.js"),$_smarty_tpl);?>

        <?php echo '<script'; ?>
 language="javascript">
            var parentIsSugar = false;
            try {
                parentIsSugar = (parent.window != window)
                    && (typeof parent.SUGAR != "undefined")
                    && (typeof parent.SUGAR.App.router != "undefined");
            } catch (e) {
                // if we got here, we were trying to access parent window from different domain
            }
            if (parentIsSugar) {
                parent.SUGAR.App.router.navigate("#Home", { trigger:true });
            } else {
                var App;
                <?php if ((($tmp = $_smarty_tpl->tpl_vars['authorization']->value ?? null)===null||$tmp==='' ? false ?? null : $tmp)) {?>
                let authStore = SUGAR.App.config.authStore || "cache";
                let keyPrefix = (authStore == "cache") ? "<?php echo $_smarty_tpl->tpl_vars['appPrefix']->value;?>
" : "";
                let keyValueStore = SUGAR.App[authStore];
                <?php if ($_smarty_tpl->tpl_vars['authorization']->value['impersonation_for']) {?>
                    
                    if (!keyValueStore.has(keyPrefix + "ImpersonationFor")) {
                        keyValueStore.set(keyPrefix + "OriginAuthAccessToken", keyValueStore.get(keyPrefix + "AuthAccessToken"));
                        keyValueStore.set(keyPrefix + "OriginAuthRefreshToken", keyValueStore.get(keyPrefix + "AuthRefreshToken"));
                    }
                    
                keyValueStore.set(keyPrefix + "ImpersonationFor", "<?php echo $_smarty_tpl->tpl_vars['authorization']->value['impersonation_for'];?>
");
                <?php } else { ?>
                    keyValueStore.cut(keyPrefix + "ImpersonationFor");
                <?php }?>

                keyValueStore.set(keyPrefix + "AuthAccessToken", "<?php echo $_smarty_tpl->tpl_vars['authorization']->value['access_token'];?>
");
                <?php if ($_smarty_tpl->tpl_vars['authorization']->value['refresh_token']) {?>
                keyValueStore.set(keyPrefix + "AuthRefreshToken", "<?php echo $_smarty_tpl->tpl_vars['authorization']->value['refresh_token'];?>
");
                <?php }?>
                if (window.SUGAR.App.config.siteUrl != '') {
                    history.replaceState(null, 'SugarCRM', window.SUGAR.App.config.siteUrl+"/"+window.location.hash);
                } else {
                    history.replaceState(
                            null,
                            'SugarCRM',
                            window.location.origin + window.location.pathname + window.location.hash
                    );
                }
                <?php }?>

                const getAppearancePreference = () => {
                    // Look at user preferences as a first priority, and fall back
                    // to local storage if necessary
                    if (App && App.user && App.user.get('id')) {
                        return App.user.get('appearance');
                    }
                    return localStorage.getItem('last_appearance_preference') || 'system_default';
                };

                const updateAppearance = appearancePreference => {
                    let classToAdd = appearancePreference === 'dark' ? 'sugar-dark-theme' : 'sugar-light-theme';
                    let classToRemove = appearancePreference === 'dark' ? 'sugar-light-theme' : 'sugar-dark-theme';

                    // Update main (sidecar) body class
                    document.body.classList.add(classToAdd);
                    document.body.classList.remove(classToRemove);

                    // Update BWC iframe to reflect changes (if available)
                    let bwcIframe = document.getElementById('bwc-frame');
                    if (bwcIframe) {
                        bwcIframe.contentDocument.body.classList.add(classToAdd);
                        bwcIframe.contentDocument.body.classList.remove(classToRemove);
                    }
                };

                const switchAppearance = () => {
                    let systemInDarkMode = window.matchMedia &&
                        window.matchMedia('(prefers-color-scheme: dark)').matches;
                    let userPref = getAppearancePreference();

                    let isDarkMode = userPref === 'dark' || (userPref === 'system_default' && systemInDarkMode);
                    let appearancePreference = isDarkMode ? 'dark' : 'light';

                    // Update app appearance if applicable: document, BWC, MarketingExtras
                    updateAppearance(appearancePreference);

                    // Only save the preference to local storage if it came directly
                    // from the user preferences
                    if (App && App.user && App.user.get('id')) {
                        localStorage.setItem('last_appearance_preference', userPref);

                        // Check if the browser is Safari, if so don't use the 'secure' property, because Safari
                        // doesn't properly support this property and even in HTTPS it doesn't work as intended
                        let cookieProperties = App.userAgent.browserEngine === 'webkit' ? '' : '; Secure';
                        document.cookie = 'appearance=' + appearancePreference + cookieProperties;
                    }
                };

                // Immediately check if we need to toggle dark mode. User preferences aren't available at
                // this point, so we'll rely on local storage until the app is done initializing
                switchAppearance();

                App = SUGAR.App.init({
                    el: "#sidecar",
                    callback: function(app){
                        app.progress.set(0.6);

                        app.once("app:view:change", function(){
                            // Determine if we need to add a top level class to fix jumping elements in Safari
                            var isSafariBrowser = app.userAgent.browserEngine === 'webkit';

                            if (isSafariBrowser) {
                                var bodyElement = document.querySelector('body');
                                bodyElement.classList += ' safari-browser';
                            }

                            // Add the current language for improved screen reader accessibility
                            var currentLanguage = (
                                app.user
                                && app.user.get('preferences')
                                && app.user.get('preferences').language
                            )
                                || app.lang.getLanguage();
                            if (currentLanguage) {
                                var currentLanguageForDom = _.first(currentLanguage.split('_'));
                                // Use the simple language code as per HTML qualifications
                                document.documentElement.lang = currentLanguageForDom;
                                document.documentElement.dir = app.lang.direction;
                            }

                            // Set the dark mode flag if enabled, and listen for any future changes
                            switchAppearance();
                            app.user.on('change:appearance', switchAppearance);

                            app.progress.done();
                        });

                        app.alert.dismissAll();
                        app.start();
                    }
                });
                App.api.debug = App.config.debugSugarApi;
            }
        <?php echo '</script'; ?>
>

        <?php if (!empty($_smarty_tpl->tpl_vars['voodooFile']->value)) {?>
            <?php echo '<script'; ?>
 src="<?php echo smarty_function_sugar_getjspath(array('file'=>$_smarty_tpl->tpl_vars['voodooFile']->value),$_smarty_tpl);?>
"><?php echo '</script'; ?>
>
        <?php }?>
        <?php if (!empty($_smarty_tpl->tpl_vars['processAuthorFiles']->value)) {?>
            <?php echo smarty_function_sugar_getscript(array('file'=>"cache/include/javascript/pmse.utils.min.js"),$_smarty_tpl);?>

            <?php echo smarty_function_sugar_getscript(array('file'=>"cache/include/javascript/pmse.jcore.min.js"),$_smarty_tpl);?>

            <?php echo smarty_function_sugar_getscript(array('file'=>"cache/include/javascript/pmse.ui.min.js"),$_smarty_tpl);?>

            <?php echo smarty_function_sugar_getscript(array('file'=>"cache/include/javascript/pmse.libraries.min.js"),$_smarty_tpl);?>

            <?php echo smarty_function_sugar_getscript(array('file'=>"cache/include/javascript/pmse.designer.min.js"),$_smarty_tpl);?>

        <?php }?>
    </body>
</html>
<?php }
}
