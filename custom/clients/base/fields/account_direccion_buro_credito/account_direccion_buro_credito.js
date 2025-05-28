({
    events: {
        //Eventos para nueva dirección
        'focusout #newPostalInputTemp': 'getInfoAboutCP',     //Recupera información asociada a CP
        'change .multiTipoNew': 'updateValueTipoMultiselect',     //Recupera valor para mapeo de tipo
        'change .newCalle': 'updateValueCalle',     //Actualiza calle a modelo
        'change .newNumExt': 'updateValueNumExt',     //Actualiza número exterior a modelo
        'change .newNumInt': 'updateValueNumInt',     //Actualiza número interior a modelo
        'change .newPais': 'populateEdoByPais',     //Actualiza dependencias por País
        'change .newEstado': 'populateCiudadesByEstado',      //Actualiza estado a modelo y filtra ciudad y municipio
        'change .newMunicipio': 'populateColoniasByMunicipio',      //Actualiza municipio a modelo y filtra colonia
        'change .newColonia': 'updateValueColonia',     //Actualiza colonia a modelo
        'change .newCiudad': 'updateValueCiudad',     //Actualiza ciudad a modelo
    },

    initialize: function (options) {
        this._super('initialize', [options]);

        var muestraCampo = this.validaPermiso();
        this.loadFieldDireccionBuro = false;

        if(muestraCampo) this.loadFieldDireccionBuro = true;
        
        contexto_dire_buro = this;

        this.tipo_direccion_list = App.lang.getAppListStrings('dir_tipo_unique_list');
        this.indicador_list = App.lang.getAppListStrings('dir_indicador_unique_list');
        //Tipos de dirección hidden para guardar valores en dire_direccion
        this.def.dir_tipo_list_html = App.lang.getAppListStrings('tipodedirecion_list');
        this.def.indicador_html = App.lang.getAppListStrings('dir_Indicador_list');
        this.def.listMapTipo = App.lang.getAppListStrings('tipo_dir_map_list');
        this.def.listTipo = App.lang.getAppListStrings('dir_tipo_unique_list');
        this.def.listMapIndicador = App.lang.getAppListStrings('dir_indicador_map_list');
        this.def.listIndicador = App.lang.getAppListStrings('dir_indicador_unique_list');


        //Declaración de modelo para nueva dirección
        this.nuevaDireccion = this.limpiaNuevaDireccion();

        this.model.on("sync", this.loadDireccionBuro, this);
    },

    validaPermiso: function () {
    var privilegio_buro = App.user.get("seguimiento_bc_c");

    var permiso = false;

    if (privilegio_buro == 1) {
      permiso = true;
    }

    return permiso;
  },

    loadDireccionBuro: function(){
        var idCliente = this.model.get("id");
        contexto_dire_buro.direccionBuro = [];
        contexto_dire_buro.prev_direccionBuro = [];
        app.api.call('GET', app.api.buildURL('Accounts/' + idCliente + '/link/accounts_dire_direccion_1?filter[0][indicador][$equals]=64'), null, {
                success: _.bind(function (data) {
                    if( data.records.length > 0 ){
                        for (var i = 0; i < data.records.length; i++) {
                            //Asignando valores de los campos
                            var tipo = data.records[i].tipodedireccion.toString();
                            var tipoSeleccionados = '^' + this.def.listMapIndicador[tipo].replace(/,/gi, "^,^") + '^';
                            var indicador = data.records[i].indicador;

                            //Se obtiene campo description para obtener los id (recordar que el description guarda los id separados por pipeline | 
                            //ejemplo: "{$idPais}|{$idEstado}|{$idCiudad}|{$idMunicipio}|{$idColonia}"

                            var description=data.records[i].description;
                            var ids=description.split('|');

                            var identificadorPais=ids[0];
                            var identificadorEstado=ids[1];
                            var identificadorCiudad=ids[2];
                            var identificadorMunicipio=ids[3];
                            var identificadorColonia=ids[4];

                            var valCodigoPostal = data.records[i].codigo_postal_c;
                            var idCodigoPostal=data.records[i].dir_sepomex_dire_direcciondir_sepomex_ida;
                            var valPais = data.records[i].pais_c;
                            var idPais = identificadorPais;
                            var valEstado = data.records[i].estado_c;
                            var idEstado = identificadorEstado;
                            var valMunicipio = data.records[i].municipio_c;
                            var idMunicipio = identificadorMunicipio;
                            var valCiudad = data.records[i].ciudad_c;
                            var idCiudad=identificadorCiudad;
                            var valColonia = data.records[i].colonia_c;
                            var idColonia = identificadorColonia;
                            
                            var calle = data.records[i].calle;
                            var numExt = data.records[i].numext;
                            var numInt = data.records[i].numint;
                            var principal = (data.records[i].principal == true) ? 1 : 0;
                            var inactivo = (data.records[i].inactivo == true) ? 1 : 0;
                            var secuencia = data.records[i].secuencia;
                            var idDireccion = data.records[i].id;
                            var direccionCompleta = data.records[i].name;
                            
                           
                            //Parsea a objeto direccion
                            var direccion = {
                                "tipodedireccion": tipo,
                                "listTipo": contexto_dire_buro.def.listTipo,
                                "tipoSeleccionados": tipoSeleccionados,
                                "indicador": indicador,
                                "valCodigoPostal": valCodigoPostal,
                                "postal": idCodigoPostal,
                                "valPais": valPais,
                                "pais": idPais,
                                "listPais": {},
                                "listPaisFull": {},
                                "valEstado": valEstado,
                                "estado": idEstado,
                                "listEstado": {},
                                "listEstadoFull": {},
                                "valMunicipio": valMunicipio,
                                "municipio": idMunicipio,
                                "listMunicipio": {},
                                "listMunicipioFull": {},
                                "valCiudad": valCiudad,
                                "ciudad": idCiudad,
                                "listCiudad": {},
                                "listCiudadFull": {},
                                "valColonia": valColonia,
                                "colonia": idColonia,
                                "listColonia": {},
                                "listColoniaFull": {},
                                "calle": calle,
                                "numext": numExt,
                                "numint": numInt,
                                "principal": principal,
                                "inactivo": inactivo,
                                "secuencia": secuencia,
                                "id": idDireccion,
                                "direccionCompleta": direccionCompleta
                            };

                            //Agregar dirección
                            contexto_dire_buro.direccionBuro.push(direccion);

                            if(valCodigoPostal!=""){

                                //recupera información asociada a CP
                                var strUrl = 'DireccionesCP/' + valCodigoPostal + '/' + i;
                                app.api.call('GET', app.api.buildURL(strUrl), null, {
                                    success: _.bind(function (data) {
                                        //recupera info
                                        var list_paises = data.paises;
                                        var list_municipios = data.municipios;
                                        //var city_list = App.metadata.getCities();
                                        var list_ciudades=data.ciudades;
                                        var city_list = data.ciudades_metadata;
                                        var list_estados = data.estados;
                                        var list_colonias = data.colonias;
                                        //Poarsea valores para listas
                                        //País
                                        listPais = {};
                                        for (var i = 0; i < list_paises.length; i++) {
                                            listPais[list_paises[i].idPais] = list_paises[i].namePais;
                                        }
                                        contexto_dire_buro.direccionBuro[data.indice].listPais = listPais;
                                        contexto_dire_buro.direccionBuro[data.indice].listPaisFull = listPais;
                                        //Municipio
                                        listMunicipio = {};
                                        for (var i = 0; i < list_municipios.length; i++) {
                                            listMunicipio[list_municipios[i].idMunicipio] = list_municipios[i].nameMunicipio;
                                        }
                                        contexto_dire_buro.direccionBuro[data.indice].listMunicipio = listMunicipio;
                                        contexto_dire_buro.direccionBuro[data.indice].listMunicipioFull = listMunicipio;
                                        //Estado
                                        listEstado = {};
                                        for (var i = 0; i < list_estados.length; i++) {
                                            listEstado[list_estados[i].idEstado] = list_estados[i].nameEstado;
                                        }
                                        contexto_dire_buro.direccionBuro[data.indice].listEstado = listEstado;
                                        contexto_dire_buro.direccionBuro[data.indice].listEstadoFull = listEstado;
                                        //Colonia
                                        listColonia = {};
                                        for (var i = 0; i < list_colonias.length; i++) {
                                            //listColonia[list_colonias[i].idColonia] = list_colonias[i].nameColonia;
                                            listColonia[i]={};
                                            listColonia[i]['idColonia']=list_colonias[i].idColonia;
                                            listColonia[i]['nameColonia']=list_colonias[i].nameColonia;
                                            listColonia[i]['idCodigoPostal']=list_colonias[i].idCodigoPostal;
                                        }
                                        contexto_dire_buro.direccionBuro[data.indice].listColonia = listColonia;
                                        contexto_dire_buro.direccionBuro[data.indice].listColoniaFull = listColonia;
                                        //Ciudad
                                        listCiudad = {}
                                        idSinCiudad='';
                                        /*
                                        ciudades = Object.values(city_list);
                                        for (var [key, value] of Object.entries(contexto_dire_buro.direccionBuro[data.indice].listEstado)) {
                                            for (var i = 0; i < ciudades.length; i++) {
                                                if (ciudades[i].estado_id == key) {
                                                    listCiudad[ciudades[i].id] = ciudades[i].name;
                                                    idSinCiudad = (ciudades[i].name == 'SIN CIUDAD') ? ciudades[i].id : idSinCiudad;
                                                }
                                            }
                                        }
                                        */
                                        listCiudad = {};
                                        for (var i = 0; i < list_ciudades.length; i++) {
                                            listCiudad[list_ciudades[i].idCiudad] = list_ciudades[i].nameCiudad;
                                            idSinCiudad = (list_ciudades[i].nameCiudad == 'SIN CIUDAD') ? list_ciudades[i].idCiudad : idSinCiudad;
                                        }


                                        contexto_dire_buro.direccionBuro[data.indice].ciudad = (contexto_dire_buro.direccionBuro[data.indice].ciudad=='') ? idSinCiudad : contexto_dire_buro.direccionBuro[data.indice].ciudad;
                                        contexto_dire_buro.direccionBuro[data.indice].valCiudad = (contexto_dire_buro.direccionBuro[data.indice].valCiudad =='') ? 'SIN CIUDAD' : contexto_dire_buro.direccionBuro[data.indice].valCiudad;
                                        contexto_dire_buro.direccionBuro[data.indice].listCiudad = listCiudad;
                                        contexto_dire_buro.direccionBuro[data.indice].listCiudadFull = listCiudad;

                                        //Genera objeto con valores previos para control de cancelar
                                        contexto_dire_buro.prev_direccionBuro = app.utils.deepCopy(contexto_dire_buro.direccionBuro);                                        
                                        //Aplica render a campo custom
                                        contexto_dire_buro.render();

                                    }, contexto_dire_buro)
                                });
                            }
                        }

                    }

                }, this)
            });
    },

     _render: function () {
        this._super("_render");

        if ($('[data-fieldname="account_direccion_buro_credito"] > span').length > 0) {
          $('[data-fieldname="account_direccion_buro_credito"] > span').show();
        }

     },

     getInfoAboutCP: function (evt) {

        //Recupera y almacena CP
        var cp = evt.currentTarget.value;
        var str_length = cp.length;
        //Valida formato
        var pattern = /^\d+$/;
        var isNumber = pattern.test(cp);
        if (str_length >= 5 && isNumber){
            if(contexto_dire_buro.direccionBuro[0].valCodigoPostal != cp){
                //LLamada a api custom
                app.alert.show('loading_cp', {
                    level: 'process',
                    title: 'Cargando información de Código Postal ...',
                });

                var strUrl = 'DireccionesCP/' + cp + '/0';
                app.api.call('GET', app.api.buildURL(strUrl), null, {
                    success: _.bind(function (data) {
                        
                        //Limpiar valores de modelo
                        contexto_dire_buro.direccionBuro[0].listPais = {};
                        contexto_dire_buro.direccionBuro[0].listMunicipio = {};
                        contexto_dire_buro.direccionBuro[0].listEstado = {};
                        contexto_dire_buro.direccionBuro[0].listColonia = {};
                        contexto_dire_buro.direccionBuro[0].listCiudad = {};
                        contexto_dire_buro.direccionBuro[0].listPaisFull = {};
                        contexto_dire_buro.direccionBuro[0].listMunicipioFull = {};
                        contexto_dire_buro.direccionBuro[0].listEstadoFull = {};
                        contexto_dire_buro.direccionBuro[0].listColoniaFull = {};
                        contexto_dire_buro.direccionBuro[0].listCiudadFull = {};
                        contexto_dire_buro.direccionBuro[0].pais = "";
                        contexto_dire_buro.direccionBuro[0].estado = "";
                        contexto_dire_buro.direccionBuro[0].municipio = "";
                        contexto_dire_buro.direccionBuro[0].ciudad = "";
                        contexto_dire_buro.direccionBuro[0].colonia = "";
                        contexto_dire_buro.direccionBuro[0].postal = "";
                        contexto_dire_buro.direccionBuro[0].valCodigoPostal = "";

                        //Agrega valores recuperados a modelo
                        if (data.idCP) {
                            //recupera info
                            contexto_dire_buro.direccionBuro[0].valCodigoPostal = cp;
                            contexto_dire_buro.direccionBuro[0].postal = data.idCP;
                            var list_paises = data.paises;
                            var list_municipios = data.municipios;
                            //var city_list = App.metadata.getCities();
                            var city_list = data.ciudades;
                            var list_estados = data.estados;
                            var list_colonias = data.colonias;

                            //Poarsea valores para listas
                            //País
                            listPais = {};
                            for (var i = 0; i < list_paises.length; i++) {
                                listPais[list_paises[i].idPais] = list_paises[i].namePais;
                            }
                            contexto_dire_buro.direccionBuro[0].listPais = listPais;
                            contexto_dire_buro.direccionBuro[0].listPaisFull = listPais;
                            //Municipio
                            listMunicipio = {};
                            for (var i = 0; i < list_municipios.length; i++) {
                                listMunicipio[list_municipios[i].idMunicipio] = list_municipios[i].nameMunicipio;
                            }
                            contexto_dire_buro.direccionBuro[0].listMunicipio = listMunicipio;
                            contexto_dire_buro.direccionBuro[0].listMunicipioFull = listMunicipio;
                            //Estado
                            listEstado = {};
                            for (var i = 0; i < list_estados.length; i++) {
                                listEstado[list_estados[i].idEstado] = list_estados[i].nameEstado;
                            }
                            contexto_dire_buro.direccionBuro[0].listEstado = listEstado;
                            contexto_dire_buro.direccionBuro[0].listEstadoFull = listEstado;
                            //Colonia
                            listColonia = {};
                            for (var i = 0; i < list_colonias.length; i++) {
                                listColonia[list_colonias[i].idColonia] = list_colonias[i].nameColonia;

                            }
                            contexto_dire_buro.direccionBuro[0].listColonia = listColonia;
                            contexto_dire_buro.direccionBuro[0].listColoniaFull = listColonia;
                            //Ciudad
                            /*
                            listCiudad = {}
                            ciudades = Object.values(city_list);
                            cont_dir.nuevaDireccion.estado = (Object.keys(cont_dir.nuevaDireccion.listEstado)[0] != undefined) ? Object.keys(cont_dir.nuevaDireccion.listEstado)[0] : "";
                            for (var [key, value] of Object.entries(cont_dir.nuevaDireccion.listEstado)) {
                                for (var i = 0; i < ciudades.length; i++) {
                                    if (ciudades[i].estado_id == key) {
                                        listCiudad[ciudades[i].id] = ciudades[i].name;
                                    }
                                }
                            }
                            */
                            //Ciudad
                            listCiudad = {};
                            for (var i = 0; i < city_list.length; i++) {
                                listCiudad[city_list[i].idCiudad] = city_list[i].nameCiudad;
                            }
                            contexto_dire_buro.direccionBuro[0].listCiudad = listCiudad;
                            contexto_dire_buro.direccionBuro[0].listCiudadFull = listCiudad;

                            //Ejecuta filtro por dependencia de País
                            contexto_dire_buro.direccionBuro[0].pais = (Object.keys(contexto_dire_buro.direccionBuro[0].listPais)[0] != undefined) ? Object.keys(contexto_dire_buro.direccionBuro[0].listPais)[0] : "";
                            contexto_dire_buro.populateEdoByPais(contexto_dire_buro.direccionBuro[0].pais);
                            contexto_dire_buro.populateCiudadesByEstado(contexto_dire_buro.direccionBuro[0].estado);
                            contexto_dire_buro.populateColoniasByMunicipio(contexto_dire_buro.direccionBuro[0].municipio);

                        }else {
                            app.alert.show('cp_not_found', {
                                level: 'error',
                                autoClose: true,
                                messages: 'C\u00F3digo Postal no encontrado'
                            });
                        }
                        //Ejecuta render a campo
                        cont_dir.render();
                        app.alert.dismiss('loading_cp');
                    }, cont_dir)
                });
            }
        } else if( cp != "" ){
            contexto_dire_buro.direccionBuro[0].valCodigoPostal = "";
            app.alert.show('invalid_cp', {
                level: 'error',
                autoClose: true,
                messages: 'C\u00F3digo Postal inv\u00E1lido'
            });

        } else {
            contexto_dire_buro.direccionBuro[0].valCodigoPostal = "";
            contexto_dire_buro.direccionBuro[0].nuevaDireccion.postal = "";
        }

    },

    updateValueTipoMultiselect: function (evt) {

        //Recupera valor
        var tipoSeleccionados = this.$('#multiTipoNew').val().toString();
        //Limpia borde
        this.$('.multiTipoNew').children().eq(0).css('border-color', '');
        //Parsea valor con mapeo
        contexto_dire_buro.direccionBuro[0].tipodedireccion = "";
        for (var [key, value] of Object.entries(this.def.listMapTipo)) {
            if (value == tipoSeleccionados) {
              contexto_dire_buro.direccionBuro[0].tipodedireccion = key;
            }
        }
        //Actualiza valor a modelo
        contexto_dire_buro.direccionBuro[0].tipoSeleccionados = '^'+tipoSeleccionados.replace(/,/gi, "^,^")+'^';
    },

    updateValueCalle: function(evt) {
        //Recupera valor
        calle = this.$('.newCalle').val();
        calle = calle.toUpperCase();
        //Limipa borde
        this.$('.newCalle').css('border-color', '');
        //Actualiza modelo
        contexto_dire_buro.direccionBuro[0].calle = calle;
    },

    updateValueNumExt: function(evt) {
        //Recupera valor
        numExt = this.$('.newNumExt').val();
        numExt = numExt.toUpperCase();
        //Limpia borde
        this.$('.newNumExt').css('border-color', '');
        //Actualiza modelo
        contexto_dire_buro.direccionBuro[0].numext = numExt;
    },

    updateValueNumInt: function(evt) {
        //Recupera valor
        numInt = this.$('.newNumInt').val();
        numInt = numInt.toUpperCase();
        //Actualiza modelo
        contexto_dire_buro.direccionBuro[0].numint = numInt;
    },

    updateValueColonia: function(evt) {
        //Recupera valor
        var idColonia = this.$(evt.currentTarget).val();
        //Actualiza modelo
       contexto_dire_buro.direccionBuro[0].colonia = idColonia;

    },

    updateValueCiudad: function(evt) {
        //Recupera valor
        var idCiudad = this.$(evt.currentTarget).val();
        //Actualiza modelo
        contexto_dire_buro.direccionBuro[0].ciudad = idCiudad;
    },

    populateEdoByPais: function (evt) {
        //Recuperar valores por pais
        var id_pais = (evt.length == undefined) ? $(evt.currentTarget).val() : evt;
        var filtroEstado = this.arraySearch(contexto_dire_buro.direccionBuro[0].listEstadoFull, id_pais, 'pais');
        var filtroMunicipio = this.arraySearch(contexto_dire_buro.direccionBuro[0].listMunicipioFull, id_pais, 'pais');
        var filtroCiudad = this.arraySearch(contexto_dire_buro.direccionBuro[0].listCiudadFull, id_pais, 'pais');
        var filtroColonia = this.arraySearch(contexto_dire_buro.direccionBuro[0].listColoniaFull, id_pais, 'pais');

        //Establece nuevos valores
        contexto_dire_buro.direccionBuro[0].pais = id_pais;
        contexto_dire_buro.direccionBuro[0].listEstado = filtroEstado;
        contexto_dire_buro.direccionBuro[0].listMunicipio = filtroMunicipio;
        contexto_dire_buro.direccionBuro[0].listCiudad = filtroCiudad;
        contexto_dire_buro.direccionBuro[0].listColonia = filtroColonia;

        //Limpia dependencias
        contexto_dire_buro.direccionBuro[0].municipio = (Object.keys(contexto_dire_buro.direccionBuro[0].listMunicipio)[0] != undefined) ? Object.keys(contexto_dire_buro.direccionBuro[0].listMunicipio)[0] : "";
        contexto_dire_buro.direccionBuro[0].estado = (Object.keys(contexto_dire_buro.direccionBuro[0].listEstado)[0] != undefined) ? Object.keys(contexto_dire_buro.direccionBuro[0].listEstado)[0] : "";
        contexto_dire_buro.direccionBuro[0].colonia = (Object.keys(contexto_dire_buro.direccionBuro[0].listColonia)[0] != undefined) ? Object.keys(contexto_dire_buro.direccionBuro[0].listColonia)[0] : "";
        contexto_dire_buro.direccionBuro[0].ciudad = (Object.keys(contexto_dire_buro.direccionBuro[0].listCiudad)[0] != undefined) ? Object.keys(contexto_dire_buro.direccionBuro[0].listCiudad)[0] : "";
        this.render();
    },

    populateCiudadesByEstado: function (evt) {
        //Recuperar valores por estado
        var idEstado = (evt.length == undefined) ? $(evt.currentTarget).val() : evt;
        var filtroCiudad = this.arraySearch(contexto_dire_buro.direccionBuro[0].listCiudadFull, idEstado, 'estado');
        var filtroMunicipio = this.arraySearch(contexto_dire_buro.direccionBuro[0].listMunicipioFull, idEstado, 'estado');

        //Establece nuevos valores
        contexto_dire_buro.direccionBuro[0].estado = idEstado;
        contexto_dire_buro.direccionBuro[0].listCiudad = filtroCiudad;
        contexto_dire_buro.direccionBuro[0].listMunicipio = filtroMunicipio;

        //Establece ids default
        contexto_dire_buro.direccionBuro[0].ciudad = (Object.keys(contexto_dire_buro.direccionBuro[0].listCiudad)[0] != undefined) ? Object.keys(contexto_dire_buro.direccionBuro[0].listCiudad)[0] : "";
        contexto_dire_buro.direccionBuro[0].municipio = (Object.keys(contexto_dire_buro.direccionBuro[0].listMunicipio)[0] != undefined) ? Object.keys(contexto_dire_buro.direccionBuro[0].listMunicipio)[0] : "";

        //Actualiza Colonia
        this.populateColoniasByMunicipio(contexto_dire_buro.direccionBuro[0].municipio);
        this.render();
    },

    populateColoniasByMunicipio: function (evt) {

        //Recuperar valores por pais
        var id_municipio = (evt.length == undefined) ? $(evt.currentTarget).val() : evt;
        var filtroColonia = this.arraySearch(contexto_dire_buro.direccionBuro[0].listColoniaFull, id_municipio, 'municipio');

        //Establece nuevos valores
        contexto_dire_buro.direccionBuro[0].municipio = id_municipio;
        contexto_dire_buro.direccionBuro[0].listColonia = filtroColonia;

        //Establece ids default
        contexto_dire_buro.direccionBuro[0].colonia = (Object.keys(contexto_dire_buro.direccionBuro[0].listColonia)[0] != undefined) ? Object.keys(contexto_dire_buro.direccionBuro[0].listColonia)[0] : "";
        this.render();
    },

    arraySearch: function (arr, val, tipo) {
        var filtroLista = {};
        if (tipo == 'pais') {
            val = (val.length==1)? "00"+val:val;
            val = (val.length==2)? "0"+val:val;
        }
        for (var [key, value] of Object.entries(arr)) {
            if (key.startsWith(val)) {
              filtroLista[key]=value;
            }
        }
        return filtroLista;
    },

        limpiaNuevaDireccion: function(){
        //Declaración de modelo para nueva dirección
        var nuevaDireccion = {
            "tipodedireccion":"",
            "listTipo":this.def.listTipo,
            "indicador":"",
            "listIndicador":this.def.listIndicador,
            "valCodigoPostal":"",
            "postal":"",
            "valPais":"",
            "pais":"",
            "listPais":{},
            "listPaisFull":{},
            "valEstado":"",
            "estado":"",
            "listEstado":{},
            "listEstadoFull":{},
            "valMunicipio":"",
            "municipio":"",
            "listMunicipio":{},
            "listMunicipioFull":{},
            "valCiudad":"",
            "ciudad":"",
            "listCiudad":{},
            "listCiudadFull":{},
            "valColonia":"",
            "colonia":"",
            "listColonia":{},
            "listColoniaFull":{},
            "calle":"",
            "numext":"",
            "numint":"",
            "principal":"",
            "inactivo":"",
            "secuencia":"",
            "id":"",
            "direccionCompleta":"",
            "bloqueado":"",
            "editaCiudad":0,
            "validaDireccion":false
        };
        return nuevaDireccion;

    },
    /**
     * When data changes, re-render the field only if it is not on edit (see MAR-1617).
     * @inheritdoc
     */
    bindDataChange: function () {
        this.model.on('change:' + this.name, function () {
            if (this.action !== 'edit') {
                this.render();
            }
        }   , this);
    },

})