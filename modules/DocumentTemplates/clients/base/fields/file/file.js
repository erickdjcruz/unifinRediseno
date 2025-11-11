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
 * @class View.Fields.Base.DocumentTemplates.FileField
 * @alias SUGAR.App.view.fields.BaseDocumentTemplatesFileField
 * @extends View.Fields.Base.BaseField
 */
({
    extendsFrom: 'BaseFileField',

    supportedFileExtensions: ['docx', 'xlsx', 'pptx',],

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', arguments);

        app.error.errorName2Keys = app.error.errorName2Keys || {};
        app.error.errorName2Keys.unsupportedExtension = 'ERROR_WRONG_EXTENSION';
    },

    /**
     * @inheritdoc
     */
    _doValidateFile: function(fields, errors, callback) {
        const fieldName = this.name;
        const fileName = this.model.get(this.name);

        if (this.def.required && _.isEmpty(fileName)) {
            errors[fieldName] = errors[fieldName] || {};
            errors[fieldName].required = true;
            callback(null, fields, errors);
            return;
        }

        if (_.isString(fileName)) {
            const extension = fileName.substr(fileName.lastIndexOf('.') + 1);
            if (!this.supportedFileExtensions.includes(extension)) {
                errors[this.name] = errors[this.name] || {};
                errors[this.name].unsupportedExtension = true;
                callback(null, fields, errors);
                app.alert.show('file_ext_error', {
                    level: 'error',
                    messages: app.lang.getModString('LBL_ERROR_WRONG_EXTENSION', this.module),
                });
                return;
            }
        }

        var fileInputEl = this.$(this.fieldTag);
        if (fileInputEl.length === 0) {
            callback(null, fields, errors);
            return;
        }

        var val = fileInputEl.val();
        if (!_.isEmpty(val)) {
            this._uploadFile(fieldName, fileInputEl, fields, errors, callback);
        } else {
            callback(null, fields, errors);
            return;
        }
    }
})
