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
({
    extendsFrom: 'RecordView',

    duplicateClicked: function() {
        let self = this;
        let prefill = app.data.createBean('Notes');

        prefill.copy(this.model);
        this._copyNestedCollections(this.model, prefill);
        self.model.trigger('duplicate:before', prefill);
        prefill.unset('id');
        prefill.unset('is_escalated');
        prefill.attributes.attachment_list.models = [];

        app.drawer.open({
            layout: 'create',
            context: {
                create: true,
                model: prefill,
                copiedFromModelId: this.model.get('id')
            }
        }, function(context, newModel) {
            if (newModel && newModel.id) {
                if (self.closestComponent('side-drawer')) {
                    let recordContext = {
                        layout: 'record',
                        dashboardName: newModel.get('name'),
                        context: {
                            layout: 'record',
                            name: 'record-drawer',
                            contentType: 'record',
                            modelId: newModel.id,
                            dataTitle: app.sideDrawer.getDataTitle('Notes', 'LBL_RECORD', newModel.get('name'))
                        }
                    };
                    app.sideDrawer.open(recordContext, null, true);
                    return;
                }
                app.router.navigate('Notes' + '/' + newModel.id, {trigger: true});
            }
        });

        prefill.trigger('duplicate:field', self.model);
    }
})
