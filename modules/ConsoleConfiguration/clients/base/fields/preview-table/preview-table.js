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
 * @class View.Fields.Base.ConsoleConfiguration.PreviewTableField
 * @alias SUGAR.App.view.fields.BaseConsoleConfigurationPreviewTableField
 * @extends View.Fields.Base.BaseField
 */
({
    /**
     * The name of the module the fields belong to.
     * @property {string}
     */
    moduleName: '',

    /**
     * A mapping of fields to be rendered on the preview table.
     * @property {Object}
     */
    fieldList: null,

    /**
     * The number of rows to be shown in the preview.
     * @property {number}
     */
    previewRows: 5,

    /**
     * A mapping of class names that describe how the data rows should appear if there are no live data available.
     * This mapping is based on the fieldList and sent to the template.
     */
    rowDesign: [],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.bindChangeEvent(options.model);
    },

    /**
     * Will listen to an event that signals a change in the console configuration.
     *
     * @param {Data.Bean} model The model used for the preview.
     */
    bindChangeEvent: function(model) {
        if (model && model.get('enabled_module')) {
            this.moduleName = model.get('enabled_module');
            this.eventName = 'consoleconfig:preview:' + this.moduleName;
            this.context.on(this.eventName, this.renderPreview, this);
        }
    },

    /**
     * It will create a mapping of css classes that corresponds to the list of columns to be displayed.
     * Odd and even rows while having a single sub-field should render alternating long and short placeholders,
     * while if there is a field with multiple sub-fields, 2 placeholders should be shown (1 long, 1 short).
     *
     * @param {Array} list A list of fields that have to appear as columns in the preview.
     */
    setRowDesign: function(list) {
        var i;
        var oneRow;
        var singleClass;
        var longClass = 'cell-bar--long';
        var shortClass = 'cell-bar--short';

        this.rowDesign = [];
        for (i = 1; i <= this.previewRows; i++) {
            singleClass = i % 2 === 0 ? shortClass : longClass;
            oneRow = _.reduce(list, function(row, subFields) {
                row.push(subFields.length > 1 ? [longClass, shortClass] : [singleClass]);
                return row;
            }, []);
            this.rowDesign.push(oneRow);
        }
    },

    /**
     * Triggers a render of the prevoew based on a list of field labels. The order of the columns
     * will be inherited from the the order of strings.
     *
     * @param {Array} list A list of fields that have to appear as columns in the preview.
     */
    renderPreview: function(list) {
        this.fieldList = list;
        this.setRowDesign(list);
        this.render();
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.context.off(this.eventName, this.renderPreview, this);
        this._super('_dispose');
    },
})
