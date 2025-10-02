({
    extendsFrom: 'BaseView',
    events: {
        'submit #comentarioForm': 'enviarComentario'
    },

    initialize: function (options) {
        this._super("initialize", [options]);

        //Extrae los parametros
        var hashParams = window.location.hash.split("?")[1];
        var urlParams = new URLSearchParams(hashParams);
        this.idBL = urlParams.get('id');
        this.accion = urlParams.get('accion');
        this.puedeAprobar= false;
        this.acepta = 0;
        this.rechaza = 0;

        // Obtener el timestamp actual
        var timestamp = Date.now(); // milisegundos desde 1970-01-01
        // Convertir a objeto Date
        var dateObj = new Date(timestamp);
        // Formato SugarCRM DATETIME (YYYY-MM-DD HH:mm:ss)
        var formattedDateTime = app.date(dateObj).format('YYYY-MM-DD[T]HH:mm:ssZ');

        console.log('ID recibido:', this.idBL);
        console.log('Acción recibida:', this.accion);

        // Validaciones de parámetros
        if ((this.idBL == null || this.id == '') || (this.accion == null || this.accion == '')) {
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
        var approvalList = app.lang.getAppListStrings('aprobador_reasignacion_bl_list');

        if (this.idBL != '') {
            try {
                var url = app.api.buildURL('lev_Backlog/' + this.idBL, null, null,);
                app.api.call('GET', url, {}, {
                    success: _.bind(function (data) {
                        if (data != null) {
                            this.acepta = this.accion === 'aceptar' ? 1 : 0;
                            this.rechaza = this.accion === 'rechazar' ? 1 : 0;
                            var estatus_backlog_c = data.estatus_backlog_c;
                            var aprueba_reactivacion = data.aprueba_reactivacion_c;
                            var aprobador_dir = data.aprobador_reactivacion_c;

                            console.log("estatus_backlog_c", estatus_backlog_c);
                            console.log("aprueba_reactivacion ", aprueba_reactivacion);

                            //VALIDA PERMISOS DE APROBACION
                            if(Object.values(approvalList).includes(app.user.id) || aprobador_dir == app.user.id){
                                this.puedeAprobar = true;
                            }

                            if(this.puedeAprobar  && this.rechaza){
                                this.puedeAprobarRechazo = true;
                            }

                            if (!this.puedeAprobar) {
                                this.mostrarMensaje("No tiene permisos para realizar esta acción", "error");
                                alert("No tiene permisos para realizar esta acción");
                                // Redirigir después de 1 segundo
                                _.delay(function () {
                                    app.router.navigate("#lev_Backlog", { trigger: true });
                                }, 1000);
                                return;

                            } else {
                                //ACCION ACEPTAR
                                if (this.acepta) {
                                    //Valida estatus actual y desbloque BL
                                    if (estatus_backlog_c == '6') {
                                        if (aprueba_reactivacion == 'RECHAZAR') {

                                            alert("La solicitud fue rechazada previamente");
                                            // Redirigir después de 2 segundos
                                            _.delay(function () {
                                                app.router.navigate("#lev_Backlog", { trigger: true });
                                            }, 2000);
                                            return;

                                        } else {
                                            // Actualizar el BL
                                            var actualizaBL = {
                                                idRegistro : data.id,
                                                aprueba_reactivacion_c: 'ACEPTAR',
                                                estatus_backlog_c: '1',
                                                motivo_declinacion_c: '',
                                                fecha_reactivacion_c: formattedDateTime
                                            };
                                            this.decisionCambioBL(actualizaBL);
                                        }
                                    } else {
                                        alert("El Backlog no se encuentra en proceso de reactivación actualmente");
                                        // Redirigir después de 2 segundos
                                        _.delay(function () {
                                            app.router.navigate("#lev_Backlog", { trigger: true });
                                        }, 2000);
                                        return;
                                    }

                                } else {
                                    //Valida estatus actual y rechaza BL
                                    if(estatus_backlog_c == '6' && aprueba_reactivacion != 'RECHAZAR'){                                        
                                        // Actualizar el BL
                                        var updatePayload = {
                                            idRegistro : data.id,
                                            aprueba_reactivacion_c: 'RECHAZAR',
                                            estatus_backlog_c: '2',
                                            fecha_reactivacion_neg_c: formattedDateTime
                                        };
                                        this.decisionCambioBL(updatePayload);
                                        
                                    } else if (estatus_backlog_c == '2' && aprueba_reactivacion == 'RECHAZAR'){
                                        alert("El Backlog ya ha sido rechazado anteriormente");
                                        // Redirigir después de 2 segundos
                                        _.delay(function () {
                                            app.router.navigate("#lev_Backlog", { trigger: true });
                                        }, 2000);
                                        return;
                                    } else {
                                        alert("El Backlog no se encuentra bloqueado actualmente");
                                        // Redirigir después de 2 segundos
                                        _.delay(function () {
                                            app.router.navigate("#lev_Backlog", { trigger: true });
                                        }, 2000);
                                        return;
                                    }
                                }
                            }
                        } else {
                            alert("El Backlog no existe en el sistema, favor de validar");
                            // Redirigir después de 2 segundos
                            _.delay(function () {
                                app.router.navigate("#lev_Backlog", { trigger: true });
                            }, 2000);
                            return;
                        }
                    }, this)
                });
            } catch (err) {
                console.log(err.message);
                // Redirigir después de 2 segundos
                _.delay(function () {
                    app.router.navigate("#lev_Backlog", { trigger: true });
                }, 2000);
                return;
            }
        }

        this._render();
    },

    decisionCambioBL: function (idBL) {
        console.log("...ACEPTA REACTIVACION BACKLOG...");
        this.msgExitoso = 0;
        this.msgRechazado = 0;

        app.alert.show('procesa_acepta_reactivacion_bl', {
            level: 'process',
            title: 'Procesando',
        });

        app.api.call("create", app.api.buildURL("notificaReactivaBL", null, null, idBL), null, {
            success: _.bind(function (resBLnse) {
                app.alert.dismiss('procesa_acepta_reactivacion_bl');
                if (resBLnse.status == '200') {
                    if(idBL.aprueba_reactivacion_c=="ACEPTAR"){
                        this.msgExitoso = 1;
                    }else if(idBL.aprueba_reactivacion_c=="RECHAZAR"){
                        this.msgRechazado = 1;
                    }
                    
                    app.alert.show('alert_autoriza_reasignacion_bl', {
                        level: 'success',
                        messages: 'Reasignación Atendida. El Backlog fue '+resBLnse.accion,
                    });
                    this.render(); // Asegura que el mensaje aparezca en la vista
                    // Redirigir después de 2 segundos
                    _.delay(function () {
                        app.router.navigate("#lev_Backlog", { trigger: true });
                    }, 2000);
                } else {
                    this.msgExitoso = 0;
                    app.alert.show('alert_autoriza_reasignacion_bl', {
                        level: 'error',
                        messages: 'Error en el Servicio Reactivación Backlog.',
                    });
                    // Redirigir después de 2 segundos
                    _.delay(function () {
                        app.router.navigate("#lev_Backlog", { trigger: true });
                    }, 2000);
                }
            }, this),
        });
    },

    rechazaCambioOrigen: function (idBL) {
        console.log("...RECHAZA REACTIVACION BACKLOG...");
        this.msgRechazado = 0;

        app.alert.show('procesa_acepta_reactivacion_bl', {
            level: 'process',
            title: 'Procesando',
        });

        var argsRechaza = {
            "id_BL": idBL,
            "accion": 'Rechazada'
        };
        app.api.call("create", app.api.buildURL("notificaReactivaBL", null, null, argsRechaza), null, {
            success: _.bind(function (resBLnse) {
                app.alert.dismiss('procesa_acepta_reactivacion_bl');
                if (resBLnse.status == '200') {
                    this.msgRechazado = true;
                    app.alert.show('alert_rechaza_cambio_origen', {
                        level: 'success',
                        messages: 'Reasignación Rechazado...',
                    });

                    this.render(); // Asegura que el mensaje aparezca en la vista

                    // Redirigir después de 2 segundos
                    _.delay(function () {
                        app.router.navigate("#lev_Backlog", { trigger: true });
                    }, 2000);

                } else {
                    this.msgRechazado = false;
                    app.alert.show('error_rechaza_reasignacion_bl', {
                        level: 'error',
                        messages: 'Error en el Servicio Reasignacion Backlog.',
                    });
                    // Redirigir después de 1 segundos
                    _.delay(function () {
                        app.router.navigate("#lev_Backlog", { trigger: true });
                    }, 1000);
                }
            }, this),
        });
    },

    mostrarMensaje: function (texto, tiBL) {
        var mensajeDiv = this.$('#mensaje');
        mensajeDiv.removeClass().addClass('message ' + tiBL).text(texto);
    },

    _render: function () {
        this._super('_render');
    }
})
