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
 * @class View.Views.Portal.PreviewView
 * @alias SUGAR.App.view.views.PortalPreviewView
 * @extends View.Views.Base.PreviewView
 */
({
    extendsFrom: 'PreviewView',

    initialize: function(options) {
        this._super('initialize', [options]);
        // we need to force to use record layout for portal, in case base preview layout file exists.
        // this restriction should be removed in the future when its supported to edit portal preview layout in studio.
        this.dataView = 'record';
        var recordMeta = app.metadata.getView(options.module, 'record');
        this.meta = _.extend(this.meta, this._previewifyMetadata(_.extend({}, recordMeta)));
    },
})
