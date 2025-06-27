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

                console.log("API → Valor guardado:", self.model.get('id_franquicia_vendors_c'));
                console.log("API → Items:", self.items);

                _.defer(function () {
                    self.render();
                });
            },
            error: function (error) {
                console.error('Error al cargar opciones:', error);
            }
        });
    },

    handleChange: function () {
        var val = this.$('select').val();
        this.model.set('id_franquicia_vendors_c', val);
    },

    _render: function () {
        if (!this.$el || this.$el.length === 0) {
            return;
        }

        this._super('_render');

        var $select = this.$('select');
        var selectedVal = this.model.get('id_franquicia_vendors_c');

        if (this.action === 'edit') {
            if ($select.length > 0) {
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }

                $select.empty();

                if (_.isEmpty(this.dropdownOptions)) {
                    $select.append('<option value="">Cargando opciones...</option>');
                } else {
                    $select.append('<option value="">Seleccionar...</option>');
                    _.each(this.dropdownOptions, function (label, key) {
                        $select.append('<option value="' + _.escape(key) + '">' + _.escape(label) + '</option>');
                    });

                    $select.val(selectedVal);
                }

                $select.select2({ width: '100%' });
            }
        } else {
            // DETAIL VIEW
            var label = this.items[selectedVal] || 'Sin Alianza';

            // Esto es lo que evita que el campo se colapse
            this.model.set(this.name, label);

            var $span = this.$el.find('span');
            if ($span.length) {
                $span.text(label);
            }

            console.log("→ Render Detail");
            console.log("→ selectedVal:", selectedVal);
            console.log("→ items:", this.items);
            console.log("→ label:", label);
            console.log("→ $span:", $span.prop('outerHTML'));
        }
    },

    dispose: function () {
        var $select = this.$('select');
        if ($select.length && $select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }
        this._super('dispose');
    }
});
