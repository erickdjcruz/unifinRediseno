/*
 * Author levementum.com - jescamilla@levementum.com, jgarcia@levementum.com
 * File:  custom/modules/Accounts/clients/base/views/create-nodupecheck/create-nodupecheck.js
 *
 * Obtains a custom dependency list from the following API: custom/clients/base/api/customValidations.php
 * And hides or shows fields according to the rules found.
 * This acts as a custom dependency manager for Multi Select fields allowing dependencies to be interlocked.
 *
 * Target Module: Accounts - Personas
 */
({
    extendsFrom: 'CreateView',
    //extendsFrom: 'BaseCreateNodupecheckView',

    initialize: function (options) {
        self = this;
        contexto_cuenta = this;

        this._super("initialize", [options]);

        /*
         Contexto campos custom
         */
        //Teléfonos
        this.oTelefonos = [];
        this.oTelefonos.telefono = [];
        this.prev_oTelefonos = [];
        this.prev_oTelefonos.prev_telefono = [];

        //Direcciones
        this.oDirecciones = [];
        this.oDirecciones.direccion = [];
        this.prev_oDirecciones = [];
        this.prev_oDirecciones.prev_direccion = [];

        //v360
        this.ResumenCliente = [];

        //PLD
        this.ProductosPLD = [];
        this.prev_ProductosPLD = [];

        // UniProductos
        this.Oproductos = [];
        this.Oproductos.productos = [];

        //this.enableDuplicateCheck = true;

        //Funcion que quita los años futuros y menores a -5 del año actual
        this.quitaanos();
        this.model.on("change:tct_ano_ventas_ddw_c", _.bind(this.quitaanos, this));
        //add validation tasks
        this.model.addValidationTask('check_email_telefono', _.bind(this._doValidateEmailTelefono, this));
        this.model.addValidationTask('check_rfc', _.bind(this._doValidateRFC, this));
        this.model.addValidationTask('check_fecha_de_nacimiento', _.bind(this._doValidateMayoriadeEdad, this));
        this.model.addValidationTask('check_telefonos', _.bind(this.validatelefonosexisting, this));
        this.model.addValidationTask('check_account_direcciones', _.bind(this._doValidateDireccion, this));
        this.model.addValidationTask('check_Tiene_Contactos', _.bind(this._doValidateTieneContactos, this));
        this.model.addValidationTask('check_1900_year', _.bind(this.fechaMenor1900, this));
        this.model.addValidationTask('fechadenacimiento_c', _.bind(this.doValidateDateNac, this));
        this.model.addValidationTask('fechaconstitutiva_c', _.bind(this.doValidateDateCons, this));
        //this.model.addValidationTask('check_formato_curp_c', _.bind(this.ValidaFormatoCURP, this));
        this.model.addValidationTask('estado_civil_persona', _.bind(this._doValidateEdoCivil, this));
        this.model.addValidationTask('RequeridosPropietarioReal', _.bind(this.requeridosPropietarioReal, this));
        this.model.addValidationTask('validarequeridosProvRec', _.bind(this.RequeridosProveedorRecursos, this));


        //this.model.on('change:tipo_registro_cuenta_c', this._ShowDireccionesTipoRegistro, this);
        //this.model.on('change:estatus_c', this._ShowDireccionesTipoRegistro, this);
        this.model.on('change:tipodepersona_c', this._ActualizaEtiquetas, this);
        this.model.on('change:account_telefonos', this.setPhoneOffice, this);
        /*
        AA 24/06/2019 Se añade evento para desabilitar el boton genera RFC si la nacionalidad es diferente de Mexicano
      */
        //this.model.on('change:tct_pais_expide_rfc_c', this.ocultaRFC, this);

        //this.model.on('change:fechadenacimiento_c', this._doGenera_RFC_CURP, this);
        //this.model.on('change:fechaconstitutiva_c', this._doGenera_RFC_CURP, this);
        //this.model.on('change:razonsocial_c', this._doGenera_RFC_CURP, this);
        //this.model.on('change:primernombre_c', this._doGenera_RFC_CURP, this);
        //this.model.on('change:apellidopaterno_c', this._doGenera_RFC_CURP, this);
        //this.model.on('change:apellidomaterno_c', this._doGenera_RFC_CURP, this);
        //this.model.addValidationTask('guardaProductosPLD', _.bind(this.saveProdPLD, this));

        //this.model.on('change:genero_c', this._doGeneraCURP, this);
        //this.model.on('change:pais_nacimiento_c', this._doGeneraCURP, this);
        //this.model.on('change:estado_nacimiento_c', this._doGeneraCURP, this);

        this.model.on('change:profesion_c', this._doValidateProfesionRisk, this);
        this.model.on('change:pais_nacimiento_c', this._doValidateProfesionRisk, this);
        //this.model.on('change:pais_nacimiento_c',this.validaExtranjerosRFC, this);
        //this.model.on('change:rfc_c',this.validaFechaNacimientoDesdeRFC, this);
        //Validacion para el formato de los campos nombre y apellidos.
        this.model.addValidationTask('validaformato3campos', _.bind(this.validaformato, this));
        this.model.addValidationTask('validacamposcurppass', _.bind(this.validapasscurp, this));

        this.model.on('change:primernombre_c', this.checkTextOnly, this);
        this.model.on('change:apellidomaterno_c', this.checkTextOnly, this);
        this.model.on('change:apellidopaterno_c', this.checkTextOnly, this);
        this.model.on('change:ifepasaporte_c', this.checkTextAndNum, this);
        this.model.on('change:curp_c', this.checkTextAndNum, this);

        this.events['click a[name=generar_rfc_c]'] = '_doGenera_RFC_CURP';
        this.events['click a[name=generar_curp_c]'] = '_doGeneraCURP';

        /**
         * @author Carlos Zaragoza Ortiz
         * @date 16-10-2015
         * UNIFIN TASK: Modificar el riesgo en caso de seleccionar "PEP" en cuestionario de PLD
         * */
        this.model.addValidationTask('verificaRiesgoPep', _.bind(this.cambiaRiesgodePersona, this));

        /**
         * @author Carlos Zaragoza Ortiz
         * @date 16-10-2015
         * Al ser proveedor debe solicitar como obligatorio el tipo de proveedor
         * */
        this.model.addValidationTask('tipo_proveedor_requerido', _.bind(this.validaProveedorRequerido, this));
        /* END */
        this.model.addValidationTask('valida_potencial_campos_autos', _.bind(this.nodigitos, this));

        /***************Valida Campo de Página Web ****************************/
        this.model.addValidationTask('validaPaginaWeb', _.bind(this.validaPagWeb, this));

        /** Valida genero personas fisicas y fisica con actividad empesarial **/
        this.model.addValidationTask('validaGenero', _.bind(this.validaGenero, this));

        this.model.addValidationTask('valida_requeridos', _.bind(this.valida_requeridos, this));
        this.model.addValidationTask('set_custom_fields', _.bind(this.setCustomFields, this));
        this.model.addValidationTask('Guarda_campos_auto_potencial', _.bind(this.savepotauto, this));

        //this.enableDuplicateCheck = true;


		/*RFC_ValidatePadron
		  Validación de rfc en el padron de contribuyentes
		*/
        //self.rfc_antiguo = "";
        //this.model.on('change:rfc_c', this.cambioRFC, this);
        //this.model.addValidationTask('RFC_validateP', _.bind(this.RFC_ValidatePadron, this));


        //UNFIN TASK:
        //@author Carlos Zaragoza: Si la persona es extranjera debe generar RFC gen�rico (XXX010101XXX)
        //this.model.on('change:pais_nacimiento_c', this._doGeneraCURP, this);

        var valParams = {
            'modulo': 'Accounts',
        };
        var valUrl = app.api.buildURL("customValidations", '', {}, {});
        app.api.call("create", valUrl, { data: valParams }, { //Call and Collect the Dependencies
            success: _.bind(function (data) {
                if (data != null) {
                    self.validaciones = data;
                    _.each(data, function (values, parent_field) {
                        self.model.on("change:" + parent_field, function (el) { //Register on change functions for all parent fields.
                            var theField = parent_field;
                            _.each(self.validaciones, function (val_values, val_parent_field) {
                                if (parent_field == val_parent_field) {
                                    var parent_field_values = self.model.get(parent_field);
                                    _.each(val_values, function (rule, rule_name) { //VALIDATION MATCH
                                        if (_.contains(parent_field_values, rule_name) //is it contained in an array?
                                            || parent_field_values == rule_name) { //is it contained in a string?
                                            _.each(rule, function (rule_body, rule_index) {
                                                //Check to see if the rule is active
                                                if (_.isNull(rule_body.estatus) || rule_body.estatus == 'Inactivo') {
                                                    return;
                                                }

                                                //Check for visible, on this version this is the only dependency
                                                if (rule_body.visible == '0' || _.isNull(rule_body.visible)) { //VISIBLE RULE
                                                    $('[data-name="' + rule_body.campo_dependiente + '"]').addClass('vis_action_hidden disabled');
                                                }

                                                //EXECUTE SUB VALIDATIONS
                                                //jescamilla Process SubValidaciones (AND)
                                                if (rule_index == 'SubValidaciones') {
                                                    _.each(rule_body, function (subvalidacion, subvalidacion_index) {
                                                        if (self.model.get(subvalidacion.campo_padre) == subvalidacion.criterio_validacion) { //if its not required, do not enforce it
                                                            //WHAT IS THE SUB VALIDATION DIRECTIVE?
                                                            if (subvalidacion.visible == '0' || _.isNull(subvalidacion.visible)) { //VISIBLE RULE
                                                                $('[data-name="' + subvalidacion.campo_dependiente + '"]').addClass('vis_action_hidden disabled');
                                                            }

                                                            if (subvalidacion.visible == '1') { //VISIBLE RULE
                                                                var theField = $('[data-name="' + subvalidacion.campo_dependiente + '"]')
                                                                if (!theField.hasClass("vis_action_hidden")) { //do not show if its already visible
                                                                    return;
                                                                } //this prevent all the fields from flashing, only flash the fields that are being made visible
                                                                theField.removeClass('vis_action_hidden disabled'); //show the field because the dependency is no longer available
                                                                theField.closest(".row-fluid.panel_body").show(); //show row in case is hidden
                                                                theField.animate({ //flash the field to indicate the user which fields are coming back
                                                                    backgroundColor: '#FF8F8F'
                                                                }, 200, function () {
                                                                    theField.animate({
                                                                        backgroundColor: '#FFFFFF'
                                                                    }, 200, function () {
                                                                        //delete SUGAR.forms.flashInProgress[key];
                                                                    });
                                                                });
                                                            }
                                                        }
                                                    });
                                                }
                                            });
                                        } else { //VALIDATION MISSMATCH
                                            if (self.model.get(parent_field) != '') {
                                                return;
                                            }
                                            _.each(rule, function (rule_body, rule_index) {
                                                //Check to see if the rule is active
                                                if (_.isNull(rule_body.estatus) || rule_body.estatus == 'Inactivo') {
                                                    return;
                                                }

                                                //Check for visible, on this version this is the only dependency
                                                if (rule_body.visible == '0' || _.isNull(rule_body.visible)) { //VISIBLE RULE
                                                    var theField = $('[data-name="' + rule_body.campo_dependiente + '"]')
                                                    if (!theField.hasClass("vis_action_hidden")) { //do not show if its already visible
                                                        return;
                                                    } //this prevent all the fields from flashing, only flash the fields that are being made visible
                                                    theField.removeClass('vis_action_hidden disabled'); //show the field because the dependency is no longer available
                                                    theField.closest(".row-fluid.panel_body").show(); //show row in case is hidden
                                                    theField.animate({ //flash the field to indicate the user which fields are coming back
                                                        backgroundColor: '#FF8F8F'
                                                    }, 200, function () {
                                                        theField.animate({
                                                            backgroundColor: '#FFFFFF'
                                                        }, 200, function () {
                                                            //delete SUGAR.forms.flashInProgress[key];
                                                        });
                                                    });
                                                }
                                            });
                                        }
                                    });
                                }
                            });
                        }, this);
                    });

                    /*jgarcia@levementum.com 9/28/2015 Description: Copiar relaciones activas de la Relacion creada desde el modulo de Relaciones y copiar esos valores en el campo de tipo de relacion*/
                    try {
                        if (relContext != null) {
                            self.model.set("tipo_relacion_c", relContext.model.get("relaciones_activas"));
                        }
                    } catch (e) {
                        console.log('No es relación el error: ' + e);
                    }
                }
            }, this)
        });

        /** BEGIN CUSTOMIZATION: jgarcia@levementum.com 7/14/2015 Description: Cuando estamos en el modulo de Personas, no queremos que se muestre la opcion Persona para el tipo de registro */
        var new_options = app.lang.getAppListStrings('tipo_registro_cuenta_list');

        try {
            if (relContext != null) {
                Object.keys(new_options).forEach(function (key) {
                    if (key != "4") {
                        delete new_options[key];
                    }
                });
            }
        } catch (e) {
            console.log('No es relación  error: ' + e);
            // var new_options = app.lang.getAppListStrings('tipo_registro_cuenta_list');

            if (this.context.parent.attributes.module == "Accounts") {
                Object.keys(new_options).forEach(function (key) {
                    if (key != "4") {
                        delete new_options[key];
                    }
                });
            }
            else {
                Object.keys(new_options).forEach(function (key) {
                    if (key == "4") {
                        delete new_options[key];
                    }
                });

                if (App.user.attributes.tct_alta_clientes_chk_c == 1 && App.user.attributes.tct_altaproveedor_chk_c == 1) {
                    Object.keys(new_options).forEach(function (key) {
                        if (key != "3" && key != "5") {
                            delete new_options[key];
                        }
                    });
                } else if (App.user.attributes.tct_alta_clientes_chk_c == 1) {

                    Object.keys(new_options).forEach(function (key) {
                        if (key != "3") {
                            delete new_options[key];
                        }
                    });
                } else if (App.user.attributes.tct_altaproveedor_chk_c == 1) {

                    Object.keys(new_options).forEach(function (key) {
                        if (key != "5") {
                            delete new_options[key];
                        }
                    });
                }
                //En otro caso, solo mostrar Lead
                else {
                    Object.keys(new_options).forEach(function (key) {
                        if (key != "1") {
                            delete new_options[key];
                        }
                    });
                }
            }
        }

        this.model.fields['tipo_registro_cuenta_c'].options = new_options;
        //this.model.on('change:name', this.cleanName, this);
        this.model.on('change:no_website_c', this.rowebsite, this);
        //Ocultar panel Analizate
        this.$("[data-panelname='LBL_RECORDVIEW_PANEL18']").hide();
        //this.model.addValidationTask('UniclickCanal', _.bind(this.requeridosUniclickCanal, this));
        this.model.addValidationTask('tipo_proveedor_compras', _.bind(this.tipoProveedor, this));
        //Limpia los campos dependientes de Origen y Detalle Origen
        this.model.on('change:origen_cuenta_c', this._cleanDependenciesOrigen, this);
        this.model.on('change:detalle_origen_c', this._cleanDependencies, this);
        this.model.addValidationTask('clean_name', _.bind(this.cleanName, this));

        //Validation task que muestra modal sobre duplicados
        this.model.addValidationTask('check_duplicados_account', _.bind(this.check_duplicados_account, this));
    },

    _render: function () {
        this._super("_render");

        //Ocultar campo "Ruta de Imagen QR" siempre. Se agregó a la vista para que esté disponible a través de this.model
        $('[data-name="path_img_qr_c"]').hide();

        //Ocultar campo "No Contactar" siempre. Se agregó a la vista para que esté disponible a través de this.model
        $('[data-name="tct_no_contactar_chk_c"]').hide();
        this._doValidateProfesionRisk();
        /** BEGIN CUSTOMIZATION: jgarcia@levementum.com 9/28/2015 Description: Copiar relaciones activas de la Relacion creada desde el modulo de Relaciones y copiar esos valores en
         * el campo de tipo de relacion*/
        //Oculta la etiqueta del campo PLD
        $('div[data-name=accounts_tct_pld]').find('div.record-label').addClass('hide');
		$('[data-name=tct_nuevo_pld_c]').hide();
        try {
            if (relContext != null) {
                self.model.set("tipo_relacion_c", relContext.model.get("relaciones_activas"));
            }
        } catch (e) {
            console.log('No es relación  error: ' + e);
        }
        /* END CUSTOMIZATION */
        //Ocultarel panel de Lead no viable (checks).
        $('[data-name=tct_noviable]').hide();

        //Se oculta check de cuenta homonima
        $('div[data-name=tct_homonimo_chk_c]').hide();

        //campo Pais que expide el RFC nace oculto.
        //$('[data-name=tct_pais_expide_rfc_c]').hide();
        //Oculta panel del campo Tipo de Cuenta por Producto
        this.$("[data-panelname='LBL_RECORDVIEW_PANEL17']").hide();
        //Oculta nombre de campo Potencial_Autos
        $("div.record-label[data-name='potencial_autos']").attr('style', 'display:none;');

        this.ocultaRFC();

        //cuando creamos una relacion de account a account, el tipo de registro siempre debe de ser persona
        this.model.set('tipo_registro_cuenta_c', '4');
        // this.model.on("change:tipo_registro_cuenta_c", _.bind(function () {
        //     this.model.set('tipo_registro_cuenta_c','Persona');
        // }, this));
        /*
         * @author Carlos Zaragoza ortiz
         * Ocultar campo de estatus Activo/Inactivo en creaci�n de personas
         * */
        this.$('div[data-name=estatus_persona_c]').hide();
        $('[data-name=tct_nuevo_pld_c]').hide(); //Oculta campo tct_nuevo_pld_c

        /*
           AF - 2018/07/06
           Cambio: Se coultan pesta�as:  Vista 360, Cuestionario PLD y campo show panel
        */
        //Oculta vista 360 y Cuestionario PLD
        //TabNav
        $("#drawers li.tab").removeClass('active');
        $('#drawers li.tab.panel_body').addClass("active");
        $('#drawers li.tab.LBL_RECORDVIEW_PANEL8').hide();
        //$('#drawers li.tab.LBL_RECORDVIEW_PANEL1').hide();
        $('#drawers li.tab.LBL_RECORDVIEW_PANEL2').hide();

		//Oculta Peps de Persona Moral
        this.$("[data-panelname='LBL_RECORDVIEW_PANEL7']").hide();
        this.$("[data-panelname='LBL_RECORDVIEW_PANEL6']").hide();
        this.$("[data-panelname='LBL_RECORDVIEW_PANEL9']").hide();

        if (this.context.parent.attributes.module == "Accounts" && this.model.get('tipo_relacion_c').includes('Propietario Real')) {
            $('#drawers li.tab.LBL_RECORDVIEW_PANEL1').show();
        }
        /*
        * F. Javier G. Solar 06/08/2018
        * Si se crea desde Oportunidades el arreglo se recorre una posicion
        * para ocultar correctamente los paneles**/

        if (this.context.parent.attributes.module == "Opportunities") {
            //Tabcontent
            $("#drawers div.tab-content").children()[1].classList.remove('active');
            $("#drawers div.tab-content").children()[2].classList.add('active');
            $("#drawers div.tab-content").children()[2].classList.remove('fade');
        }
        else {
            //Tabcontent posiciones default
            $("#drawers div.tab-content").children()[0].classList.remove('active');
            $("#drawers div.tab-content").children()[1].classList.add('active');
            $("#drawers div.tab-content").children()[1].classList.remove('fade');
        }

        //Oculta campo
        $("div[data-name='show_panel_c']").hide();
        /*
        * F. Javier G. Solar 06/08/2018
         Se oculta el boton de mas opciones en las petañas de cuentas(record) */
        $('.nav-tabs li a.dropdown-toggle').hide();


        /* @author F. Javier Garcia S. 05/10/2018
        Se oculta panel NPS,al crear cuenta desde el modulo Rel_Relacion".
         */
        this.$("[data-panelname='LBL_RECORDVIEW_PANEL10']").hide();
        //Oculta campo Analizate
        this.$("[data-panelname='LBL_RECORDVIEW_PANEL18']").attr('style', 'display:none;');

        //Oculta panel de uni_productos
        //this.$("[data-panelname='LBL_RECORDVIEW_PANEL19']").attr('style', 'display:none;');

        //Deshabilita campo cuenta especial
        if (app.user.attributes.cuenta_especial_c == 0 || app.user.attributes.cuenta_especial_c == "") {
            $('div[data-name=cuenta_especial_c]').css("pointer-events", "none");
        }

        this.$('[data-name="account_tipoSubtipo"]').hide();
        this.$("div.record-label[data-name='rfc_qr']").attr('style', 'display:none;');

        //Deshabilita campo Actualizar Multilínea
        if(app.user.attributes.multilinea_c == 0 || app.user.attributes.multilinea_c == "") {
            $('div[data-name=multilinea_c]').css("pointer-events", "none");
        }
        //Oculta campos de Macro Sector
        this.$("div[data-name='tct_macro_sector_ddw_c']").hide();
        this.$("div[data-name='sectoreconomico_c']").hide();
        this.$("div[data-name='subsectoreconomico_c']").hide();
        this.$("div[data-name='actividadeconomica_c']").hide();
        this.$(".record-cell[data-name='blank_space']").hide();
        this.$(".record-cell[data-name='blank_space']").hide();
        //Oculta Botón Generar RFC
		var seguros = 0;
        for (var i = 0; i < app.user.attributes.roles.length; i++) {
            if (app.user.attributes.roles[i] == "Seguros") seguros = 1;
        }
        var accesoFiscal = App.user.attributes.tct_alta_clientes_chk_c + App.user.attributes.tct_altaproveedor_chk_c + App.user.attributes.tct_alta_cd_chk_c + App.user.attributes.deudor_factoraje_c + seguros;
        if (accesoFiscal == 0 && this.model.get('tipo_registro_cuenta_c') != '4') {
          this.$('div[data-name=rfc_c]').css("pointer-events", "none");
          this.$('div[data-name="generar_rfc_c"]').hide();
        }
        if (App.user.attributes.puestousuario_c != 32 && App.user.attributes.puestousuario_c != 47) {
          this.$('div[data-name=tipo_proveedor_compras_c]').css("pointer-events", "none");
        }
    },

    _ActualizaEtiquetas: function () {
        if (this.model.get('tipodepersona_c') != 'Persona Moral' ) {
            this.$("div.record-label[data-name='pais_nacimiento_c']").text("Pa\u00EDs de nacimiento");
            //Establece etiqueta a campo personalizado de pais y estado
            $('[data-fieldname="account_paises_estados"]').find('.record-label').eq(0).text("Pa\u00EDs de nacimiento");
        } else {
            this.$("div.record-label[data-name='pais_nacimiento_c']").text("Pa\u00EDs de constituci\u00F3n");
            $('[data-fieldname="account_paises_estados"]').find('.record-label').eq(0).text("Pa\u00EDs de constituci\u00F3n");
        }

        if (this.model.get('tipodepersona_c') != 'Persona Moral') {
            this.$("div.record-label[data-name='estado_nacimiento_c']").text("Estado de nacimiento");
            $('[data-fieldname="account_paises_estados"]').find('.record-label').eq(1).text("Estado de nacimiento");
        } else {
            this.$("div.record-label[data-name='estado_nacimiento_c']").text("Estado de constituci\u00F3n");
            $('[data-fieldname="account_paises_estados"]').find('.record-label').eq(1).text("Estado de constituci\u00F3n");
        }
    },

    _doGeneraCURP: function () {
        if (this.model.get('tipodepersona_c') != 'Persona Moral') {
            //Valida que se tenga la informaci�n requerida para generar la CURP
            if (this.model.get('fechadenacimiento_c') != null && this.model.get('genero_c') != null && this.model.get('genero_c') != ''
                && this.model.get('primernombre_c') != null && this.model.get('apellidopaterno_c') != null && this.model.get('apellidomaterno_c') != null
                && this.model.get('pais_nacimiento_c') != null && this.model.get('estado_nacimiento_c') != null) {
                var firmoParams = {
                    'fechadenacimiento': this.model.get('fechadenacimiento_c'),
                    'primernombre': this.model.get('primernombre_c'),
                    'apellidoP': this.model.get('apellidopaterno_c'),
                    'apellidoM': this.model.get('apellidomaterno_c'),
                    'genero': this.model.get('genero_c'),
                    'pais': this.model.get('pais_nacimiento_c'),
                    'estado': this.model.get('estado_nacimiento_c'),
                    'tipodepersona': this.model.get('tipodepersona_c')
                };
                var dnbProfileUrl = app.api.buildURL("Accounts/GenerarCURP", '', {}, {});
                if (this.model.get('pais_nacimiento_c') == 2) {
                    app.api.call("create", dnbProfileUrl, { curpdata: firmoParams }, {
                        success: _.bind(function (data) {
                            if (data['UNI2_UTL_002_CreaCurpPersonaResult']['resultado']) {
                                this.model.set('curp_c', data['UNI2_UTL_002_CreaCurpPersonaResult']['curp']);
                            }
                        }, this)
                    });
                } else {
                    this.model.set('curp_c', '');
                }
            } else {
                app.alert.show("Generar CURP", {
                    level: "error",
                    title: "Faltan datos para poder generar el CURP",
                    autoClose: false
                });
            }
        }
    },

    _doValidateTieneContactos: function (fields, errors, callback) {
        if (this.model.get('tipodepersona_c') == 'Persona Moral' &&
            (/*this.model.get('tipo_registro_cuenta_c') == "Cliente" || this.model.get('estatus_c') == "Interesado"
    ||*/ this.model.get('tipo_registro_cuenta_c') == "2")) {
            if (_.isEmpty(this.model.get('account_contacts'))) {
                app.alert.show("Persona sin contactos registrados", {
                    level: "error",
                    title: "Debe registrar al menos un contacto.",
                    autoClose: false
                });
                errors['account_contacts'] = errors['account_contacts'] || {};
                errors['account_contacts'].required = true;
            }
        }
        callback(null, fields, errors);
    },

    ValidaFormatoCURP: function (fields, errors, callback) {
        if (this.model.get('tipodepersona_c') != 'Persona Moral') {
            var CURP = this.model.get('curp_c');
            if (CURP != '' && CURP != null) {
                CURP = CURP.toUpperCase().trim();

                if (!CURP.match("[A-Z]{4}[0-9]{6}[A-Z]{6}[0-9]?[0-9]?")) {
                    app.alert.show("CURP incorrecto", {
                        level: "error",
                        title: "El CURP no tiene un formato correcto.",
                        autoClose: false
                    });
                    errors['curp_c'] = errors['curp_c'] || {};
                    errors['curp_c'].required = true;
                }
            }
        }
        callback(null, fields, errors);
    },

    /*_ShowDireccionesTipoRegistro: function(){
        if(this.model.get('tipo_registro_cuenta_c') == "Cliente" || this.model.get('estatus_c') == "Interesado" || this.model.get('tipo_registro_cuenta_c') == "Persona"){
            this.$("div[data-name='account_direcciones']").show();
        }else{
            this.$("div[data-name='account_direcciones']").hide();
        }
        // Carlos Zaragoza: Se elimina el campo por defaiult de tipo de proveedor del registro pero sies proveedor, se selecciona bienes por default
        if(this.model.get('tipo_registro_cuenta_c') == 'Proveedor'){
            this.model.set('tipo_proveedor_c', '1');
        }
    },*/

    _doValidateDireccion: function (fields, errors, callback) {
        //Valida dirección
        if (this.model.get('tipo_registro_cuenta_c') == "3" || this.model.get('tipo_registro_cuenta_c') == "5" || this.model.get('tipo_registro_cuenta_c') == "2") {
            if (_.isEmpty(this.oDirecciones.direccion)) {
                errors[$(".addDireccion")] = errors['account_direcciones'] || {};
                errors[$(".addDireccion")].required = true;
                $('.direcciondashlet').css('border-color', 'red');
                app.alert.show("Direccion requerida", {
                    level: "error",
                    title: "Al menos una direcci\u00F3n es requerida.",
                    autoClose: false
                });
            } else {
                //Dirección activa
                var activa = 0;
                console.log('Validacion dir.activa');
                console.log(direcciones);
                var direcciones = this.oDirecciones.direccion;
                for (i = 0; i < direcciones.length; i++) {
                    if (direcciones[i].inactivo == 0) {
                        activa++;
                    }
                }
                //Valida variable nacional
                if (activa == 0) {
                    //Valdaci�n Nacional
                    console.log('Dir.activa requerida');
                    errors[$(".addDireccion")] = errors['account_direcciones'] || {};
                    errors[$(".addDireccion")].required = true;

                    $('.direcciondashlet').css('border-color', 'red');
                    app.alert.show("Direccion activa requerida", {
                        level: "error",
                        title: "Al menos una direcci\u00F3n activa es requerida.",
                        autoClose: false
                    });
                }
            }
        }
        //Campos requeridos
        var cont = 0;
        var direccion = this.oDirecciones.direccion;
        for (iDireccion = 0; iDireccion < direccion.length; iDireccion++) {
            //Tipo
            if (direccion[iDireccion].tipodedireccion == "") {
                cont++;
                this.$('.multi_tipo_existing ul.select2-choices').eq(iDireccion).css('border-color', 'red');
            } else {
                this.$('.multi_tipo_existing ul.select2-choices').eq(iDireccion).css('border-color', '');
            }
            //Indicador
            if (direccion[iDireccion].indicador == "") {
                cont++;
                this.$('.multi1_n_existing ul.select2-choices').eq(iDireccion).css('border-color', 'red');
            } else {
                this.$('.multi1_n_existing ul.select2-choices').eq(iDireccion).css('border-color', '');
            }
            //Código Postal
            if (direccion[iDireccion].valCodigoPostal == "") {
                cont++;
                this.$('.postalInputTempExisting').eq(iDireccion).css('border-color', 'red');
            } else {
                this.$('.postalInputTempExisting').eq(iDireccion).css('border-color', '');
            }
            //Calle
            if (direccion[iDireccion].calle.trim() == "") {
                cont++;
                this.$('.calleExisting').eq(iDireccion).css('border-color', 'red');
            } else {
                this.$('.calleExisting').eq(iDireccion).css('border-color', '');
            }
            //Número Exterior
            if (direccion[iDireccion].numext.trim() == "") {
                cont++;
                this.$('.numExtExisting').eq(iDireccion).css('border-color', 'red');
            } else {
                this.$('.numExtExisting').eq(iDireccion).css('border-color', '');
            }
        }
        //Muestra error en direcciones existentes
        if (cont > 0) {
            app.alert.show("empty_fields_dire", {
                level: "error",
                messages: "Favor de llenar los campos se\u00F1alados en <b> Direcciones </b> .",
                autoClose: false
            });
            errors['dire_direccion_req'] = errors['dire_direccion_req'] || {};
            errors['dire_direccion_req'].required = true;

        }

        //Valida direcciones duplicadas
        if (direccion.length > 0) {
            var coincidencia = 0;
            var indices = [];
            for (var i = 0; i < direccion.length; i++) {
                for (var j = 0; j < direccion.length; j++) {
                    if (i != j && direccion[i].inactivo == 0 && direccion[j].calle.toLowerCase() + direccion[j].ciudad + direccion[j].colonia + direccion[j].estado + direccion[j].municipio + direccion[j].numext.toLowerCase() + direccion[j].pais + direccion[j].postal + direccion[j].inactivo == direccion[i].calle.toLowerCase() + direccion[i].ciudad + direccion[i].colonia + direccion[i].estado + direccion[i].municipio + direccion[i].numext.toLowerCase() + direccion[i].pais + direccion[i].postal + direccion[i].inactivo) {
                        coincidencia++;
                        indices.push(i);
                        indices.push(j);
                    }
                }
            }
            //indices=indices.unique();
            if (coincidencia > 0) {
                app.alert.show('error_direccion_duplicada', {
                    level: 'error',
                    autoClose: false,
                    messages: 'Existen direcciones iguales, favor de corregir.'
                });
                //$($input).focus();
                if (indices.length > 0) {
                    for (var i = 0; i < indices.length; i++) {
                        $('.calleExisting').eq(indices[i]).css('border-color', 'red');
                        $('.numExtExisting').eq(indices[i]).css('border-color', 'red');
                        $('.postalInputTempExisting').eq(indices[i]).css('border-color', 'red');
                    }
                }
                errors['dire_direccion_duplicada'] = errors['dire_direccion_duplicada'] || {};
                errors['dire_direccion_duplicada'].required = true;
            }
        }

        callback(null, fields, errors);
    },

    /** BEGIN CUSTOMIZATION: jgarcia@levementum.com 6/12/2015 Description: Persona Fisica and Persona Fisica con Actividad Empresarial must have an email or a Telefono*/
    _doValidateEmailTelefono: function (fields, errors, callback) {
        //Valida que no sea relación - Persona tipo: tipo_relacion_c = Referencia Cliente/Proveedor
        //Actualización: Se añaden como requeridos los teléfonos y direcciones cuando se crea como relación Tarjetahabiente
        if(this.model.get('tipo_relacion_c').includes('Tarjetahabiente')){
            if (_.isEmpty(this.oDirecciones.direccion)) {
                app.alert.show("Direccion requerida", {
                    level: "error",
                    title: "Al menos una dirección es requerida.",
                    autoClose: false
                });

                errors['account_direcciones'] = errors['account_direcciones'] || {};
                errors['account_direcciones'].required = true;
            }

        }
        if (this.model.get('tipo_relacion_c').includes('Referencia Cliente') || this.model.get('tipo_relacion_c').includes('Referencia Proveedor') || this.model.get('tipo_relacion_c').includes('Tarjetahabiente')) {
            //Pide teléfono requerido
            if (_.isEmpty(this.oTelefonos.telefono)) {
                app.alert.show("Telefono requerido", {
                    level: "error",
                    title: "Al menos un tel\u00E9fono es requerido.",
                    autoClose: false
                });

                errors['account_telefonos'] = errors['account_telefonos'] || {};
                errors['account_telefonos'].required = true;
            }

        } else {
            //Pide teléfono/correo requerido
            if (this.model.get('tipo_registro_cuenta_c') != '4' && this.model.get('tipo_registro_cuenta_c') != '5') {
                var relaciones = this.model.get('tipo_relacion_c');
                relaciones = relaciones.toString();

                relaciones = relaciones.replace(/Referencia Cliente/g, "");
                relaciones = relaciones.replace(/Referencia Proveedor/g, "");
                relaciones = relaciones.replace(/Propietario Real/g, "");
                relaciones = relaciones.replace(/Contacto/g, "");
                relaciones = relaciones.replace(/Proveedor de Recursos L/g, "");
                relaciones = relaciones.replace(/Proveedor de Recursos F/g, "");
                relaciones = relaciones.replace(/Proveedor de Recursos CA/g, "");
                relaciones = relaciones.replace(/Proveedor de Recursos CS/g, "");
                relaciones = relaciones.replace(/^/g, "");
                relaciones = relaciones.replace(/,/g, "");
                relaciones = relaciones.replace(/ /g, "");

                //                if (relaciones != "") {
                if (_.isEmpty(this.model.get('email')) && _.isEmpty(this.oTelefonos.telefono)) {
                    app.alert.show("Correo requerido", {
                        level: "error",
                        title: "Al menos un correo electr\u00F3nico o un tel\u00E9fono es requerido.",
                        autoClose: false
                    });
                    errors['email'] = errors['email_telefono'] || {};
                    errors['email'].required = true;
                    errors['account_telefonos'] = errors['account_telefonos'] || {};
                    errors['account_telefonos'].required = true;
                }
                //                }
            }
        }
        callback(null, fields, errors);
    },

    _doValidateRFC: function (fields, errors, callback) {
        var fields = ["primernombre_c", "segundonombre_c", "apellidopaterno_c", "apellidomaterno_c", 'rfc_c'];
        var RFC = this.model.get('rfc_c');
        if (RFC != '' && RFC != null && this.model.get('tct_pais_expide_rfc_c') == "2") {
            /*M�todo que tiene la funci�n de validar el rfc*/
            RFC = RFC.toUpperCase().trim();
            var expReg = "";
            if (this.model.get('tipodepersona_c') != 'Persona Moral') {
                expReg = "[A-Z&]{4}[0-9]{6}[A-Z0-9]{3}";
            } else {
                expReg = "[A-Z&]{3}[0-9]{6}[A-Z0-9]{3}";
            }
            if (!RFC.match(expReg)) {
                app.alert.show("RFC incorrecto", {
                    level: "error",
                    title: "El RFC no tiene un formato correcto.",
                    autoClose: false
                });
                errors['rfc_c'] = errors['rfc_c'] || {};
                errors['rfc_c'].required = true;
            }
        }
        // var PrimerNombre = this.model.get('primernombre_c');
        // var SegundoNombre = this.model.get('segundonombre_c');
        // var ApellidoP = this.model.get('apellidopaterno_c');
        // var ApellidoM = this.model.get('apellidomaterno_c');
        var Nombre = this.model.get("name").toString();
        var c = 0;
        /*@Jesus Carrillo*/
        //var fields2=[PrimerNombre.trim(),SegundoNombre.trim(),ApellidoP.trim(),ApellidoM.trim()]
        var fields2 = [Nombre.trim()]
        for (var i = 0; i > fields2.length; i++) {
            if (fields2[i] != '' || fields2[i] != null) {
                c++;
            }
        }
        if (c > 0) {
            app.api.call("read", app.api.buildURL("Accounts/", null, null, {
                fields: fields.join(','),
                max_num: 5,
                "filter": [
                    {
                        "rfc_c": RFC,
                        "primernombre_c": PrimerNombre,
                        "segundonombre_c": SegundoNombre,
                        "apellidopaterno_c": ApellidoP,
                        "apellidomaterno_c": ApellidoM,
                    }
                ]
            }), null, {
                success: _.bind(function (data) {
                    if (data.records.length > 0) {

                        app.alert.show("DuplicateCheck", {
                            level: "error",
                            title: "Se encontro un registro con Id " + data.records[0].id + " con mismo nombre y RFC.",
                            autoClose: false
                        });
                        errors['rfc_c'] = errors['rfc_c'] || {};
                        errors['rfc_c'].required = true;

                        errors['primernombre_c'] = errors['primernombre_c'] || {};
                        errors['primernombre_c'].required = true;

                        errors['apellidopaterno_c'] = errors['apellidopaterno_c'] || {};
                        errors['apellidopaterno_c'].required = true;

                        errors['apellidomaterno_c'] = errors['apellidomaterno_c'] || {};
                        errors['apellidomaterno_c'].required = true;

                    }

                }, this)
            });
        }
        callback(null, fields, errors);
    },

    _doValidateMayoriadeEdad: function (fields, errors, callback) {
        if (this.model.get('tipodepersona_c') != 'Persona Moral' && this.model.get('tipo_registro_cuenta_c') != '4') {
            var nacimiento = new Date(this.model.get("fechadenacimiento_c"));
            var enteredAge = this.getAge(nacimiento);
            if (enteredAge < 18) {
                app.alert.show("fechaDeNacimientoCheck", {
                    level: "error",
                    title: "Persona debe de ser mayor de 18 a�os.",
                    autoClose: false
                });
                errors['fechadenacimiento_c'] = errors['fechadenacimiento_c'] || {};
                errors['fechadenacimiento_c'].required = true;
            }
        }

        callback(null, fields, errors);
    },

    _doValidateSoloTexto: function (fields, errors, callback) {
        if (this.model.get('tipodepersona_c') != 'Persona Moral') {
            var nacimiento = new Date(this.model.get("fechadenacimiento_c"));
            var enteredAge = this.getAge(nacimiento);
            if (enteredAge < 18) {
                app.alert.show("fechaDeNacimientoCheck", {
                    level: "error",
                    title: "Persona debe de ser mayor de 18 a�os.",
                    autoClose: false
                });
                errors['fechadenacimiento_c'] = errors['fechadenacimiento_c'] || {};
                errors['fechadenacimiento_c'].required = true;
            }
        }
        callback(null, fields, errors);
    },

    getAge: function (DOB) {
        var today = new Date();
        var birthDate = new Date(DOB);
        var age = today.getFullYear() - birthDate.getFullYear();
        var m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        return age;
    },

    _doValidateWSRFC: function () {
        var firmoParams = {
            'fechadenacimiento': this.model.get("fechadenacimiento_c"),
            'primernombre': this.model.get("primernombre_c"),
            'apellidoP': this.model.get("apellidopaterno_c"),
            'apellidoM': this.model.get("apellidomaterno_c"),
            'genero': this.model.get("genero_c"),
            'pais': this.model.get("pais_nacimiento_c"),
            'estado': this.model.get("estado_nacimiento_c"),
            'razonsocial': this.model.get("razonsocial_c"),
            'tipodepersona': this.model.get("tipodepersona_c"),
            'fechaconstitutiva': this.model.get("fechaconstitutiva_c")
        };

        var dnbProfileUrl = app.api.buildURL("Accounts/ValidarRFC", '', {}, {});
        app.api.call("create", dnbProfileUrl, { rfcdata: firmoParams }, {
            success: _.bind(function (data) {
                if (data != null) {
                    var rfc = this.model.get('rfc_c');
                    //Obtiene el resultado del WS dependiendo del regimen de la persona
                    if (this.model.get('tipodepersona_c') != 'Persona Moral') {
                        var rfc_SinHomoclave = (data['UNI2_CTE_02_CreaRfcPersonaFisicaResult']['resultado'] ?
                            data['UNI2_CTE_02_CreaRfcPersonaFisicaResult']['rfcGenerado'] : "");
                        var rfc_local = (data['UNI2_CTE_02_CreaRfcPersonaFisicaResult']['resultado'] ?
                            data['UNI2_CTE_02_CreaRfcPersonaFisicaResult']['rfcGenerado'] + data['UNI2_CTE_02_CreaRfcPersonaFisicaResult']['homoClaveDV'] : "");
                    } else if (this.model.get("tipodepersona_c") == 'Persona Moral') {
                        var rfc_SinHomoclave = (data['UNI2_CTE_03_CreaRfcPersonaMoralResult']['resultado'] ?
                            data['UNI2_CTE_03_CreaRfcPersonaMoralResult']['rfcGenerado'] : "");
                        var rfc_local = (data['UNI2_CTE_03_CreaRfcPersonaMoralResult']['resultado'] ?
                            data['UNI2_CTE_03_CreaRfcPersonaMoralResult']['rfcGenerado'] + data['UNI2_CTE_03_CreaRfcPersonaMoralResult']['homoClaveDV'] : "");
                    }

                    if (rfc != "" && rfc != null) {
                        rfc = (this.model.get("tipodepersona_c") != 'Persona Moral' ? rfc.substring(0, 10) : rfc.substring(0, 9));
                        if (rfc != rfc_SinHomoclave) {
                            app.alert.show("Validar RFC", {
                                level: "confirmation",
                                messages: "El RFC calculado es diferente al escrito, �Desea reemplazarlo?",
                                autoClose: false,

                                onConfirm: function () {
                                    console.log("*** JSR *** el rfc se remplazo con �xito CONFIRMED");
                                    self.model.set("rfc_c", rfc_local);
                                },
                                onCancel: function () {
                                    console.log("*** JSR *** no se modific� el RFC");
                                    //alert("Cancelled!");
                                }
                            });
                        } else {
                            app.alert.show("RFC correcto", {
                                level: "error",
                                title: "El RFC capturado actualmente es correcto",
                                autoClose: true
                            });
                        }
                    } else {
                        console.log("*** JSR *** el rfc est� vacio");
                        this.model.set("rfc_c", rfc_local);
                    }
                }
            }, this)
        });
        //callback(null, fields, errors);
    },

    _doGenera_RFC_CURP: function () {
        if (this.model.get('pais_nacimiento_c') != 2 && this.model.get('pais_nacimiento_c') != '' && this.model.get('pais_nacimiento_c') != null
            && (this.model.get('tipo_registro_cuenta_c') != '2' || this.model.get('estatus_c') != 'Interesado')) {
            if (this.model.get('tipodepersona_c') != 'Persona Moral') {
                this.model.set('rfc_c', 'XXXX010101XXX');
            } else {
                this.model.set('rfc_c', 'XXX010101XXX');
            }
        } else {
            if (this.model.get('tipodepersona_c') != 'Persona Moral') {
                if (this.model.get('fechadenacimiento_c') != null && this.model.get('fechadenacimiento_c') != '' && this.model.get('primernombre_c') != null && this.model.get('primernombre_c') != ''
                    && this.model.get('apellidopaterno_c') != null && this.model.get('apellidopaterno_c') != '' && this.model.get('apellidomaterno_c') != null && this.model.get('apellidomaterno_c') != '') {
                    this._doValidateWSRFC();
                } else {
                    app.alert.show("Generar RFC", {
                        level: "error",
                        title: "Faltan datos para poder generar el RFC",
                        autoClose: true
                    });
                }
            } else {
                if (this.model.get('razonsocial_c') != null && this.model.get('fechaconstitutiva_c') != null) {
                    this._doValidateWSRFC();
                } else {
                    app.alert.show("Generar RFC", {
                        level: "error",
                        title: "Faltan datos para poder generar el RFC",
                        autoClose: true
                    });
                }
            }
        }
    },

    //Evento no acepta numeros, solo letras (a-z).
    checkTextOnly: function () {
        app.alert.dismiss('Error_validacion_Campos');
        var camponame = "";
        var expresion = new RegExp(/^[a-zA-ZÀ-ÿ\s]*$/g);
        if (this.model.get('primernombre_c') != "" && this.model.get('primernombre_c') != undefined) {
            var nombre = this.model.get('primernombre_c');
            var comprueba = expresion.test(nombre);
            if (comprueba != true) {
                camponame = camponame + '<b>-Primer Nombre<br></b>';;
            }
        }
        if (this.model.get('apellidopaterno_c') != "" && this.model.get('apellidopaterno_c') != undefined) {
            var apaterno = this.model.get('apellidopaterno_c');
            var expresion = new RegExp(/^[a-zA-ZÀ-ÿ\s]*$/g);
            var validaap = expresion.test(apaterno);
            if (validaap != true) {
                camponame = camponame + '<b>-Apellido Paterno<br></b>';;
            }
        }
        if (this.model.get('apellidomaterno_c') != "" && this.model.get('apellidomaterno_c') != undefined) {
            var amaterno = this.model.get('apellidomaterno_c');
            var expresion = new RegExp(/^[a-zA-ZÀ-ÿ\s]*$/g);
            var validaam = expresion.test(amaterno);
            if (validaam != true) {
                camponame = camponame + '<b>-Apellido Materno<br></b>';;
            }
        }
        if (camponame) {
            app.alert.show("Error_validacion_Campos", {
                level: "error",
                messages: 'Los siguientes campos no permiten caracteres especiales:<br>' + camponame,
                autoClose: false
            });
        }
    },

    checkTextAndNum: function () {
        //Modificacion a validacion del campo, debe cumplir un formato.
        app.alert.dismiss('Error_validacion_Passport');
        var campoPass = "";
        var expresion = new RegExp(/^[0-9a-zA-Z]+$/g);
        if (this.model.get('ifepasaporte_c') != "" && this.model.get('ifepasaporte_c') != undefined) {
            var nombre = this.model.get('ifepasaporte_c');
            var comprueba = expresion.test(nombre);
            if (comprueba != true) {
                campoPass = campoPass + '<b>-IFE/Pasaporte<br></b>';
            }
        }
        if (this.model.get('curp_c') != "" && this.model.get('curp_c') != undefined) {
            var expresionC = new RegExp(/^[0-9a-zA-Z]+$/g);
            var curp = this.model.get('curp_c');
            var comprueba = expresionC.test(curp);
            if (comprueba != true) {
                campoPass = campoPass + '<b>-CURP<br></b>';
            }
        }
        if (campoPass) {
            app.alert.show("Error_validacion_Passport", {
                level: "error",
                messages: 'Los siguientes campos no permiten el ingreso de caracteres especiales:<br>' + campoPass,
                autoClose: false
            });
        }
    },

    _doValidateProfesionRisk: function () {
        if (!_.isEmpty(this.model.get("profesion_c")) || this.model.get("profesion_c") != null) {
            this.model.set("riesgo_c", "Bajo");
            var profesionActual = this.model.get("profesion_c");
            var profesiones_de_riesgo = app.lang.getAppListStrings('profesion_riesgo_list');
            Object.keys(profesiones_de_riesgo).forEach(function (key) {
                if (key == profesionActual) {
                    self.model.set("riesgo_c", "Alto");
                }
            });
        }
    },

    fechaMenor1900: function (fields, errors, callback) {
        var nacimiento = new Date(this.model.get("fechadenacimiento_c"));
        var year = nacimiento.getFullYear();
        if (year <= 1900) {
            app.alert.show("fechaDeNacimientoCheck", {
                level: "error",
                title: "La fecha de nacimiento no puede ser menor a 1900",
                autoClose: false
            });
            errors['fechadenacimiento_c'] = errors['fechadenacimiento_c'] || {};
            errors['fechadenacimiento_c'].required = true;
        }


        callback(null, fields, errors);
    },

    doValidateDateNac: function (fields, errors, callback) {
        /* if  date not empty, then check with today date and return error */
        if (!_.isEmpty(this.model.get('fechadenacimiento_c'))) {

            var fecnac_date = new Date(this.model.get('fechadenacimiento_c'));
            var today_date = new Date();

            if (fecnac_date > today_date) {

                app.alert.show("fechaDeNacimientoValidate", {
                    level: "error",
                    title: "La fecha de nacimiento no puede ser mayor al d\u00EDa de hoy",
                    autoClose: false
                });
                errors['fechadenacimiento_c'] = errors['fechadenacimiento_c'] || {};
                //errors['fechaapertura'] = 'La fecha de apertura no puede ser posterior al d�a de hoy' || {};
                errors['fechadenacimiento_c'].required = true;
            }
        }
        callback(null, fields, errors);
    },

    doValidateDateCons: function (fields, errors, callback) {
        /* if  date not empty, then check with today date and return error */
        if (!_.isEmpty(this.model.get('fechaconstitutiva_c'))) {

            var feccons_date = new Date(this.model.get('fechaconstitutiva_c'));
            var today_date = new Date();

            if (feccons_date > today_date) {

                app.alert.show("fechaDeConsValidate", {
                    level: "error",
                    title: "La fecha constitutiva no puede ser mayor al d\u00EDa de hoy",
                    autoClose: false
                });

                errors['fechaconstitutiva_c'] = errors['fechaconstitutiva_c'] || {};
                //errors['fechaapertura'] = 'La fecha de apertura no puede ser posterior al d�a de hoy' || {};
                errors['fechaconstitutiva_c'].required = true;
            }
        }
        callback(null, fields, errors);
    },

    /*
    validaExtranjerosRFC: function (){
        if((this.model.get('pais_nacimiento_c')!=2 && this.model.get('pais_nacimiento_c')!="") && (this.model.get('tipo_registro_cuenta_c') != 'Prospecto' && this.model.get('tipo_registro_cuenta_c') != 'Persona')){
            this.model.set('rfc_c','XXX010101XXX');
        }
        if(this.model.get('tipo_registro_cuenta_c') == 'Prospecto' && this.model.get('estatus_c') == 'Interesado' && this.model.get('pais_nacimiento_c')!=2){
            this.model.set('rfc_c','XXX010101XXX');
        }
    },
    */

    validaFechaNacimientoDesdeRFC: function () {
        //this._doValidateRFC();
        var RFC = this.model.get('rfc_c');
        if (RFC != '' && RFC != null && RFC != 'XXX010101XXX') {
            console.log(this.model.get('rfc_c'));
            var expReg = "";
            var tipoControl = "";
            var fecha = "";
            if (this.model.get('tipodepersona_c') != 'Persona Moral') {
                tipoControl = 'fechadenacimiento_c';
                expReg = "[A-Z&]{4}[0-9]{6}[A-Z0-9]{3}";
                fecha = new Date(RFC.substring(6, 8) + "-" + RFC.substring(8, 10) + "-" + RFC.substring(4, 6));
            } else {
                tipoControl = 'fechaconstitutiva_c';
                expReg = "[A-Z&]{3}[0-9]{6}[A-Z0-9]{3}";
                fecha = new Date(RFC.substring(5, 7) + "-" + RFC.substring(7, 9) + "-" + RFC.substring(3, 5));
            }
            if (!RFC.match(expReg)) {
                app.alert.show("RFC incorrecto", {
                    level: "error",
                    title: "El RFC no tiene un formato correcto para ser evaluado",
                    autoClose: true
                });
            } else {
                var fechaFormateada = fecha.getFullYear() + "-" + (fecha.getMonth() < 10 ? "0" + (fecha.getMonth() + 1) : fecha.getMonth() + 1) + "-" + (fecha.getDate() < 10 ? "0" + fecha.getDate() : fecha.getDate());
                this.model.set(tipoControl, fechaFormateada);
            }


        }
    },
    /**
     * @author Carlos Zaragoza Ortiz
     * @date 16-10-2015
     * Al ser proveedor debe solicitar como obligatorio el tipo de proveedor
     * @type function
     * */
    validaProveedorRequerido: function (fields, errors, callback) {
        if (this.model.get('tipo_registro_cuenta_c') == '5') {
            var tipoProveedor = new String(this.model.get('tipo_proveedor_c'));
            if (tipoProveedor.length == 0) {
                /*app.alert.show("Proveedor Requerido", {
                    level: "error",
                    title: "Debe seleccionar un un tipo de proveedor al menos",
                    autoClose: false
                });*/
                errors['tipo_proveedor_c'] = errors['tipo_proveedor_c'] || {};
                errors['tipo_proveedor_c'].required = true;
            }
        }
        callback(null, fields, errors);
    },
    /* END */

    /**
     * @author Salvador Lopez Balleza
     * @date 13/03/2018
     * Establecer campo phone_office con la misma informaci�n que el campo personalizado account_telefonos
     * */
    setPhoneOffice: function () {

        if (!_.isEmpty(this.oTelefonos.telefono)) {
            var telefono = this.oTelefonos.telefono;
            for (var i = 0; i < telefono.length; i++) {
                if (telefono[i].principal) {
                    this.model.set('phone_office', "" + telefono[i].telefono);
                }
            }
        }
    },

    /**
     * @author Carlos Zaragoza Ortiz
     * @date 16-10-2015
     * UNIFIN TASK: Modificar el riesgo en caso de seleccionar "PEP" en cuestionario de PLD
     * */
    cambiaRiesgodePersona: function (fields, errors, callback) {
        var riesgo = this.model.get('ctpldpoliticamenteexpuesto_c') == true ? 'Alto' : 'Bajo';
        this.model.set("riesgo_c", riesgo);
        console.log(this.model.get('ctpldpoliticamenteexpuesto_c'));
        console.log(this.model.get('riesgo_c'));
        callback(null, fields, errors);
    },

    /*
      AF- 2018-08-06
      Validación en relaciones tipo persona: Referenciado Cliente/Proveedor
    */
    _doValidateEdoCivil: function (fields, errors, callback) {
        if (this.model.get('tipo_registro_cuenta_c') == '4') {
            var relaciones = this.model.get('tipo_relacion_c');
            relaciones = relaciones.toString();
            relaciones = relaciones.replace(/Referencia Cliente/g, "");
            relaciones = relaciones.replace(/Referencia Proveedor/g, "");
            relaciones = relaciones.replace(/Propietario Real/g, "");
            relaciones = relaciones.replace(/Contacto/g, "");
            relaciones = relaciones.replace(/Proveedor de Recursos L/g, "");
            relaciones = relaciones.replace(/Proveedor de Recursos F/g, "");
            relaciones = relaciones.replace(/Proveedor de Recursos CA/g, "");
            relaciones = relaciones.replace(/Proveedor de Recursos CS/g, "");
            relaciones = relaciones.replace(/^/g, "");
            relaciones = relaciones.replace(/,/g, "");
            relaciones = relaciones.replace(/ /g, "");
/*          if (relaciones != "") {
              if ((this.model.get('estadocivil_c') == "" || this.model.get('estadocivil_c') == null) && this.model.get('tipodepersona_c') != 'Persona Moral') {
                  errors['estadocivil_c'] = errors['estadocivil_c'] || {};
                  errors['estadocivil_c'].required = true;
              }
            }*/
        }
        callback(null, fields, errors);
    },

    valida_requeridos: function (fields, errors, callback) {
		var seguros = 0;
        for (var i = 0; i < app.user.attributes.roles.length; i++) {
            if (app.user.attributes.roles[i] == "Seguros") seguros = 1;
        }
		if(!seguros) {
			var campos = "";
			_.each(errors, function (value, key) {
                //SE OMITE APELLIDO MATERNO COMO REQUERIDO
                if (key === 'apellidomaterno_c') {
                    delete errors[key];
                    return;
                }                
				_.each(this.model.fields, function (field) {                    
					if (_.isEqual(field.name, key)) {
						if (field.vname) {
							campos = campos + '<b>' + app.lang.get(field.vname, "Accounts") + '</b><br>';
						}
					}
				}, this);
			}, this);

			if (this.model.get('tipo_relacion_c').includes('Propietario Real')) {
				if (errors.error1AP) {
					campos = campos + '<b>' + 'Pregunta 1 Arrendamiento Puro' + '</b><br>';
				}
				if (errors.error2AP) {
					campos = campos + '<b>' + 'Pregunta 2 Arrendamiento Puro' + '</b><br>';
				}
				if (errors.error3FF) {
					campos = campos + '<b>' + 'Pregunta 1 Factoraje Financiero' + '</b><br>';
				}
				if (errors.error4FF) {
					campos = campos + '<b>' + 'Pregunta 2 Factoraje Financiero' + '</b><br>';
				}
				if (errors.error5CA) {
					campos = campos + '<b>' + 'Pregunta 1 Crédito Automotriz' + '</b><br>';
				}
				if (errors.error6CA) {
					campos = campos + '<b>' + 'Pregunta 2 Crédito Automotriz' + '</b><br>';
				}
				if (errors.account_direcciones) {
					campos = campos.replace("Direcciones", "Dirección");
				}
				if (errors.account_telefonos) {
					campos = campos.replace("Telefonos", "Teléfono");
				}
			} else {
				campos = campos.replace("<b>Telefonos</b><br>", "");
				campos = campos.replace("<b>Direcciones</b><br>", "");
				campos = campos.replace("<b>Dirección de Correo Electrónico</b><br>", "");
			}

			if (campos) {
				app.alert.show("Campos Requeridos", {
					level: "error",
					messages: "Hace falta completar la siguiente información en la <b>Cuenta:</b><br>" + campos,
					autoClose: false
				});
			}
		}
        callback(null, fields, errors);
    },

    saveProdPLD: function (fields, errors, callback) {
        if (this.model.get('tipo_registro_cuenta_c') != '5' && (this.model.get('tipo_registro_cuenta_c') != '4' || (this.model.get('tipo_registro_cuenta_c') == '4' && this.model.get('tipo_relacion_c').includes('Propietario Real')))) {
            // Actualizar modelo de pld.ProductosPLD
            var ProductosPLD = {
                'arrendamientoPuro': {},
                'factorajeFinanciero': {},
                'creditoAutomotriz': {},
                'creditoSimple': {},
                'creditoRevolvente': {}
            };
            // ProductosPLD.arrendamientoPuro.campo1 = this.$('.campo1txt-ap').val();
            ProductosPLD.arrendamientoPuro.campo2 = this.$('.campo2ddw-ap').select2('val');
            ProductosPLD.arrendamientoPuro.campo3 = this.$('.campo3rel-ap')[0]['innerText'];
            ProductosPLD.arrendamientoPuro.campo3_id = this.$('.campo3rel-ap').select2('val');
            ProductosPLD.arrendamientoPuro.campo4 = this.$('.campo4ddw-ap').select2('val');
            ProductosPLD.arrendamientoPuro.campo5 = this.$('.campo5rel-ap')[0]['innerText'];
            ProductosPLD.arrendamientoPuro.campo5_id = this.$('.campo5rel-ap').select2('val');
            ProductosPLD.arrendamientoPuro.campo6 = this.$('.campo6ddw-ap').select2('val');
            // ProductosPLD.arrendamientoPuro.campo7 = this.$('.campo7ddw-ap').select2('val');
            // ProductosPLD.arrendamientoPuro.campo8 = this.$('.campo8txt-ap').val();
            // ProductosPLD.arrendamientoPuro.campo9 = this.$('.campo9ddw-ap').select2('val');
            // ProductosPLD.arrendamientoPuro.campo10 = this.$('.campo10txt-ap').val();
            ProductosPLD.arrendamientoPuro.campo11 = this.$('.campo11ddw-ap').select2('val');
            //ProductosPLD.arrendamientoPuro.campo13 = this.$('.campo13chk-ap')[0].checked;
            ProductosPLD.arrendamientoPuro.campo14 = this.$('.campo14chk-ap')[0].checked;
            ProductosPLD.arrendamientoPuro.campo16 = this.$('.campo16ddw-ap').select2('val').toString();
            ProductosPLD.arrendamientoPuro.campo17 = this.$('.campo17txt-ap').val();
            ProductosPLD.arrendamientoPuro.campo25 = this.$('.campo25ddw-ap').select2('val');
            ProductosPLD.arrendamientoPuro.campo26 = this.$('.campo26txt-ap').val();
            // ProductosPLD.factorajeFinanciero.campo1 = this.$('.campo1txt-ff').val();
            ProductosPLD.factorajeFinanciero.campo2 = this.$('.campo2ddw-ff').select2('val');
            ProductosPLD.factorajeFinanciero.campo3 = this.$('.campo3rel-ff').val();
            ProductosPLD.factorajeFinanciero.campo3_id = this.$('.campo3rel-ff').select2('val');
            ProductosPLD.factorajeFinanciero.campo4 = this.$('.campo4ddw-ff').select2('val');
            ProductosPLD.factorajeFinanciero.campo5 = this.$('.campo5rel-ff').val();
            ProductosPLD.factorajeFinanciero.campo5_id = this.$('.campo5rel-ff').select2('val');
            ProductosPLD.factorajeFinanciero.campo21 = this.$('.campo21ddw-ff').select2('val');
            ProductosPLD.factorajeFinanciero.campo22 = this.$('.campo22int-ff').val();
            ProductosPLD.factorajeFinanciero.campo23 = this.$('.campo23dec-ff').val();
            ProductosPLD.factorajeFinanciero.campo16 = this.$('.campo16ddw-ff').select2('val').toString();
            ProductosPLD.factorajeFinanciero.campo17 = this.$('.campo17txt-ff').val();
            ProductosPLD.factorajeFinanciero.campo14 = this.$('.campo14chk-ff')[0].checked;
            ProductosPLD.factorajeFinanciero.campo24 = this.$('.campo24ddw-ff').select2('val');
            ProductosPLD.factorajeFinanciero.campo6 = this.$('.campo6ddw-ff').select2('val');
            //  ProductosPLD.creditoAutomotriz.campo1 = this.$('.campo1txt-ca').val();
            ProductosPLD.creditoAutomotriz.campo2 = this.$('.campo2ddw-ca').select2('val');
            ProductosPLD.creditoAutomotriz.campo3 = this.$('.campo3rel-ca').val();
            ProductosPLD.creditoAutomotriz.campo3_id = this.$('.campo3rel-ca').select2('val');
            ProductosPLD.creditoAutomotriz.campo4 = this.$('.campo4ddw-ca').select2('val');
            ProductosPLD.creditoAutomotriz.campo5 = this.$('.campo5rel-ca').val();
            ProductosPLD.creditoAutomotriz.campo5_id = this.$('.campo5rel-ca').select2('val');
            ProductosPLD.creditoAutomotriz.campo6 = this.$('.campo6ddw-ca').select2('val');
            // ProductosPLD.creditoSimple.campo1 = this.$('.campo1txt-cs').val();
            ProductosPLD.creditoSimple.campo2 = this.$('.campo2ddw-cs').select2('val');
            ProductosPLD.creditoSimple.campo3 = this.$('.campo3rel-cs').val();
            ProductosPLD.creditoSimple.campo3_id = this.$('.campo3rel-cs').select2('val');
            ProductosPLD.creditoSimple.campo4 = this.$('.campo4ddw-cs').select2('val');
            ProductosPLD.creditoSimple.campo5 = this.$('.campo5rel-cs').val();
            ProductosPLD.creditoSimple.campo5_id = this.$('.campo5rel-cs').select2('val');
            ProductosPLD.creditoSimple.campo18 = this.$('.campo18ddw-cs').select2('val').toString();
            ProductosPLD.creditoSimple.campo19 = this.$('.campo19txt-cs').val();
            ProductosPLD.creditoSimple.campo14 = this.$('.campo14chk-cs')[0].checked;
            ProductosPLD.creditoSimple.campo20 = this.$('.campo20ddw-cs').select2('val');
            ProductosPLD.creditoSimple.campo6 = this.$('.campo6ddw-cs').select2('val');
            //Campos Credito Revolvente
            ProductosPLD.creditoRevolvente.campo1=$('.campo1int-ce').val();
            ProductosPLD.creditoRevolvente.campo2=$('.campo2dec-ce').val().replace(/,/gi, "");
            ProductosPLD.creditoRevolvente.campo3=$('.campo3ddw-ce').select2('val').toString();
            ProductosPLD.creditoRevolvente.campo5=$('.campo5ddw-ce').select2('val');
            ProductosPLD.creditoRevolvente.campo6=$('.campo6ddw-ce').select2('val');
            ProductosPLD.creditoRevolvente.campo7=$('.campo7ddw-ce').select2('val').toString();
            ProductosPLD.creditoRevolvente.campo8=$('.campo8ddw-ce').select2('val');
            ProductosPLD.creditoRevolvente.campo9=$('.campo9rel-ce').select2('val');
            ProductosPLD.creditoRevolvente.campo9_id=$('.campo9rel-ce').select2('val');
            ProductosPLD.creditoRevolvente.campo10=$('.campo10ddw-ce').select2('val');
            ProductosPLD.creditoRevolvente.campo11=$('.campo11rel-ce').select2('val');
            ProductosPLD.creditoRevolvente.campo11_id=$('.campo11rel-ce').select2('val');

            if ($.isEmptyObject(errors)) {
                contexto_cuenta.ProductosPLD = pld.formatDetailPLD(ProductosPLD);
                this.model.set('tct_nuevo_pld_c', JSON.stringify(ProductosPLD));
                this.ProductosPLD = ProductosPLD;
                pld.ProductosPLD = this.ProductosPLD;
                pld.render();
            }
        }
        callback(null, fields, errors);
    },

    //@Jesus Carrillo
    validatelefonosexisting: function (fields, errors, callback) {
        var msjError = "";
        var msjErrorT = "";
        var telefono = this.oTelefonos.telefono;
        for (iTelefono = 0; iTelefono < telefono.length; iTelefono++) {
            //Valida valor
            valor4 = telefono[iTelefono].telefono.trim();
            if (valor4 == "") {
                msjError += '<br>-Teléfono vacío';
            } else {
                //Valida númerico
                var valNumerico = /^\d+$/;
                if (!valNumerico.test(valor4)) {
                    msjError += '<br>-Solo números son permitidos';
                }
                //Valida longitud
                if (valor4.length < 8) {
                    msjError += '<br>-Debe contener 8 o más dígitos';
                }
                //Valida números repetidos
                if (valor4.length > 1) {
                    var repetido = true;
                    for (var iValor4 = 0; iValor4 < valor4.length; iValor4++) {
                        repetido = (valor4[0] != valor4[iValor4]) ? false : repetido;
                    }
                    if (repetido) {
                        msjError += '<br>-Caracter repetido';
                    }
                }
            }
            // Agerga teléfono a mensaje de error
            if (msjError != "") {
                msjErrorT += '<br><b>' + valor4 + '</b> :' + msjError + '<br>';
                $('.Telefonot').eq(iTelefono).css('border-color', 'red');
            }
            msjError = "";
        }
        //Muestra errores
        if (msjErrorT != "") {
            app.alert.show('phone_save_error', {
                level: 'error',
                autoClose: false,
                messages: 'Formato de teléfono(s) incorrecto:' + msjErrorT
            });
            //Agrega errores
            errors['Tel_Telefonos_numero'] = errors['Tel_Telefonos_numero'] || {};
            errors['Tel_Telefonos_numero'].required = true;
        }

        //Valida duplicados
        if (telefono.length > 0) {
            var coincidencia = 0;
            var indices = [];
            for (var i = 0; i < telefono.length; i++) {
                for (var j = 0; j < telefono.length; j++) {
                    if (telefono[j].telefono == telefono[i].telefono && telefono[j].estatus == 'Activo' && telefono[i].estatus == 'Activo' && i != j) {
                        coincidencia++;
                        indices.push(i);
                        indices.push(j);
                    }
                }
            }
            //indices=indices.unique();
            if (coincidencia > 0) {
                app.alert.show('error_sametelefono3', {
                    level: 'error',
                    autoClose: false,
                    messages: 'Existen n\u00FAmeros telef\u00F3nicos iguales, favor de corregir.'
                });
                //$($input).focus();
                if (indices.length > 0) {
                    for (var i = 0; i < indices.length; i++) {
                        $('.Telefonot').eq(indices[i]).css('border-color', 'red');
                    }
                }
                errors['Tel_Telefonos_duplicado'] = errors['Tel_Telefonos_duplicado'] || {};
                errors['Tel_Telefonos_duplicado'].required = true;
            }
        }
        callback(null, fields, errors);
    },

    requeridosPropietarioReal: function (fields, errors, callback) {
        var reqpropreal = "";
        var productos = App.user.attributes.productos_c;
        if (this.model.get('tipodepersona_c') != "Persona Moral" && this.model.get('tipo_relacion_c').includes('Propietario Real')) {
            if (this.model.get('primernombre_c') == "" || this.model.get('primernombre_c') == undefined) {
                errors['primernombre_c'] = errors['primernombre_c'] || {};
                errors['primernombre_c'].required = true;
            }
            if (this.model.get('apellidopaterno_c') == "" || this.model.get('apellidopaterno_c') == undefined) {
                errors['apellidopaterno_c'] = errors['apellidopaterno_c'] || {};
                errors['apellidopaterno_c'].required = true;
            }
            // if (this.model.get('apellidomaterno_c') == "" || this.model.get('apellidomaterno_c') == undefined) {
            //     errors['apellidomaterno_c'] = errors['apellidomaterno_c'] || {};
            //     errors['apellidomaterno_c'].required = true;
            // }
            if (this.model.get('genero_c') == "" || this.model.get('genero_c') == undefined) {
                errors['genero_c'] = errors['genero_c'] || {};
                errors['genero_c'].required = true;
            }
            if (this.model.get('fechadenacimiento_c') == "" || this.model.get('fechadenacimiento_c') == undefined) {
                errors['fechadenacimiento_c'] = errors['fechadenacimiento_c'] || {};
                errors['fechadenacimiento_c'].required = true;
            }
            if (this.model.get('pais_nacimiento_c') == "" || this.model.get('pais_nacimiento_c') == undefined) {
                errors['pais_nacimiento_c'] = errors['pais_nacimiento_c'] || {};
                errors['pais_nacimiento_c'].required = true;
            }
            if (this.model.get('estado_nacimiento_c') == "" || this.model.get('estado_nacimiento_c') == undefined) {
                errors['estado_nacimiento_c'] = errors['estado_nacimiento_c'] || {};
                errors['estado_nacimiento_c'].required = true;
            }
            if (this.model.get('nacionalidad_c') == "" || this.model.get('nacionalidad_c') == undefined || this.model.get('nacionalidad_c') == "0") {
                errors['nacionalidad_c'] = errors['nacionalidad_c'] || {};
                errors['nacionalidad_c'].required = true;
            }
            //Validacion Actividad Economica custom
            /*if ($('.list_ae').select2('val') == "0" || $('.list_ae').select2('val') == "" || $('.list_ae').select2('val') == undefined) {
                errors['actividadeconomica_c'] = errors['actividadeconomica_c'] || {};
                errors['actividadeconomica_c'].required = true;
            }*/
            //Requerido Subsector custom
            // if ($('.list_sse').select2('val') == "" || $('.list_sse').select2('val') == undefined) {
            //     errors['subsectoreconomico_c'] = errors['subsectoreconomico_c'] || {};
            //     errors['subsectoreconomico_c'].required = true;
            // }
            // //Requerido Sector custom
            // if ($('.list_se').select2('val') == "" || $('.list_se').select2('val') == undefined) {
            //     errors['sectoreconomico_c'] = errors['sectoreconomico_c'] || {};
            //     errors['sectoreconomico_c'].required = true;
            // }
            // //Requerido macro sector custom
            // if ($('.list_ms').select2('val') == "" || $('.list_ms').select2('val') == undefined) {
            //     errors['tct_macro_sector_ddw_c'] = errors['tct_macro_sector_ddw_c'] || {};
            //     errors['tct_macro_sector_ddw_c'].required = true;
            // }
            if (this.oTelefonos.telefono.length == 0) {
                errors['account_telefonos'] = errors['account_telefonos'] || {};
                errors['account_telefonos'].required = true;
            }
            if (this.oDirecciones.direccion.length == 0) {
                errors['account_direcciones'] = errors['account_direcciones'] || {};
                errors['account_direcciones'].required = true;
            }
            if (productos.includes("1")) {
                if (this.$('.campo2ddw-ap').select2('val') == "" || this.$('.campo2ddw-ap').select2('val') == null) {
                    $('.campo2ddw-ap').find('.select2-choice').css('border-color', 'red');
                    errors['error1AP'] = errors['Pregunta 1 Arrendamiento Puro'] || {};
                    errors['error1AP'].required = true;
                }
                if (this.$('.campo4ddw-ap').select2('val') == "" || this.$('.campo4ddw-ap').select2('val') == null) {
                    $('.campo4ddw-ap').find('.select2-choice').css('border-color', 'red');
                    errors['error2AP'] = errors['Pregunta 2 Arrendamiento Puro'] || {};
                    errors['error2AP'].required = true;
                }
            }
            if (productos.includes("4")) {
                if (this.$('.campo2ddw-ff').select2('val') == "" || this.$('.campo2ddw-ff').select2('val') == null) {
                    $('.campo2ddw-ff').find('.select2-choice').css('border-color', 'red');
                    errors['error3FF'] = errors['Pregunta 1 Factoraje Financiero'] || {};
                    errors['error3FF'].required = true;
                }
                if (this.$('.campo4ddw-ff').select2('val') == "" || this.$('.campo4ddw-ff').select2('val') == null) {
                    $('.campo4ddw-ff').find('.select2-choice').css('border-color', 'red');
                    errors['error4FF'] = errors['Pregunta 2 Factoraje Financiero'] || {};
                    errors['error4FF'].required = true;
                }
            }
            if (productos.includes("3")) {
                if (this.$('.campo2ddw-ca').select2('val') == "" || this.$('.campo2ddw-ca').select2('val') == null) {
                    $('.campo2ddw-ca').find('.select2-choice').css('border-color', 'red');
                    errors['error5CA'] = errors['Pregunta 1 Credito Automotriz'] || {};
                    errors['error5CA'].required = true;
                }
                if (this.$('.campo4ddw-ca').select2('val') == "" || this.$('.campo4ddw-ca').select2('val') == null) {
                    $('.campo4ddw-ca').find('.select2-choice').css('border-color', 'red');
                    errors['error6CA'] = errors['Pregunta 2 Credito Automotriz'] || {};
                    errors['error6CA'].required = true;
                }
            }

        } if (this.model.get('tipodepersona_c') == "Persona Moral" && this.model.get('tipo_relacion_c').includes('Propietario Real')) {
            app.alert.show("Es persona Moral", {
                level: "error",
                title: "Una persona moral no puede ser Propietario Real",
                autoClose: false
            });
            errors['errorcuentamoral'] = errors['errorcuentamoral'] || {};
            errors['errorcuentamoral'].required = true;
        }
        callback(null, fields, errors);

    },

    ocultaRFC: function () {
        if (this.model.get('tipo_relacion_c').includes('Proveedor de Recursos L') || this.model.get('tipo_relacion_c').includes('Proveedor de Recursos CA') || this.model.get('tipo_relacion_c').includes('Proveedor de Recursos CS') || this.model.get('tipo_relacion_c').includes('Proveedor de Recursos F')) {
            $('[data-name=tct_pais_expide_rfc_c]').show();
        }
        if (this.model.get('tct_pais_expide_rfc_c') != "2") {
            this.$('[data-name="generar_rfc_c"]').attr('style', 'pointer-events:none;');
        } else {
            this.$('[data-name="generar_rfc_c"]').attr('style', 'pointer-events:block;');
        }
    },

    RequeridosProveedorRecursos: function (fields, errors, callback) {
        var RequeridosProvRec = "";
        if (this.model.get('tipo_relacion_c').includes('Proveedor de Recursos L') || this.model.get('tipo_relacion_c').includes('Proveedor de Recursos F') || this.model.get('tipo_relacion_c').includes('Proveedor de Recursos CA')) {

            if (this.model.get('tipodepersona_c') == "Persona Fisica" || this.model.get('tipodepersona_c') == "Persona Fisica con Actividad Empresarial") {

                /*if (this.model.get('primernombre_c') == "") {
                    RequeridosProvRec = RequeridosProvRec + '<b>-Nombre<br></b>';
                }
                if (this.model.get('apellidopaterno_c') == "") {
                    RequeridosProvRec = RequeridosProvRec + '<b>-Apellido Paterno<br></b>';
                }
                if (this.model.get('apellidomaterno_c') == "" || this.model.get('apellidomaterno_c') == undefined) {
                    RequeridosProvRec = RequeridosProvRec + '<b>-Apellido Materno<br></b>';
                    $('[name=apellidomaterno_c]').css('border-color', 'red');
                }*/
                if (this.model.get('fechadenacimiento_c') == "" || this.model.get('fechadenacimiento_c') == undefined) {
                    RequeridosProvRec = RequeridosProvRec + '<b>-Fecha de Nacimiento<br></b>';
                    $('[name=fechadenacimiento_c]').css('border-color', 'red');
                }
                if (this.model.get('nacionalidad_c') == "0" || this.model.get('nacionalidad_c') == undefined) {
                    RequeridosProvRec = RequeridosProvRec + '<b>-Nacionalidad<br></b>';
                    $('[data-name=nacionalidad_c]').find('.select2-choice').css('border-color', 'red');
                }
                if (this.model.get('rfc_c') == "" || this.model.get('rfc_c') == undefined) {
                    RequeridosProvRec = RequeridosProvRec + '<b>-RFC<br></b>';
                    $('[name=rfc_c]').css('border-color', 'red');
                }
                if (this.model.get('tct_pais_expide_rfc_c') == "" || this.model.get('tct_pais_expide_rfc_c') == undefined) {
                    RequeridosProvRec = RequeridosProvRec + '<b>-País que expide el RFC o equivalente<br></b>';
                    $('[data-name=tct_pais_expide_rfc_c]').find('.select2-choice').css('border-color', 'red');
                }
                if (this.model.get('curp_c') == "" || this.model.get('curp_c') == undefined) {
                    RequeridosProvRec = RequeridosProvRec + '<b>-CURP<br></b>';
                    $('[name=curp_c]').css('border-color', 'red');
                }
                if (this.model.get('ctpldnoseriefiel_c') == "" || this.model.get('ctpldnoseriefiel_c') == undefined) {
                      RequeridosProvRec = RequeridosProvRec + '<b>-No. serie FIEL<br></b>';
                    $('[name=ctpldnoseriefiel_c]').css('border-color', 'red');
                }
                // if (this.model.get('profesion_c') == "" || this.model.get('profesion_c') == undefined) {
                //     RequeridosProvRec = RequeridosProvRec + '<b>-Profesión<br></b>';
                //     $('[data-name=profesion_c]').find('.select2-choice').css('border-color', 'red');
                // }
                //Requerido Subsector custom
                // if ($('.list_sse').select2('val') == "") {
                //     RequeridosProvRec = RequeridosProvRec + '<b>-Subsector Económico<br></b>';
                //     // $('.campoSSE').find('.record-label').css('color', 'red');
                //     // $('.list_sse').find('.select2-choice').css('border-color', 'red');
                // }
                // //Requerido Sector custom
                // if ($('.list_se').select2('val') == "") {
                //     RequeridosProvRec = RequeridosProvRec + '<b>-Sector Económico<br></b>';
                //     // $('.campoSE').find('.record-label').css('color', 'red');
                //     // $('.list_se').find('.select2-choice').css('border-color', 'red');
                // }
                // //Requerido macro sector custom
                // if ($('.list_ms').select2('val') == "") {
                //     RequeridosProvRec = RequeridosProvRec + '<b>-Macro Sector<br></b>';
                //     // $('.campoMS').find('.record-label').css('color', 'red');
                //     // $('.list_ms').find('.select2-choice').css('border-color', 'red');
                // }
                var direcciones = 0;
                var tipodireccion = this.oDirecciones.direccion;
                if (tipodireccion.length > 0) {
                    for (var i = 0; i < tipodireccion.length; i++) {
                        if (tipodireccion[i].inactivo == 0 && (tipodireccion[i].tipodedireccion.includes("1") || tipodireccion[i].tipodedireccion.includes("3") || tipodireccion[i].tipodedireccion.includes("5") || tipodireccion[i].tipodedireccion.includes("7"))) {
                            direcciones++;
                        }
                    }
                }
                if (direcciones == 0) {
                    RequeridosProvRec = RequeridosProvRec + '<b>-Dirección<br></b>';
                    $('.direcciondashlet').css('border-color', 'red');
                }
/*                if ((this.model.get('rfc_c') == undefined || this.model.get('rfc_c') == "") && (this.model.get('curp_c') == "" || this.model.get('curp_c') == undefined) && (this.model.get('ctpldnoseriefiel_c') == "" || this.model.get('ctpldnoseriefiel_c') == undefined)) {
                    RequeridosProvRec = RequeridosProvRec + '<b><br>Al menos la captura de alguno de estos campos:<br><br>-RFC<br>-CURP<br>-Firma Electrónica Avanzada<br><br></b>';
                    $('[name=rfc_c]').css('border-color', 'red');
                    $('[name=curp_c]').css('border-color', 'red');
                    $('[name=ctpldnoseriefiel_c]').css('border-color', 'red');
                }*/
                if (RequeridosProvRec != "") {
                    app.alert.show("Campos faltantes en cuenta", {
                        level: "error",
                        messages: 'Hace falta completar la siguiente información en la cuenta para una relación tipo <b>Proveedor de Recursos</b>:<br> ' + RequeridosProvRec,
                        autoClose: false
                    });
                    errors['faltantescuenta'] = errors['faltantescuenta'] || {};
                    errors['faltantescuenta'].required = true;
                }
                callback(null, fields, errors);
            }

            if (this.model.get('tipodepersona_c') == "Persona Moral") {
                if (this.model.get('fechaconstitutiva_c') == "" || this.model.get('fechaconstitutiva_c') == undefined) {
                    RequeridosProvRec = RequeridosProvRec + '<b>-Fecha Constitutiva<br></b>';
                    $('[name=fechaconstitutiva_c]').css('border-color', 'red');
                }
                if (this.model.get('nacionalidad_c') == "0" || this.model.get('nacionalidad_c') == undefined) {
                    RequeridosProvRec = RequeridosProvRec + '<b>-Nacionalidad<br></b>';
                    $('[data-name=nacionalidad_c]').find('.select2-choice').css('border-color', 'red');
                }
                if (this.model.get('rfc_c') == "" || this.model.get('rfc_c') == undefined) {
                    RequeridosProvRec = RequeridosProvRec + '<b>-RFC<br></b>';
                    $('[name=rfc_c]').css('border-color', 'red');
                }
                if (this.model.get('tct_pais_expide_rfc_c') == "" || this.model.get('tct_pais_expide_rfc_c') == undefined) {
                    RequeridosProvRec = RequeridosProvRec + '<b>-País que expide el RFC o equivalente<br></b>';
                    $('[data-name=tct_pais_expide_rfc_c]').find('.select2-choice').css('border-color', 'red');
                }
                if (this.model.get('ctpldnoseriefiel_c') == "" || this.model.get('ctpldnoseriefiel_c') == undefined) {
                    RequeridosProvRec = RequeridosProvRec + '<b>-No. serie FIEL<br></b>';
                    $('[name=ctpldnoseriefiel_c]').css('border-color', 'red');
                }
                if ($('.list_ae').select2('val') == "0" || $('.list_ae').select2('val') == "" || $('.list_ae').select2('val') == null) {
                    RequeridosProvRec = RequeridosProvRec + '<b>-Actividad Económica<br></b>';
                    $('.list_ae').find('.select2-choice').css('border-color', 'red');
                }
                var direccionesm = 0;
                var tipodireccion = this.oDirecciones.direccion;
                if (tipodireccion.length > 0) {
                    direccionesm++;
                }
                if (direccionesm == 0) {
                    RequeridosProvRec = RequeridosProvRec + '<b>-Domicilio<br></b>';
                    $('.direcciondashlet').css('border-color', 'red');
                }
                if (RequeridosProvRec != "") {
                    app.alert.show("Campos faltantes en cuenta", {
                        level: "error",
                        messages: 'Hace falta completar la siguiente información en la cuenta para una relación tipo <b>Proveedor de Recursos</b>:<br> ' + RequeridosProvRec,
                        autoClose: false
                    });
                    errors['errorpersonamoral'] = errors['errorpersonamoral'] || {};
                    errors['errorpersonamoral'].required = true;
                }
                callback(null, fields, errors);
            }
        } else {
            callback(null, fields, errors);
        }
    },

    setButtonStates: function (state) {
        this._super("setButtonStates", [state]);
        var $saveButtonEl = this.buttons[this.saveButtonName];
        if ($saveButtonEl) {
            switch (state) {
                case this.STATE.CREATE:
                case this.STATE.SELECT:
                    $saveButtonEl.getFieldElement().text(app.lang.get('LBL_SAVE_BUTTON_LABEL', this.module));
                    break;
                case this.STATE.DUPLICATE:
                    $saveButtonEl.getFieldElement().text(app.lang.get('LBL_IGNORE_DUPLICATE_AND_SAVE', this.module)).hide();
                    //OCULTANDO BOT�N CON JQUERY
                    $('[name="duplicate_button"]').hide();
                    $('[data-event="list:dupecheck-list-select-edit:fire"]').addClass("hidden");
                    break;
            }
        }
    },

    setCustomFields: function (fields, errors, callback) {
        //Teléfonos
        this.model.set('account_telefonos', this.oTelefonos.telefono);
        //Direcciones
        this.model.set('account_direcciones', this.oDirecciones.direccion);

        callback(null, fields, errors);
    },

    quitaanos: function () {
        var anoactual = ((new Date).getFullYear());
        var anoactual5 = anoactual - 5
        var lista = App.lang.getAppListStrings('ano_ventas_ddw_list');
        Object.keys(lista).forEach(function (key) {
            //Quita años previos
            if (key < anoactual5) {
                delete lista[key];
            }
            //Quita años futuros al actual
            if (key > anoactual) {
                delete lista[key];
            }
        });
        this.model.fields['tct_ano_ventas_ddw_c'].options = lista;
    },

    cleanName: function (fields, errors, callback) {
        if(_.isEmpty(errors)){
            //Recupera variables
            var postData = {
                'name': this.model.get("name").toString()
            };
            //Consume servicio
            if(this.model.get("name").trim()!='') {
                var serviceURI = app.api.buildURL("getCleanName", '', {}, {});
                App.api.call("create", serviceURI, postData, {
                    success: _.bind(function (data) {
                        if (data['status']=='200') {
                            this.model.set('clean_name', data['cleanName']);
                        }else{
                            //Error
                            app.alert.show('error_clean_name', {
                                level: 'error',
                                autoClose: false,
                                messages: data['error']
                            });
                            //Agrega errores
                            errors['clean_name'] = errors['clean_name']|| {};
                            errors['clean_name'].required = true;
                        }
                        callback(null, fields, errors);
                    }, this)
                });
            }else{
                //Error
                app.alert.show('error_clean_name', {
                    level: 'error',
                    autoClose: false,
                    messages: 'Se requiere ingresar nombre de la cuenta'
                });
                //Agrega errores
                errors['clean_name'] = errors['clean_name'] || {};
                errors['clean_name'].required = true;
                callback(null, fields, errors);
            }
        }else{
          callback(null, fields, errors);
        }
    },

    check_duplicados_account:function (fields, errors, callback) {
        
        if(Object.keys(errors).length==0 && this.options.context.flagGuardarAcc!="1"){
            var telefonos=[];
            //Obtener los telefonos
            if(this.oTelefonos.telefono.length>0){
                for (let index = 0; index < this.oTelefonos.telefono.length; index++) {
                    telefonos.push(this.oTelefonos.telefono[index].name);  
                }
            }

            var email="";
            if(this.model.attributes.email !=undefined){
                if(this.model.attributes.email.length>0){
                    email=this.model.attributes.email[0].email_address;
                }
            }

            var rfc="";
            if(this.model.get('rfc_c') != undefined && this.model.get('rfc_c') != ""){
                rfc=this.model.get('rfc_c');
            }
            
            //Parámetros para consumir servicio
            var params = {
                'nombre': this.model.get('name').toString(),
                'correo': email,
                'telefonos': telefonos,
                'rfc': rfc,
            };
            
            /*
            var params={
                //"nombre":"27 MICRAS INTERNACIONAL",
                "nombre":"GRUASDELVALLESANMARTIN",
                "correo":"GGONZALEZ@UNIFIN.COM.MX",
                "telefonos":[
                    "12345643",
                    "323232344",
                    "5579389732"
                ],
                "rfc":""
            };
            */

            var urlValidaDuplicados = app.api.buildURL("validaDuplicado", '', {}, {});
            
            App.alert.show('obteniendoDuplicados', {
                level: 'process',
                title: 'Cargando',
            });

            app.api.call("create", urlValidaDuplicados, params, {
                success: _.bind(function (data) {
                    App.alert.dismiss('obteniendoDuplicados');
                    if(data.code=='200'){
                        if(!_.isEmpty(data.registros)){
                            self.duplicados=data.registros;

                            //formateando el nivel match
                            for (var property in self.duplicados) {
                                self.duplicados[property].coincidencia= self.duplicados[property].coincidencia;
                            }
                            errors['modal_duplicadosacc'] = errors['modal_duplicadosacc'] || {};
                            errors['modal_duplicadosacc'].custom_message1 = true;

                            app.alert.show("posibles_coincidencias_acc", {
                                level: "error",
                                title: "Se han identificado posibles duplicados. Favor de validar",
                                autoClose: false
                            });

                            //Mandamos a llamar el popup custom
                            if (Modernizr.touch) {
                                app.$contentEl.addClass('content-overflow-visible');
                            }
                            /**check whether the view already exists in the layout.
                             * If not we will create a new view and will add to the components list of the record layout
                             * */
                
                            var quickCreateView = null;
                            if (!quickCreateView) {
                                /** Create a new view object */
                                quickCreateView = app.view.createView({
                                    context: this.context,
                                    errors:errors,
                                    registros:self.duplicados,
                                    name: 'ValidaDuplicadoAccModal',
                                    layout: this.layout,
                                    module: 'Accounts'
                                });
                                /** add the new view to the components list of the record layout*/
                                this.layout._components.push(quickCreateView);
                                this.layout.$el.append(quickCreateView.$el);
                            }
                            /**triggers an event to show the pop up quick create view*/
                            this.layout.trigger("app:view:ValidaDuplicadoAccModal");
                        }
                    }
                    
                    callback(null, fields, errors);
                    
                }, this)
            });


        }else{
            callback(null, fields, errors);
        }
    },

    validaformato: function (fields, errors, callback) {
        //Validacion para pasar una expresion regular por los 3 campos y verificar dicho formato.
        var errorescampos = "";
		if(this.model.get('tipodepersona_c') != 'Persona Moral'){
			if (this.model.get('primernombre_c') == undefined || this.model.get('apellidopaterno_c') == undefined || this.model.get('apellidomaterno_c') == undefined) {
				if (this.model.get('primernombre_c') == undefined) {
					errorescampos = errorescampos + '<b>-Primer Nombre<br></b>';;
					errors['primernombre_c'] = errors['primernombre_c'] || {};
					errors['primernombre_c'].required = true;
				}
				if (this.model.get('apellidopaterno_c') == undefined) {
					errorescampos = errorescampos + '<b>-Apellido Paterno<br></b>';;
					errors['apellidopaterno_c'] = errors['apellidopaterno_c'] || {};
					errors['apellidopaterno_c'].required = true;
				}
				if (this.model.get('apellidomaterno_c') == undefined) {
					errorescampos = errorescampos + '<b>-Apellido Materno<br></b>';;
					errors['apellidomaterno_c'] = errors['apellidomaterno_c'] || {};
					errors['apellidomaterno_c'].required = true;
				}
			}else if (this.model.get('primernombre_c') != "" || this.model.get('apellidopaterno_c') != "" || this.model.get('apellidomaterno_c') != "") {
				var expresion = new RegExp(/^[a-zA-ZÀ-ÿ\s]*$/g);
				if (this.model.get('primernombre_c') != "" && this.model.get('primernombre_c') != undefined) {
					var nombre = this.model.get('primernombre_c');
					var res = expresion.test(nombre);
					if (res != true) {
						errorescampos = errorescampos + '<b>-Primer Nombre<br></b>';;
						errors['primernombre_c'] = errors['primernombre_c'] || {};
						errors['primernombre_c'].required = true;
					}
				}
				if (this.model.get('apellidopaterno_c') != "" && this.model.get('apellidopaterno_c') != undefined) {
					var apaterno = this.model.get('apellidopaterno_c');
					var expresion = new RegExp(/^[a-zA-ZÀ-ÿ\s]*$/g);
					var res = expresion.test(apaterno);
					if (res != true) {
						errorescampos = errorescampos + '<b>-Apellido Paterno<br></b>';;
						errors['apellidopaterno_c'] = errors['apellidopaterno_c'] || {};
						errors['apellidopaterno_c'].required = true;
					}
				}
				if (this.model.get('apellidomaterno_c') != "" && this.model.get('apellidomaterno_c') != undefined) {
					var amaterno = this.model.get('apellidomaterno_c');
					var expresion = new RegExp(/^[a-zA-ZÀ-ÿ\s]*$/g);
					var res = expresion.test(amaterno);
					if (res != true) {
						errorescampos = errorescampos + '<b>-Apellido Materno<br></b>';;
						errors['apellidomaterno_c'] = errors['apellidomaterno_c'] || {};
						errors['apellidomaterno_c'].required = true;
					}
				}
				if (errorescampos) {
					app.alert.show("Error_validacion_Campos", {
						level: "error",
						messages: 'Los siguientes campos no permiten caracteres especiales:<br>' + errorescampos,
						autoClose: false
					});
				}
			}
		}

        callback(null, fields, errors);
    },
    validapasscurp: function (fields, errors, callback) {
        if (this.model.get('ifepasaporte_c') != "" || this.model.get('curp_c') != "") {
            var campoPass = "";
            var expresion = new RegExp(/^[0-9a-zA-Z]+$/g);
            if (this.model.get('ifepasaporte_c') != "" && this.model.get('ifepasaporte_c') != undefined) {
                var nombre = this.model.get('ifepasaporte_c');
                var comprueba = expresion.test(nombre);
                if (comprueba != true) {
                    campoPass = campoPass + '<b>-IFE/Pasaporte<br></b>';
                    errors['ifepasaporte_c'] = errors['ifepasaporte_c'] || {};
                    errors['ifepasaporte_c'].required = true;
                }
            }
            if (this.model.get('curp_c') != "" && this.model.get('curp_c') != undefined) {
                var expresionC = new RegExp(/^[0-9a-zA-Z]+$/g);
                var curp = this.model.get('curp_c');
                var comprueba = expresionC.test(curp);
                if (comprueba != true) {
                    campoPass = campoPass + '<b>-CURP<br></b>';
                    errors['curp_c'] = errors['curp_c'] || {};
                    errors['curp_c'].required = true;
                }
            }
            if (campoPass) {
                app.alert.show("Error_validacion_Passport", {
                    level: "error",
                    messages: 'Los siguientes campos no permiten caracteres especiales:<br>' + campoPass,
                    autoClose: false
                });
            }
        }
        callback(null, fields, errors);
    },

    nodigitos: function (fields, errors, callback) {
        if ($('.campo1pa').val() != "" || $('.campo2pa').val() != "" || $('.campo3pa').val() != "" || $('.campo4pa').val() != "") {
            if ($('.campo1pa').val() !== "") {
                var expreg = /^[0-9]{1,10}$/;
                var num1 = $('.campo1pa').val();
                if (!expreg.test(num1)) {
                    $('.campo1pa').css('border-color', 'red');
                    app.alert.show('error-numero-potencial1', {
                        level: 'error',
                        autoClose: false,
                        messages: "El campo <b>Número de Autos Utilitarios</b> no acepta caracteres especiales."
                    });
                    errors['campo1apPotencial'] = errors['campo1apPotencial'] || {};
                    errors['campo1apPotencial'].required = true;
                }
            }
            if ($('.campo2pa').val() !== "") {
                var expreg = /^[0-9]{1,10}$/;
                var num2 = $('.campo2pa').val();
                if (!expreg.test(num2)) {
                    $('.campo2pa').css('border-color', 'red');
                    app.alert.show('error-numero-potencial2', {
                        level: 'error',
                        autoClose: false,
                        messages: "El campo <b>Número de Autos Ejecutivos</b> no acepta caracteres especiales."
                    });
                    errors['campo2apPotencial'] = errors['campo2apPotencial'] || {};
                    errors['campo2apPotencial'].required = true;
                }
            }
            if ($('.campo3pa').val() !== "") {
                var expreg = /^[0-9]{1,10}$/;
                var num3 = $('.campo3pa').val();
                if (!expreg.test(num3)) {
                    $('.campo3pa').css('border-color', 'red');
                    app.alert.show('error-numero-potencial3', {
                        level: 'error',
                        autoClose: false,
                        messages: "El campo <b>Número de Motos</b> no acepta caracteres especiales."
                    });
                    errors['campo3apPotencial'] = errors['campo3apPotencial'] || {};
                    errors['campo3apPotencial'].required = true;
                }
            }
            if ($('.campo4pa').val() !== "") {
                var expreg = /^[0-9]{1,10}$/;
                var num4 = $('.campo4pa').val();
                if (!expreg.test(num4)) {
                    $('.campo4pa').css('border-color', 'red');
                    app.alert.show('error-numero-potencial4', {
                        level: 'error',
                        autoClose: false,
                        messages: "El campo <b>Número de Camiones</b> no acepta caracteres especiales."
                    });
                    errors['campo4apPotencial'] = errors['campo4apPotencial'] || {};
                    errors['campo4apPotencial'].required = true;
                }
            }
        }
        callback(null, fields, errors);
    },

    savepotauto: function (fields, errors, callback) {
        var PotencialAutos = {};
        PotencialAutos.autos = {};
        PotencialAutos.autos.tct_no_autos_u_int_c = this.$('.campo1pa').val();
        PotencialAutos.autos.tct_no_autos_e_int_c = this.$('.campo2pa').val();
        PotencialAutos.autos.tct_no_motos_int_c = this.$('.campo3pa').val();
        PotencialAutos.autos.tct_no_camiones_int_c = this.$('.campo4pa').val();

        if ($.isEmptyObject(errors)) {
            this.model.set('potencial_autos', JSON.stringify(PotencialAutos));
            this.Pautos = PotencialAutos;
            Pautos.render();
        }
        callback(null, fields, errors);
    },

    /*************Valida campo de Página Web*****************/
    validaPagWeb: function (fields, errors, callback) {
        var webSite = this.model.get('website');
        if (webSite != "" && webSite != undefined) {
            //var expreg = /^https?:\/\/[\w\-]+(\.[\w\-]+)+[/#?]?.$|^[\w\-]+(\.[\w\-]+)+[/#?]?.$/;
            var expreg = /^(https?:\/\/)?([\da-z\.-i][\w\-.]+)\.([\da-z\.i]{1,6})([\/\w\.=#%?-]*)*\/?$/;

            if (!expreg.test(webSite)) {

                app.alert.show('error-website', {
                    level: 'error',
                    autoClose: false,
                    messages: "El formato de <b>Página Web</b> no es valido."
                });
                errors['website'] = errors['website'] || {};
                errors['website'].required = true;
                callback(null, fields, errors);
            } else {
                app.api.call('GET', app.api.buildURL('validacion_sitio_web/?website=' + webSite), null, {
                    success: _.bind(function (data) {
                        //console.log(data);
                        if (data == "02") {
                            app.alert.show("error-website", {
                                level: "error",
                                autoClose: false,
                                messages: "El dominio ingresado en <b>Página Web</b> no existe."
                            });
                            errors['website'] = errors['website'] || {};
                            errors['website'].required = true;
                            //callback(null, fields, errors);
                        }
                        if (data == "01") {
                            app.alert.show("error-website", {
                                level: "error",
                                autoClose: false,
                                messages: "El dominio ingresado en <b>Página Web</b> no existe o no esta activa."
                            });
                            errors['website'] = errors['website'] || {};
                            errors['website'].required = true;
                            //callback(null, fields, errors);
                        }
                        callback(null, fields, errors);
                    }, this),
                });
            }
        } else {
            callback(null, fields, errors);
        }
    },

    /*************Valida Genero *****************/
    validaGenero: function (fields, errors, callback) {
        var genero = this.model.get('genero_c');
        if ((genero == "" || genero == null) && (this.model.get('tipodepersona_c') == "Persona Fisica" ||
            this.model.get('tipodepersona_c') == "Persona Fisica con Actividad Empresarial")) {
            errors['genero_c'] = errors['genero_c'] || {};
            errors['genero_c'].required = true;
            callback(null, fields, errors);
        } else {
            callback(null, fields, errors);
        }
    },

    rowebsite: function () {
        if (this.model.get('no_website_c')) {
            if (this.model.get('website')) {
                app.api.call('GET', app.api.buildURL('validacion_sitio_web/?website=' + this.model.get('website')), null, {
                    success: _.bind(function (data) {
                        if (data == "00") {
                            app.alert.show("error-website", {
                                level: "error",
                                autoClose: false,
                                messages: "La Página Web es correcta, no se puede borrar."
                            });
                            self.model.set('no_website_c', 0);
                        }
                        else {
                            self.model.set('website', '');
                            self.noEditFields.push('website');
                        }
                    }, self),
                });
            }
            $('[data-name="website"]').attr('style', 'pointer-events:none');
        }
        else {
            $('[data-name="website"]').attr('style', 'pointer-events:auto');
        }
    },
    requeridosUniclickCanal: function (fields, errors, callback) {
		var seguros = 0;
        for (var i = 0; i < app.user.attributes.roles.length; i++) {
            if (app.user.attributes.roles[i] == "Seguros") seguros = 1;
        }
		if(!seguros) {
			var faltantesUniclickCanal = 0;
			var userprod = (app.user.attributes.productos_c).replace(/\^/g, "");
			if ($('.list_u_canal').select2('val') == "0" && userprod.includes('8')) {
				$('.list_u_canal').find('.select2-choice').css('border-color', 'red');
				faltantesUniclickCanal += 1;
			}
			else {
				$('.list_u_canal').find('.select2-choice').css('border-color', 'black');
			}
			if (faltantesUniclickCanal > 0) {
				app.alert.show("Faltante canal Uniclick", {
					level: "error",
					title: 'Hace falta seleccionar algún canal para el producto Uniclick',
					autoClose: false
				});
				errors['error_UniclickUP'] = errors['error_UniclickUP'] || {};
				errors['error_UniclickUP'].required = true;
			}
		}
        callback(null, fields, errors);
    },

    /* Valida RFC con servicio de revisión del padron de contribuyentes */
    //        this.model.on('change:tipodepersona_c', this._ActualizaEtiquetas, this);
    //RFC_ValidatePadron: function (fields, errors, callback) {
    //
    //	var rfc = this.getField('rfc_c');
    //	var valuerfc = this.model.get('rfc_c');
    //	var anticrfc = this._get_rfc_antiguo();
    //
    //	if( (this.model.get('pais_nacimiento_c') == "2")
    //		&& ( !_.isEmpty(valuerfc) && valuerfc != "" && valuerfc != "undefined")
    //		&& (anticrfc != valuerfc)
    //		&& (rfc.action === "edit" || rfc.action === "create")
    //		&& ( this.model.get('estado_rfc_c') == null || this.model.get('estado_rfc_c') == ""  || this.model.get('estado_rfc_c') == "0")){
    //
    //		app.api.call('GET', app.api.buildURL('GetRFCValido/?rfc='+this.model.get('rfc_c')),null, {
    //			success: _.bind(function (data) {
    //				if (data != "" && data != null) {
    //					console.log("rfc");
    //					console.log(data);
    //					if (data.code == '1') {
    //						this.model.set('estado_rfc_c', "");
    //						app.alert.show("Error Validar RFC", {
    //							level: "error",
    //							title: 'Estructura del RFC incorrecta',
    //							autoClose: false
    //						});
    //						errors['error_RFC_Padron'] = errors['error_RFC_Padron'] || {};
    //						errors['error_RFC_Padron'].required = true;
    //                    }else if (data.code == '2') {
    //						this.model.set('estado_rfc_c', '0');
    //						app.alert.show("Error Validar RFC", {
    //							level: "error",
    //							title: 'RFC no registrado en el padrón de contribuyentes',
    //							autoClose: false
    //						});
    //						errors['error_RFC_Padron'] = errors['error_RFC_Padron'] || {};
    //						errors['error_RFC_Padron'].required = true;
    //                    }else if (data.code == '4') {
    //						this.model.set('estado_rfc_c', '1');
    //					}
    //				}else{
    //					app.alert.show("Error Validar RFC", {
    //						level: "error",
    //						title: 'Error de envío para validar RFC',
    //						autoClose: false
    //					});
    //					errors['error_RFC_Padron'] = errors['error_RFC_Padron'] || {};
    //					errors['error_RFC_Padron'].required = true;
    //				}
    //				callback(null, fields, errors);
    //			}, this),
    //			error: _.bind(function (error) {
    //				app.alert.show("Error Validar RFC", {
    //					level: "error",
    //					title: 'Error de envío',
    //					autoClose: false
    //				});
    //				errors['error_RFC_Padron'] = errors['error_RFC_Padron'] || {};
    //				errors['error_RFC_Padron'].required = true;
    //                console.log("Este fue el error:", error);
    //				callback(null, fields, errors);
    //            },this),
    //		});
    //	}else{
    //      	  callback(null, fields, errors);
    //    }
    //},
    //
    //cambioRFC: function(){
    //	var original_rfc = this.model._previousAttributes.rfc_c;
    //	this._set_rfc_antiguo(original_rfc);
    //},
    //
    //_get_rfc_antiguo: function(){
    //	return self.rfc_antiguo;
    //},
    //
    //_set_rfc_antiguo: function(rfca){
    //	self.rfc_antiguo = rfca;
    //},

    _cleanDependenciesOrigen: function () {

        /*******Limpia campos dependientes de Origen*******/
        //Origen: Prospección propia
        if (this.model.get('origen_cuenta_c') != '3') {
            this.model.set('prospeccion_propia_c', ''); //Limpia campo Prospeccion propia
        }
        //Origen: Referenciado Socio Comercial
        if (this.model.get('origen_cuenta_c') != '6') {
            this.model.set('account_id_c', ''); //Elimina usuario referenciado por db
            this.model.set('referenciador_c', ''); //Elimina usuario referenciado por vista
        }
        //Origen: Referenciado Unifin
        if (this.model.get('origen_cuenta_c') != '7') {
            this.model.set('user_id5_c', ''); //Elimina usuario referido por db
            this.model.set('tct_referenciado_dir_rel_c', ''); //Elimina usuario referido por vista
        }
        //Origen: Referenciado Cliente = 4, Referenciado Proveedor = 5, Referenciado Vendor = 8
        if (this.model.get('origen_cuenta_c') != '4' && this.model.get('origen_cuenta_c') != '5' && this.model.get('origen_cuenta_c') != '8') {
            this.model.set('account_id1_c', ''); //Elimina usuario referido db
            this.model.set('referido_cliente_prov_c', ''); //Elimina usuario referido vista
        }
    },

    _cleanDependencies: function () {

        /*******Limpia campos dependientes de Detalle Origen*******/
        //Acciones Estrategicas
        if (this.model.get('detalle_origen_c') != '5') {
            this.model.set('evento_c', ''); //Limpia campo que ¿Que evento?
        }
        //Base de datos Emp
        if (this.model.get('detalle_origen_c') != '1') {
            this.model.set('tct_origen_busqueda_txf_c', ''); //Limpia campo Base
        }
        //Base de datos Afiliaciones
        if (this.model.get('detalle_origen_c') != '6') {
            this.model.set('camara_c', ''); //Limpia campo ¿De que Cámara Proviene?
        }
        //Cartera Asesores
        if (this.model.get('detalle_origen_c') != '10') {
            this.model.set('tct_que_promotor_rel_c', ''); //Elimina ¿Que Asesor? Vista
            this.model.set('user_id4_c', ''); //Elimina ¿Que Asesor? DB
        }
        //Acciones Estrategicas = 5, Base de datos Emp = 1, Base de datos Afiliaciones = 6
        //Digital = 3, Offline = 9, Cartera Asesores = 10
        if (this.model.get('detalle_origen_c') != '5' && this.model.get('detalle_origen_c') != '1' &&
            this.model.get('detalle_origen_c') != '6' && this.model.get('detalle_origen_c') != '3' &&
            this.model.get('detalle_origen_c') != '9' && this.model.get('detalle_origen_c') != '10') {

            this.model.set('tct_origen_ag_tel_rel_c', ''); //Se elimina Agente Telefonico Vista
            this.model.set('user_id3_c', '');  //Se elimina Agente Telefonico DB
        }
    },

    tipoProveedor: function (fields, errors, callback) {
        if ((this.model.get('esproveedor_c') || this.model.get('tipo_registro_cuenta_c') == '5') && (App.user.attributes.puestousuario_c == 32 || App.user.attributes.puestousuario_c == 47) && (this.model.get('tipo_proveedor_compras_c') == null || this.model.get('tipo_proveedor_compras_c') == '') ) {
            app.alert.show("tipo_proveedor_compras_c", {
                level: "error",
                title: 'Hace falta seleccionar un valor para el campo Tipo de proveedor compras',
                autoClose: false
            });
            errors['tipo_proveedor_compras_c'] = errors['tipo_proveedor_compras_c'] || {};
            errors['tipo_proveedor_compras_c'].required = true;
        }
        callback(null, fields, errors);
    },
})
