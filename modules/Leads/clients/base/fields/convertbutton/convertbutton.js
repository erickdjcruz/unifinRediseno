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
 * @class View.Fields.Base.Leads.ConvertbuttonField
 * @alias SUGAR.App.view.fields.BaseLeadsConvertbuttonField
 * @extends View.Fields.Base.RowactionField
 */
({
    extendsFrom: 'RowactionField',

    initialize: function (options) {
        this._super("initialize", [options]);
        this.type = 'rowaction';

        // Fix for when the convert button is in the dashablerecord view
        if (this.view.layout && this.view.layout.type === 'dashlet-grid-wrapper') {
            this.model = this.view.layout.getComponent("dashablerecord").model;
        }
    },

    _render: function () {
        var convertMeta = app.metadata.getLayout('Leads', 'convert-main');
        var missingRequiredAccess = _.some(convertMeta.modules, function (moduleMeta) {
            return (moduleMeta.required === true && !app.acl.hasAccess('create', moduleMeta.module));
        }, this);

        if (this.model.get('converted') || missingRequiredAccess) {
            this.hide();
        } else {
            this._super("_render");
        }
    },

    /**
     * Event to trigger the convert lead process for the lead
     */
    rowActionSelect: function() {
        let model = app.data.createBean(this.model.module);
        model.set(app.utils.deepCopy(this.model.attributes));

        let isOnDashlet = this.view.name === 'dashlet-toolbar';

        app.drawer.open({
            layout : "convert",
            context: {
                forceNew: true,
                skipFetch: true,
                module: 'Leads',
                leadsModel: model,
                doRedirect: !isOnDashlet
            }
        }, success => {
            if (success && isOnDashlet) {
                let dashlet = this.view.layout.getComponent('dashablerecord');
                dashlet._updateAllowedButtons();
            }
        });
    },

    bindDataChange: function () {
        if (this.model) {
            this.model.on("change", this.render, this);
        }
    },

    /**
     * @inheritdoc
     */
    isAllowedDropdownButton: function() {
        // Filter logic for when its on a dashlet
        if (this.view.name === 'dashlet-toolbar') {
            if (this.module === 'Leads') {
                var model = this.context.parent.get('model');
                return model && !model.get('converted') && !_.isUndefined(model.get('converted'));
            }
            return false;
        }
        return true;
    }
})
