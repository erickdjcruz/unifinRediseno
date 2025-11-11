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
({
    extendsFrom: 'HomeDashboardtitleField',

    hintStateKey: 'hintEnabled',

    events: {
        'click .dropdown-toggle': 'toggleClicked',
        'click a[data-id]': 'navigateClicked',
        'click a[data-action=manager]': 'managerClicked',
        'click a[data-type=hint-dashboardtitle]': 'editClicked',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        options.context.set('forceNew', true);
        this._super('initialize', [options]);

        if (this.isRecordView()) {
            if (this.getHintState()) {
                var model = this.context.parent.get('model');
                setTimeout(function() {
                    app.events.trigger('preview:render', model);
                }, 0);
            }

            app.events.on('preview:close', function() {
                this.setHintState(false);
            }, this);
        }
    },

    /**
     * Get hint state
     *
     * @return {string}
     */
    getHintState: function() {
        return app.user.lastState.get(this.hintStateKey);
    },

    /**
     * Set hint state
     *
     * @param {string} value
     */
    setHintState: function(value) {
        app.user.lastState.set(this.hintStateKey, value);
    },

    /**
     * Check if is record view
     *
     * @return {bool}
     */
    isRecordView: function() {
        var ctxParent = this.context.parent;
        return ctxParent && ctxParent.get('dataView') === 'record';
    },

    /**
     * @inheritdoc
     */
    toggleClicked: function(event) {
        this._super('toggleClicked', [event]);
        var isNotAdded = this.$('.dropdown-menu [data-id=\'stage2\']').length < 1;
        if (this.isRecordView() && isNotAdded) {
            var template = `<li><a href=\'javascript:void(0);\' data-id=\'stage2\'>${Handlebars.Utils.escapeExpression(
                app.lang.get('LBL_HINT_PANEL')
            )}</a></li>`;
            this.$('.dropdown-menu').prepend(template);
        }
    },

    /**
     * @inheritdoc
     */
    navigateClicked: function(evt) {
        this._super('navigateClicked', [evt]);
    },

    /**
     * @inheritdoc
     */
    managerClicked: function() {
        this._super('managerClicked', []);
    },

    /**
     * @inheritdoc
     */
    editClicked: function(evt) {
        this._super('editClicked', [evt]);
    },

    /**
     * @inheritdoc
     */
    navigate: function(id, type) {
        var isHintState = id === 'stage2';

        this.setHintState(isHintState);
        if (isHintState) {
            app.events.trigger('preview:render', this.context.parent.get('model'));
        } else {
            this._super('navigate', [id, type]);
        }
    }
});
