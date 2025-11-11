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

    var hint = (function() {

        var getPanelDefinitionFieldsByName;

        setDefaultValuesForFields = function(field, module) {
            switch (field.name) {
                case 'hint_account_website':
                    return 'www.example.net';
                case 'account_name':
                    return 'Example Corp';
                case 'full_name':
                    return 'Jon Doe';
                case 'title':
                    return 'President';
                default:
                    return field.label;
            }
        };

        /**
         * For a specific view, return a single 'panel' within the metadata
         *
         * @param module
         * @param panelName
         * @return {Array}
         */
        getPanelDefinitionFieldsByName = function(module, panelName) {
            var fields = [];
            var viewName = module.toLowerCase() === 'accounts' ? 'stage2-account-preview' : 'stage2-preview';
            var refPanels = app.metadata.get(module).views[viewName].meta.panels;
            //Deep copy the results so we're no longer passing by reference.
            var panels = JSON.parse(JSON.stringify(refPanels));

            _.each(panels, function(panel) {

                if (panel.name !== panelName) {
                    return;
                }

                _.each(panel.fields, function(field) {
                    field.label = app.lang.get(field.label, module);
                    field.value = setDefaultValuesForFields(field, module);
                    if (field.label) {
                        fields.push(field);
                    }
                });

            });

            return fields;
        };

        /**
         * Module reveal pattern, public accessors below.
         */
        return {
            /**
             * Retrieve all of the fields in any panel within our stage2 preview metadata
             *
             * @param {string} module
             * @return {Object}
             */
            getAllFieldsForView: function(module) {
                var fields = {};

                var allFields = this.getModuleFieldsAvailableForSelection(module);

                _.each(allFields, function(field) {
                    field.value = setDefaultValuesForFields(field, module);
                    fields[field.name] = field;
                });

                return fields;
            },
            /**
             * Returns all fields in the record metadata for a particular module
             *
             * @param {string} module
             * @return {Array}
             */
            getModuleFieldsAvailableForSelection: function(module) {
                var fields = [];

                var refPanels = App.metadata.getModule(module).views.record.meta.panels;

                //Deep copy the results so we're no longer passing by reference.
                var panels = JSON.parse(JSON.stringify(refPanels));

                _.each(panels, function(panel) {
                    _.each(panel.fields, function(field) {
                        if (typeof (field.readonly) !== 'undefined' && field.readonly) {
                            return;
                        }

                        if (!field.label || field.label === '') {
                            console.log('Missing field for label: ' + field.name);
                            return;
                        }

                        field.label = app.lang.get(field.label, module);

                        fields.push(field);
                    });
                });

                //Merge in all hint fields as well
                if (module == 'Accounts') {
                    var allDefaultFields = this.getAccountDefaultBasicPanelFields().concat(
                        this.getAccountDefaultExpandedPanelFields());
                } else {
                    var allDefaultFields = this.getPeopleDefaultBasicPanelFields(module).concat(
                        this.getPeopleDefaultExpandedPanelFields(module));
                }

                var allFieldsWithDups = _.union(fields, allDefaultFields);
                fields = _.uniq(allFieldsWithDups, false, function(item) { return item.name; });

                //Sort alphabetically
                fields = _.sortBy(fields, function(f) { return f.label; });

                return fields;
            },

            /**
             * Get panel fields from configuration
             *
             * @param {string} module
             * @param {string} panelType
             * @param {Array} defaultPanel
             * @return {Array}
             */
            getPanelFieldsFromConfiguration: function(module, panelType, defaultPanel, otherPanel) {
                var metdata = app.modules.metadata.getModule(module);

                if (metdata && metdata.config && typeof (metdata.config.hintConfig) !== 'undefined' &&
                    typeof (metdata.config.hintConfig[panelType]) !== 'undefined') {

                    var panelFields = [];
                    var fieldDefinitions = this.getAllFieldsForView(module);
                    var savedFields = metdata.config.hintConfig[panelType];

                    for (var $x = 0; $x < savedFields.length; $x++) {

                        var fieldName = savedFields[$x];

                        //If the field definition is available in our 'default' panels we prefer to pull those in
                        //as definitions from the record metadata may vary slightly (eg. have additional data points)
                        var existsInBasicPanel = _.find(defaultPanel, function(field) {
                            return field.name == fieldName;
                        });

                        if (existsInBasicPanel) {
                            panelFields.push(existsInBasicPanel);
                            continue;
                        }

                        var existsInOtherPanel = _.find(otherPanel, function(field) {
                            return field.name == fieldName;
                        });

                        if (existsInOtherPanel) {
                            panelFields.push(existsInOtherPanel);
                            continue;
                        }

                        if (fieldDefinitions[fieldName]) {

                            panelFields.push(fieldDefinitions[fieldName]);
                        }
                    }

                    //We may need to copy default metadata entries over from the default pannel as
                    //they can be 'lost' otherwise.
                    if (module == 'Accounts') {
                        var allDefaultFields = this.getAccountDefaultBasicPanelFields().concat(
                            this.getAccountDefaultExpandedPanelFields());

                        //Lets re-organize so we can access by kield name
                        var allDefaultFieldsByKey = {};
                        _.each(allDefaultFields, function(field) {
                            allDefaultFieldsByKey[field.name] = field;
                        });

                        //These are the attributes that should be copied over
                        var attributesToCopy = ['person_name', 'person_label'];
                        _.each(panelFields, function(field) {

                            var fieldDefaultPanelDefinition = allDefaultFieldsByKey[field.name];
                            if (fieldDefaultPanelDefinition) {
                                for (var x = 0; x < attributesToCopy.length; x++) {
                                    var key = attributesToCopy[x];
                                    if (typeof (fieldDefaultPanelDefinition[key]) !== 'undefined') {
                                        field[key] = fieldDefaultPanelDefinition[key];
                                    }
                                }
                            }
                        });

                    }

                    // The following guard is made for users of Hint version less than 5.1.
                    // Name and website are duplicates from the main panel, but previous to 5.1
                    // they could be enabled on the expanded panel through Layout configuration.
                    if (panelType === 'expanded') {
                        var excludedFields = ['name', 'website'];
                        panelFields = _.reduce(excludedFields, function(fields, fieldName) {
                            return _.without(fields, _.findWhere(fields, {name: fieldName}));
                        }, panelFields);
                    }

                    return panelFields;
                } else {
                    return defaultPanel;
                }
            },

            /**
             * Get field definition from default panel
             *
             * @param {string} module
             * @param {string} fieldName
             * @return {boolean}
             */
            getFieldDefinitionFromDefaultPanel: function(module, fieldName) {
                var refAccountMetadata = app.metadata.getView(null, 'stage2-account-preview').panels;
                var refPeopleMetadata = app.metadata.getView(null, 'stage2-preview').panels;

                //We need to deep clone the metadata itself otherwise any modifications will be reflected in other areas
                //of the app (which we don't want)
                var accountMetadata = JSON.parse(JSON.stringify(refAccountMetadata));
                var peopleMetadata = JSON.parse(JSON.stringify(refPeopleMetadata));

                var metadataToParse = module == 'Accounts' ? accountMetadata : peopleMetadata;

                var result = false;
                _.every(metadataToParse, function(panel) {

                    result = _.find(panel.fields, function(field) {
                        return field.name && field.name == fieldName;
                    });

                    return !result;
                });

                return result;
            },

            /**
             * Get panel metadata
             *
             * @param module
             * @return {Object}
             */
            getPanelMetadata: function(module) {
                var originalModule = SUGAR.App.sideDrawer && SUGAR.App.sideDrawer.isOpen() ?
                    SUGAR.App.sideDrawer._components[0].module :
                    SUGAR.App.controller.layout.model.module;
                var isPersonOrientedModule = !_.includes(['Accounts', 'Administration'], originalModule);

                var panelToMapping = {
                    'company_info': this.getAccountBasicPanelFields(),
                    'company_extended': this.getAccountExpandedPanelFields(),
                    'contacts_basic': this.getPeopleBasicPanelFields(module),
                    'contacts_extended': this.getPeopleExpandedPanelFields(module)
                };
                var refAccountMetadata = app.metadata.getView(null, 'stage2-account-preview').panels;
                var refPeopleMetadata = app.metadata.getView(null, 'stage2-preview').panels;

                //We need to deep clone the metadata itself otherwise any modifications will be reflected in other areas
                //of the app (which we don't want)
                var accountMetadata = JSON.parse(JSON.stringify(refAccountMetadata));
                var peopleMetadata = JSON.parse(JSON.stringify(refPeopleMetadata));

                var metdataToParse = module == 'Accounts' ? accountMetadata : peopleMetadata;

                //Replace the default panel fields with whats saved in the configuration if applicable.
                for (var x = 0; x < metdataToParse.length; x++) {

                    var tmpPanel = metdataToParse[x];
                    if (panelToMapping[tmpPanel.name]) {
                        tmpPanel.fields = panelToMapping[tmpPanel.name];

                        //Fix phone field metadata, we always want to add the custom phone fields if we find phone_work
                        //for the people modules.
                        if (module == 'Contacts' || module == 'Leads') {

                            var phoneWorkFieldIndex = _.findIndex(tmpPanel.fields, function(f) {
                                return f.name == 'phone_work';
                            });

                            var educationIndex = _.findIndex(tmpPanel.fields, function(f) {
                                return f.name == 'hint_education';
                            });

                            var secondEducationIndex = _.findIndex(tmpPanel.fields, function(f) {
                                return f.name == 'hint_education_2';
                            });

                            if (educationIndex >= 0 && secondEducationIndex < 0) {
                                tmpPanel.fields.splice(++educationIndex, 0,
                                    this.getFieldDefinitionFromDefaultPanel(module, 'hint_education_2'));
                            }

                            //Check if we need to remap any of the account fields
                            if (tmpPanel.name == 'company_extended' || tmpPanel.name == 'company_info') {

                                _.each(tmpPanel.fields, function(field) {

                                    if (typeof (field.person_name) !== 'undefined') {
                                        field.name = field.person_name;
                                    }

                                    if (field.name === 'annual_revenue' && isPersonOrientedModule) {
                                        field.name = 'hint_account_annual_revenue';
                                    }

                                    if (typeof (field.person_label) !== 'undefined') {
                                        field.label = field.person_label;
                                    }
                                });
                            }
                        }
                        _.each(tmpPanel.fields, function(field) {
                            if (field.name === 'sic_code' && originalModule === 'Accounts') {
                                field.name = 'hint_account_sic_code_label';
                            }
                        });
                    }
                }

                return metdataToParse;
            },

            /**
             * Get people default basic panel fields
             *
             * @param {string} module
             * @return {Array}
             */
            getPeopleDefaultBasicPanelFields: function(module) {
                var fields = getPanelDefinitionFieldsByName(module, 'contacts_basic');
                return this.filterPersonPanelFields(fields);

            },

            /**
             * Get people default expanded panel fields
             *
             * @param {string} module
             * @return {Array}
             */
            getPeopleDefaultExpandedPanelFields: function(module) {
                var fields = getPanelDefinitionFieldsByName(module, 'contacts_extended');
                return this.filterPersonPanelFields(fields);
            },

            /**
             * Get account default basic panel fields
             *
             * @return {Array}
             */
            getAccountDefaultBasicPanelFields: function() {
                return getPanelDefinitionFieldsByName('accounts', 'company_info');

            },

            /**
             * Get account default expanded panel fields
             *
             * @return {Array}
             */
            getAccountDefaultExpandedPanelFields: function() {
                return getPanelDefinitionFieldsByName('accounts', 'company_extended');
            },

            /**
             * Get account basic panel fields
             *
             * @return {Array}
             */
            getAccountBasicPanelFields: function() {
                var module = 'Accounts';
                var panelType = 'basic';
                var defaultPanel = this.getAccountDefaultBasicPanelFields();
                var otherPanel = this.getAccountDefaultExpandedPanelFields();

                return this.getPanelFieldsFromConfiguration(module, panelType, defaultPanel, otherPanel);
            },

            /**
             * Get account expanded panel fields
             *
             * @return {Array}
             */
            getAccountExpandedPanelFields: function() {
                var module = 'Accounts';
                var panelType = 'expanded';
                var defaultPanel = this.getAccountDefaultExpandedPanelFields();
                var otherPanel = this.getAccountDefaultBasicPanelFields();

                return this.getPanelFieldsFromConfiguration(module, panelType, defaultPanel, otherPanel);
            },

            /**
             * Get people expanded panel fields
             *
             * @param {string} module
             * @return {Array}
             */
            getPeopleExpandedPanelFields: function(module) {
                var panelType = 'expanded';
                var defaultPanel = this.getPeopleDefaultExpandedPanelFields(module);
                var otherPanel = this.getPeopleDefaultBasicPanelFields(module);

                var fields = this.getPanelFieldsFromConfiguration(module, panelType, defaultPanel, otherPanel);

                return this.filterPersonPanelFields(fields);
            },

            /**
             * Get people basic panel fields
             *
             * @param {string} module
             * @return {Array}
             */
            getPeopleBasicPanelFields: function(module) {
                var panelType = 'basic';
                var defaultPanel = this.getPeopleDefaultBasicPanelFields(module);
                var otherPanel = this.getPeopleDefaultExpandedPanelFields(module);

                var fields = this.getPanelFieldsFromConfiguration(module, panelType, defaultPanel, otherPanel);

                return this.filterPersonPanelFields(fields);
            },

            /**
             * Filter person panel fields
             *
             * @param {Array} fields
             * @return {Array}
             */
            filterPersonPanelFields: function(fields) {
                //Always filter out phone fields, they are a special case.
                fields = _.without(fields, _.findWhere(fields, {name: 'hint_phone_1'}));
                fields = _.without(fields, _.findWhere(fields, {name: 'hint_phone_2'}));
                return _.without(fields, _.findWhere(fields, {name: 'hint_education_2'}));
            },

            /**
             * Get visible fields from all pannels for default selection
             *
             * @param {string} module
             * @return {Array}
             */
            getVisibleFieldsFromAllPannelsForDefaultSelection: function(module) {
                var basicFields = [];
                var extendedFields = [];

                switch (module.toLowerCase()) {

                    case 'accounts':
                        basicFields = app.hint.getAccountBasicPanelFields();
                        extendedFields = app.hint.getAccountExpandedPanelFields();
                        break;
                    default:
                        basicFields = app.hint.getPeopleBasicPanelFields(module);
                        extendedFields = app.hint.getPeopleExpandedPanelFields(module);
                }

                return basicFields.concat(extendedFields);
            },

            /**
             * Get basic view default fields
             *
             * @param {string} module
             * @return {Array}
             */
            getBasicViewDefaultFields: function(module) {
                var panelMapping = {
                    'Accounts': {view: 'stage2-account-preview',
                        panelFields: ['company_extended', 'company_info', 'company_header']},
                    'Contacts': {view: 'stage2-preview',
                        panelFields: ['panel_header', 'contacts_basic', 'contacts_extended']},
                    'Leads': {view: 'stage2-preview',
                        panelFields: ['panel_header', 'contacts_basic', 'contacts_extended']},
                };

                var fields = [];

                var panelName = panelMapping[module].view;
                var refPanels = app.metadata.get(module).views[panelName].meta.panels;
                //Deep clone our metadata
                var panels = JSON.parse(JSON.stringify(refPanels));

                if (panelName && panels) {
                    _.each(panels, function(panel) {

                        if (_.contains(panelMapping[module].panelFields, panel.name)) {
                            fields = fields.concat(panel.fields);
                        }

                    });
                }

                return fields;
            },

            /**
             * Get panels for hint enrich fields
             *
             * @param {string} module
             * @return {Array}
             */
            getPanelsForHintEnrichFields: function(module) {
                var panelMapping = {
                    'Accounts': {view: 'stage2-account-preview',
                        panelFields: ['company_extended', 'company_info', 'company_header']},
                    'Contacts': {view: 'stage2-preview',
                        panelFields: ['panel_header', 'contacts_basic', 'contacts_extended']},
                    'Leads': {view: 'stage2-preview',
                        panelFields: ['panel_header', 'contacts_basic', 'contacts_extended']},
                };
                var fields = [];
                var results = [];
                var allfields = this.getModuleFieldsAvailableForSelection(module);
                var panelName = panelMapping[module].view;
                var refPanels = app.metadata.get(module).views[panelName].meta.panels;
                //Deep clone our metadata
                var panels = JSON.parse(JSON.stringify(refPanels));
                _.each(panels, function(panel) {
                    if (panel.fields) {
                        fields = fields.concat(panel.fields);
                    }
                });
                for (let i = 0; i < allfields.length; i++) {
                    if (allfields[i].name === 'picture') {
                        results.push(allfields[i]);
                    }
                    for (var j = 0; j < fields.length; j++) {
                        if (allfields[i].name === fields[j].name) {
                            results.push(allfields[i]);
                        }
                    }
                }
                return results;
            },

            /**
             * Verson compare
             *
             * @param {string} validSugarVersion
             * @param {bool} shouldDisplayLicenseErrMessage
             * @param {string} sugarVersion
             * @return {int}
             */
            versionCompare: function(validSugarVersion, shouldDisplayLicenseErrMessage, sugarVersion) {
                validSugarVersion = validSugarVersion || '9.1.0';
                sugarVersion = sugarVersion || app.metadata.getServerInfo().version;
                var isCreateDrawerOpen = app.drawer;
                // shouldDisplayLicenseErrMessage: {params} is used to bypass the createDrawer component check.
                // It's used to display error message when the versionCompare() is called when createDrawer is open.
                if (!shouldDisplayLicenseErrMessage && isCreateDrawerOpen &&
                    !_.isEmpty(isCreateDrawerOpen._events.render[0].ctx._components)) {
                    return -1;
                }
                if (validSugarVersion === sugarVersion) {
                    return 0;
                }
                if (sugarVersion) {
                    var versionLimit = validSugarVersion.split('.');
                    var currentSugarVerion = sugarVersion.split('.');
                    var len = Math.min(versionLimit.length, currentSugarVerion.length);

                    for (var i = 0; i < len; i++) {
                        if (parseInt(versionLimit[i]) > parseInt(currentSugarVerion[i])) {
                            return -1;
                        }

                        if (parseInt(versionLimit[i]) < parseInt(currentSugarVerion[i])) {
                            return 1;
                        }
                    }

                    if (versionLimit.length > currentSugarVerion.length) {
                        return -1;
                    }
                    if (versionLimit.length < currentSugarVerion.length) {
                        return 1;
                    }

                    return 1;
                }
                return -1;
            },

            /**
             * Should use old hint preview
             *
             * @param {string} modelName
             * @return {bool}
             */
            shouldUseOldHintPreview: function(modelName) {
                var layout = SUGAR.App.sideDrawer && SUGAR.App.sideDrawer.isOpen() ?
                    SUGAR.App.sideDrawer._components[0] :
                    SUGAR.App.controller.layout;
                if (layout && modelName) {
                    var isRecordViewlayoutType = layout.type === 'record';
                    var mainLayoutModuleName = layout.model.module;
                    var modelName = modelName;
                    if (!isRecordViewlayoutType) {
                        return true;
                    }
                    return isRecordViewlayoutType && (modelName !== mainLayoutModuleName);
                }
            },

            /**
             * Is sugar pro special case
             *
             * @return {bool}
             */
            isSugarProSpecialCase: function() {
                var sugarFlavor = SUGAR.App.metadata.getServerInfo().flavor;
                return this.versionCompare('10.3.0', true) >= 0 && sugarFlavor === 'PRO';
            },

            /**
             * Deep freeze
             *
             * As a security from the browser, so that no-one tries to penetrate the license objects.
             * deepFreeze function locks the object's attributes recursively basically making it a constant
             * to avoid any further changes to the object
             *
             * @param {Object} objToFreeze
             * @return {Object}
             */
            deepFreeze: function(objToFreeze) {
                var self = this;
                Object.keys(objToFreeze).forEach(function(prop) {
                    if (typeof objToFreeze[prop] === 'object' && !Object.isFrozen(objToFreeze[prop])) {
                        self.deepFreeze(objToFreeze[prop]);
                    }
                });
                return Object.freeze(objToFreeze);
            },

            /**
             * Check is hint user
             *
             * @return {bool}
             */
            isHintUser: function() {
                return app.user.hasLicense('HINT');
            },

            /**
             * Check is list view
             *
             * @return {bool}
             */
            isListView: function() {
                var layout = app.controller.layout;
                var isCreateDrawerActive = app.drawer.isActive();
                if ((layout.type === 'search') && isCreateDrawerActive && (this.versionCompare() < 0)) {
                    return true;
                }
                return false;
            },

            /**
             * Check is dark mode
             *
             * @return {bool}
             */
            isDarkMode: function() {
                if (app.utils.isDarkMode()) {
                    return app.utils.isDarkMode();
                }
                return false;
            },

            /**
             * Checks if the model is enriched by hint.
             *
             * @param {string} module name.
             * @return {boolean} True if it's enriched.
             */
            isEnrichedModel: function(module) {
                var enrichedModules = ['Leads', 'Contacts', 'Accounts'];
                return _.contains(enrichedModules, module);
            },
        };
    })();

    app.hint = hint;

})(SUGAR.App);
