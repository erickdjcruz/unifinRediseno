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
 * @class View.Fields.Base.TextareaField
 * @alias SUGAR.App.view.fields.BaseTextareaField
 * @extends View.Fields.Base.BaseField
 */
({
    /**
     * @inheritdoc
     */
    fieldTag : 'textarea',

    /**
     * Default settings used when none are supplied through metadata.
     *
     * Supported settings:
     * - {Number} max_display_chars The maximum number of characters to be
     *   displayed before truncating the field.
     * - {Boolean} collapsed Defines whether or not the textarea detail view
     *   should be collapsed on initial render.
     *
     *     // ...
     *     'settings' => array(
     *         'max_display_chars' => 50,
     *         'collapsed' => false
     *         //...
     *     ),
     *     //...
     *
     * @protected
     * @type {Object}
     */
    _defaultSettings: {
        max_display_chars: 450,
        collapsed: true
    },

    /**
     * State variable that keeps track of whether or not the textarea field
     * is collapsed in detail view.
     *
     * @type {Boolean}
     */
    collapsed: undefined,

    /**
     * Settings after applying metadata settings on top of
     * {@link View.Fields.BaseTextareaField#_defaultSettings default settings}.
     *
     * @protected
     */
    _settings: {},

    /**
     * @inheritdoc
     */
    events: {
        'click [data-action=toggle]': 'toggleCollapsed'
    },

    rowCount: null,

    /**
     * @inheritdoc
     *
     * Initializes settings on the field by calling
     * {@link View.Fields.BaseTextareaField#_initSettings _initSettings}.
     * Also sets {@link View.Fields.BaseTextareaField#collapsed collapsed}
     * to the value in `this._settings.collapsed` (either default or metadata).
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        this.plugins = _.union(this.plugins, 'Tooltip');
        this._initSettings();
        this.collapsed = this._settings.collapsed;
        this.rowCount = this.def.rows;
    },

    /**
     * Initialize settings, default settings are used when none are supplied
     * through metadata.
     *
     * @return {View.Fields.BaseTextareaField} Instance of this field.
     * @protected
     */
    _initSettings: function() {
        this._settings = _.extend({},
            this._defaultSettings,
            this.def && this.def.settings || {}
        );
        return this;
    },

    /**
     * @inheritdoc
     *
     * Prevents editing the textarea field in a list view.
     *
     * @param {String} name The mode to set the field to.
     */
    setMode: function(name) {
        // FIXME: This will be updated pending changes to fields in sidecar,
        // see SC-2608, SC-2776.
        // FIXME: Check on 'merge-duplicates' to identify editable fields
        // see SC-3325
        var isList = (this.tplName === 'list') && _.contains(['edit', 'disabled'], name),
            mode = isList && this.view.name !== 'merge-duplicates' ? this.tplName : name;
        this._super('setMode', [mode]);
    },

    /**
     * @inheritdoc
     *
     * Formatter that always returns the value set on the textarea field. Sets
     * a `short` value for a truncated representation, if the lenght of the
     * value on the field exceeds that of `max_display_chars`. The return value
     * can either be a string, or an object such as {long: 'abc'} or
     * {long: 'abc', short: 'ab'}, for example.
     * @param {String} value The value set on the textarea field.
     * @return {String|Object} The value set on the textarea field.
     */
    format: function(value) {
        // If the tplName is 'edit' then value needs to be a string. Otherwise
        // send back the object containing `value.long` and, if necessary,
        // `value.short`.
        var shortComment = value;
        var max = this._settings.max_display_chars;

        var valueObj = {
            long: this.getDescription(value, false),
            defaultValue: value,
            short: '',
        };

        var longValueOverMaxChars = valueObj.long.string.length > max;

        if (valueObj.long && longValueOverMaxChars) {
            valueObj.short = this.getDescription(shortComment, true);
        }

        if (this.tplName === 'edit') {
            // Based on the value to be used, format the textarea field.
            this.updateTextAreaDimensions(valueObj, longValueOverMaxChars);
            return value;
        }

        return valueObj;
    },

    /**
     * This method will set the rows attribute on the textarea, corresponding to whichever value is going to be
     * displayed, based on this.format().
     * @param {Object} value The value set on the textarea field.
     * @param {boolean} longValueOverMaxChars Whether or not the max string value is over the defined char limit.
     */
    updateTextAreaDimensions: function(value, longValueOverMaxChars) {
        var shortValueRowCount = 0;
        var longValueRowCount = 0;

        if (value.short && !_.isEmpty(value.short.string)) {
            shortValueRowCount = this._countRows(value.short, this.def.cols);
        }

        if (value.long && !_.isEmpty(value.long.string)) {
            longValueRowCount = this._countRows(value.long, this.def.cols);
        }

        var newRowCount = this.collapsed && longValueOverMaxChars ? shortValueRowCount : longValueRowCount;
        this.rowCount = newRowCount > this.def.rows ? newRowCount : this.def.rows;
    },

    /**
     * The calculation that's done here is to take the total length of the string and divide it against the
     * total number of columns. This will not produce a number of rows that will be perfect everytime but it is
     * goood for these purposes. A textarea's cols attribute is a relative measurement, based on the average size
     * of a character.
     */
    _countRows: function(value, colCount) {
        var splitValue = value.string.split('\n');
        return _.chain(splitValue)
            .map(function(s) {
                return Math.ceil(s.length / colCount);
            })
            .reduce(function(curr, prev) {
                return curr + prev;
            })
            .value();
    },

    /**
     * Displaying full or short descriptions.
     *
     * @param {string} description The value set on the textarea field.
     * @param {boolean} short Need a short value of the comment.
     * @return {string} The entry with html for any links.
     */
    getDescription: function(description, short) {
        short = !!short;
        description = Handlebars.Utils.escapeExpression(description);
        description = short ? this.getShortComment(description) : description;

        if (this.def.add_links !== false) {
            description = this.insertHtmlLinks(description);
        }

        return new Handlebars.SafeString(description);
    },

    /**
     * Replaces any text urls with html links
     * 1-th pattern - http://, https://, ftp://
     * 2-th pattern - www. sans http:// or https://
     * 3-th pattern - Email addresses
     *
     * @param {string} description The comment entry
     * @return {string} The entry with html for any links
     */
    insertHtmlLinks: function(description) {
        return description
            .replace(
                /((https?|ftp):\/\/[^\s]+)/g,
                '<a href="$&" target="_blank" rel="nofollow noopener noreferrer">$&</a>'
            )
            .replace(
                 /(^|[^\/])(www\.[\S]+(\b|$))/gim,
                 '$1<a href="http://$2" target="_blank" rel="nofollow noopener noreferrer">$2</a>'
            )
            .replace(
                  /[\w.]+@[a-zA-Z_-]+?(?:\.[a-zA-Z]{2,6})+/gim,
                  '<a href="mailto:$&">$&</a>'
            );
    },

    /**
     * Truncate the text area entry so it is shorter than the max_display_chars
     * Only truncate on full words to prevent ellipsis in the middle of words
     *
     * @param {string} description The comment log entry to truncate
     * @return {string} the shortened version of an entry if it was originally longer than max_display_chars
     */
    getShortComment: function(description) {
        if (!this.isLongSize(description)) {
            return description;
        }
        let shortDescription = description.substring(0, this._settings.max_display_chars);
        // let's cut at a full word by checking we are at a whitespace char
        while (!(/\s/.test(shortDescription[shortDescription.length - 1])) && shortDescription.length > 0) {
            shortDescription = shortDescription.substring(0, shortDescription.length - 1);
        }

        return shortDescription;
    },

    /**
     * To check size of description.
     *
     * @param {string} description The comment entry
     * @return {boolean}
     */
    isLongSize: function(description) {
        return description.length > this._settings.max_display_chars;
    },

    /**
     * Toggles the field between displaying the truncated `short` or `long`
     * value for the field, and toggles the label for the 'more/less' link.
     */
    toggleCollapsed: function() {
        this.collapsed = !this.collapsed;
        this.render();
    },

    /**
     * Overrides default implementation so that whitespaces won't be stripped.
     */
    unformat: function(value) {
        return value;
    }
})
