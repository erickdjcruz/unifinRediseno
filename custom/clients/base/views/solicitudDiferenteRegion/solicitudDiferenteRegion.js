({
    extendsFrom: 'BaseView',

    initialize: function (options) {
        this._super("initialize", [options]);

        //Extrae los parametros
        var hashParams = window.location.hash.split("?")[1];
        var urlParams = new URLSearchParams(hashParams);
        this.idCuenta = urlParams.get('id');
        this.accion = urlParams.get('accion');
        this.acepta = this.accion === 'aceptar' ? 1 : 0;
        this.rechaza = this.accion === 'rechazar' ? 1 : 0;

        console.log('ID recibido:', this.idCuenta);
        console.log('Acción recibida:', this.accion);

        // Validaciones de parámetros
        if ((this.idCuenta==null ||this.id == '' )|| (this.accion==null || this.accion== '')) {
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
        this.puedeAprobar = Object.values(approvalList).includes(app.user.id) || (this.idDirectorRegional === app.user.id);

        
        if (!this.puedeAprobar) {
            this.mostrarMensaje("No tiene permisos para realizar esta acción", "error");
            alert("No tiene permisos para realizar esta acción");
            return;
        }

        if (this.idCuenta != '') {
            var url = app.api.buildURL('tct02_Resumen/' + this.idCuenta, null, null,);
            app.api.call('GET', url, {}, {
                success: _.bind(function (data) {
                    if (data != '') {
                        this.asignacionActiva = data.asignacion_activa_c;
                        this.idDirectorRegional = data.id_director_region_aprobar_c;
                        this.idAsesorSolicita = data.id_asesor_solicita_c;

                        if (this.acepta) {
                            this.aceptaAsignacion(this.idCuenta, this.idAsesorSolicita, '');
                        } else {
                            this.rechazaAsignacion(this.idCuenta, this.idAsesorSolicita, '');
                        }
                    }
                }, this)
            });
        }
        this._render();
    },

    _render: function () {
        this._super('_render');
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
                    app.alert.show('error_autoriza_asignacion', {
                        level: 'error',
                        messages: 'Error en el servicio de Solicitud Asignación.',
                    });
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
                }
            }, this),
        });
    }
})
