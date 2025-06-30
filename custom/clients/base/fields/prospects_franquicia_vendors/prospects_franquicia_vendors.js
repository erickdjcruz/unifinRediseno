({
    events: {
        'change select': 'handleChange'
    },

    initialize: function (options) {
        this._super('initialize', [options]);

        // Cache global (memoria por sesión)
        window.franquiciaVendorsCache = window.franquiciaVendorsCache || null;

        this.dropdownOptions = {};
        this.items = {};
        this.apiError = false;

        // Siempre intenta cargar las opciones
        this.loadDropdownOptions();
    },

    loadDropdownOptions: function () {
        var self = this;

        if (window.franquiciaVendorsCache) {
            self.dropdownOptions = window.franquiciaVendorsCache;
            self.items = window.franquiciaVendorsCache;

            _.defer(function () {
                self.render();
            });
            return;
        }

        app.api.call('GET', app.api.buildURL('ListadoFranquiciaVendors'), null, {
            success: function (data) {
                var opts = {};
                _.each(data.records, function (item) {
                    if (item.id && item.nombre) {
                        opts[item.id] = item.nombre;
                    }
                });

                // Guardar en cache global
                window.franquiciaVendorsCache = opts;
                self.dropdownOptions = opts;
                self.items = opts;
                self.apiError = false;

                console.log("API → Valor guardado:", self.model.get('id_franquicia_vendors_c'));
                // console.log("API → Items:", self.items);

                _.defer(function () {
                    self.render();
                });
            },
            error: function (error) {
                console.log('Error al cargar opciones:', error);

                self.apiError = true;
                // No hay datos del servicio
                self.dropdownOptions = {};
                self.items = {};

                _.defer(function () {
                    self.render();
                });
            }
        });
    },

    handleChange: function () {
        var val = this.$('select').val();
        var label = this.dropdownOptions[val] || '';
        this.model.set('id_franquicia_vendors_c', val);
        this.model.set('label_franquicia_vendors_c', label);
    },

    _render: function () {
        if (!this.$el || this.$el.length === 0) {
            return;
        }

        this._super('_render');

        var selectedVal = this.model.get('id_franquicia_vendors_c');
        var savedLabel = this.model.get('label_franquicia_vendors_c') || '';
        var $select = this.$('select');

        if (this.action === 'edit') {
            if ($select.length > 0) {
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }

                $select.empty();

                // console.log("savedLabel ", savedLabel);
                // console.log("apiError ", this.apiError);
                // Si la API falla, mostrar la opción guardada en la base de datos
                if (this.apiError) {
                    // Si la API falla, muestra la opción guardada
                    if (selectedVal) {
                        $select.append('<option value="' + _.escape(selectedVal) + '">' + _.escape(savedLabel) + '</option>');
                        $select.val(selectedVal);
                    } else {
                        // Si no hay valor guardado, muestra "Sin Alianza"
                        $select.append('<option value="">Sin Alianza</option>');
                        $select.val('');
                    }
                } else {
                    // Si la API responde, carga las opciones
                    $select.append('<option value="">Seleccionar...</option>');
                    var exists = false;

                    _.each(this.dropdownOptions, function (label, key) {
                        $select.append('<option value="' + _.escape(key) + '">' + _.escape(label) + '</option>');
                        if (key === selectedVal) {
                            exists = true;
                        }
                    });

                    // Si no existe el valor guardado en las opciones, lo agregamos manualmente
                    if (selectedVal && !exists) {
                        $select.append('<option value="' + _.escape(selectedVal) + '">' + _.escape(savedLabel) + '</option>');
                    }

                    $select.val(selectedVal);
                }

                $select.select2({ width: '100%' });
            }
        } else {
            // DETAIL VIEW
            var label = 'Sin Alianza'; // Por defecto

            if (this.items && this.items[selectedVal]) {
                label = this.items[selectedVal];  // Etiqueta desde la API
            } else if (selectedVal) {
                label = savedLabel;  // Etiqueta guardada
            }

            // Esto es lo que evita que el campo se colapse
            this.model.set(this.name, label);

            var $span = this.$el.find('span');
            if ($span.length) {
                $span.text(label);
            }

            console.log("→ Render Detail");
            console.log("→ selectedVal:", selectedVal);
            console.log("→ label:", label);
            // console.log("→ items:", this.items);
            // console.log("→ $span:", $span.prop('outerHTML'));
        }
    },
});
