({
    extendsFrom: 'CreateView',

    events: {
        'click [name=monto_c]': 'formatcoin',
        'click [name=amount]': 'formatcoin',
        'click [name=ca_pago_mensual_c]': 'formatcoin',
        'click [name=ca_importe_enganche_c ]': 'formatcoin',
        'keydown [name=monto_c]': 'checkmoney',
        'keydown [name=amount]': 'checkmoney',
        'keydown [name=ca_pago_mensual_c]': 'checkmoney',
        'keydown [name=ca_importe_enganche_c ]': 'checkmoney',
    },

    tipoDePersona: null,
    prospecto: null,
    productoUsuario: null,
    multiProducto: null,
    productos: null,
    multilinea_prod: null,
    exist_PRodFinanciero: false,

    initialize: function (options) {
        self = this;
        window.bandera = 0;
        this._super("initialize", [options]);
        this.on('render', this.ocultaFunc, this);
        $(document).ready(function () {
            window.bandera = 1;
        });

        this.idAccount = options.context.attributes.idAccount;
        this.idNameAccount = options.context.attributes.idNameAccount;
        if (this.idAccount != undefined || this.idAccount != null) {
            this.model.set('account_id', this.idAccount);
            this.model.set('account_name', this.idNameAccount);
        }
        /*
          Author: Adrian Arauz 2018-08-28
          funcion: Validar acceso para creación de solicitudes. No debe permitir crear solicitudes si usuario tiene rol: "Gestión Comercial"
        */
        this.on('render', this._rolnocreacion, this);
        /*
          EJC_14/01/2021
          funcion: Valida tener alguna comunicación previa, llamada o reunión"
        */
        this.model.addValidationTask('contacto_previo', _.bind(this.ContactoPrevio, this));
        this.model.addValidationTask('buscaDuplicados', _.bind(this.buscaDuplicados, this));
        this.model.addValidationTask('valida_direc_indicador', _.bind(this.valida_direc_indicador, this));
        this.model.addValidationTask('check_activos_seleccionados', _.bind(this.validaClientesActivos, this));
        this.model.addValidationTask('check_activos_index', _.bind(this.validaActivoIndex, this));
        //this.model.addValidationTask('check_aforo', _.bind(this.valiaAforo, this));
        //this.model.addValidationTask('check_factoraje', _.bind(this.validaRequeridosFactoraje, this));
        //this.model.addValidationTask('check_condicionesFinancieras', _.bind(this.condicionesFinancierasCheck, this));
        // this.model.addValidationTask('check_condicionesFinancierasIncremento', _.bind(this.condicionesFinancierasIncrementoCheck, this));
        this.model.addValidationTask('checkpromotorFactoraje', _.bind(this.validacrearfactoraje, this));
        //Ajuste Salvador Lopez <salvador.lopez@tactos.com.mx>
        //Validación para evitar asociar una Persona que no sea cliente
        this.model.addValidationTask('check_person_type', _.bind(this.personTypeCheck, this));
        this.model.on("change:porciento_ri_c", _.bind(this.calcularRI, this));
        this.model.on("change:ca_importe_enganche_c", _.bind(this.calcularPorcientoRI, this));

        /*@author Carlos Zaragoza Ortiz
        * @version 1
        * @date 12/10/2015
        * Validar la cantidad de operaciones que se pueden generar para un cliente/Prospecto (solo una)
        * @type Event
        * */
        //this.model.addValidationTask('check_operaciones_permitidas', _.bind(this.validaOperacionesPermitidasPorCuenta, this));

        /*@author Carlos Zaragoza Ortiz
         * @version 1
         * @date 12/10/2015
         * Validar la cantidad de operaciones que se pueden generar para un cliente/Prospecto (solo una)
         * @type Event
         * */
        //this.model.addValidationTask('check_valida_activo', _.bind(this.validaActivo, this));
        //this.model.addValidationTask('check_valida_subactivo', _.bind(this.validaSubActivo, this));
        //this.model.addValidationTask('check_valida_subactivo2', _.bind(this.validaSubActivo2, this));
        //this.model.addValidationTask('check_valida_subactivo3', _.bind(this.validaSubActivo3, this));

        //this.model.addValidationTask('check_valida_multiactivo', _.bind(this.validaMultiactivo, this));

        //this.model.addValidationTask('check_condiciones_financieras', _.bind(this.validaCondicionesFinanceras, this));
        this.model.addValidationTask('setRequiredAforoTipoFactoraje', _.bind(this.setRequiredAforoTipoFactoraje, this));
        //this.model.addValidationTask('check_requeridos', _.bind(this.validaDatosRequeridos, this));
        this.model.addValidationTask('producto_financiero_c', _.bind(this.reqPrpductoFinanciero, this));

        /*
        * @author F. Javier G. Solar
        * 19/07/2018
        * Valida que los campos de la cuenta esten completos.
        * **/
        this.model.addValidationTask('Valida al Guardar', _.bind(this.validacion_proceso_guardar, this));
        this.model.addValidationTask('valida_requeridos', _.bind(this.valida_requeridos, this));
        this.model.addValidationTask('valida_no_vehiculos', _.bind(this._Validavehiculo, this));

        /*
        * @author Carlos Zaragoza Ortiz
        * @version 1
        * Ocultar opciones de cotización, contrato y línea de crédito al crear la oportunidad y bloquear el campo. Los cambios posteriores se controlan desde UNICS
        * */
        var new_opctions = app.lang.getAppListStrings('tipo_operacion_list');
        Object.keys(new_opctions).forEach(function (key) {
            if (key == 2 || key == 3 || key == 4) {
                delete new_opctions[key];
            }
        });
        this.model.fields['tipo_operacion_c'].options = new_opctions;


        /* @author Carlos Zaragoza Ortiz
         * @version 1
         * En operaciones de solicitud de crédito quitar opción de pipeline en lista de Forecast
         * */
        /*
        var opciones_forecast = app.lang.getAppListStrings('forecast_list');
        var operacion = this.model.get('tipo_operacion_c');
        Object.keys(opciones_forecast).forEach(function(key){
            if(key == "Pipeline"){
                if(operacion == 1){
                    delete opciones_forecast[key];
                }
            }

        });
        this.model.fields['forecast_c'].options = opciones_forecast;
        */

        //Obtiene fecha default para fecha de cierre, debe ser el ultimo día del mes corriente
        /*
        var fecha = new Date();
        var f = new Date(fecha.getFullYear(), fecha.getMonth() + 1, 1) - (24*60*60*1000);
        var FechaCierre = new Date(f);
        this.model.set('date_closed', FechaCierre.getFullYear() + '-' + (FechaCierre.getMonth()+1) + '-' + FechaCierre.getDate());
        */
        this.model.addValidationTask('check_monto_c', _.bind(this._ValidateAmount, this));
        //Valida que sea producto Leasing y SI, para setear estatus_c como 1 (En validacion Comercial)
        this.model.addValidationTask('estatus_y_etapa', _.bind(this.setValidacionComercial, this));

        this.model.on('change:tipo_producto_c', this._ActualizaEtiquetas, this);


        /*
         * @author Carlos Zaragoza Ortiz
         * @version 1
         * Crear estatus "Oportunidad de prospecto" y validar en la creación de la oportunidad si el estatus con el que debe nacer es "Oportunidad de prospecto" o "Integración de expediente" (\custom\modules\Opportunities\clients\base\views\create-actions\create-actions.js)
         * */
        this.verificaOperacionProspecto();
        //this.getCurrentYearMonth("loading");
        //this.model.on("change:anio_c", _.bind(this.getCurrentYearMonth, this));
        //Evento para el campo monto cuando la solicitud es Credito SOS
        this.model.on("change:negocio_c", _.bind(this.bloqueamonto, this));
        this.model.on("change:account_id", _.bind(this.cuenta_asociada, this));
        //Funcion para obtener las oportunidades de leasing de la cuenta asi como valida la lista de productos
        this.set_lista_productos();
        //Oculta campo de control para director de la solicitud
        $('[data-name="director_solicitud_c"]').hide();
        this.model.on("change:tipo_producto_c", _.bind(this.showSubpanels, this));
        this.model.addValidationTask('benef_suby', _.bind(this.reqBenefSuby, this));
        this.model.addValidationTask('duplicateBenefeSuby', _.bind(this._duplicateBenefeSuby, this));
        this.model.on("change:area_benef_c", _.bind(this.showfieldBenef, this));
        this.model.on("change:subyacente_c", _.bind(this.showfieldSuby, this));
        this.showSubpanels();
        this.showfieldBenef();
        this.showfieldSuby();
        //this.model.addValidationTask('benef_req', _.bind(this.reqBenfArea, this));
        // this.model.on("change:producto_financiero_c", _.bind(this.producto_financiero, this));
        // this.Updt_OptionProdFinan(); # se comenta para hacer listas dependiente
        this.model.on("change:negocio_c", _.bind(this.Updt_OptionProdFinan, this));
        this.model.on('change:negocio_c', this.verificaOperacionProspecto, this);
        this.model.on('change:producto_financiero_c', this.asesorCCP, this);
        this.adminUserCartera();
        //TIPO DE PRODUCTO TARJETA DE CREDITO - OBTIENE EL 10% DE LA SUMA DE LAS LINEAS DE CREDITO AUTORIZADAS
        this.model.on("change:tipo_producto_c", _.bind(this.montoTenPercentCreditCard, this));

        this.model.addValidationTask('dataOrigen', _.bind(this.dataOrigen, this));
        //VALIDA PERSONA FISICA CON TIPO DE PRODUCTO TARJETA DE CREDITO
        this.model.addValidationTask('validaPFCreditCard', _.bind(this.validaPFCreditCard, this));
        //VALIDA EL MONTO DEL TIPO DE PRODUCTO TARJETA DE CREDITO QUE NO SUPERE EL CONTROL DEL MONTO
        this.model.addValidationTask('validaMontoCreditCard', _.bind(this.validaMontoCreditCard, this));
        //VALIDA SI YA EXISTE UNA SOLICITUD DE TIPO DE PRODUCTO TARJETA DE CREDITO
        this.model.addValidationTask('validaTipoCreditCard', _.bind(this.validaTipoCreditCard, this));
        //VALIDA SI YA EXISTEN SOLICITUDES CON LÍNEAS DE CREDITO AUTORIZADAS ANTES DE CREAR UNA SOLICITUD CON TIPO PRODUCTO TARJETA DE CREDITO
        this.model.addValidationTask('validaSolCreditCard', _.bind(this.validaSolCreditCard, this));
        this.model.addValidationTask('dataOrigen',_.bind(this.dataOrigen, this));
		this.model.addValidationTask('SOCInicio', _.bind(this.SOCInicio, this));
		this.model.addValidationTask('negocio_c', _.bind(this.negocios, this));
		this.model.addValidationTask('validaCreacionLeasing', _.bind(this.validaCreacionLeasing, this));
    },

    /* producto_financiero: function () {
         if(this.model.get('producto_financiero_c') == 43) {
           $('[data-name="ce_destino_c"]').show();
           $('[data-name="ce_tasa_c"]').show();
           $('[data-name="ce_plazo_c"]').show();
           $('[data-name="ce_moneda_c"]').show();
           $('[data-name="ce_cambio_c"]').show();
           $('[data-name="ce_apertura_c"]').show();
           $('[data-name="ce_comentarios_c"]').show();
           $('[data-name="credito_estructurado"]').show();
         }
         else
         {
           $('[data-name="ce_destino_c"]').hide();
           $('[data-name="ce_tasa_c"]').hide();
           $('[data-name="ce_plazo_c"]').hide();
           $('[data-name="ce_moneda_c"]').hide();
           $('[data-name="ce_cambio_c"]').hide();
           $('[data-name="ce_apertura_c"]').hide();
           $('[data-name="ce_comentarios_c"]').hide();
           $('[data-name="credito_estructurado"]').hide();
         }
     },*/

    _render: function () {
        this._super("_render");
		    /*if (window.render != 1) this.obtieneCondicionesFinancieras();
        this.model.on("change:plazo_c", _.bind(function () {
            this.obtieneCondicionesFinancieras();
        }, this));*/
        // CVV - 28/03/2016 - Se reemplaza por control de condiciones financieras
        this.model.on("change:es_multiactivo_c", _.bind(function () {
            if (this.model.get('es_multiactivo_c') == true) {
                this.model.set('activo_c', '');
            } else {
                this.model.set('multiactivo_c', '');
            }
        }, this));
        this.$('div[data-name=plazo_ratificado_incremento_c]').hide();
        this.model.on("change:f_tipo_factoraje_c", _.bind(function () {
            if (this.model.get('f_tipo_factoraje_c') == '1') {
                this.model.set('f_aforo_c', '10.000000');
            } else if (this.model.get('f_tipo_factoraje_c') == '4') {
                this.model.set('f_aforo_c', '0.000000');
            } else {
                this.model.set('f_aforo_c', '0.000000');
            }
        }, this));
        //* Quitamos los campos Vendedor y Comisión
        this.$('div[data-name=opportunities_ag_vendedores_1_name]').hide();
        this.$('div[data-name=comision_c]').hide();
        //Validaciontask
        this.model.on("change:tipo_producto_c", _.bind(function () {
            if (this.model.get('tipo_producto_c') == '4') {
                if (this.tipoDePersona) {
                    app.alert.show("tipoPersonaFisica", {
                        level: "error",
                        title: "No puedes generar factoraje para Personas Fisicas",
                        autoClose: false
                    });
                    //this.model.set('tipo_producto_c','1');
                }
                //this.obtieneCondicionesFinancieras();
            }
            if (this.model.get('tipo_producto_c') == '4') {
                //Oculta los campos
                this.$('div[data-name=activo_c]').hide();
                this.$('div[data-name=sub_activo_c]').hide();
                this.$('div[data-name=sub_activo_2_c]').hide();
                this.$('div[data-name=sub_activo_3_c]').hide();
            }
            //this.obtieneCondicionesFinancieras();
            //this.verificaOperacionProspecto();
        }, this));


        /*
         * @author Carlos Zaragoza
         * @version 1
         * Validamos que se pueda usar el campo vacío en el forecast. Agregar opción "…" en forecast time para indicar que se cierra este mes (esta opción se selecciona en automático si la fecha de cierre está dentro del mes corriente) Si la fecha de cierre esta fuera del mes corriente calcular si es 30, 60, etc.
         * */
        // CVV - 28/03/2016 - El campo de fecha de cierre se elimino del layout
        /*
       this.model.on("change:date_closed", _.bind(function() {
           var fecha_cierre = this.model.get('date_closed');
           var fecha_actual = new Date();
           fecha_cierre  = new Date(fecha_cierre+"T12:00:00Z");
           var months;
           months = (fecha_cierre.getFullYear() - fecha_actual.getFullYear()) * 12;
           months -= fecha_actual.getMonth();
           months += fecha_cierre.getMonth();

           if(months == 0 ){
               this.model.set('forecast_time_c',"");
           }
           if(months == 1){
               this.model.set('forecast_time_c',"30");
           }
           if(months == 2){
               this.model.set('forecast_time_c',"60");
           }
           if(months == 3){
               this.model.set('forecast_time_c',"90");
           }
           if(months >= 4){
               this.model.set('forecast_time_c',"90mas");
           }
       },this));
       */
        /* END CUSTOMIZATION */

        this.model.on("change:monto_c", _.bind(function () {
            /*if(parseFloat(this.model.get('amount')) > parseFloat(this.model.get('monto_c'))){
                app.alert.show("Monto a operar invalido", {
                        level: "error",
                        title: "El monto a operar no puede ser mayor al monto de la linea.",
                        autoClose: false
                });
            }*/
            this.model.set('amount', this.model.get('monto_c'));
            var str = this.model.get('monto_c');
            var n = str.length;
            if (n > 22) {
                app.alert.show('monto', {
                    level: 'error',
                    autoClose: false,
                    messages: 'El campo \"Monto de l&iacutenea\" no debe exceder de 15 digitos. Favor de corregir.'
                });
                this.model.set('monto_c', 0);
            }
        }, this));

        // this.model.on("change:amount", _.bind(function () {
        //     if (parseFloat(this.model.get('amount')) > parseFloat(this.model.get('monto_c'))) {
        //         app.alert.show("Moto a operar invalido", {
        //             level: "error",
        //             title: "El monto a operar no puede ser mayor al monto de la linea.",
        //             autoClose: false
        //         });
        //         this.model.set('amount', this.model.get('monto_c'));
        //     }
        // }, this));

        this.model.on("change:account_id", _.bind(function () {
            this.verificaOperacionProspecto();
        }, this));

        // CVV - 28/03/2016 - Los campos de activo se reemplazaron por el control de condiciones financieras
        /*
        this.model.on("change:activo_c", _.bind(function(){
            //console.log("Activo cambió");
            //console.log(this.model.get('sub_activo_c'));
            this.model.set('sub_activo_c','');
        },this));

        this.model.on("change:sub_activo_c", _.bind(function(){
            //console.log("Activo cambió");
            //console.log(this.model.get('sub_activo_c'));
            this.model.set('sub_activo_2_c','');
        },this));

        this.model.on("change:sub_activo_2_c", _.bind(function(){
            //console.log("Activo cambió");
            //console.log(this.model.get('sub_activo_c'));
            this.model.set('sub_activo_3_c','');
        },this));
        */

        //Actualiza las etiquetas de acuerdo al tipo de operacion Solicitud/Cotizacion
        //Si la operacion es Cotización o Contrato cambiar etiqueta de "Monto de línea" a "Monto colocación"
        if (this.model.get('tipo_operacion_c') == '3' || this.model.get('tipo_operacion_c') == '4') {
            this.$("div.record-label[data-name='monto_c']").text("Monto colocaci\u00F3n");
            this.$("div.record-label[data-name='tipo_de_operacion_c']").text("Tipo de operaci\u00F3n");
        }
        else {
            this.$("div.record-label[data-name='monto_c']").text("Monto de l\u00EDnea");
            this.$("div.record-label[data-name='tipo_de_operacion_c']").text("Tipo de solicitud");
        }
        if (this.model.get('tipo_operacion_c') == '1' && this.model.get('tipo_de_operacion_c') == 'RATIFICACION_INCREMENTO') {
            this.$("div.record-label[data-name='monto_c']").text("Monto del incremento");
        }
        if (this.model.get('tipo_producto_c') == '1') {
            this.$("div.record-label[data-name='ca_importe_enganche_c']").text("Renta Inicial");
        } else {
            this.$("div.record-label[data-name='ca_importe_enganche_c']").text("Enganche");

        }
        if (this.model.get('tipo_producto_c') == '4') {
            this.$("div.record-label[data-name='porcentaje_ca_c']").text("Comisi\u00F3n");
        } else {
            this.$("div.record-label[data-name='porcentaje_ca_c']").text("Comisi\u00F3n por apertura");

        }
        if (this.model.get('tipo_producto_c') == '3') {
            this.$("div.record-label[data-name='porcentaje_renta_inicial_c']").text("Porcentaje de Enganche");
        } else {
            this.$("div.record-label[data-name='porcentaje_renta_inicial_c']").text("Porcentaje Renta Inicial");

        }

        //console.log(this.model.get('ratificacion_incremento_c'));
        if (this.model.get('ratificacion_incremento_c') == false) {
            //Oculta campos para condiciones financieras
            //this.$('div[data-name=ri_ca_tasa_c]').hide();
            //this.$('div[data-name=ri_deposito_garantia_c]').hide();
            this.$('div[data-name=ri_porcentaje_ca_c]').hide();
            //this.$('div[data-name=ri_porcentaje_renta_inicial_c]').hide();
            //this.$('div[data-name=ri_vrc_c]').hide();
            //this.$('div[data-name=ri_vri_c]').hide();
            this.$('div[data-name=monto_ratificacion_increment_c]').hide();
            this.$('div[data-name=plazo_ratificado_incremento_c]').hide();
            this.$('div[data-name=ri_usuario_bo_c]').hide();
        } else {
            //Prende los campos
            //this.$('div[data-name=ri_ca_tasa_c]').show();
            //this.$('div[data-name=ri_deposito_garantia_c]').show();
            this.$('div[data-name=ri_porcentaje_ca_c]').show();
            //this.$('div[data-name=ri_porcentaje_renta_inicial_c]').show();
            //this.$('div[data-name=ri_vrc_c]').show();
            //this.$('div[data-name=ri_vri_c]').show();
            this.$('div[data-name=monto_ratificacion_increment_c]').show();
            this.$('div[data-name=plazo_ratificado_incremento_c]').show();
            this.$('div[data-name=ri_usuario_bo_c]').show();
        }
        //Oculta etiqueta del campo custom pipeline_opp
        $("div.record-label[data-name='pipeline_opp']").attr('style', 'display:none;');
        $('[data-name="tct_etapa_ddw_c"]').attr('style', 'pointer-events:none');
        $('[data-name="estatus_c"]').attr('style', 'pointer-events:none');

        //Oculta campo de solicitud para Vobo
        $('[data-name="vobo_descripcion_txa_c"]').hide();
        $('[data-name="director_notificado_c"]').hide();
        this.$(".record-cell[data-name='blank_space']").hide();

        // Visualiza el campo check de Administracion de cartera del usuario
        $('[data-name="admin_cartera_c"]').attr('style', 'pointer-events:none');

        if (this.model.get('admin_cartera_c') == true && app.user.attributes.admin_cartera_c == 1 && app.user.attributes.config_admin_cartera == true) {
            //Bloquea campos de monto cuando es Administrador de cartera
            $('[data-name="monto_c"]').attr('style', 'pointer-events:none'); //Monto de línea
            $('[data-name="amount"]').attr('style', 'pointer-events:none'); //Monto a operar
            $('[data-name="ca_pago_mensual_c"]').attr('style', 'pointer-events:none'); //Pago mensual
            $('[data-name="ca_importe_enganche_c"]').attr('style', 'pointer-events:none'); //Pago unico
            $('[data-name="porciento_ri_c"]').attr('style', 'pointer-events:none'); //% Pago unico
        }
		this.$('[data-name="condiciones_financieras_quantico"]').hide();
		$('[data-name="condiciones_financieras_quantico"]').hide();
    },

    adminUserCartera: function () {
        //Valida si el usuario es Administrador de cartera y que el servicio este activo en el config_override
        if (app.user.attributes.admin_cartera_c == 1 && app.user.attributes.config_admin_cartera == true) {
            this.model.set('admin_cartera_c', true);
        }
    },

    dataOrigen: function (fields, errors, callback) {
        var userprodprin = this.model.get('tipo_producto_c');
        var textmsg = "";
        var tipom = "";
        if (this.model.get('account_id')!=undefined){

            app.api.call('GET', app.api.buildURL('Accounts/' + this.model.get('account_id')), null, {
                success: _.bind(function (cuenta) {

                    app.api.call('GET', app.api.buildURL('GetProductosCuentas/' + cuenta.id), null, {
                        success: function (data) {
                            Productos = data;
                            //ResumenProductos = Productos;
                            //estatus_atencion - tipo_producto_c
                            _.each(Productos, function (value, key) {
                                var tipoProducto = Productos[key].tipo_producto;
                                var statusProducto = Productos[key].status_management_c;
                                if (tipoProducto == userprodprin && statusProducto == '3') {
                                    textmsg = 'La cuenta está marcada como <b>Cancelada</b>. Active la cuenta para continuar.';
                                    tipom = "error";
                                }
                            });
                            if (textmsg != "") {
                                App.alert.show("producto_cancelado", {
                                    level: tipom,
                                    messages: textmsg,
                                    autoClose: false
                                });
                                errors['tipo_producto_c'] = "cuenta cancelada";
                                errors['tipo_producto_c'].required = true;
                                /****************************************/
                            }
                            callback(null, fields, errors);
                        },
                        error: function (e) {
                            throw e;
                        }
                    });

                }, self),
            });
        }else {
            callback(null, fields, errors);
        }
    },

    /*
    *Victor Martinez Lopez
    * Valida que no se pueda crear un producto factoraje para personas fisicas
     */
    validacrearfactoraje: function (fields, errors, callback) {
        if (this.model.get('tipo_producto_c') == '4') {
            if (this.tipoDePersona) {
                app.alert.show("tipoPersonaFisica", {
                    level: "error",
                    title: "No puedes generar factoraje para Personas F&iacute;sicas",
                    autoClose: false
                });
                errors['tipo_producto_c'] = "No puedes generar factoraje para Personas F&iacute;sicas";
                errors['tipo_producto_c'].required = true;
            }
        }
        callback(null, fields, errors);
    },
    /*
    * @Author F. Javier G. Solar
    * 23-07-2018
    * Valida campos requeridos antes de crear solicitud
    * */
    validacion_proceso_guardar: function (fields, errors, callback) {

        self = this;
        var producto = this.model.get('tipo_producto_c');
        //En caso de ser una solicitud de Unilease, se deben de validar los mismos campos que Leasing id=1
        if (producto == '9') {
            producto = '1';
        }

        if (this.model.get('account_id') != "" && this.model.get('account_id') != null) {
            app.api.call('GET', app.api.buildURL('ObligatoriosCuentasSolicitud/' + this.model.get('account_id') + '/1/' + producto), null, {
                success: _.bind(function (data) {

                    if (data != "") {
                        var titulo = "Campos Requeridos en Cuentas";
                        var nivel = "error";
                        var mensaje = "Hace falta completar la siguiente informaci&oacuten en la <b>Cuenta<b>:<br>" + data;


                        app.error.errorName2Keys['custom_message1'] = 'Falta información en campos requeridos de la cuenta';
                        errors['account_name_1'] = errors['account_name_1'] || {};
                        errors['account_name_1'].custom_message1 = true;
                        errors['account_name_1'].required = true;
                        self.mensajes(titulo, mensaje, nivel);

                    }
                    callback(null, fields, errors);

                }, self),
            });
        } else {
            callback(null, fields, errors);
        }

    },

    mensajes: function (descripcion, texto, nivel) {
        app.alert.show(descripcion, {
            level: nivel,
            messages: texto,
        });
    },

    _ValidateAmount: function (fields, errors, callback) {
        if (parseFloat(this.model.get('monto_c')) <= 0 && this.model.get('producto_financiero_c') != 40 && this.model.get('producto_financiero_c') != 78 && this.model.get('admin_cartera_c') != true) {
            errors['monto_c'] = errors['monto_c'] || {};
            errors['monto_c'].required = true;

            app.alert.show("Monto de Linea requerido", {
                level: "error",
                title: "Error",
                messages: "Monto de L\u00EDnea debe ser mayor a cero",
                autoClose: false
            });
        }
        //Validación Crédito Corto Plazo
        if (this.model.get('producto_financiero_c') == '78' && (parseFloat(this.model.get('monto_c')) < 500000 || parseFloat(this.model.get('monto_c')) > 10000000)) {
            delete errors.monto_c;
            errors['monto_c'] = errors['monto_c'] || {};
            errors['monto_c'].required = true;
            app.alert.show("monto_corto_plazo", {
                level: "error",
                title: "El monto de línea debe tener un valor mínimo de $500,000.00 y máximo $10,000,000.00",
                autoClose: false
            });
        }

        /*
        if (parseFloat(this.model.get('amount')) <= 0)
        {
            errors['amount'] = errors['amount'] || {};
            errors['amount'].required = true;
        }

        if (parseFloat(this.model.get('ca_pago_mensual_c')) <= 0)
        {
            errors['ca_pago_mensual_c'] = errors['ca_pago_mensual_c'] || {};
            errors['ca_pago_mensual_c'].required = true;
        }

        if (parseFloat(this.model.get('ca_importe_enganche_c')) <= 0 && this.model.get('tipo_producto_c')=="1") {
            errors['ca_importe_enganche_c'] = errors['ca_importe_enganche_c'] || {};
            errors['ca_importe_enganche_c'].required = true;

            app.alert.show("Renta inicial requerida", {
                level: "error",
                title: "Renta inicial debe ser mayor a cero",
                autoClose: false
            });

        }

        if (parseFloat(this.model.get('porciento_ri_c')) <= 0 && this.model.get('tipo_producto_c')=="1" || this.model.get('porciento_ri_c')=="" && this.model.get('tipo_producto_c')=="1") {
            errors['porciento_ri_c'] = errors['porciento_ri_c'] || {};
            errors['porciento_ri_c'].required = true;

            app.alert.show("Porcentaje Renta inicial requerida", {
                level: "error",
                title: "% Renta inicial debe ser mayor a cero",
                autoClose: false
            });

        }
         */

        callback(null, fields, errors);
    },

    getCustomSaveOptions: function (options) {
        this.createdModel = this.model;
        // since we are in a drawer
        this.listContext = this.context.parent || this.context;
        this.originalSuccess = options.success;

        var success = _.bind(function (model) {
            this.originalSuccess(model);
        }, this);

        return {
            success: success
        };
    },

    validaActivoIndex: function (fields, errors, callback) {
        //CVV - 28/03/2016 - Modulo de condiciones financieras
        /*var activo = this.model.get('activo_c');
        var subactivo = this.model.get('sub_activo_c');
        var subactivo2 = this.model.get('sub_activo_2_c');
        var subactivo3 = this.model.get('sub_activo_3_c');
        var index_c;
        var id_c;
        var field_activo;
        var field_activo_compara;
        if(activo != undefined && subactivo != undefined && subactivo2 != undefined && subactivo3 != undefined){
            field_activo = this.model.get('sub_activo_2_c');
            field_activo_compara = this.model.get('sub_activo_3_c');
        }
        if(activo != undefined && subactivo != undefined && subactivo2 != undefined && subactivo3 == undefined){
            field_activo = this.model.get('sub_activo_c');
            field_activo_compara = this.model.get('sub_activo_2_c');
        }
        if(activo != undefined && subactivo != undefined && subactivo2 == undefined && subactivo3 == undefined){
            field_activo = this.model.get('activo_c');
            field_activo_compara = this.model.get('sub_activo_c');
        }
        if(activo != undefined && subactivo == undefined && subactivo2 == undefined && subactivo3 == undefined){
            field_activo = this.model.get('activo_c');
            field_activo_compara = this.model.get('activo_c');
        }
        if(activo != undefined && subactivo == "" && subactivo2 == undefined && subactivo3 == undefined){
            field_activo = this.model.get('activo_c');
            field_activo_compara = this.model.get('activo_c');
        }
        var activo_subactivo_url = app.api.buildURL('ActivoAPI?activo=' + field_activo,
            null, null, null);

        app.api.call('READ', activo_subactivo_url, {}, {
            success: _.bind(function (data) {
                $.each(data, function( index, value ) {
                    if(field_activo_compara == value){
                        index_c = value;
                        id_c = index;
                    }
                });
                this.model.set('id_activo_c', id_c);
                this.model.set('index_activo_c', index_c);
            }, this)
        });*/
        // Obtener el primer activo del control de condiciones financieras
        this.model.set('id_activo_c', "97");
        this.model.set('index_activo_c', "000100030001");
        callback(null, fields, errors);
    },

    buscaDuplicados: function (fields, errors, callback) {
        var cliente = this.model.get('account_id');
        var tipo = this.model.get('tipo_producto_c');
        var producto = this.model.get('producto_financiero_c');
        var negocio = this.model.get('negocio_c');

        var args = {
            'account_id': cliente,
            'tipo_producto_c': tipo,
            'producto_financiero_c': producto,
            'negocio_c': negocio
        };
        var opportunities = app.api.buildURL("getOpportunities", '', {}, {});
        app.api.call("create", opportunities, { data: args }, {
            success: _.bind(function (data) {
                var duplicado = data['duplicado'];
                var mensaje = data['mensaje'];
                console.log(data);
                /* if (data.records.length > 0)
                 {
                     $(data.records).each(function (index, value)
                     {
                         if (value.estatus_c != "K" && value.tct_etapa_ddw_c != "CL" && value.tct_etapa_ddw_c != "R" && check!=1)
                         {
                             mensaje = "No es posible crear una Pre-solicitud cuando ya se encuentra una Pre-solicitud o Solicitud en proceso.";
                             duplicado = 1;

                         } else if (value.tct_etapa_ddw_c == "CL" && (value.tipo_producto_c == "1" || value.tipo_producto_c == "4") && check!=1){

                             mensaje = "No es posible crear una Pre-solicitud cuando ya se tiene una línea de crédito autorizada.";
                             duplicado = 1;

                         }
                     });
                 }*/
                if (duplicado === 1) {
                    app.alert.show("Solicitud existente", {
                        level: "error",
                        title: mensaje,
                        autoClose: false
                    });
                    app.error.errorName2Keys['custom_message'] = mensaje;
                    errors['account_name_2'] = errors['account_name_2'] || {};
                    errors['account_name_2'].custom_message = true;
                }
                callback(null, fields, errors);
            }, this)
        });
    },

    validaClientesActivos: function (fields, errors, callback) {
        if (this.model.get('account_id')) {
            var account = app.data.createBean('Accounts', { id: this.model.get('account_id') });
            account.fetch({
                success: _.bind(function (model) {
                    if (model.get('estatus_persona_c') == 'I') {
                        app.error.errorName2Keys['custom_message'] = 'No se puede iniciar Pre-Solicitud en una cuenta inactiva';
                        errors['account_name_3'] = errors['account_name_3'] || {};
                        errors['account_name_3'].custom_message = true;
                        app.alert.show("cuenta_inactiva", {
                            level: "error",
                            messages: "No se puede iniciar Pre-Solicitud en una cuenta inactiva",
                            autoClose: false
                        });
                    }
                    if (!app.user.attributes.tct_no_contactar_chk_c && !app.user.attributes.bloqueo_credito_c && !app.user.attributes.bloqueo_cumple_c) {
                        var url = app.api.buildURL('tct02_Resumen/' + this.model.get('account_id'), null, null);
                        app.api.call('read', url, {}, {
                            success: _.bind(function (data) {
                                if (data.bloqueo_cartera_c || data.bloqueo2_c || data.bloqueo3_c) {
                                    app.alert.show("cuentas_no_contactar", {
                                        level: "error",
                                        title: "Cuenta No Contactable<br>",
                                        messages: "Cualquier duda o aclaraci\u00F3n, favor de contactar al \u00E1rea de <b>Administraci\u00F3n de cartera</b>",
                                        autoClose: false
                                    });
                                    //Cerrar vista de creación de solicitud
                                    if (app.drawer.count()) {
                                        app.drawer.close(this.context);
                                        //Ocultar alertas excepto la que indica que no se pueden crear relacionados a Cuentas No Contactar
                                        var alertas = app.alert.getAll();
                                        for (var property in alertas) {
                                            if (property != 'cuentas_no_contactar') {
                                                app.alert.dismiss(property);
                                            }
                                        }
                                    } else {
                                        app.router.navigate(this.module, { trigger: true });
                                    }
                                }
                                callback(null, fields, errors);
                            }, this)
                        });
                    } else {
                        callback(null, fields, errors);
                    }
                }, this)
            });
        } else {
            callback(null, fields, errors);
        }
    },

    verificaOperacionProspecto: function () {
        var account = app.data.createBean('Accounts', { id: this.model.get('account_id') });
        account.fetch({
            success: _.bind(function (modelo) {
                //Asignamos el promotor del producto para la operación
                var producto = parseInt(this.model.get('tipo_producto_c'));
                switch (producto) {
                    case 3:
                        id_promotor = modelo.get('user_id2_c');
                        name_promotor = modelo.attributes.promotorcredit_c;
                        break;
                    case 4:
                        id_promotor = modelo.get('user_id1_c');
                        name_promotor = modelo.attributes.promotorfactoraje_c;
                        break;
                    case 6:
                        id_promotor = modelo.get('user_id6_c');
                        name_promotor = modelo.attributes.promotorfleet_c;
                        break;
                    case 8:
                        id_promotor = modelo.get('user_id7_c');
                        name_promotor = modelo.attributes.promotoruniclick_c;
                        break;
                    case 9:
                        id_promotor = modelo.get('user_id7_c');
                        name_promotor = modelo.attributes.promotoruniclick_c;
                        break;
                    case 14:
                        id_promotor = App.user.id;
                        name_promotor = App.user.attributes.full_name;
                        break;
                    default:
                        id_promotor = modelo.get('user_id_c');
                        name_promotor = modelo.attributes.promotorleasing_c;
                        //seteo de asesor RM al campo asesor_rm_c
                        id_RM = modelo.get('user_id8_c');
                        name_promotorRM = modelo.attributes.promotorrm_c;
                        this.model.set("user_id1_c", id_RM);
                        this.model.set("asesor_rm_c", name_promotorRM);
                        break;
                }

                if (parseInt(this.model.get('negocio_c')) == 10) {
                    id_promotor = modelo.get('user_id7_c');
                    name_promotor = modelo.attributes.promotoruniclick_c;
                }
                if (parseInt(this.model.get('producto_financiero_c')) == 43 || parseInt(this.model.get('producto_financiero_c')) == 78) {
                    id_promotor = app.user.id;
                    name_promotor = app.user.attributes.full_name;

                }
                if (parseInt(this.model.get('producto_financiero_c')) == 40) {
                    id_promotor = modelo.get('user_id_c');
                    name_promotor = modelo.attributes.promotorleasing_c;
                }
                //Agrega asesor solo con privilegios de Admin Cartera
                if (this.model.get('admin_cartera_c') == true && app.user.attributes.admin_cartera_c == 1 && app.user.attributes.config_admin_cartera == true) {

                    id_promotor = app.user.id;
                    name_promotor = app.user.attributes.full_name;
                }
                if (id_promotor != undefined) {
                    this.model.set("assigned_user_id", id_promotor);
                    this.model.set("assigned_user_name", name_promotor);
                }

                /*var usuario = app.data.createBean('Users', {id: promotor});
                usuario.fetch({
                    success: _.bind(function (data) {
                        if (data.get('id') != undefined) {
                            this.model.set("assigned_user_id", data.get('id'));
                            this.model.set("assigned_user_name", data.get('name'));
                        }
                    }, this)
                });
                */
                //Verificamos la lista a mostrar:
                this.tipo = modelo.get('tipo_registro_cuenta_c');
                //console.log("Registro: " + modelo.get('tipo_registro_c'));
                if (modelo.get('tipo_registro_cuenta_c') != '3') { // 3 - Cliente
                    //Si es prospecto ponemos como primer registro el value 'OP'
                    //console.log(this.model.fields['estatus_c']);
                    //this.model.set('estatus_c','OP');
                }
                if (modelo.get('tipo_registro_cuenta_c') == '3') { // 3 - Cliente
                    //Si es prospecto ponemos como primer registro el value 'OP'
                    //console.log(this.model.fields['estatus_c']);
                    //this.model.set('estatus_c','P');
                }
                // 0000080: El sistema permite crear una operación de tipo Factoraje para una PF
                // todo pendiente
                if (modelo.get('tipodepersona_c') == 'Persona Fisica' && modelo.get('id') != null) {
                    this.tipoDePersona = true;
                    //console.log("Cambiamos a tipo producto leasing");

                    //this.model.set('tipo_producto_c','1');
                } else {
                    this.tipoDePersona = false;
                }
                if (modelo.get('tipo_registro_cuenta_c') == '2') { // 2 - Prospecto
                    this.prospecto = true;
                } else {
                    this.prospecto = false;
                }
            }, this)
        });
    },
    /*@author Carlos Zaragoza Ortiz
     * @version 1
     * @date 12/10/2015
     * Validar la cantidad de operaciones que se pueden generar para un cliente/Prospecto (solo una)
     * @type Function
     * */
    validaOperacionesPermitidasPorCuenta: function (fields, errors, callback) {
        //Controlamos la solicitud del servicio:
        if (this.model.get('account_id')) {
            var OppParams = {
                'id_c': this.model.get('account_id'),
            };
            var urlOperaciones = app.api.buildURL("Opportunities/Operaciones", '', {}, {});
            app.api.call("create", urlOperaciones, { data: OppParams }, {
                success: _.bind(function (data) {
                    if (data != null) {
                        //console.log(data);
                        var cantidad = data['cantidad'];
                        //console.log("Cantidad de operaciones" + cantidad);
                        if (cantidad > 0) {
                            app.alert.show("Cantidad de operaciones", {
                                level: "error",
                                title: "No puedes generar m&aacute;s de una operaci&oacute;n para prospectos.",
                                autoClose: false
                            });
                            app.error.errorName2Keys['custom_message'] = 'Solo puede tener una operacion como prospecto ';
                            errors['account_name_4'] = errors['account_name_4'] || {};
                            errors['account_name_4'].custom_message = true;

                            //this.cancelClicked();
                            callback(null, fields, errors);
                        } else {
                            callback(null, fields, errors);
                        }
                    }
                }, this)
            });
        } else {
            callback(null, fields, errors);
        }
    },

    _ActualizaEtiquetas: function () {
        //self.model.set('negocio_c', '0');
        if (this.model.get('tipo_producto_c') == '4') {
            this.$("div.record-label[data-name='plazo_c']").text("Plazo máximo en d\u00EDas");
            this.$("div.record-label[data-name='porcentaje_ca_c']").text("Comisi\u00F3n");
        } else {
            this.$("div.record-label[data-name='plazo_c']").text("Plazo en meses");
            this.$("div.record-label[data-name='porcentaje_ca_c']").text("Comisi\u00F3n por apertura");
        }
        if (this.model.get('tipo_producto_c') == '1') {
            this.$("div.record-label[data-name='ca_importe_enganche_c']").text("Renta Inicial");
        } else if (this.model.get('tipo_producto_c') == '3') {
            this.$("div.record-label[data-name='ca_importe_enganche_c']").text("Enganche");

        }

        if (this.model.get('tipo_producto_c') == '6') {
            this.$("div.record-label[data-name='monto_c']").text("L\u00EDnea aproximada");
        } else {
            this.$("div.record-label[data-name='monto_c']").text("Monto de l\u00EDnea");

        }
        // CVV - 28/03/2016 - Se reemplaza por control de condiciones financieras
        /*
        if(this.model.get('tipo_producto_c')=='3'){
            this.$("div.record-label[data-name='porcentaje_renta_inicial_c']").text("Porcentaje de Enganche");
        }else{
            this.$("div.record-label[data-name='porcentaje_renta_inicial_c']").text("Porcentaje Renta Inicial");
        }
        */
    },
    // CVV - 28/03/2016 - Se reemplaza por modulo de condiciones financieras
    /*
    validaActivo: function (fields, errors, callback){
        //console.log(this.model.get('activo_c'));
        if(this.model.get('tipo_producto_c')!='4' && this.model.get('es_multiactivo_c') == false) {
            if (this.model.get('activo_c') == undefined || this.model.get('activo_c') == "") {
                app.alert.show("TodosActivos", {
                    level: "error",
                    title: "Selecciona el activo",
                    autoClose: true
                });
                errors['activo_c'] = errors['activo_c'] || {};
                errors['activo_c'].required = true;
            }
        }
        callback(null, fields, errors);
    },

    validaSubActivo: function (fields, errors, callback){
        //console.log(this.model.get('sub_activo_c'));
        if(this.model.get('tipo_producto_c')!='4' && this.model.get('es_multiactivo_c') == false){
            if( this.model.get('sub_activo_c')==undefined ||  this.model.get('sub_activo_c') == ""){
                app.alert.show("TodosActivos1", {
                    level: "error",
                    title: "Selecciona el Sub activo",
                    autoClose: true
                });
                errors['sub_activo_c'] = errors['sub_activo_c'] || {};
                errors['sub_activo_c'].required = true;
            }
        }
        callback(null, fields, errors);
    },
    validaSubActivo2: function (fields, errors, callback){
        //console.log(this.model.get('sub_activo_2_c'));
        if(this.model.get('tipo_producto_c')!='4' && this.model.get('es_multiactivo_c') == false) {
            if (this.model.get('sub_activo_2_c') == undefined || this.model.get('sub_activo_2_c') == "") {
                app.alert.show("TodosActivos2", {
                    level: "error",
                    title: "Selecciona el tipo de activo",
                    autoClose: true
                });
                errors['sub_activo_2_c'] = errors['sub_activo_2_c'] || {};
                errors['sub_activo_2_c'].required = true;
            }
        }
        callback(null, fields, errors);
    },
    validaSubActivo3: function (fields, errors, callback){
       // console.log(this.model.get('sub_activo_3_c'));
        if(this.model.get('tipo_producto_c')!='4' && this.model.get('es_multiactivo_c') == false) {
            if (this.model.get('sub_activo_3_c') == undefined || this.model.get('sub_activo_3_c') == "") {
                app.alert.show("TodosActivos3", {
                    level: "error",
                    title: "Selecciona la Marca",
                    autoClose: true
                });
                errors['sub_activo_3_c'] = errors['sub_activo_3_c'] || {};
                errors['sub_activo_3_c'].required = true;
            }
        }
        callback(null, fields, errors);
    },
    */
    valiaAforo: function (fields, errors, callback) {
        if (this.model.get('tipo_producto_c') == '4') {
            if (Number(this.model.get('f_aforo_c')) >= 0.000000) {

                if (this.model.get('f_tipo_factoraje_c') == '1') {
                    if (Number(this.model.get('f_aforo_c')) < 10.000000) {
                        app.alert.show("aforoMinimo", {
                            level: "error",
                            title: "El aforo para Cobranza Delegada con Recurso debe ser m\u00EDnimo 10 %",
                            autoClose: false
                        });
                        errors['f_aforo_c'] = errors['f_aforo_c'] || {};
                        errors['f_aforo_c'].required = true;
                        // el valor minimo es 10.000000%
                    }
                }
            } else {
                app.alert.show("aforoNegativo", {
                    level: "error",
                    title: "El aforo no puede ser negativo",
                    autoClose: false
                });
                errors['f_aforo_c'] = errors['f_aforo_c'] || {};
                errors['f_aforo_c'].required = true;
            }
        }


        callback(null, fields, errors);
    },
    validaRequeridosFactoraje: function (fields, errors, callback) {
        //console.log(this.model.get('f_aforo_c'));
        //console.log(this.model.get('f_tipo_factoraje_c'));
        if (this.model.get('tipo_producto_c') == '4') {
            if (this.model.get('f_tipo_factoraje_c') == undefined || this.model.get('f_tipo_factoraje_c') == "") {
                //error
                errors['f_tipo_factoraje_c'] = errors['f_tipo_factoraje_c'] || {};
                errors['f_tipo_factoraje_c'].required = true;
            }
            if (this.model.get('f_aforo_c') == "" || (Number(this.model.get('f_aforo_c')) < 0 || Number(this.model.get('f_aforo_c')) > 99.999999)) {
                //error
                errors['f_aforo_c'] = errors['f_aforo_c'] || {};
                errors['f_aforo_c'].required = true;
            }
            if (this.model.get('tipo_tasa_ordinario_c') == undefined || this.model.get('tipo_tasa_ordinario_c') == "") {
                //error
                errors['tipo_tasa_ordinario_c'] = errors['tipo_tasa_ordinario_c'] || {};
                errors['tipo_tasa_ordinario_c'].required = true;
            }
            if (this.model.get('instrumento_c') == undefined || this.model.get('instrumento_c') == "") {
                //error
                errors['instrumento_c'] = errors['instrumento_c'] || {};
                errors['instrumento_c'].required = true;
            }
            /*if (this.model.get('puntos_sobre_tasa_c') == "" || (Number(this.model.get('puntos_sobre_tasa_c')) < 0 || Number(this.model.get('puntos_sobre_tasa_c')) > 99.999999)) {
                //error
                errors['puntos_sobre_tasa_c'] = errors['puntos_sobre_tasa_c'] || {};
                errors['puntos_sobre_tasa_c'].required = true;
            }*/
            if (this.model.get('tipo_tasa_moratorio_c') == undefined || this.model.get('tipo_tasa_moratorio_c') == "") {
                //error
                errors['tipo_tasa_moratorio_c'] = errors['tipo_tasa_moratorio_c'] || {};
                errors['tipo_tasa_moratorio_c'].required = true;
            }
            if (this.model.get('instrumento_moratorio_c') == undefined || this.model.get('instrumento_moratorio_c') == "") {
                //error
                errors['instrumento_moratorio_c'] = errors['instrumento_moratorio_c'] || {};
                errors['instrumento_moratorio_c'].required = true;
            }
            if (this.model.get('puntos_tasa_moratorio_c') == "" || (Number(this.model.get('puntos_tasa_moratorio_c')) < 0 || Number(this.model.get('puntos_tasa_moratorio_c')) > 99.999999)) {
                //error
                errors['puntos_tasa_moratorio_c'] = errors['puntos_tasa_moratorio_c'] || {};
                errors['puntos_tasa_moratorio_c'].required = true;
            }
            if (this.model.get('factor_moratorio_c') == "" || (Number(this.model.get('factor_moratorio_c')) < 0 || Number(this.model.get('factor_moratorio_c')) > 99.999999)) {
                //error
                errors['factor_moratorio_c'] = errors['factor_moratorio_c'] || {};
                errors['factor_moratorio_c'].required = true;
            }
            if (this.model.get('cartera_descontar_c') == "") {
                //error
                errors['cartera_descontar_c'] = errors['cartera_descontar_c'] || {};
                errors['cartera_descontar_c'].required = true;
            }
            /*
                console.log(this.model.get('tasa_fija_ordinario_c'));
                console.log('tasa_fija_ordinario_c');
                if(this.model.get('tasa_fija_ordinario_c') == null ||this.model.get('tasa_fija_ordinario_c') == "" || (Number(this.model.get('tasa_fija_ordinario_c'))<0 || Number(this.model.get('tasa_fija_ordinario_c'))>99.999999)){
                    //error
                    errors['tasa_fija_ordinario_c'] = errors['tasa_fija_ordinario_c'] || {};
                    errors['tasa_fija_ordinario_c'].required = true;
                }
                if(this.model.get('tasa_fija_moratorio_c') == null ||this.model.get('tasa_fija_moratorio_c') == "" || (Number(this.model.get('tasa_fija_moratorio_c'))<0 || Number(this.model.get('tasa_fija_moratorio_c'))>99.999999)){
                    //error
                    errors['tasa_fija_moratorio_c'] = errors['tasa_fija_moratorio_c'] || {};
                    errors['tasa_fija_moratorio_c'].required = true;
                }
        */
        }
        callback(null, fields, errors);
    },
    // CVV - 28/03/2016 - Se sustituye por modulo de condiciones financieras
    /************/
    validaMultiactivo: function (fields, errors, callback) {
        // console.log("Valida MultiActivo");
        if (this.model.get('es_multiactivo_c') == true) {
            this.model.set('activo_c', '');
            var activos = new String(this.model.get('multiactivo_c'));
            // console.log(activos);
            //  console.log(typeof activos);
            var arrActivos = activos.split(",");
            // console.log(arrActivos);
            //  console.log(typeof arrActivos);
            //  console.log(arrActivos.length);

            if (arrActivos.length <= 1) {
                errors['multiactivo_c'] = errors['multiactivo_c'] || {};
                errors['multiactivo_c'].required = true;
            }
        }
        callback(null, fields, errors);
    }, /**/
    obtieneCondicionesFinancieras: function () {
        /*
         * Obtiene las condidionces financieras
         * */
        if (this.model.get('tipo_producto_c') == '4') {
            var OppParams = {
                'plazo_c': this.model.get('plazo_c'),
                'tipo_producto_c': this.model.get('tipo_producto_c'),
            };
            //console.log(OppParams);
            var dnbProfileUrl = app.api.buildURL("Opportunities/CondicionesFinancieras", '', {}, {});
            app.api.call("create", dnbProfileUrl, { data: OppParams }, {
                success: _.bind(function (data) {
                    if (data != null) {
                        //CVV - 28/03/2016 - Se reemplaza por control de condiciones financieras
                        /*
                         if(this.model.get('tipo_producto_c')=='1'){
                         this.model.set('porcentaje_ca_c',data.porcentaje_ca_c);
                         this.model.set('vrc_c',data.vrc_c);
                         this.model.set('vri_c',data.vri_c);
                         this.model.set('ca_tasa_c',data.ca_tasa_c);
                         this.model.set('porcentaje_renta_inicial_c',data.porcentaje_renta_inicial_c);
                         }else if(this.model.get('tipo_producto_c')=='3'){
                         this.model.set('ca_tasa_c',data.ca_tasa_c);
                         this.model.set('porcentaje_ca_c',data.porcentaje_ca_c);
                         this.model.set('porcentaje_renta_inicial_c',data.porcentaje_renta_inicial_c);
                         this.model.set('vrc_c','0.0');
                         this.model.set('vri_c','0.0');
                         }else */
                        if (this.model.get('tipo_producto_c') == '4') {
                            //this.model.set('ca_tasa_c',data.ca_tasa_c);
                            this.model.set('puntos_sobre_tasa_c', data.ca_tasa_c);
                            this.model.set('porcentaje_ca_c', data.porcentaje_ca_c);
                            //this.model.set('porcentaje_renta_inicial_c','0.0');
                            //this.model.set('vrc_c','0.0');
                            //this.model.set('vri_c','0.0');
                        }

                    }
                }, this)
            });
        }
    },

    validaCondicionesFinanceras: function (fields, errors, callback) {
        // CVV - 28/03/2016 - Se reemplaza por control de condiciones financieras
        /*if(this.model.get('tipo_producto_c')=='1'){ //Leasing
            if(Number(this.model.get('ca_tasa_c'))>=100 || Number(this.model.get('ca_tasa_c')<0) || this.model.get('ca_tasa_c')==''){
                errors['ca_tasa_c'] = errors['ca_tasa_c'] || {};
                errors['ca_tasa_c'].required = true;
            }
            if(Number(this.model.get('porcentaje_ca_c'))>=100 || Number(this.model.get('porcentaje_ca_c')<0 || this.model.get('porcentaje_ca_c')=='')){
                errors['porcentaje_ca_c'] = errors['porcentaje_ca_c'] || {};
                errors['porcentaje_ca_c'].required = true;
            }
            if(Number(this.model.get('vrc_c'))>=100 || Number(this.model.get('vrc_c')<0) || this.model.get('vrc_c') == ''){
                errors['vrc_c'] = errors['vrc_c'] || {};
                errors['vrc_c'].required = true;
            }
            if(Number(this.model.get('porcentaje_renta_inicial_c'))>=100 || Number(this.model.get('porcentaje_renta_inicial_c')<0) || this.model.get('porcentaje_renta_inicial_c') == ''){
                errors['porcentaje_renta_inicial_c'] = errors['porcentaje_renta_inicial_c'] || {};
                errors['porcentaje_renta_inicial_c'].required = true;
            }
            if(Number(this.model.get('vri_c'))>=100 || Number(this.model.get('vri_c')<0) || this.model.get('vri_c') == ''){
                errors['vri_c'] = errors['vri_c'] || {};
                errors['vri_c'].required = true;
            }
        }
        if(this.model.get('tipo_producto_c')=='3'){ //CA
            if(Number(this.model.get('ca_tasa_c'))>=100 || Number(this.model.get('ca_tasa_c')<0) || this.model.get('ca_tasa_c')==''){
                errors['ca_tasa_c'] = errors['ca_tasa_c'] || {};
                errors['ca_tasa_c'].required = true;
            }
            if(Number(this.model.get('porcentaje_ca_c'))>=100 || Number(this.model.get('porcentaje_ca_c')<0) || this.model.get('porcentaje_ca_c') == ''){
                errors['porcentaje_ca_c'] = errors['porcentaje_ca_c'] || {};
                errors['porcentaje_ca_c'].required = true;
            }
            if(Number(this.model.get('porcentaje_renta_inicial_c'))>=100 || Number(this.model.get('porcentaje_renta_inicial_c')<0) || this.model.get('porcentaje_renta_inicial_c') == ''){
                errors['porcentaje_renta_inicial_c'] = errors['porcentaje_renta_inicial_c'] || {};
                errors['porcentaje_renta_inicial_c'].required = true;
            }

        }*/
        if (this.model.get('tipo_producto_c') == '4') { //Factoraje
            if (Number(this.model.get('puntos_sobre_tasa_c')) >= 100 || Number(this.model.get('puntos_sobre_tasa_c') < 0) || this.model.get('puntos_sobre_tasa_c') == '') {
                errors['puntos_sobre_tasa_c'] = errors['puntos_sobre_tasa_c'] || {};
                errors['puntos_sobre_tasa_c'].required = true;
            }
            if (Number(this.model.get('porcentaje_ca_c')) >= 100 || Number(this.model.get('porcentaje_ca_c') < 0) || this.model.get('porcentaje_ca_c') == '') {
                errors['porcentaje_ca_c'] = errors['porcentaje_ca_c'] || {};
                errors['porcentaje_ca_c'].required = true;
            }
        }
        callback(null, fields, errors);
    },

    setRequiredAforoTipoFactoraje: function (fields, errors, callback) {
        if (this.model.get('tipo_producto_c') == '4' && this.model.get('tct_oportunidad_perdida_chk_c') == false && $('[data-name="f_tipo_factoraje_c"]').is(':visible')) {
            errors['f_tipo_factoraje_c'] = errors['f_tipo_factoraje_c'] || {};
            errors['f_tipo_factoraje_c'].required = true;
        }

        if (this.model.get('tipo_producto_c') == '4' && this.model.get('tct_oportunidad_perdida_chk_c') == false && $('[data-name="f_aforo_c"]').is(':visible')) {
            errors['f_aforo_c'] = errors['f_aforo_c'] || {};
            errors['f_aforo_c'].required = true;
        }

        callback(null, fields, errors);
    },

    getCurrentYearMonth: function (stage) {

        var currentYear = (new Date).getFullYear();
        var currentMonth = (new Date).getMonth();
        var currentDay = (new Date).getDate();
        //currentMonth += 1;

        if (currentDay < 10) {
            currentMonth += 1;
        }
        if (currentDay >= 10) {
            currentMonth += 2;
        }

        var opciones_year = app.lang.getAppListStrings('anio_list');
        Object.keys(opciones_year).forEach(function (key) {
            if (key < currentYear) {
                delete opciones_year[key];
            }
        });
        this.model.fields['anio_c'].options = opciones_year;

        var opciones_mes = app.lang.getAppListStrings('mes_list');
        if (this.model.get("anio_c") <= currentYear) {
            Object.keys(opciones_mes).forEach(function (key) {
                if (key < currentMonth) {
                    delete opciones_mes[key];
                }
            });
        }

        this.model.fields['mes_c'].options = opciones_mes;

        if (stage != "loading") {
            this.render();
        }
    },

    condicionesFinancierasCheck: function (fields, errors, callback) {

        if (this.model.get("tipo_operacion_c") == 1 && this.model.get("tipo_producto_c") != 4) {
            if (_.isEmpty(this.model.get('condiciones_financieras'))) {
                errors[$(".addCondicionFinanciera")] = errors['condiciones_financieras'] || {};
                errors[$(".addCondicionFinanciera")].required = true;

                $('.condiciones_financieras').css('border-color', 'red');
                app.alert.show("CondicionFinanciera requerida", {
                    level: "error",
                    title: "Al menos una Condicion Financiera es requerida.",
                    autoClose: false
                });
            }
        }
        callback(null, fields, errors);
    },

    // condicionesFinancierasIncrementoCheck: function (fields, errors, callback) {

    //     if (this.model.get("ratificacion_incremento_c") == 1 && this.model.get("tipo_operacion_c") == 2 && this.model.get("tipo_producto_c") != 4) {
    //         if (_.isEmpty(this.model.get('condiciones_financieras_incremento_ratificacion'))) {
    //             errors[$(".add_incremento_CondicionFinanciera")] = errors['condiciones_financieras_incremento_ratificacion'] || {};
    //             errors[$(".add_incremento_CondicionFinanciera")].required = true;

    //             $('.condiciones_financieras_incremento_ratificacion').css('border-color', 'red');
    //             app.alert.show("CondicionFinanciera requerida", {
    //                 level: "error",
    //                 title: "Al menos una Condicion Financiera de Incremento/Ratificacion es requerida.",
    //                 autoClose: false
    //             });
    //         }
    //     }
    //     callback(null, fields, errors);
    // },

    personTypeCheck: function (fields, errors, callback) {
        var self = this;
        var tipo_registro;
        //id de la Persona asociada
        var id_person = this.model.get('account_id');
        // Valida que sea diferente de Credito simple, de SERVICIOS y de TARJETA DE CREDITO
        if (this.model.get('tipo_producto_c') != '2' && this.model.get('tipo_producto_c') != '13' && this.model.get('tipo_producto_c') != '14') {
            //Recupera productos asociados
            if (id_person && id_person != '' && id_person.length > 0) {
                app.api.call('GET', app.api.buildURL('GetProductosCuentas/' + id_person), null, {
                    success: _.bind(function (data) {
                        if (data != null) {
                            //Procesa registros
                            Productos = data;
                            var tipoCuentaLabel = app.lang.getAppListStrings('tipo_registro_cuenta_list');
                            var tipoCuenta = "";
                            var tipoProducto = this.model.get('tipo_producto_c');
                            _.each(Productos, function (value, key) {
                                var tipoProductoIterado = Productos[key].tipo_producto;
                                if (tipoProducto == tipoProductoIterado) {
                                    tipoCuenta = Productos[key].tipo_cuenta;
                                }

                            });
                            if (tipoCuenta != "2" && tipoCuenta != "3") {
                                app.alert.show("Cliente no v\u00E1lido", {
                                    level: "error",
                                    title: "No se puede asociar la operaci\u00F3n a una Cuenta de tipo: " + tipoCuentaLabel[tipoCuenta],
                                    autoClose: false
                                });

                                app.error.errorName2Keys['custom_message1'] = 'La cuenta asociada debe ser tipo Cliente o Prospecto';
                                errors['account_name_5'] = errors['account_name_5'] || {};
                                errors['account_name_5'].custom_message1 = true;
                            }
                        }
                        callback(null, fields, errors);
                    }, self),
                });
            } else {
                app.error.errorName2Keys['custom_message1'] = 'La persona asociada debe ser tipo Cliente o Prospecto';
                errors['account_name_6'] = errors['account_name_6'] || {};
                errors['account_name_6'].custom_message1 = true;
                errors['account_name_6'].required = true;
                callback(null, fields, errors);
            }
        }
        else {
            callback(null, fields, errors);
        }
    },

    calcularRI: function () {

        if (!_.isEmpty(this.model.get("amount")) && !_.isEmpty(this.model.get("porciento_ri_c")) && this.model.get("porciento_ri_c") != 0) {
            var percent = (this.model.get("amount") * this.model.get("porciento_ri_c")) / 100;
            this.model.set("ca_importe_enganche_c", percent);
        }
    },

    calcularPorcientoRI: function () {

        if (!_.isEmpty(this.model.get("amount")) && !_.isEmpty(this.model.get("ca_importe_enganche_c")) && this.model.get("ca_importe_enganche_c") != 0) {
            var percent = ((this.model.get("ca_importe_enganche_c") * 100) / this.model.get("amount")).toFixed(2);
            this.model.set("porciento_ri_c", percent);
        }
    },

    ocultaFunc: function () {
        _.each(this.fields, function (field) {

            if( field.name !== "renewal" && field.name != "name" && field.name != "account_name" && field.name != "picture" ){
                $('.record-cell[data-name="' + field.name + '"]').hide();
            }
        });
        //$('.record-cell[data-name="name"]').css('display','block');
        //$('[data-name="tct_etapa_ddw_c"]').show();
        //$('[data-name="estatus_c"]').show();
        //$('[data-name="idsolicitud_c"]').show();
        //$('.record-cell[data-name="account_name"]').css('display','block');
        $('.record-cell[data-name="tipo_producto_c"]').css('display','block');
        //$('[data-name="producto_financiero_c"]').show();
        $('.record-cell[data-name="negocio_c"]').css('display','block');
        $('.record-cell[data-name="monto_c"]').css('display','block');
        $('.record-cell[data-name="assigned_user_name"]').css('display','block');
        //$('[data-name="picture"]').show();
        $('[data-name="tct_numero_vehiculos_c"]').show();
        

        //Se visualiza campo de Administrador cartera solo si el usuario tiene activo el check
        if (app.user.attributes.admin_cartera_c == 1 && app.user.attributes.config_admin_cartera == true) {
            $('[data-name="admin_cartera_c"]').show();
        }

        /**Muestra campos de area Beneficiada y Subyacente **/
        /* $('[data-name="estado_benef_c"]').show();
         $('[data-name="municipio_benef_c"]').show();
         $('[data-name="ent_gob_benef_c"]').show();
         $('[data-name="cuenta_benef_c"]').show();
         $('[data-name="emp_no_reg_benef_c"]').show();
         $('[data-name="estado_suby_c"]').show();
         $('[data-name="municipio_suby_c"]').show();
         $('[data-name="ent_gob_suby_c"]').show();
         $('[data-name="otro_suby_c"]').show();*/
        //Ocultando el panel de Oportunidad perdida
        $('div[data-panelname="LBL_RECORDVIEW_PANEL1"]').addClass('hide');
        $("div[data-name='seguro_desempleo_c']").remove();
        $("div[data-name='porciento_ri_c']").remove();
        $('div[data-panelname="LBL_RECORDVIEW_PANEL2"]').addClass('hide');
        $('div[data-panelname="LBL_RECORDVIEW_PANEL3"]').addClass('hide');
        $('div[data-panelname="LBL_RECORDVIEW_PANEL4"]').addClass('hide');
		$('[data-name="condiciones_financieras_quantico"]').remove();
    },

    _dispose: function () {
        this._super('_dispose', []);
    },

    /*@Jesus Carrillo
  Metodos que limitan el tipo moneda a 15 entetos y 2 decimales
 */
    checkmoney: function (evt) {
        var enteros = this.checkmoneyint(evt);
        var decimales = this.checkmoneydec(evt);
        $.fn.selectRange = function (start, end) {
            if (!end) end = start;
            return this.each(function () {
                if (this.setSelectionRange) {
                    this.focus();
                    this.setSelectionRange(start, end);
                } else if (this.createTextRange) {
                    var range = this.createTextRange();
                    range.collapse(true);
                    range.moveEnd('character', end);
                    range.moveStart('character', start);
                    range.select();
                }
            });
        };//funcion para posicionar cursor

        (function ($, undefined) {
            $.fn.getCursorPosition = function () {
                var el = $(this).get(0);
                var pos = [];
                if ('selectionStart' in el) {
                    pos = [el.selectionStart, el.selectionEnd];
                } else if ('selection' in document) {
                    el.focus();
                    var Sel = document.selection.createRange();
                    var SelLength = document.selection.createRange().text.length;
                    Sel.moveStart('character', -el.value.length);
                    pos = Sel.text.length - SelLength;
                }
                return pos;
            }
        })(jQuery); //funcion para obtener cursor
        var cursor = $(evt.handleObj.selector).getCursorPosition();//setear cursor


        if (enteros == "false" && decimales == "false") {
            if (cursor[0] == cursor[1]) {
                return false;
            }
        } else if (typeof enteros == "number" && decimales == "false") {
            if (cursor[0] < enteros) {
                $(evt.handleObj.selector).selectRange(cursor[0], cursor[1]);
            } else {
                $(evt.handleObj.selector).selectRange(enteros);
            }
        }

    },

    checkmoneyint: function (evt) {
        if (!evt) return;
        var $input = this.$(evt.currentTarget);
        var digitos = $input.val().split('.');
        if ($input.val().includes('.')) {
            var justnum = /[\d]+/;
        } else {
            var justnum = /[\d.]+/;
        }
        var justint = /^[\d]{0,14}$/;

        if ((justnum.test(evt.key)) == false && evt.key != "Backspace" && evt.key != "Tab" && evt.key != "ArrowLeft" && evt.key != "ArrowRight") {
            app.alert.show('error_dinero', {
                level: 'error',
                autoClose: true,
                messages: 'El campo no acepta caracteres especiales.'
            });
            return "false";
        }

        if (typeof digitos[0] != "undefined") {
            if (justint.test(digitos[0]) == false && evt.key != "Backspace" && evt.key != "Tab" && evt.key != "ArrowLeft" && evt.key != "ArrowRight") {
                console.log('no se cumplen enteros')
                if (!$input.val().includes('.')) {
                    $input.val($input.val() + '.')
                }
                return "false";

            } else {
                return digitos[0].length;
            }
        }
    },

    checkmoneydec: function (evt) {
        if (!evt) return;
        var $input = this.$(evt.currentTarget);
        var digitos = $input.val().split('.');
        if ($input.val().includes('.')) {
            var justnum = /[\d]+/;
        } else {
            var justnum = /[\d.]+/;
        }
        var justdec = /^[\d]{0,1}$/;

        if ((justnum.test(evt.key)) == false && evt.key != "Backspace" && evt.key != "Tab" && evt.key != "ArrowLeft" && evt.key != "ArrowRight") {
            app.alert.show('error_dinero', {
                level: 'error',
                autoClose: true,
                messages: 'El campo no acepta caracteres especiales.'
            });
            return "false";
        }
        if (typeof digitos[1] != "undefined") {
            if (justdec.test(digitos[1]) == false && evt.key != "Backspace" && evt.key != "Tab" && evt.key != "ArrowLeft" && evt.key != "ArrowRight") {
                console.log('no se cumplen dec')
                return "false";
            } else {
                return "true";
            }
        }
    },

    formatcoin: function (evt) {
        if (!evt) return;
        var $input = this.$(evt.currentTarget);
        while ($input.val().indexOf(',') != -1) {
            $input.val($input.val().replace(',', ''))
        }
    },

    /*
      Author: Adrian Arauz 2018-08-28
      funcion: Validar acceso para creación de solicitudes. No debe permitir crear solicitudes si usuario tiene rol: "Gestión Comercial"
    */
    _rolnocreacion: function () {
        var roles_no_crea = app.lang.getAppListStrings('roles_no_crea_sol_list');
        var roles_usuario = app.user.attributes.roles;
        var puedecrear = i;
        console.log("Valida Rol de Usuario");
        for (var i = 0; i < roles_usuario.length; i++) {
            for (var puedecrear in roles_no_crea) {
                if (roles_usuario[i] == roles_no_crea[puedecrear]) {
                    app.alert.show("No_Rol_Solicitud", {
                        level: "error",
                        title: "No puedes generar una Solicitud ya que tienes un rol no permitido.",
                        autoClose: false,
                        return: false,
                    });
                    app.drawer.closeImmediately();
                    //console.log("ok");
                }
            }
        }
    },

    /*
      Author: EJC 2021/01/14
      funcion: Valida comunicación previa llamada o reunión nivel cuentas"
    */
    ContactoPrevio: function (fields, errors, callback) {
        if (this.model.get('tipo_producto_c') == '1' && this.model.get('account_id') != undefined) {
            app.api.call('get', app.api.buildURL('getallcallmeetAccount/?id_Account=' + this.model.get('account_id')), null, {
                success: _.bind(function (data) {
                    obj = JSON.parse(data);
                    if (obj.total_account == 0) {
                        app.alert.show("Sin comunicación previa", {
                            level: "error",
                            title: "No puede generar una Solicitud ya que no se tiene una llamada o reunión previa.",
                            autoClose: false,
                            return: false,
                        });
                        app.drawer.closeImmediately();
                    } else {
                        callback(null, fields, errors);
                    }
                }, this)

            });
        } else {
            callback(null, fields, errors);
        }
    },

    /*@Jesus Carrillo
     Funcion que valida que la cuenta de la presolicitud tenga una direccion con indicador "administracion"
     */
    valida_direc_indicador: function (fields, errors, callback) {
        self = this;
        var admin = 0;

        if (typeof this.model.get('account_id') != "undefined" && this.model.get('account_id') != "") {
            app.api.call('GET', app.api.buildURL('Accounts/' + this.model.get('account_id') + '/link/accounts_dire_direccion_1'), null, {
                success: _.bind(function (data) {

                    console.log(data);

                    for (var i = 0; i < data.records.length; i++) {

                        if (data.records[i].indicador != "" && data.records[i].inactivo == false) {

                            var array_indicador = this._getIndicador(data.records[i].indicador);

                            for (var j = 0; j < array_indicador.length; j++) {
                                if (array_indicador[j] == '16') {
                                    admin++;
                                }
                            }
                        }
                    }
                    if (admin == 0) {
                        app.alert.show('indicador_fail4', {
                            level: 'error',
                            messages: 'La cuenta necesita tener al menos un tipo de direcci\u00F3n <b>Administraci\u00F3n</b> en direcciones',
                        });
                        errors['indicador_16'] = errors['indicador_16'] || {};
                        errors['indicador_16'].required = true;
                    }
                    callback(null, fields, errors);
                }, self),
            });

        } else {
            errors['account_name'] = errors['account_name'] || {};
            errors['account_name'].required = true;
            callback(null, fields, errors);
        }

    },

    _getIndicador: function (idSelected, valuesSelected) {

        //variable con resultado
        var result = null;

        //Arma objeto de mapeo
        var dir_indicador_map_list = app.lang.getAppListStrings('dir_indicador_map_list');

        var element = {};
        var object = [];
        var values = [];

        for (var key in dir_indicador_map_list) {
            var element = {};
            element.id = key;
            values = dir_indicador_map_list[key].split(",");
            element.values = values;
            object.push(element);
        }

        //Recupera arreglo de valores por id
        if (idSelected) {
            for (var i = 0; i < object.length; i++) {
                if ((object[i].id) == idSelected) {
                    result = object[i].values;
                }
            }
            console.log('Resultado de idSelected:');
            console.log(result);
        }

        //Recupera id por valores
        if (valuesSelected) {
            result = [];
            for (var i = 0; i < object.length; i++) {
                if (object[i].values.length == valuesSelected.length) {
                    //Ordena arreglos y compara
                    valuesSelected.sort();
                    object[i].values.sort();
                    var tempVal = true;
                    for (var j = 0; j < valuesSelected.length; j++) {
                        if (valuesSelected[j] != object[i].values[j]) {
                            tempVal = false;
                        }
                    }
                    if (tempVal == true) {
                        result[0] = object[i].id;
                    }

                }
            }
            console.log('Resultado de valueSelected:');
            console.log(result);
        }

        return result;
    },

    valida_requeridos: function (fields, errors, callback) {
        var campos = "";
        var omitir = [];
        _.each(errors, function (value, key) {
            if ((key == 'amount' && this.model.get('amount') < 0) || (key == 'monto_c' && this.model.get('monto_c') < 0)) {
                omitir.push(key);
            }
            else {
                _.each(this.model.fields, function (field) {
                    if (_.isEqual(field.name, key)) {
                        if (field.vname) {
                            campos = campos + '<b>' + app.lang.get(field.vname, "Opportunities") + '</b><br>';
                        }
                    }
                }, this);
            }
        }, this);

        omitir.forEach(function (element) {
            delete errors[element];
        });

        if (campos) {
            app.alert.show("Campos Requeridos", {
                level: "error",
                messages: "Hace falta completar la siguiente información en la <b>Solicitud:</b><br>" + campos,
                autoClose: false
            });
        }
        callback(null, fields, errors);
    },

    _Validavehiculo: function (fields, errors, callback) {
        if (this.model.get('tct_numero_vehiculos_c') <= 0 && this.model.get('tipo_producto_c') == "6") {
            errors['tct_numero_vehiculos_c'] = errors['tct_numero_vehiculos_c'] || {};
            errors['tct_numero_vehiculos_c'].required = true;

            app.alert.show("Numero de Vehiculos", {
                level: "error",
                messages: "El Número de vehículos debe ser mayor a cero.",
                autoClose: false
            });
        }
        callback(null, fields, errors);
    },

    set_lista_productos: function () {
        var id_account = this.model.get('account_id');
        var SLL = 0;
        var SSOS = 0;
        //Eliminar elemento de la lista de productos
        var op = app.lang.getAppListStrings('tipo_producto_list');
        var op2 = {};
        var productos = App.user.attributes.productos_c;

        //Establece el valor por default del user logueado
        productos = productos.replace(/\^/g, "");
        productos = productos.split(",");

        //Itera la lista de productos para dejar solo los del usuario logueado
        for (id in productos) {
            if (id != 'unique') {
                op2[productos[id]] = op[productos[id]];
            }
        }

        //Eliminar los productos Unilease, Credito SOS y Tarjeta de Crédito de la lista
        Object.keys(op2).forEach(function (key) {
            if (key == 9 || key == 7 || key == "0" || key == 8 || key == 14) {
                delete op2[key];
            }
        });
        this.model.fields['tipo_producto_c'].options = op2;

        var i = 0;
        if(op2[this.model.attributes.tipo_producto_c]==undefined){
          for (var prop in op2) {
              if (prop == 4 && i == 0) { //Se valida i=0 para comprobar la primera posición, equivalente a op2[0]
                  this.model.set('tipo_producto_c', '4');
              } else if (prop == 1 && i == 0) {

                  //Solo hacer set al modelo, cuando en las opciones del campo se tenga el producto s setear
                  if (this.model.fields['tipo_producto_c'].options[prop] != undefined) {
                      this.model.set('tipo_producto_c', '1');
                  }
                  //Solo agregar el producto 7 CRÉDITO SOS, cuando éste valor se encuentre en la lista de opciones del campo
                  if (7 in this.model.fields['tipo_producto_c'].options) {
                      this.model.set('tipo_producto_c', '7');
                  }
                  //console.log("LEASING");
              } else if (prop == 3 && i == 0) {
                  this.model.set('tipo_producto_c', '3');
                  //console.log("AUTMOTRIZ");
              } else if (prop == 2 && i == 0) {
                  this.model.set('tipo_producto_c', '3');
                  //console.log("3");
              }
              else if (prop == 5 && i == 0) {
                  this.model.set('tipo_producto_c', '5');
                  //console.log("5");
              }
              else if (prop == 6 && i == 0) {
                  this.model.set('tipo_producto_c', '6');
                  //console.log("5");
              }
              else if (prop == 8 && i == 0) {//Uniclick

                  if (8 in this.model.fields['tipo_producto_c'].options) {
                      this.model.set('tipo_producto_c', '8');
                  }

                  if (9 in this.model.fields['tipo_producto_c'].options) {
                      this.model.set('tipo_producto_c', '9');
                  }
              }
              else if (prop == 9 && i == 0) {//Unilease
                  if (8 in this.model.fields['tipo_producto_c'].options) {
                      this.model.set('tipo_producto_c', '8');
                  }

                  if (9 in this.model.fields['tipo_producto_c'].options) {
                      this.model.set('tipo_producto_c', '9');
                  }

              }
              i++;
          }
        }
        /*
        if (op2[0] == "4") {
            this.model.set('tipo_producto_c', '4');
            //console.log("FACTORAJE");
        } else if (op2[0] == "1") {
            this.model.set('tipo_producto_c', '1');
            this.model.set('tipo_producto_c', '7')
            //console.log("LEASING");
        } else if (op2[0] == "3") {
            this.model.set('tipo_producto_c', '3');
            //console.log("AUTMOTRIZ");
        } else if (op2[0] == "2") {
            this.model.set('tipo_producto_c', '3');
            //console.log("3");
        }
        else if (op2[0] == "5") {
            this.model.set('tipo_producto_c', '5');
            //console.log("5");
        }
        else if (op2[0] == "6") {
            this.model.set('tipo_producto_c', '6');
            //console.log("5");
        }
        else if (op2[0] == "8") {//Uniclick
            this.model.set('tipo_producto_c', '8');
            this.model.set('tipo_producto_c', '9');

        }
        else if (op2[0] == "9") {//Unilease
            this.model.set('tipo_producto_c', '8');
            this.model.set('tipo_producto_c', '9');

        }
        */
        if (this.model.get('account_id') != "" && this.model.get('account_id') != undefined) {
            //Realiza llamada para recuperar oportunidades de la cuenta, estas son solicitudes de Leasing con Linea y solicitudes SOS (Si las tiene)
            app.api.call('GET', app.api.buildURL('Accounts/' + id_account + '/link/opportunities'), null, {
                success: function (solicitudes) {
                    op2[7] = "CREDITO SOS";
                    for (var i = 0; i < solicitudes.records.length; i++) {
                        if (solicitudes.records[i].tipo_producto_c == 1 && solicitudes.records[i].tipo_operacion_c == 2 && solicitudes.records[i].monto_c >= 7500000 && solicitudes.records[i].estatus_c != "K") {
                            SLL++;
                        }
                        if (solicitudes.records[i].tipo_producto_c == 7 && solicitudes.records[i].estatus_c != "K") {
                            SSOS++;
                        }
                    }
                    ;
                    if ((SLL >= 1 && SSOS >= 1) || SLL == 0 || !App.user.attributes.productos_c.includes("1")) {

                        //Quita valor Credito SOS
                        Object.keys(op2).forEach(function (key) {
                            if (key == 7) {
                                delete op2[key];
                            }
                        });
                    }
                    //Setea finalmente el valor de op2 en la lista para su visualización.
                    self.model.fields['tipo_producto_c'].options = op2;
                },
                error: function (e) {
                    throw e;
                }
            });
        }
    },

    bloqueamonto: function () {
        if (this.model.get('negocio_c') == '2') {
            $('[name="monto_c"]').prop("disabled", true);
            this.model.set('monto_c', 0);
            this.model.set('tct_etapa_ddw_c', "P");
            this.model.set('estatus_c', "PE");
        } else {
            $('[name="monto_c"]').prop("disabled", false);
            this.model.set('tct_etapa_ddw_c', "SI");
            this.model.set('estatus_c', "");
        }
    },
    //Evento para ejecutar la
    cuenta_asociada: function () {
        if (this.model.get('account_id') != "" && this.model.get('account_id') != undefined && window.bandera == 1) {
            var account = app.data.createBean('Accounts', { id: this.model.get('account_id') });
            account.fetch({
                success: _.bind(function (modelo) {
                    //Asignamos el promotor del producto para la operación
                    var producto = parseInt(this.model.get('tipo_producto_c'));
                    switch (producto) {
                        case 3:
                            id_promotor = modelo.get('user_id2_c');
                            name_promotor = modelo.attributes.promotorcredit_c;
                            break;
                        case 4:
                            id_promotor = modelo.get('user_id1_c');
                            name_promotor = modelo.attributes.promotorfactoraje_c;
                            break;
                        case 6:
                            id_promotor = modelo.get('user_id6_c');
                            name_promotor = modelo.attributes.promotorfleet_c;
                            break;
                        case 8:
                            id_promotor = modelo.get('user_id7_c');
                            name_promotor = modelo.attributes.promotoruniclick_c;
                            break;
                        case 9:
                            id_promotor = modelo.get('user_id7_c');
                            name_promotor = modelo.attributes.promotoruniclick_c;
                            break;
                        case 14:
                            id_promotor = App.user.id;
                            name_promotor = App.user.attributes.full_name;
                            break;
                        default:
                            id_promotor = modelo.get('user_id_c');
                            name_promotor = modelo.attributes.promotorleasing_c;
                            //seteo de asesor RM al campo asesor_rm_c
                            id_RM = modelo.get('user_id8_c');
                            name_promotorRM = modelo.attributes.promotorrm_c;
                            this.model.set("user_id1_c", id_RM);
                            this.model.set("asesor_rm_c", name_promotorRM);
                            break;
                    }
                    if (parseInt(this.model.get('negocio_c')) == 10) {
                        id_promotor = modelo.get('user_id7_c');
                        name_promotor = modelo.attributes.promotoruniclick_c;
                    }
                    if (parseInt(this.model.get('producto_financiero_c')) == 43 || parseInt(this.model.get('producto_financiero_c')) == 78) {
                        id_promotor = app.user.id;
                        name_promotor = app.user.attributes.full_name;
                    }
                    if (parseInt(this.model.get('producto_financiero_c')) == 40) {
                        id_promotor = modelo.get('user_id_c');
                        name_promotor = modelo.attributes.promotorleasing_c;
                    }
                    //Agrega asesor solo con privilegios de Admin Cartera
                    if (this.model.get('admin_cartera_c') == true && app.user.attributes.admin_cartera_c == 1 && app.user.attributes.config_admin_cartera == true) {

                        id_promotor = app.user.id;
                        name_promotor = app.user.attributes.full_name;
                    }
                    this.model.set("assigned_user_id", id_promotor);
                    this.model.set("assigned_user_name", name_promotor);
                    self.render();
                    this.model.set("assigned_user_id", id_promotor);
                    this.model.set("assigned_user_name", name_promotor);
                }, this)
            });
            this.set_lista_productos();
            this.render();
        }
    },

    showSubpanels: function () {
        if (typeof this.model.get('tipo_producto_c') != "undefined" && this.model.get('tipo_producto_c') != ""
            && typeof this.model.get('account_id') != "undefined" && this.model.get('account_id') != "") {

            app.alert.show('obtiene_BenefSuby', {
                level: 'process',
                title: 'Cargando...',
            });
            app.api.call('GET', app.api.buildURL('multilienaUniprod/' + this.model.get('account_id') + "/" + this.model.get('tipo_producto_c')), null, {
                success: _.bind(function (data) {
                    app.alert.dismiss('obtiene_BenefSuby');
                    self.multilinea_prod = data;
                    if (self.multilinea_prod == 1) {
                        /** Mostrar paneles Area beneficiada y subyacente **/
                        $('div[data-panelname="LBL_RECORDVIEW_PANEL2"]').show();
                        $('div[data-panelname="LBL_RECORDVIEW_PANEL3"]').show();
                        $('[data-name="area_benef_c"]').show();
                        $('[data-name="subyacente_c"]').show();

                    }
                    else {
                        $('div[data-panelname="LBL_RECORDVIEW_PANEL2"]').hide();
                        $('div[data-panelname="LBL_RECORDVIEW_PANEL3"]').hide();
                        $('[data-name="area_benef_c"]').hide();
                        $('[data-name="subyacente_c"]').hide();
                    }

                }, self),
            });
        }
    },

    setValidacionComercial: function (fields, errors, callback) {
        var producto = this.model.get('tipo_producto_c');
        var negocio = this.model.get('negocio_c');
        var prod_financiero=this.model.get('producto_financiero_c');
        var etapa = this.model.get('tct_etapa_ddw_c');

        if (((producto== 1 && negocio == 5 /*(negocio == 5 || negocio == 3) && (prod_financiero == "" || prod_financiero == "0")*/) || (producto=="2" && (negocio!="2" || negocio!="10"))) && etapa == "SI" && $.isEmptyObject(errors)) {
            var operacion = this.model.get('tipo_de_operacion_c');
            var status = this.model.get('estatus_c');
            var cuenta = this.model.get('account_id');

            if (operacion == 'LINEA_NUEVA' && etapa == "SI" && status != 'K') {
                //se manda el producto 1 constante ya que no se tiene producto CS como producto en la cuenta
                app.api.call('GET', app.api.buildURL('productoExcluye/' + cuenta + "/1/" + negocio + "/" + prod_financiero), null, {
                    success: _.bind(function (data) {
                        if (data == '1') {
                            console.log('Recupera check excluye');
                        } else {
                            this.model.set('estatus_c', "1");
                        }
                        callback(null, fields, errors);
                    }, self),
                });
            } else {
                callback(null, fields, errors);
            }
        } else {
            callback(null, fields, errors);
        }
    },

    reqBenefSuby: function (fields, errors, callback) {

        /** Requerido Area Beneficiada**/

        var optionBenef = this.model.get('area_benef_c');
        var campos_err = [];

        if (optionBenef != '' && optionBenef != null) {
            if ((optionBenef == 1 || optionBenef == 2) && this.model.get('estado_benef_c') == "") {
                errors['estado_benef_c'] = errors['estado_benef_c'] || {};
                errors['estado_benef_c'].required = true;

                campos_err.push('estado_benef_c');
            }

            if (optionBenef == 2 && (this.model.get('estado_benef_c') == "" || this.model.get('municipio_benef_c') == "")) {
                errors['municipio_benef_c'] = errors['municipio_benef_c'] || {};
                errors['municipio_benef_c'].required = true;
                campos_err.push('municipio_benef_c');
            }

            if (optionBenef == 3 && this.model.get('ent_gob_benef_c') == "") {
                errors['ent_gob_benef_c'] = errors['ent_gob_benef_c'] || {};
                errors['ent_gob_benef_c'].required = true;
                campos_err.push('ent_gob_benef_c');
            }

            if (optionBenef == 4 && this.model.get('cuenta_benef_c') == "") {
                errors['cuenta_benef_c'] = errors['cuenta_benef_c'] || {};
                errors['cuenta_benef_c'].required = true;
                campos_err.push('cuenta_benef_c');
            }
            if (optionBenef == 5 && this.model.get('emp_no_reg_benef_c') == "") {
                errors['emp_no_reg_benef_c'] = errors['emp_no_reg_benef_c'] || {};
                errors['emp_no_reg_benef_c'].required = true;
                campos_err.push('emp_no_reg_benef_c');
            }

            var campos = "";

            for (var i = 0; i < campos_err.length; i++) {
                _.each(this.model.fields, function (field) {
                    if (_.isEqual(field.name, campos_err[i])) {
                        if (field.vname) {
                            campos = campos + '<b>' + app.lang.get(field.vname, "Opportunities") + '</b><br>';
                        }
                    }
                }, this);

            }

            if (campos) {
                app.alert.show("Campos Requeridos", {
                    level: "error",
                    messages: "Hace falta completar la siguiente información de <b>Área beneficiada</b><br>" + campos,
                    autoClose: false
                });
            }

        }

        callback(null, fields, errors);
    },

    _duplicateBenefeSuby: function (fields, errors, callback) {


        if ((this.model.get('estado_benef_c') != "" || this.model.get('municipio_benef_c') != "" || this.model.get('ent_gob_benef_c') != ""
            || this.model.get('cuenta_benef_c') != "" || this.model.get('emp_no_reg_benef_c') != "")
            && self.multilinea_prod == 1

        ) {
            var cliente = this.model.get('account_id');
            var tipo = this.model.get('tipo_producto_c');
			var negocio = this.model.get('negocio_c');
			var producto_financiero = this.model.get('producto_financiero_c');
            var edobenefe = this.model.get('estado_benef_c') == undefined ? '' : this.model.get('estado_benef_c');
            var munibenefe = this.model.get('municipio_benef_c') == undefined ? '' : this.model.get('municipio_benef_c');
            var entibenefe = this.model.get('ent_gob_benef_c') == undefined ? '' : this.model.get('ent_gob_benef_c');
            var empbenefe = this.model.get('cuenta_benef_c') == undefined ? '' : this.model.get('cuenta_benef_c');
            var noEmpbenefe = this.model.get('emp_no_reg_benef_c') == undefined ? '' : this.model.get('emp_no_reg_benef_c');

            var concatenado = edobenefe + munibenefe + entibenefe + empbenefe + noEmpbenefe;

            var args = {
                'account_id': cliente,
                'tipo_producto_c': tipo,
                'concatenado': concatenado,
				'negocio_c': negocio,
				'producto_financiero_c': producto_financiero
            };
            app.alert.show('duplicado_BenefSuby', {
                level: 'process',
                title: 'Cargando...',
            });

            var opportunities = app.api.buildURL("duplicateOpp", '', {}, {});
            app.api.call("create", opportunities, { data: args }, {
                success: _.bind(function (data) {
                    var duplicado = data['duplicado'];
                    var mensaje = data['mensaje'];

                    app.alert.dismiss('duplicado_BenefSuby');
                    if (duplicado === 1) {
                        app.alert.show("Solicitud Benef", {
                            level: "error",
                            title: mensaje,
                            autoClose: false
                        });
                        app.error.errorName2Keys['custom_message'] = mensaje;
                        errors['benefeysuby'] = errors['benefeysuby'] || {};
                        errors['benefeysuby'].custom_message = true;
                    }
                    callback(null, fields, errors);
                }, this)
            });
        }
        else {
            callback(null, fields, errors);
        }
    },

    showfieldBenef: function () {
        var optionBenef = this.model.get('area_benef_c');

        this.model.set('estado_benef_c', '');
        this.model.set('municipio_benef', '');
        this.model.set('ent_gob_benef_c', '');
        this.model.set('cuenta_benef_c', '');
        this.model.set('emp_no_reg_benef_c', '');


        if (optionBenef != "" && optionBenef != null) {

            if (optionBenef == 1 || optionBenef == 2) {
                $('[data-name="estado_benef_c"]').show();
            } else {
                $('[data-name="estado_benef_c"]').hide();
            }

            if (optionBenef == 2) {
                $('[data-name="municipio_benef_c"]').show();
            } else {
                $('[data-name="municipio_benef_c"]').hide();
            }
            if (optionBenef == 3) {
                $('[data-name="ent_gob_benef_c"]').show();
            } else {
                $('[data-name="ent_gob_benef_c"]').hide();
            }
            if (optionBenef == 4) {
                $('[data-name="cuenta_benef_c"]').show();
            } else {
                $('[data-name="cuenta_benef_c"]').hide();
            }
            if (optionBenef == 5) {
                $('[data-name="emp_no_reg_benef_c"]').show();
            } else {
                $('[data-name="emp_no_reg_benef_c"]').hide();
            }
        }
        else {
            $('[data-name="estado_benef_c"]').hide();
            $('[data-name="municipio_benef_c"]').hide();
            $('[data-name="ent_gob_benef_c"]').hide();
            $('[data-name="cuenta_benef_c"]').hide();
            $('[data-name="emp_no_reg_benef_c"]').hide();
        }


    },
    showfieldSuby: function () {

        var optionSuby = this.model.get('subyacente_c');

        this.model.set('estado_suby_c');
        this.model.set('municipio_suby_c');
        this.model.set('ent_gob_suby_c');
        this.model.set('otro_suby_c');

        if (optionSuby != "" && optionSuby != null) {
            if (optionSuby == 1 || optionSuby == 2) {
                $('[data-name="estado_suby_c"]').show();

            } else {
                $('[data-name="estado_suby_c"]').hide();
            }

            if (optionSuby == 2) {
                $('[data-name="municipio_suby_c"]').show();

            } else {
                $('[data-name="municipio_suby_c"]').hide();

            }
            if (optionSuby == 3) {
                $('[data-name="ent_gob_suby_c"]').show();

            } else {
                $('[data-name="ent_gob_suby_c"]').hide();

            }
            if (optionSuby == 4) {
                $('[data-name="otro_suby_c"]').show();

            } else {
                $('[data-name="otro_suby_c"]').hide();

            }
        }
        else {
            $('[data-name="estado_suby_c"]').hide();
            $('[data-name="municipio_suby_c"]').hide();
            $('[data-name="ent_gob_suby_c"]').hide();
            $('[data-name="otro_suby_c"]').hide();
        }
    },

    reqBenfArea: function (fields, errors, callback) {

        var optionBenef = this.model.get('area_benef_c');

        if ((optionBenef == "" || optionBenef == null) && self.multilinea_prod == 1) {
            errors['area_benef_c'] = errors['area_benef_c'] || {};
            errors['area_benef_c'].required = true;

            app.alert.show("cAMPO bENEF", {
                level: "error",
                messages: "<b>Debe seleccionar un valor de Área beneficiada.</b> ",
                autoClose: false
            });
        }

        callback(null, fields, errors);

    },

    Updt_OptionProdFinan: function () {
		this.model.set('producto_financiero_c', '0');
        /** Recuperamos los productos financieros activo**/
		if (this.model.get('tipo_producto_c') != "" && this.model.get('negocio_c') != "") {
            var tipo_producto = this.model.get('tipo_producto_c');
            var tipo_negocio = this.model.get('negocio_c');
            app.api.call("read", app.api.buildURL("GetProductosFinancieros/" + tipo_producto, null, null, {}), null, {
                success: _.bind(function (data) {
                    var temp_array = [];
                    if (data != "") {
                        for (var i = 0; i < data.length; i++) {
                            if (data[i].disponible_seleccion == 1 && data[i].negocio == tipo_negocio) {
                                temp_array.push(data[i].producto_financiero);
                            }
                            if (data[i].disponible_seleccion != 1 && data[i].negocio == tipo_negocio && data[i].excluir_precalifica_c == 1 && app.user.attributes.excluye_valida_c == 1) {
                                temp_array.push(data[i].producto_financiero);
                            }
                        }
                        var optionsProdFinan = app.lang.getAppListStrings('producto_financiero_list');
                        Object.keys(optionsProdFinan).forEach(function (key) {
                            if (!temp_array.includes(key)) {
                                delete optionsProdFinan[key];
                            }
                        });
                        this.model.fields['producto_financiero_c'].options = optionsProdFinan;
						window.render = 1;
                        this.render();
                        if (temp_array != "") {
                            $('.record-cell[data-name="producto_financiero_c"]').css('display','block');
                            this.exist_PRodFinanciero = true;
                        }
                        else {
                            $('.record-cell[data-name="producto_financiero_c"]').css('display','none');
                            this.exist_PRodFinanciero = false;
                        }
                    }
                    else {
                        $('.record-cell[data-name="producto_financiero_c"]').css('display','none');
                    }
                }, this),
            });
        }
    },

    reqPrpductoFinanciero: function (fields, errors, callback) {
        var productofinan = this.model.get('producto_financiero_c');
        var producto = this.model.get('tipo_producto_c');

        if(producto != "1"){
            if (this.exist_PRodFinanciero && productofinan == "0") {
               errors['producto_financiero_c'] = errors['producto_financiero_c'] || {};
                errors['producto_financiero_c'].required = true;
            }
        }

        callback(null, fields, errors);
    },
    asesorCCP: function () {
        if (this.model.get('producto_financiero_c') == '78') {
            this.model.set("assigned_user_id", App.user.id);
            this.model.set("assigned_user_name", App.user.attributes.full_name);
        }

    },

    montoTenPercentCreditCard: function () {
        //TIPO DE PRODUCTO TARJETA DE CREDITO - AGREGA EL 10 % DE LA SUMA DE LAS LÍNEAS DE CREDITO EN EL MONTO
        self = this;
        if (this.model.get('tipo_producto_c') == '14') {

            var id_account = this.model.get('account_id');

            if (this.model.get('account_id') != "" && this.model.get('account_id') != undefined) {
                //Realiza llamada para recuperar oportunidades de la cuenta, estas son solicitudes con Linea
                app.api.call('GET', app.api.buildURL('Accounts/' + id_account + '/link/opportunities'), null, {
                    success: function (solicitudes) {

                        var montos = 0;

                        for (var i = 0; i < solicitudes.records.length; i++) {
                            //TIPO DE OPERACION LÍNEA, ETAPA CL Y ESTATUS N
                            if (solicitudes.records[i].tipo_operacion_c == '2' && solicitudes.records[i].tct_etapa_ddw_c == 'CL' && solicitudes.records[i].estatus_c == 'N') {

                                montos += parseInt(solicitudes.records[i].monto_c); //SUMA LOS MONTOS DE LAS LÍNEAS DE CREDITO
                            }
                        }

                        var sumaMontos = parseInt(montos);
                        var totalTenPercent = (10 / 100) * sumaMontos;  //OPERACION PARA OBTENER EL 10% DE LA SUMA DE LOS MONTOS

                        if (totalTenPercent > 1000000) {
                            self.model.set('monto_c', 1000000);
                            self.model.set('control_monto_c', 1000000);

                        } else {

                            self.model.set('monto_c', totalTenPercent);
                            self.model.set('control_monto_c', totalTenPercent);
                        }

                    },
                    error: function (e) {
                        throw e;
                    }
                });
            }
        }
    },

    validaMontoCreditCard: function (fields, errors, callback) {

        var controlMonto = this.model.get('control_monto_c');
        var formatoControlMonto = Intl.NumberFormat('es-MX',{style:'currency',currency:'MXN'}).format(controlMonto); //FORMATO MONEDA MXN

        //VALIDA QUE NO SUPERE EL $1,000,000 (UN MILLON) EN EL CAMPO DEL MONTO DEL TIPO DE PRODUCTO TARJETA DE CREDITO - CREATE
        if (this.model.get('tipo_producto_c') == '14') {

            if (parseFloat(this.model.get('monto_c')) > parseFloat(this.model.get('control_monto_c'))) {

                app.alert.show('message-control-monto', {
                    level: 'error',
                    title: 'El Monto de línea no debe ser mayor a '+ formatoControlMonto,
                    autoClose: false
                });

                errors['monto_c'] = errors['monto_c'] || {};
                errors['monto_c'].required = true;

            } else {

                this.model.set('amount', this.model.get('monto_c'));
            }

            callback(null, fields, errors);

        } else {
            callback(null, fields, errors);
        }
    },

    validaTipoCreditCard: function (fields, errors, callback) {
        //VALIDA SI YA EXISTE UNA SOLICITUD DE TIPO DE PRODUCTO TARJETA DE CREDITO TOMANDO EN CONSIDERACIÓN QUE NO ESTÉN CANCELADAS
        if (this.model.get('tipo_producto_c') == '14') {

            var id_account = this.model.get('account_id');

            if (this.model.get('account_id') != "" && this.model.get('account_id') != undefined) {

                app.api.call('GET', app.api.buildURL('Accounts/' + id_account + '/link/opportunities'), null, {
                    success: function (solicitudes) {

                        var duplicado = 0;

                        for (var i = 0; i < solicitudes.records.length; i++) {

                            if (solicitudes.records[i].tipo_producto_c == '14' && solicitudes.records[i].estatus_c != 'K') {
                                duplicado = 1;
                            }
                        }

                        if (duplicado == 1) {

                            app.alert.show('message-tipo-producto', {
                                level: 'error',
                                title: 'No puede guardar la presolicitud de tipo producto Tarjeta de Crédito, existe una abierta para el mismo cliente.',
                                autoClose: false
                            });

                            errors['tipo_producto_c'] = errors['tipo_producto_c'] || {};
                            errors['tipo_producto_c'].required = true;
                        }
                        callback(null, fields, errors);
                    },
                    error: function (e) {
                        throw e;
                    }
                });
            }

        } else {
            callback(null, fields, errors);
        }
    },

    validaPFCreditCard: function (fields, errors, callback) {
        //VALIDACIÓN DE TIPO DE PERSONA EN EL TIPO DE PRODUCTO TARJETA DE CREDITO
        if (this.model.get('account_id') != "" && this.model.get('account_id') != undefined) {

            var account = app.data.createBean('Accounts', { id: this.model.get('account_id') });
            account.fetch({
                success: _.bind(function (modelo) {

                    var tipoPersona = modelo.get('tipodepersona_c');
                    var viableCreditCard = modelo.get('viable_tc_c');

                    if (tipoPersona == "Persona Fisica" && this.model.get('tipo_producto_c') == "14") {

                        app.alert.show('message-tipo-persona', {
                            level: 'error',
                            title: 'No se puede crear una solicitud con Tipo de Persona Física en el producto Tarjeta de Crédito.',
                            autoClose: false
                        });

                        errors['account_name'] = errors['account_name'] || {};
                        errors['account_name'].required = true;
                    }
                    if (this.model.get('tipo_producto_c') == "14" && viableCreditCard == false) {

                        app.alert.show('message-viable-TC', {
                            level: 'error',
                            title: 'No se puede crear una solicitud de Tarjeta de Crédito ya que la cuenta No es viable.',
                            autoClose: false
                        });

                        errors['account_name'] = errors['account_name'] || {};
                        errors['account_name'].required = true;
                    }
                    callback(null, fields, errors);
                }, this)
            });

        } else {
            callback(null, fields, errors);
        }
    },

    validaSolCreditCard: function (fields, errors, callback) {
        //VALIDA SI YA EXISTEN SOLICITUDES CON LÍNEAS DE CREDITO AUTORIZADAS ANTES DE CREAR UNA SOLICITUD CON TIPO PRODUCTO TARJETA DE CREDITO
        if (this.model.get('tipo_producto_c') == '14') {

            var id_account = this.model.get('account_id');

            if (this.model.get('account_id') != "" && this.model.get('account_id') != undefined) {

                app.api.call('GET', app.api.buildURL('Accounts/' + id_account + '/link/opportunities'), null, {
                    success: function (solicitudes) {

                        var solClienteLinea = 0;

                        for (var i = 0; i < solicitudes.records.length; i++) {

                            if (solicitudes.records[i].tipo_operacion_c == '2' && solicitudes.records[i].tct_etapa_ddw_c == 'CL' && solicitudes.records[i].estatus_c == 'N') {
                                solClienteLinea = 1;
                            }
                        }

                        if (solClienteLinea == 0) {

                            app.alert.show('message-cliente-linea', {
                                level: 'error',
                                title: 'No se puede generar la Presolicitud de tipo producto Tarjeta de Crédito, se requiere tener una línea de crédito previamente autorizada.',
                                autoClose: false
                            });

                            errors['tipo_producto_c'] = errors['tipo_producto_c'] || {};
                            errors['tipo_producto_c'].required = true;
                        }
                        callback(null, fields, errors);
                    },
                    error: function (e) {
                        throw e;
                    }
                });
            }

        } else {
            callback(null, fields, errors);
        }
    },

	SOCInicio: function (fields, errors, callback) {
		var id_cuenta=this.model.get('account_id');
		if(id_cuenta!='' && id_cuenta != undefined ){
			var account = app.data.createBean('Accounts', {id:this.model.get('account_id')});
			account.fetch({
				success: _.bind(function (model) {
					if(model.attributes.alianza_soc_chk_c){
						this.model.set('alianza_soc_chk_c',true);
					}
				}, this)
			});
		}
		callback(null, fields, errors);
    },

	negocios: function (fields, errors, callback) {
		if((this.model.get('tipo_producto_c') == 2 && this.model.get('negocio_c') == 7 && !app.user.attributes.excluye_valida_c) || (this.model.get('tipo_producto_c') == 1 && this.model.get('negocio_c') == 8)) {
            app.alert.show('combinacion', {
                level: 'error',
                title: 'No cuenta con el permiso para crear este tipo de solicitud.',
                autoClose: false
            });
            errors['negocio_c'] = errors['negocio_c'] || {};
            errors['negocio_c'].required = true;
		}
		callback(null, fields, errors);
    },

    //En la creación no se posible crear solicitudes con el Producto Leasing
    validaCreacionLeasing: function (fields, errors, callback) {
        var leasingHabilitado = App.lang.getAppListStrings('habilita_creacion_opt_list')[1] == '1' ? true : false;
        if( this.model.get('tipo_producto_c') == '1' && !leasingHabilitado){
            app.alert.show("noLeasing", {
              level: "error",
              title: "Error",
              messages:"No es posible crear solicitud con producto Leasing",
              autoClose: false,
            });
            errors["tipo_producto_c"] = errors["tipo_producto_c"] || {};
            errors["tipo_producto_c"].required = true;
        }

        callback(null, fields, errors);
    }
})
