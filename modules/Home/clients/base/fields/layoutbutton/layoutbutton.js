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
 * @class View.Fields.Base.Home.LayoutbuttonField
 * @alias SUGAR.App.view.fields.BaseHomeLayoutbuttonField
 * @extends View.Fields.Base.BaseField
 */
({
    events: {
        'click .btn.layout' : 'layoutClicked'
    },

    extendsFrom: 'ButtonField',
    getFieldElement: function() {
        return this.$el;
    },
    _render: function() {
        var buttonField = app.view._getController({type: 'field', name: 'button', platform: app.config.platform});
        buttonField.prototype._render.call(this);
    },
    _loadTemplate: function() {
        app.view.Field.prototype._loadTemplate.call(this);
        if(this.action !== 'edit' || (this.model.maxColumns <= 1)) {
            this.template = app.template.empty;
        }
    },
    format: function(value) {
        var metadata = this.model.get("metadata");
        if(metadata) {
            var components = this.getComponentsFromMetadata(metadata);
            return components ? components.length : 1;
        }
        return value;
    },
    layoutClicked: function(evt) {
        var value = $(evt.currentTarget).data('value');
        this.setLayout(value);
    },
    /**
     * Gets component from metadata.
     *
     * @param {Object} metadata for all dashboard components
     * @return {Object} dashboard component
     */
    getComponentsFromMetadata: function(metadata) {
        var component;
        // this is a tabbed dashboard
        if (metadata.tabs) {
            var tabIndex = this.context.get('activeTab') || 0;
            component = metadata.tabs[tabIndex].components;
        } else {
            component = metadata.components;
        }
        return component;
    },
    setLayout: function(value) {
        var span = 12 / value;
        if(this.value) {

            if (value === this.value) {
                return;
            }
            var setComponent = function() {
                var metadata = this.model.get("metadata");
                var components = this.getComponentsFromMetadata(metadata);

                _.each(components, function(component) {
                    component.width = span;
                }, this);

                if (components.length > value) {
                    _.times(components.length - value, function(index) {
                        components[value - 1].rows = components[value - 1].rows.concat(components[value + index].rows);
                    }, this);
                    components.splice(value);
                } else {
                    _.times(value - components.length, function(index) {
                        components.push({
                            rows: [],
                            width: span
                        });
                    }, this);
                }
                this.model.set("metadata", app.utils.deepCopy(metadata), {silent: true});
                this.model.trigger("change:metadata");
            };
            if(value !== this.value) {
                app.alert.show('resize_confirmation', {
                    level: 'confirmation',
                    messages: app.lang.get('LBL_DASHBOARD_LAYOUT_CONFIRM', this.module),
                    onConfirm: _.bind(setComponent, this),
                    onCancel: _.bind(this.render,this) // reverse the toggle done
                });
            } else {
                setComponent.call(this);
            }
        } else {
            //new data
            var metadata = {
                components: []
            };
            _.times(value, function(index) {
                metadata.components.push({
                    rows: [],
                    width: span
                });
            }, this);

            this.model.set("metadata", app.utils.deepCopy(metadata), {silent: true});
            this.model.trigger("change:metadata");
        }
    },
    bindDomChange: function() {

    },
    bindDataChange: function() {
        if (this.model) {
            this.model.on("change:metadata", this.render, this);
            if(this.model.isNew()) {
                //Assign default layout set
                this.setLayout(1);
                //clean out model changed attributes not to warn unsaved changes
                this.model.changed = {};
            }
        }
    }
})
