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
 * @extends View.Views.Base.View
 */
 ({
    className: 'calendarAddContainer',

    /**
     * @override
     */
    _render: function() {
        this._super('_render');

        let calendarParams = {
            def: {
                name: 'calendar',
                type: 'relate',
                custom_module: 'Calendar',
                ext2: 'Calendar',
                id_name: 'id',
                module: 'Calendar',
                quicksearch: 'enabled',
                required: false,
                source: 'non-db'
            },
            view: this,
            viewName: 'edit',
            model: this.model
        };
        const dashletSource = this.context.get('dashletSource');
        const mainCalendarSource = this.context.get('mainCalendarSource');

        const calendarModules = [app.controller.context.get('module')];

        if (dashletSource !== true && mainCalendarSource !== true) {
            calendarParams.def = _.extend(calendarParams.def, {
                initial_filter: 'available_calendars',
                initial_filter_label: 'LBL_CALENDAR_AVAILABLE_CALENDARS',
                filter_populate: {
                    'calendar_module':  {
                        $in: calendarModules
                    }
                }
            });
        }

        this.calendarField = app.view.createField(calendarParams);
        this.calendarField.render();
        this.$('[data-content=calendar-field]').html(this.calendarField.$el);

        this.listenTo(this.calendarField.model, 'change:calendar', _.bind(function() {
            this.context.trigger('calendar:change', this.calendarField.model.get('id'));
        }, this));
    }
});
