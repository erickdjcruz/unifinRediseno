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
 * Handlebars helpers.
 *
 * These functions are to be used in handlebars templates.
 * @class View.Handlebars.helpers
 * @singleton
 */
(function(app) {
    app.events.on("app:init", function() {

        /**
         * Gets the letters used for the icons shown in various headers for
         * each module, based on the translated singular module name.
         *
         * This does not always match the name of the module in the model,
         * e.g. "Product" maps to "Quoted Line Item".
         *
         * If the module has an icon string defined, use it, otherwise
         * fallback to the module's translated name.
         *
         * If there are spaces in the name, (e.g. Revenue Line Items or
         * Product Catalog), it takes the initials from the first two words,
         * instead of the first two letters (e.g. RL and PC, instead of Re
         * and Pr).
         *
         * @param {string} module Module to which the icon belongs.
         */
        Handlebars.registerHelper('moduleIconLabel', function(module) {
            return app.lang.getModuleIconLabel(module);
        });

        /**
         * Handlebar helper to get the Tooltip used for the icons shown in various headers for each module, based on the
         * translated singular module name.  This does not always match the name of the module in the model,
         * i. e. Product == Revenue Line Item
         * @param {String} module to which the icon belongs
         */
        Handlebars.registerHelper('moduleIconToolTip', function(module) {
            return app.lang.getModuleName(module);
        });

        /**
         * Handlebar helper to translate any dropdown values to have the appropriate labels
         * @param {String} value The value to be translated.
         * @param {String} key The dropdown list name.
         */
        Handlebars.registerHelper('getDDLabel', function(value, key) {
            return app.lang.getAppListStrings(key)[value] || value;
        });

        /**
         * Handlebar helper to retrieve a view template as a sub template
         * @param {String} key Key for the template to retrieve.
         * @param {Object} data Data to pass into the compiled template
         * @param {Object} options (optional) Optional parameters
         * @return {String} String Template
         */
        Handlebars.registerHelper('subViewTemplate', function(key, data, options) {
            var frame, template;

            template = app.template.getView(key, options.hash.module);

            // merge the hash variables into the frame so they can be added as
            // private @variables via the data option below
            frame = _.extend(Handlebars.createFrame(options.data || {}), options.hash);

            return template ? template(data, {data: frame}) : '';
        });

        /**
         * Handlebar helper to retrieve a field template as a sub template
         * @param {String} fieldName determines which field to use.
         * @param {String} view determines which template within the field to use.
         * @param {Object} data Data to pass into the compiled template
         * @param {Object} options (optional) Optional parameters
         * @return {String} String Template
         */
        Handlebars.registerHelper('subFieldTemplate', function(fieldName, view, data, options) {
            var frame, template;

            template = app.template.getField(fieldName, view, options.hash.module);

            // merge the hash variables into the frame so they can be added as
            // private @variables via the data option below
            frame = _.extend(Handlebars.createFrame(options.data || {}), options.hash);

            return template ? template(data, {data: frame}) : '';
        });

        /**
         * Handlebar helper to retrieve a layout template as a sub template
         * @param {String} key Key for the template to retrieve.
         * @param {Object} data Data to pass into the compiled template
         * @param {Object} options (optional) Optional parameters
         * @return {String} String Template
         */
        Handlebars.registerHelper('subLayoutTemplate', function(key, data, options) {
            var frame, template;

            template = app.template.getLayout(key, options.hash.module);

            // merge the hash variables into the frame so they can be added as
            // private @variables via the data option below
            frame = _.extend(Handlebars.createFrame(options.data || {}), options.hash);

            return template ? template(data, {data: frame}) : '';
        });

        /**
         * @method buildUrl
         * Builds an URL based on hashes sent on handlebars helper.
         *
         * Example:
         * <pre><code>
         * {{buildUrl url="path/to/my-static-file.svg"}}
         * </code></pre>
         *
         * @see Utils.Utils#buildUrl to know how we are building the url.
         *
         * @param {Object} options
         *   The hashes being sent by handlebars helper. Currently requires
         *   `options.hash.url` until we extend this to be used for image
         *   fields.
         * @return {String}
         *   The safely built url.
         */
        Handlebars.registerHelper('buildUrl', function(options) {
            return new Handlebars.SafeString(app.utils.buildUrl(options.hash.url));
        });

        /**
         * @method loading
         * Display animated loading message.
         *
         * To display loading message with default markup:
         *
         *     {{loading 'LBL_ALERT_TITLE_LOADING' }}
         *
         * You can also apply specific css classes:
         *
         *     // this will add the class `someCssClass` to `div.loading`.
         *     {{loading 'LBL_ALERT_TITLE_LOADING' cssClass='someCssClass'}}
         *
         * @param {Object} [options] Optional params.
         * @param {Object} [options.hash] The hash of the optional params.
         * @param {string} [options.hash.cssClass] A space-separated list of
         *   classes to apply to `div.loading`.
         */
        Handlebars.registerHelper('loading', function(str, options) {
            str = app.lang.get(str);
            var cssClass = ['loading'];
            if (_.isString(options.hash.cssClass)) {
                cssClass = _.unique(cssClass.concat(
                    Handlebars.Utils.escapeExpression(options.hash.cssClass).split(' ')
                ));
            }
            return new Handlebars.SafeString(
                '<div class="' + cssClass.join(' ') + '">'
                + Handlebars.Utils.escapeExpression(str)
                + '<i class="l1">&#46;</i><i class="l2">&#46;</i><i class="l3">&#46;</i>'
                + '</div>'
            );
        });

        Handlebars.registerHelper('decoratedField', function(type, view, options) {
            var def = {
                type: type,
                field: this
            };

            var field = app.view.createField({
                type: type,
                def: def,
                viewDefs: def,
                view: view,
                model: options.hash.model,
                viewName: options.hash.template,
            });

            if (options.hash.parent && _.isArray(options.hash.parent.fields)) {
                options.hash.parent.fields.push(field);
            }

            return field.getPlaceholder();
        });

        Handlebars.registerHelper('timeAgo', function(str, options) {
            return moment ? moment.utc(str).fromNow() : str;
        });

        /**
         * Helper implements a boolean operation "OR"
         * @param Variable number of parameters
         * @return {boolean}
         */
        Handlebars.registerHelper('or', function() {
            for (let i = 0; i < arguments.length - 1; i++) {
                if (!!arguments[i]) {
                    return true;
                }
            }

            return false;
        });

        /**
         * Helper implements a utility that allows a block of template to be run a given number of times
         * @param n number of times the block should run
         * Usage:
         *
         *   {{#times <number>}}
         *      ...
         *   {{/times}}
         *
         * Example:
         *   {{#times count>}}
         *      ...
         *   {{/times}}
         * @return {string} Result of the `block` being executed n times
         */
        Handlebars.registerHelper('times', function(n, block) {
            let accum = '';
            for (let i = 0; i < n; i++) {
                accum += block.fn(i);
            }
            return accum;
        });

        /**
         * Helpers implements a utility that allows different versions of icons to be rendered.
         * @param iconClass defined icon class passed in through metadata
         * @return {string} iconClass with its based font class
         * Usage:
         *
         *   {{buildIcon <icon-class>}}
         *
         * Example:
         *
         *   {{buildIcon 'sicon-plus'}} returns 'sicon sicon-plus'
         */
        Handlebars.registerHelper('buildIcon', function(iconClass) {
            return iconClass.includes('sicon') ?
                `sicon ${iconClass}` :
                `fa ${iconClass}`;
        });

        /**
         * Helper that returns the HTML to display a module label. Takes in the
         * module name and the size of the label to create, but also supports
         * any number of optional HTML attributes to apply to the element.
         * Label color and contents (either text or a SugarIcon) are
         * automatically added based on the stored module settings in metadata.
         *
         * Usage:
         *
         * {{moduleLabel <module> <size> <attr>="<value>"...}}
         *
         * Example:
         *
         * {{moduleLabel 'Accounts' 'lg' rel="tooltip" data-bs-placement="right" title="tooltip"}}
         *
         * When the Accounts color is set to "green" and is set to show by
         * the abbreviation "Ac", the above returns:
         *
         * <span class="label-module label-module-size-lg
         *     label-module-color-green" rel="tooltip"
         *     data-bs-placement="right" title="tooltip">
         *     Ac
         * </span>
         *
         * @param {string} module The name of the module
         * @param {string} size The size of the label to create ('sm' or 'lg'), default is 'sm'
         * @param {Object} options The optional params, supporting any key/value HTML attribute pairs
         */
        Handlebars.registerHelper('moduleLabel', function(module, size, options) {
            let contents = '';
            let attributes = _.clone(options.hash) || {};
            attributes.class = attributes.class ? `${attributes.class} label label-module` : 'label label-module';

            // Determine the size based on the passed in option
            size = _.contains(['sm', 'lg'], size) ? size : 'sm';
            attributes.class += ` label-module-size-${size}`;

            // Determine the color and contents based on module metadata
            let moduleMeta = app.metadata.getModule(module) || {};

            let color = _.has(attributes, 'color') ? attributes.color : moduleMeta.color || 'ocean';
            attributes.class += ` label-module-color-${color}`;

            if (moduleMeta.display_type === 'abbreviation') {
                contents = app.lang.getModuleIconLabel(module);
            } else {
                let icon = moduleMeta.icon || 'sicon-default-module-lg';
                attributes.class += ` sicon ${icon}`;
            }

            // Build the attributes string and return the element HTML
            attributes = _.reduce(attributes, function(string, value, key) {
                return `${string} ${key}="${value}"`;
            }, '');

            return new Handlebars.SafeString(
                `<span ${attributes.trim()}>${contents.trim()}</span>`
            );
        });

        /**
         * Helper that returns the HTML to display a tooltip.
         * Bootstrap v5 doesn't support different instances for one element.
         * For example, it's impossible to use dropdown and tooltip for the same <a> tag.
         *
         * Usage:
         *
         * {{tooltipTemplate <title> <placement>}}
         *
         * Examples:
         *
         * {{tooltipTemplate (str 'LBL_TEST_STRING' 'Accounts') 'bottom'}}
         * Placement is 'top' by default in Bootstrap:
         * {{tooltipTemplate (str 'LBL_TEST_STRING')}}
         *
         * Example of generated template:
         *
         * <span class="absolute h-full w-full top-0 left-0" rel="tooltip"
         *        data-bs-placement="bottom" title="Test String"></span>
         *
         * @param {string} title The title
         * @param {string} placement The placement
         */
        Handlebars.registerHelper('tooltipTemplate', function(title, placement) {
            let html = `<span class="absolute h-full w-full top-0 left-0" rel="tooltip" `;

            if (placement && _.isString(placement)) {
                html += `data-bs-placement="${placement}" `;
            }

            html += `data-original-title="${title}"></span>`;
            return new Handlebars.SafeString(html);
        });

        /**
         * Helper that returns the class list needed to build the container for
         * a module label. The classes will create the container with the given
         * size, and the color scheme of the given module.
         *
         * Usage:
         *
         * {{moduleLabelContainer 'Accounts' 'lg'}}
         *
         * When the Accounts color is set to "green", the above returns:
         *
         * label-module label-module-size-lg label-module-color-green
         *
         * @param {string} module The name of the module
         * @param {string} size The size of the label to create ('sm' or 'lg'), default is 'sm'
         */
        Handlebars.registerHelper('moduleLabelContainer', function(module, size) {
            let moduleMeta = app.metadata.getModule(module) || {};
            let color = moduleMeta.color || 'ocean';
            size = _.contains(['sm', 'lg'], size) ? size : 'sm';
            return `label label-module label-module-size-${size} label-module-color-${color}`;
        });

        /**
         * Creates a field widget.
         *
         * Example:
         * ```
         * {{reportField view model=mymodel index=1 template=edit parent=fieldset}}
         * ```
         *
         * @param {View} view Parent view
         * @param {Object} [options] Optional params to pass to the field.
         * @param {Backbone.Model} [options.model] The model associated with the field.
         * @param {string} [options.template] The name of the template to be used.
         * @param {Field} [options.parent] The parent field of this field.
         * @return {Handlebars.SafeString} HTML placeholder for the widget.
         */
        Handlebars.registerHelper('reportField', function(view, options) {
            const parentModel = options.hash.model;
            const index = options.hash.index;
            const html = options.hash.html;
            const model = parentModel.get(index);

            // we need to dereference this, because we might change its type
            let self = this;

            // do try-catch because JSON.parse might throw error on some poorly formatted strings
            try {
                self = app.utils.deepCopy(this);
            } catch (e) {
                // we don't need to do anything on error catch
                // it will use the first init of currentContext
            }

            // change type to text when we encounter a relate field without id
            if (_.isEmpty(model.get('id')) && self.link) {
                self.type = 'text';
                self.link = false;
            }

            if (self.type === 'currency') {
                // do not show transactional values on reports
                self.showTransactionalAmount = false;
                self.skip_preferred_conversion = true;
            }

            const field = SUGAR.App.view.createField({
                def: self,
                viewDefs: self,
                view: view,
                model: model,
                viewName: options.hash.template
            });

            if (options.hash.parent && _.isArray(options.hash.parent.fields)) {
                options.hash.parent.fields.push(field);
            }

            if (field.type === 'htmleditable_tinymce') {
                field.render();

                return new Handlebars.SafeString(field.$el.html());
            }

            if (html) {
                if (field.type === 'enum') {
                    if (_.has(self, 'options')) {
                        field.items = app.lang.getAppListStrings(self.options);
                    } else if (_.has(self, 'function') && !_.isUndefined(view.data.functionOptions) &&
                        !_.isUndefined(view.data.functionOptions[self.function])) {
                        field.items = view.data.functionOptions[self.function];
                    }
                }
                field.render();

                if (field.type === 'image' && field.value) {
                    field.resizeWidget();
                }

                return field.$el.html();
            }

            return field.getPlaceholder();
        });

        /**
         * Helper that returns true if a value is found inside an object false otherwise
         *
         * Usage:
         *
         * {{includes myObject 'myValue'}}
         *
         * @param {Object} haystack The object which we search in
         * @param {string} needle The value we're searching for
         */
        Handlebars.registerHelper('includes', function(haystack, needle, options) {
            if (haystack && (_.has(haystack, needle) ||
                (_.isString(haystack) && _.isString(needle) &&
                    haystack.toLowerCase().includes(needle.toLowerCase())) ||
                (_.isArray(haystack) && _.isString(needle) && haystack.includes(needle))
                )) {
                return options.fn(this); // Key exists, execute the content within the block
            } else {
                return options.inverse(this); // Key does not exist, execute the else block
            }
        });

        /**
         * Sanitize user-controlled HTML
         *
         * Usage:
         * {{#sanitize}}
         *     {{{value}}}
         * {{/sanitize}}
         *
         * @return {Handlebars.SafeString} sanitized HTML.
         */
        Handlebars.registerHelper('sanitize', function(options) {
            return new Handlebars.SafeString(DOMPurify.sanitize(options.fn(this), {ADD_ATTR: ['target']}));
        });
    });
})(SUGAR.App);
