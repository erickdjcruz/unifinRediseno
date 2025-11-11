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
(function utils(app) {
    app.Calendar = app.Calendar || {};

    let utils = {};

    /**
     * Builds a unique id for a user and a component
     */
    utils.buildUserKeyForStorage = function buildKeyForStorage(componentName) {
        return 'Calendar:' + app.user.id + ':' + componentName + ':storage';
    };

    /**
     * Builds a unique id for a user and a component
     */
    utils.buildUserKeyForShowFilteredRecords = function buildKeyForStorage(componentName) {
        return 'Calendar:' + app.user.id + ':' + componentName + ':applyFilter';
    };

    /**
     * Get CalendarConfigurations by key
     *
     * @param {string} key
     * @param {string} calendarCategory
     * @return {Mixed} An Array or an Object with myCalendars and otherCalendars
     */
    utils.getConfigurationsByKey = function getConfigurationsByKey(key, calendarCategory) {
        let calendarsSaved = app.user.lastState.get(key);

        let calendarConfigurations = {
            myCalendars: [],
            otherCalendars: []
        };

        if (typeof calendarsSaved === 'object') {
            if (!_.isEmpty(calendarCategory)) {
                return calendarsSaved[calendarCategory];
            }
            if (calendarsSaved.myCalendars.length > 0) {
                calendarConfigurations.myCalendars = calendarsSaved.myCalendars;
            }

            if (calendarsSaved.otherCalendars.length > 0) {
                calendarConfigurations.otherCalendars = calendarsSaved.otherCalendars;
            }
        }

        if (!_.isEmpty(calendarCategory)) {
            return calendarConfigurations[calendarCategory];
        }

        return calendarConfigurations;
    };

    utils.whiteColor = function whiteColor(c) {
        c = c.substring(1); // strip #
        const rgb = parseInt(c, 16); // convert rrggbb to decimal
        const r = (rgb >> 16) & 0xff; // extract red
        const g = (rgb >> 8) & 0xff; // extract green
        const b = (rgb >> 0) & 0xff; // extract blue

        const luma = 0.2126 * r + 0.7152 * g + 0.0722 * b; // per ITU-R BT.709

        if (luma < 100) {
            return false;
        }
        return true;
    };

    /**
     * Returns a hash code based on given input string
     *
     * @method hashFnv32a
     * @param  {string} str String to generate a hash from
     * @param  {boolean} asString Convert to 8 digit hex string
     * @param  {string} seed 8 digit hex string
     * @return {string} Hash code based on given string
     */
    function hashFnv32a(str, asString, seed) {
        /*jshint bitwise:false */
        let i;
        let l;
        let hval = seed === undefined ? 0x811c9dc5 : seed;

        for (i = 0, l = str.length; i < l; i++) {
            hval ^= str.charCodeAt(i);
            hval +=
                (hval << 1) +
                (hval << 4) +
                (hval << 7) +
                (hval << 8) +
                (hval << 24);
        }

        if (asString) {
            // Convert to 8 digit hex string
            // eslint-disable-next-line no-magic-numbers
            return ('0000000' + (hval >>> 0).toString(16)).substr(-8);
        }

        return hval >>> 0;
    }

    utils.pastelColor = function(str) {
        let hash = hashFnv32a(str);

        const rHash = Math.abs(hash % 200);
        hash = Math.round(hash / 1000);
        const gHash = Math.abs(hash % 147);
        hash = Math.round(hash / 1000);
        const bHash = Math.abs(hash % 147);

        const r = (rHash + 50).toString(16);
        const g = (gHash + 70).toString(16);
        const b = (bHash + 70).toString(16);

        return '#' + r + g + b;
    };

    const dateMapping = {
        'Y-m-d': {
            full: 'yyyy-MM-dd',
            dayAndMonth: 'MM-dd',
            dayAndVerboseMonth: 'MMMM dd',
            fullVerboseMonth: 'yyyy, MMMM dd',
        },
        'm-d-Y': {
            full: 'MM-dd-YYYY',
            dayAndMonth: 'MM-dd',
            dayAndVerboseMonth: 'MMMM dd',
            fullVerboseMonth: 'MMMM dd, yyyy',
        },
        'd-m-Y': {
            full: 'dd-MM-YYYY',
            dayAndMonth: 'dd-MM',
            dayAndVerboseMonth: 'dd MMMM',
            fullVerboseMonth: 'dd MMMM, yyyy',
        },
        'Y/m/d': {
            full: 'yyyy/MM/dd',
            dayAndMonth: 'MM/dd',
            dayAndVerboseMonth: 'MMMM dd',
            fullVerboseMonth: 'yyyy, MMMM dd',
        },
        'm/d/Y': {
            full: 'MM/dd/yyyy',
            dayAndMonth: 'MM/dd',
            dayAndVerboseMonth: 'MMMM dd',
            fullVerboseMonth: 'MMMM dd, yyyy',
        },
        'd/m/Y': {
            full: 'dd/MM/yyyy',
            dayAndMonth: 'dd/MM',
            dayAndVerboseMonth: 'dd MMMM',
            fullVerboseMonth: 'dd MMMM, yyyy',
        },
        'Y.m.d': {
            full: 'yyyy.MM.dd',
            dayAndMonth: 'MM.dd',
            dayAndVerboseMonth: 'MMMM dd',
            fullVerboseMonth: 'yyyy, MMMM dd',
        },
        'd.m.Y': {
            full: 'dd.MM.yyyy',
            dayAndMonth: 'dd.MM',
            dayAndVerboseMonth: 'dd MMMM',
            fullVerboseMonth: 'dd MMMM, yyyy',
        },
        'm.d.Y': {
            full: 'MM.dd.yyyy',
            dayAndMonth: 'MM.dd',
            dayAndVerboseMonth: 'MMMM dd',
            fullVerboseMonth: 'MMMM dd, yyyy',
        },
    };

    const timeMapping = {
        'H:i': 'H:mm',
        'h:ia': 'h:mmtt',
        'h:iA': 'h:mmtt',
        'h:i a': 'h:mm tt',
        'h:i A': 'h:mm tt',
        'H.i': 'H.mm',
        'h.ia': 'h.mmtt',
        'h.iA': 'h.mmtt',
        'h.i a': 'h.mm tt',
        'h.i A': 'h.mm tt'
    };

    /**
     * Get kendo date mapping
     *
     * @param {string} datePref Sugar date format
     * @param {string} interestingParts
     * @return {string} Kendo date format
     */
    utils.getKendoDateMapping = function(datePref, interestingParts) {
        return dateMapping[datePref][interestingParts] || dateMapping[datePref].full;
    };

    /**
     * Get kendo time mapping
     *
     * @param {string} timePref Sugar time format
     * @return {string} Kendo time format
     */
    utils.getKendoTimeMapping = function(timePref) {
        return timeMapping[timePref];
    };

    app.Calendar.utils = utils;

})(SUGAR.App);
