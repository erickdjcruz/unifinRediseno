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
 * @class View.Fields.Base.Administration.UrlField
 * @alias SUGAR.App.view.fields.BaseAdministrationUrlField
 * @extends View.Fields.Base.UrlField
 */
({
    extendsFrom: 'UrlField',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.model.addValidationTask('url_' + this.cid, _.bind(this._doValidateUrl, this));
    },

    /**
     * URL validation.
     *
     * @param {Object} fields The list of field names and their definitions.
     * @param {Object} errors The list of field names and their errors.
     * @param {Function} callback Async.js waterfall callback.
     * */
    _doValidateUrl: function(fields, errors, callback) {
        var value = this.model.get(this.name);

        if (value && !/^https?:\/\/[^\s\/$.?#]+\.[^\s]+$/.test(this.format(value))) {
            errors[this.name] = errors[this.name] || {};
            errors[this.name].url = true;
        }

        callback(null, fields, errors);
    },

    /**
     * @inheritdoc
     */
    unformat: function(value) {
        if (value && !value.match(/^([a-zA-Z]+):/)) {
            value = 'http://' + value;
        }

        return this._super('unformat', [value]);
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.model.removeValidationTask('url_' + this.cid);
        this._super('_dispose');
    },
})
