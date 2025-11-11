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
 * @class View.Fields.Base.CJForms.RelationshipField
 * @alias SUGAR.App.view.fields.BaseCJFormsRelationshipField
 * @extends View.Fields.Base.BaseField
 */
({
    /**
     * Relationship FieldTemplate (base)
     */
    extendsFrom: 'BaseField',

    /**
     * @inheritdoc
     */
    plugins: ['JSTree', 'NestedSetCollection'],

    /**
     * Class for Tree Div
     */
    divClass: 'cj-js-tree',

    /**
     * The JS Tree Object
     */
    jsTree: {},

    /**
     * Current root ID
     */
    currentRootId: '',

    /**
     * Parent root ID
     */
    parentRootId: '',

    /**
     * JSTree callbacks.
     * @property {Object} jsTreeCallbacks
     */
    jsTreeCallbacks: null,

    /**
     * Last(Leaf) Node ID
     */
    lastNodeId: null,

    /**
     * Last(Leaf) Node Level Number
     */
    lastLevel: null,

    /**
     * Constant Module name mapping on the basis of its template
     */
    MODULE_MAPPING: {
        DRI_Workflow_Templates: 'DRI_Workflows',
        DRI_SubWorkflow_Templates: 'DRI_SubWorkflows',
    },

    /**
     * Events to be triggered
     * @property {Object} events
     */
    events: {
        'change .relationship-select': 'changeRelationship',
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        if (!_.isUndefined(this.model)) {
            this._changeParentId();
            this.listenTo(this.model, 'change:parent_id', this._changeParentId);
            this.listenTo(this.model, 'change:activity_module', this.changeActivityModule);
        }
    },

    /**
     * Change the parent Id
     */
    _changeParentId: function() {
        let parentModule = this.model.get('parent_type');
        let activityModule = this.MODULE_MAPPING[parentModule];
        if (!_.isUndefined(activityModule)) {
            this.model.set('activity_module', activityModule);
            this.render();
        } else {
            let parentID = this.model.get('parent_id');
            let parentBean = app.data.createBean(parentModule, {id: parentID});
            parentBean.fetch({
                success: _.bind(function(data) {
                    this.model.set('activity_module', data.attributes.activity_type);
                    this.render();
                }, this)
            });
        }
    },

    /**
     * Change the relationship
     *
     * @param {event} event
     */
    changeRelationship: function(event) {
        let value = event.currentTarget.value;
        let index = $(event.currentTarget).data('index');
        let relationship = this.model.get('relationship') || [];
        let currentValue = relationship[index].relationship;
        let module = relationship[index].module;
        relationship[index].relationship = value;
        let bean = app.data.createBean(module);

        if (value === 'self') {
            relationship = relationship.slice(0, index + 1);
        } else if (currentValue !== value) {
            let def = bean.fields[value];
            let rel = app.metadata.getRelationship(def.relationship);
            if (relationship.length !== index + 1) {
                relationship = relationship.slice(0, index + 1);
            }
            relationship.push({
                module: rel.lhs_module !== module ? rel.lhs_module : rel.rhs_module,
                relationship: 'self',
                filters: [],
            });
        }
        this.model.unset(this.name, {silent: true}).set(this.name, relationship);
        this.render();
        let field = this.view.getField('populate_fields');
        if (field) {
            field.render();
        }
    },

    /**
     * Change the activity module
     *
     * @return {undefined}
     */
    changeActivityModule: function() {
        let parentModule = this.model.get('parent_type');
        let activityModule = this.model.get('activity_module');
        let relationship = this.model.get('relationship') || [];

        // Either Stage Template is selected Or it is calling on page load
        if (_.isEmpty(activityModule)) {
            activityModule = 'DRI_SubWorkflows';
        }
        if (!_.isUndefined(this.MODULE_MAPPING[parentModule])) {
            activityModule = this.MODULE_MAPPING[parentModule];
        }
        if (!relationship || (relationship.length && _.first(relationship).module === activityModule)) {
            return;
        }
        if (activityModule) {
            this.model.set('relationship', [
                {
                    module: activityModule,
                    relationship: 'self',
                    filters: [],
                },
            ]);
        } else {
            this.model.set('relationship', []);
        }
        this.render();
        let field = this.view.getField('populate_fields');
        if (field) {
            field.render();
        }
    },

    /**
     * Get the relationship option label
     *
     * @param {Object} def
     * @param {string} module
     * @return {string}
     */
    getRelationOptionLabel: function(def, module) {
        return `${app.lang.get(def.vname, module)} (${def.name})`;
    },

    /**
     * Get relationships for the module
     *
     * @param {string} module
     * @return {Object|undefined}
     */
    getRelationshipsForModule: function(module) {
        let options = {
            self: `self (${app.lang.get('LBL_MODULE_NAME', module)})`,
        };
        let fieldNames = [
            'created_by_link',
            'modified_user_link',
            'activities',
            'activities_users',
            'activities_teams',
            'comments',
            'commentlog_link',
            'currencies',
            'locked_fields_link',
            'following_link',
            'favorite_link',
            'tag_link',
            'teams',
            'team_link',
            'team_count_link',
            'email_attachment_for',
            'assigned_user_link',
            'current_cj_activity_at',
            'current_activity_call',
            'current_activity_meeting',
            'current_activity_task',
            'current_stage_at',
            'current_stage_link',
            'dri_workflow_template_link',
            'dri_subworkflow_template_link',
            'cj_activity_tpl_link',
        ];
        let bean = app.data.createBean(module);
        _.each(
            bean.fields,
            _.bind(function(def) {
                if (def.type !== 'link') {
                    return;
                }
                let rel = app.metadata.getRelationship(def.relationship);
                if (!rel) {
                    return;
                }
                let relModule = rel.lhs_module !== module ? rel.lhs_module : rel.rhs_module;
                let meta = app.metadata.getModule(relModule);
                if (
                    def.vname && meta && !meta.isBwcEnabled &&
                    relModule !== 'ForecastWorksheets' &&
                    def.name && !_.includes(fieldNames, def.name)
                ) {
                    options[def.name] = this.getRelationOptionLabel(def, module);
                }
            }, this)
        );
        return options;
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        let relationship = this.model.get('relationship') || [];

        this.level = 1;
        if (!_.isEmpty(_.first(relationship))) {
            this.currentRootId = this._getTreeNodeID(_.first(relationship).module);
        }
        this.values = _.map(relationship, _.bind(this.loadRelationshipsForModule, this, relationship));
        this.lastNodeId = this.currentRootId;
        this.lastLevel = this.level;

        let index = 0;
        _.each(this.values, function(value) {
            if (this.$el.find(`.${this.divClass}`)) {
                if (this._getDivForTreeLoad().children('ul').length >= 1) {
                    this._getDivForTreeLoad().find('.cj-rel-last').append(this.getLiTemplate(value, index));
                } else {
                    this._getDivForTreeLoad().append(this.getLiTemplate(value, index));
                }
                index++;
            }
        }, this);

        if (!_.isEmpty(this.values) && !((this.values).length === 0) &&
                !_.isEmpty(relationship) && !((relationship).length === 0)) {
            this.createRelationshipTree();
        }
    },

    /**
     * Provide relationships and its attributes for the module
     *
     * @param {Array}
     * @param {Object} rel
     * @param {number} index
     * @return {Object}
     */
    loadRelationshipsForModule: function(relationship, rel, index) {
        let firstLevel = 1;
        let prev = relationship[index - 1];
        let curr = relationship[index];
        let title = '';

        if (!_.isUndefined(prev) && !_.isUndefined(curr)) {
            this.level++;
            let nodeId = this._getTreeNodeID(curr.module);
            this.currentRootId = nodeId;
            if (_.isEqual(this.level, firstLevel)) {
                this.parentRootId = nodeId;
            }
        }
        if (index === 0) {
            title = app.lang.get('LBL_MODULE_NAME_SINGULAR', rel.module);
        } else {
            let fieldDef = app.data.createBean(prev.module).fields[prev.relationship];
            title = this.getRelationOptionLabel(fieldDef, prev.module);
        }
        return _.extend({}, rel, {
            title: title,
            options: this.getRelationshipsForModule(rel.module),
            level: this.level,
            nodeId: this.currentRootId,
        });
    },

    /**
     * Return the tree node id
     *
     * @param {string} module
     * @return {string}
     */
    _getTreeNodeID: function(module) {
        return `cj_rel_jstree_node_${module}_${this.level}`;
    },

    /**
     * Return the div for tree loading in view
     *
     * @return {Object}
     */
    _getDivForTreeLoad: function() {
        return this.$(`.${this.divClass}`);
    },

    /**
     * It will return the list of all the nodes which are in the tree
     *
     * @param {string} value
     * @param {string} index
     * @return {string}
     */
    getLiTemplate: function(value, index) {
        if (this._getDivForTreeLoad().find('li').length >= 1) {
            this._getDivForTreeLoad().find('li').removeClass('cj-rel-last');
        }
        let attr = `data-level="${value.level}" data-id="${value.nodeId}" id="${value.nodeId}" class="cj-rel-last"`;
        let options = this.getOptions(value.title, value.options, index);
        return `<ul><li ${attr}>${options}</li></ul>`;
    },

    /**
     * Get the dropdown option list in object form and convert it in an array with
     * the self relationship at the top
     *
     * @param {Object} optionsObject
     * @return {Array}
     */
    getSortedArrayFromRelationshipObject: function(optionsObject) {
        let optionListArray = [];
        _.each(optionsObject, function(option, key) {
            optionListArray.push({
                'key': key,
                'val': option,
            });
        }, this);

        //Sort Array
        optionListArray = optionListArray.sort(function(a, b) {
            if (a.val < b.val) {
                return -1;
            } else if (a.val > b.val) {
                return 1;
            } else {
                return 0;
            }
        });

        let listWithProperFormat = [];
        let selfRelationship;
        _.each(optionListArray, function(val) {
            if (val.key === 'self') { // remove self relationship
                selfRelationship = val;
            } else {
                listWithProperFormat.push(val);
            }
        }, this);

        listWithProperFormat.unshift(selfRelationship);
        return listWithProperFormat;
    },

    /**
     * It will return the dropdown options with the selection of option matches
     * with the relationship
     *
     * @param {string} title
     * @param {Array} optionList
     * @param {string} index
     * @return {string}
     */
    getOptions: function(title, optionList, index) {
        if (this.tplName !== 'edit') {
            return `<p style="display: contents;">${title}</p>`;
        }
        let relDropdown = `<select data-index = "${index}" class="relationship-select">`;
        let dropdownList = this.getSortedArrayFromRelationshipObject(optionList);
        _.each(dropdownList, function(value) {
            relDropdown = `${relDropdown}<option value = "${value.key}">${value.val}</option>`;
        }, this);
        return `${relDropdown}</select>`;
    },

    /**
     * Create tree for the relationship
     */
    createRelationshipTree: function() {
        this.jsTreeCallbacks = {
            onLoad: _.bind(this.loadJSTreeState, this),
        };
        this.jsTree = this._getDivForTreeLoad().jstree()
                .on('loaded.jstree', _.bind(function(e, data) {
                    this._loadedHandler(this._getDivForTreeLoad());
                    this._getDivForTreeLoad().jstree('open_all');
                }, this))
                .on('select_node.jstree', _.bind(function(event, data) {
                    this.relSelectNode(data.rslt.obj.data('id'));
                }, this));
    },

    /**
     * On selection of a node open/close the node according to the current state
     *
     * @param {type} id
     */
    relSelectNode: function(id) {
        let node = this.jsTree.find(`[data-id=${id}]`);
        this.jsTree.jstree('select_node', node);
        node.addClass('jstree-clicked');

        if (node.hasClass('jstree-open')) {
            this.jsTree.jstree('close_node', node);
            node.removeClass('jstree-open');
            node.addClass('jstree-closed');
        } else if (node.hasClass('jstree-closed')) {
            this.jsTree.jstree('open_node', node);
            node.removeClass('jstree-closed');
            node.addClass('jstree-open');
        }
    },

    /**
     * Overriding _dispose to make sure custom added jsTree listener is removed
     *
     * @private
     */
    _dispose: function() {
        if (!_.isEmpty(this.jsTree)) {
            this.jsTree.off();
        }
        this.stopListening(this.model);
        this._super('_dispose');
    },
});
