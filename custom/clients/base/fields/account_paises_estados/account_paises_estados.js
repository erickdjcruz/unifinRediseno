({
    events: {
        'change .pais_nacimiento_cstm': 'setValuesEstados',
        'change .estado_nacimiento_cstm': 'setValueEstadoInModel',
    },

    initialize: function(options) {
        this._super('initialize', [options]);

        contextoPaisesEstadosCstm = this;

        this.arrayAllPaisesEstados = [];
        this.paisesList = [];
        this.estadosList = [];

        this.idPaisCstm = "";
        this.idEstadoCstm = "";
        this.valPaisCstm = "";
        this.valEstadoCstm = "";
       
        this.loadPaisesEstados();

        this.model.addValidationTask('validaPaisEstadoCstm', _.bind(this.validaPaisEstadoCstm, this));
    },
    
    _render: function() {
        this._super('_render');
        if ($('[data-fieldname="account_paises_estados"] > span').length > 0) {
          $('[data-fieldname="account_paises_estados"] > span').show();
        }

        //Oculta etiqueta con el icono '+'
        $('[data-type="account_paises_estados"]').find('.ellipsis_inline.record-label').hide();

        //Oculta campos originales de Pais y estado de nacimiento
        $('[data-name="pais_nacimiento_c"]').hide();
        $('[data-name="estado_nacimiento_c"]').hide();

    },

    loadPaisesEstados: function(){
         app.api.call('GET', app.api.buildURL('GetAllCountriesStates'), null, {
                success: _.bind(function (data) {
                    if( data.length > 0 ){

                        contextoPaisesEstadosCstm.arrayAllPaisesEstados = data;

                        var objPaisVacio ={ "id": "0", "namePais": " " };
                        contextoPaisesEstadosCstm.paisesList.push(objPaisVacio);

                        for (let index = 0; index < data.length; index++) {

                            var objPais ={ "id": data[index].id_pais, "namePais": data[index].pais };
                            
                            contextoPaisesEstadosCstm.paisesList.push(objPais);
                            
                        }

                        var uniqueArrayPaises = contextoPaisesEstadosCstm.setUniqueArray(  contextoPaisesEstadosCstm.paisesList );
                        contextoPaisesEstadosCstm.paisesList = uniqueArrayPaises;

                        if( contextoPaisesEstadosCstm.model.get("pais_nacimiento_c") != undefined && contextoPaisesEstadosCstm.model.get("pais_nacimiento_c") != ''){

                            contextoPaisesEstadosCstm.idPaisCstm = contextoPaisesEstadosCstm.model.get("pais_nacimiento_c");

                            var idxPais = contextoPaisesEstadosCstm.searchValueFromArray(contextoPaisesEstadosCstm.paisesList, contextoPaisesEstadosCstm.idPaisCstm);
                            contextoPaisesEstadosCstm.valPaisCstm = contextoPaisesEstadosCstm.paisesList[idxPais].namePais;

                            //$('#pais_nacimiento_cstm').val(contextoPaisesEstadosCstm.idPaisCstm);
                            $('#pais_nacimiento_cstm').select2('val',contextoPaisesEstadosCstm.idPaisCstm);
                            //Se dispara función para llenar campo con estados y una vez lleno el select, se pueda establecer el valor obtenido de la bd
                            contextoPaisesEstadosCstm.setValuesEstados();

                            contextoPaisesEstadosCstm.idEstadoCstm = contextoPaisesEstadosCstm.model.get("estado_nacimiento_c");
                            var idxEstado = contextoPaisesEstadosCstm.searchValueFromArray(contextoPaisesEstadosCstm.estadosList, contextoPaisesEstadosCstm.idEstadoCstm);
                            contextoPaisesEstadosCstm.valEstadoCstm = contextoPaisesEstadosCstm.estadosList[idxEstado].nameEstado;

                            $('#estado_nacimiento_cstm').select2('val',contextoPaisesEstadosCstm.idEstadoCstm);
                            contextoPaisesEstadosCstm.setValueEstadoInModel();

                        }
                                                
                        contextoPaisesEstadosCstm.render();
                    }
                }, this)
            });
    },

    setUniqueArray: function( array ){

        const uniqueMap = {};
        const uniqueArray = array.filter((item) => {
        // Verificar si el id ya existe en el objeto
        if (!uniqueMap[item.id]) {
            // Si no existe, añadirlo al objeto y al array de elementos únicos
            uniqueMap[item.id] = true;
            return true;
        }
            return false;
        });

        return uniqueArray;
    },

    setValuesEstados: function(){
        var valorPais = "";
        if( $('#pais_nacimiento_cstm').val() != undefined ){
            valorPais = $('#pais_nacimiento_cstm').val();
        }else{
            if( contextoPaisesEstadosCstm.idPaisCstm != undefined ){
                valorPais = contextoPaisesEstadosCstm.idPaisCstm;
            }
        }

        if( valorPais == "0" ){
            //En caso de no tener país, se oculta el campo de estado
            $('.estado_nacimiento_cstm').parent().hide();

        }else{

             $('.estado_nacimiento_cstm').parent().show();
            contextoPaisesEstadosCstm.estadosList = [];
            for (let index = 0; index < contextoPaisesEstadosCstm.arrayAllPaisesEstados.length; index++) {
                
                if( contextoPaisesEstadosCstm.arrayAllPaisesEstados[index].id_pais == valorPais ){
                    var objEstado ={ "id": contextoPaisesEstadosCstm.arrayAllPaisesEstados[index].id_estado, "nameEstado": contextoPaisesEstadosCstm.arrayAllPaisesEstados[index].estado };
                                
                    contextoPaisesEstadosCstm.estadosList.push(objEstado);
                }
                
            }
    
            contextoPaisesEstadosCstm.render();
            $('#pais_nacimiento_cstm').select2('val',valorPais);
            if( valorPais == "0" ){
                contextoPaisesEstadosCstm.model.set('pais_nacimiento_c',"");
            }else{

                contextoPaisesEstadosCstm.model.set('pais_nacimiento_c',valorPais);
            }
        }
        //var valorPais = $(e.currentTarget).val();
    },

    setValueEstadoInModel: function(){
        var valorEstado = $('#estado_nacimiento_cstm').val();
        if( valorEstado != undefined ){
          contextoPaisesEstadosCstm.model.set('estado_nacimiento_c',valorEstado);
          contextoPaisesEstadosCstm.idEstadoCstm = contextoPaisesEstadosCstm.model.get("estado_nacimiento_c");
          contextoPaisesEstadosCstm.valEstadoCstm = contextoPaisesEstadosCstm.estadosList[contextoPaisesEstadosCstm.searchValueFromArray(contextoPaisesEstadosCstm.estadosList, valorEstado)].nameEstado;
        }
        
    },

    searchValueFromArray: function(array, valor){

        for (let index = 0; index < array.length; index++) {
            if( array[index].id == valor ){
                return index;
            }
            
        }
    },

    validaPaisEstadoCstm: function (fields, errors, callback) {

        $('#pais_nacimiento_cstm').parent().removeClass('error');
        if( !(this.model.get('tipo_registro_cuenta_c') == '1' || this.model.get('tipo_registro_cuenta_c') == '4' || this.model.get('subtipo_registro_cuenta_c') == '2' || this.model.get('subtipo_registro_cuenta_c') == '7') ){

            var valorPais = $('#pais_nacimiento_cstm').select2('val');
            if( valorPais == '0' ){
                
                $('#pais_nacimiento_cstm').parent().addClass('error');
                errors['pais_nacimiento_c'] = errors['pais_nacimiento_c'] || {};
                errors['pais_nacimiento_c'].required = true;

            }
        }

        callback(null, fields, errors);

    }

})
