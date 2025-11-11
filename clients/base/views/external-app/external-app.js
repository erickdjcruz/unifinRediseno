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
/**
 * @class View.Views.Base.ExternalAppView
 * @alias SUGAR.App.view.views.BaseExternalAppView
 * @extends View.View
 */
({
    mounted: false,
    rendered: false,
    className: 'external-app-interface',
    extraParcelParams: {},
    sugarAppStore: {
        listPreviewModel: undefined
    },

    /**
     * Initializing the SingleSpa, using systemJs getting hold of the needed information of the MFE.
     * singleSpa Bootstrap function is called during component initialize.
     * @inheritdoc
     */
    initialize: function(options) {
        singleSpa.start();

        // if coming from external-app-dashlet, this will be set to true or false
        // else it will be undefined. Either way, if undefined or true set to true, otherwise false.
        this.allowApp = _.isUndefined(this.allowApp) || this.allowApp;

        this.isTabbedLayout = options.layout.type === 'tabbed-layout';

        // set appId based on if this is in a tabbed-layout or not
        this.appId = this.isTabbedLayout ? options.layout.cid : this.cid;

        this._super('initialize', [options]);

        // Creating Listeners for various Sugar Events.
        this._sdkEventHandler();

        // pass any env options to be mounted with the external app
        if (this.meta && this.meta.env) {
            this.extraParcelParams = this.meta.env;
        }

        if (this.isTabbedLayout) {
            this.context.on(
                'sugarApp:' + this.appId + ':load:' + this.meta.srn,
                this._onSugarAppLoad,
                this
            );
        } else {
            this._onSugarAppLoad();
        }
    },

    /**
     * This Method is the Handler to SugarEvents that stores the event callback data to the sugarAppStore object so that
     * SDK wrapper can use it for SugarApps.
     * @private
     */
    _sdkEventHandler: function() {
        this.on('sugarApp:' + this.appId + ':store:get', function(callback) {
            callback(this.sugarAppStore);
        }, this);

        this.context.on('list:preview:fire', function(model) {
            this.sugarAppStore.listPreviewModel = model;

            // trigger to let store has changed
            this.trigger('sugarApp:' + this.appId + ':store:change', this.sugarAppStore);
        }, this);
    },

    /**
     * singleSpa Mount function is called during the initial render
     */
    render: function() {
        this.rendered = true;
        this._mountApp();
    },

    /**
     * Click handler that imports / loads the spa module that was clicked
     * @protected
     */
    _onSugarAppLoad: function() {
        var serverInfo = app.metadata.getServerInfo();

        // don't re-import already mounted parcel apps
        if (this.meta.src && !this.parcelLib) {
            var url = this.meta.src;
            if (this.meta.appendVersion && serverInfo.version) {
                url += (url.indexOf('?') ? '&' : '?') + 'sugar_version=' + serverInfo.version;
            }

            System.import(url).then(function(mod) {
                if (!mod) {
                    app.log.error('Unable to load external module from ' + url);
                }

                //Check if the export was under 'default' rather than at the top level of the module
                for (var i = 0; i < 3; i++) {
                    var props = Object.getOwnPropertyNames(mod).filter(function(name) {
                        return name.substr(0, 2) !== '__';
                    });

                    if (mod.default && (props.length === 1 || mod.__useDefault)) {
                        mod = mod.default;
                    } else {
                        break;
                    }
                }

                if (this.allowApp) {
                    // only if the app is allowed, continue loading it
                    this.parcelLib = mod;
                    //If we haven't been asked to render yet, don't force a render.
                    //If we have been rendered, mount the app into our element.
                    if (this.rendered) {
                        this._mountApp();
                    }
                }

            }.bind(this)).catch(function(e) {
                if (!this.allowApp) {
                    // catalog could not find the dashlet, and the service url failed
                    this.errorCode = 'SVC-404';
                    this.displayError();
                }
                System.delete(url);
            }.bind(this));
        }
    },

    /**
     * Displays an error message with error code into the template
     */
    displayError: function() {
        this.errorMsg = app.lang.get('LBL_SUGAR_APPS_DASHLET_CATALOG_ERROR', null, {
            errorCode: this.errorCode
        });
        this.$el.empty();
        this.$el.append(this.template(this));
    },

    /**
     * singleSpa Update function is called when the component in render is called after the initial render
     * @private
     */
    _mountApp: function() {
        if (!this.mounted && this.parcelLib) {
            this.extraParcelParams = _.assign(this.extraParcelParams, {component: this});

            var root = document.createElement('div');
            //Since we can't use a shadow dom, we can at least reset the css to isolate styling.
            this.el.appendChild(root);
            this.parcelParams = {
                domElement: root,
                view: this
            };

            // update parcelParams with any extra keys added
            if (this.extraParcelParams) {
                for (var key in this.extraParcelParams) {
                    if (this.extraParcelParams.hasOwnProperty(key)) {
                        this.parcelParams[key] = this.extraParcelParams[key];
                    }
                }
            }

            this.parcelApp = singleSpa.mountRootParcel(this.parcelLib, this.parcelParams);
            this.mounted = true;
        }
        if (this.mounted && this.parcelApp && this.parcelApp.update) {
            this.parcelApp.update(this.parcelParams);
        }
    },

    /**
     * singleSpa Unmount function is called when dispose is called on the sidecar view
     * @inheritdoc
     * @private
     */
    _dispose: function() {
        if (this.parcelApp && this.parcelApp.unmount) {
            this.parcelApp.unmount();
        }

        // Removing listener on sugar app dispose.
        this.off('sugarApp:' + this.appId + ':store:get', null, this);

        this._super('_dispose');
    }
});
