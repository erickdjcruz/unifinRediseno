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
        app.plugins.register('DocumentMergeActions', ['view'], {
            events: {
                'click .send-email': 'openEmailDrawer',
                'click .send-link': 'openUsersDrawer',
                'click .send-docusign': 'sendToDocuSign'
            },

            /**
             * @inheritdoc
             *
             * Add the actions for the document merging row
             */
            onAttach: function(component, plugin) {
                this.on('init', function() {
                    //Add the actions only in these views
                    this.acceptedViews = {
                        'document-merge-widget-list': 'document-merge-widget-list',
                    };
                }, this);
            },

            /**
             * Entrypoint function for generating the necessary data in order to open
             * an email drawer with an attached document
             *
             * @param {Event} evt
             */
            openEmailDrawer: function(evt) {
                var generatedDocumentId = this._getDocumentId(evt);

                if (_.isString(generatedDocumentId)) {
                    /**
                     * We need data from the DocumentMerge record and the Document record
                     */
                    this.getEmailDrawerData(generatedDocumentId, this._openEmailDrawer);
                }
            },

            /**
             * First we need to get the generated document id
             * We get that from the DocumentMerge record
             *
             * @param {string} generatedDocumentId
             * @param {Function} callback
             */
            getEmailDrawerData: function(generatedDocumentId, callback) {
                //retrieve the Document record
                app.data.createBean('Documents', {
                    id: generatedDocumentId
                }).fetch({
                    fields: ['name', 'filename', 'document_revision_id', 'latest_revision_file_size',
                    'latest_revision_file_ext', 'latest_revision_file_mime_type'],
                    success: _.bind(this._documentRetrieved, this, callback),
                    error: this._showError
                });
            },

            /**
             * call the callback here
             *
             * @param {Function} callback
             * @param {Bean} generatedDocumentModel
             */
            _documentRetrieved: function(callback, generatedDocumentModel) {
                callback.apply(this, [generatedDocumentModel]);
            },

            /**
             * Build all the File/Notes/Email models
             * Here we finally open the drawer
             *
             * @param {Bean} documentModel
             */
            _openEmailDrawer: function(documentModel) {
                var file = app.data.createBean('Notes', {
                    _link: 'attachments',
                    upload_id: documentModel.get('document_revision_id'),
                    name: documentModel.get('filename') || documentModel.get('name'),
                    filename: documentModel.get('filename') || documentModel.get('name'),
                    file_mime_type: documentModel.get('latest_revision_file_mime_type'),
                    file_size: documentModel.get('latest_revision_file_size'),
                    file_ext: documentModel.get('latest_revision_file_ext'),
                    file_source: 'DocumentRevisions'
                });

                var emailModel = app.data.createBean('Emails');

                if (emailModel.get('attachments_collection')) {
                    emailModel.get('attachments_collection').add(file, {});
                }

                /**
                 * If we are able to get an email address from the current context,
                 * add it to the 'to' emails.
                 */
                var layout = app.controller.layout;
                var emailList = layout ? layout.model.get('email') : [];
                var toRecords = this._addEmailParticipants(emailList);

                if (emailModel.get('to_collection') instanceof app.data.beanCollection) {
                    emailModel.get('to_collection').add(toRecords);
                }

                if (this._isRecordView()) {
                    emailModel.set({
                        'parent_id': layout.model.get('id'),
                        'parent_name': layout.model.get('name'),
                        'parent': layout.model
                    });
                }

                app.drawer.open({
                    layout: 'compose',
                    context: {
                        create: true,
                        model: emailModel,
                        module: 'Emails',
                    }
                }, _.bind(this._triggerEmailsSubpanelReload, this));
            },

            /**
             * Adds the email participants.
             *
             * @param {?Array} emailList
             */
            _addEmailParticipants: function(emailList) {
                var toRecords = [];
                if (!_.isArray(emailList)) {
                    return toRecords;
                }

                for (var i = 0; i < emailList.length; i++) {
                    toRecords.push(app.data.createBean('EmailParticipants', {
                        _link: 'to',
                        deleted: false,
                        email_address: emailList[i].email_address || '',
                        email_address_id: emailList[i].email_address_id || '',
                        invalid_email: false
                    }));
                }

                return toRecords;
            },

            /**
             * Checkes if the context of the drawer is arecord view
             * If it is, then we set the parent of the email
             *
             * @return {boolean}
             */
            _isRecordView: function() {
                if (app.controller.context.get('layout') === 'record' &&
                    app.controller.context.get('module') !== 'Home') {
                    return true;
                }

                return false;
            },

            /**
             * Reloads the Email subpanel
             *
             * @param {Bean} email
             * @param {Bean} model
             */
            _triggerEmailsSubpanelReload: function(email, model) {
                if (typeof model === 'object') {
                    app.controller.context.trigger('subpanel:reload', {
                        links: ['archived_emails']
                    });
                }
            },

            /**
             * Opens a drawer for user selection
             *
             * @param {Event} evt
             */
            openUsersDrawer: function(evt) {
                var generatedDocumentId = this._getDocumentId(evt);

                if (_.isString(generatedDocumentId)) {
                    //Retrieve the document id from the DocumentMerge record
                    this.getEmailDrawerData(generatedDocumentId, this._sendNotification);
                }
            },

            /**
             * Send the document link in a notification
             *
             * @param {Bean} generatedDocumentModel The Document record model
             */
            _sendNotification: function(generatedDocumentModel) {
                app.drawer.open({
                    layout: 'multi-selection-list',
                    context: {
                        module: 'Users',
                        isMultiSelect: true
                    }
                }, _.bind(this.createNotification, this, generatedDocumentModel));
            },

            /**
             * Create the notifications with the document link
             *
             * @param {Bean} generatedDocumentModel
             * @param {Array} selectedUsers
             */
            createNotification: function(generatedDocumentModel, selectedUsers) {
                if (_.isArray(selectedUsers)) {
                    var documentName = generatedDocumentModel.get('filename') || generatedDocumentModel.get('name');
                    var documentId = generatedDocumentModel.get('id');

                    var documentLink = this._getDocumentLink(documentId, documentName);

                    for (var index = 0; index < selectedUsers.length; index++) {
                        var user = selectedUsers[index];

                        const options = {
                            name: documentName,
                            assigned_user_id: user.id,
                            created_by: app.user.id,
                            description: documentLink,
                            severity: 'Document Widget List',
                            is_read: false
                        };

                        const mergedRecord = _.find(this.merges, function(merge) {
                            return merge.generated_document_id === generatedDocumentModel.get('id');
                        });
                        if (!_.isUndefined(mergedRecord)) {
                            options.parent_id = mergedRecord.parent_id;
                            options.parent_type = mergedRecord.parent_type;
                        }
                        const notification = app.data.createBean('Notifications', options);

                        notification.save();
                    }

                    app.alert.dismiss('create_notification');
                } else {
                    app.alert.show('select-users', {
                        level: 'info',
                        title: app.lang.getModString('LBL_NO_USERS_SELECTED', 'DocumentMerges'),
                    });
                }
            },

            /**
             * Send document to DocuSign
             * @param {Event} evt
             */
            sendToDocuSign: function(evt) {
                const clickedMergeId = this._getMergeId(evt);
                const mergeObject = _.find(this.merges, function(merge) {
                    return merge.id === clickedMergeId;
                });
                if (_.isUndefined(mergeObject)) {
                    return;
                }

                const parentModule = mergeObject.parent.type;
                const parentId = mergeObject.parent.id;
                const documentId = this._getDocumentId(evt);
                const documents = [documentId];

                app.events.trigger('docusign:send:initiate', {
                    returnUrlParams: {
                        parentRecord: parentModule,
                        parentId: parentId,
                        token: app.api.getOAuthToken()
                    },
                    documents: documents,
                }, 'openEnvelope');
            },

            /**
             * Builds a link to the document
             *
             * @param {string} documentPath
             * @param {string} documentName
             *
             * @return {string}
             */
            _getDocumentLink: function(documentId, documentName) {
                var documentPath = app.api.buildFileURL({
                    module: 'Documents',
                    id: documentId,
                    field: 'filename'
                });

                var link = '<a href=\'' +
                         documentPath +
                         '\'>' +
                         documentName +
                         '</a>';

                return link;
            },

            /**
             * Returns the generated document id
             *
             * @param {jQuery} evt
             * @return {?string}
             */
            _getDocumentId: function(evt) {
                var downloadElement = $(evt.target).closest('.merge-row').find('.download');
                if (downloadElement instanceof jQuery) {
                    var generatedDocumentId = downloadElement.attr('document-id');
                    return generatedDocumentId;
                }

                return null;
            },

            /**
             * Returns the merge id
             *
             * @param {jQuery} evt
             * @return {?string}
             */
            _getMergeId: function(evt) {
                var rowElement = $(evt.target).closest('.merge-row');
                if (rowElement instanceof jQuery) {
                    return rowElement.attr('merge-id');
                }

                return null;
            },

            /**
             * Generic function to display an error alert
             *
             * @param {Error} error
             *
             */
            _showError: function(error) {
                app.alert.show('merges-error', {
                    level: 'error',
                    autoClose: true,
                    messages: error.message,
                });
            }
        });
    });
})(SUGAR.App);
