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
        app.plugins.register('RowEditable', ['view'], {
            /**
             * Stores references to fields by model ID
             */
            rowFields: {},

            /**
             * Stores any list edit toggled models by model ID
             */
            toggledListModels: {},

            /**
             * Inline editing events
             */
            rowEditEvents: {
                'click [data-action=edit-list-row]': '_handleRowEditClicked',
                'click [data-action=cancel-list-row]': '_handleRowCancelClicked',
                'click [data-action=save-list-row]': '_handleRowSaveClicked',
                'dblclick tr.single': '_handleRowDoubleClick',
            },

            /**
             * Stores references to all fields in the current view by model ID for quick access
             *
             * @private
             */
            _setRowFields: function() {
                this.rowFields = {};
                _.each(this.fields, function(field) {
                    if (field.model && field.model.cid && _.isUndefined(field.parent)) {
                        this.rowFields[field.model.cid] = this.rowFields[field.model.cid] || [];
                        this.rowFields[field.model.cid].push(field);
                    }
                }, this);
            },

            /**
             * Toggles an editable list row between edit/detail mode
             *
             * @param {jQuery} row the jQuery row element object
             * @param {bool} isEdit true to set the row to edit mode; false to set it to detail mode
             * @private
             */
            _toggleRow(row, isEdit) {
                if (!row) {
                    return;
                }
                let model = this.collection.get(row.data('id'));
                if (model && app.acl.hasAccessToModel('edit', model)) {
                    row.toggleClass('tr-inline-edit', isEdit);

                    if (isEdit) {
                        this.toggledListModels[model.id] = model;
                    } else {
                        delete this.toggledListModels[model.id];
                    }

                    this.toggleFields(this.rowFields[model.cid], isEdit);
                }
            },

            /**
             * Given a list of model IDs, toggles edit mode on the list rows
             * representing those IDs
             *
             * @param {Array} ids the list of model IDs
             * @param {bool} isEdit true to set the rows to edit mode; false to set them to list mode
             * @private
             */
            _toggleRowsByModelId(ids, isEdit) {
                _.each(ids, function(id) {
                    let row = this.$el.find(`tr[data-id=${id}]`);
                    if (row.length) {
                        this._toggleRow(row, isEdit);
                    }
                }, this);
            },

            /**
             * Checks if the given element is "clickable" - that is, if it is an element that always
             * performs some action, if it is a focus icon, or if it has an event associated in another way
             *
             * @param element
             * @return {boolean}
             * @private
             */
            _isClickableElement: function(element) {
                let tagNames = [element.tagName, element.parentElement.tagName].map(tag => tag.toLowerCase());
                if (['a', 'button', 'input'].some(tag => tagNames.includes(tag))) {
                    return true;
                }
                if (element.classList.contains('focus-icon')) {
                    return true;
                }
                return ['data-action', 'data-clipboard', 'data-event'].some(attr => {
                    return element.getAttribute(attr) || element.parentElement.getAttribute(attr);
                });
            },

            /**
             * For list tabs, marks collection models that the user does not have edit access to
             *
             * @private
             */
            _checkRowEditAccess: function() {
                if (!this.collection) {
                    return;
                }
                _.each(this.collection.models, function(model) {
                    model.hasEditAccess = app.acl.hasAccessToModel('edit', model);
                }, this);
            },

            /**
             * Given a click event, gets the editable list row associated with the click target
             *
             * @param {Event} event the click event
             * @return {jQuery} a jQuery element representing the row clicked
             * @private
             */
            _getClickedRowElement: function(event) {
                return $(event.currentTarget).closest('tr');
            },

            /**
             * Handles when the edit button is clicked on an editable list row
             *
             * @param event
             * @private
             */
            _handleRowEditClicked: function(event) {
                let row = this._getClickedRowElement(event);
                this._toggleRow(row, true);
            },

            /**
             * Handles when an editable list row has been double-clicked
             *
             * @param event
             * @private
             */
            _handleRowDoubleClick: function(event) {
                if (!this._isClickableElement(event.target)) {
                    let row = this._getClickedRowElement(event);
                    this._toggleRow(row, true);
                }
            },

            /**
             * Handles when the cancel button is clicked on an editable list row
             *
             * @param event
             * @private
             */
            _handleRowCancelClicked: function(event) {
                let row = this._getClickedRowElement(event);

                let model = this.collection.get(row.data('id'));
                if (model) {
                    model.revertAttributes();
                }

                this._toggleRow(row, false);
            },

            /**
             * Adds checks to see if any list view tab models are dirty
             *
             * @return {*}
             */
            hasUnsavedChanges: function() {
                let formFields = [];
                _.each(this.rowFields[_.first(_.keys(this.rowFields))], function(field) {
                    if (field.name) {
                        formFields.push(field.name);
                    }
                    if (field.def.fields) {
                        formFields = _.chain(field.def.fields).pluck('name').compact().union(formFields).value();
                    }
                }, this);

                let hasUnsavedListTabChanges = _.some(_.values(this.toggledListModels), function(model) {
                    var changedAttributes = model.changedAttributes(model.getSynced());
                    return !_.isEmpty(_.intersection(_.keys(changedAttributes), formFields));
                }, this);

                const protoUnsavedChanges = () => {
                    const proto = Object.getPrototypeOf(this);

                    return _.isFunction(proto.hasUnsavedChanges) ?
                        proto.hasUnsavedChanges.call(this) :
                        false;
                };

                return hasUnsavedListTabChanges || protoUnsavedChanges();
            },
        });
    });
})(SUGAR.App);
