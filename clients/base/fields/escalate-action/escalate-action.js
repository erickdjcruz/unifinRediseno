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
 * @class View.Fields.Base.EscalateActionField
 * @alias SUGAR.App.view.fields.BaseEscalateActionField
 * @extends View.Fields.Base.RowactionField
 */
({
    extendsFrom: 'RowactionField',

    plugins: ['LinkedModel'],

    /**
     * Flag that determines whether this action is present on a dashlet view
     */
    onDashlet: false,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this._setUpEscalationEvents();

        // Determine if this is rendered on a dashlet or not
        this.onDashlet = this.view && this.view.name === 'dashlet-toolbar';
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        if (!this.isEscalatable(this.module, this.def.acl_action)) {
            this.hide();
        } else {
            this._super('_render');
        }
    },

    /**
     * Determines if the specified module is escalatable
     * 1. Module is marked escalatable
     * 2. Escalation subpanel is visible
     *
     * @return {boolean}
     */
    isModuleEscalatable: function(module) {
        var moduleDef = app.metadata.getModule(module);
        var hiddenSubpanels = app.metadata.getHiddenSubpanels();
        if (moduleDef && !_.contains(hiddenSubpanels, 'escalations')) {
            return moduleDef.isEscalatable;
        }
        // Default to false
        return false;
    },

    /**
     * Determines generic is escalatable
     * 1. User has access to Escalations module
     * 2. Module is escalatable
     *
     * @param module
     * @param aclAction
     * @return {boolean}
     */
    isEscalatable: function(module, aclAction = 'create') {
        var hasAccess = app.acl.hasAccess(aclAction, 'Escalations');
        return hasAccess && this.isModuleEscalatable(module);
    },

    /**
     * @inheritdoc
     */
    _loadTemplate: function() {
        this.type = 'rowaction';
        this._super('_loadTemplate');
        this.type = 'escalate-action';
    },

    /**
     * Handle click event for Escalate button
     */
    escalateClicked: function() {
        this._escalateModule();
    },

    /**
     * Trigger the creation of a related Escalation record
     * @private
     */
    _escalateModule: function() {
        // From LinkedModel with hardcoded Escalations module argument and escalations link argument
        this.createRelatedRecord('Escalations', 'escalations');
    },

    /**
     * Set up the escalation event handlers if the module is escalation enabled
     * @private
     */
    _setUpEscalationEvents: function() {
        this.context.on('button:escalate_button:click', this.escalateClicked, this);

        // This allows us to mimic the same behavior as when a user adds a subpanel relationship record
        this.on('linked-model:create', function() {
            // Used to grab the escalation subpanel context if it exists
            var escalationContext;

            // If this action button is not on a dashlet and is instead rendered on the record view
            if (!this.onDashlet) {
                // Grab the escalation subpanel context so that we can update the subpanel at the same time
                escalationContext = this._getEscalationSubpanelContext(this.context);
            }

            // If the module doesnt have a subpanel relationship defined, dont do anything
            // This probably shouldnt be hit since the button wont show when escalation subpanel is hidden, but
            // it protects against a custom module being built that didnt add escalation as a subpanel
            if (!_.isUndefined(escalationContext)) {
                escalationContext.set('skipFetch', false);
                escalationContext.reloadData();
            }
        }, this);
    },

    /**
     * Retrieve the escalation subpanel context from a view context
     * @param context the view context containing the subpanels
     * @private
     */
    _getEscalationSubpanelContext: function(context) {
        return _.find(context.children, function(child) {
            return child.get('module') === 'Escalations';
        });
    },

    /**
     * @inheritdoc
     */
    isAllowedDropdownButton: function() {
        return this.isEscalatable(this.module, this.def.acl_action);
    }
})
