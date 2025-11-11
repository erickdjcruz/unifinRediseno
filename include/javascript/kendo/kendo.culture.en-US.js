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
 * Expanded Month View - change from default behavior: instad of showing max 2 events in a cell, show them all
 */
kendo.cultures["en-US"] = {
    // <language code>-<country/region code>
    name: "en-US",
      // The "numberFormat" defines general number formatting rules.
    numberFormat: {
        //numberFormat has only negative pattern unlike the percent and currency
        //negative pattern: one of (n)|-n|- n|n-|n -
        pattern: ["-n"],
        //number of decimal places
        decimals: 2,
        //string that separates the number groups (1,000,000)
        ",": ",",
        // A string that separates a number from the fractional point.
        ".": ".",
        //the length of each number group
        groupSize: [3],
        //formatting rules for percent number
        percent: {
            //[negative pattern, positive pattern]
            // negativePattern: one of -n %|-n%|-%n|%-n|%n-|n-%|n%-|-% n|n %-|% n-|% -n|n- %
            //positivePattern: one of n %|n%|%n|% n
            pattern: ["-n %", "n %"],
            // The number of decimal places.
            decimals: 2,
            // The string that separates the number groups (1,000,000 %).
            ",": ",",
            // The string that separates a number from the fractional point.
            ".": ".",
            // The length of each number group.
            groupSize: [3],
            //percent symbol
            symbol: "%"
        },
        currency: {
            // [negative pattern, positive pattern]
            // negativePattern: one of "($n)|-$n|$-n|$n-|(n$)|-n$|n-$|n$-|-n $|-$ n|n $-|$ n-|$ -n|n- $|($ n)|(n $)"
            //positivePattern: one of "$n|n$|$ n|n $"
            pattern: ["($n)", "$n"],
            // The number of decimal places.
            decimals: 2,
            // The string that separates the number groups (1,000,000 $).
            ",": ",",
            // The string that separates a number from the fractional point.
            ".": ".",
            // The length of each number group.
            groupSize: [3],
            // The currency symbol.
            symbol: "$"
        }
    },
    calendars: {
        standard: {
            days: {
                // The full day names.
                names : [
                    App.lang.getModString('LBL_SUNDAY', 'Calendar'),
                    App.lang.getModString('LBL_MONDAY', 'Calendar'),
                    App.lang.getModString('LBL_TUESDAY', 'Calendar'),
                    App.lang.getModString('LBL_WEDNESDAY', 'Calendar'),
                    App.lang.getModString('LBL_THURSDAY', 'Calendar'),
                    App.lang.getModString('LBL_FRIDAY', 'Calendar'),
                    App.lang.getModString('LBL_SATURDAY', 'Calendar'),
                ],
                // The abbreviated day names.
                namesAbbr : [
                    App.lang.getModString('LBL_SUN', 'Calendar'),
                    App.lang.getModString('LBL_MON', 'Calendar'),
                    App.lang.getModString('LBL_TUE', 'Calendar'),
                    App.lang.getModString('LBL_WED', 'Calendar'),
                    App.lang.getModString('LBL_THU', 'Calendar'),
                    App.lang.getModString('LBL_FRI', 'Calendar'),
                    App.lang.getModString('LBL_SAT', 'Calendar'),
                ],
                // The shortest day names.
                namesShort : [
                    App.lang.getModString('LBL_SU', 'Calendar'),
                    App.lang.getModString('LBL_MO', 'Calendar'),
                    App.lang.getModString('LBL_TU', 'Calendar'),
                    App.lang.getModString('LBL_WE', 'Calendar'),
                    App.lang.getModString('LBL_TH', 'Calendar'),
                    App.lang.getModString('LBL_FR', 'Calendar'),
                    App.lang.getModString('LBL_SA', 'Calendar'),
                ]
            },
            months: {
                // The full month names.
                names: [
                    App.lang.getModString('LBL_JANUARY', 'Calendar'),
                    App.lang.getModString('LBL_FEBRUARY', 'Calendar'),
                    App.lang.getModString('LBL_MARCH', 'Calendar'),
                    App.lang.getModString('LBL_APRIL', 'Calendar'),
                    App.lang.getModString('LBL_MAY', 'Calendar'),
                    App.lang.getModString('LBL_JUNE', 'Calendar'),
                    App.lang.getModString('LBL_JULY', 'Calendar'),
                    App.lang.getModString('LBL_AUGUST', 'Calendar'),
                    App.lang.getModString('LBL_SEPTEMBER', 'Calendar'),
                    App.lang.getModString('LBL_OCTOBER', 'Calendar'),
                    App.lang.getModString('LBL_NOVEMBER', 'Calendar'),
                    App.lang.getModString('LBL_DECEMBER', 'Calendar'),
                ],
                // abbreviated month names
                namesAbbr: [
                    App.lang.getModString('LBL_JAN', 'Calendar'),
                    App.lang.getModString('LBL_FEB', 'Calendar'),
                    App.lang.getModString('LBL_MAR', 'Calendar'),
                    App.lang.getModString('LBL_APR', 'Calendar'),
                    App.lang.getModString('LBL_MAY', 'Calendar'),
                    App.lang.getModString('LBL_JUN', 'Calendar'),
                    App.lang.getModString('LBL_JUL', 'Calendar'),
                    App.lang.getModString('LBL_AUG', 'Calendar'),
                    App.lang.getModString('LBL_SEP', 'Calendar'),
                    App.lang.getModString('LBL_OCT', 'Calendar'),
                    App.lang.getModString('LBL_NOV', 'Calendar'),
                    App.lang.getModString('LBL_DEC', 'Calendar'),
                ]
            },
              // The AM and PM designators.
              // [standard,lowercase,uppercase]
            AM: [ "AM", "am", "AM" ],
            PM: [ "PM", "pm", "PM" ],
              // The set of predefined date and time patterns used by the culture.
            patterns: {
                d: "M/d/yyyy",
                D: "dddd, MMMM dd, yyyy",
                F: "dddd, MMMM dd, yyyy h:mm:ss tt",
                g: "M/d/yyyy h:mm tt",
                G: "M/d/yyyy h:mm:ss tt",
                m: "MMMM dd",
                M: "MMMM dd",
                s: "yyyy'-'MM'-'ddTHH':'mm':'ss",
                t: "h:mm tt",
                T: "h:mm:ss tt",
                u: "yyyy'-'MM'-'dd HH':'mm':'ss'Z'",
                y: "MMMM, yyyy",
                Y: "MMMM, yyyy"
            },
              // The first day of the week (0 = Sunday, 1 = Monday, and so on).
            firstDay: 0
        }
    }
};