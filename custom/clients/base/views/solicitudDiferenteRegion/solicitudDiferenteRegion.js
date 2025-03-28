({
    extendsFrom: 'BaseView',

    events: {
    },

    initialize: function (options) {
        this._super("initialize", [options]);

        //Extraer la parte después del #
        var hashParams = window.location.hash.split("?")[1];
        var urlParams = new URLSearchParams(hashParams);
        this.id = urlParams.get('id') || 'No recibido';
        this.accion = urlParams.get('accion') || 'No recibido';

        console.log('ID recibido:', this.idCuenta);
        console.log('Acción recibida:', this.accion);

        // Definir aceptación y rechazo como booleanos
        this.acepta = this.accion === 'aceptar' ? 1 : 0;
        this.rechaza = this.accion === 'rechazar' ? 1 : 0;

        console.log('acepta:', this.acepta);
        console.log('rechaza:', this.rechaza);

        if (this.idCuenta != '') {
            var url = app.api.buildURL('tct02_Resumen/' + this.idCuenta, null, null,);
            app.api.call('GET', url, {}, {
                success: _.bind(function (data) {
                    console.log("DATA RESUMEN ", data);
                    if (data != '') {
                        this.asignacionActiva = data.asignacion_activa_c;
                        this.idDirectorRegional = data.id_director_region_aprobar_c;
                        this.idAsesorSolicita = data.id_asesor_solicita_c;
        
                        if (this.acepta) {
                            this.aceptaAsignacion(this.idCuenta, this.idAsesorSolicita, 'comentarios');
                        } else {
                            this.rechazaAsignacion(this.idCuenta, this.idAsesorSolicita, 'comentarios');
                        }
                    }
                }, this) 
            });
        }
        this._render();
    },

    _render: function () {
        this._super('_render');
        // Actualizar el contenido en el HTML
        this.$('.id-container').text(this.idCuenta);
        this.$('.accion-container').text(this.accion);
        this.$('.aceptacion-container').text(this.acepta);
        this.$('.rechazo-container').text(this.rechaza);
    },

    aceptaAsignacion: function (idCuenta, idAsesorSolicita, comentarios) {
        console.log("ACEPTA ASIGNACION");
        var argsAcepta = {
            "id_cuenta": idCuenta,
            "id_asesor_solicita": idAsesorSolicita,
            "comentarios": comentarios
        };
        console.log(argsAcepta);
        app.api.call("create", app.api.buildURL("autorizaAsignacionCuenta", null, null, argsAcepta), null, {
            success: _.bind(function (response) {
                console.log(response);
                if (response.status == '200') {
                    app.alert.show('alert_autoriza_asignacion', {
                        level: 'success',
                        messages: 'Solicitud Autorizada...',
                    });
                }
            }, this),
        });
    },

    rechazaAsignacion: function (idCuenta, idAsesorSolicita, comentarios) {
        console.log("RECHAZA ASIGNACION");
        var argsRechaza = {
            "id_cuenta": idCuenta,
            "id_asesor_solicita": idAsesorSolicita,
            "comentarios": comentarios
        };
        console.log(argsRechaza);
        app.api.call("create", app.api.buildURL("rechazoAsignacionCuenta", null, null, argsRechaza), null, {
            success: _.bind(function (response) {
                console.log(response);
                if (response.status == '200') {
                    app.alert.show('alert_rechaza_asignacion', {
                        level: 'success',
                        messages: 'Solicitud Rechazada...',
                    });
                }

            }, this),
        });
    }
})
