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
    plugins: ['Stage2CssLoader'],

    /**
     * @inheritdoc
     */
    initialize: function(view) {
        this._super('initialize', arguments);
        var self = this;
        this.currentModule = 'Accounts';
        this.enrichHintFields = this.getAllEnrichFieldsForHint(this.currentModule);
        this.availableFields = this.getAllAvailableFields(this.currentModule);
        this.configId = app.user.get('configId');
        this.configKey = 'hintConfig';
        this.bucketList = (app.user.get('fieldConfigCache') &&
            JSON.parse(app.user.get('fieldConfigCache'))) || {};
        self._subscriptionType = '';
        this._saveConfig = this._saveConfig.bind(this);
        this._lastUpdatedCache = app.user.get('fieldConfigCacheLastUpdated');
        // Field config cache should expire after 5 minutes
        this._maxLifetime = 5 * 60 * 1000;

        // check if cache has not been updated or has expired (need to hit backend)
        // if so, then we must retrieve config from the backend and update the cache
        // along with rendering the UI
        if (!this._lastUpdatedCache || Date.now() - this._lastUpdatedCache >= this._maxLifetime) {
            this._callStage2API('read', 'hint/enrich/config', {
                success: function(data) {
                    var res = data.response;
                    var config = res.config_data;
                    self.bucketList = JSON.parse(config);
                    app.user.set('fieldConfigCache', config);
                    self.configId = res.id;
                    app.user.set('configId', self.configId);
                    app.user.set('fieldConfigCacheLastUpdated', Date.now());
                    self.render();
                },
                error: function(err) {
                    app.logger.error('Failed to reach hint/enrich/config' + JSON.stringify(err));
                }
            });
        }

        this.contextModel = self.context.get('model');
        this.context = self.context;
        this.module = self.module;
        this.tokenExpirationTimeOut = 60 * 60 * 1000;
        // Determines which fields should be 'checked' by default
        this.defaultPanelFields = app.hint.getVisibleFieldsFromAllPannelsForDefaultSelection(self.currentModule);

        app.events.on('hint:config:module:changed', function(data) {
            self.currentModule = data.module;
            self.enrichHintFields = self.getAllEnrichFieldsForHint(data.module);
            self.availableFields = self.getAllAvailableFields(data.module);
            self.defaultPanelFields = data.metadata.basicFields.concat(data.metadata.expandedFields);
            self.render();
        });

        app.events.on('hint:config:save', this._saveConfig);

        app.events.on('hint:config:defaults:restored', function(data) {
            self.enrichHintFields = self.getAllEnrichFieldsForHint(data.module);
            self.availableFields = self.getAllAvailableFields(data.module);
            self.defaultPanelFields = data.metadata.basicFields.concat(data.metadata.expandedFields);
            self.resetEnrichFieldsView(data.module);
            self.render();
        });
    },

    events: {
        'keyup .searchbox-field': 'onSearchFilterChanged',
        'click .fieldSelector': 'fieldToggled',
        'click .fieldSelectorForEnrich': 'addEnrichArray'
    },

    /**
     * Check the expiration time token
     *
     * @param {number} dataEnrichmentAccessTokenExpiration
     * @return {number}
     */
    isTokenExpired: function(dataEnrichmentAccessTokenExpiration) {
        return (!dataEnrichmentAccessTokenExpiration || dataEnrichmentAccessTokenExpiration < Date.now());
    },

    /**
     * Get metadata for saving
     * Teleported the function from hint-drawer-view.js to make it hard synchronous as there will be should be
     * one hint:config:save event.
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
     * Save config
     * Teleported the function from hint-drawer-view.js to make it hard synchronous as there will be should be
     * one hint:config:save event.
     */
    _saveConfig: function() {
        var self = this;

        self._withValidAuthenticationToken(function() {
            if (typeof ($('.hint-config-save').attr('disabled')) !== 'undefined') {
                return;
            }

            $('.hint-config-save').attr('disabled', 'disabled');

            if (!_.isEmpty(self.bucketList)) {
                var config = self.bucketList;
                app.user.set('fieldConfigCache', config);
                app.user.set('fieldConfigCacheLastUpdated', Date.now());

                self._callStage2API('update', 'hint/enrich/config', {
                    'id': self.configId,
                    'config_data': config
                });
            }

            var modulesToSave = ['Accounts', 'Contacts', 'Leads'];

            var metadataToSave = {};
            _.each(modulesToSave, function(module) {

                metadataToSave[module] = self._getMetadataForSaving(module);

            });

            self._updateHintConfigBackEnd(modulesToSave, metadataToSave);

            return;
        });
    },

    /**
     * Update hint config
     *
     * @param {Array} modulesToSave
     * @param {Object} metadataToSave
     */
    _updateHintConfigBackEnd: function(modulesToSave, metadataToSave) {
        var self = this;

        /**
         * We need to save each configuration in a serial call so we don't blow up the metadata hash.
         * This is a platform limitation that
         * we must work around.
         */
        async.forEachSeries(modulesToSave, function(module, callback) {
                var url = app.api.buildURL(module, 'config');
                var attributes = {};
                attributes[self.configKey] = metadataToSave[module];

                // In order to save the different modules in a row this is necessary,
                // otherwise we'll get an error about the metadata being out of sync.
                var apiOptions = {
                    skipMetadataHash: true
                };

                app.api.call('create', url, attributes, {
                        success: _.bind(function() {
                            callback();
                        }, this),
                        error: _.bind(function(err) {
                            callback(err);
                        }, this)
                    }, apiOptions
                );
            },
            function(err) {
                if (err) {
                    app.logger.error('Error saving configuration');
                } else {
                    if (app.drawer.count()) {
                        // close the drawer and return to the admin page
                        app.drawer.close(self.context, self.contextModel);

                        var backUrl = app.api.buildURL(self.module);
                        app.router.navigate(self.module, {trigger: true});

                        //We always require a hash sync and hard refresh
                        app.sync();
                        app.utils.hardRefresh();

                    } else {
                        app.router.navigate(self.module, {trigger: true});
                    }
                }
            }
        );
    },

    /**
     * Stage2 data enrichment config
     *
     * @param {string} method
     * @param {string} api
     * @param {Object} options
     */
    _callStage2API: function(method, api, options) {
        var self = this;

        // In order to save the different modules in a row this is necessary,
        // otherwise we'll get an error about the metadata being out of sync.
        var apiOptions = {
            skipMetadataHash: true
        };

        app.api.call(method, app.api.buildURL(api), options, {
            success: function(data) {
                if (api === 'hint/enrich/config' && method === 'update') {
                    self._callToSendEnrichFields('create', app.user.get('dataEnrichmentAccessToken'));
                }
                return options && options.success && options.success(data);
            },
            error: function(err) {
                app.logger.error('Failed to reach' + api + JSON.stringify(err));
            }
        }, apiOptions);
    },

    /**
     * Call to send enrich fields
     *
     * @param {string} method
     * @param {string} dataEnrichmentAccessToken
     */
    _callToSendEnrichFields: function(method, dataEnrichmentAccessToken) {
        var self = this;
        var username = null;
        if (app.user && app.user.attributes) {
            username = app.user.attributes.user_name;
        }
        var instanceId = app.config.uniqueKey;

        /* This is to make sure if the user has clicked ::save before _render();
           then accessToken & enrichField Options are not available and config remains same at v2
           since w/o render  user made no change. Thus, save the call to v2.
         */
        if (!_.isEmpty(dataEnrichmentAccessToken)) {
            var url = app.api.buildURL('hint/data/enrich/fields');
            var attributes = {
                'data': self.bucketList,
                'username': username,
                'instanceId': instanceId,
                'subscriptionType': self.subscriptionType,
                'moduleName': 'Admin' //REMIND: will be updated when the endpoint is completely ready.
            };

            var apiOptions = {
                skipMetadataHash: true
            };

            app.api.call(method, url, attributes, {
                success: function(data) {
                },
                error: function(err) {
                    app.logger.error('Failed to reach hint/data/enrich/fields' + JSON.stringify(err));
                }
            }, apiOptions);
        }
        return;
    },

    /**
     * Call with a valid authentication token
     *
     * @param callback
     */
    _withValidAuthenticationToken: function(callback) {
        var self = this;
        if (self.isTokenExpired(app.user.get('dataEnrichmentAccessTokenExpiration'))) {
            self._callStage2API('create', 'stage2/token', {
                success: function(data) {
                    app.user.set({
                        'dataEnrichmentAccessToken': data.accessToken,
                        'dataEnrichmentAccessTokenExpiration': Date.now() + self.tokenExpirationTimeOut
                    });
                    self._subscriptionType = data.subscriptionType;

                    if (!app.user.get('dataEnrichmentUrl')) {
                        self._callStage2API('read', 'stage2/params', {
                            success: function(data) {
                                app.user.set('dataEnrichmentUrl', data.enrichmentServiceUrl);
                                callback();
                            },
                            error: function(err) {
                                app.logger.error('Failed to reach stage2/params' + JSON.stringify(err));
                            }
                        });
                    } else {
                        callback();
                    }
                },
                error: function(err) {
                    app.logger.error('Failed to reach stage2/token' + JSON.stringify(err));
                }
            });
        } else {
            callback();
        }
    },

    /**
     * The following guard is made for users of Hint version less than 5.1.
     * Name and website are duplicates from the main panel, but below 5.1
     * they could be enabled on the extended panel through Layout configuration.
     *
     * @param {Array} allFields The full list of field definitions for the field selector.
     * @return {Array} The list of field definitions for the field selector without some duplicate fields.
     */
    excludeMainPanelFields: function(allFields) {
        var excludedFields = ['name', 'website', 'hint_education_2'];
        return _.reduce(excludedFields, function(fields, fieldName) {
            return _.without(fields, _.findWhere(fields, {name: fieldName}));
        }, allFields);
    },

    /**
     * Reset enrich fields view
     *
     * @param {string} moduleName
     */
    resetEnrichFieldsView: function(moduleName) {
        var self = this;
        _.each(_.keys(self.bucketList[moduleName].fields), function(field) {
            self.bucketList[moduleName].fields[field] = true;
        });
    },

    /**
     * Get all available fields
     *
     * @param module
     * @return {*[]}
     */
    getAllAvailableFields: function(module) {
        var results = [];
        var self = this;
        var fields = app.hint.getModuleFieldsAvailableForSelection(module);
        var selectableFields = this.excludeMainPanelFields(fields);

        _.each(selectableFields, function(field) {
            field.disabled = this.isFieldDisabled(module, field.name);
            field.value = field.label;
            if (!field.label || field.label === '') {
                return;
            }

            for (var i = 0; i < self.enrichHintFields.length; i++) {
                if (self.enrichHintFields[i].name === field.name) {
                    return;
                }
            }

            results.push(field);
        }, this);

        return results;

    },

    /**
     * Get all enrich fields for hint
     *
     * @param {string} module
     * @return {Array}
     */
    getAllEnrichFieldsForHint: function(module) {
        var results = [];
        var fields = app.hint.getPanelsForHintEnrichFields(module);
        var isPersonOrientedModule = module !== 'Accounts';

        if (isPersonOrientedModule) {
            fields = this.addAccountRelatedFields(fields);
        }

        var notSelectableFields = this.removeUnwantedfield(module);
        _.each(fields, function(field) {
            if (!_.includes(notSelectableFields, field.name)) {
                field.disabled = this.isFieldDisabled(module, field.name);
                field.hintOnlyField = this.isHintOnlyField(field);
                field.value = field.label;
                field.hideCheckbox = _.includes(['title', 'account_name', 'hint_account_website'], field.name);
                if (!field.label || field.label === '') {
                    return;
                }

                if (field.name === 'picture') {
                    field.name = 'hint_photo';
                    field.disabled = true;
                    field.hintOnlyField = false;
                }

                results.push(field);
            }
        }, this);

        results = _.sortBy(results, function(f) {
            return f.label.toLowerCase();
        });

        return results;
    },

    /**
     * Starts with
     *
     * @param {string} string
     * @param {string} searchTerm
     * @return {boolean}
     */
    startsWith: function(string, searchTerm) {
        return string.substr(0, searchTerm.length) === searchTerm;
    },

    /**
     * Add account related fields
     *
     * @param {Array} fields
     * @return {Array}
     */
    addAccountRelatedFields: function(fields) {
        var self = this;
        var accountsRelatedMetadata = app.hint.getPanelsForHintEnrichFields('Accounts');
        var hintPrefix = 'hint_account_';
        var notSelectableFields = this.removeUnwantedfield(this.currentModule);
        _.each(accountsRelatedMetadata, function(field) {
            var shouldRenameField = field.name && !_.includes(notSelectableFields, field.name) &&
                !self.startsWith(field.name, 'hint_');
            if (shouldRenameField) {
                if (field.name === 'sic_code') {
                    field.name = 'hint_account_sic_code_label';
                } else if (field.name === 'twitter') {
                    field.name = 'hint_account_twitter_handle';
                } else {
                    field.name = hintPrefix + field.name;
                }
            }

            if (field.name === 'hint_account_industry_tags') {
                field.name = 'hint_industry_tags';
            }
        });

        var haveAccountsFieldsInPersonModule = fields.concat(accountsRelatedMetadata);
        haveAccountsFieldsInPersonModule = _.uniq(haveAccountsFieldsInPersonModule, false, function(item) {
            return item.name;
        });

        haveAccountsFieldsInPersonModule = _.sortBy(haveAccountsFieldsInPersonModule, function(f) {
            return f.label;
        });

        return haveAccountsFieldsInPersonModule;
    },

    /**
     * Check if is hint only field
     *
     * @param {Object} field
     * @return {Array}
     */
    isHintOnlyField: function(field) {
        return field && field.name && field.name.match(/^hint_[\w]*/gi);
    },

    /**
     * Remove unwanted field
     *
     * @param {string} module
     * @return {Array}
     */
    removeUnwantedfield: function(module) {
        var disabledFields = {
            'Accounts': ['name', 'website', 'picture'],
            'Contacts': ['full_name', 'email', 'account_name', 'name', 'hint_account_logo', 'hint_education_2'],
            'Leads': ['full_name', 'email', 'hint_account_logo', 'name', 'hint_education_2']
        };
        return disabledFields[module];
    },

    /**
     * These are default fields that should not be selectable, should move this to metadata
     *
     * @param module
     * @param field
     */
    isFieldDisabled: function(module, field) {
        var disabledFields = {
            'Accounts': [],
            'Contacts': ['full_name', 'account_name', 'hint_photo', 'hint_education_2'],
            'Leads': ['full_name', 'hint_photo', 'hint_education_2']
        };
        return disabledFields[module] && _.contains(disabledFields[module], field);
    },

    /**
     * Handles when search term is changed, hides and shows fields
     */
    onSearchFilterChanged: _.debounce(function(evt) {
        var self = this;
        var searchTerm = $(evt.currentTarget).val();

        if (searchTerm) {
            searchTerm = searchTerm.toLowerCase();
        }

        // initially all the fields are available
        $('h1[name="subHeaderTitle1-rightpanel"]').show();
        $('h1[name="subHeaderTitle2-rightpanel"]').show();
        $('p[name="showNoMessageField1"]').text('');
        $('p[name="showNoMessageField2"]').text('');

        _.each([self.availableFields, self.enrichHintFields], function(fieldSection) {
            _.each(fieldSection, function(field) {
                if (field.name) {
                    $('div[name=" + field.name + "-rightpanel]').show();
                }
            });
        });

        // depending on the search term the fields the filtered.
        _.each([self.availableFields, self.enrichHintFields], function(fieldSection) {
            _.each(fieldSection, function(field) {
                if (field.label.toLowerCase().indexOf(searchTerm) < 0) {
                    $('div[name=" + field.name + "-rightpanel]').hide();
                }
            });
        });

        var showNoMessageField1 = self._areAnyFieldsVisible(self.enrichHintFields);
        var showNoMessageField2 = self._areAnyFieldsVisible(self.availableFields);

        // this display the No fields found message in enrichFields.
        if (showNoMessageField1) {
            $('h1[name="subHeaderTitle1-rightpanel"]').hide();
            $('p[name="showNoMessageField1"]').text(app.lang.get('LBL_HINT_NO_RESULTS_MSG'));
        }

        // this display the No fields found message in SugarOnly Fields.
        if (showNoMessageField2) {
            $('h1[name="subHeaderTitle2-rightpanel"]').hide();
            $('p[name="showNoMessageField2"]').text(app.lang.get('LBL_HINT_NO_RESULTS_MSG'));
        }

    }, 400),

    /**
     * Check if any fields are visible
     *
     * @param {Array} fieldsArray
     */
    _areAnyFieldsVisible: function(fieldsArray) {
        return _.every(fieldsArray, function(field) {
            return !$('div[name=" + field.name + "-rightpanel]').is(':visible');
        });
    },

    /**
     *
     * @param module
     * @return {Array}
     */
    getHintFieldsFromMeadata: function(module) {
        var hintFields = [];
        var allFields = app.metadata.getModule(module).fields;

        _.each(allFields, function(field) {
            if (field.name && field.name.match(/^hint_[\w]*/gi)) {
                field.label = app.lang.get(field.vname, module);
                hintFields.push(field);
            }
        });

        return hintFields;
    },

    /**
     * @override
     */
    _render: function() {
        var self = this;
        if (!_.isEmpty(self.bucketList)) {
            this._super('_render');
            //Select our default fields...
            _.each(self.defaultPanelFields, function(field) {
                $('.field-container-show' + field.name).prop('checked', true);
            });

            // check the fields in Enrich fields column
            _.each(self.enrichHintFields, function(field) {
                if (self.bucketList[self.currentModule].fields[field.name]) {
                    $('.field-container-' + field.name).prop('checked', true);
                }
            });

            //Based on the Enriched Field checked or unchecked decide to check the shared fields...
            _.each(self.enrichHintFields, function(field) {
                var ishintField = self.isHintOnlyField(field);
                var enrichFieldCheckedBox = $('.field-container-' + field.name);
                var sugarFieldCheckedBox = $('.field-container-show' + field.name);
                var checkedFieldOnRestore =  sugarFieldCheckedBox[0] && sugarFieldCheckedBox[0].checked;
                var corresspondingEnrichFieldChecked = enrichFieldCheckedBox[0] && enrichFieldCheckedBox[0].checked;

                if (checkedFieldOnRestore && !_.isEmpty(ishintField)) {
                    enrichFieldCheckedBox.prop('checked', true);
                } else if (!corresspondingEnrichFieldChecked && !_.isEmpty(ishintField)) {
                    sugarFieldCheckedBox.prop('checked', false);
                    sugarFieldCheckedBox.prop('disabled', true);
                }

                if (field.name === 'hint_photo') {
                    $('.field-container-show' + field.name).prop('checked', true);
                    $('.field-container-' + field.name).prop('disabled', false);
                }
            });
        }
    },

    /**
     * Add enrich array
     *
     * @param {Object} event
     */
    addEnrichArray: function(event) {
        var self = this;
        var fieldName = event.currentTarget.id;
        var fullField = _.find(this.enrichHintFields, function(f) {
            return f.name == fieldName;
        });

        var currentModuleFields = self.bucketList[self.currentModule].fields;

        if (event.currentTarget.checked) {
            if (fullField.hintOnlyField) {
                $('.field-container-show' + fullField.name).prop('disabled', false);
            }

            // Add the status of checked enriched fields of the hint panel.
            _.each(self.enrichHintFields, function(field) {
                var name = field.name;
                if (name === 'hint_education') {
                    currentModuleFields.hint_education_2 = $('.field-container-' + name)[0].checked;;
                } else if (name === 'sic_code' && self.currentModule === 'Accounts') {
                    currentModuleFields.hint_account_sic_code_label = $('.field-container-' + name)[0].checked;
                }
                currentModuleFields[name] = $('.field-container-' + name)[0].checked;
            });

        } else {
            _.each(self.enrichHintFields, function(field) {
                var name = field.name;
                if (name === 'hint_education') {
                    currentModuleFields.hint_education_2 = $('.field-container-' + name)[0].checked;
                } else if (name === 'sic_code' && self.currentModule === 'Accounts') {
                    currentModuleFields.hint_account_sic_code_label = $('.field-container-' + name)[0].checked;
                }
                currentModuleFields[name] = $('.field-container-' + name)[0].checked;
            });

            if (fullField.hintOnlyField) {
                $('.field-container-show' + fullField.name).prop('checked', false);
                $('.field-container-show' + fullField.name).prop('disabled', true);
                app.events.trigger('hint:config:fieldRemoved', fullField);
            }
        }
    },

    /**
     * Check field toggled
     *
     * @param {Object} event
     */
    fieldToggled: function(event) {
        var fieldName = event.currentTarget.id;
        var allFields = this.availableFields.concat(this.enrichHintFields);

        var fullField = _.find(allFields, function(f) {
            return f.name == fieldName;
        });

        if (event.currentTarget.checked) {
            app.events.trigger('hint:config:fieldAdded', fullField);
        } else {
            app.events.trigger('hint:config:fieldRemoved', fullField);
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        app.events.off('hint:config:module:changed');
        app.events.off('hint:config:save');
        app.events.off('hint:config:defaults:restored');
        this._super('_dispose');
    }
});
