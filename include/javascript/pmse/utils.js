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
/*global SUGAR, canvas, Document*/

var translate = function (label, module, replace) {
    //var string = (SUGAR.language.languages.ProcessMaker[label]) ? SUGAR.language.languages.ProcessMaker[label] : label;
    var string, language, arr;
    if (!module){
        if (!window.CURRENT_MODULE) {
            module = 'pmse_Project';
        } else {
            module = window.CURRENT_MODULE;
        }
    }
    if (App) {
        string = App.lang.get(label, module);
    } else {
        language = SUGAR.language.languages;
        arr = language[module];
        string = (arr && arr[label]) ? arr[label] : label;
    }
    if (!replace) {
        return string;
    } else {
        return string.toString().replace(/\%s/, replace);
    }
};

/**
 * Determines whether the given module is related to the Business Centers module
 * @param moduleName string containing the module name
 * @return {boolean} true if the given module has a relation to Business Centers, false otherwise
 */
var isRelatedToBusinessCenters = function(moduleName) {
    var moduleMetadata = App.metadata.getModule(moduleName);
    var moduleFields = moduleMetadata ? moduleMetadata.fields : null;

    // Check the fields of the module for links to the Business Centers module
    for (var field in moduleFields) {
        if (moduleFields[field].type === 'link' && moduleFields[field].relationship) {
            var rel = App.metadata.getRelationship(moduleFields[field].relationship);
            if ((rel.lhs_module === moduleName && rel.rhs_module === 'BusinessCenters') ||
                (rel.lhs_module === 'BusinessCenters' && rel.rhs_module === moduleName)) {
                return true;
            }
        }
    }
    return false;
};

/**
 * Iterates over an array of fields and adds an options object to each Datetime field. The options object contains
 * properties indicating what business center options should be included in the business center dropdown for timespan
 * panels that are built for the field.
 * @param options object containing options as described below:
 *        options.targetModule: string containing the target module name
 *        options.selectedModule: string containing the selected (filter) module name
 *        options.fields: array containing fields data for the selected (filter) module
 *        options.showTargetModuleOption: boolean indicating whether the "From Target Module" option should be shown if
 *                                        the target module is related to the BusinessCenters module
 *        options.showSelectedModuleOption: string indicating whether the "From {selectedModule} Module" option should
 *                                          be shown if the selected (filter) module is related to the BusinessCenters
 *                                          module. If empty, the option will not be shown. Otherwise, if the selected
 *                                          module is related to the BusinessCenters module, the option will appear in
 *                                          the above format.
 */
var setDatetimeFieldsBCOptions = function(options) {
    var targetRelatedToBC = isRelatedToBusinessCenters(options.targetModule);
    var selectedRelatedToBC = isRelatedToBusinessCenters(options.selectedModule);
    var fields = options.fields ? options.fields : [];

    for (var i = 0; i < fields.length; i++) {
        if (fields[i].type === 'Datetime') {
            fields[i].optionItem = {
                businessHours: {
                    show: true,
                    targetModuleBC: targetRelatedToBC && options.showTargetModuleOption,
                    selectedModuleBC: selectedRelatedToBC && options.showSelectedModuleOption ?
                        options.selectedModule : ''
                }
            };
        }
    }

    return fields;
};

/**
 * Removes html entities like &quot from a string
 * @param str
 * @return {string}
 */
var decodeHtmlEntities = function(str) {
    var txt = document.createElement('textarea');
    txt.innerHTML = str;
    return txt.value;
};
