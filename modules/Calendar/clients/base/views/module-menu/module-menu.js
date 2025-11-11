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
    /**
     * @inheritdoc
     */
    populateMenu: function() {
        this.populate('calendars');
    },

    /**
     * @inheritdoc
     */
    populate: function(tplName, filter, limit) {
        app.api.call('read', app.api.buildURL('Calendar/modules'), {}, {
            success: _.bind(function(data) {
                this.resetCollection(tplName);

                _.each(data.modules, function(moduleInfo, module) {
                    let collection = this.getCollection(tplName);
                    let createLabel = '';

                    if (module === 'KBContents') {
                        createLabel = app.lang.getModString('LNK_NEW_KBCONTENT_TEMPLATE', module);
                    } else {
                        let createLabelKey = 'LNK_NEW_' + moduleInfo.objName.toUpperCase();
                        createLabel = app.lang.get(createLabelKey, module);

                        if (createLabel === createLabelKey) {
                            createLabelKey = 'LNK_NEW_RECORD';
                            createLabel = app.lang.getModString(createLabelKey, module);
                        }
                    }

                    const moduleItem = {
                        module: module,
                        label: createLabel
                    };
                    collection.push(moduleItem);
                }, this);

                this._renderPartial(tplName);
            }, this)
        });
    },

    /**
     * Reset collection
     *
     * @param {string} tplName The name of the partial template which uses this collection
     */
    resetCollection: function(tplName) {
        this._collections[tplName] = [];
    },

    /**
     * @inheritdoc
     */
    getCollection: function(tplName) {
        if (!this._collections[tplName]) {
            this._collections[tplName] = [];
        }

        return this._collections[tplName];
    },

    /**
     * @inheritdoc
     */
    _renderPartial: function(tplName, options) {
        if (this.disposed || !this.isOpen()) {
            return;
        }
        options = options || {};

        let tpl = app.template.getView(this.name + '.' + tplName, this.module) ||
            app.template.getView(this.name + '.' + tplName);

        const modules = this.getCollection(tplName);

        let $placeholder = this.$('[data-container="' + tplName + '"]');
        let $old = $placeholder.nextUntil('.divider');

        //grab the focused element's route (if exists) for later re-focusing
        const focusedRoute = $old.find(document.activeElement).data('route');

        //replace the partial using newly updated modules collection
        $old.remove();
        $placeholder.after(tpl(_.extend({'modules': modules}, options)));

        //if there was a focused element previously, restore its focus
        if (focusedRoute) {
            const $new = $placeholder.nextUntil('.divider');
            const focusSelector = '[data-route="' + focusedRoute + '"]';
            const $newFocus = $new.find(focusSelector);
            if ($newFocus.length > 0) {
                $newFocus.focus();
            }
        }
    }
});
