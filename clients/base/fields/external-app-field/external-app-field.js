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
 * @class View.Fields.Base.ExternalAppFieldField
 * @alias SUGAR.App.view.fields.BaseExternalAppFieldField
 * @extends View.Fields.Base.Field
 */
({
    mounted: false,
    rendered: false,
    className: 'external-app-field',
    extraParcelParams: {},

    /**
     * The element the MFE attaches to
     */
    root: null,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this._onSugarAppLoad();
    },

    /**
     * Imports and loads the MFE
     * @private
     */
    _onSugarAppLoad: function() {
        var serverInfo = app.metadata.getServerInfo();

        // don't re-import already mounted parcel apps
        if (this.def.src && !this.parcelLib) {
            var url = this.def.src;
            if (this.def.appendVersion && serverInfo.version) {
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

                // only if the app is allowed, continue loading it
                this.parcelLib = mod;
                //If we haven't been asked to render yet, don't force a render.
                //If we have been rendered, mount the app into our element.
                if (this.rendered) {
                    this._mountApp();
                }

            }.bind(this)).catch(function(e) {
                if (!this.allowApp) {
                    // catalog could not find the mfe, and the service url failed
                    this.errorCode = 'SVC-404';
                    this.displayError();
                }
                System.delete(url);
            }.bind(this));
        }
    },

    /**
     * Lets the MFE handle the rendering
     */
    render: function() {
        this.rendered = true;
        this._mountApp();
    },

    /**
     * singleSpa Update function is called when the component in render is called after the initial render
     * @private
     */
    _mountApp: function() {
        if (!this.mounted && this.parcelLib) {
            this.root = document.createElement('div');
            //Since we can't use a shadow dom, we can at least reset the css to isolate styling.
            this.el.appendChild(this.root);
            this.parcelParams = {
                domElement: this.root,
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
            this.render();
        }

        if (this.mounted && this.parcelApp && this.parcelApp.update) {
            this.parcelApp.update(this.parcelParams);
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
})
