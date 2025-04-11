({
    extendsFrom: 'BaseView',

    initialize: function (options) {
        this._super("initialize", [options]);

        //Extrae los parametros
        var hashParams = window.location.hash.split("?")[1];
        var urlParams = new URLSearchParams(hashParams);
        this.idCuenta = urlParams.get('id');
        this.accion = urlParams.get('accion');

        this.acepta = 0;
        this.rechaza = 0;

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

        if (this.idCuenta != '') {
            try {
                var url = app.api.buildURL('tct02_Resumen/' + this.idCuenta, null, null,);
                app.api.call('GET', url, {}, {
                    success: _.bind(function (data) {
                        if (data != '') {
                            this.asignacionActiva = data.asignacion_activa_c;
                            this.idDirectorRegionalAnteriorOrigen = data.id_director_region_aprobar_c;
                            this.idDirectorRegionalSolicitaDestino = data.id_director_region_aprobar2_c;
                            this.aprobacionDirectores = parseInt(data.aprobacion_directores_c) || 0;
                            this.idAsesorSolicita = data.id_asesor_solicita_c;
                            this.acepta = this.accion === 'aceptar' ? 1 : 0;
                            this.rechaza = this.accion === 'rechazar' ? 1 : 0;
                            this.esDirRegAnteriorOrigen = this.idDirectorRegionalAnteriorOrigen === app.user.id ? 1 : 0;
                            this.esDirRegSolicitaDestino = this.idDirectorRegionalSolicitaDestino === app.user.id ? 1 : 0;
                            this.apruebaAsignacion = 0;

                            console.log("ID USUARIO EN SESION ", app.user.id);
                            console.log("APROBACION DIRECTORES " + this.aprobacionDirectores);
                            console.log("ID DIR REG ANTERIOR ORIGEN " + this.idDirectorRegionalAnteriorOrigen + " " + this.esDirRegAnteriorOrigen);
                            console.log("ID DIR REG SOLICITA DESTINO " + this.idDirectorRegionalSolicitaDestino + " " + this.esDirRegSolicitaDestino);                            

                            // Valida las aprobaciones de los directores 
                            if (this.esDirRegAnteriorOrigen || this.esDirRegSolicitaDestino) {         
                                if (this.aprobacionDirectores === 1) {
                                    if (this.esDirRegAnteriorOrigen) {
                                        // Ya aprobó antes
                                        app.alert.show('aprobado_previamente', {
                                            level: 'warning',
                                            messages: 'La solicitud ya fue atendida por usted previamente.',
                                            autoClose: false
                                        });
                                        this.apruebaAsignacion = 0;

                                        // Redirigir después de 2 segundos
                                        _.delay(function () {
                                            app.router.navigate("#Accounts", { trigger: true });
                                        }, 2000);
                                        return;
                                    } else if (this.esDirRegSolicitaDestino) {
                                        // Segundo director válido
                                        this.apruebaAsignacion = 1;
                                    }
                                } else if (this.aprobacionDirectores === 2) {
                                    if (this.esDirRegSolicitaDestino) {
                                        // Ya aprobó antes
                                        app.alert.show('aprobado_previamente', {
                                            level: 'warning',
                                            messages: 'La solicitud ya fue atendida por usted previamente.',
                                            autoClose: false
                                        });
                                        this.apruebaAsignacion = 0;

                                        // Redirigir después de 2 segundos
                                        _.delay(function () {
                                            app.router.navigate("#Accounts", { trigger: true });
                                        }, 2000);
                                        return;
                                    } else if (this.esDirRegAnteriorOrigen) {
                                        // Segundo director válido
                                        this.apruebaAsignacion = 1;
                                    }
                                }

                                //Valida las aprobaciones y asigna el valor a aprobacion_directores
                                if (this.aprobacionDirectores === 0) {
                                    if (this.esDirRegAnteriorOrigen) {
                                        this.valorDirRegDecide = 1;
                                    } else if (this.esDirRegSolicitaDestino) {
                                        this.valorDirRegDecide = 2;
                                    }
                                } else if (this.aprobacionDirectores === 1) {
                                    // Ya aprobó el director de origen, ahora entra el destino
                                    if (this.esDirRegSolicitaDestino) {
                                        this.valorDirRegDecide = 2;
                                    }
                                } else if (this.aprobacionDirectores === 2) {
                                    // Ya aprobó el director de destino, ahora entra el origen
                                    if (this.esDirRegAnteriorOrigen) {
                                        this.valorDirRegDecide = 1;
                                    }
                                } 

                            } else {                                
                                this.apruebaAsignacion = 0;
                            }
                            
                            console.log("APRUEBA ASIGNACION " + this.apruebaAsignacion);
                            console.log("VALOR DIRECTOR QUIEN DECIDE " + this.valorDirRegDecide);
                            //VALIDA PERMISOS DE APROBACION
                            this.puedeAprobar = Object.values(approvalList).includes(app.user.id) || this.idDirectorRegionalAnteriorOrigen === app.user.id || this.idDirectorRegionalSolicitaDestino === app.user.id;

                            if (!this.puedeAprobar) {
                                this.mostrarMensaje("No tiene permisos para realizar esta acción", "error");
                                alert("No tiene permisos para realizar esta acción");
                                // Redirigir después de 2 segundos
                                _.delay(function () {
                                    app.router.navigate("#Accounts", { trigger: true });
                                }, 1000);
                                return;

                            } else if (!this.asignacionActiva && !this.idDirectorRegionalAnteriorOrigen && !this.idAsesorSolicita && !this.idDirectorRegionalSolicitaDestino) {

                                this.mostrarMensaje("La cuenta ya fue atendida.", "error");
                                alert("La cuenta ya fue atendida.");

                                // Redirigir después de 2 segundos
                                _.delay(function () {
                                    app.router.navigate("#Accounts", { trigger: true });
                                }, 2000);
                                return;

                            } else {
                                if (this.acepta) {
                                    this.aceptaAsignacion(this.idCuenta, this.idAsesorSolicita, '', this.valorDirRegDecide, this.apruebaAsignacion);
                                } else {
                                    this.rechazaAsignacion(this.idCuenta, this.idAsesorSolicita, '');
                                }
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

    aceptaAsignacion: function (idCuenta, idAsesorSolicita, comentarios, valorDirRegDecide, apruebaAsignacion) {
        console.log("ACEPTA ASIGNACION");
        this.msgExitoso = 0;

        app.alert.show('procesa_acepta_asignacion', {
            level: 'process',
            title: 'Procesando',
        });

        var argsAcepta = {
            "id_cuenta": idCuenta,
            "id_asesor_solicita": idAsesorSolicita,
            "comentarios": comentarios,
            "valor_director_decide": valorDirRegDecide,
            "aprueba_asignacion": apruebaAsignacion
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
                    // Redirigir después de 2 segundos
                    _.delay(function () {
                        app.router.navigate("#Accounts", { trigger: true });
                    }, 2000);
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
