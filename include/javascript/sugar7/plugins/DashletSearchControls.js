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
(function(app) {
    app.events.on('app:init', function() {

        /**
         * This plugin adds search and sort controls to a dashlet.
         *
         * Two variables must be defined on the dashlet:
         *   - sortItems: array of kv pairs for the sort dropdown
         *   - searchFieldPlaceholder: string to display as placeholder text in the search field
         *
         * This plugin does not handle the details of the search - that is left to the dashlet.
         * It only sets up the controls. The dashlet must provide the following functions that will
         * be called when a search or sort event fires:
         *   - applySearch
         *   - applySort
         **/
        app.plugins.register('DashletSearchControls', ['view'], {
            onAttach: function(component, plugin) {
                this.on('init', function() {
                    if (!_.isEmpty(component.sortItems) && !_.isEmpty(component.searchFieldPlaceholder)) {
                        this._initDashletControls();
                        this._listenForEvents();
                    }
                }, this);
            },

            /**
             * Adds the dashlet search and sort controls to the top of the dashlet
             * @private
             */
            _initDashletControls: function() {
                if (!this.layout || this._mode === 'config') {
                    return;
                }

                // Add the search and sort controls
                var searchControls = app.view.createView({
                    context: this.context,
                    type: 'dashlet-search-controls',
                    module: this.module,
                    primary: false,
                    layout: this.layout,
                    sortItems: this.sortItems,
                    searchFieldPlaceholder: this.searchFieldPlaceholder
                });
                this.layout.addComponent(searchControls, {
                    prepend: true
                });
            },

            /**
             * Set up event listeners for sort/search changes
             * @private
             */
            _listenForEvents: function() {
                if (!this.layout || this._mode === 'config') {
                    return;
                }

                if (_.isFunction(this.applySort)) {
                    this.listenTo(this.layout, 'dashlet:controls:sort', this.applySort);
                }
                if (_.isFunction(this.applySearch)) {
                    this.listenTo(this.layout, 'dashlet:controls:search', this.applySearch);
                }
            }
        });
    });
})(SUGAR.App);
