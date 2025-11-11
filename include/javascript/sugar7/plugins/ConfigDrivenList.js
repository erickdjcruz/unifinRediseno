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
 * This plugin can be added to existing list layouts/views to allow for admin configured properties to affect styling
 * and functionality. The contract needed to be honored for each function is provided on each function
 */
(function(app) {
    app.events.on('app:init', function() {
        app.plugins.register('ConfigDrivenList', ['layout', 'view'], {
            /**
             * @inheritdoc
             *
             * Bind events related to config properties
             */
            onAttach(component, plugin) {
                this.before('render', this._beforeRenderActions, this);
            },

            /**
             * Function responsible for housing the various actions to be performed before rendering of the list
             * component. This can be added to as functionality is increased
             * @private
             */
            _beforeRenderActions() {
                this._toggleFrozenHeaders();
                this._toggleFrozenFirstColumn();
            },

            /**
             * Toggles whether the list headers are frozen or not based on admin config
             *
             * toggleFrozenHeaders() is triggered before render on all list layouts/views that include this plugin and
             * it will dynamically add the 'frozen-list-headers' scoping class to the component. Lists that implement
             * this will need to update a CSS tree to apply styles that create this effect (see list.less for example)
             */
            _toggleFrozenHeaders() {
                const state = app.config.freezeListHeaders;
                this.$el.toggleClass('frozen-list-headers', state);
            },

            /**
             * Toggles whether the first column of lists are frozen or not based on admin config and list config
             *
             * toggleFrozenFirstColumn() is triggered before render on all list layouts/views that include this plugin
             * and it will dynamically add the 'sticky-first-column' scoping class to the component. Lists that
             * implement this will need to update a CSS tree to apply styles that create this effect. (see
             * datatables.less for example). Lists will also be required to have 'this.hasFrozenColumn' set on the
             * controller which is based on the lists unique configuration.
             *
             * If you want the frozen column to have the right border applied on scroll, you must also define
             * 'this.scrollContainer' on the controller. This will contain the jQuery object that points to the
             * scrolling container within the layout/view (i.e. 'this.$el.find('.dashablerecord .tab-content')')
             */
            _toggleFrozenFirstColumn() {
                const hasFrozenColumn = this._getFrozenColumnConfig();

                const state = app.config.allowFreezeFirstColumn && hasFrozenColumn;
                this.$el.toggleClass('sticky-first-column', state);

                // action check is needed because of unique scenarios like list inside of recordview dashlet
                const hasScrollContainer = !_.isNull(this.scrollContainer) || !_.isUndefined(this.scrollContainer);
                if (state && hasScrollContainer && this.action === 'list') {
                    this.listenToOnce(this, 'render', () => {
                        this.scrollContainer.on('scroll',
                            _.throttle(_.bind(this._toggleFrozenColumnBorder, this), 200));
                    });
                }
            },

            /**
             * Grabs the freeze_first_column config value
             * @return {boolean}
             */
            _getFrozenColumnConfig() {
                let settings;
                const listName = this.name;
                switch (listName) {
                    case 'dashablelist':
                    case 'dashlet-console-list':
                        settings = this.settings;
                        break;
                    case 'dashablerecord':
                        if (this._getActiveTab().type === 'list') {
                            settings = this.model;
                        } else {
                            settings = false;
                        }
                        break;
                    default:
                        settings = false;
                }

                if (!settings) {return false;}
                if (app.config.allowFreezeFirstColumn) {
                    return settings.get('freeze_first_column');
                } else {
                    return false;
                }
            },

            /**
             * Toggles the left border displayed on sticky scrolling based on the 'this.scrollContainer' defined on the
             * controller. This is called on scroll event created in _toggleFrozenFirstColumn
             */
            _toggleFrozenColumnBorder() {
                const firstCol = this.$('table tbody tr td:first-child, table thead tr th:first-child');
                firstCol.toggleClass('border-r', this.scrollContainer[0].scrollLeft > 0);
            },

            /**
             * Filters field metadata based on configuration for dashlet config view
             *
             * Publically available function to be implemented by all dashlets in order to filter their config fields
             * Called at different places depending on dashlet implementation
             */
            filterConfigFieldsForDashlet() {
                this.meta.panels.forEach(panel => {
                    panel.fields = _.filter(panel.fields, function(field) {
                        return field.showOnConfig ? app.config[field.showOnConfig] : true;
                    });
                });
            },

            /**
             * @inheritdoc
             *
             * Clean up associated event handlers.
             */
            onDetach: function(component, plugin) {
                this.off('render', this._toggleFrozenHeaders, this);
                this.off('render', this._toggleFrozenFirstColumn, this);
                this.stopListening();
            }
        });
    });
})(SUGAR.App);
