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

({
    extendsFrom: 'RecordView',

    /**
     * @inheritdoc
     *
     * Add KBContent plugin for view.
     */
    initialize: function(options) {
        this.plugins = _.union(this.plugins || [], [
            'KBContent'
        ]);

        this._super('initialize', [options]);
        this.context.on('kbcontents:category:deleted', this._categoryDeleted, this);
    },

    /**
     * Process record on category delete.
     * @param {Object} node
     * @private
     */
    _categoryDeleted: function(node) {
        if (this.model.get('category_id') === node.data('id')) {
            this.model.unset('category_id');
            this.model.unset('category_name');
        }
        if (this.disposed) {
            return;
        }
        this.render();
    },

    /**
     * @inheritdoc
     */
    handleSave: function() {
        // this is to handle the issue caused by different value between boolean and tinyint
        this.model.set('is_external', app.utils.isTruthy(this.model.get('is_external')) ? 1 : 0);
        this._super('handleSave');
    },

    /**
     * @inheritdoc
     *
     * Need to switch field to `edit` if it has errors.
     */
    handleFieldError: function(field, hasError) {
        this._super('handleFieldError', [field, hasError]);
        if (hasError && field.tplName === 'detail') {
            field.setMode('edit');
        }
    },

    /**
     * @inheritdoc
     */
    hasUnsavedChanges: function() {
        // since TinyMCE deletes double spaces on text load, check for actual changes
        const delta = this.model.changedAttributes(this.model.getSynced());
        const key = 'kbdocument_body';
        if (delta && _.isEqual(Object.keys(delta), [key])) {
            const modelValue = this.model.get(key);
            if (modelValue === delta[key].replace(/\s+/g, ' ')) {
                return false;
            }
        }
        return this._super('hasUnsavedChanges');
    }
})
