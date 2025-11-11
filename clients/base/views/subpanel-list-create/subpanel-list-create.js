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
 * Custom RecordlistView used for SubpanelCreate layouts.
 *
 * @class View.Views.Base.SubpanelListCreateView
 * @alias SUGAR.App.view.views.BaseSubpanelListCreateView
 * @extends View.Views.Base.SubpanelListView
 */
({
    extendsFrom: 'SubpanelListView',

    /**
     * @inheritdoc
     */
    dataView: 'subpanel-list-create',

    contextEvents: {
        'list:deleterow:fire': 'onDeleteRow',
        'list:addrow:fire': 'onAddRow'
    },

    /**
     * Flag if the view has all valid models
     */
    hasValidModels: true,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.events = _.extend({}, this.events, {
            'click [data-action=addRow]': 'onAddRow'
        });

        // undo flex-list's hardcoding and re-hardcode to use the subpanel-list-create.hbs
        this.template = app.template.getView('subpanel-list-create');

        // Set necessary context values
        let settings = this.context.get('settings');
        this.context.set({
            isCreateSubpanel: true,
            allowEmpty: settings && settings.allowEmpty
        });
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        var link;
        var oppsConfig;
        var userACLs;

        this._super('bindDataChange');

        link = this.context.get('link');
        oppsConfig = app.metadata.getModule('Opportunities', 'config');
        userACLs = app.user.getAcls();

        if (oppsConfig.opps_view_by === 'RevenueLineItems') {
            if (!(_.has(userACLs.Opportunities, 'edit') ||
                _.has(userACLs.RevenueLineItems, 'access') ||
                _.has(userACLs.RevenueLineItems, 'edit'))) {
                // only listen for PCDashlet if this is Opps in Opps/RLI mode and user has access
                // to both Opportunities and RLIs
                // need to trigger on app.controller.context because of contexts changing between
                // the PCDashlet, and Opps create being in a Drawer, or as its own standalone page
                // app.controller.context is the only consistent context to use
                var viewDetails = this.closestComponent('create') ||
                    this.closestComponent('record') ||
                    this.closestComponent('convert');

                if (!_.isUndefined(viewDetails)) {
                    app.controller.context.on(viewDetails.cid + ':productCatalogDashlet:add',
                        this.onAddFromProductCatalog,
                        this);
                }
            }
        }

        // listen to revalidate the collection
        this.context.parent.on('subpanel:validateCollection:' + link, this.validateModels, this);

        // listen to reset the collection
        this.context.parent.on('subpanel:resetCollection:' + link, this.resetSubpanel, this);

        this.collection.on('add remove', this.render, this);

        this.resetSubpanel();
    },

    /**
     * Handles when users click to add items from the Product Catalog dashlet to the Opportunity
     *
     * @param {Object} data The ProductCatalog Data
     */
    onAddFromProductCatalog: function(data) {
        var existingModel = this.collection.length === 1 && this.collection.at(0);
        var isEmpty = existingModel &&
            _.isEmpty(existingModel.changedAttributes()) &&
            _.isEmpty(existingModel.get('product_template_id'));

        data.likely_case = data.discount_price;
        data.best_case = data.discount_price;
        data.worst_case = data.discount_price;
        data.assigned_user_id = app.user.get('id');
        data.assigned_user_name = app.user.get('full_name');

        if (isEmpty) {
            this.collection.remove(existingModel);
        }

        this._addBeanToList(true, data);
    },

    /**
     * Resets the subpanel's collection and adds a new bean to the list
     */
    resetSubpanel: function() {
        this.collection.reset();

        // Unless we allow an empty list of models, add a default model
        if (!this.context.get('allowEmpty')) {
            this._addBeanToList(true);
        }
    },

    /**
     * @inheritdoc
     *
     * Toggles all fields in the SubpanelList to Edit view
     */
    render: function() {
        this._super('render');

        // toggle fields to edit view
        this._toggleEdit(true);

        _.defer(_.bind(function() {
            this.checkButtons();
        }, this));
    },

    /**
     * Handles toggling collection fields to edit or detail
     *
     * @param {Boolean} isEdit If we're toggling fields TO Edit view or not
     * @private
     */
    _toggleEdit: function(isEdit) {
        isEdit = isEdit || false;

        // toggle the fields in the list to be in edit mode
        _.each(this.collection.models, function(model) {
            this.toggleFields(this.rowFields[model.get('id')], isEdit);
            if (isEdit) {
                // this is a subpanel specific logic: when the subpanel is back to edit mode,
                // manually fire the dependency trigger on all its models
                this.context.trigger("list:editrow:fire", model, {def: {}});
            }
        }, this);
    },

    /**
     * Checks the -/+ buttons to enable/disable
     */
    checkButtons: function() {
        if (this.disposed) {
            return;
        }
        var delBtns = this.$('.deleteBtn');
        var addBtns = this.$('.addBtn');

        // When there is only one row, disable the delete button on it
        // unless an empty collection is allowed
        if (!this.context.get('allowEmpty') && delBtns && delBtns.length === 1 && !delBtns.hasClass('disabled')) {
            delBtns.addClass('disabled');
        }

        if (addBtns && addBtns.length > 1) {
            // disable all add buttons except the last row
            _.each(addBtns, function(btn, index) {
                if (index < addBtns.length - 1) {
                    $(btn).addClass('disabled');
                }
            });
        }
    },

    /**
     * @inheritdoc
     *
     * Overriding RecordList/FlexList addActions to use actionmenu-create for the left column
     *
     * @override
     */
    addActions: function() {
        if (this.actionsAdded) {
            return;
        }

        // just need the right-side actions
        if (this.meta && _.isObject(this.meta.rowactions)) {
            // add the fieldset and init rightColumns
            this.addRowActions();
        }

        this.actionsAdded = true;
    },

    /**
     * @inheritdoc
     *
     * Overriding flex-list addRowActions to make the rowactions-create type
     *
     * @override
     */
    addRowActions: function() {
        var _generateMeta = function(label, css_class, buttons) {
            return {
                'type': 'fieldset',
                'fields': [
                    {
                        'type': 'rowactions-create',
                        'label': label || '',
                        'css_class': css_class,
                        'buttons': buttons || [],
                        'no_default_action': true
                    }
                ],
                'value': false,
                'sortable': false
            };
        };
        var def = this.meta.rowactions;
        this.rightColumns.push(_generateMeta(def.label, def.css_class, def.actions));
    },

    /**
     * Validates the models in the subpanel
     *
     * @param {Function} callback The callback function to call after validation
     * @param {undefined|Boolean} [fromCreateView] If this function is being called from Create view or not
     */
    validateModels: function(callback, fromCreateView) {
        this.clearValidationErrors();

        fromCreateView = fromCreateView || false;

        // Wrap the callback function to be called with different parameters based
        // on the view we are in
        this.hasValidModels = true;
        let runCallback = () => {
            if (!_.isFunction(callback)) {
                return;
            }
            if (fromCreateView) {
                // the create waterfall wants the opposite of if this is validated
                callback(!this.hasValidModels);
            } else {
                // this view wants if the models are valid or not
                callback(this.hasValidModels);
            }
        };

        // If there are no created models in the subpanel, there's nothing to do
        if (_.isEmpty(this.collection.models)) {
            runCallback();
            return;
        }

        // There are created models in the subpanel, so loop through all models
        // and call doValidate on each model
        let returnCt = 0;
        _.each(this.collection.models, function(model) {
            model.doValidate(
                this.getFields(this.module, model),
                _.bind(function(isValid) {
                    returnCt++;

                    // Keep track of whether any model has failed validation
                    this.hasValidModels = this.hasValidModels && isValid;

                    // If all model validations have occurred, run the callback function
                    if (returnCt === this.collection.length) {
                        runCallback();
                    }
                }, this));
        }, this);
    },

    /**
     * Click handler for the Add (+) button.
     * Validates each model on the collection and if they all validate, calls
     */
    onAddRow: function() {
        this.validateModels(_.bind(this._addBeanToList, this));
    },

    /**
     * Handler for when the delete button is clicked
     *
     * @param model
     */
    onDeleteRow: function(model) {
        this.context.get('collection').remove(model);
        this.checkButtons();
    },

    /**
     * Adds a bean for this.module to the collection
     *
     * @param {Boolean} hasValidModels If this collection has validated models
     * @param {Object} prepopulateData The ProductCatalog data to add prepopulate an RLI
     * @private
     */
    _addBeanToList: function(hasValidModels, prepopulateData) {
        var beanId;
        var bean;
        var addAtZeroIndex;
        prepopulateData = prepopulateData || {};

        if (hasValidModels) {
            beanId = app.utils.generateUUID();
            addAtZeroIndex = !_.isEmpty(prepopulateData);

            bean = app.data.createBean(this.module);
            bean._module = this.module;
            bean.set('id', beanId);

            // check the parent record to see if an assigned user ID/name has been set
            if (this.context.parent && this.context.parent.has('model')) {
                var parentModel = this.context.parent.get('model'),
                    userId = parentModel.get('assigned_user_id'),
                    userName = parentModel.get('assigned_user_name');

                if (userId) {
                    bean.setDefault('assigned_user_id', userId);
                }

                if (userName) {
                    bean.setDefault('assigned_user_name', userName);
                }
            }

            // must add to this.collection so the bean shows up in the subpanel list
            if (addAtZeroIndex) {
                bean = this.collection.unshift(bean);
            } else {
                bean = this.collection.add(bean);
            }

            bean.set(prepopulateData);
            this._addCustomFieldsToBean(bean, addAtZeroIndex, prepopulateData);
            this._addCustomEventHandlers(bean);

            this.context.trigger('subpanel-list-create:row:added', bean);
        }

        this.checkButtons();
    },

    /**
     * Allows child functions to override and add module-specific properties to the bean
     * before it gets added to the collection
     *
     * @param {Data.Bean} bean The bean to add new properties to
     * @param {boolean} skipCurrency Skip or set currency properties
     * @param {Object} prepopulatedData data that has already been pre-populated on the bean
     * @return {Data.Bean}
     * @private
     */
    _addCustomFieldsToBean: function(bean, skipCurrency, prepopulatedData) {
        return bean;
    },

    /**
     * Allows subpanels that extend this to add module-specific event listeners to the bean,
     * for example to update fields when another changes
     * @param {Data.Bean} bean
     * @return {Data.Bean}
     * @private
     */
    _addCustomEventHandlers: function(bean) {
        return bean;
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        if (app.controller && app.controller.context) {
            var viewDetails = this.closestComponent('create') ?
                this.closestComponent('create') :
                this.closestComponent('record');

            if (!_.isUndefined(viewDetails)) {
                app.controller.context.off(viewDetails.cid + ':productCatalogDashlet:add', null, this);
            }
        }
        if (this.context && this.context.parent) {
            this.context.parent.off(null, null, this);
        }
        this._super('_dispose');
    }
})
