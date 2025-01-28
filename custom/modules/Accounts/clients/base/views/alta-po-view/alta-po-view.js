({
    className: 'alta-po-view',

    events: {
        'click #btn-cancelar': 'cancelarAltaPO',
        'click #btn-generar-po': 'altaPO',
        'click #btn-solicitar-aprobacion': 'enviarAprobacion',
        'click #btn-generar-po-manual': 'openDrawerPO',
        'click .closeModalAltaPO': 'cancelarAltaPO',
        'change .cbRelaciones': 'selectCheckBox',

        'click #btn-rechazar-creacion': 'rechazarPeticionCrearPO',
        'click #btn-aprobar-creacion': 'aprobarPeticionCrearPO',
    },

    initialize: function (options) {
        this._super("initialize", [options]);

        var idCuenta = options.context.get('model').id;
        this.relacionesCuenta = undefined;
        this.showRelaciones = true;
        this.esDirectorComercial = false;

        if( idCuenta !== undefined && idCuenta !== ""){
            this.getRelacionesSinPO( idCuenta );
        }
    },

    getRelacionesSinPO: function( idCuenta ){

        contextAltaPO = this;
        //Antes de mostrar relaciones, validamos el valor de la bandera para saber cuál es el contenido que se debe de mostrar
        app.alert.show('validaPoCreado', {
            level: 'process',
            title: 'Cargando...',
        });

        var url = app.api.buildURL('tct02_Resumen/' + idCuenta, null, null,);
        app.api.call('GET', url, {}, {
            success: function (data) {
                app.alert.dismiss('validaPoCreado');

                //Si el usuario logueado es igual al Director comercial y además ya se creó el PO, se muestra pantalla con botones para aprobar y rechazar
                if(data.po_creado_c == 1 && data.id_dir_comercial_aprueba_c == App.user.id ){
                    contextAltaPO.esDirectorComercial = true;

                    contextAltaPO.render();

                }else if( data.po_creado_c == 1 && data.id_dir_comercial_aprueba_c !== App.user.id ){ 
                    //Se muestra pantalla para ingresar motivo para crear PO en caso de que ya se haya creado un registro de PO anteriormente y 
                    //el usuario logueado no sea igual al director comercial

                    contextAltaPO.showRelaciones = false;

                    contextAltaPO.render();

                }else{

                    app.alert.show('getRelacionesSinPO', {
                        level: 'process',
                        title: 'Cargando...',
                    });
            
                    var url = app.api.buildURL('ObtenerRelacionesAltaPO/' + idCuenta, null, null,);
                    app.api.call('GET', url, {}, {
                        success: function (data) {
                            app.alert.dismiss('getRelacionesSinPO');

                            if( data.length > 0 ){

                                contextAltaPO.relacionesCuenta = data;
                
                                contextAltaPO.render();
                            }else{
                                app.alert.show('sinRelaciones', {
                                    level: 'info',
                                    messages: 'Este registro no cuenta con Relaciones que <b>NO</b> existan en Público Objetivo',
                                    autoClose: true
                                });

                                $('.modal-footer-alta-po').hide();
                                $('.modal-footer-link').attr('style', 'margin: 40px');
                                $('.mainTableAltaPO').hide()
                                //contextAltaPO.cancelarAltaPO();
                            }
                        }
                    });

                }
                
            }
        });
    },

    cancelarAltaPO: function(){
        var modal = $('#myModalAltaPO');
        if (modal) {
            modal.hide();
        }

        app.drawer.close();

    },

    altaPO: function(){
        console.log("ALTA PO");
        //Validar que se haya seleccionado un checkbox
        var idRelacionSeleccionada = this.getIdRelacionSeleccionada();
        var idAsesorCuentaPrincipal = contextAltaPO.model.get('user_id_c');

        if( idRelacionSeleccionada == "" ){

            app.alert.show('sinRelacionSeleccionada', {
                level: 'error',
                messages: 'Favor de seleccionar un registro',
                autoClose: true
            });

        }else{

            console.log("SE PROCEDE A CREAR UN PÚBLICO OBJETIVO");
            var body={
                "idRegistroRelacion" : idRelacionSeleccionada,
                "idAsesorCuentaPrincipal" : idAsesorCuentaPrincipal
            }
            app.alert.show('creacionPO', {
                level: 'process',
                closeable: false,
                messages: app.lang.get('LBL_LOADING'),
            });

            $('#btn-generar-po').attr('disabled', true);

            app.api.call('create', app.api.buildURL("crearPOdesdeRelacion"), body, {
                success: _.bind(function (data) {
                    app.alert.dismiss('creacionPO');

                    $('#btn-generar-po').removeAttr('disabled');

                    if( data['status'] == 'ok' ){
                        app.alert.show('poCreado', {
                            level: 'success',
                            messages: data['msj'] + ": " + '<a href="#Prospects/' + data['idRegistro'] + '" target= "_blank"> ' + data['nameRegistro'] + '  </a>',
                            autoClose: false
                        });

                        contextAltaPO.setBanderaPOcreado();

                    }else{
                        app.alert.show('poCreado', {
                            level: 'error',
                            messages: data['msj'],
                            autoClose: false
                        });
                    }

                }, contextAltaPO),
                error: _.bind(function (response) {
                    app.alert.dismiss('setFlagPOcreado');
                   console.log("error al activar bandera");
                   console.log(response);
                },contextAltaPO)
            });



        }

    },

    enviarAprobacion: function(){

        var mensajeCorreo= $('#motivo-creacion').val();

        if( mensajeCorreo.trim() == "" ){
            app.alert.show('mensajeVacio', {
                level: 'error',
                messages: 'Favor de ingresar el motivo de creación',
                autoClose: true
            });

        }else{
            //Enviamos correo
            var body={
                "idRegistro" : contextAltaPO.model.get('id'),
                "mensaje" : mensajeCorreo.toUpperCase()
            }
            app.alert.show('setEnviandoCorreoDirector', {
                level: 'process',
                closeable: false,
                messages: app.lang.get('LBL_LOADING'),
            });
            $('#btn-solicitar-aprobacion').attr('disabled', true);
            app.api.call('create', app.api.buildURL("AutorizaCreacionPO"), body, {
                success: _.bind(function (data) {
                    $('#btn-solicitar-aprobacion').removeAttr('disabled');
                    app.alert.dismiss('setEnviandoCorreoDirector');

                    app.alert.show('correoEnviado', {
                        level: 'success',
                        messages: data['msj'],
                        autoClose: false
                    });

                    var modal = $('#myModalAltaPO');
                    if (modal) {
                        modal.hide();
                    }
        
                    app.drawer.close();
                    
                }, contextAltaPO),
                error: _.bind(function (response) {
                    app.alert.dismiss('setFlagPOcreado');
                   console.log("error al activar bandera");
                   console.log(response);
                },contextAltaPO)
            });
        }

    },

    getIdRelacionSeleccionada: function(){

        var filas = $('#mainTableAltaPO tr.rowRelacion');
        var idRelacionSeleccionada = "";
        if( filas.length > 0 ){
            // Mostrar las filas en la consola
            filas.each(function(index, fila) {
                var $row = $(this);
                var isChecked = $row.find('input[type="checkbox"]').is(':checked');
                if (isChecked) {
                    
                    idRelacionSeleccionada = $row.attr('attr-id-cuenta');
                }
            });
        }

        return idRelacionSeleccionada;
    },

    /**
     * 
     * @param {*} e , elementoactual
     * Función que controla el funcionamiento para que únicamente se pueda seleccionar 1 checkbox dentro de la tabla
     */
    selectCheckBox: function( e ){

        const checkboxes = document.querySelectorAll('.mainTableAltaPO .cbRelaciones');
        contextAltaPO.event = e;

        if ( $(e.currentTarget).is(":checked") ) {
            // Desactivar todos los otros checkboxes
            checkboxes.forEach((cb) => {
              if (cb !== contextAltaPO.event.currentTarget ) {
                cb.checked = false;
              }
            });
        }
    },

    openDrawerPO: function(){
        var model = app.data.createBean('Prospects');
        app.drawer.open({
            layout: 'create',
            context: {
            create: true,
            module: 'Prospects',
            model: model,
            },
        },function(context,model){ //Función de callback que se dispara al cerrar drawer
            console.log("Lanzamos callback drawer");
            console.log(model); 
            if( model ){
                //Cerramos modal y habilitamos bandera que indica si ya se creó un PO
                var body={
                    "idRegistro" : contextAltaPO.model.get('id')
                }
                app.alert.show('setFlagPOcreado', {
                    level: 'process',
                    closeable: false,
                    messages: app.lang.get('LBL_LOADING'),
                });

                app.api.call('create', app.api.buildURL("EstablecerBanderaPublicoObjetivoCreado"), body, {
                    success: _.bind(function (data) {
                        app.alert.dismiss('setFlagPOcreado');

                        var modal = $('#myModalAltaPO');
                        if (modal) {
                            modal.hide();
                        }
            
                        app.drawer.close();
                        
                    }, contextAltaPO),
                    error: _.bind(function (response) {
                        app.alert.dismiss('setFlagPOcreado');
                       console.log("error al activar bandera");
                       console.log(response);
                    },contextAltaPO)
                });


            }else{

                var modal = $('#myModalAltaPO');
                if (modal) {
                    modal.hide();
                }
    
                app.drawer.close();
            }

        });
    },

    setBanderaPOcreado: function( ){

        var body={
            "idRegistro" : contextAltaPO.model.get('id')
        }
        app.alert.show('setFlagPOcreado', {
            level: 'process',
            closeable: false,
            messages: app.lang.get('LBL_LOADING'),
        });

        app.api.call('create', app.api.buildURL("EstablecerBanderaPublicoObjetivoCreado"), body, {
            success: _.bind(function (data) {
                app.alert.dismiss('setFlagPOcreado');

                var modal = $('#myModalAltaPO');
                if (modal) {
                    modal.hide();
                }
    
                app.drawer.close();
                
            }, contextAltaPO),
            error: _.bind(function (response) {
                app.alert.dismiss('setFlagPOcreado');
               console.log("error al activar bandera");
               console.log(response);
            },contextAltaPO)
        });
    },

    rechazarPeticionCrearPO: function(){

        app.alert.show('message-id', {
            level: 'confirmation',
            messages: 'Estás a punto de <b>Rechazar</b> la petición para crear un nuevo registro de PO.<br>¿Confirmas la operación?',
            autoClose: false,
            onConfirm: function(){
                 //Enviamos email de rechazo, limpiamos bandera y id de usurio en resumen
                 var body={
                    "tipoNotificacion": "rechazo",
                    "idRegistro" : contextAltaPO.model.get('id'),
                    "nameRegistro" : contextAltaPO.model.get('name'),
                    "idAsesor": contextAltaPO.model.get('user_id_c'),
                    "nombreAsesor": contextAltaPO.model.get('promotorleasing_c'),
                }
                app.alert.show('loadingRechazo', {
                    level: 'process',
                    closeable: false,
                    messages: app.lang.get('LBL_LOADING'),
                });

                $('#btn-rechazar-creacion').attr('disabled', true);
                $('#btn-aprobar-creacion').attr('disabled', true);
                app.api.call('create', app.api.buildURL("NotificacionCreacionPO"), body, {
                    success: _.bind(function (data) {
                        app.alert.dismiss('loadingRechazo');

                        $('#btn-rechazar-creacion').removeAttr('disabled');
                        $('#btn-aprobar-creacion').removeAttr('disabled');
                        
                        app.alert.show('solicitudRechazada', {
                            level: 'success',
                            messages: data['msj'],
                            autoClose: false
                        });

                        var modal = $('#myModalAltaPO');
                        if (modal) {
                            modal.hide();
                        }
            
                        app.drawer.close();
                        
                    }, contextAltaPO),
                    error: _.bind(function (response) {
                        app.alert.dismiss('loadingRechazo');
                       console.log("error al rechazar");
                       console.log(response);
                    },contextAltaPO)
                });
            },
            onCancel: function(){
               
            }
        });

    },

    aprobarPeticionCrearPO: function(){

        app.alert.show('message-id', {
            level: 'confirmation',
            messages: 'Estás a punto de <b>Aprobar</b> la petición para crear un nuevo registro de PO.<br>¿Confirmas la operación?',
            autoClose: false,
            onConfirm: function(){

                var body={
                    "tipoNotificacion": "autoriza",
                    "idRegistro" : contextAltaPO.model.get('id'),
                    "nameRegistro" : contextAltaPO.model.get('name'),
                    "idAsesor": contextAltaPO.model.get('user_id_c'),
                    "nombreAsesor": contextAltaPO.model.get('promotorleasing_c'),
                }
                app.alert.show('loadingAutoriza', {
                    level: 'process',
                    closeable: false,
                    messages: app.lang.get('LBL_LOADING'),
                });

                $('#btn-rechazar-creacion').attr('disabled', true);
                $('#btn-aprobar-creacion').attr('disabled', true);

                app.api.call('create', app.api.buildURL("NotificacionCreacionPO"), body, {
                    success: _.bind(function (data) {
                        app.alert.dismiss('loadingAutoriza');

                        $('#btn-rechazar-creacion').removeAttr('disabled');
                        $('#btn-aprobar-creacion').removeAttr('disabled');
                        
                        app.alert.show('solicitudAutorizada', {
                            level: 'success',
                            messages: data['msj'],
                            autoClose: false
                        });

                        var modal = $('#myModalAltaPO');
                        if (modal) {
                            modal.hide();
                        }
            
                        app.drawer.close();
                        
                    }, contextAltaPO),
                    error: _.bind(function (response) {
                        app.alert.dismiss('loadingRechazo');
                       console.log("error al rechazar");
                       console.log(response);
                    },contextAltaPO)
                });
                 
            },
            onCancel: function(){
               
            }
        });

    },

})