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
 * Used to display relationships for a certain module in tag builder.
 *
 * @class View.Views.Base.DocumentMerges.TagBuilderRelationshipsView
 * @alias SUGAR.App.view.views.BaseDocumentMergesTagBuilderRelatiosnhipsView
 */
({
    /**
     * @inheritdoc
     */
    events: {
        'change .relationshipSelect': 'setSelectedRelationship',
        'click .removeRelationship': 'removeRelationship',
        'click .relationshipOptions': 'showCollectionOptions',
    },

    /**
     * keeps the selected relationships
     *
     * @var array
     */
    relationshipStack: [],

    /**
     * keeps the order of the relationships selected modules
     *
     * @var array
     */
    relationshipModuleStack: [],
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', arguments);

        this.listenTo(this.context, 'tag-builder:reset-relationships', this._resetRelationships, this);
        this.listenTo(this.context, 'change:currentRelationshipsModule', this.addToRelationshipStack, this);
    },

    /**
     * @inheritdoc
     */
    render: function() {
        this._super('render');

        let relationshipList = this.$('select.relationshipSelect');

        relationshipList.select2({
            placeholder: relationshipList.attr('placeholder'),
        });
    },

    /**
     * Set the selected value on the last relationship
     *
     * @param {Event} evt
     */
    setSelectedRelationship: function(evt) {
        const relationship = evt.target.value;
        let lastRelationship = this.relationshipStack.pop();

        //find the selected relationship and set the selected value on that item
        for (let rel of lastRelationship) {
            if (rel.name === relationship) {
                const relationshipMeta = app.metadata.getRelationship(rel.relationship);
                const relationshipModule = relationshipMeta.lhs_module !==
                    (this.currentRelationshipsModule || this.context.get('currentModule')) ?
                    relationshipMeta.lhs_module : relationshipMeta.rhs_module;
                rel.selected = true;
                this.currentRelationshipsModule = relationshipModule;
            } else {
                rel.selected = false;
            }
        }

        this.relationshipStack.push(lastRelationship);

        this.context.set({
            currentRelationshipsModule: this.currentRelationshipsModule,
            currentModule: this.currentRelationshipsModule
        });
    },

    /**
     * Adds relatiosnhips to stack
     *
     * @param {app.Context} context
     * @param {string} module
     */
    addToRelationshipStack: function(context, module) {
        if (_.isEmpty(module)) {
            this.relationshipStack = [];
            this.relationshipModuleStack = [];
            this.currentModule = module;
            this.currentRelationshipsModule = module;
            this.render();
            return;
        }

        let relationships = this.filterRelationships(module);

        if (!_.isEmpty(relationships)) {
            this.relationshipStack.push(relationships);
        }
        this.relationshipModuleStack.push(module);

        this.secondLastRelationshipStackIndex = this.relationshipStack.length > 1 ?
            this.relationshipStack.length - 2 : 0;
        this.context.set('currentModule', module);
        this.render();
    },

    /**
     * Gets the relationship fields for a module
     *
     * @param {string} module
     * @return {Array}
     */
    filterRelationships: function(module) {
        if (!module) {
            return [];
        }

        const moduleMeta = app.metadata.getModule(module) || {};
        if (!moduleMeta.fields) {
            return [];
        }

        let filteredRels = _.filter(moduleMeta.fields, function(field) {
            return field.type === 'link' && field.link_type !== 'one';
        });

        // make sure the relationship has a module so it can translate labels
        return _.map(filteredRels, function(relationship) {
            if (!app.lang.getModString(relationship.vname, relationship.module)) {
                relationship.module = module;
            }
            return relationship;
        });
    },

    /**
     * Clear the select relationship form stack
     *
     * @param {Event} evt
     */
    removeRelationship: function(evt) {
        if (!this.currentRelationshipsModule) {
            return;
        }
        const stackIndex = evt.target.getAttribute('stack-index');
        this.removeFromRelationshipStack(parseInt(stackIndex));

        if (this.relationshipModuleStack.length > 1) {
            this.relationshipModuleStack.pop();
            this.currentRelationshipsModule = [...this.relationshipModuleStack].pop();
        } else {
            //there's only one module in the stack
            this.currentRelationshipsModule = this.relationshipModuleStack[0];
        }
        if (this.secondLastRelationshipStackIndex > 0) {
            this.secondLastRelationshipStackIndex--;
        }
        this.context.set('currentModule', this.currentRelationshipsModule);

        this.render();
    },

    /**
     * When removing from the stack,
     * we need to make sure there is at least one remaining relationship
     *
     * @param {number} stackIndex
     */
    removeFromRelationshipStack: function(stackIndex) {
        if (this.relationshipStack.length > 1) {
            this.relationshipStack.splice(stackIndex + 1, 1);
            this.resetRelationshipsSelected();
            this.currentRelationshipsModule = [...this.relationshipModuleStack].pop();
        } else {
            this.resetRelationshipsSelected();
            this.currentRelationshipsModule = null;
        }
    },

    /**
     * reset the selected variable on the last relationship inside the stack
     */
    resetRelationshipsSelected: function() {
        length = this.relationshipStack.length;
        for (let relationship of this.relationshipStack[length - 1]) {
            relationship.selected = false;
        }
    },

    /**
     * Trigger collection options
     *
     * @param {Event} evt
     */
    showCollectionOptions: function(evt) {
        if (!this.currentRelationshipsModule) {
            return;
        }
        const stackIndex = evt.target.getAttribute('stack-index');
        const currentRelationships = this.relationshipStack[stackIndex];

        let selectedRelationship = _.filter(currentRelationships, function(item) {
            return item.selected === true;
        })[0];

        let currentRelationshipsModule = this.relationshipModuleStack[stackIndex];
        selectedRelationship.currentModule = currentRelationshipsModule;

        if (selectedRelationship) {
            this.context.trigger('tag-builder-options:show', selectedRelationship);
        }
    },

    /**
     * Whenever the module is changed we need to reset
     * all relationship information.
     *
     * @param {string} module
     */
    _resetRelationships: function(module) {
        this.relationshipStack = [];
        this.relationshipModuleStack = [module];
        this.currentRelationshipsModule = module;
    },
});
