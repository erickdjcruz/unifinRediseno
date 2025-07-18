({
    events: {
        // 'click  .btn-ReenviarF': 'ReenviaCorreoF',
        'click  .btn-DescargarF': 'DescargaArchivoF',
        // 'click  .btn-ReenviarC': 'ReenviaCorreoC',
        'click  .btn-DescargarC': 'DescargaArchivoC',
    },

    initialize: function (options) {
        //Inicializa campo custom
        options = options || {};
        options.def = options.def || {};
        cont_nlzt = this;
        this._super('initialize', [options]);
        //Carga lista de valores para la creacion de la url portal
        cont_nlzt.lista_url = App.lang.getAppListStrings('analizate_url_list');
    },

    _render: function () {
        this._super("_render");
        $('[data-fieldname="accounts_analizate"]').children().show();
    },

    DescargaArchivo: function (empresa) {
        cont_nlzt.Financiera = cont_nlzt.Analizate.Financiera;
        cont_nlzt.Credit = cont_nlzt.Analizate.Credit;
        if (cont_nlzt.Financiera != undefined || cont_nlzt.Credit!= undefined) {
            //Valida tipo de empresa
            if (empresa==1){
                var archivo = cont_nlzt.Financiera.url_documento;
            }else{
                var archivo = cont_nlzt.Credit.url_documento;
            }
            if (archivo != null && archivo != "") {
                $('.btn-DescargarF').bind('click', false);
                $('.btn-DescargarC').bind('click', false);
                App.alert.show('enventoDescargaPDF', {
                    level: 'process',
                    title: 'Generando descarga, por favor espere.',
                });
                //Peticion de servicio para obtener el documento con el id en url_documento

                //var host = App.config.analizate;
                var host = App.lang.getAppListStrings('analizate_hosts_documentos_list')['proveedor'];
                var direccion = host + archivo;
                direccion = btoa(direccion);
                /*Realiza api call para obtener el documento en base 64 para añadirle cabecera
               extensión y descargarlo*/

                var valUrl = app.api.buildURL("ObtieneDocumento/" + direccion, '', {}, {});
                app.api.call("read", valUrl, null, {
                    success: _.bind(function (data) {
                        if (data != null) {
                            App.alert.dismiss('enventoDescargaPDF');
                            $('.btn-DescargarF').unbind('click', false);
                            $('.btn-DescargarC').unbind('click', false);
                            //Para descarga del pdf
                            var archivo = 'data:application/octet-stream;base64,' + data;
                            //Crea elemento
                            var downloadLink = document.createElement("a");
                            downloadLink.href = archivo;
                            downloadLink.download = "ActualizatePDF.pdf";
                            document.body.appendChild(downloadLink);
                            downloadLink.click();
                            document.body.removeChild(downloadLink);
                        }
                    }, this)
                });

            } else {
                app.alert.show('message_documento', {
                    level: 'info',
                    messages: 'Actualmente no cuenta con el documento de descarga.',
                    autoClose: false
                });
            }
        }
    },

    // ReenviaCorreo: function (empresa) {
    //         //Valida que sea proveedor para reenviar
    //         if (this.model.get('tipo_registro_cuenta_c') != "5" && !this.model.get('esproveedor_c')) {
    //             app.alert.show('No Proveedor', {
    //                 level: 'error',
    //                 messages: 'Sólo se puede reenviar el correo a cuentas de tipo Proveedor.',
    //                 autoClose: false
    //             });
    //             return;
    //         }

    //         if (this.model.get('email1') == "" || this.model.get('email1') == undefined) {
    //             app.alert.show('No Envio', {
    //                 level: 'error',
    //                 messages: 'La cuenta no contiene un correo electrónico.',
    //                 autoClose: false
    //             });
    //             return;
    //         }
    //         $('.btn-ReenviarF').bind('click', false);
    //         $('.btn-ReenviarC').bind('click', false);
    //         App.alert.show('eventoenviomail', {
    //             level: 'process',
    //             title: 'Cargando, por favor espere.',
    //         });
    //         //Se declaran variables para armar la url
    //         var rfc = btoa(this.model.get('rfc_c'));
    //         var id = btoa(this.model.get('id'));
    //         var mailAccount = btoa(this.model.get('email1'));
    //         var link = '&UUID=' + id + '&RFC_CIEC=' + rfc + '&MAIL=' + mailAccount;

    //         // FECHA ACTUAL
    //         var today = new Date();
    //         var yyyy = today.getFullYear();
    //         var mm = today.getMonth()+1; //January is 0!
    //         var dd = today.getDate();
    //         var hour = today.getHours();
    //         var min = today.getMinutes();
    //         var secs = today.getSeconds();
    //         var zona= new Date().getTimezoneOffset()/60;

    //         if(mm<10) {
    //         mm = '0'+mm
    //         }
    //         if(dd<10) {
    //         dd = '0'+dd
    //         }
    //         if(hour<10){
    //         hour='0'+hour
    //         }
    //         if(min<10){
    //         min='0'+min
    //         }
    //         if(secs<10){
    //         secs='0'+secs
    //         }
    //         var fecha= yyyy + '-' + mm + '-' + dd + 'T'+hour +':'+min+':'+secs+'-0'+zona+':00';

    //         //enviar elementos de la cuenta
    //         var api_params = {
    //             "tipo": "1",
    //             "estado": "1",
    //             "documento": "",
    //             "url_portal": link,
    //             "url_documento": "",
    //             "empresa": empresa,
    //             "fecha_actualizacion": fecha,
    //             "anlzt_analizate_accountsaccounts_ida": this.model.id,
    //             "tipo_registro_cuenta_c":"5" //Proveedor
    //         };
    //         var url = app.api.buildURL('ANLZT_analizate/', null, null);
    //         app.api.call('create', url, api_params, {
    //             success: function (data) {
    //                 App.alert.dismiss('eventoenviomail');
    //                 $('.btn-ReenviarF').unbind('click', false);
    //                 $('.btn-ReenviarC').unbind('click', false);
    //                 app.alert.show('Correo_reenviado', {
    //                     level: 'success',
    //                     messages: 'Se ha enviado un nuevo correo a la cuenta.',
    //                     autoClose: false
    //                 });
    //             },
    //             error: function (e) {
    //                 App.alert.dismiss('eventoenviomail');
    //                 $('.btn-ReenviarF').unbind('click', false);
    //                 $('.btn-ReenviarC').unbind('click', false);
    //                 app.alert.show('Correo_no_reenviado', {
    //                     level: 'error',
    //                     messages: 'No se ha podido enviar un nuevo correo a la cuenta.',
    //                     autoClose: false
    //                 });
    //             }
    //         });
    // },

    // ReenviaCorreoF: function () {
    //   this.ReenviaCorreo(1);
    // },

    // ReenviaCorreoC: function () {
    //     this.ReenviaCorreo(2);
    // },

    DescargaArchivoF: function () {
      this.DescargaArchivo(1);
    },

    DescargaArchivoC: function () {
        this.DescargaArchivo(2);
    },

})
