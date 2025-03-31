({
    extendsFrom: 'BaseView',

    events: {
        'submit #comentarioForm': 'enviarComentario'
    },

    initialize: function (options) {
        this._super('initialize', [options]);
        this.resultado = 0;

        //Extrae los parametros
        var hashParams = window.location.hash.split("?")[1];
        var urlParams = new URLSearchParams(hashParams);

        this.idCuenta = urlParams.get('id');
        this.accion = urlParams.get('accion');

        console.log('ID recibido:', this.idCuenta);
        console.log('Acción recibida:', this.accion);

        // Validaciones de parámetros
        if ((this.idCuenta == null || this.id == '') || (this.accion == null || this.accion == '')) {
            this.mostrarMensaje("Valores faltantes para petición", "error");
            return;
        }
        // Validación de acción
        if (this.accion !== 'aceptar' && this.accion !== 'rechazar') {
            this.mostrarMensaje("La acción indicada no es válida", "error");
            return;
        }
        // Validar sesión de usuario
        if (!app.user || !app.user.id) {
            this.mostrarMensaje("Se requiere iniciar sesión", "error");
            return;
        }

        // Validar si el usuario tiene permisos
        var approvalList = app.lang.getAppListStrings('ids_aprobador_reasignacion_director_list');
        //this.puedeAprobar = approvalList.includes(app.user.id) || (this.idDirectorRegional === app.user.id);
        // Llamada al API
        if (this.idCuenta != '') {
            try {
                var url = app.api.buildURL('tct02_Resumen/' + this.idCuenta, null, null,);
                app.api.call('GET', url, {}, {
                    success: _.bind(function (data) {
                        if (data != '') {
                            this.asignacionActiva = data.asignacion_activa_c;
                            this.idDirectorRegional = data.id_director_region_aprobar_c;
                            this.idAsesorSolicita = data.id_asesor_solicita_c;

                            this.puedeAprobar = Object.values(approvalList).includes(app.user.id) || (this.idDirectorRegional === app.user.id);
                            if (!this.puedeAprobar) {
                                this.mostrarMensaje("No tiene permisos para realizar esta acción", "error");
                                alert("No tiene permisos para realizar esta acción");
                                // Redirigir al módulo de Cuentas
                                app.router.navigate("#Accounts", { trigger: true });
                                return;

                            } else if (!this.asignacionActiva && !this.idDirectorRegional && !this.idAsesorSolicita) {
                                // Ocultar el formulario
                                this.puedeAprobar = false;
                                this.render();

                                this.mostrarMensaje("La cuenta ya fue atendida.", "error");
                                alert("La cuenta ya fue atendida.");

                                // Redirigir después de 2 segundos
                                _.delay(function () {
                                    app.router.navigate("#Accounts", { trigger: true });
                                }, 2000);
                                return;
                            
                            } else {
                                this._render();
                            }
                        } else {
                            alert("La cuenta no existe, favor de validar");
                            // Redirigir después de 2 segundos
                            _.delay(function () {
                                app.router.navigate("#Accounts", { trigger: true });
                            }, 2000);
                            return;
                        }
                    }, this)
                });
            } catch (err) {
                console.log(err.message);
                // Redirigir después de 2 segundos
                _.delay(function () {
                    app.router.navigate("#Accounts", { trigger: true });
                }, 2000);
                return;
            }
        }
        this._render();
    },

    enviarComentario: function (event) {
        this.mostrarMensaje("", "");
        event.preventDefault(); // Evita recargar la página

        var comentarios = this.$('#comentarios').val().trim();

        // Validación de longitud de comentario
        if (comentarios.length < 150 || comentarios.length > 500) {
            this.mostrarMensaje("El comentario debe tener entre 150 y 500 caracteres.", "error");
            return;
        } else {
            this.mostrarMensaje("", "");
        }

        // Ocultar el formulario
        this.puedeAprobar = false;
        this.render();

        // Definir aceptación y rechazo como booleanos
        this.acepta = this.accion === 'aceptar' ? 1 : 0;
        this.rechaza = this.accion === 'rechazar' ? 1 : 0;

        if (this.acepta) {
            this.aceptaAsignacion(this.idCuenta, this.idAsesorSolicita, comentarios);
        } else {
            this.rechazaAsignacion(this.idCuenta, this.idAsesorSolicita, comentarios);
        }

    },

    aceptaAsignacion: function (idCuenta, idAsesorSolicita, comentarios) {
        console.log("ACEPTA ASIGNACION");
        this.msgExitoso = 0;

        app.alert.show('procesa_acepta_asignacion', {
            level: 'process',
            title: 'Procesando',
        });

        var argsAcepta = {
            "id_cuenta": idCuenta,
            "id_asesor_solicita": idAsesorSolicita,
            "comentarios": comentarios
        };
        app.api.call("create", app.api.buildURL("autorizaAsignacionCuenta", null, null, argsAcepta), null, {
            success: _.bind(function (response) {
                app.alert.dismiss('procesa_acepta_asignacion');
                if (response.status == '200') {
                    this.msgExitoso = 1;
                    app.alert.show('alert_autoriza_asignacion', {
                        level: 'success',
                        messages: 'Solicitud Autorizada...',
                    });

                    this.render(); // Asegura que el mensaje aparezca en la vista

                    // Redirigir después de 2 segundos
                    _.delay(function () {
                        app.router.navigate("#Accounts", { trigger: true });
                    }, 2000);

                } else {
                    this.msgExitoso = 0;
                    app.alert.show('error_rechaza_asignacion', {
                        level: 'error',
                        messages: 'Error en el servicio de Solicitud Asignación.',
                    });
                    // Redirigir después de 1 segundos
                    _.delay(function () {
                        app.router.navigate("#Accounts", { trigger: true });
                    }, 1000);
                }
            }, this),
        });
    },

    rechazaAsignacion: function (idCuenta, idAsesorSolicita, comentarios) {
        console.log("RECHAZA ASIGNACION");
        this.msgRechazado = 0;

        app.alert.show('procesa_rechazo_asignacion', {
            level: 'process',
            title: 'Procesando',
        });

        var argsRechaza = {
            "id_cuenta": idCuenta,
            "id_asesor_solicita": idAsesorSolicita,
            "comentarios": comentarios
        };
        app.api.call("create", app.api.buildURL("rechazoAsignacionCuenta", null, null, argsRechaza), null, {
            success: _.bind(function (response) {
                app.alert.dismiss('procesa_rechazo_asignacion');
                if (response.status == '200') {
                    this.msgRechazado = true;
                    app.alert.show('alert_rechaza_asignacion', {
                        level: 'success',
                        messages: 'Solicitud Rechazada...',
                    });

                    this.render(); // Asegura que el mensaje aparezca en la vista

                    // Redirigir después de 2 segundos
                    _.delay(function () {
                        app.router.navigate("#Accounts", { trigger: true });
                    }, 2000);

                } else {
                    this.msgRechazado = false;
                    app.alert.show('error_rechaza_asignacion', {
                        level: 'error',
                        messages: 'Error en el servicio de Solicitud Asignación.',
                    });
                    // Redirigir después de 1 segundos
                    _.delay(function () {
                        app.router.navigate("#Accounts", { trigger: true });
                    }, 1000);
                }

            }, this),
        });
    },

    mostrarMensaje: function (texto, tipo) {
        var mensajeDiv = this.$('#mensaje');
        mensajeDiv.removeClass().addClass('message ' + tipo).text(texto);
    },

    _render: function () {
        this._super('_render');
    }

})
