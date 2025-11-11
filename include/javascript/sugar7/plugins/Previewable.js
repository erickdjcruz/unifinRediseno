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
        /**
         * This plugin allows a component to render preview data without saving
         */
        app.plugins.register('Previewable', ['view'], {
            /**
             * @inheritdoc
             */
            onAttach: function(component, plugin) {
                this.on('data:preview', _.bind(function(data) {
                    var hasChanges = this.handleDataPreview(data);

                    // components can implement a 'dataPreviewCallbacks' function to be invoked
                    // as part the 'data:preview' event
                    if (this.dataPreviewCallbacks && _.isFunction(this.dataPreviewCallbacks)) {
                        this.dataPreviewCallbacks(hasChanges, data);
                    }
                }, this));
            },

            /**
             * Handles the 'data:preview' event
             *
             * Expects the data event argument to look like:
             * {
             *     fields: [
             *         'field_1_name',
             *         'field_2_name'
             *     ],
             *     properties: [
             *         'propertyOne',
             *         'propertyTwo',
             *     ],
             *     preview_data: 'New Value'
             * }
             *
             * @param data
             * @return boolean true if has changes, false otherwise
             */
            handleDataPreview: function(data) {
                var hasChanges =
                    this.setFieldsPreviewMeta(data) ||
                    this.setPropertiesPreviewData(data);

                if (hasChanges) {
                    this.render();
                }

                return hasChanges;
            },

            /**
             * Set the specified preview data for all specified fields
             *
             * The goal of this function is to set the minimum required metadata
             * to allow a field to show preview data. Each field may have
             * different metadata requirements, so a set[Field]PreviewMeta
             * function may be needed for different fields
             *
             * Fields are disposed upon rendering, so set field metadata
             * before the next render
             * see {@link View/View#_render}
             *
             * Expects the data argument to look like:
             * {
             *     fields: [
             *         'field_1_name',
             *         'field_2_name'
             *     ],
             *     preview_data: 'New Value'
             * }
             *
             * @param data
             * @return boolean true if has changes, false otherwise
             */
            setFieldsPreviewMeta: function(data) {
                var hasChanges = false;

                if (!data.fields || _.isEmpty(data.fields)) {
                    return hasChanges;
                }

                _.each(data.fields, function(fieldName) {
                    var field = this.getField(fieldName);

                    // the specified field is not found on this component
                    if (!field) {
                        return;
                    }

                    var result = false;
                    var fieldType = field.type;

                    // add field type specific behaviors here
                    //
                    // see each set[Field]PreviewMeta function for minimum required metadata
                    switch (fieldType) {
                        case 'button':
                            result = this.setButtonPreviewMeta(fieldName, {
                                label: data.preview_data
                            });
                            break;
                        default:
                    }

                    hasChanges = hasChanges || result;
                }, this);

                return hasChanges;
            },

            /**
             * Update button metadata
             *
             * A button can render preview text with the following metadata:
             * {
             *     label: 'New Value'
             * }
             *
             * @param fieldName the button name
             * @param metadata the preview metadata
             * @return boolean true if has changes, false otherwise
             */
            setButtonPreviewMeta: function(fieldName, metadata) {
                var buttonIndex = _.findIndex(this.meta.buttons, function(button) {
                    return button.name === fieldName;
                }, this);

                if (buttonIndex === -1 || _.isEmpty(metadata)) {
                    return false;
                }

                this.meta.buttons[buttonIndex] = _.extend(this.meta.buttons[buttonIndex], metadata);

                return true;
            },

            /**
             * Set the specified preview data for all specified properties
             *
             * Expects the data event argument to look like:
             * {
             *     properties: [
             *         'propertyOne',
             *         'propertyTwo',
             *     ],
             *     preview_data: 'New Value'
             * }
             *
             * @param data
             * @return boolean true if has changes, false otherwise
             */
            setPropertiesPreviewData: function(data) {
                var hasChanges = false;

                if (!data.properties || _.isEmpty(data.properties)) {
                    return hasChanges;
                }

                _.each(data.properties, function(property) {
                    if (!Object.prototype.hasOwnProperty.call(this, property)) {
                        return;
                    }

                    this[property] = data.preview_data;
                    hasChanges = true;
                }, this);

                return hasChanges;
            },

            /**
             * @inheritdoc
             */
            onDetach: function(component, plugin) {
                this.off('data:preview', this.handleDataPreview, this);
            }
        });
    });
})(SUGAR.App);
