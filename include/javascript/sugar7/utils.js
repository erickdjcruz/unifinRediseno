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
    app.events.on("app:init", function() {

        var tooltipTemplate = Handlebars.compile('<div class="tooltip">' +
            '<div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>');

        var $tooltipTemplate = $(tooltipTemplate());

        /**
         * Sets the `parent_type`, `parent_id`, and `parent_name` attributes of
         * the model using the data from the parent.
         *
         * The attributes are updated asynchronously if the parent's name isn't
         * known and must be retrieved. The attributes cannot be set if the
         * user does not have `list` access to the parent's module.
         *
         * @param {Data.Bean} model
         * @param {Data.Bean} parent
         */
        function setParentAttributes(model, parent) {
            var fields = app.metadata.getModule(model.module, 'fields');
            var modules;
            var name;

            modules = _.chain(app.lang.getAppListStrings(fields.parent_type.options))
                .keys()
                .filter(function(module) {
                    return app.acl.hasAccess('list', module);
                })
                .value();

            if (!_.contains(modules, parent.module)) {
                return;
            }

            function setAttributes(parent) {
                model.set('parent', _.extend({type: parent.module}, parent.toJSON()));
                model.set('parent_type', parent.module);
                model.set('parent_id', parent.get('id'));
                model.set('parent_name', parent.get('name'));
            }

            if (_.isEmpty(parent.get('id'))) {
                return;
            }

            if (_.isEmpty(parent.get('name')) && !app.utils.isNameErased(parent)) {
                // If the name field is empty but other fields are present,
                // then it may be possible to construct the name. We try to
                // do that before making an unnecessary request.
                name = app.utils.getRecordName(parent);

                if (!_.isEmpty(name)) {
                    // We were able to determine the name.
                    parent.set('name', name);
                    setAttributes(parent);
                } else {
                    // We need more data from this record.
                    parent.fetch({
                        showAlerts: false,
                        success: setAttributes,
                        fields: ['name']
                    });
                }
            } else {
                setAttributes(parent);
            }
        }

        /**
         * Populates an email with data from a case. This is used when
         * composing an email that is related to a case.
         *
         * The subject of the email becomes "[CASE:<case_number>] <case_name>".
         * Contacts related to the case are added as recipients in the To
         * field.
         *
         * @param {Data.Bean} email
         * @param {Data.Bean} aCase
         */
        function prepopulateEmailWithCase(email, aCase) {
            var config;
            var keyMacro = '%1';
            var caseMacro;
            var subject;
            var contacts;

            if (aCase.module !== 'Cases') {
                return;
            }

            config = app.metadata.getConfig();
            caseMacro = config.inboundEmailCaseSubjectMacro;
            subject = caseMacro + ' ' + aCase.get('name');
            subject = subject.replace(keyMacro, aCase.get('case_number'));

            email.set('name', subject);

            if (email.get('to_collection').length === 0) {
                // no addresses, attempt to populate from contacts relationship
                contacts = aCase.getRelatedCollection('contacts');
                contacts.fetch({
                    relate: true,
                    success: function(data) {
                        var records = data.map(function(record) {
                            var parentName = app.utils.getRecordName(record);

                            // Don't set email_address_id. It will be set when
                            // the email is archived.
                            return app.data.createBean('EmailParticipants', {
                                _link: 'to',
                                parent: _.extend({type: record.module}, app.utils.deepCopy(record.attributes)),
                                parent_type: record.module,
                                parent_id: record.get('id'),
                                parent_name: parentName
                            });
                        });

                        email.get('to_collection').add(records);
                    },
                    fields: ['id', 'name', 'email']
                });
            }
        }

        /**
         * Populates attributes on an email from the data provided.
         *
         * @param {Date.Bean} email
         * @param {Object} data Attributes to set on the email.
         * @param {string} [data.name] Sets the subject of the email.
         * @param {string} [data.description_html] Sets the HTML body of the
         * email.
         * @param {Array|Data.BeanCollection|Data.Bean} [data.from_collection]
         * The sender to add to the From field.
         * @param {Array|Data.BeanCollection|Data.Bean} [data.from] A more
         * convenient alternative name for the sender.
         * @param {Array|Data.BeanCollection|Data.Bean} [data.to_collection]
         * The recipients to add to the To field.
         * @param {Array|Data.BeanCollection|Data.Bean} [data.to] A more
         * convenient alternative name for the To recipients.
         * @param {Array|Data.BeanCollection|Data.Bean} [data.cc_collection]
         * The recipients to add to the CC field.
         * @param {Array|Data.BeanCollection|Data.Bean} [data.cc] A more
         * convenient alternative name for the CC recipients.
         * @param {Array|Data.BeanCollection|Data.Bean} [data.bcc_collection]
         * The recipients to add to the BCC field.
         * @param {Array|Data.BeanCollection|Data.Bean} [data.bcc] A more
         * convenient alternative name for the BCC recipients.
         * @param {Array|Data.BeanCollection|Data.Bean} [data.attachments_collection]
         * The attachments to attach to the email.
         * @param {Array|Data.BeanCollection|Data.Bean} [data.attachments] A
         * more convenient alternative name for the attachments.
         * @param {Data.Bean} [data.related] A convenience for relating a
         * parent record to the email.
         * @param {boolean} [data.skip_prepopulate_with_case] Prevent
         * prepopulating case data in the email.
         * @return {Object} Any key-values pairs that could not be assigned are
         * returned so the caller can decide what to do with them.
         */
        function prepopulateEmailForCreate(email, data) {
            var fields = app.metadata.getModule('Emails', 'fields');
            var fieldNames = _.keys(fields);
            var fieldMap = {
                attachments: 'attachments_collection',
                from: 'from_collection',
                to: 'to_collection',
                cc: 'cc_collection',
                bcc: 'bcc_collection'
            };
            var nonFieldData = {};

            _.each(data, function(value, fieldName) {
                var fieldDefs;

                fieldName = fieldMap[fieldName] || fieldName;
                fieldDefs = fields[fieldName] || {};

                if (fieldName === 'related') {
                    if (data.skip_prepopulate_with_case !== true) {
                        prepopulateEmailWithCase(email, value);
                    }

                    setParentAttributes(email, value);
                } else if (fieldDefs.type === 'collection') {
                    if (value instanceof app.BeanCollection) {
                        value = value.models;
                    }

                    email.get(fieldName).add(value);
                } else if (_.contains(fieldNames, fieldName)) {
                    email.set(fieldName, value);
                } else {
                    nonFieldData[fieldName] = value;
                }
            });

            return nonFieldData;
        }

        app.utils = _.extend(app.utils, {
            /**
             * Converts the Component data into view metadata
             *
             * @param {Object} comp The Component object data
             * @return {Array} List of dashlet views from the app metadata
             */
            convertCompToDashletView: function(comp) {
                var dashletConfigs = comp.view.dashletConfig;
                dashletConfigs = _.isArray(dashletConfigs) ? dashletConfigs : [dashletConfigs];
                return _.map(dashletConfigs, function(dashletConfig) {
                    var metadata = {
                        component: 'external-app-dashlet',
                        config: {
                            customConfig: true,
                            src: comp.view.src,
                            dashletConfig: comp.view.dashletConfig,
                        },
                        customDashletMeta: comp,
                        description: comp.view.description,
                        label: comp.view.name,
                        dashletType: dashletConfig.type,
                        type: 'external-app-dashlet'
                    };
                    return {
                        description: comp.view.description,
                        metadata: metadata,
                        title: comp.view.name,
                        type: 'external-app-dashlet'
                    };
                });
            },
            /**
             * Gets a list of MFE custom dashlets available in this context
             *
             * @param {Object} context The context object data
             * @return {Array} List of dashlet views
             */
            getMfeDashletViews: function(context) {
                var isMultiLine = context && context.parent && context.parent.get('layout') === 'multi-line';
                var parentModule = isMultiLine ? context.parent.get('module') : app.controller.context.get('module');
                var parentView = isMultiLine ?
                    'record' :
                    app.controller.context.get('layout');
                var dashletView = parentView === 'records' ?
                    'list-dashlet' :
                    'record-dashlet';
                var dashletMeta = app.metadata.getLayout(parentModule, dashletView);
                if (!dashletMeta) {
                    return [];
                }
                return _(dashletMeta.components)
                    .chain()
                    .map(function(comp) {
                        try {
                            return app.utils.convertCompToDashletView(comp);
                        } catch (err) {
                            return [];
                        }
                    })
                    .flatten()
                    .value();
            },
            /**
             * If Forecasts is not setup, it will convert commit_stage into a spacer field and show no-data
             *
             * @param {Object} panels
             */
            hideForecastCommitStageField: function(panels) {
                var config = app.metadata.getModule('Forecasts', 'config'),
                    isSetup = (config && config.is_setup);
                if (!isSetup) {
                    _.each(panels, function(panel) {
                        // use _.every so we can break out after we found the commit_stage field
                        _.every(panel.fields, function(field, index) {
                            if (field.name == 'commit_stage') {
                                panel.fields[index] = {
                                    'name': 'spacer',
                                    'label': field.label,
                                    'span': 6,
                                    'readonly': true
                                };
                                return false;
                            }
                            return true;
                        }, this);
                    }, this);
                }
            },

            /**
             * Takes two Forecasts models and returns HTML for the history log
             *
             * @param {Backbone.Model} oldestModel the oldest model by date_entered
             * @param {Backbone.Model} newestModel the most recent model by date_entered
             * @param {boolean} isRepWorksheet Are we showing the history log on the rep worksheet?
             * @return {Object}
             */
            createHistoryLog: function(oldestModel, newestModel, isRepWorksheet) {
                var labels = [];

                if (_.isEmpty(oldestModel)) {
                    oldestModel = new Backbone.Model({
                        best_case: 0,
                        likely_case: 0,
                        worst_case: 0,
                        date_entered: ''
                    });
                    labels.push('LBL_COMMITTED_HISTORY_SETUP_FORECAST');
                } else {
                    labels.push('LBL_COMMITTED_HISTORY_UPDATED_FORECAST');
                }

                var fields = ['worst', 'likely', 'best'],
                    final = [],
                    num_shown = 0,
                    config = app.metadata.getModule('Forecasts', 'config'),
                    aclModule = (isRepWorksheet) ? config.forecast_by : newestModel.module;

                _.each(fields, function(field) {
                    var fieldName = field + '_case';
                    if (config['show_worksheet_' + field] && app.acl.hasAccess('view', aclModule, null, fieldName)) {
                        var diff = this.getDifference(oldestModel, newestModel, fieldName);
                        num_shown++;

                        if (diff != 0) {
                            var direction = this.getDirection(diff);
                            final.push(
                                this.gatherLangArgsByParams(
                                    direction,
                                    this.getArrowDirectionSpan(direction),
                                    diff,
                                    newestModel,
                                    fieldName
                                )
                            );
                            labels.push('LBL_COMMITTED_HISTORY_' + field.toUpperCase() + '_CHANGED');
                        } else {
                            final.push([]);
                            labels.push('LBL_COMMITTED_HISTORY_' + field.toUpperCase() + '_SAME');
                        }
                    }
                }, this);

                var lang_string_key = 'LBL_COMMITTED_HISTORY_' + num_shown + '_SHOWN',
                    hb = Handlebars.compile('{{{str key module args}}}');

                final = this.parseArgsAndLabels(final, labels);

                //Compile the language string for the log
                var text = hb({'key': lang_string_key, 'module': 'Forecasts', 'args': final});

                // need to tell Handlebars not to escape the string when it renders it, since there might be
                // html in the string, args returned for testing purposes
                return {
                    'text': new Handlebars.SafeString(text),
                    'text2': app.date(newestModel.get('date_modified')).formatUser(false, app.user)
                };
            },

            /**
             * Returns an array of three args for the html for the arrow,
             * the difference (amount changed), and the new value
             *
             * @param {String} dir direction of the arrow, LBL_UP/LBL_DOWN
             * @param {String} arrow HTML for the arrow string
             * @param {Number} diff difference between the new model and old model
             * @param {Backbone.Model} model the newestModel being used so we can get the current caseStr
             * @param {String} attrStr the attr string to get from the newest model
             * @return {Object}
             */
            gatherLangArgsByParams: function(dir, arrow, diff, model, attrStr) {
                return {
                    'direction' : new Handlebars.SafeString(app.lang.get(dir, 'Forecasts') + arrow),
                    'from' : app.currency.formatAmountLocale(Math.abs(diff)),
                    'to' : app.currency.formatAmountLocale(model.get(attrStr))
                };
            },

            /**
             * checks the direction class passed in to determine what span to create to show the appropriate arrow
             * or lack of arrow to display on the
             * @param {String} directionClass class being used for the label ('LBL_UP' or 'LBL_DOWN')
             * @return {String}
             */
            getArrowDirectionSpan: function(directionClass) {
                return directionClass == 'LBL_UP' ? '&nbsp;<i class="sicon sicon-arrow-up font-green"></i>' :
                    directionClass == 'LBL_DOWN' ? '&nbsp;<i class="sicon sicon-arrow-down font-red"></i>' : '';
            },

            /**
             * Centralizes our forecast type switch.
             *
             * @param {Boolean} isManager
             * @param {Boolean} showOpps
             * @return {String} 'Direct' or 'Rollup'
             */
            getForecastType: function(isManager, showOpps) {
                /**
                 * Three cases exist when a row is showing commitLog icon:
                 *
                 * Manager - showOpps=1 - isManager=1 => Mgr's Opportunities row - forecast_type = 'Direct'
                 * Manager - showOpps=0 - isManager=1 => Mgr has another mgr in their MgrWkst - forecast_type = 'Rollup'
                 * Rep     - showOpps=0 - isManager=0 => Sales Rep (not a manager) row - forecast_type = 'Direct'
                 */
                return (!showOpps && isManager) ? 'Rollup' : 'Direct';
            },

            /**
             * Parses through labels array and adds the proper args in to the string
             *
             * @param {Array} argsArray of args (direction arrow html, amount difference and the new amount)
             * @param {Array} labels of lang key labels to use
             * @return {Array}
             */
            parseArgsAndLabels: function(argsArray, labels) {
                var retArgs = {},
                    argsKeys = ['first', 'second', 'third'],
                    hb = Handlebars.compile('{{{str key module args}}}');

                // labels should have one more item in its array than argsArray
                // because of the SETUP or UPDATED label which has no args
                if ((argsArray.length + 1) != labels.length) {
                    // SOMETHING CRAAAAZY HAPPENED!
                    var msg = 'ForecastsUtils.parseArgsAndLabels() :: ' +
                            'argsArray and labels params are not the same length';
                    app.logger.error(msg);
                    return null;
                }

                // get the first argument off the label array
                retArgs.intro = hb({'key': _.first(labels), 'module': 'Forecasts', 'args': []});

                // get the other values, with out the first value
                labels = _.last(labels, labels.length - 1);

                // loop though all the other values
                _.each(labels, function(label, index) {
                    retArgs[argsKeys[index]] = hb({'key': label, 'module': 'Forecasts', 'args': argsArray[index]});
                });

                return retArgs;
            },

            /**
             * Returns the difference between the newest model and the oldest
             *
             * @param {Backbone.Model} oldModel
             * @param {Backbone.Model} newModel
             * @param {String} attr the attribute key to get from the models
             * @return {*}
             */
            getDifference: function(oldModel, newModel, attr) {
                return (app.math.isDifferentWithPrecision(newModel.get(attr), oldModel.get(attr))) ?
                    app.math.getDifference(newModel.get(attr), oldModel.get(attr)) : 0;
            },


            /**
             * Returns the proper direction label to use
             *
             * @param {Number} difference the amount of difference between newest and oldest models
             * @return {String} LBL_UP, LBL_DOWN, or ''
             */
            getDirection: function(difference) {
                return difference > 0 ? 'LBL_UP' : (difference < 0 ? 'LBL_DOWN' : '');
            },

            /**
             * Returns the subpanel list with link module name and corresponding LBL_
             *
             * @param {module} string
             * @return {Object} The subpanel list
             */
            getSubpanelList: function(module) {
                var list = {},
                    subpanels = app.metadata.getModule(module),
                    comps;

                // Get the subpanel metadata for layouts...
                if (subpanels && subpanels.layouts) {
                    subpanels = subpanels.layouts.subpanels;
                    if (subpanels && subpanels.meta) {
                        // If this module defines itself as using dynamic
                        // subpanels then let it do it's thing
                        if (subpanels.meta.dynamic) {
                            comps = this.getDynamicSubpanelList(module);
                            if (comps && comps.meta) {
                                comps = comps.meta.components || [];
                            }
                        } else {
                            // Otherwise use what we have in metadata
                            comps = subpanels.meta.components || [];
                        }
                    }
                }

                // If there are components, traverse them and get what is needed
                if (comps) {
                    _.each(comps, function(comp) {
                        if (comp.context && comp.context.link) {
                            list[comp.label] = comp.context.link;
                        } else {
                            app.logger.warning("Subpanel's subpanels.meta.components "
                                + "has component with no context or context.link");
                        }
                    });
                }

                return list;
            },

            /**
             * Gets a list of subpanel metadata for a module.
             * @return {object}
             */
            getDynamicSubpanelList: function(module) {
                var dSubpanels = this.getDynamicSubpanelMetadata(module);
                return {meta: dSubpanels};
            },

            /**
             * Gets dynamic subpanel metadata for a module from the relationships
             * for that module that are marked as dynamic subpanels
             * @return {object}
             */
            getDynamicSubpanelMetadata: function(module) {
                // Get our relationship metadata for validation
                var rels = app.metadata.get().relationships,
                    // Get the fields for the tags module to get the links from
                    fields = app.metadata.getModule(module).fields,
                    // Need the modules for validation as well
                    modules = app.metadata.getModules(),
                    // Declaring the components array for return
                    comps = [],
                    // Because javascript
                    self = this;

                // Loop over each field and get the link fields from the field def
                _.each(fields, function(fDefs, fName) {
                    var relModule,
                        relDefName,
                        spLabel;

                    // If this field is a link type and there is a relationship for it...
                    if (fDefs.type && fDefs.type === 'link' && fDefs.relationship) {
                        relDefName = fDefs.relationship;
                        // If the relationship exists and supports dynamic subpanels...
                        if (self.hasDynamicSubpanelDef(rels, relDefName, module)) {
                            relModule = rels[relDefName].lhs_module;
                            // And the left side module is in the module list...
                            if (modules[relModule]) {
                                // Add handling for modules whose subpanel layouts are not just 'subpanel'
                                var layoutName = 'subpanel';
                                if (modules[relModule].additionalProperties
                                    && modules[relModule].additionalProperties.dynamic_subpanel_name) {
                                    layoutName = modules[relModule].additionalProperties.dynamic_subpanel_name;
                                }
                                // Add it to the components array
                                comps.push({
                                    context: {link: fDefs.name},
                                    label: self.getDynamicSubpanelLabel(module, relModule),
                                    layout: layoutName
                                });
                            }
                        }
                    }
                });

                return {components: comps};
            },

            /**
             * Gets the label for the Subpanel from the module or related module
             *
             * @param {string} module The parent module
             * @param {string} relModule The relate module
             * @return {string} The label for the subpanel
             */
            getDynamicSubpanelLabel: function(module, relModule) {
                // Define the label index we need to check for a named module
                var lIndex = 'LBL_' + relModule.toUpperCase() + '_SUBPANEL_TITLE';
                // Definitions to apply in order to find the label
                var stackDef = [
                    // Start with the default subpanel title for the related module first to pick
                    // up renamed module names
                    {key: 'LBL_DEFAULT_SUBPANEL_TITLE', mod: relModule},

                    // Try the parent module to see any hardcoded subpanel titles
                    {key: lIndex, mod: module},

                    // Try getting the label from the related module
                    {key: lIndex, mod: relModule},

                    // Lastly, try getting the label from the related module name
                    {key: 'LBL_MODULE_NAME', mod: relModule}
                ];
                // The label to return
                var label;

                // Loop through stackDef and break if we find a label
                for (var i in stackDef) {
                    if (label = app.lang.getModString(stackDef[i].key, stackDef[i].mod)) {
                        return label;
                    }
                }

                // If we couldn't find a label, return the related module
                return relModule;
            },

            /**
             * Checks if a relationship def supports dynamic subpanel loading
             *
             * @param {object} rels The relationship collection
             * @param {string} relDefName The name of the current relationship
             * @param {string} module The module to check against
             * @return {Boolean}
             */
            hasDynamicSubpanelDef: function(rels, relDefName, module) {
                return rels[relDefName]
                       && rels[relDefName].dynamic_subpanel
                       && rels[relDefName].lhs_module
                       && rels[relDefName].rhs_module
                       && rels[relDefName].rhs_module === module;
            },

            /**
             * Returns TRUE if any of the related fields associated with this link are required,
             * which would make this link required.  Returns FALSE otherwise.
             *
             * @param {String} module Parent module name
             * @param {String} link Link name
             * @return {Boolean}
             */
            isRequiredLink: function(module, link){
                var relatedFields = app.data.getRelateFields(module, link),
                    requiredField = _.some(relatedFields, function(field){
                        return field.required === true;
                    }, this);
                return requiredField;
            },

            /**
             * Contains a list of column names from metadata and maps them to correct config param
             * e.g. 'likely_case' column is controlled by the Forecast config.show_worksheet_likely param
             * Used by forecastsWorksheetManager, forecastsWorksheetManagerTotals
             *
             * @property tableColumnsConfigKeyMapManager
             * @private
             */
            _tableColumnsConfigKeyMapManager: {
                'likely_case': 'show_worksheet_likely',
                'likely_case_adjusted': 'show_worksheet_likely',
                'best_case': 'show_worksheet_best',
                'best_case_adjusted': 'show_worksheet_best',
                'worst_case': 'show_worksheet_worst',
                'worst_case_adjusted': 'show_worksheet_worst'
            },

            /**
             * Contains a list of column names from metadata and maps them to correct config param
             * e.g. 'likely_case' column is controlled by the Forecast config.show_worksheet_likely param
             * Used by forecastsWorksheet, forecastsWorksheetTotals
             *
             * @property tableColumnsConfigKeyMapRep
             * @private
             */
            _tableColumnsConfigKeyMapRep: {
                'likely_case': 'show_worksheet_likely',
                'best_case': 'show_worksheet_best',
                'worst_case': 'show_worksheet_worst'
            },

            /**
             * Function checks the proper _tableColumnsConfigKeyMap___ for the key and returns the config setting
             *
             * @param key {String} table key name (eg: 'likely_case')
             * @param viewName {String} the name of the view calling the function (eg: 'forecastsWorksheet')
             * @return {*}
             */
            getColumnVisFromKeyMap: function(key, viewName) {
                var moduleMap = {
                        'forecastsWorksheet': 'rep',
                        'forecastsWorksheetTotals': 'rep',
                        'forecastsWorksheetManager': 'mgr',
                        'forecastsWorksheetManagerTotals': 'mgr'
                    },
                    whichKeyMap,
                    keyMap,
                    returnValue;

                // which key map to use from the moduleMap
                whichKeyMap = moduleMap[viewName];

                // get the proper keymap
                keyMap = (whichKeyMap === 'rep') ? this._tableColumnsConfigKeyMapRep
                                                 : this._tableColumnsConfigKeyMapManager;

                returnValue = app.metadata.getModule('Forecasts', 'config')[keyMap[key]];
                // If we've been passed a value that doesn't exist in the keymaps
                if(!_.isUndefined(returnValue)) {
                    // convert it to boolean
                    returnValue = returnValue == 1
                } else {
                    // if return value was null (not found) then set to true
                    returnValue = true;
                }
                return returnValue;
            },

            /**
             * If the passed in User is a Manager, then get his direct reportees, and then set the user
             * on the context, if they are not a Manager, just set user to the context
             * @param selectedUser
             * @param context
             */
            getSelectedUsersReportees: function(selectedUser, context) {
                if(selectedUser.is_manager) {
                    // make sure the reportee's array is there
                    if(_.isUndefined(selectedUser.reportees)) {
                        selectedUser.reportees = [];
                    }
                    var url = app.api.buildURL('Users', 'filter'),
                        post_args = {
                            "filter": [
                                {
                                    'reports_to_id': selectedUser.id,
                                    'status' : 'Active'
                                }
                            ],
                            'fields': 'full_name',
                            'max_num': 1000
                        },
                        options = {};
                    options.success = _.bind(function(resp, status, xhr) {
                        _.each(resp.records, function(user) {
                            selectedUser.reportees.push({id: user.id, name: user.full_name});
                        });
                        this.set({
                            selectedUser: selectedUser,
                            forecastType: app.utils.getForecastType(selectedUser.is_manager, selectedUser.showOpps)
                        });
                    }, context);
                    app.api.call("create", url, post_args, options);
                } else {
                    // update context with selected user which will trigger checkRender
                    context.set({
                        selectedUser: selectedUser,
                        forecastType: app.utils.getForecastType(selectedUser.is_manager, selectedUser.showOpps)
                    });
                }
            },

            /**
             * Makes sure that Sales Stage Won/Lost values from the database Forecasts config settings
             * exist in the sales_stage_dom
             *
             * @returns {Boolean} if forecasts is configured to run ok
             */
            checkForecastConfig: function() {
                var forecastConfigOK = true,
                    cfg = app.metadata.getModule('Forecasts', 'config') || {},
                    salesWonVals = cfg.sales_stage_won,
                    salesLostVals = cfg.sales_stage_lost,
                    salesWonLostVals = cfg.sales_stage_won.concat(cfg.sales_stage_lost),
                    domVals = app.lang.getAppListStrings('sales_stage_dom');

                if(salesWonVals.length == 0 || salesLostVals.length == 0 || _.isEmpty(domVals)) {
                    forecastConfigOK = false;
                } else {
                    forecastConfigOK = _.every(salesWonLostVals, function(val) {
                        return (val != '' && _.has(domVals, val));
                    }, this);
                }

                return forecastConfigOK;
            },

            /**
             * Returns if the current browser has touch events
             *
             * @return {boolean}
             */
            isTouchDevice: function() {
                return Modernizr.touch;
            },

            /**
             * Builds a route for module in either bwc or new sidecar.
             *
             * This overrides the normal router to check first if the module
             * is in BWC or not. If not, this will fallback to default
             * {@link Core.Routing#buildRoute}.
             *
             * @inheritdoc
             * @param {Boolean} inBwc If `true` it will force bwc, if `false`
             * it will force sidecar, if not defined, will use metadata
             * information on module. This is a temporary param (hack) and will
             * be removed after we change all the views/layouts to be the ones
             * pointing if it should be loaded in BWC or not.
             *
             * @override Core.Routing#buildRoute
             * @see Bwc#buildRoute()
             */
            customBuildRoute: function(moduleOrContext, id, action, inBwc) {
                var module, moduleMeta;

                // Since _.isString(undefined) returns false,
                // the following block prevent going getter block
                if (_.isEmpty(moduleOrContext)) {
                    return '';
                }

                if (_.isString(moduleOrContext)) {
                    module = moduleOrContext;
                } else {
                    module = moduleOrContext.get('module');
                }

                if (_.isEmpty(module) || !app.bwc) {
                    return '';
                }

                moduleMeta = app.metadata.getModule(module) || {};
                if (inBwc === false || (_.isUndefined(inBwc) && !moduleMeta.isBwcEnabled)) {
                    return '';
                }

                return app.bwc.buildRoute(module, id, app.bwc.getAction(action));
            },

            /**
             * Adds `bwcFrame=1` to the URL if it's not there.
             *
             * @param {string} url The original url.
             * @return {string} The url with the iframe mark.
             */
            addIframeMark: function(url) {
                var parts = url.split('?');
                if (parts[1] && parts[1].indexOf('bwcFrame=1') != -1) return url;
                return parts[0] + '?' + (parts[1] ? parts[1] + '&bwcFrame=1' : 'bwcFrame=1');
            },

            /**
             * Removes `bwcFrame=1` from the URL if it's there.
             *
             * @param {string} url The original url.
             * @return {string} The url without the iframe mark.
             */
            rmIframeMark: function(url) {
                var parts = url.split('?');
                if (!parts[1]) {
                    return url;
                }
                // scan and drop bwcFrame=1
                return parts[0] + '?' + _.reduce(parts[1].split('&'), function(acc, item) {
                    if (item == 'bwcFrame=1') {
                        return acc;
                    } else {
                        return acc ? acc + '&' + item : item;
                    }
                }, '');
            },

            /**
             * Returns a collection of subpanel models from the LHS context
             * Only tested in Record View!
             *
             * @param ctx the LHS context
             * @param {String} module the name of the module to look for
             * @returns {*} returns the collection or undefined
             */
            getSubpanelCollection: function(ctx, module) {
                var retCollection = undefined,
                    mdl = _.find(ctx.children, function(child) {
                        return (child.get('module') == module);
                    });
                if(mdl && _.has(mdl.attributes, 'collection')) {
                    retCollection = mdl.get('collection');
                }

                return retCollection;
            },
            /**
             * Extracts the full record name from a model.
             *
             * @param {Data.Bean} model The model concerned.
             * @return {string} The record name.
             */
            getRecordName: function(model) {
                var metadata = app.metadata.getModule(model.module) || {};
                var format;
                var name;

                if (!_.isEmpty(metadata.nameFormat)) {
                    // Defaulting the format avoids having to fix numerous
                    // tests because the preference isn't set.
                    format = app.user.getPreference('default_locale_name_format') || 's f l';
                    name = app.utils.formatNameModel(model.module, model.attributes, format);
                } else if (model.module === 'Documents' && model.has('document_name')) {
                    name = model.get('document_name');
                }

                return name || model.get('full_name') || model.get('name') || '';
            },

            /**
             * Returns the first email address that matches the options or an
             * empty string if none exist.
             *
             * An assumption is made that the associated email addresses will
             * be found as an array on the model's `email` attribute.
             *
             * @param {Data.Bean} model
             * @param {Object} [options]
             * @param {Boolean} [options.primary_address]
             * @param {Boolean} [options.invalid_email]
             * @param {Boolean} [options.opt_out]
             * @return {string}
             */
            getEmailAddress: function(model, options) {
                var addresses;

                options || (options = {});
                addresses = model.get('email');

                return _.chain(addresses).findWhere(options).pick('email_address').values().first().value() || '';
            },

            /**
             * Returns the first valid email address associated with the model
             * or an empty string if none exist.
             *
             * An email address is considered valid if its `invalid_email`
             * property is `false`. The email address with `true`
             * for the `primary_address` property is tested first.
             *
             * This method is implemented to simulate the server-side method
             * SugarEmailAddress::getPrimaryAddress().
             *
             * @param {Data.Bean} model
             * @return {string}
             */
            getPrimaryEmailAddress: function(model) {
                return app.utils.getEmailAddress(model, {primary_address: true, invalid_email: false}) ||
                    app.utils.getEmailAddress(model, {invalid_email: false});
            },

            /**
             * Resolve data conflict on bean update. Open a drawer to pick between data in the database
             * or the data currently in the bean.
             * @param error
             * @param model
             * @param callback
             */
            resolve409Conflict: function(error, model, callback) {
                app.drawer.open({
                    layout: 'resolve-conflicts',
                    context: {
                        dataInDb: error.payload.record,
                        modelToSave: model
                    }
                }, callback);
            },

            /**
             * Returns the Forecasts not setup message with link to config if isAdmin is true
             *
             * @param {boolean} isAdmin is the user an admin
             * @returns {string} a translated string with a link to Forecasts config if user is an admin
             */
            getForecastNotSetUpMessage: function(isAdmin) {
                var langKey = (isAdmin) ? 'LBL_DASHLET_FORECAST_NOT_SETUP_ADMIN' : 'LBL_DASHLET_FORECAST_NOT_SETUP',
                    msg = app.lang.get(langKey, 'Forecasts');
                if(isAdmin) {
                    var linkText = app.lang.get('LBL_DASHLET_FORECAST_CONFIG_LINK_TEXT', 'Forecasts');
                    msg += '  <a href="#Forecasts/config">' + linkText + '</a>';
                }

                return msg;
            },

            /**
             * Retrieves the labels for the fields that are searchable in the quicksearch.
             *
             * @param {string} moduleName The module name the fields belong to.
             * @param {string[]} fields The list of searchable fields.
             * @return {string[]} The list of labels.
             */
            getFieldLabels: function(moduleName, fields) {
                var moduleMeta = app.metadata.getModule(moduleName);
                var labels = [];

                _.each(_.flatten(fields), function(fieldName) {
                    var fieldMeta = moduleMeta.fields[fieldName];
                    if (fieldMeta) {
                        labels.push(app.lang.get(fieldMeta.vname, moduleName));
                    }
                });

                return labels;
            },

            /**
             * Convert a raw file size into a human readable size
             *
             * @param {int} size
             * @return {string}
             */
            getReadableFileSize: function(size) {
                var i = -1;
                var units = ['K', 'M', 'G', 'T'];

                if (_.isEmptyValue(size) || size < 1) {
                    size = 0;
                } else if (size < 1000) {
                    //Not displaying bytes, round up to 1K
                    size = 1000;
                }

                do {
                    size = Math.round(size / 1000);
                    i++;
                } while (size >= 1000 && i < (units.length - 1));

                return size + units[i];
            },

            /**
             * gets window location parameters by name regardless of case
             * @param {String} name name of parameter being searched for
             * @param {String} queryString
             * @returns {String}
             */
            getWindowLocationParameterByName: function (name, queryString) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)', 'g'),
                    matchResults = queryString.match(regex);
                if (matchResults && matchResults.length > 0) {
                    var results = regex.exec(matchResults[matchResults.length - 1]);
                }
                return (results === undefined || results === null) ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            },

            /**
             * Javascript equivalent to the Sugar `isTruthy` utility function.
             *
             * @param {string|boolean} value The value to test.
             * @return {boolean} `true` if the value to test is roughly
             *   equivalent to `true` (in a Sugar context).
             */
            isTruthy: function(value) {
                if (_.isString(value)) {
                    value = value.toLowerCase();
                }
                return value === true
                    || value === 'true'
                    || value === 1
                    || value === '1'
                    || value === 'on'
                    || value === 'yes';
            },

            /**
             * Returns the set of fields on a model that are components of the name field.
             * @param {app.data.Bean} model
             * @param {string} (optional) format "s f l t" style name format string.
             * @return Array names of fields that make up the user formated name for this model.
             */
            getFullnameFieldComponents: function(model, format) {
                var components = [];
                var nameDef = model.fields.name;

                if (!nameDef) {
                    return components;
                }

                if (nameDef.type === 'fullname') {
                    format = format || app.user.getPreference('default_locale_name_format');
                    var metadata = app.metadata.getModule(model.module) || {fields: {}};
                    var formatMap = metadata.nameFormat || {};

                    if (_.isEmpty(formatMap)) {
                        return ['name'];
                    }
                    _.each(format.split(''), function(letter) {
                        if (formatMap[letter]) {
                            components.push(formatMap[letter]);
                        }
                    });

                    return components;
                } else if (nameDef.fields) {
                    return nameDef.fields;
                }

                return ['name'];
            },

            /**
             * Returns true if the name or fields that make up the name have been erased
             * and all name components are empty
             * @param model
             * @return {boolean}
             */
            isNameErased: function(model) {
                var erasedFields = model.get('_erased_fields');

                if (_.isEmpty(erasedFields)) {
                    return false;
                }

                //Otherwise check for the name/fullname components
                var nameComponents = this.getFullnameFieldComponents(model);
                var anyNameFieldErased =  _.some(nameComponents, function(field) {
                    return _.contains(erasedFields, field);
                });

                //Ensure that the name is empty before marking it erased
                return anyNameFieldErased && _.every(nameComponents.concat('name'), function(field) {
                    return !model.get(field);
                });
            },

            /**
             * Populates the data on the email and then opens the specified
             * Emails layout in a drawer.
             *
             * @param {string} layout Use `create` for creating an archived
             * email and `compose-email` for creating a draft/sending an email.
             * @param {Object} [data] Any data to populate on the model before
             * opening the drawer. The keys `create` and `module` are reserved.
             * Any key-value pairs that can't be populated on the model will
             * become attributes on the drawer's context.
             * @param {Data.Bean} [data.model] Can be used to specify a model
             * for the drawer's context. The model must be an Emails model. If
             * not specified, a new Emails model will be created for the
             * context and the data will be populated on that new model.
             * @param {Function} [onClose] The callback that is called when the
             * drawer closes.
             */
            openEmailCreateDrawer: function(layout, data, onClose) {
                var context = {
                    create: true,
                    module: 'Emails'
                };

                data = data || {};

                if (data.model && data.model instanceof app.Bean && data.model.module === context.module) {
                    // Use the provided model for the context.
                    context.model = data.model;
                } else {
                    // Create a new model for the context.
                    context.model = app.data.createBean(context.module);
                }

                // We're done with the provided model. Remove it so nothing
                // funky happens while populating the email.
                data = _.omit(data, 'model');

                // Populate the email with the rest of the data. Any data that
                // can't be mapped to an Emails field will be returned so it
                // can become an attribute on the context.
                data = prepopulateEmailForCreate(context.model, data);

                // Merge context over the remaining data so that it is
                // impossible for anyone to override `create` and `module`.
                context = _.extend(data, context);

                app.drawer.open({
                    layout: layout,
                    context: context
                }, onClose);
            },

            /**
             * Get all of the links on the primary module that relate records
             * to the related module.
             *
             * @param {string} primaryModule The module with the link fields.
             * @param {string} relatedModule The module on the other side of
             * the link fields we care about.
             * @return {Array}
             */
            getLinksBetweenModules: function(primaryModule, relatedModule) {
                var fields = app.metadata.getModule(primaryModule).fields;

                return _.chain(fields).filter(function(field) {
                    return field.type && field.type === 'link';
                }).filter(function(link) {
                    return app.data.getRelatedModule(primaryModule, link.name) === relatedModule;
                }, this).value();
            },

            /**
             * Get the full site URL.
             *
             * @return {string}
             */
            getSiteUrl: function() {
                var origin;

                if (app.config.siteUrl) {
                    return app.config.siteUrl;
                }

                if (window.location.origin) {
                    origin = window.location.origin;
                } else {
                    origin = window.location.protocol + '//' +
                        window.location.hostname +
                        (window.location.port ? ':' + window.location.port : '');
                }

                return origin + window.location.pathname;
            },

            /**
             * Returns the Probability percent given a sales stage
             *
             * @param {string} salesStage The sales stage to check
             * @return {number} The whole value of the percent 60 = "60%"
             */
            getProbabilityBySalesStage: function(salesStage) {
                var cfg = app.metadata.getModule('Forecasts', 'config');
                var salesProb = 0;

                if (!cfg.is_setup) {
                    return salesProb;
                }

                salesProb = app.lang.getAppListStrings('sales_probability_dom');

                return salesProb[salesStage];
            },

            /**
             * Returns the Commit Stage given a sales stage
             *
             * @param {string} salesStage The sales stage to check
             * @return {string} The Commit Stage value
             */
            getCommitStageBySalesStage: function(salesStage) {
                var cfg = app.metadata.getModule('Forecasts', 'config');
                var stage = '';

                if (!cfg.is_setup) {
                    return stage;
                }

                var probability = app.utils.getProbabilityBySalesStage(salesStage);
                var ranges = cfg[cfg.forecast_ranges + '_ranges'];

                _.find(ranges, function(range, index) {
                    if (probability >= range.min && probability <= range.max) {
                        stage = index;
                        return true;
                    }
                    return false;
                });

                return stage;
            },

            /**
             * Returns whether the password rule is set
             *
             * @param {Mixed} passwordRule The password rule to check
             * @return {boolean}
             */
            isPasswordRuleSet: function(passwordRule) {
                if (_.isString(passwordRule)) {
                    return (!isNaN(passwordRule) && parseInt(passwordRule, 10) > 0) ? true : false;
                }
                return passwordRule;
            },

            /**
             * Validate if the password meets the password rules configured in Password Management:
             *
             * e.g.
             * 1. Must contain one upper case letter (A-Z)
             * 2. Must contain one lower case letter (a-z)
             * 3. Must contain one number (0-9)
             * 4. Minimum Length = 6
             *
             * @param {string} password
             * @return {Object}
             */
            validatePassword: function(password) {
                var passwordSetting = app.config.passwordsetting || {};
                var invalidPassword = {
                    isValid: false,
                    error: '',
                };
                // We only validate the password when non-empty password is provided.
                // An empty password would skip this check and return true because
                // it may come from Contacts recordview/preview on Base. In these cases,
                // when no passwords are entered, we don't want to enforce any rules.
                if (password && passwordSetting) {
                    if (app.utils.isPasswordRuleSet(passwordSetting.maxpwdlength) &&
                        password.length > passwordSetting.maxpwdlength) {
                        invalidPassword.error += '<li>' + app.utils.formatString(
                            app.lang.get('LBL_PASSWORD_MAX_LENGTH'),
                            [passwordSetting.maxpwdlength]
                        ) + '</li>';
                    }

                    if (app.utils.isPasswordRuleSet(passwordSetting.minpwdlength) &&
                        password.length < passwordSetting.minpwdlength) {
                        invalidPassword.error += '<li>' + app.utils.formatString(
                            app.lang.get('LBL_PASSWORD_MIN_LENGTH'),
                            [passwordSetting.minpwdlength]
                        ) + '</li>';
                    }

                    if (app.utils.isPasswordRuleSet(passwordSetting.onenumber) && !(/\d+/).test(password)) {
                        invalidPassword.error += '<li>' + app.lang.get('LBL_PASSWORD_ONE_NUMBER') + '</li>';
                    }

                    if (app.utils.isPasswordRuleSet(passwordSetting.onelower) && !(/[a-z]/).test(password)) {
                        invalidPassword.error += '<li>' + app.lang.get('LBL_PASSWORD_ONE_LOWERCASE') + '</li>';
                    }

                    if (app.utils.isPasswordRuleSet(passwordSetting.oneupper) && !(/[A-Z]/).test(password)) {
                        invalidPassword.error += '<li>' + app.lang.get('LBL_PASSWORD_ONE_UPPERCASE') + '</li>';
                    }

                    if (app.utils.isPasswordRuleSet(passwordSetting.onespecial) &&
                        !(/[~!@#$%^&*()_+\-={}|]/).test(password)) {
                        invalidPassword.error += '<li>' + app.lang.get('LBL_PASSWORD_ONE_SPECIAL_CHAR') + '</li>';
                    }

                    // We must NOT match the custom regex
                    if (_.isString(passwordSetting.customregex) && passwordSetting.customregex.length > 0) {
                        var regex = new RegExp(passwordSetting.customregex);
                        if (regex.test(password)) {
                            invalidPassword.error += '<li>' + app.utils.formatString(
                                app.lang.get('LBL_PASSWORD_REGEX_REQUIREMENT'),
                                [passwordSetting.customregex]
                            ) + '</li>';
                        }
                    }

                    if (invalidPassword.error) {
                        invalidPassword.error = '<ul>' + invalidPassword.error + '</ul>';
                        return invalidPassword;
                    }
                }
                return {isValid: true, error: ''};
            },

            /**
             * Create user SRN based on tenant
             *
             * @param {string} userId
             * @return {string}
             */
            createUserSrn: function(userId) {
                let srnParts = app.config.tenant
                    .split(':')
                    .slice(0, 5);
                srnParts.splice(3, 1, '');  // delete region
                srnParts.push('user', userId);
                return srnParts.join(':');
            },

            /**
             * Gets a stringKey value from an string_key input
             * @param {string} str The underscore delimited input string
             * @return {string} The camelCase string output
             */
            getUnderscoreToCamelCaseString: function(str) {
                return str.replace(/_(\D)/g, function(a, b) {
                    return b.toUpperCase();
                });
            },

            /**
             * Converts kebab-case to camelCase
             *
             * @param {string} str String to format
             */
            kebabToCamelCase: function(str) {
                str = str.split('-')
                    .map(_.bind(function(a, i) {
                        return i === 0 ? a : this.capitalize(a);
                    }, this)).join('');

                return str;
            },

            /**
             * Utils for map
             */
            maps: {
                /**
                 * Transforms an array of objects into a simple key-value object
                 *
                 * @param {Array} array
                 * @return {Object}
                 */
                arrayToObject: function(array) {
                    if (!_.isArray(array) || _.isEmpty(array)) {
                        return {};
                    }

                    return array.reduce(function reduceArray(obj, item) {
                        const fieldName = Object.keys(item).pop();

                        obj[fieldName] = item[fieldName];

                        return obj;
                    });
                },

                /**
                 * Check if maps is enabled for the given module
                 *
                 * @param {string} module
                 * @return {bool}
                 */
                isMapsModuleEnabled: function(module) {
                    if (!_.has(app.config, 'maps')) {
                        return false;
                    }

                    if (!_.has(app.config.maps, 'enabled_modules')) {
                        return false;
                    }

                    const allowedModules = app.config.maps.enabled_modules;

                    return _.contains(allowedModules, module);
                }
            },

            /**
             * Utils for map
             */
            reports: {
                /**
                 * Get data series meta for chart
                 *
                 * @param {string} dataSeries
                 * @return {Object|boolean}
                 */
                getDataSeries: function(dataSeries) {
                    if (!_.isString(dataSeries)) {
                        return false;
                    }

                    const dataSeriesParts = dataSeries.split(':');

                    if (!dataSeriesParts || dataSeriesParts.length <= 1) {
                        return false;
                    }

                    const groupFunction = dataSeriesParts.pop();
                    let fieldName;
                    let tableKey;

                    if (groupFunction === 'count') {
                        // function format for count table_key:count
                        fieldName = 'id';
                    } else {
                        // function format for others table_key:field_name:function_name(ex: avg)
                        fieldName = dataSeriesParts.pop();
                    }

                    tableKey = dataSeriesParts.join(':');

                    return {
                        fieldName,
                        tableKey,
                        groupFunction,
                    };
                },

                /**
                 * Check if the current user has access to all the report fields/modules
                 *
                 * @param {Data.Bean|Object} model
                 * @param {string} typeOfAction
                 *
                 * @return {boolean}
                 */
                hasAccessToAllReport: function(model, typeOfAction = 'view') {
                    if (!_.isObject(model)) {
                        return true;
                    }

                    let hasAccess = true;

                    const content = model.get ? model.get('content') : model.content;
                    const reportDef = app.utils.tryParseJSONObject(content);

                    if (!reportDef) {
                        return hasAccess;
                    }

                    if (!app.acl.hasAccess(typeOfAction, reportDef.module)) {
                        hasAccess = false;

                        return hasAccess;
                    }

                    const displayColumnsKey = 'display_columns';
                    const summaryColumnsKey = 'summary_columns';
                    const tableListKey = 'full_table_list';
                    const tableKey = 'table_key';

                    const tables = reportDef[tableListKey];
                    const displayColumns = reportDef[displayColumnsKey] || [];
                    const summaryColumns = reportDef[summaryColumnsKey] || [];

                    if (!tables) {
                        return hasAccess;
                    }

                    const fields = displayColumns.concat(summaryColumns);

                    _.each(fields, function checkACL(fieldData) {
                        const tableName = fieldData[tableKey];
                        const fieldName = fieldData.name;
                        const module = tables[tableName].module;
                        const bean = app.data.createBean(module);

                        const accessToField = app.acl.hasAccessToModel(typeOfAction, bean, fieldName);
                        const accessToModule = app.acl.hasAccess(typeOfAction, module);

                        if (!accessToField || !accessToModule) {
                            hasAccess = false;
                        }
                    }, this);

                    return hasAccess;
                },
            },

            /**
             * Setting fields editability options for IDM and non-IDM
             *
             * @param {Array} fields list of fields objects
             */
            setUsersEditableFields: function(fields, tplName) {
                let idmException = [
                    'email1',
                    'email',
                    'email_addresses',
                    'email_addresses_primary',
                ];
                let readonlyFields = ['is_admin'];
                let vardefFields = app.metadata.getModule('Users', 'fields');
                _.each(fields, function(field) {
                    if (app.config.idmModeEnabled && 'record' === tplName &&
                        _.contains(idmException, field.name)) {
                        return;
                    }

                    if (!app.config.idmModeEnabled) {
                        let adminDeveloperReadonlyFields = ['is_admin'];
                        let regularUserReadonlyFields = [
                            'user_name',
                            'status',
                            'is_admin',
                            'license_type',
                            'employee_status',
                            'title',
                            'department',
                            'reports_to_link',
                            'reportees',
                        ];
                        let adminTypeUser = 'admin';
                        let acls = app.user.getAcls().Users;
                        let isAdmin = !_.has(acls, adminTypeUser);
                        let isDev = !_.has(acls, 'developer');
                        let userType = app.user.get('type');

                        //for Admin & Developer
                        if (isAdmin && isDev && userType !== adminTypeUser &&
                            _.contains(adminDeveloperReadonlyFields, field.name)) {
                            field.readonly = true;
                            return;
                        }

                        //for regular user
                        if (!isAdmin && userType !== adminTypeUser &&
                            _.contains(regularUserReadonlyFields, field.name)) {
                            field.readonly = true;
                            return;
                        }

                        return;
                    }

                    if (_.contains(readonlyFields, field.name) || field.idm_mode_disabled) {
                        field.readonly = true;

                        return;
                    }

                    _.each(vardefFields, function(vardef, index) {
                        if (index === field.name && vardef.idm_mode_disabled) {
                            field.readonly = true;

                            return;
                        }
                    }, this);
                }, this);
            },

            /**
             * Check if field is always readonly
             * @param {Object} fieldDef field definition
             * @param {Object} viewDef (optional) view defintion of the field
             * @return {boolean} true if the field is readonly with no conditions
             */
            isFieldAlwaysReadOnly: function(fieldDef, viewDef) {
                // readonly flag on the viewdef, if present, should always override the readonly flag
                // on vardefs. If vardefs specify a readonly_formula, but viewdefs include a readonly
                // flag with no readonly_formula, then the field should always be readonly.
                if (_.isUndefined(viewDef) || _.isUndefined(viewDef.readonly)) {
                    return app.utils.isTruthy(fieldDef.readonly) && _.isUndefined(fieldDef.readonly_formula);
                } else {
                    return app.utils.isTruthy(viewDef.readonly);
                }
            },

            /**
             * Updates the pendo visitor and account info object
             * @param {Object} addCustomFieldToVisitor The custom metadata to add to visitor object
             * @param {Object} addCustomFieldToAccount The custom metadata to add to account object
             */
            updatePendoMetadata: function(addCustomFieldToVisitor, addCustomFieldToAccount) {
                var pendoObject = app.analytics.connectors.Pendo.getPendoMetadata();
                var updatedVisitorObject = addCustomFieldToVisitor ?
                    _.extend(pendoObject.visitor, addCustomFieldToVisitor) : pendoObject.visitor;
                var updatedAccountObject = addCustomFieldToAccount ?
                    _.extend(pendoObject.account, addCustomFieldToAccount) : pendoObject.account;

                pendo.updateOptions({
                    visitor: updatedVisitorObject,
                    account: updatedAccountObject
                });
            },

            /**
             * Open the focus drawer if it's not already oepned with the same context.
             * @param {Object} focusDrawer - the focus drawer object
             * @param {string} module - module name
             * @param {string} modelId - bean id
             * @param {Object} $el
             */
            openFocusDrawer: function(focusDrawer, module, modelId, $el) {
                if (focusDrawer && module && modelId) {
                    var context = {
                        layout: 'row-model-data',
                        context: {
                            layout: 'focus',
                            module: module,
                            modelId: modelId
                        }
                    };
                    // If the drawer is already open to the same context, don't reload it unnecessarily
                    if (focusDrawer.isOpen() && _.isEqual(context, focusDrawer.currentContextDef)) {
                        return;
                    }
                    focusDrawer.open(context, null, focusDrawer.isOpen(), $el);
                }
            },

            /**
             * Delete attachment from file field, then save the model with
             * appropriate field cleared.
             * @param {Field} field - field from which we remove the file
             * @param {Object} data - data about the file to remove
             * @param {boolean} shouldRender - should this field re-render?
             */
            deleteFileFromField: function(field, data, shouldRender) {
                callbacks = {
                    success: function() {
                        field.model.set(data.field, '');
                        field.model.save({}, {
                            //Show alerts for this request
                            showAlerts: {
                                'process': true,
                                'success': {
                                    messages: app.lang.get('LBL_FILE_DELETED')
                                }
                            },
                            fields: [data.field]
                        });
                        if (self.disposed) {
                            return;
                        }
                        if (shouldRender) {
                            field.render();
                        }
                    },
                    error: function(data) {
                        // refresh token if it has expired
                        app.error.handleHttpError(data, {});
                    }
                };
                app.api.file('delete', data, null, callbacks, {htmlJsonFormat: false});
            },


            /**
             * Checks if the given field can be cascaded to the RLI model on create
             * @param model RLI model
             * @param fieldName name of the cascadable field
             * @param attrs (optional) extra attributes to check
             * @return {boolean}
             */
            isRliFieldValidForCascade: function(model, fieldName, attrs) {
                const getValue = fieldName => {
                    if (attrs && attrs[fieldName]) {
                        return attrs[fieldName];
                    }
                    return model.get(fieldName);
                };

                // Service start date is valid to cascade as long as the RLI is a service
                if (fieldName === 'service_start_date') {
                    return app.utils.isTruthy(getValue('service'));
                }

                // Service duration is valid to cascade if the RLI is a service and not an add on or linked
                // to a product with a locked duration
                if (['service_duration_value', 'service_duration_unit'].includes(fieldName)) {
                    return app.utils.isTruthy(getValue('service')) &&
                        _.isEmpty(getValue('add_on_to_id')) &&
                        !app.utils.isTruthy(getValue('lock_duration'));
                }

                // Forecast is valid to cascade if the user has access to Forecasts and the RLI is not in a
                // closed sales stage
                if (fieldName === 'commit_stage') {
                    if (!app.acl.hasAccess('access', 'Forecasts')) {
                        return false;
                    }

                    let forecastConfig = app.metadata.getModule('Forecasts', 'config');
                    let closedSalesStages = [...forecastConfig.sales_stage_won, ...forecastConfig.sales_stage_lost];
                    return !closedSalesStages.includes(getValue('sales_stage'));
                }

                // For other fields, cascading is fine on create
                return true;
            },
            DocumentMerge: {
                /**
                 * Function that will get you to the Doc Merge documentation
                 *
                 * @param {string} module
                 */
                openHelpDocumentation: function(module) {
                    const serverInfo = app.metadata.getServerInfo();
                    const version = encodeURIComponent(serverInfo.version);
                    const edition = encodeURIComponent(serverInfo.flavor);
                    const lang = encodeURIComponent(app.lang.getLanguage());
                    const products = app.user.getProductCodes().join(',');
                    const key = encodeURIComponent(App.metadata.getConfig().uniqueKey);
                    const devStatus = encodeURIComponent(this.getVersionStatus(version));

                    const url = `http://www.sugarcrm.com/crm/product_doc.php?edition=${edition}&
                    version=${version}&lang=${lang}&module=${module}&products=${products}
                    &status=${devStatus}&key=${key}&help_action=`;

                    window.open(url);
                },

                /**
                 * Given a version such as 5.5.0RC1 return RC. If we have a version such as: 5.5 then return GA
                 *
                 * @param string $version
                 * @return string RC, BETA, GA
                 */
                getVersionStatus: function(version) {
                    const matches = version.match('/^[\d\.]+?([a-zA-Z]+?)[\d]*?$/si');
                    if (_.isArray(matches) && !_.isEmpty(matches)) {
                        return (matches[1]).toUpperCase();
                    } else {
                        return 'GA';
                    }
                },

                /**
                 * @param {string} type
                 * @param {string} name
                 * @param {Array} attributes
                 */
                TagBuilderFactory: function(type, name, attributes) {
                    this.type = type;
                    this.name = name;
                    this.attributes = attributes;

                    /**
                     * base, collection, directive, conditional
                     * @param {string} type
                     */
                    this.getTagBuilder = function getTagBuilder(type) {
                        this.type = type || this.type;
                        let tagBuilder = null;

                        switch (this.type) {
                            case 'base':
                                tagBuilder = new BaseTagBuilder();
                                break;
                            case 'collection':
                                tagBuilder = new CollectionTagBuilder();
                                break;
                            case 'directive':
                                tagBuilder = new DirectiveTagBuilder();
                                break;
                            case 'conditional':
                                tagBuilder = new ConditionalTagBuilder();
                                break;
                        }
                        return tagBuilder;
                    };

                    /**
                     * Factory for creating a specific tag.
                     */
                    function TagFactory() {

                        /**
                         * Gets the specific tag.
                         * @param {string} type
                         */
                        this.getTag = function getTag(type) {
                            let tag = null;

                            switch (type) {
                                case 'base':
                                    tag = new Tag();
                                    break;
                                case 'directive':
                                    tag = new DirectiveTag();
                                    break;
                                case 'conditional':
                                    tag = new ConditionalTag();
                                    break;
                                case 'collection':
                                    tag = new CollectionTag();
                                    break;
                            }

                            return tag;
                        };
                    };

                    /**
                     * Base tag class.
                     */
                    function Tag() {
                        this.type = null;
                        this.name = null;
                        this.attributes = [];
                        this.tagValue = '';

                        /**
                         * Sets the tag name.
                         * @param {string} baseName
                         * @param {string} relateName
                         */
                        this.setName = function setName(baseName, relateName) {
                            this.name = baseName;

                            if (relateName) {
                                this.name += '.' + relateName;
                            }

                            return this;
                        };

                        /**
                         * Sets multiple attributes to the tag.
                         * @param {Object} attributes
                         */
                        this.setAttributes = function setAttributes(attributes) {
                            this.attributes = attributes;

                            return this;
                        };

                        /**
                         * Sets a single attribute to the tag.
                         * @param {Object} attribute
                         */
                        this.setAttribute = function setAttribute(attribute) {
                            _.extend(this.attributes, attribute);

                            return this;
                        };

                        /**
                         * Sets the value of the tag.
                         * @param {string} tagValue
                         */
                        this.setTagValue = function(tagValue) {
                            this.tagValue = tagValue;

                            return this;
                        };

                        /**
                         * Gets the value of the tag.
                         */
                        this.getTagValue = function() {
                            return this.tagValue;
                        };

                        /**
                         * Gets the tag name.
                         */
                        this.getName = function() {
                            return this.name;
                        };

                        /**
                         * Gets the tag attributes.
                         */
                        this.getAttributes = function() {
                            return this.attributes;
                        };

                        /**
                         * Removes a tag attribute.
                         * @param {string} attrName
                         */
                        this.removeAttribute = function(attrName) {
                            delete this.attributes[attrName];
                        };

                        /**
                         * Compiles the tag attributes and sets the tag value.
                         */
                        this.compile = function compile() {
                            let attributes = this.getAttributes();
                            let name = this.getName();
                            let eachAttributes = '';

                            for (let attributeIndex in attributes) {
                                let attributeValue = attributes[attributeIndex];
                                eachAttributes += ' ' + attributeIndex + '=\'' + attributeValue + '\'';
                            }

                            this.setTagValue('{' + name + eachAttributes + '}');

                            return this;
                        };
                    };

                    /**
                     * Collection tag class.
                     */
                    function CollectionTag() {
                        Tag.call(this);

                        /**
                         * Compiles the tag attributes and sets the tag value.
                         */
                        this.compile = function compile() {
                            let attributes = this.getAttributes();
                            let name = this.getName();
                            let eachAttributes = '';
                            let fieldTags = this.getFields() || '';

                            for (let attributeIndex in attributes) {
                                let attributeValue = attributes[attributeIndex];
                                eachAttributes += ' ' + attributeIndex + '=\'' + attributeValue + '\'';
                            }

                            let startingCollectionTag = `{#${name} ${eachAttributes}}`;
                            let endingCollectionTag = `{/${name}}`;

                            this.setCollectionTags(startingCollectionTag, endingCollectionTag);
                            this.setTagValue(startingCollectionTag + fieldTags + endingCollectionTag);

                            return this;
                        };

                        /**
                         * Sets the collection table fields
                         *1
                         * @param {string} fieldName
                         * @param {string} fieldLabel
                         */
                        this.setCollectionTableFields = function(fieldName, fieldLabel) {
                            let field = {
                                name: `{${fieldName}}`,
                                label: fieldLabel,
                            };

                            this.collectionTags.fields = this.collectionTags.fields || [];
                            this.collectionTags.fields.push(field);

                            return this;
                        };

                        /**
                         * Sets the collection tags.
                         *
                         * @param {string} startingCollectionTag
                         * @param {string} endingCollectionTag
                         */
                        this.setCollectionTags = function(startingCollectionTag, endingCollectionTag) {
                            this.fieldTags = {};

                            this.collectionTags = {
                                startingCollectionTag: startingCollectionTag,
                                endingCollectionTag: endingCollectionTag,
                            };

                            return this;
                        };

                        /**
                         * Gets the collection tags.
                         */
                        this.getCollectionTags = function() {
                            return this.collectionTags;
                        };

                        /**
                         * Sets the fields inside the collection tag.
                         * @param {Array} fields
                         */
                        this.setFields = function setFields(fields) {
                            this.fieldTags = '';

                            for (let field of fields) {
                                this.fieldTags = this.fieldTags + '{' + field + '}';
                            }

                            return this;
                        };

                        /**
                         * Gets the fields inside the collection.
                         */
                        this.getFields = function getFields() {
                            return this.fieldTags;
                        };
                    };

                    /**
                     * Directive tag class.
                     */
                    function DirectiveTag() {
                        Tag.call(this);

                        /**
                         * Compiles the tag attributes and sets the tag value.
                         */
                        this.compile = function compile() {
                            let attributes = this.getAttributes();
                            let name = this.getName();
                            let eachAttributes = '';

                            for (let attributeIndex in attributes) {
                                let attributeValue = attributes[attributeIndex];
                                eachAttributes += ' ' + attributeIndex + '=\'' + attributeValue + '\'';
                            }

                            this.setTagValue('{!' + name + eachAttributes + '}');

                            return this;
                        };
                    };

                    /**
                     * Conditional tag class.
                     */
                    function ConditionalTag() {
                        Tag.call(this);

                        /**
                         * Compiles the tag attributes and sets the tag value.
                         */
                        this.compile = function compile() {
                            let attributes = this.getAttributes();
                            let tag = '';

                            for (let key in attributes) {
                                if (key === 'if' || key === 'else') {
                                    let data = attributes[key];
                                    tag += '{' + key + ' ' + data.condition + '}' + data.result;
                                }
                                if (key === 'elseifs') {
                                    for (let elseif of attributes[key]) {
                                        tag += '{elseif ' + elseif.condition + '}' + elseif.result;
                                    }
                                }
                            }

                            this.setTagValue(tag + '{endif}');

                            return this;
                        };
                    };

                    /**
                     * Builder for the base tag.
                     * @param {strig} name
                     * @param {Object} attributes
                     */
                    function BaseTagBuilder(name, attributes) {
                        this.name = name;
                        this.attributes = attributes || {};
                        this.tag = null;
                        this.type = this.type || 'base';

                        /**
                         * Creates a new tag based on the type.
                         */
                        this.newTag = function newTag() {
                            let tagFactory = new TagFactory();
                            this.tag = tagFactory.getTag(this.type);
                            return this;
                        };

                        /**
                         * Sets the tag name.
                         * @param {string} name
                         */
                        this.setName = function setName(name) {
                            this.tag.setName(name);
                            return this;
                        };

                        /**
                         * Sets a single attribute to the tag.
                         * @param {Object} attribute
                         */
                        this.setAttribute = function setAttribute(attribute) {
                            this.tag.setAttribute(attribute);
                            return this;
                        };

                        /**
                         * Sets multiple attributes to the tag.
                         * @param {Object} attributes
                         */
                        this.setAttributes = function setAttributes(attributes) {
                            this.tag.setAttributes(attributes);
                            return this;
                        };

                        /**
                         * Gets the tag.
                         */
                        this.get = function get() {
                            return this.tag;
                        };
                    };

                    /**
                     * Builder for the collection tag.
                     *
                     * @param {string} name
                     * @param {Object} attributes
                     */
                    function CollectionTagBuilder(name, attributes) {
                        BaseTagBuilder.call(this, name, attributes);

                        this.type = 'collection';
                        this.collectionTags = {
                            startingCollectionTag: '',
                            endingCollectionTag: '',
                            fields: []
                        };

                        /**
                         * Sets the tag name.
                         * @param {string} name
                         */
                        this.setName = function(name) {
                            this.tag.setName('#' + name);

                            return this;
                        };
                    };

                    /**
                     * Builder for the directive tag.
                     *
                     * @param {string} name
                     * @param {Object} attributes
                     */
                    function DirectiveTagBuilder(name, attributes) {
                        this.type = 'directive';

                        /**
                         * Sets the tag name.
                         * @param {string} name
                         */
                        this.setName = function(name) {
                            this.tag.setName('!' + name);

                            return this;
                        };

                        BaseTagBuilder.call(this, name, attributes);
                    };

                    /**
                     * Builder for the conditional tag.
                     *
                     * @param {string} name
                     * @param {Object} attributes
                     */
                    function ConditionalTagBuilder(name, attributes) {
                        BaseTagBuilder.call(this, name, attributes);

                        this.type = 'conditional';
                    };
                },
            },

            /**
             * Returns True if Dark Mode is active
             *
             * @param parent (optional) boolean value for looking at window.parent instead of current window.
             * Used in areas that are loaded in an iframe.
             * @return {boolean} True if Dark Mode is active
             */
            isDarkMode: function(parent = false) {
                let bodyHasDarkModeClass = null;

                if (app.utils.isTruthy(parent)) {
                    bodyHasDarkModeClass = window.parent.document.body.classList.contains('sugar-dark-theme');
                } else {
                    bodyHasDarkModeClass = document.body.classList.contains('sugar-dark-theme');
                }

                return bodyHasDarkModeClass || localStorage.getItem('last_appearance_preference') === 'dark';
            },

            /**
             * Get color approach to white or to black
             *
             * @param {string} color Hex color
             * @return {boolean}
             */
            isWhiteColor: function(color) {
                color = color.substring(1); // strip #
                var rgb = parseInt(color, 16); // convert rrggbb to decimal
                var r = (rgb >> 16) & 0xff; // extract red
                var g = (rgb >> 8) & 0xff; // extract green
                var b = (rgb >> 0) & 0xff; // extract blue

                var luma = 0.2126 * r + 0.7152 * g + 0.0722 * b; // per ITU-R BT.709

                if (luma < 100) {
                    return false;
                }
                return true;
            },

            /**
             * Safe parse JSON string
             *
             * @param {string} jsonString
             */
            tryParseJSONObject: function(jsonString) {
                try {
                    const response = JSON.parse(jsonString);

                    if (response && _.isObject(response)) {
                        return response;
                    }
                }
                catch (e) { }

                return false;
            },
        });
    });
})(SUGAR.App);
