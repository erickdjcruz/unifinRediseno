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
 * This is the base view for Config Framework. To create a new config page, eg, for category 'test' with one setting 'anything',
 * at least you need to create a metadata file: modules/Administration/clients/base/views/test-config/test-config.php
 * with following content:
 *
 *       $viewdefs['Administration']['base']['view']['test-config'] = [
 *           'template' => 'record',
 *           'label' => 'LBL_TEST_CONFIG_TITLE',
 *           'saveMessage' => 'LBL_TEST_CONFIG_SAVE_MESSAGE',
 *           'panels' => [
 *               [
 *                   'name' => 'panel_body',
 *                   'label' => 'LBL_PANEL_1',
 *                   'columns' => 1,
 *                   'labelsOnTop' => true,
 *                   'placeholders' => true,
 *                   'newTab' => false,
 *                   'panelDefault' => 'expanded',
 *                   'fields' => [
 *                       [
 *                           'name' => 'test_anything',
 *                           'type' => 'text',
 *                           'label' => 'LBL_TEST_ANYTHING',
 *                           'span' => 6,
 *                           'labelSpan' => 4,
 *                           'required' => true,
 *                       ],
 *                   ],
 *                   'helpLabels' => [
 *                       [
 *                           'text' => 'LBL_TEST_CONFIG_HELP_TEXT_CONTENT',
 *                       ],
 *                   ],
 *               ],
 *           ],
 *      ];
 *
 * The url for this page will be sugar_url/#Administration/config/test.
 *
 * If needed, you can create a custom controller by extending this view.
 *
 * @class View.Views.Base.AdministrationConfigView
 * @alias SUGAR.App.view.views.BaseAdministrationConfigView
 * @extends View.Views.Base.RecordView
 */
