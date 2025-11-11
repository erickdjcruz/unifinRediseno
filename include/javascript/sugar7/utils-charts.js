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

        app.utils = _.extend(app.utils, {
            charts: {

                /**
                 * Checks if provided value is a finite number
                 * @param value
                 * @return {boolean}
                 */
                isNumeric: function(value) {
                    let v = parseFloat(value);
                    return !isNaN(v) && typeof v === 'number' && isFinite(v);
                },

                /**
                 * Rounds the value to the given number of figures
                 * @param x
                 * @param n
                 * @return {number}
                 */
                round: function(x, n) {
                    // Sigh...
                    let tenN = Math.pow(10, n);
                    return Math.round(x * tenN) / tenN;
                },

                /**
                 * Returns the number of significant figures after the decimal
                 * @param value
                 * @return {number}
                 */
                countSigFigsAfter: function(value) {
                    // consider: d3.precisionFixed(value);
                    // if value has decimals
                    // compare "$123.45k"
                    let re = /^[^\d]*([\d.,\s]+)[^\d]*$/;
                    let digits = value.toString().match(re)[1].replace(/[,\s]/, '.');
                    let sigfigs = 0;
                    if (Math.floor(digits) !== parseFloat(digits)) {
                        sigfigs = parseFloat(digits).toString().split('.').pop().length || 0;
                    }
                    return sigfigs;
                },

                /**
                 * Returns the number of significant figures before the decimal
                 * @param value
                 * @return {number}
                 */
                countSigFigsBefore: function(value) {
                    return Math.floor(value).toString().replace(/0+$/g, '').length;
                },

                /**
                 * Returns the SI decimal
                 * @param n
                 * @return {number}
                 */
                siDecimal: function(n) {
                    return Math.pow(10, Math.floor(Math.log(n) * Math.LOG10E));
                },

                /**
                 * Returns the SI value for the given identified
                 * @param si
                 * @return {number}
                 */
                siValue: function(si) {
                    if (app.utils.charts.isNumeric(si)) {
                        return app.utils.charts.siDecimal(si);
                    }
                    let units = {
                        y: 1e-24,
                        yocto: 1e-24,
                        z: 1e-21,
                        zepto: 1e-21,
                        a: 1e-18,
                        atto: 1e-18,
                        f: 1e-15,
                        femto: 1e-15,
                        p: 1e-12,
                        pico: 1e-12,
                        n: 1e-9,
                        nano: 1e-9,
                        µ: 1e-6,
                        micro: 1e-6,
                        m: 1e-3,
                        milli: 1e-3,
                        k: 1e3,
                        kilo: 1e3,
                        M: 1e6,
                        mega: 1e6,
                        G: 1e9,
                        giga: 1e9,
                        T: 1e12,
                        tera: 1e12,
                        P: 1e15,
                        peta: 1e15,
                        E: 1e18,
                        exa: 1e18,
                        Z: 1e21,
                        zetta: 1e21,
                        Y: 1e24,
                        yotta: 1e24
                    };
                    return units[si] || 0;
                },

                /**
                 * Rounds and formats the number with the provided precision, currency, and locale
                 * @param number
                 * @param precision
                 * @param currency
                 * @param locale
                 * @return {string}
                 */
                numberFormat: function(number, precision, currency, locale) {
                    let d = parseFloat(number);
                    let c = typeof currency === 'boolean' ? currency : false;
                    d = parseFloat(number);
                    c = typeof currency === 'boolean' ? currency : false;
                    if (!app.utils.charts.isNumeric(d) || (d === 0 && !c)) {
                        return number.toString();
                    }
                    let m = app.utils.charts.countSigFigsAfter(d);
                    let p = app.utils.charts.isNumeric(precision) ?
                        Math.floor(precision) :
                        typeof locale !== 'undefined' ?
                            locale.precision :
                            c ? 2 : null;
                    p = !app.utils.charts.isNumeric(p) ? m : m && c ? p : Math.min(p, m);
                    let f = typeof locale === 'undefined' ? d3sugar.format : d3sugar.formatLocale(locale).format;
                    let s = c ? '$,' : ',';
                    s += m ? ('.' + p + 'f') : '';
                    return f(s)(d);
                },

                /**
                 * Formats the number with the provided precision, currency, and locale
                 * @param d
                 * @param p
                 * @param c
                 * @param l
                 * @return {string}
                 */
                numberFormatFixed: function(d, p, c, l) {
                    if (!app.utils.charts.isNumeric(d)) {
                        return d.toString();
                    }
                    c = typeof c === 'boolean' ? c : false;
                    p = app.utils.charts.isNumeric(p) ? p : c ? 2 : 0;
                    let f = typeof l === 'undefined' ? d3sugar.format : d3sugar.formatLocale(l).format;
                    let s = c ? '$,' : ',';
                    s += '.' + p + 'f';
                    return f(s)(d);
                },

                /**
                 * Formats a number in SI units
                 * @param d
                 * @param p
                 * @param c
                 * @param l
                 * @return {string}
                 */
                numberFormatSI: function(d, p, c, l) {
                    if (!app.utils.charts.isNumeric(d)) {
                        return d;
                    }
                    c = typeof c === 'boolean' ? c : false;
                    p = app.utils.charts.isNumeric(p) ? p : 0;
                    let f = typeof l === 'undefined' ? d3sugar.format : d3sugar.formatLocale(l).format;
                    let m = app.utils.charts.countSigFigsAfter(d);
                    let s = c ? '$,' : ',';
                    // if currency less than 1k with decimal places
                    if (c && m && d < 1000) {
                        d = app.utils.charts.round(d, p);
                        m = app.utils.charts.countSigFigsAfter(d);
                        p = Math.min(m, p);
                        if (p === 1) {
                            p = 2;
                        }
                        // try using: d = parseFloat(d.toFixed(p)).toString();
                        // use fixed formatting with rounding
                        s += '.' + p + 'f';
                    }
                    // if absolute value less than 1
                    else if (Math.abs(d) < 1) {
                        s += (
                            // if rounding to precision results in 0
                            +d.toFixed(p) === 0 ?
                                // use next si unit
                                '.1s' :
                                // round to a single significant figure
                                '.' + Math.min(p, m) + 'f'
                        );
                    }
                    // if absolute value less than 1k
                    else if (Math.abs(d) < 1000) {
                        d = app.utils.charts.round(d, p);
                    } else if (Math.abs(d) >= 1e9) {
                        // Format as billions with "B"
                        d /= 1e9;
                        f = typeof l === 'undefined' ? d3sugar.format : d3sugar.formatLocale(l).format;
                        return f('s')(d) + 'B';
                    } else {
                        f = typeof l === 'undefined' ? d3sugar.formatPrefix : d3sugar.formatLocale(l).formatPrefix;
                        if (p !== 0) {
                            var d1 = f('.' + p + 's', d)(d);
                            var d2 = d1.split('.').pop().replace(/[^\d]+$/, '').match(/0+$/g);
                            if (Array.isArray(d2)) {
                                p -= d2.pop().length;
                            }
                        }
                        s += '.' + p + 's';
                        return f(s, d)(d);
                    }
                    return f(s)(d);
                },

                /**
                 * Formats number in SI units to a fixed position
                 * @param d
                 * @param p
                 * @param c
                 * @param l
                 * @param si
                 * @return {string}
                 */
                numberFormatSIFixed: function(d, p, c, l, si) {
                    if (!app.utils.charts.isNumeric(d)) {
                        return d.toString();
                    }
                    c = typeof c === 'boolean' ? c : false;
                    p = app.utils.charts.isNumeric(p) ? p : c ? 2 : 0;
                    let f = typeof l === 'undefined' ? d3sugar.formatPrefix : d3sugar.formatLocale(l).formatPrefix;
                    si = app.utils.charts.siValue(si);
                    let s = c ? '$,' : ',';
                    s += '.' + p + 's';
                    return f(s, si)(d);
                },

                /**
                 * Formats number as a percentage with given precision
                 * @param number
                 * @param total
                 * @param locale
                 * @return {string}
                 */
                numberFormatPercent: function(number, total, locale) {
                    let t = parseFloat(total);
                    let n = app.utils.charts.isNumeric(t) && t > 0 ? (number * 100 / t) : 100;
                    let p = locale && typeof locale.precision !== 'undefined' ? locale.precision : 1;
                    let d = app.utils.charts.numberFormat(n, p, false, locale);
                    //TODO: d3.format does not support locale percent formatting (boo)
                    //Some countries have space between number and symbol and some countries put symbol at the beginning
                    return d + '%';
                },

                /**
                 * Builds a d3 locality map
                 * @param l
                 * @param d
                 * @return {Object}
                 */
                buildLocality: function(l, d) {
                    let locale = l || {};
                    let deep = !!d;
                    let unfer = function(a) {
                        return a.join('|').split('|').map(function(b) {
                            return !(b) ? '' : isNaN(b) ? b : +b;
                        });
                    };
                    let definition = {
                        'decimal': '.',
                        'thousands': ',',
                        'grouping': [3],
                        'currency': ['$', ''],
                        'precision': 2,
                        'periods': ['AM', 'PM'],
                        'days': ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                        'shortDays': ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                        'months': [
                            'January', 'February', 'March', 'April', 'May', 'June', 'July',
                            'August', 'September', 'October', 'November', 'December'
                        ],
                        'shortMonths': [
                            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                        ],
                        'date': '%b %-d, %Y', //%x
                        'time': '%-I:%M:%S %p', //%X
                        'dateTime': '%B %-d, %Y at %X GMT%Z', //%c
                        // Custom patterns
                        'full': '%A, %c',
                        'long': '%c',
                        'medium': '%x, %X',
                        'short': '%-m/%-d/%y, %-I:%M %p',
                        'yMMMEd': '%a, %x',
                        'yMEd': '%a, %-m/%-d/%Y',
                        'yMMMMd': '%B %-d, %Y',
                        'yMMMd': '%x',
                        'yMd': '%-m/%-d/%Y',
                        'yMMMM': '%B %Y',
                        'yMMM': '%b %Y',
                        'MMMd': '%b %-d',
                        'MMMM': '%B',
                        'MMM': '%b',
                        'y': '%Y'
                    };

                    Object.getOwnPropertyNames(locale).forEach(function(key) {
                        let def = locale[key];
                        definition[key] = !deep || !Array.isArray(def) ? def : unfer(def);
                    });

                    return definition;
                },
            },
        });
    });
})(SUGAR.App);
