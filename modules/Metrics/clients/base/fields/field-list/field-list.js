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
 * @class View.Fields.Base.Metrics.FieldListField
 * @alias SUGAR.App.view.fields.BaseMetricsFieldListField
 * @extends View.Fields.Base.BaseField
 */
({
    removeFldIcon: '<i class="sicon sicon-remove console-field-remove"></i>',
    removeColIcon: '<i class="sicon sicon-remove multi-field-column-remove"></i>',

    events: {
        'click .sicon.sicon-remove.console-field-remove': 'removePill',
        'click .sicon.sicon-remove.multi-field-column-remove': 'removeMultiLineField',
    },

    /**
     * Fields mapped to their subfields.
     */
    mappedFields: {},

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this.mappedFields = this.getMappedFields();
        this.previewEvent = 'consoleconfig:preview:' + this.model.get('metric_module');
    },

    /**
     * @inheritdoc
     *
     * Overrides the parent bindDataChange to make sure this field is re-rendered
     * when the config is reset.
     */
    bindDataChange: function() {
        if (this.model) {
            this.context.on('consoleconfig:reset:defaultmetarelay', function() {
                var defaultViewMeta = this.context.get('defaultViewMeta');
                var moduleName = this.model.get('metric_module');
                if (defaultViewMeta) {
                    this.mappedFields = this.getMappedFields();
                    this.context.set('defaultViewMeta', null);
                    this.render();
                    this.handleColumnsChanging();
                }
            }, this);
        }
    },

    /**
     * Removes a pill from the selected fields list.
     *
     * @param {e} event Remove icon click event.
     */
    removePill: function(event) {
        var pill = event.target.parentElement;
        var container = $(pill.parentElement);

        event.target.remove();
        pill.setAttribute('class', 'pill outer');
        this.getAvailableSortable().append(pill);
        if (container.hasClass('multi-field-sortable')) {
            this.updateMultiLineField(container);
            this.addMultiFieldHint(container);
        }
        this.handleColumnsChanging();
        this.triggerPreviewUpdate();
    },

    /**
     * Remove a multi line field column and fields inside.
     *
     * @param {e} event Remove icon click event.
     */
    removeMultiLineField: function(event) {
        var multiLineField = event.target.parentElement.parentElement.parentElement;

        _.each($(multiLineField).find('.pill'), function(pill) {
            pill.children[0].remove();
            pill.setAttribute('class', 'pill outer');
            this.getAvailableSortable().append(pill);
        }, this);

        multiLineField.remove();
        this.handleColumnsChanging();
        this.triggerPreviewUpdate();
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        this.initSingleFieldDragAndDrop();
        if (this.options.def.type == 'field-list') {
            this.triggerPreviewUpdate();
        }
    },

    /**
     * Initialize drag & drop for the selected field (main) list.
     */
    initSingleFieldDragAndDrop: function() {
        var sortableEl = this.$('#columns-sortable');
        sortableEl.sortable({
            items: '.outer.pill',
            connectWith: '.connectedSortable',
            receive: _.bind(this.handleSingleFieldDrop, this),
            update: _.bind(this.handleSingleFieldStop, this),
        });

        var multiFieldSortables = sortableEl.find('.multi-field-sortable.multi-field.connectedSortable');
        _.each(multiFieldSortables, function(multiField) {
            this.initMultiFieldDragAndDrop($(multiField));
        }, this);
    },

    /**
     * Initialize drag & drop for a multi field container.
     *
     * @param {Object} element The multi-field container element.
     */
    initMultiFieldDragAndDrop: function(element) {
        element.sortable({
            items: '.pill',
            connectWith: '.connectedSortable',
            receive: _.bind(this.handleMultiLineFieldDrop, this),
            update: _.bind(this.handleMultiLineFieldStop, this),
            over: _.bind(this.handleMultiLineFieldOver, this),
            out: _.bind(this.handleMultiLineFieldOut, this),
        });
    },

    /**
     * Event handler for the single field drag & drop. The event is fired when an item is dropped to a list.
     * Several actions are performed:
     * - When moving a field from the right to the left we add the remove icon.
     * - When moving a field from a multi line field to the outside we change selector.
     * - The library can't handle the case when the last item from the list is a multi line field.
     *   In such cases we manually insert the moved item after the group;
     *   dropping into a multi-line field is handled in `handleMultiLineFieldDrop`.
     *
     * @param {e} event jQuery sortable event handler.
     * @param {Object} ui jQuery UI's helper object for drag & drop operations.
     */
    handleSingleFieldDrop: function(event, ui) {
        if ('fields-sortable' == ui.sender.attr('id')) {
            ui.item.append(this.removeFldIcon);
        }

        if (ui.sender.hasClass('multi-field-sortable')) {
            ui.item.addClass('outer');
            this.addMultiFieldHint(ui.sender);
        }

        this.repositionItem(ui);
    },

    /**
     * Event handler for the single field drag & drop.
     * The event is fired when drop has been finished and the DOM has been updated.
     *
     * @param {e} event jQuery sortable event handler.
     * @param {Object} ui jQuery UI's helper object for drag & drop operations.
     */
    handleSingleFieldStop: function(e, ui) {
        this.repositionItem(ui);
        this.handleColumnsChanging();
        this.triggerPreviewUpdate();
    },

    /**
     * Event handler for the multi field drag & drop. The event is fired when an item is dropped to a list.
     * Several actions are performed here:
     * - If certain conditions are met the drag & drop is cancelled.
     * - If there is a hint text, it is removed.
     * - When moving a field from the right to the left we add the remove icon.
     * - When a field is being moved from the right to the left or from the ouside inside the selector is changed.
     *
     * @param {e} event jQuery sortable event handler.
     * @param {Object} ui jQuery UI's helper object for drag & drop operations.
     */
    handleMultiLineFieldDrop: function(event, ui) {
        var multiLineFields = $(event.target).find('.pill');

        if (this.shouldRejectFieldDrop(ui, multiLineFields)) {
            ui.sender.sortable('cancel');
            this.updateMultiLineField(ui.sender);
        } else {
            $(event.target).find('.multi-field-hint').remove();
            if ('fields-sortable' == ui.sender.attr('id')) {
                ui.item.append(this.removeFldIcon);
            }

            if (ui.sender.hasClass('multi-field-sortable')) {
                this.addMultiFieldHint(ui.sender);
            } else {
                ui.item.removeClass('outer');
            }

            this.triggerPreviewUpdate();
        }
    },

    /**
     * Event handler for the multi field drag & drop.
     * The event is fired when drop has been finished and the DOM has been updated.
     *
     * @param {e} event jQuery sortable event handler.
     */
    handleMultiLineFieldStop: function(event) {
        this.updateMultiLineField($(event.target));
        this.handleColumnsChanging();
        this.triggerPreviewUpdate();
    },

    /**
     * Event handler for the multi field drag over
     * The event is fired when drag over with a draggable element has occurred
     *
     * @param {e} event jQuery sortable event handler
     * @param {Object} jQuery ui object selector
     */
    handleMultiLineFieldOver: function(event, ui) {
        var eventTarget = $(event.target);
        var multiLineFields = eventTarget.find('.pill');
        if (multiLineFields.length > 2 && !ui.item.parent().hasClass('multi-field-sortable')) {
            ui.item.css('cursor', 'no-drop');
            ui.placeholder.addClass('multi-field-block-placeholder-none');
        } else {
            eventTarget.parent().addClass('multi-field-block-highlight');
        }
    },

    /**
     * Event handler for the multi field drag out
     * The event is fired when drag out with a draggable element has occurred
     *
     * @param {e} event jQuery sortable event handler
     * @param {Object} jQuery ui object selector
     */
    handleMultiLineFieldOut: function(event, ui) {
        ui.item.css('cursor', '');
        ui.placeholder.removeClass('multi-field-block-placeholder-none');
        $(event.target).parent().removeClass('multi-field-block-highlight');
    },

    /**
     * Update columns property of the model basing on the selected columns.
     */
    handleColumnsChanging: function() {
        var fieldName;
        var columns = {};
        var moduleName = this.model.get('metric_module');
        var columnsSortable = $('#list-layout-side')
            .find('#columns-sortable .pill:not(.multi-field-block)');

        var fields = app.metadata.getModule(moduleName, 'fields');
        _.each(columnsSortable, function(item) {
            fieldName = $(item).attr('fieldname');
            columns[fieldName] = fields[fieldName];
        });

        this.model.set('columns', columns);
    },

    /**
     * jQuery UI does not support drag & drop into nested containers. When the last item is a multi line field,
     * we have to check for the correct drop area and if the library targets the multi line field instead of the
     * main container as a drop zone, we move the dropped item to the outside container.
     *
     * @param {Object} ui jQuery UI's helper object for drag & drop operations.
     */
    repositionItem: function(ui) {
        var parentContainer = ui.item.parent();
        if (parentContainer.hasClass('multi-field')) {
            var parentStartPos = parentContainer.offset().top;
            var parentEndPos = parentStartPos + parentContainer.height();
            if (ui.offset.top <= parentStartPos || ui.offset.top >= parentEndPos) {
                parentContainer.parent().after(ui.item);
            }
        }
    },

    /**
     * Checks 4 conditions in which drag & drop into a multi line field should not be allowed.
     * The 4 conditions are the following:
     * - When there are already 2 fields in the block.
     * - When a multi line field block is being dropped.
     * - When a field that is defined as a multi-line field is dropped into a block with at least 1 item already.
     * - When the block contains already a field defined as a multi-line field (such fields count as 2 simple fields).
     *
     * @param {Object} ui The jQuery UI library sortable action object.
     * @param {Array} multiLineFields The list of fields inside a multi field block.
     */
    shouldRejectFieldDrop: function(ui, multiLineFields) {
        var moduleName = this.model.get('metric_module');
        var droppedFieldName = ui.item.attr('fieldname');
        var fieldDefinitions = app.metadata.getModule(moduleName, 'fields');
        var isDefinedAsMultiLine = this.isDefinedAsMultiLine(droppedFieldName, fieldDefinitions);

        // IMPORTANT NOTE: the placeholder is considered another field present in cases
        // when we perform operation other than a simple reordering of items.
        var subFieldLimit = 2;

        // Reject conditions.
        var hasAlready2Fields = multiLineFields.length > subFieldLimit;
        var isMultiLineIntoMultiLineDrop = ui.item.hasClass('multi-field-block');
        var isMultiFieldDrop = isDefinedAsMultiLine && multiLineFields.length > (subFieldLimit - 1);
        var containsAlreadyAMultiLineFieldDef = multiLineFields.length == subFieldLimit &&
            this.containsMultiLineFieldDef(multiLineFields, fieldDefinitions);

        return hasAlready2Fields || isMultiLineIntoMultiLineDrop ||
            isMultiFieldDrop || containsAlreadyAMultiLineFieldDef;
    },

    /**
     * Check whether a multi line field contains and fields that are defined as multi line fields.
     *
     * @param {jQuery} multiLineFields The pills from a multi line field.
     * @param {Object} fieldDefinitions The list of field definitions for the current module.
     * @return {boolean} True or false.
     */
    containsMultiLineFieldDef: function(multiLineFields, fieldDefinitions) {
        return _.isObject(_.find(multiLineFields, function(field) {
            var fieldName = field.getAttribute('fieldname');
            return fieldName && this.isDefinedAsMultiLine(fieldName, fieldDefinitions);
        }, this));
    },

    /**
     * Will add a text hint about possible drag & drop to a multi line field.
     *
     * @param {jQuery} multiLineField The multi field into which the hint text should be inserted.
     */
    addMultiFieldHint: function(multiLineField) {
        var pills = multiLineField.children('.pill');
        var hint = multiLineField.children('.multi-field-hint');
        if (!hint.length && pills.length == 0) {
            multiLineField.append(
                '<div class="multi-field-hint">' +
                app.lang.get('LBL_METRIC_MULTI_ROW_HINT', 'Metrics') +
                '</div>'
            );
        }
    },

    /**
     * It will create a new aggregated header text and label for a multi line field.
     * In case there are no fields in a multi line field the default values will be set.
     *
     * @param {jQuery} fields The pills found inside a multi line field.
     * @return {Object} A header title text and custom label.
     */
    getNewHeaderDetails: function(fields) {
        var lbl = '';
        var name = '';
        var text = '';
        var delimiter = '';
        _.each(fields, function(field) {
            lbl += delimiter + field.getAttribute('fieldlabel');
            name += delimiter + field.getAttribute('fieldname');
            text += delimiter + field.getAttribute('data-original-title');
            delimiter = '/';
        });
        return {
            fieldName: name || '',
            label: lbl || '',
            text: text || app.lang.get('LBL_METRIC_MULTI_ROW', this.module)
        };
    },

    /**
     * It will update a multi line field depending on the number of pills it contains.
     * If there are no fields inside, a hint will be displayed. If a field has been added,
     * the hint text will be removed. Additionally the multi line field header text will be changed.
     *
     * @param {jQuery} multiLineField A multi line field to be updated.
     */
    updateMultiLineField: function(multiLineField) {
        var fields = multiLineField.children('.pill');
        var headerDetails = this.getNewHeaderDetails(fields);

        if (fields.length) {
            multiLineField.children('.multi-field-hint').remove();
        }

        var header = multiLineField.children('.list-header');
        header.text(headerDetails.text).append(this.removeColIcon)
            .attr('data-original-title', headerDetails.text)
            .attr('fieldname', headerDetails.fieldName)
            .attr('fieldlabel', headerDetails.label);
    },

    /**
     * Checks if a given field is defined as a multi-line field.
     *
     * @param {string} fieldName The name of the field to check.
     * @return {boolean} True if it is a multi line field definition.
     */
    isDefinedAsMultiLine: function(fieldName) {
        var moduleName = this.model.get('metric_module');
        var fieldDefinitions = app.metadata.getModule(moduleName, 'fields');
        return _.isObject(_.find(fieldDefinitions, function(field) {
            return field.multiline && field.type === 'widget' && field.name === fieldName;
        }));
    },

    /**
     * Return the proper view metadata. If there is a default metadata we restore it,
     * otherwise we return the view metadata.
     *
     * @param {string} moduleName The selected module name from the available modules.
     * @return {Object} The default view meta or the multi line list metadata.
     */
    getViewMetaData: function(moduleName) {
        var defaultViewMeta = this.context.get('defaultViewMeta');
        if (!_.isEmpty(defaultViewMeta)) {
            return defaultViewMeta;
        }
        var viewMeta = this.model.get('viewdefs');
        if (!_.isEmpty(viewMeta)) {
            return viewMeta.base.view['multi-line-list'];
        } else {
            return app.metadata.getView(moduleName, 'multi-line-list');
        }
    },

    /**
     * Will cache and return the sortable list with the available fields.
     *
     * @return {jQuery} The available fields sortable lost node.
     */
    getAvailableSortable: function() {
        var parentSelector = '#list-layout-side';
        return this.availableSortable || (this.availableSortable = $(parentSelector).find('#fields-sortable'));
    },

    /**
     * Gets the module's multi-line list fields from the model with the parent field mapping
     *
     * @return {Object} the fields
     */
    getMappedFields: function() {
        var tabContentFields = {};
        var whitelistedProperties = [
            'name',
            'label',
            'widget_name',
        ];
        var multiLineMeta = this.getViewMetaData(this.model.get('metric_module'));

        _.each(multiLineMeta.panels, function(panel) {
            _.each(panel.fields, function(fieldDefs) {
                var subfields = [];
                _.each(fieldDefs.subfields, function(subfield) {
                    var parsedSubfield = _.pick(subfield, whitelistedProperties);

                    // if label does not exist, get it from the parent's vardef
                    if (!_.has(parsedSubfield, 'label')) {
                        parsedSubfield.label = this.model.fields[parsedSubfield.name].label ||
                            this.model.fields[parsedSubfield.name].vname;
                    }

                    parsedSubfield.parent_name = fieldDefs.name;
                    parsedSubfield.parent_label = fieldDefs.label;

                    if (_.has(parsedSubfield, 'widget_name')) {
                        parsedSubfield.name = parsedSubfield.widget_name;
                    }

                    subfields = subfields.concat(parsedSubfield);
                }, this);

                tabContentFields[fieldDefs.name] = _.has(tabContentFields, fieldDefs.name) ?
                    tabContentFields[fieldDefs.name].concat(subfields) : subfields;
            }, this);
        }, this);

        return tabContentFields;
    },

    /**
     * It will trigger an update on the multi lint list preview. To trigger the preview it needs a
     * list of selected fields based on the sortable list. In case the preview is triggered from a
     * multi field, we have have to climb higher to find the sortable list.
     */
    triggerPreviewUpdate: function() {
        var domFieldList = this.$el.find('#columns-sortable');
        if (!domFieldList.length) {
            domFieldList = this.$el.parent().parent().parent().find('#columns-sortable');
        }
        this.context.trigger(this.previewEvent, this.getSelectedFieldList(domFieldList));
    },

    /**
     * Taking the dom list of fields, creates an accurate mapping of fields for the preview.
     *
     * @param {jQuery} node The DOM representation of the selected fields.
     * @return {Array} The list of selected fields.
     */
    getSelectedFieldList: function(node) {
        var subFields;
        var fieldList = [];

        node.children().each(function(index, field) {
            if ($(field).hasClass('multi-field-block')) {
                subFields = [];
                $(field).find('.pill').each(function(index, subField) {
                    subFields.push({
                        name: $(subField).attr('fieldname'),
                        label: $(subField).attr('fieldlabel')
                    });
                });
                if (subFields.length) {
                    fieldList.push(subFields);
                }
            } else {
                fieldList.push([{
                    name: $(field).attr('fieldname'),
                    label: $(field).attr('fieldlabel')
                }]);
            }
        });

        return fieldList;
    },
})
