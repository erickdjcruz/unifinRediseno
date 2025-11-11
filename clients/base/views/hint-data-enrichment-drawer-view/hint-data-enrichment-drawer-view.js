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
 * @class View.Layouts.Base.ConfigDrawerLayout
 * @alias SUGAR.App.view.layouts.BaseConfigDrawerLayout
 * @extends View.Layout
 */
({
    plugins: ['Stage2CssLoader'],

    events: {
        'click .restoreDefaults': 'restoreDefaults',
    },

    /**
     * @inheritdoc
     */
    initialize: function(view) {
        var self = this;
        this._super('initialize', arguments);
        var ctx = this.context;
        var accountsModule = 'Accounts';
        var contactsModule = 'Contacts';
        var leadsModule = 'Leads';
        this.isDarkMode = app.hint.isDarkMode();
        this.currentModule = 'Accounts'; //Default module we always start with
        this.documentationLink = 'http://www.sugarcrm.com/crm/product_doc.php?module=HintAdminConfig';
        this.hintConfig = app.metadata.getModule(this.currentModule, 'config');
        this.configKey = 'hintConfig';
        this.moduleList = app.metadata.getFullModuleList();
        this.shouldShowAccountsTab = this.moduleList[accountsModule] ? true : false;
        this.shouldShowContactsTab = this.moduleList[contactsModule] ? true : false;
        this.shouldShowLeadsTab = this.moduleList[leadsModule] ? true : false;
        this.modulesMetaData = [{
            module: 'accounts',
            'basicFields': app.hint.getAccountBasicPanelFields(),
            'expandedFields': app.hint.getAccountExpandedPanelFields()
        }, {
            module: 'contacts',
            'basicFields': app.hint.getPeopleBasicPanelFields('Contacts'),
            'expandedFields': app.hint.getPeopleExpandedPanelFields('Contacts')
        }, {
            module: 'leads',
            'basicFields': app.hint.getPeopleBasicPanelFields('Leads'),
            'expandedFields': app.hint.getPeopleExpandedPanelFields('Leads')
        }];

        app.events.on('hint:config:fieldRemoved', function(fieldRemoved) {
            _.each(self.modulesMetaData, function(moduleMetaData) {

                if (moduleMetaData.module.toLowerCase() == self.currentModule.toLowerCase()) {

                    moduleMetaData.basicFields =
                        _.without(moduleMetaData.basicFields, _.findWhere(moduleMetaData.basicFields, {
                            name: fieldRemoved.name
                        }));

                    moduleMetaData.expandedFields =
                        _.without(moduleMetaData.expandedFields, _.findWhere(moduleMetaData.expandedFields, {
                            name: fieldRemoved.name
                        }));
                }
            });
            this.render();
        }, this);

        app.events.on('hint:config:fieldAdded', function(fieldAdded) {
            var self = this;
            _.each(this.modulesMetaData, function(metadata) {
                if (metadata.module == self.currentModule.toLowerCase()) {

                    var existsInBasicPanel = _.find(metadata.basicFields, function(field) {
                        return field == fieldAdded;
                    });

                    var existsInExtendedPanel = _.find(metadata.expandedFields, function(field) {
                        return field == fieldAdded;
                    });

                    if (!existsInBasicPanel && !existsInExtendedPanel) {
                        metadata.expandedFields.push(fieldAdded);
                    }

                }
            });
            this.render();
        }, this);
    },

    /**
     * Restore default config
     *
     * @param {Object} event
     */
    restoreDefaults: function(event) {
        var self = this;
        event.preventDefault();

        app.alert.show('message-id', {
            level: 'confirmation',
            messages: app.lang.get('LBL_HINT_CONFIG_WARNING_MESSAGE', self.currentModule, {
                module: self.currentModule
            }),
            autoClose: false,
            onConfirm: function() {
                self._restoreModulesMetaData();
                self.render();
                app.events.trigger('hint:config:defaults:restored', {
                    module: self.currentModule, metadata: self._getMetadataForModule(self.currentModule)
                });
            },
            onCancel: function() {
                $.noop();
            }
        });
    },

    /**
     * Restore modules metadata
     */
    _restoreModulesMetaData: function() {
        var basicPanelAccounts;
        var basicPanelContacts;
        var basicPanelLeads;
        var expandedPanelAccounts;
        var expandedPanelContacts;
        var expandedPanelLeads;

        _.each(this.modulesMetaData, function(moduleMetaData) {
            switch (moduleMetaData.module.toLowerCase()) {
                case 'accounts':
                    basicPanelAccounts = moduleMetaData.basicFields;
                    expandedPanelAccounts = moduleMetaData.expandedFields;
                    break;
                case 'contacts':
                    basicPanelContacts = moduleMetaData.basicFields;
                    expandedPanelContacts = moduleMetaData.expandedFields;
                    break;
                case 'leads':
                    basicPanelLeads = moduleMetaData.basicFields;
                    expandedPanelLeads = moduleMetaData.expandedFields;
                    break;
            }
        }, this);

        this.modulesMetaData = [
            {
                module: 'accounts',
                'basicFields': (this.currentModule === 'Accounts') ?
                    app.hint.getAccountDefaultBasicPanelFields() : basicPanelAccounts,
                'expandedFields': (this.currentModule === 'Accounts') ?
                    app.hint.getAccountDefaultExpandedPanelFields() : expandedPanelAccounts
            }, {
                module: 'contacts',
                'basicFields': (this.currentModule === 'Contacts') ?
                    app.hint.getPeopleDefaultBasicPanelFields('Contacts') : basicPanelContacts,
                'expandedFields': (this.currentModule === 'Contacts') ?
                    app.hint.getPeopleDefaultExpandedPanelFields('Contacts') : expandedPanelContacts
            }, {
                module: 'leads',
                'basicFields': (this.currentModule === 'Leads') ?
                    app.hint.getPeopleDefaultBasicPanelFields('Leads') : basicPanelLeads,
                'expandedFields': (this.currentModule === 'Leads') ?
                    app.hint.getPeopleDefaultExpandedPanelFields('Leads') : expandedPanelLeads
            }
        ];
    },

    /**
     * Get metadata for saving
     *
     * @param {string} module
     * @return {Object}
     */
    _getMetadataForSaving: function(module) {
        var basicPanelFields = [];
        var expandedPanelFields = [];

        //Get all the fields from the dom and their order.
        $('div#' + module.toLowerCase() + '-basic-panel').find('div.configurable > div.row-fluid').each(
            function(index) {
                basicPanelFields.push($(this).data('name'));
            });

        $('div#' + module.toLowerCase() + '-expanded-panel').find('div.configurable > div.row-fluid').each(
            function(index) {
                expandedPanelFields.push($(this).data('name'));
            });

        return {'basic': basicPanelFields, 'expanded': expandedPanelFields};
    },

    /**
     * @param currentModule
     * @private
     */
    _hidePanelsForOtherModules: function(currentModule) {
        var lowerCaseModule = '#' + currentModule.toLowerCase();
        var basicPanelToShow = lowerCaseModule + '-basic-panel';
        var expandedPanelToShow = lowerCaseModule + '-expanded-panel';
        var directionsPanelToShow = lowerCaseModule + '-directions';

        $('.hint-config-container').hide();
        $('.directions-container').hide();

        $(basicPanelToShow).show();
        $(expandedPanelToShow).show();
        $(directionsPanelToShow).show();
        if (this.currentModule !== currentModule) {
            this.currentModule = currentModule;
            this._render();
        }
    },

    /**
     * Given our metadata and an array of sorted fields, return a copy of the metadata sorted with the same order
     * as the ordered fields.  O(n) time complexity.
     *
     * @private
     */
    _sortMetadataFields: function(metadataFields, sortedFields) {
        if (!sortedFields) {
            return metadata;
        }

        //Restructure to a hashmap for easy sorting
        var metadataFieldsByKey = {};
        for (var z = 0; z < metadataFields.length; z++) {
            var key = metadataFields[z].name;
            metadataFieldsByKey[key] = metadataFields[z];
        }

        var results = [];
        for (var x = 0; x < sortedFields.length; x++) {
            var currentField = sortedFields[x];

            if (typeof (metadataFieldsByKey[currentField]) !== 'undefined') {
                results.push(metadataFieldsByKey[currentField]);
            }
        }

        return results;
    },
    /**
     * Event handler for the sortstop "drop" event.  We need to resort the backing data
     * with what the dom is showing otherwise when we switch tabs or call any other render functionality
     * we'll loose the state.
     *
     * @param {jQuery.Event} evt The jQuery sortstop event
     * @param {Object} ui The jQuery Sortable UI Object
     * @private
     */
    _onDragStop: function(evt, ui) {
        var self = this;
        var sortedMetadata = self._getMetadataForSaving(this.currentModule);

        _.each(self.modulesMetaData, function(moduleMetaData) {

            if (moduleMetaData.module.toLowerCase() == self.currentModule.toLowerCase()) {
                var allMetadataFields = moduleMetaData.basicFields.concat(moduleMetaData.expandedFields);
                moduleMetaData.basicFields = self._sortMetadataFields(allMetadataFields, sortedMetadata.basic);
                moduleMetaData.expandedFields = self._sortMetadataFields(allMetadataFields, sortedMetadata.expanded);
            }
        });

    },

    /**
     * Get metadata for module
     *
     * @param {string} module
     * @private
     */
    _getMetadataForModule: function(module) {

        var self = this;
        return _.find(this.modulesMetaData, function(moduleMetaData) {

            return moduleMetaData.module.toLowerCase() == module.toLowerCase();
        });
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        var self = this;
        this._super('_render');

        self._hidePanelsForOtherModules(this.currentModule);

        //This will ensure that our panels are drag and drop enabled.
        $('.panel.configurable').sortable({
            items: 'div.row-fluid',
            // allow drag to only go in Y axis direction
            axis: 'y',
            // make the "helper" row (the row the user actually drags around) a clone of the original row
            helper: 'clone',
            // adds a slow animation when "dropping" a group, removing this causes the row
            // to immediately snap into place wherever it's sorted
            revert: 150,
            // the CSS class to apply to the placeholder underneath the helper clone the user is dragging
            placeholder: 'ui-state-highlight',
            // the cursor to use when dragging
            cursor: 'move',
            //All items to move to a different panel
            connectWith: '.panel.configurable',
            // handler for when dragging stops; the "drop" event
            stop: _.bind(this._onDragStop, this),
        });

        //Listen to tab changes for the tab events (module changed)
        $('li[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            var target = $(e.target).attr('data-bs-target'); // activated tab
            var module = target.substring(1).charAt(0).toUpperCase() + target.substring(2);

            self._hidePanelsForOtherModules(module);
            self.currentModule = module;
            app.events.trigger('hint:config:module:changed', {
                module: module, metadata: self._getMetadataForModule(module)
            });
        });
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        app.events.off('hint:config:fieldRemoved');
        app.events.off('hint:config:fieldAdded');
        this._super('_dispose');
    }
});
