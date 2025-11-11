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
 * @class View.Fields.Base.Calls.CallRecordingField
 * @alias SUGAR.App.view.fields.BaseCallsCallRecordingField
 * @extends View.Fields.Base.BaseField
 */
({
    /**
     * The call recording URL
     */
    recordingUrl: '',

    /**
     * The friendly display name
     */
    recordingName: '',

    /**
     * @inheritdoc
     */
    _render: function() {
        this.setRecordingUrl();
        this._super('_render');
    },

    /**
     * Set the call recording URL and the friendly display name
     */
    setRecordingUrl: function() {
        this.recordingUrl = this.model.get('call_recording_url');

        if (this.recordingUrl) {
            this.recordingName = app.date(this.model.get('date_entered')).formatUser();
        }
    }
})
