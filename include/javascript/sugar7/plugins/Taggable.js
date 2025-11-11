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
(function (app) {
    // This plugin depends on the Filters module being enabled.
    app.events.on("app:init", function () {
        let tagTextTemplate = Handlebars.compile('@[{{module}}:{{id}}:{{name}}]');
        let taggingHtml = '<span class="sugar_tagging">&nbsp;</span>';
        let tagListContainerHtml = '<ul class="activitystream-tag-dropdown dropdown-menu ml-0 text-xs w-full"></ul>';
        let mention = '@';
        let reference = '#';
        let keycodeAt = 64;
        let keycodeHash = 35;
        let keycodeEsc = 27;
        let keycodeEnter = 13;
        let keycodeTab = 9;
        let keycodeUp = 38;
        let keycodeDown = 40;
        let tagRegExp = /@\[([\w]+):([\d\w\-]+):(.+?)\]/g;
        let nbspRegExp = /&nbsp;/g;

        app.plugins.register('Taggable', ['view', 'field'], {
            events: {
                'keypress .taggable': '_onKeyPress',
                'keydown .taggable': '_onKeydown',
                'keyup .taggable': '_onKeyup',
                'mouseover .activitystream-tag-dropdown li': '_setListOptionAsActive',
                'click .activitystream-tag-dropdown li': '_insertTag'
            },

            taggableSearchAfter: 2, //search after entering this many characters
            taggableListLength: 8, //the number of search results that should be returned
            taggableCursorPosition: 0, // position of the cursor

            /**
             * Reset typeahead when user clicks anywhere outside the dropdown.
             *
             * @param {Component} component
             * @param {Plugin} plugin
             */
            onAttach: function(component, plugin) {
                this._searchForTags = _.debounce(this._searchForTags, 300);

                $(document).on('click.' + component.cid, function(event) {
                    var $target = $(event.target),
                        clickedOutsideDropdown = ($target.parents('.activitystream-tag-dropdown').length === 0),
                        clickedOutsideTaggingSpan = !$target.hasClass('sugar_tagging');

                    if (component._taggableEnabled && clickedOutsideDropdown && clickedOutsideTaggingSpan) {
                        component._resetTaggable();
                    }
                });

                this.useAvatarNamePills();
            },

            /**
             * Use avatar and text pills
             */
            useAvatarNamePills: function() {
                this.tagTemplate = Handlebars.compile(
                    '<span class="label label-module-color-{{color}} sugar_tag">' +
                    '<a href="#{{buildRoute module=module id=id}}">{{name}}</a></span>'
                );
                this.tagInEditTemplate = Handlebars.compile(
                    '<span>{{moduleLabel module "sm" class="mr-2" contenteditable="false"}}<a>{{name}}</a></span>'
                );
                this.tagListOptionTemplate = Handlebars.compile('<li{{#if noAccess}} class="disabled"{{/if}}>' +
                    '<a>{{moduleLabel module "sm" class="pull-left mr-2"}}{{{htmlName}}}{{#if noAccess}}' +
                    '<div class="add-on">{{str "LBL_NO_ACCESS_LOWER"}}</div>{{/if}}</a></li>');
            },

            /**
             * Use square pills
             */
            useSquarePills: function() {
                this.tagTemplate = Handlebars.compile(
                    '<span class="label label-{{module}} sugar_tag">' +
                    '<a href="#{{buildRoute module=module id=id}}">{{name}}</a></span>'
                );
                this.tagInEditTemplate = Handlebars.compile(
                    '<span class="label label-{{module}} sugar_tag" contenteditable="false">' +
                    '<a>{{name}}</a></span>'
                );
                this.tagListOptionTemplate = Handlebars.compile(
                    '<li{{#if noAccess}} class="disabled"{{/if}}><a>' +
                    '{{moduleLabel module "sm" class="pull-left mr-2 initial"}}{{{htmlName}}}{{#if noAccess}}' +
                    '<div class="add-on">{{str "LBL_NO_ACCESS_LOWER"}}</div>{{/if}}</a></li>'
                );
            },

            /**
             * Remove click event handler
             *
             * @param {Component} component
             * @param {Plugin} plugin
             */
            onDetach: function(component, plugin) {
                $(document).off('click.' + component.cid);
            },

            /**
             * Converts HTML tags to a text-based format so that it can be stored in the database.
             *
             * @param {JQuery} $input
             * @returns {Object}
             */
            unformatTags: function($input) {
                var text = '',
                    tags = [];

                $input.contents().each(function() {
                    var $node = $(this),
                        data = $node.data();

                    if (this.nodeType === Node.TEXT_NODE) {
                        text += this.nodeValue.replace(nbspRegExp, ' ');
                    } else if (data && data.module && data.id && data.name) {
                        text += tagTextTemplate(data);
                        tags.push(data);
                    } else {
                        text += $node.text();
                    }
                });

                return {
                    value: text.trim(),
                    tags: this._filterOutDuplicateTags(tags)
                };
            },

            /**
             * Converts a text-based tags into HTML format.
             *
             * @param {String} text
             * @returns {String}
             */
            formatTags: function(text) {
                var html = '';
                var labelNames = [
                    'LBL_VALUE_ERASED',
                    'LBL_NO_DATA_AVAILABLE_NO_PERIOD',
                ];

                if (text && (text.length > 0)) {
                    html = text.replace(tagRegExp, _.bind(function(str, module, id, name) {
                        // Support for Value Erased and No data available
                        if (_.contains(labelNames, name)) {
                            name = app.lang.get(name, module);
                        }

                        // The backend mangles special characters, so we must
                        // tell Handlebars that the string is safe.
                        name = new Handlebars.SafeString(name);

                        module = (module === 'Users') ? 'Employees' : module;

                        let moduleMeta = app.metadata.getModule(module);
                        return this.tagTemplate({module: module, id: id, name: name, color: moduleMeta.color});
                    }, this));
                }

                return html.trim();
            },

            /**
             * Specify which record this tag will be applied for record view.
             *
             * @param {String} module
             * @param {String} id
             */
            setTaggableRecord: function(module, id) {
                this._taggableModuleName = module;
                this._taggableModelId = id;
            },

            _taggableEnabled: false,
            _taggableModuleName: null,
            _taggableModelId: null,
            _taggableLastSearchTerm: null,
            _taggableListOpen: null,

            /**
             * Enable taggable typeahead when @ or # is pressed.
             *
             * @param keypress
             * @private
             */
            _onKeyPress: function(event) {
                this.taggableCursorPosition = this._getTaggableInput().prop('selectionStart');

                // When taggable is disabled
                if (!this._taggableEnabled) {
                    // enable taggable typeahead when @ or # is pressed
                    switch (event.which) {
                        case keycodeAt:
                        case keycodeHash:
                            this._enableTaggable();
                            break;
                    }
                }
            },

            /**
             * Listen to keydown events. Perform various actions depending upon what keys have been pressed in
             * varying states.
             *
             * @event keydown
             * @private
             */
            _onKeydown: function(event) {
                // When taggable is enabled but the tag search result list has not been opened...
                if (this._taggableEnabled && !this._taggableListOpen) {
                    switch (event.which) {
                        // reset typeahead when escape, enter, or tab is pressed
                        case keycodeEsc:
                        case keycodeEnter:
                        case keycodeTab:
                            event.preventDefault();
                            this._resetTaggable();
                            break;
                    }
                }

                // When taggable is enabled and the tag search result list is open...
                if (this._taggableEnabled && (this._taggableListOpen === true)) {
                    switch (event.which) {
                        // remove typeahead when escape key is pressed
                        case keycodeEsc:
                            event.preventDefault();
                            this._resetTaggable();
                            break;
                        // select the currently selected tag
                        case keycodeEnter:
                        case keycodeTab:
                            event.preventDefault();
                            this._getCurrentlyActiveOption().click();
                            break;
                        // select the option above the currently selected tag
                        case keycodeUp:
                            event.preventDefault();
                            this._selectNextListOption(false);
                            break;
                        // select the option below the currently selected tag
                        case keycodeDown:
                            event.preventDefault();
                            this._selectNextListOption(true);
                            break;
                    }
                }

                var isTextarea = this._isTextArea(this._getTaggableInput());
                // Reset taggable if we delete the @ or #
                if (this._taggableEnabled && isTextarea && this._willDeleteMention()) {
                    this._resetTaggable();
                }
            },

            /**
             * Checks if the char to be deleted will be a @ or #
             * This is only to be used by textarea fields
             * @return {boolean}
             * @private
             */
            _willDeleteMention: function() {
                var currentChar = this._getTaggableInput().val()[this.taggableCursorPosition];
                if (currentChar === '@' || currentChar === '#') {
                    return true;
                }
                return false;
            },

            /**
             * Listen to keyup events. Perform various actions depending upon what keys have been pressed in
             * varying states.
             *
             * @event keyup
             * @private
             */
            _onKeyup: function(event) {
                var selection = window.getSelection(),
                    range, $container, searchTerm;
                var $taggable = this._getTaggableInput();
                var isTextarea = this._isTextArea($taggable);

                if (this._taggableEnabled) {
                    // Do not perform search if enter, tab, up arrow, or down arrow has been pressed while tag search
                    // result is open.
                    let denyKeyCodes = [keycodeEnter, keycodeTab, keycodeUp, keycodeDown];
                    if (this._taggableListOpen && _.contains(denyKeyCodes, event.which)) {
                        return;
                    }

                    if (isTextarea) {
                        $container = $taggable;
                        searchTerm = this.getCurrentSearchTerm($container);
                    } else if (selection.rangeCount > 0) {
                        range = selection.getRangeAt(0);
                        $container = $(range.startContainer.parentNode);
                        searchTerm = $container.text();
                    } else {
                        return;
                    }

                    searchTerm = searchTerm.trim();

                    // Reset taggable if the cursor is outside the tagging span
                    if (!$container.hasClass('sugar_tagging') && !isTextarea) {
                        this._resetTaggable();
                    } else if (this._taggableListOpen && (searchTerm.length <= this.taggableSearchAfter)) {
                        this._getDropdown().hide();
                        this._taggableListOpen = null;
                        this._taggableLastSearchTerm = null;
                    } else {
                        if ((searchTerm.indexOf(mention) === 0) || (searchTerm.indexOf(reference) === 0)) {
                            // Search for possible matches
                            this._searchForTags(searchTerm);
                        } else {
                            // Reset taggable if user deletes either the beginning @ or # character
                            this._resetTaggable();
                        }
                    }
                }
            },

            /**
             * Gets the search term including @ or #
             * Only supports textarea and input fields
             *
             * @param {jQuery} $container jQuery object for textarea
             * @return {string}
             */
            getCurrentSearchTerm: function($container) {
                var value = $container.val();
                var char = value[this.taggableCursorPosition];
                var start = this.taggableCursorPosition;
                // find where the closest preceding @ or # is
                while (char !== '@' && char !== '#') {
                    if (start < 0) {
                        return '';
                    }
                    start--;
                    char = value[start];
                }
                return value.substring(start, this.taggableCursorPosition + 1);
            },

            /**
             * Insert a placeholder where tags can be searched.
             *
             * @private
             */
            _enableTaggable: function() {
                if (this._isTextArea(this._getTaggableInput())) {
                    this._taggableEnabled = true;
                } else {
                    var selection = window.getSelection();
                    var range = selection.getRangeAt(0);
                    var tagElement = $(taggingHtml);
                    var textNode = tagElement.contents()[0];
                    var cursorPosition = _.some([/chrome/, /safari/, /opera\//, /webkit/], function(rx) {
                            return rx.test(navigator.userAgent.toLowerCase());
                        }) ? 1 : 0;

                    if (this._shouldEnableTaggable(range)) {
                        range.insertNode(tagElement.get(0));
                        range.setStart(textNode, cursorPosition);
                        range.setEnd(textNode, cursorPosition);

                        selection.removeAllRanges();
                        selection.addRange(range);

                        this._taggableEnabled = true;
                    }
                }
            },

            /**
             * Checks to see if the cursor is in the right position to enable taggable. If the @ or # is either the
             * first character or is prefixed by a space, the cursor is in the right position to enable taggable.
             * If the cursor is at the beginning of a text node, taggable can be enabled.
             *
             * @param {Range} range
             * @returns {boolean}
             * @private
             */
            _shouldEnableTaggable: function(range) {
                var text = range.startContainer.nodeValue,
                    charBeforeCursor,
                    result = false;

                if ((range.startContainer.nodeType === Node.ELEMENT_NODE) || (text.length === 0) || (range.startOffset === 0)) {
                    result = true;
                } else {
                    charBeforeCursor = text.charAt(range.startOffset - 1);
                    if ((charBeforeCursor === String.fromCharCode(160)) || (charBeforeCursor === String.fromCharCode(32))) {
                        result = true;
                    }
                }

                return result;
            },

            /**
             * Remove taggable placeholder, remove search results list, and reset state.
             *
             * @private
             */
            _resetTaggable: function() {
                var $taggable = this._getTaggableInput().focus();
                var selection = window.getSelection();
                if (selection.rangeCount > 0) {
                    var range = selection.getRangeAt(0);
                }
                var isTextarea = this._isTextArea($taggable);

                if (this._taggableEnabled && !isTextarea) {
                    var $taggingSpan = $taggable.find('.sugar_tagging');

                    if ($taggingSpan.length > 0) {
                        range.selectNodeContents($taggingSpan.get(0));
                        range.collapse(false);

                        selection.removeAllRanges();
                        selection.addRange(range);

                        $taggingSpan
                            .before($taggingSpan.html().trim())
                            .remove();
                    } else {
                        // Fix bug for Chrome where <b> tag gets inserted when tagging span is the only content inside
                        // taggable area and a user selects all via keyboard shortcut and presses delete.
                        $taggable.blur().focus();
                    }

                    this._removeDropdown();

                    this._taggableEnabled = false;
                    this._taggableListOpen = null;
                    this._taggableLastSearchTerm = null;
                }
            },

            /**
             * Insert currently active tag from the search results list into the content editable area.
             *
             * @event click
             * @private
             */
            _insertTag: function(event) {
                var $selected = $(event.currentTarget),
                    $taggable = this._getTaggableInput(),
                    taggableData = $selected.data(),
                    $tagToReplace = $taggable.find('.sugar_tagging'),
                    selection, range, $tagHtml;

                var isTextArea = this._isTextArea($taggable);

                if (!$selected.hasClass('disabled')) { //do not insert disabled tag option
                    $taggable.focus();

                    if (isTextArea) {
                        this._insertIntoTextarea($taggable, taggableData);
                    } else {
                        selection = window.getSelection();
                        range = selection.getRangeAt(0);
                        $tagHtml = $(this.tagInEditTemplate(taggableData));

                        range.selectNode($tagToReplace.get(0));
                        range.insertNode($tagHtml.get(0));
                        range.selectNode($tagHtml.get(0));
                        range.collapse(false);

                        selection.removeAllRanges();
                        selection.addRange(range);

                        $tagToReplace
                            .before('&nbsp;')
                            .remove();

                        $tagHtml.data({
                            id: taggableData.id,
                            name: taggableData.name,
                            module: taggableData.module
                        });
                    }
                    this._removeDropdown();

                    this._taggableEnabled = false;
                    this._taggableListOpen = null;
                    this._taggableLastSearchTerm = null;
                }

                event.preventDefault();
            },

            /**
             * Insert the taggable variable directly into the textarea field and replace the searchterm
             *
             * @param {jQuery} $textarea textarea jQuery object
             * @param {Object} taggableData contains module, id and name of mention tag
             * @private
             */
            _insertIntoTextarea: function($textarea, taggableData) {
                if (!$textarea) {
                    return;
                }

                var variable = '@[' + taggableData.module + ':' + taggableData.id + ':' + taggableData.name + ']';
                var lengthOfSearchTerm = this.getCurrentSearchTerm($textarea).length;
                var startOfSearchTerm = this.taggableCursorPosition - lengthOfSearchTerm + 1;
                var currentText = $textarea.val();

                var front = currentText.substring(0, startOfSearchTerm);
                var back = currentText.substring(this.taggableCursorPosition + 1, currentText.length);
                // compose new text data that removes the searchterm (and the @ or #) and replaces it with
                // the @mention variable
                $textarea.val(front + variable + back);
                $textarea.trigger('change');
            },

            /**
             * Make a server call to search for users and records that match the specified search term.
             *
             * @param {String} searchTerm
             * @private
             */
            _searchForTags: function(searchTerm) {
                var searchParams;
                var referenceSearchFields = ['name', 'first_name', 'last_name'];
                var tagAction = searchTerm.charAt(0); // @ or # character
                var filtersBeanPrototype = app.data.getBeanClass('Filters').prototype;

                searchTerm = searchTerm.substr(1).trim();

                // Do not perform search if the number of characters typed so far in typeahead is less than what is
                // specified in taggableSearchAfter and if search term is the same as the last searched term.
                if ((searchTerm.length >= this.taggableSearchAfter) && (searchTerm !== this._taggableLastSearchTerm)) {
                    searchParams = {
                        q: searchTerm,
                        max_num: this.taggableListLength,
                        fields: 'name'
                    };

                    // Reset taggable if there were no results returned during previous search and the user continues to type
                    if ((this._taggableListOpen === false) && (searchTerm.indexOf(this._taggableLastSearchTerm) === 0)) {
                        this._resetTaggable();
                    } else {
                        if (tagAction === mention) {
                            app.data.createBeanCollection('Users').fetch({
                                success: _.bind(function(collection, resp) {
                                    if (this._taggableEnabled && resp) {
                                        this._populateTagList(collection, searchTerm);
                                    }
                                }, this),
                                filter: filtersBeanPrototype.buildSearchTermFilter('Users', searchTerm),
                                params: {
                                    has_access_module: this._taggableModuleName,
                                    has_access_record: this._taggableModelId
                                }
                            });
                        } else if (tagAction === reference) {
                            searchParams.search_fields = referenceSearchFields.join();

                            app.api.search(searchParams, {
                                success: _.bind(function(response) {
                                    if (this._taggableEnabled && response) {
                                        this._populateTagList(app.data.createMixedBeanCollection(response.records), searchTerm);
                                    }
                                }, this)
                            });
                        }

                        this._taggableLastSearchTerm = searchTerm;
                    }
                }
            },

            /**
             * Build the tag search results list with possible matches.
             *
             * @param {Collection} collection
             * @param {String} searchTerm
             * @private
             */
            _populateTagList: function(collection, searchTerm) {
                var $tagList = this._initializeDropdown(),
                    currentSearchTerm;
                var isTextarea = this._isTextArea(this._getTaggableInput());

                if (collection.length > 0) {
                    searchTerm = searchTerm.trim();

                    // If the current search term differs from what was searched, do not display the dropdown list
                    if (isTextarea) {
                        currentSearchTerm = this.getCurrentSearchTerm(this._getTaggableInput()).trim();
                    } else {
                        currentSearchTerm = this._getTaggableInput().find('.sugar_tagging').text().trim();
                    }
                    if (currentSearchTerm.substr(1).trim() !== searchTerm) {
                        this._taggableLastSearchTerm = null;
                        return;
                    }

                    // Append search results to the dropdown list
                    collection.each(function(model, index) {
                        var $tagListOption, data, escapedSearchTerm, name, secureName, htmlName;

                        name = app.utils.getRecordName(model);
                        // secureName used as htmlName to insert into template without escaping ("triple-stash")
                        secureName = Handlebars.Utils.escapeExpression(name).trim();
                        // searchTerm can contains special symbols that escaped in secureName
                        escapedSearchTerm = Handlebars.Utils.escapeExpression(searchTerm).trim();
                        htmlName = secureName.replace(new RegExp('(' + escapedSearchTerm + ')', 'ig'), function($1, match) {
                            return '<strong>' + match + '</strong>';
                        });

                        data = {
                            module: model.get('_module'),
                            id: model.get('id'),
                            name: name,
                            htmlName: htmlName,
                            // only if false, undefined does not mean no access
                            noAccess: (model.get('has_access') === false)
                        };

                        $tagListOption = $(this.tagListOptionTemplate(data)).data(data);
                        $tagList.append($tagListOption);
                    }, this);

                    //mark first on the list as active
                    this._selectNextListOption(true);

                    this._taggableListOpen = true;
                    $tagList.show();
                } else {
                    this._taggableListOpen = false;
                    $tagList.hide();
                }
            },

            /**
             * Get a new dropdown list.
             *
             * @returns {JQuery}
             * @private
             */
            _initializeDropdown: function() {
                var $dropdown = this._getDropdown(),
                    $taggable = this._getTaggableInput();

                if ($dropdown.length === 0) {
                    // Create new dropdown list and place it below the input box
                    $dropdown = $(tagListContainerHtml)
                        .hide()
                        .appendTo($taggable.parent());
                } else {
                    // Empty existing dropdown list
                    $dropdown.empty();
                }

                return $dropdown;
            },

            /**
             * Get current dropdown list.
             *
             * @returns {JQuery}
             * @private
             */
            _getDropdown: function() {
                return this.$('.activitystream-tag-dropdown');
            },

            /**
             * Delete dropdown list.
             *
             * @private
             */
            _removeDropdown: function() {
                this.$('.activitystream-tag-dropdown').remove();
            },

            /**
             * Get the currently selected tag search result in the dropdown list.
             *
             * @returns {JQuery}
             * @private
             */
            _getCurrentlyActiveOption: function() {
                return this._getDropdown().find('.active');
            },

            /**
             * Given the currently selected option, select the next option, whether it is
             * by going down or up.
             *
             * @param {boolean} down
             * @param {jQuery} $from (Optional)
             * @private
             */
            _selectNextListOption: function(down, $current) {
                var $next;

                $current = $current || this._getCurrentlyActiveOption();

                if ($current.length === 0) {
                    $next = this._getDropdown().children().first();
                } else {
                    $next = down ? $current.next() : $current.prev();
                }

                if ($next.length > 0) {
                    $current.removeClass('active');

                    if ($next.hasClass('disabled')) {
                        this._selectNextListOption(down, $next);
                    } else {
                        $next.addClass('active');
                    }
                }
            },

            /**
             * Make the dropdown option the currently selected option on hover.
             *
             * @event mouseover
             * @private
             */
            _setListOptionAsActive: function(event) {
                var currentTarget = this.$(event.currentTarget);

                if (!currentTarget.hasClass('disabled')) {
                    this._getDropdown().find('.active').removeClass('active');
                    currentTarget.addClass('active');
                }
            },

            /**
             * Get the content editable area that has been marked as taggable
             *
             * @returns {JQuery}
             * @private
             */
            _getTaggableInput: function() {
                return this.$('.taggable');
            },

            /**
             * Checks if the $input is a textarea
             *
             * @param {jQuery} $input Input to be checked
             * @return {boolean}
             * @private
             */
            _isTextArea: function($input) {
                return $input.prop('nodeName') === 'TEXTAREA';
            },

            /**
             * Filter out all duplicate tags based on IDs
             *
             * @param {Array} tags
             * @returns {Array}
             * @private
             */
            _filterOutDuplicateTags: function(tags) {
                tags = _.uniq(tags, function(tag){
                    return tag.id;
                });

                return tags;
            }
        });
    });
})(SUGAR.App);
