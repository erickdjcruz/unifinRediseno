
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
 * @class View.Fields.Base.DriWorkflows.CJDRIWorkflowFilterField
 * @alias SUGAR.App.view.fields.DriWorkflows.BaseCJDRIWorkflowFilterField
 * @extends View.Fields.Base.ActiondropdownField
 */
({
    extendsFrom: 'ActiondropdownField',

    selectedOption: 'active_smart_guides',

    filterOptions: {
        'archived': 'archive_smart_guides',
        'active': 'active_smart_guides',
        'all': 'all_smart_guides',
    },

    /**
     * Gets the dropdown template and caches it to `this.dropdownTpl`.
     *
     * @return {Function} The handlebars dropdown template.
     * @protected
     */
    _getDropdownTpl: function() {
        this.dropdownTpl = this.dropdownTpl ||
            app.template.getField('cj-dri-workflow-filter', 'dropdown', this.module);
        return this.dropdownTpl;
    },

    /**
     * Appends the dropdown from `dropdown.hbs` and binds the
     * {@link #switchButton} method to the dropdown buttons if necessary.
     *
     * @param {Event} evt The `click` event.
     */
    renderDropdown: function(evt) {
        let $dropdown = this.$(this.dropdownTag);

        if ($dropdown.is(':empty')) {
            let dropdownTpl = this._getDropdownTpl();

            $dropdown.append(dropdownTpl(this));
        }

        _.each(this.dropdownFields, function(field) {
            this.handleFieldCss(field);
            if (this.def.switch_on_click && !this.def.no_default_action) {
                field.$el.on('click.' + this.cid, _.bind(this.switchButton, this));
            }
            field.render();
        }, this);
    },

    /**
     * Handles css for the button field
     *
     * @param field
     */
    handleFieldCss: function(field) {
        let filterName = this.getFiltername();
        this.selectedOption = !_.isUndefined(filterName) ? filterName : this.selectedOption;

        if (field.def.name !== this.selectedOption) {
            field.def.icon = '';
            field.def.css_class = 'filter-check-hidden';
        } else {
            field.def.css_class = 'filter-check-shown highlight';
        }

        field.setElement(this.$('span[sfuuid="' + field.sfId + '"]'));
        field.$el.off(`mouseover.${this.cid}`);
        field.$el.on(`mouseover.${this.cid}`, _.bind(function() {
            this.$el
                .closest('.workflow-filter-view-items')
                .find('.highlight')
                .removeClass('highlight');
        }, field));
    },

    /**
     * Get the filter name
     */
    getFiltername: function() {
        if (!_.isUndefined(this.model.get('cj_active_or_archive_filter'))) {
            if (_.isEqual(this.model.get('cj_active_or_archive_filter'), 'active')) {
                return 'active_smart_guides';
            } else if (_.isEqual(this.model.get('cj_active_or_archive_filter'), 'archived')) {
                return 'archive_smart_guides';
            }
            return 'all_smart_guides';
        } else {
            const module = (this.context && this.context.get('parentModule')) ? this.context.get('parentModule') : '';
            const mode = app.CJBaseHelper.getValueFromCache('toggleActiveArchived', 'cj_active_or_archive_filter',
            module, 'dri-workflows-widget-configuration');
            return this.filterOptions[mode];

        }
    },

    /**
     * @inheritdoc
     */
    _toggleAria: function() {
        this._super('_toggleAria');
        if (this.$el.find('a.dropdown-toggle').hasClass('show')) {
            this.$el.find('.dropdown-toggle').toggleClass('filter-dropdown-open', true);
            this.$el.find('.dropdown-toggle').removeClass('filter-dropdown-close');
            //this is particular for parent span
            this.$el.find('.dropdown-toggle').parent().toggleClass('filterBg', true);
        } else {
            this.$el.find('.dropdown-toggle').removeClass('filter-dropdown-open');
        }
    },
})
