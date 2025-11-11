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

const Events = require('./events');

function makeLastState() {
    var keySeparator = ':',
        keyPrefix = 'last-state',
        lastStates = {},
        preservedKeys = [ ];

    var buildLastStateKeyForStorage = function(key) {
        var keyParts = key.split(keySeparator);
        var storedKey = [SUGAR.App.user.id, keyPrefix];

        storedKey = storedKey.concat(keyParts);

        return storedKey.join(keySeparator);
    };

    var getLastStateId = function(component) {
        var lastStateId;

        if (component.meta && component.meta.last_state) {
            lastStateId = component.meta.last_state.id;
        }

        return lastStateId;
    };

    /**
     * Allows interactions with the last state values, which are used to
     * implement last application states or "stickiness".
     * @class
     * @name Core/User.LastState
     */

    /**
     * @type {Core/User.LastState}
     * @name lastState
     * @memberOf module:Core/User
     */
    return {
        /**
         * Get the last state value given a key. If it doesn't exist,
         * return the default value as specified in the component
         * metadata.
         *
         * @param {string} key The local storage key.
         * @return {*} The last state
         *   value.
         * @memberOf Core/User.LastState
         */
        get: function(key) {
            var result, storedKey;

            if (!_.isUndefined(key)) {
                storedKey = buildLastStateKeyForStorage(key);
                result = SUGAR.App.cache.get(storedKey);
                result = result === void 0 ? this.defaults(key) : result;
            }

            return result;
        },

        /**
         * Saves the last state in local storage.
         *
         * @param {string} key The local storage key.
         * @param {string} value The value to associate with `key`.
         * @param {boolean} updateDbValue (optional) if true, updates the key in the DB as well
         * @memberOf Core/User.LastState
         */
        set: function(key, value, updateDbValue = true) {
            if (!_.isUndefined(key) && !_.isUndefined(value)) {
                var storedKey = buildLastStateKeyForStorage(key);
                SUGAR.App.cache.set(storedKey, value);

                if (updateDbValue &&
                    SUGAR.App.api.isAuthenticated() &&
                    SUGAR.App.user.get('type') !== 'support_portal'
                ) {
                    this.changesToSync = this.changesToSync || {};
                    this.changesToSync[key] = value;
                    this._syncDb();
                }
            }
        },

        /**
         * Registers a state as important (should survive a cache clean).
         *
         * @param {string} key The key of the state to preserve.
         * @memberOf Core/User.LastState
         */
        preserve: function(key) {
            if (!_.isUndefined(key)) {
                preservedKeys.push(key);
            }
        },

        /**
         * Gets the key for a given component, which is used as a key
         * for CRUD operations on last state values.
         *
         * @param {string} name State type name.
         * @param {Object} component Component name.
         * @return {string} A last state key corresponding to the
         *   given values.
         * @memberOf Core/User.LastState
         */
        key: function(name, component) {
            var lastStateId = getLastStateId(component);
            return this.buildKey(name, lastStateId, component.module);
        },

        /**
         * Builds the key for a given name, lastStateId, and
         * (optionally) module, which is used as a key for CRUD
         * operations on last state values.
         *
         * @param {string} name State type name.
         * @param {string} lastStateId Last state identifier.
         * @param {string} [module] Module name.
         * @return {string} A last state key corresponding to the
         *   given values.
         * @memberOf Core/User.LastState
         */
        buildKey: function(name, lastStateId, module) {
            var keyString, keyParts = [];

            if (lastStateId) {
                if (module) {
                    keyParts.push(module);
                }
                keyParts.push(lastStateId, name);
                keyString = keyParts.join(keySeparator);
            }

            return keyString;
        },

        /**
         * Gets the default last state for a key.
         *
         * @param {string} key A last state key.
         * @return {string} The default last state for the given `key`.
         * @memberOf Core/User.LastState
         */
        defaults: function(key) {
            return lastStates[key];
        },

        /**
         * Registers last states default values given a component.
         * The default value is specified in the component metadata.
         *
         * @param {Object} component Component to register default
         *   states for.
         * @memberOf Core/User.LastState
         */
        register: function(component) {
            var lastStateId = getLastStateId(component);
            if (lastStateId){
                _.each(component.meta.last_state.defaults, function(defaultState, key) {
                    lastStates[this.key(key, component)] = defaultState;
                }, this);
            }
        },

        /**
         * Deletes last state from local storage.
         *
         * @param {string} key The state to remove from local storage.
         * @memberOf Core/User.LastState
         */
        remove: function(key) {
            var storedKey;
            if (!_.isUndefined(key)) {
                storedKey = buildLastStateKeyForStorage(key);
                SUGAR.App.cache.cut(storedKey);
            }
        },

        /**
         * Fetch the user's last state from the DB
         *
         * @param callback
         */
        load: function(callback) {
            SUGAR.App.api.call('read', SUGAR.App.api.buildURL('me/last_states'), null, {
                success: (data) => {
                    if (!_.isEmpty(data)) {
                        Object.entries(data).forEach(([key, value]) => {
                            this.set(key, value, false);
                        });
                    }
                    if (_.isFunction(callback)) {
                        callback();
                    }
                },
                error: (err) => {
                    SUGAR.App.error.handleHttpError(err);
                    if (_.isFunction(callback)) {
                        callback(err);
                    }
                }
            });
        },

        /**
         * Retrieves a list of important last value keys in the cache.
         *
         * @return {Array} List of high value keys in cache.
         * @private
         * @memberOf Core/User.LastState
         */
        _getPreservedKeys: function() {
            var ret = [];
            _.each(preservedKeys, function(key) {
                ret.push(buildLastStateKeyForStorage(key));
            });
            return ret;
        },

        /**
         * Syncs the current lastState data for the user with the DB
         */
        _syncDb: _.debounce(function() {
            if (!this.isUpdating) {
                // Allow one DB update at a time to prevent issues with concurrent saves
                this.isUpdating = true;

                // Get the changed lastStates since the last sync
                let args = {
                    values: Object.assign({}, this.changesToSync)
                };
                this.changesToSync = {};

                // On success, check to see if we need to save again (if another
                // change was made since the last save call was triggered)
                let callbacks = {
                    success: () => {
                        this.isUpdating = false;
                        if (this.resave) {
                            this.resave = false;
                            this._syncDb();
                        }
                    },
                    error: (err) => {
                        SUGAR.App.error.handleHttpError(err);
                    }
                };

                SUGAR.App.user.syncTimezone();

                SUGAR.App.api.call('update', SUGAR.App.api.buildURL('me/last_states'), args, callbacks);
            } else {
                // A save is already in progress. Since another change has been made,
                // mark this to trigger another save after the current one is complete
                this.resave = true;
            }
        }, 500),
    };
}

