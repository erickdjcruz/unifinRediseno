({
    extendsFrom: 'CreateView',
    previas: null,
    initialize: function (options)
	{
		relContext = this;
        accountContext = null; //._previousAttributes.model._previousAttributes;
        if(options.context.attributes.from360!=undefined){
            this.desdeVista360=options.context.attributes.from360;
        }

        /*
         If you are on 7.2.0, there is a dependency bug that can cause the screen to render an empty form.
         To correct this, uncomment the code below:
         if (!_.has(options.meta, "template"))
         {
            *options.meta.template = 'record';
         }
         */
        this._super('initialize', [options]);

        //CVV INICIO
		//this.events['blur input[name=relaciones_activas]'] = 'doRelationFields';
        this.model.addValidationTask('valida_cuenta_no_contactar', _.bind(this.valida_cuenta_no_contactar, this));
    	this.model.addValidationTask('check_Campos_Contacto', _.bind(this._doValidateContactFields, this));
    	//this.model.addValidationTask('check_custom_validations', _.bind(this.checarValidacionesonSave, this));
    	this.model.addValidationTask('check_custom_relacion_c', _.bind(this.checarRelacion, this));
    	this.model.addValidationTask('check_Relaciones_Permitidas', _.bind(this.RelacionesPermitidas, this));
    	this.model.addValidationTask('check_Relaciones_Duplicadas', _.bind(this.relacionesDuplicadas, this));
        this.model.addValidationTask('crearrelacionaccionista', _.bind(this.Relacionaccionista, this));
        this.model.addValidationTask('validarequeridosPropReal',_.bind(this.validaPropietarioReal, this));
        this.model.addValidationTask('validarequeridosProvRec',_.bind(this.validaProveedorRecursos, this));
        this.model.addValidationTask('validarequeridosRelActivas',_.bind(this.validaRelacionesValidation, this));
        this.model.addValidationTask('ValidaReqdeUniclickPLD',_.bind(this.validaReqUniclickPLD, this));

    	//this.model.on('change:relacion_c', this.checarValidaciones, this);
    	//this.model.on('change:relaciones_activas',this.checarValidaciones, this);
    	this.model.on('change:relaciones_activas',this.doRelationFields, this);
    	this.model.on('change:relaciones_activas',this.chkjuridico, this);
    	//this.model.on('change:relaciones_activas',this.validaPropietarioRealchange, this);
    	this.model.on('change:relaciones_activas',this.changejuridico, this);
    	//this.model.on('change:relaciones_activas',this.validaProveedorRecursoschange, this);
        //this.model.on('change:relaciones_activas',this.validaRelacionesChange, this);
        //this.model.on('change:relacion_c', this.validaRelacionesChange, this);

    	//Perform check of parent data once parent record finishes loading
    	/*this.model.once('data:sync:complete', this.doRecordCheck, this);*/
    	/** BEGIN CUSTOMIZATION: jgarcia@levementum.com 8/27/2015 Description: When a new relationship is created, the same person is pre-selected in the “Relacion” field.
    	* Modify the out of the box behavior to start with a blank “Relacion”*/
    	this.model.set("relacion_c", "   ");
    	/* END CUSTOMIZATION */

    	var valParams = {
    		'modulo': 'Accounts',
    	};
    	var valUrl = app.api.buildURL("customValidations", '', {}, {});
    	app.api.call("create", valUrl, {data: valParams}, { //Call and Collect the Dependencies
    		success: _.bind(function (data) {
    			if (data != null) {
    				self.validaciones = data;
    				console.log(self.validaciones);
    			}
    		}, this)
    	});
        this.previas = new String(this.model.get('relaciones_activas'));
	},

    saveAndClose: function (){
        if (accountContext==null) {
            accountContext=this.context;
        }
        /* Works  Ok
        this.initiateSave(_.bind(function () {
            if (this.closestComponent('drawer')) {
                app.events.trigger('create:model:changed', false);
                this.$el.off();
                accountContext.parent.attributes.model.attributes.account_telefonos= accountContext.parent.attributes.model._previousAttributes.account_telefonos;
                accountContext.parent.attributes.model.attributes.account_direcciones= accountContext.parent.attributes.model._previousAttributes.account_direcciones;
                app.drawer.close(accountContext);
            } else {
                app.navigate(this.context, this.model);
            }
        }, this));
        */
        //Test
        this.initiateSave(_.bind(function () {
            if (this.closestComponent('drawer')) {
                accountContext.parent.attributes.model.attributes.account_telefonos= accountContext.parent.attributes.model._previousAttributes.account_telefonos;
                accountContext.parent.attributes.model.attributes.account_direcciones= accountContext.parent.attributes.model._previousAttributes.account_direcciones;
                app.drawer.close(accountContext, this.model);
            } else {
                app.navigate(this.context, this.model);
            }
        }, this));
    },

	_render: function() {
		this._super("_render");
		this.doRelationFields();
        this.$('div[data-name=relacion_c]').hide();
        $('[data-name=tct_validado_juridico_chk_c]').hide();
        if(this.desdeVista360!=undefined){
            if(this.desdeVista360=='1'){
                this.$('div[data-name=relacion_c]').show();
            }
        }
        this.model.on("change:relaciones_activas", _.bind(function(){
            if(new String(this.model.get('relaciones_activas'))==""){
                this.$('div[data-name=relacion_c]').hide();
            }else{
                this.$('div[data-name=relacion_c]').show();
            }
        },this));
	},

	RelacionesPermitidas: function(fields, errors, callback){
		//Valida los tipos de relación permitidos para el regimen de la persona relacionada
    if (this.model.get("account_id1_c") == "" || this.model.get("account_id1_c") == undefined) {
      errors['relaciones_activas'] = "Seleccione la cuenta relacionada"; //errors['relaciones_activas'] || {};
      errors['relaciones_activas'].required = true;
      callback(null, fields, errors);
    }else{
  		var account = app.data.createBean('Accounts', {id:this.model.get('account_id1_c')});
          account.fetch({
              success: _.bind(function (model) {
              	console.log("El regimen fiscal de la persona relacionada es " + model.get('tipodepersona_c'));
                  if (model.get('tipodepersona_c') == 'Persona Moral') {
                  	var sRelaciones = new String(this.model.get('relaciones_activas'));
                  	var arrRelaciones = sRelaciones.split(",");
                  	console.log("Cadena sRelaciones:" + sRelaciones);
                  	console.log(arrRelaciones);
                  	for (rel in arrRelaciones){
                  		console.log("Item:" + arrRelaciones[rel]);
  						if (arrRelaciones[rel] == 'Contacto' || arrRelaciones[rel] == 'Conyuge' || arrRelaciones[rel] == 'Depositario'
  						|| arrRelaciones[rel] == 'Directivo' || arrRelaciones[rel] == 'Referencia Personal'){
  							app.alert.show("Tipo de relación no permitida", {
  								level: "error",
  								title: "Una persona moral no puede ser " + arrRelaciones[rel],
  								autoClose: false
  							});
  		        			errors['relacion_c'] = errors['relacion_c'] || {};
  							errors['relacion_c'].required = true;

  							errors['relaciones_activas'] = errors['relaciones_activas'] || {};
  							errors['relaciones_activas'].required = true;
  						}
  					}
                  }
                  callback(null, fields, errors);
              }, this)
          });
      }
	},

    valida_cuenta_no_contactar:function (fields, errors, callback) {
		if (!app.user.attributes.tct_no_contactar_chk_c && !app.user.attributes.bloqueo_credito_c && !app.user.attributes.bloqueo_cumple_c) {
			if (this.model.get('rel_relaciones_accountsaccounts_ida')!='' && this.model.get('rel_relaciones_accountsaccounts_ida') !=undefined) {
				var account = app.data.createBean('Accounts', {id:this.model.get('rel_relaciones_accountsaccounts_ida')});
				account.fetch({
					success: _.bind(function (model) {
						var url = app.api.buildURL('tct02_Resumen/' + this.model.get('rel_relaciones_accountsaccounts_ida'), null, null);
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
			} else {
				callback(null, fields, errors);
			}
        } else {
            callback(null, fields, errors);
        }
    },

	_doValidateContactFields: function (fields, errors, callback) {
    	var sRelaciones = new String(this.model.get('relaciones_activas'));
        if (sRelaciones.search("Contacto") >= 0) {
            if (_.isEmpty(this.model.get('tipodecontacto'))) {
                errors['tipodecontacto'] = errors['tipodecontacto'] || {};
                errors['tipodecontacto'].required = true;
            }
        }
        callback(null, fields, errors);
    },

	/*** ALI INICIO ***/
	doRelationFields: function()
	{
		var sRelaciones = new String(this.model.get('relaciones_activas'));
		var arrRelaciones = sRelaciones.split(",");
		this.$("div[data-name='montodeparticipacion']").hide();
		this.$("div[data-name='miembrodecomite']").hide();
		this.$("div[data-name='porcentaje_participacion_c']").hide();
		this.$("div[data-name='tipodecontacto']").hide();
		this.$("div[data-name='limitedecredito']").hide();
		this.$("div[data-name='plazoendias']").hide();
		this.$("div[data-name='parentesco']").hide();
		this.$("div[data-name='tiempodeconocerloano']").hide();
		this.$("div[data-name='tiempodeconocerlomeses']").hide();
		this.$("div[data-name='probabilidadincremento']").hide();
		this.$("div[data-name='facturacionmensual']").hide();
		this.$("div[data-name='opinion']").hide();
		this.$("div[data-name='consumomensual']").hide();
		this.$("div[data-name='linea']").hide();
		this.$("div[data-name='saldo']").hide();
		this.$("div[data-name='producto']").hide();
		this.$("div[data-name='puesto']").hide();
		this.$("div[data-name='puestodescription']").hide();
        this.$("div[data-name='estatus_tarjeta_c']").hide();


		for(var x = 0; x < arrRelaciones.length; x++){
			switch (arrRelaciones[x]){
				case "Accionista":
					this.$("div[data-name='montodeparticipacion']").show();
					this.$("div[data-name='miembrodecomite']").show();
					this.$("div[data-name='porcentaje_participacion_c']").show();
					break;
				case "Tarjetahabiente":
					this.$("div[data-name='estatus_tarjeta_c']").show();
					break;
                case "Contacto":
                    this.$("div[data-name='tipodecontacto']").show();
                    break;
				case "Referencia Personal":
					this.$("div[data-name='parentesco']").show();
					this.$("div[data-name='tiempodeconocerloano']").show();
					this.$("div[data-name='tiempodeconocerlomeses']").show();

					break;
				case "Referencia Proveedor":
					this.$("div[data-name='limitedecredito']").show();
					this.$("div[data-name='plazoendias']").show();
					this.$("div[data-name='opinion']").show();
					this.$("div[data-name='tiempodeconocerloano']").show();
					this.$("div[data-name='tiempodeconocerlomeses']").show();
					this.$("div[data-name='consumomensual']").show();
					this.$("div[data-name='linea']").show();
					this.$("div[data-name='saldo']").show();
					this.$("div[data-name='producto']").show();

					break;
				case "Referencia Cliente":
					this.$("div[data-name='limitedecredito']").show();
					this.$("div[data-name='plazoendias']").show();
					this.$("div[data-name='tiempodeconocerloano']").show();
					this.$("div[data-name='tiempodeconocerlomeses']").show();
					this.$("div[data-name='probabilidadincremento']").show();
					this.$("div[data-name='facturacionmensual']").show();
					this.$("div[data-name='opinion']").show();
					break;
				case "Representante":
					this.$("div[data-name='puesto']").show();
					this.$("div[data-name='puestodescription']").show();
					break;
			}
		}
	},

	/** BEGIN CUSTOMIZATION: jgarcia@levementum.com 8/31/2015 Description: During the creation of a “Relacion” if you select an existing “Persona”
	 * validate that all the mandatory fields for the type of relationships selected are captured*/
	checarValidaciones: function(){

		if(this.model.get('relaciones_activas') != "") {
			self.RequeridosFaltantes = [];
			app.api.call("read", app.api.buildURL("Accounts/", null, null, {
				"filter": [
					{
						"id": this.model.get("account_id1_c")
					}
				]
			}), null, {
				success: _.bind(function (data) {
					if(data.records.length > 0) {
						_.each(self.validaciones, function (val_values, val_parent_field) {
							_.each(val_values, function (rule, rule_name) {
								if (_.contains(relContext.model.get('relaciones_activas'), rule_name) || relContext.model.get('relaciones_activas') == rule_name) { //is it contained in an array?
									_.each(rule, function (rule_body, rule_index) {
										if (_.isNull(rule_body.estatus) || rule_body.estatus == 'Inactivo') {
											return;
										}
										//Check for visible, on this version this is the only dependency
                                        if (rule_body.requerido == '1') { //REQUIRED RULE
                                            var campo = rule_body.campo_dependiente;
                                            _.each(data.records[0], function (data_value, data_index) {
                                                if(campo == data_index) {
                                                    if(data_value == "" || data_value == null){


                                                        var pr=app.metadata.getField({module:'Accounts',name:data_index});
                                                        //pr.labelValue;
                                                        //console.log("Valores campos Ja  " + data_index + " su etiqueta  " + pr.labelValue);

                                                        self.RequeridosFaltantes.push(pr.labelValue);

                                                    }
                                                }
                                            });

                                           // console.log("Repetidos  "+ self.RequeridosFaltantes);
                                           // console.log("sin repetir  "+ self.RequeridosFaltantes.filter((item, i, ar) => ar.indexOf(item) == i););
                                            if (self.RequeridosFaltantes.length>0){
                                                self.RequeridosFaltantes=self.RequeridosFaltantes.filter((item, i, ar) => ar.indexOf(item) == i);;
                                            }

										console.log("lista "+self.RequeridosFaltantes);
                                        }
                                        //jescamilla Process SubValidaciones (AND)
                                        if (rule_index == 'SubValidaciones') {
                                            _.each(rule_body, function (subvalidacion, subvalidacion_index) {
                                                if(data.records[0][subvalidacion.campo_padre] == subvalidacion.criterio_validacion && subvalidacion.requerido != 1){ //if its not required, do not enforce it
                                                    var pr2=app.metadata.getField({module:'Accounts',name:subvalidacion.campo_dependiente});
                                                    if (pr2!==undefined){
                                                        subvalidacion.campo_dependiente=pr2.labelValue;
                                                    }
													//self.RequeridosFaltantes = _.without(self.RequeridosFaltantes, _.findWhere(self.RequeridosFaltantes, subvalidacion.campo_dependiente));
                                                    //Salvador Lopez <salvador.lopez@tactos.com.mx>, replace findWhere by find
                                                    self.RequeridosFaltantes = _.without(self.RequeridosFaltantes, _.find(self.RequeridosFaltantes, function (x) { return x == subvalidacion.campo_dependiente }));
                                                }
                                            });
                                        }
									});
								}
							});
						});

						if(self.RequeridosFaltantes != "" && self.RequeridosFaltantes != null){
						    if (data.records[0].tipodepersona_c=="Persona Moral"){
                                relaciones=self.RequeridosFaltantes.toString();
                                relaciones = relaciones.replace(/Pais de nacimiento/g, "País de Constitución");
                                relaciones = relaciones.replace(/Estado de nacimiento/g, "Estado de Constitución");
                                var arrRelaciones = relaciones.split(",");
                                self.RequeridosFaltantes = arrRelaciones;
                            }
							relContext.RequeridosFaltantes = self.RequeridosFaltantes;
							app.drawer.open({
								layout:'custom-RequiredFields',
								context:{
									RequeridosFaltantes:self.RequeridosFaltantes,
									relatedAcct:relContext.model.get("account_id1_c"),
									relaciones_activas:relContext.model.get('relaciones_activas'),
									relatedAcctName:relContext.model.get('relacion_c'),
								}
							});
						}
					}

				}, this)
			});
		}
	},

    /*Configuracion*/
    checarValidacionesonSave: function(fields, errors, callback){
        /* Ejecuta de nuevo la función checharValidaciones para no mandar el mensaje de campos restantes
        * Victor Martinez Lopez 2018-08-14
        * */
        if(this.model.get('relaciones_activas') != "") {
            self.RequeridosFaltantes = [];
            app.api.call("read", app.api.buildURL("Accounts/", null, null, {
                "filter": [
                    {
                        "id": this.model.get("account_id1_c")
                    }
                ]
            }), null, {
                success: _.bind(function (data) {
                    /*Código original*/


                    /*Fin*/

                    if(data.records.length > 0) {
                        _.each(self.validaciones, function (val_values, val_parent_field) {
                            _.each(val_values, function (rule, rule_name) {
                                if (_.contains(relContext.model.get('relaciones_activas'), rule_name) || relContext.model.get('relaciones_activas') == rule_name) { //is it contained in an array?
                                    _.each(rule, function (rule_body, rule_index) {
                                        if (_.isNull(rule_body.estatus) || rule_body.estatus == 'Inactivo') {
                                            return;
                                        }
                                        //Check for visible, on this version this is the only dependency
                                        if (rule_body.requerido == '1') { //REQUIRED RULE
                                            var campo = rule_body.campo_dependiente;
                                            _.each(data.records[0], function (data_value, data_index) {
                                                if(campo == data_index) {
                                                    if(data_value == "" || data_value == null){
                                                        var pr=app.metadata.getField({module:'Accounts',name:data_index});

                                                        self.RequeridosFaltantes.push(pr.labelValue);
                                                    }
                                                }
                                            });
                                            self.RequeridosFaltantes=$.unique(self.RequeridosFaltantes);
                                        }
                                        //jescamilla Process SubValidaciones (AND)
                                        if (rule_index == 'SubValidaciones') {
                                            _.each(rule_body, function (subvalidacion, subvalidacion_index) {
                                                if(data.records[0][subvalidacion.campo_padre] == subvalidacion.criterio_validacion && subvalidacion.requerido != 1){ //if its not required, do not enforce it
                                                    /*cambio*/
													var pr2=app.metadata.getField({module:'Accounts',name:subvalidacion.campo_dependiente});
                                                    if (pr2!==undefined){
                                                        subvalidacion.campo_dependiente=pr2.labelValue;
													}

													//self.RequeridosFaltantes = _.without(self.RequeridosFaltantes, _.findWhere(self.RequeridosFaltantes, subvalidacion.campo_dependiente));
                                                    //Salvador Lopez <salvador.lopez@tactos.com.mx>, replace findWhere by find
                                                    self.RequeridosFaltantes = _.without(self.RequeridosFaltantes, _.find(self.RequeridosFaltantes, function (x) { return x == subvalidacion.campo_dependiente }));
                                                }
                                            });
                                        }
                                    });
                                }
                            });
                        });

                        //Removiendo elementos 'undefined' del arreglo
                        if(self.RequeridosFaltantes.includes(undefined)){

                            for(var i=0;i<self.RequeridosFaltantes.length;i++){

                                var pos=self.RequeridosFaltantes.indexOf(undefined);
                                if(pos !=-1){
                                    self.RequeridosFaltantes.splice(pos,1);
                                }

                            }

                        }

                        if(self.RequeridosFaltantes != "" && self.RequeridosFaltantes != null){
                            relContext.RequeridosFaltantes = self.RequeridosFaltantes;
                            app.drawer.open({
                                layout:'custom-RequiredFields',
                                context:{
                                    RequeridosFaltantes:self.RequeridosFaltantes,
                                    relatedAcct:relContext.model.get("account_id1_c"),
                                    relaciones_activas:relContext.model.get('relaciones_activas'),
                                    relatedAcctName:relContext.model.get('relacion_c'),
                                }
                            });
                            console.log(relContext.RequeridosFaltantes);
                            errors['relacion_c'] = errors['relacion_c'] || {};
                            errors['relacion_c'].required = true;

                            errors['relaciones_activas'] = errors['relaciones_activas'] || {};
                            errors['relaciones_activas'].required = true;

                            app.alert.show("CamposRequeridosFaltantes", {
                                level: "error",
                                title: "La persona relacionada no cumple con los datos requeridos para las relaciones activas seleccionadas.",
                                autoClose: false
                            });
                        }



                    }
                    callback(null, fields, errors);

                }, this)
            });
        }
        else {
            callback(null, fields, errors);
        }
    },
	/* END CUSTOMIZATION */

	checarRelacion: function (fields, errors, callback){
		if(this.model.get("relacion_c") === "   "){
			errors['relacion_c'] = errors['relacion_c'] || {};
			errors['relacion_c'].required = true;
		}
		callback(null, fields, errors);
	},
	relacionesDuplicadas: function (fields, errors, callback){
		//console.log("relacionesDuplicadas");

    if (this.model.get("account_id1_c") == "" || this.model.get("account_id1_c") == undefined) {
      errors['relaciones_activas'] = "Seleccione la cuenta relacionada"; //errors['relaciones_activas'] || {};
      errors['relaciones_activas'].required = true;
    }else{
  		var verificaDuplicidadURL = app.api.buildURL("obtieneRelacionesDuplicadas",'',{},{});
  		app.api.call("create",verificaDuplicidadURL, {'guid_cliente':  this.model.get("rel_relaciones_accountsaccounts_ida"),
  			'guid_relacion':  this.model.get("account_id1_c"),
  			'relacion': String(this.model.get('relaciones_activas')),
  			'previas' : this.previas},{
  			success: _.bind(function(data){
                  console.log(data);
  				if(data!=null){
  					if(data.length>=1){
  						mensaje = data.length==1?'La relaci\u00F3n '+data.toString()+
                          ' ya existe, favor de verificar':'Las relaciones '+ data.join(", ") + ' ya existen, por favor verificar';

  						errors['relaciones_activas'] = mensaje; //errors['relaciones_activas'] || {};
  						errors['relaciones_activas'].required = true;

  						app.alert.show("RelacionesRepetidas", {
  							level: "error",
  							title: mensaje,
  							autoClose: false
  						});
  					}

  				}
  			},this)
  		});
    }
    callback(null, fields, errors);
	},

    Relacionaccionista: function (fields, errors, callback) {
    	if (this.model.get('relaciones_activas').includes('Accionista')) {
            if (parseFloat(this.model.get('porcentaje_participacion_c')) <= 0.0000){
                errors['porcentaje_participacion_c'] = errors['porcentaje_participacion_c'] || {};
                errors['porcentaje_participacion_c'].required = true;

                app.alert.show("Participacion_mayor_a_cero", {
                    level: "error",
                    messages: "El valor de <b>Porcentaje de Participaci\u00F3n</b> debe ser mayor a cero.",
                    autoClose: false
                });
            }
            // Valida valor mayor a 100
            if (parseFloat(this.model.get('porcentaje_participacion_c')) > 100.00) {

                errors['porcentaje_participacion_c'] = errors['porcentaje_participacion_c'] || {};
                errors['porcentaje_participacion_c'].required = true;

                app.alert.show("Participacion_menor_a_cero", {
                    level: "error",
                    messages: "El valor de <b>Porcentaje de Participaci\u00F3n</b> debe ser menor o igual a cien.",
                    autoClose: false
                });
            }
            if (this.model.get('porcentaje_participacion_c')=="" || this.model.get('porcentaje_participacion_c')== undefined){
                errors['porcentaje_participacion_c'] = errors['porcentaje_participacion_c'] || {};
                errors['porcentaje_participacion_c'].required = true;
            }
        }
        callback(null, fields, errors);
    },

    chkjuridico: function (){
        if (this.model.get('relaciones_activas').includes('Propietario Real')){
            $('[data-name=tct_validado_juridico_chk_c]').show();
        }else{
            $('[data-name=tct_validado_juridico_chk_c]').hide();
        }
        if (App.user.attributes.tct_propietario_real_chk_c== "1"){
            $('[data-fieldname=tct_validado_juridico_chk_c]').attr('style', 'pointer-events:block;');
        }else{
            $('[data-fieldname=tct_validado_juridico_chk_c]').attr('style', 'pointer-events:none;');
        }
    },

    changejuridico : function (){
        if (!this.model.get('relaciones_activas').includes('Propietario Real') && this.model.get("tct_validado_juridico_chk_c")== true){
            this.model.set("tct_validado_juridico_chk_c", 'false');
        }

    },

	validaPropietarioReal: function (fields, errors, callback){
        var RequeridosPR = "";
        var productospld = App.user.attributes.productos_c;
        if (this.model.get('relaciones_activas').includes('Propietario Real') && this.model.get("relacion_c").trim()!= "") {

            app.api.call("read", app.api.buildURL("Accounts/" + this.model.get("rel_relaciones_accountsaccounts_ida")), null, {
                success: _.bind(function (data) {
                    if (data.tipodepersona_c == "Persona Moral") {

                        app.api.call("read", app.api.buildURL("Accounts/" + this.model.get("account_id1_c")), null, {
                            success: _.bind(function (data) {
                                if (data.tipodepersona_c != "Persona Moral") {

                                    if (data.primernombre_c == "") {
                                        RequeridosPR = RequeridosPR + '<b>Nombre<br></b>';
                                    }
                                    if (data.apellidopaterno_c == "") {
                                        RequeridosPR = RequeridosPR + '<b>Apellido Paterno<br></b>';
                                    }
                                    // if (data.apellidomaterno_c == "") {
                                    //     RequeridosPR = RequeridosPR + '<b>Apellido Materno<br></b>';
                                    // }
                                    if (data.genero_c == "") {
                                        RequeridosPR = RequeridosPR + '<b>Género<br></b>';
                                    }
                                    if (data.fechadenacimiento_c == "") {
                                        RequeridosPR = RequeridosPR + '<b>Fecha de Nacimiento<br></b>';
                                    }
                                    if (data.paisdenacimiento == "") {
                                        RequeridosPR = RequeridosPR + '<b>País de Nacimiento<br></b>';
                                    }
                                    if (data.estado_nacimiento_c == "") {
                                        RequeridosPR = RequeridosPR + '<b>Estado de Nacimiento<br></b>';
                                    }
                                    if (data.nacionalidad_c == "") {
                                        RequeridosPR = RequeridosPR + '<b>Nacionalidad<br></b>';
                                    }
                                    // if (data.tct_macro_sector_ddw_c == "") {
                                    //     RequeridosPR = RequeridosPR + '<b>Macro Sector<br></b>';
                                    // }
                                    // if (data.sectoreconomico_c == "") {
                                    //     RequeridosPR = RequeridosPR + '<b>Sector Económico<br></b>';
                                    // }
                                    // if (data.subsectoreconomico_c == "") {
                                    //     RequeridosPR = RequeridosPR + '<b>Subsector Económico<br></b>';
                                    // }
                                    // if (data.actividadeconomica_c == "") {
                                    //     RequeridosPR = RequeridosPR + '<b>Actividad Económica<br></b>';
                                    // }
                                    if (data.phone_office == "") {
                                        RequeridosPR = RequeridosPR + '<b>Teléfono<br></b>';
                                    }
                                    app.api.call("read", app.api.buildURL("Accounts/" + this.model.get("account_id1_c") + "/link/accounts_dire_direccion_1"), null, {
                                        success: _.bind(function (data) {

                                            var flag_inactivo =0;
                                            for(var i=0;i<data.records.length;i++){
                                                if(data.records[i].inactivo==true){
                                                    flag_inactivo++;
                                                }
                                            }

                                            if (data.records.length <= flag_inactivo) {
                                                RequeridosPR = RequeridosPR + '<b>Dirección<br></b>';
                                            }
                                            app.api.call("read", app.api.buildURL("Accounts/" + this.model.get("account_id1_c") + "/link/accounts_tct_pld_1"), null, {
                                                success: _.bind(function (data) {
                                                    if (data.records.length > 0) {
                                                        for (var indice in data.records) {
                                                            console.log(data.records[indice].description);

                                                            if (data.records[indice].description == "AP" && productospld.includes("1")) {

                                                                if (data.records[indice].tct_pld_campo2_ddw == "") {
                                                                    RequeridosPR = RequeridosPR + '<b>Pregunta 1 Arrendamiento Puro<br></b>';
                                                                }
                                                                if (data.records[indice].tct_pld_campo4_ddw == "") {
                                                                    RequeridosPR = RequeridosPR + '<b>Pregunta 2 Arrendamiento Puro<br></b>';
                                                                }
                                                            }
                                                            if (data.records[indice].description == "CA" && productospld.includes("3")) {

                                                                if (data.records[indice].tct_pld_campo2_ddw == "") {
                                                                    RequeridosPR = RequeridosPR + '<b>Pregunta 1 Crédito Automotriz<br></b>';
                                                                }
                                                                if (data.records[indice].tct_pld_campo4_ddw == "") {
                                                                    RequeridosPR = RequeridosPR + '<b>Pregunta 2 Crédito Automotriz<br></b>';
                                                                }
                                                            }
                                                            if (data.records[indice].description == "FF" && productospld.includes("4")) {

                                                                if (data.records[indice].tct_pld_campo2_ddw == "") {
                                                                    RequeridosPR = RequeridosPR + '<b>Pregunta 1 Factoraje Financiero<br></b>';
                                                                }
                                                                if (data.records[indice].tct_pld_campo4_ddw == "") {
                                                                    RequeridosPR = RequeridosPR + '<b>Pregunta 2 Factoraje Financiero<br></b>';
                                                                }
                                                            }
                                                        }
                                                    } else {
                                                        RequeridosPR = RequeridosPR + '<b>Hace Falta completar información de la pestaña cuestionario PLD<br></b>';
                                                    }
                                                    if (RequeridosPR != "") {
                                                        app.alert.show("Campos faltantes en cuenta", {
                                                            level: "error",
                                                            messages: 'Hace falta completar la siguiente información en la cuenta ' + '<a href="#Accounts/' + this.model.get("account_id1_c") + '" target= "_blank"> ' + this.model.get('relacion_c') + '  </a>' + 'para una relación tipo Propietario Real:<br> ' + RequeridosPR,
                                                            autoClose: false
                                                        });
                                                        errors['errorpersonamoral'] = errors['errorpersonamoral'] || {};
                                                        errors['errorpersonamoral'].required = true;

                                                    }
                                                    callback(null, fields, errors);
                                                }, this)
                                            });

                                        }, this)
                                    });
                                } else {
                                    app.alert.show("Es persona Moral", {
                                        level: "error",
                                        title: "Una persona moral no puede ser Propietario Real",
                                        autoClose: false
                                    });
                                    errors['errorpersonamoral'] = errors['errorpersonamoral'] || {};
                                    errors['errorpersonamoral'].required = true;

                                    callback(null, fields, errors);
                                }
                            }, this)
                        });

                    } else {
                        app.alert.show("Error cuenta no es candidata a PR", {
                            level: "error",
                            messages: 'La cuenta principal es persona física y no puede generar una relación de tipo Propietario Real.',
                            autoClose: false
                        });
                        errors['errorcuenta'] = errors['errorcuenta'] || {};
                        errors['errorcuenta'].required = true;

                        callback(null, fields, errors);
                    }
                }, this)
            });
        }else {
            callback(null, fields, errors);
        }
    },

    validaPropietarioRealchange: function (){
        var Requeridoschange = "";
        var productospld = App.user.attributes.productos_c;

        if (this.model.get('relaciones_activas').includes('Propietario Real') && this.model.get("relacion_c").trim()!= "" ){

            app.api.call("read", app.api.buildURL("Accounts/" + this.model.get("account_id1_c")), null, {
                success: _.bind(function (data) {
                    if (data.tipodepersona_c!="Persona Moral") {

                        if (data.primernombre_c == "") {
                            Requeridoschange = Requeridoschange + '<b>Nombre<br></b>';
                        }
                        if (data.apellidopaterno_c == "") {
                            Requeridoschange = Requeridoschange + '<b>Apellido Paterno<br></b>';
                        }
                        // if (data.apellidomaterno_c == "") {
                        //     Requeridoschange = Requeridoschange + '<b>Apellido Materno<br></b>';
                        // }
                        if (data.genero_c == "") {
                            Requeridoschange = Requeridoschange + '<b>Género<br></b>';
                        }
                        if (data.fechadenacimiento_c == "") {
                            Requeridoschange = Requeridoschange + '<b>Fecha de Nacimiento<br></b>';
                        }
                        if (data.paisdenacimiento == "") {
                            Requeridoschange = Requeridoschange + '<b>País de Nacimiento<br></b>';
                        }
                        if (data.estado_nacimiento_c == "") {
                            Requeridoschange = Requeridoschange + '<b>Estado de Nacimiento<br></b>';
                        }
                        if (data.nacionalidad_c == "") {
                            Requeridoschange = Requeridoschange + '<b>Nacionalidad<br></b>';
                        }
                        // if (data.tct_macro_sector_ddw_c == "") {
                        //     Requeridoschange = Requeridoschange + '<b>Macro Sector<br></b>';
                        // }
                        // if (data.sectoreconomico_c == "") {
                        //     Requeridoschange = Requeridoschange + '<b>Sector Económico<br></b>';
                        // }
                        // if (data.sectoreconomico_c == "") {
                        //     Requeridoschange = Requeridoschange + '<b>Sector Económico<br></b>';
                        // }
                        // if (data.subsectoreconomico_c == "") {
                        //     Requeridoschange = Requeridoschange + '<b>Subsector Económico<br></b>';
                        // }
                        // if (data.actividadeconomica_c == "") {
                        //     Requeridoschange = Requeridoschange + '<b>Actividad Económica<br></b>';
                        // }
                        if (data.phone_office == "") {
                            Requeridoschange = Requeridoschange + '<b>Teléfono<br></b>';
                        }
                        app.api.call("read", app.api.buildURL("Accounts/" + this.model.get("account_id1_c") + "/link/accounts_dire_direccion_1"), null , {
                            success: _.bind(function (data) {
                                if (data.records <= 0) {
                                    Requeridoschange = Requeridoschange + '<b>Dirección<br></b>';
                                }
                                app.api.call("read", app.api.buildURL("Accounts/" + this.model.get("account_id1_c") + "/link/accounts_tct_pld_1"), null, {
                                    success: _.bind(function (data) {
                                        if (data.records.length>0) {
                                            for (var indice in data.records) {
                                                console.log(data.records[indice].description);

                                                if (data.records[indice].description == "AP" && productospld.includes("1")) {

                                                    if (data.records[indice].tct_pld_campo2_ddw == "") {
                                                        Requeridoschange = Requeridoschange + '<b>Pregunta 1 Arrendamiento Puro<br></b>';
                                                    }
                                                    if (data.records[indice].tct_pld_campo4_ddw == "") {
                                                        Requeridoschange = Requeridoschange + '<b>Pregunta 2 Arrendamiento Puro<br></b>';
                                                    }
                                                }
                                                if (data.records[indice].description == "CA" && productospld.includes("3")) {

                                                    if (data.records[indice].tct_pld_campo2_ddw == "") {
                                                        Requeridoschange = Requeridoschange + '<b>Pregunta 1 Crédito Automotriz<br></b>';
                                                    }
                                                    if (data.records[indice].tct_pld_campo4_ddw == "") {
                                                        Requeridoschange = Requeridoschange + '<b>Pregunta 2 Crédito Automotriz<br></b>';
                                                    }
                                                }
                                                if (data.records[indice].description == "FF" && productospld.includes("4")) {

                                                    if (data.records[indice].tct_pld_campo2_ddw == "") {
                                                        Requeridoschange = Requeridoschange + '<b>Pregunta 1 Factoraje Financiero<br></b>';
                                                    }
                                                    if (data.records[indice].tct_pld_campo4_ddw == "") {
                                                        Requeridoschange = Requeridoschange + '<b>Pregunta 2 Factoraje Financiero<br></b>';
                                                    }
                                                }
                                            }
                                        }else{
                                            Requeridoschange = Requeridoschange + '<b>Hace Falta completar información de la pestaña cuestionario PLD<br></b>';
                                        }
                                        if (Requeridoschange!= "") {
                                            app.alert.show("Campos faltantes en cuenta", {
                                                level: "error",
                                                messages: 'Hace falta completar la siguiente información en la cuenta '+'<a href="#Accounts/' + this.model.get("account_id1_c") + '" target= "_blank"> ' + this.model.get('relacion_c')+'  </a>' + 'para una relación tipo Propietario Real:<br> ' + Requeridoschange ,
                                                autoClose: false
                                            });
                                        }

                                    }, this)
                                });

                            }, this)
                        });
                    }else{
                        app.alert.show("Es persona Moral", {
                            level: "error",
                            title: "Una persona moral no puede ser Propietario Real",
                            autoClose: false
                        });

                    }
                }, this)
            });
        }
    },


    validaProveedorRecursos: function (fields, errors, callback){
        var RequeridosProvRec = "";
        if (this.model.get("account_id1_c") == "" || this.model.get("account_id1_c") == undefined) {
            errors['faltantescuenta'] = "Seleccione la cuenta relacionada"; //errors['relaciones_activas'] || {};
            errors['faltantescuenta'].required = true;
            callback(null, fields, errors);
        }else{
            if (this.model.get('relaciones_activas').includes('Proveedor de Recursos L') || this.model.get('relaciones_activas').includes('Proveedor de Recursos F')
            || this.model.get('relaciones_activas').includes('Proveedor de Recursos CA') || this.model.get('relaciones_activas').includes('Proveedor de Recursos CR') && this.model.get("relacion_c").trim()!= "") {

                app.api.call("read", app.api.buildURL("Accounts/" + this.model.get("rel_relaciones_accountsaccounts_ida")), null, {
                    success: _.bind(function (data) {
                        if (data.tipodepersona_c != "") {

                            app.api.call("read", app.api.buildURL("Accounts/" + this.model.get("account_id1_c")), null, {
                                success: _.bind(function (data) {
                                    if (data.tipodepersona_c == "Persona Fisica" || data.tipodepersona_c == "Persona Fisica con Actividad Empresarial") {

                                        if (data.primernombre_c == "") {
                                            RequeridosProvRec = RequeridosProvRec + '<b>-Nombre<br></b>';
                                        }
                                        if (data.apellidopaterno_c == "") {
                                            RequeridosProvRec = RequeridosProvRec + '<b>-Apellido Paterno<br></b>';
                                        }
                                        // if (data.apellidomaterno_c == "") {
                                        //     RequeridosProvRec = RequeridosProvRec + '<b>-Apellido Materno<br></b>';
                                        // }
                                        if (data.fechadenacimiento_c == "") {
                                            RequeridosProvRec = RequeridosProvRec + '<b>-Fecha de Nacimiento<br></b>';
                                        }
                                        if (data.nacionalidad_c == "0") {
                                            RequeridosProvRec = RequeridosProvRec + '<b>-Nacionalidad<br></b>';
                                        }
                                        if (data.rfc_c == "") {
                                            RequeridosProvRec = RequeridosProvRec + '<b>-RFC<br></b>';
                                        }
                                        if (data.tct_pais_expide_rfc_c == "") {
                                            RequeridosProvRec = RequeridosProvRec + '<b>-País que expide el RFC o equivalente<br></b>';
                                        }
                                        if (data.curp_c == "") {
                                            RequeridosProvRec = RequeridosProvRec + '<b>-CURP<br></b>';
                                        }
                                        // if (data.profesion_c == "") {
                                        //     RequeridosProvRec = RequeridosProvRec + '<b>-Profesión<br></b>';
                                        // }
                                        if (data.ctpldnoseriefiel_c == "" ) {
                                            RequeridosProvRec = RequeridosProvRec + '<b>-No. Serie FIEL<br></b>';
                                        }
                                        app.api.call("read", app.api.buildURL("Accounts/" + this.model.get("account_id1_c") + "/link/accounts_dire_direccion_1"), null, {
                                                success: _.bind(function (data) {
                                                    var flag_inactivo =0;
                                                    for(var i=0;i<data.records.length;i++){
                                                        if(data.records[i].inactivo==true){
                                                            flag_inactivo++;
                                                        }
                                                    }
                                                    if (data.records.length <= flag_inactivo) {
                                                        //for (var d = 0; d < data.records.length; d++) {
                                                            //if (!data.records[d].tipodedireccion.includes("1") && !data.records[d].tipodedireccion.includes("3") && !data.records[d].tipodedireccion.includes("5") && !data.records[d].tipodedireccion.includes("7")) {
                                                                RequeridosProvRec = RequeridosProvRec + '<b>-Dirección<br></b>';
                                                            //}
                                                        //}
                                                    }
                                                    if (RequeridosProvRec != "") {
                                                        app.alert.show("Campos faltantes en cuenta", {
                                                            level: "error",
                                                            messages: 'Hace falta completar la siguiente información en la cuenta ' + '<a href="#Accounts/' + this.model.get("account_id1_c") + '" target= "_blank"> ' + this.model.get('relacion_c') + '  </a>' + 'para una relación tipo Proveedor de Recursos:<br> ' + RequeridosProvRec,
                                                            autoClose: false
                                                        });
                                                        errors['faltantescuenta'] = errors['faltantescuenta'] || {};
                                                        errors['faltantescuenta'].required = true;
                                                    }
                                                    callback(null, fields, errors);
                                                }, this)
                                        });
                                    }else {
                                        if (data.razonsocial_c == "") {
                                            RequeridosProvRec = RequeridosProvRec + '<b>-Denominación o Razón Social<br></b>';
                                        }
                                        if (data.nombre_comercial_c == "") {
                                            RequeridosProvRec = RequeridosProvRec + '<b>-Nombre Comercial<br></b>';
                                        }
                                        if (data.fechaconstitutiva_c == "") {
                                            RequeridosProvRec = RequeridosProvRec + '<b>-Fecha Constitutiva<br></b>';
                                        }
                                        if (data.nacionalidad_c == "0") {
                                            RequeridosProvRec = RequeridosProvRec + '<b>-Nacionalidad<br></b>';
                                        }
                                        if (data.rfc_c == "") {
                                            RequeridosProvRec = RequeridosProvRec + '<b>-RFC<br></b>';
                                        }
                                        if (data.tct_pais_expide_rfc_c == "") {
                                            RequeridosProvRec = RequeridosProvRec + '<b>-País que expide el RFC o equivalente<br></b>';
                                        }
                                        if (data.ctpldnoseriefiel_c == "" ){
                                            RequeridosProvRec = RequeridosProvRec + '<b>-No. Serie FIEL<br></b>';
                                        }
                                        // if (data.actividadeconomica_c == "" ){
                                        //     RequeridosProvRec = RequeridosProvRec + '<b>-Actividad Económica<br></b>';
                                        // }
                                        app.api.call("read", app.api.buildURL("Accounts/" + this.model.get("account_id1_c") + "/link/accounts_dire_direccion_1"), null, {
                                                success: _.bind(function (data) {
                                                    var flag_inactivo =0;
                                                    for(var i=0;i<data.records.length;i++){
                                                        if(data.records[i].inactivo==true){
                                                            flag_inactivo++;
                                                        }
                                                    }
                                                    if (data.records.length <= flag_inactivo) {
                                                        RequeridosProvRec = RequeridosProvRec + '<b>-Domicilio<br></b>';
                                                    }
                                                    if (RequeridosProvRec != "") {
                                                        app.alert.show("Campos faltantes en cuenta", {
                                                            level: "error",
                                                            messages: 'Hace falta completar la siguiente información en la cuenta ' + '<a href="#Accounts/' + this.model.get("account_id1_c") + '" target= "_blank"> ' + this.model.get('relacion_c') + '  </a>' + 'para una relación tipo Proveedor de Recursos:<br> ' + RequeridosProvRec,
                                                            autoClose: false
                                                        });
                                                        errors['errorpersonamoral'] = errors['errorpersonamoral'] || {};
                                                        errors['errorpersonamoral'].required = true;
                                                    }
                                                    callback(null, fields, errors);
                                                }, this)
                                        });
                                    }
                                }, this)
                            });
                        }
                    }, this)
                });
            } else {
                callback(null, fields, errors);
            }
        }
    },

    validaProveedorRecursoschange: function (){
        var RequeridosProvRec = "";
        if ((this.model.get('relaciones_activas').includes('Proveedor de Recursos L') || this.model.get('relaciones_activas').includes('Proveedor de Recursos F') ||
            this.model.get('relaciones_activas').includes('Proveedor de Recursos CA')) || this.model.get('relaciones_activas').includes('Proveedor de Recursos CR') && this.model.get("relacion_c").trim()!= "") {

            app.api.call("read", app.api.buildURL("Accounts/" + this.model.get("rel_relaciones_accountsaccounts_ida")), null, {
                success: _.bind(function (data) {
                    if (data.tipodepersona_c != "") {

                        app.api.call("read", app.api.buildURL("Accounts/" + this.model.get("account_id1_c")), null, {
                            success: _.bind(function (data) {
                                if (data.tipodepersona_c == "Persona Fisica" || data.tipodepersona_c == "Persona Fisica con Actividad Empresarial") {

                                    if (data.primernombre_c == "") {
                                        RequeridosProvRec = RequeridosProvRec + '<b>-Nombre<br></b>';
                                    }
                                    if (data.apellidopaterno_c == "") {
                                        RequeridosProvRec = RequeridosProvRec + '<b>-Apellido Paterno<br></b>';
                                    }
                                    // if (data.apellidomaterno_c == "") {
                                    //     RequeridosProvRec = RequeridosProvRec + '<b>-Apellido Materno<br></b>';
                                    // }
                                    if (data.fechadenacimiento_c == "") {
                                        RequeridosProvRec = RequeridosProvRec + '<b>-Fecha de Nacimiento<br></b>';
                                    }
                                    if (data.nacionalidad_c == "0") {
                                        RequeridosProvRec = RequeridosProvRec + '<b>-Nacionalidad<br></b>';
                                    }
                                    if (data.rfc_c == "") {
                                        RequeridosProvRec = RequeridosProvRec + '<b>-RFC<br></b>';
                                    }
                                    if (data.tct_pais_expide_rfc_c == "") {
                                        RequeridosProvRec = RequeridosProvRec + '<b>-País que expide el RFC o equivalente<br></b>';
                                    }
                                    if (data.curp_c == "") {
                                        RequeridosProvRec = RequeridosProvRec + '<b>-CURP<br></b>';
                                    }
                                    // if (data.profesion_c == "") {
                                    //     RequeridosProvRec = RequeridosProvRec + '<b>-Profesión<br></b>';
                                    // }
                                    if (data.ctpldnoseriefiel_c == "" ) {
                                        RequeridosProvRec = RequeridosProvRec + '<b>-No. Serie FIEL<br></b>';
                                    }
                                    app.api.call("read", app.api.buildURL("Accounts/" + this.model.get("account_id1_c") + "/link/accounts_dire_direccion_1"), null, {
                                            success: _.bind(function (data) {
                                                if (data.records.length > 0) {
                                                    //for (var d = 0; d < data.records.length; d++) {
                                                        //if (!data.records[d].tipodedireccion.includes("1") && !data.records[d].tipodedireccion.includes("3") && !data.records[d].tipodedireccion.includes("5") && !data.records[d].tipodedireccion.includes("7")) {
                                                            RequeridosProvRec = RequeridosProvRec + '<b>-Dirección<br></b>';
                                                        //}
                                                    //}
                                                }
                                                if (RequeridosProvRec != "") {
                                                    app.alert.show("Campos faltantes en cuenta", {
                                                        level: "error",
                                                        messages: 'Hace falta completar la siguiente información en la cuenta ' + '<a href="#Accounts/' + this.model.get("account_id1_c") + '" target= "_blank"> ' + this.model.get('relacion_c') + '  </a>' + 'para una relación tipo <b>Proveedor de Recursos</b>:<br> ' + RequeridosProvRec,
                                                        autoClose: false
                                                    });
                                                }
                                                return;
                                            }, this)
                                        });
                                }else {
                                    if (data.razonsocial_c == "") {
                                        RequeridosProvRec = RequeridosProvRec + '<b>-Denominación o Razón Social<br></b>';
                                    }
                                    if (data.nacionalidad_c == "0") {
                                        RequeridosProvRec = RequeridosProvRec + '<b>-Nacionalidad<br></b>';
                                    }
                                    if (data.rfc_c == "") {
                                        RequeridosProvRec = RequeridosProvRec + '<b>-RFC<br></b>';
                                    }
                                    if (data.tct_pais_expide_rfc_c == "") {
                                        RequeridosProvRec = RequeridosProvRec + '<b>-País que expide el RFC o equivalente<br></b>';
                                    }
                                    if (data.ctpldnoseriefiel_c == "" ){
                                        RequeridosProvRec = RequeridosProvRec + '<b>-No. Serie FIEL<br></b>';
                                    }
                                    // if (data.actividadeconomica_c == "" ){
                                    //     RequeridosProvRec = RequeridosProvRec + '<b>-Actividad Económica<br></b>';
                                    // }
                                    app.api.call("read", app.api.buildURL("Accounts/" + this.model.get("account_id1_c") + "/link/accounts_dire_direccion_1"), null, {
                                            success: _.bind(function (data) {
                                                if (data.records <= 0) {
                                                    RequeridosProvRec = RequeridosProvRec + '<b>-Domicilio<br></b>';
                                                }
                                                if (RequeridosProvRec != "") {
                                                    app.alert.show("Campos faltantes en cuenta", {
                                                        level: "error",
                                                        messages: 'Hace falta completar la siguiente información en la cuenta ' + '<a href="#Accounts/' + this.model.get("account_id1_c") + '" target= "_blank"> ' + this.model.get('relacion_c') + '  </a>' + 'para una relación tipo <b>Proveedor de Recursos</b>:<br> ' + RequeridosProvRec,
                                                        autoClose: false
                                                    });
                                                }
                                                return;
                                            }, this)
                                    });
                                }
                            }, this)
                        });
                    }
                }, this)
            });
        }
    },

    //Funcion para validar campos requeridos de relacion tipo Aval, Accionista y Representante
    validaRelacionesChange: function (){
        var requests=[];
        var request={};
        var Cuenta= this.model.get('account_id1_c');
        request.url="";
        request.method="GET";

        if ((this.model.get('relaciones_activas').includes('Aval') || this.model.get('relaciones_activas').includes('Accionista') || this.model.get('relaciones_activas').includes('Representante')) && this.model.get("relacion_c").trim()!= "" && Cuenta != "") {
            var requestA = app.utils.deepCopy(request);
            var url = app.api.buildURL("Accounts/" + Cuenta);
            requestA.url = url.substring(4);
            requests.push(requestA);
            var requestB = app.utils.deepCopy(request);
            var url = app.api.buildURL("Accounts/" + Cuenta + "/link/accounts_dire_direccion_1");
            requestB.url = url.substring(4);
            requests.push(requestB);
            var requestC = app.utils.deepCopy(request);
            var url = app.api.buildURL("Accounts/" + Cuenta + "/link/accounts_tel_telefonos_1")
            requestC.url = url.substring(4);
            requests.push(requestC);
            var faltantes=[];
            var relacionesActivas=[];
            var self = this;

            app.api.call("create", app.api.buildURL("bulk", '', {}, {}), {requests: requests}, {
                success: _.bind(function (data) {
                    //Variables para controlar las direcciones y telefonos
                    var direP=0;
                    var direF=0;
                    var telCyC=0;
                    var telO=0;

                    //Itera direcciones
                    for (var d = 0; d < data[1].contents.records.length; d++) {
                        //Itera direccion Particular
                        if (App.lang.getAppListStrings('tipo_dir_map_list')[data[1].contents.records[d].tipodedireccion[0]].includes('1') && data[1].contents.records[d].inactivo == false) {
                            direP++;
                        }
                        //Valida direccion Fiscal
                        if(App.lang.getAppListStrings('dir_indicador_map_list')[data[1].contents.records[d].indicador] != undefined){
	                        //Valida direccion Fiscal
	                        if (App.lang.getAppListStrings('dir_indicador_map_list')[data[1].contents.records[d].indicador].includes('2') && data[1].contents.records[d].inactivo == false) {
	                            direF++;
	                        }
						}
                    }
                    //Itera telefonos
                    for (var t = 0; t < data[2].contents.records.length; t++) {
                        //Itera telefono casa y celular
                        if (data[2].contents.records[t].tipotelefono.includes('1') || data[2].contents.records[t].tipotelefono.includes('3')) {
                            telCyC++;
                        }
                        //Itera para telefono de trabajo y celular trabajo
                        if (data[2].contents.records[t].tipotelefono.includes('2') || data[2].contents.records[t].tipotelefono.includes('4')) {
                            telO++;
                        }
                    }
                    if (data) {
                        if (this.model.get('relaciones_activas').includes('Aval')){
                            //Valida Relación: Aval
                            relacionesActivas.push("Aval");
                            if (data[0].contents.tipodepersona_c != "Persona Moral") {
                                if (data[0].contents.primernombre_c == "") {
                                    faltantes.push('Nombre');
                                }
                                if (data[0].contents.apellidopaterno_c == "") {
                                    faltantes.push('Apellido Paterno');
                                }
                                // if (data[0].contents.apellidomaterno_c == "") {
                                //     faltantes.push('Apellido Materno');
                                // }
                                if (data[0].contents.fechadenacimiento_c == "") {
                                    faltantes.push('Fecha de Nacimiento');
                                }
                                if (data[0].contents.pais_nacimiento_c == "") {
                                    faltantes.push('País de Nacimiento');
                                }
                                if (data[0].contents.estado_nacimiento_c == "") {
                                    faltantes.push('Estado de Nacimiento');
                                }
                                if (data[0].contents.rfc_c == "") {
                                    faltantes.push('RFC');
                                }
                                // if (data[0].contents.profesion_c == "") {
                                //     faltantes.push('Profesión');
                                // }
                                if (data[0].contents.curp_c == "") {
                                    faltantes.push('CURP');
                                }
                                //Validación PFAE
                                if (data[0].contents.tipodepersona_c != "Persona Fisica") {
                                //   if (data[0].contents.sectoreconomico_c == "") {
                                //       faltantes.push('Sector Económico');
                                //   }
                                //   if (data[0].contents.subsectoreconomico_c == "") {
                                //       faltantes.push('Sub Sector Económico');
                                //   }
                                  // if (data[0].contents.actividadeconomica_c == "") {
                                  //     faltantes.push('Actividad Económica');
                                  // }
                                }
                            } else {
                                if (data[0].contents.razonsocial_c == "") {
                                    faltantes.push('Razón Social');
                                }
                                if (data[0].contents.rfc_c == "") {
                                    faltantes.push('RFC');
                                }
                                if (data[0].contents.fechaconstitutiva_c == "") {
                                    faltantes.push('Fecha Constitutiva');
                                }
                                // if (data[0].contents.sectoreconomico_c == "") {
                                //     faltantes.push('Sector Económico');
                                // }
                                // if (data[0].contents.actividadeconomica_c == "") {
                                //     faltantes.push('Actividad Económica');
                                // }
                                // if (data[0].contents.subsectoreconomico_c == "") {
                                //     faltantes.push('Sub Sector Económico');
                                // }
                                // if (data[0].contents.subsectoreconomico_c == "") {
                                //     faltantes.push('Sub Sector Económico');
                                // }
                                if (data[0].contents.pais_nacimiento_c == "") {
                                    faltantes.push('País de Constitución');
                                }
                                if (data[0].contents.estado_nacimiento_c == "") {
                                    faltantes.push('Estado de Constitución');
                                }
                            }
                            //Pregunta por la direccion
                            if (direP == 0) {
                                faltantes.push('Dirección Particular');
                            }
                            //Pregunta por el telefono
                            /*if (telCyC== 0) {
                                faltantes.push('Teléfono Casa o Celular');
                            }*/
                        }
                        //valida relación: Accionista
                        if (this.model.get('relaciones_activas').includes('Accionista')) {
                            relacionesActivas.push("Accionista");
                            if (data[0].contents.tipodepersona_c != "Persona Moral") {
                                if (data[0].contents.primernombre_c == "") {
                                    faltantes.push('Nombre');
                                }
                                if (data[0].contents.apellidopaterno_c == "") {
                                    faltantes.push('Apellido Paterno');
                                }
                                if (data[0].contents.fechadenacimiento_c == "") {
                                    faltantes.push('Fecha de Nacimiento');
                                }
                                if (data[0].contents.pais_nacimiento_c == "") {
                                    faltantes.push('País de Nacimiento');
                                }
                                if (data[0].contents.estado_nacimiento_c == "") {
                                    faltantes.push('Estado de Nacimiento');
                                }
                                if (data[0].contents.rfc_c == "") {
                                    faltantes.push('RFC');
                                }
                                if (data[0].contents.curp_c == "") {
                                    faltantes.push('CURP');
                                }
                            }else{
                                if (data[0].contents.razonsocial_c == "") {
                                    faltantes.push('Razón Social');
                                }
                                if (data[0].contents.rfc_c == "") {
                                    faltantes.push('RFC');
                                }
                                if (data[0].contents.fechaconstitutiva_c == "") {
                                    faltantes.push('Fecha Constitutiva');
                                }
                                if (data[0].contents.pais_nacimiento_c == "") {
                                    faltantes.push('País de Constitución');
                                }
                                if (data[0].contents.estado_nacimiento_c == "") {
                                    faltantes.push('Estado de Constitución');
                                }
                            }
                            //Pregunta por el telefono
                            /*if (telO== 0) {
                                faltantes.push('Teléfono de Trabajo o Celular Trabajo');
                            }*/
                            //Pregunta por la direccion fiscal
                            if (direF == 0) {
                                faltantes.push('Dirección Fiscal');
                            }
                            if (self.model.get('porcentaje_participacion_c')=="" || self.model.get('porcentaje_participacion_c')==undefined){
                                faltantes.push('Porcentaje de Participación');
                            }

                        }
                        if (this.model.get('relaciones_activas').includes('Representante')) {
                            //Valida relación: Representate
                            relacionesActivas.push("Representante");
                            if (data[0].contents.tipodepersona_c != "Persona Moral") {
                                if (data[0].contents.primernombre_c == "") {
                                    faltantes.push('Nombre');
                                }
                                if (data[0].contents.apellidopaterno_c == "") {
                                    faltantes.push('Apellido Paterno');
                                }
                                if (data[0].contents.fechadenacimiento_c == "") {
                                    faltantes.push('Fecha de Nacimiento');
                                }
                                if (data[0].contents.pais_nacimiento_c == "") {
                                    faltantes.push('País de Nacimiento');
                                }
                                if (data[0].contents.estado_nacimiento_c == "") {
                                    faltantes.push('Estado de Nacimiento');
                                }
                                if (data[0].contents.rfc_c == "") {
                                    faltantes.push('RFC');
                                }
                                // if (data[0].contents.profesion_c == "") {
                                //     faltantes.push('Profesión');
                                // }
                                if (data[0].contents.curp_c == "") {
                                    faltantes.push('CURP');
                                }
                            }else{
                                if (data[0].contents.razonsocial_c == "") {
                                    faltantes.push('Razón Social');
                                }
                                if (data[0].contents.rfc_c == "") {
                                    faltantes.push('RFC');
                                }
                                if (data[0].contents.pais_nacimiento_c == "") {
                                    faltantes.push('País de Constitución');
                                }
                                if (data[0].contents.estado_nacimiento_c == "") {
                                    faltantes.push('Estado de Constitución');
                                }
                            }
                            //Pregunta por el telefono
                            /*if (telO== 0) {
                                faltantes.push('Teléfono de Trabajo o Celular Trabajo');
                            }*/
							//Pregunta por la direccion fiscal
							if (direF == 0) {
								faltantes.push('Dirección Fiscal');
							}
                        }
                    }

                    if (faltantes.length >  0) {
                        faltantes=faltantes.filter((item, i, ar) => ar.indexOf(item) == i);
                        var lista="";
                        faltantes.forEach(element => lista=lista+'<br><b> '+element + '</b>');
                        app.alert.show("Campos_faltantes_en_cuenta", {
                            level: "error",
                            messages: 'Hace falta completar la siguiente información en la cuenta ' + '<a href="#Accounts/' + this.model.get("account_id1_c") + '" target= "_blank"> ' + this.model.get('relacion_c') + '  </a>' + 'para una relación  tipo '+ relacionesActivas+':' + lista,
                            autoClose: false
                        });

                    }

                }, this)
            });
        }
    },

    validaRelacionesValidation: function (fields, errors, callback){
        var requests=[];
        var request={};
        var Cuenta= this.model.get('account_id1_c');
        request.url="";
        request.method="GET";

        if ((this.model.get('relaciones_activas').includes('Garante') || this.model.get('relaciones_activas').includes('Tarjetahabiente') ||  this.model.get('relaciones_activas').includes('Conyuge') || this.model.get('relaciones_activas').includes('Fiador') || this.model.get('relaciones_activas').includes('Contacto') || this.model.get('relaciones_activas').includes('Coacreditado') ||  this.model.get('relaciones_activas').includes('Directivo') || this.model.get('relaciones_activas').includes('Referencia') || this.model.get('relaciones_activas').includes('Obligado solidario') || this.model.get('relaciones_activas').includes('Firmantes VR')) && this.model.get("relacion_c").trim()!= "" && Cuenta != "") {
            var requestA = app.utils.deepCopy(request);
            var url = app.api.buildURL("Accounts/" + Cuenta);
            requestA.url = url.substring(4);
            requests.push(requestA);
            var requestB = app.utils.deepCopy(request);
            var url = app.api.buildURL("Accounts/" + Cuenta + "/link/accounts_dire_direccion_1");
            requestB.url = url.substring(4);
            requests.push(requestB);
            var requestC = app.utils.deepCopy(request);
            var url = app.api.buildURL("Accounts/" + Cuenta + "/link/accounts_tel_telefonos_1");
            requestC.url = url.substring(4);
            requests.push(requestC);
			var requestD = app.utils.deepCopy(request);
            var url = app.api.buildURL("Accounts/" + Cuenta + "/link/accounts_tct_pld_1");
            requestD.url = url.substring(4);
            requests.push(requestD);
            var requestE = app.utils.deepCopy(request);
            var url = app.api.buildURL("Accounts/" + Cuenta + "/link/accounts_uni_productos_1");
            requestE.url = url.substring(4);
            requests.push(requestE);
            var faltantes=[];
            var relacionesActivas=[];
            var self = this;

            app.api.call("create", app.api.buildURL("bulk", '', {}, {}), {requests: requests}, {
                success: _.bind(function (data) {
                    //Variables para controlar las direcciones y telefonos
                    var direP=0;
                    var direF=0;
                    var telCyC=0;
                    var telO=0;

                    //Itera direcciones
                    for (var d = 0; d < data[1].contents.records.length; d++) {
                        //Itera direccion Particular
                        if (App.lang.getAppListStrings('tipo_dir_map_list')[data[1].contents.records[d].tipodedireccion[0]].includes('1') && data[1].contents.records[d].inactivo == false) {
                            direP++;
                        }
                        //Valida direccion Fiscal                       
                        if(App.lang.getAppListStrings('dir_indicador_map_list')[data[1].contents.records[d].indicador] != undefined){
	                        //Valida direccion Fiscal
	                        if (App.lang.getAppListStrings('dir_indicador_map_list')[data[1].contents.records[d].indicador].includes('2') && data[1].contents.records[d].inactivo == false) {
	                            direF++;
	                        }
						}
                    }
                    //Itera telefonos
                    for (var t = 0; t < data[2].contents.records.length; t++) {
                        //Itera telefono casa y celular
                        if (data[2].contents.records[t].tipotelefono.includes('1') || data[2].contents.records[t].tipotelefono.includes('3')) {
                            telCyC++;
                        }
                        //Itera para telefono de trabajo y celular trabajo
                        if (data[2].contents.records[t].tipotelefono.includes('2') || data[2].contents.records[t].tipotelefono.includes('4')) {
                            telO++;
                        }
                    }
                    if (data) {
                        //Valida Relación: Aval
                        if (this.model.get('relaciones_activas').includes('Aval')){
                            relacionesActivas.push("Aval");
                            if (data[0].contents.tipodepersona_c != "Persona Moral") {
                                if (data[0].contents.primernombre_c == "") {
                                    faltantes.push('Nombre');
                                }
                                if (data[0].contents.apellidopaterno_c == "") {
                                    faltantes.push('Apellido Paterno');
                                }
                                // if (data[0].contents.apellidomaterno_c == "") {
                                //     faltantes.push('Apellido Materno');
                                // }
                                if (data[0].contents.estadocivil_c == "") {
                                    faltantes.push('Estado Civil');
                                }
                                if (data[0].contents.fechadenacimiento_c == "") {
                                    faltantes.push('Fecha de Nacimiento');
                                }
                                if (data[0].contents.pais_nacimiento_c == "") {
                                    faltantes.push('País de Nacimiento');
                                }
                                if (data[0].contents.estado_nacimiento_c == "") {
                                    faltantes.push('Estado de Nacimiento');
                                }
                                if (data[0].contents.rfc_c == "") {
                                    faltantes.push('RFC');
                                }
                                // if (data[0].contents.profesion_c == "") {
                                //     faltantes.push('Profesión');
                                // }
                                if (data[0].contents.curp_c == "") {
                                    faltantes.push('CURP');
                                }
                                //Validación PFAE
                                if (data[0].contents.tipodepersona_c != "Persona Fisica") {
                                //   if (data[0].contents.sectoreconomico_c == "") {
                                //       faltantes.push('Sector Económico');
                                //   }
                                //   if (data[0].contents.subsectoreconomico_c == "") {
                                //       faltantes.push('Sub Sector Económico');
                                //   }
                                  // if (data[0].contents.actividadeconomica_c == "") {
                                  //     faltantes.push('Actividad Económica');
                                  // }
                                }
                            } else {
                                if (data[0].contents.razonsocial_c == "") {
                                    faltantes.push('Razón Social');
                                }
                                if (data[0].contents.nombre_comercial_c == "") {
                                    faltantes.push('Nombre Comercial');
                                }
                                if (data[0].contents.rfc_c == "") {
                                    faltantes.push('RFC');
                                }
                                if (data[0].contents.fechaconstitutiva_c == "") {
                                    faltantes.push('Fecha Constitutiva');
                                }
                                // if (data[0].contents.sectoreconomico_c == "") {
                                //     faltantes.push('Sector Económico');
                                // }
                                // if (data[0].contents.actividadeconomica_c == "") {
                                //     faltantes.push('Actividad Económica');
                                // }
                                // if (data[0].contents.subsectoreconomico_c == "") {
                                //     faltantes.push('Sub Sector Económico');
                                // }
                                if (data[0].contents.pais_nacimiento_c == "") {
                                    faltantes.push('País de Constitución');
                                }
                                if (data[0].contents.estado_nacimiento_c == "") {
                                    faltantes.push('Estado de Constitución');
                                }
                            }
                            //Pregunta por la direccion
                            if (direP == 0) {
                                faltantes.push('Dirección Particular');
                            }
                            //Pregunta por el telefono
                            /*if (telCyC== 0) {
                                faltantes.push('Teléfono Casa o Celular');
                            }*/
                        }
                        //valida relación: Conyuge
                        if (this.model.get('relaciones_activas').includes('Conyuge')) {
                            relacionesActivas.push("Conyuge");
                            if (data[0].contents.tipodepersona_c != "Persona Moral") {
                                if (data[0].contents.primernombre_c == "") {
                                    faltantes.push('Nombre');
                                }
                                if (data[0].contents.apellidopaterno_c == "") {
                                    faltantes.push('Apellido Paterno');
                                }
                                if (data[0].contents.regimenpatrimonial_c == "") {
                                    faltantes.push('Régimen Patrimonial');
                                }
                                if (data[0].contents.estadocivil_c == "") {
                                    faltantes.push('Estado Civil');
                                }
                                //Validación PFAE
                                if (data[0].contents.tipodepersona_c != "Persona Fisica") {
                                  if (data[0].contents.nombre_comercial_c == "") {
                                      faltantes.push('Nombre Comercial');
                                  }
                                }
                            }
                        }
                        //valida relación: Contacto
                        if (this.model.get('relaciones_activas').includes('Contacto')) {
                            relacionesActivas.push("Contacto");
                            if (data[0].contents.tipodepersona_c != "Persona Moral") {
                                if (data[0].contents.primernombre_c == "") {
                                    faltantes.push('Nombre');
                                }
                                if (data[0].contents.apellidopaterno_c == "") {
                                    faltantes.push('Apellido Paterno');
                                }
                                if (data[0].contents.estadocivil_c == "") {
                                    faltantes.push('Estado Civil');
                                }
                                if (data[0].contents.tipodecontacto == "") {
                                    faltantes.push('Tipo de Contacto');
                                }
                                //Validación PFAE
                                if (data[0].contents.tipodepersona_c != "Persona Fisica") {
                                  if (data[0].contents.nombre_comercial_c == "") {
                                      faltantes.push('Nombre Comercial');
                                  }
                                }
								if(this.model.get("tipodecontacto") === "Seguros") {
									if (data[0].contents.genero_c == "") faltantes.push('Género');
									if (data[0].contents.email1 == "") faltantes.push('Correo electrónico');
									if (data[2].contents.records.length == 0) faltantes.push('Teléfono');
								}
                            }
                        }
                        //Valida Relación: Fiador
                        if (this.model.get('relaciones_activas').includes('Fiador')){
                            relacionesActivas.push("Fiador");
                            if (data[0].contents.tipodepersona_c != "Persona Moral") {
                                if (data[0].contents.primernombre_c == "") {
                                    faltantes.push('Nombre');
                                }
                                if (data[0].contents.apellidopaterno_c == "") {
                                    faltantes.push('Apellido Paterno');
                                }
                                if (data[0].contents.estadocivil_c == "") {
                                    faltantes.push('Estado Civil');
                                }
                                if (data[0].contents.fechadenacimiento_c == "") {
                                    faltantes.push('Fecha de Nacimiento');
                                }
                                if (data[0].contents.pais_nacimiento_c == "") {
                                    faltantes.push('País de Nacimiento');
                                }
                                if (data[0].contents.estado_nacimiento_c == "") {
                                    faltantes.push('Estado de Nacimiento');
                                }
                                if (data[0].contents.rfc_c == "") {
                                    faltantes.push('RFC');
                                }
                                // if (data[0].contents.profesion_c == "") {
                                //     faltantes.push('Profesión');
                                // }
                                //Validación PFAE
                                if (data[0].contents.tipodepersona_c != "Persona Fisica") {
                                  if (data[0].contents.nombre_comercial_c == "") {
                                      faltantes.push('Nombre Comercial');
                                  }
                                //   if (data[0].contents.subsectoreconomico_c == "") {
                                //       faltantes.push('Sub Sector Económico');
                                //   }
                                  // if (data[0].contents.actividadeconomica_c == "") {
                                  //     faltantes.push('Actividad Económica');
                                  // }
                                }
                            } else {
                                if (data[0].contents.razonsocial_c == "") {
                                    faltantes.push('Razón Social');
                                }
                                if (data[0].contents.nombre_comercial_c == "") {
                                    faltantes.push('Nombre Comercial');
                                }
                                if (data[0].contents.rfc_c == "") {
                                    faltantes.push('RFC');
                                }
                                if (data[0].contents.fechaconstitutiva_c == "") {
                                    faltantes.push('Fecha Constitutiva');
                                }
                                // if (data[0].contents.actividadeconomica_c == "") {
                                //     faltantes.push('Actividad Económica');
                                // }
                                // if (data[0].contents.subsectoreconomico_c == "") {
                                //     faltantes.push('Sub Sector Económico');
                                // }
                                if (data[0].contents.pais_nacimiento_c == "") {
                                    faltantes.push('País de Constitución');
                                }
                                if (data[0].contents.estado_nacimiento_c == "") {
                                    faltantes.push('Estado de Constitución');
                                }
                            }
                        }
                        //valida relación: Accionista
                        if (this.model.get('relaciones_activas').includes('Accionista')) {
                            relacionesActivas.push("Accionista");
                            if (data[0].contents.tipodepersona_c != "Persona Moral") {
                                if (data[0].contents.primernombre_c == "") {
                                    faltantes.push('Nombre');
                                }
                                if (data[0].contents.apellidopaterno_c == "") {
                                    faltantes.push('Apellido Paterno');
                                }
                                if (data[0].contents.estadocivil_c == "") {
                                    faltantes.push('Estado Civil');
                                }
                                if (data[0].contents.fechadenacimiento_c == "") {
                                    faltantes.push('Fecha de Nacimiento');
                                }
                                if (data[0].contents.pais_nacimiento_c == "") {
                                    faltantes.push('País de Nacimiento');
                                }
                                if (data[0].contents.estado_nacimiento_c == "") {
                                    faltantes.push('Estado de Nacimiento');
                                }
                                if (data[0].contents.rfc_c == "") {
                                    faltantes.push('RFC');
                                }
                                if (data[0].contents.curp_c == "") {
                                    faltantes.push('CURP');
                                }
                                //Validación PFAE
                                if (data[0].contents.tipodepersona_c != "Persona Fisica") {
                                  if (data[0].contents.nombre_comercial_c == "") {
                                      faltantes.push('Nombre Comercial');
                                  }
                                }
                            }else{
                                if (data[0].contents.razonsocial_c == "") {
                                    faltantes.push('Razón Social');
                                }
                                if (data[0].contents.nombre_comercial_c == "") {
                                    faltantes.push('Nombre Comercial');
                                }
                                if (data[0].contents.rfc_c == "") {
                                    faltantes.push('RFC');
                                }
                                if (data[0].contents.fechaconstitutiva_c == "") {
                                    faltantes.push('Fecha Constitutiva');
                                }
                                if (data[0].contents.pais_nacimiento_c == "") {
                                    faltantes.push('País de Constitución');
                                }
                                if (data[0].contents.estado_nacimiento_c == "") {
                                    faltantes.push('Estado de Constitución');
                                }
                            }
                            //Pregunta por el telefono
                            /*if (telO== 0) {
                                faltantes.push('Teléfono de Trabajo o Celular Trabajo');
                            }*/
                            //Pregunta por la direccion fiscal
                            if (direF == 0) {
                                faltantes.push('Dirección Fiscal');
                            }
                            if (self.model.get('porcentaje_participacion_c')=="" || self.model.get('porcentaje_participacion_c')==undefined){
                                faltantes.push('Porcentaje de Participación');
                            }

                        }
                        //Valida relación: Representate
                        if (this.model.get('relaciones_activas').includes('Representante')) {
                            relacionesActivas.push("Representante");
                            if (data[0].contents.tipodepersona_c != "Persona Moral") {
                                if (data[0].contents.primernombre_c == "") {
                                    faltantes.push('Nombre');
                                }
                                if (data[0].contents.apellidopaterno_c == "") {
                                    faltantes.push('Apellido Paterno');
                                }
                                if (data[0].contents.estadocivil_c == "") {
                                    faltantes.push('Estado Civil');
                                }
                                if (data[0].contents.fechadenacimiento_c == "") {
                                    faltantes.push('Fecha de Nacimiento');
                                }
                                if (data[0].contents.pais_nacimiento_c == "") {
                                    faltantes.push('País de Nacimiento');
                                }
                                if (data[0].contents.estado_nacimiento_c == "") {
                                    faltantes.push('Estado de Nacimiento');
                                }
                                if (data[0].contents.rfc_c == "") {
                                    faltantes.push('RFC');
                                }
                                // if (data[0].contents.profesion_c == "") {
                                //     faltantes.push('Profesión');
                                // }
                                if (data[0].contents.curp_c == "") {
                                    faltantes.push('CURP');
                                }
                                //Validación PFAE
                                if (data[0].contents.tipodepersona_c != "Persona Fisica") {
                                  if (data[0].contents.nombre_comercial_c == "") {
                                      faltantes.push('Nombre Comercial');
                                  }
                                }
                            }else{
                                if (data[0].contents.razonsocial_c == "") {
                                    faltantes.push('Razón Social');
                                }
                                if (data[0].contents.nombre_comercial_c == "") {
                                    faltantes.push('Nombre Comercial');
                                }
                                if (data[0].contents.rfc_c == "") {
                                    faltantes.push('RFC');
                                }
                                if (data[0].contents.pais_nacimiento_c == "") {
                                    faltantes.push('País de Constitución');
                                }
                                if (data[0].contents.estado_nacimiento_c == "") {
                                    faltantes.push('Estado de Constitución');
                                }
                            }
                            //Pregunta por el telefono
                            /*if (telO== 0) {
                                faltantes.push('Teléfono de Trabajo o Celular Trabajo');
                            }*/
                            //Pregunta por la direccion fiscal
                            if (direF == 0) {
                                faltantes.push('Dirección Fiscal');
                            }
                        }
                        //Valida Relación: Coacreditado, Depositario, 'Obligado solidario y Firmantes VR
                        if (this.model.get('relaciones_activas').includes('Coacreditado') || this.model.get('relaciones_activas').includes('Depositario') || this.model.get('relaciones_activas').includes('Obligado solidario') || this.model.get('relaciones_activas').includes('Firmantes VR')){
                            if (this.model.get('relaciones_activas').includes('Coacreditado')) relacionesActivas.push("Coacreditado");
                            if (this.model.get('relaciones_activas').includes('Depositario')) relacionesActivas.push("Depositario");
                            if (this.model.get('relaciones_activas').includes('Obligado solidario')) relacionesActivas.push("Obligado solidario");
                            if (this.model.get('relaciones_activas').includes('Firmantes VR')) relacionesActivas.push("Firmantes VR");
                            if (data[0].contents.tipodepersona_c != "Persona Moral") {
                                if (data[0].contents.primernombre_c == "") {
                                    faltantes.push('Nombre');
                                }
                                if (data[0].contents.apellidopaterno_c == "") {
                                    faltantes.push('Apellido Paterno');
                                }
                                if (data[0].contents.estadocivil_c == "") {
                                    faltantes.push('Estado Civil');
                                }
                                if (data[0].contents.fechadenacimiento_c == "") {
                                    faltantes.push('Fecha de Nacimiento');
                                }
                                if (data[0].contents.pais_nacimiento_c == "") {
                                    faltantes.push('País de Nacimiento');
                                }
                                if (data[0].contents.estado_nacimiento_c == "") {
                                    faltantes.push('Estado de Nacimiento');
                                }
                                if (data[0].contents.rfc_c == "") {
                                    faltantes.push('RFC');
                                }
                                // if (data[0].contents.profesion_c == "") {
                                //     faltantes.push('Profesión');
                                // }
                                //Validación PFAE
                                if (data[0].contents.tipodepersona_c != "Persona Fisica") {
                                  if (data[0].contents.nombre_comercial_c == "") {
                                      faltantes.push('Nombre Comercial');
                                  }
                                //   if (data[0].contents.sectoreconomico_c == "") {
                                //       faltantes.push('Sector Económico');
                                //   }
                                //   if (data[0].contents.subsectoreconomico_c == "") {
                                //       faltantes.push('Sub Sector Económico');
                                //   }
                                  // if (data[0].contents.actividadeconomica_c == "") {
                                  //     faltantes.push('Actividad Económica');
                                  // }
                                }
                            } else {
                                if (data[0].contents.razonsocial_c == "") {
                                    faltantes.push('Razón Social');
                                }
                                if (data[0].contents.nombre_comercial_c == "") {
                                    faltantes.push('Nombre Comercial');
                                }
                                if (data[0].contents.rfc_c == "") {
                                    faltantes.push('RFC');
                                }
                                if (data[0].contents.fechaconstitutiva_c == "") {
                                    faltantes.push('Fecha Constitutiva');
                                }
                                // if (data[0].contents.actividadeconomica_c == "") {
                                //     faltantes.push('Actividad Económica');
                                // }
                                // if (data[0].contents.sectoreconomico_c == "") {
                                //     faltantes.push('Sector Económico');
                                // }
                                // if (data[0].contents.subsectoreconomico_c == "") {
                                //     faltantes.push('Sub Sector Económico');
                                // }
                                if (data[0].contents.pais_nacimiento_c == "") {
                                    faltantes.push('País de Constitución');
                                }
                                if (data[0].contents.estado_nacimiento_c == "") {
                                    faltantes.push('Estado de Constitución');
                                }
                            }
                        }
                        //valida relación: Directivo, Referencia
                        if (this.model.get('relaciones_activas').includes('Directivo') || this.model.get('relaciones_activas').includes('Referencia')) {
                            if (this.model.get('relaciones_activas').includes('Directivo')) relacionesActivas.push("Directivo");
                            if (this.model.get('relaciones_activas').includes('Referencia')) relacionesActivas.push("Referencia");
                            if (data[0].contents.tipodepersona_c != "Persona Moral") {
                                if (data[0].contents.primernombre_c == "") {
                                    faltantes.push('Nombre');
                                }
                                if (data[0].contents.apellidopaterno_c == "") {
                                    faltantes.push('Apellido Paterno');
                                }
                                if (data[0].contents.estadocivil_c == "") {
                                    faltantes.push('Estado Civil');
                                }
                                //Validación PFAE
                                if (data[0].contents.tipodepersona_c != "Persona Fisica") {
                                  if (data[0].contents.nombre_comercial_c == "") {
                                      faltantes.push('Nombre Comercial');
                                  }
                                }
                            }
                            else {
                              if (this.model.get('relaciones_activas').includes('Referencia')) {
                                if (data[0].contents.razonsocial_c == "") {
                                    faltantes.push('Razón Social');
                                }
                                if (data[0].contents.nombre_comercial_c == "") {
                                    faltantes.push('Nombre Comercial');
                                }
                              }
                            }
                        }
						//Valida Relación: Garante
						var terceros = 0;
						var recursos = 0;
                        if (this.model.get('relaciones_activas').includes('Garante')){
							relacionesActivas.push("Garante");
							//Itera PLD PRoductos
							var productuser =App.user.attributes.tipodeproducto_c;
							for (var t = 0; t < data[3].contents.records.length; t++) {
								if( (data[3].contents.records[t].description == 'CS' && productuser == '5') ||
									(data[3].contents.records[t].description == 'CA' && productuser == '3') ||
									(data[3].contents.records[t].description == 'FF' && productuser == '4') ||
									(data[3].contents.records[t].description == 'AP' && productuser == '1')
								){
									if (data[3].contents.records[t].tct_pld_campo2_ddw != '' ) {
										terceros++;
									}
									if (data[3].contents.records[t].tct_pld_campo4_ddw != '' ) {
										recursos++;
									}
								}
							}
							if (data[0].contents.tipodepersona_c != "Persona Moral") {
								if (data[0].contents.primernombre_c == "") {
									faltantes.push('Nombre');
								}
								if (data[0].contents.apellidopaterno_c == "") {
									faltantes.push('Apellido Paterno');
								}
								// if (data[0].contents.apellidomaterno_c == "") {
								// 	faltantes.push('Apellido Materno');
								// }
								if (data[0].contents.fechadenacimiento_c == "") {
									faltantes.push('Fecha de Nacimiento');
								}
								if (data[0].contents.pais_nacimiento_c == "") {
									faltantes.push('País de Nacimiento');
								}
								if (data[0].contents.estado_nacimiento_c == "") {
									faltantes.push('Estado de Nacimiento');
								}
								if (data[0].contents.genero_c == "") {
									faltantes.push('Género');
								}
								if (data[0].contents.curp_c == "") {
									faltantes.push('CURP');
								}
                                //Pregunta por la direccion
								if (direF == 0) {
									faltantes.push('Dirección Particular');
								}
								//Validación PF y PFAE
								if (data[0].contents.tipodepersona_c == "Persona Fisica") {
									//Pregunta por el telefono Casa
									if (telCyC == 0) {
										faltantes.push('Teléfono Casa o Celular');
									}
									// if (data[0].contents.profesion_c == "") {
									// 	faltantes.push('Profesión');
									// }
								}else{
									//Pregunta por el telefono Trabajo
									if (telO == 0) {
										faltantes.push('Teléfono Casa o Trabajo');
									}
								}
								if (terceros == 0) {
									faltantes.push('PLD-¿Usted actúa a nombre y por cuenta propia o a nombre y por cuenta de un tercero?');
								}
								if(data[0].contents.ctpldfuncionespublicas_c == "1"){
									if(data[0].contents.ctpldfuncionespublicascargo_c == "" ||data[0].contents.tct_dependencia_pf_c == ""
									|| data[0].contents.tct_fecha_ini_pf_c == "" || data[0].contents.tct_fecha_fin_pf_c == ""){
										faltantes.push("PLD Pep's Personal: ");
									}
									if (data[0].contents.ctpldfuncionespublicascargo_c == "") {
										faltantes.push(" -Cargo público que tiene o tuvo");
									}
									if (data[0].contents.tct_dependencia_pf_c == "") {
										faltantes.push(" -Dependencia donde ejerce o ejerció el cargo");
									}
									if (data[0].contents.tct_fecha_ini_pf_c == "") {
										faltantes.push(" -Fecha de inicio del cargo");
									}
									if (data[0].contents.tct_fecha_fin_pf_c == "") {
										faltantes.push(" -Fecha de término del cargo");
									}
								}
								if(data[0].contents.ctpldconyuge_c == "1"){
									if (data[0].contents.ctpldconyugecargo_c == "" ||data[0].contents.tct_nombre_pf_peps_c == ""
									||data[0].contents.tct_cargo2_pf_c == "" ||data[0].contents.tct_dependencia2_pf_c == ""
									|| data[0].contents.tct_fecha_ini2_pf_c == "" || data[0].contents.tct_fecha_fin2_pf_c == ""){
										faltantes.push("PLD Pep's Familiar: ");
									}
									if (data[0].contents.ctpldconyugecargo_c == "") {
										faltantes.push(" - Especificar parentesco o relación");
									}
									if (data[0].contents.tct_nombre_pf_peps_c == "") {
										faltantes.push(" - Nombre de la persona que ocupa el puesto");
									}
									if (data[0].contents.tct_cargo2_pf_c == "") {
										faltantes.push(" - Cargo público que tiene o tuvo");
									}
									if (data[0].contents.tct_dependencia2_pf_c == "") {
										faltantes.push(" - Dependencia donde ejerce o ejerció el cargo");
									}
									if (data[0].contents.tct_fecha_ini2_pf_c == "") {
										faltantes.push(" - Fecha de inicio del cargo");
									}
									if (data[0].contents.tct_fecha_fin2_pf_c == "") {
										faltantes.push(" - Fecha de término del cargo");
									}
								}
							} else {
								if (data[0].contents.razonsocial_c == "") {
									faltantes.push('Razón Social');
								}
								if (data[0].contents.fechaconstitutiva_c == "") {
									faltantes.push('Fecha Constitutiva');
								}
								//Pregunta por el telefono Trabajo
								if (telO== 0) {
									faltantes.push('Teléfono Casa o Trabajo');
								}
								//Pregunta por la direccion
								if (direF == 0) {
									faltantes.push('Dirección Fiscal');
								}
								if(data[0].contents.ctpldaccionistas_c == "1"){
									if (data[0].contents.tct_socio_pm_c == "" || data[0].contents.ctpldaccionistascargo_c == ""
									|| data[0].contents.tct_dependencia_pm_c == "" || data[0].contents.tct_fecha_ini_pm_c == ""
									|| data[0].contents.tct_fecha_fin_pm_c == "") {
										faltantes.push("PLD Pep's Personal: ");
									}
									if (data[0].contents.tct_socio_pm_c == "") {
										faltantes.push(" - Nombre del Socio o Accionista");
									}
									if (data[0].contents.ctpldaccionistascargo_c == "") {
										faltantes.push(" - Cargo público que tiene o tuvo");
									}
									if (data[0].contents.tct_dependencia_pm_c == "") {
										faltantes.push(" - Dependencia donde ejerce o ejerció el cargo");
									}
									if (data[0].contents.tct_fecha_ini_pm_c == "") {
										faltantes.push(" - Fecha de inicio del cargo");
									}
									if (data[0].contents.tct_fecha_fin_pm_c == "") {
										faltantes.push(" - Fecha de término del cargo");
									}
								}
								if(data[0].contents.ctpldaccionistasconyuge_c == "1"){
									if (data[0].contents.tct_socio2_pm_c == "" || data[0].contents.tct_nombre_pm_c == ""
									|| data[0].contents.ctpldaccionistasconyugecargo_c == "" || data[0].contents.tct_cargo_pm_c == ""
									|| data[0].contents.tct_dependencia2_pm_c == "" || data[0].contents.tct_fecha_ini2_pm_c == ""
									|| data[0].contents.tct_fecha_fin2_pm_c == "") {
										faltantes.push("PLD Pep's Familiar: ");
									}
									if (data[0].contents.ctpldaccionistasconyugecargo_c == "") {
										faltantes.push(" -Especificar parentesco o relación");
									}
									if (data[0].contents.tct_nombre_pm_c == "") {
										faltantes.push(" -Nombre de la persona que ocupa el puesto");
									}
									if (data[0].contents.tct_cargo_pm_c == "") {
										faltantes.push(" -Cargo público que tiene o tuvo");
									}
									if (data[0].contents.tct_dependencia2_pm_c == "") {
										faltantes.push(" -Dependencia donde ejerce o ejerció el cargo");
									}
									if (data[0].contents.tct_fecha_ini2_pm_c == "") {
										faltantes.push(" -Fecha de inicio del cargo");
									}
									if (data[0].contents.tct_fecha_fin2_pm_c == "") {
										faltantes.push(" -Fecha de término del cargo");
									}
								}

							}
							if (data[0].contents.email.length == 0) {
								faltantes.push('Correo electrónico');
							}
							// if (data[0].contents.actividadeconomica_c == "") {
							// 	faltantes.push('Actividad Económica');
							// }
							if (data[0].contents.nacionalidad_c == "" || data[0].contents.nacionalidad_c == "0") {
								faltantes.push('Nacionalidad');
							}
							if (data[0].contents.rfc_c == "") {
								faltantes.push('RFC con homoclave');
							}
							if (data[0].contents.tct_pais_expide_rfc_c == "") {
								faltantes.push('País que expide el RFC ');
							}
							if (data[0].contents.ctpldnoseriefiel_c == "") {
								faltantes.push('PLD-No serie FIEL');
							}
							if ( recursos == 0) {
								faltantes.push('PLD-¿Los recursos son propios o los recursos son de un tercero?');
							}
                        }

                        //Valida Relación: Tarjetahabiente
                        if (this.model.get('relaciones_activas').includes('Tarjetahabiente')){
                            relacionesActivas.push("Tarjetahabiente");

							if (data[0].contents.tipodepersona_c != "Persona Moral") {
                                if (data[0].contents.primernombre_c == "") {
									faltantes.push('Nombre');
								}
								if (data[0].contents.apellidopaterno_c == "") {
									faltantes.push('Apellido Paterno');
								}
								// if (data[0].contents.apellidomaterno_c == "") {
								// 	faltantes.push('Apellido Materno');
								// }
                                if (data[0].contents.fechadenacimiento_c == "") {
									faltantes.push('Fecha de Nacimiento');
								}
								if (data[0].contents.nacionalidad_c == "" || data[0].contents.nacionalidad_c == "0") {
                                    faltantes.push('Nacionalidad');
                                }

                                if(data[0].contents.tipodepersona_c=="Persona Fisica con Actividad Empresarial"){
                                    if (data[0].contents.rfc_c == "") {
                                        faltantes.push('RFC con homoclave');
                                    }
                                }

                                if (data[0].contents.genero_c == ""){
                                    faltantes.push('Género');
                                }

                                if (data[0].contents.pais_nacimiento_c == ""){
                                    faltantes.push('País de nacimiento');
                                }

                                if (data[0].contents.estado_nacimiento_c == "1" || data[0].contents.estado_nacimiento_c == ""){
                                    faltantes.push('Entidad federativa de nacimiento');
                                }

                                // if (data[0].contents.actividadeconomica_c == "0" || data[0].contents.actividadeconomica_c == ""){
                                //     faltantes.push('Actividad o giro del negocio al que se dedique el cliente');
                                // }

                                if(data[0].contents.tipodepersona_c=="Persona Fisica con Actividad Empresarial"){
                                    if (data[0].contents.tct_pais_expide_rfc_c == ""){
                                        faltantes.push('País que asignó RFC');
                                    }
                                }

                                if (data[0].contents.ctpldnoseriefiel_c == ""){
                                    faltantes.push('Número de serie de la firma electrónica avanzada');
                                }

                                //validando sección de PLD
                                if (data[3].contents.records.length==0){
                                    faltantes.push('¿Usted actúa a nombre y por cuenta propia o a nombre y por cuenta de un tercero?');
                                }
                                if(data[3].contents.records.length>0){
                                    for (let index = 0; index < data[3].contents.records.length; index++) {
                                        if(data[3].contents.records[index].name=='Crédito Revolvente'){
                                            if(data[3].contents.records[index].tct_pld_campo2_ddw ==""){
                                                faltantes.push('¿Usted actúa a nombre y por cuenta propia o a nombre y por cuenta de un tercero?');
                                            }
                                        }
                                    }

                                }

                                //Validando que el usuario firmado se encuentra asignado a algún producto de la cuenta, en caso de que SI esté asignado
                                //se valida dirección, correo y teléfono
                                var id_user_log=app.user.get('id');
                                var arr_flag=[];

                                if(data[0].contents.user_id_c==id_user_log){ //Validando asignación en producto leasing
                                   arr_flag.push(1);
                                }
                                if(data[0].contents.user_id1_c==id_user_log){
                                   arr_flag.push(1);
                                }
                                if(data[0].contents.user_id2_c==id_user_log){
                                    arr_flag.push(1);
                                }
                                if(data[0].contents.user_id6_c==id_user_log){
                                    arr_flag.push(1);
                                }
                                if(data[0].contents.user_id7_c==id_user_log){
                                    arr_flag.push(1);
                                }
                                if(data[0].contents.user_id8_c==id_user_log){
                                    arr_flag.push(1);
                                }

                                //Una vez lleno el arreglo auxiliar que guarda banderas (arr_flag), se valida que exista el valor 1, ya que éste indica que el usuario si está asignado a un producto
                                //Por lo tanto, se validan direcciones, teléfonos y correo
                                var flag_mostrar_msj=0;
                                if(arr_flag.includes(1)){
                                    if (direP == 0) { //No tiene dirección particular
                                        faltantes.push('Dirección Particular');
                                        flag_mostrar_msj++;
                                    }
                                    if(telCyC==0 && telO==0){
                                        faltantes.push('Teléfono de contacto');
                                        flag_mostrar_msj++;
                                    }
                                    /*
                                    if(data[0].contents.email1==""){
                                        faltantes.push('Correo electrónico');
                                        flag_mostrar_msj++;
                                    }
                                    */

                                }else{//No está asignado a ningún producto de la cuenta, pero se valida la información de direcciones, teléfonos y direcciones
                                    //para solicitar la asignación del registro para poder editar direcciones, teléfonos y direcciones
                                    if (direP == 0) { //No tiene dirección particular
                                        flag_mostrar_msj++;
                                    }
                                    if(telCyC==0 && telO==0){
                                        flag_mostrar_msj++;
                                    }
                                    if(data[0].contents.email1==""){
                                        flag_mostrar_msj++;
                                    }
                                    if(flag_mostrar_msj>0){
                                        app.alert.show("msj_asignacion_contacto", {
                                            level: "info",
                                            messages: 'Solicita la asignación de ' + '<a href="#Accounts/' + this.model.get("account_id1_c") + '" target= "_blank"> ' + this.model.get('relacion_c') + '  </a> para completar dirección, teléfono y correo electrónico',
                                            autoClose: false
                                        });

                                    }
                                }
							}else{
                                faltantes.push('Una persona moral no puede ser Tarjetahabiente');
                            }
                        }
                    }

                    if (faltantes.length >  0) {
                        if(faltantes.includes('Una persona moral no puede ser Tarjetahabiente')){
                            app.alert.show("Campos_faltantes_en_cuenta", {
                                level: "error",
                                messages: 'Una persona moral no puede ser Tarjetahabiente',
                                autoClose: false
                            });

                            errors['validacionRelacionesActivas'] = errors['validacionRelacionesActivas'] || {};
                            errors['validacionRelacionesActivas'].required = true;

                        }else{
                            faltantes=faltantes.filter((item, i, ar) => ar.indexOf(item) == i);
                            var lista="";
                            faltantes.forEach(element => lista=lista+'<br><b> '+element + '</b>');
                            app.alert.show("Campos_faltantes_en_cuenta", {
                                level: "error",
                                messages: 'Hace falta completar la siguiente información en la cuenta ' + '<a href="#Accounts/' + this.model.get("account_id1_c") + '" target= "_blank"> ' + this.model.get('relacion_c') + '  </a>' + 'para una relación  tipo '+ relacionesActivas+':' + lista,
                                autoClose: false
                            });
                            errors['validacionRelacionesActivas'] = errors['validacionRelacionesActivas'] || {};
                            errors['validacionRelacionesActivas'].required = true;

                        }

                    }
                    callback(null, fields, errors);
                }, this)
            });
        }else{
            callback(null, fields, errors);
        }
    },

    validaReqUniclickPLD: function (fields, errors, callback) {
        if(App.user.attributes.id == ResumenProductos.uniclick.assigned_user_id){
                        var necesarios="";
                        var requests=[];
                        var request={};
                        var Cuenta = this.model.get('account_id1_c');
                        var CuentaPadre=this.model.get('rel_relaciones_accountsaccounts_ida');
                        //Validamos que se tenga alguna de las siguientes relaciones activas
                    if ( this.model.get('relaciones_activas').includes('Aval') || this.model.get('relaciones_activas').includes('Representante') || this.model.get('relaciones_activas').includes('Propietario Real') ||
                    this.model.get('relaciones_activas').includes('Proveedor de Recursos CS') ||  this.model.get('relaciones_activas').includes('Accionista') || this.model.get('relaciones_activas').includes('Obligado solidario')&& Cuenta != "") {
                       //Obtenemos las opps de la cuenta
                       var requestA = app.utils.deepCopy(request);
                       var url = app.api.buildURL("Accounts/" + CuentaPadre + "/link/opportunities?filter[0][tipo_producto_c][$equals]=2&filter[1][negocio_c][$equals]=10&filter[2][negocio_c][$equals]=10&filter[3][estatus_c][$not_equals]=K&filter[4][tct_etapa_ddw_c][$not_equals]=N&filter[5][estatus_c][$not_equals]=R");
                           requestA.url = url.substring(4);
                           requests.push(requestA);
                           var requestB = app.utils.deepCopy(request);
                           var url = app.api.buildURL("Accounts/" + Cuenta + "/link/accounts_dire_direccion_1");
                           requestB.url = url.substring(4);
                           requests.push(requestB);
                           var requestC = app.utils.deepCopy(request);
                           var url = app.api.buildURL("Accounts/" + Cuenta + "/link/accounts_tel_telefonos_1");
                           requestC.url = url.substring(4);
                           requests.push(requestC);
                           var requestD = app.utils.deepCopy(request);
                           var url = app.api.buildURL("Accounts/" + Cuenta + "/link/accounts_tct_pld_1?filter[0][name][$equals]=Crédito Simple");
                           requestD.url = url.substring(4);
                           requests.push(requestD);
                           //Peticion para tener datos de la cuenta (debajo=4)
                           var requestE = app.utils.deepCopy(request);
                            var url = app.api.buildURL("Accounts/" + Cuenta);
                            requestE.url = url.substring(4);
                            requests.push(requestE);

                           app.api.call("create", app.api.buildURL("bulk", '', {}, {}), {requests: requests}, {
                               success: _.bind(function (data) {
                                   //Variables para controlar las direcciones y telefonos
                                   var direP=0;
                                   var telCyC=0;
                                   var telO=0;

                                   if (data[0].contents.records.length > 0){
                                    //Valida direcciones y teléfonos

                                            for (var d = 0; d < data[1].contents.records.length; d++) {
                                            //Itera direccion Particular
                                                if (App.lang.getAppListStrings('tipo_dir_map_list')[data[1].contents.records[d].tipodedireccion[0]].includes('1') && data[1].contents.records[d].inactivo == false) {
                                                    direP++;
                                                }
                                            }
                                            //Itera telefonos
                                            for (var t = 0; t < data[2].contents.records.length; t++) {
                                                //Itera telefono casa y celular
                                                if (data[2].contents.records[t].tipotelefono.includes('1') || data[2].contents.records[t].tipotelefono.includes('3')) {
                                                    telO++;
                                                }
                                                //Itera para telefono de trabajo y celular trabajo
                                                if (data[2].contents.records[t].tipotelefono.includes('2') || data[2].contents.records[t].tipotelefono.includes('4')) {
                                                    telO++;
                                                }
                                            }
                                        //Validamos requeridos de la cuenta
                                        if (data[4].contents.tipodepersona_c != 'Persona Moral'){
                                            if (this.model.get('relaciones_activas').includes('Aval') || this.model.get('relaciones_activas').includes('Representante') || this.model.get('relaciones_activas').includes('Propietario Real')||
                                            this.model.get('relaciones_activas').includes('Proveedor de Recursos CS') ||  this.model.get('relaciones_activas').includes('Accionista')) {
                                                if (data[4].contents.primernombre_c == "" || data[4].contents.primernombre_c == null) {
                                                    necesarios = necesarios + '<b>Nombre<br></b>';
                                                }
                                                if (data[4].contents.apellidopaterno_c == "" || data[4].contents.apellidopaterno_c == null) {
                                                    necesarios = necesarios + '<b>Apellido Paterno<br></b>';
                                                }
                                                // if (data[4].contents.apellidomaterno_c == "" || data[4].contents.apellidomaterno_c == null) {
                                                //     necesarios = necesarios + '<b>Apellido Materno<br></b>';
                                                // }
                                            }
                                            //Valida direcciones y feléfono PF
                                            if (this.model.get('relaciones_activas').includes('Aval') || this.model.get('relaciones_activas').includes('Propietario Real') || this.model.get('relaciones_activas').includes('Proveedor de Recursos CS')) {
                                                //Evaluamos campos faltantes en direccion
                                                    if(direP<=0){
                                                        necesarios = necesarios + '<b>Dirección Particular<br></b>';
                                                    }
                                                    //Evaluamos campos faltantes en direccion
                                                    if(telO<=0){
                                                        necesarios = necesarios + '<b>Teléfono<br></b>';
                                                    }
                                                }
                                            if ( this.model.get('relaciones_activas').includes('Aval') || this.model.get('relaciones_activas').includes('Propietario Real') || this.model.get('relaciones_activas').includes('Proveedor de Recursos CS')) {
                                                if (data[4].contents.genero_c == "" || data[4].contents.genero_c == null) {
                                                    necesarios = necesarios + '<b>G\u00E9nero</b><br>';
                                                }
                                                if (data[4].contents.fechadenacimiento_c == "" || data[4].contents.fechadenacimiento_c == null) {
                                                    necesarios = necesarios + '<b>Fecha de Nacimiento<br></b>';
                                                }
                                                if (data[4].contents.pais_nacimiento_c == "" || data[4].contents.pais_nacimiento_c == null ||data[4].contents.pais_nacimiento_c=='0') {
                                                        necesarios = necesarios + '<b>Pa\u00EDs de Nacimiento</b><br>';
                                                }
                                                if (data[4].contents.nacionalidad_c == "" || data[4].contents.nacionalidad_c == null || data[4].contents.nacionalidad_c=='0') {
                                                        necesarios = necesarios + '<b>Nacionalidad</b><br>';
                                                }
                                                // if (data[4].contents.profesion_c == "" || data[4].contents.profesion_c == null) {
                                                //         necesarios = necesarios + '<b>Profesión</b><br>';
                                                // }
                                                if (data[4].contents.rfc_c == "" || data[4].contents.rfc_c == null ) {
                                                        necesarios = necesarios + '<b>RFC</b><br>';
                                                }
                                                if (data[4].contents.tct_pais_expide_rfc_c == "" || data[4].contents.tct_pais_expide_rfc_c == null ) {
                                                    necesarios = necesarios + '<b>Pa\u00EDs que expide el RFC</b><br>';
                                                }
                                                if (data[4].contents.nacionalidad_c== "2" && data[4].contents.tipodepersona_c != 'Persona Moral') {

                                                    if (data[4].contents.ctpldnoseriefiel_c == "" || data[4].contents.ctpldnoseriefiel_c == null ) {
                                                        necesarios = necesarios + '<b>Número de serie de la Firma Electrónica Avanzada</b><br>';
                                                    }
                                                    if (data[4].contents.curp_c == "" || data[4].contents.curp_c == null) {
                                                        necesarios = necesarios + '<b>CURP</b><br>';
                                                    }
                                                    if (data[4].contents.estado_nacimiento_c == "" || data[4].contents.estado_nacimiento_c == null || data[4].contents.estado_nacimiento_c == "1") {
                                                        necesarios = necesarios + '<b>Estado de Nacimiento<br></b>';
                                                    }
                                                }
                                            }
                                            if(this.model.get('relaciones_activas').includes('Aval') || this.model.get('relaciones_activas').includes('Propietario Real')){
                                                     //Sección PEPS Fisica Personal
                                                    if (data[4].contents.ctpldfuncionespublicas_c == true) {
                                                        var banderaPEPSPersonal="";
                                                        if (data[4].contents.ctpldfuncionespublicascargo_c == "" || data[4].contents.ctpldfuncionespublicascargo_c == null) {
                                                            banderaPEPSPersonal = banderaPEPSPersonal + '<b>-Cargo público que tiene o tuvo<br></b>';
                                                        }
                                                        if (data[4].contents.tct_dependencia_pf_c == "" || data[4].contents.tct_dependencia_pf_c == null) {
                                                            banderaPEPSPersonal = banderaPEPSPersonal + '<b>-Dependencia donde ejerce o ejerció el cargo<br></b>';
                                                        }
                                                        if (data[4].contents.tct_periodo_pf1_c == "" || data[4].contents.tct_periodo_pf1_c == null) {
                                                            banderaPEPSPersonal = banderaPEPSPersonal + '<b>-Periodo en el cargo<br></b>';
                                                        }
                                                        if (data[4].contents.tct_fecha_ini_pf_c == "" || data[4].contents.tct_fecha_ini_pf_c == null) {
                                                            banderaPEPSPersonal = banderaPEPSPersonal + '<b>-Fecha Inicio<br></b>';
                                                        }
                                                        if (data[4].contents.tct_fecha_fin_pf_c == "" || data[4].contents.tct_fecha_fin_pf_c == null) {
                                                            banderaPEPSPersonal = banderaPEPSPersonal + '<b>-Fecha de término<br></b>';
                                                        }
                                                        if (banderaPEPSPersonal!=""){
                                                            necesarios =  necesarios +'<br>'+"Sección PEPS Personal:<br>" + banderaPEPSPersonal
                                                        }
                                                    }

                                                    //Sección PEPS Fisica Familiar
                                                    if (data[4].contents.ctpldconyuge_c == true) {
                                                        var banderaPEPSFamiliar="";
                                                        if (data[4].contents.ctpldconyugecargo_c == "" || data[4].contents.ctpldconyugecargo_c == null) {
                                                            banderaPEPSFamiliar = banderaPEPSFamiliar + '<b>-Especificar parentesco o relación<br></b>';
                                                        }
                                                        if (data[4].contents.tct_nombre_pf_peps_c == "" || data[4].contents.tct_nombre_pf_peps_c == null) {
                                                            banderaPEPSFamiliar = banderaPEPSFamiliar + '<b>-Nombre de la persona que ocupa el puesto<br></b>';
                                                        }
                                                        if (data[4].contents.tct_cargo2_pf_c == "" || data[4].contents.tct_cargo2_pf_c == null) {
                                                            banderaPEPSFamiliar = banderaPEPSFamiliar + '<b>-Cargo público que tiene o tuvo<br></b>';
                                                        }
                                                        if (data[4].contents.tct_dependencia2_pf_c == "" || data[4].contents.tct_dependencia2_pf_c == null) {
                                                            banderaPEPSFamiliar = banderaPEPSFamiliar + '<b>-Dependencia donde ejerce o ejerció el cargo<br></b>';
                                                        }
                                                        if (data[4].contents.tct_periodo2_pf_c == "" || data[4].contents.tct_periodo2_pf_c == null) {
                                                            banderaPEPSFamiliar = banderaPEPSFamiliar + '<b>-Periodo en el cargo<br></b>';
                                                        }
                                                        if (data[4].contents.tct_fecha_ini2_pf_c == "" || data[4].contents.tct_fecha_ini2_pf_c == null) {
                                                            banderaPEPSFamiliar = banderaPEPSFamiliar + '<b>-Fecha de Inicio<br></b>';
                                                        }
                                                        if (data[4].contents.tct_fecha_fin2_pf_c == "" || data[4].contents.tct_fecha_fin2_pf_c == null) {
                                                            banderaPEPSFamiliar = banderaPEPSFamiliar + '<b>-Fecha de término<br></b>';
                                                        }
                                                        if (banderaPEPSFamiliar!=""){
                                                            necesarios = necesarios +'<br>'+"Sección PEPS Familiar:<br>" + banderaPEPSFamiliar
                                                        }
                                                    }
                                            }


                                            //Preguntas PLD
                                            /*if (data[3].contents.records.length>0){
                                                if (data[3].contents.records[0].tct_pld_campo2_ddw == "" || data[3].contents.records[0].tct_pld_campo2_ddw  == null) {
                                                    necesarios = necesarios + '<b>Pregunta 1 PLD-Crédito Simple<br></b>';
                                                }
                                                if (data[3].contents.records[0].tct_pld_campo4_ddw == "" || data[3].contents.records[0].tct_pld_campo4_ddw == null) {
                                                    necesarios = necesarios + '<b>Pregunta 3 PLD-Crédito Simple<br></b>';
                                                }
                                                if (data[3].contents.records[0].tct_pld_campo18_ddw == "" || data[3].contents.records[0].tct_pld_campo18_ddw == null) {
                                                    necesarios = necesarios + '<b>Pregunta 5 PLD-Crédito Simple<br></b>';
                                                }
                                                /*if (data[3].contents.records[0].tct_pld_campo14_chk == "" || data[3].contents.records[0].tct_pld_campo14_chk == null) {
                                                    necesarios = necesarios + '<b>Pregunta 6 PLD-Crédito Simple<br></b>';
                                                }
                                                if (data[3].contents.records[0].tct_pld_campo19_txt == "" || data[3].contents.records[0].tct_pld_campo19_txt == null) {
                                                    necesarios = necesarios + '<b>Pregunta 5.1 PLD-Crédito Simple<br></b>';
                                                }
                                                if (data[3].contents.records[0].tct_pld_campo20_ddw == "" || data[3].contents.records[0].tct_pld_campo20_ddw == null) {
                                                    necesarios = necesarios + '<b>Pregunta 7 PLD-Crédito Simple<br></b>';
                                                }
                                                if (data[3].contents.records[0].tct_pld_campo6_ddw == "" || data[3].contents.records[0].tct_pld_campo6_ddw == null) {
                                                    necesarios = necesarios + '<b>Pregunta 8 PLD-Crédito Simple<br></b>';
                                                }
                                            }*/
                                        }else{
                                            //PERSONA MORAL
                                        if ( this.model.get('relaciones_activas').includes('Aval') || this.model.get('relaciones_activas').includes('Proveedor de Recursos CS') || this.model.get('relaciones_activas').includes('Accionista')) {
                                                if (data[4].contents.razonsocial_c == "" || data[4].contents.razonsocial_c == null) {
                                                    necesarios = necesarios + '<b>Razón Social<br></b>';
                                                }
                                        }
                                        if (this.model.get('relaciones_activas').includes('Aval') || this.model.get('relaciones_activas').includes('Proveedor de Recursos CS')) {
                                            //Evaluamos campos faltantes en direccion
                                                if(direP<=0){
                                                    necesarios = necesarios + '<b>Dirección Particular<br></b>';
                                                }
                                                //Evaluamos campos faltantes en direccion
                                                if(telO<=0){
                                                    necesarios = necesarios + '<b>Teléfono<br></b>';
                                                }
                                            }
                                        if (this.model.get('relaciones_activas').includes('Aval') || this.model.get('relaciones_activas').includes('Proveedor de Recursos CS')) {
                                                //Valida persona Moral
                                            // if (data[4].contents.actividadeconomica_c == "" || data[4].contents.actividadeconomica_c == null) {
                                            //     necesarios = necesarios + '<b>Actividad Económica<br></b>';
                                            // }
                                            if (data[4].contents.nacionalidad_c == "" || data[4].contents.nacionalidad_c == null || data[4].contents.nacionalidad_c == '0') {
                                                necesarios = necesarios + '<b>Nacionalidad</b><br>';
                                            }
                                            if (data[4].contents.rfc_c == "" || data[4].contents.rfc_c == null ) {
                                                    necesarios = necesarios + '<b>RFC</b><br>';
                                            }
                                            if (data[4].contents.tct_pais_expide_rfc_c == "" || data[4].contents.tct_pais_expide_rfc_c == null) {
                                                necesarios = necesarios + '<b>Pa\u00EDs que expide el RFC</b><br>';
                                            }
                                            if (data[4].contents.ctpldnoseriefiel_c == "" || data[4].contents.ctpldnoseriefiel_c == null) {
                                                    necesarios = necesarios + '<b>Número de serie de la Firma Electrónica Avanzada</b><br>';
                                            }
                                            if (data[4].contents.fechaconstitutiva_c == "" || data[4].contents.fechaconstitutiva_c == null) {
                                                necesarios = necesarios + '<b>Fecha Constitutiva</b><br>';
                                            }
                                            //Preguntas CHECK deudor_factor_c
                                            if (data[4].contents.deudor_factor_c==true){
                                                if (data[4].contents.apoderado_nombre_c == "" || data[4].contents.apoderado_nombre_c == null) {
                                                    necesarios = necesarios + '<b>Nombre Apoderado Legal</b><br>';
                                                }
                                                if (data[4].contents.apoderado_apaterno_c == "" || data[4].contents.apoderado_apaterno_c == null) {
                                                    necesarios = necesarios + '<b>Apellido Paterno Apoderado Legal</b><br>';
                                                }
                                                if (data[4].contents.apoderado_amaterno_c == "" || tdata[4].contents.apoderado_amaterno_c == null) {
                                                    necesarios = necesarios + '<b>Apellido Materno Apoderado Legal</b><br>';
                                                }
                                            }

                                        }

                                        if(this.model.get('relaciones_activas').includes('Aval') || this.model.get('relaciones_activas').includes('Obligado solidario')){
                                            if (data[4].contents.tct_cpld_pregunta_u1_ddw_c == "" || data[4].contents.tct_cpld_pregunta_u1_ddw_c == null) {
                                                necesarios = necesarios + '<b>Pregunta SOFOM</b><br>';
                                            }
                                            if (data[4].contents.tct_cpld_pregunta_u3_ddw_c == "" || data[4].contents.tct_cpld_pregunta_u3_ddw_c == null) {
                                                necesarios = necesarios + '<b>¿Cotiza en Bolsa?</b><br>';
                                            }
                                            if (data[4].contents.tct_fedeicomiso_chk_c == "" || data[4].contents.tct_fedeicomiso_chk_c == null) {
                                                necesarios = necesarios + '<b>¿Es Fideicomiso?</b><br>';
                                            }
                                        }
                                        if(this.model.get('relaciones_activas').includes('Proveedor de Recursos CS')){
                                                 //PEPS Moral Familiar
                                            if (data[4].contents.ctpldaccionistasconyuge_c == true) {
                                                var banderaPEPSMoralFamiliar="";
                                                if (data[4].contents.tct_socio2_pm_c == "" || data[4].contents.tct_socio2_pm_c == null) {
                                                    banderaPEPSMoralFamiliar = banderaPEPSMoralFamiliar + '<b>-Nombre del Socio o Accionista<br></b>';
                                                }
                                                if (data[4].contents.ctpldaccionistasconyugecargo_c == "" || data[4].contents.ctpldaccionistasconyugecargo_c == null) {
                                                    banderaPEPSMoralFamiliar = banderaPEPSMoralFamiliar + '<b>-Especificar parentesco o relación<br></b>';
                                                }
                                                if (data[4].contents.tct_nombre_pm_c == "" || data[4].contents.tct_nombre_pm_c == null) {
                                                    banderaPEPSMoralFamiliar = banderaPEPSMoralFamiliar + '<b>-Nombre de la persona que ocupa el puesto<br></b>';
                                                }
                                                if (data[4].contents.tct_cargo_pm_c == "" || data[4].contents.tct_cargo_pm_c == null) {
                                                    banderaPEPSMoralFamiliar = banderaPEPSMoralFamiliar + '<b>-Cargo público que tiene o tuvo<br></b>';
                                                }
                                                if (data[4].contents.tct_dependencia2_pm_c == "" || data[4].contents.tct_dependencia2_pm_c == null) {
                                                    banderaPEPSMoralFamiliar = banderaPEPSMoralFamiliar + '<b>-Dependencia donde ejerce o ejerció el cargo<br></b>';
                                                }
                                                if (data[4].contents.tct_periodo2_pm_c == "" || data[4].contents.tct_periodo2_pm_c == null) {
                                                    banderaPEPSMoralFamiliar = banderaPEPSMoralFamiliar + '<b>-Periodo en el cargo<br></b>';
                                                }
                                                if (data[4].contents.tct_fecha_ini2_pm_c == "" || data[4].contents.tct_fecha_ini2_pm_c == null) {
                                                    banderaPEPSMoralFamiliar = banderaPEPSMoralFamiliar + '<b>-Fecha de Inicio<br></b>';
                                                }
                                                if (data[4].contents.tct_fecha_fin2_pm_c == "" || data[4].contents.tct_fecha_fin2_pm_c == null) {
                                                    banderaPEPSMoralFamiliar = banderaPEPSMoralFamiliar + '<b>-Fecha de término<br></b>';
                                                }
                                                if (banderaPEPSMoralFamiliar!=""){
                                                    necesarios = necesarios +'<br>'+ "Sección PEPS Moral Familiar:<br>" + banderaPEPSMoralFamiliar
                                                }
                                            }

                                            //PEPS Moral Personal
                                            if(data[4].contents.ctpldaccionistas_c==true){
                                                var banderaPEPSMoralPersonal="";
                                                if (data[4].contents.tct_socio_pm_c == "" || data[4].contents.tct_socio_pm_c == null) {
                                                    banderaPEPSMoralPersonal = banderaPEPSMoralPersonal + '<b>-Nombre del Socio o Accionista</b><br>';
                                                }
                                                if (data[4].contents.ctpldaccionistascargo_c == "" || data[4].contents.ctpldaccionistascargo_c == null) {
                                                    banderaPEPSMoralPersonal = banderaPEPSMoralPersonal + '<b>-Cargo público que tiene o tuvo</b><br>';
                                                }
                                                if (data[4].contents.tct_dependencia_pm_c == "" || data[4].contents.tct_dependencia_pm_c == null) {
                                                    banderaPEPSMoralPersonal = banderaPEPSMoralPersonal + '<b>-Dependencia donde ejerce o ejerció el cargo</b><br>';
                                                }
                                                if (data[4].contents.tct_periodo_pm_c == "" || data[4].contents.tct_periodo_pm_c == null) {
                                                    banderaPEPSMoralPersonal = banderaPEPSMoralPersonal + '<b>-Periodo en el cargo</b><br>';
                                                }
                                                if (data[4].contents.tct_fecha_ini_pm_c == "" || data[4].contents.tct_fecha_ini_pm_c == null) {
                                                    banderaPEPSMoralPersonal = banderaPEPSMoralPersonal + '<b>-Fecha de Inicio</b><br>';
                                                }
                                                if (data[4].contents.tct_fecha_fin_pm_c == "" || data[4].contents.tct_fecha_fin_pm_c == null) {
                                                    banderaPEPSMoralPersonal = banderaPEPSMoralPersonal + '<b>-Fecha de término</b><br>';
                                                }
                                                if (banderaPEPSMoralPersonal!=""){
                                                    necesarios = necesarios +'<br>'+"Sección PEPS Moral Personal:<br>" + banderaPEPSMoralPersonal
                                                }
                                            }
                                        }



                                            //Preguntas PLD
                                           /* if (data[3].contents.records.length>0){
                                                if (data[3].contents.records.tct_pld_campo4_ddw == "" || data[3].contents.records.tct_pld_campo4_ddw == null) {
                                                    necesarios = necesarios + '<b>Pregunta 3 PLD<br></b>';
                                                }
                                                if (data[3].contents.records.tct_pld_campo18_ddw == "" || data[3].contents.records.tct_pld_campo18_ddw == null) {
                                                    necesarios = necesarios + '<b>Pregunta 5 PLD<br></b>';
                                                }
                                                /*if (data[3].contents.records.tct_pld_campo14_chk == "" || data[3].contents.records.tct_pld_campo14_chk == null) {
                                                    necesarios = necesarios + '<b>Pregunta 6 PLD<br></b>';
                                                }
                                                if (data[3].contents.records.tct_pld_campo19_txt == "" || data[3].contents.records.tct_pld_campo19_txt == null) {
                                                    necesarios = necesarios + '<b>Pregunta 5.1 PLD<br></b>';
                                                }
                                                if (data[3].contents.records.tct_pld_campo20_ddw == "" || data[3].contents.records.tct_pld_campo20_ddw == null) {
                                                    necesarios = necesarios + '<b>Pregunta 7 PLD<br></b>';
                                                }
                                                if (data[3].contents.records.tct_pld_campo6_ddw == "" || data[3].contents.records.tct_pld_campo6_ddw == null) {
                                                    necesarios = necesarios + '<b>Pregunta 8 PLD<br></b>';
                                                }
                                            } */


                                        }
                                            //Evalua si hay campos requeridos y muestra alerta
                                            if (necesarios!="") {
                                                app.alert.show("Campos Requeridos para opp CS y negocio Uniclick Moral", {
                                                level: "error",
                                                messages: "Hace falta completar la siguiente información en la <b>Cuenta</b>"+ '<a href="#Accounts/' + this.model.get("account_id1_c")  +'" target= "_blank"> ' + this.model.get('relacion_c') + '  </a>' + 'para una relación de Producto Uniclick:<br> ' + necesarios,
                                                autoClose: false
                                                    });
                                                    errors['accounts_cstm'] = errors['accounts_cstm'] || {};
                                                    errors['accounts_cstm'].required = true;
                                            }

                                   }
                                   callback(null, fields, errors);
                               }, this)
                           });
                }else{
                    callback(null, fields, errors);
                }
        }else{
         callback(null, fields, errors);
        }
	},
})
