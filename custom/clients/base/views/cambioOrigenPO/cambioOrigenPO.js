({
    extendsFrom: 'BaseView',

    initialize: function (options) {
        this._super("initialize", [options]);

        //Extrae los parametros
        var hashParams = window.location.hash.split("?")[1];
        var urlParams = new URLSearchParams(hashParams);
        this.idPO = urlParams.get('id');
        this.accion = urlParams.get('accion');

        this.acepta = 0;
        this.rechaza = 0;

        console.log('ID recibido:', this.idPO);
        console.log('Acción recibida:', this.accion);

        // Validaciones de parámetros
        if ((this.idPO == null || this.id == '') || (this.accion == null || this.accion == '')) {
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
        var approvalList = app.lang.getAppListStrings('aprueba_cambio_origen_po_list');

        if (this.idPO != '') {
            try {
                var url = app.api.buildURL('Prospects/' + this.idPO, null, null,);
                app.api.call('GET', url, {}, {
                    success: _.bind(function (data) {
                        if (data != '') {
                            this.acepta = this.accion === 'aceptar' ? 1 : 0;
                            this.rechaza = this.accion === 'rechazar' ? 1 : 0;
                            var origenBloquedado = data.origen_bloqueado_c;
                            var apruebaCambioOrigen = data.aprueba_cambio_origen_c;

                            console.log("origenBloquedado ", origenBloquedado);
                            console.log("apruebaCambioOrigen ", apruebaCambioOrigen);

                            //VALIDA PERMISOS DE APROBACION
                            this.puedeAprobar = Object.values(approvalList).includes(app.user.id);

                            if (!this.puedeAprobar) {
                                this.mostrarMensaje("No tiene permisos para realizar esta acción", "error");
                                alert("No tiene permisos para realizar esta acción");
                                // Redirigir después de 1 segundo
                                _.delay(function () {
                                    app.router.navigate("#Prospects", { trigger: true });
                                }, 1000);
                                return;

                            } else {
                                //ACCION ACEPTAR
                                if (this.acepta) {
                                    //Valida estatus actual y desbloque PO
                                    if (origenBloquedado) {
                                        if (apruebaCambioOrigen == 'RECHAZAR') {

                                            alert("La solicitud fue rechazada previamente");
                                            // Redirigir después de 2 segundos
                                            _.delay(function () {
                                                app.router.navigate("#Prospects", { trigger: true });
                                            }, 2000);
                                            return;

                                        } else {
                                            // Actualizar el PO
                                            var actualizaPO = {
                                                origen_bloqueado_c: '0',
                                                aprueba_cambio_origen_c: 'Aceptar'
                                            };
                                            var updateUrl = app.api.buildURL('Prospects/' + this.idPO, null, null);
                                            app.api.call('update', updateUrl, actualizaPO, {
                                                success: _.bind(function (response) {
                                                    //INICIA PROCESO DE ACEPTACION
                                                    this.aceptaCambioOrigen(this.idPO, app.user.id);

                                                }, this),
                                                error: function (error) {
                                                    console.error('Error al actualizar el Prospecto:', error);
                                                    alert("Ocurrió un error al actualizar el Prospecto");
                                                    // Redirigir después de 2 segundos
                                                    _.delay(function () {
                                                        app.router.navigate("#Prospects", { trigger: true });
                                                    }, 2000);
                                                    return;
                                                }
                                            });

                                        }
                                    } else {
                                        alert("El PO no se encuentra bloqueado actualmente");
                                        // Redirigir después de 2 segundos
                                        _.delay(function () {
                                            app.router.navigate("#Prospects", { trigger: true });
                                        }, 2000);
                                        return;
                                    }

                                } else {
                                    //Valida estatus actual y rechaza PO
                                    if(origenBloquedado && apruebaCambioOrigen != 'RECHAZAR'){                                        
                                        // Actualizar el PO
                                        var updatePayload = {
                                            aprueba_cambio_origen_c: 'Rechazar'
                                        };

                                        var updateUrl = app.api.buildURL('Prospects/' + this.idPO, null, null);
                                        app.api.call('update', updateUrl, updatePayload, {
                                            success: _.bind(function (response) {
                                                //INICIA PROCESO DE RECHAZO
                                                this.rechazaCambioOrigen(this.idPO, app.user.id);

                                            }, this),
                                            error: function (error) {
                                                console.error('Error al actualizar el Prospecto:', error);
                                                alert("Ocurrió un error al actualizar el Prospecto");
                                                // Redirigir después de 2 segundos
                                                _.delay(function () {
                                                    app.router.navigate("#Prospects", { trigger: true });
                                                }, 2000);
                                                return;
                                            }
                                        });

                                    } else if (origenBloquedado && apruebaCambioOrigen == 'Rechazar'){
                                        alert("El PO ya ha sido rechazado anteriormente");
                                        // Redirigir después de 2 segundos
                                        _.delay(function () {
                                            app.router.navigate("#Prospects", { trigger: true });
                                        }, 2000);
                                        return;
                                    } else {
                                        alert("El PO no se encuentra bloqueado para cambio de Origen");
                                        // Redirigir después de 2 segundos
                                        _.delay(function () {
                                            app.router.navigate("#Prospects", { trigger: true });
                                        }, 2000);
                                        return;
                                    }
                                }
                            }
                        } else {
                            alert("El PO no existe en el sistema, favor de validar");
                            // Redirigir después de 2 segundos
                            _.delay(function () {
                                app.router.navigate("#Prospects", { trigger: true });
                            }, 2000);
                            return;
                        }
                    }, this)
                });
            } catch (err) {
                console.log(err.message);
                // Redirigir después de 2 segundos
                _.delay(function () {
                    app.router.navigate("#Prospects", { trigger: true });
                }, 2000);
                return;
            }
        }

        this._render();
    },

    aceptaCambioOrigen: function (idPO, idAsesor) {
        console.log("...ACEPTA_CAMBIO_ORIGEN...");
        this.msgExitoso = 0;

        app.alert.show('procesa_acepta_cambio_origen', {
            level: 'process',
            title: 'Procesando',
        });

        var argsAcepta = {
            "id_po": idPO,
            "id_usuario": idAsesor,
            "accion": 'Aceptada'
        };
        app.api.call("create", app.api.buildURL("notificaEdicionOrigenPO", null, null, argsAcepta), null, {
            success: _.bind(function (response) {
                app.alert.dismiss('procesa_acepta_cambio_origen');
                if (response.status == '200') {
                    this.msgExitoso = 1;
                    app.alert.show('alert_autoriza_cambio_origen', {
                        level: 'success',
                        messages: 'Cambio de Origen Autorizado...',
                    });
                    this.render(); // Asegura que el mensaje aparezca en la vista
                    // Redirigir después de 2 segundos
                    _.delay(function () {
                        app.router.navigate("#Prospects", { trigger: true });
                    }, 2000);
                } else {
                    this.msgExitoso = 0;
                    app.alert.show('error_autoriza_cambio_origen', {
                        level: 'error',
                        messages: 'Error en el Servicio Cambio de Origen Notificación PO.',
                    });
                    // Redirigir después de 2 segundos
                    _.delay(function () {
                        app.router.navigate("#Prospects", { trigger: true });
                    }, 2000);
                }
            }, this),
        });
    },

    rechazaCambioOrigen: function (idPO, idAsesor) {
        console.log("...RECHAZA_CAMBIO_ORIGEN...");
        this.msgRechazado = 0;

        app.alert.show('procesa_rechazo_cambio_origen', {
            level: 'process',
            title: 'Procesando',
        });

        var argsRechaza = {
            "id_po": idPO,
            "id_usuario": idAsesor,
            "accion": 'Rechazada'
        };
        app.api.call("create", app.api.buildURL("notificaEdicionOrigenPO", null, null, argsRechaza), null, {
            success: _.bind(function (response) {
                app.alert.dismiss('procesa_rechazo_cambio_origen');
                if (response.status == '200') {
                    this.msgRechazado = true;
                    app.alert.show('alert_rechaza_cambio_origen', {
                        level: 'success',
                        messages: 'Cambio de Origen Rechazado...',
                    });

                    this.render(); // Asegura que el mensaje aparezca en la vista

                    // Redirigir después de 2 segundos
                    _.delay(function () {
                        app.router.navigate("#Prospects", { trigger: true });
                    }, 2000);

                } else {
                    this.msgRechazado = false;
                    app.alert.show('error_rechaza_cambio_origen', {
                        level: 'error',
                        messages: 'Error en el Servicio Cambio de Origen Notificación PO.',
                    });
                    // Redirigir después de 1 segundos
                    _.delay(function () {
                        app.router.navigate("#Prospects", { trigger: true });
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
