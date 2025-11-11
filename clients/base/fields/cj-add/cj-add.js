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
 * @class View.Fields.Base.CjAddField
 * @alias SUGAR.App.view.fields.BaseCjAddField
 * @extends View.Fields.Base.RowactionField
*/
({
    extendsFrom: 'RowactionField',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.listenTo(this.model, 'change:dri_workflow_template_id', this.addTemplate, this);
    },

    /**
     * Sets the dri_workflow_template_id
     */
    addTemplate: function() {
        if (this.model.get('dri_workflow_template_id') !== '') {
            let startButton = _.find(this.view.fields, function(value) {
                return !_.isEmpty(_.first(value.fields)) && _.first(value.fields).name === 'start_cycle';
            });

            if (startButton) {
                startButton.setDisabled(false);
            }
        } else {
            this._render();
        }
    },

    /**
      * @inheritdoc
      */
    _render: function() {
        this._super('_render');
        if (_.isEmpty(this.model.get('dri_workflow_template_id')) ||
            _.isUndefined(this.model.get('dri_workflow_template_id')) ||
            this.model.get('dri_workflow_template_id') === '') {
            let startButton = _.find(this.view.fields, function(value) {
                return !_.isEmpty(_.first(value.fields)) && _.first(value.fields).name === 'start_cycle';
            });
            if (!_.isUndefined(startButton)) {
                startButton = _.first(startButton.fields);
                if (startButton) {
                    startButton.setDisabled(true);
                }
            }
        }
    },

    /**
     * @inheritdoc
    */
    _dispose: function() {
        this.stopListening();
        this._super('_dispose');
    }
});
