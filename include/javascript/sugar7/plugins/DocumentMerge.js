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
        app.plugins.register('DocumentMerge', ['view'], {

            /**
             * Modules where the merging buttons should not appear
             *
             * @var array
             */
            modulesDenyList: ['Calendar', 'DocuSignEnvelopes',],

            /**
             * @inheritdoc
             *
             * Add the Template Merging Buttons
             */
            onAttach: function(component, plugin) {
                this.on('init', function() {
                    //Append the buttons only in these views
                    this.templatesModule = 'DocumentTemplates';

                    this.acceptedViews = {
                        'record': 'record',
                        'recordlist': 'recordlist',
                        'subpanellist': 'subpanel-list'
                    };

                    this._addDocumentMergeButtons();
                }, this);
            },

            /**
             * @inheritdoc
             *
             * Clean up associated event handlers.
             */
            onDetach: function(component, plugin) {
                this.context.off('button:merge_template_pdf:click', this.mergeTemplate, this);
                this.context.off('button:merge_template:click', this.mergeTemplate, this);
            },

            /**
             * Takes care of adding the merge document buttons
             */
            _addDocumentMergeButtons: function() {
                let isUsersSubpanel = this.context.get('isSubpanel') && this.context.get('parentModule') === 'Users';

                /**
                 * Merge buttons meta
                 */
                let mergeButtonsMeta = [
                    {
                        'type': 'rowaction',
                        'event': 'button:merge_template:click',
                        'name': 'merge_template',
                        'icon': 'sicon-document-lg',
                        'label': 'LBL_MERGE_TEMPLATE_BUTTON_LABEL',
                        'acl_action': isUsersSubpanel ? 'admin' : 'view',
                    },
                    {
                        'type': 'rowaction',
                        'event': 'button:merge_template_pdf:click',
                        'name': 'merge_template_pdf',
                        'label': 'LBL_MERGE_TEMPLATE_PDF_BUTTON_LABEL',
                        'acl_action': isUsersSubpanel ? 'admin' : 'view',
                    },
                    {
                        'type': 'divider',
                    }
                ];

                let viewName = this.name;
                let dropdown = this.getDropdown(viewName);

                if (_.isArray(dropdown) && this.hasAccess() && !this.modulesDenyList.includes(this.module)) {
                    dropdown.push(...mergeButtonsMeta);
                    /**
                     * We need to add the buttons on the rowactions too
                     */
                    if (viewName === 'recordlist') {
                        let rowActionsDropdown = this.getRowActionsDropdown(viewName);
                        rowActionsDropdown.push(...mergeButtonsMeta);
                    }
                    this._registerTemplateButtonsEvents();
                }
            },

            /**
             * register the events for the merge buttons, on the view context
             */
            _registerTemplateButtonsEvents: function() {
                let convertAction = 'convert';
                let mergeAction = 'merge';

                if (this.context.get('dataView') === 'list') {
                    mergeAction = 'multimerge';
                    convertAction = 'multimerge_convert';
                }

                this.context.on('button:merge_template_pdf:click', this.mergeTemplate.bind(this, convertAction), this);
                this.context.on('button:merge_template:click', this.mergeTemplate.bind(this, mergeAction), this);
            },

            /**
             * Initiate the merge process, by opening the template drawer
             *
             * @param {string} mergeType
             */
            mergeTemplate: function(mergeType, model) {
                this.mergeType = this._processMergeType(mergeType);

                let collectionOptions = {
                    persistentFilter: {
                        template_module: this.module,
                    }
                };

                if (this._isMultiMerge()) {
                    this.selectedModels = this.context.get('mass_collection').models;
                    collectionOptions.persistentFilter.file_ext = 'docx';
                }

                //create collection with filter for the current module
                let filterCollection = app.data.createBeanCollection(this.templatesModule, null, collectionOptions);

                filterCollection.setOption('endpoint', this.documentTemplatesCollectionEndpoint);

                //open drawer with document templates
                app.drawer.open(
                    {
                        layout: 'selection-list',
                        context: {
                            module: this.templatesModule,
                            collection: filterCollection,
                            model: app.data.createBean(this.templatesModule),
                            fields: ['name', 'filename', 'use_revisions', 'file_ext', 'label_merging',],
                        }
                    },
                    _.bind(this._templateSelectedCallback, this, model)
                );
            },

            /**
             * Document tempokates collection endpoint
             *
             * @param {string} method
             * @param {Array} collection
             * @param {Object} options
             * @param {Object} callbacks
             * @return {HttpRequest}
             */
            documentTemplatesCollectionEndpoint: function(method, collection, options, callbacks) {
                const ctxCollection = this.context.get('collection');
                const templateModule = ctxCollection._persistentOptions.persistentFilter.template_module;
                if (!_.isUndefined(templateModule)) {
                    if (!options.params.filter) {
                        options.params.filter = [];
                    }
                    options.params.filter.push({'template_module': {
                        '$starts': templateModule
                    }});
                }

                const fileExt = ctxCollection._persistentOptions.persistentFilter.file_ext;
                if (!_.isUndefined(fileExt)) {
                    if (!options.params.filter) {
                        options.params.filter = [];
                    }
                    options.params.filter.push({'file_ext': {
                        '$equals': fileExt
                    }});
                }
                var url = app.api.buildURL(
                    this.context.get('module'),
                    null,
                    {},
                    options.params
                );

                return app.api.call('read', url, null, callbacks);
            },

            /**
             * When the merge comes from a row action it will send the multimerge type
             * We need the sigle merge type translated here
             *
             * @param {string} mergeType
             * @return string
             */
            _processMergeType: function(mergeType) {
                if (this.context.get('dataView') === 'list') {
                    const collection = this.context.get('mass_collection');
                    if (!collection.length) {
                        if (mergeType === 'multimerge') {
                            mergeType = 'merge';
                        } else if (mergeType === 'multimerge_convert') {
                            mergeType = 'convert';
                        }
                    }
                }

                return mergeType;
            },

            /**
             * After the template is selected, perform the merge api call
             *
             * @param {Object} template
             */
            _templateSelectedCallback: function(model, template) {
                if (_.isEmpty(template)) {
                    return;
                }

                this._setMergeType(template.file_ext, template.label_merging);

                if (!this.mergeType) {
                    app.alert.show('merge_error', {
                        level: 'error',
                        messages: app.lang.getModString('LBL_MERGE_NOT_SUPPORTED', 'DocumentMerges'),
                    });
                    return;
                }

                let payload = {
                    'mergeType': this.mergeType,
                    'useRevision': template.use_revisions,
                    'templateName': template.value,
                    'templateId': template.id,
                    'recordId': model.get('id'),
                    'recordModule': model.get('_module')
                };

                // if it is a multimerge then we need to set the record ids on the payload.
                if (this._isMultiMerge()) {
                    payload.selectedRecords = _.map(this.selectedModels, function(model) {
                        return {
                            id: model.get('id'),
                            name: model.get('name'),
                            module: model.get('_module')
                        };
                    });

                    // also we need to set the correct record module
                    payload.recordModule = this.module;
                }

                const mergeUrl = App.api.buildURL('DocumentMerge', 'merge');
                app.api.call('create', mergeUrl, payload, {
                    success: _.bind(function(documentMergeId) {
                        //open widget in order to show the currently merging document
                        app.events.trigger('document_merge:show_widget');
                        //start polling for changes on the merge request
                        app.events.trigger('document_merge:poll_merge', documentMergeId);
                    } ,this),
                    error: function(errorMessage) {
                        app.alert.show('merge_error', {
                            level: 'error',
                            messages: errorMessage
                        });
                    }
                });
            },

            /**
             * Retrieves the dropdown buttons.
             * Depending on the view it is located differently
             * @param {string} viewName
             *
             * @return {?Array}
             */
            getDropdown: function(viewName) {
                let dropdown = null;

                switch (viewName) {
                    case this.acceptedViews.record:
                        dropdown = this.getRecordMainDropdown();
                        break;
                    case this.acceptedViews.recordlist:
                        dropdown = this.getRecordListDropdown();
                        break;
                    case this.acceptedViews.subpanellist:
                        dropdown = this.getRowActionsDropdown();
                        break;
                }

                // Maybe the view is a subpanel-list extension
                if (!dropdown && this.type === this.acceptedViews.subpanellist) {
                    dropdown = this.getRowActionsDropdown();
                }

                return dropdown;
            },

            /**
             * returns the meta object representing the record main dropdown
             *
             * @return {?Array}
             */
            getRecordMainDropdown: function() {
                if (this.meta.buttons) {
                    let mainDropdown = _.filter(this.meta.buttons, function(button) {
                        return button.name === 'main_dropdown';
                    });

                    if (_.isArray(mainDropdown) && _.isEmpty(mainDropdown) === false) {
                        return _.first(mainDropdown).buttons;
                    }
                }

                return null;
            },

            /**
             * returns the meta object representing the recordlist dropdown
             *
             * @return {?Array}
             */
            getRecordListDropdown: function() {
                if (_.has(this.meta, 'selection') && _.has(this.meta.selection, 'actions')) {
                    return this.meta.selection.actions;
                }

                return null;
            },

            /**
             * Returns the meta object representing the subpanel list dropdown
             * or row actions for list
             *
             * @return {?Array}
             */
            getRowActionsDropdown: function() {
                if (_.has(this.meta, 'rowactions') && _.has(this.meta.rowactions, 'actions')) {
                    return this.meta.rowactions.actions;
                }

                return null;
            },

            /**
             * Checks if the merge is a multimerge
             *
             * @return {boolean}
             */
            _isMultiMerge: function() {
                return (this.mergeType === 'multimerge' ||
                    this.mergeType === 'multimerge_convert' ||
                    this.mergeType === 'labelsgenerate' ||
                    this.mergeType === 'labelsgenerate_convert');
            },

            /**
             * Sets the correct merge type based on the template extension
             *
             * @param {string} ext
             * @param {boolean} labelMerging - meant for label merging
             */
            _setMergeType: function(ext, labelMerging) {
                switch (ext) {
                    case 'pptx':
                        if (this._isMultiMerge()) {
                            this.mergeType = null;
                        } else if (this.mergeType === 'convert') {
                            this.mergeType = 'presentation_convert';
                        } else {
                            this.mergeType = 'presentation';
                        }

                        break;
                    case 'xlsx':
                        if (this._isMultiMerge()) {
                            this.mergeType = null;
                        } else if (this.mergeType === 'convert') {
                            this.mergeType = 'excel_convert';
                        } else {
                            this.mergeType = 'excel';
                        }

                        break;
                }

                if (labelMerging) {
                    if (this.mergeType === 'multimerge') {
                        this.mergeType = 'labelsgenerate';
                    }else if (this.mergeType === 'multimerge_convert') {
                        this.mergeType = 'labelsgenerate_convert';
                    }

                    if (!this._isMultiMerge()) {
                        this.mergeType = null;
                    }
                }
            },

            /**
             * Check if the user has access to document merging
             * In order to get access to Document Merging,you need edit access to Document Merges
             * view access to DocumentTemplates.
             *
             * @return bool
             */
            hasAccess: function() {
                var documentTemplateAccess = app.acl.hasAccess('view', 'DocumentTemplates');
                var documentMergesAccess = app.acl.hasAccess('edit', 'DocumentMerges');
                return documentMergesAccess && documentTemplateAccess && this._isAvailable();
            },

            /**
             * Util to determine if Document Merging is available for this instance
             *
             * @return {boolean} True if Document Merging should be available
             * @private
             */
            _isAvailable: function() {
                return app.api.isAuthenticated() && app.user.isSetupCompleted();
            },
        });
    });
})(SUGAR.App);
