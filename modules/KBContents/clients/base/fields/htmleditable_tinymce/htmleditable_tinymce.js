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
    extendsFrom: 'Htmleditable_tinymceField',

    /**
     * Flag indicates, should we disable field.
     * @property {boolean}
     */
    shouldDisable: null,

    /**
     * The defined iframe height, in pixels. If no height is defined,
     * use the default height for iframes (150px)
     */
    defaultIframeHeight: 150,

    /**
     * KB specific parameters.
     * @private
     */
    _tinyMCEConfig: {
        'height': '300',
    },

    /**
     * @inheritdoc
     * Additional override fieldSelector property from field's meta.
     */
    initialize: function(opts) {
        if (opts.view.action === 'filter-rows') {
            opts.viewName = 'filter-rows-edit';
        }
        this._super('initialize', [opts]);
        this.shouldDisable = false;
        this.resizeWindowHandler =  _.debounce(_.bind(this.adjustBodyHeight, this), 100);
        window.addEventListener('resize', this.resizeWindowHandler);
        if (!_.isUndefined(this.def.fieldSelector)) {
            this.fieldSelector = '[data-htmleditable=' + this.def.fieldSelector + ']';
        }
        this.before('render', function() {
            if (this.shouldDisable != this.isDisabled()) {
                this.setDisabled(this.shouldDisable);
                return false;
            }
        }, this);
    },

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');

        // non-editor view
        if (this.tplName === 'detail') {
            this.defaultIframeHeight = this._getHtmlEditableField().height();
        }
    },

    /**
     * Gets the iframe body element and updates its height based on window size
     */
    adjustBodyHeight: function() {
        var $iframeElement = this._getHtmlEditableField();

        // adjust the KB body height once the iframe is ready
        $iframeElement.ready(_.bind(this.updateBodyHeight, this, $iframeElement));
    },

    /**
     * @inheritdoc
     *
     * Apply inline css style to iframe elements in detail view.
     */
    setViewContent: function(value) {
        if (!_.isEmpty(value)) {
            var elemArr = $.parseHTML(value) || [];
            if (elemArr.length > 0) {
                var firstElem = $(elemArr[0]);
                // This makes sure that the first element is aligned with the label
                firstElem.css('font-size', '14px');
                firstElem.css('margin-top', '7.5px');
                elemArr[0] = firstElem[0];
                // clear the value string before assigning it modified value
                value = '';

                value += '<div class="kbdocument-body">';
                // iterate over each element
                _.each(elemArr, function(elem) {
                    // append the outerHTML of each element to recreate value string
                    value += elem.outerHTML;
                });
                value += '</div>';
            }
        }
        this._super('setViewContent', [value]);

        this.adjustBodyHeight();
    },

    /**
     * @inheritdoc
     *
     * Apply document css style to editor.
     */
    getTinyMCEConfig: function() {
        var config = this._super('getTinyMCEConfig'),
            content_css = [];

        // To open a link in the same window we need to use _top instead of _self as target
        _.each(config.link_target_list, function(target) {
            if (target.text === app.lang.getAppString('LBL_TINYMCE_TARGET_SAME')) {
                target.value = '_top';
            }
        }, this);

        config = _.extend(config, this._tinyMCEConfig);

        return config;
    },

    /**
     * @inheritdoc
     * Need to strip tags for list and activity stream.
     */
    format: function(value) {
        var result;
        switch (this.view.tplName) {
            case 'audit':
            case 'list':
            case 'activitystream':
                result = this.stripTags(value);
                break;
            default:
                result = this._super('format', [value]);
                break;
        }
        return result;
    },

    /**
     * Strip HTML tags from text.
     * @param {string} value Value to strip tags from.
     * @return {string} Plain text.
     */
    stripTags: function(value) {
        var $el = $('<div/>').html(value),
            texts = $el.contents()
            .map(function() {
                if (this.nodeType === 1 && this.nodeName != 'STYLE' && this.nodeName != 'SCRIPT') {
                    return this.textContent.replace(/ +?\r?\n/g, ' ').trim();
                }
                if (this.nodeType === 3) {
                    return this.textContent.replace(/ +?\r?\n/g, ' ').trim();
                }
            });
        return _.filter(texts, function(value) {
            return (value.length > 0);
        }).join(' ');
    },

    /**
     * @inheritdoc
     * Should check, if field should be disabled while mode change.
     */
    setMode: function(mode) {
        this.shouldDisable = (mode === 'edit' &&
            (this.view.tplName === 'list' ||
            (this.view.tplName == 'flex-list' && (this.tplName == 'subpanel-list' || this.tplName == 'list'))
            )
        );
        this._super('setMode', [mode]);
    },

    /**
     * We are trying to get HTML content instead of raw one because
     * when editor initialized it already contains some HTML (blank <p> or <br> tags).
     * In this case it will be considered as non-empty value for this field even if we don't enter anything.
     * It comes from ticket RS-1072.
     *
     * @override
     * @inheritdoc
     */
    getEditorContent: function() {
        // We can't use getContent({format: 'html'}) due to this issue https://github.com/tinymce/tinymce/issues/794
        // That's why we save HTML Editor content to HTML Field and get content directly from HTML field.
        this._htmleditor.save();
        var text = this._getHtmlEditableField().html();
        //We don't need to get empty html, to prevent model changes.
        if (text !== '') {
            text = this._super('getEditorContent');
        }
        return text;
    },

    /**
     * @inheritdoc
     */
    setViewName: function ()
    {
        this.destroyTinyMCEEditor();
        this._super('setViewName', arguments);
    },

    /**
     * Update the height of the KB body, up to maxBodyHeight
     *
     * @param $element the jQuery element
     */
    updateBodyHeight: function($element) {
        var windowHeight = $(window).height();
        this.maxBodyHeight = 6 * windowHeight / 10;
        var contentHeight = this._getContentHeight();

        // do nothing if the content height is less than the default iframe height
        if (contentHeight < this.defaultIframeHeight) {
            return;
        }

        // add padding to account for bottom margins/padding
        contentHeight += 20;

        if (contentHeight < this.maxBodyHeight) {
            $element.height(contentHeight);
        } else {
            $element.height(this.maxBodyHeight);
        }
    },

    /**
     * @inheritdoc
     *
     * Adds a button for selecting and applying a template.
     */
    addCustomButtons: function(editor) {
        // if the user has access to KB Templates then add the template button
        if (app.acl.hasAccess('view', 'KBContentTemplates')) {
            editor.ui.registry.addButton('kbtemplate', {
                tooltip: app.lang.get('LBL_TEMPLATE', this.module),
                icon: 'document-properties',
                name: 'template',
                onAction: () => {
                    this.context.trigger('button:add-template:click');
                },
            });
        }
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        window.removeEventListener('resize', this.resizeWindowHandler);
        this._super('_dispose');
    }
})
