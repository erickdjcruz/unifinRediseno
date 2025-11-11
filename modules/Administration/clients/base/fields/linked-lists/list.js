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
 * @class View.Views.Base.AdministrationLinkedListsField
 */
({
    selectorListDefault: '#df-fields-default',
    selectorListDenorm: '#df-fields-denorm',
    selectorListDenormId: 'df-fields-denorm',

    render: function(options) {
        this._super('render', [options]);

        this.initSortable();
    },

    initSortable: function() {
        var self = this;

        this.getElementFieldListDefault().sortable({
            connectWith: this.selectorListDenorm,
            cursor: 'grabbing',
            placeholder: 'label-warning',
            revert: true,
            items: 'li:not(.muted)',
            dropOnEmpty: true
        }).disableSelection();
        this.getElementFieldListDenormalized().sortable({
            connectWith: this.selectorListDefault,
            cursor: 'grabbing',
            placeholder: 'label-warning',
            revert: true,
            update: self.onUpdate.bind(this),
            items: 'li:not(.muted)',
            dropOnEmpty: true
        }).disableSelection();
    },

    getElementFieldListDefault: function() {
        return this.$(this.selectorListDefault);
    },

    getElementFieldListDenormalized: function() {
        return this.$(this.selectorListDenorm);
    },

    getLiTemplate: function(field, isDenormalized) {
        return '<li data-name="' + field.name + '" data-is-denormalized="' + (isDenormalized ? '1' : '') +
            '" style="cursor: grab; border-bottom: 1px solid #e4e4e4">' +
            field.name + '<span class="pull-right">' + field.type + '</span></li>';
    },

    refresh: function(fields, denormalizedFieldList) {
        var self = this;
        denormalizedFieldList = denormalizedFieldList || [];
        this.clearFieldLists();
        var fieldListDefault = this.getElementFieldListDefault();
        var fieldListDenormalized = this.getElementFieldListDenormalized();
        _.each(fields, function(el) {
            var isDenormalized = !!denormalizedFieldList[el.name];
            var fieldList = isDenormalized ? fieldListDenormalized : fieldListDefault;
            fieldList.append(self.getLiTemplate(el, isDenormalized));
        });
        this.update();
    },

    clearFieldLists: function() {
        this.getElementFieldListDefault().find('li').remove();
        this.getElementFieldListDenormalized().find('li').remove();
    },

    onUpdate: function(event, ui) {
        var isDenormList = !!ui.sender;
        var isDenormField = ui.item.data('is-denormalized') === 1;

        var listTo = isDenormList ? this.getElementFieldListDenormalized() : this.getElementFieldListDefault();
        var listFrom = !isDenormList ? this.getElementFieldListDenormalized() : this.getElementFieldListDefault();
        if (isDenormField !== isDenormList) {
            listFrom.find('li').addClass('muted');
            listTo.find('li').addClass('muted');
            ui.item.removeClass('muted').addClass('text-success');
            this.context.trigger('field-lists:pending');
        } else {
            listFrom.find('li').removeClass('muted text-success');
            listTo.find('li').removeClass('muted text-success');
            this.context.trigger('field-lists:initial');
        }

        this.getElementFieldListDefault().sortable('destroy');
        this.getElementFieldListDenormalized().sortable('destroy');
        this.initSortable();

        this.update();
    },

    update: function() {
        var data = {'not_denormalized': [], 'denormalized': []};
        this.getElementFieldListDefault().find('li').each(function() {
            data.not_denormalized.push($(this).data('name'));
        });
        this.getElementFieldListDenormalized().find('li').each(function(el) {
            data.denormalized.push($(this).data('name'));
        });

        this.model.set('field-lists', data, {silent: true});
    }
})
