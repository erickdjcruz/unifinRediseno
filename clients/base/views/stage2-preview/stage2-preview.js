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
 * @class View.Views.Base.RecordView
 * @alias SUGAR.App.view.views.RecordView
 */
({
    extendsFrom: 'RecordView',

    plugins: ['ToggleMoreLess', 'Stage2CssLoader'],

    events: {
        'click [data-action="copy"]': 'copy',

        'click [data-action="recordLinkClick"]': '_socialLinkClicked',
        // Have to define these events and related functions for sugar7 tooltip implementation.
        'mouseenter [rel="tooltip"]': 'showTooltip',
        'mouseleave [rel="tooltip"]': 'hideTooltip'
    },

    /**
     * Show tooltip
     *
     * @param {Object} e
     */
    showTooltip: function(e) {
        this.$(e.currentTarget).tooltip('show');
    },

    /**
     * Hide tooltip
     *
     * @param {Object} e
     */
    hideTooltip: function(e) {
        this.$(e.currentTarget).tooltip('hide');
    },

    enrichFieldsOptions: [],
    containsPreAccountFields: false,
    shouldShowHintCloudLogo: false,
    previousAccountQueue: [],
    switching: false,
    isHintRequestLoading: true,
    SUGAR_INTERNAL_FIELD_SUFFIX: '',
    SUGAR_INTERNAL_FIELD_PREFIX: 'hint_',
    oldHintPreview: '',
    isListView: '',
    fieldTypeTranslations: {
        'title': 'LBL_DASHLET_CONFIGURE_TITLE',
        'hint_account_pic': 'LBL_HINT_COMPANY_LOGO',
        'hint_contact_pic': 'LBL_HINT_CONTACTS_AVATAR',
        'phone_work': 'LBL_HINT_OFFICE_PHONE',
        'phone_mobile': 'LBL_HINT_MOBILE_PHONE',
        'phone_other': 'LBL_HINT_OTHER_PHONE',
        'website': 'LBL_HINT_WEBSITE',
        'hint_education': 'LBL_HINT_EDUCATION',
        'hint_education_2': 'LBL_HINT_EDUCATION_2',
        'hint_job_2': 'LBL_HINT_JOB_2',
        'hint_facebook': 'LBL_HINT_FACEBOOK',
        'hint_twitter': 'LBL_HINT_TWITTER',
        'account_name': 'LBL_LIST_ACCOUNT_NAME',
        'description': 'LBL_HINT_COMPANY_DESCRIPTION',
        'sic_code': 'LBL_HINT_COMPANY_SIC_CODE_LABEL',
        'twitter': 'LBL_HINT_COMPANY_TWITTER',
        'hint_account_industry_tags': 'LBL_HINT_COMPANY_INDUSTRY_TAGS',
        'annual_revenue': 'LBL_HINT_COMPANY_ANNUAL_REVENUE',
        'hint_account_website': 'LBL_HINT_COMPANY_WEBSITE',
        'hint_account_size': 'LBL_HINT_COMPANY_SIZE',
        'hint_account_industry': 'LBL_HINT_COMPANY_INDUSTRY',
        'hint_account_location': 'LBL_HINT_COMPANY_LOCATION',
        'hint_account_annual_revenue': 'LBL_HINT_COMPANY_ANNUAL_REVENUE',
        'hint_account_description': 'LBL_HINT_COMPANY_DESCRIPTION',
        'hint_account_naics_code_lbl': 'LBL_HINT_COMPANY_NAICS_CODE_LABEL',
        'hint_account_sic_code_label': 'LBL_HINT_COMPANY_SIC_CODE_LABEL',
        'hint_account_fiscal_year_end': 'LBL_HINT_COMPANY_FISCAL_YEAR_END',
        'hint_account_founded_year': 'LBL_HINT_COMPANY_FOUNDED_YEAR',
        'hint_account_facebook_handle': 'LBL_HINT_COMPANY_FACEBOOK',
        'hint_account_twitter_handle': 'LBL_HINT_COMPANY_TWITTER',
        'hint_industry_tags': 'LBL_HINT_COMPANY_INDUSTRY_TAGS',
    },
    uniqueEmail: '',
    /**
     * hintPanelFields array needs to be updated when we update the fields in the hint panel view.
     */
    hintPanelFields: [
        'title',
        'phone_work',
        'phone_mobile',
        'phone_other',
        'website',
        'hint_education',
        'hint_education_2',
        'hint_job_2',
        'hint_facebook',
        'hint_twitter',
        'account_name',
        'hint_account_website',
        'hint_account_size',
        'hint_account_industry',
        'hint_account_location',
        'hint_account_annual_revenue',
        'hint_account_description',
        'hint_account_naics_code_lbl',
        'hint_account_sic_code_label',
        'hint_account_fiscal_year_end',
        'hint_account_founded_year',
        'hint_account_facebook_handle',
        'hint_account_twitter_handle',
        'hint_industry_tags'
    ],
    preFilledDataFields: [],
    modelDiffFlag: true,
    previousAccountsName: '',
    previousAccountsWebsite: '',
    cancelName: false,
    previousBeanAccountsFields: '',
    phoneFieldTypes: ['phone_work', 'phone_other', 'phone_mobile'],

    /**
     * Use this field names to send to person enrichment.
     * Why? Data exposure, we only send fields to our endpoint that it really needs
     * We don't need all the data anyway for enriching people.
     * Enriched attribute list is also extract from this list. So the item order in this list matters.
     *
     * @return {Array} [description]
     */
    getFieldNamesForSending: function() {
        if (this.moduleName === 'Accounts') {
            return [
                'annual_revenue',
                'description',
                'email1',
                'hint_account_facebook_handle',
                'hint_account_fiscal_year_end',
                'hint_account_founded_year',
                'hint_account_industry',
                'hint_account_industry_tags',
                'hint_account_location',
                'hint_account_logo',
                'hint_account_pic',
                'hint_account_naics_code_lbl',
                'hint_account_size',
                'name',
                'sic_code',
                'tag',
                'website'
            ];
        } else {
            return [
                'title',
                'phone_work',
                'email',
                'account_name',
                'hint_education',
                'hint_education_2',
                'hint_job_2',
                'hint_facebook',
                'hint_twitter',
                'hint_account_website',
                'hint_account_size',
                'hint_account_industry',
                'hint_account_location',
                'hint_account_description',
                'hint_account_founded_year',
                'hint_account_facebook_handle',
                'hint_account_twitter_handle',
                'hint_industry_tags',
                'hint_account_naics_code_lbl',
                'hint_account_sic_code_label',
                'hint_account_fiscal_year_end',
                'hint_account_annual_revenue',
                'hint_photo',
                'picture',
                'phone_other',
                'phone_home',
                'full_name',
                'phone_mobile',
                'hint_account_logo',
                'lead_source',
                'first_name',
                'last_name',
                'salutation',
                'alt_address_city',
                'alt_address_country',
                'alt_address_postalcode',
                'alt_address_state',
                'alt_address_street',
                'primary_address_city',
                'primary_address_country',
                'primary_address_postalcode',
                'primary_address_state',
                'primary_address_street',
                'twitter',
                'website',
            ];
        }
    },

    enrichLeadsAttributeList: [
        'title',
        'phone_work',
        'hint_phone_1',
        'hint_phone_2',
        'email',
        'account_name',
        'hint_education',
        'hint_education_2',
        'hint_job_2',
        'hint_facebook',
        'hint_twitter',
        'hint_account_website',
        'hint_account_size',
        'phone_mobile',
        'phone_other',
        'hint_account_industry',
        'hint_account_location',
        'hint_account_description',
        'hint_account_founded_year',
        'hint_account_facebook_handle',
        'hint_account_twitter_handle',
        'hint_industry_tags',
        'hint_account_naics_code_lbl',
        'hint_account_sic_code_label',
        'hint_account_fiscal_year_end',
        'hint_account_annual_revenue',
    ],

    enrichAccountsAttributeList: [
        'name',
        'website',
        'description',
        'sic_code',
        'annual_revenue',
        'twitter',
        'hint_account_size',
        'hint_account_location',
        'hint_account_industry',
        'hint_account_founded_year',
        'hint_account_facebook_handle',
        'hint_account_industry_tags',
        'hint_account_naics_code_lbl',
        'hint_account_fiscal_year_end',
    ],

    saveLeadsDisableAttrList: [
        'hint_account_logo',
        'hint_photo',
        'account_name'
    ],

    saveAccountsDisableAttrList: [
        'hint_account_logo',
        'name'
    ],

    /**
     * Ends with
     *
     * @param {string} string
     * @param {string} searchTerm
     * @return {boolean}
     */
    endsWith: function(string, searchTerm) {
        var position = string.length - searchTerm.length;
        var lastIndex = string.lastIndexOf(searchTerm, position);
        return lastIndex === position;
    },

    /**
     * Starts with
     *
     * @param {string} string
     * @param {string} searchTerm
     * @return {boolean}
     */
    startsWith: function(string, searchTerm) {
        return string.substr(0, searchTerm.length) === searchTerm;
    },

    // Converts the given SugarInternal bean into Stage2 bean. This is needed before invoking
    // data enrichment on Stage2.
    convertSugarBeanToHintPayload: function(siBean) {
        var stage2bean = {};
        _.each(siBean, function(value, key) {
            if (key.length > this.SUGAR_INTERNAL_FIELD_SUFFIX.length + this.SUGAR_INTERNAL_FIELD_PREFIX.length &&
                this.endsWith(key, this.SUGAR_INTERNAL_FIELD_SUFFIX) &&
                this.startsWith(key, this.SUGAR_INTERNAL_FIELD_PREFIX)) {
                // remove suffix and prefix
                var newKey = key.substr(0, key.length - this.SUGAR_INTERNAL_FIELD_SUFFIX.length);
                newKey = newKey.replace(this.SUGAR_INTERNAL_FIELD_PREFIX, '');
                stage2bean[newKey] = value;
            } else {
                stage2bean[key] = value;
            }
        }, this);

        if (this.moduleName === 'Accounts') {
            if (!_.isEmpty(siBean.email) && _.isArray(siBean.email)) {
                stage2bean.email1 = siBean.email[0].email_address;
                if (siBean.email[1] && !_.isEmpty(siBean.email[1])) {
                    stage2bean.email2 = siBean.email[1].email_address;
                }
            }
        }
        return stage2bean;
    },

    /**
     * Convert to account bean
     *
     * @param {Object} stage2bean
     * @param {Object} siBean
     * @return {Object}
     */
    _convertToAccountBean: function(stage2bean, siBean) {
        var self = this;
        // We are not enrich these fields, but they come from the returned bean.
        // To avoid inconsistence when save enrichment result, omit these fields.
        stage2bean = _.omit(stage2bean, 'billing_address_city', 'billing_address_state',
            'billing_address_postalcode', 'billing_address_country', 'billing_address_street', 'ownership',
            'ticker_symbol');

        _.each(stage2bean, function(value, key) {
            if (this.startsWith(key, 'account_')) {
                if (key === 'account_twitter_handle') {
                    siBean.twitter = 'http://www.twitter.com/' + value;
                } else {
                    //re-map the naic code
                    if (key === 'account_naics_code_label') {
                        key = 'account_naics_code_lbl';
                    } else if (key === 'account_facebook_handle') {
                        value = (value.indexOf('www.facebook.com/') > -1) ? value : 'www.facebook.com/' + value;
                    }
                    siBean[self.SUGAR_INTERNAL_FIELD_PREFIX + key + self.SUGAR_INTERNAL_FIELD_SUFFIX] = value;
                }
            } else {
                if (key === 'annual_revenue') {
                    // Since from data provider we get value in USD app.user.getPreference('currency_symbol')
                    //should always be $.
                    // Annual Revenue field value being string by default check has been applied.
                    value = (typeof value !== 'string') ? '$' + value.toLocaleString() : value;
                }
                siBean[key] = value;
            }
        }, this);
        return siBean;
    },


    /**
     * Convert to lead bean
     *
     * @param {Object} stage2bean
     * @param {Object} siBean
     * @return {Object}
     */
    _convertToLeadBean: function(stage2bean, siBean) {
        _.each(stage2bean, function(value, key) {
            switch (key) {
                case 'phone_work':
                case 'phone_mobile':
                case 'phone_other':
                    siBean = this._distributePhones(siBean, key, value);
                    break;
                case 'account_name':
                case 'title':
                    siBean[key] = value;
                    break;
                case 'account_domain':
                    if (_.isEmpty(siBean.hint_account_website) && !_.isEmpty(value)) {
                        siBean.hint_account_website = 'http://' + value;
                    }
                    break;
                case 'account_naics_code_label':
                    siBean[this.SUGAR_INTERNAL_FIELD_PREFIX + 'account_naics_code_lbl' +
                        this.SUGAR_INTERNAL_FIELD_SUFFIX] = value;
                    break;

                case 'account_annual_revenue':
                    // Since from data provider we get value in USD app.user.getPreference('currency_symbol')
                    //should always be $.
                    // Annual Revenue field value being string by default check has been applied.
                    value = (typeof value !== 'string') ? '$' + value.toLocaleString() : value;
                // fall through deliberately
                default:
                    if (key === 'account_twitter_handle') {
                        value = 'www.twitter.com/' + value;
                    } else if (key === 'account_facebook_handle') {
                        value = (value.indexOf('www.facebook.com/') > -1) ? value : 'www.facebook.com/' + value;
                    }
                    siBean[this.SUGAR_INTERNAL_FIELD_PREFIX + key + this.SUGAR_INTERNAL_FIELD_SUFFIX] = value;
            }
        }, this);

        // For erase field feature.
        if (this._erasedFields && _.contains(this._erasedFields, 'phone_work')) {
            if (siBean.hint_phone_1 && siBean.hint_phone_1 !== '' && !_.contains(this._erasedFields, 'hint_phone_1')) {
                this._erasedFields.push('hint_phone_1');
                siBean.hint_phone_1 = '';
            }
            if (siBean.hint_phone_2 && siBean.hint_phone_2 !== '' && !_.contains(this._erasedFields, 'hint_phone_2')) {
                this._erasedFields.push('hint_phone_2');
                siBean.hint_phone_2 = '';
            }
        }
        if (this.moduleName === 'Leads' && this._erasedFields && _.contains(this._erasedFields, 'website')) {
            siBean.hint_account_website = '';
            this.$('[data-name="hint_account_website"]').children('#hint_website_erased').removeClass('hidden');
        }
        if (this.moduleName === 'Contacts' && this._erasedFields && _.contains(this._erasedFields, 'account_name')) {
            siBean.account_name = '';
            siBean.hint_account_logo = '';
        }

        return siBean;
    },

    /**
     * Convert hint payload to SugarBean
     *
     * @param {Object} stage2bean
     * @return {Object}
     */
    convertHintPayloadToSugarBean: function(stage2bean) {
        if (this.moduleName !== 'Accounts') {
            if (!stage2bean.account_name && !this._originalModel.get('account_name')) {
                if (!_.contains(this._erasedFields, 'account_name')) {
                    this.$('[data-type="text"][data-name="account_name"]')
                        .append('<div class="ellipsis_inline not_found">'
                        .concat(app.lang.get('LBL_HINT_PREVIEW_NO_ACCOUNT_INFO'), '</div>'));
                }
            }
        }
        siBean = (this.moduleName === 'Accounts') ?
            this._convertToAccountBean(stage2bean, {}) : this._convertToLeadBean(stage2bean, {});
        // Skip empty (non-enriched) values.
        siBean = _.omit(siBean, function(value, key) {
            return this._isEmpty(value);
        }, this);

        if (this.moduleName === 'Accounts') {
            // Remove email1, email2 from attrs
            siBean = _.omit(siBean, function(value, key) {
                return key === 'email1' || key === 'email2' || key === 'email3' || key === 'email4';
            }, this);
        }
        return siBean;
    },

    /**
     * Distribute phones
     * Delete the enriched phone numbers also in original model and fill the rest into siBean in order.
     *
     * @param {Object} siBean
     * @param {string} key
     * @param {string} value
     * @return {Object}
     */
    _distributePhones: function(siBean, key, value) {
        siBean[key] = value ? value.replaceAll(', ', '. ') : '';
        return siBean;
    },

    /**
     * Track event
     *
     * @param {string} category
     * @param {string} action
     */
    _trackEvent: function(category, action) {
        if (!this.instanceId) {
            return;
        }
        var eventObject = {
            category: this.moduleName + category,
            action: action,
            label: this.instanceId
        };
        if (app.analytics && app.analytics.connector) {
            app.analytics.connector.trackEvent(eventObject);
        }
    },

    /**
     * Get the error code
     *
     * @param err
     */
    _setStage2ErrorCode: function(err) {
        if (_.isUndefined(this._stage2ErrorCode)) {
            this._stage2ErrorCode = err.status;
            this.isValidVersion = (SUGAR.App.hint.versionCompare() >= 0);
            this.oldHintPreview = SUGAR.App.hint.shouldUseOldHintPreview(this.moduleName);
            this.isListView = SUGAR.App.hint.isListView();
            this.trigger('changed:_stage2ErrorCode');
        }
    },

    /**
     * Set the field view model
     */
    _setFieldViewModel: function() {
        var self = this;
        _.each(this.metadata, function(panel) {
            _.each(panel.fields, function(field) {
                //For account fields we need set the true parent model.
                // This should be derived from metadata in the near future but trying to minimize
                // the chance of regression for the v4.0 fix.
                if (self.moduleName !== 'Accounts' &&
                    (panel.name == 'company_info' ||
                    panel.name == 'company_extended') && !/^hint_[\w]*/i.test(field.name)) {
                    if (field.type !== 'enum') {
                        field.model = self.model;
                    } else {
                        //Hint prefixed fields belong to the parent module
                        field.model = self.account_model;
                    }
                } else {
                    field.model = self.model;
                }
            });
        });
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this.options = options;
        var self = this;
        this._super('initialize', [options]);
        this.isDarkMode = app.hint.isDarkMode();
        var isAccountCreateDrawerOpen = (SUGAR.App.router._currentFragment === 'Accounts/create');
        this.isValidVersion = (app.hint.versionCompare() >= 0);
        this.metadata = options.meta.panels = app.hint.getPanelMetadata(options.module);
        this.configedFields = [];
        this.preFilledDataFields = [],
            self.modelDiffFlag = true;
        _.each(options.meta.panels, function(panel) {
            _.each(panel.fields, function(field) {
                self.configedFields.push(field.name);
            });
        });

        this.dataView = 'stage2-preview';
        this._activeRequest = null;
        this._parentDataView = this.context.parent.get('dataView');
        this._delegateEvents();
        this.moduleName = this.context.get('module');
        this.isModuleAdmin = _.isUndefined(app.user.getAcls()[this.moduleName].admin);
        this.stage2CreateMode = this.context.parent.get('create');
        this.enrichAttributeList =
            (this.moduleName === 'Accounts') ? this.enrichAccountsAttributeList : this.enrichLeadsAttributeList;
        this.saveDisableAttrList =
            (this.moduleName === 'Accounts') ? this.saveAccountsDisableAttrList : this.saveLeadsDisableAttrList;
        this._originalModel = this.model;
        this.lastFetchedName = '';

        // Clears the hint pic if sugar picture is already loaded by the user.
        if (this._originalModel.get('picture')) {
            this._originalModel.save('hint_contact_pic', '');
        }

        // empties the queue which stores the previous Account Name.
        if (isAccountCreateDrawerOpen) {
            self.previousAccountQueue = [];
        }
        // The updateModel() is called to render the Accounts Module, when the saved record is about to render
        // to get the updated data from the record view.
        if (this.moduleName === 'Accounts' && !isAccountCreateDrawerOpen) {
            self._updateModel();
        }

        this.model = this.model.clone();
        this.account_model = app.data.createBean('Accounts', {});

        var leadsAttrs = self.getFieldNamesForSending();

        self.fetchNewData = this._originalModel.clone();
        self.fetchNewDataAccounts = this._originalModel.clone();

        _.each(leadsAttrs, function(field) {
            if (field !== 'email' && field !== 'first_name' && field !== 'last_name') {
                self.fetchNewData.attributes[field] = '';
            }
        });

        _.each(leadsAttrs, function(field) {
            if (field !== 'name' && field !== 'website' && !self.context.get('model').exitDropdownNoEnrich) {
                self.fetchNewDataAccounts.attributes[field] = '';
            }
        });

        this._setFieldViewModel();

        this._callRetryCounter = 0;

        if (this.moduleName === 'Leads') {
            this.saveDisableAttrList = _.without(this.saveDisableAttrList, 'account_name');
        }

        this._callStage2API('GET', 'stage2/params', {
            success: function(data) {
                self.instanceId = data.instanceId;
                if (app.analytics && app.analytics.connector) {
                    app.analytics.connector.set('&uid', data.analyticsUserId);
                    app.analytics.connector.set('dimension1', data.instanceId);
                }
            },
            error: function(err) {
                return;
            }
        });

        this.on('changed:_stage2ErrorCode', this.render, this);
        this.on('changed:_hintRequestCompleted', this.render, this);

        if (this.stage2CreateMode) {
            // Make sure the orginal model has all hint attributes.
            this._resetModel();

            app.events.on('hint:user-input', function(_keyAttrChanged) {
                if (_keyAttrChanged) {
                    self._updateModel();
                } else {
                    this.model.set(_.omit(self._originalModel.changedAttributes(), 'phone_work'));
                }
            }, this);

            this.on('hint:user-save', function() {
                this.model.set(_.omit(self._originalModel.changedAttributes(), 'phone_work'));
            }, this);
        } else {
            // On list view, if the model changed before preview called, sync already happened during change
            // So listento sync won't trigger '_updateModel', we have to call '_updateModel' directly.
            if (this._parentDataView === 'list' && !_.isEmpty(this._originalModel.changed)) {
                this._updateModel();
            } else {
                this.listenToOnce(this._originalModel, 'sync', this._updateModel);
            }
        }
    },

    /**
     * Reset model
     */
    _resetModel: function() {
        var _resetParams = {};
        _.each(this.enrichAttributeList.concat(this.saveDisableAttrList), function(item) {
            if (!this._originalModel.has(item)) {
                _resetParams[item] = '';
            }
        }, this);
        this._originalModel.set(_resetParams, {
            silent: true
        });
    },

    /**
     * Handle phones
     *
     * @param {string} phoneStr
     */
    _handlePhones: function(phoneStr) {
        if (this.moduleName === 'Leads') {
            this.model.set('hint_account_website', this._originalModel.get('website') || '');
        }
    },

    /**
     * Update model
     */
    _updateModel: function() {
        var self = this;
        self._setFieldViewModel();

        if (this.model) {
            this.model.set(this._originalModel.attributes);
            if (this.moduleName !== 'Accounts') {
                this._handlePhones(this._originalModel.get('phone_work'));
            }
            this.$('[data-name="saveAll"]').tooltip({
                title: 'Data enrichment is processing.',
                placement: 'left'
            });
            self._erasedFields = self.model.get('_erased_fields');

            // Get account ID first.
            var account = this.model.get('accounts');
            if (this.moduleName === 'Contacts' && account && account.id && !_.isEmpty(account.id)) {
                var accountBean = app.data.createBean('Accounts', {
                    id: account.id
                });
                accountBean.fetch({
                    success: function(accountBean) {
                        if (_.contains(accountBean.get('_erased_fields'), 'name')) {
                            self._erasedFields.push('account_name');
                        }
                        self.account_model = accountBean;
                        self._setFieldViewModel();
                        self._enrichModel();
                    },
                    error: function(err) {
                        app.logger.error('Failed to fetch accountBean: ' + JSON.stringify(err));
                        self._enrichModel();
                    }
                });
            } else {
                this._enrichModel();
            }
        }
    },

    /**
     * Delegate events
     */
    _delegateEvents: function() {
        app.events.on('preview:collection:change', this.showPreviousNextBtnGroup, this);
        app.events.on('preview:module:update', this.updatePreviewModule, this);
        if (this.layout) {
            this.layout.on('preview:pagination:fire', this.switchPreview, this);
        }
    },

    /**
     * Update preview module
     *
     * @param {string} module
     */
    updatePreviewModule: function(module) {
        this.previewModule = module;
    },

    /**
     * Filter collection
     */
    filterCollection: function() {
        this.collection.remove(_.filter(this.collection.models, function(model) {
            return !app.acl.hasAccessToModel('view', model);
        }, this), {
            silent: true
        });
    },

    /**
     * Set editable fields
     */
    setEditableFields: function() {
        this.editableFields = [];
    },

    /**
     * @inheritdoc
     */
    _renderHtml: function() {
        this.showPreviousNextBtnGroup();
        app.view.View.prototype._renderHtml.call(this);
    },

    /**
     * Show previous and next buttons groups on the view.
     * This gets called everytime the collection gets updated. It also depends
     * if we have a current model or layout.
     */
    showPreviousNextBtnGroup: function() {
        if (!this.model || !this.layout) {
            return;
        }

        var collection = this.collection;
        if (!collection || !collection.size()) {
            this.layout.hideNextPrevious = true;
            // Need to rerender the preview header
            this.layout.trigger('preview:pagination:update');
            return;
        }

        var recordIndex = collection.indexOf(collection.get(this.model.id));
        this.layout.previous = collection.models[recordIndex - 1] ? collection.models[recordIndex - 1] : undefined;
        this.layout.next = collection.models[recordIndex + 1] ? collection.models[recordIndex + 1] : undefined;
        this.layout.hideNextPrevious = _.isUndefined(this.layout.previous) && _.isUndefined(this.layout.next);

        // Need to rerender the preview header
        this.layout.trigger('preview:pagination:update');
    },

    /**
     * Switch model
     *
     * @param {Object} model
     */
    switchModel: function(model) {
        this.stopListening(this._originalModel);
        this._originalModel = model;
        model = model.clone();
        this.model = model;

        // Close preview when model destroyed by deleting the record
        this.listenTo(this.model, 'destroy', function() {
            // Remove the decoration of the highlighted row
            app.events.trigger('list:preview:decorate', false);
            // Close the preview panel
            app.events.trigger('preview:close');
        });

        if (this._parentDataView === 'list' && !_.isEmpty(this._originalModel.changed)) {
            this._updateModel();
        } else {
            this.listenToOnce(this._originalModel, 'sync', this._updateModel);
        }
    },

    /**
     * Switches preview to left/right model in collection.
     *
     * @param {Object} data
     * @param {string} data.direction Direction that we are switching to, either 'left' or 'right'.
     * @param index Optional current index in list
     * @param id Optional
     * @param module Optional
     */
    switchPreview: function(data, index, id, module) {
        var currID = id || this.model.get('id');
        var currIndex = index || _.indexOf(this.collection.models, this.collection.get(currID));

        if (this.switching || this.collection.models.length < 2) {
            // We're currently switching previews or we don't have enough models, so ignore any pagination click events.
            return;
        }
        this.switching = true;

        if (data.direction === 'left' && (currID === _.first(this.collection.models).get('id')) ||
            data.direction === 'right' && (currID === _.last(this.collection.models).get('id'))) {
            this.switching = false;
            return;
        } else {
            // We can increment/decrement
            data.direction === 'left' ? currIndex -= 1 : currIndex += 1;
            //Reset the preview
            app.events.trigger('preview:render', this.collection.models[currIndex], this.collection, true);
            this.switching = false;
        }
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        var self = this;
        if (this.collection) {
            this.collection.on('reset', this.filterCollection, this);
            // when remove active model from collection then close preview
            this.collection.on('remove', function(model) {
                if (model && this.model && (this.model.get('id') == model.get('id'))) {
                    // Remove the decoration of the highlighted row
                    app.events.trigger('list:preview:decorate', false);
                    // Close the preview panel
                    app.events.trigger('preview:close');
                }
            }, this);
        }

        // Update model in list view.
        this.context.on('change:model', function(ctx, model) {
            this.switchModel(model);
            self.isHintRequestLoading = true;
            this.render();
        }, this);

        // Update model in record view.
        if (this.moduleName === 'Leads' || this.moduleName === 'Contacts' || this.moduleName === 'Accounts') {
            if (this._parentDataView === 'record') {
                self._updateModel();
            }
        }
    },

    /**
     * Check if is empty value
     *
     * @param {string} value
     * @return {Object}
     */
    _isEmpty: function(value) {
        if (_.isUndefined(value) || _.isNull(value) || _.isNaN(value)) {
            return true;
        }
        if (_.isNumber(value)) {
            return false;
        }
        if (_.isString(value)) {
            return value.trim() === '';
        }
        // Object and array.
        return _.isEmpty(value);
    },

    /**
     * Issue data enrichment request
     * Apply the data enrichment
     *
     * @param {string} type
     * @param {string} url
     * @param {Object} data
     * @param {Object} options
     */
    issueDataEnrichmentRequest: function(type, url, data, options) {
        var self = this;
        if (self._activeRequest) {
            self._activeRequest.abort();
        }
        self.isHintRequestLoading = true;
        self.isValidVersion = (SUGAR.App.hint.versionCompare() >= 0);
        self.oldHintPreview = SUGAR.App.hint.shouldUseOldHintPreview(this.moduleName);
        self.isListView = SUGAR.App.hint.isListView();
        self.trigger('changed:_hintRequestCompleted');

        if (!app.hint.isEnrichedModel(data.moduleName)) {
            return;
        }
        // it is time to apply the data enrichment
        self._activeRequest = $.ajax({
            type: type,
            url: url,
            data: data,
            headers: {
                authToken: self._stage2accessToken
            },
            success: function(data) {
                if (self.disposed === true) {
                    return;
                }
                options && options.success && options.success(data);
            },
            error: function(err) {
                options && options.error && options.error(err);
            }
        });
    },

    /**
     * Issue request
     *
     * @param {string} type
     * @param {string} url
     * @param {Object} data
     * @param {Object} options
     * @return {Object}
     */
    _issueRequest: function(type, url, data, options) {
        var self = this;
        return $.ajax({
            type: type,
            url: url,
            data: data,
            headers: {
                authToken: self._stage2accessToken
            },
            success: function(data) {
                options && options.success && options.success(data);
            },
            error: function(err) {
                options && options.error && options.error(err);
            }
        });
    },

    /**
     * Track response
     *
     * @param {Object} data
     */
    _trackResponse: function(data) {
        // Tracking enrichment request event.
        this._trackEvent(' Enrichment - List View Preview', 'Request Enrichment');
        if (data && data.enriched) {
            this._trackEvent(' Enrichment - List View Preview', 'Non-Empty Response');
        } else {
            this._trackEvent(' Enrichment - List View Preview', 'Empty Response');
        }
    },

    /**
     * Record metrics token
     * Encapsulate access to storage representation
     *
     * @param {Object} data
     */
    _recordMetricsToken: function(data) {
        app.user.set('hintMetricsToken', data.metricsToken);
    },

    /**
     * Dispose hint view
     */
    _disposeHintView: function() {
        if (this.stage2CreateMode) {
            app.events.trigger('preview:close');
            this.context.parent.trigger('app:preview:stage2-show-notification');
            return;
        }
        this.$('[data-name="saveAll"]').remove();
        if (this.moduleName !== 'Accounts') {
            if (!this._originalModel.get('account_name')) {
                this.$('[data-type="text"][data-name="account_name"]').innerHTML = ''
                    .concat(
                        '<div class="ellipsis_inline not_found">',
                        app.lang.get('LBL_HINT_PREVIEW_NO_ACCOUNT_INFO'),
                        '</div>'
                    );
            }
        }
    },

    /**
     * If the bean is the same with original model, means no data enriched.
     *
     * @param {Object} bean
     * @return {boolean}
     */
    _beanEnriched: function(bean) {
        bean = _.omit(bean, 'hint_first_name_c', 'hint_last_name_c', 'hint_full_name_c');
        var enrichedAttrs = _.pick(bean, function(value, key) {
            return this._isEmpty(this._originalModel.get(key)) && !this._isEmpty(value);
        }, this);
        return !_.isEmpty(enrichedAttrs) ? true : false;
    },

    /**
     * Called after finishing account enrichment process in the create drawer. Used to reset back to initial state
     * where we auto fill enrichment results based on the account selected from the dropdown. Only when the user
     * prematurely tabs/clicks out of the dropdown do we not want to autofill the enrichment results.
     */
    _resetEnrichAutoFillState: function() {
        var isAccountCreateDrawerOpen = (SUGAR.App.router._currentFragment === 'Accounts/create');
        if (isAccountCreateDrawerOpen && this.context) {
            this.context.get('model').exitDropdownNoEnrich = false;
            this.shouldShowHintCloudLogo = false;
        }
    },

    /**
     * Enrich model
     */
    _enrichModel: function() {
        var self = this;

        if (_.isEmpty(this.enrichFieldsOptions)) {
            this._callStage2API('read', 'hint/enrich/config', {
                success: function(data) {
                    var rawConfigData = data && data.response && data.response.config_data;
                    if (rawConfigData) {
                        self.enrichFieldsOptions = JSON.parse(rawConfigData);
                        self._enrichModel();
                    }
                },
                error: function(err) {
                    app.logger.error('Failed to reach hint/enrich/config' + JSON.stringify(err));
                }
            });
            return;
        }

        var isCreateDrawerAccountsOpen = SUGAR.App.router._currentFragment === 'Accounts/create';

        // Show cloud icons for downloading in two cases
        // 1. User is in the accounts create drawer and has tabbed/clicked out of the account name dropdown
        // 2. User is in the contacts/leads create drawer
        if (this.context) {
            this.shouldShowHintCloudLogo =
                isCreateDrawerAccountsOpen ? this.context.get('model').exitDropdownNoEnrich : true;
        }

        var accountsHintFields = this.enrichAccountsAttributeList;

        if ((this.moduleName === 'Leads' || this.moduleName === 'Contacts') && this._parentDataView === 'list') {
            this.fetchNewData.attributes.id = this._originalModel.attributes.id;
            this.fetchNewData.attributes.first_name = this._originalModel.attributes.first_name;
            this.fetchNewData.attributes.last_name = this._originalModel.attributes.last_name;
            this.fetchNewData.attributes.email = this._originalModel.attributes.email;
        }

        if (this._callRetryCounter > 3) {
            this._callRetryCounter = 0;
            this.$('[data-name="saveAll"]').remove();
            this._resetEnrichAutoFillState();
            return;
        } else if (this._callRetryCounter !== 0) {
            app.logger.debug('Data enrichment retry #' + this._callRetryCounter);
        }

        _.each(accountsHintFields, function(field) {
            if (field !== 'name' && field !== 'website' && self._originalModel.get(field) && self.modelDiffFlag) {
                self.containsPreAccountFields = true;
                if (!self.includes(self.preFilledDataFields, field)) {
                    self.preFilledDataFields.push(field);
                }
            }
        });

        this.modelDiffFlag =  false;

        if (_.isEmpty(self._stage2urlV2)) {
            this._callStage2API('GET', 'stage2/params', {
                success: function(data) {
                    self._enrichModel();
                },
                error: function(err) { }
            });
            return;
        }

        if (!self._stage2accessToken) {
            this._callStage2API('create', 'stage2/token', {
                success: function(data) {
                    self._stage2accessToken = data.accessToken;
                    self._subscriptionType = data.subscriptionType;
                    self._enrichModel();
                },
                error: function(err) { }
            });
            return;
        }
        var username = null;
        if (app.user && app.user.attributes) {
            username = app.user.attributes.user_name;
        }

        var instanceId = app.config.uniqueKey;

        // clearing the originalModuleAttributes, so that the SIBean will enrich all of the data.
        // checking if there are already some fields populated.
        if (isCreateDrawerAccountsOpen) {
            if (((this.previousAccountsName !== this._originalModel.get('name')) &&
                !_.isEmpty(this._originalModel.get('name'))) || this.cancelName) {
                this.cancelName = false;
                _.each(accountsHintFields, function(field) {
                    //enriches new data based on account name. It handles pre-typed data on the record view to be saved.
                    if (field !== 'name' && self._originalModel.get(field) &&
                        !self.includes(self.preFilledDataFields, field)) {
                        self.containsPreAccountFields = true;
                        if (self.preFilledDataFields.length) {
                            self.fetchNewDataAccounts.attributes[field] = '';
                        } else if (!self.shouldShowHintCloudLogo) {
                            self._originalModel.attributes[field] = '';
                        }
                    }
                });
            } else if (((this.previousAccountsWebsite !== this._originalModel.get('website')) &&
                !_.isEmpty(this._originalModel.get('website'))) || this.cancelName) {
                _.each(accountsHintFields, function(field) {
                    //enriches new data based on account website.
                    //It handles pre-typed data on the record view to be saved.
                    if (field !== 'website' && self._originalModel.get(field) &&
                    !self.includes(self.preFilledDataFields, field)) {
                        self.containsPreAccountFields = true;
                        if (self.preFilledDataFields.length) {
                            self.fetchNewDataAccounts.attributes[field] = '';
                        } else if (!self.shouldShowHintCloudLogo) {
                            self._originalModel.attributes[field] = '';
                        }
                    }
                });
            }
        }
        this.fetchNewDataAccounts.attributes.name = this._originalModel.attributes.name;
        this.fetchNewDataAccounts.attributes.website = this._originalModel.attributes.website;
        var SIBean = (this.moduleName === 'Leads' || this.moduleName === 'Contacts') ?
            JSON.stringify(this.convertSugarBeanToHintPayload(
                _.pick(this.fetchNewData.attributes, this.getFieldNamesForSending()))) :
            JSON.stringify(this.convertSugarBeanToHintPayload(
                _.pick(this.fetchNewDataAccounts.attributes, this.getFieldNamesForSending())));

        var endpoint = this.moduleName === 'Accounts' ? '/enrich-account-bean' : '/enrich-person-bean';
        this._attrsToSave = {};

        // This check is to prevent multiple API calls when the user is on the create drawer and tries to make
        // an another search for other candidate (Leads or Contacts).
        this.issueDataEnrichmentRequest('GET', this._stage2urlV2 + endpoint, {
            bean: SIBean,
            username: username,
            instanceId: instanceId,
            moduleName: self.moduleName,
            subscriptionType: self._subscriptionType
        }, {
            success: function(data) {
                if (self.disposed === true) {
                    return;
                }
                if (self.moduleName === 'Accounts' && self.isCreateDrawerAccountsOpen) {
                    // 'exitDropdownNoEnrich' will be true when the user tabs/clicks out of the name/website field (we
                    // don't want to auto enrich when tabbing/clicking out of the dropdown). Generally, when we tab out,
                    // we want to show the cloud icons for downloading. When the user tabs out of the account name from
                    // the dropdown and it's NOT the same as the account name from the previously fetched data
                    //(especially when the account to enrich was inferred from the website provided by the user),
                    //then we want to show the clouds. However, if the previous scenario were the same except that the
                    //previous account fetched was the same as the account we just tried to fetch now, then we don't
                    //show the cloud icons.
                    var previousAndCurrentNameEqual =
                        data.bean.name.toLowerCase() === self.lastFetchedName.toLowerCase();
                    if (self.context.get('model').exitDropdownNoEnrich && !previousAndCurrentNameEqual) {
                        self.shouldShowHintCloudLogo = true;
                    } else if (previousAndCurrentNameEqual) {
                        self.shouldShowHintCloudLogo = false;
                    }
                }

                self._activeRequest = null;
                self._callRetryCounter = 0;
                self._trackResponse(data);
                self._recordMetricsToken(data);
                self.isHintRequestLoading = false;
                self.trigger('changed:_hintRequestCompleted');

                if (self.$('[data-name="saveAll"]')) {
                    self.$('[data-name="saveAll"]').tooltip('dispose');
                }
                if (self.disposed || !data || !data.enriched) {
                    self._disposeHintView();
                    self._resetEnrichAutoFillState();
                    return;
                }

                var bean = self.convertHintPayloadToSugarBean(data.bean);

                var AccountName = self._originalModel.get('name') || bean.name ||  self.previousAccountsName;
                var websiteName = self._originalModel.get('website') || bean.website || self.previousAccountsWebsite;
                var previousAccountName = bean.name;

                if (previousAccountName === AccountName && !_.isEmpty(previousAccountName) &&
                    !self.shouldShowHintCloudLogo) {
                    previousAccountName = self.previousAccountQueue.pop();
                    self.previousAccountQueue.push(AccountName);
                }

                var beanAccountFields = self._getFieldsFromBean(bean);

                // this ensures the prefilled fields are cleared on successful bean enrichment.
                _.each(accountsHintFields, function(field) {
                    if (field !== 'name' && field !== 'website' && bean[field]) {
                        self.preFilledDataFields = [];
                        self.modelDiffFlag = false;
                    }
                });

                if ((self.containsPreAccountFields) && isCreateDrawerAccountsOpen && !self.shouldShowHintCloudLogo) {
                    self._warningAlertForDuplicates(self, beanAccountFields, previousAccountName);
                    self.previousBeanAccountsFields = beanAccountFields;
                } else if (isCreateDrawerAccountsOpen) {
                    // so we need to show the preview clouds on tab out, when there's already account data pre existing
                    if (!self.shouldShowHintCloudLogo) {
                        self.model.set(beanAccountFields);
                        self._originalModel.set(beanAccountFields);
                        self.previousAccountsName = self._originalModel.get('name');
                        self.previousAccountsWebsite = self._originalModel.get('website');
                        self.previousBeanAccountsFields = beanAccountFields;
                    }
                }

                var shouldAllowUserOverwrite = true;
                _.each(bean, function(value, attr) {
                    if (attr !== 'website' && attr !== 'name' && bean[attr]) {
                        shouldAllowUserOverwrite = false;
                    }
                });

                if (shouldAllowUserOverwrite) {
                    shouldAllowUserOverwrite = false;
                    self._originalModel.attributes.website = websiteName;
                    self._originalModel.attributes.name = AccountName;
                }

                if (!self._beanEnriched(bean)) {
                    self._disposeHintView();
                    self._resetEnrichAutoFillState();
                    return;
                }

                self.$('[data-name="saveAll"]').removeClass('hint-preview-icon--loading');
                self._enrichHintDashletWithBean(bean);
                self._filterFieldsBasedOnConfig();
                self._fixLengthPhoneFields();

                // Hide other two phone fields if there is no value.
                if (self.moduleName === 'Leads' || self.moduleName === 'Contacts') {
                    if (self._isEmpty(self.model.get('hint_phone_1'))) {
                        self.$('[data-name="hint_phone_1"]').closest('.row-fluid').addClass('hidden');
                    }
                    if (self._isEmpty(self.model.get('hint_phone_2'))) {
                        self.$('[data-name="hint_phone_2"]').closest('.row-fluid').addClass('hidden');
                    }
                }
                if (!_.isEmpty(self._attrsToSave)) {
                    self.$('[data-name="saveAll"]').addClass('btn btn-primary saveAll').tooltip({
                        title: app.lang.get('LBL_HINT_PREVIEW_TOOLTIP_TITLE'),
                        placement: 'left'
                    });
                    self.$('.saveAll').text(app.lang.get('LBL_HINT_ENRICH_ALL'));
                }
                self._resetEnrichAutoFillState();

                // Record the last account name at the very end of of handling the enrichment response.
                // This is used to help handle the cloud download icon displaying logic.
                self.lastFetchedName = data.bean.name;
            },
            error: function(err) {
                app.logger.error('Failed to enrich data on Hint: ' + JSON.stringify(err));
                self.isHintRequestLoading = false;
                self._activeRequest = null;

                // For aborted requests don't do anything: this avoids race conditions, repeated
                // aborted data enrichments and so on.
                if (err && err.statusText === 'abort') {
                    self._resetEnrichAutoFillState();
                    return;
                }
                self._stage2accessToken = null;
                // Track when response return with error.
                self._trackEvent(' Enrichment - List View Preview', 'Error Response');
                ++self._callRetryCounter;
                self._enrichModel();
            }
        });
    },

    /**
     * Get truncated phone based on field width
     *
     * @param {string} phone
     * @param {string} phoneField
     * @return {string}
     */
    _getTruncatedPhoneBasedOnFieldWidth: function(phone, phoneField) {
        var metadataOfModule = app.metadata.getModule(this.moduleName);
        var phoneFieldMetadataLength = metadataOfModule && metadataOfModule.fields[phoneField] &&
            metadataOfModule.fields[phoneField].len;
        if (!phoneFieldMetadataLength) {
            return phone;
        }
        return phone.substring(0, phoneFieldMetadataLength);
    },

    /**
     * Fix length phone fields
     */
    _fixLengthPhoneFields: function() {
        var self = this;
        _.each(this.phoneFieldTypes, function(phoneField) {
            var recordViewPhoneNumber = self._originalModel.get(phoneField);
            var phone = self.model.get(phoneField);
            var truncatedPhoneFromHint = phone && self._getTruncatedPhoneBasedOnFieldWidth(phone, phoneField);
            if (recordViewPhoneNumber === truncatedPhoneFromHint) {
                self._attrsToSave = _.omit(self._attrsToSave, phoneField);
                self.$('.hint-preview-icon--cloud[data-name="' + phoneField + '"]').addClass('hidden');
            }
        });
    },

    /**
     * Get fields from bean
     *
     * @param {Object} bean
     * @return {Object}
     */
    _getFieldsFromBean: function(bean) {
        return {
            'name': bean.name,
            'website': bean.website,
            'description': bean.description,
            'sic_code': bean.sic_code,
            'annual_revenue': bean.annual_revenue,
            'twitter': bean.twitter,
            'hint_account_size': bean.hint_account_size,
            'hint_account_location': bean.hint_account_location,
            'hint_account_industry': bean.hint_account_industry,
            'hint_account_founded_year': bean.hint_account_founded_year,
            'hint_account_facebook_handle': bean.hint_account_facebook_handle,
            'hint_account_industry_tags': bean.hint_account_industry_tags,
            'hint_account_naics_code_lbl': bean.hint_account_naics_code_lbl,
            'hint_account_fiscal_year_end': bean.hint_account_fiscal_year_end,
            'hint_account_pic':  bean.hint_account_logo
        };
    },

    /**
     * Filter fields based on config
     */
    _filterFieldsBasedOnConfig: function() {
        var currentModule = this.moduleName;
        var self = this;
        if (this.enrichFieldsOptions && this.enrichFieldsOptions[currentModule]) {
            var enrichField = this.enrichFieldsOptions[currentModule].fields;
            _.each(enrichField, function(value, attr) {
                if (!value) {
                    var isAccountSicCodeSpecialCase =
                        currentModule === 'Accounts' && attr === 'hint_account_sic_code_label';
                    if (isAccountSicCodeSpecialCase) {
                        var sicCodeFromRecordView = self.context.get('model').attributes.sic_code;
                        self.model.set('hint_account_sic_code_label', sicCodeFromRecordView);
                        self.$('.hint-preview-icon--cloud[data-name="' + attr + '"]').addClass('hidden');
                    } else {
                        self.model.set(attr, this.context.get('model').attributes[attr]);
                        self.$('.hint-preview-icon--cloud[data-name="' + attr + '"]').addClass('hidden');
                    }
                }
            }, this);
        }
    },

    /**
     * Get the new logo
     *
     * @param {Object} bean
     * @param attr
     */
    _newLogosFound: function(bean, attr) {
        var accountsSicFieldFound = this._accountContainsSICLabel(attr);
        var accountsEnrichPhotoViaWebsite = (this.moduleName === 'Accounts' && attr === 'website');
        var otherHintModules = ['Contacts', 'Leads'];
        var enrichPhotoViaTitle = (attr === 'title' && this.includes(otherHintModules, this.moduleName));

        if (accountsSicFieldFound) {
            return this._isFieldDifferent(bean, 'sic_code');
        } else if (accountsEnrichPhotoViaWebsite) {
            var doPhotoURLsDiffer = bean.hint_account_logo != this._originalModel.attributes.hint_account_pic;
            var doWebsitesDiffer = (attr === 'website' && bean[attr] != this._originalModel.attributes.website);
            return doPhotoURLsDiffer || doWebsitesDiffer;
        } else if (enrichPhotoViaTitle) {
            var doTitlesDiffer = attr === 'title' && bean[attr] != this._originalModel.attributes.title;
            var doPhotosDiffer = bean.hint_photo && bean.hint_photo != this._originalModel.attributes.hint_contact_pic;
            return doPhotosDiffer || doTitlesDiffer;
        } else {
            return this._isFieldDifferent(bean, attr);
        }
    },

    /**
     * Enrich hint dashlet with bean
     *
     * @param {Object} bean
     */
    _enrichHintDashletWithBean: function(bean) {
        var self = this;
        _.each(bean, function(value, attr) {
            var accountsSicFieldFound = self._accountContainsSICLabel(attr);
            var newDataFound = self._newLogosFound(bean, attr);

            // It checks if its an actual overwrite with same phone numbers but different order.
            var phoneTypeFields = ['phone_work', 'phone_other', 'phone_mobile'];
            if (self.includes(phoneTypeFields, attr)) {
                var recordHintPhoneFieldsArray = self._originalModel.attributes[attr] ?
                    self._originalModel.attributes[attr].split('. ') : [];
                var newHintPhoneFieldsArray = bean[attr] ? bean[attr].split('. ') : [];
                if (recordHintPhoneFieldsArray.length === newHintPhoneFieldsArray.length) {
                    newDataFound = recordHintPhoneFieldsArray.length !==
                        _.intersection(newHintPhoneFieldsArray, recordHintPhoneFieldsArray).length;
                }
            }

            // Tracking when specific field gets enriched by stage2.
            if (_.contains(self.enrichAttributeList, attr)) {
                self._trackEvent(' Enrichment - List View Preview', attr + ' enriched');
            }

            if (accountsSicFieldFound) {
                self.model.set('sic_code', bean.sic_code);
                self.model.set(attr, value);
            }

            // Only allow enrichment of fields that have enriched value but don't have
            // an pre-existing value themselves.
            if (_.contains(self.configedFields, attr) && (self._isEmpty(self.model.get(attr)) || newDataFound) &&
                !_.contains(self._erasedFields, attr)) {
                self.model.set(attr, value);
                if (!_.contains(self.saveDisableAttrList, attr)) {
                    // If the field is not allowed to edit, set lock icon.
                    if (!app.acl.hasAccessToModel('edit', self.model, attr)) {
                        self.$('[data-name="' + attr + '"][data-action="lock"]').removeClass('hidden');
                        //Hide the default no access span
                        self.$('.noaccess').addClass('hidden');
                    } else {
                        self._attrsToSave[attr] = value;
                        if (attr === 'hint_account_website' && self.moduleName === 'Leads') {
                            self._attrsToSave.website = value;
                        }
                        self.$('[data-name="' + attr + '"][data-action="copy"]').removeClass('hidden');
                    }
                }
            }
        }, this);
    },

    /**
     * Check if is different field
     *
     * @param {Object} bean
     * @param {string} attr
     * @return {boolean}
     */
    _isFieldDifferent: function(bean, attr) {
        return (bean[attr] != this._originalModel.attributes[attr]);
    },

    /**
     * @param {Array} arrayToCheck
     * @param {string} searchValue
     * @return {boolean}
     */
    includes: function(arrayToCheck, searchValue) {
        var returnValue = false;
        var pos = arrayToCheck.indexOf(searchValue);
        if (pos >= 0) {
            returnValue = true;
        }
        return returnValue;
    },

    /**
     * This function pops a warning message if any duplicates are found in the record.
     *
     * @param {Object} self is the 'this' object for the model.
     * @param {Object} beanAccountFields field objects which we get from the hint data provider to populate fields
     * @param {Object} previousAccountName it stores the name of previous account searched when user hits cancel.
     */
    _warningAlertForDuplicates: function(self, beanAccountFields, previousAccountName) {
        var showWarningMessage = false;
        if (!this.shouldShowHintCloudLogo) {
            var duplicateAccountCheck = (!_.isEqual(beanAccountFields, self.previousBeanAccountsFields) &&
                !_.isEmpty(self.previousBeanAccountsFields)) || self.containsPreAccountFields;
            _.each(beanAccountFields, function(value, attr) {
                if (attr !== 'name' && attr !== 'website' && !_.isEmpty(value) && duplicateAccountCheck) {
                    showWarningMessage = true;
                }
            }, this);
        }

        if (showWarningMessage) {
            app.alert.show('message-id', {
                level: 'confirmation',
                messages: SUGAR.App.lang.get('LBL_HINT_MSG_TOTAL_OVERWRITE'),
                autoClose: false,
                // only enrich if the user has intended to do so (meaning they have
                // chosen a company from the dropdown list)
                onConfirm: function() {
                    if (!self.shouldShowHintCloudLogo) {
                        // reset cloud icons & enrich auto fill back to false for any future
                        // enrichment calls
                        self._resetEnrichAutoFillState();
                        self.model.set(beanAccountFields);
                        self._originalModel.set(beanAccountFields);
                        self.previousAccountsName = self._originalModel.get('name');
                        self.previousAccountsWebsite = self._originalModel.get('website');
                    }
                },
                onCancel: function() {
                    // reset cloud icons & enrich auto fill back to false for any future
                    // enrichment calls
                    self._resetEnrichAutoFillState();

                    self.cancelName = true;
                    self._originalModel.set('name', previousAccountName);
                    self.containsPreAccountFields = false;
                }
            });
        }
    },

    /**
     * Warning alert
     *
     * @param {Object} attr
     * @param {string} value
     * @param {string} msg
     */
    _warningAlert: function(attr, value, msg) {
        var self = this;
        var fieldName = SUGAR.App.lang.get(this.fieldTypeTranslations[attr]);
        var message = msg ? msg : SUGAR.App.lang.get('LBL_HINT_MSG_OVERWRITE_FIELD',
            this.module, {fieldName: fieldName});
        var accountsSicFieldFound = this._accountContainsSICLabel(attr);
        // This Flag is set when saveAll cloud icon is clicked and an alert is shown iff data in the record view
        // is not equal to the data found  by the Hint.
        var warningMessageDisplayFlag = false;
        if (typeof attr === 'object') {
            for (var key in attr) {
                if (attr.hasOwnProperty(key)) {
                    if (this._accountContainsSICLabel(key)) {
                        if (this._isValueUpdated(attr, 'sic_code')) {
                            warningMessageDisplayFlag = true;
                        }
                    } else if (this._isValueUpdated(attr, key)) {
                        warningMessageDisplayFlag = true;
                    }
                }
            }
        }

        var doIndividualFieldsDiffer = accountsSicFieldFound ?
            this._doesSICCodeExist(this.model.get('sic_code'), 'sic_code') : this._doesSICCodeExist(value, attr);

        // This check is to display warning iff an individual in the record view is not equal to the one found
        //by the Hint.
        if (warningMessageDisplayFlag || doIndividualFieldsDiffer) {
            app.alert.show('message-id', {
                level: 'confirmation',
                messages: message,
                autoClose: false,
                onConfirm: function() {
                    // Check to avoid adding duplicates in the phoneFields and completely overwrite it.
                    // Teh value parameter is of the form (oldAttrValue(From Hint record view field),
                    // newAttrValue(found by hint)).
                    const isValidAvailablePhoneField = self.isValidPhoneType(attr) &&
                        value.indexOf(self._originalModel.get(attr) + '. ') > -1;
                    if (isValidAvailablePhoneField) {
                        var truncatedValueField = value && self._getTruncatedPhoneBasedOnFieldWidth(value, attr);
                        self._originalModel.save(attr, truncatedValueField);
                    }  else if (accountsSicFieldFound) {
                        self._originalModel.save('sic_code', self.model.get('sic_code'));
                    } else {
                        self._enrichFieldsToModules(attr, value);
                    }
                },
                onCancel: function() {
                    self._resetOnCancel(attr);
                }
            });
        } else {
            // it will execute if there are no fields in the record view which needs to be overwritten
            // and hence no warning message.
            this._enrichFieldsToModules(attr, value);
        }
    },

    /**
     * Warning alert for create drawer
     *
     * @param {Object} attr
     * @param {string} value
     * @param {string} msg
     */
    _warningAlertForCreateDrawer: function(attr, value, msg) {
        var self = this;
        var fieldName = SUGAR.App.lang.get(this.fieldTypeTranslations[attr]);
        var accountsSicFieldFound = this._accountContainsSICLabel(attr);
        var hintSicViewField = accountsSicFieldFound ? this.model.get('sic_code') : this.model.get(attr);
        var recordSicViewField =
            accountsSicFieldFound ? this._originalModel.get('sic_code') : this._originalModel.get(attr);

        var message = msg ? msg : SUGAR.App.lang.get('LBL_HINT_MSG_OVERWRITE_FIELD',
            this.module, {fieldName: fieldName});
        // This Flag is set when saveAll cloud icon is clicked and an alert is shown iff data in the record view
        // is not equal to the data found  by the Hint.
        var warningMessageDisplayFlag = false;
        if (typeof attr === 'object') {
            for (var key in attr) {
                if (attr.hasOwnProperty(key)) {
                    if (this._isValueUpdated(attr, key)) {
                        warningMessageDisplayFlag = true;
                    }
                }
            }
        } else if (accountsSicFieldFound && !_.isEmpty(recordSicViewField) && hintSicViewField !== recordSicViewField) {
            warningMessageDisplayFlag = true;
        }
        var doIndividualFieldsDiffer = this._doesSICCodeExist(value, attr);

        // This check is to display warning iff an individual in the record view is not equal to the one
        //found by the Hint.
        if (warningMessageDisplayFlag || doIndividualFieldsDiffer) {
            app.alert.show('message-id', {
                level: 'confirmation',
                messages: message,
                autoClose: false,
                onConfirm: function() {
                    // Check to avoid adding duplicates in the phoneFields and completely overwrite it.
                    // The value parameter is of the form (oldAttrValue(From Hint record view field),
                    // newAttrValue(found by hint)).
                    const isValidAvailablePhoneField = self.isValidPhoneType(attr) &&
                        value.indexOf(self._originalModel.get(attr) + '. ') > -1;
                    if (isValidAvailablePhoneField) {
                        var truncatedValueField = value && self._getTruncatedPhoneBasedOnFieldWidth(value, attr);
                        self._originalModel.set(attr, truncatedValueField);
                    } else if (accountsSicFieldFound) {
                        self._originalModel.set('sic_code', self.model.get('sic_code'));
                    } else {
                        self._setFieldsInCreateDrawer(attr, value);
                    }
                },
                onCancel: function() {
                    self._enrichModel();
                }
            });
        } else {
            this._setFieldsInCreateDrawer(attr, value);
        }
    },

    /**
     * Check if the account contains SIC label
     *
     * @param {string} attr
     * @return {boolean}
     */
    _accountContainsSICLabel: function(attr) {
        return this.moduleName === 'Accounts' && attr === 'hint_account_sic_code_label';
    },

    /**
     * Is value updated
     *
     * @param {Array} attr
     * @param {string} key
     * @return {boolean}
     */
    _isValueUpdated: function(attr, key) {
        if (!_.isEmpty(this._originalModel.get(key))) {
            // REMIND: to be included for accounts as well when its implemented.
            if (key === 'title' && (this._originalModel.get('picture') !== this.model.get('hint_photo'))) {
                return true;
            }
            return attr[key] !== this._originalModel.get(key);
        }
        return false;
    },

    /**
     * Check if SIC code exist
     *
     * @param {string} value
     * @param {Object} attr
     * @return {boolean}
     */
    _doesSICCodeExist: function(value, attr) {
        return (value !== this._originalModel.get(attr) && !_.isEmpty(this._originalModel.get(attr)));
    },

    /**
     * Check if is a valid phone number
     *
     * @param {string} key
     * @return {boolean}
     */
    isValidPhoneType: function(key) {
        return _.includes(this.phoneFieldTypes, key);
    },

    /**
     * Reset on cancel
     *
     * @param attr
     */
    _resetOnCancel: function(attr) {
        var accountsLogoURL = this._originalModel.get('hint_account_pic');
        var foundNewLogo = (!_.isEmpty(accountsLogoURL) &&
            accountsLogoURL !== this.model.get('hint_account_logo'));

        var contactsLeadsPhotoURL = this._originalModel.get('hint_contact_pic');
        var contactPhotoFromHint = this.model.get('hint_photo');
        var imagePathInSugar = this._originalModel.get('picture');

        var foundNewPhoto = ((!_.isEmpty(contactsLeadsPhotoURL) &&
            contactsLeadsPhotoURL !== contactPhotoFromHint) ||
            (!_.isEmpty(imagePathInSugar) && imagePathInSugar !== contactPhotoFromHint));

        if (attr === 'title' && foundNewPhoto) {
            this._warningAlertForImageOverwrite('hint_contact_pic', true, false);
        } else if (attr === 'website' && foundNewLogo) {
            this._warningAlertForImageOverwrite('hint_account_pic', true, false);
        } else {
            // _updateModel() is called to prevent changes in model when user hits cancel
            // after save-All cloud icon is being hit.
            this._updateModel();
        }
    },

    /**
     * Warning alert for image overwrite
     *
     * @param {string} attr
     * @param {boolean} overwriteOnlyImage
     * @param {boolean} titlePhotoWaterfall
     */
    _warningAlertForImageOverwrite: function(attr, overwriteOnlyImage, titlePhotoWaterfall) {
        var self = this;
        var fieldName = SUGAR.App.lang.get(this.fieldTypeTranslations[attr]);
        var message = SUGAR.App.lang.get('LBL_HINT_MSG_OVERWRITE_FIELD', this.module, {fieldName: fieldName});
        app.alert.show('message-id', {
            level: 'confirmation',
            messages: message,
            autoClose: false,
            onConfirm: function() {
                self._saveHintsImageURL(overwriteOnlyImage);
                if (titlePhotoWaterfall) {
                    self._warningAlertForCreateDrawer('title', self.model.get('title'), '');
                }
            },
            onCancel: function() {
                self._updateModel();
            }
        });
    },

    /**
     * Enrich fields to module
     *
     * @param {Object} attr
     * @param {string} value
     */
    _enrichFieldsToModules: function(attr, value) {
        var overwriteOnlyLogo = true;
        if (this.moduleName === 'Accounts') {
            var accountsLogoURL = this._originalModel.get('hint_account_pic');
            attr.hint_account_pic = this.model.get('hint_account_logo');
            for (var key in attr) {
                if (attr.hasOwnProperty(key)) {
                    if (key === 'hint_account_sic_code_label') {
                        attr.sic_code = this.model.get('sic_code');
                    }  else if (key !== 'hint_account_pic') {
                        attr[key] = String(this.model.get(key));
                    }
                }
            }
            this._originalModel.save(attr, value);
            if (!_.isEmpty(accountsLogoURL) && accountsLogoURL !== this.model.get('hint_account_logo')) {
                // attr will be an object when both the website and account logo needs to be changed.
                if (typeof(attr) === 'object') {
                    this._saveHintsImageURL(overwriteOnlyLogo);
                } else if (attr === 'hint_account_pic') {
                    this._warningAlertForImageOverwrite('hint_account_pic', false, false);
                }
            }
        } else {
            attr.hint_contact_pic = this.model.get('hint_photo');
            for (var key in attr) {
                var isValidPhoneField = attr.hasOwnProperty(key) && this.isValidPhoneType(key);
                if (isValidPhoneField) {
                    var phone = this.model.get(key);
                    attr[key] = phone && this._getTruncatedPhoneBasedOnFieldWidth(phone, key);
                } else if (key === 'website' && this.moduleName === 'Leads') {
                    attr[key] = this.model.get('hint_account_website');
                } else if (key !== 'hint_contact_pic') {
                    attr[key] = String(this.model.get(key));
                }
            }
            this._originalModel.save(attr, value);
            var contactsLeadsPhotoURL = this._originalModel.get('picture');
            if (!_.isEmpty(contactsLeadsPhotoURL) && (contactsLeadsPhotoURL !== this.model.get('hint_photo'))) {
                // attr will be an object when both the title and profile logo needs to be changed.
                if (typeof(attr) === 'object') {
                    this._saveHintsImageURL(overwriteOnlyLogo);
                } else {
                    this._warningAlertForImageOverwrite('hint_contact_pic', false, false);
                }
            }
        }
    },

    /**
     * The attr is of type object when we try to hit saveAll cloud icon.
     *
     * @param {Object} attr
     * @param keyToCheck
     * @return {boolean}
     */
    _isValidAttrForLogoEnrich: function(attr, keyToCheck) {
        return attr === keyToCheck || typeof(attr) === 'object';
    },

    /**
     * Set fields in create drawer
     *
     * @param {Object} attr
     * @param {string} value
     */
    _setFieldsInCreateDrawer: function(attr, value) {
        if (this.moduleName === 'Accounts') {
            var accountsLogoURL = this._originalModel.get('hint_account_pic');
            var hintAccountLogoUrl = this.model.get('hint_account_logo');
            var accountsSicFieldFound = this._accountContainsSICLabel(attr);
            for (var key in attr) {
                if (attr.hasOwnProperty(key)) {
                    if (key === 'hint_account_sic_code_label') {
                        attr.sic_code = this.model.get('sic_code');
                    } else if (key === 'hint_account_logo') {
                        attr.hint_account_pic = hintAccountLogoUrl;
                    } else {
                        attr[key] = String(this.model.get(key));
                    }
                }
            }
            if (this._isValidAttrForLogoEnrich(attr, 'hint_account_logo') && (_.isEmpty(accountsLogoURL) ||
                accountsLogoURL !== hintAccountLogoUrl)) {
                this._saveHintsImageURL();
            }
            if (typeof attr === 'object') {
                this._originalModel.set(attr);
            } else if (accountsSicFieldFound) {
                this._originalModel.set('sic_code', this.model.get('sic_code'));
            } else {
                this._originalModel.set(attr, value);
            }
        } else {
            var contactsPhotoURL = this.model.get('hint_photo');
            for (var key in attr) {
                var isValidPhoneField = attr.hasOwnProperty(key) && this.isValidPhoneType(key);
                var phoneNumber = this.model.get(key);
                if (isValidPhoneField) {
                    attr[key] = phoneNumber && this._getTruncatedPhoneBasedOnFieldWidth(phoneNumber, key);
                } else if (key === 'hint_photo') {
                    attr.hint_contact_pic = contactsPhotoURL;
                }  else if (key === 'website') {
                    attr[key] = String(this.model.get('hint_account_website'));
                } else {
                    attr[key] = String(this.model.get(key));
                }
            }
            this._originalModel.set(attr, value);
            var contactsLeadsPhotoURL = this._originalModel.get('hint_contact_pic');
            if (this._isValidAttrForLogoEnrich(attr, 'hint_photo') && (_.isEmpty(contactsLeadsPhotoURL) ||
                contactsLeadsPhotoURL !== contactsPhotoURL)) {
                this._saveHintsImageURL();
            }
        }
    },

    /**
     * Stage2 data enrichment call
     *
     * @param {string} method
     * @param {string} api
     * @param {Object} options
     */
    _callStage2API: function(method, api, options) {
        var self = this;
        if (app.hint.isHintUser()) {
            app.api.call(method, app.api.buildURL(api), null, {
                success: function(data) {
                    if (api === 'stage2/params') {
                        self._stage2url = data.enrichmentServiceUrl;
                        self._stage2urlV2 = data.enrichmentServiceUrlV2;
                    }
                    options && options.success && options.success(data);
                },
                error: function(err) {
                    app.logger.error('Failed to get Hint param: ' + JSON.stringify(err));
                    self._setStage2ErrorCode(err);
                    options && options.error && options.error(err);
                }
            });
        } else {
            var err = {
                status: 402
            };
            self._setStage2ErrorCode(err);
        }
    },

    /**
     * Record event
     *
     * @param {string} eventType
     * @param {string} target
     */
    _recordEvent: function(eventType, target) {
        var self = this;
        if (_.isEmpty(self._stage2url)) {
            self._fetchStage2Url('GET', 'stage2/params', function(/*data*/) {
                self._recordEvent(eventType, target);
            });
            return;
        }
        this._issueRequest('POST', self._stage2url + '/event', {
            metricsToken: self._getMetricsToken(),
            eventType: eventType,
            target: target,
            origin: self.moduleName
        }, {
            success: function() { },
            error: function(err) {
                app.logger.error('Failed to record event: ' + JSON.stringify(err));
            }
        });
    },

    /**
     * Social link clicked
     *
     * @param {Object} evt
     */
    _socialLinkClicked: function(evt) {
        var clickInfo = {
            clickType: 'socialMediaLink',
            clickedURL: evt.currentTarget.href,
            metricsToken: this._getMetricsToken(),
            origin: this.context.get('module')
        };
        this._recordLinkClick(clickInfo);
    },

    /**
     * Record link click
     *
     * @param {Object} clickInfo
     */
    _recordLinkClick: function(clickInfo) {
        var self = this;
        if (_.isEmpty(self._stage2url)) {
            self._fetchStage2Url('GET', 'stage2/params', function(/*data*/) {
                self._recordLinkClick(clickInfo);
            });
            return;
        }
        this._issueRequest('POST', self._stage2url + '/url-click', clickInfo, {
            success: function() { },
            error: function(err) {
                app.logger.error('Failed to record link click event: ' + JSON.stringify(err));
            }
        });
    },

    /**
     * Encapsulate access to storage representation
     */
    _getMetricsToken: function() {
        return app.user.get('hintMetricsToken');
    },

    /**
     * Check if only accounts logo differ
     *
     * @param {Object} para1
     * @return {boolean}
     */
    _doOnlyAccountsLogosDiffer: function(para1) {
        var accountsLogoURL = this._originalModel.get('hint_account_pic');
        var doesLogoUrlDiffer = !_.isEmpty(accountsLogoURL) && accountsLogoURL !== this.model.get('hint_account_logo');
        var isWebsiteFieldSame = para1 === 'website' &&
            (this._originalModel.get('website') === this.model.get('website'));

        return isWebsiteFieldSame && doesLogoUrlDiffer;
    },

    /**
     * Check if non accounts photo url differ
     *
     * @return {boolean}
     */
    _doNonAccountsPhotoURLsDiffer: function() {
        var photoURL = this._originalModel.get('hint_contact_pic');
        var photoURLFromHint = this.model.get('hint_photo');
        var imagePathInSugar = this._originalModel.get('picture');
        return (!_.isEmpty(photoURL) && photoURL !== photoURLFromHint) ||
            (!_.isEmpty(imagePathInSugar) && imagePathInSugar !== photoURLFromHint);
    },

    /**
     * Check if only photos differ
     *
     * @param {Object} para1
     * @return {boolean}
     */
    _doOnlyPhotosDiffer: function(para1) {
        var jobTitle = this._originalModel.get('title');
        var isTitleFieldSame = para1 === 'title' && (jobTitle === this.model.get('title'));
        return isTitleFieldSame && this._doNonAccountsPhotoURLsDiffer();
    },

    /**
     * Check if non accounts create photo url differ
     *
     * @return {boolean}
     */
    _doNonAccountsCreatePhotoURLsDiffer: function() {
        var photoURL = this._originalModel.get('hint_contact_pic');
        var photoURLFromHint = this.model.get('hint_photo');
        var imagePathInSugar = this._originalModel.get('picture_guid');
        return (!_.isEmpty(photoURL) && photoURL !== photoURLFromHint) ||
            (!_.isEmpty(imagePathInSugar) && imagePathInSugar !== photoURLFromHint);
    },

    /**
     * Check if only create drawer photos differ
     *
     * @param {Object} para1
     * @return {boolean}
     */
    _doOnlyCreateDrawerPhotosDiffer: function(para1) {
        var jobTitle = this._originalModel.get('title');
        var isTitleFieldSame = para1 === 'title' && (jobTitle === this.model.get('title'));
        return isTitleFieldSame && this._doNonAccountsCreatePhotoURLsDiffer();
    },

    /**
     * Update base model
     *
     * @param {Object} para1
     * @param {Object} para2
     */
    _updateBaseModel: function(para1, para2) {
        var accountsSicFieldFound = this._accountContainsSICLabel(para1);
        var hintViewField = accountsSicFieldFound ? this.model.get('sic_code') : this.model.get(para1);
        var recordViewField =
            accountsSicFieldFound ? this._originalModel.get('sic_code') : this._originalModel.get(para1);
        var msg = SUGAR.App.lang.get('LBL_HINT_MSG_OVERWRITE_ALL');
        msg = typeof(para1) === 'object' ? msg : '';

        if (this.stage2CreateMode) {
            if (this.moduleName !== 'Accounts') {
                if (msg) {
                    this._warningAlertForCreateDrawer(para1, para2, msg);
                } else {
                    if (para1 === 'title' && this._doOnlyCreateDrawerPhotosDiffer(para1)) {
                        this._warningAlertForImageOverwrite('hint_contact_pic', true, false);
                    } else if (para1 === 'title' && this._doNonAccountsCreatePhotoURLsDiffer() &&
                        (this._originalModel.get('title') !== this.model.get('title'))) {
                        this._warningAlertForImageOverwrite('hint_contact_pic', true, true);
                    }  else if (para1 === 'title' && _.isEmpty(this._originalModel.get('hint_contact_pic'))) {
                        this._saveHintsImageURL();
                    } else if (recordViewField && (recordViewField !== hintViewField)) {
                        this._warningAlertForCreateDrawer(para1, para2, msg);
                    } else {
                        this._setFieldsInCreateDrawer(para1, para2);
                    }
                }
            } else {
                if (para1 === 'website' && _.isEmpty(this._originalModel.get('hint_account_pic'))) {
                    this._saveHintsImageURL();
                } else {
                    this._warningAlertForCreateDrawer(para1, para2, msg);
                }
            }
        } else if (msg) {
            this._warningAlert(para1, para2, msg);
        }  else if (this._doOnlyAccountsLogosDiffer(para1)) {
            this._warningAlertForImageOverwrite('hint_account_pic', true, false);
        } else if (this._doOnlyPhotosDiffer(para1)) {
            this._warningAlertForImageOverwrite('hint_contact_pic', true, false);
        } else if (recordViewField && (recordViewField !== hintViewField)) {
            this._warningAlert(para1, para2, msg);
        } else if (accountsSicFieldFound) {
            this._originalModel.save('sic_code', this.model.get('sic_code'));
        } else {
            this._saveDataInRecordField(para1, para2);
        }
    },

    /**
     * Save data in record field
     *
     * @param {Object} para1
     * @param {Object} para2
     */
    _saveDataInRecordField: function(para1, para2) {
        var needsAccountPic = para1 === 'website' &&
                     _.isEmpty(this._originalModel.get('hint_account_pic')) &&
                     this.module === 'Accounts';
        var needsContactPic = para1 === 'title' && _.isEmpty(this._originalModel.get('hint_contact_pic'));
        var enrichNewContactPicAndTitle = _.isEmpty(this._originalModel.get('title')) &&
            this._doNonAccountsPhotoURLsDiffer();

        if (needsAccountPic) {
            this._saveHintsImageURL();
        } else if (enrichNewContactPicAndTitle) {
            this._warningAlertForImageOverwrite('hint_contact_pic', false, false);
        } else if (needsContactPic) {
            this._saveHintsImageURL();
        } else if (this.isValidPhoneType(para1)) {
            var truncatedPhone = this._getTruncatedPhoneBasedOnFieldWidth(para2, para1);
            this._originalModel.save(para1, truncatedPhone);
        } else {
            this._originalModel.save(para1, para2);
        }
    },

    /**
     * Set original model
     *
     * @param {Object} para1
     * @param {Object} para2
     */
    _setOriginalModel: function(para1, para2) {
        if (this.isValidPhoneType(para1)) {
            var truncatedPhone = this._getTruncatedPhoneBasedOnFieldWidth(para2, para1);
            this._originalModel.set(para1, truncatedPhone);
        } else {
            this._originalModel.set(para1, para2);
        }
        this.trigger('hint:user-save');
    },

    /**
     * Get sidebar image field
     */
    _getSidebarImageField: function() {
        var sidebarComponent = this.layout.closestComponent('sidebar');
        return sidebarComponent && sidebarComponent._components[0] && sidebarComponent._components[0]._components[0] &&
            sidebarComponent._components[0]._components[0].getField('picture');
    },

    /**
     * Save hints image URL
     *
     * @param {boolean} overwriteOnlyImage
     */
    _saveHintsImageURL: function(overwriteOnlyImage) {
        var websiteRecordField = this._originalModel.get('website');
        var TitleRecordField = this._originalModel.get('title');
        var doEnrichWebsiteLogoDuo = !overwriteOnlyImage &&
            (_.isEmpty(websiteRecordField) || (websiteRecordField !== this.model.get('website')));
        var enrichAccountsFieldForLogo = {
            'website': this.model.get('website'),
            'hint_account_pic': this.model.get('hint_account_logo')
        };
        var doEnrichTitlePhotoDuo = !overwriteOnlyImage &&
            (_.isEmpty(TitleRecordField) || (TitleRecordField !== this.model.get('title')));
        var enrichNonAccountFieldForLogo = {
            'hint_contact_pic': this.model.get('hint_photo'),
            'title': this.model.get('title')
        };

        var isSugarEditMode = app.router._currentFragment.indexOf('/edit') !== -1;

        if (this.moduleName === 'Accounts') {
            if (doEnrichWebsiteLogoDuo) {
                if (this.stage2CreateMode) {
                    this._originalModel.set(enrichAccountsFieldForLogo);
                } else {
                    this._originalModel.save(enrichAccountsFieldForLogo);
                }
            } else {
                if (this.stage2CreateMode) {
                    this._originalModel.set('hint_account_pic', this.model.get('hint_account_logo'));
                } else {
                    this._originalModel.save('hint_account_pic', this.model.get('hint_account_logo'));
                }
            }
        } else {
            if (doEnrichTitlePhotoDuo) {
                if (this.stage2CreateMode || isSugarEditMode) {
                    this._originalModel.set('picture_guid', null);
                    this._originalModel.set(enrichNonAccountFieldForLogo);
                } else {
                    var field = this._getSidebarImageField();
                    if (!_.isEmpty(field)) {
                        field.deleteImage(this._originalModel, enrichNonAccountFieldForLogo);
                    } else {
                        this._originalModel.save(enrichNonAccountFieldForLogo);
                    }
                }
            } else {
                if (this.stage2CreateMode) {
                    this._originalModel.set('picture_guid', null);
                    this._originalModel.set('hint_contact_pic', this.model.get('hint_photo'));
                } else {
                    var field = this._getSidebarImageField();
                    if (!_.isEmpty(field)) {
                        field.deleteImage(this._originalModel, this.model.get('hint_photo'));
                    } else {
                        this._originalModel.save('hint_contact_pic', this.model.get('hint_photo'));
                    }
                }
            }
        }
    },

    /**
     * Update work phones
     *
     * @return {string}
     */
    _updateWorkPhones: function() {
        var phones = this.model.get('phone_work') || '';
        var newPhone = this.model.get('hint_phone_1');
        if (!this._isEmpty(newPhone) && phones.indexOf(newPhone) < 0) {
            phones = (phones === '') ? newPhone : (phones + '. ' + newPhone);
        }
        newPhone = this.model.get('hint_phone_2');
        if (!this._isEmpty(newPhone) && phones.indexOf(newPhone) < 0) {
            phones = (phones === '') ? newPhone : (phones + '. ' + newPhone);
        }
        return phones;
    },

    /**
     * Copy
     *
     * @param {Object} e
     */
    copy: function(e) {
        var self = this;
        var $btn = $(e.currentTarget);
        var attr = $btn.data('name');
        $btn.attr('data-action', '');

        var erasedFields = self.model.get('_erased_Fields');

        if (_.isEqual(attr, 'saveAll')) {

            self.filteredAttrsToSave = this._attrsToSave;
            // Update work phone value.
            if (self.moduleName !== 'Accounts') {
                var phones = this._updateWorkPhones();
                if (!self._isEmpty(phones)) {
                    self.filteredAttrsToSave.phone_work = phones;
                }
                self.filteredAttrsToSave = _.omit(self.filteredAttrsToSave, 'hint_phone_1', 'hint_phone_2');
            }
            // Add loading animation for saveAll icon.
            $btn.html('');
            $btn.removeClass('btn btn-primary saveAll').addClass('hint-preview-icon--loading');

            //Remove any erased fields
            self.filteredAttrsToSave = _.omit(self.filteredAttrsToSave, self._erasedFields);

            setTimeout(function() {
                $btn.removeClass('hint-preview-icon--loading').addClass('hint-preview-icon--confirm bg-transparent');

                setTimeout(function() {
                    $btn.addClass('hidden');
                }, 2000);
                setTimeout(function() {
                    if (!_.isEmpty(self.filteredAttrsToSave)) {
                        self._recordEvent('saveAllHintPersonFields', '*');
                        self._updateBaseModel(self.filteredAttrsToSave);
                        this.$('[data-action="copy"]').addClass('hidden');
                        self.$('[data-name="saveAll"]').tooltip('dispose');
                    }
                }, 500);
            }, 2000);
            // Save clicked field.
        } else {
            // Show loading icon first
            $btn.removeClass('hint-preview-icon--cloud').addClass('hint-preview-icon--loading');
            self._recordEvent('saveHintPersonField', attr);
            switch (attr) {
                case 'hint_account_website':
                    if (self.moduleName === 'Leads') {
                        self._updateBaseModel('website', this.model.get(attr));
                        self._attrsToSave = _.omit(self._attrsToSave, 'website');
                    }
                // fall through deliberately
                default:
                    self._updateBaseModel(attr, String(this.model.get(attr)));
                    self._attrsToSave = _.omit(self._attrsToSave, attr);
            }

            // Tracking when specific field gets saved by client.
            if (_.contains(this.enrichAttributeList, attr)) {
                this._trackEvent(' Enrichment - List View Preview', attr + ' saved');
            }

            $btn.removeClass('hint-preview-icon--loading').addClass('hint-preview-icon--confirm bg-transparent');
            setTimeout(function() {
                $btn.addClass('hidden');
            }, 1500);

            if (!_.isEmpty(self._attrsToSave)) {
                self.$('[data-name="saveAll"]').tooltip({
                    title: app.lang.get('LBL_HINT_PREVIEW_TOOLTIP_TITLE'),
                    placement: 'left'
                });
            } else {
                self.$('[data-name="saveAll"]').remove();
            }
        }
    }
});
