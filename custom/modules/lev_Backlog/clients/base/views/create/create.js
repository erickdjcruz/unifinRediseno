/**
 * Created by Levementum on 3/2/2016.
 * User: jgarcia@levementum.com
 */

({
    extendsFrom: 'CreateView',
	val : null,

    initialize: function (options) {
        self = this;
        this._super("initialize", [options]);


        this.getCurrentYearMonth("loading");
        this.getTipoOperacion();
        this.model.on("change:anio", _.bind(this.getCurrentYearMonth, this));
        this.model.on("change:cliente", _.bind(this.getTipoCliente, this));

        this.model.on("change:anio", _.bind(this.getTipoOperacion, this));
        this.model.on("change:mes", _.bind(this.getTipoOperacion, this));
        this.model.on("change:porciento_ri", _.bind(this.calcularRI, this));
        this.model.on("change:renta_inicial_comprometida", _.bind(this.calcularPorcientoRI, this));

        this.model.on("change:monto_comprometido", _.bind(this.asignaMontoFinal, this));
        //this.model.on("change:renta_inicial_comprometida", _.bind(this.asignaRIFinal, this));

        this.model.addValidationTask('valida_cuenta_no_contactar', _.bind(this.valida_cuenta_no_contactar, this));
        this.model.addValidationTask('check_monto_c', _.bind(this._ValidateAmount, this));
        this.model.addValidationTask('check_tipo_cliente', _.bind(this._ValidateTipo, this));

        /*@author Victor.Martinez
         * 23-07-2018
         * Valida si el cliente cuenta con al menos una solicitud de los tipos (Linea Nueva o Ratificacion/Incremento
         */
        //this.model.addValidationTask('check_solicitud', _.bind(this._ValidateSolicitud, this));
        this.model.addValidationTask('check_existingBL', _.bind(this._ValidateExistingBL, this));
        this.model.addValidationTask('camponovacio',_.bind(this.validacampoconversion,this));
        /************  CAmbiar valores tipo PRoducto LEasing   *****************/
		this.model.addValidationTask('num_tipo_producto',_.bind(this.num_tipo_leasing, this));
		this.model.addValidationTask('tipo_producto_requerido',_.bind(this.tipo_producto_requerido, this));
		/*********** ---- ***********************/
		this.model.addValidationTask('valida_requeridos',_.bind(this.valida_requeridos, this));
		this.model.addValidationTask('fecha_compromiso',_.bind(this.fecha_compromiso, this));

        /*
        var usuario = app.data.createBean('Users',{id:app.user.get('id')});
        usuario.fetch({
            success: _.bind(function(modelo) {
                this.model.set("region", modelo.get("region_c"));
                this.productos = modelo.get('productos_c');
                this.productoUsuario = modelo.get('tipodeproducto_c');
                this.multiProducto = modelo.get('multiproducto_c');


                var op = app.lang.getAppListStrings('tipo_producto_list');
                var op2 = {};
                for (id in this.productos){
                    op2[this.productos[id]] = op[this.productos[id]];
                }
                var lista = this.getField('producto');
                lista.items = op2;
                lista.render();

                this.model.set('producto',this.productos[0]);
                /*
                 if(this.productos[0] == "4"){
                 this.model.set('producto','4');
                 }else if(this.productos[0] == "1"){
                 this.model.set('producto','1');
                 }else if(this.productos[0] == "3"){
                 this.model.set('producto','3');
                 }else if(this.productos[0] == "2"){
                 this.model.set('producto','3');
                 }
                 else if(this.productos[0] == "5"){
                 this.model.set('producto','5');
                 }*/
            /*},this)
        });*/

        // validación de los campos con formato númerico
        this.events['keydown [name=dif_residuales_c]'] = 'checkInVentas';
        this.events['keydown [name=tasa_c]'] = 'checkInVentas';
        this.events['keydown [name=comision_c]'] = 'checkInVentas';
        this.events['keydown [name=monto_comprometido]'] = 'checkInVentas';
        this.events['keydown [name=porciento_ri]'] = 'checkInVentas';
        this.events['keydown [name=renta_inicial_comprometida]'] = 'checkInVentas';
        this.events['keydown [name=tct_conversion_c]'] = 'checkInVentas';

    },

    _render: function() {
        this._super("_render");

        this.$('[data-name=editar]').hide();
        if(this.$('[data-fieldname=lev_backlog_opportunities_name]').children().children(['data-original-title']).html() != null&&
            this.$('[data-fieldname=lev_backlog_opportunities_name]').children().children(['data-original-title']).html() != ""){
            this.model.set("editar", false);
      
        }

		this.setValores(this.val);

        // Oculta campos al crear BL
        this.$('div[data-name=numero_de_backlog]').hide();
        this.$('div[data-name=tipo_c]').hide();
        //this.$('div[data-name=producto_c]').hide();
        this.$('div[data-name=region]').hide();
        this.$('div[data-name=estatus_operacion_c]').hide();
        this.$('div[data-name=lev_backlog_opportunities_name]').hide();
        this.$('div[data-name=numero_de_solicitud]').hide();
        this.$('div[data-name=monto_final_comprometido_c]').hide();
        this.$('div[data-name=ri_final_comprometida_c]').hide();
        this.$('div[data-name=monto_real_logrado]').hide();
        this.$('div[data-name=renta_inicial_real]').hide();
        this.$('div[data-name=etapa_c]').hide();
        this.$('div[data-name=etapa_preliminar_c]').hide();
        this.$('div[data-name=description]').hide();
        this.$('div[data-name=progreso]').hide();
        this.$('div[data-name=date_entered_by]').hide();
        this.$('div[data-name=date_modified_by]').hide();

        //Ocultar banderas de control para establecer registro como solo lectura
        this.$('div[data-name=tct_carga_masiva_chk_c]').hide();
        this.$('div[data-name=tct_bloqueo_txf_c]').hide();

		this.$(".record-cell[data-name='blank_space']").hide();

        var usuario = app.data.createBean('Users',{id:app.user.get('id')});
        usuario.fetch({
            success: _.bind(function(modelo) {
                this.model.set("region", modelo.get("region_c"));
                this.model.set("equipo", modelo.get("equipo_c"));
                this.productos = modelo.get('productos_c');
                this.productoUsuario = modelo.get('tipodeproducto_c');
                this.multiProducto = modelo.get('multiproducto_c');


                var op = app.lang.getAppListStrings('producto_activo_backlog_list');
                var op2 = {};
                /*for (id in this.productos){
                    op2[this.productos[id]] = op[this.productos[id]];
                }*/
				for (id in this.productos){
					for (key in op){
						if(this.productos[id] ==  key && key !=""){
							//delete lista_productos[key];
							op2[this.productos[id]] = op[key];
						}
					}
                }

                var lista = this.getField('producto_c');
                lista.items = op2;
                lista.render();

                //Establece valor por default, solo si el campo viene null
                if(this.model.get('producto_c')==null){
                    this.model.set('producto_c',this.productos[0]);
                }
                /*
                if(this.productos[0] == "4"){
                    this.model.set('producto','4');
                }else if(this.productos[0] == "1"){
                    this.model.set('producto','1');
                }else if(this.productos[0] == "3"){
                    this.model.set('producto','3');
                }else if(this.productos[0] == "2"){
                    this.model.set('producto','3');
                }
                else if(this.productos[0] == "5"){
                    this.model.set('producto','5');
                }*/
            },this)
        });
    },


    checkInVentas:function (evt) {
        var enteros=this.checkmoneyint(evt);
        var decimales=this.checkmoneydec(evt);
        $.fn.selectRange = function(start, end) {
            if(!end) end = start;
            return this.each(function() {
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
            $.fn.getCursorPosition = function() {
                var el = $(this).get(0);
                var pos = [];
                if('selectionStart' in el) {
                    pos = [el.selectionStart,el.selectionEnd];
                } else if('selection' in document) {
                    el.focus();
                    var Sel = document.selection.createRange();
                    var SelLength = document.selection.createRange().text.length;
                    Sel.moveStart('character', -el.value.length);
                    pos = Sel.text.length - SelLength;
                }
                return pos;
            }
        })(jQuery); //funcion para obtener cursor
        var cursor=$(evt.handleObj.selector).getCursorPosition();//setear cursor


        if (enteros == "false" && decimales == "false") {
            if(cursor[0]==cursor[1]) {
                return false;
            }
        }else if (typeof enteros == "number" && decimales == "false") {
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
        if($input.val().includes('.')) {
            var justnum = /[\d]+/;
        }else{
            var justnum = /[\d.]+/;
        }
        var justint = /^[\d]{0,14}$/;

        if((justnum.test(evt.key))==false && evt.key!="Backspace" && evt.key!="Tab" && evt.key!="ArrowLeft" && evt.key!="ArrowRight"){
            app.alert.show('error_dinero', {
                level: 'error',
                autoClose: true,
                messages: 'El campo no acepta caracteres especiales.'
            });
            return "false";
        }

        if(typeof digitos[0]!="undefined") {
            if (justint.test(digitos[0]) == false && evt.key != "Backspace" && evt.key != "Tab" && evt.key != "ArrowLeft" && evt.key != "ArrowRight") {
                console.log('no se cumplen enteros')
                if(!$input.val().includes('.')) {
                    $input.val($input.val()+'.')
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
        if($input.val().includes('.')) {
            var justnum = /[\d]+/;
        }else{
            var justnum = /[\d.]+/;
        }
        var justdec = /^[\d]{0,1}$/;

        if((justnum.test(evt.key))==false && evt.key!="Backspace" && evt.key!="Tab" && evt.key!="ArrowLeft" && evt.key!="ArrowRight"){
            app.alert.show('error_dinero', {
                level: 'error',
                autoClose: true,
                messages: 'El campo no acepta caracteres especiales.'
            });
            return "false";
        }
        if(typeof digitos[1]!="undefined") {
            if (justdec.test(digitos[1]) == false && evt.key != "Backspace" && evt.key != "Tab" && evt.key != "ArrowLeft" && evt.key != "ArrowRight") {
                console.log('no se cumplen dec')
                return "false";
            } else {
                return "true";
            }
        }
    },



    /*@author Victor.Martinez
    * 23-07-2018
    * Valida que el cliente tenga solictud de tipos "Linea nueva", "Ractificación/Incremento" o "Ambas"
    */
        _ValidateSolicitud:function(fields, errors, callback){

            self = this;
            var accountid=this.model.get('account_id_c');
            var producto=this.model.get('producto_c');
            if(producto!="2"){ //Se aplica validación únicamente cuando el producto no es Crédito Simple
                if (accountid) {
                    app.api.call('GET', app.api.buildURL('Accounts/'+accountid+'/link/opportunities', null, null, {
                        "filter":[
                            {
                                $or:[
                                    {
                                        "tipo_de_operacion_c":"LINEA_NUEVA"
                                    },
                                    {
                                        "tipo_de_operacion_c":"RATIFICACION_INCREMENTO"
                                    }
                                ]
                            }
                        ]
                    }), null, {
                        success: _.bind(function (data){

                            if (data.records.length<1) {
                                app.error.errorName2Keys[''] = '';
                                errors[''] = errors[''] || {};
                                errors[''] = errors[''] || {};
                                errors[''].custom_message1 = true;
                                errors[''].required = true;
                                app.alert.show('validaSolicitudes', {
                                    level: 'error',
                                    messages: 'Para crear un Backlog es necesario que el cliente cuente m&iacutenimo con una Pre-Solicitud de l&iacutenea'
                                });
                            }
                            callback(null, fields, errors);

                        }, self)
                    });
                }else {
                    callback(null, fields, errors);
                }

            }else{
                callback(null, fields, errors);
            }

        },

    _ValidateExistingBL:function(fields, errors, callback){

        //var id_account=$('input[name="cliente"]').val();

        var self=this;

        var id_account=this.model.get('account_id_c');
        var mes=this.model.get('mes');
        var anio=this.model.get('anio');
		var producto=this.model.get('producto_c');

        if(id_account && id_account != '' && id_account.length>0){


            var bl_url = app.api.buildURL('lev_Backlog?filter[0][account_id_c][$equals]='+id_account+'&filter[1][mes][$equals]='+mes+'&filter[2][anio][$equals]='+anio+'&filter[3][estatus_operacion_c][$not_equals]=1&filter[4][producto_c][$equals]='+producto+'&fields=id,mes,estatus_operacion_c,producto_c',
                null, null, null);


            app.api.call('GET', bl_url, {}, {
                success: _.bind(function (data) {

                    if(data!=null){
                        var meses =['0','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']
                        if(data.records.length>0){

                            app.alert.show('error_bl_mes', {
                                level: 'error',
                                messages: 'Esta Cuenta ya posee un backlog en el mes: '+meses[data.records[0].mes],
                                autoClose: false
                            });
                            app.error.errorName2Keys['custom_message1'] = 'Esta Cuenta ya posee un backlog en el mes: '+meses[data.records[0].mes];
                            errors['cliente'] = errors['cliente'] || {};
                            errors['cliente'].custom_message1 = true;

                        }

                    }

                    callback(null, fields, errors);

                },self),

            });

        }else{

            app.error.errorName2Keys['custom_message1'] = 'La cuenta ya posee un backlog en el mes establecido';
            errors['cliente'] = errors['cliente'] || {};
            errors['cliente'].custom_message1 = true;
            errors['cliente'].required = true;

            callback(null, fields, errors);
        }


    },

    getCurrentYearMonth: function(stage){

        var currentYear = (new Date).getFullYear();
        var currentMonth = (new Date).getMonth();
        var currentDay = (new Date).getDate();

        //currentMonth += 1;

        if(currentDay < 10){
            currentMonth += 1;
        }
        if(currentDay >= 10){
            currentMonth += 2;
        }

        /*
          @AF - 2018-07-16
          Ajuste: Se deben mostrar los 3 meses siguientes y sólo el año correspondiente.
                  Si se incluyen meses del siguiente año, agregar año futuro a lista de valores.
        */

        //Valida número de mes actual
        var limitMonth = currentMonth + 2;
        var nextMonth = 0;
        var nextYear = currentYear;
        if (limitMonth > 12) {
          nextMonth = limitMonth - 12;
          nextYear = currentYear + 1;
        }

        //Valida Año
        var opciones_year = app.lang.getAppListStrings('anio_list');
        Object.keys(opciones_year).forEach(function(key){
            //Quita años previos
            if(key < currentYear){
                delete opciones_year[key];
            }
            //Habilita años futuros
            if(key > nextYear){
                delete opciones_year[key];
            }

        });
        //Establece valores para año
        this.model.fields['anio'].options = opciones_year;
        // if (this.model.get("anio")== "") {
        //     this.model.set("anio",currentYear);
        // }

        //Valida Meses
        var opciones_mes = app.lang.getAppListStrings('mes_list');
        //Quita mese para año futuro
        if(this.model.get("anio") > currentYear){
          Object.keys(opciones_mes).forEach(function(key){
                if(key != ''){
                    if(key > nextMonth){
                        delete opciones_mes[key];
                    }
                }
          });
        }
        //Quita mese para año actual
        if(this.model.get("anio") == currentYear || this.model.get("anio")==""){
          Object.keys(opciones_mes).forEach(function(key){
                if(key != ''){
                    //Quita meses fuera de rango(3 meses)
                    if(key < currentMonth || key >limitMonth ){
                        delete opciones_mes[key];
                    }
                }
          });
        }

        this.model.fields['mes'].options = opciones_mes;

        //this.model.set("mes", '');
		this.val = this.getValores();

        if(stage != "loading"){
            this.render();
			this.setValores(this.val);;
        }
    },

	getValores: function(){
      var valores = {
        tempProducto : this.model.get('producto_c'),
		tempProductoLead : this.model.get('num_tipo_op_leasing_c'),
		tempProductoCred : this.model.get('num_tipo_op_credito_c')
      }
      return valores;
    },

    setValores: function(valores){
		this.model.set("producto_c", valores.tempProducto);
		this.model.set("num_tipo_op_leasing_c", valores.tempProductoLead);
		this.model.set("num_tipo_op_credito_c", valores.tempProductoCred);

    },

    getTipoCliente: function(){
        //console.log("getTipoCliente");
        if(this.model.get('account_id_c') =='' || this.model.get('account_id_c') == undefined){
          return;
        }
        var disponible = 0;
        app.alert.show('getDetailUser', {
            level: 'process',
            title: 'Cargando...'
        });
		    this.val = this.getValores();
        app.api.call("read", app.api.buildURL("Accounts/" + this.model.get('account_id_c'), null, null, {
            fields: name,
        }), null, {
            success: _.bind(function (data) {
                if(data.tipo_registro_cuenta_c == "2"){ // 2 - Prospecto
                    this.model.set("tipo_c","2");
                    this.model.set("etapa_preliminar_c","3");
                    this.model.set("etapa_c","3");
                }else if(data.tipo_registro_cuenta_c == "3"){ // 3 - Cliente
                    //console.log("Valida lineas de credito autorizadas para leasing");
                    app.api.call("read", app.api.buildURL("Accounts/" + this.model.get('account_id_c') + "/link/opportunities", null, null, {
                        fields: name,
                        "filter": [
                            {
                                "tipo_operacion_c": "2",
                                "tipo_producto_c": "1",
                                "amount":{
                                    "$gt":"0"
                                }
                            }
                        ]
                    }), null, {
                        success: _.bind(function (data) {
                            $.each(data.records,function() {
                                disponible += parseFloat(this.amount);
                                //console.log(disponible);
                            });
                            //console.log(data.records.length);
                            if (data.records.length > 0) {
                                this.model.set("tipo_c","3");
                                //this.model.set("etapa_preliminar","Autorizada");
                                //this.model.set("etapa","Autorizada");
                                this.model.set("monto_original",disponible);
                            }else{
                                this.model.set("tipo_c","2");
                                //this.model.set("etapa_preliminar","Prospecto");
                                //this.model.set("etapa","Prospecto");
                                this.model.set("monto_original",0);
                            }
                        }, this)
                    });
                    var MontoOperar = this.model.get("monto_comprometido") - this.model.get("renta_inicial_comprometida");

                    //console.log("Disponible: " + disponible);
                    //console.log("Monto a operar: " + MontoOperar);

                    if (disponible >= MontoOperar){
                        //console.log("Le alcanza, se va a autorizada");
                        this.model.set("etapa_preliminar_c","1");
                        this.model.set("etapa_c","1");
                    }else{
                        //console.log("No le alcanza, se va a prospecto");
                        this.model.set("etapa_preliminar_c","3");
                        this.model.set("etapa_c","3");
                    }
                }else{
                    this.model.set("tipo_c","4");
                    this.model.set("etapa_preliminar_c","3");
                    this.model.set("etapa_c","3");
                }

                //var promotor = data.user_id_c;
                var promotor = "";
                //Obtener producto principal del usuario firmado
                var producto_usuario=app.user.attributes.tipodeproducto_c;
                switch(producto_usuario){
                    case '1':
                        promotor=data.user_id_c; //Leasing
                        break;
                    case '3':
                        promotor=data.user_id2_c; //credito
                        break;
                    case '4':
                        promotor=data.user_id1_c; //factoraje
                        break;
                    case '6':
                        promotor=data.user_id6_c; //fleet
                        break;
                    case '8':
                        promotor=data.user_id7_c; //uniclick
                        break;
                    case '9':
                        promotor=data.user_id7_c; //unilease, mismo que uniclick
                        break;
                    default:
                        promotor=data.user_id_c;
                        break;
                }



                //Obtiene el promotor del cliente, equipo y region del mismo
                app.api.call("read", app.api.buildURL("Infouser/" + promotor, null, null, {}), null, {
                  success: _.bind(function (modelo) {
                    app.alert.dismiss('getDetailUser');
                    if(modelo.id!=undefined){
                        //establece las opciones disponibles en campo de producto, correspondientes
                        var lista_productos= app.lang.getAppListStrings('tipo_producto_list');
                        var productos_user=modelo.productos_c;

                        Object.keys(lista_productos).forEach(function (key) {
                            if(!productos_user.includes(key) && key !=""){
                                delete lista_productos[key];
                            }
                        });
                        
                        this.model.fields['producto_c'].options = lista_productos;
                        //Estableciendo nuevas opciones en campo de producto
                        var campo_producto=this.getField('producto_c');
                        campo_producto.items=lista_productos;
                        campo_producto.render();
                        this.model.set("region", modelo.region_c);
                        this.model.set("equipo", modelo.equipo_c);
                        this.model.set("assigned_user_id", modelo.id);
                        this.model.set("assigned_user_name", modelo.first_name + ' ' + modelo.last_name);
                    }
                    
                  }, this)
                });
            }, this)
        });
    },

    getTipoOperacion: function (){

        var currentYear = (new Date).getFullYear();
        var currentMonth = (new Date).getMonth();
        var currentDay = (new Date).getDate();

        //currentMonth += 1;

        if(currentDay <= 20){
            currentMonth += 1;
        }
        if(currentDay > 20){
            currentMonth += 2;
        }

        if(this.model.get("anio") <= currentYear){

            if(currentMonth == this.model.get("mes")){
                this.model.set("tipo_operacion_c", "3");
            }else{
                this.model.set("tipo_operacion_c", "2");
            }

        }else{
            this.model.set("tipo_operacion_c", "2");
        }

    },

    valida_cuenta_no_contactar:function (fields, errors, callback) {
		if (!app.user.attributes.tct_no_contactar_chk_c && !app.user.attributes.bloqueo_credito_c && !app.user.attributes.bloqueo_cumple_c) {
			if (this.model.get('account_id_c')!= "" && this.model.get('account_id_c')!= undefined) {
				var account = app.data.createBean('Accounts', {id:this.model.get('account_id_c')});
				account.fetch({
					success: _.bind(function (model) {
						var url = app.api.buildURL('tct02_Resumen/' + this.model.get('account_id_c'), null, null);
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
										var alertas=app.alert.getAll();
										for (var property in alertas) {
											if(property != 'cuentas_no_contactar'){
												app.alert.dismiss(property);
											}
										}
									} else {
										app.router.navigate(this.module, {trigger: true});
									}
								}
								callback(null, fields, errors);
							}, this)
						});
					}, this)
				});
			}else {
				callback(null, fields, errors);
			}
        } else {
            callback(null, fields, errors);
        }
    },

    _ValidateAmount: function (fields, errors, callback){
        //CVV evaluamos si el monto disponible alcanza para la operaci�n
        //console.log("Evaluamos si el monto disponible alcanza para la operaci�n");
        var MontoOperar = this.model.get("monto_comprometido") - this.model.get("renta_inicial_comprometida");
        var disponible = this.model.get("monto_original");
        //console.log("Disponible: " + disponible);
        //console.log("Monto a operar: " + MontoOperar);
        if (disponible >= MontoOperar){
            this.model.set("etapa_preliminar_c","1");
            this.model.set("etapa_c","1");
        }else{
            this.model.set("etapa_preliminar_c","3");
            this.model.set("etapa_c","3");
        }

        if (parseFloat(this.model.get('monto_comprometido')) <= 0)
        {
            errors['monto_comprometido'] = errors['monto_comprometido'] || {};
            errors['monto_comprometido'].required = true;
        }

        /*
        if (parseFloat(this.model.get('renta_inicial_comprometida')) <= 0)
        {
            errors['renta_inicial_comprometida'] = errors['renta_inicial_comprometida'] || {};
            errors['renta_inicial_comprometida'].required = true;
        }
        */

        callback(null, fields, errors);
    },

    _ValidateTipo: function(fields, errors, callback){
        if(this.model.get("tipo_c") == "4"){

            errors['tipo_c'] = errors['tipo_c'] || {};
            errors['tipo_c'].required = true;

            app.alert.show('tipo de persona', {
                level: 'error',
                messages: 'Para poder generar una operaci\u00F3n, la persona debe ser un cliente o prospecto.',
                autoClose: false
            });
        }
        callback(null, fields, errors);
    },

    calcularRI: function(){
        if(this.model.get("monto_comprometido") == 1){
            this.model.set("porciento_ri",0);
            this.model.set("renta_inicial_comprometida", 0);
        }
        if(!_.isEmpty(this.model.get("monto_comprometido")) && !_.isEmpty(this.model.get("porciento_ri"))){
            /*if(this.model.get("monto_comprometido") == 1){
                console.log("El monto es $1 por lo que el porcentaje de RI debe ponerse en 0 y el monto tmbn.");
                this.model.set("porciento_ri",0);
                console.log(this.model.get("porciento_ri"));
                this.model.set("renta_inicial_comprometida", 0);
            }else{*/
                var percent = ((this.model.get("monto_comprometido") * this.model.get("porciento_ri")) / 100).toFixed(2);
                this.model.set("renta_inicial_comprometida", percent);
            //}
        }
    },

    calcularPorcientoRI: function(){
        if (this.model.get("renta_inicial_comprometida") == 0){
            this.model.set("porciento_ri", 0);
        }else{
            if(!_.isEmpty(this.model.get("monto_comprometido")) && !_.isEmpty(this.model.get("renta_inicial_comprometida")) && this.model.get("renta_inicial_comprometida") != 0){
                var percent = ((this.model.get("renta_inicial_comprometida") * 100) / this.model.get("monto_comprometido")).toFixed(2);
                this.model.set("porciento_ri", percent);
            }else{
                if(!_.isEmpty(this.model.get("porciento_ri")) && !_.isEmpty(this.model.get("renta_inicial_comprometida")) && this.model.get("porciento_ri") != 0){
                    var comprometido = ((this.model.get("renta_inicial_comprometida") * 100) / this.model.get("porciento_ri")).toFixed(2);
                    this.model.set("monto_comprometido", comprometido);
                }
            }
        }
        this.model.set("ri_final_comprometida_c", this.model.get("renta_inicial_comprometida"));
    },

    asignaMontoFinal: function(){
        var Monto = this.model.get("monto_comprometido");
        this.model.set("monto_final_comprometido_c", Monto);

        if(Monto == 1){
            this.model.set("porciento_ri",0);
            this.model.set("renta_inicial_comprometida",0);
        }

        //Valida si le alcanza y actualiza la Etapa
        if(this.model.get("monto_original")>0){
            var MontoOperar = this.model.get("monto_comprometido") - this.model.get("renta_inicial_comprometida");
            if (this.model.get("monto_original") >= MontoOperar){
                this.model.set("etapa_preliminar_c","1");
                this.model.set("etapa_c","1");
            }else{
                this.model.set("etapa_preliminar_c","3");
                this.model.set("etapa_c","3");
            }
        }

        this.calcularRI();
    },
    /*
    asignaRIFinal: function(){
        var Monto = this.model.get("renta_inicial_comprometida");
        this.model.set("ri_final_comprometida_c", Monto);
    },
    */

    valida_requeridos: function(fields, errors, callback) {
        var campos = "";
        _.each(errors, function(value, key) {
            _.each(this.model.fields, function(field) {
                if(_.isEqual(field.name,key)) {
                    if(field.vname) {
                        campos = campos + '<b>' + app.lang.get(field.vname, "lev_Backlog") + '</b><br>';
                    }
          		  }
       	    }, this);
        }, this);
        if(campos) {
            app.alert.show("Campos Requeridos", {
                level: "error",
                messages: "Hace falta completar la siguiente información en el <b>Backlog:</b><br>" + campos,
                autoClose: false
            });
        }
        callback(null, fields, errors);
    },

    validacampoconversion: function(fields, errors, callback){
        if (this.model.get('tct_conversion_c')=="" || this.model.get('tct_conversion_c')==undefined) {
            errors['tct_conversion_c'] = errors['tct_conversion_c'] || {};
            errors['tct_conversion_c'].required = true;
        }
        //Valda valor menor o igual a 0
        if (parseFloat(this.model.get('tct_conversion_c')) <= 0){

            errors['tct_conversion_c'] = errors['tct_conversion_c'] || {};
            errors['tct_conversion_c'].required = true;

            app.alert.show("Campo con valor cero", {
                level: "error",
                messages: "El campo <b>Probabilidad de Conversión</b> debe ser mayor a cero.",
                autoClose: false
            });

        }
        // Valida valor mayor a 100
        if (parseFloat(this.model.get('tct_conversion_c')) > 100){

            errors['tct_conversion_c'] = errors['tct_conversion_c'] || {};
            errors['tct_conversion_c'].required = true;

            app.alert.show("conversion_mayor_cien", {
                level: "error",
                messages: "El campo <b>Probabilidad de Conversión</b> debe ser menor o igual a cien.",
                autoClose: false
            });

        }
        callback(null, fields, errors);
    },

	num_tipo_leasing: function(fields, errors, callback) {
		var tiposnum = app.lang.getAppListStrings('num_tipo_op_leasing_list');
		var data1 = this.model.get('tct_tipo_op_leasing_mls_c');
		var producto = this.model.get('producto_c');
		var salida = [];

		if(producto == "2"){
			if(this.model.get('comision_c') == undefined){
				errors['comision_c'] = errors['comision_c'] || {};
				errors['comision_c'].required = true;
			}else if(parseFloat(this.model.get('comision_c')) <= 0.0 ){
				errors['comision_c'] = errors['comision_c'] || {};
				errors['comision_c'].required = true;
				app.alert.show("comision", {
					level: "error",
					messages: "El campo <b>Comisión</b> debe ser mayor a 0.",
					autoClose: false
				});
			}
		}
		callback(null, fields, errors);
    },

	tipo_producto_requerido: function(fields, errors, callback) {
		var producto = this.model.get('producto_c');
		var salida = [];

		if(producto == "1"){
			if(this.model.get('num_tipo_op_leasing_c')=='' || this.model.get('num_tipo_op_leasing_c')==null){
				errors['num_tipo_op_leasing_c'] = errors['num_tipo_op_leasing_c'] || {};
				errors['num_tipo_op_leasing_c'].required = true;
			}
		}else if(producto == "2"){
			if(this.model.get('num_tipo_op_credito_c')== '' || this.model.get('num_tipo_op_credito_c')== null){
				errors['num_tipo_op_credito_c'] = errors['num_tipo_op_credito_c'] || {};
				errors['num_tipo_op_credito_c'].required = true;
			}
		}
		callback(null, fields, errors);
    },

    fecha_compromiso: function(fields, errors, callback) {
		var h = new Date();
		h.setHours(0, 0, 0, 0);
		var f = new Date(this.model.get('fecha_compromiso_c'));
		f.setDate(f.getDate() + 1);
        if(f < h || f > new Date(new Date(h).setMonth(h.getMonth() + 3))) {
			errors['fecha_compromiso_c'] = errors['fecha_compromiso_c'] || {};
			errors['fecha_compromiso_c'].required = true;
            app.alert.show("fecha_compromiso", {
                level: "error",
                messages: "La Fecha compromiso no puede ser menor al día de hoy ni mayor a tres meses",
                autoClose: false
            });
        }
        callback(null, fields, errors);
    },
})
