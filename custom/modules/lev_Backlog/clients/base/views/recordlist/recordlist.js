({
    extendsFrom: 'RecordlistView',

    initialize: function (options) {
        this._super('initialize', [options]);
        //OCULTA BOTON DE CREAR EN BACKLOG
        this.on('render', this.observeCreateButton, this);
    },

    observeCreateButton: function () {
        var toolbar = document.querySelector('div.btn-toolbar.pull-right');

        if (!toolbar) {
            return;
        }
        // Creamos un observer solo una vez
        if (!this._observer) {
            this._observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    mutation.addedNodes.forEach(function (node) {
                        if (node.nodeType === 1) {
                            var btn = node.querySelector('a[name="create_button"]');
                            if (btn) {
                                btn.style.display = 'none';
                            }
                        }
                    });
                });
            });

            this._observer.observe(toolbar, { childList: true, subtree: true });
        }
        // Intento inmediato por si ya existe
        var existingBtn = toolbar.querySelector('a[name="create_button"]');
        if (existingBtn) {
            existingBtn.style.display = 'none';
        }
    }
});
