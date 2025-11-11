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
 * Extends `clients/base/layouts/preview/preview.js` in a way that allows
 * a safe uninstall. The purpose of this extension is to add custom views
 * to the existing preview layout so the users would be able to view
 * enriched data more easily.
 */
(function(app) {
    /**
     * Check the type of the main layout.
     *
     * @param {string} layoutName The name of the expected layout.
     * @return {boolean} True if the expected layout is rendered.
     */
    function isGivenLayout(layoutName) {
        if (app.sideDrawer && app.sideDrawer.isOpen()) {
            return app.sideDrawer._components[0].name === layoutName;
        }
        return app.controller.layout && app.controller.layout.name === layoutName;
    }

    /**
     * Will try to lookup and return a view based on a hierarchy tree,
     * where the last item is the name of the target view.
     *
     * @param {View.Layout} component A layout representing the root element.
     * @param {Array} hierarchyPath A path through the component hierarchy tree.
     * Given a root element and the navigation path we should be able to
     * access the target component.
     * @return {View.Layout|View.View|undefined} Returns the target view or undefined if not found.
     */
    function getView(component, hierarchyPath) {
        while (component && hierarchyPath.length) {
            component = component.getComponent(hierarchyPath.shift());
        }
        return component;
    }

    /**
     * Get preview
     *
     * Will attempt to get the preview layout. There may be 2 preview layouts simultaneously
     * (one on the base layout and one in a create drawer). In order to get the relevant layout
     * we will assume that the drawer is active and get the layout from it. If we don't succeed,
     * it means that we have the preview layout on the base layout.
     *
     * @return {View.Layout|undefined} A preview layout or undefined if not found.
     */
    function getPreview() {
        var previewCmpPath = ['sidebar', 'preview-pane', 'preview'];
        return getView(app.drawer, ['create'].concat(previewCmpPath)) ||
            getView(app.sideDrawer, ['record'].concat(previewCmpPath)) ||
            getView(app.controller.layout, previewCmpPath);
    }

    /**
     * Get preview pane
     *
     * @return {Object}
     */
    function _getPreviewPane() {
        var previewCmpPath = ['sidebar', 'preview-pane'];
        return getView(app.drawer, ['create'].concat(previewCmpPath)) ||
            getView(app.sideDrawer, ['record'].concat(previewCmpPath)) ||
            getView(app.controller.layout, previewCmpPath);
    }

    /**
     * Get specific layout position
     *
     * @param {Object} path
     * @param {string} layout
     * @return {int}
     */
    function _getSpecificLayoutPosition(path, layout) {
        const component = path ? path._components : [];
        for (var i = 0; i < component.length; i++) {
            if (component[i].label === layout || component[i].name === layout) {
                return i;
            }
        }
        return -1;
    }

    /**
     * Get non hint preview
     *
     * @return {app.view.Component}
     */
    function _getNonHintPreview() {
        var previewPane = _getPreviewPane();
        var componentIndex = _getSpecificLayoutPosition(previewPane, 'preview');
        if (componentIndex >= 0) {
            return previewPane._components[componentIndex];
        }
    }

    /**
     * Get hint preview
     *
     * Will attempt to get the Hint-preview layout. There are two preview layouts as added bu the layout/records.php
     * First one displays the regular preview and the other one displays the Hint. As defined by the records.php
     * The Hint layout is displayed next to the preview and then the remaining Sync calls are made to the external Apps
     * viz. Ankara.
     *
     * @return {View.Layout|undefined} A hint-preview layout or undefined if not found.
     */
    function _getHintPreview() {
        var previewPane = _getPreviewPane();
        var componentIndex = _getSpecificLayoutPosition(previewPane, 'Hint-Tab');
        if (componentIndex >= 0) {
            return previewPane._components[componentIndex];
        }
    }

    /**
     * Will add a child component meta to the preview indicated.
     *
     * @param {View.Layout} preview The parent of the components to be added.
     * @param {Object} cmp The definition of a component to be added.
     * @param {string|undefined} type The type of the component, optional, defaults to 'view'.
     */
    function addViewToPanelMeta(preview, cmp, type) {
        var component = {
            context: {
                forceNew: true
            }
        };
        component[type || 'view'] = cmp;
        preview._componentsMeta.push(component);
    }

    /**
     * Checks if the model is enriched by hint.
     *
     * @param {Data.Bean} model The model used to populate the preview.
     * @return {boolean} True if it's enriched.
     */
    function isEnrichedModel(model) {
        var enrichedModules = ['Leads', 'Contacts', 'Accounts'];
        return _.contains(enrichedModules, model.module);
    }

    /**
     * Is triggered on subpanel
     *
     * Checks if the preview has been triggered from a module which may be related
     * to a hint enriched module. This may happen if a record view has a subpanel
     * related to a module enriched by hint. The given modules may hold such subpanel entries.
     *
     * @param {Data.Bean} model The model to populate the preview with.
     * @return {boolean} True if the given model appears in the subpanel
     * of the active record view.
     */
    function isTriggeredOnSubpanel(model) {
        var hasModelFromSubpanel = false;
        var recordModel = app.sideDrawer && app.sideDrawer.isOpen() ?
            app.sideDrawer._components[0].model :
            app.controller.layout.model;
        var moduleLink = model.link && model.link.name;
        if (moduleLink && isGivenLayout('record') && recordModel) {
            var relatedCollection = recordModel.getRelatedCollection(moduleLink);
            var relatedModel = relatedCollection && relatedCollection.get(model.cid);
            hasModelFromSubpanel = !!relatedModel;
        }
        return hasModelFromSubpanel;
    }

    /**
     * Checks if the preview has been triggered from a list view. Have to consider
     * the case when the user opens a merge preview, since the underlieing active
     * layout is still the list view.
     *
     * @param {Data.Bean} preview The preview being subjct of the render event.
     * @return {boolean} True if the preview meant to be rendered on a list view.
     */
    function isTriggeredOnListview(preview) {
        return isGivenLayout('records') && !isInMergeView(preview);
    }

    /**
     * Checks if the given preview exists in scope of a merge duplicate process.
     *
     * @param {View.Layout} preview The active preview layout.
     * @return {boolean} True of the merge duplicates layout is opened.
     */
    function isInMergeView(preview) {
        return preview.options.type === 'merge-duplicates-preview';
    }

    /**
     * Checks if the base layout is the create layout.
     * Create layout may exist in scope of a drawer, but in case the user hits
     * refresh Sugar reloads and renders the create layout as it is, without drawer.
     *
     * @return {boolean} True if the create layout is opened.
     */
    function isCreateLayout() {
        return !!(getView(app.drawer, ['create']) || isGivenLayout('create'));
    }

    /**
     * Checks if preview is triggered from an enriched record view. Since by default preview
     * is not available on a record view, we check if the appropriate preview id has been set.
     * For more details about preview id please check the `hint-dashboardtitle` field.
     *
     * @param {Data.Bean} model The model to populate the preview with.
     * @return {boolean} True if the preview has been triggered by the Hint dashboard button.
     */
    function isEnrichedRecordView(model) {
        var isEnrichedRecord = isGivenLayout('record') && isEnrichedModel(model);
        var dashBoardHeaderPath = ['sidebar', 'dashboard-pane', 'dashboard', 'dashboard-headerpane'];
        var recordLayout = app.sideDrawer && app.sideDrawer.isOpen() ? app.sideDrawer._components[0] :
            app.controller.layout;
        var dashBoardHeader = getView(recordLayout, dashBoardHeaderPath);
        if (isEnrichedRecord && dashBoardHeader) {
            var dashboardTitle = _.findWhere(dashBoardHeader.fields, {type: 'hint-dashboardtitle'});
            isEnrichedRecord = dashboardTitle && dashboardTitle.getHintState(dashboardTitle.hintStateKey);
        }
        return isEnrichedRecord;
    }

    /**
     * Checks if the hint preview should be rendered instead of default preview. There are four cases, when
     * hint preview should be applied (for each case we need to have a hint enriched model):
     * 1. Preview is triggered from the create layout.
     * 2. Preview is triggered through a global search result.
     * 3. Preview is triggered from a regular list view.
     * 4. Preview is triggered from a subpanel of a module which is not enriched by hint.
     * 5. Preview is triggered from an enriched record view.
     *
     * @param {View.Layout} preview The active preview layout.
     * @param {Data.Bean} model The model used to populate the given preview, but returned through an event.
     * The difference is that it holds extra information compared to the model directly accessible through preview.
     * @return {boolean} True if the hint preview should be rendered.
     */
    function isHintPreview(preview, model) {
        var doesHintApply = false;
        var shouldHintBeDisplayed = app.hint.isSugarProSpecialCase() || app.hint.isHintUser();
        if (isEnrichedModel(model)) {
            doesHintApply = isCreateLayout() || isGivenLayout('search') ||
                isTriggeredOnListview(preview) || isTriggeredOnSubpanel(model) || isEnrichedRecordView(model);
        }
        return doesHintApply && shouldHintBeDisplayed;
    }

    /**
     * In case of Accounts we need to display any related accounts, but only on list view.
     *
     * @return {boolean} True if related contacts should be displayed.
     */
    function shouldShowRelatedContacts(model) {
        return model.module === 'Accounts' && isGivenLayout('records');
    }

    /**
     * Will add metadata to the preview so the preview would be able to
     * render components specific to the hint data-enrichment.
     *
     * @param {View.Layout} preview The active preview layout.
     * @param {Data.Bean} model The model used to populate the preview layout.
     * @param {Data.BeanCollection} collection The
     * {@link Data.BeanCollection collection} of preview models.
     */
    function addHintPreviewComponents(preview, model, collection) {
        preview._componentsMeta = [];
        addViewToPanelMeta(preview, 'hint-preview-header');
        preview._componentsMeta.push({
            view: 'stage2-preview',
        });
        if (app.hint.isHintUser()) {
            if (!isCreateLayout()) {
                if (shouldShowRelatedContacts(model)) {
                    addViewToPanelMeta(preview, {
                        type: 'hint-panel-header',
                        icon: 'user',
                        title: 'LBL_HINT_CONTACTS_TITLE'
                    });
                    addViewToPanelMeta(preview, 'stage2-related-contacts');
                }
                addViewToPanelMeta(preview, 'hint-news-panel', 'layout');
                addViewToPanelMeta(preview, {
                    type: 'hint-panel-header',
                    icon: 'history',
                    title: 'LBL_HINT_HISTORY_TITLE'
                });
                addViewToPanelMeta(preview, 'stage2-history');
            }
        }
    }

    /**
     * Check if sugar have tabbed dashlets
     *
     * @return {bool}
     */
    function doesSugerHaveTabbedDashlets() {
        return app.hint.versionCompare() >= 0;
    }

    /**
     * Tab view validation check
     *
     * @param {Object} model
     * @return {bool}
     */
    function _tabViewValidationCheck(model) {
        var validModules = ['Accounts', 'Contacts', 'Leads'];
        return _.contains(validModules, model.module) && doesSugerHaveTabbedDashlets();
    }

    /**
     * Add default preview components
     *
     * Will add back the default metadata to the preview layout.
     * This needs to be done after a non-enriched module's record
     * is previewed after an enriched model has been.
     *
     * @param {string} modelName Module name to be supplied for the getLayout method to work correctly.
     * @param {View.Layout} preview The active preview layout.
     */
    function addDefaultPreviewComponents(modelName, preview) {
        preview._componentsMeta = app.metadata.getLayout(modelName, 'preview').components;
    }

    /**
     * Edit styles
     *
     * Will try to display hidden Tabs after non Hint SubModules are clicked.
     * It also overrides the method defined the tabbed-layout.js to display the regular preview and the Hint Preview
     * to deal with address the issue caused by thge _toggle() of the preview-layout. Since there are 2 preview layouts
     * the overwriting the tabbed-layout.js of SUGAR handles the issue correctly.
     * The classNames are unique for the Tabbable and nav-tabs components.
     */
    function _editStyles() {
        var $navTabs;
        var $tabbableClass;
        var tabbable = App.sideDrawer && App.sideDrawer.isOpen() ?
            App.sideDrawer._components[0]._components[0] :
            App.controller.layout._components[0]; //calling the parent component in sidebar.
        var self = tabbable._components[2]; //preview-pane in sugar layout/tabbed-layout.js
        var hintTabsHidden = tabbable.$(tabbable.$('.tabbable.hide-tabs.preview-active'));
        var hintLabelTitle = tabbable.$(tabbable.$('.nav-item'));
        var hintLabelNonHidden = tabbable.$(tabbable.$('.nav.nav-tabs.related-tabs.preview-pane-tabs'));
        var hintLabelHidden =  tabbable.$(tabbable.$('.nav.nav-tabs.related-tabs.hide.preview-pane-tabs'));
        var hintPreviewPosition = _getSpecificLayoutPosition(_getPreviewPane(), 'Hint-Tab') + 1;

        self.$(hintLabelTitle[hintPreviewPosition]).addClass('hint-nav-tab');

        if (hintLabelHidden.length) {
            hintLabelHidden.removeClass('hide');
            hintLabelTitle[hintPreviewPosition].children[0].innerText = app.lang.get('LBL_HINT_PANEL');
        } else if (hintLabelNonHidden.length) {
            hintLabelTitle[hintPreviewPosition].children[0].innerText = app.lang.get('LBL_HINT_PANEL');
        }

        if (hintTabsHidden.length) {
            hintTabsHidden.removeClass('hide-tabs');
        }

        self.$('li[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            var tabName = $(e.target.parentElement).data('tab-name');
            $navTabs = $(e.target).parents('.nav-tabs');
            $tabbableClass = $(e.target).parents('.tabbable');
            $navTabs.addClass('preview-pane-tabs', tabName === 'preview');
            $tabbableClass.addClass('preview-active', tabName === 'preview');
        });
    }

    /**
     * Edit styles undo
     *
     * Will try to hide Tabs after non Hint SubModules are clicked.
     * It also overrides the method defined the tabbed-layout.js to display the regular preview and make the regular
     * preview tab as active by comparing the version number the appropriate styles are applied
     * for SUGAR Version >= 9.1.0 and hide the tabs created by records.php for the previous versions of SUGAR.
     * The classNames are unique for the Tabbable and nav-tabs components.
     */
    function _editStylesHintUndo() {
        var tabbable = App.sideDrawer && App.sideDrawer.isOpen() ?
            App.sideDrawer._components[0]._components[0] :
            App.controller.layout._components[0]; // calling the parent component in sidebar.
        var hintTabs = tabbable.$(tabbable.$('.tabbable.preview-active'));
        var hintLabel = tabbable.$(tabbable.$('.nav.nav-tabs.related-tabs.preview-pane-tabs'));
        if (hintLabel.length) {
            hintLabel.addClass('hide');
        }
        if (hintTabs.length) {
            hintTabs.addClass('hide-tabs');
        }

        if (tabbable._components) {
            var self = tabbable._components[2];
            var previewPosition = _getSpecificLayoutPosition(_getPreviewPane(), 'preview');
            var hintPreviewPosition = _getSpecificLayoutPosition(_getPreviewPane(), 'Hint-Tab');

            if (previewPosition >= 0 && self) {
                var nonHintSubmodulePreview = self.$(self.$('li')[previewPosition]).hasClass('active');
                var nonHintSubmodulePreview2 = self.$(self.$('.tab-pane')[previewPosition]).hasClass('active');
                if (!nonHintSubmodulePreview && !nonHintSubmodulePreview2) {
                    self.$(self.$('li')[previewPosition]).addClass('active');
                    self.$(self.$('.tab-pane')[previewPosition]).addClass('active');
                }
            }

            if (hintPreviewPosition >= 0) {
                var hintPreviewChosen = self.$(self.$('li')[hintPreviewPosition]).hasClass('active');
                var hintPreviewChosen2 = self.$(self.$('.tab-pane')[hintPreviewPosition]).hasClass('active');
                if (hintPreviewChosen && hintPreviewChosen2) {
                    self.$(self.$('li')[hintPreviewPosition]).removeClass('active');
                    self.$(self.$('.tab-pane')[hintPreviewPosition]).removeClass('active');
                }
            }
        }
    }

    /**
     * Edit styles preview undo
     */
    function _editStylesPreviewUndo() {
        var tabbable = App.sideDrawer && App.sideDrawer.isOpen() ?
            App.sideDrawer._components[0]._components[0] :
            App.controller.layout._components[0]; // calling the parent component in sidebar.
        var hintTabs = tabbable.$(tabbable.$('.tabbable.preview-active'));
        var hintLabel = tabbable.$(tabbable.$('.nav.nav-tabs.related-tabs.preview-pane-tabs'));
        if (hintLabel.length) {
            hintLabel.addClass('hide');
        }
        if (hintTabs.length) {
            hintTabs.addClass('hide-tabs');
        }

        var self = tabbable._components[2];
        var previewPosition = _getSpecificLayoutPosition(_getPreviewPane(), 'preview');
        var hintPreviewPosition = _getSpecificLayoutPosition(_getPreviewPane(), 'Hint-Tab');

        if (hintPreviewPosition >= 0) {
            var nonHintSubmodulePreview = self.$(self.$('li')[hintPreviewPosition]).hasClass('active');
            var nonHintSubmodulePreview2 = self.$(self.$('.tab-pane')[hintPreviewPosition]).hasClass('active');
            if (!nonHintSubmodulePreview && !nonHintSubmodulePreview2) {
                self.$(self.$('li')[hintPreviewPosition]).addClass('active');
                self.$(self.$('.tab-pane')[hintPreviewPosition]).addClass('active');
            }
        }

        if (previewPosition >= 0) {
            var hintPreviewChosen = self.$(self.$('li')[previewPosition]).hasClass('active');
            var hintPreviewChosen2 = self.$(self.$('.tab-pane')[previewPosition]).hasClass('active');
            if (hintPreviewChosen && hintPreviewChosen2) {
                self.$(self.$('li')[previewPosition]).removeClass('active');
                self.$(self.$('.tab-pane')[previewPosition]).removeClass('active');
            }
        }
    }

    /**
     * Remove extra styles
     *
     * It hides the extra tabs created by records.php for the previous versions of SUGAR.
     * The classNames are unique for the Tabbable and nav-tabs components.
     */
    function _removeExtraStyles() {
        var tabbable = App.sideDrawer && App.sideDrawer.isOpen() ?
            App.sideDrawer._components[0]._components[0] :
            App.controller.layout._components[0];
        var hintLabel = tabbable.$(tabbable.$('.nav.nav-tabs.related-tabs')[3]);
        var addHideClassOnce = tabbable.$(tabbable.$('.nav.nav-tabs.related-tabs.hide')[3]).hasClass('hide');
        if (hintLabel.length && !(addHideClassOnce)) {
            hintLabel.addClass('hide');
        }
    }

    /**
     * Toggle Preview
     *
     * Event listener which is triggered by the `preview:render` event.
     * In case an active preview layout is found and the model also has been changed,
     * it will check which kind of preview should be rendered (default or hint).
     * This listener is executed before the default listener from the original preview;
     * by modifying the metadata we can indicate which components need to be rendered.
     *
     * @param {Data.Bean} model The model used to populate the active preview layout.
     */
    function togglePreview(model, collection) {
        var preview;
        var modelName = model.module;
        if (app.hint.isHintUser()) {
            var isRecordViewlayoutType = SUGAR.App.sideDrawer && SUGAR.App.sideDrawer.isOpen() ?
                SUGAR.App.sideDrawer._components[0].type === 'record' :
                SUGAR.App.controller.layout.type === 'record';
            var hintViewInTab = app.hint.shouldUseOldHintPreview(modelName);
            if (_tabViewValidationCheck(model) && !isRecordViewlayoutType) {
                _editStyles();
                preview = _getHintPreview();
            } else if (_tabViewValidationCheck(model) && hintViewInTab) {
                _editStyles();
                preview = _getHintPreview();
            } else if (_tabViewValidationCheck(model) && !hintViewInTab) {
                _editStylesPreviewUndo();
                preview = _getHintPreview();
            } else if (doesSugerHaveTabbedDashlets()) {
                _editStylesHintUndo();
                preview = _getNonHintPreview();
            } else {
                _removeExtraStyles();
                preview = getPreview();
            }
        } else if (app.hint.isSugarProSpecialCase()) {
            _editStyles();
            preview = _getHintPreview();
        } else {
            _editStylesHintUndo();
            preview = _getNonHintPreview();
        }

        if (!preview || !preview._isActive()) {
            return;
        }

        var hasComponents = !_.isEmpty(preview._components);
        var isSameModel = model == preview.context.get('model');
        var modelChanged = preview.context.get('module') !== modelName;
        if (!isSameModel && (!hasComponents || modelChanged)) {
            if (isHintPreview(preview, model)) {
                addHintPreviewComponents(preview, model, collection);
            } else {
                addDefaultPreviewComponents(modelName, preview);
            }
        }
    }
    app.events.on('preview:render', togglePreview);
})(SUGAR.App);
