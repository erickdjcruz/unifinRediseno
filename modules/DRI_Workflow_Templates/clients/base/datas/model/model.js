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
 * @class Model.Datas.Base.DriWorkflowTemplatesModel
 * @alias SUGAR.App.model.datas.BaseDriWorkflowTemplatesModel
 * @extends Data.Bean
 */
({
    /**
     * Set the copied template id and name
     *
     * @param {Object} parent
     */
    copy: function(parent) {
        app.Bean.prototype.copy.call(this, parent);
        this.set('copied_template_id', parent.get('id'));
        this.set('copied_template_name', parent.get('name'));
    }
});
