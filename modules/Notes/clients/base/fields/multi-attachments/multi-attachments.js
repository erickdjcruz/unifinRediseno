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
 * Multi Attachment modifications specific to Notes. These
 * modifications create a pill for files stored directly
 * on the note record's file fields.
 *
 * @class View.Fields.Base.Notes.MultiAttachmentsField
 * @alias SUGAR.App.view.fields.BaseNotesMultiAttachmentsField
 * @extends View.Fields.Base.MultiAttachmentsField
 */

({
    /**
     * Override multi-attachments field for notes
     */
    extendsFrom: 'BaseMultiAttachmentsField',

    /**
     * Name of file field on model
     */
    fileFieldName: 'filename',

    /**
     * Name of field holding file mime type
     */
    fileMimeTypeFieldName: 'file_mime_type',

    /**
     * If we are displaying only a single image, we show preview for it
     */
    singleImage: false,

    /**
     * Mapping of accepted image mimetypes to file extensions
     */
    supportedImageExtensions: {
        'image/jpeg': 'jpg',
        'image/png': 'png',
        'image/gif': 'gif'
    },

    /**
     * Re-render when the file and mimetype fields change
     */
    bindDataChange: function() {
        this._super('bindDataChange');
        this.model.on('change:' + this.fileFieldName, this.render, this);
        this.model.on('change:' + this.fileMimeTypeFieldName, this.render, this);
    },

    /**
     * Override format to add/remove single file pills, and set flag for image
     * preview
     *
     * @param {array} value array of field pills
     * @returns {array} formatted array of field pills
     */
    format: function(value) {
        if (!this._pillAdded(value) && this._modelHasFileAttachment()) {
            var attr = this._getPillFromFile();

            if (value instanceof app.BeanCollection) {
                var model = app.data.createBean('Notes', attr);
                value.models.push(model);
            } else {
                value.push(attr);
            }
        }
        value = this._super('format', [value]);
        this.singleImage = this._singleImagePill(value);
        return value;
    },

    /**
     * Determine if a pill has already been added to this field for an
     * attachment stored directly on the note.
     * @param value
     * @returns {boolean}
     * @private
     */
    _pillAdded: function(value) {
        value = value instanceof app.BeanCollection ? value.models : value;

        return _.reduce(value, function(base, item) {
            item = item instanceof Backbone.Model ? item.toJSON() : item;
            return base || item.id === this.model.id;
        }, false, this);
    },

    /**
     * Util to see if this model has a file stored directly
     * @returns {boolean}
     * @private
     */
    _modelHasFileAttachment: function() {
        return !!(this.model.get(this.fileFieldName) &&
                  this.model.get(this.fileMimeTypeFieldName));
    },

    /**
     * Creates a pill in the format needed by select2 for file stored directly
     * on the note.
     * @returns {Object} {filename: file name,
     *                    id: ID of this note for file link,
     *                    file_mime_type: file mime type}
     * @private
     */
    _getPillFromFile: function() {
        var attr = {id: this.model.get('id')};
        attr[this.fileFieldName] = this.model.get(this.fileFieldName);
        attr[this.fileMimeTypeFieldName] = this.model.get(this.fileMimeTypeFieldName);
        return attr;
    },

    /**
     * Override base field's removeAttachment method to remove file stored
     * directly on the note if needed
     * @param event {Object}
     */
    removeAttachment: function(event) {
        if (event.val === this.model.get('id')) {
            this._removeLegacyAttachment();
            this.pillAdded = false;
            this.render();
        } else {
            this._super('removeAttachment', [event]);
        }
    },

    /**
     * Removes file stored directly on the note
     * @private
     */
    _removeLegacyAttachment: function() {
        this.model.set(this.fileFieldName, '');
        var value = this.model.get(this.name).models;
        this.model.get(this.name).models = _.filter(value, function(model) {
            return model.get('id') !== this.model.get('id');
        }, this);
    },

    /**
     * Check if input mime type is an image or not.
     *
     * @param {String} mimeType - file mime type.
     * @return {Boolean} true if mime type is an image.
     * @private
     */
    _isImage: function(mimeType) {
        return !!this.supportedImageExtensions[mimeType];
    },

    /**
     * Check if we're rendering a single pill for an image file
     * @param {array} value - list of pills to display
     * @returns {boolean}
     * @private
     */
    _singleImagePill: function(value) {
        return value && value.length === 1 && value[0].mimeType === 'image';
    }
})
