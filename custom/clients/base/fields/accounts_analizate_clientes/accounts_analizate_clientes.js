({
    events: {
        'click  .btn-ReenviarCliente': 'ReenviaCorreoCliente',
        'click  .btn-DescargarCliente': 'DescargaArchivoCliente',
        'click  .btn-DescargarCotejoDigital': 'descargarCotejoDigital',
    },

    initialize: function (options) {
        //Inicializa campo custom
        options = options || {};
        options.def = options.def || {};
        analizate_cl=this;
        this._super('initialize', [options]);
        //Carga lista de valores para la creacion de la url portal
        cont_nlzt.lista_url = App.lang.getAppListStrings('analizate_url_list');
        listaEstado = App.lang.getAppListStrings('anlzt_estado_list');
        //this.model.on('sync', this.cargapipelineCliente, this);
        this.AnalizateCliente = [];
        this.AnalizateCliente.Estado1=[];
        this.AnalizateCliente.Estado2=[];
        this.AnalizateCliente.Estado3=[];
        this.AnalizateCliente.Estado4=[];
        this.AnalizateCliente.Estado5=[];
        this.AnalizateCliente.Estado1.Valor='';
        this.AnalizateCliente.Estado1.Class='';
        this.AnalizateCliente.Estado1.Fecha='';
        this.AnalizateCliente.Estado1.FechaClass='ocult';
        this.AnalizateCliente.Estado2.Valor='';
        this.AnalizateCliente.Estado2.Class='';
        this.AnalizateCliente.Estado2.Fecha='';
        this.AnalizateCliente.Estado2.FechaClass='ocult';
        this.AnalizateCliente.Estado3.Valor='';
        this.AnalizateCliente.Estado3.Class='';
        this.AnalizateCliente.Estado3.Fecha='';
        this.AnalizateCliente.Estado3.FechaClass='ocult';
        this.AnalizateCliente.Estado4.Valor='';
        this.AnalizateCliente.Estado4.Class='';
        this.AnalizateCliente.Estado4.Fecha='';
        this.AnalizateCliente.Estado4.FechaClass='ocult';
        this.AnalizateCliente.Estado5.Valor='';
        this.AnalizateCliente.Estado5.Class='';
        this.AnalizateCliente.Estado5.Fecha='';
        this.AnalizateCliente.Estado5.FechaClass='ocult';
    },

    _render: function () {
        this._super("_render");
        if($('[data-fieldname="accounts_analizate_clientes"] > span').length >0){
            $('[data-fieldname="accounts_analizate_clientes"] > span').show();
        }
        this.verificaEstatusSAT();
    },

    cargapipelineCliente: function () {
        var estado = cont_nlzt.Analizate.Cliente.estado;
        var fecha = cont_nlzt.Analizate.Cliente.fecha;
        console.log("Inicia campo cstm analizate clientes.");
        this.AnalizateCliente.Estado1.Valor = listaEstado[1];
        this.AnalizateCliente.Estado2.Valor = listaEstado[2];
        this.AnalizateCliente.Estado3.Valor = listaEstado[3];
        this.AnalizateCliente.Estado4.Valor = listaEstado[4];
        this.AnalizateCliente.Estado5.Valor = 'Datos CFDI Actualizados';
        this.AnalizateCliente.Estado1.Fecha = fecha;
        this.AnalizateCliente.Estado2.Fecha = fecha;
        this.AnalizateCliente.Estado3.Fecha = fecha;
        this.AnalizateCliente.Estado4.Fecha = fecha;
        this.AnalizateCliente.Estado5.Fecha = fecha;
        switch(estado) {
          case "1":
            this.AnalizateCliente.Estado1.Valor = listaEstado[estado];
            this.AnalizateCliente.Estado1.Class = 'current';
            this.AnalizateCliente.Estado1.FechaClass='success';
            break;
          case "2":
            this.AnalizateCliente.Estado2.Valor = listaEstado[estado];
            this.AnalizateCliente.Estado1.Class = 'done';
            this.AnalizateCliente.Estado2.Class = 'current';
            this.AnalizateCliente.Estado2.FechaClass='success';
            break;
          case "3":
            this.AnalizateCliente.Estado3.Valor = listaEstado[estado];
            this.AnalizateCliente.Estado1.Class = 'done';
            this.AnalizateCliente.Estado2.Class = 'done';
            this.AnalizateCliente.Estado3.Class = 'current';
            this.AnalizateCliente.Estado3.FechaClass='success';
            break;
          case "4":
            this.AnalizateCliente.Estado4.Valor = listaEstado[estado];
            this.AnalizateCliente.Estado1.Class = 'done';
            this.AnalizateCliente.Estado2.Class = 'done';
            this.AnalizateCliente.Estado3.Class = 'done';
            this.AnalizateCliente.Estado4.Class = 'current';
            this.AnalizateCliente.Estado4.FechaClass='success';
            break;
          case "5":
            this.AnalizateCliente.Estado5.Valor = 'Datos CFDI Actualizados';
            this.AnalizateCliente.Estado1.Class = 'done';
            this.AnalizateCliente.Estado2.Class = 'done';
            this.AnalizateCliente.Estado3.Class = 'done';
            this.AnalizateCliente.Estado4.Class = 'done';
            this.AnalizateCliente.Estado5.Class = 'current';
            this.AnalizateCliente.Estado5.FechaClass='success';
            break;
          default:
            this.AnalizateCliente.Estado1.Valor = 'Pendiente';
            this.AnalizateCliente.Estado1.Class = 'current';
        }
    },

    ReenviaCorreoCliente: function () {
        //Valida que sea proveedor para reenviar
        if (this.model.get('tipo_registro_cuenta_c') == "1" ||this.model.get('tipo_registro_cuenta_c') == "5") {
            app.alert.show('No_Cliente', {
                level: 'error',
                messages: 'Sólo se puede reenviar el correo a cuentas de tipo Cliente, Persona o Prospecto.',
                autoClose: false
            });
            return;
        }

        if (this.model.get('email1') == "" || this.model.get('email1') == undefined) {
            app.alert.show('No_Envio', {
                level: 'error',
                messages: 'La cuenta no contiene un correo electrónico.',
                autoClose: false
            });
            return;
        }
        $('.btn-ReenviarCliente').bind('click', false);
        App.alert.show('eventoEnvioMailCliente', {
            level: 'process',
            title: 'Cargando, por favor espere.',
        });

        //enviar elementos de la cuenta
        var api_params = {
            "idCuenta": this.model.id,
            "idUsuario": App.user.id
        };
        var url = app.api.buildURL('solicitaCIECCliente/', null, null);
        app.api.call('create', url, api_params, {
            success: function (data) {
                App.alert.dismiss('eventoEnvioMailCliente');
                $('.btn-ReenviarCliente').unbind('click', false);
                var levelStatus = (data['status'] == '200') ? 'success' : 'error';
                app.alert.show('Correo_reenviado', {
                    level: levelStatus,
                    messages: data['message'],
                    autoClose: false
                });
            },
            error: function (e) {
                App.alert.dismiss('eventoEnvioMailCliente');
                $('.btn-ReenviarCliente').unbind('click', false);
                app.alert.show('Correo_no_reenviado', {
                    level: 'error',
                    messages: 'No se ha podido enviar un nuevo correo a la cuenta.',
                    autoClose: false
                });
            }
        });
    },

    DescargaArchivoCliente: function () {
        //Valida existencia de contexto cliente
        if (cont_nlzt.Analizate.Cliente != undefined) {
            var archivo = cont_nlzt.Analizate.Cliente.url_documento;
            if (archivo != null && archivo != "") {
                $('.btn-DescargarCliente').bind('click', false);
                App.alert.show('enventoDescargaPDFCliente', {
                    level: 'process',
                    title: 'Generando descarga, por favor espere.',
                });
                //Peticion de servicio para obtener el documento con el id en url_documento
                //var host = App.config.analizate;
                var host = App.lang.getAppListStrings('analizate_hosts_documentos_list')['cliente'];
                var direccion = host + archivo;
                direccion = btoa(direccion);
                /*Realiza api call para obtener el documento en base 64 para añadirle cabecera
               extensión y descargarlo*/

                var valUrl = app.api.buildURL("ObtieneDocumento/" + direccion, '', {}, {});
                app.api.call("read", valUrl, null, {
                    success: _.bind(function (data) {
                        if (data != null) {
                            App.alert.dismiss('enventoDescargaPDFCliente');
                            $('.btn-DescargarCliente').unbind('click', false);
                            //Para descarga del pdf
                            var archivo = 'data:application/octet-stream;base64,' + data;
                            //Crea elemento
                            var downloadLink = document.createElement("a");
                            downloadLink.href = archivo;
                            downloadLink.download = "CFDIActualizado.pdf";
                            document.body.appendChild(downloadLink);
                            downloadLink.click();
                            document.body.removeChild(downloadLink);
                        }
                    }, this)
                });

            } else {
                app.alert.show('message_documento_cliente', {
                    level: 'info',
                    messages: 'Actualmente no cuenta con el documento de descarga.',
                    autoClose: false
                });
            }
        }
    },

    descargarCotejoDigital:function(){

        if ( _.isEmpty(cont_nlzt.Analizate.Cliente.url_documento) ) {
            app.alert.show("sin_csf", {
                level: "warning",
                title: "Sin CSF",
                messages: "Esta cuenta no tiene cargada una CSF y no se podrá obtener su Cotejo Digital",
                autoClose: false
            });

            return;
        }

        if( _.isEmpty(this.model.get('rfc_c')) ){
            app.alert.show("sin_rfc", {
                level: "warning",
                title: "Sin RFC",
                messages: "Esta cuenta no tiene RFC y no se podrá obtener su Cotejo Digital",
                autoClose: false
            });

            return;
        }

        App.alert.show('cotejoDescarga', {
            level: 'process',
            title: 'Generando descarga, por favor espere.',
        });

        $('#btn-DescargarCotejo').attr("disabled",true);

        var idPersona = this.model.get('id');
        var bodyRequest = {
            "idPersona": idPersona
        };

        var valUrl = app.api.buildURL("ObtenerCotejoDigital", '', {}, {});
        
        app.api.call("create", valUrl, bodyRequest , {
            success: _.bind(function (data) {
                $('#btn-DescargarCotejo').removeAttr("disabled");
                if (data != null) {
                    App.alert.dismiss('cotejoDescarga');
                    if( data.status == "OK" ){
                        //Crea elemento
                        var downloadLink = document.createElement("a");
                        downloadLink.href = data.mssg;
                        downloadLink.download = "CotejoDigital.pdf";
                        document.body.appendChild(downloadLink);
                        downloadLink.click();
                        document.body.removeChild(downloadLink);
                    }else{
                        app.alert.show("no_descarga_cotejo", {
                            level: "warning",
                            title: "Advertencia",
                            messages: data.mssg,
                            autoClose: false
                        });
                    }

                }
            }, this)
        });
    },

    verificaEstatusSAT: function () {
        var id = this.model.get('id');
        app.api.call('GET', app.api.buildURL('estatusProcesoRobina/' + id), null, {
            success: function (data) {
                var status_gen = data.status;
                var estatus = data.estatus_procesado;

                if (estatus != "Procesado" && status_gen != '300' ) {
                    // Deshabilita los botones
                    /*$('.btn-ReenviarCliente').prop('disabled', true);
                    $('.btn-DescargarCliente').prop('disabled', true);
                    $('.btn-DescargarCotejoDigital').prop('disabled', true);
                    */
                   $('.btn-ReenviarCliente, .btn-DescargarCliente, .btn-DescargarCotejoDigital')
                    .attr('disabled', true)
                    .css({
                        'pointer-events': 'none',
                        'opacity': '0.6',
                        'cursor': 'not-allowed'
                    });
                    // Muestra el mensaje
                    /*if ($('#msg-proceso-sat').length === 0) {
                        $('.record').prepend('<div id="msg-proceso-sat" style="color:red;font-weight:bold;margin:10px 0;">Procesando datos SAT, no puede realizar las descargas</div>');
                    }*/

                } else {
                    // Habilita los botones
                    /*$('.btn-ReenviarCliente').prop('disabled', false);
                    $('.btn-DescargarCliente').prop('disabled', false);
                    $('.btn-DescargarCotejoDigital').prop('disabled', false);
                    $('#msg-proceso-sat').remove();
                    */
                   $('.btn-ReenviarCliente, .btn-DescargarCliente, .btn-DescargarCotejoDigital')
                    .removeAttr('disabled')
                    .css({
                        'pointer-events': '',
                        'opacity': '',
                        'cursor': ''
                    });
                }
            },
            error: function (e) {
                console.log(e);                
            }
        });
    },
})
