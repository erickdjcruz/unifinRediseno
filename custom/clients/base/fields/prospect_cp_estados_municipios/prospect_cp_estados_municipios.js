({
    events: {
        'focusout #cp_po': 'getInfoCPPO',
        'change #estado_po': 'setValueZonaGeografica',
        'change #municipio_po': 'setValueMunicipio',
    },

    initialize: function(options) {
        this._super('initialize', [options]);

        context_cp_po = this;

        this.model.on("sync", this.loadDataCP, this);
    },
    
    _render: function() {
        this._super('_render');

        if ($('[data-fieldname="prospect_cp_estados_municipios"] > span').length > 0) {
          $('[data-fieldname="prospect_cp_estados_municipios"] > span').show();
        }

        
        $('[data-type="prospect_cp_estados_municipios"]').find('.ellipsis_inline.record-label').hide();

        //Ocultando campos pertenecientes al modelo
        $('[data-name="zona_geografica_c"]').hide();
        $('[data-name="municipio_po_c"]').hide();
        $('[data-name="cp_po_c"]').hide();

    },

    loadDataCP:function(){

        this.nameCP = this.model.get('cp_po_c');
        this.nameZonaGeografica = this.model.get('zona_geografica_c');
        this.nameMunicipio = this.model.get('municipio_po_c');

        //this.render();
        if(this.nameCP !== "" && this.nameCP !== null && this.nameCP !== undefined){

            this.idEstadoFromZonaGeografica =  this.convertZonaGeografica(this.nameZonaGeografica);
            //this.estadoDetail =  App.lang.getAppListStrings("zonageografica_list")[this.nameZonaGeografica];
    
            this.getInfoCPPO( this.nameCP, "first" );
        }

    },

    /**
     * 
     * @param {evento o valor de código postal} e 
     * @param {valor que sirve como bandera para saber si se ejecuta la función por primera vez} init 
     */
    getInfoCPPO: function(e, init){

        var cp = ( e.currentTarget == undefined ) ? e : e.currentTarget.value;

        //Solo ejecutar el evento cuando realmente hubo cambio en el campo ya que sin ésta condición cada que nos situabamos en el campo de cp y después nos situabamos en otro
        //aunque no haya cambiado el valor, siempre se ejecutaba la función para consumir el servicio
        if( context_cp_po.nameCP != cp && e.currentTarget != undefined || init == "first" ){

            var str_length = cp.length;
            //Valida formato
            var pattern = /^\d+$/;
            var isNumber = pattern.test(cp);
            if (str_length >= 5 && isNumber){
                console.log("CODIGO POSTAL VALIDO");
                this.model.set("cp_po_c",cp);
                context_cp_po.nameCP = cp;
                app.alert.show('loading_cp_po', {
                        level: 'process',
                        title: 'Cargando información de Código Postal ...',
                    });
    
                    var strUrl = 'DireccionesMexCP/' + cp + '/0';
                    app.api.call('GET', app.api.buildURL(strUrl), null, {
                        success: _.bind(function (data) {
                            app.alert.dismiss('loading_cp_po');
                            context_cp_po.estadosList = data.estados;
                            context_cp_po.municipiosList = data.municipios;
    
                            context_cp_po.estadoDetail = context_cp_po.findNameInArray( context_cp_po.estadosList, context_cp_po.idEstadoFromZonaGeografica, 'estado' );
							var municipiomin = context_cp_po.nameMunicipio;
							if(municipiomin == undefined) {
								municipiomin = '';
							}
							else {
								municipiomin = municipiomin.toLowerCase();
							}
							context_cp_po.municipioDetail = context_cp_po.findNameInArray( context_cp_po.municipiosList, municipiomin, 'municipio' );
                            context_cp_po.render();
    
                            $("#estado_po").trigger('change');
                            $("#municipio_po").trigger('change');
    
                        }, context_cp_po)
                    });
    
    
            }else if( cp != "" ){
                app.alert.show('invalid_cp', {
                    level: 'error',
                    autoClose: true,
                    messages: 'C\u00F3digo Postal inv\u00E1lido'
                });
    
            }else{
                //Si el cp es vacío, se procede a limpiar los campos
                this.nameCP = "";
                this.estadoDetail = "";
                this.municipioDetail = "";
                this.estadosList = [];
                this.municipiosList = [];

                this.render();

                this.model.set('cp_po_c',"");
                this.model.set('zona_geografica_c',"");
                this.model.set('municipio_po_c',"");

            } 
        }
    },

    convertZonaGeografica: function( nameZonaGeografica ){

        var listMapeo = App.lang.getAppListStrings("mapeo_dire_estado_zona_geografica_list");
        for (let clave in listMapeo) {
            if (listMapeo.hasOwnProperty(clave)) {
                if (listMapeo[clave] === nameZonaGeografica) {
                    return clave;
                }
            }
        }
        return null;
    },

    findNameInArray: function( arreglo, id, nombreArreglo) {
        for (let record of arreglo) {

            if( nombreArreglo == "estado" ){
                if (record.idEstado === id) {
                    return record.nameEstado;
                }
            }else{
                if (record.idMunicipio === id) {
                    return record.nameMunicipio;
                }
            }
            
        }
        
        return null;

    },
    

    setValueZonaGeografica: function(e){
        var valueEstado = e.currentTarget.value;
        var valueZonaGeografica = "";
        if( valueEstado != "" ){
            valueZonaGeografica = App.lang.getAppListStrings('mapeo_dire_estado_zona_geografica_list')[valueEstado];

        }

        context_cp_po.model.set("zona_geografica_c",valueZonaGeografica);
    },

    setValueMunicipio: function(e){
        var valueMunicipio = e.currentTarget.value;
        if( valueMunicipio != "" ){
            this.municipioDetail = "";
            context_cp_po.model.set("municipio_po_c",valueMunicipio);
        }

    }

})