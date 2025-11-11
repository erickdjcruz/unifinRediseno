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

<!DOCTYPE HTML>
<html class="no-js">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
        <meta charset="UTF-8">
        <title>SugarCRM</title>

        {* Preload the stylesheets we need for proper loading styling. *}
        {foreach from=$css_url item=url}
            <link rel="preload" as="style" href="{sugar_getjspath file=$url}">
        {/foreach}
        <link rel="preload" as="style" href="styleguide/assets/css/loading.css">

        <link rel="shortcut icon" type="image/png" href="{sugar_getjspath file='include/images/sugar-favicon.png'}">
        <!-- CSS -->
        {*
            Loading the cached CSS file first to reduce changes of page loading without the necessary styles. This
            helps the situation where there is a white flash while starting a page load in Firefox.
        *}
        {sugar_getstylesheet file="styleguide/assets/css/build.tailwind.css" type="text/css"}
        {foreach from=$css_url item=url}
            <link rel="stylesheet" href="{sugar_getjspath file=$url}">
        {/foreach}
        <link rel="stylesheet" href="styleguide/assets/css/loading.css" type="text/css">
        <link rel="stylesheet" href="styleguide/assets/css/gridstack.css" type="text/css">
        <link rel="stylesheet" href="styleguide/assets/css/gridstack-extra.css" type="text/css">
        {sugar_getscript file="include/javascript/modernizr.js"}
    </head>
    <body>
        <div id="sugarcrm">
            <div id="sidecar">
                <div id="alerts" class="alert-top">
                    <div class="alert-wrapper">
                        <div class="alert alert-process rounded-md shadow-lg bg-alert-background mb-2">
                            <strong>
                                <div class="loading">
                                    {$LBL_LOADING}<i class="l1">&#46;</i><i class="l2">&#46;</i><i class="l3">&#46;</i>
                                </div>
                            </strong>
                            <button class="close btn btn-invisible" onclick="$('.alert-process').hide();"><i class="sicon sicon-close"></i></button>
                        </div>
                    </div>
                    <noscript>
                        <div class="alert-top">
                            <div class="alert alert-danger">
                                <strong>{$LBL_ENABLE_JAVASCRIPT}</strong>
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
        {sugar_getscript file="sidecar/minified/sidecar.min.js"}
        <script src='{sugar_getjspath file=$sugarSidecarPath}'></script>
        <script src='{sugar_getjspath file=$SLFunctionsPath}'></script>
        <!-- <script src='{sugar_getjspath file='sidecar/minified/sugar.min.js'}'></script> -->
        <script src='{sugar_getjspath file=$configFile|cat:'?hash=$configHash'}'></script>
        {sugar_getscript file="cache/include/javascript/sugar_grp7.min.js"}
        <script language="javascript">
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
                {if $authorization|default:false}
                let authStore = SUGAR.App.config.authStore || "cache";
                let keyPrefix = (authStore == "cache") ? "{$appPrefix}" : "";
                let keyValueStore = SUGAR.App[authStore];
                {if $authorization.impersonation_for}
                    {literal}
                    if (!keyValueStore.has(keyPrefix + "ImpersonationFor")) {
                        keyValueStore.set(keyPrefix + "OriginAuthAccessToken", keyValueStore.get(keyPrefix + "AuthAccessToken"));
                        keyValueStore.set(keyPrefix + "OriginAuthRefreshToken", keyValueStore.get(keyPrefix + "AuthRefreshToken"));
                    }
                    {/literal}
                keyValueStore.set(keyPrefix + "ImpersonationFor", "{$authorization.impersonation_for}");
                {else}
                    keyValueStore.cut(keyPrefix + "ImpersonationFor");
                {/if}

                keyValueStore.set(keyPrefix + "AuthAccessToken", "{$authorization.access_token}");
                {if $authorization.refresh_token}
                keyValueStore.set(keyPrefix + "AuthRefreshToken", "{$authorization.refresh_token}");
                {/if}
                if (window.SUGAR.App.config.siteUrl != '') {ldelim}
                    history.replaceState(null, 'SugarCRM', window.SUGAR.App.config.siteUrl+"/"+window.location.hash);
                {rdelim} else {ldelim}
                    history.replaceState(
                            null,
                            'SugarCRM',
                            window.location.origin + window.location.pathname + window.location.hash
                    );
                {rdelim}
                {/if}

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
        </script>

        {if !empty($voodooFile)}
            <script src="{sugar_getjspath file=$voodooFile}"></script>
        {/if}
        {if !empty($processAuthorFiles)}
            {sugar_getscript file="cache/include/javascript/pmse.utils.min.js"}
            {sugar_getscript file="cache/include/javascript/pmse.jcore.min.js"}
            {sugar_getscript file="cache/include/javascript/pmse.ui.min.js"}
            {sugar_getscript file="cache/include/javascript/pmse.libraries.min.js"}
            {sugar_getscript file="cache/include/javascript/pmse.designer.min.js"}
        {/if}
    </body>
</html>
