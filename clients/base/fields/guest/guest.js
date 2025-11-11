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
 * @class View.Fields.Base.GuestField
 * @alias SUGAR.App.view.fields.BaseGuestField
 * @extends View.Fields.Base.RelateField
 */
({
    extendsFrom: 'RelateField',

    /**
     * @inheritdoc
     * @param {Object} options
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.customDropdownRender = this._renderEditableDropdown;
    },

    /**
     * Extend the parent(relate) _loadTemplate
     */
    _loadTemplate: function() {
        this.type = 'relate';
        this._super('_loadTemplate');
        this.type = this.def.type;
    },

    /**
     * @override
     * @return {Array}
     */
    getSearchModules: function() {
        return _.chain(this.def.links).map(function(link) {
            return app.data.getRelatedModule(this.module, this.module === 'Messages' ? 'invitee_' + link : link);
        }, this).value();
    },

    /**
     * @override
     * @return {string}
     */
    getPlaceHolder: function() {
        return app.lang.get('LBL_SEARCH_SELECT');
    },

    /**
     * @override
     */
    openSelectDrawer: function() {
        var layout;
        var module;
        var modules;

        if (!_.isUndefined(this.def.links)) {
            layout = 'selection-list-module-switch';
            modules = this.getSearchModules();
            module = _.first(modules);
        } else {
            layout = 'selection-list';
            module = this.module;
            modules = [module];
        }

        app.drawer.open({
            layout: layout,
            context: {
                module: module,
                filterList: modules
            }
        }, _.bind(this.setValue, this));
    },

    /**
     * @override
     * @param {Object} value
     * @return {Object} This field's value. Need to change to object with all
     *   data that we need to render the field.
     */
    format: function(value) {
        if (value && value.models) {
            var model = _.last(value.models);
            if (model) {
                this.formattedIds = model.get('id');
                this.formattedRname = model.get('name');
            } else {
                this.formattedIds = '';
                this.formattedRname = '';
            }
        }
        return value;
    },

    /**
     * @override
     * @return {app.BeanCollection}
     */
    getFieldValue: function() {
        var value = this.model.get(this.name);

        if (!(value instanceof app.BeanCollection)) {
            app.logger.error('The value must be a BeanCollection');
        }

        return value;
    },

    /**
     * @override
     * @param e
     * @private
     */
    _onSelect2Change: function(e) {
        var attributes = {};
        if (e.added) {
            attributes = e.added.attributes;
        }

        this.setValue(attributes);
    },

    /**
     * @override
     * @param model
     */
    setValue: function(model) {
        var guest;
        try {
            guest = this.getFieldValue();
        } catch (err) {
            app.logger.error('Unable to fetch the invitees collection');
        }
        if (guest.length) {
            guest.reset();
        }
        if (model && model.id) {
            guest.add(model);
        }
    },

    /**
     * @override
     * @private
     */
    _updateField: function() {
        if (this.disposed) {
            return;
        }
        var $dropdown = this.$(this.fieldTag);
        var field;
        try {
            field = this.getFieldValue();
        } catch (err) {
            app.logger.error('Unable to fetch the invitees collection');
        }
        if (!_.isEmpty($dropdown.data('select2')) && field.length) {
            var value = _.first(field.models).get('name');
            value = _.isArray(value) ? value.join(this._separator) : value;
            value = value ? value.trim() : value;
            if (this._isErasedField()) {
                value = app.lang.getAppString('LBL_VALUE_ERASED');
            }

            $dropdown.data('rname', value);

            // `id` can be an array of ids if the field is a multiselect.
            var id = _.first(field.models).get('id');
            if (_.isEqual($dropdown.select2('val'), id)) {
                return;
            }

            $dropdown.select2('val', id);
        } else {
            this.render();
        }
    },

    /**
     * @override
     * @param query
     */
    search: _.debounce(function(query) {
        var data;
        var fields;
        var participants;
        var success;

        data = {
            results: [],
            more: false
        };

        success = function(result) {
            result.each(function(record) {
                var participant = participants.get(record.id);

                if (participant) {
                    app.logger.debug(record.module + '/' + record.id + ' is already in the collection');
                } else {
                    record.text = record.get('name');
                    data.results.push(record);
                }
            });
        };

        try {
            participants = this.getFieldValue();
            participants.search({
                query: query.term,
                success: success,
                search_fields: ['full_name', 'email', 'account_name'],
                fields: ['name'],
                complete: function() {
                    query.callback(data);
                }
            });
        } catch (e) {
            app.logger.warn(e);
            query.callback(data);
        }
    }, app.config.requiredElapsed || 500),
})
