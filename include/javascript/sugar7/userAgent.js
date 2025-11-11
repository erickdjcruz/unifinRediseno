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
(function(app) {
    /**
     * A utility method to give us the name of the browser engine we are using.
     * https://developer.mozilla.org/en-US/docs/Web/HTTP/Browser_detection_using_the_user_agent#rendering_engine
     * See SS-1078 (https://sugarcrm.atlassian.net/browse/SS-1078) for more details.
     */
    function parseUserAgentForBrowserEngine() {
        var engine = null;
        var userAgent = navigator.userAgent;

        var geckoRegExp = new RegExp('Gecko', 'i');
        var likeGeckoRegExp = new RegExp('like Gecko', 'i');
        var blinkRegExp = new RegExp('Chrome', 'i');
        var webkitRegExp = new RegExp('AppleWebKit', 'i');
        var ieRegExp = new RegExp('Trident', 'i');

        if (geckoRegExp.test(userAgent) && !likeGeckoRegExp.test(userAgent)) {
            engine = 'gecko';
        } else if (blinkRegExp.test(userAgent)) {
            engine = 'blink';
        } else if (webkitRegExp.test(userAgent)) {
            engine = 'webkit';
        } else if (ieRegExp.test(userAgent)) {
            engine = 'trident';
        }

        return engine;
    };

    /**
     * UserAgent Helper. Provides access to viewing what the user's platform is.
     *
     * @class Core.UserAgentHelper
     * @alias SUGAR.App.userAgent
     */
    app.augment('userAgent', {
        browserEngine: parseUserAgentForBrowserEngine(),
    });
})(SUGAR.App);
