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
 * @class View.Views.Base.AdministrationModuleNamesAndIconsView
 * @alias SUGAR.App.view.views.BaseAdministrationModuleNamesAndIconsView
 * @extends View.Views.Base.ConfigPanelView
 */
({
    extendsFrom: 'ConfigPanelView',

    events: {
        'click a[name="cancel_button"]': 'cancelConfig',
        'click a[name="save_button"]:not(.disabled)': 'saveConfig'
    },

    /**
     * Store fields that fail validation
     */
    invalidFields: [],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.metaFields = this._getMetaFields();
        this._currentUrl = Backbone.history.getFragment();
        this.model.set({'language_selection': app.lang.getLanguage()});
        this.fetchModules();
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');
        app.routing.before('route', this.beforeRouteChange, this);
        this.listenTo(this.model, 'change:language_selection', this.fetchModules);
        this.listenTo(this.collection, 'change:module_display_type', this._displayTypeChanged);
    },

    /**
     * Gets the set of field definitions on the view from metadata
     *
     * @return {Object} the map of {field name} => {field def} from meta fields
     * @private
     */
    _getMetaFields: function() {
        let metaFields = [];

        let panelFields = this.options.meta.panels ? _.first(this.options.meta.panels).fields : [];
        _.each(panelFields, function(panelField) {
            metaFields.push(panelField);
            if (panelField.fields) {
                _.each(panelField.fields, function(subfield) {
                    metaFields.push(subfield);
                }, this);
            }
        }, this);

        return _.object(_.pluck(metaFields, 'name'), metaFields);
    },

    /**
     * @inheritdoc
     * @return {boolean}
     */
    beforeRouteChange: function() {
        let isDirty = _.some(this.collection.models, function(model) {
            return model.changedAttributes();
        });
        if (isDirty) {
            let targetUrl = Backbone.history.getFragment();
            // Replace the url hash back to the current staying page
            app.router.navigate(this._currentUrl, {trigger: false, replace: true});
            app.alert.show('leave_confirmation', {
                level: 'confirmation',
                messages: app.lang.get('LBL_WARN_UNSAVED_CHANGES', this.module),
                onConfirm: _.bind(function() {
                    this.collection.reset();
                    if (app.drawer.count()) {
                        app.drawer.close();
                    }
                    app.router.navigate(targetUrl, {trigger: true});
                }, this),
                onCancel: $.noop
            });
            return false;
        }
        return true;
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        // For each model in the collection, adjust the display type column to
        // show/operate correctly based on the selected display type
        _.each(this.collection.models, function(model) {
            this._adjustModelDisplayTypeField(model);
        }, this);
    },

    /**
     * Handles when the display type field changes on a model
     *
     * @param {Backbone.Model} model the module model that was changed
     * @private
     */
    _displayTypeChanged: function(model) {
        // Revert any changes made to the last shown display type field
        let displayType = model.get('module_display_type');
        let revertField = displayType === 'icon' ? 'module_abbreviation' : 'module_icon';
        model.set(revertField, model._syncedAttributes[revertField] || '');
        this._adjustModelDisplayTypeField(model);
    },

    /**
     * Adjusts the Display Type column for the given model so the subfields
     * show/operate correctly based on the selected display type
     *
     * @param {Backbone.Model} model the model representing the module settings
     * @private
     */
    _adjustModelDisplayTypeField: function(model) {
        let displayType = model.get('module_display_type');
        let iconField = this.getField('module_icon', model);
        let abbreviationField = this.getField('module_abbreviation', model);

        if (iconField) {
            iconField.def.required = displayType === 'icon';
            iconField.render();
            iconField.$el.parent().toggle(displayType === 'icon');
        }

        if (abbreviationField) {
            abbreviationField.def.required = displayType === 'abbreviation';
            abbreviationField.render();
            abbreviationField.$el.parent().toggle(displayType === 'abbreviation');
        }
    },

    /**
     * Function to do the api call to fetch the renamable module list with values
     */
    fetchModules: function() {
        let options = {
            success: _.bind(function(modulesData) {
                let newModels = [];
                modulesData.forEach(function(moduleData) {
                    let model = new Backbone.Model(moduleData);
                    model.fields = app.utils.deepCopy(this.metaFields);
                    model._syncedAttributes = app.utils.deepCopy(model.attributes);
                    newModels.push(model);
                }, this);
                this.collection.reset(newModels);
                this.render();
            }, this),
            error: _.bind(this._showErrorAlert, this),
            complete: _.bind(function() {
                app.alert.dismiss('module-names-and-icons-loading');
            }, this),
        };

        app.alert.show('module-names-and-icons-loading', {
            level: 'process',
            title: app.lang.get('LBL_LOADING'),
        });

        app.api.call('read', this._getConfigURL(), [], options, {context: this});
    },

    /**
     * Click handler for the save button, triggers save event
     */
    saveConfig: function() {
        if (!this.validateCollection()) {
            this._showErrorAlert({
                message: 'ERR_RESOLVE_ERRORS'
            });
            return;
        }
        if (this.triggerBefore('save')) {
            let saveButton = this.getField('save_button');
            if (saveButton && _.isFunction(saveButton.setDisabled)) {
                saveButton.setDisabled(true);
            }
            this._saveConfig();
        }
    },

    /**
     * Validates each model in the collection
     */
    validateCollection: function() {
        let isValid = true;
        _.each(this.collection.models, function(model) {
            isValid = this.validateModel(model) && isValid;
        }, this);

        return isValid;
    },

    /**
     * Validates the fields of a given model. Applies error styling to the
     * field if errors are encountered
     *
     * @param {Backbone.Model} model The model that was changed
     */
    validateModel: function(model) {
        let isValid = true;
        _.each(_.keys(model.fields), function(fieldName) {
            let field = this.getField(fieldName, model);
            if (!field) {
                return;
            }

            let fieldEl = field.$el;
            fieldEl.removeClass('error');
            if (field.def.required && _.isEmpty(model.get(fieldName))) {
                fieldEl.addClass('error');
                isValid = false;
            }
        }, this);

        return isValid;
    },

    /**
     * Function to show alert message
     *
     * @param err [Error] if an error occurred, this value will be filled
     * @private
     */
    _showErrorAlert: function(err) {
        let saveButton = this.getField('save_button');
        if (saveButton && _.isFunction(saveButton.setDisabled)) {
            saveButton.setDisabled(false);
        }
        app.alert.show('module-names-and-icons-warning', {
            level: 'error',
            title: app.lang.get('LBL_ERROR'),
            messages: err.message,
        });
    },

    /**
     * Calls the context model save and saves the config model in case
     * the default model save needs to be overwritten
     *
     * @protected
     */
    _saveConfig: function() {
        let options = {
            success: _.bind(function() {
                this.showSavedConfirmation();
                this.collection.reset();
                if (app.drawer.count()) {
                    app.drawer.close(this.context, this.context.get('model'));
                } else {
                    app.router.navigate(this.module, {trigger: true});
                }
                app.sync();
            }, this),
            error: _.bind(this._showErrorAlert, this),
            complete: _.bind(function() {
                app.alert.dismiss('module-names-and-icons-save');
            }, this),
        };
        app.alert.show('module-names-and-icons-save', {
            level: 'process',
            title: app.lang.get('LBL_SAVING'),
            autoClose: false
        });

        app.api.call('update', this._getConfigURL(), this._getSaveConfigAttributes(), options);
    },

    /**
     * Extensible function that returns the module/config URL for save
     *
     * @return {string} The Config Save URL
     * @protected
     */
    _getConfigURL: function() {
        return app.api.buildURL(this.module, `module-names-and-icons/${this.model.get('language_selection')}`);
    },

    /**
     * Extensible function that returns the model attributes for save
     *
     * @return {Object} The Config Save attributes object
     * @protected
     */
    _getSaveConfigAttributes: function() {
        let changedModules = [];
        this.collection.models.forEach(function(model) {
            if (Object.keys(model.changedAttributes()).length > 0) {
                changedModules.push({
                    module_key: model.get('module_key'),
                    module_name: model.get('module_name'),
                    module_singular: model.get('module_singular'),
                    module_plural: model.get('module_plural'),
                    module_display_type: model.get('module_display_type'),
                    module_abbreviation: model.get('module_abbreviation'),
                    module_icon: model.get('module_icon'),
                    module_color: model.get('module_color'),
                });
            }
        });
        return {
            changedModules: changedModules
        };
    },

    /**
     * Show the saved confirmation alert
     *
     * @param {Object|Undefined} [onClose] the function fired upon closing.
     */
    showSavedConfirmation: function(onClose) {
        onClose = onClose || function() {};
        app.alert.dismiss('module-names-and-icons-save');
        var alert = app.alert.show('module_config_success', {
            level: 'success',
            title: app.lang.get('LBL_MODULE_NAMES_AND_ICONS_SETTINGS', this.module, this.moduleLangObj) + ':',
            messages: app.lang.get('LBL_MODULE_NAMES_AND_ICONS_SETTINGS_SAVED', this.module, this.moduleLangObj),
            autoClose: true,
            autoCloseDelay: 10000,
            onAutoClose: _.bind(function() {
                alert.getCloseSelector().off();
                onClose();
            })
        });
        var $close = alert.getCloseSelector();
        $close.on('click', onClose);
        app.accessibility.run($close, 'click');
    },

    /**
     * Cancels the changing module names and icons process and redirects back
     */
    cancelConfig: function() {
        if (this.triggerBefore('cancel')) {
            // If we're inside a drawer
            if (app.drawer.count()) {
                // close the drawer
                app.drawer.close(this.context, this.context.get('model'));
            } else {
                app.router.navigate(this.module, {trigger: true});
            }
        }
    },

    /**
     * @inheritdoc
     */
    dispose: function() {
        app.routing.offBefore('route', this.beforeRouteChange, this);
        this.stopListening();
        this._super('dispose');
    }
})
