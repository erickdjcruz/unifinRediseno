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
 * @class View.Views.Base.Calendar.AddCalendarcontainerView
 * @alias SUGAR.App.view.views.BaseCalendarAddCalendarcontainerView
 * @extends View.Views.Base.FlexListView
 */
({
    extendsFrom: 'FlexListView',

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        var plugins = [
            'ListColumnEllipsis',
            'Pagination',
        ];

        this.plugins = _.union(this.plugins || [], plugins);
        this._super('initialize', [options]);

        this.context.on('change:selection_model', this._selectUserOrTeamAndClose, this);
        this.context.on('calendar:change', this._selectCalendarAndClose, this);

        this.events = {
            'click .single': 'triggerCheck'
        };
        this.newCalendar = {
            calendarId: '',
            userId: '',
            teamId: ''
        };
    },

    /**
     * Closes the drawer passing the selected model attributes to the callback if calendar is set
     *
     * @param {Object} context
     * @param {Data.Bean} selectionModel The selected calendar configuration.
     */
    _selectUserOrTeamAndClose: function(context, selectionModel) {
        var selectedModule = selectionModel.get('_module');

        if (selectedModule == 'Users') {
            this.newCalendar.userId = selectionModel.get('id');
            this.newCalendar.teamId = '';
        } else if (selectedModule == 'Teams') {
            this.newCalendar.userId = '';
            this.newCalendar.teamId = selectionModel.get('id');
        }

        if (!_.isEmpty(this.newCalendar.calendarId)) {
            app.drawer.close(this.newCalendar);
        }
    },

    /**
     * Select calendar and eventually close the drawer
     *
     * @param {string} calendarId
     */
    _selectCalendarAndClose: function(calendarId) {
        this.newCalendar.calendarId = calendarId;

        if (!_.isEmpty(this.newCalendar.userId) || !_.isEmpty(this.newCalendar.teamId)) {
            app.drawer.close(this.newCalendar);
        }
    },

    /**
     * Trigger check
     *
     * @param {Object} event
     */
    triggerCheck: function(event) {
        var checkbox = $(event.currentTarget).find('[data-check=one]');
        checkbox[0].click();
    }
});
