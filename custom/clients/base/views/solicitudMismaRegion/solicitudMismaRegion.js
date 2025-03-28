({
    extendsFrom: 'BaseView',  

    events: {
        'submit #comentarioForm': 'enviarComentario'
    },

    initialize: function(options){
        this._super('initialize', [options]);
        this.resultado = 0;
      
        //Extraer la parte después del #
        var hashParams = window.location.hash.split("?")[1];
        var urlParams = new URLSearchParams(hashParams);

        this.id = urlParams.get('id') || 'No recibido';
        this.accion = urlParams.get('accion') || 'No recibido';

        console.log('ID recibido:', this.id);
        console.log('Acción recibida:', this.accion);
        // Mostrar el parámetro en la consola

        this._render();
        
    },   

    enviarComentario: function(event) {
        event.preventDefault(); // Evita recargar la página

        var comentarios = this.$('#comentarios').val().trim();

        // Validación de longitud de comentario
        if (comentarios.length < 150 || comentarios.length > 500) {
            this.mostrarMensaje("El comentario debe tener entre 150 y 500 caracteres.", "error");
            return;
        }

        var data = {
            id: this.id,
            accion: this.accion,
            comentarios: comentarios
        };

        // Llamada al API
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
                            this.aceptaAsignacion(this.idCuenta, this.idAsesorSolicita, comentarios);
                        } else {
                            this.rechazaAsignacion(this.idCuenta, this.idAsesorSolicita, comentarios);
                        }
                    }
                }, this) 
            });
        }
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
    },

    mostrarMensaje: function(texto, tipo) {
        var mensajeDiv = this.$('#mensaje');
        mensajeDiv.removeClass().addClass('message ' + tipo).text(texto);
    },

      _render: function () {
        this._super('_render');

        // Actualizar el contenido en el HTML
        this.$('.id-container').text(this.id);
        this.$('.accion-container').text(this.accion);
        this.$('.aceptacion-container').text(this.aceptacion ? 1 : 0);
        this.$('.rechazo-container').text(this.rechazo ? 1 : 0);
    }
    
})
