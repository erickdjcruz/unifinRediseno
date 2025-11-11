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
 * @class View.Views.Base.AdministrationModuleSelectField
 * @extends View.Fields.Base.EnumField
 */
({
    extendsFrom: 'EnumField',
    waitingForRender: false,
    moduleFields: {},

    render: function(options) {
        if (this.waitingForRender) {
            return;
        }
        this.waitingForRender = true;

        var self = this;
        this.load(function() {
            self._super('render', [options]);
            self.waitingForRender = false;
        });
    },

    load: function(callback) {
        var self = this;

        this.setEnabled(false);

        var options = {
            success: _.bind(function(data) {
                self.items = {};
                self.moduleFields = {};
                _.each(data, function(value, key) {
                    self.items[key] = key;
                    self.moduleFields[key] = value;
                });
            }, this),
            error: function() {
                self.items = {};
            },
            complete: function() {
                if (callback) {
                    callback();
                }
                self.setEnabled(true);
            }
        };

        app.api.call(
            'get',
            app.api.buildURL(this.module, 'denormalization/configuration'), [], options, {context: this}
        );
    },

    getFieldsForModule: function(module) {
        return this.moduleFields[module] || [];
    },

    /**
     * @inheritdoc
     */
    getSelect2Options: function() {
        var options = this._super('getSelect2Options');

        options.escapeMarkup = function(m) {
            return m;
        };

        return options;
    },

    /**
     * @inheritdoc
     */
    _filterOptions: function(options) {
        var self = this;

        options = this._super('_filterOptions', [options]);

        var filtered = {};
        _.each(options, function(item, key) {
            if (!_.isEmpty(self.view.getFieldsForModule(key))) {
                filtered[key] = item;
            }
        });

        return filtered;
    },

    _sortResults: function(results) {
        results = this._super('_sortResults', [results]);

        var self = this;
        var updated = [];
        var fields;
        var df;
        var nf;

        _.each(results, function(item) {
            fields = self.view.getFieldsForModule(item.id);
            df = nf = 0;
            _.each(fields, function(field) {
                if (field.is_denormalized) {
                    df += 1;
                } else {
                    nf += 1;
                }
            });

            if (df > 0) {
                item.text = '<span style="font-weight: bold">' + item.text + '</span>' +
                    '<small style="margin-left: 10px">' + nf +
                    ' / <b style="color: #54cb14">' + df + '</b></small>';
            } else {
                item.text += '<small style="margin-left: 10px">' + nf + '</small>';
            }

            updated.push(item);
        });

        return updated;
    },

    setEnabled: function(flag) {
        var cmd = flag ? 'enable' : 'disable';
        this.$(this.fieldTag).select2(cmd);
    }
})
