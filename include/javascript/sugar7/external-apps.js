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
(function(app) {
    var findDashboardPreviewLayout = function(meta, layoutName, targetLayout, layoutMeta) {
        if (meta.name && meta.name === layoutName) {
            targetLayout = meta;
            return targetLayout;
        }

        if (_.isArray(meta)) {
            var len = meta.length;
            for (var i = 0; i < len; i++) {
                if (_.isArray(meta[i]) || _.isObject(meta[i])) {
                    targetLayout = findDashboardPreviewLayout(meta[i], layoutName, targetLayout, layoutMeta);
                }
                if (!_.isEmpty(targetLayout)) {
                    return targetLayout;
                }
            }
        } else {
            for (var prop in meta) {
                if (_.isArray(meta[prop]) || _.isObject(meta[prop])) {
                    targetLayout = findDashboardPreviewLayout(meta[prop], layoutName, targetLayout, layoutMeta);

                    if (!_.isEmpty(targetLayout)) {
                        return targetLayout;
                    }
                }
            }
        }
    };

    var addCompToLayout = function(metadata, targetModule, targetLayout, def, fieldsToAdd) {
        var modMeta = metadata.modules[targetModule];
        var layoutPieces = targetLayout.split('-');
        var hasLayoutInModule;
        var dashPrevLayout;
        var meta;
        var layoutName;
        var view;

        // handle i18n for name
        if (def.view.name && def.view.name.indexOf('LBL_') !== -1) {
            // try to find language string in target module first then global scope
            def.view.name = app.lang.get(def.view.name, targetModule);
        }
        // handle i18n for description
        if (def.view.description && def.view.description.indexOf('LBL_') !== -1) {
            // try to find language string in target module first then global scope
            def.view.description = app.lang.get(def.view.description, targetModule);
        }

        if (modMeta) {
            modMeta.layouts = modMeta.layouts || {};
            // try to get the metadata for the specific module layout
            meta = modMeta.layouts[targetLayout];
            hasLayoutInModule = !!meta;

            // handle list-field and record-field items
            if (layoutPieces.length === 2 &&
                _.contains(['record', 'list', 'multiline'], layoutPieces[0]) &&
                layoutPieces[1] === 'field') {

                var layoutName = _fixViewName(layoutPieces[0]);
                var layoutEventName = layoutName === 'list' ? 'recordlist' : layoutName;
                // Defensive code to make sure layoutName is supported
                // e.g. PRO doesn't support 'multi-line-list', the process will be skipped
                if (layoutName && _.isUndefined(modMeta.views[layoutName])) {
                    return;
                }

                // get the view: 'list' or 'record'
                view = modMeta.views[layoutName];

                _.each(fieldsToAdd, function(field) {
                    // set the src for the field
                    field.src = def.view.src;
                    // if the field is already in the view's meta, find it and add mfe related properties to it
                    var metaField = _findFieldInMeta(field, view);
                    if (metaField) {
                        _.extend(metaField, field);

                    // if the field isn't in meta but we still want to force it in,
                    // we can use the panel and field indices from manifest
                    } else if (!_.isUndefined(field.panelIndex) && !_.isUndefined(field.fieldIndex)) {
                        _spliceFieldIntoMeta(field, view, field.panelIndex, field.fieldIndex);
                        view.meta.hasExternalFields = true;
                    }
                }, this);

                // this currently only has a listener in the recordlist view otherwise this will do nothing
                app.events.trigger('sugarApp:' + targetModule + ':' + layoutEventName + ':updated', fieldsToAdd);
            } else if (layoutPieces.length === 2 &&
                (layoutPieces[1] === 'dashboard' || layoutPieces[1] === 'preview')) {
                // if the targetLayout has 2 pieces "extra-info", "record-dashboard", etc
                // and the second piece is either "dashboard" or "preview"
                // we need to handle these components in a different way to "extra-info" components

                // if the first piece of he layout is "list" then we want the "records" layout
                // otherwise we want the "record" layout
                layoutName = layoutPieces[0] === 'list' ? 'records' : 'record';

                // try to get layout for the module. if the layout does not exist
                // getLayout will return us the base/core layoutName
                meta = app.metadata.getLayout(targetModule, layoutName);

                // recurse through the records or record layout metadata
                // to find "dashboard-pane" or "preview-pane" layout
                dashPrevLayout = findDashboardPreviewLayout(meta, layoutPieces[1] + '-pane', {});

                if (dashPrevLayout) {
                    // we've found the "dashboard-pane" or "preview-pane" layout
                    // now push the new component def into its components
                    dashPrevLayout.components.push(def);

                    // begin building the component layout structure we need to push back to metadata
                    if (!modMeta.layouts[layoutName]) {
                        modMeta.layouts[layoutName] = {};
                    }
                    if (!modMeta.layouts[layoutName].meta) {
                        modMeta.layouts[layoutName].meta = {};
                    }
                    if (!modMeta.layouts[layoutName].meta.components) {
                        modMeta.layouts[layoutName].meta.components = [];
                    }

                    metadata.modules[targetModule].layouts[layoutName].meta.components = meta.components;

                    // Set the whole metadata block back onto the app so it can be fetched again
                    // by app.metadata.getLayout if there are multiple dashboard/preview components
                    // that need to be set here
                    App.metadata.set(metadata);
                } else {
                    // wat?! somehow there's no dashboard-pane or preview-pane component in the layout
                    App.logger.warn('The ' + layoutName + ' layout for the ' + targetModule + ' does not contain a ' +
                        layoutPieces[1] + '-pane component inside the layout.');
                }
            } else if (!hasLayoutInModule) {
                // Merge with the global metadata before modifying if the module
                // didn't originally specify a config for this layout.
                modMeta.layouts[targetLayout] = {
                    meta: app.metadata.getLayout('', targetLayout) || {components: []}
                };

                modMeta.layouts[targetLayout].meta.components.push(def);
                // let the layout know metadata has been updated for this particular MFE module/layout
                app.events.trigger('sugarApp:' + targetModule + ':' + targetLayout + ':updated');
            } else {
                if (!meta.meta) {
                    meta.meta = {};
                }
                if (!meta.meta.components) {
                    meta.meta.components = [];
                }

                meta.meta.components.push(def);
            }
        }
    };

    /**
     * Find the field if it exists in the view's meta by field name
     *
     * @param {Object} fieldDef The fieldDef object. Must contain at least the `name` property
     * @param {Object} viewMeta View meta that has the format of meta.panels.fields
     * @return {Object|null} Finds the field in the metadata if it exists
     * @private
     */
    var _findFieldInMeta = function(fieldDef, viewMeta) {
        var found = null;
        if (_.isUndefined(fieldDef) || _.isUndefined(viewMeta)) {
            return null;
        }
        _.some(viewMeta.meta.panels, function(panel) {
            return _.some(panel.fields, function(metaField) {
                if (metaField.subfields) {
                    return _.some(metaField.subfields, function(subField) {
                        if (subField.name === fieldDef.name) {
                            found = subField;
                            return true;
                        }
                    });
                } else if (metaField.name === fieldDef.name) {
                    found = metaField;
                    return true;
                }
            });
        });
        return found;
    };

    /**
     * Sanitize the name of the view
     *
     * @param {string} name
     * @return {string}
     * @private
     */
    var _fixViewName = function(name) {
        var nameMap = {
            multiline: 'multi-line-list',
        };

        return nameMap[name] || name;
    };

    /**
     * Adds field to a specified position in the view's meta
     *
     * @param {Object} fieldDef The fieldDef to add
     * @param {Object} viewMeta View meta that has the format of meta.panels.fields
     * @param {number} panelIndex Which panel to add the field to
     * @param {number} fieldIndex Where to add the field in the fields array
     * @private
     */
    var _spliceFieldIntoMeta = function(fieldDef, viewMeta, panelIndex, fieldIndex) {
        var panels = viewMeta.meta.panels;
        var panel = panelIndex > panels.length ? panels[panels.length - 1] : panels[panelIndex];
        var fields = panel.fields;
        delete fieldDef.panelIndex;
        delete fieldDef.fieldIndex;
        if (fieldIndex < fields.length) {
            // fieldIndex is less than the length of fields,
            // so add field into the array at the specified index
            fields.splice(fieldIndex, 0, fieldDef);
        } else {
            // fieldIndex is greater than the length of fields,
            // add field to the end of fields
            fields.push(fieldDef);
        }
    };

    app.metadata.addSyncTask(function(metadata, options) {
        if (!app.config.catalogEnabled) {
            // if Sugar Catalog is not enabled, stop execution
            return;
        }
        var catalogUrl = app.config.catalogUrl;

        if (options.getPublic) {
            // skipping external app sync for public metadata
            return Promise.resolve();
        }

        if (catalogUrl && catalogUrl !== '' && _.isString(catalogUrl)) {
            // if catalogUrl does not start with http or https, prepend https://
            if (!(catalogUrl.indexOf('http://') === 0 || catalogUrl.indexOf('https://') === 0)) {
                catalogUrl = 'https://' + catalogUrl;
            }
            catalogUrl = catalogUrl.match(/^.+\:\/\/[^\/]+/)[0] + '/catalog?isAuthorized=true';

            var getCatalog = function(onSuccess, onError, onLogin) {
                $.ajax({
                    url: catalogUrl,
                    headers: {
                        'X-IDM-ACCESS-TOKEN': app.api.getOAuthToken()
                    },
                    xhrFields: {
                        withCredentials: true,
                        cors: true
                    },
                    contentType: 'application/json; charset=utf-8',
                    dataType: 'json',
                    crossDomain: true,
                    success: function(catalog) {
                        if (catalog.loginRedirect && onLogin) {
                            onLogin(catalog.loginRedirect);
                        } else {
                            onSuccess(catalog);
                        }
                    },
                    error: function(error) {
                        onError(error);
                    }
                });
            };
            var addCatalogToLayout = function(catalog) {
                if (!catalog || !catalog.layouts) {
                    return;
                }
                _.each(catalog.layouts, function(def) {
                    if (def.module && def.layout) {
                        catalog.type = 'external-app';

                        addCompToLayout(metadata, def.module, def.layout, {
                            view: catalog
                        }, def.fields);
                    }
                });
            };

            var updateManifest = function(catalog) {
                if (catalog.customValidityCheck) {
                    return System.import(catalog.src)
                        .then(function(mod) {
                            var props = Object.getOwnPropertyNames(mod).filter(function(name) {
                                return name.substr(0, 2) !== '__';
                            });

                            if (mod.default && (props.length === 1 || mod.__useDefault)) {
                                mod = mod.default;
                            }
                            return mod.updateManifest(catalog);
                        })
                        .then(function(result) {
                            if (_.isObject(result)) {
                                return result;
                            } else if (result) {
                                return catalog;
                            } else {
                                return null;
                            }
                        })
                        .catch(function(err) {
                            console.error('Failed to test module validity', err);
                            return null;
                        });
                } else {
                    return Promise.resolve(catalog);
                }
            };

            return new Promise(function(res, error) {
                var serverInfo = App.metadata.getServerInfo();
                var fetchAppLayout = function(app) {
                    $.ajax({
                        url: app.src,
                        dataType: 'json',
                        data: {
                            flavor: serverInfo.flavor,
                            tenantId: App.config.tenant,
                            version: serverInfo.version,
                            licenses: App.user.attributes.licenses
                        },
                        xhrFields: {
                            withCredentials: true
                        },
                        crossDomain: true,
                        contentType: 'application/json; charset=utf-8',
                        mode: 'cors',
                        success: function(catalog) {
                            updateManifest(catalog)
                                .then(function(manifest) {
                                    if (manifest.apps) {
                                        _.each(manifest.apps, function(app) {
                                            var catEntry = _.pick(
                                                manifest,
                                                'clientFileName',
                                                'scope',
                                                'src',
                                                'srn',
                                                'version'
                                            );
                                            // app will overwrite any values in catEntry e.g. src
                                            catEntry = _.extend(catEntry, app);
                                            updateManifest(catEntry)
                                                .then(addCatalogToLayout);
                                        }, this);

                                    } else {
                                        addCatalogToLayout(manifest);
                                    }
                                });
                        },
                        error: function(error) {
                            console.error(error);
                        }
                    });
                };

                var handleCatalog = function(catalog) {
                    _.each(catalog.apps, function(app) {
                        fetchAppLayout(app);
                    });

                    res();
                };

                var onError = function(err) {
                    app.logger.error(err.message);
                    res();
                };

                getCatalog(handleCatalog, onError, function(loginUrl) {
                    var iframe = document.createElement('iframe');
                    var cleanup = function() {
                        iframe.parentElement.removeChild(iframe);
                        window.removeEventListener('message', eventCallback);
                    };
                    var eventCallback = function(event) {
                        // TODO: Verify the catalog service origin instead of assuming any
                        // origin besides the one we are on is correct

                        var iframeOrigin = window.location.href.match(/^.+\:\/\/[^\/]+/)[0];
                        if (event.origin === iframeOrigin) {
                            cleanup();
                            // After the iframe event callback, we need to load the catalog again
                            // but this time expect to get data.
                            getCatalog(handleCatalog, onError, function(url) {
                                var err = 'Unable to authenticate with catalog service: Second Login URL:' + url;
                                app.logger.error(err);
                                res();
                            });
                        }
                    };

                    iframe.onload = function() {
                        console.log('loaded before we got the event ', arguments);
                        cleanup();
                        res();
                    };
                    iframe.src = loginUrl;
                    iframe.style = 'display:none;\n' +
                        'position: absolute;\n' +
                        'width: 500px;\n' +
                        'height: 500px;\n' +
                        'top: calc(50% - 250px);\n' +
                        'left: calc(50% - 250px);';

                    window.addEventListener('message', eventCallback);
                    document.body.appendChild(iframe);
                });
            });
        }
        return Promise.resolve();
    });
})(SUGAR.App);
