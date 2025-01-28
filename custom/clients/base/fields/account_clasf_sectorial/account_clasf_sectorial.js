({
    //Carga de Listas de valores
    actividad_list: null,
    subsector_list: null,
    sector_list: null,
    macro_list: null,
    clase_list: null,
    subrama_list: null,
    rama_list: null,
    macro_cnbv_list: null,
    sector_cnbv_list: null,
    subsector_cnbv_list: null,

    initialize: function (options) {
        //Inicializa campo custom
        this._super('initialize', [options]);
        options = options || {};
        options.def = options.def || {};
        clasf_sectorial = this;

        //Guarda valores en los campos de clasificacion sectorial
        this.model.addValidationTask('GuardaClasfSectorial', _.bind(this.SaveClasfSectorial, this));
        this.model.on('sync', this.loadData, this);
        clasf_sectorial.renderlista = 0;
        clasf_sectorial.check_uni2 = 0;


        this.ActividadEconomica = {
            // 'combinaciones': '',
            'ae': {
                'id': '',
            },
            'sse': {
                'id': '',
            },
            'se': {
                'id': '',
            },
            'ms': {
                'id': '',
            },
            'inegi_clase': '',
            'inegi_subrama': '',
            'inegi_rama': '',
            'inegi_subsector': '',
            'inegi_sector': '',
            'inegi_macro': '',
            'label_subsector': '',
            'label_sector': '',
            'label_macro': '',
            'label_clase': '',
            'label_subrama': '',
            'label_rama': '',
            'label_isubsector': '',
            'label_isector': '',
            'label_imacro': '',
            'label_div': '',
            'label_grp': '',
            'label_cls': ''
        };

        this.prevActEconomica = {
            // 'combinaciones': '',
            'ae': {
                'id': '',
            },
            'sse': {
                'id': '',
            },
            'se': {
                'id': '',
            },
            'ms': {
                'id': '',
            },
            'inegi_clase': '',
            'inegi_subrama': '',
            'inegi_rama': '',
            'inegi_subsector': '',
            'inegi_sector': '',
            'inegi_macro': '',
            'label_subsector': '',
            'label_sector': '',
            'label_macro': '',
            'label_clase': '',
            'label_subrama': '',
            'label_rama': '',
            'label_isubsector': '',
            'label_isector': '',
            'label_imacro': '',
            'label_div': '',
            'label_grp': '',
            'label_cls': ''
        };

        this.ResumenCliente = {
            'inegi': {
                'inegi_clase': '',
                'inegi_subrama': '',
                'inegi_rama': '',
                'inegi_subsector': '',
                'inegi_sector': '',
                'inegi_macro': ''
            },
            'pb': {
                'pb_division': '',
                'pb_grupo': '',
                'pb_clase': ''
            },
        };

    },

    loadData: function () {
        clasf_sectorial = this;
        // clasf_sectorial.ActividadEconomica.ae.id = this.model.get("actividadeconomica_c");
        // clasf_sectorial.ActividadEconomica.sse.id = this.model.get("subsectoreconomico_c");
        // clasf_sectorial.ActividadEconomica.se.id = this.model.get("sectoreconomico_c");
        // clasf_sectorial.ActividadEconomica.ms.id = this.model.get("tct_macro_sector_ddw_c");
        clasf_sectorial['prevActEconomica'] = app.utils.deepCopy(clasf_sectorial.ActividadEconomica);
        clasf_sectorial.render();
        if (clasf_sectorial.ActividadEconomica.ae.id != "") {
            $('.list_ae').trigger('change');
        }
        //Api ResumenCliente para los campos de INEGI
        // var idCuenta = clasf_sectorial.model.id; //Id de la Cuenta
        // if (idCuenta != '' && idCuenta != undefined && idCuenta != null) {
        //     var url = app.api.buildURL('ResumenCliente/' + idCuenta, null, null,);
        //     app.api.call('GET', url, {}, {
        //         success: function (data) {
        //             clasf_sectorial.ResumenCliente = data;
        // 			//Etiquetas de PB para Input del HBS en edit
        // 			clasf_sectorial.ActividadEconomica.label_div = app.lang.getAppListStrings('pb_division_list')[clasf_sectorial.ResumenCliente.pb.pb_division];
        // 			clasf_sectorial.ActividadEconomica.label_grp = app.lang.getAppListStrings('pb_grupo_list')[clasf_sectorial.ResumenCliente.pb.pb_grupo];
        // 			clasf_sectorial.ActividadEconomica.label_cls = app.lang.getAppListStrings('pb_clase_list')[clasf_sectorial.ResumenCliente.pb.pb_clase];
        // 			clasf_sectorial.check_uni2 = clasf_sectorial.ResumenCliente.inegi.inegi_acualiza_uni2;
        //             clasf_sectorial['prevActEconomica'] = app.utils.deepCopy(clasf_sectorial.ActividadEconomica);
        //             _.extend(this, clasf_sectorial.ResumenCliente);
        //             clasf_sectorial.render();
        //         }
        //     });
        // }
    },

    bindDataChange: function () {
        this.model.on('change:' + this.name, function () {
            if (this.action !== 'edit') {
            }
        }, this);
    },

    _render: function () {
        this._super("_render");

        if ($('[data-fieldname="account_clasf_sectorial"] > span').length > 0) {
            $('[data-fieldname="account_clasf_sectorial"] > span').show();
        }
        //campo custom account_clasf_sectorial ocualta la Etiqueta
        $("div.record-label[data-name='account_clasf_sectorial']").attr('style', 'display:none;');
        //Muestra y Oculta campos dependientes de Actividad Economica
        if ($('.list_ae').select2('val') != "" || clasf_sectorial.ActividadEconomica.ae.id != "") {
            $('.list_ae').trigger('change');
        }
        //funcion de cargar listas
        if (clasf_sectorial.renderlista != 1) {
            this.cargalistas();
        }
        //Funcion para dar estilo select2 a las listas deplegables.
        var $select = $('select.select2');
        $select.select2();

        if (clasf_sectorial.check_uni2 != 0) {
            //Campos ReadOnly de Actividad Economica dependiendo del check de uni2
            $(".campoAE").attr('style', 'pointer-events:none;');
        }

        //Campos CNBV de Solo Lectura
        $(".campoSSE").attr('style', 'pointer-events:none;');
        $(".campoSE").attr('style', 'pointer-events:none;');
        $(".campoMS").attr('style', 'pointer-events:none;');
        //Campos INEGI de Solo Lectura 
        $(".campoIC").attr('style', 'pointer-events:none;');
        $(".campoISR").attr('style', 'pointer-events:none;');
        $(".campoIR").attr('style', 'pointer-events:none;');
        $(".campoISS").attr('style', 'pointer-events:none;');
        $(".campoIS").attr('style', 'pointer-events:none;');
        $(".campoIMS").attr('style', 'pointer-events:none;');
        //Campos PB de Solo Lectura
        $(".campodiv").attr('style', 'pointer-events:none;');
        $(".campogrp").attr('style', 'pointer-events:none;');
        $(".campocls").attr('style', 'pointer-events:none;');

        //Carga los valores en los campos dependientes de Actividad Económica al momento de hacer el change
        $('.list_ae').change(function (evt) {
            clasf_sectorial.fClasfSectorial(evt);
            // clasf_sectorial.MuestraCamposAE(evt);
            // clasf_sectorial.ClasfSectorialApi(evt);
        });
        // //carga los valores del sector dependientes del sub sector
        // $('.list_sse').change(function (evt) {
        //     clasf_sectorial.obtenerSubSector();
        // });
        // //carga los valores del macrosector dependientes del sector
        // $('.list_se').change(function (evt) {
        //     clasf_sectorial.obtenerSector();
        // });
    },

    fClasfSectorial: function (evt) {

        if ($(evt.currentTarget).val() == '' || $(evt.currentTarget).val() == null || $(evt.currentTarget).val() == undefined) {
            idActEconomica = clasf_sectorial.ActividadEconomica.ae.id;
        } else {
            idActEconomica = $('.list_ae').select2('val');
        }

        app.alert.show('obtiene_clasf_sectorial', {
            level: 'process',
            title: 'Cargando...',
        });

        if (idActEconomica != "" && idActEconomica != null && idActEconomica != undefined) {
            console.log("idActEconomica " + idActEconomica);

            app.api.call('GET', app.api.buildURL('clasificacionSectorialCNVB/' + idActEconomica), null, {
                success: function (data) {
                    dataInegi = data;
                    app.alert.dismiss('obtiene_clasf_sectorial');
                    // console.log(data);
                    if (dataInegi != '') {
                        //Campos CNBV
                        clasf_sectorial.ActividadEconomica.ae.id = idActEconomica;
                        clasf_sectorial.ActividadEconomica.sse.id = dataInegi['id_subsector_economico_cnbv'];
                        clasf_sectorial.ActividadEconomica.se.id = dataInegi['id_sector_economico_cnbv'];
                        clasf_sectorial.ActividadEconomica.ms.id = dataInegi['id_macro_sector_cnbv'];

                        //Etiquetas de los campos CNBV para Input del HBS en edit
                        clasf_sectorial.ActividadEconomica.label_subsector = app.lang.getAppListStrings('subsector_cnbv_list')[clasf_sectorial.ActividadEconomica.sse.id];
                        clasf_sectorial.ActividadEconomica.label_sector = app.lang.getAppListStrings('sector_cnbv_list')[clasf_sectorial.ActividadEconomica.se.id];
                        clasf_sectorial.ActividadEconomica.label_macro = app.lang.getAppListStrings('macro_cnbv_list')[clasf_sectorial.ActividadEconomica.ms.id];

                        //Envia los valores de los campos de INEGI a la vista HBS
                        clasf_sectorial.ResumenCliente.inegi.inegi_clase = dataInegi['id_clase_inegi'];
                        clasf_sectorial.ResumenCliente.inegi.inegi_subrama = dataInegi['id_subrama_inegi'];
                        clasf_sectorial.ResumenCliente.inegi.inegi_rama = dataInegi['id_rama_inegi'];
                        clasf_sectorial.ResumenCliente.inegi.inegi_subsector = dataInegi['id_subsector_inegi'];
                        clasf_sectorial.ResumenCliente.inegi.inegi_sector = dataInegi['id_sector_inegi'];
                        clasf_sectorial.ResumenCliente.inegi.inegi_macro = dataInegi['id_macro_inegi'];

                        //Envia los valores al LH para guardarlos en el modulo de Resumen
                        clasf_sectorial.ActividadEconomica.inegi_clase = dataInegi['id_clase_inegi'];
                        clasf_sectorial.ActividadEconomica.inegi_subrama = dataInegi['id_subrama_inegi'];
                        clasf_sectorial.ActividadEconomica.inegi_rama = dataInegi['id_rama_inegi'];
                        clasf_sectorial.ActividadEconomica.inegi_subsector = dataInegi['id_subsector_inegi'];
                        clasf_sectorial.ActividadEconomica.inegi_sector = dataInegi['id_sector_inegi'];
                        clasf_sectorial.ActividadEconomica.inegi_macro = dataInegi['id_macro_inegi'];

                        //Etiquetas de los campos INEGI para Input del HBS en edit
                        clasf_sectorial.ActividadEconomica.label_clase = app.lang.getAppListStrings('clase_list')[clasf_sectorial.ActividadEconomica.inegi_clase];
                        clasf_sectorial.ActividadEconomica.label_subrama = app.lang.getAppListStrings('subrama_list')[clasf_sectorial.ActividadEconomica.inegi_subrama];
                        clasf_sectorial.ActividadEconomica.label_rama = app.lang.getAppListStrings('rama_list')[clasf_sectorial.ActividadEconomica.inegi_rama];
                        clasf_sectorial.ActividadEconomica.label_isubsector = app.lang.getAppListStrings('subsector_list')[clasf_sectorial.ActividadEconomica.inegi_subsector];
                        clasf_sectorial.ActividadEconomica.label_isector = app.lang.getAppListStrings('sector_list')[clasf_sectorial.ActividadEconomica.inegi_sector];
                        clasf_sectorial.ActividadEconomica.label_imacro = app.lang.getAppListStrings('macro_list')[clasf_sectorial.ActividadEconomica.inegi_macro];

                        clasf_sectorial.render();
                    }
                },
                error: function (e) {
                    throw e;
                }
            });
        }
    },

    SaveClasfSectorial: function (fields, errors, callback) {

        if ($('.list_ae').select2('val') != '') {
            this.ActividadEconomica = clasf_sectorial.ActividadEconomica;
        }
        //Establece el objeto para guardar en los campos de Clasificacion Sectorial 
        this.model.set('account_clasf_sectorial', this.ActividadEconomica);

        callback(null, fields, errors);
    },

    //Carga las listas desplegables para los campos.
    cargalistas: function () {
        //LISTAS CNBV
        clasf_sectorial.actividad_list = app.lang.getAppListStrings('actividad_list');
        clasf_sectorial.subsector_cnbv_list = app.lang.getAppListStrings('subsector_cnbv_list');
        clasf_sectorial.sector_cnbv_list = app.lang.getAppListStrings('sector_cnbv_list');
        clasf_sectorial.macro_list = app.lang.getAppListStrings('macro_list');
        clasf_sectorial.macro_cnbv_list = app.lang.getAppListStrings('macro_cnbv_list');

        //LISTAS INEGI
        clasf_sectorial.clase_list = app.lang.getAppListStrings('clase_list');
        clasf_sectorial.subrama_list = app.lang.getAppListStrings('subrama_list');
        clasf_sectorial.rama_list = app.lang.getAppListStrings('rama_list');
        clasf_sectorial.subsector_list = app.lang.getAppListStrings('subsector_list');
        clasf_sectorial.sector_list = app.lang.getAppListStrings('sector_list');
    },

    // MuestraCamposAE: function (evt) {
    //     //Muestra los campos dependientes de Actividad Economica
    //     if (evt != undefined) {
    //         //Validacion evt para record y create
    //         if ($(evt.currentTarget).val() == "") {

    //             $('.campoSSE').hide();
    //             $('.campoSE').hide();
    //             $('.campoMS').hide();

    //         } else {
    //             $('.campoSSE').show();
    //             $('.campoSE').show();
    //             $('.campoMS').show();
    //         }

    //     } else {
    //         //Validación al momento de cargar la vista de la cuenta por render
    //         if ($('.list_ae').select2('val') == "" || $('.list_ae').val() == undefined || $('.list_ae').val() == "") {

    //             $('.campoSSE').hide();
    //             $('.campoSE').hide();
    //             $('.campoMS').hide();

    //         } else {
    //             $('.campoSSE').show();
    //             $('.campoSE').show();
    //             $('.campoMS').show();
    //         }
    //     }
    // },

    // ClasfSectorialApi: function (evt) {

    //     clasf_sectorial = this;
    //     dataCS = [];
    //     var idActEconomica = "";

    //     if ($(evt.currentTarget).val() == '' || $(evt.currentTarget).val() == null || $(evt.currentTarget).val() == undefined) {
    //         idActEconomica = clasf_sectorial.ActividadEconomica.ae.id;
    //     } else {
    //         idActEconomica = $('.list_ae').select2('val');
    //     }
    //     // console.log("AE " + idActEconomica);
    //     if (idActEconomica != '' && idActEconomica != null && idActEconomica != undefined) {

    //         app.alert.show('obtiene_subsector', {
    //             level: 'process',
    //             title: 'Cargando...',
    //         });

    //         app.api.call('GET', app.api.buildURL('GetClasfSectorial/' + idActEconomica), null, {
    //             success: function (data) {
    //                 dataCS = data;
    //                 app.alert.dismiss('obtiene_subsector');
    //                 if (dataCS != '') {
    //                     clasf_sectorial['ActividadEconomica'] = dataCS;
    //                     clasf_sectorial.combinaciones = dataCS['combinaciones'];

    //                     var arrSubSector = [];
    //                     for (var key in clasf_sectorial.combinaciones) { //actividad economica
    //                         // console.log(key);
    //                         var contador = 0;
    //                         for (llave in clasf_sectorial.combinaciones[key]) {  //subsector economico
    //                             arrSubSector[contador] = llave;
    //                             contador++;
    //                         }
    //                     }

    //                     var lista_sse = clasf_sectorial.valorSubsector(arrSubSector);
    //                     clasf_sectorial.subsectoreconomico_list = lista_sse;
    //                     // clasf_sectorial.render();
    //                     $('.list_sse').trigger('change');
    //                     $('.list_se').trigger('change');
    //                 }
    //             },
    //             error: function (e) {
    //                 throw e;
    //             }
    //         });
    //     }
    // },

    // valorSubsector: function (valores) {
    //     //Se obtiene el Subsector dependiendo de la Actividad Economica que le corresponde 
    //     var subsector_list = app.lang.getAppListStrings('subsector_list');
    //     var newSubSector_list = {};
    //     for (var i = 0; i < valores.length; i++) {
    //         var element = valores[i];

    //         Object.keys(subsector_list).forEach(function (key) {

    //             if (key == element) {
    //                 newSubSector_list[key] = subsector_list[key];
    //             }
    //         });
    //     }
    //     return newSubSector_list;
    // },

    // obtenerSubSector: function () {
    //     //Se obtiene el ID del Subsector para setear el Sector que le corresponde
    //     var idActEconomica = clasf_sectorial.ActividadEconomica.ae.id;
    //     var idSubSector = clasf_sectorial.ActividadEconomica.sse.id;

    //     if (idActEconomica != '' && idActEconomica != undefined && idSubSector != '' && idSubSector != null && idSubSector != undefined) {

    //         var arrsector = clasf_sectorial.combinaciones[idActEconomica][idSubSector];
    //         var sector_list = app.lang.getAppListStrings('sector_list');
    //         var newSector_list = {};

    //         for (var keys in arrsector) {
    //             var elemento = keys;
    //             // console.log(elemento);
    //             Object.keys(sector_list).forEach(function (key) {
    //                 if (key == elemento) {
    //                     newSector_list[key] = sector_list[key];
    //                 }
    //             });
    //         }

    //         clasf_sectorial.sectoreconomico_list = newSector_list;
    //         clasf_sectorial.renderlista = 1;
    //         clasf_sectorial.render();
    //         $('.list_sse').select2('val', idSubSector);
    //     }

    //     /*************************************************API INEGI*************************************************/
    //     app.alert.show('obtiene_INEGI', {
    //         level: 'process',
    //         title: 'Cargando...',
    //     });

    //     app.api.call('GET', app.api.buildURL('clasificacionSectorialCNVB/' + idActEconomica + '/' + idSubSector), null, {
    //         success: function (data) {
    //             dataInegi = data;
    //             app.alert.dismiss('obtiene_INEGI');
    //             // console.log(data);
    //             if (dataInegi != '') {
    //                 //Envia los valores de los campos de INEGI a la vista HBS
    //                 clasf_sectorial.ResumenCliente.inegi.inegi_sector = dataInegi['id_sector_inegi'];
    //                 clasf_sectorial.ResumenCliente.inegi.inegi_subsector = dataInegi['id_subsector_inegi'];
    //                 clasf_sectorial.ResumenCliente.inegi.inegi_rama = dataInegi['id_rama_inegi'];
    //                 clasf_sectorial.ResumenCliente.inegi.inegi_subrama = dataInegi['id_subrama_inegi'];
    //                 clasf_sectorial.ResumenCliente.inegi.inegi_clase = dataInegi['id_clase_inegi'];
    //                 clasf_sectorial.ResumenCliente.inegi.inegi_descripcion = dataInegi['id_descripcion_inegi'];
    //                 //Envia los valores al LH para guardarlos en el modulo de Resumen
    //                 clasf_sectorial.ActividadEconomica.inegi_sector = dataInegi['id_sector_inegi'];
    //                 clasf_sectorial.ActividadEconomica.inegi_subsector = dataInegi['id_subsector_inegi'];
    //                 clasf_sectorial.ActividadEconomica.inegi_rama = dataInegi['id_rama_inegi'];
    //                 clasf_sectorial.ActividadEconomica.inegi_subrama = dataInegi['id_subrama_inegi'];
    //                 clasf_sectorial.ActividadEconomica.inegi_clase = dataInegi['id_clase_inegi'];
    //                 clasf_sectorial.ActividadEconomica.inegi_descripcion = dataInegi['id_descripcion_inegi'];

    //                 clasf_sectorial.render();
    //             }
    //         },
    //         error: function (e) {
    //             throw e;
    //         }
    //     });
    // },

    // obtenerSector: function () {
    //     //Se obtiene el ID Sector para poder setear el Macro Sector que le corresponde
    //     var idActEconomica = clasf_sectorial.ActividadEconomica.ae.id;
    //     var idSubSector = clasf_sectorial.ActividadEconomica.sse.id;
    //     var idSector = clasf_sectorial.ActividadEconomica.se.id;

    //     if (idActEconomica != '' && idActEconomica != undefined && idSubSector != '' && idSubSector != undefined &&
    //         idSector != '' && idSector != null && idSector != undefined) {

    //         var arrmacro = clasf_sectorial.combinaciones[idActEconomica][idSubSector][idSector];
    //         var macro_list = app.lang.getAppListStrings('macro_list');
    //         var newMacro_list = {};

    //         for (var i = 0; i < arrmacro.length; i++) {
    //             var elementom = arrmacro[i];
    //             // console.log(elementom);
    //             Object.keys(macro_list).forEach(function (key) {

    //                 if (key == elementom) {
    //                     newMacro_list[key] = macro_list[key];
    //                 }
    //             });
    //         }

    //         clasf_sectorial.tct_macro_sector_ddw_list = newMacro_list;
    //         clasf_sectorial.renderlista = 1;
    //         clasf_sectorial.render();
    //         $('.list_se').select2('val', idSector);
    //     }
    // },

})
