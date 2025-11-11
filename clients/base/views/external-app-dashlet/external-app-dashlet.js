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
 * This view allows users to add Sugar Apps (external-app) to a Dashboard
 *
 * @class View.Views.Base.ExternalAppDashletView
 * @alias SUGAR.App.view.views.BaseExternalAppDashletView
 * @extends View.Views.Base.ExternalAppView
 */
({
    extendsFrom: 'ExternalAppView',

    plugins: ['Dashlet'],

    className: 'external-app-dashlet',

    // wait for 20seconds for MFE to return
    scriptTimeoutTime: 20000,

    // True if we are on a focus drawer on a list view
    isInsideFocusDrawer: false,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        // Check if we are on a focus drawer on a list view
        // TODO: Maybe to make things easier in the future we can find a way
        // to add something like isFocus: true to this.context for all dashlets
        this.isInsideFocusDrawer = !!(options.context.parent &&
            options.context.parent.get('layout') === 'focus');

        if (!options.meta.config) {
            this.allowApp = this._checkCatalogAccess(options);

            if (!this.allowApp) {
                // if the App is not allowed, set the Catalog Error code
                this.errorCode = 'CAT-404';
            }
        }
        this._super('initialize', [options]);
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        if (this.meta.config) {
            return;
        }

        if (!this.allowApp) {
            // only need to listen for the metadata update if we have not gotten the metadata yet
            app.events.on(`sugarApp:${this.module}:${this._getLayoutName()}:updated`, this._checkMeta, this);
            this.dashletSetTimeout = _.delay(this._failedToLoadScript.bind(this), this.scriptTimeoutTime);
        }
        this._super('bindDataChange');
    },

    /**
     * @inheritdoc
     */
    initDashlet: function() {
        if (this.meta.config && !this.meta.customConfig) {
            var fields = _.flatten(_.pluck(this.dashletConfig.panels, 'fields'));
            var appDataField = _.find(fields, function(f) {
                return f.name === 'src';
            });
            var options = {};
            this.servicesObj = {};
            _.each(this.services, function(s) {
                options[s.view.src] = s.view.name;
                this.servicesObj[s.view.src] = s.view;
            }, this);
            appDataField.options = options;

            this.settings.on('change', function(model) {
                if (model.changed.src) {
                    // if the service source changed,
                    // update the title and config options
                    this.setAppUrlTitle();
                }
            }, this);
        }
    },

    /**
     * @inheritdoc
     */
    render: function() {
        if (this.meta.config && !this.meta.customConfig) {
            // skip external-app's render and call View's render
            app.view.View.prototype.render.call(this);

            this.setAppUrlTitle();
        } else {
            if (this.allowApp) {
                // if the dashlet is allowed, go ahead and render
                this._super('render');
            } else {
                // else display error that the dashlet is not available
                this.displayError();
            }
        }
    },

    /**
     * Sets the Dashlet title when the URL changes or Dashlet config renders for the first time
     */
    setAppUrlTitle: function() {
        var url = this.settings.get('src');
        if (_.isArray(url)) {
            url = url[0];
        }

        if (url && (!this.currentService || this.currentService !== this.servicesObj[url])) {
            this.currentService = this.servicesObj[url];

            if (this.currentService) {
                // set the dashlet title
                this.settings.set({
                    label: this.currentService.name
                });

                this._render();
            }
        }
    },

    /**
     * @inheritdoc
     */
    loadData: function(callbacks) {
        if (!callbacks) {
            // on first load, no onComplete callback is used
            // so call parent loadData to continue operations
            this._super('loadData');
        } else if (!this.parcelApp) {
            // callbacks exists when user manually clicks "Refresh" button
            // if parcelApp doesn't exist for some reason, try to load it again
            this._onSugarAppLoad();
        } else if (callbacks && this.parcelApp) {
            // user has manually clicked Refresh on dashlet and the parcel
            // is already mounted, so unmount and remount
            this.parcelApp.unmount().then(function() {
                this.parcelApp.mount(this.parcelParams);
            }.bind(this));
        }

        if (callbacks && callbacks.complete) {
            // if complete() exists, call it
            callbacks.complete();
        }
    },

    /**
     * Returns the layout name
     *
     * @return {string}
     * @private
     */
    _getLayoutName: function() {
        let layoutName;

        if (this.isInsideFocusDrawer) {
            layoutName = 'record';
        } else {
            layoutName = app.controller.context.get('layout') === 'records' ? 'list' : 'record';
        }

        return `${layoutName}-dashlet`;
    },

    /**
     * Checks the metadata for a given module, layout, and filename
     *
     * @private
     */
    _checkMeta: function() {
        // check to see if the MFE that was just added to metadata is this dashlet's src
        this.allowApp = this._checkCatalogAccess(this.options);
        if (this.allowApp) {
            if (this.dashletSetTimeout) {
                // stop the setTimeout if it exists
                clearTimeout(this.dashletSetTimeout);
                this.dashletSetTimeout = null;
            }
            // clear the element
            this.$el.empty();
            // load the src file from meta
            this._onSugarAppLoad();
            // rerender the dashlet
            this.render();
        }
    },

    /**
     * The MFE failed to load in time, remove Loading... and show the error
     * @private
     */
    _failedToLoadScript: function() {
        // remove the event listener waiting for MFE to load from endpoint
        // if it took too long, we dont want it
        app.events.off(`sugarApp:${this.module}:${this._getLayoutName()}:updated`, null, null);
        // hide the Loading... indicator
        this.$('.loading-label').addClass('hide');
        // show the error message
        this.$('.error-msg').removeClass('hide');
    },

    /**
     * Gets any available services that have been added to metadata
     *
     * @param options Init options
     * @return {Array} The array of components available to this view or an empty array
     * @private
     */
    _getAvailableServices: function(options) {
        var meta = app.metadata.getLayout(options.module, this._getLayoutName());

        return meta && meta.components ? meta.components : [];
    },

    /**
     * Checks metadata services to make sure Catalog sent over this service to be used
     *
     * @param options Init options
     * @return {boolean} True if the dashlet definition was found in services
     * @private
     */
    _checkCatalogAccess: function(options) {
        // update this.services with any of the latest metadata
        this.services = this._getAvailableServices(options);

        var dashletDef = _.find(this.services, function(svc) {
            // find the dashlet service with the same src
            return svc.view.src === options.meta.src;
        }, this);

        return !!dashletDef;
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        if (this.dashletSetTimeout) {
            // stop the setTimeout if it exists
            clearTimeout(this.dashletSetTimeout);
        }
        app.events.off(`sugarApp:${this.module}:${this._getLayoutName()}:updated`, null, null);

        this._super('_dispose');
    }
})
