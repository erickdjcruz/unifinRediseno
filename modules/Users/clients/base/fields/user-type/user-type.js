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
 * @class View.Fields.Base.Users.UserTypeField
 * @alias SUGAR.App.view.fields.BaseUsersUserTypeField
 * @extends View.Fields.Base.EnumField
 */
({
    extendsFrom: 'EnumField',

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');
        this.listenTo(this.model, `change:${this.name}`, this.render);
    },

    /**
     * @inheritdoc
     *
     * Turns boolean model value into number template value
     */
    format: function(value) {
        return +value;
    },

    /**
     * @inheritdoc
     *
     * Turns number template value into boolean model value
     */
    unformat: function(value) {
        return !!parseInt(value);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        // Add the info label for the user type
        let optionInfoDef = this.def.optionInfo || {};
        let optionInfo = optionInfoDef[+this.model.get(this.name)] || '';
        this.$el.append(app.lang.get(optionInfo, this.module));
    },

    /**
     * @inheritdoc
     */
    _loadTemplate: function() {
        this.type = 'enum';
        this._super('_loadTemplate');
        this.type = this.def.type;
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this._super('_dispose');
        this.stopListening();
    }
})