/**
 * Represents application's current user object.
 *
 * The user object contains settings that are fetched from the server
 * and whatever settings the application wants to store.
 *
 * ```
 * // Sample user object that is fetched from the server:
 * {
 *      id: "1",
 *      full_name: "Administrator",
 *      user_name: "admin",
 *      preferences: {
 *          timezone: "America\/Los_Angeles",
 *          datepref: "m\/d\/Y",
 *          timepref: "h:ia"
 *      }
 * }
 *
 * // Use it like this:
 * const User = require('./user');
 * var userId = User.get('id');
 * // Set app specific settings
 * User.set('sortBy:Cases', 'case_number');
 *
 * // Bind event handlers if necessary
 * User.on('change', function() {
 *     // Do your thing
 * });
 * ```
 *
 * @module Core/User
 */
var User = Backbone.Model.extend({
    /**
     * Retrieves and sets the user preferences.
     *
     * @param {Function} [callback] Callback called when update completes.
     */
    load: function(callback) {
        SUGAR.App.api.me('read', null, null, {
            success: _.bind(function(data) {
                let useCallback = true;
                if (data && data.current_user) {
                    // Set the user pref hash into the cache for use in
                    // checking user pref state change
                    if (data.current_user._hash) {
                        SUGAR.App.cache.set('userpref:hash', data.current_user._hash);
                    }
                    this.set(data.current_user);
                    var language = this.getPreference('language');
                    if (SUGAR.App.lang.getLanguage() !== language) {
                        SUGAR.App.lang.setLanguage(language, null, {
                            noUserUpdate: true,
                            noSync: SUGAR.App.isSynced || SUGAR.App.metadata.isSyncing()
                        });
                    }

                    // After the user object is fetched, load the user's last state as well.
                    if (data.current_user.type !== 'support_portal') {
                        useCallback = false;
                        this.lastState.load(callback);
                    }
                }

                if (callback && useCallback) {
                    callback();
                }
            }, this),
            error: function(err) {
                SUGAR.App.error.handleHttpError(err);
                if (callback) callback(err);
            }
        });
    },

    /**
     * Update timezone in user's preferences.
     *
     * @return {void}
     */
    syncTimezone: function() {
        if (!this.attributes ||
            !this.attributes.preferences) {
            return;
        }

        const clientPref = this.getClientTimezonePref();
        const serverPref = _.pick(this.get('preferences'), [
            'timezone',
            'tz_offset',
            'tz_offset_sec',
        ]);

        if (!_.isEqual(clientPref, serverPref)) {
            const updateCallback = () => {
                if (this.isSetupCompleted()) {
                    SUGAR.App.alert.show('success-timezone-update', {
                        level: 'success',
                        messages: SUGAR.App.lang.get('LBL_TIMEZONE_UPDATED'),
                    });
                }
            }

            this.updatePreferences(clientPref, updateCallback);
        }
    },

    /**
     * Return client's timezone params.
     *
     * @return {Object}
     **/
    getClientTimezonePref: function() {
        const secOffset = new Date().getTimezoneOffset() * 60;
        const tzOffsetSec = (secOffset === 0) ? secOffset : -secOffset;
        const tzOffset = (new Date()).toString().match(/([-\+][0-9]+)\s/)[1];
        const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

        return {
            timezone: timezone,
            tz_offset: tzOffset,
            tz_offset_sec: tzOffsetSec,
        };
    },

    // Fixme This doesn't belong in user. See TY-526.
    /**
     * Loads the current user's locale.
     *
     * @param {Function} [callback] Called when loading the locale completes.
     */
    loadLocale: function(callback) {
        SUGAR.App.api.call('read', SUGAR.App.api.buildURL('locale'), null, {
            success: function(data) {
                if (callback) callback(data);
            },
            error: function(err) {
                SUGAR.App.error.handleHttpError(err);
                if (callback) callback(err);
            }
        });
    },

    /**
     * Retrieves the current user's preferred language.
     *
     * @return {string} The current user's preferred language.
     */
    getLanguage: function() {
        return this.getPreference('language') || SUGAR.App.cache.get('lang');
    },

    /**
     * Updates the user's preferred language.
     *
     * @param {string} language Language key.
     * @param {Function} [callback] Callback called when update completes.
     */
    updateLanguage: function(language, callback) {
        //Note that `err` is only relevant here when called for error case
        var done = function(err) {
            if (!err) SUGAR.App.lang.updateLanguage(language);
            if (callback) callback(err);
        };
        this.update("update", {preferred_language: language}, done);
    },

    /**
     * Updates the user's profile.
     *
     * @param {Object} attributes The model attributes to update for user.
     * @param {Function} [callback] Callback called when update completes.
     */
    updateProfile: function(attributes, callback) {
        //Note that `err` is only relevant here when called for error case
        var done = function(err) {
            if (callback) callback(err);
        };
        this.update('update', attributes, done);
    },

    /**
     * Updates the user's preferences.
     *
     * @param {Object} attributes The attributes to update for user.
     * @param {Function} [callback] Callback called when update completes.
     */
    updatePreferences: function(attributes, callback) {
        var self = this;
        if (SUGAR.App.api.isAuthenticated()) {
            SUGAR.App.api.call('update', SUGAR.App.api.buildURL('me/preferences'), attributes, {
                success: function(data) {
                    if (data._hash) {
                        SUGAR.App.cache.set('userpref:hash', data._hash);
                        self.set({'_hash': data._hash});
                    }
                    //Immediately update our user's preferences to reflect latest changes
                    _.each(attributes, function(val, key) {
                        if (!_.isUndefined(data[key])) {
                            self.setPreference(key, data[key]);
                        }
                    });
                    if (callback) callback();
                },
                error: function(err) {
                    SUGAR.App.error.handleHttpError(err);
                    if (callback) callback(err);
                }
            });
        } else {
            if (callback) callback();
        }
    },

    /**
     * Updates the user.
     *
     * @param {string} method Operation type: either 'read', 'update',
     *   'create', or 'delete'. {@see SUGAR.Api#me}.
     * @param {Object} payload An object literal with payload.
     * @param {Object} callback Callback called when update completes. In
     *   case of error, `App.error.handleHttpError` will be called here.
     */
    update: function(method, payload, callback) {
        if (SUGAR.App.api.isAuthenticated()) {
            SUGAR.App.api.me(method, payload, null, {
                success: _.bind(function(data) {
                    if (data.current_user) {
                        if (data.current_user._hash) {
                            SUGAR.App.cache.set('userpref:hash', data.current_user._hash);
                        }
                        this.set(data.current_user);
                    }
                    if (callback) callback();
                }, this),
                error: function(err) {
                    SUGAR.App.error.handleHttpError(err);
                    if (callback) callback(err);
                }
            });
        } else {
            callback();
        }
    },

    /**
     * Gets ACLs.
     * Precondition: either the user is logged in or an `_reset` call has
     * set the user manually.
     *
     * @return {Object} Dictionary of ACLs.
     */
    getAcls: function() {
        return this.get('acl') || {};
    },

    /**
     * Gets a preference by name.
     *
     * @param {string} name The preference name.
     * @return {*} The value of the user preference, or `name` if no
     *   corresponding preference value exists.
     *
     * @todo support category parameter for preferences.
     */
    getPreference: function(name) {
        var preferences = this.get('preferences') || {};
        return preferences[name];
    },

    /**
     * Set preference by name. Will only be stored locally.
     *
     * @param {string} name The preference name.
     * @param {*} value The new preference value.
     * @return {Object} The instance of this user.
     *
     * @todo support category parameter for preferences.
     * @todo support save preferences on server.
     */
    setPreference: function(name, value) {
        var preferences = this.get('preferences') || {};
        preferences[name] = value;
        return this.set('preferences', preferences);
    },

    /**
     * Returns an object with all the user's currency preferences.
     * If the user hasn't specified any preferences,
     * these default to system currency preferences.
     *
     * @return {Object} The user's currency preferences.
     */
    getCurrency: function() {
        var preferences = this.get('preferences'),
            currencyObj = {};

        if (preferences) {
            preferences.currency_create_in_preferred = preferences.currency_create_in_preferred || false;
            currencyObj = _.pick(
                preferences,
                'currency_id',
                'currency_iso',
                'currency_name',
                'currency_rate',
                'currency_show_preferred',
                'currency_create_in_preferred',
                'currency_symbol'
            );
        }

        return currencyObj;
    },

    /**
     * Checks if the user has one or more licenses.
     * @param {string|Array} licenseType one or more licenses to check
     * @param {boolean} hasAll if true, the user must have all the provided licenses
     * @return {boolean} true if the user's licenses match the criteria, false otherwise
     */
    hasLicense: function(licenseType, hasAll = false) {
        let userLicenses = this.get('licenses');
        if (!_.isArray(userLicenses)) {
            return false;
        }

        if (_.isString(licenseType)) {
            licenseType = [licenseType];
        }

        let licenseMatcher = license => userLicenses.includes(license);
        return hasAll ? licenseType.every(licenseMatcher) : licenseType.some(licenseMatcher);
    },

    /**
     * Gets a list of all the product codes of products in use by the user
     *
     * @return {Array} the unique list of product codes
     */
    getProductCodes: function() {
        return _.uniq(_.pluck(this.get('products'), 'product_code'));
    },

    /**
     * Gets a list of all the readable names of products in use by the user
     *
     * @return {Array} the unique list of product names
     */
    getProductNames: function() {
        return _.uniq(_.pluck(this.get('products'), 'product_name'));
    },

    /**
     * Checks if the user has a Sell license.
     * @return {boolean}
     */
    hasSellLicense: function() {
        return this.hasLicense('SUGAR_SELL');
    },

    /**
     * Checks if the user has a Serve license.
     * @return {boolean}
     */
    hasServeLicense: function() {
        return this.hasLicense('SUGAR_SERVE');
    },

    /**
     * Checks if the user has a Sell/Serve license. If hasBoth is true, then the user must
     * have both licenses. If hasBoth is false (default), then having at least one license is enough.
     * @param {boolean} hasBoth
     * @return {boolean}
     */
    hasSellServeLicense: function(hasBoth = false) {
        return this.hasLicense(['SUGAR_SELL', 'SUGAR_SERVE'], hasBoth);
    },

    /**
     * Checks if the user has a Connect license.
     * @return {boolean}
     */
    hasConnectLicense: function() {
        return this.hasLicense('CONNECT');
    },

    /**
     * Checks if the user has a Discover license.
     * @return {boolean}
     */
    hasDiscoverLicense: function() {
        return this.hasLicense('DISCOVER');
    },

    /**
     * Checks if the user has a Hint license.
     * @return {boolean}
     */
    hasHintLicense: function() {
        return this.hasLicense('HINT');
    },

    /**
     * Checks if the user has a Sugar Automate license.
     * @return {boolean}
     */
     hasAutomateLicense: function() {
        return this.hasLicense('AUTOMATE');
    },

    /**
     * Checks if the user has a Maps license.
     * @return {boolean}
     */
    hasMapsLicense: function() {
        return this.hasLicense('MAPS');
    },

    /**
     * Checks if the user has an Advanced Forecasting license.
     * @return {boolean}
     */
    hasAdvancedForecastingLicense: function() {
        return this.hasLicense('ADVANCEDFORECAST');
    },

    /**
     * Checks if the user has a Predict Advanced or Predict Premier license.
     * @return {boolean}
     */
    hasPredictLicense: function() {
        return this.hasPredictAdvancedLicense() || this.hasPredictPremierLicense();
    },

    /**
     * Checks if the user has a Predict Advanced license.
     * @return {boolean}
     */
    hasPredictAdvancedLicense: function() {
        return this.hasLicense('PREDICT_ADVANCED');
    },

    /**
     * Checks if the user has a Predict Premier license.
     * @return {boolean}
     */
    hasPredictPremierLicense: function() {
        return this.hasLicense('PREDICT_PREMIER');
    },

    /**
     * Checks if the user has completed their set up steps
     * @return {boolean}
     */
    isSetupCompleted: function() {
        return !this.get('show_wizard');
    },

    lastState: makeLastState()
});

const MyUser = new User();

Events.on('app:logout', function(clear) {
    SUGAR.App.cache.cut('userpref:hash');
    if (clear === true) {
        MyUser.clear({silent: true});
    }
});

Events.on('cache:clean', function(cb) {
    cb(MyUser.lastState._getPreservedKeys());
});

module.exports = MyUser;
