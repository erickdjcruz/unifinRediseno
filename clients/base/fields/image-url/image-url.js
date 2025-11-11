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
 * @class View.Fields.Base.ImageUrl
 * @alias SUGAR.App.view.fields.BaseImageUrl
 * @extends View.Fields.Base.UrlField
 */
({
    extendsFrom: 'UrlField',

    preview: null,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        app.error.errorName2Keys.url = 'ERROR_URL';

        this.setPreview();

        this.model.on('change:' + this.name, function() {
            this.setPreview();
            this.render();
        }, this);

        this.model.addValidationTask('image_url_' + this.cid, _.bind(this._doValidateUrl, this));
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

        if (value && !/^(https?|ftp):\/\/[^\s\/$.?#]+\.[^\s]+$/.test(this.format(value))) {
            errors[this.name] = errors[this.name] || {};
            errors[this.name].url = true;
        }

        callback(null, fields, errors);
    },

    /*
     * Set preview value
     */
    setPreview: function() {
        this.preview = this.model.get(this.name) || '';

        if (!this.preview && this.def.default) {
            this.preview = this.def.default + '?v=' + app.config.versionMark;
        }
    },

    /**
     * Remove validation on the model.
     * @inheritdoc
     */
    _dispose: function() {
        this.model.removeValidationTask('image_url_' + this.cid);
        this._super('_dispose');
    },
})
