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
 * FriendlyTime displays a duration in a human-readable manner.
 *
 * @class View.Fields.Base.FriendlyTimeField
 * @alias SUGAR.App.view.fields.BaseFriendlyTimeField
 * @extends View.Fields.Base.BaseField
 */
({
    _baseUnit: 'minutes',

    /**
     * Display the duration in a human-friendly manner.
     *
     * @param {number} elapsed Elapsed time.
     * @return {string} Human-readable representation of the duration.
     * @override
     */
    format: function(elapsed) {
        if (_.isUndefined(elapsed) || _.isNull(elapsed)) {
            return app.lang.get('LBL_NO_DATA');
        } else if (elapsed === 0) {
            return this._getZeroDurationString();
        }

        var duration = app.date.duration(elapsed, this._baseUnit);
        return duration.format() || this._getZeroDurationString();
    },

    /**
     * Get the "zero" duration string.
     *
     * @return {string} The way to display a duration of 0.
     * @private
     */
    _getZeroDurationString: function() {
        return '0 ' + app.lang.get('LBL_DURATION_' + this._baseUnit.toUpperCase());
    }
})
