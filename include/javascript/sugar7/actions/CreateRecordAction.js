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
(function register(app) {
    app.events.on('app:init', function init() {
        /**
         * Create Record Action
         *
         * @class Core.Actions.CreateRecordAction
         * @alias SUGAR.App.Actions.CreateRecordAction
         *
         * @param {Object} def Action Definition
         */
        function CreateRecord(def) {
            this.def = def;
            this.deniedFields = [
                'id',
                '_acl',
                '_hash',
                '_module',
                'user_sync',
                'created_by',
                'modified_by_name',
                'modified_user_id',
                'modified_user_link',
                'created_by_link',
                'created_by_name',
                'date_entered',
                'date_modified',
                'locked_fields',
                '_erased_fields',
                'acl_team_names',
                'acl_team_set_id',
                'created_by_link',
                'sync_key',
                'team_count_link'
            ];
        }

        /**
         * Open up the sidecar create drawer, or immediately create the record through API, based on configuration.
         *
         * @param {Data.Bean} opts.recordModel Current bean model
         * @param {View.Views.Base.RecordView} opts.recordView Source action record view
         * @param {Function} opts.createLinkModelFct Callback to create a linked record
         * @param {Object} currentExecution Queue of actions to be executed in the current context
         *
         */
        CreateRecord.prototype.run = function(opts, currentExecution) {
            if (this.hasEditAccess(this.def.properties.module)) {
                this._createRecord(opts, currentExecution);
            } else {
                this.noAccessAlert(currentExecution);
            }
        };

        /**
         * Actual logic for record creation
         *
         * @param {Data.Bean} opts.recordModel Current bean model
         * @param {View.Views.Base.RecordView} opts.recordView Source action record view
         * @param {Function} opts.createLinkModelFct Callback to create a linked record
         * @param {Object} currentExecution Queue of actions to be executed in the current context
         * @private
         *
         */
        CreateRecord.prototype._createRecord = function(opts, currentExecution) {
            const parentRecordModel = opts.recordModel;
            const parentRecordView = opts.recordView;
            const actionProperties = this.def.properties;
            const module = actionProperties.module;
            const shouldLink = actionProperties.mustLinkRecord;
            const relLink = actionProperties.link;

            var bean = null;

            if (shouldLink) {
                bean = opts.createLinkModelFct(parentRecordModel, relLink);
            } else {
                bean = app.data.createBean(module);
            }

            if (actionProperties.copyFromParent) {
                bean = this._populateModelFromParent(bean, parentRecordModel);
            }

            bean = this._populateModelWithParentAttributes(
                bean,
                parentRecordModel,
                actionProperties.parentAttributes
            );

            this._resolveModelWithCustomAttributes(
                bean,
                parentRecordView,
                actionProperties,
                opts,
                currentExecution
            );
        };

        /**
         * We have to check if there is at last one calculated field to see
         * if we need to make a request to the backend
         *
         * @param {Object} attributes Bean field value mapping object
         *
         * @return {bool}
         */
        CreateRecord.prototype._hasAsyncLogic = function(attributes) {
            const calculatedFields = _.filter(attributes,
                function checkCalculatedFields(item) {
                    return item.isCalculated || (_.has(item, 'required_formula') && item.required_formula);
                }
            );

            return calculatedFields.length > 0;
        };

        /**
         * Fill in custom attributes on the bean
         *
         * @param {Data.Bean} newBean New record bean
         * @param {View.Views.Base.RecordView} parentRecordView Record view of source button action
         * @param {Object} actionProperties Action definition
         * @param {Object} actionProperties.attributes Bean field value mapping object
         * @param {Data.Bean} opts.recordModel Current bean model
         * @param {View.Views.Base.RecordView} opts.recordView Source action record view
         * @param {Function} opts.createLinkModelFct Callback to create a linked record
         * @param {Object} currentExecution Queue of actions to be executed in the current context
         *
         */
        CreateRecord.prototype._resolveModelWithCustomAttributes = function(
            newBean, parentRecordView, actionProperties, opts, currentExecution) {

            const targetFields = actionProperties.attributes;
            const module = actionProperties.module;
            let recordViewFields = [];

            if (module === parentRecordView.module) {
                recordViewFields = parentRecordView.getFields(module, opts.recordModel);
            } else {
                recordViewFields = this._getRecordViewFields(module);
            }

            const requiredFields = this._getRequiredFieldsWithFormula(recordViewFields);
            _.each(requiredFields, function(field) {
                const fN = field.name;
                const tField = targetFields[fN];
                if (tField && tField.value && tField.value[fN] && !newBean.get(fN)) {
                    newBean.set(fN, tField.value[fN]);
                }

                targetFields[fN] = field;
            });

            if (this._hasAsyncLogic(targetFields)) {
                this._populateAsyncModelWithCustomAttributes(
                    newBean, parentRecordView, actionProperties, opts, currentExecution, recordViewFields);
            } else {
                this._populateSyncModelWithCustomAttributes(
                    newBean,
                    parentRecordView,
                    actionProperties,
                    targetFields,
                    opts,
                    currentExecution,
                    recordViewFields
                    );
            }
        };

        /**
         * Fill in model attributes without reaching out to the rest API to resolve calculated values.
         *
         * @param {Data.Bean} newBean New Bean
         * @param {View.Views.Base.RecordView} parentRecordView Record view of source button action
         * @param {Object} actionProperties Action definition
         * @param {Object} actionProperties.attributes Bean field value mapping object
         * @param {Data.Bean} opts.recordModel Current bean model
         * @param {View.Views.Base.RecordView} opts.recordView Source action record view
         * @param {Function} opts.createLinkModelFct Callback to create a linked record
         * @param {Object} currentExecution Queue of actions to be executed in the current context
         *
         */
        CreateRecord.prototype._populateSyncModelWithCustomAttributes = function(
            newBean,
            parentRecordView,
            actionProperties,
            targetFields,
            opts,
            currentExecution,
            recordViewFields = []
        ) {
            const module = actionProperties.module;

            newBean = this._populateModelWithCustomAttributes(
                newBean,
                targetFields
            );

            if (actionProperties.autoCreate) {
                this._trySetTeam(newBean);

                var validationErrors = {};

                newBean.on('error:validation', function handleValidationError(errors) {
                    validationErrors = errors;
                });

                newBean.doValidate(recordViewFields, _.bind(function modelValidated(isValid) {
                    if (isValid) {
                        newBean.save({}, {
                            showAlerts: false,
                            success: _.bind(function successSave(model, changed) {
                                app.alert.show('create-success', {
                                    level: 'success',
                                    messages: this._displayMessage(model, module),
                                    autoClose: true,
                                    autoCloseDelay: 10000,
                                    onLinkClick: function() {
                                        app.alert.dismiss('create-success');
                                    }
                                });

                                this._refreshCollection(parentRecordView, actionProperties);

                                currentExecution.nextAction();
                            }, this)
                        });
                    } else {
                        var fields = _.chain(validationErrors)
                            .keys()
                            .map(function getFieldLabel(f) {
                                return '<li>' + app.lang.get(recordViewFields[f].vname, newBean.module) + '</li>';
                            })
                            .value();

                        var msg = _.union([app.lang.get('LBL_ACTIONBUTTON_INVALID_FIELD_VALUES')], fields);

                        app.alert.show('invalid-data', {
                            level: 'error',
                            messages: msg
                        });

                        currentExecution.nextAction();
                    }
                }, this));
            } else {
                if (module === 'Quotes') {
                    this._navigateCreateQuote(newBean, module, parentRecordView);
                } else {
                    this._openCreateDrawer(newBean, module, parentRecordView, actionProperties);
                }

                currentExecution.nextAction();
            }
        };

        /**
         * Gets all the fields from the record view
         *
         * @param {string} module
         *
         * @return {Array}
         */
        CreateRecord.prototype._getRecordViewFields = function(module) {
            let fields = [];
            let fieldDefs = [];

            const moduleMeta = app.metadata.getView(module, 'record');

            if (moduleMeta && moduleMeta.panels) {
                const fieldNames = _.map(moduleMeta.panels, function(panel) {
                    const panelFields = panel.fields;
                    const nestedFieldNames = _.chain(panelFields)
                        .pluck('fields')
                        .compact()
                        .flatten()
                        .pluck('name')
                        .value();
                    const panelFieldNames = _.pluck(panelFields, 'name');
                    const relatedFields = _.chain(panelFields).pluck('related_fields').compact().flatten().value();

                    return [...nestedFieldNames, ...panelFieldNames, ...relatedFields];
                });

                fields = _.reduce(fieldNames, function(memo, tempFields) {
                    return memo.concat(tempFields);
                });
            }

            fields = _.chain(fields).uniq().compact().value();

            const fieldMetadata = app.metadata.getModule(module, 'fields');
            if (fieldMetadata) {
                fields = _.reject(fields, function(name) {
                    return _.isUndefined(fieldMetadata[name]);
                });

                let relates = [];
                _.each(fields, function(name) {
                    const field = fieldMetadata[name];

                    if (field.type === 'relate') {
                        relates.push(field.id_name);
                    } else if (field.type === 'parent') {
                        relates.push(field.id_name);
                        relates.push(field.type_name);
                    }

                    if (_.isArray(field.fields)) {
                        relates = relates.concat(field.fields);
                    }
                });

                fields = _.union(fields, relates);
            }

            _.each(fields, function(fieldName) {
                fieldDefs.push(app.metadata.getField({module: module, name: fieldName}));
            });

            return fieldDefs;
        };

        /**
         * Gets the fields that have required_formula
         *
         * @param {Array|Object} fields
         *
         * @return {Array}
         */
        CreateRecord.prototype._getRequiredFieldsWithFormula = function(fields) {
            let requiredFields = [];

            _.each(fields, function(field) {
                if (_.has(field, 'required_formula') && field.required_formula) {
                    requiredFields.push(field);
                }
            });

            return requiredFields;
        };

        /**
         * Try to set the default bean team, if it's missing from the set attributes
         *
         * @param {Data.Bean} bean
         *
         */
        CreateRecord.prototype._trySetTeam = function(bean) {
            if (_.isEmpty(bean.get('team_name'))) {
                var teams = app.utils.deepCopy(app.user.getPreference('default_teams'));
                bean.set('team_name', teams);
                bean.set('team_count', teams.length);
            }

            if (!_.isEmpty(bean.get('team_name')) && _.isEmpty(bean.get('team_count'))) {
                let teams = bean.get('team_name');
                bean.set('team_count', teams.length);
            }
        };

        /**
         * Send user to the Create Quote view, rather than open a drawer
         *
         * @param {Data.Bean} newBean New Model Bean
         * @param {string} module New Model target module
         * @param {View.View} parentView Source action record view
         */
        CreateRecord.prototype._navigateCreateQuote = function(bean, module, parentRecordView) {
            var loadViewObj = {
                module: 'Quotes',
                layout: 'create',
                action: 'edit',
                convert: false,
                create: true,
                model: bean,
                collection: app.data.createBeanCollection('Quotes')
            };
            app.controller.loadView(loadViewObj);
            app.router.navigate('#Quotes/create', {
                trigger: false
            });
        };

        /**
         * Opens up a create drawer with a pre-filled bean
         *
         * @param {Data.Bean} newBean New Model Bean
         * @param {string} module New Model target module
         * @param {View.View} parentView Source action record view
         * @param {Object} actionProperties Action definition
         *
         */
        CreateRecord.prototype._openCreateDrawer = function(newBean, module, parentView, actionProperties) {
            app.drawer.open({
                layout: 'create',
                context: {
                    create: true,
                    model: newBean,
                    module: module,
                    parentView: parentView
                }
            }, _.bind(function drawerClose(context, model) {
                if (!model) {
                    return;
                }

                if (parentView.type === 'dashlet-toolbar') {
                    parentView.refreshClicked();
                } else {
                    let context = parentView.context;

                    context.set('skipFetch', false);
                    context.reloadData();

                    this._refreshCollection(parentView, actionProperties);
                }
            },this));
        };

        /**
         * Fill in model attributes by reaching out to the rest API to resolve calculated values.
         *
         * @param {Data.Bean} newBean Current record bean
         * @param {View.Views.Base.RecordView} parentRecordView Record view of source button action
         * @param {Object} actionProperties Action definition
         * @param {Object} actionProperties.attributes Bean field value mapping object
         * @param {Data.Bean} opts.recordModel Current bean model
         * @param {View.Views.Base.RecordView} opts.recordView Source action record view
         * @param {Function} opts.createLinkModelFct Callback to create a linked record
         * @param {Object} currentExecution Queue of actions to be executed in the current context
         *
         */
        CreateRecord.prototype._populateAsyncModelWithCustomAttributes = function(
            newBean,
            parentRecordView,
            actionProperties,
            opts,
            currentExecution,
            recordViewFields
        ) {
            let targetFields = actionProperties.attributes;

            const recordModule = parentRecordView.model.module;
            const recordId = parentRecordView.model.id;

            const requestType = 'create';
            const apiPath = 'actionButton/evaluateExpression';

            const calculatedFields = _.pick(targetFields, function pickCalculated(item) {
                return item.isCalculated || (_.has(item, 'required_formula') && item.required_formula);
            });

            const simpleFields = _.pick(targetFields, function pickCalculated(item) {
                return !item.isCalculated && (!_.has(item, 'required_formula') && !item.required_formula);
            });

            newBean = this._populateModelWithCustomAttributes(
                newBean,
                simpleFields
            );

            const beanFieldsData = newBean.attributes;

            let requestMeta = {
                targetFields: calculatedFields,
                targetRecordId: recordId,
                targetModule: recordModule,
                beanData: beanFieldsData,
            };

            const apiCallbacks = {
                success: _.bind(function calculatedFormula(result) {
                    _.each(result, function addNewValues(item, key) {
                        if (typeof (targetFields[key]) === 'string') {
                            targetFields = {};
                        }

                        const value = {};

                        value[key] = item;

                        targetFields[key] = {
                            fieldName: key,
                            isCalculated: false,
                            formula: '',
                            value: value
                        };
                    }, this);

                    this._populateSyncModelWithCustomAttributes(
                        newBean,
                        parentRecordView,
                        actionProperties,
                        targetFields,
                        opts,
                        currentExecution,
                        recordViewFields
                    );
                }, this)
            };

            const apiUrl = app.api.buildURL(apiPath, requestType, requestMeta, {});

            app.api.call(requestType, apiUrl, requestMeta, null, apiCallbacks);
        };

        /**
         * Populate model with static field values
         *
         * @param {Data.Bean} newBean New model bean
         * @param {Array} targetFields Record fields with values to be set on the model
         *
         */
        CreateRecord.prototype._populateModelWithCustomAttributes = function(
            newBean,
            targetFields
        ) {
            let fieldValues = _.chain(targetFields)
                .values()
                .map(a => this.normalizeFieldValue(a, newBean))
                .flatten(1)
                .map(a => _.pairs(a))
                .flatten(1)
                .object()
                .value();

            newBean.set(fieldValues);

            return newBean;
        };

        /**
         * Clone a field value from a parent record, keeping in consideration
         * any of the field dependencies
         *
         * @param {Object} fieldInfo
         * @param {Data.Bean} record
         * @param {Data.Bean} parent
         *
         */
        CreateRecord.prototype._cloneParentFieldValue = function(fieldInfo, record, parent) {
            const recordField = fieldInfo.fieldName;
            const parentField = fieldInfo.parentFieldName;
            const recordFieldDef = record.fields[recordField];
            const parentFieldDef = parent.fields[parentField];

            let value = {};

            if (this.deniedFields.indexOf(recordField) !== -1) {
                return value;
            };

            if (this._validateFieldsRelationship(recordFieldDef, parentFieldDef, 'relate', 'relate')) {
                value = this._getParentFieldValues(recordField, parentField, recordFieldDef, parentFieldDef, parent);
            } else if (this._validateFieldsRelationship(recordFieldDef, parentFieldDef, 'parent', 'relate')) {
                value = this._getParentFieldValues(recordField, parentField, recordFieldDef, parentFieldDef, parent);
                value.parent_type = parentFieldDef.module;
            } else {
                value[recordField] = parent.get(parentField);
            };

            return value;
        };

        /**
         * Gets values from parent field
         *
         * @param {string} recordField
         * @param {string} parentField
         * @param {Object} recordFieldDef
         * @param {Object} parentFieldDef
         * @param {Data.Bean} parent
         *
         */
        CreateRecord.prototype._getParentFieldValues = function(
            recordField,
            parentField,
            recordFieldDef,
            parentFieldDef,
            parent
        ) {
            let value = {};
            value[recordField] = parent.get(parentField);
            value[recordFieldDef.id_name] = parent.get(parentFieldDef.id_name);

            return value;
        };

        /**
         * Validates the relationship between record and parent fields
         *
         * @param {Object} recordFieldDef
         * @param {Object} parentFieldDef
         * @param {string} recordFieldType
         * @param {string} parentFieldType
         *
         */
        CreateRecord.prototype._validateFieldsRelationship = function(
            recordFieldDef,
            parentFieldDef,
            recordFieldType,
            parentFieldType
        ) {
            return recordFieldDef.type === recordFieldType &&
                parentFieldDef.type === parentFieldType &&
                !_.isEmpty(recordFieldDef.id_name) &&
                !_.isEmpty(parentFieldDef.id_name);
        };

        /**
         * Populate model with parent record's selected field values
         *
         * @param {Data.Bean} newBean New model bean
         * @param {Data.Bean} parentRecordModel Current record model bean
         * @param {Array} targetFields Record fields with values to be set on the model
         *
         * @return {Data.Bean}
         */
        CreateRecord.prototype._populateModelWithParentAttributes = function(
            newBean,
            parentRecordModel,
            targetFields
        ) {
            let fieldValues = _.chain(targetFields)
                .values()
                .map(a => this._cloneParentFieldValue(a, newBean, parentRecordModel))
                .flatten(1)
                .map(a => _.pairs(a))
                .flatten(1)
                .object()
                .value();

            newBean.set(fieldValues);

            return newBean;
        };

        /**
         * Populate model with all of the parent record's compatible fields values
         *
         * @param {Data.Bean} newBean New model bean
         * @param {Data.Bean} parentRecordModel Current record model bean
         *
         * @return {Data.Bean}
         */
        CreateRecord.prototype._populateModelFromParent = function(
            newBean,
            parentRecordModel
        ) {
            let fieldValues = _.chain(newBean.fields)
                .filter(function filterCloneableFields(f) {
                    return (
                        this.deniedFields.indexOf(f.name) === -1 &&
                        !_.isEmpty(parentRecordModel.fields[f.name]) &&
                        f.type === parentRecordModel.fields[f.name].type &&
                        !_.isUndefined(parentRecordModel.get(f.name))
                    );
                }, this)
                .map(function createCloneTuple(f) {
                    return [f.name, parentRecordModel.get(f.name)];
                })
                .object()
                .value();

            newBean.set(fieldValues);

            return newBean;
        };

        /**
         * Refresh related collections in parent view. (eg. Subpanels)
         *
         * @param {View.Views.Base.RecordView} parentRecordView Record view of source button action
         * @param {Object} actionProperties Action definition
         * @param {Object} actionProperties.attributes Bean field value mapping object
         *
         */
        CreateRecord.prototype._refreshCollection = function(parentRecordView, actionProperties) {
            if (actionProperties.mustLinkRecord) {
                var collection = parentRecordView.model.getRelatedCollection(actionProperties.link);

                collection.fetch({relate: true});
            }
        };

        /**
         * Return record saved dialog message based on wether new record is related to current record or not.
         *
         * @param {Data.Bean} model Model being saved
         * @param {string} module Module of model being saved
         *
         */
        CreateRecord.prototype._displayMessage = function(model, module) {
            var modelAttributes;
            var successLabel = 'LBL_RECORD_SAVED_SUCCESS';
            var successMessageContext;

            //if we have model attributes, use them to build the message, otherwise use a generic message
            if (model && model.attributes) {
                modelAttributes = model.attributes;

                if (model.get('no_success_label_link')) {
                    successLabel = 'LBL_RECORD_SAVED_SUCCESS_NO_LINK';
                }
            } else {
                modelAttributes = {};
                successLabel = 'LBL_RECORD_SAVED';
            }

            //use the model attributes combined with data from the view to build the success message context
            successMessageContext = _.extend({
                module: module,
                moduleSingularLower: app.lang.getModuleName(module).toLowerCase()
            }, modelAttributes);

            return app.lang.get(successLabel, module, successMessageContext);
        };

        /**
         * Check wether the current user has access to the module or not
         *
         * @param {string} module
         *
         * @return {bool}
         */
        CreateRecord.prototype.hasEditAccess = function(module) {
            const access = app.acl.hasAccess('edit', module);

            return access;
        };

        /**
         * Display the no-access message alert
         *
         * @param {Object} currentExecution Queue of actions to be executed in the current context
         *
         */
        CreateRecord.prototype.noAccessAlert = function(currentExecution) {
            app.alert.show('alert_no_access', {
                level: 'error',
                title: app.lang.get('ERR_NO_VIEW_ACCESS_TITLE'),
                messages: app.lang.get('ERR_NO_VIEW_ACCESS_ACTION'),
                autoClose: true,
                autoCloseDelay: 5000
            });

            currentExecution.nextAction();
        };

        /**
         * Process actionbutton fullname update value
         *
         * @param {Object} fieldDef SugarCRM field definition
         * @param {Object} fieldData ActionButton field update data
         *
         * @return {Object} Sidecar model update data
         */
        CreateRecord.prototype.normalizeFullameValue = function(fieldDef, fieldData) {
            if (_.isString(fieldData.value[fieldData.fieldName]) &&
                _.isUndefined(fieldData.value.first_name) &&
                _.isUndefined(fieldData.value.last_name)) {
                // Handling calculated value for full name, so we need to split it to a first_name/last_name
                // value pair and apply it to the record.
                let nameParts = fieldData.value[fieldData.fieldName].split(' ');
                let lastName = nameParts.pop().trim();
                let firstName = nameParts.join(' ').trim();

                return {
                    first_name: firstName,
                    last_name: lastName
                };
            } else {
                return fieldData.value;
            }
        };

        /**
         * Process actionbutton datetime update value
         *
         * @param {Object} fieldDef SugarCRM field definition
         * @param {Object} fieldData ActionButton field update data
         *
         * @return {Object} Sidecar model update data
         */
        CreateRecord.prototype.normalizeDatetimeValue = function(fieldDef, fieldData) {
            if (_.isString(fieldData.value[fieldData.fieldName])) {
                let value = {};

                const userDateTimeFormat = `${app.date.getUserDateFormat()} ${app.date.getUserTimeFormat()}`;
                const dateValue = fieldData.value[fieldData.fieldName];
                const date = app.date(dateValue, userDateTimeFormat);

                if (date.isValid()) {
                    value[fieldData.fieldName] = date.format();
                } else {
                    value[fieldData.fieldName] = app.date.utc(fieldData.value[fieldData.fieldName]).format();
                }

                return value;
            } else {
                return fieldData.value;
            }
        };

        /**
         * Process actionbutton relate update value
         *
         * @param {Object} fieldDef SugarCRM field definition
         * @param {Object} fieldData ActionButton field update data
         *
         * @return {Object} Sidecar model update data
         */
        CreateRecord.prototype.normalizeRelateValue = function(fieldDef, fieldData) {
            let idName = fieldDef.id_name;

            if (!_.isEmpty(fieldData.value[idName])) {
                return fieldData.value;
            } else {
                let value = {};

                value[fieldData.fieldName] = fieldData.value[fieldData.fieldName];
                value[idName] = fieldData.value[fieldData.fieldName];

                return value;
            }
        };

        /**
         * Process actionbutton parent update value
         *
         * @param {Object} fieldDef SugarCRM field definition
         * @param {Object} fieldData ActionButton field update data
         *
         * @return {Object} Sidecar model update data
         */
        CreateRecord.prototype.normalizeParentValue = function(fieldDef, fieldData) {
            let typeName = fieldDef.type_name;
            let idName = fieldDef.id_name;

            let value = {};

            if (_.isObject(fieldData.value.parent)) {
                // sourcing data from the trimmed down parent record object
                value[fieldData.fieldName] = fieldData.value.parent.name;
                value[typeName] = fieldData.value.parent._module;
                value[idName] = fieldData.value.parent.id;
            } else if (!_.isEmpty(fieldData.value[typeName]) && !_.isEmpty(fieldData.value[idName])) {
                // no trimmed down record, but we have the actual field data
                value[fieldData.fieldName] = fieldData.value[fieldData.fieldName];
                value[typeName] = fieldData.value[typeName];
                value[idName] = fieldData.value[idName];
            } else {
                // no actual field data, formula must have been used,
                // need to decode it from a bar separate format, eg:
                // <module>|<id>|<name>
                let parentParts = fieldData.value[fieldData.fieldName].split('|');
                let recordModule = (parentParts.shift() || '').trim();
                let recordId = (parentParts.shift() || '').trim();
                let recordName = (parentParts.join('') || '').trim();

                value[fieldData.fieldName] = recordName;
                value[typeName] = recordModule;
                value[idName] = recordId;
            }

            return value;
        };

        /**
         * Process actionbutton tag update value
         *
         * @param {Object} fieldDef SugarCRM field definition
         * @param {Object} fieldData ActionButton field update data
         *
         * @return {Object} Sidecar model update data
         */
        CreateRecord.prototype.normalizeTagValue = function(fieldDef, fieldData) {
            if (_.isString(fieldData.value[fieldData.fieldName])) {
                let value = {};

                value[fieldData.fieldName] = fieldData.value[fieldData.fieldName].split(',');

                return value;
            } else {
                return fieldData.value;
            }
        };

        /**
         * Certain field values need to be processed a bit before being applied to the model.
         *
         * @param {Object} fieldData
         * @param {Data.Bean} model
         * @return {Array}
         */
        CreateRecord.prototype.normalizeFieldValue = function(fieldData, model) {
            let result = [];
            let fieldDef = model.fields[fieldData.fieldName];

            switch (fieldDef.type) {
                case 'fullname':
                    result.push(this.normalizeFullameValue(fieldDef, fieldData));
                    break;
                case 'datetime':
                case 'datetimecombo':
                    result.push(this.normalizeDatetimeValue(fieldDef, fieldData));
                    break;
                case 'relate':
                    result.push(this.normalizeRelateValue(fieldDef, fieldData));
                    break;
                case 'parent':
                    result.push(this.normalizeParentValue(fieldDef, fieldData));
                    break;
                case 'tag':
                    result.push(this.normalizeTagValue(fieldDef, fieldData));
                    break;
                default:
                    result.push(fieldData.value);
                    break;
            }

            return result;
        };

        app.actions = app.actions || {};

        app.actions = _.extend(app.actions, {
            CreateRecord: CreateRecord
        });
    });
})(SUGAR.App);
