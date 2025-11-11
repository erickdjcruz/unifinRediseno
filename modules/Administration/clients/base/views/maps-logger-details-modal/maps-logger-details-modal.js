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
 * @class View.Views.Base.AdministrationMapsLoggerDetailsModalView
 * @alias SUGAR.App.view.views.AdministrationMapsLoggerDetailsModalView
 * @extends View.View
 */
({
    /**
     * @inheritdoc
     */
    events: {
        'click .close': 'closeModal',
        'click [class="modal-backdrop in"]': 'closeModal',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        this._initProperties(options);
    },

    /**
     * Init properties
     *
     * @param {Object} options
     */
    _initProperties: function(options) {
        this.detailedLogs = options.detailedLogs;
    },

    /**
     * Open Detail Modal
     */
    openModal: function() {
        this.render();

        let modalEl = this.$('[data-content=maps-logger-details-modal]');

        modalEl.modal({
            backdrop: 'static'
        });
        modalEl.modal('show');

        modalEl.on('hidden.bs.modal', _.bind(function handleModalClose() {
            this.$('[data-content=maps-logger-details-modal]').remove();
        }, this));
    },

    /**
     * Close the modal and destroy it
     */
    closeModal: function() {
        this.dispose();
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        this.$('[data-content=maps-logger-details-modal]').remove();
        $('.modal-backdrop').remove();

        this._super('_dispose');
    },
});
