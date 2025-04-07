({
    extendsFrom: 'RecordView',
    /**
     * @author bdekoning@levementum.com
     * @date 6/9/15
     * @brief Override for handleCancel to ensure the account_telefonos attribute is properly reverted
     *
     * @override
     */
    oculta: 0,

    initialize: function (options) {
        //self = this;
        contexto_cuenta = this;
        self.hasContratosActivos = false;
        this._super("initialize", [options]);

        this.context.on('button:cancel_button:click', this.handleCancel, this);

        this.totalllamadas = 0;
        this.totalreuniones = 0;
        this.flagheld = 0;

        //Funcion que quita los años futuros y menores a -5 del año actual
        this.quitaanos();
        this.model.on("change:tct_ano_ventas_ddw_c", _.bind(this.quitaanos, this));

        //add validation tasks
        this.model.addValidationTask('checkaccdatestatements', _.bind(this.checkaccdatestatements, this));
        this.model.addValidationTask('duplicate_check', _.bind(this.DuplicateCheck, this));
        this.model.addValidationTask('validaduplicadoRFC', _.bind(this.RFC_DuplicateCheck, this));
        this.model.addValidationTask('validaduplicadoCURP', _.bind(this.CURP_DuplicateCheck, this));
        this.model.addValidationTask('check_email_telefono', _.bind(this._doValidateEmailTelefono, this));
        this.model.addValidationTask('check_telefonos', _.bind(this.validatelefonos, this));
        this.model.addValidationTask('check_rfc', _.bind(this._doValidateRFC, this));
        this.model.addValidationTask('check_fecha_de_nacimiento', _.bind(this._doValidateMayoriadeEdad, this));
        this.model.addValidationTask('check_account_direcciones', _.bind(this._doValidateDireccion, this));
        //this.model.addValidationTask('check_account_direccionesCP', _.bind(this._doValidateDireccionCP, this));
        //this.model.addValidationTask('check_Tiene_Contactos', _.bind(this._doValidateTieneContactos, this));
        this.model.addValidationTask('check_1900_year', _.bind(this.fechaMenor1900, this));
        this.model.addValidationTask('fechadenacimiento_c', _.bind(this.doValidateDateNac, this));
        this.model.addValidationTask('fechaconstitutiva_c', _.bind(this.doValidateDateCons, this));
        this.model.addValidationTask('verificaRiesgoPep', _.bind(this.cambiaRiesgodePersona, this));
        this.model.addValidationTask('tipo_proveedor_requerido', _.bind(this.validaProveedorRequerido, this));
        this.model.addValidationTask('check_info', _.bind(this.doValidateInfoReq, this));
        //this.model.addValidationTask('macrosector', _.bind(this.macrosector, this));
        // this.model.addValidationTask('sectoreconomico', _.bind(this.sectoreconomico, this));
        //this.model.addValidationTask('checkEmptyFieldsDire', _.bind(this.validadirecc, this));
        this.model.addValidationTask('change:email', _.bind(this.expmail, this));
        //Valida que el campo Alta Cedente este check en el perfil del usuario. Adrian Arauz 20/09/2018
        this.model.addValidationTask('check_alta_cedente', _.bind(this.validacedente, this));
        /*Funcion para validar los campos ventas anuales y activo fijo al editar una cuenta de tipo
         * Integración de Expediente
         * Adrian Arauz 4/10/2018
         * */
        this.model.addValidationTask('valida_potencial_campos_autos', _.bind(this.nodigitos, this));

        this.model.addValidationTask('valida_potencial', _.bind(this.validapotencial, this));

        /***************Valida Campo de Página Web ****************************/
        this.model.addValidationTask('validaPaginaWeb', _.bind(this.validaPagWeb, this));
        /** Valida genero personas fisicas y fisica con actividad empesarial **/
        this.model.addValidationTask('validaGenero', _.bind(this.validaGenero, this));

        this.model.addValidationTask('valida_requeridos', _.bind(this.valida_requeridos, this));

        /*Validacion de campos requeridos en el cuestionario PLD y sus productos
         * Adrian Arauz 23/01/2019
         * */
        this.model.addValidationTask('RequeridosPLD', _.bind(this.validaRequeridosPLD, this));

        //this.model.addValidationTask('camposnumericosPLDFF', _.bind(this.validacantidades, this));

        /* F. Javier G. Solar
         OBS299 Validar que las Direcciones no se repitan 21/11/2018
         */

        this.model.addValidationTask('validate_Direccion_Duplicada', _.bind(this._direccionDuplicada, this));

        //Validacion para que la fecha de nac/constitutiva sea igual al RFC
        this.model.addValidationTask('Valida_RFC', _.bind(this.validaRFC, this));

        /*
         Eduardo Carrasco
         revisa que la persona no tenga contratos existentes despues de cambiar el RFC. Si hay contratos existentes, no se podra cambiar el RFC
         */
        this.model.on("change:rfc_c", _.bind(function () {
            var rfc = this.getField('rfc_c');
            /*if (rfc.action === "edit") {
             if (App.user.id!=self.model.get('user_id_c') && App.user.id!= self.model.get('user_id1_c') && App.user.id!= self.model.get('user_id2_c') ) {
             App.alert.show("validar_rfc", {
             level: "error",
             title: "\u00DAnicamente los promotores asociados a la cuenta pueden cambiar el RFC.",
             autoClose: false
             });
             }
             }*/
            if (!_.isEmpty(this.model.get('idcliente_c')) && rfc.action === "edit") {
                app.api.call("read", app.api.buildURL("Accounts/AccountsCustomAPI/" + this.model.get('idcliente_c'), null, null, {}), null, {
                    success: _.bind(function (data) {
                        if (data.UNI2_CTE_029_VerificaClienteTieneContratoResult._tieneContratos == true) {
                            if (App.user.id != self.model.get('user_id_c') && App.user.id != self.model.get('user_id1_c') && App.user.id != self.model.get('user_id2_c') && App.user.id != self.model.get('user_id6_c') && App.user.id != self.model.get('user_id7_c')) {
                                app.alert.show("Validar Contratos", {
                                    level: "error",
                                    title: "\u00DAnicamente los promotores asociados a la cuenta pueden cambiar el RFC a Cliente con contratos existentes.",
                                    autoClose: false
                                });
                                this.cancelClicked();
                                this.$("input[name='rfc_c']").prop("readonly", true);
                            }
                        }
                    }, this)
                });
            }
        }, this));

        /*RFC_ValidatePadron
          Validación de rfc en el padron de contribuyentes
        */
        //self.rfc_antiguo = "";
        //this.model.on('change:rfc_c', this.cambioRFC, this);
        //this.model.addValidationTask('RFC_validateP', _.bind(this.RFC_ValidatePadron, this));


        //Validacion para el formato de los campos nombre y apellidos.
        this.model.addValidationTask('validaformato3campos', _.bind(this.validaformato, this));
        this.model.addValidationTask('validacamposcurppass', _.bind(this.validapasscurp, this));
        this.model.addValidationTask('porcentajeIVA', _.bind(this.validaiva, this));
        this.model.addValidationTask('ValidacionReferidoPorVENDOR', _.bind(this.validaReferido, this));


        /*
         Salvador Lopez
         Se añaden eventos change para mostrar teléfonos y direcciones al vincular o desvincular algún registro relacionado
         */
        //this.model.on('change:account_telefonos', this.refresca, this);
        this.model.on('change:tipodepersona_c', this._ActualizaEtiquetas, this);
        this.model.on('change:profesion_c', this._doValidateProfesionRisk, this);
        this.model.on('change:pais_nacimiento_c', this._doValidateProfesionRisk, this);
        this.model.on('change:origen_cuenta_c', this.changeLabelMarketing, this);
        this.model.on('change:detalle_origen_c', this._cleanDependencies, this);

        //Se añade función para establecer phone_office
        this.model.on('change:account_telefonos', this.setPhoneOffice, this);
        /*
         AF - 26/12/17
         Ajuste: Ocultar campo dependiente de multiselect "¿Instrumento monetario con el que espera realizar los pagos?"
         */
        //this.changeInstMonetario();
        //this.model.on('change:tct_inst_monetario_c', this.changeInstMonetario, this);

        /*
         AA 24/06/2019 Se añade evento para desabilitar el boton genera RFC si la nacionalidad es diferente de Mexicano
         */
        this.model.on('change:tct_pais_expide_rfc_c', this.ocultaRFC, this);

        /*
         Salvador Lopez
         Se añaden eventos change para mostrar u ocultar paneles
         */
        this.model.on('change:tct_fedeicomiso_chk_c', this._hideFideicomiso, this);
        this.model.on('change:tipodepersona_c', this._hidePeps, this);

        this.model.on('change:primernombre_c', this.checkTextOnly, this);
        this.model.on('change:apellidomaterno_c', this.checkTextOnly, this);
        this.model.on('change:apellidopaterno_c', this.checkTextOnly, this);
        this.events['keydown input[name=rfc_c]'] = 'checkTextAndNumRFC';
        this.model.on('change:ifepasaporte_c', this.checkTextAndNum, this);
        this.model.on('change:curp_c', this.checkTextAndNum, this);

        this.events['click a[name=generar_rfc_c]'] = '_doGenera_RFC_CURP';
        this.events['click a[name=generar_curp_c]'] = '_doGeneraCURP';
        //Evento boton Portal Proveedores
        this.events['click a[name=portal_proveedores]'] = 'func_Proveedor';

        /* LEV INICIO */
        /** BEGIN CUSTOMIZATION: jgarcia@levementum.com 7/14/2015 Description: Cuando estamos en el modulo de Personas, no queremos que se muestre la opcion Persona para el tipo de registro */

        /*
         self.model.on("change", function() {
         if (self.model.get('tipo_registro_cuenta_c') != null){
         if(self.model.get('tipo_registro_cuenta_c') != 'Persona') {
         var new_options = app.lang.getAppListStrings('tipo_registro_cuenta_list');
         Object.keys(new_options).forEach(function (key) {
         if (key == "Persona") {
         delete new_options[key];
         }
         });

         self.model.fields['tipo_registro_cuenta_c'].options = new_options;
         }

         }
         });
         */
        /* LEV FIN */

        this.model.on('change:name', this.cleanName, this);
        this.model.on('change:no_website_c', this.rowebsite, this);

        /* EJC: 2025-03-24
         Ajuste para tipo de cuenta proveedor, no edite       */
         this.model.on('sync', this.blockProveedor, this);
        /*
         AF. 12-02-2018
         Ajuste para mostrar direcciones y teléfonos
         */
        //Carga de funcion quitar años lista para ventas anuales
        this.model.on('sync', this.quitaanos, this);
        this.model.on('sync', this.blockRecordNoContactar, this);
        //bloquear no viable
        this.model.on('sync', this.blockRecordNoViable, this);
        //this.model.on('sync', this._render, this);
        this.model.on('sync', this.hideconfiinfo, this);
        this.model.on('sync', this.disable_panels_rol, this); //@Jesus Carrilllo; metodo que deshabilita panels de acuerdo a rol;
        this.model.on('sync', this.disable_panels_team, this);
        this.model.on('sync', this.fulminantcolor, this); //*@Jesus Carrillo; Funcion que pinta de color los paneles relacionados
        this.model.on('sync', this.valida_centro_prospec, this);
        this.model.on('sync', this.valida_backoffice, this);
        //this.model.on('sync', this.checkTelNorepeat, this);
        //this.model.on('sync', this.CamposCstmLoad, this);

        /*
         @author Salvador Lopez
         Se llaman a la funciones para mostrar u ocultar paneles de Fideicomiso y Peps
         * */
        this.model.on('sync', this._hideFideicomiso, this);
        this.model.on('sync', this._hidePeps, this);
        // @author Salvador Lopez
        //Se manda a llamar función para omitir opción de Persona en ddw
        this.model.on('sync', this.deleteOptionPersona, this);
        this.model.on('sync', this._ActualizaEtiquetas, this);
        this.model.on('sync', this.muestracheks, this);
        /*Victor Martinez Lopez
         * Deshabilita el campo es proveedor 13-09-2018
         * */
        this.model.on('sync', this.checkProveedor, this);
        //Display or Hide Vista360
        this.model.on('sync', this._hideVista360, this);
        //Display or Hide Proveedor Analizte
        this.model.on('sync', this._panel_anlzt_proveedor, this);
        //Display or Hide Analizte Cliente
        this.model.on('sync', this._panel_anlzt_cliente, this);

        this.model.on('sync', this.hide_panel_buro_credito, this);
        //Solo Lectura campos Origen
        this.model.on('sync', this.readOnlyOrigen, this);
        /* @author F. Javier Garcia S. 10/07/2018
         Agregar dependencia al panel NPS, para ser visible si "Tipo de Cuenta" es "Cliente".
         */
        this.model.on('sync', this._hideNPS, this);
        this.model.on('sync', this.hideButton_Conversion, this);
        this.model.on('sync', this.hideButton_Conversion_change, this);

        //Validacion para mostrar chk para cuentas homonimas
        this.model.on('sync', this.homonimo, this);

        //Oculta Botón Generar RFC
        this.model.on('sync', this.ocultaGeneraRFC, this);

        //Oculta Menú Tarea IE Proveedor Quantico
        // this.model.on('sync', this.ocultaproveedor, this);

        //Recupera datos para custom fields
        this.get_addresses();
        this.CamposCstmLoad();
        //this.get_noviable();


        //Funcion para eliminar duplicados de arrays
        Array.prototype.unique = function (a) {
            return function () {
                return this.filter(a)
            }
        }(function (a, b, c) {
            return c.indexOf(a, b + 1) < 0
        });

        // validación de los campos con formato númerico
        this.events['keydown [name=ventas_anuales_c]'] = 'checkInVentas';
        this.events['keydown [name=activo_fijo_c]'] = 'checkInVentas';
        this.events['keydown [name=tct_prom_cheques_cur_c]'] = 'checkInVentas';
        this.events['keydown [name=tct_depositos_promedio_c]'] = 'checkInVentas';
        this.events['keydown [name=ctpldnoseriefiel_c]'] = 'keyDownNewExtension';
        this.events['keydown [name=tct_cpld_pregunta_u2_txf_c]'] = 'keyDownNewExtension';
        //this.events['keydown [name=tct_cpld_pregunta_u4_txf_c]'] = 'keyDownNewExtension';
        //this.events['keydown [name=ctpldnoseriefiel_c]'] = 'checkInVentas';
        this.events['keydown [name=tct_cpld_pregunta_u2_txf_c]'] = 'checkInVentas';
        //this.events['keydown [name=tct_cpld_pregunta_u4_txf_c]'] = 'checkInVentas';
        // this.model.addValidationTask('LeasingNV', _.bind(this.requeridosleasingnv, this));
        // this.model.addValidationTask('FactorajeNV', _.bind(this.requeridosfacnv, this));
        // this.model.addValidationTask('CreditAutoNV', _.bind(this.requeridoscanv, this));
        this.model.addValidationTask('proveedorDeRecursos', _.bind(this.proveedorRecursos, this));

        this.model.addValidationTask('valida_direcciones_de_relaciones_PR', _.bind(this.direccionesparticularPR, this));
        this.model.addValidationTask('set_custom_fields', _.bind(this.setCustomFields, this));
        this.model.addValidationTask('Guarda_campos_auto_potencial', _.bind(this.savepotauto, this));
        /** Logica para Asignación modal **/
        this.model.on('sync', this.hideButtonsModal_Account, this);
        this.context.on('button:get_account_asesor:click', this.get_Account, this);
        this.context.on('button:send_account_asesor:click', this.set_Account, this);
        this.context.on('button:bloquea_cuenta:click', this.bloquea_cuenta, this);
        this.context.on('button:desbloquea_cuenta:click', this.desbloquea_cuenta, this);
        this.context.on('button:aprobar_noviable:click', this.aprobar_noviable, this);
        this.context.on('button:desaprobar_noviable:click', this.rechazar_noviable, this);
        this.context.on('button:reactivar_noviable:click', this.reactivar_noviable, this);
        this.model.on('sync', this.bloqueo, this);

        /********* Validacion grupo empresarial ****************/
        this.model.addValidationTask('validaGrupoEmpresarial', _.bind(this.validaGrupoEmpresarial, this));
        this.model.on('change:situacion_gpo_empresarial_c', this.val_SituacionEmpresarial, this);


        this.context.on('button:open_negociador_quantico:click', this.open_negociador_quantico, this);
        // this.context.on('button:proveedor_quantico:click', this.proveedor_quantico, this);
        /***************Validacion de Campos No viables en los Productos********************/
        this.model.addValidationTask('LeasingUP', _.bind(this.requeridosLeasingUP, this));
        this.model.addValidationTask('FactorajeUP', _.bind(this.requeridosFactorajeUP, this));
        this.model.addValidationTask('CreditAutoUP', _.bind(this.requeridosCAUP, this));
        this.model.addValidationTask('FleetUP', _.bind(this.requeridosFleetUP, this));
        this.model.addValidationTask('UniclickUP', _.bind(this.requeridosUniclickUP, this));
        //this.model.addValidationTask('UniclickCanal', _.bind(this.requeridosUniclickCanal, this));
        this.model.addValidationTask('tipo_proveedor_compras', _.bind(this.tipoProveedor, this));
        this.model.addValidationTask('AlertaCamposRequeridosUniclick', _.bind(this.validaReqUniclick, this));
        this.model.addValidationTask('validaReqPLDPropReal_CS', _.bind(this.validaPropRealCR, this));
        this.model.addValidationTask('requestDynamics', _.bind(this.requestDynamics1, this));
        //this.model.addValidationTask('clean_name', _.bind(this.cleanName, this));
        //Funcion para que se pueda o no editar el check de Alianza SOC
        this.model.on('sync', this.userAlianzaSoc, this);
        //this.model.on('sync',this.validaReqUniclickInfo,this);
        //Se omite llamada a funcion para deshabilitar ya que se opta por habilitar bloqueo via dependencia
        this.model.on('sync', this.deshabilitaOrigenCuenta, this);
        //Función para eliminar opciones del campo origen
        this.estableceOpcionesOrigen();
        //Clic solicitar CIEC
        this.context.on('button:solicitar_ciec:click', this.solicitar_ciec_function, this);
        this.context.on('button:alta_po:click', this.muestra_modal_alta_po, this);
        //Oculta Menú Solicitar CIEC
        this.model.on('sync', this.ocultaSolicitarCIEC, this);
        //Parche utilizado para ocultar las filas que siguen mostrándose aunque ningún campo se encuentren en ellas
        this.model.on('sync', this.hideRowsNoHideByDependency, this);
        //Se bloquean campos de nombre para los registros tipo "Cliente"
        this.model.on('sync', this.disableNameCliente, this);
        //Muestra mensaje Dynamics265
        this.model.on('sync', this.dynamics365, this);
        //ASIGNACION AUTOMATICA
        this.context.on('button:solicitud_asignacion:click', this.solicitudAsignacionCuenta, this);
    },

    /** Asignacion modal */
    hideButtonsModal_Account: function () {
        var Boton1 = this.getField("get_account_asesor");
        var Boton2 = this.getField("send_account_asesor");
        var userprod = (app.user.attributes.productos_c).replace(/\^/g, "");
        var userpuesto = app.user.attributes.puestousuario_c;
        var puestosBtn1 = ['18'];
        var puestosBtn2 = ['27', '31'];

        if (Boton1) {
            Boton1.listenTo(Boton1, "render", function () {
                console.log(userpuesto);
                if (puestosBtn1.includes(userpuesto)) {
                    Boton1.show();
                } else {
                    Boton1.hide();
                }
            });
        }

        if (Boton2) {
            Boton2.listenTo(Boton2, "render", function () {
                if (puestosBtn2.includes(userpuesto)) {
                    Boton2.show();
                } else {
                    Boton2.hide();
                }
            });
        }
    },

    get_Account: function () {

        if (Modernizr.touch) {
            app.$contentEl.addClass('content-overflow-visible');
        }
        /**check whether the view already exists in the layout.
         * If not we will create a new view and will add to the components list of the record layout
         * */
        var quickCreateView = this.layout.getComponent('getAccountModal');
        if (!quickCreateView) {
            /** Create a new view object */
            quickCreateView = app.view.createView({
                context: this.context,
                name: 'getAccountModal',
                layout: this.layout,
                module: 'Accounts'
            });
            /** add the new view to the components list of the record layout*/
            this.layout._components.push(quickCreateView);
            this.layout.$el.append(quickCreateView.$el);
        }
        /**triggers an event to show the pop up quick create view*/
        this.layout.trigger("app:view:getAccountModal");
    },

    set_Account: function () {
        if (Modernizr.touch) {
            app.$contentEl.addClass('content-overflow-visible');
        }
        /**check whether the view already exists in the layout.
         * If not we will create a new view and will add to the components list of the record layout
         * */
        var quickCreateView = this.layout.getComponent('setAccountModal');
        if (!quickCreateView) {

            /** Create a new view object */
            quickCreateView = app.view.createView({
                context: this.context,
                name: 'setAccountModal',
                layout: this.layout,
                module: 'Accounts'
            });
            /** add the new view to the components list of the record layout*/
            this.layout._components.push(quickCreateView);
            this.layout.$el.append(quickCreateView.$el);
        }
        /**triggers an event to show the pop up quick create view*/
        this.layout.trigger("app:view:setAccountModal");
    },

    open_negociador_quantico: function () {
        //Abrir nueva ventana del entrypoint del Negociador Quantico
        var idCuenta = this.model.get('id');
        window.open("#bwc/index.php?entryPoint=NegociadorQuantico&idPersona=" + idCuenta);

    },

    /*
        saveProdPLD: function (fields, errors, callback) {
    
            if (this.model.get('tipo_registro_cuenta_c') != '') {
                 //Valida cambios
                 if ($.isEmptyObject(errors) && (this.inlineEditMode == false || (this.inlineEditMode && typeof ($('.campo4ddw-cs').select2('val')) == "string"))) {
    
                // Actualizar modelo de this.ProductosPLD
                // this.ProductosPLD.arrendamientoPuro.campo1 = $('.campo1txt-ap').val();
                if (this.ProductosPLD != null && typeof (this.$('.campo4ddw-cs').select2('val')) == "string") {
    
                    // this.ProductosPLD.arrendamientoPuro.campo1 = this.$('.campo1txt-ap').val();
                    this.ProductosPLD.arrendamientoPuro.campo2 = this.$('.campo2ddw-ap').select2('val');
                    this.ProductosPLD.arrendamientoPuro.campo3 = this.$('.campo3rel-ap')[0]['innerText'];
                    this.ProductosPLD.arrendamientoPuro.campo3_id = this.$('.campo3rel-ap').select2('val');
                    this.ProductosPLD.arrendamientoPuro.campo4 = this.$('.campo4ddw-ap').select2('val');
                    //this.ProductosPLD.arrendamientoPuro.campo5 = this.$('.campo5rel-ap')[0]['innerText'];
                    //this.ProductosPLD.arrendamientoPuro.campo5_id = this.$('.campo5rel-ap').select2('val');
                    this.ProductosPLD.arrendamientoPuro.campo6 = this.$('.campo6ddw-ap').select2('val');
                    // this.ProductosPLD.arrendamientoPuro.campo7 = this.$('.campo7ddw-ap').select2('val');
                    // this.ProductosPLD.arrendamientoPuro.campo8 = this.$('.campo8txt-ap').val();
                    // this.ProductosPLD.arrendamientoPuro.campo9 = this.$('.campo9ddw-ap').select2('val');
                    // this.ProductosPLD.arrendamientoPuro.campo10 = this.$('.campo10txt-ap').val();
                    this.ProductosPLD.arrendamientoPuro.campo11 = this.$('.campo11ddw-ap').select2('val');
                    //this.ProductosPLD.arrendamientoPuro.campo13 = this.$('.campo13chk-ap')[0].checked;
                    this.ProductosPLD.arrendamientoPuro.campo14 = this.$('.campo14chk-ap')[0].checked;
                    this.ProductosPLD.arrendamientoPuro.campo16 = this.$('.campo16ddw-ap').select2('val').toString();
                    this.ProductosPLD.arrendamientoPuro.campo17 = this.$('.campo17txt-ap').val();
                    this.ProductosPLD.arrendamientoPuro.campo25 = this.$('.campo25ddw-ap').select2('val');
                    this.ProductosPLD.arrendamientoPuro.campo26 = this.$('.campo26txt-ap').val();
                    // this.ProductosPLD.factorajeFinanciero.campo1 = this.$('.campo1txt-ff').val();
                    this.ProductosPLD.factorajeFinanciero.campo2 = this.$('.campo2ddw-ff').select2('val');
                    this.ProductosPLD.factorajeFinanciero.campo3 = this.$('.campo3rel-ff').val();
                    this.ProductosPLD.factorajeFinanciero.campo3_id = this.$('.campo3rel-ff').select2('val');
                    this.ProductosPLD.factorajeFinanciero.campo4 = this.$('.campo4ddw-ff').select2('val');
                    //this.ProductosPLD.factorajeFinanciero.campo5 = this.$('.campo5rel-ff').val();
                    //this.ProductosPLD.factorajeFinanciero.campo5_id = this.$('.campo5rel-ff').select2('val');
                    this.ProductosPLD.factorajeFinanciero.campo21 = this.$('.campo21ddw-ff').select2('val');
                    this.ProductosPLD.factorajeFinanciero.campo22 = this.$('.campo22int-ff').val();
                    this.ProductosPLD.factorajeFinanciero.campo23 = this.$('.campo23dec-ff').val().replace(/,/gi, "");
                    this.ProductosPLD.factorajeFinanciero.campo16 = this.$('.campo16ddw-ff').select2('val').toString();
                    this.ProductosPLD.factorajeFinanciero.campo17 = this.$('.campo17txt-ff').val();
                    this.ProductosPLD.factorajeFinanciero.campo14 = this.$('.campo14chk-ff')[0].checked;
                    this.ProductosPLD.factorajeFinanciero.campo24 = this.$('.campo24ddw-ff').select2('val');
                    this.ProductosPLD.factorajeFinanciero.campo6 = this.$('.campo6ddw-ff').select2('val');
                    //  this.ProductosPLD.creditoAutomotriz.campo1 = this.$('.campo1txt-ca').val();
                    this.ProductosPLD.creditoAutomotriz.campo2 = this.$('.campo2ddw-ca').select2('val');
                    this.ProductosPLD.creditoAutomotriz.campo3 = this.$('.campo3rel-ca').val();
                    this.ProductosPLD.creditoAutomotriz.campo3_id = this.$('.campo3rel-ca').select2('val');
                    this.ProductosPLD.creditoAutomotriz.campo4 = this.$('.campo4ddw-ca').select2('val');
                    //this.ProductosPLD.creditoAutomotriz.campo5 = this.$('.campo5rel-ca').val();
                    //this.ProductosPLD.creditoAutomotriz.campo5_id = this.$('.campo5rel-ca').select2('val');
                    this.ProductosPLD.creditoAutomotriz.campo6 = this.$('.campo6ddw-ca').select2('val');
                    // this.ProductosPLD.creditoSimple.campo1 = this.$('.campo1txt-cs').val();
                    this.ProductosPLD.creditoSimple.campo2 = this.$('.campo2ddw-cs').select2('val');
                    this.ProductosPLD.creditoSimple.campo3 = this.$('.campo3rel-cs').val();
                    this.ProductosPLD.creditoSimple.campo3_id = this.$('.campo3rel-cs').select2('val');
                    this.ProductosPLD.creditoSimple.campo4 = this.$('.campo4ddw-cs').select2('val');
                    //this.ProductosPLD.creditoSimple.campo5 = this.$('.campo5rel-cs').val();
                    //this.ProductosPLD.creditoSimple.campo5_id = this.$('.campo5rel-cs').select2('val');
                    this.ProductosPLD.creditoSimple.campo18 = this.$('.campo18ddw-cs').select2('val').toString();
                    this.ProductosPLD.creditoSimple.campo19 = this.$('.campo19txt-cs').val();
                    this.ProductosPLD.creditoSimple.campo14 = this.$('.campo14chk-cs')[0].checked;
                    this.ProductosPLD.creditoSimple.campo20 = this.$('.campo20ddw-cs').select2('val');
                    this.ProductosPLD.creditoSimple.campo6 = this.$('.campo6ddw-cs').select2('val');
                    //Campos Credito Revolvente
                    this.ProductosPLD.creditoRevolvente.campo1=this.$('.campo1int-ce').val();
                    this.ProductosPLD.creditoRevolvente.campo2=this.$('.campo2dec-ce').val().replace(/,/gi, "");
                    this.ProductosPLD.creditoRevolvente.campo3=this.$('.campo3ddw-ce').select2('val').toString();
                    this.ProductosPLD.creditoRevolvente.campo5=this.$('.campo5ddw-ce').select2('val').toString();
                    this.ProductosPLD.creditoRevolvente.campo6=this.$('.campo6ddw-ce').select2('val');
                    this.ProductosPLD.creditoRevolvente.campo7=this.$('.campo7ddw-ce').select2('val').toString();
                    this.ProductosPLD.creditoRevolvente.campo8=this.$('.campo8ddw-ce').select2('val');
                    this.ProductosPLD.creditoRevolvente.campo9=this.$('.campo9rel-ce').select2('val');
                    this.ProductosPLD.creditoRevolvente.campo9_id=this.$('.campo9rel-ce').select2('val');
                    this.ProductosPLD.creditoRevolvente.campo10=this.$('.campo10ddw-ce').select2('val');
                    this.ProductosPLD.creditoRevolvente.campo11=this.$('.campo11rel-ce').select2('val');
                    this.ProductosPLD.creditoRevolvente.campo11_id=this.$('.campo11rel-ce').select2('val');
    
                }
                    //var obj_pld_old=JSON.stringify(this.model.get('accounts_tct_pld_1'));
                    //var obj_pld_new=JSON.stringify(this.ProductosPLD);
                    app.api.call('create', app.api.buildURL('SavePLD'), this.ProductosPLD, {
                        success: function (data) {
                            if (data != "") {
                                console.log("Actualiza pld");
                            }
                            contexto_cuenta.ProductosPLD = pld.formatDetailPLD(contexto_cuenta.ProductosPLD);
                            pld.ProductosPLD = contexto_cuenta.ProductosPLD;
                            contexto_cuenta.prev_ProductosPLD = app.utils.deepCopy(contexto_cuenta.ProductosPLD);
                            pld.render();
                            callback(null, fields, errors);
                        },
                        error: function (e) {
                            //throw e;
                            pld.render();
                            callback(null, fields, errors);
                        }
                    });
                } else {
                    // contexto_cuenta.ProductosPLD = pld.formatDetailPLD(contexto_cuenta.ProductosPLD);
                    // pld.ProductosPLD = contexto_cuenta.ProductosPLD;
                    // pld.render();
                    callback(null, fields, errors);
                }
            } else {
                // contexto_cuenta.ProductosPLD = pld.formatDetailPLD(contexto_cuenta.ProductosPLD);
                // pld.ProductosPLD = contexto_cuenta.ProductosPLD;
                // pld.render();
                callback(null, fields, errors);
            }
        },
    */

    /* F. Javier G. Solar
     OBS299 Validar que las Direcciones no se repitan 21/11/2018
     */
    _direccionDuplicada: function (fields, errors, callback) {
        /* SE VALIDA DIRECTAMENTE DE LOS ELEMENTOS DEL HTML POR LA COMPLEJIDAD DE
         OBETENER LAS DESCRIPCIONES DE LOS COMBOS*/
        //var objDirecciones = $('.control-group.direccion')
        // var objDirecciones = $('.control-group.direccion')
        // var concatDirecciones = [];
        // var strDireccionTemp = "";
        // for (var i = 0; i < objDirecciones.length - 1; i++) {
        //     if (objDirecciones.eq(i).find('select.inactivo option:selected') == 0) {
        //         strDireccionTemp = objDirecciones.eq(i).find('.calleExisting').val() +
        //             objDirecciones.eq(i).find('.numExtExisting').val() +
        //             objDirecciones.eq(i).find('.numIntExisting').val() +
        //             objDirecciones.eq(i).find('select.coloniaExisting option:selected').text() +
        //             objDirecciones.eq(i).find('select.municipioExisting option:selected').text() +
        //             objDirecciones.eq(i).find('select.estadoExisting option:selected').text() +
        //             objDirecciones.eq(i).find('select.ciudadExisting option:selected').text() +
        //             objDirecciones.eq(i).find('.postalInputTempExisting').val();
        //         concatDirecciones.push(strDireccionTemp.replace(/\s/g, "").toUpperCase());
        //     }
        // }
        // // validamos  el arreglo generado
        // var existe = false;
        // for (var j = 0; j < concatDirecciones.length; j++) {
        //     for (var k = j + 1; k < concatDirecciones.length; k++) {
        //         if (concatDirecciones[j] == concatDirecciones[k]) {
        //             existe = true;
        //         }
        //     }
        // }
        // if (existe) {
        //     app.alert.show('Direcci\u00F3n', {
        //         level: 'error',
        //         autoClose: false,
        //         messages: 'Existe una o mas direcciones repetidas'
        //     });
        //     var messages1 = 'Existe una o mas direcciones repetidas';
        //     errors['xd'] = errors['xd'] || {};
        //     // errors['xd'].messages1 = true;
        //     errors['xd'].required = true;
        // }
        /********************************************MEJORA DE DUPLICIDAD DE DIRECCIONES******************************************/
        var direccion = this.oDirecciones.direccion;
        var keys = Object.keys(direccion); // Obtiene todas las claves
        var cDuplicado = 0;
        for (let i = 0; i < keys.length; i++) {
            let keyA = keys[i]; //Dirección a comparar A

            for (let j = i + 1; j < keys.length; j++) { // Compara con las siguientes direcciones
                let keyB = keys[j]; //Dirección a comparar B

                var duplicado = 0;
                var dirA = direccion[keyA];
                var dirB = direccion[keyB];

                // Compara atributos clave
                duplicado += ((dirA.valCodigoPostal ?? "").trim() === (dirB.valCodigoPostal ?? "").trim()) ? 1 : 0;
                duplicado += ((dirA.pais ?? "").trim() === (dirB.pais ?? "").trim()) ? 1 : 0;
                duplicado += ((dirA.estado ?? "").trim() === (dirB.estado ?? "").trim()) ? 1 : 0;
                duplicado += ((dirA.municipio ?? "").trim() === (dirB.municipio ?? "").trim()) ? 1 : 0;
                duplicado += ((dirA.ciudad ?? "").trim() === (dirB.ciudad ?? "").trim()) ? 1 : 0;
                duplicado += ((dirA.colonia ?? "").trim() === (dirB.colonia ?? "").trim()) ? 1 : 0;
                duplicado += ((dirA.calle ?? "").trim().toLowerCase() === (dirB.calle ?? "").trim().toLowerCase()) ? 1 : 0;
                duplicado += ((dirA.numext ?? "").trim().toLowerCase() === (dirB.numext ?? "").trim().toLowerCase()) ? 1 : 0;

                var inactivoA = parseInt(dirA.inactivo) || 0;
                var inactivoB = parseInt(dirB.inactivo) || 0;
                duplicado += (inactivoA === inactivoB) ? 1 : 0;

                console.log(`Comparando dirección ${keyA} con ${keyB}: duplicado =`, duplicado);

                // Si coinciden 9 atributos, es duplicado
                if (duplicado === 9) {
                    cDuplicado++;
                }
            }
        }
        // Mostrar error si hay direcciones repetidas
        if (cDuplicado >= 1) {
            app.alert.show('Direcci\u00F3n', {
                level: 'error',
                autoClose: false,
                messages: '<b>Existe una o más direcciones repetidas.</b>'
            });
            errors['xd'] = errors['xd'] || {};
            errors['xd'].required = true;
        }

        callback(null, fields, errors);
    },

    checkInVentas: function (evt) {
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
                //console.log('no se cumplen enteros')
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
                //console.log('no se cumplen dec')
                return "false";
            } else {
                return "true";
            }
        }
    },


    fulminantcolor: function () {
        $('#space').remove();
        $('.search-filter').find('.control-group').before('<div id="space" style="background-color:#021741"><br></div>');
        // $('.control-group').css("background-color", "#e5e5e5");
        // $('.a11y-wrapper').css("background-color", "#e5e5e5");
        //$('.a11y-wrapper').css("background-color", "#c6d9ff");
    },

    /*Victor Martinez Lopez 12-09-2018
     *La casilla proveedor se debe mantener activa al crear un proveedor
     * */
    checkProveedor: function () {
        if (this.model.get('tipo_registro_cuenta_c') == '5') {
            this.$('[data-name="esproveedor_c"]').attr('style', 'pointer-events:none;');
        }
    },
    /** BEGIN CUSTOMIZATION:
     * Salvador Lopez 19/01/2018
     * Descripción: Función que oculta o muestra panel de fideicomiso dependiendo el valor de check ¿Es Fideicomisio? */

    _hideVista360: function () {
        if (this.model.get('show_panel_c') == true) {
            //Muestra vista 360
            //TabNav
            //$("#recordTab>li.tab").removeClass('active');
            //Se elimina clase active de todas las pestañas
            $("a.block").removeClass("active");
            $('li.tab.LBL_RECORDVIEW_PANEL8').addClass("active");

            //Tabcontent
            $("#tabContent>div.tab-pane").addClass('fade')
            $("#tabContent>div.tab-pane").removeClass('active')
            $('#tabContent').children()[0].classList.add('active');
            $('#tabContent').children()[0].classList.remove('fade');


        } else {
            //Oculta vista 360
            //TabNav
            $("#recordTab>li.tab").removeClass('active');
            $('li.tab.panel_body').addClass("active");
            $('li.tab.LBL_RECORDVIEW_PANEL8').hide();

            //Tabcontent
            $("#tabContent>div.tab-pane").addClass('fade')
            $("#tabContent>div.tab-pane").removeClass('active')
            $('#tabContent').children()[1].classList.add('active');
            $('#tabContent').children()[1].classList.remove('fade');

        }

        //Oculta campo
        $("div[data-name='show_panel_c']").hide();
        // Se oculta el boton de mas opciones en las petañas de cuentas(record)
        $('.nav-tabs li a.dropdown-toggle').hide();
    },

    /**
     * 
     * Manda a llamar función padre de record.js
     * Se aplica condición para quitar clase "active" al panel de vista360 ya que para ocultar o mostrar el panel se utiliza js en la función  _hideVista360
     * Ya que al agregar dicha clase "active" siempre deja la pestaña de vista360 activa (LBL_RECORDVIEW_PANEL8)
     */

    setActiveTab: function (event) {
        if (!$(event.currentTarget).parent().hasClass("LBL_RECORDVIEW_PANEL8")) {
            //Al remover clase active, a la pestaña se le quita el color azul
            $(".LBL_RECORDVIEW_PANEL8").removeClass("active");

            //Se oculta el contenido de la pestaña de Vista 360
            $("#tabContent>div.tab-pane").addClass("fade");
            $("#tabContent>div.tab-pane").removeClass("active");

            //Obtenemos id del contenido al que se hace referencia la pestaña
            var idElement = $(event.currentTarget)[0].href.split("#")[1];
            //Clase active agrega espacio para mostrar contenido
            $("#" + idElement).addClass("active");
            //Al remover clase fade, se muestra el contenido de la pestaña correspondiente
            $("#" + idElement).removeClass("fade");

        }
        this._super('setActiveTab', [event]);
    },


    _hideFideicomiso: function () {
        if (this.model.get('tct_fedeicomiso_chk_c')) {
            //Muestra
            this.$("li.tab.LBL_RECORDVIEW_PANEL2").show();

        } else {
            //Oculta
            this.$("li.tab.LBL_RECORDVIEW_PANEL2").hide();
        }
    },

    /** BEGIN CUSTOMIZATION:
     * Salvador Lopez 19/01/2018
     * Descripción: Función que oculta o muestra paneles de Peps según sea el valor de Tipo de Persona*/

    _hidePeps: function () {

        if (this.model.get('tipodepersona_c') == "Persona Fisica" ||
            this.model.get('tipodepersona_c') == "Persona Fisica con Actividad Empresarial") {
            //Muestra Peps de Persona Física
            this.$("[data-panelname='LBL_RECORDVIEW_PANEL4']").show();
            this.$("[data-panelname='LBL_RECORDVIEW_PANEL5']").show();
            //Oculta Peps de Persona Moral
            this.$("[data-panelname='LBL_RECORDVIEW_PANEL7']").hide();
            this.$("[data-panelname='LBL_RECORDVIEW_PANEL6']").hide();
            //Oculta Propietario Real
            this.$("[data-panelname='LBL_RECORDVIEW_PANEL9']").hide();
        } else {
            //Oculta Peps de Persona Física
            this.$("[data-panelname='LBL_RECORDVIEW_PANEL4']").hide();
            this.$("[data-panelname='LBL_RECORDVIEW_PANEL5']").hide();
            //Muestra Peps de Persona Moral
            this.$("[data-panelname='LBL_RECORDVIEW_PANEL7']").show();
            this.$("[data-panelname='LBL_RECORDVIEW_PANEL6']").show();
            //Muestra Propietario Real
            if (this.model.get('tipo_registro_cuenta_c') == "3") {
                this.$("[data-panelname='LBL_RECORDVIEW_PANEL9']").show();
            }
        }
    },

    readOnlyOrigen: function () {
        //Recupera variables
        var origen = this.model.get('origen_cuenta_c');
        var puesto = App.user.attributes.puestousuario_c; //27=> Agente Tel, 31=> Coordinador CP,
        var listaEdicionOrigen = [];    //Recupera Ids de usuarios que pueden editar origen
        Object.entries(App.lang.getAppListStrings('usuario_edicion_origen_list')).forEach(([key, value]) => {
            listaEdicionOrigen.push(value);
        });
        listaEdicionOrigen.includes(App.user.attributes.id)

        /*
         -- Bloquea campos si;
         1.- Origen es Marketing = 1 o Inteligencia de negocio = 2 (Ya no aplica la regla de solo lectura cuando el Origen es Marketing, solo se deja la condición para Inteligencia de negocio)
         2.- Puesto es diferente de Agente Tel. y Coordinador de centro de prospección
         3.- Usuario no está en lista de Usuario que pueden editar
         */
        if ((origen == "2") && (puesto != '27' && puesto != '31') && !listaEdicionOrigen.includes(App.user.attributes.id)) {
            //Establece como no editables campos de origen
            this.noEditFields.push('origen_cuenta_c');
            this.noEditFields.push('detalle_origen_c');
            this.noEditFields.push('tct_origen_base_ddw_c');
            this.noEditFields.push('tct_origen_busqueda_txf_c');
            this.noEditFields.push('medio_detalle_origen_c');
            this.noEditFields.push('punto_contacto_origen_c');
            this.noEditFields.push('evento_c');
            this.noEditFields.push('camara_c');
            this.noEditFields.push('tct_que_promotor_rel_c');
            this.noEditFields.push('como_se_entero_c');
            this.noEditFields.push('cual_c');
            //Deshabilita campos de Origen
            this.$("[data-name='origen_cuenta_c']").attr('style', 'pointer-events:none;');
            this.$("[data-name='detalle_origen_c']").attr('style', 'pointer-events:none;');
            this.$("[data-name='tct_origen_base_ddw_c']").attr('style', 'pointer-events:none;');
            this.$("[data-name='tct_origen_busqueda_txf_c']").attr('style', 'pointer-events:none;');
            this.$("[data-name='medio_detalle_origen_c']").attr('style', 'pointer-events:none;');
            this.$("[data-name='punto_contacto_origen_c']").attr('style', 'pointer-events:none;');
            this.$("[data-name='evento_c']").attr('style', 'pointer-events:none;');
            this.$("[data-name='camara_c']").attr('style', 'pointer-events:none;');
            this.$("[data-name='tct_que_promotor_rel_c']").attr('style', 'pointer-events:none;');
            this.$("[data-name='como_se_entero_c']").attr('style', 'pointer-events:none;');
            this.$("[data-name='cual_c']").attr('style', 'pointer-events:none;');

        }
    },

    /* BEGIN CUSTOMIZATION:
     * Salvador Lopez 21/02/2018
     * Refresca pantalla para mostrar telefonos y direcciones */
    refresca: function () {
        var telefonos = this.getField('account_telefonos');
        if (telefonos.action !== "edit") {
            this.render();
        }
    },

    borraTel: function () {
        var delids = window.ids;
        for (i = 0; i < delids.length; i++) {
            var idtel = delids[i];
            app.api.call('delete', app.api.buildURL('Tel_Telefonos/' + idtel), null, {
                success: _.bind(function (data) {
                    console.log('Esto es lo que devuelve la funcion borratel:');
                    console.log(data);
                    console.log(app.api.buildURL('Tel_Telefonos/' + idtel));
                }, this),
                error: _.bind(function (error) {
                    console.log("Este fue el error:", error)
                }, this),
            });
        }
    },

    handleCancel: function () {
        this._super("handleCancel");
        this.$('#rfcModal').hide();
        /*		if(contexto_cuenta.cambioEdit != undefined && contexto_cuenta.cambioEdit != 0 && contexto_cuenta.cambio_previo_mail != undefined ){
                    var rfc_c = this.model._previousAttributes.rfc_c;
                    var tipodepersona_c = this.model._previousAttributes.tipodepersona_c;
                    var razonsocial_c = this.model._previousAttributes.razonsocial_c;
                    var nombre_comercial_c = this.model._previousAttributes.nombre_comercial_c;
                    var fechaconstitutiva_c = this.model._previousAttributes.fechaconstitutiva_c;
                    var primernombre_c = this.model._previousAttributes.primernombre_c;
                    var apellidopaterno_c = this.model._previousAttributes.apellidopaterno_c;
                    var apellidomaterno_c = this.model._previousAttributes.apellidomaterno_c;
                    var fechadenacimiento_c = this.model._previousAttributes.fechadenacimiento_c;
                    var curp_c = this.model._previousAttributes.curp_c;
                    var email = '';
                    if( contexto_cuenta.cambio_previo_mail==1){
                        email = this.model._previousAttributes.email;
                    }else{
                        email = this.model.attributes.email;
                    }
                }
        */
        //Teléfonos
        var account_telefonos = app.utils.deepCopy(this.prev_oTelefonos.prev_telefono);
        this.model.set('account_telefonos', account_telefonos);
        this.oTelefonos.telefono = account_telefonos;
        cont_tel.render();

        //Direcciones
        var account_direcciones = app.utils.deepCopy(this.prev_oDirecciones.prev_direccion);
        this.model.set('account_direcciones', account_direcciones);
        this.oDirecciones.direccion = account_direcciones;
        cont_dir.nuevaDireccion = cont_dir.limpiaNuevaDireccion();
        cont_dir.render();

        //PLD
        var accounts_tct_pld_1 = app.utils.deepCopy(contexto_cuenta.prev_ProductosPLD);
        this.model.set('accounts_tct_pld_1', accounts_tct_pld_1);
        pld.ProductosPLD = accounts_tct_pld_1;
        pld.render();
        //Potencial Autos
        Pautos.autos = app.utils.deepCopy(Pautos.prev_autos);
        // this.model.set('potencial_autos', Pautos);
        Pautos.render();
        // this.model._previousAttributes.account_telefonos = account_telefonos;
        // this.model._previousAttributes.account_direcciones = account_direcciones;

        this.$('[data-name="promotorleasing_c"]').attr('style', '');
        this.$('[data-name="promotorfactoraje_c"]').attr('style', '');
        this.$('[data-name="promotorcredit_c"]').attr('style', '');
        this.$('[data-name="promotorfleet_c"]').attr('style', '');
        /*
                if(contexto_cuenta.cambioEdit != undefined && contexto_cuenta.cambioEdit != 0 && contexto_cuenta.cambio_previo_mail != undefined ){
                    this.model.set( 'rfc_c', rfc_c);
                    this.model.set( 'tipodepersona_c', tipodepersona_c);
                    this.model.set( 'email', email);
        
                    if(tipodepersona_c == "Persona Moral") {
                        this.model.set( 'razonsocial_c', razonsocial_c);
                        this.model.set( 'nombre_comercial_c', nombre_comercial_c);
                        this.model.set( 'fechaconstitutiva_c', fechaconstitutiva_c);
                    }else {
                        this.model.set( 'primernombre_c', primernombre_c);
                        this.model.set( 'apellidopaterno_c', apellidopaterno_c);
                        this.model.set( 'apellidomaterno_c', apellidomaterno_c);
                        this.model.set( 'fechadenacimiento_c', fechadenacimiento_c);
                        this.model.set( 'curp_c', curp_c);
                    }
                    contexto_cuenta.cambio_previo_mail = 0;
                }
        */
        //Valores Previos Clasificacion Sectorial - Actividad Economica e INEGI
        clasf_sectorial.ActividadEconomica = app.utils.deepCopy(clasf_sectorial.prevActEconomica);
        clasf_sectorial.ResumenCliente.inegi.inegi_clase = clasf_sectorial.prevActEconomica.inegi_clase;
        clasf_sectorial.ResumenCliente.inegi.inegi_subrama = clasf_sectorial.prevActEconomica.inegi_subrama;
        clasf_sectorial.ResumenCliente.inegi.inegi_rama = clasf_sectorial.prevActEconomica.inegi_rama;
        clasf_sectorial.ResumenCliente.inegi.inegi_subsector = clasf_sectorial.prevActEconomica.inegi_subsector;
        clasf_sectorial.ResumenCliente.inegi.inegi_sector = clasf_sectorial.prevActEconomica.inegi_sector;
        clasf_sectorial.ResumenCliente.inegi.inegi_macro = clasf_sectorial.prevActEconomica.inegi_macro;
        clasf_sectorial.render();
    },

    handleEdit: function (e, cell) {
        var target,
            cellData,
            field;

        if (e) { // If result of click event, extract target and cell.
            target = this.$(e.target);
            cell = target.parents('.record-cell');
            // hide tooltip
            this.handleMouseLeave(e);
        }

        cellData = cell.data();
        field = this.getField(cellData.name);

        // If the focus drawer icon was clicked, open the focus drawer instead
        // of entering edit mode
        if (target && target.hasClass('focus-icon') && field && field.focusEnabled) {
            field.handleFocusClick();
            return;
        }

        // Set Editing mode to on.
        this.inlineEditMode = true;

        this.setButtonStates(this.STATE.EDIT);

        this.toggleField(field);

        if (this.$('.headerpane').length > 0) {
            this.toggleViewButtons(true);
            this.adjustHeaderpaneFields();
        }
        this.deshabilitaOrigenCuenta();
    },

    /*
    Se sobreescribe la función de caja para poder evaluar si los campos de origen se deben de bloquear ya que a nivel de dependencoa
    no estaba tomando los diapradores para bloquear dichos campos
    */
    focusFirstInput: function () {
        var self = this;
        $(function () {
            var $element = (app.drawer && (app.drawer.count() > 0)) ?
                app.drawer._components[app.drawer.count() - 1].$el
                : app.$contentEl;
            var $firstInput = $element.find('input[type=text]').first();

            if (($firstInput.length > 0) && $firstInput.is(':visible')) {
                $firstInput.focus();
                self.setCaretToEnd($firstInput);
            }
            self.deshabilitaOrigenCuenta();
        });
    },

    bindDataChange: function () {
        this._super("bindDataChange");
        //Si el registro es Persona Fisica, ya no se podra cambiar a Persona Moral
        this.model.on("change:tipodepersona_c", _.bind(function () {

            if (this.model._previousAttributes.tipodepersona_c == 'Persona Fisica') {
                if (this.model.get('tipodepersona_c') == 'Persona Moral') {
                    this.model.set('tipodepersona_c', 'Persona Fisica');
                }
            }
            if (this.model._previousAttributes.tipodepersona_c == 'Persona Fisica con Actividad Empresarial') {
                if (this.model.get('tipodepersona_c') == 'Persona Moral') {
                    this.model.set('tipodepersona_c', 'Persona Fisica con Actividad Empresarial');
                }
            }
            //Si es Persona Moral, ya no se podra cambiar a Persona Fisica
            if (this.model._previousAttributes.tipodepersona_c == 'Persona Moral') {
                if (this.model.get('tipodepersona_c') == 'Persona Fisica' || this.model.get('tipodepersona_c') == 'Persona Fisica con Actividad Empresarial') {
                    this.model.set('tipodepersona_c', 'Persona Moral');
                }
            }
        }, this));
    },

    _doValidateTieneContactos: function (fields, errors, callback) {
        if (this.model.get('tipodepersona_c') == "Persona Moral" && (/*this.model.get('tipo_registro_cuenta_c') == "Cliente" || this.model.get('estatus_c') == "Interesado" || */this.model.get('tipo_registro_cuenta_c') == "2")) {
            app.api.call("read", app.api.buildURL("Accounts/" + this.model.get('id') + "/link/rel_relaciones_accounts_1", null, null, {
                fields: name,
            }), null, {
                success: _.bind(function (data) {
                    var ContacFlag = false;

                    if (data.records.length > 0) {
                        $(data.records).each(function (index, value) {
                            if ($.inArray("Contacto", value.relaciones_activas) > -1) {
                                //YES IS A CONTACT!!!!
                                ContacFlag = true;
                            }
                        });
                    }

                    if (ContacFlag == false && _.isEmpty(this.model.get('account_contacts'))) {
                        app.alert.show("Cliente sin contactos registrados", {
                            level: "error",
                            title: "Debe registrar al menos un contacto para el cliente.",
                            autoClose: false
                        });
                        errors['account_contacts'] = errors['account_contacts'] || {};
                        errors['account_contacts'].required = true;
                    }
                }, this)
            });
        }
        callback(null, fields, errors);
    },

    /** BEGIN CUSTOMIZATION: jgarcia@levementum.com 8/26/2015 Description: When Pais or Profesion field is changed, Recalculate the Riesgo */
    _doValidateProfesionRisk: function () {
        var riskCounter = 0;
        if (this.model.get("lista_negra_c") == "0" || this.model.get("pep_c") == "0") {
            if (!_.isEmpty(this.model.get("pais_nacimiento_c"))) {
                app.api.call("read", app.api.buildURL("dire_Pais/", null, null, {
                    fields: "altoriesgo",
                    "filter": [
                        {
                            "id": this.model.get("pais_nacimiento_c")
                        }
                    ]
                }), null, {
                    success: _.bind(function (data) {
                        if (data.records.length > 0) {
                            if (data.records[0].altoriesgo == true) {
                                riskCounter++;
                            }
                        }
                        if (!_.isEmpty(this.model.get("profesion_c"))) {
                            var profesionActual = this.model.get("profesion_c");
                            var profesiones_de_riesgo = app.lang.getAppListStrings('profesion_riesgo_list');
                            Object.keys(profesiones_de_riesgo).forEach(function (key) {
                                if (key == profesionActual) {
                                    riskCounter++;
                                }
                            });
                        }
                        if (riskCounter > 0) {
                            this.model.set("riesgo_c", "Alto");
                        } else {
                            this.model.set("riesgo_c", "Bajo");
                        }
                    }, this)
                });
            }
        }
    },

    /**
     * Función para habilitar campos a solo lectura evaluando condiciones específicas
     */
    _renderHtml: function ()
    //Establecer todos los campos como solo lectura cuando el registro actual es el contacto genérico
    {
        var id = app.lang.getAppListStrings('tct_persona_generica_list');
        if (this.model.get('id') === id['accid'] && app.user.get('type') !== 'admin') {
            var self = this;
            _.each(this.model.fields, function (field) {
                self.noEditFields.push(field.name);
            });
        }

        /*
         @author Victo Martinez - 01/08/2018
         Deshabilita campos: Tipo de cuenta y subtipo de cuenta
         */
        //        this.noEditFields.push('subtipo_registro_cuenta_c');
        //        this.noEditFields.push('tipo_registro_cuenta_c');

        /*
         *
         * Salvador Lopez <salvador.lopez@tactos.com.mx>
         */
        //Establecer campos de promotores como solo lectura cuando el id_cliente_c ya ha sido llenado
        if (this.model.get('id') !== "") {
            var self = this;
            self.noEditFields.push('promotorleasing_c');
            self.noEditFields.push('promotorfactoraje_c');
            self.noEditFields.push('promotorcredit_c');
            self.noEditFields.push('promotorfleet_c');
            self.noEditFields.push('promotoruniclick_c');
            self.noEditFields.push('promotorrm_c');
            self.noEditFields.push('tipo_registro_cuenta_c');
        }

        /*var origen = this.model.get('origen_cuenta_c');
        if (origen == "Marketing" || origen == "2") {
            var self = this;
            self.noEditFields.push('origen_cuenta_c');
            self.noEditFields.push('detalle_origen_c');
            self.noEditFields.push('tct_origen_base_ddw_c');
            self.noEditFields.push('tct_origen_ag_tel_rel_c');
            self.noEditFields.push('tct_origen_busqueda_txf_c');
            self.noEditFields.push('medio_detalle_origen_c');
            self.noEditFields.push('punto_contacto_origen_c');
            self.noEditFields.push('evento_c');
            self.noEditFields.push('camara_c');
            self.noEditFields.push('tct_que_promotor_rel_c');
        }*/

        if (App.user.attributes.deudor_factoraje_c != true) {
            //Readonly check factoraje
            self.noEditFields.push('deudor_factor_c');
        }

        if (this.model.get('no_website_c')) {
            self.noEditFields.push('website');
        }

        //Oculta menú lateral para relaciones
        $('[data-subpanel-link="rel_relaciones_accounts_1"]').find(".dropdown-toggle").hide();

        if (App.user.attributes.puestousuario_c != 32 && App.user.attributes.puestousuario_c != 47) {
            //Se agrega validacion para la lista de Vendors y puedan editar el campo Tipo Proveedor Compras C
            var Banderita = 0;
            Object.entries(App.lang.getAppListStrings('equipo_a_eco_y_est_list')).forEach(([key, value]) => {
                if (value == App.user.attributes.id) {
                    Banderita = 1;
                }
            });
            if (Banderita != 1) {
                self.noEditFields.push('tipo_proveedor_compras_c');
                self.noEditFields.push('vendor_c');
            }
        }

        //Campos Denominación y Régimen Fiscal SAT
        var listaUsuarios = [];
        Object.entries(App.lang.getAppListStrings('actualiza_sat_list')).forEach(([key, value]) => {
            listaUsuarios.push(value);
        });
        if (!listaUsuarios.includes(app.user.attributes.id)) {
            self.noEditFields.push('denominacion_c');
            self.noEditFields.push('regimen_fiscal_sat_c');
        }
        this._super('_renderHtml');
    },

    _render: function (options) {
        //Oculta menú lateral para relaciones
        $('[data-subpanel-link="rel_relaciones_accounts_1"]').find(".dropdown-toggle").hide();

        this._super("_render");

        //Ocultar campo "Ruta de Imagen QR" siempre. Se agregó a la vista para que esté disponible a través de this.model
        $('[data-name="path_img_qr_c"]').hide();

        //Ocultar campo "No Contactar" siempre. Se agregó a la vista para que esté disponible a través de this.model
        $('[data-name="tct_no_contactar_chk_c"]').hide();

        //campo Pais que expide el RFC nace oculto.
        // $('[data-name=tct_pais_expide_rfc_c]').hide();
        // $('div[data-name=accounts_tct_pld]').find('div.record-label').addClass('hide');
        $('[data-name=tct_nuevo_pld_c]').hide(); //Oculta campo tct_nuevo_pld_c
        //Oculta la etiqueta del campo PLD
        this.$('div[data-name=accounts_tct_pld]').find('div.record-label').addClass('hide');
        //Oculta nombre de campo Potencial_Autos
        $("div.record-label[data-name='potencial_autos']").attr('style', 'display:none;');
        //Oculta etiqueta de Analizate y analizate cliente
        this.$("div.record-label[data-name='accounts_analizate']").attr('style', 'display:none;');
        this.$("div.record-label[data-name='accounts_analizate_clientes']").attr('style', 'display:none;');
        //Oculta etiqueta de uni_productos
        this.$("div.record-label[data-name='account_uni_productos']").attr('style', 'display:none;');

        //Se oculta check de cuenta homonima
        $('div[data-name=tct_homonimo_chk_c]').hide();
        //Oculta etiqueta del campo Tipo de Cuenta por Producto
        this.$('div[data-name=cuenta_productos]').find('div.record-label').addClass('hide');
        //@Jesus Carrillo
        //Ocultar Div y boton "Prospecto Contactado"
        $('div[data-name=tct_prospecto_contactado_chk_c]').hide();
        //Oculta campo proveedor
        $('[name="portal_proveedores"]').hide();


        // Validación para no poder inactivar clientes con contratos activos
        if (this.model.dataFetched) {
            this.model.on("change:estatus_persona_c", _.bind(function () {
                if (this.model.get('estatus_persona_c') == "I" && this.model.get('idcliente_c')) {
                    //Si el Cliente con valor inactivo, verificar que no tenga contratos activos:
                    //se requiere el número de cliente
                    app.api.call("read", app.api.buildURL("Accounts/AccountsCustomAPI/" + this.model.get('idcliente_c'), null, null, {}), null, {
                        success: _.bind(function (data) {
                            if (data.UNI2_CTE_029_VerificaClienteTieneContratoResult._tieneContratos == true) {
                                app.alert.show("Validar Contratos", {
                                    level: "error",
                                    title: "No puedes inactivar clientes con contratos activos",
                                    autoClose: false
                                });
                                this.cancelClicked();
                            }
                        }, this)
                    });
                }
            }, this));
        }

        if (!_.isEmpty(this.model.get('idcliente_c'))) {
            app.api.call("read", app.api.buildURL("Accounts/AccountsCustomAPI/" + this.model.get('idcliente_c'), null, null, {}), null, {
                success: _.bind(function (data) {
                    /*
                     * @author Carlos Zaragoza
                     * Validar campos editables de persona cuando se trata de cliente con contratos activos (falta definición UNIFIN - Deben bloquearse RFC, Razón social /nombres y dirección fiscal)
                     * */
                    if (data.UNI2_CTE_029_VerificaClienteTieneContratoResult._tieneContratos == true) {
                        self.hasContratosActivos = true;
                        $('.record-edit-link-wrapper[data-name="rfc_c"]').remove();
                        $('.record-edit-link-wrapper[data-name="razonsocial_c"]').remove();
                        $('.record-edit-link-wrapper[data-name="primernombre_c"]').remove();
                        $('.record-edit-link-wrapper[data-name="segundonombre_c"]').remove();
                        $('.record-edit-link-wrapper[data-name="apellidopaterno_c"]').remove();
                        $('.record-edit-link-wrapper[data-name="apellidomaterno_c"]').remove();

                    }
                }, this)
            });
        }

        //Oculta correo, telefonos y direcciones
        if (this.oculta === 1) {
            $('div[data-name=account_telefonos]').hide();
            $('div[data-name=email]').hide();
            $('div[data-name=account_direcciones]').hide();
        }
        else {
            $('div[data-name=account_telefonos]').show();
            $('div[data-name=email]').show();
            $('div[data-name=account_direcciones]').show();
        }

        //Evento para validar acciones
        $('a.btn.dropdown-toggle.btn-primary').on('click', function (e) {
            contexto_cuenta.hideButton_Conversion_change();

        });

        if (app.user.attributes.cuenta_especial_c == 0 || app.user.attributes.cuenta_especial_c == "") {
            $('div[data-name=cuenta_especial_c]').css("pointer-events", "none");
        }

        //
        this.$("div.record-label[data-name='rfc_qr']").attr('style', 'pointer-events:none;');
        this.$("div.record-label[data-name='rfc_qr']").attr('style', 'display:none;');

        // if (app.user.attributes.multilinea_c == 0 || app.user.attributes.multilinea_c == "") {
        //     $('div[data-name=multilinea_c]').css("pointer-events", "none");
        // }

        //Oculta campos de Macro Sector
        this.$("div[data-name='tct_macro_sector_ddw_c']").hide();
        this.$("div[data-name='sectoreconomico_c']").hide();
        this.$("div[data-name='subsectoreconomico_c']").hide();
        this.$("div[data-name='actividadeconomica_c']").hide();
        this.$(".record-cell[data-name='blank_space']").hide();

        //Oculta campos de Dynamics
        $('[data-name="control_dynamics_365_c"]').hide();
        $('[data-name="id_cpp_365_chk_c"]').hide();

        //Oculta fecha de bloqueo para saber si el Origen se habilita
        $('[data-name="fecha_bloqueo_origen_c"]').hide();

        //Oculta etiquetas 360
        this.$('.record-edit-link-wrapper[data-name="account_vista360"]').remove();
        this.$('div[data-name=account_vista360]').find('div.record-label').addClass('hide');
        //OCULTA ó MUESTRA BOTON DE SOLICITUD ASIGNACION
        this._hideBtnSolicitudAsignacion();
    },

    editClicked: function () {
        this._super("editClicked");
        this.$('[data-name="promotorleasing_c"]').attr('style', 'pointer-events:none');
        this.$('[data-name="promotorfactoraje_c"]').attr('style', 'pointer-events:none');
        this.$('[data-name="promotorcredit_c"]').attr('style', 'pointer-events:none');
        this.$('[data-name="promotorfleet_c"]').attr('style', 'pointer-events:none');
        this.$('[data-name="promotorrm_c"]').attr('style', 'pointer-events:none');
        var roles = App.user.attributes.roles;
        var roles_seguros = App.lang.getAppListStrings('roles_edicion_ctas_seguros_list');

        var seguros = 0;
        for (const [key, value] of Object.entries(roles_seguros)) {
            if (roles.includes(value)) {
                seguros = 1;
            }
        }
        var accesoFiscal = App.user.attributes.tct_alta_clientes_chk_c + App.user.attributes.tct_altaproveedor_chk_c + App.user.attributes.tct_alta_cd_chk_c + App.user.attributes.deudor_factoraje_c + seguros;
        if (accesoFiscal == 0 && this.model.get('tipo_registro_cuenta_c') != '4') {
            this.$('div[data-name=rfc_c]').css("pointer-events", "none");
            $('[data-name="generar_rfc_c"]').hide();
        }
        contexto_cuenta.cambioEdit = 1;
    },

    hideconfiinfo: function () {
        $('div[data-name=account_telefonos]').hide();
        $('div[data-name=email]').hide();
        $('div[data-name=account_direcciones]').hide();
        //self=this;
        if (this.model.get('id') != "") {
            app.api.call('GET', app.api.buildURL('GetUsersBoss/' + this.model.get('id')), null, {
                success: _.bind(function (data) {
                    if (data == false) {
                        this.oculta = 1;
                        $('div[data-name=account_telefonos]').hide();
                        $('div[data-name=email]').hide();
                        $('div[data-name=account_direcciones]').hide();
                    } else {
                        this.oculta = 0;
                        $('div[data-name=account_telefonos]').show();
                        $('div[data-name=email]').show();
                        $('div[data-name=account_direcciones]').show();
                    }
                    return data;
                }, this),
            });
            //self.render();
        }
        console.log("valor fuera " + this.model.get('id'));
    },

    disable_panels_rol: function () {

        self_this = this;
        if (this.model.get('id') != "") {
            var roles_limit = app.lang.getAppListStrings('edicion_cuentas_list');
            var roles_logged = app.user.attributes.roles;
            var coincide_rol = 0;
            for (var i = 0; i < roles_logged.length; i++) {
                for (var rol_limit in roles_limit) {
                    if (roles_logged[i] == roles_limit[rol_limit]) {
                        coincide_rol++;
                    }
                }
            }
            if (coincide_rol != 0) {
                app.api.call('GET', app.api.buildURL('GetUsersBoss/' + this.model.get('id')), null, {
                    success: _.bind(function (data) {
                        console.log(data);
                        if (data == false) {

                            if (this.model.get('tipo_registro_cuenta_c') != "4") {

                                //$('.noEdit.fieldset.actions.detail.btn-group').hide();
                                self_this.$('[data-event="button:edit_button:click"]').hide();

                                $('i').removeClass('fa-pencil');

                                $('.record-cell').children().not('.normal.index').click(function (e) { //Habilita solo links
                                    e.stopPropagation();
                                    e.preventDefault();
                                    e.stopImmediatePropagation();
                                    return false;
                                });
                            }
                        }
                        return data;
                    }, this),
                });
                //self.render();
            }
        }
    },

    disable_panels_team: function () {
        self1 = this;
        if (this.model.get('id') != "") {
            var roles_limit = app.lang.getAppListStrings('edicion_cuentas_list');
            var roles_logged = app.user.attributes.roles;
            var coincide_rol = 0;
            for (var i = 0; i < roles_logged.length; i++) {
                for (var rol_limit in roles_limit) {
                    if (roles_logged[i] == roles_limit[rol_limit]) {
                        coincide_rol++;
                    }
                }
            }
            if (coincide_rol != 0) {
                app.api.call('GET', app.api.buildURL('GetUsersTeams/' + this.model.get('id') + '/Accounts'), null, {
                    success: _.bind(function (data) {
                        console.log(data);
                        if (data == false) {

                            if (this.model.get('tipo_registro_cuenta_c') != "4") {

                                //$('.noEdit.fieldset.actions.detail.btn-group').hide();
                                self1.$('[data-event="button:edit_button:click"]').hide();


                                $('i').removeClass('fa-pencil');

                                $('.record-cell').children().not('.normal.index').click(function (e) { //Habilita solo links
                                    e.stopPropagation();
                                    e.preventDefault();
                                    e.stopImmediatePropagation();
                                    return false;
                                });
                            }
                        } else {
                            /*$('.noEdit.fieldset.actions.detail.btn-group').hide();
                             $('i').removeClass('fa-pencil');

                             var panels_hide = app.lang.getAppListStrings('panels_hide_list');
                             var fields_hide = app.lang.getAppListStrings('fields_hide_list');

                             for (var panel in panels_hide) {
                             if(panel=='LBL_RECORDVIEW_PANEL9' || panel=='LBL_RECORDVIEW_PANEL13'){
                             $('.record-cell').children().not('.normal.index').click(function (e) { //Habilita solo links
                             e.stopPropagation();
                             e.preventDefault();
                             e.stopImmediatePropagation();
                             return false;
                             });
                             }else {
                             $('.row-fluid.panel_body.' + panel).click(function (e) {
                             e.stopPropagation();
                             e.preventDefault();
                             e.stopImmediatePropagation();
                             return false;
                             });
                             }
                             }

                             for (var field in fields_hide) {
                             $('div[data-name='+field+']').click(function (e) {
                             e.stopPropagation();
                             e.preventDefault();
                             e.stopImmediatePropagation();
                             return false;
                             });
                             }*/

                        }
                        return data;
                    }, this),
                });
                //self.render();
            }
        }
    },


    /*
     * @author F. Javier G. Solar
     * 18/07/2018
     * Se debe ocultar los botones de Regresa a Lead y Prospecto contactado si cumple
     * con las condiciones de visibilidad .
     * */
    hideButton_Conversion: function () {
        var myField = this.getField("regresalead");
        var myField1 = this.getField("prospectocontactado");
        var myField2 = this.getField("conviertelead");
        // var myField3 = this.getField("clienteuniclick");
        var myField4 = this.getField("portal_proveedores");

        if (myField) {
            myField.listenTo(myField, "render", function () {
                var leasingprod = Oproductos.productos.tct_tipo_cuenta_l_c;
                var factprod = Oproductos.productos.tct_tipo_cuenta_f_c;
                var caprod = Oproductos.productos.tct_tipo_cuenta_ca_c;
                var fleetprod = Oproductos.productos.tct_tipo_cuenta_fl_c;
                var ucprod = Oproductos.productos.tct_tipo_cuenta_uc_c;
                var leasingsub = Oproductos.productos.tct_subtipo_l_txf_c;
                var factsub = Oproductos.productos.tct_subtipo_f_txf_c;
                var casub = Oproductos.productos.tct_subtipo_ca_txf_c;
                var fleetsub = Oproductos.productos.tct_subtipo_fl_txf_c;
                var userprod = App.user.attributes.productos_c;
                var logueado = App.user.id;
                var asesorL = this.model.get('user_id_c');
                var asesorF = this.model.get('user_id1_c');
                var asesorCA = this.model.get('user_id2_c');
                var asesorFL = this.model.get('user_id6_c');
                var asesorUC = this.model.get('user_id7_c');
                myField.hide();
                if ((leasingprod == "2" && leasingsub == "2" && userprod.includes('1') && asesorL == logueado) || (factprod == "2" && factsub == "2" && userprod.includes("4") && asesorF == logueado) || (caprod == "2" && casub == "2" && userprod.includes("3") && asesorCA == logueado) ||
                    (fleetprod == "2" && fleetsub == "2" && userprod.includes('6') && asesorFL == logueado)) {
                    myField.show();
                } else {
                    myField.hide();
                }
            });
        }
        if (myField1) {
            myField1.listenTo(myField1, "render", function () {
                myField1.hide();
                var leasingprod = Oproductos.productos.tct_tipo_cuenta_l_c;
                var factprod = Oproductos.productos.tct_tipo_cuenta_f_c;
                var caprod = Oproductos.productos.tct_tipo_cuenta_ca_c;
                var fleetprod = Oproductos.productos.tct_tipo_cuenta_fl_c;
                var ucprod = Oproductos.productos.tct_tipo_cuenta_uc_c;
                var leasingsub = Oproductos.productos.tct_subtipo_l_txf_c;
                var factsub = Oproductos.productos.tct_subtipo_f_txf_c;
                var casub = Oproductos.productos.tct_subtipo_ca_txf_c;
                var fleetsub = Oproductos.productos.tct_subtipo_fl_txf_c;
                var userprod = App.user.attributes.productos_c;
                var logueado = App.user.id;
                var asesorL = this.model.get('user_id_c');
                var asesorF = this.model.get('user_id1_c');
                var asesorCA = this.model.get('user_id2_c');
                var asesorFL = this.model.get('user_id6_c');
                var asesorUC = this.model.get('user_id7_c');
                //Para mostrar/ocultar el boton de convertir a Lead y Convertir a Prospecto Contactado. 22/08/2018
                if ((leasingprod == "1" && userprod.includes('1') && asesorL == logueado) || (factprod == "1" && userprod.includes("4") && asesorF == logueado) || (caprod == "1" && userprod.includes("3") && asesorCA == logueado) ||
                    (fleetprod == "1" && userprod.includes('6') && asesorFL == logueado)) {
                    myField1.show();
                } else {
                    myField1.hide();
                }
            });
        }
        if (myField2) {
            myField2.listenTo(myField2, "render", function () {
                var leasingprod = Oproductos.productos.tct_tipo_cuenta_l_c;
                var factprod = Oproductos.productos.tct_tipo_cuenta_f_c;
                var caprod = Oproductos.productos.tct_tipo_cuenta_ca_c;
                var fleetprod = Oproductos.productos.tct_tipo_cuenta_fl_c;
                var ucprod = Oproductos.productos.tct_tipo_cuenta_uc_c;
                var leasingsub = Oproductos.productos.tct_subtipo_l_txf_c;
                var factsub = Oproductos.productos.tct_subtipo_f_txf_c;
                var casub = Oproductos.productos.tct_subtipo_ca_txf_c;
                var fleetsub = Oproductos.productos.tct_subtipo_fl_txf_c;
                var userprod = App.user.attributes.productos_c;
                var logueado = App.user.id;
                var asesorL = this.model.get('user_id_c');
                var asesorF = this.model.get('user_id1_c');
                var asesorCA = this.model.get('user_id2_c');
                var asesorFL = this.model.get('user_id6_c');
                var asesorUC = this.model.get('user_id7_c');
                myField2.hide();
                //Antes de mostrar el campo, hay que validar si los valores vienen como string "PROVEEDOR" o como número "5"
                if (isNaN(Number(leasingprod))) {//el valor viene como string
                    if (((leasingprod.toLowerCase() == "proveedor" || leasingprod.toLowerCase() == "persona") && userprod.includes('1') && asesorL == logueado) || ((factprod.toLowerCase() == "proveedor" || factprod.toLowerCase() == "persona") && userprod.includes("4") && asesorF == logueado) || ((caprod.toLowerCase() == "proveedor" || caprod.toLowerCase() == "persona") && userprod.includes("3") && asesorCA == logueado) ||
                        ((fleetprod.toLowerCase() == "proveedor" || fleetprod.toLowerCase() == "persona") && userprod.includes('6') && asesorFL == logueado) || ((ucprod.toLowerCase() == "proveedor" || ucprod.toLowerCase() == "persona") && userprod.includes('8') && asesorUC == logueado)) {
                        myField2.show();
                    } else {
                        myField2.hide();
                    }
                } else {//el valor viene como número
                    if (((leasingprod == "5" || leasingprod == "4") && userprod.includes('1') && asesorL == logueado) || ((factprod == "5" || factprod == "4") && userprod.includes("4") && asesorF == logueado) || ((caprod == "5" || caprod == "4") && userprod.includes("3") && asesorCA == logueado) ||
                        ((fleetprod == "5" || fleetprod == "4") && userprod.includes('6') && asesorFL == logueado) || ((ucprod == "5" || ucprod == "4") && userprod.includes('8') && asesorUC == logueado)) {
                        myField2.show();
                    } else {
                        myField2.hide();
                    }
                }
            });
        }
        // if (myField3) {
        //     myField3.listenTo(myField3, "render", function () {
        //         var conversioncUC = App.user.attributes.tct_alta_credito_simple_chk_c;
        //         var userprod = App.user.attributes.productos_c;
        //         var logueado = App.user.id;
        //         var uniclickval = Oproductos.productos.tct_tipo_cuenta_uc_c;
        //         var asesorUC = this.model.get('user_id7_c');
        //         myField3.hide();
        //         if ((uniclickval != "3" && userprod.includes('8') && asesorUC == logueado && conversioncUC == 1)) {
        //             myField3.show();
        //         } else {
        //             myField3.hide();
        //         }
        //     });
        // }
        if (myField4) {
            myField4.listenTo(myField4, "render", function () {
                myField4.hide();
                if ((this.model.get('esproveedor_c') == '1' || this.model.get('tipo_registro_cuenta_c') == '5') && App.user.attributes.portal_proveedores_c == '1' && !this.model.get('alta_portal_proveedor_chk_c')) {
                    myField4.show();
                } else {
                    myField4.hide();
                }
            });
        }

    },

    hideButton_Conversion_change: function () {
        var leasingprod = Oproductos.productos.tct_tipo_cuenta_l_c;
        var factprod = Oproductos.productos.tct_tipo_cuenta_f_c;
        var caprod = Oproductos.productos.tct_tipo_cuenta_ca_c;
        var fleetprod = Oproductos.productos.tct_tipo_cuenta_fl_c;
        var ucprod = Oproductos.productos.tct_tipo_cuenta_uc_c;
        var userprod = App.user.attributes.productos_c;
        var leasingsub = Oproductos.productos.tct_subtipo_l_txf_c;
        var factsub = Oproductos.productos.tct_subtipo_f_txf_c;
        var casub = Oproductos.productos.tct_subtipo_ca_txf_c;
        var subtipofleet = Oproductos.productos.tct_subtipo_fl_txf_c;
        var conversioncUC = App.user.attributes.tct_alta_credito_simple_chk_c;
        var uniclickval = Oproductos.productos.tct_tipo_uc_txf_c;
        var logueado = App.user.id;
        var asesorL = this.model.get('user_id_c');
        var asesorF = this.model.get('user_id1_c');
        var asesorCA = this.model.get('user_id2_c');
        var asesorFL = this.model.get('user_id6_c');
        var asesorUC = this.model.get('user_id7_c');
        //oculta botones
        $('[name="regresalead"]').hide();
        $('[name="prospectocontactado"]').hide();
        $('[name="conviertelead"]').hide();
        // $('[name="clienteuniclick"]').hide();

        //Evaluación para mostrar botones
        /*
         * Regresar a lead:
         * tipo_registro_cuenta_c = Prospecto
         * && subtipo_registro_cuenta_c = Contactado
         */
        if ((leasingprod == "2" && leasingsub == "2" && userprod.includes('1') && asesorL == logueado) || (factprod == "2" && factsub == "2" && userprod.includes("4") && asesorF == logueado) || (caprod == "2" && casub == "2" && userprod.includes("3") && asesorCA == logueado) ||
            (fleetprod == "2" && subtipofleet == "2" && userprod.includes('6') && asesorFL == logueado)) {
            $('[name="regresalead"]').show();
            $('[name="prospectocontactado"]').hide();
            $('[name="conviertelead"]').hide();
        }

        //Evaluación para mostrar botones
        /*
         * Prospecto contactado:
         * tipo_registro_cuenta_c = Lead
         */
        if ((leasingprod == "1" && userprod.includes('1') && asesorL == logueado) || (factprod == "1" && userprod.includes("4") && asesorF == logueado) || (caprod == "1" && userprod.includes("3") && asesorCA == logueado) || (fleetprod == "1" && userprod.includes('6') && asesorFL == logueado)) {
            $('[name="regresalead"]').hide();
            $('[name="prospectocontactado"]').show();
            $('[name="conviertelead"]').hide();
        }

        /*
         * Conviert a Lead:
         * tipo_registro_cuenta_c = Persona
         * OR tipo_registro_cuenta_c = Proveedor
         */
        if (((leasingprod == "4" || leasingprod == "5") && userprod.includes('1') && asesorL == logueado) || ((factprod == "4" || factprod == "5") && userprod.includes("4") && asesorF == logueado) || ((caprod == "4" || caprod == "5") && userprod.includes("3") && asesorCA == logueado) || ((fleetprod == "4" || fleetprod == "5") && userprod.includes('6') && asesorFL == logueado) || ((ucprod == "5" || ucprod == "4") && userprod.includes('8') && asesorUC == logueado)) {
            $('[name="regresalead"]').hide();
            $('[name="prospectocontactado"]').hide();
            $('[name="conviertelead"]').show();
        }

        //Evaluación para mostrar botones
        /*
         * Convertir Cliente Uniclick
         * tipo_registro_cuenta_c = Lead
         */
        // if ((uniclickval != "3" && userprod.includes('8') && asesorUC == logueado && conversioncUC == 1)) {
        //     $('[name="regresalead"]').hide();
        //     $('[name="clienteuniclick"]').show();
        //     $('[name="conviertelead"]').hide();
        //     $('[name="prospectocontactado"]').hide();
        // }

        //Boton de envio a Portal de Proveedores
        if ((this.model.get('esproveedor_c') == '1' || this.model.get('tipo_registro_cuenta_c') == '5') && App.user.attributes.portal_proveedores_c == '1' && !this.model.get('alta_portal_proveedor_chk_c')) {
            $('[name="portal_proveedores"]').show();
        } else {
            $('[name="portal_proveedores"]').hide();
        }
    },

    /* @author F. Javier Garcia S. 10/07/2018
     Funcion para  ser visible panel NPS si "Tipo de Cuenta" es "Cliente".
     */
    _hideNPS: function () {
        if (this.model.get('tipo_registro_cuenta_c') != "3") {
            this.$("[data-panelname='LBL_RECORDVIEW_PANEL10']").hide();
        }
    },

    /*
     @author Salvador Lopez
     Se omite la opción de "Persona" dentro del campo tipo_registro_cuenta_c
     * */
    deleteOptionPersona: function () {
        if (this.model.get('tipo_registro_cuenta_c') != null) {
            if (this.model.get('tipo_registro_cuenta_c') != '4') {
                var new_options = app.lang.getAppListStrings('tipo_registro_cuenta_list');
                Object.keys(new_options).forEach(function (key) {
                    if (key == "4") {
                        delete new_options[key];
                    }
                });

                this.model.fields['tipo_registro_cuenta_c'].options = new_options;
            }

        }
    },

    _ActualizaEtiquetas: function () {
        if (this.model.get('tipodepersona_c') != 'Persona Moral') {
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

    //Evento no acepta numeros, solo letras (a-z).
    checkTextOnly: function () {
        app.alert.dismiss('Error_validacion_Campos');
        var camponame = "";
        var expresion = new RegExp(/^[a-zA-ZÀ-ÿ\s]*$/g);
        if (this.model.get('primernombre_c') != "" && this.model.get('primernombre_c') != undefined) {
            var nombre = this.model.get('primernombre_c');
            var comprueba = expresion.test(nombre);
            if (comprueba != true) {
                camponame = camponame + '<b>-Primer Nombre<br></b>';
                ;
            }
        }
        if (this.model.get('apellidopaterno_c') != "" && this.model.get('apellidopaterno_c') != undefined) {
            var apaterno = this.model.get('apellidopaterno_c');
            var expresion = new RegExp(/^[a-zA-ZÀ-ÿ\s]*$/g);
            var validaap = expresion.test(apaterno);
            if (validaap != true) {
                camponame = camponame + '<b>-Apellido Paterno<br></b>';
                ;
            }
        }
        if (this.model.get('apellidomaterno_c') != "" && this.model.get('apellidomaterno_c') != undefined) {
            var amaterno = this.model.get('apellidomaterno_c');
            var expresion = new RegExp(/^[a-zA-ZÀ-ÿ\s]*$/g);
            var validaam = expresion.test(amaterno);
            if (validaam != true) {
                camponame = camponame + '<b>-Apellido Materno<br></b>';
                ;
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

    //No aceptar caracteres especiales incluyendo puntos(.) y comas(,)
    checkTextAndNumRFC: function (evt) {
        if ($.inArray(evt.keyCode, [45, 33, 36, 46, 35, 34, 8, 9, 20, 16, 17, 37, 40, 39, 38, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 16, 32, 192, 186, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105]) < 0) {
            app.alert.show("Caracter Invalido", {
                level: "error",
                title: "Caracter Invalido.",
                autoClose: true
            });
            return false;
        }
    },

    _doGeneraCURP: function () {
        if (this.model.get('tipodepersona_c') != 'Persona Moral') {
            //Valida que se tenga la informaci�n requerida para generar la CURP
            if (this.model.get('fechadenacimiento_c') != '' && this.model.get('genero_c') != '' && this.model.get('primernombre_c') != '' && this.model.get('apellidopaterno_c') != '' && this.model.get('apellidomaterno_c') != '' && this.model.get('pais_nacimiento_c') != '' && this.model.get('estado_nacimiento_c') != '' && this.model.get('estado_nacimiento_c') != "1") {
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
                var necesarios = "";  //Se habilita variable para concatenar campos faltantes para generar el CURP
                //Adrian Arauz 10/09/2018
                if (this.model.get('fechadenacimiento_c') == "" || this.model.get('fechadenacimiento_c') == null) {
                    necesarios = necesarios + '<b>Fecha de Nacimiento<br></b>';
                }
                if (this.model.get('genero_c') == "" || this.model.get('genero_c') == null) {
                    necesarios = necesarios + '<b>G\u00E9nero</b><br>';
                }
                if (this.model.get('primernombre_c') == "" || this.model.get('primernombre_c') == null) {
                    necesarios = necesarios + '<b>Primer Nombre</b><br>';
                }
                if (this.model.get('apellidopaterno_c') == "" || this.model.get('apellidopaterno_c') == null) {
                    necesarios = necesarios + '<b>Apellido Paterno</b><br>';
                }
                if (this.model.get('apellidomaterno_c') == "" || this.model.get('apellidomaterno_c') == null) {
                    necesarios = necesarios + '<b>Apellido Materno</b><br>';
                }
                if (this.model.get('pais_nacimiento_c') == "" || this.model.get('pais_nacimiento_c') == null) {
                    necesarios = necesarios + '<b>Pa\u00EDs de Nacimiento</b><br>';
                }

                if (this.model.get('estado_nacimiento_c') == "" || this.model.get('estado_nacimiento_c') == null || this.model.get('estado_nacimiento_c') == "1") {
                    necesarios = necesarios + '<b>Estado de Nacimiento</b><br>';
                }

                else (necesarios != "")
                {
                    console.log("Confirma necesarios");
                    app.alert.show("Generar CURP", {
                        level: "error",
                        title: "Faltan los siguientes datos para poder generar el CURP: <br>" + necesarios,
                        autoClose: false
                    });
                }
            }
        }
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
                    var faltantes = "";
                    console.log('Valida campos para RFC');
                    if (this.model.get('fechadenacimiento_c') == "" || this.model.get('fechadenacimiento_c') == null) {
                        faltantes = faltantes + '<b>Fecha de Nacimiento<br></b>';
                    }
                    if (this.model.get('primernombre_c') == "" || this.model.get('primernombre_c') == null) {
                        faltantes = faltantes + '<b>Primer Nombre<br></b>';
                    }
                    if (this.model.get('apellidopaterno_c') == "" || this.model.get('apellidopaterno_c') == null) {
                        faltantes = faltantes + '<b>Apellido Paterno<br></b>';
                    }
                    if (this.model.get('apellidomaterno_c') == "" || this.model.get('apellidomaterno_c') == null) {
                        faltantes = faltantes + '<b>Apellido Materno<br></b>';
                    }

                    else (faltantes != "")
                    app.alert.show("Generar RFC", {
                        level: "error",
                        title: "Faltan los siguientes datos para poder generar el RFC: <br>" + faltantes,
                        autoClose: true
                    });
                }
            }
            else {
                if ((this.model.get('razonsocial_c') != null && this.model.get('razonsocial_c') != "") && (this.model.get('fechaconstitutiva_c') != null && this.model.get('fechaconstitutiva_c') != "")) {
                    this._doValidateWSRFC();
                } else {
                    var falta = "";
                    console.log('Entra P Moral RFC');
                    if (this.model.get('fechaconstitutiva_c') == "" || this.model.get('fechaconstitutiva_c') == null) {
                        falta = falta + '<b>Fecha Constitutiva<br></b>';
                    }
                    /*if (this.model.get('nombre_comercial_c') == "" || this.model.get('nombre_comercial_c') == null) {
                     falta = falta + '<b>Nombre Comercial<br></b>';
                     }*/
                    if (this.model.get('razonsocial_c') == "" || this.model.get('razonsocial_c') == null) {
                        falta = falta + '<b>Raz\u00F3n Social<br></b>';
                    }
                    app.alert.show("Generar RFC", {
                        level: "error",
                        title: "Faltan los siguientes datos para poder generar el RFC: <br>" + falta,
                        autoClose: true
                    });
                }
            }
        }
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
     if(this.model.get('tipo_registro_cuenta_c') == "Cliente" || this.model.get('estatus_c') == "Interesado")
     {
     this.$("div[data-name='account_direcciones']").show();
     }
     else
     {
     this.$("div[data-name='account_direcciones']").hide();
     }
     // Carlos Zaragoza: Se elimina el campo por defaiult de tipo de proveedor del registro pero sies proveedor, se selecciona bienes por default
     if(this.model.get('tipo_registro_cuenta_c') == 'Proveedor'){
     this.model.set('tipo_proveedor_c', '1');
     }
     },*/

    _doValidateDireccionCP: function (fields, errors, callback) {
        //Valida CP
        console.log('Validación CP');
        var direcciones = this.model.get('account_direcciones_n');
        if (direcciones != undefined) {
            for (i = 0; i < direcciones.length; i++) {
                if (direcciones[i].codigo_postal == '' || direcciones[i].codigo_postal == null) {
                    errors[$(".account_direcciones")] = errors['account_direcciones'] || {};
                    errors[$(".account_direcciones")].required = true;
                    app.alert.show("Direccion requerida", {
                        level: "error",
                        title: "Favor de seleccionar C.P. en direcci\u00F3n: " + direcciones[i].calle + " " + direcciones[i].numext,
                        autoClose: false
                    });
                }

            }
        }
        //Return
        callback(null, fields, errors);
    },

    _doValidateDireccion: function (fields, errors, callback) {
        if (this.model.get('tipo_registro_cuenta_c') == "3" || this.model.get('tipo_registro_cuenta_c') == "5"
            || (this.model.get('tipo_registro_cuenta_c') == "2" && this.model.get('subtipo_registro_cuenta_c') != "1") || this.model.get('esproveedor_c') == true) {
            if (_.isEmpty(this.oTelefonos.telefono) && this.model.get('tipo_registro_cuenta_c') == "2") {
                $('#tabletelefonos').css('border', '2px solid red');
                errors['account_telefonos1'] = errors['account_telefonos1'] || {};
                errors['account_telefonos1'].required = true;
                app.alert.show("Telefono requeridp", {
                    level: "error",
                    title: "Al menos un tel\u00E9fono es requerido.",
                    autoClose: false
                });
            }
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
                if (activa > 0) {
                    //Valdación Nacional
                    if (this.model.get('tipodepersona_c') != 'Persona Moral') {
                        var nacional = 0;
                        console.log('Validacion Dir.Nacional');
                        var direcciones = this.oDirecciones.direccion;
                        for (i = 0; i < direcciones.length; i++) {
                            if (direcciones[i].pais == 2 && direcciones[i].inactivo == 0 || direcciones[i].sinSepomex) {
                                nacional = 1;
                            }
                        }
                        //Valida variable nacional
                        if (nacional != 1) {
                            console.log('Dir. Nacional requerida');
                            errors[$(".addDireccion")] = errors['account_direcciones'] || {};
                            errors[$(".addDireccion")].required = true;

                            $('.direcciondashlet').css('border-color', 'red');
                            app.alert.show("Direccion nacional requerida", {
                                level: "error",
                                title: "Al menos una direcci\u00F3n nacional es requerida.",
                                autoClose: false
                            });
                        }
                    }
                } else {
                    console.log('Dir. activa requerida');
                    errors[$(".addDireccion")] = errors['account_direcciones'] || {};
                    errors[$(".addDireccion")].required = true;

                    $('.direcciondashlet').css('border-color', 'red');
                    app.alert.show("Direccion nacional activa requerida", {
                        level: "error",
                        title: "Al menos una direcci\u00F3n nacional activa es requerida.",
                        autoClose: false
                    });
                }
            }
        }
        callback(null, fields, errors);
    },

    delegateButtonEvents: function () {
        this._super("delegateButtonEvents");
        this.context.on('button:cotizador_button:click', this.cotizadorClicked, this);
        this.context.on('button:expediente_button:click', this.expedienteClicked, this);
        this.context.on('button:regresa_lead:click', this.regresa_leadClicked, this);
        this.context.on('button:prospecto_contactado:click', this.prospectocontactadoClicked, this);
        // this.context.on('button:conversion_cliente_uniclick:click', this.clienteuniclickClicked, this);
        this.context.on('button:cancel_button:click', this.handleCancel, this);
        // this.context.on('button:save_button:click', this.borraTel, this);
        //this.context.on('button:prospecto_contactado:click',this.validaContactado, this);  //se añade validación para validar campos al convertir prospecto contactado.
        this.context.on('button:convierte_lead:click', this.validalead, this);
        this.context.on('button:dynamics_button:click', this.requestDynamics, this);

        this.context.on('button:verificar_cambios:click', this.verificarCambiosRazonSocial, this);

    },

    /*
     * @author F. Javier G. Solar
     * 18/07/2018
     * El botón tendrá como finalidad cambiar el Tipo y Subtipo de Cuenta.
     * */

    regresa_leadClicked: function () {
        App.alert.show('RegresaAlead', {
            level: 'process',
            title: 'Convirtiendo cuenta, por favor espere.',
        });
        var totalProspecto = 0;
        var totalProspectoG = 0;
        var productousuario = App.user.attributes.productos_c;
        var api_params = {};

        //Validacion para actualizar el producto del usuario logueado asi como el tipo de registro de la cuenta
        //Leasing
        if (Oproductos.productos.tct_tipo_l_txf_c == "Prospecto" && Oproductos.productos.tct_subtipo_l_txf_c == "Contactado") {
            totalProspectoG++;
            if (productousuario.includes('1') && App.user.id == this.model.get('user_id_c')) {
                api_params["tct_tipo_l_txf_c"] = "Prospecto";
                api_params["tct_subtipo_l_txf_c"] = "Sin Contactar";
                api_params["tct_tipo_cuenta_l_c"] = "PROSPECTO SIN CONTACTAR";
                totalProspecto++;
            }
        }
        //Factoraje
        if (Oproductos.productos.tct_tipo_f_txf_c == "Prospecto" && Oproductos.productos.tct_subtipo_f_txf_c == "Contactado") {
            totalProspectoG++;
            if (productousuario.includes('4') && App.user.id == this.model.get('user_id1_c')) {
                totalProspecto++;
                api_params["tct_tipo_f_txf_c"] = "Prospecto";
                api_params["tct_subtipo_f_txf_c"] = "Sin Contactar";
                api_params["tct_tipo_cuenta_f_c"] = "PROSPECTO SIN CONTACTAR";
            }

        }
        //CA
        if (Oproductos.productos.tct_tipo_ca_txf_c == "Prospecto" && Oproductos.productos.tct_subtipo_ca_txf_c == "Contactado") {
            totalProspectoG++;
            if (productousuario.includes('3') && App.user.id == this.model.get('user_id2_c')) {
                totalProspecto++;
                api_params["tct_tipo_ca_txf_c"] = "Prospecto";
                api_params["tct_subtipo_ca_txf_c"] = "Sin Contactar";
                api_params["tct_tipo_cuenta_ca_c"] = "PROSPECTO SIN CONTACTAR";
            }
        }
        //Fleet
        if (Oproductos.productos.tct_tipo_fl_txf_c == "Prospecto" && Oproductos.productos.tct_subtipo_fl_txf_c == "Contactado") {
            totalProspectoG++;
            if (productousuario.includes('6') && App.user.id == this.model.get('user_id6_c')) {
                totalProspecto++;
                api_params["tct_tipo_fl_txf_c"] = "Prospecto";
                api_params["tct_subtipo_fl_txf_c"] = "Sin Contactar";
                api_params["tct_tipo_cuenta_fl_c"] = "PROSPECTO SIN CONTACTAR";
            }
        }
        if (this.model.get("tipo_registro_cuenta_c") == "2" && this.model.get("subtipo_registro_cuenta_c") == "2" && totalProspecto == totalProspectoG) {
            //Al entrar en esta condicion significa que solo hay un campo como Prospecto, lo cual puede cambiar de Prospecto a lead
            vista360.ResumenCliente.general_cliente.tipo = "PROSPECTO SIN CONTACTAR";
            this.model.set("tipo_registro_cuenta_c", "2");
            this.model.set("subtipo_registro_cuenta_c", "1");
            this.model.set("tct_tipo_subtipo_txf_c", "PROSPECTO SIN CONTACTAR");
            this.model.set("tct_prospecto_contactado_chk_c", false);
            //this.model.set("show_panel_c",0);
            this.model.save();
        }
        if (api_params != undefined) {

            var idC = this.model.get('id');
            var url = app.api.buildURL('tct02_Resumen/' + idC, null, null);
            app.api.call('update', url, api_params, {
                success: _.bind(function (data) {
                    //this._render();
                    app.alert.dismiss('RegresaAlead');
                    Oproductos.productos = data;
                    app.alert.show('alert_change_success', {
                        level: 'success',
                        messages: 'Cambio realizado',
                    });
                    //Actualiza modelo vista v360
                    vista360.ResumenCliente.leasing.tipo_cuenta = data.tct_tipo_cuenta_l_c;
                    vista360.ResumenCliente.factoring.tipo_cuenta = data.tct_tipo_cuenta_f_c;
                    vista360.ResumenCliente.credito_auto.tipo_cuenta = data.tct_tipo_cuenta_ca_c;
                    vista360.ResumenCliente.fleet.tipo_cuenta = data.tct_tipo_cuenta_fl_c;
                    Oproductos.render();
                    vista360.render();
                })
            });
        }
    },

    cotizadorClicked: function () {
        var Accountid = this.model.get('id');
        var Clientid = this.model.get('idcliente_c');
        if (Clientid == '') {
            Clientid = 0;
        }
        window.open("#bwc/index.php?entryPoint=OpportunidadVaadin&Accountid=" + Accountid + "&clientId=" + Clientid);
    },

    expedienteClicked: function () {
        var Accountid = this.model.get('id');
        window.open("#bwc/index.php?entryPoint=ExpedienteVaadin&Accountid=" + Accountid);
    },


    /* @Jesus Carrillo
     Metodo para verificar  las llamadas de la cuenta
     */

    //CAMBIOS EFECTUADOS
    getllamadas: function (callback) {
        var cday = new Date();
        var llamadas = 0;
        self = this;
        App.api.call("read", app.api.buildURL("Accounts/" + this.model.get('id') + "/link/calls", null, null, {}), null, {
            success: _.bind(function (data) {
                this.datallamadas = data;
                if (data.records.length > 0) {
                    for (var i = 0; i < data.records.length; i++) {
                        var tempdate = Date.parse(data.records[i].date_start);
                        if (tempdate < cday) {
                            if (data.records[i].status == 'Held') { //Conversión de LEAD a Prospecto contactado, solo cuando esten como realizadas
                                llamadas++;
                            }
                        }
                    }
                }
                self.flagheld++;
                callback(llamadas, null, self);
                //This.totalllamadas=llamadas;
                //return llamadas;
            }, this)
        });
    },
    /* @Jesus Carrillo
     Metodo para verificar  las reuniones de la cuenta
     */
    getreuniones: function (callBackResult) {
        var cday = new Date();
        var reuniones = 0;
        self = this;
        App.api.call("read", app.api.buildURL("Accounts/" + this.model.get('id') + "/link/meetings", null, null, {}), null, {
            success: _.bind(function (data) {
                if (data.records.length > 0) {
                    for (var i = 0; i < data.records.length; i++) {
                        var tempdate = Date.parse(data.records[i].date_start);
                        if (tempdate < cday) {
                            if (data.records[i].status == 'Held') { //Conversión de LEAD a Prospecto Contactado, solo cuando esten como realizadas
                                reuniones++;
                            }
                        }
                    }
                }
                //this.totalreuniones=reuniones;
                //return reuniones;
                self.flagheld++;
                callBackResult(null, reuniones, self);
            }, this)
        });
    },
    /* @Jesus Carrillo
     Metodo para validar campos de telefonos y direcciones
     */
    validar_fields: function (valContacto, validar_fields) {
        App.alert.show('loadingConvertir', {
            level: 'process',
            title: 'Convirtiendo cuenta, por favor espere',
        });

        var datos_telefonos = this.oTelefonos.telefono;
        var tipolabel = [];
        var pais = [];
        var estatus = [];
        var datos_dirreciones = this.oDirecciones.direccion;
        var tipolabel2 = [];
        var cp = [];
        var municipio = [];
        var calle = [];
        var indicador = [];
        var ciudad = [];
        var numext = [];
        var numint = [];
        var estado = [];
        var colonia = [];
        for (var i = 0; i < datos_telefonos.length; i++) {
            tipolabel.push(datos_telefonos[i].tipo_label);
            pais.push(datos_telefonos[i].pais);
            estatus.push(datos_telefonos[i].estatus);
        }
        for (var i = 0; i < datos_dirreciones.length; i++) {
            tipolabel2.push(datos_dirreciones[i].tipodedireccion);
            cp.push(datos_dirreciones[i].postal);
            municipio.push(datos_dirreciones[i].municipio);
            calle.push(datos_dirreciones[i].calle);
            indicador.push(datos_dirreciones[i].indicador);
            ciudad.push(datos_dirreciones[i].ciudad);
            numext.push(datos_dirreciones[i].numext);
            numint.push(datos_dirreciones[i].numint);
            estado.push(datos_dirreciones[i].estado);
            colonia.push(datos_dirreciones[i].colonia);
        }
        var allfields = [tipolabel, pais, estatus, tipolabel2, cp, municipio, calle, indicador, ciudad, numext, numint, estado, colonia];
        var allfields2 = [];
        console.log(allfields);
        var indica_direc_admin = 0;
        for (var i = 0; i < allfields.length; i++) {
            var betext = 0;
            for (var j = 0; j < allfields[i].length; j++) {
                if (allfields[i][j] != null || allfields[i][j] != "") {
                    betext++;
                    if (i == 7) {//si estas apuntando al campo indicador
                        if (allfields[i][j] == '16' || allfields[i][j] == '17' || allfields[i][j] == '18' || allfields[i][j] == '19' || allfields[i][j] == '20' || allfields[i][j] == '21'
                            || allfields[i][j] == '22' || allfields[i][j] == '23' || allfields[i][j] == '24' || allfields[i][j] == '25' || allfields[i][j] == '26' || allfields[i][j] == '27'
                            || allfields[i][j] == '28' || allfields[i][j] == '29' || allfields[i][j] == '30' || allfields[i][j] == '31') {
                            indica_direc_admin++;
                        }
                    }
                }
            }
            if (betext == 0) {
                allfields2.push(false);
            } else {
                allfields2.push(true);
            }
        }
        console.log(allfields2);
        var fieldstelefono = allfields2.slice(0, 2);
        var fieldsdirec = allfields2.slice(3);
        var valMedios = 0;

        if (fieldstelefono.includes(false) == true) {
            App.alert.dismiss('loadingConvertir');
            app.alert.show('alert_fields_empty1', {
                level: 'error',
                messages: 'Para convertir a Prospecto Contactado es necesario que tenga al menos un <b>Tel\u00E9fono</b>',
            });
            valMedios = 1;
        }
        /*
         if(fieldsdirec.includes(false)==true){
         app.alert.show('alert_fields_empty2', {
         level: 'error',
         messages: 'Para convertir a Prospecto Contactado es necesario que tenga al menos una <b>Direcci\u00F3n</b>',
         });
         valMedios = 1;
         }
         */

        /*    if(indica_direc_admin==0){
         app.alert.show('alert_fields_empty3', {
         level: 'error',
         messages: 'Para convertir a Prospecto Contactado es necesario que tenga al menos una <b>Direcci\u00F3n</b> con Indicador <b>Administraci\u00F3n</b>',
         });
         valMedios = 1;
         } */

        if (valMedios == 0 && valContacto == 0 && validar_fields == 0) {
            if (this.model.get('tipo_registro_cuenta_c') == "1") {
                this.model.set('tipo_registro_cuenta_c', '2');
                this.model.set('subtipo_registro_cuenta_c', '2');
                this.model.set('tct_prospecto_contactado_chk_c', true);
                //this.model.set("show_panel_c",1);
                this.model.save();
            }
            var productousuario = App.user.attributes.productos_c;
            var api_params = {};

            if (productousuario.includes('1') && Oproductos.productos.tct_tipo_l_txf_c == "Lead") {
                if (App.user.id == this.model.get('user_id_c')) {
                    api_params["tct_tipo_l_txf_c"] = "Prospecto";
                    api_params["tct_subtipo_l_txf_c"] = "Sin Contactar";
                    api_params["tct_tipo_cuenta_l_c"] = "PROSPECTO SIN CONTACTAR";
                }
            }
            if (productousuario.includes('3') && Oproductos.productos.tct_tipo_ca_txf_c == "Lead") {
                if (App.user.id == this.model.get('user_id2_c')) {
                    api_params["tct_tipo_ca_txf_c"] = "Prospecto";
                    api_params["tct_subtipo_ca_txf_c"] = "Sin Contactar";
                    api_params["tct_tipo_cuenta_ca_c"] = "PROSPECTO SIN CONTACTAR";
                }
            }
            if (productousuario.includes('4') && Oproductos.productos.tct_tipo_f_txf_c == "Lead") {
                if (App.user.id == this.model.get('user_id1_c')) {
                    api_params["tct_tipo_f_txf_c"] = "Prospecto";
                    api_params["tct_subtipo_f_txf_c"] = "Sin Contactar";
                    api_params["tct_tipo_cuenta_f_c"] = "PROSPECTO SIN CONTACTAR";
                }
            }
            if (productousuario.includes('6') && Oproductos.productos.tct_tipo_fl_txf_c == "Lead") {
                if (App.user.id == this.model.get('user_id6_c')) {
                    api_params["tct_tipo_fl_txf_c"] = "Prospecto";
                    api_params["tct_subtipo_fl_txf_c"] = "Sin Contactar";
                    api_params["tct_tipo_cuenta_fl_c"] = "PROSPECTO SIN CONTACTAR";
                }
            }
            if (api_params != undefined) {

                var idC = this.model.get('id');
                var url = app.api.buildURL('tct02_Resumen/' + idC, null, null);
                app.api.call('update', url, api_params, {
                    success: _.bind(function (data) {
                        //this._render();
                        Oproductos.productos = data;
                        app.alert.dismiss('loadingConvertir');
                        app.alert.show('alert_change_success', {
                            level: 'success',
                            messages: 'Cambio realizado',
                        });
                        //Actualiza modelo vista v360
                        vista360.ResumenCliente.general_cliente.tipo = "PROSPECTO SIN CONTACTAR";
                        vista360.ResumenCliente.leasing.tipo_cuenta = data.tct_tipo_cuenta_l_c;
                        vista360.ResumenCliente.factoring.tipo_cuenta = data.tct_tipo_cuenta_f_c;
                        vista360.ResumenCliente.credito_auto.tipo_cuenta = data.tct_tipo_cuenta_ca_c;
                        vista360.ResumenCliente.fleet.tipo_cuenta = data.tct_tipo_cuenta_fl_c;
                        Oproductos.render();
                        vista360.render();
                    })
                });
            }
        }
    },
    /* @Jesus Carrillo
     Metodo que convierte a prospecto contactado
     *Solo promotores y directorees pueden cambiar una cuenta de Lead a Prospecto contactado
     * 22-08-2018 Victor Martínez
     */
    prospectocontactadoClicked: function () {
        App.alert.show('convierteLead_a_Prospecto', {
            level: 'process',
            title: 'Convirtiendo cuenta, por favor espere',
        });
        if ((this.model.get('tipo_registro_cuenta_c') == "1" && $('.chk_l_nv')[0].checked && $('.chk_f_nv')[0].checked && $('.chk_ca_nv')[0].checked) &&
            (this.model.get('user_id_c') == "cc736f7a-4f5f-11e9-856a-a0481cdf89eb" && this.model.get('user_id1_c') == "cc736f7a-4f5f-11e9-856a-a0481cdf89eb" && this.model.get('user_id2_c') == "cc736f7a-4f5f-11e9-856a-a0481cdf89eb")) {
            app.alert.dismiss('convierteLead_a_Prospecto');
            app.alert.show("Cumple 3 checks", {
                level: "error",
                title: 'Esta cuenta no se puede convertir a prospecto ya que es un lead no viable en los tres productos.',
                autoClose: false
            });
            return;
        }
        /*var validacion =  this.validaContactado();
         if(validacion == 1){
         return;

         }*/

        self = this;
        self.flagheld = 0;
        if (this.model.get('id') != "") { //en lugar de self es this
            app.api.call('GET', app.api.buildURL('GetUsersBoss/' + this.model.get('id')), null, {
                success: _.bind(function (data) {
                    var usuario = App.user.attributes.puestousuario_c;
                    console.log(data);
                    if (data == false) {


                        if (usuario == "5" ||
                            usuario == "11" ||
                            usuario == "16" ||
                            //Gerentes
                            usuario == "15" ||
                            usuario == "4" ||
                            usuario == "10" ||
                            //subdirectores
                            usuario == "3" ||
                            usuario == "9" ||
                            usuario == "28" ||
                            //Directores
                            usuario == "1" ||
                            usuario == "2" ||
                            usuario == "8" ||
                            usuario == "14" ||
                            usuario == "21"
                            || usuario == "18" //Ajuste para poder trabajar con la cuenta de Wendy
                        ) {

                            //Valida llamadas y reuniones
                            var valRelacionados = 0;
                            //self.getllamadas();
                            //self.getreuniones();
                            app.alert.dismiss('convierteLead_a_Prospecto');
                            app.alert.show('loadcontactado', {
                                level: 'process',
                            });
                            self.getllamadas(this.resultCallback);
                            self.getreuniones(this.resultCallback);

                        }
                        else {
                            app.alert.dismiss('convierteLead_a_Prospecto');
                            app.alert.show("No acceso", {
                                level: "error",
                                title: "Usted no tiene el permiso para llevar a cabo esta acci\u00F3n",
                                autoClose: true
                            });
                        }

                        /*

                         if(this.totalllamadas==0 && self.totalreuniones==0){
                         app.alert.show('alert_calls', {
                         level: 'error',
                         messages: 'El proceso de conversi\u00F3n requiere que la cuenta contenga una <b>llamada</b> o <b>reuni\u00F3n</b> con estado <b>Realizada</b> y con fecha al d\u00EDa de hoy o anterior.',
                         });
                         valRelacionados = 1;
                         }

                         //Valida datos de cuenta
                         var valContacto = self.validaContactado();
                         self.validar_fields(valContacto, valRelacionados);
                         */

                    }
                    else if (data == true) {

                        //Valida llamadas y reuniones
                        var valRelacionados = 0;
                        //self.getllamadas();
                        //self.getreuniones();
                        app.alert.dismiss('convierteLead_a_Prospecto');
                        app.alert.show('loadcontactado', {
                            level: 'process',
                        });
                        self.getllamadas(this.resultCallback);
                        self.getreuniones(this.resultCallback);


                    }


                }, self)
            });
            //self.render();
        }

        console.log("valor fuera " + this.model.get('id'));
    },

    resultCallback: function (resultLlamadas, resultReuniones, context) {
        self = context;
        var valRelacionados = 0;
        if (resultLlamadas != null) {
            self.totalllamadas = resultLlamadas;

        }
        if (resultReuniones != null) {
            self.totalreuniones = resultReuniones;

        }

        // if (self.totalllamadas != undefined && self.totalreuniones != undefined) {
        if (self.flagheld >= 2) {
            if (self.totalllamadas == 0 && self.totalreuniones == 0) {
                app.alert.dismiss('loadingConvertir');
                app.alert.show('alert_calls', {
                    level: 'error',
                    messages: 'El proceso de conversi\u00F3n requiere que la cuenta contenga una <b>llamada</b> o <b>reuni\u00F3n</b> con estado <b>Realizada</b> y con fecha al d\u00EDa de hoy o anterior.',
                });
                valRelacionados = 1;
            }

            //Valida datos de cuenta
            var valContacto = self.validaContactado();
            self.validar_fields(valContacto, valRelacionados);
            app.alert.dismiss('loadcontactado');
            app.alert.dismiss('loadingConvertir');

        }

    },


    //Validación para que los campos contengan informacion para poder convertir de LEAD a Prospecto/Contactado. Adrian Arauz 15/08/2018
    validaContactado: function () {
        var campos = "";

        if (this.model.get('origen_cuenta_c') == "" || this.model.get('origen_cuenta_c') == null) {
            campos = campos + '<b>Origen, </b>';
        }

        if (this.model.get('name') == "" || this.model.get('name') == null) {
            campos = campos + '<b>Nombre, </b>';
        }

        if ((this.model.get('apellidopaterno_c') == "" || this.model.get('apellidopaterno_c') == null) && this.model.get('tipodepersona_c') != 'Persona Moral') {
            campos = campos + '<b>Apellido Paterno, </b>';
        }

        if (this.model.get('email') == "" || this.model.get('email') == null) {
            campos = campos + '<b>E Mail, </b>';
        }

        if ((this.model.get('nombre_comercial_c') == "" || this.model.get('nombre_comercial_c') == null) && this.model.get('tipodepersona_c') == 'Persona Moral') {
            campos = campos + '<b>Nombre Comercial. </b> ';
        }

        if (campos != "") {
            app.alert.dismiss('loadingConvertir');
            console.log('Validacion Campos OK');
            app.alert.show('alert_calls2', {
                level: 'error',
                messages: 'Para convertir a Prospecto Contactado es necesario se llenen los campos requeridos: <br>' + campos,
            });

            return 1;
        } else {
            return 0;
        }
    },

    //Validaciòn para convertir el tipo de cuenta Persona a LEAD, Adrian Arauz 21/08/2018
    validalead: function () {
        App.alert.show('conviertePaL', {
            level: 'process',
            title: 'Convirtiendo cuenta, por favor espere',
        });
        var reqs = "";
        if (this.model.get('name') == "" || this.model.get('name') == null) {
            reqs = reqs + '<b>Nombre<br></b>';
        }
        if ((this.model.get('apellidopaterno_c') == "" || this.model.get('apellidopaterno_c') == null) && this.model.get('tipodepersona_c') != 'Persona Moral') {
            reqs = reqs + '<b>Apellido Paterno<br></b>';
        }
        if (this.model.get('email') == "" || this.model.get('email') == null) {
            reqs = reqs + '<b>Email<br></b>';
        }
        if ((this.model.get('nombre_comercial_c') == "" || this.model.get('nombre_comercial_c') == null) && this.model.get('tipodepersona_c') == 'Persona Moral') {
            reqs = reqs + '<b>Nombre Comercial<br></b>';
        }
        if (reqs != "") {
            app.alert.dismiss('conviertePaL');
            console.log('Validacion Campos LEAD');
            app.alert.show('alert_calls4', {
                level: 'error',
                messages: 'Para convertir a Lead es necesario que se llenen los siguientes campos requeridos: ' + reqs,
            });
        }
        else {
            var usuario = app.data.createBean('Users', { id: app.user.id });
            usuario.fetch({
                success: _.bind(function (modelo) {
                    var contains = function (needle) {
                        // Per spec, the way to identify NaN is that it is not equal to itself
                        var findNaN = needle !== needle;
                        var indexOf;

                        if (!findNaN && typeof Array.prototype.indexOf === 'function') {
                            indexOf = Array.prototype.indexOf;
                        } else {
                            indexOf = function (needle) {
                                var i = -1, index = -1;

                                for (i = 0; i < this.length; i++) {
                                    var item = this[i];

                                    if ((findNaN && item !== item) || item === needle) {
                                        index = i;
                                        break;
                                    }
                                }

                                return index;
                            };
                        }

                        return indexOf.call(this, needle) > -1;
                    };
                    /** Modificaci�n a Multiproducto para promotores por default
                     * Carlos Zaragoza
                     * Enero 25, 2016 10:15 AM
                     * */
                    if (contains.call(modelo.get('productos_c'), "1") && this.model.get('user_id_c') == "") {
                        this.model.set('promotorleasing_c', modelo.get('name'));
                        this.model.set('user_id_c', modelo.get('id'));
                    } else if (this.model.get('user_id_c') == "") {
                        this.model.set('promotorleasing_c', '9 - Sin Gestor');
                        this.model.set('user_id_c', '569246c7-da62-4664-ef2a-5628f649537e');
                    }
                    if (contains.call(modelo.get('productos_c'), "4") && this.model.get('user_id_c') == "") {
                        this.model.set('promotorfactoraje_c', modelo.get('name'));
                        this.model.set('user_id1_c', modelo.get('id'));
                    } else if (this.model.get('user_id_c') == "") {
                        this.model.set('promotorfactoraje_c', '9 - Sin Gestor');
                        this.model.set('user_id1_c', '569246c7-da62-4664-ef2a-5628f649537e');
                    }
                    if (contains.call(modelo.get('productos_c'), "3") && this.model.get('user_id_c') == "") {
                        this.model.set('promotorcredit_c', modelo.get('name'));
                        this.model.set('user_id2_c', modelo.get('id'));
                    } else if (this.model.get('user_id_c') == "") {
                        this.model.set('promotorcredit_c', '9 - Sin Gestor');
                        this.model.set('user_id2_c', '569246c7-da62-4664-ef2a-5628f649537e');
                    }
                    if (contains.call(modelo.get('productos_c'), "6") && this.model.get('user_id_c') == "") {
                        this.model.set('promotorfleet_c', modelo.get('name'));
                        this.model.set('user_id6_c', modelo.get('id'));
                    } else if (this.model.get('user_id_c') == "") {
                        this.model.set('promotorfleet_c', '9 - Sin Gestor');
                        this.model.set('user_id6_c', '569246c7-da62-4664-ef2a-5628f649537e');
                    }
                    if (contains.call(modelo.get('productos_c'), "8") && this.model.get('user_id_c') == "") {
                        this.model.set('promotoruniclick_c', modelo.get('name'));
                        this.model.set('user_id7_c', modelo.get('id'));
                    } else if (this.model.get('user_id_c') == "") {
                        this.model.set('promotoruniclick_c', '9 - Sin Gestor');
                        this.model.set('user_id7_c', '569246c7-da62-4664-ef2a-5628f649537e');
                    }

                    if (contains.call(modelo.get('productos_c'), "11") && this.model.get('user_id_c') == "") {
                        this.model.set('promotorrm_c', modelo.get('name'));
                        this.model.set('user_id8_c', modelo.get('id'));
                    } else if (this.model.get('user_id_c') == "") {
                        this.model.set('promotorrm_c', '9 - Sin Gestor');
                        this.model.set('user_id8_c', '569246c7-da62-4664-ef2a-5628f649537e');
                    }

                    if (contains.call(modelo.get('productos_c'), "1") == false && contains.call(modelo.get('productos_c'), "3") == false && contains.call(modelo.get('productos_c'), "4") == false && contains.call(modelo.get('productos_c'), "6") == false) {
                        this.model.set('promotorleasing_c', '9 - Sin Gestor');
                        this.model.set('user_id_c', '569246c7-da62-4664-ef2a-5628f649537e');
                        this.model.set('promotorfactoraje_c', '9 - Sin Gestor');
                        this.model.set('user_id1_c', '569246c7-da62-4664-ef2a-5628f649537e');
                        this.model.set('promotorcredit_c', '9 - Sin Gestor');
                        this.model.set('user_id2_c', '569246c7-da62-4664-ef2a-5628f649537e');
                        this.model.set('promotorfleet_c', '9 - Sin Gestor');
                        this.model.set('user_id6_c', '569246c7-da62-4664-ef2a-5628f649537e');
                        this.model.set('promotoruniclick_c', '9 - Sin Gestor');
                        this.model.set('user_id7_c', '569246c7-da62-4664-ef2a-5628f649537e');
                        this.model.set('promotorrm_c', '9 - Sin Gestor');
                        this.model.set('user_id8_c', '569246c7-da62-4664-ef2a-5628f649537e');
                    }

                    if (this.model.get("tipo_registro_cuenta_c") == "4" || this.model.get('tipo_registro_cuenta_c') == "5") {
                        vista360.ResumenCliente.general_cliente.tipo = "PROSPECTO SIN CONTACTAR";
                        this.model.set("tipo_registro_cuenta_c", "2");
                        this.model.set("subtipo_registro_cuenta_list", "1");
                        this.model.set("show_panel_c", 1);
                        this.model.save();
                    }
                }, this)
            });
            var productousuario = App.user.attributes.productos_c;
            var api_params = {};
            var tipo_producto = 0;
            if ((Oproductos.productos.tct_tipo_cuenta_l_c == "4" || Oproductos.productos.tct_tipo_cuenta_l_c == "5") && productousuario.includes('1')) {
                if (App.user.id == this.model.get('user_id_c')) {
                    tipo_producto = 1;
                    api_params["tct_tipo_l_txf_c"] = "Prospecto";
                    api_params["tct_subtipo_l_txf_c"] = "Sin Contactar";
                    api_params["tct_tipo_cuenta_l_c"] = "PROSPECTO SIN CONTACTAR";
                    Oproductos.productos.tct_tipo_cuenta_l_c = '2';
                    Oproductos.productos.tct_subtipo_l_txf_c = '1';
                    vista360.ResumenCliente.leasing.tipo_cuenta = '2';
                    vista360.ResumenCliente.leasing.subtipo_cuenta = '1';
                }

            }
            if ((Oproductos.productos.tct_tipo_cuenta_ca_c == "4" || Oproductos.productos.tct_tipo_cuenta_ca_c == "5") && productousuario.includes('3')) {
                if (App.user.id == this.model.get('user_id2_c')) {
                    tipo_producto = 3;
                    api_params["tct_tipo_ca_txf_c"] = "Prospecto";
                    api_params["tct_subtipo_ca_txf_c"] = "Sin Contactar";
                    api_params["tct_tipo_cuenta_ca_c"] = "PROSPECTO SIN CONTACTAR";
                    Oproductos.productos.tct_tipo_cuenta_ca_c = '2';
                    Oproductos.productos.tct_subtipo_ca_txf_c = '1';
                    vista360.ResumenCliente.credito_auto.tipo_cuenta = '2';
                    vista360.ResumenCliente.credito_auto.subtipo_cuenta = '1';
                }
            }
            if ((Oproductos.productos.tct_tipo_cuenta_f_c == "4" || Oproductos.productos.tct_tipo_cuenta_f_c == "5") && productousuario.includes('4')) {
                if (App.user.id == this.model.get('user_id1_c')) {
                    tipo_producto = 4;
                    api_params["tct_tipo_f_txf_c"] = "Prospecto";
                    api_params["tct_subtipo_f_txf_c"] = "Sin Contactar";
                    api_params["tct_tipo_cuenta_f_c"] = "PROSPECTO SIN CONTACTAR";
                    Oproductos.productos.tct_tipo_cuenta_f_c = '2';
                    Oproductos.productos.tct_subtipo_f_txf_c = '1';
                    vista360.ResumenCliente.factoring.tipo_cuenta = '2';
                    vista360.ResumenCliente.factoring.subtipo_cuenta = '1';
                }
            }
            if ((Oproductos.productos.tct_tipo_cuenta_fl_c == "4" || Oproductos.productos.tct_tipo_cuenta_fl_c == "5") && productousuario.includes('6')) {
                if (App.user.id == this.model.get('user_id6_c')) {
                    tipo_producto = 6;
                    api_params["tct_tipo_fl_txf_c"] = "Prospecto";
                    api_params["tct_subtipo_fl_txf_c"] = "Sin Contactar";
                    api_params["tct_tipo_cuenta_fl_c"] = "PROSPECTO SIN CONTACTAR";
                    Oproductos.productos.tct_tipo_cuenta_fl_c = '2';
                    Oproductos.productos.tct_subtipo_fl_txf_c = '1';
                    vista360.ResumenCliente.fleet.tipo_cuenta = '2';
                    vista360.ResumenCliente.fleet.subtipo_cuenta = '1';
                }
            }
            if ((Oproductos.productos.tct_tipo_cuenta_uc_c == "4" || Oproductos.productos.tct_tipo_cuenta_uc_c == "5") && productousuario.includes('8')) {
                if (App.user.id == this.model.get('user_id7_c')) {
                    tipo_producto = 8;
                    api_params["tct_tipo_uc_txf_c"] = "Prospecto";
                    api_params["tct_subtipo_uc_txf_c"] = "Sin Contactar";
                    api_params["tct_tipo_cuenta_uc_c"] = "PROSPECTO SIN CONTACTAR";
                    Oproductos.productos.tct_tipo_cuenta_uc_c = '2';
                    Oproductos.productos.tct_subtipo_uc_txf_c = '1';
                    vista360.ResumenCliente.uniclick.tipo_cuenta = '2';
                    vista360.ResumenCliente.uniclick.subtipo_cuenta = '1';
                }
            }
            // Actualiza Productos
            _.each(Productos, function (value, key) {
                var idprod = '';
                if (app.user.id == this.model.get('user_id_c') && Productos[key].tipo_producto == 1) idprod = Productos[key].id;
                if (app.user.id == this.model.get('user_id1_c') && Productos[key].tipo_producto == 4) idprod = Productos[key].id;
                if (app.user.id == this.model.get('user_id2_c') && Productos[key].tipo_producto == 3) idprod = Productos[key].id;
                if (app.user.id == this.model.get('user_id6_c') && Productos[key].tipo_producto == 6) idprod = Productos[key].id;
                if (app.user.id == this.model.get('user_id7_c') && Productos[key].tipo_producto == 8) idprod = Productos[key].id;
                if (idprod) {
                    var params = {};
                    params["tipo_cuenta"] = "2";
                    params["subtipo_cuenta"] = "1";
                    params["tipo_subtipo_cuenta"] = "PROSPECTO SIN CONTACTAR";
                    var uni = app.api.buildURL('uni_Productos/' + idprod, null, null);
                    app.api.call('update', uni, params, {
                        success: _.bind(function (data) {
                        })
                    });
                }
            }, this);
            // Actualiza Resumen
            var idC = this.model.get('id');
            setTimeout(function () {
                if (api_params != undefined) {
                    var url = app.api.buildURL('tct02_Resumen/' + idC, null, null);
                    app.api.call('update', url, api_params, {
                        success: _.bind(function (data) {
                            app.alert.dismiss('conviertePaL');
                            //Oproductos.productos = data;
                            app.alert.show('alert_change_success', {
                                level: 'success',
                                messages: 'Cambio realizado',
                            });


                            cont_uni_p.render();
                            Oproductos.render();
                            vista360.render();
                            //Deja activa la pestaña de la vista360
                            $('li.tab.LBL_RECORDVIEW_PANEL8').removeAttr("style");
                            $("#recordTab>li.tab").removeClass('active');
                            $('li.tab.LBL_RECORDVIEW_PANEL8').addClass("active");
                            //window.location.reload();
                        })
                    });
                }
            }, 5000);
        }
    },

    requestDynamics: function () {
        //Valida que sea proveedor
        var tipo_cuenta = this.model.get('tipo_registro_cuenta_c');
        var proveedor = this.model.get('esproveedor_c');
        var cedente = this.model.get('cedente_factor_c');
        var deudor = this.model.get('deudor_factor_c');
        if (tipo_cuenta == '5' || tipo_cuenta == '3' || proveedor || cedente || deudor) {
            var self = this;
            var body = {
                "accion": this.model.get('id')
            }
            app.alert.show('infoDynamics', {
                level: 'process',
                closeable: false,
                messages: app.lang.get('LBL_LOADING'),
            });
            //Consumir servicio de OTP
            app.api.call('create', app.api.buildURL("Dynamics365"), body, {
                success: _.bind(function (data) {
                    var params = {};
                    app.alert.dismiss('infoDynamics');
                    if (data != null) {
                        self.model.set('control_dynamics_365_c', data[0]);
                        self.model.set('id_cpp_365_chk_c', data[1]);
                        params["error_dynamics365_c"] = "";
                    }
                    else {
                        params["error_dynamics365_c"] = 'Error al enviar información hacia Dynamics 365: Petición mal realizada (Cuentas por pagar).';
                        app.alert.dismiss('infoDynamics');
                        app.alert.show('error_otp', {
                            level: 'warning',
                            messages: 'Error al enviar información hacia Dynamics 365: Petición mal realizada (Cuentas por pagar).',
                            autoClose: true
                        });
                    }
                    var url1 = app.api.buildURL('tct02_Resumen/' + this.model.get('id'), null, null);
                    app.api.call('update', url1, params, {
                        success: _.bind(function (data1) {
                        }, this)
                    });
                }, this),
                error: _.bind(function (response) {
                    var params = {};
                    params["error_dynamics365_c"] = response.textStatus + '\n"Error al enviar información hacia Dynamics 365"';
                    app.alert.dismiss('infoDynamics');
                    app.alert.show('error_otp', {
                        level: 'error',
                        messages: response.textStatus + '\n"Error al enviar información hacia Dynamics 365"',
                        autoClose: true
                    });
                    var url1 = app.api.buildURL('tct02_Resumen/' + this.model.get('id'), null, null);
                    app.api.call('update', url1, params, {
                        success: _.bind(function (data1) {
                        }, this)
                    });
                }, this)
            });
        } else {
            app.alert.show('no_envia_dynamics', {
                level: 'warning',
                messages: 'La cuenta no cumple con los criterios de Proveedor para enviar a Dynamics 365',
                autoClose: true
            });
        }

    },

    requestDynamics1: function (fields, errors, callback) {
        //Valida que sea proveedor
        var tipo_cuenta = this.model.get('tipo_registro_cuenta_c');
        var proveedor = this.model.get('esproveedor_c');
        var cedente = this.model.get('cedente_factor_c');
        var deudor = this.model.get('deudor_factor_c');
        var url = app.api.buildURL('tct02_Resumen/' + this.model.get('id'), null, null);
        app.api.call('read', url, {}, {
            success: _.bind(function (resumen) {
                if ((tipo_cuenta == '5' || tipo_cuenta == '3' || proveedor || cedente || deudor) && resumen.error_dynamics365_c) {
                    var body = {
                        "accion": this.model.get('id')
                    }
                    app.alert.show('infoDynamics', {
                        level: 'process',
                        closeable: false,
                        messages: app.lang.get('LBL_LOADING'),
                    });
                    //Consumir servicio de OTP
                    app.api.call('create', app.api.buildURL("Dynamics365"), body, {
                        success: _.bind(function (data) {
                            var params = {};
                            app.alert.dismiss('infoDynamics');
                            if (data != null) {
                                this.model.set('control_dynamics_365_c', data[0]);
                                this.model.set('id_cpp_365_chk_c', data[1]);
                                params["error_dynamics365_c"] = "";
                            }
                            else {
                                params["error_dynamics365_c"] = 'Error al enviar información hacia Dynamics 365: Petición mal realizada (Cuentas por pagar).';
                                app.alert.dismiss('infoDynamics');
                                app.alert.show('error_otp', {
                                    level: 'warning',
                                    messages: 'Error al enviar información hacia Dynamics 365: Petición mal realizada (Cuentas por pagar).',
                                    autoClose: true
                                });
                            }
                            var url1 = app.api.buildURL('tct02_Resumen/' + this.model.get('id'), null, null);
                            app.api.call('update', url1, params, {
                                success: _.bind(function (data1) {
                                    callback(null, fields, errors);
                                }, this)
                            });
                        }, this),
                        error: _.bind(function (response) {
                            var params = {};
                            params["error_dynamics365_c"] = response.textStatus + '\n"Error al enviar información hacia Dynamics 365"';
                            app.alert.dismiss('infoDynamics');
                            app.alert.show('error_otp', {
                                level: 'warning',
                                messages: response.textStatus + '\n"Error al enviar información hacia Dynamics 365"',
                                autoClose: true
                            });
                            var url1 = app.api.buildURL('tct02_Resumen/' + this.model.get('id'), null, null);
                            app.api.call('update', url1, params, {
                                success: _.bind(function (data1) {
                                    callback(null, fields, errors);
                                }, this)
                            });
                        }, this)
                    });
                }
                else {
                    callback(null, fields, errors);
                }
            }, this)
        });
    },

    verificarCambiosRazonSocial: function () {

        if (this.model.get('valid_cambio_razon_social_c')) {
            //Carga productos para saber si es multiproducto o uniclick
            // Si es uniclick, únicamente Samuel Álvarez tiene permiso de ver los cambios
            //Si es multiproducto, únicamente el equipo de Crédito tiene permiso de ver los cambios
            App.alert.show('showModalCambios', {
                level: 'process',
            });
            App.api.call("read", app.api.buildURL("Accounts/" + this.model.get('id') + "/link/accounts_uni_productos_1", null, null, {}), null, {
                success: _.bind(function (data) {
                    app.alert.dismiss('showModalCambios');
                    if (data.records.length > 0) {
                        var array_tipo_cuenta_producto = {};
                        var current_user_id = App.user.id;
                        for (var index = 0; index < data.records.length; index++) {

                            var tipoCuenta = data.records[index].tipo_cuenta;
                            var tipoProducto = data.records[index].tipo_producto;

                            switch (tipoProducto) {
                                case '1': //Leasing
                                    array_tipo_cuenta_producto['leasing'] = tipoCuenta;
                                    break;
                                case '2': //Crédito Simple
                                    array_tipo_cuenta_producto['cs'] = tipoCuenta;
                                    break;
                                case '3': //Credito-Automotriz
                                    array_tipo_cuenta_producto['ca'] = tipoCuenta;
                                    break;
                                case '4': //Factoraje
                                    array_tipo_cuenta_producto['factoraje'] = tipoCuenta;
                                    break;
                                case '6': //Fleet
                                    array_tipo_cuenta_producto['fleet'] = tipoCuenta;
                                    break;
                                case '8': //Uniclick
                                    array_tipo_cuenta_producto['uniclick'] = tipoCuenta;
                                    break;
                                case '14': //Tarjeta Crédito
                                    array_tipo_cuenta_producto['tc'] = tipoCuenta;
                                    break;

                            }

                        }

                        var contador_cliente = 0;
                        var contador_cliente_uniclick = 0;
                        for (var key in array_tipo_cuenta_producto) {
                            if (array_tipo_cuenta_producto[key] == '3') {
                                if (key == 'uniclick') {
                                    contador_cliente_uniclick += 1;
                                } else {
                                    contador_cliente += 1;
                                }
                            }
                        }

                        if (contador_cliente_uniclick > 0 && contador_cliente == 0) {

                            var lista_verificadores_uniclick = App.lang.getAppListStrings('verificadores_ids_uniclick_list');
                            var arr_permiso_uniclick = [];
                            Object.keys(lista_verificadores_uniclick).forEach(function (key) {
                                if (lista_verificadores_uniclick[key] == current_user_id) {
                                    arr_permiso_uniclick.push(1);
                                }
                            })
                            //ES CLIENTE UNICLICK, SE ESTABLECE ÁREA INTERNA UNICLICK
                            //Establecer como Visor y aprobador a Samuel Álvarez
                            if (arr_permiso_uniclick.includes(1)) {
                                this.showModalVerificar();
                            } else {
                                app.alert.show("validar_error", {
                                    level: "error",
                                    title: 'Error',
                                    messages: 'No tienes permiso para ejecutar esta acción',
                                    autoClose: false
                                });

                            }

                        }
                        if (contador_cliente >= 0) {
                            //ES MULTIPRODUCTO, SE ESTABLECE ÁREA INTERNA CRÉDITO
                            //ESTABLECER COMO VISORES A EQUIPO DE CREDITO
                            var lista_verificadores = App.lang.getAppListStrings('verificadores_ids_list');
                            var arr_permiso = [];
                            Object.keys(lista_verificadores).forEach(function (key) {
                                if (lista_verificadores[key] == current_user_id) {
                                    arr_permiso.push(1);
                                }
                            });

                            if (arr_permiso.includes(1)) {
                                this.showModalVerificar();
                            } else {
                                app.alert.show("validar_error", {
                                    level: "error",
                                    title: 'Error',
                                    messages: 'No tienes permiso para ejecutar esta acción',
                                    autoClose: false
                                });
                            }

                        }

                        /*if( contador_cliente == 0 && contador_cliente_uniclick == 0){
                            //NO ES CLIENTE EN NINGÚN PRODUCTO
                            console.log('NO ES CLIENTE EN NINGÚN PRODUCTO');
                            app.alert.show("validar_error", {
                                level: "error",
                                title: 'Error',
                                messages: 'No tienes permiso para ejecutar esta acción',
                                autoClose: false
                            });

                        }*/

                    }

                }, this)
            });
        } else {
            app.alert.show("validar_error", {
                level: "error",
                title: 'Error',
                messages: 'El registro no está en proceso de validación',
                autoClose: false
            });
        }

    },

    showModalVerificar: function () {
        var selfModal = this;
        app.drawer.open({
            layout: 'layout-verificaCambios',
            context: {
                context: this.context,
                model: this.model,
            },
        }, function (context, model, update) {
            console.log("CIERRA DRAWER");
            if (update == 'update') {
                //Refresca el modelo para mostrar los valores reestablecidos
                App.controller.context.attributes.model.fetch();
                //Manda llamar a función para volver a cargar las direcciones y de esta manera se puedan ver los cambios aprobados
                selfModal.get_addresses();

            }

        });
    },

    /** BEGIN CUSTOMIZATION: jgarcia@levementum.com 6/12/2015 Description: Persona Fisica and Persona Fisica con Actividad Empresarial must have an email or a Telefono RECORD*/
    _doValidateEmailTelefono: function (fields, errors, callback) {
        if ((this.model.get('tipo_registro_cuenta_c') == "2" && (this.model.get('subtipo_registro_cuenta_c') == '8' || this.model.get('subtipo_registro_cuenta_c') == '9'
            || this.model.get('subtipo_registro_cuenta_c') == '10' || this.model.get('subtipo_registro_cuenta_c') == '12')) || this.model.get('tipo_registro_cuenta_c') == "3") {
            var validPhone = false;
            for (var i = 0; i < this.oTelefonos.telefono.length; i++) {
                if (this.oTelefonos.telefono[i].estatus == 'Activo') {
                    validPhone = true;

                }
            }
            if (validPhone == false) {
                $('#tabletelefonos').css('border', '2px solid red');
                errors['account_telefonos'] = errors['account_telefonos'] || {};
                errors['account_telefonos'].required = true;
            }

        } else if (this.model.get('tipo_registro_cuenta_c') !== '4' || this.model.get('tipo_registro_cuenta_c') !== '5') {
            if (_.isEmpty(this.model.get('email')) && _.isEmpty(this.oTelefonos.telefono)) {
                app.alert.show("Correo requerido", {
                    level: "error",
                    title: "Al menos un correo electr\u00F3nico o un tel\u00E9fono es requerido.",
                    autoClose: false
                });
                errors['email'] = errors['email'] || {};
                errors['email'].required = true;
                $('#tabletelefonos').css('border', '2px solid red');
                errors['account_telefonos1'] = errors['account_telefonos1'] || {};
                errors['account_telefonos1'].required = true;
            }
        }
        callback(null, fields, errors);
    },

    DuplicateCheck: function (fields, errors, callback) {
        //Valida homonimo
        if (this.model.get('tct_homonimo_chk_c') != true) {
            var clean_name = this.model.get('clean_name');
            app.api.call("read", app.api.buildURL("Accounts/", null, null, {
                fields: "clean_name",
                max_num: 5,
                "filter": [
                    {
                        "clean_name": clean_name,
                        "id": {
                            $not_equals: this.model.id,
                        }
                    }
                ]
            }), null, {
                success: _.bind(function (data) {
                    if (data.records.length > 0) {
                        var usuarios = App.lang.getAppListStrings('usuarios_homonimo_name_list');
                        var etiquetas = "";
                        Object.keys(usuarios).forEach(function (key) {
                            if (key != '') {
                                etiquetas += usuarios[key] + '<br>';
                            }
                        });
                        app.alert.show("DuplicateCheck", {
                            level: "error",
                            messages: "Ya existe una persona registrada con el mismo nombre. Favor de comunicarse con alguno de los siguientes usuarios:<br><b>" + etiquetas + "</b>",
                            autoClose: false
                        });

                        if (this.model.get('tipodepersona_c') != 'Persona Moral') {
                            errors['primernombre_c'] = errors['primernombre_c'] || {};
                            errors['primernombre_c'].required = true;
                            errors['apellidopaterno_c'] = errors['apellidopaterno_c'] || {};
                            errors['apellidopaterno_c'].required = true;
                            errors['apellidomaterno_c'] = errors['apellidomaterno_c'] || {};
                            errors['apellidomaterno_c'].required = true;
                        } else {
                            errors['razonsocial_c'] = errors['razonsocial_c'] || {};
                            errors['razonsocial_c'].required = true;
                        }

                    }
                    callback(null, fields, errors);
                }, this)
            });
        } else {
            callback(null, fields, errors);
        }
    },

    RFC_DuplicateCheck: function (fields, errors, callback) {
        var RFC = this.model.get('rfc_c');
        if (RFC != '' && RFC != null && (RFC != 'XXX010101XXX' && RFC != 'XXXX010101XXX' && RFC != 'XXX010101000')) {
            app.api.call("read", app.api.buildURL("Accounts/", null, null, {
                fields: "rfc_c",
                max_num: 5,
                "filter": [
                    {
                        "rfc_c": RFC,
                        "id": {
                            $not_equals: this.model.id,
                        }
                    }
                ]
            }), null, {
                success: _.bind(function (data) {
                    if (data.records.length > 0) {
                        app.alert.show("DuplicateCheck", {
                            level: "error",
                            title: "Ya existe una persona registrada con el mismo RFC.",
                            autoClose: false
                        });

                        errors['rfc_c'] = errors['rfc_c'] || {};
                        errors['rfc_c'].required = true;
                    }
                    callback(null, fields, errors);
                }, this)
            });
        } else {
            callback(null, fields, errors);
        }
    },

    //revisa que no exista un nombre o RFC duplicado
    _doValidateRFC: function (fields, errors, callback) {
        var RFC = this.model.get('rfc_c');
        if (RFC != '' && RFC != null && (RFC != 'XXX010101XXX' && RFC != 'XXXX010101XXX' && this.model.get('tct_pais_expide_rfc_c') == "2")) {
            /*Método que tiene la función de validar el rfc*/
            RFC = RFC.toUpperCase().trim();
            var expReg = "";
            if (this.model.get('tipodepersona_c') != 'Persona Moral') {
                expReg = /^[A-ZÑ&]{4}\d{6}[A-Z0-9]{3}$/; // /^([A-Z\u00D1&]{3,4}) ?(?:- ?)?(\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])) ?(?:- ?)?([A-Z\d]{2})([A\d])$/;
            } else {
                expReg = /^[A-ZÑ&]{3}\d{6}[A-Z0-9]{3}$/; // /^([A-Z\u00D1&]{3,4}) ?(?:- ?)?(\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])) ?(?:- ?)?([A-Z\d]{2})([A\d])$/;
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
        callback(null, fields, errors);
    },

    //validar fecha de nacimiento. Persona debe ser mayor de 18 años
    _doValidateMayoriadeEdad: function (fields, errors, callback) {
        if (this.model.get('tipodepersona_c') != 'Persona Moral' && this.model.get('fechadenacimiento_c') != "") {
            var nacimiento = new Date(this.model.get('fechadenacimiento_c'));
            var enteredAge = this.getAge(nacimiento);
            if (enteredAge < 18) {
                app.alert.show("fechaDeNacimientoCheck", {
                    level: "error",
                    title: "Persona debe de ser mayor de 18 años.",
                    autoClose: false
                });
                errors['fechadenacimiento_c'] = errors['fechadenacimiento_c'] || {};
                errors['fechadenacimiento_c'].required = true;
            }
        }

        callback(null, fields, errors);
    },

    //metodo para validar fecha de nacimiento
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

    //metodo para llamar web service que regresa un RFC valido el cual comparamos con el que el usuario introdujo y decidimos si es valido o no
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
                                messages: "El RFC calculado es diferente al escrito, ¿Desea reemplazarlo?",
                                autoClose: false,

                                onConfirm: function () {
                                    contexto_cuenta.model.set("rfc_c", rfc_local);
                                },
                                onCancel: function () {
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
                        this.model.set("rfc_c", rfc_local);
                    }
                }
            }, this)
        });
        //callback(null, fields, errors);
    },

    /** BEGIN CUSTOMIZATION: jgarcia@levementum.com 8/26/2015 Description: On Inline edit disable the TAB Key in order to prevent the field from going to detail mode.*/
    handleKeyDown: function (e, field) {
        if (e.which === 9) {
            if (field.name != this.model.fields.account_contacts.name && field.name != this.model.fields.account_direcciones.name && field.name != this.model.fields.account_telefonos.name) {
                e.preventDefault();
                this.nextField(field, e.shiftKey ? 'prevField' : 'nextField');
                this.adjustHeaderpane();
            }
        }
    },
    /* END CUSTOMIZATION */

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

    doValidateDateCons: function (fields, errors, callback) {
        /* if  date not empty, then check with today date and return error */
        if (!_.isEmpty(this.model.get('fechaconstitutiva_c')) && this.model.get('tipodepersona_c') == 'Persona Moral') {

            var feccons_date = this.model.get('fechaconstitutiva_c');
            var today_date = new Date().toISOString().slice(0, 10);

            if (feccons_date > today_date) {
                app.alert.show("fechaDeConsValidate", {
                    level: "error",
                    title: "La fecha constitutiva no puede ser mayor al d\u00EDa de hoy",
                    autoClose: false
                });

                errors['fechaconstitutiva_c'] = errors['fechaconstitutiva_c'] || {};
                //errors['fechaapertura'] = 'La fecha de apertura no puede ser posterior al día de hoy' || {};
                errors['fechaconstitutiva_c'].required = true;
            }
        }
        callback(null, fields, errors);
    },

    doValidateDateNac: function (fields, errors, callback) {
        /* if  date not empty, then check with today date and return error */
        if (!_.isEmpty(this.model.get('fechadenacimiento_c')) && this.model.get('tipodepersona_c') != 'Persona Moral') {

            var fecnac_date = this.model.get('fechadenacimiento_c');
            var today_date = new Date().toISOString().slice(0, 10);

            if (fecnac_date >= today_date) {
                app.alert.show("fechaDeNacimientoValidate", {
                    level: "error",
                    title: "La fecha de nacimiento no puede ser mayor o igual al d\u00EDa de hoy",
                    autoClose: false
                });
                errors['fechadenacimiento_c'] = errors['fechadenacimiento_c'] || {};
                //errors['fechaapertura'] = 'La fecha de apertura no puede ser posterior al día de hoy' || {};
                errors['fechadenacimiento_c'].required = true;
            }
        }
        callback(null, fields, errors);
    },

    /*
     validaExtranjerosRFC: function (){
     if((this.model.get('pais_nacimiento_c')!=2) && (this.model.get('tipo_registro_cuenta_c') != 'Prospecto' && this.model.get('tipo_registro_cuenta_c') != 'Persona')){
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

    validaProveedorRequerido: function (fields, errors, callback) {
        if (this.model.get('tipo_registro_cuenta_c') == '5' || this.model.get('esproveedor_c') == true) { //duda
            this.model.set("esproveedor_c", true);
            var tipoProveedor = new String(this.model.get('tipo_proveedor_c'));
            //if (tipoProveedor.length == 0) {
                /*app.alert.show("Proveedor Requerido", {
                 level: "error",
                 title: "Debe seleccionar un un tipo de proveedor al menos",
                 autoClose: false
                 });*/
                //errors['tipo_proveedor_c'] = errors['tipo_proveedor_c'] || {};
                //errors['tipo_proveedor_c'].required = true;
            //}
            //Validacion de Actividad Economica - antes macrosector
            /*if ($('.list_ae').select2('val') == "0" || $('.list_ae').select2('val') == '' || $('.list_ae')[0].innerText.trim() == "" || $('.list_ae').select2('val') == null) {
                //Entra a modo edición el campo custom
                fieldAE1 = this.getField('account_clasf_sectorial');
                this.inlineEditMode = true;
                this.setButtonStates(this.STATE.EDIT);
                this.toggleField(fieldAE1);

                $('.campoAE').find('.record-label').css('color', 'red');
                $('.list_ae').find('.select2-choice').css('border-color', 'red');
                errors['actividadeconomica_c'] = errors['actividadeconomica_c'] || {};
                errors['actividadeconomica_c'].required = true;
            }*/
            if (this.model.get('rfc_c') == '' || this.model.get('rfc_c') == null) {
                /*app.alert.show("RFC requerido", {
                 level: "error",
                 title: "El campo RFC es requerido",
                 autoClose: false
                 });*/
                errors['rfc_c'] = errors['rfc_c'] || {};
                errors['rfc_c'].required = true;
            }
            if (this.model.get('tipodepersona_c') != 'Persona Moral') {
                /*app.alert.show("Fecha de nacimiento requerida", {
                 level: "error",
                 title: "El campo fecha de nacimiento es requerido",
                 autoClose: false
                 });*/
                if (this.model.get('fechadenacimiento_c') == '' || this.model.get('fechadenacimiento_c') == null) {
                    errors['fechadenacimiento_c'] = errors['fechadenacimiento_c'] || {};
                    errors['fechadenacimiento_c'].required = true;
                }
                /*app.alert.show("Pais de nacimiento requerido", {
                 level: "error",
                 title: "El campo pa\u00EDs de nacimiento es requerido",
                 autoClose: false
                 });*/
                if (this.model.get('pais_nacimiento_c') == '' || this.model.get('pais_nacimiento_c') == null) {
                    errors['pais_nacimiento_c'] = errors['pais_nacimiento_c'] || {};
                    errors['pais_nacimiento_c'].required = true;
                }
                if (this.model.get('estado_nacimiento_c') == "" || this.model.get('estado_nacimiento_c') == null) {
                    errors['estado_nacimiento_c'] = errors['estado_nacimiento_c'] || {};
                    errors['estado_nacimiento_c'].required = true;
                }
                /*app.alert.show("Estado civil requerido", {
                 level: "error",
                 //title: "El campo estado civil es requerido",
                 autoClose: false
                 });*/
                if (this.model.get('estadocivil_c') == '' || this.model.get('estadocivil_c') == null) {
                    errors['estadocivil_c'] = errors['estadocivil_c'] || {};
                    errors['estadocivil_c'].required = true;
                }

                /*app.alert.show("Profesion requerido", {
                 level: "error",
                 title: "El campo profesi\u00F3n es requerido",
                 autoClose: false
                 });*/
                // if (this.model.get('profesion_c') == '' || this.model.get('profesion_c') == null) {
                //     errors['profesion_c'] = errors['profesion_c'] || {};
                //     errors['profesion_c'].required = true;
                // }
            }
            else {
                /*app.alert.show("Pais de constitucion", {
                 level: "error",
                 title: "El campo pa\u00EDs de constituci\u00F3n es requerido",
                 autoClose: false
                 });*/
                if (this.model.get('pais_nacimiento_c') == '' || this.model.get('pais_nacimiento_c') == null) {
                    errors['pais_nacimiento_c'] = errors['pais_nacimiento_c'] || {};
                    errors['pais_nacimiento_c'].required = true;
                }
                if (this.model.get('estado_nacimiento_c') == "" || this.model.get('estado_nacimiento_c') == null) {
                    errors['estado_nacimiento_c'] = errors['estado_nacimiento_c'] || {};
                    errors['estado_nacimiento_c'].required = true;
                }
                if (this.model.get('fechaconstitutiva_c') == '' || this.model.get('fechaconstitutiva_c') == null) {
                    errors['fechaconstitutiva_c'] = errors['fechaconstitutiva_c'] || {};
                    errors['fechaconstitutiva_c'].required = true;
                }
            }
        }
        callback(null, fields, errors);
    },

    cambiaRiesgodePersona: function (fields, errors, callback) {
        var riesgo = this.model.get('ctpldpoliticamenteexpuesto_c') == true ? 'Alto' : 'Bajo';
        this.model.set("riesgo_c", riesgo);
        callback(null, fields, errors);
    },

    cleanName: function () {
        //Consume servicio
        if (this.model.get("name").trim() != '') {
            //Recupera variables
            var postData = {
                'name': this.model.get("name")
            };
            var serviceURI = app.api.buildURL("getCleanName", '', {}, {});
            App.api.call("create", serviceURI, postData, {
                success: _.bind(function (data) {
                    if (data['status'] == '200') {
                        this.model.set('clean_name', data['cleanName']);
                    }
                }, this)
            });
        }
    },

    /*
     AF - 26/12/17
     Ajuste: Ocultar campo dependiente de multiselect "¿Instrumento monetario con el que espera realizar los pagos?"
     */
    changeInstMonetario: function () {
        //console.log("Cambio de Inst monetario");
        var instMonetario = this.model.get('tct_inst_monetario_c');
        if (instMonetario.includes("Otro")) {
            this.model.set('imotro_c', true);
            //this.$('[data-name="imotrodesc_c"]').show();
        } else {
            this.model.set('imotro_c', false);
            //this.$('[data-name="imotrodesc_c"]').hide();
        }
    },

    /**Pendiente de Validar*/
    changeLabelMarketing: function () {
        console.log("Cambio de Origen");
        if (this.model.get('origen_cuenta_c') == 'Mercadotecnia') {
            console.log("Se eligio Mecadotecnia");
            this.$("div.record-label[data-name='evento_marketing_c']").text("Detalle marketing");
        }
        if (this.model.get('origen_cuenta_c') == 'Eventos Mercadotecnia') {
            console.log("Se eligio Eventos Mecadotecnia");
            this.$("div.record-label[data-name='evento_marketing_c']").text("Evento marketing");
        }

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

    /**
     * @author Salvador Lopez Balleza
     * @date 13/03/2018
     * Establecer campo phone_office con la misma informaci�n que el campo personalizado account_telefonos
     * */
    setPhoneOffice: function () {

        if (this.oTelefonos != undefined) {
            if (this.oTelefonos.telefono != undefined) {
                var telefono = this.oTelefonos.telefono;
                for (var i = 0; i < telefono.length; i++) {
                    if (telefono[i].principal) {
                        //if (telefono[i].pais!='52'){
                        //this.model.set('phone_office', "base" + telefono[i].pais + " " + telefono[i].telefono);
                        //}else{
                        this.model.set('phone_office', "" + telefono[i].telefono);
                        //}
                    }
                }
            }
        }
    },

    doValidateInfoReq: function (fields, errors, callback) {
        if (this.model.get('origen_cuenta_c') == '3') {
            var metodoProspeccion = new String(this.model.get('prospeccion_propia_c'));
            if (metodoProspeccion.length == 0 || this.model.get('prospeccion_propia_c') == null) {
                /*app.alert.show("Metodo de Prospeccion Requerido", {
                 level: "error",
                 title: "Debe indicar el metodo de prospecci\u00F3n",
                 autoClose: false
                 });*/
                errors['prospeccion_propia_c'] = errors['prospeccion_propia_c'] || {};
                errors['prospeccion_propia_c'].required = true;
            }
        }
        callback(null, fields, errors);
    },

    macrosector: function (fields, errors, callback) {
        //Validacion Actividad Economica - antes macro sector
        if (($('.list_ae').select2('val') == "0" || $('.list_ae').select2('val') == "" || $('.list_ae')[0].innerText.trim() == "") && (this.model.get('tipo_registro_cuenta_c') == '3' || this.model.get('tipo_registro_cuenta_c') == '5'
            || this.model.get('esproveedor_c') == true || this.model.get('subtipo_registro_cuenta_c') == '7' || this.model.get('subtipo_registro_cuenta_c') == '8' || this.model.get('subtipo_registro_cuenta_c') == '9')) {

            //Entra a modo edición el campo custom
            fieldAE5 = this.getField('account_clasf_sectorial');
            this.inlineEditMode = true;
            this.setButtonStates(this.STATE.EDIT);
            this.toggleField(fieldAE5);

            $('.campoAE').find('.record-label').css('color', 'red');
            $('.list_ae').find('.select2-choice').css('border-color', 'red');
            errors['actividadeconomica_c'] = "Error: Favor de verificar los errores";
            errors['actividadeconomica_c'].required = true;
        }

        //Validacion de php SetRequired para macro sector - ahora actividad economica
        // if (this.model.get('tipo_registro_cuenta_c') == '3' || this.model.get('tipo_registro_cuenta_c') == '5' ||
        //     this.model.get('subtipo_registro_cuenta_c') == '7' || this.model.get('subtipo_registro_cuenta_c') == '8' || this.model.get('subtipo_registro_cuenta_c') == '9') {

        //     $('.campoAE').find('.record-label').css('color', 'red');
        //     $('.list_ae').find('.select2-choice').css('border-color', 'red');
        //     errors['actividadeconomica_c'] = "Error: Favor de verificar los errores";
        //     errors['actividadeconomica_c'].required = true;

        // }
        callback(null, fields, errors);
    },

    // sectoreconomico: function (fields, errors, callback) {
    //Validacion de Sector Economico custom
    // if (this.model.get('tipodepersona_c') != 'Persona Fisica' && ($('.list_se').select2('val') == '' || $('.list_se')[0].innerText.trim() == '') && (this.model.get('tipo_registro_cuenta_c') == '3' || this.model.get('tipo_registro_cuenta_c') == '5' || this.model.get('esproveedor_c') == true)) {

    //     $('.campoSE').find('.record-label').css('color', 'red');
    //     $('.list_se').find('.select2-choice').css('border-color', 'red');
    //     errors['sectoreconomico_c'] = "Error: Favor de verificar los errores";
    //     errors['sectoreconomico_c'].required = true;
    // }

    //Validacion de php SetRequired para Sector Economico custom
    // if (this.model.get('tipo_registro_cuenta_c') != '1' || this.model.get('tipo_registro_cuenta_c') != '3' || this.model.get('subtipo_registro_cuenta_c') != '2') {

    //     $('.campoSE').find('.record-label').css('color', 'red');
    //     $('.list_se').find('.select2-choice').css('border-color', 'red');
    //     errors['sectoreconomico_c'] = "Error: Favor de verificar los errores";
    //     errors['sectoreconomico_c'].required = true;
    // }
    // callback(null, fields, errors);
    // },

    validadirecc: function (fields, errors, callback) {
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
            /*
            //Colonia
            if (direccion[iDireccion].colonia == "") {
                cont++;
                this.$('.coloniaExisting .select2-choice').eq(iDireccion).css('border-color', 'red');
            } else {
                this.$('.coloniaExisting .select2-choice').eq(iDireccion).css('border-color', '');
            }
            */
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
            var direcciones = [];
            Object.keys(direccion).forEach(key => {

                //Se agrega al arreglo de direcciones para validar duplicadas, solo si la dirección se encuentra Activa
                if (direccion[key].inactivo == 0) {
                    var direccion_string = direccion[key].valCodigoPostal + direccion[key].pais + direccion[key].estado + direccion[key].municipio + direccion[key].ciudad + direccion[key].colonia + direccion[key].calle.trim().toLowerCase() + direccion[key].numint.trim().toLowerCase() + direccion[key].numext.trim().toLowerCase();
                    direcciones.push(direccion_string);
                }

            });

            /*
            var coincidencia = 0;
            var indices = [];
            for (var i = 0; i < direccion.length; i++) {
                for (var j = 0; j < direccion.length; j++) {
                    if (i != j && direccion[i].inactivo == 0 && direccion[j].calle.trim().toLowerCase() + direccion[j].ciudad + direccion[j].colonia + direccion[j].estado + direccion[j].municipio + direccion[j].numext.trim().toLowerCase() + direccion[j].pais + direccion[j].postal + direccion[j].inactivo == direccion[i].calle.trim().toLowerCase() + direccion[i].ciudad + direccion[i].colonia + direccion[i].estado + direccion[i].municipio + direccion[i].numext.trim().toLowerCase() + direccion[i].pais + direccion[i].postal + direccion[i].inactivo) {
                        coincidencia++;
                        indices.push(i);
                        indices.push(j);
                    }
                }
            }*/
            //indices=indices.unique();
            if (direcciones.length > 0) {
                if (this.containsDuplicates(direcciones)) {
                    app.alert.show('error_direccion_duplicada', {
                        level: 'error',
                        autoClose: false,
                        messages: 'Existen direcciones iguales, favor de corregir.'
                    });
                    errors['dire_direccion_duplicada'] = errors['dire_direccion_duplicada'] || {};
                    errors['dire_direccion_duplicada'].required = true;

                }

            }
        }

        callback(null, fields, errors);
    },

    containsDuplicates: function (array) {
        if (array.length !== new Set(array).size) {
            return true;
        }
        return false;
    },

    validatelefonos: function (fields, errors, callback) {
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
                if (valor4.length !== 10) {
                    msjError += '<br>-Debe contener 10 dígitos';
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
                    var tel1 = telefono[j].telefono.replace(/ /gi, "");
                    var tel2 = telefono[i].telefono.replace(/ /gi, "");
                    if (tel1 == tel2 && telefono[j].estatus == 'Activo' && telefono[i].estatus == 'Activo' && i != j) {
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


    valida_backoffice: function () {
        var roles_limit = app.lang.getAppListStrings('roles_limit_list');
        var roles_logged = app.user.attributes.roles;
        var coincide_rol = 0;
        for (var i = 0; i < roles_logged.length; i++) {
            for (var rol_limit in roles_limit) {
                if (roles_logged[i] == roles_limit[rol_limit]) {
                    coincide_rol++;
                }
            }
        }
        if (coincide_rol != 0) {
            app.api.call('GET', app.api.buildURL('GetUsersTeams/' + this.model.get('id') + '/Accounts'), null, {
                success: _.bind(function (pertenece_a_equipo) {
                    if (pertenece_a_equipo == false) {
                        console.log('Funcion Valida_backoffice:' + pertenece_a_equipo);
                        app.alert.show("No Rol", {
                            level: "error",
                            title: "No puedes ver la cuenta ya no formas parte de ningun equipo.",
                            autoClose: false,
                            return: false,
                        });
                        app.router.navigate('#Accounts', { trigger: true });
                    }
                }, this),
            });
        }
    },

    valida_centro_prospec: function () {
        var roles_limit = app.lang.getAppListStrings('roles_limit_list_2');
        var roles_logged = app.user.attributes.roles;
        var coincide_rol = 0;
        for (var i = 0; i < roles_logged.length; i++) {
            for (var rol_limit in roles_limit) {
                if (roles_logged[i] == roles_limit[rol_limit]) {
                    coincide_rol++;
                }
            }
        }
        if (coincide_rol != 0) {
            if (this.model.get('tipo_registro_cuenta_c') != "1") {
                app.alert.show("No Rol2", {
                    level: "error",
                    title: "No puedes ver la cuenta ya que no tienes  el perfil adecuado.",
                    autoClose: false,
                    return: false,
                });
                app.router.navigate('#Accounts', { trigger: true });
            } else {
                app.api.call('GET', app.api.buildURL('GetUsersBoss/' + this.model.get('id')), null, {
                    success: _.bind(function (es_promotor) {
                        if (es_promotor == false) {
                            console.log('Funcion valida_centro_prospec:' + es_promotor);
                            app.alert.show("No Rol3", {
                                level: "error",
                                title: "No puedes ver la cuenta ya que no tienes  el perfil adecuado.",
                                autoClose: false,
                                return: false,
                            });
                            app.router.navigate('#Accounts', { trigger: true });
                        }
                    }, this),
                });
            }
        }
    },

    //Funcion que valida el contenido ingresado en el campo del Email
    expmail: function (fields, errors, callback) {
        if (this.model.get('email') != null && this.model.get('email') != "" && this.model.get('email') != undefined) {

            var input = (this.model.get('email'));
            var expresion = /^\S+@\S+\.\S+[$%&|<>#]?$/;
            var cumple = true;

            for (i = 0; i < input.length; i++) {

                if (expresion.test(input[i].email_address) == false) {
                    cumple = false;
                }
                if (input[i].email_address.includes('@unifin') || input[i].email_address.includes('@uniclick')) {
                    cumple = false;
                } else {
                    cumple = true;
                }
            }

            if (cumple == false) {
                app.alert.show('Error al validar email', {
                    level: 'error',
                    autoClose: true,
                    messages: '<b>Formato de email incorrecto.</b>'
                })
                errors['email'] = errors['email'] || {};
                errors['email'].required = true;
            }
        }

        callback(null, fields, errors);
    },


    validacedente: function (fields, errors, callback) {

        if (this.model.get('cedente_factor_c') == true) {

            var value = this.oDirecciones.direccion;
            var totalindicadores = "";

            if (value != undefined) {
                for (i = 0; i < value.length; i++) {
                    console.log("Valida Cedente");
                    if (this._getIndicador(value[i].inactivo) != "1") {
                        var valorecupera = this._getIndicador(value[i].indicador);
                        totalindicadores = totalindicadores + "," + valorecupera;
                    }
                }
            }

            var arregloindicadores = [];
            if (value == "" || value == null) {
                arregloindicadores = [0];

            } else {
                arregloindicadores = totalindicadores.split(",");
            }

            var direccionesfaltantes = "";

            if (arregloindicadores.indexOf("1") == -1) {
                direccionesfaltantes = direccionesfaltantes + 'Correspondencia<br>';
            }
            if (arregloindicadores.indexOf("2") == -1) {
                direccionesfaltantes = direccionesfaltantes + 'Fiscal<br>';
            }
            if (arregloindicadores.indexOf("4") == -1) {
                direccionesfaltantes = direccionesfaltantes + 'Entrega de Bienes<br>';
            }

            if (direccionesfaltantes != "") {
                $('.select2-choices').css('border-color', 'red');
                app.alert.show('Error al validar Direcciones', {
                    level: 'error',
                    autoClose: false,
                    messages: 'Debe tener las siguientes direcciones: <br><b>' + direccionesfaltantes + '</b>'
                })
                errors['account_direcciones_c'] = errors['account_direcciones_c'] || {};
                errors['account_direcciones_c'].required = true;
            }
            else {
                $('.select2-choices').css('border-color', '');
            }
            //Validar campos adionales
            if (this.model.get('tipo_registro_cuenta_c') == '4' || this.model.get('tipo_registro_cuenta_c') == '2') {

                if (this.model.get('rfc_c') == "" || this.model.get('rfc_c') == null) {
                    errors['rfc_c'] = errors['rfc_c'] || {};
                    errors['rfc_c'].required = true;
                }
                if (this.model.get('pais_nacimiento_c') == "" || this.model.get('pais_nacimiento_c') == null) {
                    errors['pais_nacimiento_c'] = errors['pais_nacimiento_c'] || {};
                    errors['pais_nacimiento_c'].required = true;
                }
                if (this.model.get('estado_nacimiento_c') == "" || this.model.get('estado_nacimiento_c') == null) {
                    errors['estado_nacimiento_c'] = errors['estado_nacimiento_c'] || {};
                    errors['estado_nacimiento_c'].required = true;
                }
                if (this.model.get('tipodepersona_c') == 'Persona Moral') {
                    //Requerido Actividad Economica - antes macro sector
                    /*if ($('.list_ae').select2('val') == "0" || $('.list_ae').select2('val') == "" || $('.list_ae')[0].innerText.trim() == "" || $('.list_ae').select2('val') == null) {

                        //Entra a modo edición el campo custom
                        fieldAE2 = this.getField('account_clasf_sectorial');
                        this.inlineEditMode = true;
                        this.setButtonStates(this.STATE.EDIT);
                        this.toggleField(fieldAE2);

                        $('.campoAE').find('.record-label').css('color', 'red');
                        $('.list_ae').find('.select2-choice').css('border-color', 'red');
                        errors['actividadeconomica_c'] = errors['actividadeconomica_c'] || {};
                        errors['actividadeconomica_c'].required = true;
                    }*/
                    if (this.model.get('fechaconstitutiva_c') == "" || this.model.get('fechaconstitutiva_c') == null) {
                        errors['fechaconstitutiva_c'] = errors['fechaconstitutiva_c'] || {};
                        errors['fechaconstitutiva_c'].required = true;
                    }
                } else {

                    if (this.model.get('curp_c') == "" || this.model.get('curp_c') == null) {
                        errors['curp_c'] = errors['curp_c'] || {};
                        errors['curp_c'].required = true;
                    }
                    if (this.model.get('apellidomaterno_c') == "" || this.model.get('apellidomaterno_c') == null) {
                        errors['apellidomaterno_c'] = errors['apellidomaterno_c'] || {};
                        errors['apellidomaterno_c'].required = true;
                    }
                    if (this.model.get('fechadenacimiento_c') == "" || this.model.get('fechadenacimiento_c') == null) {
                        errors['fechadenacimiento_c'] = errors['fechadenacimiento_c'] || {};
                        errors['fechadenacimiento_c'].required = true;
                    }
                    if (this.model.get('genero_c') == "" || this.model.get('genero_c') == null) {
                        errors['genero_c'] = errors['genero_c'] || {};
                        errors['genero_c'].required = true;
                    }

                    /*if ($('.list_ae').select2('val') == "0" || $('.list_ae').select2('val') == "" || $('.list_ae')[0].innerText.trim() == "" || $('.list_ae').select2('val') == null) {

                        //Entra a modo edición el campo custom
                        fieldAE3 = this.getField('account_clasf_sectorial');
                        this.inlineEditMode = true;
                        this.setButtonStates(this.STATE.EDIT);
                        this.toggleField(fieldAE3);

                        $('.campoAE').find('.record-label').css('color', 'red');
                        $('.list_ae').find('.select2-choice').css('border-color', 'red');
                        errors['actividadeconomica_c'] = errors['actividadeconomica_c'] || {};
                        errors['actividadeconomica_c'].required = true;
                    }*/
                }
            }
        }


        if (this.model.get('deudor_factor_c') == true) {

            /**********Campos requeridos para check Deudor Factor*******/
            var value = this.oDirecciones.direccion;
            var totalindicadores = "";

            if (value != undefined) {
                for (i = 0; i < value.length; i++) {
                    if (this._getIndicador(value[i].inactivo) != "1") {
                        var valorecupera = this._getIndicador(value[i].indicador);
                        totalindicadores = totalindicadores + "," + valorecupera;
                    }
                }
            }

            var arregloindicadores = [];
            if (value == "" || value == null) {
                arregloindicadores = [0];

            } else {
                arregloindicadores = totalindicadores.split(",");
            }

            var direccionesfaltantes = "";
            if (arregloindicadores.indexOf("2") == -1) {
                direccionesfaltantes = direccionesfaltantes + 'Fiscal<br>';
            }
            if (direccionesfaltantes != "") {

                app.alert.show('Error al validar Direcciones', {
                    level: 'error',
                    autoClose: false,
                    messages: 'Debe tener las siguiente direcci\u00F3n: <br><b>' + direccionesfaltantes + '</b>'
                })
                /****************Se agrega requerido campo Tipo de Dirección para Fiscal************/
                errors['account_direcciones_c'] = errors['account_direcciones_c'] || {};
                errors['account_direcciones_c'].required = true;
                this.$('#s2id_multiIndicador .select2-choices').css('border-color', 'red');
            } else {
                this.$('#s2id_multiIndicador .select2-choices').css('border-color', '');
            }

            if (this.model.get('tipodepersona_c') == "Persona Moral" && (this.model.get('razonsocial_c') == "" || this.model.get('razonsocial_c') == null)) {
                errors['razonsocial_c'] = errors['razonsocial_c'] || {};
                errors['razonsocial_c'].required = true;
            }
            //Requerido Actividad Economica custom
            /*if ($('.list_ae').select2('val') == "0" || $('.list_ae').select2('val') == "" || $('.list_ae')[0].innerText.trim() == "" || $('.list_ae').select2('val') == null) {

                //Entra a modo edición el campo custom
                fieldAE4 = this.getField('account_clasf_sectorial');
                this.inlineEditMode = true;
                this.setButtonStates(this.STATE.EDIT);
                this.toggleField(fieldAE4);

                $('.campoAE').find('.record-label').css('color', 'red');
                $('.list_ae').find('.select2-choice').css('border-color', 'red');
                errors['actividadeconomica_c'] = errors['actividadeconomica_c'] || {};
                errors['actividadeconomica_c'].required = true;
            }*/
            if (this.model.get('rfc_c') == "" || this.model.get('rfc_c') == null) {
                errors['rfc_c'] = errors['rfc_c'] || {};
                errors['rfc_c'].required = true;
            }
            if (this.model.get('tct_pais_expide_rfc_c') == "" || this.model.get('tct_pais_expide_rfc_c') == null) {
                errors['tct_pais_expide_rfc_c'] = errors['tct_pais_expide_rfc_c'] || {};
                errors['tct_pais_expide_rfc_c'].required = true;
            }

        }

        callback(null, fields, errors);

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

            console.log(result);
        }

        return result;
    },

    validapotencial: function (fields, errors, callback) {

        if ((this.model.get('tipo_registro_cuenta_c') == '2' && this.model.get('subtipo_registro_cuenta_c') == '8') || this.model.get('tipo_registro_cuenta_c') == '3') {
            if (this.model.get('ventas_anuales_c') == undefined || this.model.get('ventas_anuales_c') == "" || (Number(this.model.get('ventas_anuales_c')) <= 0)) {
                errors['ventas_anuales_c'] = "Este campo debe tener un valor mayor a 0.";
                errors['ventas_anuales_c'].required = true;
                app.alert.show('Error_ventas_anuales', {
                    level: 'error',
                    autoClose: false,
                    messages: 'El campo <b>ventas anuales</b> debe tener un valor mayor a 0.'
                });
            }
            if (this.model.get('tct_ano_ventas_ddw_c') == undefined || this.model.get('tct_ano_ventas_ddw_c') == "") {
                errors['tct_ano_ventas_ddw_c'] = "Se debe seleccionar el año de ventas";
                errors['tct_ano_ventas_ddw_c'].required = true;
            }
            if (this.model.get('activo_fijo_c') == undefined || this.model.get('activo_fijo_c') == "" || (Number(this.model.get('activo_fijo_c')) <= 0)) {
                errors['activo_fijo_c'] = "Este campo debe tener un valor mayor a 0.";
                errors['activo_fijo_c'].required = true;
                app.alert.show('Error_activof', {
                    level: 'error',
                    autoClose: false,
                    messages: 'El campo <b>activo fijo</b> debe tener un valor mayor a 0.'
                });
            }
        }
        callback(null, fields, errors);
    },

    valida_requeridos: function (fields, errors, callback) {
        var roles = App.user.attributes.roles;
        var roles_seguros = App.lang.getAppListStrings('roles_edicion_ctas_seguros_list');

        var seguros = 0;
        for (const [key, value] of Object.entries(roles_seguros)) {
            if (roles.includes(value)) {
                seguros = 1;
            }
        }
        if (!seguros) {
            var campos = "";
            _.each(errors, function (value, key) {
                _.each(this.model.fields, function (field) {
                    if (_.isEqual(field.name, key)) {
                        if (field.vname) {
                            if (field.vname == 'LBL_PAIS_NACIMIENTO_C' && this.model.get('tipodepersona_c') == 'Persona Moral') {
                                campos = campos + '<b>País de constitución</b><br>';
                            }
                            else {
                                if (field.vname == 'LBL_ESTADO_NACIMIENTO' && this.model.get('tipodepersona_c') == 'Persona Moral') {
                                    campos = campos + '<b>Estado de constitución</b><br>';
                                }
                                else {
                                    campos = campos + '<b>' + app.lang.get(field.vname, "Accounts") + '</b><br>';
                                }
                            }
                        }
                    }
                }, this);
            }, this);
            //Remueve campos custom: Teléfonos, Direcciones, Correo
            //campos = campos.replace("<b>Telefonos</b><br>", "");
            campos = campos.replace("<b>Direcciones</b><br>", "");
            //campos = campos.replace("<b>Dirección de Correo Electrónico</b><br>", "");

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

    validaRequeridosPLD: function (fields, errors, callback) {
        var faltantesAP = "";
        var faltantesFF = "";
        var faltantesCA = "";
        var faltantesCS = "";

        //Valida requeridos a partir de Prospecto Interesado
        var tipoCuenta = this.model.get('tipo_registro_cuenta_c');
        var subtipoCuenta = this.model.get('subtipo_registro_cuenta_c');
        if (tipoCuenta != '') {
            //Valida campos para AP
            if (App.user.attributes.tipodeproducto_c == '1') {

                //Pregunta: Propietario Real-ap
                if (contexto_cuenta.ProductosPLD.arrendamientoPuro.campo3 == '' && this.model.get('tipodepersona_c') != 'Persona Moral' && contexto_cuenta.ProductosPLD.arrendamientoPuro.campo2 == '2') {
                    $('.campo3rel-ap').find('.select2-choice').css('border-color', 'red');
                    faltantesAP = faltantesAP + '<b>-Propietario Real<br></b>';
                } else {
                    $('.campo3rel-ap').find('.select2-choice').css('border-color', '');
                }

                //Pregunta: Especifique AP
                if (contexto_cuenta.ProductosPLD.arrendamientoPuro.campo17 == '' && contexto_cuenta.ProductosPLD.arrendamientoPuro.campo14 == true) {
                    $('.campo17txt-ap').css('border-color', 'red');
                    faltantesAP = faltantesAP + '<b>-Especifique:<br></b>';
                } else {
                    $('.campo17txt-ap').css('border-color', '');
                }

            }
            //Valida campos para FF
            if (App.user.attributes.tipodeproducto_c == '4') {

                //Pregunta: Propietario Real-ff
                if (contexto_cuenta.ProductosPLD.factorajeFinanciero.campo3 == '' && this.model.get('tipodepersona_c') != 'Persona Moral' && contexto_cuenta.ProductosPLD.factorajeFinanciero.campo2 == '2') {
                    $('.campo3rel-ff').find('.select2-choice').css('border-color', 'red');
                    faltantesFF = faltantesFF + '<b>-Propietario Real<br></b>';
                } else {
                    $('.campo3rel-ff').find('.select2-choice').css('border-color', '');
                }

                //Pregunta: Especifique-ff
                if (contexto_cuenta.ProductosPLD.factorajeFinanciero.campo17 == '' && contexto_cuenta.ProductosPLD.factorajeFinanciero.campo14 == true && contexto_cuenta.ProductosPLD.factorajeFinanciero.campo2 == '2') {
                    $('.campo17txt-ff').css('border-color', 'red');
                    faltantesFF = faltantesFF + '<b>-Especifique<br></b>';
                } else {
                    $('.campo17txt-ff').css('border-color', '');
                }
            }
            //Valida campos para CA
            if (App.user.attributes.tipodeproducto_c == '3') {

                //Pregunta: Propietario Real-ca
                if (contexto_cuenta.ProductosPLD.creditoAutomotriz.campo3 == '' && this.model.get('tipodepersona_c') != 'Persona Moral' && contexto_cuenta.ProductosPLD.creditoAutomotriz.campo2 == '2') {
                    $('.campo3rel-ca').find('.select2-choice').css('border-color', 'red');
                    faltantesCA = faltantesCA + '<b>-Propietario Real<br></b>';
                } else {
                    $('.campo3rel-ca').find('.select2-choice').css('border-color', '');
                }
            }
            //Valida campos para Credito Simple
            //Valida Propietario Real-cs
            if (contexto_cuenta.ProductosPLD.creditoSimple.campo2 == "2" && contexto_cuenta.ProductosPLD.creditoSimple.campo3 == "") {
                $('.campo3rel-cs').find('.select2-choice').css('border-color', 'red');
                faltantesCS = faltantesCS + '<b>-Propietario Real<br></b>';
            }
            if (faltantesCS != "") {
                errors['error_CS'] = errors['error_CS'] || {};
                errors['error_CS'].required = true;
                app.alert.show("Faltante preguntas de Credito Simple", {
                    level: "error",
                    title: "PLD Crédito Simple - Faltan las siguientes preguntas por contestar: <br>" + faltantesCS,
                });

            }
            //Genera alertas
            if (faltantesAP != "") {
                errors['PreguntasAP'] = "";
                errors['PreguntasAP'].required = true;
                app.alert.show("faltantesAP", {
                    level: "error",
                    title: "PLD Arrendamiento puro - Faltan las siguientes preguntas por contestar: <br>" + faltantesAP
                });
            }

            if (faltantesFF != "") {
                errors['PreguntasFF'] = "";
                errors['PreguntasFF'].required = true;
                app.alert.show("faltantesFF", {
                    level: "error",
                    title: "PLD Factoraje financiero - Faltan las siguientes preguntas por contestar: <br>" + faltantesFF
                });
            }

            if (faltantesCA != "") {
                errors['PreguntasCA'] = "";
                errors['PreguntasCA'].required = true;
                app.alert.show("faltantesCA", {
                    level: "error",
                    title: "PLD Crédito automotriz - Faltan las siguientes preguntas por contestar: <br>" + faltantesCA
                });
            }

        }
        callback(null, fields, errors);
    },

    checkaccdatestatements: function (fields, errors, callback) {
        if (this.model.get('tct_dates_acc_statements_c') != "") {
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth() + 1; //January is 0!
            var yyyy = today.getFullYear();
            if (dd < 10) {
                dd = '0' + dd
            }
            if (mm < 10) {
                mm = '0' + mm
            }
            today = yyyy + '-' + mm + '-' + dd;

            this.obj_dates = JSON.parse(this.model.get('tct_dates_acc_statements_c'));
            var c = 0;
            for (var elem in this.obj_dates) { // revisar con Axel antes subir venia let cambie por var
                if (this.obj_dates[elem].trim() == "") {
                    $('#' + elem).css('border-color', 'red');
                    c++;
                }
            }
            if (c > 0) {
                app.alert.show("empty_date", {
                    level: "error",
                    title: "Existen fechas de los estados de cuenta <b>vac\u00EDas</b>, favor de verificar",
                    autoClose: false
                });

                errors['empty_date'] = errors['empty_date'] || {};
                errors['empty_date'].required = true;
            }
        }
        callback(null, fields, errors);
    },

    keyDownNewExtension: function (evt) {
        if (!evt) return;
        if (!this.validanumeros(evt)) {
            return false;
        }
    },
    validanumeros: function (evt) {
        if ($.inArray(evt.keyCode, [110, 188, 45, 33, 36, 35, 34, 8, 9, 20, 16, 17, 37, 40, 39, 38, 16, 49, 50, 51, 52, 53, 54, 55, 56, 57, 48, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105]) < 0) {
            app.alert.show("Caracter Invalido", {
                level: "error",
                title: "Solo n\u00FAmeros son permitidos en este campo.",
                autoClose: true
            });
            return false;

        } else {
            return true;
        }
    },

    validacantidades: function (fields, errors, callback) {
        if ($('.campo23dec-ff').val() != "" && $('.campo23dec-ff').val() != undefined && $('.campo23dec-ff').val() <= 0) {
            $('.campo23dec-ff').css('border-color', 'red');
            app.alert.show("Valor Invalido", {
                level: "error",
                title: "El campo Número de pagos no debe tener un valor menor a 0.",
                autoClose: true
            });
            errors['campo23dec-ff'] = "El campo Número de pagos no debe tener un valor menor a 0.";
            errors['campo23dec-ff'].required = true;
        }
        if ($('.campo22int-ff').val() != "" && $('.campo22int-ff').val() != undefined && $('.campo22int-ff').val() <= 0) {
            $('.campo22int-ff').css('border-color', 'red');
            app.alert.show("Valor Invalido2", {
                level: "error",
                title: "El campo Monto total aproximado no debe tener un valor menor a 0.",
                autoClose: true
            });
            errors['campo22int-ff'] = "El campo Monto total aproximado no debe tener un valor menor a 0.";
            errors['campo22int-ff'].required = true;
        }

        callback(null, fields, errors);
    },

    /*******************************REQUERIDOS NO VIABLE************************** */
    // requeridosleasingnv: function (fields, errors, callback) {
    //     var faltantesleasnv = 0;
    //     if ($('.campo1chk')[0].checked && ($('.campo4nvl').select2('val') == "" || $('.campo4nvl').select2('val') == "0")) {
    //         $('.campo4nvl').find('.select2-choice').css('border-color', 'red');
    //         faltantesleasnv += 1;
    //     }
    //     if ($('.campo1chk')[0].checked == true && $('.campo4nvl').select2('val') == "1" && ($('.campo7nvl').select2('val') == "" || $('.campo7nvl').select2('val') == "0")) {
    //         $('.campo7nvl').find('.select2-choice').css('border-color', 'red');
    //         faltantesleasnv += 1;
    //     }
    //     if ($('.campo1chk')[0].checked == true && $('.campo4nvl').select2('val') == "2" && ($('.campo19nvl').select2('val') == "" || $('.campo19nvl').select2('val') == "0")) {
    //         $('.campo19nvl').find('.select2-choice').css('border-color', 'red');
    //         faltantesleasnv += 1;
    //     }
    //     if ($('.campo1chk')[0].checked == true && $('.campo4nvl').select2('val') == "7" && ($('.campo25nvl').select2('val') == "" || $('.campo25nvl').select2('val') == "0")) {
    //         $('.campo25nvl').find('.select2-choice').css('border-color', 'red');
    //         faltantesleasnv += 1;
    //     }
    //     if ($('.campo1chk')[0].checked == true && $('.campo4nvl').select2('val') == "3" && $('.campo10nvl').val().trim() == "" && $('.campo13nvl').val().trim() == "") {
    //         $('.campo10nvl').css('border-color', 'red');
    //         $('.campo13nvl').css('border-color', 'red');
    //         faltantesleasnv += 1;
    //     }
    //     if ($('.campo1chk')[0].checked == true && $('.campo4nvl').select2('val') == "3" && $('.campo10nvl').val().trim() == "" && $('.campo13nvl').val().trim() != "") {
    //         $('.campo10nvl').css('border-color', 'red');
    //         faltantesleasnv += 1;
    //     }
    //     if ($('.campo1chk')[0].checked == true && $('.campo4nvl').select2('val') == "3" && $('.campo10nvl').val().trim() != "" && $('.campo13nvl').val().trim() == "") {
    //         $('.campo13nvl').css('border-color', 'red');
    //         faltantesleasnv += 1;
    //     }
    //     if ($('.campo1chk')[0].checked == true && $('.campo4nvl').select2('val') == "4" && ($('.campo16nvl').select2('val') == "" || $('.campo16nvl').select2('val') == "0")) {
    //         $('.campo16nvl').find('.select2-choice').css('border-color', 'red');
    //         faltantesleasnv += 1;
    //     }
    //     if (($('.campo4nvl').select2('val') == "4" || $('.campo4nvl option:selected').text() == "4" || $('.campo4nvl')[0].innerText.trim() == "4") && ($('.campo16nvl').select2('val') == "4" || $('.campo16nvl option:selected').text() == "4" || $('.campo16nvl')[0].innerText.trim() == "4") && $('.campo1chk')[0].checked && $('.campo22nvl').val().trim() == "") {
    //         $('.campo22nvl').css('border-color', 'red');
    //         faltantesleasnv += 1;
    //     }
    //     if (faltantesleasnv > 0) {
    //         app.alert.show("Faltantes no viable Leasing", {
    //             level: "error",
    //             title: 'Hace falta seleccionar alguna de las razones del cat\u00E1logo <b>Raz\u00F3n lead no viable en Leasing.',
    //             autoClose: false
    //         });
    //         errors['error_leasing'] = errors['error_leasing'] || {};
    //         errors['error_leasing'].required = true;
    //     }
    //     if (faltantesleasnv == 0 && $('.campo1chk')[0].checked == true && lnv.leadNoViable.PromotorLeasing == "") {
    //         this.model.set('promotorleasing_c', '9 - No Viable');
    //         this.model.set('user_id_c', 'cc736f7a-4f5f-11e9-856a-a0481cdf89eb');
    //         lnv.leadNoViable.PromotorLeasing = App.user.attributes.id;
    //     }
    //     callback(null, fields, errors);

    // },
    // requeridosfacnv: function (fields, errors, callback) {
    //     var faltantesfactnv = 0;
    //     if ($('.campo2chk')[0].checked == true && ($('.campo5nvf').select2('val') == "" || $('.campo5nvf').select2('val') == "0")) {
    //         $('.campo5nvf').find('.select2-choice').css('border-color', 'red');
    //         faltantesfactnv += 1;
    //     }
    //     if ($('.campo2chk')[0].checked == true && $('.campo5nvf').select2('val') == "1" && ($('.campo8nvf').select2('val') == "" || $('.campo8nvf').select2('val') == "0")) {
    //         $('.campo8nvf').find('.select2-choice').css('border-color', 'red');
    //         faltantesfactnv += 1;
    //     }
    //     if ($('.campo2chk')[0].checked == true && $('.campo5nvf').select2('val') == "2" && ($('.campo20nvf').select2('val') == "" || $('.campo20nvf').select2('val') == "0")) {
    //         $('.campo20nvf').find('.select2-choice').css('border-color', 'red');
    //         faltantesfactnv += 1;
    //     }
    //     if ($('.campo2chk')[0].checked == true && $('.campo5nvf').select2('val') == "7" && ($('.campo26nvf').select2('val') == "" || $('.campo26nvf').select2('val') == "0")) {
    //         $('.campo26nvf').find('.select2-choice').css('border-color', 'red');
    //         faltantesfactnv += 1;
    //     }
    //     if ($('.campo2chk')[0].checked == true && $('.campo5nvf').select2('val') == "3" && $('.campo11nvf').val().trim() == "" && $('.campo14nvf').val().trim() == "") {
    //         $('.campo11nvf').css('border-color', 'red');
    //         $('.campo14nvf').css('border-color', 'red');
    //         faltantesfactnv += 1;
    //     }
    //     if ($('.campo2chk')[0].checked == true && $('.campo5nvf').select2('val') == "3" && $('.campo11nvf').val().trim() == "" && $('.campo14nvf').val().trim() != "") {
    //         $('.campo11nvf').css('border-color', 'red');
    //         faltantesfactnv += 1;
    //     }
    //     if ($('.campo2chk')[0].checked == true && $('.campo5nvf').select2('val') == "3" && $('.campo11nvf').val().trim() != "" && $('.campo14nvf').val().trim() == "") {
    //         $('.campo14nvf').css('border-color', 'red');
    //         faltantesfactnv += 1;
    //     }
    //     if ($('.campo2chk')[0].checked == true && $('.campo5nvf').select2('val') == "4" && ($('.campo17nvf').select2('val') == "" || $('.campo17nvf').select2('val') == "0")) {
    //         $('.campo17nvf').find('.select2-choice').css('border-color', 'red');
    //         faltantesfactnv += 1;
    //     }
    //     if (($('.campo5nvf').select2('val') == "4" || $('.campo5nvf option:selected').text() == "4" || $('.campo5nvf')[0].innerText.trim() == "4") && ($('.campo17nvf').select2('val') == "4" || $('.campo17nvf option:selected').text() == "4" || $('.campo17nvf')[0].innerText.trim() == "4") && $('.campo2chk')[0].checked && $('.campo23nvf').val().trim() == "") {
    //         $('.campo23nvf').css('border-color', 'red');
    //         faltantesfactnv += 1;
    //     }
    //     if (faltantesfactnv > 0) {
    //         app.alert.show("Faltantes no viable Factoraje", {
    //             level: "error",
    //             title: 'Hace falta seleccionar alguna de las razones del cat\u00E1logo <b>Raz\u00F3n lead no viable en Factoraje.',
    //             autoClose: false
    //         });
    //         errors['error_factoraje'] = errors['error_factoraje'] || {};
    //         errors['error_factoraje'].required = true;
    //     } else if (faltantesfactnv == 0 && $('.campo2chk')[0].checked == true && lnv.leadNoViable.PromotorFactoraje == "") {
    //         this.model.set('promotorfactoraje_c', '9 - No Viable');
    //         this.model.set('user_id1_c', 'cc736f7a-4f5f-11e9-856a-a0481cdf89eb');
    //         lnv.leadNoViable.PromotorFactoraje = App.user.attributes.id;
    //     }
    //     callback(null, fields, errors);
    // },
    // requeridoscanv: function (fields, errors, callback) {
    //     var faltantescanv = 0;
    //     if ($('.campo3chk')[0].checked == true && ($('.campo6nvca').select2('val') == "" || $('.campo6nvca').select2('val') == "0")) {
    //         $('.campo6nvca').find('.select2-choice').css('border-color', 'red');
    //         faltantescanv += 1;
    //     }
    //     if ($('.campo3chk')[0].checked == true && $('.campo6nvca').select2('val') == "1" && ($('.campo9nvca').select2('val') == "" || $('.campo9nvca').select2('val') == "0")) {
    //         $('.campo9nvca').find('.select2-choice').css('border-color', 'red');
    //         faltantescanv += 1;
    //     }
    //     if ($('.campo3chk')[0].checked == true && $('.campo6nvca').select2('val') == "2" && ($('.campo21nvca').select2('val') == "" || $('.campo21nvca').select2('val') == "0")) {
    //         $('.campo21nvca').find('.select2-choice').css('border-color', 'red');
    //         faltantescanv += 1;
    //     }
    //     if ($('.campo3chk')[0].checked == true && $('.campo6nvca').select2('val') == "7" && ($('.campo27nvca').select2('val') == "" || $('.campo27nvca').select2('val') == "0")) {
    //         $('.campo27nvca').find('.select2-choice').css('border-color', 'red');
    //         faltantescanv += 1;
    //     }
    //     if ($('.campo3chk')[0].checked == true && $('.campo6nvca').select2('val') == "3" && $('.campo12nvca').val().trim() == "" && $('.campo15nvca').val().trim() == "") {
    //         $('.campo12nvca').css('border-color', 'red');
    //         $('.campo15nvca').css('border-color', 'red');
    //         faltantescanv += 1;
    //     }
    //     if ($('.campo3chk')[0].checked == true && $('.campo6nvca').select2('val') == "3" && $('.campo12nvca').val().trim() == "" && $('.campo15nvca').val().trim() != "") {
    //         $('.campo12nvca').css('border-color', 'red');
    //         faltantescanv += 1;
    //     }
    //     if ($('.campo3chk')[0].checked == true && $('.campo6nvca').select2('val') == "3" && $('.campo12nvca').val().trim() != "" && $('.campo15nvca').val().trim() == "") {
    //         $('.campo15nvca').css('border-color', 'red');
    //         faltantescanv += 1;
    //     }
    //     if ($('.campo3chk')[0].checked == true && $('.campo6nvca').select2('val') == "4" && ($('.campo18nvca').select2('val') == "" || $('.campo18nvca').select2('val') == "0")) {
    //         $('.campo18nvca').find('.select2-choice').css('border-color', 'red');
    //         faltantescanv += 1;
    //     }
    //     if (($('.campo6nvca').select2('val') == "4" || $('.campo6nvca option:selected').text() == "4" || $('.campo6nvca')[0].innerText.trim() == "4") && ($('.campo18nvca').select2('val') == "4" || $('.campo18nvca option:selected').text() == "4" || $('.campo18nvca')[0].innerText.trim() == "4") && $('.campo3chk')[0].checked && $('.campo24nvca').val().trim() == "") {
    //         $('.campo24nvca').css('border-color', 'red');
    //         faltantescanv += 1;
    //     }
    //     if (faltantescanv > 0) {
    //         app.alert.show("Faltantes no viable Crédito Automotriz", {
    //             level: "error",
    //             title: 'Hace falta seleccionar alguna de las razones del cat\u00E1logo <b>Raz\u00F3n lead no viable en Credito Automotriz.',
    //             autoClose: false
    //         });
    //         errors['error_ca'] = errors['error_ca'] || {};
    //         errors['error_ca'].required = true;
    //     } else if (faltantescanv == 0 && $('.campo3chk')[0].checked == true && lnv.leadNoViable.PromotorCreditA == "") {
    //         this.model.set('promotorcredit_c', '9 - No Viable');
    //         this.model.set('user_id2_c', 'cc736f7a-4f5f-11e9-856a-a0481cdf89eb');
    //         lnv.leadNoViable.PromotorCreditA = App.user.attributes.id;
    //     }
    //     callback(null, fields, errors);

    // },
    //Pregunta si la cuenta es LEAD para poder mostrar los checks de leads no viables:
    // muestracheks: function () {
    //     if (Oproductos.productos != undefined) {
    //         if (Oproductos.productos.tct_tipo_ca_txf_c != 'Lead' && Oproductos.productos.tct_tipo_f_txf_c != 'Lead' && Oproductos.productos.tct_tipo_l_txf_c != 'Lead') {
    //             $('[data-name=tct_noviable]').hide();
    //         }
    //     }
    // },

    ocultaRFC: function () {
        // if (this.model.get('tipo_relacion_c').includes('Proveedor de Recursos')) {
        //     $('[data-name=tct_pais_expide_rfc_c]').show();
        // }
        if (this.model.get('tct_pais_expide_rfc_c') != "2") {
            this.$('[data-name="generar_rfc_c"]').attr('style', 'pointer-events:none;');
        } else {
            this.$('[data-name="generar_rfc_c"]').attr('style', 'pointer-events:block;');
        }
    },

    proveedorRecursos: function (fields, errors, callback) {
        if ($('.campo4ddw-ap').select2('val') == "2" || $('.campo4ddw-ca').select2('val') == "2" || $('.campo4ddw-ff').select2('val') == "2" || $('.campo4ddw-cs').select2('val') == "2" || $('.campo10ddw-ce').select2('val') == "2") {
            var Cuenta = this.model.get('id')
            var apicall = app.api.buildURL('Rel_Relaciones/?filter[0][$or][0][account_id1_c][$equals]=' + Cuenta + '&filter[0][$or][1][rel_relaciones_accounts_1accounts_ida][$equals]=' + Cuenta, null, null);
            app.api.call('GET', apicall, {}, {
                success: _.bind(function (data) {

                    var relacionl = 0;
                    var relacionca = 0;
                    var relacionff = 0;
                    var relacioncs = 0;
                    var relacioncr = 0;
                    var productos = "";
                    var esPropietario = false;
                    var esCLiente = false;
                    var esTercero = false;
                    var tieneProvRec = false;
                    esCLiente = (this.model.get('tipo_registro_cuenta_c') == "3") ? true : false;
                    tienePR = (contexto_cuenta.ProductosPLD.creditoRevolvente.campo9 == '') ? false : true;
                    esTercero = (contexto_cuenta.ProductosPLD.creditoRevolvente.campo10 == '2') ? true : false;
                    if (data.records.length > 0) {
                        for (var l = 0; l < data.records.length; l++) {
                            //Producto Arrendamiento Puro
                            if (App.user.attributes.productos_c.includes(1) && $('.campo4ddw-ap').select2('val') == "2") {

                                if (data.records[l].relaciones_activas.includes('Proveedor de Recursos L') && data.records[l].rel_relaciones_accounts_1accounts_ida == Cuenta) {
                                    relacionl++;

                                }
                            }
                            //Producto Credito Automotriz
                            if (App.user.attributes.productos_c.includes(3) && $('.campo4ddw-ca').select2('val') == "2") {

                                if (data.records[l].relaciones_activas.includes('Proveedor de Recursos CA') && data.records[l].rel_relaciones_accounts_1accounts_ida == Cuenta) {
                                    relacionca++;
                                }
                            }
                            //Producto Factoraje Financiero
                            if (App.user.attributes.productos_c.includes(4) && $('.campo4ddw-ff').select2('val') == "2") {

                                if (data.records[l].relaciones_activas.includes('Proveedor de Recursos F') && data.records[l].rel_relaciones_accounts_1accounts_ida == Cuenta) {
                                    relacionff++;
                                }
                            }
                            //Producto Credito Simple
                            if ($('.campo4ddw-cs').select2('val') == "2") {

                                if (data.records[l].relaciones_activas.includes('Proveedor de Recursos CS') && data.records[l].rel_relaciones_accounts_1accounts_ida == Cuenta) {
                                    relacioncs++;
                                }
                            }
                            //Credito Envolvente
                            if ((App.user.attributes.productos_c.includes(8) || App.user.attributes.productos_c.includes(14)) && $('.campo10ddw-ce').select2('val') == "2") {
                                if (data.records[l].relaciones_activas.includes('Proveedor de los Recursos CR') && data.records[l].rel_relaciones_accounts_1accounts_ida == Cuenta) {
                                    relacioncr++;
                                    tieneProvRec = true;
                                }
                                if (data.records[l].relaciones_activas.includes('Propietario Real') && data.records[l].account_id1_c == Cuenta) {
                                    esPropietario = true;
                                }
                            }
                        }
                    }

                    //Validacion Arrendamiento Puro
                    if (relacionl == 0 && $('.campo4ddw-ap').select2('val') == "2") {
                        $('.campo4ddw-ap').find('.select2-choice').css('border-color', 'red');
                        productos = productos + '<b>Arrendamiento Puro</b><br>';
                        errors['error_leasingPR'] = errors['error_leasingPR'] || {};
                        errors['error_leasingPR'].required = true;
                    } else {
                        $('.campo4ddw-ap').find('.select2-choice').css('border-color', '');
                    }
                    //Validacion Credito Automotriz
                    if (relacionca == 0 && $('.campo4ddw-ca').select2('val') == "2") {
                        $('.campo4ddw-ca').find('.select2-choice').css('border-color', 'red');
                        productos = productos + '<b>Crédito Automotriz</b><br>';
                        errors['error_CAPR'] = errors['error_CAPR'] || {};
                        errors['error_CAPR'].required = true;
                    } else {
                        $('.campo4ddw-ca').find('.select2-choice').css('border-color', '');
                    }
                    //Validacion Factoraje Financiero
                    if (relacionff == 0 && $('.campo4ddw-ff').select2('val') == "2") {
                        $('.campo4ddw-ff').find('.select2-choice').css('border-color', 'red');
                        productos = productos + '<b>Factoraje Financiero</b><br>';
                        errors['error_FPR'] = errors['error_FPR'] || {};
                        errors['error_FPR'].required = true;
                    } else {
                        $('.campo4ddw-ff').find('.select2-choice').css('border-color', '');
                    }

                    //Validacion Credito Simple
                    if (relacioncs == 0 && $('.campo4ddw-cs').select2('val') == "2") {
                        $('.campo4ddw-cs').find('.select2-choice').css('border-color', 'red');
                        productos = productos + '<b>Crédito Simple</b><br>';
                        errors['error_FPR'] = errors['error_FPR'] || {};
                        errors['error_FPR'].required = true;
                    } else {
                        $('.campo4ddw-cs').find('.select2-choice').css('border-color', '');
                    }
                    //Validacion Credito revolvente
                    if ((!esPropietario && esTercero) || (esCLiente && esTercero) && !tieneProvRec) {
                        $('.campo10ddw-ce').find('.select2-choice').css('border-color', 'red');
                        productos = productos + '<b>Crédito Revolvente</b><br>';
                        errors['error_CR'] = errors['error_FPR'] || {};
                        errors['error_CR'].required = true;
                    } else {
                        $('.campo10ddw-ce').find('.select2-choice').css('border-color', '');
                    }
                    if (productos != "") {
                        app.alert.show("Faltante Relacion Proveedor de Recursos", {
                            level: "error",
                            messages: 'Hace falta completar la siguiente información en la <b>Cuenta:</b><br> (Se debe agregar una relación de tipo <b>Proveedor de Recursos</b> para el/los producto(s):<br>' + productos,
                            autoClose: false
                        });
                    }
                    callback(null, fields, errors);
                }, this)
            });
        } else {
            callback(null, fields, errors);
        }

    },

    direccionesparticularPR: function (fields, errors, callback) {
        //Valida direcciones en relaciones
        if ($('.campo4ddw-ap').select2('val') == "2" || $('.campo4ddw-ff').select2('val') == "2" || $('.campo4ddw-ca').select2('val') == "2" || $('.campo4ddw-cs').select2('val') == "2" && this.model.get('tipodepersona_c') != 'Persona Moral') {
            var apicalldir = app.api.buildURL('DireCuenta/' + this.model.get("id"), null);
            app.api.call('GET', apicalldir, {}, {
                success: _.bind(function (data) {
                    if (data != "") {
                        app.alert.show("Falta direccion Particular en cuenta", {
                            level: "error",
                            messages: 'Hace falta agregar una dirección para la(s) siguientes <b>Cuentas</b><br> (En una relación de tipo <b>Proveedor de Recursos</b>):<br>' + data + '<br><br><b>Nota:</b> Se require dirección <b>Particular</b> para persona Física y FCAE. En caso de persona moral al menos un tipo de dirección.',
                            autoClose: false
                        });
                        errors['errordireccionPR'] = errors['errordireccionPR'] || {};
                        errors['errordireccionPR'].required = true;
                    }
                    callback(null, fields, errors);
                }, this)
            });
        } else {
            callback(null, fields, errors);
        }

    },

    quitaanos: function () {
        var anoactual = ((new Date).getFullYear());
        var anoactual5 = anoactual - 5
        var anoselect = this.model.get('tct_ano_ventas_ddw_c');
        var lista = App.lang.getAppListStrings('ano_ventas_ddw_list');
        Object.keys(lista).forEach(function (key) {
            //Quita años previos
            if (key < anoactual5) {
                //delete lista[key]; //Se dejan habilitadois años previos
            }
            //Quita años futuros al actual
            if (key > anoactual) {
                delete lista[key];
            }
        });
        if (anoselect != undefined) {
            lista[anoselect] = anoselect;
        }
        this.model.fields['tct_ano_ventas_ddw_c'].options = lista;
    },

    blockRecordNoContactar: function () {
        //Consulta resumen para validar bloqueo de registro
        //if(!app.user.attributes.tct_no_contactar_chk_c && !app.user.attributes.bloqueo_credito_c && !app.user.attributes.bloqueo_cumple_c) {
        var url = app.api.buildURL('tct02_Resumen/' + this.model.get('id'), null, null);
        app.api.call('read', url, {}, {
            success: _.bind(function (data) {
                if (data.bloqueo_cartera_c || data.bloqueo2_c || data.bloqueo3_c) {
                    var equipo = "";
                    if (data.bloqueo_cartera_c) equipo = "Cartera<br>";
                    if (data.bloqueo2_c) equipo = equipo + "Crédito<br>";
                    if (data.bloqueo3_c) equipo = equipo + "Cumplimiento";

                    this.context.param_equipo = equipo;
                    //Bloquear el registro completo y mostrar alerta
                    $(".record-cell").attr("style", "pointer-events:none");
                    $('[name="edit_button"].rowaction').hide();

                    this.ocultaOpcionesSubpanel();

                    /**LLAMADA A NUEVA VISTA */
                    if (Modernizr.touch) {
                        app.$contentEl.addClass("content-overflow-visible");
                    }
                    /**check whether the view already exists in the layout.
                     * If not we will create a new view and will add to the components list of the record layout
                     * */
                    var quickCreateView = this.layout.getComponent(
                        "alert-account-no-contactar"
                    );
                    if (!quickCreateView) {
                        /** Create a new view object */
                        quickCreateView = app.view.createView({
                            context: this.context,
                            name: "alert-account-no-contactar",
                            layout: this.layout,
                            module: "Accounts",
                        });
                        /** add the new view to the components list of the record layout*/
                        this.layout._components.push(quickCreateView);
                        this.layout.$el.append(quickCreateView.$el);
                    }
                    /**triggers an event to show the pop up quick create view*/
                    this.layout.trigger("app:view:alert-account-no-contactar");

                }
            }, this)
        });
        //}
    },

    ocultaOpcionesSubpanel: function () {

        //Obtiene atributo de CAC
        var esCac = App.user.attributes.cac_c;
        //Oculta botón de creación en subpaneles
        $(".subpanels-layout")
            .find(".filtered.tabbable")
            .find('[name="create_button"]')
            .hide();
        //Oculta botón de creación en emails
        $(".subpanels-layout")
            .find(".filtered.tabbable")
            .find('[name="email_compose_button"]')
            .hide();
        //Oculta botón de acciones en subpaneles
        $(".subpanels-layout")
            .find(".filtered.tabbable")
            .find(".btn.dropdown-toggle")
            .hide();

        //Para cuentas bloqueadas, si el usuario pertenece al CAC, si tiene permiso de crear Casos
        if (esCac == 1) {
            $(".subpanels-layout")
                .find('.filtered.tabbable[data-subpanel-link="cases"]')
                .find('[name="create_button"]')
                .show();

            $(".subpanels-layout")
                .find('.filtered.tabbable[data-subpanel-link="cases"]')
                .find(".btn.dropdown-toggle")
                .show();
        }


    },

    blockRecordNoViable: function () {
        var userpuesto = app.user.attributes.puestousuario_c;
        var puestos = ['5', '11', '16', '53', '54'];

        var idCuenta = this.model.get('id');
        var listCondicion = App.lang.getAppListStrings('status_management_list');
        var listRazon = App.lang.getAppListStrings('razon_list');


        app.api.call('GET', app.api.buildURL('GetProductosCuentas/' + idCuenta), null, {
            success: function (data) {
                valProd = data;

                var bloquemsg = false;
                var estatusmsg = "";
                var razonmsg = "";
                _.each(valProd, function (value, key) {
                    if (userpuesto.includes(puestos) || app.user.id == valProd[key]['user_id_c']) {
                        if (valProd[key]['aprueba1_c'] == '1' && valProd[key]['aprueba2_c'] == '1') {
                            var strUrl = 'tct4_Condiciones?filter[][condicion]=' + valProd[key].status_management_c + '&filter[][razon]=' + valProd[key].razon_c;
                            app.api.call("GET", app.api.buildURL(strUrl), null, {
                                success: _.bind(function (data1) {
                                    if (data1.records.length > 0) {
                                        razon = Productos[key].razon_c;
                                        motivo = (Productos[key].motivo_c == null) ? "" : Productos[key].motivo_c;

                                        _.each(data1.records, function (valor, llave) {
                                            if (data1.records[llave].razon == razon && data1.records[llave].motivo == motivo && data1.records[llave].bloquea) {
                                                bloquemsg = true;
                                                estatusmsg = data1.records[llave].condicion;
                                                razonmsg = data1.records[llave].razon;
                                            }

                                        });

                                        if (bloquemsg) {
                                            $('.record.tab-layout').attr('style', 'pointer-events:none');
                                            $('.subpanel').attr('style', 'pointer-events:none');
                                            app.alert.show("cuentas_no_contactar", {
                                                level: "error",
                                                title: "Cuenta No Contactable",
                                                messages: "La cuenta se encuentra " + listCondicion[estatusmsg] + " debido a " + listRazon[razonmsg] + " . <br>Es necesario reactivar la cuenta, para retomar actividad comercial",
                                                autoClose: false
                                            });
                                        }

                                    }
                                }, this)
                            });
                        }
                    }
                });
            },
            error: function (e) {
                throw e;
            }
        });
    },


    get_addresses: function () {

        //Extiende This
        this.oDirecciones = [];
        this.oDirecciones.direccion = [];
        this.prev_oDirecciones = [];
        this.prev_oDirecciones.prev_direccion = [];

        //Define variables
        var listMapTipo = App.lang.getAppListStrings('tipo_dir_map_list');
        var listTipo = App.lang.getAppListStrings('dir_tipo_unique_list');
        var listMapIndicador = App.lang.getAppListStrings('dir_indicador_map_list');
        var listIndicador = App.lang.getAppListStrings('dir_indicador_unique_list');
        var idCuenta = this.model.get('id');
        var json_direccion = {};
        /*
        # Valida bloqueo de indicador fiscal
        */
        var edicionCiudadList = App.lang.getAppListStrings('edicion_ciudad_list');
        this.editaCiudad = false;
        for (const [key, value] of Object.entries(edicionCiudadList)) {
            //console.log('value:'+value);
            if (App.user.id == value) {
                this.editaCiudad = true;
            }
        }

        //Recupera información
        if (!_.isEmpty(idCuenta) && idCuenta != "") {
            app.api.call('GET', app.api.buildURL('Accounts/' + idCuenta + '/link/accounts_dire_direccion_1?filter[0][indicador][$not_equals]=64'), null, {
                success: _.bind(function (data) {
                    contexto_cuenta.length_direcciones = data.records.length;
                    //Itera y agrega direcciones
                    for (var i = 0; i < data.records.length; i++) {
                        //Asignando valores de los campos
                        var tipo = data.records[i].tipodedireccion.toString();
                        var tipoSeleccionados = '^' + listMapIndicador[tipo].replace(/,/gi, "^,^") + '^';
                        var indicador = data.records[i].indicador;
                        var indicadorSeleccionados = '^' + listMapIndicador[indicador].replace(/,/gi, "^,^") + '^';

                        /*
                        var valCodigoPostal = data.records[i].dire_direccion_dire_codigopostal_name;
                        var idCodigoPostal = data.records[i].dire_direccion_dire_codigopostaldire_codigopostal_ida;
                        var valPais = data.records[i].dire_direccion_dire_pais_name;
                        var idPais = data.records[i].dire_direccion_dire_paisdire_pais_ida;
                        var valEstado = data.records[i].dire_direccion_dire_estado_name;
                        var idEstado = data.records[i].dire_direccion_dire_estadodire_estado_ida;
                        var valMunicipio = data.records[i].dire_direccion_dire_municipio_name;
                        var idMunicipio = data.records[i].dire_direccion_dire_municipiodire_municipio_ida;
                        var valCiudad = data.records[i].dire_direccion_dire_ciudad_name;
                        var idCiudad = data.records[i].dire_direccion_dire_ciudaddire_ciudad_ida;
                        var valColonia = data.records[i].dire_direccion_dire_colonia_name;
                        var idColonia = data.records[i].dire_direccion_dire_coloniadire_colonia_ida;
                        */
                        //Se obtiene campo description para obtener los id (recordar que el description guarda los id separados por pipeline | 
                        //ejemplo: "{$idPais}|{$idEstado}|{$idCiudad}|{$idMunicipio}|{$idColonia}"

                        var description = data.records[i].description;

                        var ids = description.split('|');

                        var identificadorPais = ids[0];
                        var identificadorEstado = ids[1];
                        var identificadorCiudad = ids[2];
                        var identificadorMunicipio = ids[3];
                        var identificadorColonia = ids[4];

                        var valCodigoPostal = data.records[i].codigo_postal_c;
                        var idCodigoPostal = data.records[i].dir_sepomex_dire_direcciondir_sepomex_ida;
                        var valPais = data.records[i].pais_c;
                        var idPais = identificadorPais;
                        var valEstado = data.records[i].estado_c;
                        var idEstado = identificadorEstado;
                        var valMunicipio = data.records[i].municipio_c;
                        var idMunicipio = identificadorMunicipio;
                        var valCiudad = data.records[i].ciudad_c;
                        var idCiudad = identificadorCiudad;
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
                        var bloqueado = (indicadorSeleccionados.indexOf('2') != -1) ? 1 : 0;
                        var accesoFiscal = App.user.attributes.tct_alta_clientes_chk_c + App.user.attributes.tct_altaproveedor_chk_c + App.user.attributes.tct_alta_cd_chk_c + App.user.attributes.deudor_factoraje_c;
                        bloqueado = (this.model.get('tipo_registro_cuenta_c') == 4 || this.model.get('tipo_registro_cuenta_c') == 5) ? 0 : bloqueado;
                        var editaCiudad = contexto_cuenta.editaCiudad;
                        if (this.model.get('tipo_registro_cuenta_c') == '3' && this.model.get('subtipo_registro_cuenta_c') != '11' && this.model.get('origen_cuenta_c') != '11' && indicadorSeleccionados.includes('^2^')) {
                            bloqueado = 1;
                        }
                        var validaDireccion = data.records[i].cambio_direccion_c;
                        if (!validaDireccion) {
                            validaDireccion = (contexto_cuenta.model.get('cambio_dirfiscal_c') != undefined && indicadorSeleccionados.includes('^2^')) ? contexto_cuenta.model.get('cambio_dirfiscal_c') : validaDireccion;
                        }

                        //Parsea a objeto direccion
                        var direccion = {
                            "tipodedireccion": tipo,
                            "listTipo": listTipo,
                            "tipoSeleccionados": tipoSeleccionados,
                            "indicador": indicador,
                            "listIndicador": listIndicador,
                            "indicadorSeleccionados": indicadorSeleccionados,
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
                            "direccionCompleta": direccionCompleta,
                            "bloqueado": bloqueado,
                            "editaCiudad": editaCiudad,
                            "validaDireccion": validaDireccion
                        };

                        if (_.isEmpty(idCodigoPostal)) {
                            app.alert.show("no_match_sepomex", {
                                level: "info",
                                messages: 'Una dirección no está homologada con sepomex.<br>Favor de verificar.',
                                autoClose: false
                            });
                            direccion['sinSepomex'] = true;

                            direccion['oldCP'] = data.records[i].dire_direccion_dire_codigopostal_name;
                            direccion['oldPais'] = data.records[i].dire_direccion_dire_pais_name;
                            direccion['oldEstado'] = data.records[i].dire_direccion_dire_estado_name;
                            direccion['oldMunicipio'] = data.records[i].dire_direccion_dire_municipio_name;
                            direccion['oldCiudad'] = data.records[i].dire_direccion_dire_ciudad_name;
                            direccion['oldColonia'] = data.records[i].dire_direccion_dire_colonia_name;

                        }

                        //Agregar dirección
                        contexto_cuenta.oDirecciones.direccion.push(direccion);

                        if (valCodigoPostal != "") {

                            //recupera información asociada a CP
                            var strUrl = 'DireccionesCP/' + valCodigoPostal + '/' + i;
                            app.api.call('GET', app.api.buildURL(strUrl), null, {
                                success: _.bind(function (data) {
                                    //recupera info
                                    var list_paises = data.paises;
                                    var list_municipios = data.municipios;
                                    //var city_list = App.metadata.getCities();
                                    var city_list = data.ciudades_metadata;
                                    var list_ciudades = data.ciudades;
                                    var list_estados = data.estados;
                                    var list_colonias = data.colonias;
                                    //Poarsea valores para listas
                                    //País
                                    listPais = {};
                                    for (var i = 0; i < list_paises.length; i++) {
                                        listPais[list_paises[i].idPais] = list_paises[i].namePais;
                                    }
                                    contexto_cuenta.oDirecciones.direccion[data.indice].listPais = listPais;
                                    contexto_cuenta.oDirecciones.direccion[data.indice].listPaisFull = listPais;
                                    //Municipio
                                    listMunicipio = {};
                                    for (var i = 0; i < list_municipios.length; i++) {
                                        listMunicipio[list_municipios[i].idMunicipio] = list_municipios[i].nameMunicipio;
                                    }
                                    contexto_cuenta.oDirecciones.direccion[data.indice].listMunicipio = listMunicipio;
                                    contexto_cuenta.oDirecciones.direccion[data.indice].listMunicipioFull = listMunicipio;
                                    //Estado
                                    listEstado = {};
                                    for (var i = 0; i < list_estados.length; i++) {
                                        listEstado[list_estados[i].idEstado] = list_estados[i].nameEstado;
                                    }
                                    contexto_cuenta.oDirecciones.direccion[data.indice].listEstado = listEstado;
                                    contexto_cuenta.oDirecciones.direccion[data.indice].listEstadoFull = listEstado;
                                    //Colonia
                                    listColonia = {};
                                    for (var i = 0; i < list_colonias.length; i++) {
                                        //listColonia[list_colonias[i].idColonia] = list_colonias[i].nameColonia;
                                        listColonia[i] = {};
                                        listColonia[i]['idColonia'] = list_colonias[i].idColonia;
                                        listColonia[i]['nameColonia'] = list_colonias[i].nameColonia;
                                        listColonia[i]['idCodigoPostal'] = list_colonias[i].idCodigoPostal;
                                    }

                                    //Se ordena la lista de colonias
                                    /*
                                    var  arrayColonias = Object.entries(listColonia);
                                    // Ordenar el array por valores de manera ascendente
                                    arrayColonias.sort(function (a, b) {
                                        return a[1].localeCompare(b[1]);
                                    });
                                    */
                                    // Se establece el arreglo ya ordenado
                                    //listColonia = Object.fromEntries(arrayColonias);

                                    contexto_cuenta.oDirecciones.direccion[data.indice].listColonia = listColonia;
                                    contexto_cuenta.oDirecciones.direccion[data.indice].listColoniaFull = listColonia;
                                    //Ciudad
                                    //listCiudad = {}
                                    idSinCiudad = '';
                                    //ciudades = Object.values(city_list);
                                    /*
                                    for (var [key, value] of Object.entries(contexto_cuenta.oDirecciones.direccion[data.indice].listEstado)) {
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
                                    }

                                    //Se ordena la lista de ciudades
                                    var arrayCiudades = Object.entries(listCiudad);
                                    arrayCiudades.sort(function (a, b) {
                                        return a[1].localeCompare(b[1]);
                                    });
                                    // Se establece el arreglo ya ordenado
                                    listCiudad = Object.fromEntries(arrayCiudades);

                                    contexto_cuenta.oDirecciones.direccion[data.indice].ciudad = (contexto_cuenta.oDirecciones.direccion[data.indice].ciudad == '') ? idSinCiudad : contexto_cuenta.oDirecciones.direccion[data.indice].ciudad;
                                    contexto_cuenta.oDirecciones.direccion[data.indice].valCiudad = (contexto_cuenta.oDirecciones.direccion[data.indice].valCiudad == '') ? 'SIN CIUDAD' : contexto_cuenta.oDirecciones.direccion[data.indice].valCiudad;
                                    contexto_cuenta.oDirecciones.direccion[data.indice].listCiudad = listCiudad;
                                    contexto_cuenta.oDirecciones.direccion[data.indice].listCiudadFull = listCiudad;

                                    //Genera objeto con valores previos para control de cancelar
                                    contexto_cuenta.prev_oDirecciones.prev_direccion = app.utils.deepCopy(contexto_cuenta.oDirecciones.direccion);
                                    cont_dir.oDirecciones = contexto_cuenta.oDirecciones;

                                    //Construye JSON para controlar cambio de dirección fiscal
                                    //var json_direccion = {};
                                    if (Number(data['indice']) + 1 == contexto_cuenta.length_direcciones) {
                                        try {
                                            json_direccion = JSON.parse(contexto_cuenta.model.get('json_direccion_audit_c'));
                                        } catch (error) {
                                            json_direccion = {};
                                        }
                                        json_direccion['json_dire_actual'] = contexto_cuenta.prev_oDirecciones.prev_direccion;
                                        //json_direccion['json_dire_actualizar'] = cont_dir.oDirecciones.direccion;
                                        //contexto_cuenta.model.set('json_direccion_audit_c',JSON.stringify(json_direccion));
                                    }

                                    //Aplica render a campo custom
                                    cont_dir.render();

                                }, contexto_cuenta)
                            });
                        }
                    }
                }, this)
            });
        }
    },


    setCustomFields: function (fields, errors, callback) {
        if ($.isEmptyObject(errors)) {
            //Teléfonos
            this.prev_oTelefonos.prev_telefono = app.utils.deepCopy(this.oTelefonos.telefono);
            this.model.set('account_telefonos', this.oTelefonos.telefono);

            //Direcciones
            this.prev_oDirecciones.prev_direccion = app.utils.deepCopy(this.oDirecciones.direccion);

            //Actualiza campo que guarda json de direcciones
            var json_direcciones_campo = this.model.get('json_direccion_audit_c');

            if (json_direcciones_campo != "") {
                var json_direcciones = JSON.parse(json_direcciones_campo);

                json_direcciones['json_dire_actualizar'] = this.oDirecciones.direccion;
                var d = new Date();
                var fecha_actual = d.toLocaleString();
                json_direcciones['fecha_cambio'] = fecha_actual;
                this.model.set('json_direccion_audit_c', JSON.stringify(json_direcciones));
            }

            this.model.set('account_direcciones', this.oDirecciones.direccion);

            this.model.set('account_direccion_buro_credito', contexto_dire_buro.direccionBuro);

        }
        //Callback a validation task
        callback(null, fields, errors);
    },

    //Sobre escribe función para recuperar info de registros relacionados
    _saveModel: function () {
        var options,
            successCallback = _.bind(function () {
                // Loop through the visible subpanels and have them sync. This is to update any related
                // fields to the record that may have been changed on the server on save.
                _.each(this.context.children, function (child) {
                    if (child.get('isSubpanel') && !child.get('hidden')) {
                        if (child.get('collapsed')) {
                            child.resetLoadFlag({ recursive: false });
                        } else {
                            child.reloadData({ recursive: false });
                        }
                    }
                });
                if (this.createMode) {
                    app.navigate(this.context, this.model);
                } else if (!this.disposed && !app.acl.hasAccessToModel('edit', this.model)) {
                    //re-render the view if the user does not have edit access after save.
                    this.render();
                }
                //Refresca cambios en teléfonos, direcciones y pld(Recupera ids de nuevos teléfonos)
                //location.reload();
                this.get_addresses();
                this.CamposCstmLoad();
                /************************************/

                /***********************************/
            }, this);

        //Call editable to turn off key and mouse events before fields are disposed (SP-1873)
        this.turnOffEvents(this.fields);

        options = {
            showAlerts: true,
            success: successCallback,
            error: _.bind(function (model, error) {
                if (error.status === 412 && !error.request.metadataRetry) {
                    this.handleMetadataSyncError(error);
                } else if (error.status === 409) {
                    app.utils.resolve409Conflict(error, this.model, _.bind(function (model, isDatabaseData) {
                        if (model) {
                            if (isDatabaseData) {
                                successCallback();
                            } else {
                                this._saveModel();
                            }
                        }
                    }, this));
                } else if (error.status === 403 || error.status === 404) {
                    this.alerts.showNoAccessError.call(this);
                } else {
                    this.editClicked();
                }
            }, this),
            lastModified: this.model.get('date_modified'),
            viewed: true
        };

        // ensure view and field are sent as params so collection-type fields come back in the response to PUT requests
        // (they're not sent unless specifically requested)
        options.params = options.params || {};
        if (this.context.has('dataView') && _.isString(this.context.get('dataView'))) {
            options.params.view = this.context.get('dataView');
        }

        if (this.context.has('fields')) {
            options.params.fields = this.context.get('fields').join(',');
        }

        options = _.extend({}, options, this.getCustomSaveOptions(options));

        this.model.save({}, options);
    },

    validaformato: function (fields, errors, callback) {
        //Validacion para pasar una expresion regular por los 3 campos y verificar dicho formato.
        var errorescampos = "";
        if (this.model.get('primernombre_c') != "" || this.model.get('apellidopaterno_c') != "" || this.model.get('apellidomaterno_c') != "") {
            var expresion = new RegExp(/^[a-zA-ZÀ-ÿ\s]*$/g);
            if (this.model.get('primernombre_c') != "" && this.model.get('primernombre_c') != undefined) {
                var nombre = this.model.get('primernombre_c');
                var res = expresion.test(nombre);
                if (res != true) {
                    errorescampos = errorescampos + '<b>-Primer Nombre<br></b>';
                    ;
                    errors['primernombre_c'] = errors['primernombre_c'] || {};
                    errors['primernombre_c'].required = true;
                }
            }
            if (this.model.get('apellidopaterno_c') != "" && this.model.get('apellidopaterno_c') != undefined) {
                var apaterno = this.model.get('apellidopaterno_c');
                var expresion = new RegExp(/^[a-zA-ZÀ-ÿ\s]*$/g);
                var res = expresion.test(apaterno);
                if (res != true) {
                    errorescampos = errorescampos + '<b>-Apellido Paterno<br></b>';
                    ;
                    errors['apellidopaterno_c'] = errors['apellidopaterno_c'] || {};
                    errors['apellidopaterno_c'].required = true;
                }
            }
            if (this.model.get('apellidomaterno_c') != "" && this.model.get('apellidomaterno_c') != undefined) {
                var amaterno = this.model.get('apellidomaterno_c');
                var expresion = new RegExp(/^[a-zA-ZÀ-ÿ\s]*$/g);
                var res = expresion.test(amaterno);
                if (res != true) {
                    errorescampos = errorescampos + '<b>-Apellido Materno<br></b>';
                    ;
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
    validaiva: function (fields, errors, callback) {
        if (this.model.get('tipo_registro_cuenta_c') == "5" || this.model.get('esproveedor_c') == true) {
            if (this.model.get('iva_c') !== "" && this.model.get('iva_c') != undefined && (Number(this.model.get('iva_c')) <= 0 || Number(this.model.get('iva_c')) > 100.00)) {
                if (parseFloat(this.model.get('iva_c')) <= 0.0000) {
                    errors['iva_c'] = errors['iva_c'] || {};
                    errors['iva_c'].required = true;

                    app.alert.show("Iva_mayor_a_cero", {
                        level: "error",
                        messages: "El campo <b>% de IVA</b> debe ser mayor a cero.",
                        autoClose: false
                    });
                }
                // Valida valor mayor a 100
                if (parseFloat(this.model.get('iva_c')) > 100.00) {

                    errors['iva_c'] = errors['iva_c'] || {};
                    errors['iva_c'].required = true;

                    app.alert.show("Iva_menor_a_cero", {
                        level: "error",
                        messages: "El campo <b>% de IVA</b> debe ser menor o igual a cien.",
                        autoClose: false
                    });
                }

            }
        }
        callback(null, fields, errors);
    },

    homonimo: function () {
        var listahom = App.lang.getAppListStrings('usuarios_permiso_homonimos_list');
        var usr1 = listahom[1];
        var usr2 = listahom[2]
        var userlogueado = App.user.attributes.id;
        if (usr1 == userlogueado || usr2 == userlogueado) {
            $('div[data-name=tct_homonimo_chk_c]').show();
        } else {
            $('div[data-name=tct_homonimo_chk_c]').hide();
        }
    },
    //Valida campos de Autos en Potencial
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
        if (Pautos.action == "edit") {
            var PotencialAutos = {};
            PotencialAutos.autos = {};
            PotencialAutos.autos.tct_no_autos_u_int_c = this.$('.campo1pa').val();
            PotencialAutos.autos.tct_no_autos_e_int_c = this.$('.campo2pa').val();
            PotencialAutos.autos.tct_no_motos_int_c = this.$('.campo3pa').val();
            PotencialAutos.autos.tct_no_camiones_int_c = this.$('.campo4pa').val();

            if ($.isEmptyObject(errors)) {
                this.model.set('potencial_autos', JSON.stringify(PotencialAutos));
                Pautos.autos = PotencialAutos.autos;
                //Guarda una copia previa en prev_autos
                Pautos.prev_autos = app.utils.deepCopy(Pautos.autos);
                Pautos.render();
            }
        }
        callback(null, fields, errors);
    },

    // clienteuniclickClicked: function () {
    //     App.alert.show('convierte_Cliente_uniclick', {
    //         level: 'process',
    //         title: 'Convirtiendo cuenta, por favor espere',
    //     });
    //     var necesarios = "";

    //     if (this.model.get('origen_cuenta_c') == "" || this.model.get('origen_cuenta_c') == null) {
    //         necesarios = necesarios + '<b>Origen<br></b>';
    //     }
    //     if (this.model.get('rfc_c') == "" || this.model.get('rfc_c') == null) {
    //         necesarios = necesarios + '<b>RFC<br></b>';
    //     }
    //     //Requerido Actividad Economica - antes macro sector
    //     if ($('.list_ae').select2('val') == "0" || $('.list_ae').select2('val') == "" || $('.list_ae')[0].innerText.trim() == "" || $('.list_ae').select2('val') == null) {
    //         necesarios = necesarios + '<b>Actividad Económica<br></b>';
    //     }
    //     //Requerido Sector Económico custom
    //     // if ($('.list_se').select2('val') == "" || $('.list_se')[0].innerText.trim() == "" || $('.list_se').select2('val') == null) {
    //     //     necesarios = necesarios + '<b>Sector Económico<br></b>';
    //     // }
    //     if (this.model.get('ventas_anuales_c') == "" || this.model.get('ventas_anuales_c') == null) {
    //         necesarios = necesarios + '<b>Ventas Anuales<br></b>';
    //     }
    //     if (this.model.get('activo_fijo_c') == "" || this.model.get('activo_fijo_c') == null) {
    //         necesarios = necesarios + '<b>Activo Fijo<br></b>';
    //     }
    //     if (_.isEmpty(this.model.get('email')) && _.isEmpty(this.oTelefonos.telefono)) {
    //         necesarios = necesarios + '<b>Al menos un correo electr\u00F3nico o un tel\u00E9fono<br></b>';
    //     }
    //     if (_.isEmpty(this.oDirecciones.direccion)) {
    //         necesarios = necesarios + '<b>Dirección<br></b>';
    //     } else {
    //         var direcciones = 0;
    //         var tipodireccion = this.oDirecciones.direccion;
    //         if (tipodireccion.length > 0) {
    //             for (var i = 0; i < tipodireccion.length; i++) {
    //                 if (tipodireccion[i].inactivo == 1) {
    //                     direcciones++;
    //                 }
    //             }
    //         }
    //         if (direcciones == tipodireccion.length) {
    //             necesarios = necesarios + '<b>Dirección<br></b>';
    //         }
    //     }
    //     if (this.model.get('tipodepersona_c') != "Persona Moral") {
    //         if (this.model.get('primernombre_c') == "" || this.model.get('primernombre_c') == null) {
    //             necesarios = necesarios + '<b>Primer Nombre</b><br>';
    //         }
    //         if (this.model.get('apellidopaterno_c') == "" || this.model.get('apellidopaterno_c') == null) {
    //             necesarios = necesarios + '<b>Apellido Paterno</b><br>';
    //         }
    //         if (this.model.get('apellidomaterno_c') == "" || this.model.get('apellidomaterno_c') == null) {
    //             necesarios = necesarios + '<b>Apellido Materno</b><br>';
    //         }
    //         if (this.model.get('fechadenacimiento_c') == "" || this.model.get('fechadenacimiento_c') == null) {
    //             necesarios = necesarios + '<b>Fecha de Nacimiento<br></b>';
    //         }
    //         if (this.model.get('genero_c') == "" || this.model.get('genero_c') == null) {
    //             necesarios = necesarios + '<b>G\u00E9nero</b><br>';
    //         }
    //         if (this.model.get('pais_nacimiento_c') == "" || this.model.get('pais_nacimiento_c') == null) {
    //             necesarios = necesarios + '<b>Pa\u00EDs de Nacimiento</b><br>';
    //         }
    //         if (this.model.get('ifepasaporte_c') == "" || this.model.get('ifepasaporte_c') == null) {
    //             necesarios = necesarios + '<b>IFE/Pasaporte<br></b>';
    //         }
    //         if (this.model.get('curp_c') == "" || this.model.get('curp_c') == null) {
    //             necesarios = necesarios + '<b>Curp<br></b>';
    //         }
    //         if (this.model.get('estadocivil_c') == "" || this.model.get('estadocivil_c') == null) {
    //             necesarios = necesarios + '<b>Estado Civil<br></b>';
    //         }
    //         if (this.model.get('profesion_c') == "" || this.model.get('profesion_c') == null) {
    //             necesarios = necesarios + '<b>Profesión<br></b>';
    //         }
    //     } else {
    //         if (this.model.get('razonsocial_c') == "" || this.model.get('razonsocial_c') == null) {
    //             necesarios = necesarios + '<b>Razón Social<br></b>';
    //         }
    //         if (this.model.get('nombre_comercial_c') == "" || this.model.get('nombre_comercial_c') == null) {
    //             necesarios = necesarios + '<b>Nombre Comercial<br></b>';
    //         }
    //         if (this.model.get('fechaconstitutiva_c') == "" || this.model.get('fechaconstitutiva_c') == null) {
    //             necesarios = necesarios + '<b>Fecha Constitutiva<br></b>';
    //         }
    //         if (this.model.get('pais_nacimiento_c') == "" || this.model.get('pais_nacimiento_c') == null) {
    //             necesarios = necesarios + '<b>Pa\u00EDs de Constitución</b><br>';
    //         }
    //     }
    //     if (necesarios != "") {
    //         app.alert.dismiss('convierte_Cliente_uniclick');
    //         app.alert.show("Campos Faltantes", {
    //             level: "error",
    //             title: "Faltan los siguientes campos para poder convertir la cuenta a Cliente: <br><br>" + necesarios,
    //             autoClose: false
    //         });
    //         return;
    //     } else {
    //         if (Oproductos.productos.tipo_registro_uc != '3') {
    //             var productousuario = App.user.attributes.productos_c;

    //             // Actualiza Cuenta
    //             if (this.model.get('tipo_registro_cuenta_c') != "3") {
    //                 this.model.set("tipo_registro_cuenta_c", "3");
    //                 this.model.set("subtipo_registro_cuenta_c", "18");
    //                 this.model.set("tct_tipo_subtipo_txf_c", "CLIENTE CON LÍNEA VIGENTE");
    //                 Oproductos.productos.tct_tipo_cuenta_uc_c = '3';
    //                 Oproductos.productos.tct_subtipo_uc_txf_c = '18';
    //                 this.model.save();

    //             }
    //             // Actualiza Productos
    //             _.each(Productos, function (value, key) {
    //                 var idprod = '';
    //                 if (app.user.id == this.model.get('user_id7_c') && Productos[key].tipo_producto == 8) {
    //                     idprod = Productos[key].id;
    //                 }
    //                 if (idprod) {
    //                     var params = {};
    //                     params["tipo_cuenta"] = "3";
    //                     params["subtipo_cuenta"] = "18";
    //                     params["tipo_subtipo_cuenta"] = "CLIENTE CON LÍNEA VIGENTE";
    //                     var uni = app.api.buildURL('uni_Productos/' + idprod, null, null);
    //                     app.api.call('update', uni, params, {
    //                         success: _.bind(function (data) {
    //                             vista360.ResumenCliente.uniclick.tipo_cuenta = '3';
    //                             vista360.ResumenCliente.uniclick.subtipo_cuenta = '18';
    //                             vista360.ResumenCliente.general_cliente.tipo = 'CLIENTE CON LÍNEA VIGENTE';
    //                             app.alert.dismiss('convierte_Cliente_uniclick');
    //                             app.alert.show('errorAlert', {
    //                                 level: 'success',
    //                                 messages: "Se ha realizado la conversión correctamente.",
    //                             });

    //                             vista360.render();
    //                             Oproductos.render();
    //                             //Deja activa la pestaña de la vista360
    //                             $('li.tab.LBL_RECORDVIEW_PANEL8').removeAttr("style");
    //                             $("#recordTab>li.tab").removeClass('active');
    //                             $('li.tab.LBL_RECORDVIEW_PANEL8').addClass("active");
    //                         })
    //                     });
    //                 }
    //             }, this);

    //         }
    //     }
    // },




    /*************************************************REQUERIDOS NO VIABLE POR UNI PRODUCTOS******************************************/

    /***********************************VALIDACION NO VIABLE PRODUCTO LEASING*********************************/
    requeridosLeasingUP: function (fields, errors, callback) {
        var faltantesleasup = 0;
        if ($('.chk_l_nv')[0] != undefined) {
            if ($('.chk_l_nv')[0].checked && ($('.list_l_nv_razon').select2('val') == "" || $('.list_l_nv_razon').select2('val') == null || $('.list_l_nv_razon').select2('val') == "0")) {
                $('.list_l_nv_razon').find('.select2-choice').css('border-color', 'red'); //Razón de Lead no viable
                faltantesleasup += 1;
            }
            if ($('.chk_l_nv')[0].checked == true && $('.list_l_nv_razon').select2('val') == "1" &&
                ($('.list_l_nv_razon_fp').select2('val') == "" || $('.list_l_nv_razon_fp').select2('val') == null || $('.list_l_nv_razon_fp').select2('val') == "0")) {

                $('.list_l_nv_razon_fp').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                faltantesleasup += 1;
            }
            if ($('.chk_l_nv')[0].checked == true && $('.list_l_nv_razon').select2('val') == "2" &&
                ($('.list_l_nv_razon_cf').select2('val') == "" || $('.list_l_nv_razon_cf').select2('val') == null || $('.list_l_nv_razon_cf').select2('val') == "0")) {

                $('.list_l_nv_razon_cf').find('.select2-choice').css('border-color', 'red'); //Condiciones Financieras
                faltantesleasup += 1;
            }
            if ($('.chk_l_nv')[0].checked == true && $('.list_l_nv_razon').select2('val') == "7" &&
                ($('.list_l_nv_razon_ni').select2('val') == "" || $('.list_l_nv_razon_ni').select2('val') == null || $('.list_l_nv_razon_ni').select2('val') == "0")) {

                $('.list_l_nv_razon_ni').find('.select2-choice').css('border-color', 'red'); //Razón No se encuentra interesado
                faltantesleasup += 1;
            }
            if ($('.chk_l_nv')[0].checked == true && $('.list_l_nv_razon').select2('val') == "3" && $('.txt_l_nv_quien').val().trim() == "" && $('.txt_l_nv_porque').val().trim() == "") {
                $('.txt_l_nv_quien').css('border-color', 'red');  //TXT ¿Quién?
                $('.txt_l_nv_porque').css('border-color', 'red'); //TXT ¿Por qué?
                faltantesleasup += 1;
            }
            if ($('.chk_l_nv')[0].checked == true && $('.list_l_nv_razon').select2('val') == "3" && $('.txt_l_nv_quien').val().trim() == "" && $('.txt_l_nv_porque').val().trim() != "") {
                $('.txt_l_nv_quien').css('border-color', 'red'); //TXT ¿Quién?
                faltantesleasup += 1;
            }
            if ($('.chk_l_nv')[0].checked == true && $('.list_l_nv_razon').select2('val') == "3" && $('.txt_l_nv_quien').val().trim() != "" && $('.txt_l_nv_porque').val().trim() == "") {
                $('.txt_l_nv_porque').css('border-color', 'red'); //TXT ¿Por qué?
                faltantesleasup += 1;
            }
            if ($('.chk_l_nv')[0].checked == true && $('.list_l_nv_razon').select2('val') == "4" &&
                ($('.list_l_nv_producto').select2('val') == "" || $('.list_l_nv_producto').select2('val') == null || $('.list_l_nv_producto').select2('val') == "0")) {

                $('.list_l_nv_producto').find('.select2-choice').css('border-color', 'red'); //¿Qué producto?
                faltantesleasup += 1;
            }
            if (($('.list_l_nv_razon').select2('val') == "4" || $('.list_l_nv_razon option:selected').text() == "4" || $('.list_l_nv_razon')[0].innerText.trim() == "4") &&
                ($('.list_l_nv_producto').select2('val') == "4" || $('.list_l_nv_producto option:selected').text() == "4" || $('.list_l_nv_producto')[0].innerText.trim() == "4") &&
                $('.chk_l_nv')[0].checked && $('.txt_l_nv_otro').val().trim() == "") {

                $('.txt_l_nv_otro').css('border-color', 'red'); //TXT ¿Qué producto?
                faltantesleasup += 1;
            }
            if (($('.list_l_nv_razon').select2('val') == "4" || $('.list_l_nv_razon option:selected').text() == "4" || $('.list_l_nv_razon')[0].innerText.trim() == "4") &&
                ($('.list_l_nv_producto').select2('val') == "4" || $('.list_l_nv_producto option:selected').text() == "4" || $('.list_l_nv_producto')[0].innerText.trim() == "4") &&
                $('.chk_l_nv')[0].checked && $('.txt_l_nv_otro').val().trim() == "") {
                $('.txt_l_nv_otro').css('border-color', 'red'); //TXT ¿Qué producto?
                faltantesleasup += 1;
            }
            if (faltantesleasup > 0) {
                app.alert.show("Faltantes no viable Leasing", {
                    level: "error",
                    title: 'Hace falta seleccionar alguna de las razones del catálogo <b>No Viable Leasing.',
                    autoClose: false
                });
                errors['error_leasingUP'] = errors['error_leasingUP'] || {};
                errors['error_leasingUP'].required = true;
            }
        }

        var productos = App.user.attributes.productos_c; //USUARIOS CON LOS SIGUIENTES PRODUCTOS
        if (ResumenProductos.leasing != undefined && (document.getElementById("list_l_estatus_lm") != undefined || document.getElementById("list_l_estatus_lm") != null)) {

            if (((productos.includes("1") && (App.user.attributes.id == ResumenProductos.leasing.assigned_user_id))
                && (!ResumenProductos.leasing.notificacion_noviable_c)) || App.user.attributes.bloqueo_cuentas_c == 1) {
                var faltantelm = 0;
                var selectlm = document.getElementById("list_l_estatus_lm");
                var selectlrazon = document.getElementById("list_l_so_razon");
                var selectlmotivo = document.getElementById("list_l_so_motivo");
                var motivo_flag = false;
                var detalle_flag = false;
                var validador2 = false;

                var errorLM = "";

                if (selectlm.value != "" && (selectlm.value == "4" || selectlm.value == "5")) {
                    if (selectlrazon.value == '') {
                        $('.list_l_so_razon').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                        faltantelm += 1;
                        errorLM += "Razón <br>";
                    }

                    if ($('.list_l_respval_1').select2('val') == null || $('.list_l_respval_1').select2('val') == "" || $('.list_l_respval_1').select2('val') == "0") {
                        $('.list_l_respval_1').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                        errorLM += "Responsable de Validación 1 <br>";
                        faltantelm += 1;
                    }

                    for (var i = 0; i < contexto_cuenta.datacondiciones.records.length; i++) {
                        if (contexto_cuenta.datacondiciones.records[i].condicion == selectlm.value
                            && contexto_cuenta.datacondiciones.records[i].razon == selectlrazon.value
                            && contexto_cuenta.datacondiciones.records[i].motivo != "") {
                            if (selectlmotivo.value == "") {
                                $('.list_l_so_motivo').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                                //$('.list_l_so_motivo').css('border-color', 'red'); //TXT ¿Qué producto?
                                //errorLM +="Motivo <br>";
                                motivo_flag = true;
                            }
                        }
                        if (contexto_cuenta.datacondiciones.records[i].condicion == selectlm.value && contexto_cuenta.datacondiciones.records[i].razon == selectlrazon.value && contexto_cuenta.datacondiciones.records[i].motivo == selectlmotivo.value) {
                            if (contexto_cuenta.datacondiciones.records[i].detalle == true) {
                                if ($('.txt_l_so_detalle').val().trim() == "") {
                                    $('.txt_l_so_detalle').css('border-color', 'red'); //TXT ¿Qué producto?
                                    //errorLM +="Detalle <br>";
                                    detalle_flag = true;
                                }
                            }
                        }
                        if (contexto_cuenta.datacondiciones.records[i].condicion == selectlm.value && contexto_cuenta.datacondiciones.records[i].razon == selectlrazon.value && contexto_cuenta.datacondiciones.records[i].motivo == selectlmotivo.value) {
                            if (contexto_cuenta.datacondiciones.records[i].notifica == true && ($('.list_l_respval_2').select2('val') == "" || $('.list_l_respval_2').select2('val') == "0")) {
                                $('.list_l_respval_2').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                                //errorLM +="Responsable de Validación 2 <br>";
                                validador2 = true;
                            }
                        }
                    }
                    if (motivo_flag) {
                        errorLM += "Motivo <br>";
                        faltantelm += 1;
                    }
                    if (detalle_flag) {
                        errorLM += "Detalle <br>";
                        faltantelm += 1;
                    }
                    if (validador2) {
                        errorLM += "Responsable de Validación 2 <br>";
                        faltantelm += 1;
                    }
                    if (validador2) {
                        if (($('.list_l_respval_2').select2('val') != null || $('.list_l_respval_2').select2('val') != "" || $('.list_l_respval_2').select2('val') != "0" || $('.list_l_respval_2').select2('val') == null)
                            && ($('.list_l_respval_1').select2('val') != null || $('.list_l_respval_1').select2('val') != "" || $('.list_l_respval_1').select2('val') != "0") && ($('.list_l_respval_2').select2('val') == $('.list_l_respval_1').select2('val'))) {
                            $('.list_l_respval_2').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                            $('.list_l_respval_1').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                            errorLM += "Los Responsables de Validación no pueden ser iguales para <b>No Viable Leasing </b>. <br>";
                            errors['error_leasingUP'] = errors['error_leasingUP'] || {};
                            errors['error_leasingUP'].required = true;
                            faltantelm += 1;
                        }
                    }

                    /*if ($('.chk_l_nv')[0].checked == true && selectlmotivo.value == '' && (selectlm.value =="4" || selectlm.value =="5")) {
                        $('.selectlmotivo').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                        faltantelm += 1;
                    }*/

                }
                if (faltantelm > 0) {
                    app.alert.show("Faltantes No viable - Lead Management", {
                        level: "error",
                        title: ' Para el cambio de estatus <b> ' + app.lang.getAppListStrings('status_management_list')[selectlm.value] + ' en Leasing </b> <br> Hace falta llenar los campos: <br>' + errorLM,
                        autoClose: false
                    });
                    errors['error_leasingUP'] = errors['error_leasingUP'] || {};
                    errors['error_leasingUP'].required = true;
                }
            }
        }
        /*if (faltantesleasup == 0 && $('.chk_l_nv')[0].checked == true && cont_uni_p.ResumenProductos.leasing.status_management_c != "3") {
            this.model.set('promotorleasing_c', '9 - No Viable');
            this.model.set('user_id_c', 'cc736f7a-4f5f-11e9-856a-a0481cdf89eb');
            cont_uni_p.ResumenProductos.leasing.assigned_user_id = 'cc736f7a-4f5f-11e9-856a-a0481cdf89eb'; //'9 - No Viable' en Uni_Productos
        }*/

        callback(null, fields, errors);
    },
    /***********************************VALIDACION NO VIABLE PRODUCTO FACTORAJE*******************************/
    requeridosFactorajeUP: function (fields, errors, callback) {
        var faltantesFactorajeUP = 0;
        if ($('.chk_f_nv')[0] != undefined) {
            if ($('.chk_f_nv')[0].checked && ($('.list_f_nv_razon').select2('val') == "" || $('.list_f_nv_razon').select2('val') == null || $('.list_f_nv_razon').select2('val') == "0")) {
                $('.list_f_nv_razon').find('.select2-choice').css('border-color', 'red'); //Razón de Lead no viable
                faltantesFactorajeUP += 1;
            }
            if ($('.chk_f_nv')[0].checked == true && $('.list_f_nv_razon').select2('val') == "1" &&
                ($('.list_f_nv_razon_fp').select2('val') == "" || $('.list_f_nv_razon_fp').select2('val') == null || $('.list_f_nv_razon_fp').select2('val') == "0")) {

                $('.list_f_nv_razon_fp').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                faltantesFactorajeUP += 1;
            }
            if ($('.chk_f_nv')[0].checked == true && $('.list_f_nv_razon').select2('val') == "2" &&
                ($('.list_f_nv_razon_cf').select2('val') == "" || $('.list_f_nv_razon_cf').select2('val') == null || $('.list_f_nv_razon_cf').select2('val') == "0")) {

                $('.list_f_nv_razon_cf').find('.select2-choice').css('border-color', 'red'); //Condiciones Financieras
                faltantesFactorajeUP += 1;
            }
            if ($('.chk_f_nv')[0].checked == true && $('.list_f_nv_razon').select2('val') == "7" &&
                ($('.list_f_nv_razon_ni').select2('val') == "" || $('.list_f_nv_razon_ni').select2('val') == null || $('.list_f_nv_razon_ni').select2('val') == "0")) {

                $('.list_f_nv_razon_ni').find('.select2-choice').css('border-color', 'red'); //Razón No se encuentra interesado
                faltantesFactorajeUP += 1;
            }
            if ($('.chk_f_nv')[0].checked == true && $('.list_f_nv_razon').select2('val') == "3" && $('.txt_f_nv_quien').val().trim() == "" && $('.txt_f_nv_porque').val().trim() == "") {
                $('.txt_f_nv_quien').css('border-color', 'red');  //TXT ¿Quién?
                $('.txt_f_nv_porque').css('border-color', 'red'); //TXT ¿Por qué?
                faltantesFactorajeUP += 1;
            }
            if ($('.chk_f_nv')[0].checked == true && $('.list_f_nv_razon').select2('val') == "3" && $('.txt_f_nv_quien').val().trim() == "" && $('.txt_f_nv_porque').val().trim() != "") {
                $('.txt_f_nv_quien').css('border-color', 'red'); //TXT ¿Quién?
                faltantesFactorajeUP += 1;
            }
            if ($('.chk_f_nv')[0].checked == true && $('.list_f_nv_razon').select2('val') == "3" && $('.txt_f_nv_quien').val().trim() != "" && $('.txt_f_nv_porque').val().trim() == "") {
                $('.txt_f_nv_porque').css('border-color', 'red'); //TXT ¿Por qué?
                faltantesFactorajeUP += 1;
            }
            if ($('.chk_f_nv')[0].checked == true && $('.list_f_nv_razon').select2('val') == "4" &&
                ($('.list_f_nv_producto').select2('val') == "" || $('.list_f_nv_producto').select2('val') == null || $('.list_f_nv_producto').select2('val') == "0")) {

                $('.list_f_nv_producto').find('.select2-choice').css('border-color', 'red'); //¿Qué producto?
                faltantesFactorajeUP += 1;
            }
            /*if (faltantesFactorajeUP == 0 && $('.chk_f_nv')[0].checked == true && cont_uni_p.ResumenProductos.factoring.status_management_c != "3") {
                this.model.set('promotorfactoraje_c', '9 - No Viable');
                this.model.set('user_id1_c', 'cc736f7a-4f5f-11e9-856a-a0481cdf89eb');
                cont_uni_p.ResumenProductos.factoring.assigned_user_id = 'cc736f7a-4f5f-11e9-856a-a0481cdf89eb'; //'9 - No Viable' en Uni_Productos
            }*/
            if (faltantesFactorajeUP > 0) {
                app.alert.show("Faltantes no viable Factoraje", {
                    level: "error",
                    title: 'Hace falta seleccionar alguna de las razones del catálogo <b>No Viable Factoraje.',
                    autoClose: false
                });
                errors['error_FactorajeUP'] = errors['error_FactorajeUP'] || {};
                errors['error_FactorajeUP'].required = true;
            }
        }

        var productos = App.user.attributes.productos_c; //USUARIOS CON LOS SIGUIENTES PRODUCTOS

        if ((document.getElementById("list_fac_estatus_lm") != undefined || document.getElementById("list_fac_estatus_lm") != null) &&
            ResumenProductos.factoring != undefined) {

            if (((productos.includes("4") && (App.user.attributes.id == ResumenProductos.factoring.assigned_user_id))
                && (!ResumenProductos.factoring.notificacion_noviable_c)) || App.user.attributes.bloqueo_cuentas_c == 1) {
                var faltantelm = 0;
                var selectlm = document.getElementById("list_fac_estatus_lm");
                var selectlrazon = document.getElementById("list_f_razon_lm");
                var selectlmotivo = document.getElementById("list_f_so_motivo");
                var errorLM = "";
                var motivo_flag = false;
                var detalle_flag = false;
                var validador2 = false;

                if (selectlm.value != "" && (selectlm.value == "4" || selectlm.value == "5")) {
                    if (selectlrazon.value == '') {
                        $('.list_f_razon_lm').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                        faltantelm += 1;
                        errorLM += "Razón <br>";
                    }
                    /*if ($('.chk_f_nv')[0].checked == true && selectlmotivo.value == '' && (selectlm.value =="4" || selectlm.value =="5")) {
                        $('.selectlmotivo').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                        faltantelm += 1;
                    }*/
                    if (($('.list_f_respval_1').select2('val') == null || $('.list_f_respval_1').select2('val') == "" || $('.list_f_respval_1').select2('val') == "0")) {
                        $('.list_f_respval_1').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                        faltantelm += 1;
                        errorLM += "Responsable de Validación 1 <br>";
                    }

                    for (var i = 0; i < this.datacondiciones.records.length; i++) {
                        if (contexto_cuenta.datacondiciones.records[i].condicion == selectlm.value
                            && this.datacondiciones.records[i].razon == selectlrazon.value
                            && this.datacondiciones.records[i].motivo != "") {
                            if (selectlmotivo.value == "") {
                                $('.list_f_so_motivo').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                                //$('.list_l_so_motivo').css('border-color', 'red'); //TXT ¿Qué producto?
                                motivo_flag = true;
                            }
                        }
                        if (contexto_cuenta.datacondiciones.records[i].condicion == selectlm.value
                            && this.datacondiciones.records[i].razon == selectlrazon.value
                            && this.datacondiciones.records[i].motivo == selectlmotivo.value) {
                            if (this.datacondiciones.records[i].detalle == true) {
                                if ($('.txt_f_so_detalle').val().trim() == "") {
                                    $('.txt_f_so_detalle').css('border-color', 'red'); //TXT ¿Qué producto?
                                    detalle_flag = true;
                                }
                            }
                        }
                        if (contexto_cuenta.datacondiciones.records[i].condicion == selectlm.value && contexto_cuenta.datacondiciones.records[i].razon == selectlrazon.value && contexto_cuenta.datacondiciones.records[i].motivo == selectlmotivo.value) {
                            if (contexto_cuenta.datacondiciones.records[i].notifica == true && ($('.list_f_respval_2').select2('val') == "" || $('.list_f_respval_2').select2('val') == "0")) {
                                $('.list_f_respval_2').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                                validador2 = true;
                            }
                        }
                    }
                    if (motivo_flag) {
                        errorLM += "Motivo <br>";
                        faltantelm += 1;
                    }
                    if (detalle_flag) {
                        errorLM += "Detalle <br>";
                        faltantelm += 1;
                    }
                    if (validador2) {
                        errorLM += "Responsable de Validación 2 <br>";
                        faltantelm += 1;
                    }
                    if (validador2) {
                        if (($('.list_f_respval_2').select2('val') != null || $('.list_f_respval_2').select2('val') != "" || $('.list_f_respval_2').select2('val') != "0")
                            && ($('.list_f_respval_1').select2('val') != null || $('.list_f_respval_1').select2('val') != "" || $('.list_f_respval_1').select2('val') != "0") && ($('.list_l_respval_2').select2('val') == $('.list_l_respval_1').select2('val'))) {
                            $('.list_f_respval_2').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                            $('.list_f_respval_1').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                            app.alert.show("Faltantes No viable - Lead Management", {
                                level: "error",
                                title: 'Los Responsables de Validación no pueden ser iguales para <b>No Viable Factoraje. </b>.',
                                autoClose: false
                            });
                            errors['error_FactorajeUP'] = errors['error_FactorajeUP'] || {};
                            errors['error_FactorajeUP'].required = true;
                            faltantelm += 1;
                        }
                    }
                }

                if (faltantelm > 0) {
                    app.alert.show("Faltantes No viable - Lead Management", {
                        level: "error",
                        title: 'Para el cambio de estatus <b>' + app.lang.getAppListStrings('status_management_list')[selectlm.value] + ' en Factoraje </b> <br> Hace falta llenar los campos :<br>' + errorLM,
                        autoClose: false
                    });
                    errors['error_FactorajeUP'] = errors['error_FactorajeUP'] || {};
                    errors['error_FactorajeUP'].required = true;
                }
            }
        }

        callback(null, fields, errors);
    },
    /***********************************VALIDACION NO VIABLE PRODUCTO CREDITO - AUTOMOTRIZ********************/
    requeridosCAUP: function (fields, errors, callback) {
        var faltantesCAUP = 0;
        if ($('.chk_ca_nv')[0] != undefined) {
            if ($('.chk_ca_nv')[0].checked && ($('.list_ca_nv_razon').select2('val') == "" || $('.list_ca_nv_razon').select2('val') == null || $('.list_ca_nv_razon').select2('val') == "0")) {
                $('.list_ca_nv_razon').find('.select2-choice').css('border-color', 'red'); //Razón de Lead no viable
                faltantesCAUP += 1;
            }
            if ($('.chk_ca_nv')[0].checked == true && $('.list_ca_nv_razon').select2('val') == "1" &&
                ($('.list_ca_nv_razon_fp').select2('val') == "" || $('.list_ca_nv_razon_fp').select2('val') == null || $('.list_ca_nv_razon_fp').select2('val') == "0")) {

                $('.list_ca_nv_razon_fp').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                faltantesCAUP += 1;
            }
            if ($('.chk_ca_nv')[0].checked == true && $('.list_ca_nv_razon').select2('val') == "2" &&
                ($('.list_ca_nv_razon_cf').select2('val') == "" || $('.list_ca_nv_razon_cf').select2('val') == null || $('.list_ca_nv_razon_cf').select2('val') == "0")) {

                $('.list_ca_nv_razon_cf').find('.select2-choice').css('border-color', 'red'); //Condiciones Financieras
                faltantesCAUP += 1;
            }
            if ($('.chk_ca_nv')[0].checked == true && $('.list_ca_nv_razon').select2('val') == "7" &&
                ($('.list_ca_nv_razon_ni').select2('val') == "" || $('.list_ca_nv_razon_ni').select2('val') == null || $('.list_ca_nv_razon_ni').select2('val') == "0")) {

                $('.list_ca_nv_razon_ni').find('.select2-choice').css('border-color', 'red'); //Razón No se encuentra interesado
                faltantesCAUP += 1;
            }
            if ($('.chk_ca_nv')[0].checked == true && $('.list_ca_nv_razon').select2('val') == "3" && $('.txt_ca_nv_quien').val().trim() == "" && $('.txt_ca_nv_porque').val().trim() == "") {
                $('.txt_ca_nv_quien').css('border-color', 'red');  //TXT ¿Quién?
                $('.txt_ca_nv_porque').css('border-color', 'red'); //TXT ¿Por qué?
                faltantesCAUP += 1;
            }
            if ($('.chk_ca_nv')[0].checked == true && $('.list_ca_nv_razon').select2('val') == "3" && $('.txt_ca_nv_quien').val().trim() == "" && $('.txt_ca_nv_porque').val().trim() != "") {
                $('.txt_ca_nv_quien').css('border-color', 'red'); //TXT ¿Quién?
                faltantesCAUP += 1;
            }
            if ($('.chk_ca_nv')[0].checked == true && $('.list_ca_nv_razon').select2('val') == "3" && $('.txt_ca_nv_quien').val().trim() != "" && $('.txt_ca_nv_porque').val().trim() == "") {
                $('.txt_ca_nv_porque').css('border-color', 'red'); //TXT ¿Por qué?
                faltantesCAUP += 1;
            }
            if ($('.chk_ca_nv')[0].checked == true && $('.list_ca_nv_razon').select2('val') == "4" &&
                ($('.list_ca_nv_producto').select2('val') == "" || $('.list_ca_nv_producto').select2('val') == null || $('.list_ca_nv_producto').select2('val') == "0")) {

                $('.list_ca_nv_producto').find('.select2-choice').css('border-color', 'red'); //¿Qué producto?
                faltantesCAUP += 1;
            }
            if (($('.list_ca_nv_razon').select2('val') == "4" || $('.list_ca_nv_razon option:selected').text() == "4" || $('.list_ca_nv_razon')[0].innerText.trim() == "4") &&
                ($('.list_ca_nv_producto').select2('val') == "4" || $('.list_ca_nv_producto option:selected').text() == "4" || $('.list_ca_nv_producto')[0].innerText.trim() == "4") &&
                $('.chk_ca_nv')[0].checked && $('.txt_ca_nv_otro').val().trim() == "") {

                $('.txt_ca_nv_otro').css('border-color', 'red'); //TXT ¿Qué producto?
                faltantesCAUP += 1;
            }
            if (faltantesCAUP > 0) {
                app.alert.show("Faltantes no viable Credito Auto", {
                    level: "error",
                    title: 'Hace falta seleccionar alguna de las razones del catálogo <b>No Viable Crédito Automotriz.',
                    autoClose: false
                });
                errors['error_CAUP'] = errors['error_CAUP'] || {};
                errors['error_CAUP'].required = true;
            }
        }

        /*if (faltantesCAUP == 0 && $('.chk_ca_nv')[0].checked == true && cont_uni_p.ResumenProductos.credito_auto.status_management_c != "3") {
            this.model.set('promotorcredit_c', '9 - No Viable');
            this.model.set('user_id2_c', 'cc736f7a-4f5f-11e9-856a-a0481cdf89eb');
            cont_uni_p.ResumenProductos.credito_auto.assigned_user_id = 'cc736f7a-4f5f-11e9-856a-a0481cdf89eb'; //'9 - No Viable' en Uni_Productos
        }*/
        var productos = App.user.attributes.productos_c; //USUARIOS CON LOS SIGUIENTES PRODUCTOS
        if ((document.getElementById("list_ca_estatus_lm") != undefined || document.getElementById("list_ca_estatus_lm") != null)
            && ResumenProductos.credito_auto != undefined) {

            if (((productos.includes("3") && (App.user.attributes.id == ResumenProductos.credito_auto.assigned_user_id))
                && (!ResumenProductos.credito_auto.notificacion_noviable_c)) || App.user.attributes.bloqueo_cuentas_c == 1) {
                var selectlm = document.getElementById("list_ca_estatus_lm");
                var selectlrazon = document.getElementById("list_ca_so_razon");
                var selectlmotivo = document.getElementById("list_ca_so_motivo");
                var faltantelm = 0;
                var motivo_flag = false;
                var detalle_flag = false;
                var validador2 = false;
                var errorLM = "";

                if (selectlm.value != "" && (selectlm.value == "4" || selectlm.value == "5")) {
                    if (selectlrazon.value == '') {
                        $('.list_ca_so_razon').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                        faltantelm += 1;
                        errorLM += "Razón <br>";
                    }
                    /*if ($('.chk_ca_nv')[0].checked == true && selectlmotivo.value == '' && (selectlm.value =="4" || selectlm.value =="5")) {
                        $('.selectlmotivo').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                        faltantelm += 1;
                    }*/
                    if (($('.list_ca_respval_1').select2('val') == null || $('.list_ca_respval_1').select2('val') == "" || $('.list_ca_respval_1').select2('val') == "0")) {
                        $('.list_ca_respval_1').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                        faltantelm += 1;
                        errorLM += "Responsable de Validación 1 <br>";
                    }

                    for (var i = 0; i < this.datacondiciones.records.length; i++) {
                        if (this.datacondiciones.records[i].condicion == selectlm.value
                            && this.datacondiciones.records[i].razon == selectlrazon.value
                            && this.datacondiciones.records[i].motivo != "") {
                            if (selectlmotivo.value == "") {
                                $('.list_ca_so_motivo').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                                //$('.list_l_so_motivo').css('border-color', 'red'); //TXT ¿Qué producto?
                                fmotivo_flag = true;
                            }
                        }
                        if (this.datacondiciones.records[i].condicion == selectlm.value
                            && this.datacondiciones.records[i].razon == selectlrazon.value
                            && this.datacondiciones.records[i].motivo == selectlmotivo.value) {
                            if (this.datacondiciones.records[i].detalle == true) {
                                if ($('.txt_ca_so_detalle').val().trim() == "") {
                                    $('.txt_ca_so_detalle').css('border-color', 'red'); //TXT ¿Qué producto?
                                    detalle_flag = true;
                                }
                            }
                        }
                        if (contexto_cuenta.datacondiciones.records[i].condicion == selectlm.value && contexto_cuenta.datacondiciones.records[i].razon == selectlrazon.value && contexto_cuenta.datacondiciones.records[i].motivo == selectlmotivo.value) {
                            if (contexto_cuenta.datacondiciones.records[i].notifica == true && ($('.list_ca_respval_2').select2('val') == "" || $('.list_ca_respval_2').select2('val') == "0")) {
                                $('.list_ca_respval_2').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                                validador2 = true;
                            }
                        }
                    }

                    if (motivo_flag) {
                        errorLM += "Motivo <br>";
                        faltantelm += 1;
                    }
                    if (detalle_flag) {
                        errorLM += "Detalle <br>";
                        faltantelm += 1;
                    }
                    if (validador2) {
                        errorLM += "Responsable de Validación 2 <br>";
                        faltantelm += 1;
                    }
                    if (validador2) {
                        if (($('.list_ca_respval_2').select2('val') != null || $('.list_ca_respval_2').select2('val') != "" || $('.list_ca_respval_2').select2('val') != "0")
                            && ($('.list_ca_respval_1').select2('val') != null || $('.list_ca_respval_1').select2('val') != "" || $('.list_ca_respval_1').select2('val') == "0") && ($('.list_ca_respval_2').select2('val') == $('.list_ca_respval_1').select2('val'))) {
                            $('.list_ca_respval_2').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                            $('.list_ca_respval_1').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                            app.alert.show("Faltantes No viable - Lead Management", {
                                level: "error",
                                title: 'Los Responsables de Validación no pueden ser iguales para <b>No Viable Crédito Automotriz </b>.',
                                autoClose: false
                            });
                            errors['error_CAUP'] = errors['error_CAUP'] || {};
                            errors['error_CAUP'].required = true;
                            faltantelm += 1;
                        }
                    }
                }
                if (faltantelm > 0) {
                    app.alert.show("Faltantes No viable - Lead Management", {
                        level: "error",
                        title: ' Para el cambio de estatus <b> ' + app.lang.getAppListStrings('status_management_list')[selectlm.value] + '  Crédito Automotriz.</b>  <br> Hace falta llenar los campos :<br>' + errorLM,
                        autoClose: false
                    });
                    errors['error_CAUP'] = errors['error_CAUP'] || {};
                    errors['error_CAUP'].required = true;
                }
            }
        }

        callback(null, fields, errors);
    },
    /***********************************VALIDACION NO VIABLE PRODUCTO FLEET***********************************/
    requeridosFleetUP: function (fields, errors, callback) {
        var faltantesFleetUP = 0;
        if ($('.chk_fl_nv')[0] != undefined) {
            if ($('.chk_fl_nv')[0].checked && ($('.list_fl_nv_razon').select2('val') == "" || $('.list_fl_nv_razon').select2('val') == null || $('.list_fl_nv_razon').select2('val') == "0")) {
                $('.list_fl_nv_razon').find('.select2-choice').css('border-color', 'red'); //Razón de Lead no viable
                faltantesFleetUP += 1;
            }
            if ($('.chk_fl_nv')[0].checked == true && $('.list_fl_nv_razon').select2('val') == "1" &&
                ($('.list_fl_nv_razon_fp').select2('val') == "" || $('.list_fl_nv_razon_fp').select2('val') == null || $('.list_fl_nv_razon_fp').select2('val') == "0")) {

                $('.list_fl_nv_razon_fp').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                faltantesFleetUP += 1;
            }
            if ($('.chk_fl_nv')[0].checked == true && $('.list_fl_nv_razon').select2('val') == "2" &&
                ($('.list_fl_nv_razon_cf').select2('val') == "" || $('.list_fl_nv_razon_cf').select2('val') == null || $('.list_fl_nv_razon_cf').select2('val') == "0")) {

                $('.list_fl_nv_razon_cf').find('.select2-choice').css('border-color', 'red'); //Condiciones Financieras
                faltantesFleetUP += 1;
            }
            if ($('.chk_fl_nv')[0].checked == true && $('.list_fl_nv_razon').select2('val') == "7" &&
                ($('.list_fl_nv_razon_ni').select2('val') == "" || $('.list_fl_nv_razon_ni').select2('val') == null || $('.list_fl_nv_razon_ni').select2('val') == "0")) {

                $('.list_fl_nv_razon_ni').find('.select2-choice').css('border-color', 'red'); //Razón No se encuentra interesado
                faltantesFleetUP += 1;
            }
            if ($('.chk_fl_nv')[0].checked == true && $('.list_fl_nv_razon').select2('val') == "3" && $('.txt_fl_nv_quien').val().trim() == "" && $('.txt_fl_nv_porque').val().trim() == "") {
                $('.txt_fl_nv_quien').css('border-color', 'red');  //TXT ¿Quién?
                $('.txt_fl_nv_porque').css('border-color', 'red'); //TXT ¿Por qué?
                faltantesFleetUP += 1;
            }
            if ($('.chk_fl_nv')[0].checked == true && $('.list_fl_nv_razon').select2('val') == "3" && $('.txt_fl_nv_quien').val().trim() == "" && $('.txt_fl_nv_porque').val().trim() != "") {
                $('.txt_fl_nv_quien').css('border-color', 'red'); //TXT ¿Quién?
                faltantesFleetUP += 1;
            }
            if ($('.chk_fl_nv')[0].checked == true && $('.list_fl_nv_razon').select2('val') == "3" && $('.txt_fl_nv_quien').val().trim() != "" && $('.txt_fl_nv_porque').val().trim() == "") {
                $('.txt_fl_nv_porque').css('border-color', 'red'); //TXT ¿Por qué?
                faltantesFleetUP += 1;
            }
            if ($('.chk_fl_nv')[0].checked == true && $('.list_fl_nv_razon').select2('val') == "4" &&
                ($('.list_fl_nv_producto').select2('val') == "" || $('.list_fl_nv_producto').select2('val') == null || $('.list_fl_nv_producto').select2('val') == "0")) {

                $('.list_fl_nv_producto').find('.select2-choice').css('border-color', 'red'); //¿Qué producto?
                faltantesFleetUP += 1;
            }
            if (($('.list_fl_nv_razon').select2('val') == "4" || $('.list_fl_nv_razon option:selected').text() == "4") &&
                ($('.list_fl_nv_producto').select2('val') == "4" || $('.list_fl_nv_producto option:selected').text() == "4" || $('.list_fl_nv_producto')[0].innerText.trim() == "4") &&
                $('.chk_fl_nv')[0].checked && $('.txt_fl_nv_otro').val().trim() == "") {

                $('.txt_fl_nv_otro').css('border-color', 'red'); //TXT ¿Qué producto?
                faltantesFleetUP += 1;
            }
            if (faltantesFleetUP > 0) {
                app.alert.show("Faltantes no viable Fleet", {
                    level: "error",
                    title: 'Hace falta seleccionar alguna de las razones del catálogo <b>No Viable Fleet.',
                    autoClose: false
                });
                errors['error_FLeetUP'] = errors['error_FLeetUP'] || {};
                errors['error_FLeetUP'].required = true;
            }
        }

        /*if (faltantesFleetUP == 0 && $('.chk_fl_nv')[0].checked == true && cont_uni_p.ResumenProductos.fleet.status_management_c != "3") {
            this.model.set('promotorfleet_c', '9 - No Viable');
            this.model.set('user_id6_c', 'cc736f7a-4f5f-11e9-856a-a0481cdf89eb');
            cont_uni_p.ResumenProductos.fleet.assigned_user_id = 'cc736f7a-4f5f-11e9-856a-a0481cdf89eb'; //'9 - No Viable' en Uni_Productos
        }*/
        var productos = App.user.attributes.productos_c; //USUARIOS CON LOS SIGUIENTES PRODUCTOS
        if ((document.getElementById("list_fl_estatus_lm") != undefined || document.getElementById("list_fl_estatus_lm") != null)
            && ResumenProductos.fleet != undefined) {

            if (((productos.includes("6") && (App.user.attributes.id == ResumenProductos.fleet.assigned_user_id))
                && (!ResumenProductos.fleet.notificacion_noviable_c)) || App.user.attributes.bloqueo_cuentas_c == 1) {

                var faltantelm = 0;
                var selectlm = document.getElementById("list_fl_estatus_lm");
                var selectlrazon = document.getElementById("list_fl_so_razon");
                var selectlmotivo = document.getElementById("list_fl_so_motivo");
                var motivo_flag = false;
                var detalle_flag = false;
                var validador2 = false;
                var errorLM = "";

                if (selectlm.value != "" && (selectlm.value == "4" || selectlm.value == "5")) {

                    if (selectlrazon.value == '') {
                        $('.list_fl_so_razon').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                        faltantelm += 1;
                        errorLM += "Razón <br>";
                    }
                    /*if ($('.chk_fl_nv')[0].checked == true && selectlmotivo.value == '' && (selectlm.value =="4" || selectlm.value =="5")) {
                        $('.selectlmotivo').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                        faltantelm += 1;
                    }*/
                    if (($('.list_fl_respval_1').select2('val') == null || $('.list_fl_respval_1').select2('val') == "" || $('.list_fl_respval_1').select2('val') == "0")) {
                        $('.list_fl_respval_1').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                        faltantelm += 1;
                        errorLM += "Responsable de Validación 1 <br>";
                    }

                    for (var i = 0; i < this.datacondiciones.records.length; i++) {
                        if (this.datacondiciones.records[i].condicion == selectlm.value
                            && this.datacondiciones.records[i].razon == selectlrazon.value
                            && this.datacondiciones.records[i].motivo != "") {
                            if (selectlmotivo.value == "") {
                                $('.list_fl_so_motivo').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                                //$('.list_l_so_motivo').css('border-color', 'red'); //TXT ¿Qué producto?
                                motivo_flag = true;
                            }
                        }
                        if (this.datacondiciones.records[i].condicion == selectlm.value
                            && this.datacondiciones.records[i].razon == selectlrazon.value
                            && this.datacondiciones.records[i].motivo == selectlmotivo.value) {
                            if (this.datacondiciones.records[i].detalle == true) {
                                if ($('.txt_fl_so_detalle').val().trim() == "") {
                                    $('.txt_fl_so_detalle').css('border-color', 'red'); //TXT ¿Qué producto?
                                    detalle_flag = true;
                                }
                            }
                        }
                        if (contexto_cuenta.datacondiciones.records[i].condicion == selectlm.value && contexto_cuenta.datacondiciones.records[i].razon == selectlrazon.value && contexto_cuenta.datacondiciones.records[i].motivo == selectlmotivo.value) {
                            if (contexto_cuenta.datacondiciones.records[i].notifica == true && ($('.list_fl_respval_2').select2('val') == "" || $('.list_fl_respval_2').select2('val') == "0")) {
                                $('.list_fl_respval_2').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                                validador2 = true;
                            }
                        }
                    }
                    if (motivo_flag) {
                        errorLM += "Motivo <br>";
                        faltantelm += 1;
                    }
                    if (detalle_flag) {
                        errorLM += "Detalle <br>";
                        faltantelm += 1;
                    }
                    if (validador2) {
                        errorLM += "Responsable de Validación 2 <br>";
                        faltantelm += 1;
                    }
                    if (validador2) {
                        if (($('.list_fl_respval_2').select2('val') != null || $('.list_fl_respval_2').select2('val') != "" || $('.list_fl_respval_2').select2('val') != "0")
                            && ($('.list_fl_respval_1').select2('val') != null || $('.list_fl_respval_1').select2('val') != "" || $('.list_fl_respval_1').select2('val') != "0") && ($('.list_fl_respval_2').select2('val') == $('.list_fl_respval_1').select2('val'))) {
                            $('.list_l_respval_2').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                            $('.list_fl_respval_1').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                            app.alert.show("Faltantes No viable - Lead Management", {
                                level: "error",
                                title: 'Los Responsables de Validación no pueden ser iguales para <b>No Viable Fleet </b>.',
                                autoClose: false
                            });
                            errors['error_FLeetUP'] = errors['error_FLeetUP'] || {};
                            errors['error_FLeetUP'].required = true;
                            faltantelm += 1;
                        }
                    }
                }
                if (faltantelm > 0) {
                    app.alert.show("Faltantes No viable - Lead Management", {
                        level: "error",
                        title: ' Para el cambio de estatus <b> ' + app.lang.getAppListStrings('status_management_list')[selectlm.value]
                            + ' en Fleet.</b> <br> Hace falta llenar los campos :<br>' + errorLM,
                        autoClose: false
                    });
                    errors['error_FLeetUP'] = errors['error_FLeetUP'] || {};
                    errors['error_FLeetUP'].required = true;
                }

            }
        }

        callback(null, fields, errors);
    },
    /***********************************VALIDACION NO VIABLE PRODUCTO UNICLICK********************************/
    requeridosUniclickUP: function (fields, errors, callback) {
        var faltantesUniclickUP = 0;
        if ($('.chk_u_nv')[0] != undefined) {
            if ($('.chk_u_nv')[0].checked && ($('.list_u_nv_razon').select2('val') == "" || $('.list_u_nv_razon').select2('val') == null || $('.list_u_nv_razon').select2('val') == "0")) {
                $('.list_u_nv_razon').find('.select2-choice').css('border-color', 'red'); //Razón de Lead no viable
                faltantesUniclickUP += 1;
            }
            if ($('.chk_u_nv')[0].checked == true && $('.list_u_nv_razon').select2('val') == "1" &&
                ($('.list_u_nv_razon_fp').select2('val') == "" || $('.list_u_nv_razon_fp').select2('val') == null || $('.list_u_nv_razon_fp').select2('val') == "0")) {

                $('.list_u_nv_razon_fp').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                faltantesUniclickUP += 1;
            }
            if ($('.chk_u_nv')[0].checked == true && $('.list_u_nv_razon').select2('val') == "2" &&
                ($('.list_u_nv_razon_cf').select2('val') == "" || $('.list_u_nv_razon_cf').select2('val') == null || $('.list_u_nv_razon_cf').select2('val') == "0")) {

                $('.list_u_nv_razon_cf').find('.select2-choice').css('border-color', 'red'); //Condiciones Financieras
                faltantesUniclickUP += 1;
            }
            if ($('.chk_u_nv')[0].checked == true && $('.list_u_nv_razon').select2('val') == "7" &&
                ($('.list_u_nv_razon_ni').select2('val') == "" || $('.list_u_nv_razon_ni').select2('val') == null || $('.list_u_nv_razon_ni').select2('val') == "0")) {

                $('.list_u_nv_razon_ni').find('.select2-choice').css('border-color', 'red'); //Razón No se encuentra interesado
                faltantesUniclickUP += 1;
            }
            if ($('.chk_u_nv')[0].checked == true && $('.list_u_nv_razon').select2('val') == "3" && $('.txt_u_nv_quien').val().trim() == "" && $('.txt_u_nv_porque').val().trim() == "") {
                $('.txt_u_nv_quien').css('border-color', 'red');  //TXT ¿Quién?
                $('.txt_u_nv_porque').css('border-color', 'red'); //TXT ¿Por qué?
                faltantesUniclickUP += 1;
            }
            if ($('.chk_u_nv')[0].checked == true && $('.list_u_nv_razon').select2('val') == "3" && $('.txt_u_nv_quien').val().trim() == "" && $('.txt_u_nv_porque').val().trim() != "") {
                $('.txt_u_nv_quien').css('border-color', 'red'); //TXT ¿Quién?
                faltantesUniclickUP += 1;
            }
            if ($('.chk_u_nv')[0].checked == true && $('.list_u_nv_razon').select2('val') == "3" && $('.txt_u_nv_quien').val().trim() != "" && $('.txt_u_nv_porque').val().trim() == "") {
                $('.txt_u_nv_porque').css('border-color', 'red'); //TXT ¿Por qué?
                faltantesUniclickUP += 1;
            }
            if ($('.chk_u_nv')[0].checked == true && $('.list_u_nv_razon').select2('val') == "4" &&
                ($('.list_u_nv_producto').select2('val') == "" || $('.list_u_nv_producto').select2('val') == null || $('.list_u_nv_producto').select2('val') == "0")) {

                $('.list_u_nv_producto').find('.select2-choice').css('border-color', 'red'); //¿Qué producto?
                faltantesUniclickUP += 1;
            }
            if (($('.list_u_nv_razon').select2('val') == "4" || $('.list_u_nv_razon option:selected').text() == "4" || $('.list_u_nv_razon')[0].innerText.trim() == "4") &&
                ($('.list_u_nv_producto').select2('val') == "4" || $('.list_u_nv_producto option:selected').text() == "4" || $('.list_u_nv_producto')[0].innerText.trim() == "4") &&
                $('.chk_u_nv')[0].checked && $('.txt_u_nv_otro').val().trim() == "") {

                $('.txt_u_nv_otro').css('border-color', 'red'); //TXT ¿Qué producto?
                faltantesUniclickUP += 1;
            }
            if (faltantesUniclickUP > 0) {
                app.alert.show("Faltantes no viable Uniclick", {
                    level: "error",
                    title: 'Hace falta seleccionar alguna de las razones del catálogo <b>No Viable Uniclick.',
                    autoClose: false
                });
                errors['error_UniclickUP'] = errors['error_UniclickUP'] || {};
                errors['error_UniclickUP'].required = true;
            }
        }

        /*if (faltantesUniclickUP == 0 && $('.chk_u_nv')[0].checked == true && cont_uni_p.ResumenProductos.uniclick.status_management_c != "3") {
            this.model.set('promotoruniclick_c', '9 - Sin Gestor');
            this.model.set('user_id7_c', 'cc736f7a-4f5f-11e9-856a-a0481cdf89eb');
            cont_uni_p.ResumenProductos.uniclick.assigned_user_id = 'cc736f7a-4f5f-11e9-856a-a0481cdf89eb'; //'9 - No Viable' en Uni_Productos
        }*/
        var productos = App.user.attributes.productos_c; //USUARIOS CON LOS SIGUIENTES PRODUCTOS

        if ((document.getElementById("list_u_estatus_lm") != undefined || document.getElementById("list_u_estatus_lm") != null)
            && ResumenProductos.uniclick != undefined) {

            if (((productos.includes("8") && (App.user.attributes.id == ResumenProductos.uniclick.assigned_user_id))
                && (!ResumenProductos.uniclick.notificacion_noviable_c)) || App.user.attributes.bloqueo_cuentas_c == 1) {
                var faltantelm = 0;
                var selectlm = document.getElementById("list_u_estatus_lm");
                var selectlrazon = document.getElementById("list_u_so_razon");
                var selectlmotivo = document.getElementById("list_u_so_motivo");
                var motivo_flag = false;
                var detalle_flag = false;
                var validador2 = false;
                var errorLM = "";

                if (selectlm.value != "" && (selectlm.value == "4" || selectlm.value == "5")) {
                    if (selectlrazon.value == '') {
                        $('.list_u_so_razon').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                        faltantelm += 1;
                        errorLM += "Razón <br>";
                    }
                    /*if ($('.chk_u_nv')[0].checked == true && selectlmotivo.value == '' && (selectlm.value =="4" || selectlm.value =="5")) {
                        $('.selectlmotivo').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                        faltantelm += 1;
                    }*/
                    if (($('.list_u_respval_1').select2('val') == null || $('.list_u_respval_1').select2('val') == "" || $('.list_u_respval_1').select2('val') == "0")) {
                        $('.list_u_respval_1').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                        faltantelm += 1;
                        errorLM += "Responsable de Validación 1 <br>";
                    }

                    for (var i = 0; i < this.datacondiciones.records.length; i++) {
                        if (this.datacondiciones.records[i].condicion == selectlm.value
                            && this.datacondiciones.records[i].razon == selectlrazon.value
                            && this.datacondiciones.records[i].motivo != "") {
                            if (selectlmotivo == "") {
                                $('.list_u_so_motivo').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                                //$('.list_l_so_motivo').css('border-color', 'red'); //TXT ¿Qué producto?
                                motivo_flag = true;
                            }
                        }
                        if (this.datacondiciones.records[i].condicion == selectlm.value
                            && this.datacondiciones.records[i].razon == selectlrazon.value
                            && this.datacondiciones.records[i].motivo == selectlmotivo.value) {
                            if (this.datacondiciones.records[i].detalle == true) {
                                if ($('.txt_u_so_detalle').val().trim() == "") {
                                    $('.txt_u_so_detalle').css('border-color', 'red'); //TXT ¿Qué producto?
                                    detalle_flag = true;
                                }
                            }
                        }

                        if (contexto_cuenta.datacondiciones.records[i].condicion == selectlm.value && contexto_cuenta.datacondiciones.records[i].razon == selectlrazon.value && contexto_cuenta.datacondiciones.records[i].motivo == selectlmotivo.value) {
                            if (contexto_cuenta.datacondiciones.records[i].notifica == true && ($('.list_l_respval_2').select2('val') == "" || $('.list_l_respval_2').select2('val') == "0")) {
                                $('.list_u_respval_2').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                                validador2 = true;
                            }
                        }
                    }
                    if (motivo_flag) {
                        errorLM += "Motivo <br>";
                        faltantelm += 1;
                    }
                    if (detalle_flag) {
                        errorLM += "Detalle <br>";
                        faltantelm += 1;
                    }
                    if (validador2) {
                        errorLM += "Responsable de Validación 2 <br>";
                        faltantelm += 1;
                    }
                    if (validador2) {
                        if (($('.list_u_respval_2').select2('val') != null || $('.list_u_respval_2').select2('val') != "" || $('.list_u_respval_2').select2('val') != "0")
                            && ($('.list_u_respval_1').select2('val') != null || $('.list_u_respval_1').select2('val') != "" || $('.list_u_respval_1').select2('val') != "0") && ($('.list_u_respval_2').select2('val') == $('.list_u_respval_1').select2('val'))) {
                            $('.list_u_respval_2').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                            $('.list_u_respval_1').find('.select2-choice').css('border-color', 'red'); //Fuera de Perfil (Razón)
                            app.alert.show("Faltantes No viable - Lead Management", {
                                level: "error",
                                title: 'Los Responsables de Validación no pueden ser iguales para <b>Uniclick </b>.',
                                autoClose: false
                            });
                            errors['error_UniclickUP'] = errors['error_UniclickUP'] || {};
                            errors['error_UniclickUP'].required = true;
                            faltantelm += 1;
                        }
                    }
                    if (faltantelm > 0) {
                        app.alert.show("Faltantes No viable - Lead Management", {
                            level: "error",
                            title: 'Para el cambio de estatus <b> ' + app.lang.getAppListStrings('status_management_list')[selectlm.value] + ' en Uniclick</b> <br> Hace falta llenar los campos :<br>' + errorLM,
                            autoClose: false
                        });
                        errors['error_UniclickUP'] = errors['error_UniclickUP'] || {};
                        errors['error_UniclickUP'].required = true;
                    }

                }
            }
        }

        callback(null, fields, errors);
    },

    /*************Valida campo de Página Web*****************/
    validaPagWeb: function (fields, errors, callback) {
        var webSite = this.model.get('website');
        if (webSite != "") {

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
        var roles = App.user.attributes.roles;
        var roles_seguros = App.lang.getAppListStrings('roles_edicion_ctas_seguros_list');

        var seguros = 0;
        for (const [key, value] of Object.entries(roles_seguros)) {
            if (roles.includes(value)) {
                seguros = 1;
            }
        }
        if (!seguros) {
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

    ocultaGeneraRFC: function () {
        //Oculta Botón Generar RFC
        var roles = App.user.attributes.roles;
        var roles_seguros = App.lang.getAppListStrings('roles_edicion_ctas_seguros_list');

        var seguros = 0;
        for (const [key, value] of Object.entries(roles_seguros)) {
            if (roles.includes(value)) {
                seguros = 1;
            }
        }
        var accesoFiscal = App.user.attributes.tct_alta_clientes_chk_c + App.user.attributes.tct_altaproveedor_chk_c + App.user.attributes.tct_alta_cd_chk_c + App.user.attributes.deudor_factoraje_c + seguros;
        if (accesoFiscal == 0 && this.model.get('tipo_registro_cuenta_c') != '4') {
            this.$('div[data-name=rfc_c]').css("pointer-events", "none");
            this.$('div[data-name="generar_rfc_c"]').hide();
        }
    },

    tipoProveedor: function (fields, errors, callback) {
        if ((this.model.get('esproveedor_c') || this.model.get('tipo_registro_cuenta_c') == '5') && (App.user.attributes.puestousuario_c == 32 || App.user.attributes.puestousuario_c == 47) && (this.model.get('tipo_proveedor_compras_c') == null || this.model.get('tipo_proveedor_compras_c') == '')) {
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

    /* Valida RFC con servicio de revisión del padron de contribuyentes */
    //        this.model.on('change:tipodepersona_c', this._ActualizaEtiquetas, this);

    //RFC_ValidatePadron: function (fields, errors, callback) {
    //
    //	var rfc = this.getField('rfc_c');
    //	var valuerfc = this.model.get('rfc_c');
    //	var anticrfc = this._get_rfc_antiguo();
    //
    //	if( (this.model.get('pais_nacimiento_c') == "2")
    //		&& (!_.isEmpty(valuerfc) && valuerfc != "" && valuerfc != "undefined")
    //		&& (anticrfc != valuerfc) && (rfc.action === "edit" || rfc.action === "create")
    //		&& ( this.model.get('estado_rfc_c') == null || this.model.get('estado_rfc_c') == "" || this.model.get('estado_rfc_c') == "0")){
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

    validaRFC: function (fields, errors, callback) {
        if (this.model.get('tipodepersona_c') != "" && this.model.get('tipodepersona_c') != "Persona Moral") {
            if (this.model.get('rfc_c') != "" && this.model.get('rfc_c') != 'XXXX010101XXX' && this.model.get('fechadenacimiento_c') != "") {
                //Obtiene valor de la fecha y resconstruye
                var fecha = this.model.get('fechadenacimiento_c');
                var convert = fecha.split('-');
                var ano = convert[0];
                ano = ano.substring(2);
                var mes = convert[1];
                var dia = convert[2];
                var complete = "";
                complete = complete.concat(ano, mes, dia);
                //ValidacionRFC
                var rfc = this.model.get('rfc_c');
                rfc = rfc.substring(4, 10);

                if (rfc != complete) {
                    app.alert.show("Error_validacion_RFC", {
                        level: "error",
                        messages: 'La fecha no coincide con el RFC favor de corregir',
                        autoClose: false
                    });
                    errors['Error_validacion_RFC'] = errors['Error_validacion_RFC'] || {};
                    errors['Error_validacion_RFC'].required = true;
                }
            }
        } else {
            if (this.model.get('rfc_c') != "" && this.model.get('rfc_c') != 'XXX010101XXX' && this.model.get('fechaconstitutiva_c') != "") {
                //Obtiene valor de la fecha y resconstruye
                var fecha = this.model.get('fechaconstitutiva_c');
                var convert = fecha.split('-');
                var ano = convert[0];
                ano = ano.substring(2);
                var mes = convert[1];
                var dia = convert[2];
                var complete = "";
                complete = complete.concat(ano, mes, dia);
                //ValidacionRFC
                var rfc = this.model.get('rfc_c');
                rfc = rfc.substring(3, 9);

                if (rfc != complete) {
                    app.alert.show("Error_validacion_RFC_Moral", {
                        level: "error",
                        messages: 'La fecha no coincide con el RFC favor de corregir',
                        autoClose: false
                    });
                    errors['Error_validacion_RFC_Moral'] = errors['Error_validacion_RFC_Moral'] || {};
                    errors['Error_validacion_RFC_Moral'].required = true;
                }
            }
        }
        callback(null, fields, errors);
    },

    bloquea_cuenta: function () {
        var consulta = app.api.buildURL('tct02_Resumen/' + this.model.get('id'), null, null);

        app.alert.show('loadingBloqueo', {
            level: 'process',
            title: 'Cargando',
        });

        app.api.call('read', consulta, {}, {
            success: _.bind(function (data) {
                if ((this.model.get('tct_no_contactar_chk_c')) || data.bloqueo_credito_c || data.bloqueo_cumple_c) {
                    var params = {};
                    if (this.model.get('tct_no_contactar_chk_c')) {
                        params["bloqueo_cartera_c"] = 1;
                        params["user_id1_c"] = App.user.id;
                    }
                    if (data.bloqueo_credito_c) {
                        params["bloqueo2_c"] = 1;
                        params["user_id3_c"] = App.user.id;
                    }
                    if (data.bloqueo_cumple_c) {
                        params["bloqueo3_c"] = 1;
                        params["user_id5_c"] = App.user.id;
                    }

                    var actualiza = app.api.buildURL('tct02_Resumen/' + this.model.get('id'), null, null);
                    app.api.call('update', actualiza, params, {
                        success: _.bind(function (data) {
                            app.alert.dismiss('loadingBloqueo');
                            app.alert.show('alert_change_success', {
                                level: 'success',
                                messages: 'Cuenta Bloqueada',
                            });
                            $('[name="bloquea_cuenta"]').hide();
                            location.reload();
                        }, this)
                    });
                }
                app.alert.dismiss('loadingBloqueo');
            }, this)
        });
    },

    desbloquea_cuenta: function () {

        app.alert.show('loadingDesbloqueo', {
            level: 'process',
            title: 'Cargando',
        });
        var consulta = app.api.buildURL('tct02_Resumen/' + this.model.get('id'), null, null);
        app.api.call('read', consulta, {}, {
            success: _.bind(function (data) {
                app.alert.dismiss('loadingDesbloqueo');
                if ((this.model.get('tct_no_contactar_chk_c')) || data.bloqueo_credito_c || data.bloqueo_cumple_c || data.bloqueo2_c || data.bloqueo3_c || data.bloqueo_cartera_c) {
                    var params = {};
                    var actualiza = app.api.buildURL('tct02_Resumen/' + this.model.get('id'), null, null);

                    if (data.bloqueo_cartera_c) params["bloqueo_cartera_c"] = 0;
                    if (data.bloqueo2_c) params["bloqueo2_c"] = 0;
                    if (data.bloqueo3_c) params["bloqueo3_c"] = 0;

                    if (this.model.get('tct_no_contactar_chk_c') || data.bloqueo_cartera_c) {
                        this.model.set("tct_no_contactar_chk_c", false);
                        this.model.save();
                        params["condicion_cliente_c"] = "";
                        params["razon_c"] = "";
                        params["motivo_c"] = "";
                        params["detalle_c"] = "";
                        params["user_id_c"] = "";
                        params["user_id1_c"] = "";
                    }
                    if (data.bloqueo_credito_c || data.bloqueo2_c) {
                        params["bloqueo_credito_c"] = 0;
                        params["condicion2_c"] = "";
                        params["razon2_c"] = "";
                        params["motivo2_c"] = "";
                        params["detalle2_c"] = "";
                        params["user_id2_c"] = "";
                        params["user_id3_c"] = "";
                    }
                    if (data.bloqueo_cumple_c || data.bloqueo3_c) {
                        params["bloqueo_cumple_c"] = 0;
                        params["condicion3_c"] = "";
                        params["razon3_c"] = "";
                        params["motivo3_c"] = "";
                        params["detalle3_c"] = "";
                        params["user_id4_c"] = "";
                        params["user_id5_c"] = "";
                    }
                    //Consulta Grupo Empresarial
                    app.api.call("read", app.api.buildURL("Accounts/" + this.model.get('id') + "/link/members", null, null, {}), null, {
                        success: _.bind(function (data1) {
                            if (data1.records.length > 0) {
                                app.alert.show('errorAlert2', {
                                    level: 'confirmation',
                                    messages: "¿Desea desbloquear todas las cuentas del grupo empresarial?",
                                    autoClose: false,
                                    onCancel: function () {
                                        app.api.call('update', actualiza, params, {
                                            success: _.bind(function (data2) {
                                                app.alert.show('alert_change_success', {
                                                    level: 'success',
                                                    messages: 'Cuenta Desbloqueada',
                                                });
                                                $('[name="bloquea_cuenta"]').hide();
                                                $('[name="desbloquea_cuenta"]').hide();
                                                location.reload();
                                            }, this)
                                        });
                                    },
                                    onConfirm: function () {
                                        if (data.grupo_c) {
                                            params["grupo_c"] = 0;
                                        } else {
                                            params["grupo_c"] = 1;
                                        }
                                        app.api.call('update', actualiza, params, {
                                            success: _.bind(function (data2) {
                                                app.alert.show('alert_change_success', {
                                                    level: 'success',
                                                    messages: 'Cuenta Desbloqueada',
                                                });
                                                $('[name="bloquea_cuenta"]').hide();
                                                $('[name="desbloquea_cuenta"]').hide();
                                                location.reload();
                                            }, this)
                                        });
                                    },
                                });
                            } else {
                                app.api.call('update', actualiza, params, {
                                    success: _.bind(function (data2) {
                                        app.alert.show('alert_change_success', {
                                            level: 'success',
                                            messages: 'Cuenta Desbloqueada',
                                        });
                                        $('[name="bloquea_cuenta"]').hide();
                                        $('[name="desbloquea_cuenta"]').hide();
                                        location.reload();
                                    }, this)
                                });
                            }
                        }, this)
                    });
                }
            }, this)
        });
    },

    aprobar_noviable: function () {
        var Productos = [];

        app.api.call('GET', app.api.buildURL('GetProductosCuentas/' + this.model.get('id')), null, {
            success: function (data) {
                Productos = data;
                apruebaGeneral2 = false;
                apruebaGeneral1 = false;
                _.each(Productos, function (value, key) {
                    if (Productos[key].no_viable && (Productos[key].user_id1_c == app.user.id || Productos[key].user_id2_c == app.user.id)) {
                        var params = {};
                        var strUrl = 'tct4_Condiciones?filter[][condicion]=' + Productos[key].status_management_c + '&filter[][razon]=' + Productos[key].razon_c;
                        app.api.call("GET", app.api.buildURL(strUrl), null, {
                            success: _.bind(function (data1) {
                                if (data1.records.length > 0) {
                                    var bloqueo = false;
                                    var razon = "";
                                    var motivo = "";
                                    //var apruebaGeneral2 = false;
                                    //var apruebaGeneral1 = false;

                                    _.each(data1.records, function (valor, llave) {
                                        razon = Productos[key].razon_c;
                                        motivo = (Productos[key].motivo_c == null) ? "" : Productos[key].motivo_c;
                                        aprueba2 = (Productos[key].aprueba2_c == "0") ? false : true;
                                        aprueba1 = (Productos[key].aprueba1_c == "0") ? false : true;
                                        reactivacion = (Productos[key].reactivacion_c == "0") ? false : true;

                                        if (!reactivacion) {
                                            if (razon != "" && motivo == "") {
                                                if (data1.records[llave].razon == razon && data1.records[llave].bloquea) {

                                                    if (app.user.id == Productos[key].user_id1_c) {
                                                        params["aprueba1_c"] = 1;
                                                        apruebaGeneral1 = true;
                                                        if (aprueba2) {
                                                            bloqueo = true;
                                                        }
                                                    }
                                                    if (app.user.id == Productos[key].user_id2_c) {
                                                        params["aprueba2_c"] = 1;
                                                        apruebaGeneral2 = true;
                                                        if (aprueba1) {
                                                            bloqueo = true;
                                                        }
                                                    }
                                                }
                                            }
                                            if (razon != "" && motivo != "") {
                                                if ((data1.records[llave].razon == razon) && (data1.records[llave].motivo == motivo)
                                                    && data1.records[llave].bloquea) {
                                                    //bloqueo = true;
                                                    if (app.user.id == Productos[key].user_id1_c) {
                                                        params["aprueba1_c"] = 1;
                                                        apruebaGeneral1 = true;
                                                        if (aprueba2) {
                                                            bloqueo = true;
                                                        }
                                                    }
                                                    if (app.user.id == Productos[key].user_id2_c) {
                                                        params["aprueba2_c"] = 1;
                                                        apruebaGeneral2 = true;
                                                        if (aprueba1) {
                                                            bloqueo = true;
                                                        }
                                                    }
                                                }
                                            }
                                        } else {
                                            if ((data1.records[llave].razon == razon) && (data1.records[llave].motivo == motivo)
                                                && data1.records[llave].bloquea) {
                                                bloqueo = true;
                                                params["aprueba1_c"] = false;
                                                params["aprueba1_c"] = false;

                                            }
                                        }
                                    });
                                    if (bloqueo) {

                                        params["status_management_c"] = Productos[key].status_management_c;
                                        params["razon_c"] = Productos[key].razon_c;
                                        params["motivo_c"] = Productos[key].motivo_c;
                                        params["detalle_c"] = Productos[key].detalle_c;
                                        params["user_id_c"] = Productos[key].user_id_c;
                                        params["user_id1_c"] = Productos[key].user_id1_c;
                                        params["user_id2_c"] = Productos[key].user_id2_c;
                                        params["aprueba1_c"] = 1;
                                        params["aprueba2_c"] = 1;
                                        params["estatus_atencion"] = '3';
                                        params["reactivacion_c"] = false;
                                        params["tipoupdate"] = '2';
                                        params["notificacion_noviable_c"] = 1;
                                        params["user_id"] = app.user.id;

                                        /*_.each(Productos, function (value1, key1) {
                                            var actualiza = app.api.buildURL('uni_Productos/' + Productos[key1].id, null, null);
                                            app.api.call('update', actualiza, params, {
                                                success: _.bind(function (data2) {
                                                }, this)
                                            });
                                        });*/
                                        _.each(Productos, function (value1, key1) {
                                            params["id_Producto"] = Productos[key1].id;
                                            var uni = app.api.buildURL('actualizaProductosPermisos', null, null, params);
                                            var resp;
                                            app.api.call('create', uni, null, {
                                                success: function (data) {
                                                    /*app.alert.show('Rechazar No viable cuenta', {
                                                        level: 'warning',
                                                        messages: 'Se aprobó el No Viable, para la cuenta',
                                                    });*/
                                                },
                                                error: function (e) {
                                                    throw e;
                                                }
                                            });
                                        });
                                        location.reload();
                                        //cont_uni_p.render();

                                    } else {
                                        if (apruebaGeneral2 || apruebaGeneral1) {
                                            params["aprueba1_c"] = (apruebaGeneral1) ? 1 : 0;
                                            params["aprueba2_c"] = (apruebaGeneral2) ? 1 : 0;
                                            params["estatus_atencion"] = '1';
                                        } else {
                                            params["aprueba1_c"] = 1;
                                            params["aprueba2_c"] = 1;
                                            params["estatus_atencion"] = '3';
                                        }
                                        params["user_id"] = app.user.id;
                                        //if(Productos[key].user_id1_c == app.user.id) params["aprueba1_c"] = 1;
                                        //if(Productos[key].user_id2_c == app.user.id) params["aprueba2_c"] = 1;
                                        params["id_Producto"] = Productos[key].id;
                                        params["tipoupdate"] = '2';
                                        params["reactivacion_c"] = 0;
                                        params["status_management_c"] = Productos[key].status_management_c;
                                        params["razon_c"] = Productos[key].razon_c;
                                        params["motivo_c"] = Productos[key].motivo_c;
                                        params["detalle_c"] = Productos[key].detalle_c;
                                        params["user_id_c"] = Productos[key].user_id_c;
                                        params["user_id1_c"] = Productos[key].user_id1_c;
                                        params["user_id2_c"] = Productos[key].user_id2_c;
                                        params["notificacion_noviable_c"] = 1;

                                        //var actualiza = app.api.buildURL('actualizaProductosPermisos/' + Productos[key].id, null, null);
                                        var uni = app.api.buildURL('actualizaProductosPermisos', null, null, params);
                                        var resp;
                                        app.api.call('create', uni, null, {
                                            success: function (data) {
                                                /*app.alert.show('Rechazar No viable cuenta', {
                                                    level: 'warning',
                                                    messages: 'Se aprobó el No Viable, para la cuenta',
                                                });*/
                                                location.reload();
                                                //cont_uni_p.render();
                                            },
                                            error: function (e) {
                                                throw e;
                                            }
                                        });

                                        /*var actualiza = app.api.buildURL('uni_Productos/' + Productos[key].id, null, null);
                                        app.api.call('update', actualiza, params, {
                                            success: _.bind(function (data2) {
                                                app.alert.show('alert_change_success', {
                                                    level: 'success',
                                                    messages: 'Cuenta Bloqueada',
                                                });
                                            }, this)
                                        });
                                        */
                                    }
                                }
                                //location.reload();
                            }, this)
                        });
                    }
                });
            },
            error: function (e) {
                throw e;
            }
        });
    },

    /*
    Función para controlar la visualización de los botones para bloquear o desbloquear cuentas
    */
    bloqueo: function () {
        if (app.user.attributes.tct_no_contactar_chk_c == '1' || app.user.attributes.bloqueo_credito_c == '1' || app.user.attributes.bloqueo_cumple_c == '1') {
            if (app.user.attributes.tct_no_contactar_chk_c == '1') {
                this.condicion_permiso = 1;
            }
            if (app.user.attributes.bloqueo_credito_c == '1') {
                this.condicion_permiso = 2;
            }
            if (app.user.attributes.bloqueo_cumple_c == '1') {
                this.condicion_permiso = 3;
            }
            this.ids_responsables = [];
            App.alert.show('loadingShowHideBotonesBloqueo', {
                level: 'process',
                title: 'Cargando',
            });
            var url_condiciones = 'tct4_Condiciones?filter[][condicion]=' + this.condicion_permiso;
            app.api.call("GET", app.api.buildURL(url_condiciones), null, {
                success: _.bind(function (data) {
                    if (data.records.length > 0) {
                        //Obtiene el identificador del equipo de responsables para poder obtener a sus respectivos miembros para que esos tengan el acceso para bloquear/desbloquear
                        //Se asume que el equipo de responsables será el MISMO en caso de que sea la misma condición
                        var id_equipo_responsables = "";
                        for (var i = 0; i < data.records.length; i++) {

                            if (data.records[i].user_id_c != "") {
                                id_equipo_responsables = data.records[i].user_id_c;
                            }
                        }

                        if (id_equipo_responsables != "") {
                            var paramsUser = {
                                "filter": [
                                    {
                                        "status": {
                                            "$in": ["Active"],
                                        }
                                    }
                                ]
                            };
                            app.api.call("read", app.api.buildURL("Teams/" + id_equipo_responsables + "/link/users", null, null, paramsUser), null, {
                                success: _.bind(function (data_members) {
                                    if (data_members.records) {
                                        for (var j = 0; j < data_members.records.length; j++) {
                                            this.ids_responsables.push(data_members.records[j].id);
                                        }
                                        // Cuentas No Contactar
                                        var id_cuenta_resumen = this._currentUrl.split('/')[1];
                                        var consulta = app.api.buildURL('tct02_Resumen/' + id_cuenta_resumen, null, null);
                                        app.api.call('read', consulta, {}, {
                                            success: _.bind(function (dataResumen) {
                                                app.alert.dismiss('loadingShowHideBotonesBloqueo');
                                                //Obtener los usuarios del equipo de responsables de validación correspondientes a la condición del usuario logueado
                                                //En caso de que la cuenta ya haya sido bloqueada de manera definitiva, mostrar la alerta, en otro caso se muestra el botón
                                                if (this.ids_responsables.includes(app.user.id)) {
                                                    //Control para mostrar botón de bloqueo
                                                    if (
                                                        (contexto_cuenta.model.get('tct_no_contactar_chk_c') && !dataResumen.bloqueo_cartera_c) ||
                                                        (dataResumen.bloqueo_credito_c && !dataResumen.bloqueo2_c) ||
                                                        (dataResumen.bloqueo_cumple_c && !dataResumen.bloqueo3_c)
                                                    ) {
                                                        $('[name="bloquea_cuenta"]').removeClass('hidden');
                                                    }
                                                    if (
                                                        (contexto_cuenta.model.get('tct_no_contactar_chk_c') && dataResumen.bloqueo_cartera_c) ||
                                                        (dataResumen.bloqueo_credito_c && dataResumen.bloqueo2_c) ||
                                                        (dataResumen.bloqueo_cumple_c && dataResumen.bloqueo3_c)
                                                    ) {
                                                        var nombre_usuario = "";
                                                        if (this.model.get('tct_no_contactar_chk_c')) {
                                                            nombre_usuario = dataResumen.validacion_c;
                                                        }
                                                        if (dataResumen.bloqueo_credito_c) {
                                                            nombre_usuario = dataResumen.validacion2_c;
                                                        }
                                                        if (dataResumen.bloqueo_cumple_c) {
                                                            nombre_usuario = dataResumen.validacion3_c;
                                                        }

                                                        app.alert.show('Cuenta bloqueada', {
                                                            level: 'error',
                                                            messages: 'Esta cuenta ya ha sido validada y bloqueada por <b>' + nombre_usuario + '</b>',
                                                        });
                                                    }
                                                    //Control para mostrar botón de desbloqueo
                                                    if (
                                                        (contexto_cuenta.model.get('tct_no_contactar_chk_c') || dataResumen.bloqueo_cartera_c) ||
                                                        (dataResumen.bloqueo_credito_c || dataResumen.bloqueo2_c) ||
                                                        (dataResumen.bloqueo_cumple_c || dataResumen.bloqueo3_c)
                                                    ) {
                                                        $('[name="desbloquea_cuenta"]').removeClass('hidden');
                                                    }
                                                }
                                            }, this)
                                        });
                                    }
                                }, this)
                            });
                        }
                    }
                }, this)
            });
        }

        // No viable
        var Productos = [];
        var reactivacion = false;
        app.api.call('GET', app.api.buildURL('GetProductosCuentas/' + this.model.get('id')), null, {
            success: function (data) {
                Productos = data;
                _.each(Productos, function (value, key) {
                    var ap1 = (Productos[key].aprueba1_c == "0") ? false : true;
                    var ap2 = (Productos[key].aprueba2_c == "0") ? false : true;
                    var react = (Productos[key].reactivacion_c == "0") ? false : true;

                    if (App.user.attributes.bloqueo_cuentas_c == '1') {
                        if (ap1 && ap2) {
                            $('[name="reactivar_noviable"]').removeClass('hidden');
                        }
                    } else {
                        if (react && !reactivacion) {
                            /*if((!ap1 && !ap2) && (Productos[key].user_id_c == app.user.id )) {
                                $('[name="reactivar_noviable"]').removeClass('hidden');
                            }*/
                            reactivacion = true;
                        }

                        if (reactivacion || !ap1 || !ap2) {
                            if (!ap1 && (Productos[key].user_id1_c == app.user.id) && (Productos[key].status_management_c == '4' || Productos[key].status_management_c == '5')) {
                                $('[name="aprobar_noviable"]').removeClass('hidden');
                                $('[name="desaprobar_noviable"]').removeClass('hidden');
                                if (react) {
                                    $('[name="aprobar_noviable"]')[0].text = "Rechazar Reactivación";
                                    $('[name="desaprobar_noviable"]')[0].text = "Confirmar Reactivación";
                                    $('[name="aprobar_noviable"]')[0].className = "btn btn-danger";
                                    $('[name="desaprobar_noviable"]')[0].className = "btn btn-success";
                                }
                            }
                            if (!ap2 && (Productos[key].user_id2_c == app.user.id) && (Productos[key].status_management_c == '4' || Productos[key].status_management_c == '5')) {
                                $('[name="aprobar_noviable"]').removeClass('hidden');
                                $('[name="desaprobar_noviable"]').removeClass('hidden');
                                if (react) {
                                    $('[name="aprobar_noviable"]')[0].text = "Rechazar Reactivación";
                                    $('[name="desaprobar_noviable"]')[0].text = "Confirmar Reactivación";
                                    $('[name="aprobar_noviable"]')[0].className = "btn btn-danger";
                                    $('[name="desaprobar_noviable"]')[0].className = "btn btn-success";
                                }
                            }
                        } else {
                            if ((ap1 && ap2) && (Productos[key].user_id_c == app.user.id)) {
                                $('[name="reactivar_noviable"]').removeClass('hidden');
                            }
                        }
                    }
                });
            },
            error: function (e) {
                throw e;
            }
        });
    },

    rechazar_noviable: function () {
        var noviable = 0;
        var Productos = [];

        var params = {};
        params["razon_c"] = ''; //razon lm
        params["motivo_c"] = ''; //motivo lm
        params["detalle_c"] = ''; //detalle lm
        params["user_id1_c"] = '';  //user id1
        params["user_id2_c"] = '';  //user id2
        params["user_id_c"] = '';  //user id
        params["status_management_c"] = '1';
        params["id_Account"] = this.model.get('id');
        params["user_id"] = app.user.id;
        params["tipoupdate"] = '1';
        params["notificacion_noviable_c"] = false;
        params["estatus_atencion"] = '1';

        //var uni = app.api.buildURL('actualizaProductosPermisos/');
        var uni = app.api.buildURL('actualizaProductosPermisos', null, null, params);
        var resp;
        app.api.call('create', uni, null, {
            success: function (data) {
                resp = data;
                /*if(resp > 0){
                    app.alert.show('Rechazar No viable cuenta', {
                       level: 'info',
                       messages: 'No se aprobó el No Viable, para la cuenta',
                    });
                }else{
                    app.alert.show('Rechazar No viable cuenta', {
                        level: 'error',
                        messages: 'No se encuentra producto a Desaprobar No viable',
                     });
                }*/
                //this.model.save();
                location.reload();
            },
            error: function (e) {
                throw e;
            }
        });
    },

    reactivar_noviable: function () {

        var params = {};

        params["status_management_c"] = '1';
        params["id_Account"] = this.model.get('id');
        params["user_id"] = app.user.id;
        if (App.user.attributes.bloqueo_cuentas_c == 1) {
            params["tipoupdate"] = '4';
        } else {
            params["tipoupdate"] = '3';
        }
        params["reactivacion_c"] = true;
        //params["estatus_atencion"] = '1';

        App.alert.show('loadingReactivar', {
            level: 'process',
            title: 'Reactivando cuenta, por favor espere',
        });

        //var uni = app.api.buildURL('actualizaProductosPermisos/');
        var uni = app.api.buildURL('actualizaProductosPermisos', null, null, params);
        var resp;
        app.api.call('create', uni, null, {
            success: function (data) {
                resp = data;
                /*if(resp > 0){
                    app.alert.show('Rechazar No viable cuenta', {
                       level: 'info',
                       messages: 'Se envío la notificación a directores, para reactivar la cuenta',
                    });
                }else{
                    app.alert.show('Rechazar No viable cuenta', {
                        level: 'error',
                        messages: 'No se encuentra producto a Reactivar No viable',
                     });
                }*/
                //this.model.save();
                location.reload();
            },
            error: function (e) {
                throw e;
            }
        });
    },

    userAlianzaSoc: function () {
        //Recupera variables
        //var chksock = this.model.get('alianza_soc_chk_c');
        var productos = App.user.attributes.productos_c; //lista de productos del usuario,
        var idUser = App.user.attributes.id; //Id del usuario,
        var puesto = App.user.attributes.puestousuario_c; //27=> Agente Tel, 31=> Coordinador CP,
        //var listaProductosSock = [];    //Recupera Ids de usuarios que pueden editar origen
        //listaProductosSock = app.lang.getAppListStrings('producto_soc_usuario_list');
        var readonly = true;

        /*Object.entries(App.lang.getAppListStrings('producto_soc_usuario_list')).forEach(([key, value]) => {
            if(this.model.get(value) == idUser && productos.includes(key) ){
                readonly = false;
            }
        });
        */
        Object.entries(App.lang.getAppListStrings('soc_usuario_list')).forEach(([key, value]) => {
            if (value == idUser) {
                readonly = false;
            }
        });

        if (readonly) {
            this.$("[data-name='alianza_soc_chk_c']").attr('style', 'pointer-events:none;');
        }
    },

    deshabilitaOrigenCuenta: function () {
        var today = new Date();
        var yyyy = today.getFullYear();
        var mm = today.getMonth() + 1; // Months start at 0!
        var dd = today.getDate();

        if (dd < 10) dd = '0' + dd;
        if (mm < 10) mm = '0' + mm;

        var hoy = yyyy + '-' + mm + '-' + dd;
        var fecha_actual = new Date(hoy);
        var fecha_bloqueo = new Date(this.model.get("fecha_bloqueo_origen_c"));

        if (fecha_actual <= fecha_bloqueo) {
            $('.record-cell[data-name="origen_cuenta_c"]').find('.normal.index').find('.edit').addClass('disabled');
            $('.record-cell[data-name="origen_cuenta_c"]').find('.normal.index').find('.select2-container').addClass('select2-container-disabled');
            $('.record-cell[data-name="origen_cuenta_c"]').find('.normal.index').find('.select2-container').find('.select2-focusser').attr('disabled', "");
            $('.record-cell[data-name="origen_cuenta_c"]').find('.normal.index').find('input[type="hidden"]').attr('disabled', "");
            $('.record-cell[data-name="origen_cuenta_c"]').find('.record-edit-link-wrapper').addClass('hide');

            $('.record-cell[data-name="detalle_origen_c"]').find('.normal.index').find('.edit').addClass('disabled');
            $('.record-cell[data-name="detalle_origen_c"]').find('.normal.index').find('.select2-container').addClass('select2-container-disabled');
            $('.record-cell[data-name="detalle_origen_c"]').find('.normal.index').find('.select2-container').find('.select2-focusser').attr('disabled', "");
            $('.record-cell[data-name="detalle_origen_c"]').find('.normal.index').find('input[type="hidden"]').attr('disabled', "");
            $('.record-cell[data-name="detalle_origen_c"]').find('.record-edit-link-wrapper').addClass('hide');

            $('.record-cell[data-name="prospeccion_propia_c"]').find('.normal.index').find('.edit').addClass('disabled');
            $('.record-cell[data-name="prospeccion_propia_c"]').find('.normal.index').find('.select2-container').addClass('select2-container-disabled');
            $('.record-cell[data-name="prospeccion_propia_c"]').find('.normal.index').find('.select2-container').find('.select2-focusser').attr('disabled', "");
            $('.record-cell[data-name="prospeccion_propia_c"]').find('.normal.index').find('input[type="hidden"]').attr('disabled', "");
            $('.record-cell[data-name="prospeccion_propia_c"]').find('.record-edit-link-wrapper').addClass('hide');

            $('.record-cell[data-name="medio_detalle_origen_c"]').find('.normal.index').find('.edit').addClass('disabled');
            $('.record-cell[data-name="medio_detalle_origen_c"]').find('.normal.index').find('.select2-container').addClass('select2-container-disabled');
            $('.record-cell[data-name="medio_detalle_origen_c"]').find('.normal.index').find('.select2-container').find('.select2-focusser').attr('disabled', "");
            $('.record-cell[data-name="medio_detalle_origen_c"]').find('.normal.index').find('input[type="hidden"]').attr('disabled', "");
            $('.record-cell[data-name="medio_detalle_origen_c"]').find('.record-edit-link-wrapper').addClass('hide');

            $('.record-cell[data-name="punto_contacto_origen_c"]').find('.normal.index').find('.edit').addClass('disabled');
            $('.record-cell[data-name="punto_contacto_origen_c"]').find('.normal.index').find('.select2-container').addClass('select2-container-disabled');
            $('.record-cell[data-name="punto_contacto_origen_c"]').find('.normal.index').find('.select2-container').find('.select2-focusser').attr('disabled', "");
            $('.record-cell[data-name="punto_contacto_origen_c"]').find('.normal.index').find('input[type="hidden"]').attr('disabled', "");
            $('.record-cell[data-name="punto_contacto_origen_c"]').find('.record-edit-link-wrapper').addClass('hide');

            $('[data-name="evento_c"]').css({ "pointer-events": "none" });
            $('[data-name="camara_c"]').css({ "pointer-events": "none" });
            $('[data-name="tct_que_promotor_rel_c"]').css({ "pointer-events": "none" });
            $('[data-name="codigo_expo_c"]').css({ "pointer-events": "none" });
            $('.record-cell[data-name="codigo_expo_c"]').find('.record-edit-link-wrapper').addClass('hide');


        }
    },

    estableceOpcionesOrigen: function () {
        var opciones_origen = app.lang.getAppListStrings('origen_lead_list');

        if (App.user.attributes.puestousuario_c != '53') { //Si no tiene puesto uniclick, se eliminan las opciones Closer y Growth
            Object.keys(opciones_origen).forEach(function (key) {
                if (key == "14" || key == "15") {
                    delete opciones_origen[key];
                }
            });
        }

        this.model.fields['origen_cuenta_c'].options = opciones_origen;

    },

    func_Proveedor: function () {
        App.alert.show('ProcesoProveedor', {
            level: 'process',
            title: 'Enviando cuenta a portal de proveedores, por favor espere.',
        });
        App.api.call("read", app.api.buildURL("AltaProveedor/" + this.model.get('id'), null, null, {}), null, {
            success: _.bind(function (data) {
                App.alert.dismiss('ProcesoProveedor');
                var level = (data.status == '200') ? 'success' : 'error';
                if (data.status != '400') {
                    self.model.set('alta_portal_proveedor_chk_c', 1);
                }
                App.alert.show('alert_func_Proveedor', {
                    level: level,
                    messages: data.message,
                });
            }, this),
        });
    },

    validaReqUniclick: function (fields, errors, callback) {
        if (App.user.attributes.id == ResumenProductos.uniclick.assigned_user_id) {
            var necesarios = "";
            var requests = [];
            var request = {};
            var Cuenta = this.model.get('id');
            //Obtenemos las opps de la cuenta
            var requestA = app.utils.deepCopy(request);
            var url = app.api.buildURL("Accounts/" + Cuenta + "/link/opportunities?filter[0][tipo_producto_c][$equals]=2&filter[1][negocio_c][$equals]=10&filter[2][negocio_c][$equals]=10&filter[3][estatus_c][$not_equals]=K&filter[4][tct_etapa_ddw_c][$not_equals]=N&filter[5][estatus_c][$not_equals]=R");
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

            app.api.call("create", app.api.buildURL("bulk", '', {}, {}), { requests: requests }, {
                success: _.bind(function (data) {
                    //Variables para controlar las direcciones y telefonos
                    var direP = 0;
                    var telCyC = 0;
                    var telO = 0;

                    if (data[0].contents.records.length > 0) {
                        //Valida direcciones y teléfonos
                        //Itera direcciones
                        for (var d = 0; d < this.oDirecciones.direccion.length; d++) {
                            //Itera direccion Particular
                            if (App.lang.getAppListStrings('tipo_dir_map_list')[self.oDirecciones.direccion[d].tipodedireccion[0]].includes('1') && self.oDirecciones.direccion[d].inactivo == false) {
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
                        //Evaluamos campos faltantes en direccion
                        if (direP <= 0) {
                            necesarios = necesarios + '<b>Dirección Particular<br></b>';
                        }
                        //Evaluamos campos faltantes en direccion
                        if (telO <= 0) {
                            necesarios = necesarios + '<b>Teléfono<br></b>';
                        }
                        //Validamos requeridos de la cuenta
                        if (this.model.get('tipodepersona_c') != 'Persona Moral') {
                            if (this.model.get('primernombre_c') == "" || this.model.get('primernombre_c') == null) {
                                necesarios = necesarios + '<b>Nombre<br></b>';
                            }
                            if (this.model.get('apellidopaterno_c') == "" || this.model.get('apellidopaterno_c') == null) {
                                necesarios = necesarios + '<b>Apellido Paterno<br></b>';
                            }
                            if (this.model.get('genero_c') == "" || this.model.get('genero_c') == null) {
                                necesarios = necesarios + '<b>G\u00E9nero</b><br>';
                            }
                            if (this.model.get('fechadenacimiento_c') == "" || this.model.get('fechadenacimiento_c') == null) {
                                necesarios = necesarios + '<b>Fecha de Nacimiento<br></b>';
                            }
                            if (this.model.get('pais_nacimiento_c') == "" || this.model.get('pais_nacimiento_c') == null || this.model.get('pais_nacimiento_c') == '0') {
                                necesarios = necesarios + '<b>Pa\u00EDs de Nacimiento</b><br>';
                            }
                            if (this.model.get('nacionalidad_c') == "" || this.model.get('nacionalidad_c') == null || this.model.get('nacionalidad_c') == '0') {
                                necesarios = necesarios + '<b>Nacionalidad</b><br>';
                            }
                            // if (this.model.get('profesion_c') == "" || this.model.get('profesion_c') == null) {
                            //         necesarios = necesarios + '<b>Profesión</b><br>';
                            // }

                            if (this.model.get('rfc_c') == "" || this.model.get('rfc_c') == null) {
                                necesarios = necesarios + '<b>RFC</b><br>';
                            }
                            if (this.model.get('nacionalidad_c') != "2") {
                                if (this.model.get('tct_pais_expide_rfc_c') == "" || this.model.get('tct_pais_expide_rfc_c') == null) {
                                    necesarios = necesarios + '<b>Pa\u00EDs que expide el RFC</b><br>';
                                }

                            } else {
                                if (this.model.get('ctpldnoseriefiel_c') == "" || this.model.get('ctpldnoseriefiel_c') == null) {
                                    necesarios = necesarios + '<b>Número de serie de la Firma Electrónica Avanzada</b><br>';
                                }
                                if (this.model.get('curp_c') == "" || this.model.get('curp_c') == null) {
                                    necesarios = necesarios + '<b>CURP</b><br>';
                                }
                                if (this.model.get('estado_nacimiento_c') == "" || this.model.get('estado_nacimiento_c') == null || this.model.get('estado_nacimiento_c') == "1") {
                                    necesarios = necesarios + '<b>Estado de Nacimiento<br></b>';
                                }
                            }
                            //Sección PEPS Física Personal
                            if (this.model.get('ctpldfuncionespublicas_c') == true) {
                                var banderaPEPSPersonal = "";
                                if (this.model.get('ctpldfuncionespublicascargo_c') == "" || this.model.get('ctpldfuncionespublicascargo_c') == null) {
                                    banderaPEPSPersonal = banderaPEPSPersonal + '<b>-Cargo público que tiene o tuvo<br></b>';
                                }
                                if (this.model.get('tct_dependencia_pf_c') == "" || this.model.get('tct_dependencia_pf_c') == null) {
                                    banderaPEPSPersonal = banderaPEPSPersonal + '<b>-Dependencia donde ejerce o ejerció el cargo<br></b>';
                                }
                                if (this.model.get('tct_periodo_pf1_c') == "" || this.model.get('tct_periodo_pf1_c') == null) {
                                    banderaPEPSPersonal = banderaPEPSPersonal + '<b>-Periodo en el cargo<br></b>';
                                }
                                if (this.model.get('tct_fecha_ini_pf_c') == "" || this.model.get('tct_fecha_ini_pf_c') == null) {
                                    banderaPEPSPersonal = banderaPEPSPersonal + '<b>-Fecha Inicio<br></b>';
                                }
                                if (this.model.get('tct_fecha_fin_pf_c') == "" || this.model.get('tct_fecha_fin_pf_c') == null) {
                                    banderaPEPSPersonal = banderaPEPSPersonal + '<b>-Fecha de término<br></b>';
                                }
                                if (banderaPEPSPersonal != "") {
                                    necesarios = necesarios + '<br>' + "Sección PEPS Personal:<br>" + banderaPEPSPersonal
                                }
                            }

                            //Sección PEPS Física Familiar
                            if (this.model.get('ctpldconyuge_c') == true) {
                                var banderaPEPSFamiliar = "";
                                if (this.model.get('ctpldconyugecargo_c') == "" || this.model.get('ctpldconyugecargo_c') == null) {
                                    banderaPEPSFamiliar = banderaPEPSFamiliar + '<b>-Especificar parentesco o relación<br></b>';
                                }
                                if (this.model.get('tct_nombre_pf_peps_c') == "" || this.model.get('tct_nombre_pf_peps_c') == null) {
                                    banderaPEPSFamiliar = banderaPEPSFamiliar + '<b>-Nombre de la persona que ocupa el puesto<br></b>';
                                }
                                if (this.model.get('tct_cargo2_pf_c') == "" || this.model.get('tct_cargo2_pf_c') == null) {
                                    banderaPEPSFamiliar = banderaPEPSFamiliar + '<b>-Cargo público que tiene o tuvo<br></b>';
                                }
                                if (this.model.get('tct_dependencia2_pf_c') == "" || this.model.get('tct_dependencia2_pf_c') == null) {
                                    banderaPEPSFamiliar = banderaPEPSFamiliar + '<b>-Dependencia donde ejerce o ejerció el cargo<br></b>';
                                }
                                if (this.model.get('tct_periodo2_pf_c') == "" || this.model.get('tct_periodo2_pf_c') == null) {
                                    banderaPEPSFamiliar = banderaPEPSFamiliar + '<b>-Periodo en el cargo<br></b>';
                                }
                                if (this.model.get('tct_fecha_ini2_pf_c') == "" || this.model.get('tct_fecha_ini2_pf_c') == null) {
                                    banderaPEPSFamiliar = banderaPEPSFamiliar + '<b>-Fecha de Inicio<br></b>';
                                }
                                if (this.model.get('tct_fecha_fin2_pf_c') == "" || this.model.get('tct_fecha_fin2_pf_c') == null) {
                                    banderaPEPSFamiliar = banderaPEPSFamiliar + '<b>-Fecha de término<br></b>';
                                }
                                if (banderaPEPSFamiliar != "") {
                                    necesarios = necesarios + '<br>' + "Sección PEPS Familiar:<br>" + banderaPEPSFamiliar
                                }
                            }

                            //Preguntas PLD
                            if (data[3].contents.records.length > 0) {
                                if (this.$('.campo2ddw-cs').select2('val') == "" || this.$('.campo2ddw-cs').select2('val') == null) {
                                    necesarios = necesarios + '<b>Pregunta 1 PLD-Crédito Simple<br></b>';
                                }
                                if (this.$('.campo4ddw-cs').select2('val') == "" || this.$('.campo4ddw-cs').select2('val') == null) {
                                    necesarios = necesarios + '<b>Pregunta 3 PLD-Crédito Simple<br></b>';
                                }
                                if (this.$('.campo18ddw-cs').select2('val').toString() == "" || this.$('.campo18ddw-cs').select2('val').toString() == null) {
                                    necesarios = necesarios + '<b>Pregunta 5 PLD-Crédito Simple<br></b>';
                                }
                                /*if (this.$('.campo14chk-cs')[0].checked == false) {
                                    necesarios = necesarios + '<b>Pregunta 6 PLD-Crédito Simple<br></b>';
                                }
                                if (this.$('.campo19txt-cs').val() == "" || this.$('.campo19txt-cs').val() == null) {
                                    necesarios = necesarios + '<b>Pregunta 5.1 PLD-Crédito Simple<br></b>';
                                }*/
                                if (this.$('.campo20ddw-cs').select2('val') == "" || this.$('.campo20ddw-cs').select2('val') == null) {
                                    necesarios = necesarios + '<b>Pregunta 7 PLD-Crédito Simple<br></b>';
                                }
                                if (this.$('.campo6ddw-cs').select2('val') == "" || this.$('.campo6ddw-cs').select2('val') == null) {
                                    necesarios = necesarios + '<b>Pregunta 8 PLD-Crédito Simple<br></b>';
                                }
                            }
                        } else {
                            //Valida persona Moral
                            if (this.$('.list_ae').select2('val') == "" || this.$('.list_ae').select2('val') == null || this.$('.list_ae').select2('val') == '0') {
                                necesarios = necesarios + '<b>Actividad Económica<br></b>';
                            }
                            if (this.model.get('razonsocial_c') == "" || this.model.get('razonsocial_c') == null) {
                                necesarios = necesarios + '<b>Razón Social<br></b>';
                            }
                            if (this.model.get('nacionalidad_c') == "" || this.model.get('nacionalidad_c') == null || this.model.get('nacionalidad_c') == '0') {
                                necesarios = necesarios + '<b>Nacionalidad</b><br>';
                            }
                            if (this.model.get('rfc_c') == "" || this.model.get('rfc_c') == null) {
                                necesarios = necesarios + '<b>RFC</b><br>';
                            }
                            if (this.model.get('tct_pais_expide_rfc_c') == "" || this.model.get('tct_pais_expide_rfc_c') == null) {
                                necesarios = necesarios + '<b>Pa\u00EDs que expide el RFC</b><br>';
                            }
                            if (this.model.get('ctpldnoseriefiel_c') == "" || this.model.get('ctpldnoseriefiel_c') == null) {
                                necesarios = necesarios + '<b>Número de serie de la Firma Electrónica Avanzada</b><br>';
                            }
                            if (this.model.get('fechaconstitutiva_c') == "" || this.model.get('fechaconstitutiva_c') == null) {
                                necesarios = necesarios + '<b>Fecha Constitutiva</b><br>';
                            }
                            if (this.model.get('tct_cpld_pregunta_u1_ddw_c') == "" || this.model.get('tct_cpld_pregunta_u1_ddw_c') == null) {
                                necesarios = necesarios + '<b>Pregunta SOFOM</b><br>';
                            }
                            if (this.model.get('tct_cpld_pregunta_u3_ddw_c') == "" || this.model.get('tct_cpld_pregunta_u3_ddw_c') == null) {
                                necesarios = necesarios + '<b>¿Cotiza en Bolsa?</b><br>';
                            }
                            /*if (this.model.get('tct_fedeicomiso_chk_c') == "" || this.model.get('tct_fedeicomiso_chk_c') == null) {
                                necesarios = necesarios + '<b>¿Es Fideicomiso?</b><br>';
                            }*/
                            //Preguntas CHECK deudor_factor_c
                            if (this.model.get('deudor_factor_c') == true) {
                                if (this.model.get('apoderado_nombre_c') == "" || this.model.get('apoderado_nombre_c') == null) {
                                    necesarios = necesarios + '<b>Nombre Apoderado Legal</b><br>';
                                }
                                if (this.model.get('apoderado_apaterno_c') == "" || this.model.get('apoderado_apaterno_c') == null) {
                                    necesarios = necesarios + '<b>Apellido Paterno Apoderado Legal</b><br>';
                                }
                                if (this.model.get('apoderado_amaterno_c') == "" || this.model.get('apoderado_amaterno_c') == null) {
                                    necesarios = necesarios + '<b>Apellido Materno Apoderado Legal</b><br>';
                                }
                            }
                            //Preguntas PLD
                            if (data[3].contents.records.length > 0) {
                                if (this.$('.campo4ddw-cs').select2('val') == "" || this.$('.campo4ddw-cs').select2('val') == null) {
                                    necesarios = necesarios + '<b>Pregunta 3 PLD-Crédito Simple<br></b>';
                                }
                                if (this.$('.campo18ddw-cs').select2('val').toString() == "" || this.$('.campo18ddw-cs').select2('val').toString() == null) {
                                    necesarios = necesarios + '<b>Pregunta 5 PLD-Crédito Simple<br></b>';
                                }
                                /*if (this.$('.campo14chk-cs')[0].checked == false) {
                                    necesarios = necesarios + '<b>regunta 6 PLD-Crédito Simple<br></b>';
                                }
                                if (this.$('.campo19txt-cs').val() == "" || this.$('.campo19txt-cs').val() == null) {
                                    necesarios = necesarios + '<b>regunta 5.1 PLD-Crédito Simple<br></b>';
                                }*/
                                if (this.$('.campo20ddw-cs').select2('val') == "" || this.$('.campo20ddw-cs').select2('val') == null) {
                                    necesarios = necesarios + '<b>Pregunta 7 PLD-Crédito Simple<br></b>';
                                }
                                if (this.$('.campo6ddw-cs').select2('val') == "" || this.$('.campo6ddw-cs').select2('val') == null) {
                                    necesarios = necesarios + '<b>Pregunta 8 PLD-Crédito Simple<br></b>';
                                }
                            }
                            //PEPS Moral Familiar
                            if (this.model.get('ctpldaccionistasconyuge_c') == true) {
                                var banderaPEPSMoralFamiliar = "";
                                if (this.model.get('tct_socio2_pm_c') == "" || this.model.get('tct_socio2_pm_c') == null) {
                                    banderaPEPSMoralFamiliar = banderaPEPSMoralFamiliar + '<b>-Nombre del Socio o Accionista<br></b>';
                                }
                                if (this.model.get('ctpldaccionistasconyugecargo_c') == "" || this.model.get('ctpldaccionistasconyugecargo_c') == null) {
                                    banderaPEPSMoralFamiliar = banderaPEPSMoralFamiliar + '<b>-Especificar parentesco o relación<br></b>';
                                }
                                if (this.model.get('tct_nombre_pm_c') == "" || this.model.get('tct_nombre_pm_c') == null) {
                                    banderaPEPSMoralFamiliar = banderaPEPSMoralFamiliar + '<b>-Nombre de la persona que ocupa el puesto<br></b>';
                                }
                                if (this.model.get('tct_cargo_pm_c') == "" || this.model.get('tct_cargo_pm_c') == null) {
                                    banderaPEPSMoralFamiliar = banderaPEPSMoralFamiliar + '<b>-Cargo público que tiene o tuvo<br></b>';
                                }
                                if (this.model.get('tct_dependencia2_pm_c') == "" || this.model.get('tct_dependencia2_pm_c') == null) {
                                    banderaPEPSMoralFamiliar = banderaPEPSMoralFamiliar + '<b>-Dependencia donde ejerce o ejerció el cargo<br></b>';
                                }
                                if (this.model.get('tct_periodo2_pm_c') == "" || this.model.get('tct_periodo2_pm_c') == null) {
                                    banderaPEPSMoralFamiliar = banderaPEPSMoralFamiliar + '<b>-Periodo en el cargo<br></b>';
                                }
                                if (this.model.get('tct_fecha_ini2_pm_c') == "" || this.model.get('tct_fecha_ini2_pm_c') == null) {
                                    banderaPEPSMoralFamiliar = banderaPEPSMoralFamiliar + '<b>-Fecha de Inicio<br></b>';
                                }
                                if (this.model.get('tct_fecha_fin2_pm_c') == "" || this.model.get('tct_fecha_fin2_pm_c') == null) {
                                    banderaPEPSMoralFamiliar = banderaPEPSMoralFamiliar + '<b>-Fecha de término<br></b>';
                                }
                                if (banderaPEPSMoralFamiliar != "") {
                                    necesarios = necesarios + '<br>' + "Sección PEPS Moral Familiar:<br>" + banderaPEPSMoralFamiliar
                                }
                            }
                            //PEPS Moral Personal
                            if (this.model.get('ctpldaccionistas_c') == true) {
                                var banderaPEPSMoralPersonal = "";
                                if (this.model.get('tct_socio_pm_c') == "" || this.model.get('tct_socio_pm_c') == null) {
                                    banderaPEPSMoralPersonal = banderaPEPSMoralPersonal + '<b>-Nombre del Socio o Accionista</b><br>';
                                }
                                if (this.model.get('ctpldaccionistascargo_c') == "" || this.model.get('ctpldaccionistascargo_c') == null) {
                                    banderaPEPSMoralPersonal = banderaPEPSMoralPersonal + '<b>-Cargo público que tiene o tuvo</b><br>';
                                }
                                if (this.model.get('tct_dependencia_pm_c') == "" || this.model.get('tct_dependencia_pm_c') == null) {
                                    banderaPEPSMoralPersonal = banderaPEPSMoralPersonal + '<b>-Dependencia donde ejerce o ejerció el cargo</b><br>';
                                }
                                if (this.model.get('tct_periodo_pm_c') == "" || this.model.get('tct_periodo_pm_c') == null) {
                                    banderaPEPSMoralPersonal = banderaPEPSMoralPersonal + '<b>-Periodo en el cargo</b><br>';
                                }
                                if (this.model.get('tct_fecha_ini_pm_c') == "" || this.model.get('tct_fecha_ini_pm_c') == null) {
                                    banderaPEPSMoralPersonal = banderaPEPSMoralPersonal + '<b>-Fecha de Inicio</b><br>';
                                }
                                if (this.model.get('tct_fecha_fin_pm_c') == "" || this.model.get('tct_fecha_fin_pm_c') == null) {
                                    banderaPEPSMoralPersonal = banderaPEPSMoralPersonal + '<b>-Fecha de término</b><br>';
                                }
                                if (banderaPEPSMoralPersonal != "") {
                                    necesarios = necesarios + '<br>' + "Sección PEPS Moral Personal:<br>" + banderaPEPSMoralPersonal
                                }
                            }

                        }
                        //Evalua si hay campos requeridos y muestra alerta
                        if (necesarios != "") {
                            app.alert.show("Campos Requeridos para opp CS y negocio Uniclick Moral", {
                                level: "error",
                                messages: "Hace falta completar la siguiente información en la <b>Cuenta</b> para el producto Uniclick:<br>" + necesarios,
                                autoClose: false
                            });
                            errors['accounts_cstm'] = errors['accounts_cstm'] || {};
                            errors['accounts_cstm'].required = true;
                        }

                    }
                    callback(null, fields, errors);
                }, this)
            });


        } else {
            callback(null, fields, errors);
        }


    },

    validaReqUniclickInfo: function () {
        if (App.user.attributes.id == ResumenProductos.uniclick.assigned_user_id) {
            var necesarios = "";
            var requests = [];
            var request = {};
            var Cuenta = this.model.get('id');
            //Obtenemos las opps de la cuenta
            var requestA = app.utils.deepCopy(request);
            var url = app.api.buildURL("Accounts/" + Cuenta + "/link/opportunities?filter[0][tipo_producto_c][$equals]=2&filter[1][negocio_c][$equals]=10&filter[2][estatus_c][$not_equals]=K&filter[3][tct_etapa_ddw_c][$not_equals]=N&filter[4][estatus_c][$not_equals]=R");
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

            app.api.call("create", app.api.buildURL("bulk", '', {}, {}), { requests: requests }, {
                success: _.bind(function (data) {
                    //Variables para controlar las direcciones y telefonos
                    var direP = 0;
                    var telCyC = 0;
                    var telO = 0;

                    if (data[0].contents.records.length > 0) {
                        //Valida direcciones y telefonos:

                        //Itera direcciones
                        for (var d = 0; d < this.oDirecciones.direccion.length; d++) {
                            //Itera direccion Particular
                            if (App.lang.getAppListStrings('tipo_dir_map_list')[self.oDirecciones.direccion[d].tipodedireccion[0]].includes('1') && self.oDirecciones.direccion[d].inactivo == false) {
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
                        //Evaluamos campos faltantes en direccion
                        if (direP <= 0) {
                            necesarios = necesarios + '<b>Dirección Particular<br></b>';
                        }
                        //Evaluamos campos faltantes en direccion
                        if (telO <= 0) {
                            necesarios = necesarios + '<b>Teléfono<br></b>';
                        }
                        //Validamos requeridos de la cuenta
                        if (this.model.get('tipodepersona_c') != 'Persona Moral') {
                            if (this.model.get('primernombre_c') == "" || this.model.get('primernombre_c') == null) {
                                necesarios = necesarios + '<b>Nombre<br></b>';
                            }
                            if (this.model.get('apellidopaterno_c') == "" || this.model.get('apellidopaterno_c') == null) {
                                necesarios = necesarios + '<b>Apellido Paterno<br></b>';
                            }
                            if (this.model.get('genero_c') == "" || this.model.get('genero_c') == null) {
                                necesarios = necesarios + '<b>G\u00E9nero</b><br>';
                            }
                            if (this.model.get('fechadenacimiento_c') == "" || this.model.get('fechadenacimiento_c') == null) {
                                necesarios = necesarios + '<b>Fecha de Nacimiento<br></b>';
                            }

                            if (this.model.get('pais_nacimiento_c') == "" || this.model.get('pais_nacimiento_c') == null || this.model.get('pais_nacimiento_c') == '0') {
                                necesarios = necesarios + '<b>Pa\u00EDs de Nacimiento</b><br>';
                            }
                            if (this.model.get('nacionalidad_c') == "" || this.model.get('nacionalidad_c') == null || this.model.get('nacionalidad_c') == '0') {
                                necesarios = necesarios + '<b>Nacionalidad</b><br>';
                            }
                            // if (this.model.get('profesion_c') == "" || this.model.get('profesion_c') == null) {
                            //         necesarios = necesarios + '<b>Profesión</b><br>';
                            // }

                            if (this.model.get('rfc_c') == "" || this.model.get('rfc_c') == null) {
                                necesarios = necesarios + '<b>RFC</b><br>';
                            }
                            if (this.model.get('nacionalidad_c') != "2") {
                                if (this.model.get('tct_pais_expide_rfc_c') == "" || this.model.get('tct_pais_expide_rfc_c') == null) {
                                    necesarios = necesarios + '<b>Pa\u00EDs que expide el RFC</b><br>';
                                }

                            } else {
                                if (this.model.get('ctpldnoseriefiel_c') == "" || this.model.get('ctpldnoseriefiel_c') == null) {
                                    necesarios = necesarios + '<b>Número de serie de la Firma Electrónica Avanzada</b><br>';
                                }
                                if (this.model.get('curp_c') == "" || this.model.get('curp_c') == null) {
                                    necesarios = necesarios + '<b>CURP</b><br>';
                                }
                                if (this.model.get('estado_nacimiento_c') == "" || this.model.get('estado_nacimiento_c') == null || this.model.get('estado_nacimiento_c') == "1") {
                                    necesarios = necesarios + '<b>Estado de Nacimiento<br></b>';
                                }
                            }
                            //Sección PEPS Física Personal
                            if (this.model.get('ctpldfuncionespublicas_c') == true) {
                                var banderaPEPSPersonal = "";
                                if (this.model.get('ctpldfuncionespublicascargo_c') == "" || this.model.get('ctpldfuncionespublicascargo_c') == null) {
                                    banderaPEPSPersonal = banderaPEPSPersonal + '<b>-Cargo público que tiene o tuvo<br></b>';
                                }
                                if (this.model.get('tct_dependencia_pf_c') == "" || this.model.get('tct_dependencia_pf_c') == null) {
                                    banderaPEPSPersonal = banderaPEPSPersonal + '<b>-Dependencia donde ejerce o ejerció el cargo<br></b>';
                                }
                                if (this.model.get('tct_periodo_pf1_c') == "" || this.model.get('tct_periodo_pf1_c') == null) {
                                    banderaPEPSPersonal = banderaPEPSPersonal + '<b>-Periodo en el cargo<br></b>';
                                }
                                if (this.model.get('tct_fecha_ini_pf_c') == "" || this.model.get('tct_fecha_ini_pf_c') == null) {
                                    banderaPEPSPersonal = banderaPEPSPersonal + '<b>-Fecha Inicio<br></b>';
                                }
                                if (this.model.get('tct_fecha_fin_pf_c') == "" || this.model.get('tct_fecha_fin_pf_c') == null) {
                                    banderaPEPSPersonal = banderaPEPSPersonal + '<b>-Fecha de término<br></b>';
                                }
                                if (banderaPEPSPersonal != "") {
                                    necesarios = necesarios + '<br>' + "Sección PEPS Personal:<br>" + banderaPEPSPersonal
                                }
                            }

                            //Sección PEPS Física Familiar
                            if (this.model.get('ctpldconyuge_c') == true) {
                                var banderaPEPSFamiliar = "";
                                if (this.model.get('ctpldconyugecargo_c') == "" || this.model.get('ctpldconyugecargo_c') == null) {
                                    banderaPEPSFamiliar = banderaPEPSFamiliar + '<b>-Especificar parentesco o relación<br></b>';
                                }
                                if (this.model.get('tct_nombre_pf_peps_c') == "" || this.model.get('tct_nombre_pf_peps_c') == null) {
                                    banderaPEPSFamiliar = banderaPEPSFamiliar + '<b>-Nombre de la persona que ocupa el puesto<br></b>';
                                }
                                if (this.model.get('tct_cargo2_pf_c') == "" || this.model.get('tct_cargo2_pf_c') == null) {
                                    banderaPEPSFamiliar = banderaPEPSFamiliar + '<b>-Cargo público que tiene o tuvo<br></b>';
                                }
                                if (this.model.get('tct_dependencia2_pf_c') == "" || this.model.get('tct_dependencia2_pf_c') == null) {
                                    banderaPEPSFamiliar = banderaPEPSFamiliar + '<b>-Dependencia donde ejerce o ejerció el cargo<br></b>';
                                }
                                if (this.model.get('tct_periodo2_pf_c') == "" || this.model.get('tct_periodo2_pf_c') == null) {
                                    banderaPEPSFamiliar = banderaPEPSFamiliar + '<b>-Periodo en el cargo<br></b>';
                                }
                                if (this.model.get('tct_fecha_ini2_pf_c') == "" || this.model.get('tct_fecha_ini2_pf_c') == null) {
                                    banderaPEPSFamiliar = banderaPEPSFamiliar + '<b>-Fecha de Inicio<br></b>';
                                }
                                if (this.model.get('tct_fecha_fin2_pf_c') == "" || this.model.get('tct_fecha_fin2_pf_c') == null) {
                                    banderaPEPSFamiliar = banderaPEPSFamiliar + '<b>-Fecha de término<br></b>';
                                }
                                if (banderaPEPSFamiliar != "") {
                                    necesarios = necesarios + '<br>' + "Sección PEPS Familiar:<br>" + banderaPEPSFamiliar
                                }
                            }

                            //Preguntas PLD
                            if (data[3].contents.records.length > 0) {
                                if (data[3].contents.records[0].tct_pld_campo2_ddw == "" || data[3].contents.records[0].tct_pld_campo2_ddw == null) {
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
                                }*/
                                if (data[3].contents.records[0].tct_pld_campo20_ddw == "" || data[3].contents.records[0].tct_pld_campo20_ddw == null) {
                                    necesarios = necesarios + '<b>Pregunta 7 PLD-Crédito Simple<br></b>';
                                }
                                if (data[3].contents.records[0].tct_pld_campo6_ddw == "" || data[3].contents.records[0].tct_pld_campo6_ddw == null) {
                                    necesarios = necesarios + '<b>Pregunta 8 PLD-Crédito Simple<br></b>';
                                }
                            }
                        } else {
                            //Valida persona Moral
                            /*if (this.model.get('actividadeconomica_c') == "" || this.model.get('actividadeconomica_c') == null) {
                                necesarios = necesarios + '<b>Actividad Económica<br></b>';
                            }*/
                            if (this.model.get('razonsocial_c') == "" || this.model.get('razonsocial_c') == null) {
                                necesarios = necesarios + '<b>Razón Social<br></b>';
                            }
                            if (this.model.get('nacionalidad_c') == "" || this.model.get('nacionalidad_c') == null || this.model.get('nacionalidad_c') == '0') {
                                necesarios = necesarios + '<b>Nacionalidad</b><br>';
                            }
                            if (this.model.get('rfc_c') == "" || this.model.get('rfc_c') == null) {
                                necesarios = necesarios + '<b>RFC</b><br>';
                            }
                            if (this.model.get('tct_pais_expide_rfc_c') == "" || this.model.get('tct_pais_expide_rfc_c') == null) {
                                necesarios = necesarios + '<b>Pa\u00EDs que expide el RFC</b><br>';
                            }
                            if (this.model.get('ctpldnoseriefiel_c') == "" || this.model.get('ctpldnoseriefiel_c') == null) {
                                necesarios = necesarios + '<b>Número de serie de la Firma Electrónica Avanzada</b><br>';
                            }
                            if (this.model.get('fechaconstitutiva_c') == "" || this.model.get('fechaconstitutiva_c') == null) {
                                necesarios = necesarios + '<b>Fecha Constitutiva</b><br>';
                            }
                            if (this.model.get('tct_cpld_pregunta_u1_ddw_c') == "" || this.model.get('tct_cpld_pregunta_u1_ddw_c') == null) {
                                necesarios = necesarios + '<b>Pregunta SOFOM</b><br>';
                            }
                            if (this.model.get('tct_cpld_pregunta_u3_ddw_c') == "" || this.model.get('tct_cpld_pregunta_u3_ddw_c') == null) {
                                necesarios = necesarios + '<b>¿Cotiza en Bolsa?</b><br>';
                            }
                            /*if (this.model.get('tct_fedeicomiso_chk_c') == "" || this.model.get('tct_fedeicomiso_chk_c') == null) {
                                necesarios = necesarios + '<b>¿Es Fideicomiso?</b><br>';
                            }*/
                            //Preguntas CHECK deudor_factor_c
                            if (this.model.get('deudor_factor_c') == true) {
                                if (this.model.get('apoderado_nombre_c') == "" || this.model.get('apoderado_nombre_c') == null) {
                                    necesarios = necesarios + '<b>Nombre Apoderado Legal</b><br>';
                                }
                                if (this.model.get('apoderado_apaterno_c') == "" || this.model.get('apoderado_apaterno_c') == null) {
                                    necesarios = necesarios + '<b>Apellido Paterno Apoderado Legal</b><br>';
                                }
                                if (this.model.get('apoderado_amaterno_c') == "" || this.model.get('apoderado_amaterno_c') == null) {
                                    necesarios = necesarios + '<b>Apellido Materno Apoderado Legal</b><br>';
                                }
                            }
                            //Preguntas PLD
                            if (data[3].contents.records.length > 0) {
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
                                }*/
                                if (data[3].contents.records[0].tct_pld_campo20_ddw == "" || data[3].contents.records[0].tct_pld_campo20_ddw == null) {
                                    necesarios = necesarios + '<b>Pregunta 7 PLD-Crédito Simple<br></b>';
                                }
                                if (data[3].contents.records[0].tct_pld_campo6_ddw == "" || data[3].contents.records[0].tct_pld_campo6_ddw == null) {
                                    necesarios = necesarios + '<b>Pregunta 8 PLD-Crédito Simple<br></b>';
                                }
                            }
                            //PEPS Moral Familiar
                            if (this.model.get('ctpldaccionistasconyuge_c') == true) {
                                var banderaPEPSMoralFamiliar = "";
                                if (this.model.get('tct_socio2_pm_c') == "" || this.model.get('tct_socio2_pm_c') == null) {
                                    banderaPEPSMoralFamiliar = banderaPEPSMoralFamiliar + '<b>-Nombre del Socio o Accionista<br></b>';
                                }
                                if (this.model.get('ctpldaccionistasconyugecargo_c') == "" || this.model.get('ctpldaccionistasconyugecargo_c') == null) {
                                    banderaPEPSMoralFamiliar = banderaPEPSMoralFamiliar + '<b>-Especificar parentesco o relación<br></b>';
                                }
                                if (this.model.get('tct_nombre_pm_c') == "" || this.model.get('tct_nombre_pm_c') == null) {
                                    banderaPEPSMoralFamiliar = banderaPEPSMoralFamiliar + '<b>-Nombre de la persona que ocupa el puesto<br></b>';
                                }
                                if (this.model.get('tct_cargo_pm_c') == "" || this.model.get('tct_cargo_pm_c') == null) {
                                    banderaPEPSMoralFamiliar = banderaPEPSMoralFamiliar + '<b>-Cargo público que tiene o tuvo<br></b>';
                                }
                                if (this.model.get('tct_dependencia2_pm_c') == "" || this.model.get('tct_dependencia2_pm_c') == null) {
                                    banderaPEPSMoralFamiliar = banderaPEPSMoralFamiliar + '<b>-Dependencia donde ejerce o ejerció el cargo<br></b>';
                                }
                                if (this.model.get('tct_periodo2_pm_c') == "" || this.model.get('tct_periodo2_pm_c') == null) {
                                    banderaPEPSMoralFamiliar = banderaPEPSMoralFamiliar + '<b>-Periodo en el cargo<br></b>';
                                }
                                if (this.model.get('tct_fecha_ini2_pm_c') == "" || this.model.get('tct_fecha_ini2_pm_c') == null) {
                                    banderaPEPSMoralFamiliar = banderaPEPSMoralFamiliar + '<b>-Fecha de Inicio<br></b>';
                                }
                                if (this.model.get('tct_fecha_fin2_pm_c') == "" || this.model.get('tct_fecha_fin2_pm_c') == null) {
                                    banderaPEPSMoralFamiliar = banderaPEPSMoralFamiliar + '<b>-Fecha de término<br></b>';
                                }
                                if (banderaPEPSMoralFamiliar != "") {
                                    necesarios = necesarios + '<br>' + "Sección PEPS Moral Familiar:<br>" + banderaPEPSMoralFamiliar
                                }
                            }
                            //PEPS Moral Personal
                            if (this.model.get('ctpldaccionistas_c') == true) {
                                var banderaPEPSMoralPersonal = "";
                                if (this.model.get('tct_socio_pm_c') == "" || this.model.get('tct_socio_pm_c') == null) {
                                    banderaPEPSMoralPersonal = banderaPEPSMoralPersonal + '<b>-Nombre del Socio o Accionista</b><br>';
                                }
                                if (this.model.get('ctpldaccionistascargo_c') == "" || this.model.get('ctpldaccionistascargo_c') == null) {
                                    banderaPEPSMoralPersonal = banderaPEPSMoralPersonal + '<b>-Cargo público que tiene o tuvo</b><br>';
                                }
                                if (this.model.get('tct_dependencia_pm_c') == "" || this.model.get('tct_dependencia_pm_c') == null) {
                                    banderaPEPSMoralPersonal = banderaPEPSMoralPersonal + '<b>-Dependencia donde ejerce o ejerció el cargo</b><br>';
                                }
                                if (this.model.get('tct_periodo_pm_c') == "" || this.model.get('tct_periodo_pm_c') == null) {
                                    banderaPEPSMoralPersonal = banderaPEPSMoralPersonal + '<b>-Periodo en el cargo</b><br>';
                                }
                                if (this.model.get('tct_fecha_ini_pm_c') == "" || this.model.get('tct_fecha_ini_pm_c') == null) {
                                    banderaPEPSMoralPersonal = banderaPEPSMoralPersonal + '<b>-Fecha de Inicio</b><br>';
                                }
                                if (this.model.get('tct_fecha_fin_pm_c') == "" || this.model.get('tct_fecha_fin_pm_c') == null) {
                                    banderaPEPSMoralPersonal = banderaPEPSMoralPersonal + '<b>-Fecha de término</b><br>';
                                }
                                if (banderaPEPSMoralPersonal != "") {
                                    necesarios = necesarios + '<br>' + "Sección PEPS Moral Personal:<br>" + banderaPEPSMoralPersonal
                                }
                            }

                        }
                        //Evalua si hay campos requeridos y muestra alerta
                        if (necesarios != "") {
                            app.alert.show("Campos Requeridos para opp CS y negocio Uniclick Moral", {
                                level: "info",
                                messages: "Hace falta completar la siguiente información en la <b>Cuenta</b> para el producto Uniclick:<br>" + necesarios,
                                autoClose: false
                            });

                        }

                    }

                }, this)
            });

        }
    },

    CamposCstmLoad: function () {

        var requests = [];
        var request = {};
        var Cuenta = this.model.get('id');
        //Obtenemos las peticiones de los campos cstm: telefonos 0
        var requestA = app.utils.deepCopy(request);
        var url = app.api.buildURL('Accounts/' + Cuenta + '/link/accounts_tel_telefonos_1');
        requestA.url = url.substring(4);
        requests.push(requestA);
        //Obtenemos peticion para la cuenta y traer campos para Cuenta 1
        var requestH = app.utils.deepCopy(request);
        var campos = "actividadeconomica_c,subsectoreconomico_c,sectoreconomico_c,tct_macro_sector_ddw_c";
        var url = app.api.buildURL('Accounts/' + Cuenta + '?fields=' + campos);
        requestH.url = url.substring(4);
        requests.push(requestH);
        //Obtenemos las peticiones de los campos cstm: Clasificacion Sectorial y V360 2
        var requestC = app.utils.deepCopy(request);
        var url = app.api.buildURL('ResumenCliente/' + Cuenta);
        requestC.url = url.substring(4);
        requests.push(requestC);
        //Obtenemos las peticiones de los campos cstm: Pipeline y productos (cont_uni_p) 3
        var requestD = app.utils.deepCopy(request);
        var url = app.api.buildURL('GetProductosCuentas/' + Cuenta);
        requestD.url = url.substring(4);
        requests.push(requestD);
        //Obtenemos las peticiones de los campos cstm: Analizate 4
        var requestE = app.utils.deepCopy(request);
        var url = app.api.buildURL('ObtieneFinanciera/' + Cuenta);
        requestE.url = url.substring(4);
        requests.push(requestE);
        //Obtenemos las peticiones de los campos cstm: PLD 5
        var requestF = app.utils.deepCopy(request);
        var url = app.api.buildURL('GetProductosPLD/' + Cuenta);
        requestF.url = url.substring(4);
        requests.push(requestF);
        //Obtenemos las peticiones de los campos cstm: Pautos 6
        var requestG = app.utils.deepCopy(request);
        var campos = "tct_no_autos_u_int_c, tct_no_autos_e_int_c, tct_no_motos_int_c, tct_no_camiones_int_c";
        var url = app.api.buildURL('tct02_Resumen/' + Cuenta + '?fields=' + campos);
        requestG.url = url.substring(4);
        requests.push(requestG);
        //Obtenemos peticion para la cuenta y traer campos para condiciones 7
        var filter_arguments =
        {
            max_num: -1,
            "fields": [
                "id",
                "condicion",
                "razon",
                "motivo",
                "detalle",
                "responsable1",
                "responsable2",
                "bloquea",
                "notifica",
            ],
        };
        filter_arguments["filter"] = [
            {
                "$or": [
                    {
                        "condicion": "4"
                    },
                    {
                        "condicion": "5"
                    }
                ]
            }
        ];
        var requestI = app.utils.deepCopy(request);
        var url = app.api.buildURL("tct4_Condiciones", null, null, filter_arguments);
        requestI.url = url.substring(4);
        requests.push(requestI);
        //Obtenemos las peticiones de los campos cstm: Direcciones SN
        /*var requestB = app.utils.deepCopy(request);
        var url = app.api.buildURL("Accounts/" + Cuenta + "/link/accounts_dire_direccion_1");
        requestB.url = url.substring(4);
        requests.push(requestB);*/


        app.api.call("create", app.api.buildURL("bulk", '', {}, {}), { requests: requests }, {
            success: _.bind(function (data) {
                //Extiende This
                this.oTelefonos = [];
                this.oTelefonos.telefono = [];
                this.prev_oTelefonos = [];
                this.prev_oTelefonos.prev_telefono = [];
                if (data[0].contents.records.length > 0) {
                    //Validaciones para Telefonos
                    this.model.set('account_telefonos', this.oTelefonos.telefono);
                    //Recupera información
                    idCuenta = this.model.get('id');
                    for (var i = 0; i < data[0].contents.records.length; i++) {
                        //Asignando valores de los campos
                        var valor1 = data[0].contents.records[i].tipotelefono;
                        var valor2 = data[0].contents.records[i].pais;
                        var valor3 = data[0].contents.records[i].estatus;
                        var valor4 = data[0].contents.records[i].telefono;
                        var valor5 = data[0].contents.records[i].extension;
                        var valor6 = (data[0].contents.records[i].principal == true) ? 1 : 0;
                        var valor7 = (data[0].contents.records[i].whatsapp_c == true) ? 1 : 0;
                        var valor8 = (data[0].contents.records[i].registro_reus_c == true) ? 1 : 0;
                        if (valor8 == 1 && valor7 == 1) { valor7 = 0; }
                        var idtel = data[0].contents.records[i].id;
                        //Estatus Teléfono 06/01/2022 ECB
                        var valor9 = data[0].contents.records[i].estatus_telefono_c;
                        var estatus = '';
                        var estatus1 = 0;
                        var estatus2 = 0;
                        var estatus3 = 0;
                        var c4 = 0;
                        if (valor9) {
                            var estatus_tel = JSON.parse(valor9);
                            if (estatus_tel[0].result == 0) {
                                estatus1 = 1;
                                estatus = app.lang.getAppListStrings('estatus_telefono_list')[1];
                            }
                            if (estatus_tel[0].result == 1) {
                                estatus2 = 1;
                                estatus = app.lang.getAppListStrings('estatus_telefono_list')[2] + ' (' + estatus_tel[0].Compania + ')';
                            }
                            if (estatus_tel[0].result == 2) {
                                estatus3 = 1;
                                estatus = app.lang.getAppListStrings('estatus_telefono_list')[3];
                            }
                            if (estatus_tel[0].Estatus_reporte) {
                                if (estatus_tel[0].Estatus_reporte.substring(0, 3) == 'Con') c4 = 1;
                            }
                        }
                        else {
                            estatus1 = 1;
                            estatus = app.lang.getAppListStrings('estatus_telefono_list')[1];
                        }
                        var telefono = {
                            "name": valor4,
                            "tipotelefono": valor1,
                            "pais": valor2,
                            "estatus": valor3,
                            "extension": valor5,
                            "telefono": valor4,
                            "principal": valor6,
                            "whatsapp_c": valor7,
                            "id_cuenta": idCuenta,
                            "id": idtel,
                            "reus": valor8,
                            "estatus1": estatus1,
                            "estatus2": estatus2,
                            "estatus3": estatus3,
                            "estatus_tel": estatus,
                            "c4": c4
                        };
                        var prev_telefono = {
                            "name": valor4,
                            "tipotelefono": valor1,
                            "pais": valor2,
                            "estatus": valor3,
                            "extension": valor5,
                            "telefono": valor4,
                            "principal": valor6,
                            "whatsapp_c": valor7,
                            "id_cuenta": idCuenta,
                            "id": idtel,
                            "reus": valor8,
                            "estatus1": estatus1,
                            "estatus2": estatus2,
                            "estatus3": estatus3,
                            "estatus_tel": estatus,
                            "c4": c4
                        };
                        contexto_cuenta.oTelefonos.telefono.push(telefono);
                        contexto_cuenta.prev_oTelefonos.prev_telefono.push(prev_telefono);
                    }
                    cont_tel.oTelefonos = contexto_cuenta.oTelefonos;
                    cont_tel.render();
                    //Oculta campo Accounts_telefonosV2
                    $("div.record-label[data-name='account_telefonos']").attr('style', 'display:none;');
                }
                //Cuenta
                if (data[1].contents != "") {
                    var campo1 = data[1].contents.actividadeconomica_c;
                    var campo2 = data[1].contents.subsectoreconomico_c;
                    var campo3 = data[1].contents.sectoreconomico_c;
                    var campo4 = data[1].contents.tct_macro_sector_ddw_c;
                }
                //Validaciones para Clasificacion Sectorial y V360
                if (data[2].contents != "") {
                    //Extiende This
                    vista360.ResumenCliente = [];
                    vista360.ResumenCliente = data[2].contents;
                    vista360.render();
                    //Etiquetas del campo custom Clasificacion Sectorial
                    clasf_sectorial.ActividadEconomica = {
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
                    clasf_sectorial.prevActEconomica = {
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
                    clasf_sectorial.ResumenCliente = data[2].contents;
                    clasf_sectorial.ActividadEconomica.ae.id = campo1;
                    clasf_sectorial.ActividadEconomica.sse.id = campo2;
                    clasf_sectorial.ActividadEconomica.se.id = campo3;
                    clasf_sectorial.ActividadEconomica.ms.id = campo4;
                    clasf_sectorial['prevActEconomica'] = app.utils.deepCopy(clasf_sectorial.ActividadEconomica);
                    clasf_sectorial.ActividadEconomica.label_div = app.lang.getAppListStrings('pb_division_list')[clasf_sectorial.ResumenCliente.pb.pb_division];
                    clasf_sectorial.ActividadEconomica.label_grp = app.lang.getAppListStrings('pb_grupo_list')[clasf_sectorial.ResumenCliente.pb.pb_grupo];
                    clasf_sectorial.ActividadEconomica.label_cls = app.lang.getAppListStrings('pb_clase_list')[clasf_sectorial.ResumenCliente.pb.pb_clase];
                    clasf_sectorial.check_uni2 = clasf_sectorial.ResumenCliente.inegi.inegi_acualiza_uni2;
                    _.extend(this, clasf_sectorial.ResumenCliente);
                    contexto_cuenta.ActividadEconomica = clasf_sectorial.ActividadEconomica;
                    contexto_cuenta.ResumenCliente = clasf_sectorial.ResumenCliente;
                    clasf_sectorial.render();

                }
                //Productos y pipeline
                if (data[3].contents != "") {
                    Productos = [];
                    //Facha Actual
                    var today = new Date();
                    var dd = today.getDate();
                    var mm = today.getMonth() + 1;
                    var yyyy = today.getFullYear();
                    if (dd < 10) { dd = '0' + dd }
                    if (mm < 10) { mm = '0' + mm }
                    today = yyyy + '-' + mm + '-' + dd;
                    Productos = data[3].contents;
                    ResumenProductos = [];
                    _.each(Productos, function (value, key) {
                        var tipoProducto = Productos[key].tipo_producto;
                        var fechaAsignacion = Productos[key].fecha_asignacion_c;
                        var fecha1 = moment(today);
                        var fecha2 = moment(fechaAsignacion);
                        Productos[key]['visible_noviable'] = (Productos[key]['visible_noviable'] != "0") ? true : false;
                        Productos[key]['no_viable'] = (Productos[key]['no_viable'] != "0") ? true : false;
                        Productos[key]['multilinea_c'] = (Productos[key]['multilinea_c'] == "1") ? true : false;
                        Productos[key]['exclu_precalif_c'] = (Productos[key]['exclu_precalif_c'] == "1") ? true : false;
                        Productos[key]['notificacion_noviable_c'] = (Productos[key]['notificacion_noviable_c'] == "1") ? true : false;
                        Productos[key]['reactivacion_c'] = (Productos[key]['reactivacion_c'] == "1") ? true : false;
                        Productos[key]['razon_c'] = (Productos[key]['razon_c'] == null) ? "" : Productos[key]['razon_c'];
                        Productos[key]['motivo_c'] = (Productos[key]['motivo_c'] == null) ? "" : Productos[key]['motivo_c'];
                        Productos[key]['aprueba1_c'] = (Productos[key]['aprueba1_c'] == "1") ? true : false;
                        Productos[key]['aprueba2_c'] = (Productos[key]['aprueba2_c'] == "1") ? true : false;
                        Productos[key]["aprueba2_c"] =
                            Productos[key]["estado_cliente_c"] = Productos[key].estado_cliente_c;

                        switch (tipoProducto) {
                            case "1": //Leasing
                                var dias = fecha1.diff(fecha2, 'days');
                                Productos[key]['dias'] = dias;
                                ResumenProductos['leasing'] = Productos[key];
                                Oproductos.productos.tct_tipo_cuenta_l_c = Productos[key]['tipo_cuenta'];
                                Oproductos.productos.tct_subtipo_l_txf_c = Productos[key]['subtipo_cuenta'];
                                break;
                            case "3": //Credito-auto
                                var dias = fecha1.diff(fecha2, 'days');
                                Productos[key]['dias'] = dias;
                                ResumenProductos['credito_auto'] = Productos[key];
                                Oproductos.productos.tct_tipo_cuenta_ca_c = Productos[key]['tipo_cuenta'];
                                Oproductos.productos.tct_subtipo_ca_txf_c = Productos[key]['subtipo_cuenta'];
                                break;
                            case "4": //Factoraje
                                var dias = fecha1.diff(fecha2, 'days');
                                Productos[key]['dias'] = dias;
                                ResumenProductos['factoring'] = Productos[key];
                                Oproductos.productos.tct_tipo_cuenta_f_c = Productos[key]['tipo_cuenta'];
                                Oproductos.productos.tct_subtipo_f_txf_c = Productos[key]['subtipo_cuenta'];
                                break;
                            case "6": //Fleet
                                var dias = fecha1.diff(fecha2, 'days');
                                Productos[key]['dias'] = dias;
                                ResumenProductos['fleet'] = Productos[key];
                                Oproductos.productos.tct_tipo_cuenta_fl_c = Productos[key]['tipo_cuenta'];
                                Oproductos.productos.tct_subtipo_fl_txf_c = Productos[key]['subtipo_cuenta'];
                                break;
                            case "8": //Uniclick
                                var dias = fecha1.diff(fecha2, 'days');
                                Productos[key]['dias'] = dias;
                                ResumenProductos['uniclick'] = Productos[key];
                                Oproductos.productos.tct_tipo_cuenta_uc_c = Productos[key]['tipo_cuenta'];
                                Oproductos.productos.tct_subtipo_uc_txf_c = Productos[key]['subtipo_cuenta'];
                                break;
                            case "9": //Unilease
                                var dias = fecha1.diff(fecha2, 'days');
                                Productos[key]['dias'] = dias;
                                ResumenProductos['unilease'] = Productos[key];
                                Oproductos.productos.tct_tipo_cuenta_ul_c = Productos[key]['tipo_cuenta'];
                                Oproductos.productos.tct_subtipo_ul_txf_c = Productos[key]['subtipo_cuenta'];
                                break;
                            case "10": //Seguros
                                var dias = fecha1.diff(fecha2, 'days');
                                Productos[key]['dias'] = dias;
                                ResumenProductos['seguros'] = Productos[key];
                                Oproductos.productos.tct_tipo_cuenta_se_c = Productos[key]['tipo_cuenta'];
                                Oproductos.productos.tct_subtipo_se_txf_c = Productos[key]['subtipo_cuenta'];
                                break;
                            case "14": //Tarjeta Crédito
                                var dias = fecha1.diff(fecha2, 'days');
                                Productos[key]['dias'] = dias;
                                ResumenProductos['tarjetaCredito'] = Productos[key];
                                Oproductos.productos.tct_tipo_cuenta_tc_c = Productos[key]['tipo_cuenta'];
                                Oproductos.productos.tct_subtipo_tc_txf_c = Productos[key]['subtipo_cuenta'];
                                break;
                            case "2": //Crédito Simple
                                var dias = fecha1.diff(fecha2, 'days');
                                Productos[key]['dias'] = dias;
                                ResumenProductos['tarjetaCredito'] = Productos[key];
                                Oproductos.productos.tct_tipo_cuenta_cs_c = Productos[key]['tipo_cuenta'];
                                Oproductos.productos.tct_subtipo_cs_txf_c = Productos[key]['subtipo_cuenta'];
                                break;
                            default:
                                break;
                        }
                    });
                    cont_uni_p['ResumenProductos'] = ResumenProductos;
                    contexto_cuenta['ResumenProductos'] = ResumenProductos;
                    cont_uni_p.render();
                    Oproductos.render();

                    //Limpia pipeline
                    pipeacc.render();
                    //Ejecuta funcion para actualizar pipeline
                    pipeacc.tipoSubtipo_vista();

                }
                //ANALIZATE
                if (data[4].contents != "") {
                    cont_nlzt.Analizate = [];
                    cont_nlzt.Analizate.Financiera = [];
                    cont_nlzt.Analizate.Credit = [];
                    cont_nlzt.Analizate.Cliente = [];
                    cont_nlzt.Analizate.Financiera = data[4].contents.Financiera;
                    cont_nlzt.Analizate.Credit = data[4].contents.Credit;
                    cont_nlzt.Analizate.Cliente = data[4].contents.AnalizateCliente;
                    cont_nlzt.render();
                    analizate_cl.Analizate = [];
                    analizate_cl.Analizate.Financiera = [];
                    analizate_cl.Analizate.Credit = [];
                    analizate_cl.Analizate.Cliente = [];
                    analizate_cl.Analizate.Financiera = data[4].contents.Financiera;
                    analizate_cl.Analizate.Credit = data[4].contents.Credit;
                    analizate_cl.Analizate.Cliente = data[4].contents.AnalizateCliente;
                    analizate_cl.cargapipelineCliente();
                    analizate_cl.render();


                }
                //PLD
                if (data[5].contents != "") {
                    //Recupera resultado
                    contexto_cuenta.ProductosPLD = pld.formatDetailPLD(data[5].contents);
                    //Establece visibilidad por tipo de productos
                    //AP
                    if (App.user.attributes.tipodeproducto_c == '1') {
                        contexto_cuenta.ProductosPLD.arrendamientoPuro.visible = 'block';
                    }
                    //FF
                    if (App.user.attributes.tipodeproducto_c == '4') {
                        contexto_cuenta.ProductosPLD.factorajeFinanciero.visible = 'block';
                    }
                    //CA
                    if (App.user.attributes.tipodeproducto_c == '3') {
                        contexto_cuenta.ProductosPLD.creditoAutomotriz.visible = 'block';
                    }
                    //Genera objeto con valores previos para control de cancelar
                    contexto_cuenta.prev_ProductosPLD = app.utils.deepCopy(contexto_cuenta.ProductosPLD);
                    pld.ProductosPLD = contexto_cuenta.ProductosPLD;

                    //Aplica render a campo custom
                    pld.render();

                }
                //Pautos
                if (data[6].contents != "") {
                    contexto_cuenta.Pautos = [];
                    contexto_cuenta.Pautos.autos = [];
                    contexto_cuenta.Pautos.autos = data[6].contents;
                    contexto_cuenta.Pautos.prev_autos = app.utils.deepCopy(Pautos.autos);
                    Pautos.render();
                }

                //Setea dataCondiciones
                if (data[7].contents != "") {
                    this.datacondiciones = [];
                    if (data[7].contents.records.length > 0) {
                        contexto_cuenta.datacondiciones = data[7].contents;
                        this.datacondiciones = data[7].contents;
                    }
                }
                //Final de funcion, mandamos ejecutar funcion de requniclick
                this.validaReqUniclickInfo();
                //Ejecuta funcion para boton CIEC
                this.muestraCIEC();
                //render a campo cstm Clientes analizate
                analizate_cl.render();

            }, this)
        });

    },

    validaGrupoEmpresarial: function (fields, errors, callback) {
        var subtipo_prospecto = ['7', '8', '9', '10', '12'];
        var subtipo_cliente = ['11', '12', '13', '14', '15', '16', '17', '18', '19', '20'];
        var tipo_registro_cuenta_c = this.model.get("tipo_registro_cuenta_c");
        var subtipo_registro_cuenta_c = this.model.get("subtipo_registro_cuenta_c");
        var tipo_gp_emp = this.model.get("situacion_gpo_empresarial_c");
        var error = false;
        var errorText = "";
        //Valida que no se asocie la misma cuenta como padre
        if (this.model.get('parent_id') == this.model.get('id')) {
            error = true;
            errorText += 'La cuenta está asociada a si misma como grupo empresarial. Por favor, corrige este valor.<br>';
        }
        //Valida situación de grupo empresarial para pospecto interesado en adelante
        if ((tipo_registro_cuenta_c == "2" && subtipo_prospecto.includes(subtipo_registro_cuenta_c)) || (tipo_registro_cuenta_c == "3" && subtipo_cliente.includes(subtipo_registro_cuenta_c))) {
            if (tipo_gp_emp.indexOf("4") !== -1 && this.model.get('parent_id') == "") {
                error = true;
                errorText += 'La Situación del Grupo Empresarial no puede ser <b>Sin Grupo Empresarial Verificado</b>. Por favor, corrige este valor o bien asocia la cuenta a un <b>Grupo Empresarial</b>.<br>';
            }
            if (tipo_gp_emp.indexOf("3") !== -1 && (tipo_gp_emp.indexOf("1") !== -1 || tipo_gp_emp.indexOf("2") !== -1)) {
                error = true;
                errorText += 'La Situación del Grupo Empresarial no puede estar asociada y no pertencer a ningun <b>Grupo Empresarial</b>.<br>';
            }
            if (tipo_gp_emp.indexOf("3") !== -1 && this.model.get('parent_id') != "") {
                error = true;
                errorText += 'Grupo Empresarial debe tener <b>Situación Empresarial Definida</b>.';
            }
        }
        //Valida y muestra errires
        if (error) {
            app.alert.show("Situacion Grupo Empresarial", {
                level: "error",
                messages: errorText,
                autoClose: false
            });
            errors['situacion_gpo_empresarial_c'] = errors['situacion_gpo_empresarial_c'] || {};
            errors['situacion_gpo_empresarial_c'].required = true;
        }
        callback(null, fields, errors);

    },

    validaReferido: function (fields, errors, callback) {
        var referido = this.model.get('account_id1_c');
        var consulta = app.api.buildURL('Accounts/' + referido, null, null);

        if (this.model.get('origen_cuenta_c') == '8') {
            app.api.call('read', consulta, {}, {
                success: _.bind(function (data) {
                    if (data.tipo_proveedor_compras_c != '6' && data.codigo_vendor_c == "") {
                        app.alert.show("Cuenta no VENDOR", {
                            level: "error",
                            messages: 'La cuenta Referida no tiene un <b>código vendor</b>. Favor de verificar.',
                            autoClose: false
                        });
                        errors['referido_cliente_prov_c'] = errors['referido_cliente_prov_c'] || {};
                        errors['referido_cliente_prov_c'].required = true;

                    }
                    callback(null, fields, errors);
                }, this)
            });
        } else {
            callback(null, fields, errors);
        }

    },

    val_SituacionEmpresarial: function () {
        var tipo_gp_emp = this.model.get("situacion_gpo_empresarial_c");

        if (event.type == 'mouseup') {
            if (tipo_gp_emp.indexOf("1") !== -1 || tipo_gp_emp.indexOf("2") !== -1) {
                this.model.set("situacion_gpo_empresarial_c", "");
                app.alert.show("Situación Grupo Empresarial", {
                    level: "error",
                    title: "Las opciones Cuenta Primaria y Cuenta Secundaria no son elegibles manualmente. Para usar éstas 2 opciones debes asociar la cuenta a un grupo empresarial.",
                    autoClose: false
                });
            }

            if (tipo_gp_emp.indexOf("3") !== -1) {
                this.model.set("parent_name", "");
                this.model.set("parent_id", "");
            }
            //Si se tiene la combinación 3 y 4 se borra el valor 4
            if (tipo_gp_emp.includes('3') && tipo_gp_emp.includes('4')) {
                delete tipo_gp_emp[tipo_gp_emp.indexOf('4')];
                this.model.set("situacion_gpo_empresarial_c", tipo_gp_emp);
            }
        }
    },


    // ocultaproveedor: function () {
    //     var Proveedor = 0;
    //     var Boton1 = this.getField("proveedor_quantico");
    //     if (this.model.get("esproveedor_c") || this.model.get("tipo_registro_cuenta_c") == 5) Proveedor = 1;
    //     if (Boton1) {
    //         Boton1.listenTo(Boton1, "render", function () {
    //             if (Proveedor) {
    //                 Boton1.show();
    //             } else {
    //                 Boton1.hide();
    //             }
    //         });
    //     }
    // },

    disableNameCliente: function () {
        if (this.model.get('tipo_registro_cuenta_c') == '3') {
            this.noEditFields.push('primernombre_c');
            this.noEditFields.push('apellidopaterno_c');
            this.noEditFields.push('apellidomaterno_c');
            this.noEditFields.push('razonsocial_c');
            this.noEditFields.push('nombre_comercial_c');

            this.$("[data-name='primernombre_c']").attr('style', 'pointer-events:none;');
            this.$("[data-name='apellidopaterno_c']").attr('style', 'pointer-events:none;');
            this.$("[data-name='apellidomaterno_c']").attr('style', 'pointer-events:none;');
            this.$("[data-name='razonsocial_c']").attr('style', 'pointer-events:none;');
            this.$("[data-name='nombre_comercial_c']").attr('style', 'pointer-events:none;');
        }
    },

    // proveedor_quantico: function () {
    //     //Creación de tarea de integración de expediente de proveedor en Quantico
    //     app.alert.show('proveedor_quantico', {
    //         level: 'process',
    //         title: 'Creando tarea de integración de expediente en Quantico para el proveedor, por favor espere.',
    //     });
    //     app.api.call("read", app.api.buildURL("tarea_quantico/" + this.model.get('id'), null, null, {}), null, {
    //         success: _.bind(function (data) {
    //             app.alert.dismiss('proveedor_quantico');
    //             app.alert.show('tarea_quantico', {
    //                 level: 'warning',
    //                 messages: data,
    //             });
    //         }, this),
    //     });
    // },
    validaPropRealCR: function (fields, errors, callback) {
        var esPropietario = false;
        var esCLiente = false;
        var esTercero = false;
        var tienePR = false;

        esCLiente = (this.model.get('tipo_registro_cuenta_c') == "3") ? true : false;
        esTercero = (contexto_cuenta.ProductosPLD.creditoRevolvente.campo8 == '2') ? true : false;
        tienePR = (contexto_cuenta.ProductosPLD.creditoRevolvente.campo9 == '') ? false : true;


        if (App.user.attributes.productos_c.includes('14')) {
            if ((this.model.get('tipo_registro_cuenta_c') != "4" || this.model.get('tipo_registro_cuenta_c') != "5")) {

                //Realizamos apicall para buscar que la cuenta tenga alguna relacion con otra
                var Cuenta = this.model.get('id');
                var consulta = app.api.buildURL('Rel_Relaciones/?filter[0][account_id1_c][$equals]=' + Cuenta, null, null);
                app.api.call('read', consulta, {}, {
                    success: _.bind(function (data) {
                        if (data.records.length > 0) {
                            //Validamos que las relaciones sean de tipo Propietario Real
                            for (var i = 0; i < data.records.length; i++) {
                                if (data.records[i].relaciones_activas.includes('Propietario Real')) {
                                    esPropietario = true;
                                }
                            }
                        }
                        if ((!esPropietario && esTercero && !tienePR) || (esCLiente && esTercero && !tienePR)) {
                            $('.campo9rel-ce').find('.select2-choice').css('border-color', 'red');

                            app.alert.show("existen_relaciones_PR", {
                                level: "error",
                                messages: "Favor de seleccionar un <b>Propietario Real</b> en la sección de PLD- Crédito Revolvente.",
                                autoClose: false
                            });
                            errors['propetariorealCR'] = errors['propetariorealCR'] || {};
                            errors['propetariorealCR'].required = true;

                        }
                        callback(null, fields, errors);
                    }, this)
                });
            } else {
                callback(null, fields, errors);
            }
        } else {
            callback(null, fields, errors);
        }
    },

    muestraCIEC: function () {
        //Validamos que el usuario logueado sea el mismo asignado a alguno de los productos de la cuenta
        var leasing = App.user.attributes.id == contexto_cuenta.ResumenProductos.leasing.assigned_user_id && contexto_cuenta.ResumenProductos.leasing.tipo_cuenta == '3' ? true : false;;
        var factoring = App.user.attributes.id == contexto_cuenta.ResumenProductos.factoring.assigned_user_id && contexto_cuenta.ResumenProductos.factoring.tipo_cuenta == '3' ? true : false;;
        var creditauto = App.user.attributes.id == contexto_cuenta.ResumenProductos.credito_auto.assigned_user_id && contexto_cuenta.ResumenProductos.credito_auto.tipo_cuenta == '3' ? true : false;;
        var fleet = App.user.attributes.id == contexto_cuenta.ResumenProductos.fleet.assigned_user_id && contexto_cuenta.ResumenProductos.fleet.tipo_cuenta == '3' ? true : false;;
        var uniclick = App.user.attributes.id == contexto_cuenta.ResumenProductos.uniclick.assigned_user_id && contexto_cuenta.ResumenProductos.uniclick.tipo_cuenta == '3' ? true : false;;

        if (leasing == true || factoring == true || creditauto == true || fleet == true || uniclick == true) {
            $('[name="solicitar_ciec"]').show();
        } else {
            $('[name="solicitar_ciec"]').hide();
        }
    },

    _panel_anlzt_proveedor: function () {
        if (this.model.get('tipo_registro_cuenta_c') == '5' || this.model.get('esproveedor_c') == true) {
            //Muestra subpanel Proveedor De Analizate
            this.$("[data-panelname='LBL_RECORDVIEW_PANEL18']").show();
        } else {
            this.$("[data-panelname='LBL_RECORDVIEW_PANEL18']").hide();
        }
    },
    _panel_anlzt_cliente: function () {
        if (this.model.get("tipo_registro_cuenta_c") == '3' || this.model.get("tipo_registro_cuenta_c") == '2' || this.model.get("tipo_registro_cuenta_c") == '4') {
            //Muestra subpanel Cliente De Analizate
            this.$("[data-panelname='LBL_RECORDVIEW_PANEL24']").show();
        } else {
            this.$("[data-panelname='LBL_RECORDVIEW_PANEL24']").hide();
        }
    },

    hide_panel_buro_credito: function () {

        //Obtiene bandera de buró del registro de Resumen relacionado
        var url = app.api.buildURL('tct02_Resumen/' + this.model.get('id'), null, null);
        app.api.call('read', url, {}, {
            success: _.bind(function (resumen) {
                var privilegio_buro = App.user.get("seguimiento_bc_c");
                if (resumen.seguimiento_bc_c == "" || resumen.seguimiento_bc_c == 0) {
                    this.$("[data-panelname='LBL_RECORDVIEW_PANEL27']").hide();
                } else {
                    if (privilegio_buro == 0 || privilegio_buro == "") {
                        this.$("[data-panelname='LBL_RECORDVIEW_PANEL27']").hide();
                    }
                }

            }, this)
        });
    },

    ocultaSolicitarCIEC: function () {
        var cliente = (this.model.get("tipo_registro_cuenta_c") == 3 || this.model.get("tipo_registro_cuenta_c") == 2 || this.model.get("tipo_registro_cuenta_c") == 4) ? true : false;
        var botonSC = this.getField("solicitar_ciec");
        if (botonSC) {
            botonSC.listenTo(botonSC, "render", function () {
                if (cliente) {
                    botonSC.show();
                } else {
                    botonSC.hide();
                }
            });
        }
    },

    /*
    Función utilizada como parche (en la actualización a la version 12 de sugar) para poder ocultar las filas que siguen mostrándose en la vista
    aunque los campos que están dentro de dicha fila se encuentren ocultos a través de la dependencia de visibilidad
    El no aplicar esta función hacía que en la vista de registro se mostraran algunas filas en blanco
    */
    hideRowsNoHideByDependency: function () {
        //La clase vis_action_hidden se agrega cuando un campo se oculta a través de una fórmula en studio o una dependencia
        var hidden_rows = $('.LBL_RECORDVIEW_PANEL16 > .vis_action_hidden');
        hidden_rows.each(function (i, obj) {
            //Se oculta la fila cuando se detecta que el campo está oculto y además el campo que está junto a el es el campo custom "blank_space" o es una celda "relleno" habilitada desde studio
            if ($(obj).siblings('[data-name="blank_space"]').length > 0 || $(obj).siblings('.filler-cell').length > 0) {
                $(obj).parent().addClass('hide');
            }
        });
    },

    solicitar_ciec_function: function () {
        //Valida que sea proveedor para reenviar
        if (this.model.get('tipo_registro_cuenta_c') == "1" || this.model.get("tipo_registro_cuenta_c") == 5) {
            app.alert.show('No_Cliente', {
                level: 'error',
                messages: 'Sólo se puede solcitar CIEC para cuentas de tipo Cliente, Persona o Prospecto.',
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
                var levelStatus = (data['status'] == '200') ? 'success' : 'error';
                app.alert.show('Correo_reenviado', {
                    level: levelStatus,
                    messages: data['message'],
                    autoClose: false
                });
            },
            error: function (e) {
                App.alert.dismiss('eventoEnvioMailCliente');
                app.alert.show('Correo_no_reenviado', {
                    level: 'error',
                    messages: 'No se ha podido generar solicitud CIEC. Intente nuevamente.',
                    autoClose: false
                });
            }
        });
    },

    muestra_modal_alta_po: function () {

        var selfModalAltaPO = this;

        //Mostramos el modal para el director leasing solo si se solictó la creación y si el asesor logueado es el asesor leasing
        app.alert.show('loadingMuestraModal', {
            level: 'process',
            title: 'Cargando...',
        });

        var url = app.api.buildURL('tct02_Resumen/' + this.model.get('id'), null, null,);
        app.api.call('GET', url, {}, {
            success: function (data) {
                app.alert.dismiss('loadingMuestraModal');

                var asesorLeasingId = selfModalAltaPO.model.get('user_id_c');
                var currentUserId = App.user.id;

                if (data.bloqueo_cartera_c || data.bloqueo2_c || data.bloqueo3_c) {
                    app.alert.show('cuentaBloqueada', {
                        level: 'error',
                        messages: 'Acción no disponible.<b>Cuenta Bloqueada</b>',
                        autoClose: false
                    });
                }
                //Si está notificado y entra alguien que no es director, muestra alaerta de pendiente de aprobación
                if (data.po_creado_estado_c == '1' && data.id_dir_comercial_aprueba_c != currentUserId) {
                    app.alert.show('pendienteAprobacion', {
                        level: 'error',
                        messages: 'Actualmente se tiene una solicitud de aprobación en proceso. Espere la confirmación o rechazo de su director',
                        autoClose: false
                    });
                }

                //Si el usuario logueado es igual al Director comercial y además ya se creó el PO, se muestra pantalla con botones para aprobar y rechazar
                else if ((data.po_creado_c == 1 && data.id_dir_comercial_aprueba_c == currentUserId) || currentUserId == asesorLeasingId) {
                    app.drawer.open({
                        layout: 'layout-alta-po',
                        context: {
                            context: selfModalAltaPO.context,
                            model: selfModalAltaPO.model,
                        },
                    }, function (context, model, update) {
                        console.log("CIERRA DRAWER ALTA PO");


                    });

                } else {

                    app.alert.show('sinPermisoModal', {
                        level: 'error',
                        messages: 'No cuentas con el privilegio para realizar esta acción',
                        autoClose: false
                    });


                }

            }
        });

    },

    dynamics365: function () {
        //Muestra mensaje de Dynamics365
        var url = app.api.buildURL('tct02_Resumen/' + this.model.get('id'), null, null);
        app.api.call('read', url, {}, {
            success: _.bind(function (resumen) {
                contexto_cuenta.showSubpanels();
                contexto_cuenta.renderDireccionSinSepomex();
                if (resumen.error_dynamics365_c) {
                    app.alert.show('error_otp', {
                        level: 'warning',
                        messages: "Se ha identificado un problema con la sincronización a Dynamics 365. Para mayor detalle consulta con el equipo de TI",
                        autoClose: false
                    });
                }
            }, this)
        });
    },

    showSubpanels: function () {

        $("[data-subpanel-link]").show();

        $('[data-name="control_dynamics_365_c"]').hide();

        //Se agrega parche para mostrar icono de + en subpanels de Venta cruzada y Seguros ya que desde panel-top no se está tomando
        $('[data-subpanel-link="accounts_ref_venta_cruzada_1"]')
            .find(".fa-plus")
            .addClass("sicon sicon-plus")
            .removeClass("fa")
            .removeClass("fa-plus");

        $('[data-subpanel-link="s_seguros_accounts"]')
            .find(".fa-plus")
            .addClass("sicon sicon-plus")
            .removeClass("fa")
            .removeClass("fa-plus");

        //Se refrescan los subpaneles manualmente para evitar bug al aplicar refresh a la página, los subpaneles no mostraban datos
        this.refreshAllSubpanels();


    },

    /**
     * Se aplica render de account_direcciones en caso de que la(s) dirección(es) tengan el problema de que no tenga match con sepomex
     */
    renderDireccionSinSepomex: function () {

        const sinMatchSepomex = [];

        for (let index = 0; index < contexto_cuenta.oDirecciones.direccion.length; index++) {
            if (contexto_cuenta.oDirecciones.direccion[index].hasOwnProperty("sinSepomex")) {
                sinMatchSepomex.push(1);
            }
        }

        if (sinMatchSepomex.includes(1)) {

            cont_dir.render();

        }
    },

    refreshAllSubpanels: function () {

        var arrSubpaneles = [];

        $('[data-subpanel-link]').each(function () {
            var subpanelLink = $(this).data('subpanel-link');
            arrSubpaneles.push(subpanelLink);
        });
        _.each(this.model._relatedCollections, function (collection) {

            if (arrSubpaneles.includes(collection.link.name)) {
                collection.fetch({ relate: true });
            }

        });
    },

    CURP_DuplicateCheck: function (fields, errors, callback) {
        var ACCURP = this.model.get('curp_c');
        if (this.model.get('curp_c')) {
            app.api.call("read", app.api.buildURL("Accounts/", null, null, {
                fields: "curp_c",
                max_num: 5,
                "filter": [
                    {
                        "curp_c": ACCURP,
                        "id": {
                            $not_equals: this.model.id,
                        }
                    }
                ]
            }), null, {
                success: _.bind(function (data) {
                    if (data.records.length > 0) {
                        app.alert.show("DuplicateCheck", {
                            level: "error",
                            title: "Ya existe una persona registrada con el mismo CURP.",
                            autoClose: false
                        });

                        errors['curp_c'] = errors['curp_c'] || {};
                        errors['curp_c'].required = true;
                    }
                    callback(null, fields, errors);
                }, this)
            });
        } else {
            callback(null, fields, errors);
        }
    },

    _hideBtnSolicitudAsignacion: function () {
        var btnSolicitudAsignacion = this.getField("solicitud_asignacion");
        var idUsuarioPendiente = '569246c7-da62-4664-ef2a-5628f649537e';
        //OCULTA Y MUESTRA EL BOTON DE SOLICITUD ASIGNACION
        if (btnSolicitudAsignacion) {
            btnSolicitudAsignacion.listenTo(btnSolicitudAsignacion, "render", function () {
                //VALIDA USUARIO LEASING
                var nombreAsesorAsignadoLeasing = contexto_cuenta.ResumenProductos.leasing.nombre_completo_c;
                var regionUsuarioLeasing = (contexto_cuenta.ResumenProductos.leasing.region_c || '').trim().toLowerCase();
                var regionUsuarioActual = (App.user.attributes.region_c || '').trim().toLowerCase();
                var status = contexto_cuenta.ResumenProductos.leasing.status;
                var posicionOperativaLeasing = contexto_cuenta?.ResumenProductos?.leasing?.posicion_operativa_c || "";
                var estatusAtencion = contexto_cuenta.ResumenProductos.leasing.estatus_atencion;
                var esUsuarioPendienteAsignar = this.model.get('user_id_c') === idUsuarioPendiente;
                var esMismaRegion = (regionUsuarioLeasing === regionUsuarioActual);
                var esDiferenteRegion = !esMismaRegion;
                var tipodeCuenta = contexto_cuenta.ResumenProductos.leasing.tipo_cuenta;
                esPendienteAsignar = (esUsuarioPendienteAsignar || (tipodeCuenta !== '3' && estatusAtencion === '2') || status === "Inactive" || !posicionOperativaLeasing.includes("^3^"));

                //VALIDA POSICION OPERATIVA: ASESOR Y SI ES DEL PROCESO 0 - PENDIENTE DE ASIGNAR, MISMA O DIFERENTE REGION
                if (esPendienteAsignar || esMismaRegion || esDiferenteRegion) {
                    btnSolicitudAsignacion.show();
                    //MUESTRA EL NOMBRE DEL ASESOR ACTUAL DEL PRODUCTO LEASING EN MISMA Y DIFERENTE REGION
                    if (!esPendienteAsignar && (esMismaRegion || esDiferenteRegion)) {
                        $('[data-event="button:solicitud_asignacion:click"]').html("Solicitud de Asignación -&nbsp;<b>" + nombreAsesorAsignadoLeasing + "</b>");
                    } else {
                        $('[data-event="button:solicitud_asignacion:click"]').html("Solicitud de Asignación");
                    }
                } else {
                    btnSolicitudAsignacion.hide();
                }
            });
        }
    },

    solicitudAsignacionCuenta: function () {
        console.log("...Solicitud asignacion...");
        //POSICION OPERATIVA
        var posicionOperativa = App.user.attributes.posicion_operativa_c;
        //ASESOR LEASING
        if (posicionOperativa.includes("^3^")) {
            var flagAsignacionActiva = contexto_cuenta.ResumenCliente.asignacion_automatica.asignacion_activa_c;
            var idUsuarioPendiente = '569246c7-da62-4664-ef2a-5628f649537e';
            var esValidoProcesoCeroPendienteAsignar = false;
            var esValidoProcesoMismaRegion = false;
            var esValidoProcesoDiferenteRegion = false;
            //OBTIENE INFORMACION DEL USUARIO LEASING
            var usuarioAsignadoLeasing = this.model.get('user_id_c');
            //VALIDA SI HAY UNA SOLICITUD DE ASIGNACION ACTIVA
            if (Number(flagAsignacionActiva) === 0) {
                //PROCESO ASIGNACION-AUTOMATICA
                if (usuarioAsignadoLeasing) {                
                    var status = contexto_cuenta.ResumenProductos.leasing.status;
                    var posicionOperativaLeasing = contexto_cuenta?.ResumenProductos?.leasing?.posicion_operativa_c || "";
                    var regionUsuarioLeasing = (contexto_cuenta.ResumenProductos.leasing.region_c || '').trim().toLowerCase();
                    var regionUsuarioActual = (App.user.attributes.region_c || '').trim().toLowerCase();
                    var esMismaRegion = (regionUsuarioLeasing === regionUsuarioActual);
                    var esPendienteAsignar = (usuarioAsignadoLeasing === idUsuarioPendiente);
                    var estatusAtencion = contexto_cuenta.ResumenProductos.leasing.estatus_atencion;
                    var tipodeCuenta = contexto_cuenta.ResumenProductos.leasing.tipo_cuenta;
                    var esDiferenteRegion = !esMismaRegion;
    
                    //VALIDACION 0-PENDIENTE DE ASIGNAR: Usuario = 0-pendiente o estatus atención = desatendido o estatus usuario = inactivo o puesto usuario <> asesor comercial
                    esValidoProcesoCeroPendienteAsignar = esPendienteAsignar || (tipodeCuenta !== '3' && estatusAtencion === '2') || status === "Inactive" || !posicionOperativaLeasing.includes("^3^");                  
                    if (!esValidoProcesoCeroPendienteAsignar && !esMismaRegion && !esDiferenteRegion) {
                        app.alert.show('sa_asesor_cero_pendiente_asignar', {
                            level: 'error',
                            autoClose: false,
                            messages: 'No puedes Solicitar la Asignación de la Cuenta porque <b>no cumple los requisitos de asignación por 0-Pendiente de Asignar.</b>'
                        });
                        return;
                    }
                    //VALIDACIONES MISMA REGIÓN: Si región asesor viejo = región asesor nuevo {Si tipo cuenta <> cliente y estatus atención = atendido entonces entra, Si tipo cuenta = cliente entonces entra}
                    if (!esValidoProcesoCeroPendienteAsignar && esMismaRegion) {
                        esValidoProcesoMismaRegion = (tipodeCuenta !== '3' && estatusAtencion === '1') || tipodeCuenta === '3';
                        if (!esValidoProcesoMismaRegion) {
                            app.alert.show('sa_asesor_misma_region', {
                                level: 'error',
                                autoClose: false,
                                messages: 'No puedes Solicitar la Asignación de la Cuenta porque <b>no cumple los requisitos de asignación por Misma Región.</b>'
                            });
                            return;
                        }
                    }
                    //VALIDACIONES DIFERENTE REGIÓN: Si región asesor viejo <> región asesor nuevo {Si tipo cuenta <> cliente y estatus atención = atendido entonces notifica Ricardo Gerardo Si tipo cuenta = cliente entonces VoBo Dir. Regional}
                    if (!esValidoProcesoCeroPendienteAsignar && esDiferenteRegion) {
                        esValidoProcesoDiferenteRegion = (tipodeCuenta !== '3' && estatusAtencion === '1') || tipodeCuenta === '3';
                        if (!esValidoProcesoDiferenteRegion) {
                            app.alert.show('sa_asesor_diferente_region', {
                                level: 'error',
                                autoClose: false,
                                messages: 'No puedes Solicitar la Asignación de la Cuenta porque <b>no cumple los requisitos de asignación por Diferente Región.</b>'
                            });
                            return;
                        }
                    }
                    //VALIDA SI LA POSICION OPERATIVA DEL USUARIO ASIGNADO LEASING ES VACIA
                    if (posicionOperativaLeasing === '' && !esPendienteAsignar && (!esValidoProcesoCeroPendienteAsignar || esValidoProcesoCeroPendienteAsignar)) {
                        app.alert.show('sa_asesor_asignado_no_comercial', {
                            level: 'error',
                            autoClose: false,
                            messages: 'No puedes Solicitar la Asignación de la Cuenta favor de solicitarlo al <b>Ejecutivo de Estrategia Comercial.</b>'
                        });
                        return;
                    }

                    if(cont_uni_p.ResumenProductos.leasing.dias < 16 && cont_uni_p.ResumenProductos.leasing.estatus_atencion == '1' ){
                        esValidoProcesoMismaRegion = false;
                        esValidoProcesoDiferenteRegion = false;
                        app.alert.show('sa_asesor_diferente_region', {
                            level: 'error',
                            autoClose: false,
                            messages: 'No puedes Solicitar la Asignación sigue en el tiempo de gracia de atención de 15 días.'
                        });
                    }else{
                        // Valida proceso pendiente de asignar
                        if (esValidoProcesoCeroPendienteAsignar) {
                            console.log("Proceso: 0 - Pendiente de Asignar");
                            this.enviarEmailSolicitudAsignacionAPI(true, false, false);
                        }
                        // Valida proceso misma región
                        if (esValidoProcesoMismaRegion) {
                            console.log("Proceso: Misma Region");
                            this.enviarEmailSolicitudAsignacionAPI(false, true, false);
                        }
                        // Valida proceso diferente región
                        if (esValidoProcesoDiferenteRegion) {
                            console.log("Proceso: Diferente Region");
                            this.enviarEmailSolicitudAsignacionAPI(false, false, true);
                        }
                    }                    
                }
                
            } else {
                app.alert.show('sa_asignacion_activa', {
                    level: 'error',
                    autoClose: false,
                    messages: 'La asignación de la cuenta no puede ser solicitada, ya que existe una <b>aprobación en curso.</b>'
                });
                return false;
            }           

        } else {
            app.alert.show('sa_asesor_no_comercial', {
                level: 'error',
                autoClose: false,
                messages: 'No puedes Solicitar la Asignación de la Cuenta porque <b>NO ERES UN ASESOR LEASING.</b>'
            });
            return false;
        }
    },

    enviarEmailSolicitudAsignacionAPI: function (flagPendienteAsignar, flagMismaRegion, flagDiferenteRegion) {
        console.log("Enviando email solicitud asignacion...");
        var btnSolAsignacion = this.getField('solicitud_asignacion');
        btnSolAsignacion.setDisabled(true);

        app.alert.show('proceso_solicitud_asignacion', {
            level: 'process',
            title: 'Enviando correo',
        });

        //PROCESO PENDIENTE DE ASIGNAR
        if (flagPendienteAsignar) {
            var args = {
                "id_cuenta": this.model.get('id'),
                "id_asesor_solicita": App.user.attributes.id
            };
            app.api.call("create", app.api.buildURL("solicitudAsignacionEmail", null, null, args), null, {
                success: _.bind(function (response) {
                    app.alert.dismiss('proceso_solicitud_asignacion');
                    btnSolAsignacion.setDisabled(true);
                    app.alert.show('alert_correo_sa', {
                        level: 'success',
                        messages: response,
                    });
                    // Recargar la página después del éxito
                    setTimeout(function () {
                        location.reload();
                    }, 5000); // Espera 5 segundos antes de recargar
                }, this),
            });
        }
        //PROCESO DE APROBACION MISMA REGION || DIFERENTE REGION
        if (flagMismaRegion || flagDiferenteRegion) {
            var estatusAtencion = contexto_cuenta.ResumenProductos.leasing.estatus_atencion;
            var tipodeCuenta = contexto_cuenta.ResumenProductos.leasing.tipo_cuenta;
            //VALIDA SI ES PARA EJECUTIVO DE ESTRATEGIA COMERCIAL/RICARDO GERARDO
            var esEjecutivoEstrategiaComercial = (flagDiferenteRegion && tipodeCuenta !== '3' && estatusAtencion === '1') ? true : false;
            console.log("esEjecutivoEstrategiaComercial ", esEjecutivoEstrategiaComercial);
            var argsmdr = {
                "id_cuenta": this.model.get('id'),
                "id_asesor_solicita": App.user.attributes.id,
                "id_asesor_anterior": this.model.get('user_id_c'),
                "es_diferente_region": flagDiferenteRegion,
                "es_ejecutivo_estrategia": esEjecutivoEstrategiaComercial
            };
            app.api.call("create", app.api.buildURL("voboDirectorRegionalCuentas", null, null, argsmdr), null, {
                success: _.bind(function (response) {
                    app.alert.dismiss('proceso_solicitud_asignacion');
                    btnSolAsignacion.setDisabled(true);
                    app.alert.show('alert_correo_aprobacion', {
                        level: 'success',
                        messages: response,
                    });
                }, this),
            });
        }
    },

})
