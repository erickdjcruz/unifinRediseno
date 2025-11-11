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
 * @class View.Fields.Base.NameField
 * @alias SUGAR.App.view.fields.BaseNameField
 * @extends View.Fields.Base.BaseField
 */
({
    plugins: ['FocusDrawer', 'MetadataEventDriven'],

    _render: function() {
        // FIXME: This will be cleaned up by SC-3478.
        if (['audit', 'side-drawer-headerpane'].includes(this.view.name)) {
            this.def.link = false;
        } else if (this.view.name === 'preview') {
            this.def.link = _.isUndefined(this.def.link) ? true : this.def.link;
            this.def.events = false;
        }
        this._super('_render');
    },

    /**
     * Used by the FocusDrawer plugin to get the ID of the record this field
     * links to
     *
     * @return {string} the ID of the related record
     */
    getFocusContextModelId: function() {
        return this.model && this.model.get('id') ? this.model.get('id') : '';
    },

    /**
     * Used by the FocusDrawer plugin to get the name of the module this
     * field links to
     *
     * @return {string} the name of the related module
     */
    getFocusContextModule: function() {
        return this.model && this.model.get('_module') ? this.model.get('_module') : '';
    },

    /**
     * Called by record view to set max width of inner record-cell div
     * to prevent long names from overflowing the outer record-cell container
     */
    setMaxWidth: function(width) {
        this.$el.css({'max-width': width});
    },

    /**
     * Return the width of padding on inner record-cell
     */
    getCellPadding: function() {
        let padding = 0;
        let $cell = this.$('.dropdown-toggle');

        if ($cell.length > 0) {
            padding = parseInt($cell.css('padding-left'), 10) + parseInt($cell.css('padding-right'), 10);
        }

        return padding;
    }
})
