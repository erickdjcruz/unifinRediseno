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
 * This field is a container for a field that renders on record view.
 * It's designed to update the style of the label based off the state of the
 * actual field.
 *
 * @class View.Fields.Base.RecordDecorField
 * @alias SUGAR.App.view.fields.BaseRecordDecorField
 * @extends View.Fields.Base.BaseField
 */
({
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.fields = [];
        this.actualFieldsMeta = [this.def.field];
        this.disabled = false;
        this.context.on('field:disabled', this._setDisabled, this);
    },

    /**
     * Set the disable property and redecorate.
     * @private
     */
    _setDisabled: function(fieldName) {
        var field = this.getActualField();
        if (!field || !field.name) {
            return;
        }
        if (field.name == fieldName) {
            this.disabled = true;
            this.redecorate(field);

        }
    },

    /**
     * Always load the record-decor.hbs template
     * @private
     */
    _loadTemplate: function() {
        this.template = app.template.getField(this.type, 'record-decor', this.module, false);
    },

    /**
     * Render the container first and then render the actual field
     * @return {SUGAR.App.view.fields.RecordDecorField}
     * @private
     */
    _render: function() {
        this._super('_render');
        this.wrapSetMode();
        this._renderFields(this.fields);
        this.bindChange();
        return this;
    },

    /**
     * Renders all the fields inside the record-decor field
     *
     * @param {Array} List of field instances
     * @private
     */
    _renderFields: function(fields) {
        var fieldElems = {};
        this.$('span[sfuuid]').each(function() {
            var $this = $(this);
            var sfId = $this.attr('sfuuid');
            fieldElems[sfId] = $this;
        });
        _.each(fields, function(field) {
            field.setElement(fieldElems[field.sfId]);
            if (field.view.action !== 'create') {
                // Ensures the subfield is given the correct action and renders it
                field.setMode(this.action);
            } else {
                field.render();
            }

            this.redecorate(field);
        }, this);
    },

    /**
     * Gets the field that we are showing on record view. Currently we only
     * have 1 field per container so default to getting the field at the 0 index
     *
     * @return {View.Field}
     */
    getActualField: function() {
        return this.fields[0];
    },

    /**
     * Gets the record cell and caches it for future retrieves
     *
     * @return {jQuery}
     */
    getRecordCell: function() {
        if (this.recordCellDom) {
            return this.recordCellDom;
        }
        return this.recordCellDom = this.$el.parents('.record-cell');
    },

    /**
     * Sets or removes class on the record cell
     * @param style
     */
    setCellStyle: function(style) {
        var $cell = this.getRecordCell();
        if ($cell.length === 0) {
            return;
        }
        if (style === 'pill') {
            $cell.addClass('label-pill');
        } else {
            $cell.removeClass('label-pill');
        }
    },

    /**
     * Set up an event listener on the actual field's model
     * change so that we know to update the look of the rest of the
     * container
     */
    bindChange: function() {
        var field = this.getActualField();
        if (!field || !field.name) {
            return;
        }

        field.model.on('acl:change', function() {
            this.redecorate(field);
        }, this);

        if (_.isArray(field.fields)) {
            _.each(field.fields, function(subField) {
                field.model.on('change:' + subField.name, function() {
                    this.redecorate(field);
                }, this);
            }, this);
        } else {
            field.model.on('change:' + field.name, function() {
                this.redecorate(field);
            }, this);
        }
    },

    /**
     * SetMode is used to toggle a field between detail and edit mode.
     * We wrap it so that we also update our label when setMode is called
     */
    wrapSetMode: function() {
        var field = this.getActualField();
        if (!field || !field.name) {
            return;
        }
        field.setMode = _.wrap(field.setMode, _.bind(function(setMode, args) {
            this.redecorate(field, args);
            setMode.call(field, args);
        }, this));
    },

    /**
     * Change how the contents surrounding the actual field look like
     *
     * @param {View.Field} field The actual field to show/hide based off of state
     * @param {string} toTemplate The action the field will switch to
     */
    redecorate: function(field, toTemplate) {
        // Make sure the field element exists
        if (!field.$el) {
            return;
        }

        // Allow fields to prevent their design to be controlled by record-decor
        if (this.fieldDecorationDisabled(field)) {
            return;
        }

        // Do not redecorate readonly fields
        if (app.utils.isFieldAlwaysReadOnly(field.def, field.viewDefs)) {
            return;
        }

        // First check if we are switching templates (setMode will have the new template)
        // Then check if the field is just re-rendering and use its action
        // Render based off the view's action if the field's action is unknown
        var actionToCheck = toTemplate || field.action || this.view.action || 'detail';
        // In detail mode, we have to show a pill for the label if the field is empty
        if (actionToCheck == 'detail') {
            const editAccess = app.acl.hasAccessToModel('edit', this.model, field.name);

            if (field.isFieldEmpty() && editAccess && !this.disabled &&
                !(this.view && _.contains(this.view.noEditFields, field.name))) {
                this.setCellStyle('pill');
                field.hide();
            } else {
                this.setCellStyle('none');
                field.show();
            }
        } else {
            this.setCellStyle('none');
            field.show();
        }

        if (!field.def.labelsOnTop) {
            this.relocatePencil();
        }
    },

    /**
     * Check the field to see if disableDecoration is set to true for the field,
     * or for a fieldset for its components.
     *
     * @param field
     * @return {boolean}
     */
    fieldDecorationDisabled: function(field) {
        if (field.disableDecoration) {
            return true;
        } else if (field.type === 'fieldset' && _.some(field.fields, function(field) {
            return field.disableDecoration;
        })) {
            return true;
        }
        return false;
    },

    /**
     * In labelsOnSide view, the pencil icon needs to be moved to the left
     * so it hovers near the text
     */
    relocatePencil: function() {
        var field = this.getActualField();

        var cell = this.getRecordCell();
        let isCellHidden = cell.parent().hasClass('hide');

        // if cell is hidden, display it to get non-zero width values
        if (isCellHidden) {
            this.toggleRecordCellDisplay(cell);
        }

        var pencil = cell.find('.sicon-edit');
        var wrapper = cell.find('.record-label-wrapper');
        var label = cell.find('.record-label');
        var wrapperWidth = wrapper.outerWidth();
        var labelWidth = label.outerWidth();

        // if cell was originally hidden, re-hide the cell
        if (isCellHidden) {
            this.toggleRecordCellDisplay(cell);
        }

        var offset = wrapperWidth - labelWidth - 6;
        let css = {};

        // change offset if it's showed children's label instead of label of field
        if (field && field.type === 'fieldset' && field.def.show_child_labels) {
            offset += 26;
        }

        if (field && !field.def.show_child_labels) {
            css = {top: '6px'};
        }

        var direction = app.lang.direction === 'ltr' ? 'left' : 'right';
        css[direction] = offset + 'px';

        pencil.css(css);
    },

    /**
     * Displays or hides the record cell by toggling the parent div's 'hide' class
     *
     * @param {jquery} $cell the record cell
     */
    toggleRecordCellDisplay: function($cell) {
        $cell.parent().toggleClass('hide');
    },

    /**
     * Dispose the child fields
     *
     * @override
     */
    _dispose: function() {
        _.each(this.fields, function(field) {
            field.dispose();
        });
        this._super('_dispose');
    }
})
