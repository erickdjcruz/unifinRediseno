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
/*global $, jCore */

var setSelectedNode = function (shape) {
    var id = "#" + $('a[uid ="' + shape.getID() + '"]').attr("desc");

    $(".treechild").attr("status", "unmarked");
    $(".treechild").css("background", "#fff");

    $(id).css("background", "#CEE3F6");
    $(id).attr("status", "marked");
//    var oShape = {};
};