({
    extendsFrom: 'RecordView',

    /**
     * The main setting prefix.
     * @property {string}
     */
    settingPrefix: '',

    /**
     * Message to show on successful save.
     * @property {string}
     */
    saveMessage: '',

    /**
     * The css class used for the main element.
     * * @property {string}
     */
    className: 'admin-config-body',

    /**
     * The help strings to be displayed in the help block.
     * @property {Object}
     */
    helpBlock: {},

    /**
     * A collection of variables used for help block text interpolation.
     * @property {Object}
     */
    helpBlockContext: null,

    /**
     * Initialize the help block displayed below the configuration field(s).
     *
     * @inheritdoc
     */
    initialize: function(options) {
        if (!options.meta) {
            options.meta = app.metadata.getView(options.context.get('module'), 'config');
        }
        this._super('initialize', [options]);
        this.helpBlock = this.generateHelpBlock();
        this.addValidationTask();
        this.settingPrefix = 'config/' + options.context.get('category');
        this.saveMessage = this.meta['saveMessage'] || '';
        this.boundSaveHandler = _.bind(this.validateModel, this);
        this.context.on('save:config', this.boundSaveHandler);
        this.loadSettings();
    },

    /**
     * Load any existing configuration.
     */
    loadSettings: function() {
        var options = {
            success: _.bind(this.loadSettingsSuccessCallback, this)
        };
        app.api.call('get', app.api.buildURL(this.module, this.settingPrefix), [], options, {context: this});
    },

    /**
     * The success callback to execute when settings have been retrieved
     *
     * @param settings
     */
    loadSettingsSuccessCallback: function(settings) {
        this.copySettingsToModel(settings);
        this.render();
    },

    /**
     * Render the view in edit mode and display the help block.
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        this.action = 'edit';
        this.toggleEdit(true);
        this.renderHelpBlock();
    },

    /**
     * Save the settings.
     */
    save: function() {
        var options = {
            error: _.bind(this.saveErrorHandler, this),
            success: _.bind(this.saveSuccessHandler, this)
        };
        app.api.call('create', app.api.buildURL(this.module, this.settingPrefix), this.model.toJSON(), options);
    },

    /**
     * On a successful save the Save button has to be disabled and
     * a message will be shown indicating that the settings have been saved.
     *
     * @param {Object} settings The aws connect settings.
     */
    saveSuccessHandler: function(settings) {
        this.toggleHeaderButton(false);
        this.updateConfig(settings);
        this.closeView();
        app.alert.show(this.settingPrefix + '-info', {
            autoClose: true,
            level: 'success',
            messages: app.lang.get(this.saveMessage, this.module)
        });
    },

    /**
     * Set settings on the model.
     *
     * @param {Object} settings details.
     */
    copySettingsToModel: function(settings) {
        this.boundChangeHandler = _.bind(this.toggleHeaderButton, this);
        _.each(settings, function(value, key) {
            this.model.set(key, value);
        }, this);
        this.model.on('change', this.boundChangeHandler);
    },

    /**
     * It will change the Save button enabled/disabled state.
     *
     * @param {boolean} state The state to be set.
     */
    toggleHeaderButton: function(state) {
        var header = this.layout.getComponent(this.name + '-header');
        if (header) {
            header.enableButton(state);
        }
    },

    /**
     * Show an error message if the settings could not be saved.
     */
    saveErrorHandler: function() {
        app.alert.show(this.settingPrefix + '-warning', {
            level: 'error',
            title: app.lang.get('LBL_ERROR')
        });
    },

    /**
     * Add validation tasks to the current model so any aws related fields could be validated.
     */
    addValidationTask: function() {
        this.model.addValidationTask('fields_required', _.bind(this.validateRequiredFields, this.model));
    },

    /**
     * It will validate required fields.
     *
     * @param {Array} fields The list of fields to be validated.
     * @param {Object} errors A list of error messages.
     * @param {Function} callback Callback to be called at the end of the validation.
     */
    validateRequiredFields: function(fields, errors, callback) {
        _.each(fields, function(field) {
            if (_.has(field, 'required') && field.required) {
                var key = field.name;

                if (!this.get(key)) {
                    errors[key] = errors[key] || {};
                    errors[key].required = true;
                }
            }
        }, this);
        callback(null, fields, errors);
    },

    /**
     * It triggers the save process if all fields are valid.
     *
     * @param {boolean} isValid If all the fields are valid.
     */
    validationComplete: function(isValid) {
        if (isValid) {
            this.save();
        }
    },

    /**
     * Trigger the field validation through the model
     */
    validateModel: function() {
        var fields = this.getFieldsToValidate();
        this.model.doValidate(fields, _.bind(this.validationComplete, this));
    },

    /**
     * Get fields to validate
     * @return {Object}
     */
    getFieldsToValidate: function() {
        return this.meta.panels && this.meta.panels[0] && this.meta.panels[0].fields || {};
    },

    /**
     * On a successful save return to the Administration page.
     */
    closeView: function() {
        // Config changed... reload metadata
        app.sync();
        if (app.drawer && app.drawer.count()) {
            app.drawer.close(this.context, this.context.get('model'));
        } else {
            app.router.navigate(this.module, {trigger: true});
        }
    },

    /**
     * Update the settings stored in the front-end.
     *
     * @param {Object} settings.
     */
    updateConfig: function(settings) {
        _.each(settings, function(value, key) {
            app.config[app.utils.getUnderscoreToCamelCaseString(key)] = value;
        });
    },

    /**
     * Return the strings for help block.
     *
     * @return {Object}
     */
    generateHelpBlock: function() {
        var helpTemplate = app.template.getView(this.name + '.help-block', this.module) ||
            app.template.getView('config.help-block', this.module);
        var block = {};

        _.each(this.meta.panels, function(panel) {
            var contents = [];

            contents.push(...this.getHelpLabels(panel));
            contents.push(...this.getLinkLabels(panel));

            if (!_.isEmpty(contents)) {
                block[panel.name] = helpTemplate(contents);
            }
        }, this);

        return block;
    },

    /**
     * Creates and returns the translated help block title.
     *
     * @param {Object} label An object holding labels to be translated.
     * @return {string} The help block name.
     */
    getHelpBlockName: function(label) {
        if (_.isUndefined(label.name)) {
            return '';
        }
        var translation = app.lang.get(label.name, this.module, this.helpBlockContext);

        return translation + ':';
    },

    /**
     * Creates and returns the translated help block text.
     *
     * @param {string} text The key to the language text
     * @return {string} The help block text.
     */
    getHelpBlockText: function(text) {
        if (_.isEmpty(text)) {
            return '';
        }
        var translation = app.lang.get(text, this.module, this.helpBlockContext);

        return new Handlebars.SafeString(translation);
    },

    /**
     * Render help block. By default it will append the blocks to the record container.
     */
    renderHelpBlock: function() {
        var panel = this.$el.find('.record');
        _.each(this.helpBlock, function(block) {
            if (panel.length) {
                panel.append(block);
            }
        }, this);
    },

    /** Get hbs ready help labels
     *
     * @param panel
     * @return {Array}
     */
    getHelpLabels: function(panel) {
        var help = [];

        if (!panel.helpLabels) {
            return help;
        }

        _.each(panel.helpLabels, function(label) {
            help.push({
                name: this.getHelpBlockName(label),
                label: label.text || '',
                css_class: label.css_class,
                text: this.getHelpBlockText(label.text)
            });
        }, this);

        return help;
    },

    /**
     * Get hbs ready link labels
     *
     * @param panel
     * @return {Array}
     */
    getLinkLabels: function(panel) {
        var links = [];

        if (!panel.linkLabels) {
            return links;
        }

        _.each(panel.linkLabels, function(label) {
            if (!label.link) {
                return;
            }

            links.push({
                is_link: true,
                link_text: this.getHelpBlockText(label.link.text),
                link_css_class: label.link.css_class,
                link_href: label.link.href,
                link_target: label.link.target ? label.link.target : '_self',
                name: label.name,
                css_class: label.css_class ? label.css_class : '',
                text: this.getHelpBlockText(label.text)
            });
        }, this);

        return links;
    },

    /**
     * Toggles visibility for a field. On these fields we have
     * to hide/show the field itself, and its parent record-cell
     *
     * @param {Object} field - field to hide/show
     * @param {boolean} show - whether or not to display the field
     */
    _toggleFieldVisibility: function(field, show) {
        if (!field) {
            return;
        }

        field.$el.closest('.record-cell').toggle(show);
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        if (!this.disposed) {
            this.context.off('save:config', this.boundSaveHandler);
            this.model.off('change', this.boundChangeHandler);
            this._super('_dispose');
        }
    }
})
