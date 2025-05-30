({
    extendsFrom: 'RecordView',

    initialize: function (options) {
        //self = this;
        this._super("initialize", [options]);

        this.model.addValidationTask('check_Requeridos', _.bind(this.valida_requeridos_min, this));
        this.model.on('sync', this._readonlyFields, this);
        this.context.on('button:convert_po_to_Lead:click', this.convert_Po_to_Lead, this);
        this.context.on('button:cancel_button:click', this.handleCancel, this);
        this.context.on('button:reenvio_correo:click', this.reenvio_correo, this);

        //Botones para Aprobar/Rechazar envío de correos
        this.context.on('button:vobo_envio_correo:click', this.vobo_envio_correo, this);
        this.context.on('button:rechaza_envio_correo:click', this.rechaza_envio_correo, this);

        //Solicitar edición de origen
        this.context.on('button:cambiar_origen:click', this.solicta_cambio_origen, this);

        //this.model.on('sync', this._hideBtnConvert, this);
        this._readonlyFields();
        this.events['keypress [name=phone_mobile]'] = 'validaSoloNumerosTel';
        this.events['keypress [name=phone_home]'] = 'validaSoloNumerosTel';
        this.events['keypress [name=phone_work]'] = 'validaSoloNumerosTel';
        this.events['keydown [name=phone_mobile]'] = 'validaSoloNumerosTel';
        this.events['keydown [name=phone_home]'] = 'validaSoloNumerosTel';
        this.events['keydown [name=phone_work]'] = 'validaSoloNumerosTel';
        this.events['keypress [name=telefono_aa_c]'] = 'validaSoloNumerosTel';
        this.events['keydown [name=telefono_aa_c]'] = 'validaSoloNumerosTel';
        this.model.addValidationTask('check_longDupTel', _.bind(this.validaLongDupTel, this));
        this.model.addValidationTask('check_TextOnly', _.bind(this.checkTextOnly, this));
        this.model.addValidationTask('change:email', _.bind(this.expmail, this));
        this.events['keydown [name=ventas_anuales_c]'] = 'checkInVentas';
        this.events['keydown [name=potencial_lead_c]'] = 'checkInVentas';
        this.context.on('button:llamada_mobile:click', this.llamar_movil, this);
        this.context.on('button:llamada_home:click', this.llamar_casa, this);
        this.context.on('button:llamada_work:click', this.llamar_trabajo, this);
        this.context.on('button:edit_button:click', this.noLlamar, this);
        this.model.on('sync', this.siNumero, this);
        this.context.on('button:reset_lead:click', this.reset_lead, this);
        this.model.on('sync', this._hideBtnReset, this);
        this.model.on("change:leads_leads_1_right", _.bind(this._checkContactoAsociado, this));
        //Direcciones
        contexto_prospect = this;
        this.get_addresses();
        this.model.addValidationTask('set_custom_fields', _.bind(this.setCustomFields, this));
        this.model.addValidationTask('checkEmptyFieldsDire', _.bind(this.validadirecc, this));
        this.model.addValidationTask('validate_Direccion_Duplicada', _.bind(this._direccionDuplicada, this));
        this.model.addValidationTask('valida_usuarios_inactivos', _.bind(this.valida_usuarios_inactivos, this));
        this.$("[data-panelname='LBL_RECORDVIEW_PANEL3']").hide();

        /****** validaciones SOC  **********/
        this.model.on("change:detalle_origen_c", _.bind(this.cambios_origen_SOC, this));
        this.model.on("change:origen_c", _.bind(this.cambios_origen_SOC, this));
        this.model.on("change:estatus_po_c", _.bind(this.change_estatus, this));

        //this.events['click a[track="click:actiondropdown"] > .sicon.sicon-chevron-down'] = 'clickActionsCambiaEtiquetaEnvioCorreo';
        this.events['click .ddw-main > .dropdown-toggle'] = 'clickActionsCambiaEtiquetaEnvioCorreo';
        this.model.on('sync', this.userAlianzaSoc, this);
        this.model.on('sync', this.muestraBotonCorreo, this);
        this.model.on('sync', this.hideShowBtnVoBo, this);

        this.model.on('sync', this.muestraBotonConversion, this);
        this.cmbio_soc = 0;

        //Función para eliminar opciones del campo origen
        this.estableceOpcionesOrigenLeads();
        this.model.on("change:origen_c", _.bind(this.estableceOpcionesOrigenLeads, this));
        //Función para establecer el año y el mes actual + 2 futuros
        this._estableceMesOperacion();
        this.model.on("change:mes_operacion_c", _.bind(this._estableceMesOperacion, this));
        //Valida 3 activos maximo
        this.model.addValidationTask('validate_activo_interes', _.bind(this._validateTaskActivoInteres, this));
        this.model.on("change:activos_interes_c", this._validaActivoInteres, this);
        //Valida el potencial de cierre debe ser entre 10 y 100%
        this.model.addValidationTask('validate_potencial_cierre', _.bind(this._validateTaskPotencialCierre, this));
        this.model.on("change:potencial_cierre_c", this._validaPotencialCierre, this);
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
        //this.deshabilitaOrigen();
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
            //self.deshabilitaOrigen();
        });
    },


    _disableActionsSubpanel: function () {
        $('[data-subpanel-link="calls"]').find(".subpanel-controls").hide();
        $('[data-subpanel-link="meetings"]').find(".subpanel-controls").hide();
        $('[data-subpanel-link="tasks"]').find(".subpanel-controls").hide();
        $('[data-subpanel-link="notes"]').find(".subpanel-controls").hide();
        $('[data-subpanel-link="campaigns"]').find(".subpanel-controls").hide();
        $('[data-subpanel-link="archived_emails"]').find(".subpanel-controls").hide();
        $('[data-subpanel-link="leads_leads_1"]').find(".subpanel-controls").hide();
        $("div.record-label[data-name='prospects_direcciones']").attr('style', 'display:none;');
    },

    expmail: function (fields, errors, callback) {
        if (this.model.get('email') != null && this.model.get('email') != "") {

            var input = (this.model.get('email'));
            var expresion = /^\S+@\S+\.\S+[$%&|<>#]?$/;
            var cumple = true;

            for (i = 0; i < input.length; i++) {

                if (expresion.test(input[i].email_address) == false) {
                    cumple = false;

                }
            }

            if (cumple == false) {
                app.alert.show('Error al validar email', {
                    level: 'error',
                    autoClose: false,
                    messages: '<b>Formato de Email Incorrecto.</b>'
                })
                errors['email'] = errors['email'] || {};
                errors['email'].required = true;
            }
        }

        if (this.model.get('origen_c') == '12' && (this.model.get('detalle_origen_c') == '12' || this.model.get('detalle_origen_c') == '13' || this.model.get('detalle_origen_c') == '114' || this.model.get('detalle_origen_c') == '115')) {
            //VALIDA FORMATO DE EMAIL DEL ASESOR DE ALIANZA
            if (this.model.get('email_aa_c') != null && this.model.get('email_aa_c') !== "") {

                var inputEAA = this.model.get('email_aa_c'); // Obtenemos el email
                var expresionEAA = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Expresión regular válida para emails

                if (!expresionEAA.test(inputEAA)) {
                    // Si el formato del email no es válido, mostramos el error
                    app.alert.show('Error al validar email AA', {
                        level: 'error',
                        autoClose: false,
                        messages: '<b>Formato de Email del Asesor de Alianza Incorrecto.</b>'
                    });
                    errors['email_aa_c'] = errors['email_aa_c'] || {};
                    errors['email_aa_c'].required = true;
                }
            }
        }

        callback(null, fields, errors);
    },

    checkTextOnly: function (fields, errors, callback) {
        app.alert.dismiss('Error_validacion_Campos');
        var camponame = "";
        var expresion = new RegExp(/^[a-zA-ZÀ-ÿ\s]*$/g);

        if (this.model.get('nombre_c') != "" && this.model.get('nombre_c') != undefined) {
            var nombre = this.model.get('nombre_c');
            var comprueba = expresion.test(nombre);
            if (comprueba != true) {
                camponame = camponame + '<b>' + app.lang.get("LBL_NOMBRE", "Leads") + '</b><br>';
                errors['nombre_c'] = errors['nombre_c'] || {};
                errors['nombre_c'].required = true;
            }
        }
        if (this.model.get('apellido_paterno_c') != "" && this.model.get('apellido_paterno_c') != undefined) {
            var apaterno = this.model.get('apellido_paterno_c');
            var expresion = new RegExp(/^[a-zA-ZÀ-ÿ\s]*$/g);
            var validaap = expresion.test(apaterno);
            if (validaap != true) {
                camponame = camponame + '<b>' + app.lang.get("LBL_APELLIDO_PATERNO_C", "Leads") + '</b><br>';
                errors['apellido_paterno_c'] = errors['apellido_paterno_c'] || {};
                errors['apellido_paterno_c'].required = true;
            }
        }
        if (this.model.get('apellido_materno_c') != "" && this.model.get('apellido_materno_c') != undefined) {
            var amaterno = this.model.get('apellido_materno_c');
            var expresion = new RegExp(/^[a-zA-ZÀ-ÿ\s]*$/g);
            var validaam = expresion.test(amaterno);
            if (validaam != true) {
                camponame = camponame + '<b>' + app.lang.get("LBL_APELLIDO_MATERNO_C", "Leads") + '</b><br>';
                errors['apellido_materno_c'] = errors['apellido_materno_c'] || {};
                errors['apellido_materno_c'].required = true;
            }
        }
        if (camponame) {
            app.alert.show("Error_validacion_Campos", {
                level: "error",
                messages: 'Los siguientes campos no permiten Caracteres Especiales y Números:<br>' + camponame,
                autoClose: false
            });
        }
        callback(null, fields, errors);
    },

    validaLongDupTel: function (fields, errors, callback) {

        if ((this.model.get('phone_mobile') != "" && this.model.get('phone_mobile') != undefined) || (this.model.get('phone_home') != "" && this.model.get('phone_home') != undefined) || (this.model.get('phone_work') != "" && this.model.get('phone_work') != undefined)) {

            var phoneMobile = this.model.get('phone_mobile') != "" ? this.validaTmanoRepetido(this.model.get('phone_mobile')) : false;
            var phoneHome = this.model.get('phone_home') != "" ? this.validaTmanoRepetido(this.model.get('phone_home')) : false;
            var phoneWork = this.model.get('phone_work') != "" ? this.validaTmanoRepetido(this.model.get('phone_work')) : false;

            /***********************Valida Longitud y Carácteres repetidos********************/
            num_errors = 0;
            if (phoneMobile) {
                num_errors = num_errors + 1;
                $('.Telefonom').css('border-color', 'red');
                errors['phone_mobile'] = errors['phone_mobile'] || {};
                errors['phone_mobile'].required = true;
            }
            if (phoneHome) {
                num_errors = num_errors + 1;
                $('.Telefonoc').css('border-color', 'red');
                errors['phone_home'] = errors['phone_home'] || {};
                errors['phone_home'].required = true;
            }
            if (phoneWork) {
                num_errors = num_errors + 1;
                $('.Telefonot').css('border-color', 'red');
                errors['phone_work'] = errors['phone_work'] || {};
                errors['phone_work'].required = true;
            }

            if (num_errors > 0) {
                app.alert.show("Num-invalido", {
                    level: "error",
                    title: "El teléfono debe contener 10 dígitos / Contiene carácteres repetidos",
                    autoClose: false
                });
            }

            /************************* Valida duplciados ******************************/

            duplicado = 0;
            if (this.model.get('phone_mobile') == this.model.get('phone_home') && this.model.get('phone_mobile') != "" && this.model.get('phone_home') != "") {
                duplicado = duplicado + 1;
                $('.Telefonom').css('border-color', 'red');
                $('.Telefonoc').css('border-color', 'red');
                errors['phone_mobile'] = errors['phone_mobile'] || {};
                errors['phone_mobile'].required = true;
                errors['phone_home'] = errors['phone_home'] || {};
                errors['phone_home'].required = true;

            }
            if (this.model.get('phone_mobile') == this.model.get('phone_work') && this.model.get('phone_mobile') != "" && this.model.get('phone_work') != "") {
                duplicado = duplicado + 1;
                $('.Telefonom').css('border-color', 'red');
                $('.Telefonot').css('border-color', 'red');
                errors['phone_mobile'] = errors['phone_mobile'] || {};
                errors['phone_mobile'].required = true;
                errors['phone_work'] = errors['phone_work'] || {};
                errors['phone_work'].required = true;

            }
            if (this.model.get('phone_home') == this.model.get('phone_work') && this.model.get('phone_home') != "" && this.model.get('phone_work') != "") {
                duplicado = duplicado + 1;
                $('.Telefonoc').css('border-color', 'red');
                $('.Telefonot').css('border-color', 'red');
                errors['phone_home'] = errors['phone_home'] || {};
                errors['phone_home'].required = true;
                errors['phone_work'] = errors['phone_work'] || {};
                errors['phone_work'].required = true;

            }

            if (duplicado > 0) {
                app.alert.show("Tel-Duplicado", {
                    level: "error",
                    title: "No se puede agregar el número: Ya ha sido registrado.",
                    autoClose: false
                });
            }
        }

        if (this.model.get('origen_c') == '12' && (this.model.get('detalle_origen_c') == '12' || this.model.get('detalle_origen_c') == '13' || this.model.get('detalle_origen_c') == '114' || this.model.get('detalle_origen_c') == '115')) {
            //VALIDA LA LONGITUD DE 10 DIGITOS DEL NUMERO TELEFONICO DEL ASESOR DE ALIANZA
            if (this.model.get('telefono_aa_c') != "" && this.model.get('telefono_aa_c') != null) {
                if (this.model.get('telefono_aa_c').trim() == "" || this.model.get('telefono_aa_c').trim().length != 10) {
                    app.alert.show('telefono_aa_invalido', {
                        level: 'error',
                        autoClose: false,
                        messages: 'Se requiere un teléfono válido de <b>10 dígitos</b> para el <b>Teléfono del Asesor de Alianza</b>'
                    });
                    errors['telefono_aa_c'] = errors['telefono_aa_c'] || {};
                    errors['telefono_aa_c'].required = true;
                }
            }
        }

        callback(null, fields, errors);
    },

    validaTmanoRepetido: function (telefono) {
        requerido = false;

        if (telefono != "" && telefono != undefined) {
            if (telefono.length == 10) {

                if (telefono.length > 1) {
                    var repetido = true;
                    for (var itelefono = 0; itelefono < telefono.length; itelefono++) {
                        repetido = (telefono[0] != telefono[itelefono]) ? false : repetido;
                    }
                    if (repetido) {
                        requerido = true;
                    }
                }
            }
            else {
                requerido = true;
            }
        }

        return requerido;
    },

    validaSoloNumerosTel: function (evt) {

        if (evt.which != 8 && evt.which != 9 && evt.which != 0 && (evt.which < 48 || evt.which > 57) && (evt.which < 96 || evt.which > 105)) {

            app.alert.show('Caracter_Invalido', {
                level: 'error',
                autoClose: true,
                messages: '<b>Solo números son permitidos en este campo.</b>'
            });
            return false;
        }
    },

    _hideBtnConvert: function () {

        var btnConvert = this.getField("convert_po_to_Lead");

        if (btnConvert) {
            btnConvert.listenTo(btnConvert, "render", function () {

                if (this.model.get('estatus_po_c') == '2') {
                    btnConvert.show();
                } else {
                    btnConvert.hide();
                }
            });
        }
    },

    valida_requeridos_min: function (fields, errors, callback) {
        var campos = "";

        _.each(errors, function (value, key) {
            _.each(this.model.fields, function (field) {
                if (_.isEqual(field.name, key)) {
                    if (field.vname) {
                        campos = campos + '<b>' + app.lang.get(field.vname, "Prospects") + '</b><br>';
                    }
                }
            }, this);
        }, this);

        if (this.model.get('origen_c') == '12' && (this.model.get('detalle_origen_c') == '12' || this.model.get('detalle_origen_c') == '13' || this.model.get('detalle_origen_c') == '114' || this.model.get('detalle_origen_c') == '115')) {
            //CAMPOS REQUERIDOS DE ALIANZAS
            if (this.model.get('franquicia_c') == '' || this.model.get('franquicia_c') == undefined) {
                campos = campos + '<b>' + 'Franquicia' + '</b><br>';

                errors['franquicia_c'] = errors['franquicia_c'] || {};
                errors['franquicia_c'].required = true;
            }
            if (this.model.get('asesor_alianza_c') == '' || this.model.get('asesor_alianza_c') == undefined) {
                campos = campos + '<b>' + 'Asesor de la Alianza' + '</b><br>';

                errors['asesor_alianza_c'] = errors['asesor_alianza_c'] || {};
                errors['asesor_alianza_c'].required = true;
            }
            if (this.model.get('email_aa_c') == '' || this.model.get('email_aa_c') == undefined) {
                campos = campos + '<b>' + 'Email del Asesor de Alianza' + '</b><br>';

                errors['email_aa_c'] = errors['email_aa_c'] || {};
                errors['email_aa_c'].required = true;
            }
            if (this.model.get('telefono_aa_c') == '' || this.model.get('telefono_aa_c') == undefined) {
                campos = campos + '<b>' + 'Teléfono del Asesor de Alianza' + '</b><br>';

                errors['telefono_aa_c'] = errors['telefono_aa_c'] || {};
                errors['telefono_aa_c'].required = true;
            }
        }
        //ACTIVOS DE INTERÉS
        if (this.model.get('activos_interes_c') == '' || this.model.get('activos_interes_c') == null) {
            campos = campos + '<b>' + 'Activos de interés' + '</b><br>';
            errors['activos_interes_c'] = errors['activos_interes_c'] || {};
            errors['activos_interes_c'].required = true;
        }
        //POTENCIAL DE CIERRE
        if (this.model.get('potencial_cierre_c') == '' || this.model.get('potencial_cierre_c') == null) {
            campos = campos + '<b>' + 'Potencial de cierre' + '</b><br>';
            errors['potencial_cierre_c'] = errors['potencial_cierre_c'] || {};
            errors['potencial_cierre_c'].required = true;
        }
        //MES ESTIMADO DE OPERACIÓN
        if (this.model.get('mes_operacion_c') == '' || this.model.get('mes_operacion_c') == null) {
            campos = campos + '<b>' + 'Mes estimado de operación' + '</b><br>';
            errors['mes_operacion_c'] = errors['mes_operacion_c'] || {};
            errors['mes_operacion_c'].required = true;
        }
        //ACTIVIDAD ECONOMICA
        if (this.model.get('actividad_economica_c') == '' || this.model.get('actividad_economica_c') == '0' || this.model.get('actividad_economica_c') == null) {
            campos = campos + '<b>' + 'Actividad Económica' + '</b><br>';
            $('.campoAE .record-label').css('color', '#bb0e1b');
            $('.list_ae .select2-choice').css('border', '1px solid #bb0e1b');

            errors['actividad_economica_c'] = errors['actividad_economica_c'] || {};
            errors['actividad_economica_c'].required = true;
        }
        //MONTO ESTIMADO
        var montoEstimado = parseFloat(this.model.get('potencial_lead_c'));
        if (isNaN(montoEstimado) || montoEstimado <= 0) {
            campos = campos + '<b>' + 'Monto estimado' + '</b><br>';
            errors['potencial_lead_c'] = errors['potencial_lead_c'] || {};
            errors['potencial_lead_c'].required = true;
        }
        if (campos) {
            app.alert.show("Campos Requeridos", {
                level: "error",
                messages: "Hace falta completar la siguiente información para guardar un <b>Público Objetivo: </b><br>" + campos,
                autoClose: false
            });
        }
        callback(null, fields, errors);

    },

    valida_requeridos: function () {
        var campos = "";
        var subTipoLead = this.model.get('subtipo_registro_c');
        var tipoPersona = this.model.get('regimen_fiscal_c');
        var campos_req = [];
        var response = false;
        var errors = {};

        switch (subTipoLead) {
            /*******SUB-TIPO SIN CONTACTAR*****/
            case '1':
                if (tipoPersona == '3') {
                    campos_req.push('nombre_empresa_c');
                }
                else {
                    campos_req.push('nombre_c', 'apellido_paterno_c');
                }
                break;
            /********SUB-TIPO CONTACTADO*******/
            case '2':
                if (tipoPersona == '3') {
                    campos_req.push('nombre_empresa_c');
                }
                else {
                    campos_req.push('nombre_c', 'apellido_paterno_c', 'puesto_c');
                }

                campos_req.push('macrosector_c', 'ventas_anuales_c', 'zona_geografica_c', 'email');

                break;

            default:
                break;
        }
        if (this.model.get('origen_c') == '12' && (this.model.get('detalle_origen_c') == '12' || this.model.get('detalle_origen_c') == '13' || this.model.get('detalle_origen_c') == '114' || this.model.get('detalle_origen_c') == '115')) {
            //CAMPOS REQUERIDOS DE ALIANZAS
            if (this.model.get('franquicia_c') == '' || this.model.get('franquicia_c') == null) {
                campos_req.push('franquicia_c');
            }
            if (this.model.get('asesor_alianza_c') == '' || this.model.get('asesor_alianza_c') == null) {
                campos_req.push('asesor_alianza_c');
            }
            if (this.model.get('email_aa_c') == '' || this.model.get('email_aa_c') == null) {
                campos_req.push('email_aa_c');
            }
            if (this.model.get('telefono_aa_c') == '' || this.model.get('telefono_aa_c') == null) {
                campos_req.push('telefono_aa_c');
            }
        }

        if (campos_req.length > 0) {

            for (i = 0; i < campos_req.length; i++) {

                var temp_req = campos_req[i];

                if (temp_req == 'ventas_anuales_c') {
                    if (this.model.get('ventas_anuales_c') == 0) {
                        errors[temp_req] = errors[temp_req] || {};
                        errors[temp_req].required = true;

                    }
                }

                else if (this.model.get(temp_req) == '' || this.model.get(temp_req) == null) {
                    errors[temp_req] = errors[temp_req] || {};
                    errors[temp_req].required = true;
                }
            }
        }

        _.each(errors, function (value, key) {
            _.each(this.model.fields, function (field) {
                if (_.isEqual(field.name, key)) {
                    if (field.vname) {
                        campos = campos + '<b>' + app.lang.get(field.vname, "Prospects") + '</b><br>';
                    }
                }
            }, this);
        }, this);

        if (((this.model.get('phone_mobile') == '' || this.model.get('phone_mobile') == null) &&
            (this.model.get('phone_home') == '' || this.model.get('phone_home') == null) &&
            (this.model.get('phone_work') == '' || this.model.get('phone_work') == null)) &&
            this.model.get('subtipo_registro_c') == '2') {

            campos = campos + '<b>' + 'Al menos un Teléfono' + '</b><br>';
            campos = campos.replace("<b>Móvil</b><br>", "");
            campos = campos.replace("<b>Teléfono de casa</b><br>", "");
            campos = campos.replace("<b>Teléfono de Oficina</b><br>", "");

            errors['phone_mobile'] = errors['phone_mobile'] || {};
            errors['phone_mobile'].required = true;
            errors['phone_home'] = errors['phone_home'] || {};
            errors['phone_home'].required = true;
            errors['phone_work'] = errors['phone_work'] || {};
            errors['phone_work'].required = true;
        }

        /*if (campos) {
            app.alert.show("Campos Requeridos", {
                level: "error",
                messages: "Hace falta completar la siguiente información para convertir un <b>Lead: </b><br>" + campos,
                autoClose: false
            });
        }*/

        // console.log("campos requeridos "  +campos);

        if (campos == "") {
            response = true;
        }

        return response;
    },

    _readonlyFields: function () {
        var self = this;

        //Solo Lectura Regimen Fiscal para que únicamente se establezca el PO como PF
        $('[data-name="regimen_fiscal_c"]').css('pointer-events', 'none');
        /***************************READONLY PARA SUBTIPO DE LEAD CANCELADO**************************/
        if (this.model.get('estatus_po_c') == '3' || this.model.get('estatus_po_c') == '4') {

            var editButton = self.getField('edit_button');
            editButton.setDisabled(true);

            _.each(this.model.fields, function (field) {

                self.noEditFields.push(field.name);
                self.$('.record-edit-link-wrapper[data-name=' + field.name + ']').remove();
                self.$('[data-name=' + field.name + ']').attr('style', 'pointer-events:none;');

            });
            this._disableActionsSubpanel();
        }
        /***************************READONLY PARA SUBTIPO DE LEAD CONVERTIDO**************************/
        if (this.model.get('estatus_po_c') == '3' || this.model.get('estatus_po_c') == '4') {
            var editButton = self.getField('edit_button');
            editButton.setDisabled(true);
            //var btnConvert = self.getField("convert_po_to_Lead");
            //btnConvert.hide();
            _.each(this.model.fields, function (field) {
                if (field.name != 'origen_ag_tel_c' && field.name != 'promotor_c' && field.name != 'account_to_lead' && field.name != 'assigned_user_name' && field.name != 'email') {
                    self.noEditFields.push(field.name);
                    self.$('.record-edit-link-wrapper[data-name=' + field.name + ']').remove();
                    self.$('[data-name=' + field.name + ']').attr('style', 'pointer-events:none;');
                }
            });
            this._disableActionsSubpanel();
        }
        //Se omite función para deshabilitar origen, ya que se opta por hacerlo a través de dependencias
        //ReadOnly Alianza - Utility Trailers
        if (!App.user.attributes.gestion_utility_trailers_po_c && this.model.get('origen_c') === '12' && this.model.get('detalle_origen_c') === '114') {
            $('[data-name="origen_c"]').css('pointer-events', 'none');
            self.noEditFields.push('origen_c');
            $('[data-name="detalle_origen_c"]').css('pointer-events', 'none');
            self.noEditFields.push('detalle_origen_c');
        }
        //READONLY DE ORIGEN BLOQUEADO CON ALIANZA SOC/CREDITARIA, KONNECT, VENDORS || MARKETING - ORGANICO || LEASING - LEASING
        if (!App.user.attributes.gestion_utility_trailers_po_c && (
            (this.model.get('origen_bloqueado_c') && this.model.get('origen_c') === '12' && (['12', '13', '115', '116'].includes(this.model.get('detalle_origen_c')))) ||
            (this.model.get('origen_c') === '1' && this.model.get('detalle_origen_c') === '80') ||
            (this.model.get('origen_c') === '20' && this.model.get('detalle_origen_c') === '113')
        )
        ) {
            self.noEditFields.push('origen_c');
            $('[data-name="origen_c"]').css('pointer-events', 'none');
            self.$('.record-edit-link-wrapper[data-name=origen_c]').remove();
            self.noEditFields.push('detalle_origen_c');
            $('[data-name="detalle_origen_c"]').css('pointer-events', 'none');
            self.$('.record-edit-link-wrapper[data-name=detalle_origen_c]').remove();
        }
        //READONLY: ORIGEN - MARKETING  / DETALLE ORIGEN - ORGANICO
        if (this.model.get('origen_c') === '1' && this.model.get('detalle_origen_c') === '80') {
            $('[data-name="potencial_lead_c"]').css('pointer-events', 'none');
            $('[data-name="activos_interes_c"]').css('pointer-events', 'none');
            $('[data-name="mes_operacion_c"]').css('pointer-events', 'none');
            $('[data-name="potencial_cierre_c"]').css('pointer-events', 'none');
            $('[data-fieldname="prospect_cp_estados_municipios"]').css('pointer-events', 'none');
            $('[data-fieldname="prospects_clasf_sectorial"]').css('pointer-events', 'none');
        }
    },

    deshabilitaOrigen: function () {
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
            $('.record-cell[data-name="origen_c"]').find('.normal.index').find('.edit').addClass('disabled');
            $('.record-cell[data-name="origen_c"]').find('.normal.index').find('.select2-container').addClass('select2-container-disabled');
            $('.record-cell[data-name="origen_c"]').find('.normal.index').find('.select2-container').find('.select2-focusser').attr('disabled', "");
            $('.record-cell[data-name="origen_c"]').find('.normal.index').find('input[type="hidden"]').attr('disabled', "");
            $('.record-cell[data-name="origen_c"]').find('.record-edit-link-wrapper').addClass('hide');

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

            $('.record-cell[data-name="medio_digital_c"]').find('.normal.index').find('.edit').addClass('disabled');
            $('.record-cell[data-name="medio_digital_c"]').find('.normal.index').find('.select2-container').addClass('select2-container-disabled');
            $('.record-cell[data-name="medio_digital_c"]').find('.normal.index').find('.select2-container').find('.select2-focusser').attr('disabled', "");
            $('.record-cell[data-name="medio_digital_c"]').find('.normal.index').find('input[type="hidden"]').attr('disabled', "");
            $('.record-cell[data-name="medio_digital_c"]').find('.record-edit-link-wrapper').addClass('hide');

            $('.record-cell[data-name="punto_contacto_c"]').find('.normal.index').find('.edit').addClass('disabled');
            $('.record-cell[data-name="punto_contacto_c"]').find('.normal.index').find('.select2-container').addClass('select2-container-disabled');
            $('.record-cell[data-name="punto_contacto_c"]').find('.normal.index').find('.select2-container').find('.select2-focusser').attr('disabled', "");
            $('.record-cell[data-name="punto_contacto_c"]').find('.normal.index').find('input[type="hidden"]').attr('disabled', "");
            $('.record-cell[data-name="punto_contacto_c"]').find('.record-edit-link-wrapper').addClass('hide');

            $('[data-name="evento_c"]').css({ "pointer-events": "none" });
            $('[data-name="camara_c"]').css({ "pointer-events": "none" });
            $('[data-name="promotor_c"]').css({ "pointer-events": "none" });
            $('[data-name="codigo_expo_c"]').css({ "pointer-events": "none" });
            $('.record-cell[data-name="codigo_expo_c"]').find('.record-edit-link-wrapper').addClass('hide');
        }
    },

    clickActionsCambiaEtiquetaEnvioCorreo: function () {

        if (!this.model.get('envio_correo_po_c')) {
            $('[data-event="button:reenvio_correo:click"]').html("Enviar correo");
        } else {
            $('[data-event="button:reenvio_correo:click"]').html("Reenviar correo");
        }

    },

    //Función para eliminar opciones del campo origen
    estableceOpcionesOrigenLeads: function () {
        var opciones_origen = app.lang.getAppListStrings('origen_lead_list');
        var opciones_detalle_origen = app.lang.getAppListStrings('detalle_origen_list');
        var permisosGestionTeamLeader = App.user.attributes.gestion_team_leaders_c || ""; //OBTIENE EL PERMISO KONNECT, VENDORS
        var valorDetalleActual = this.model.get('detalle_origen_c');

        // Función auxiliar para filtrar opciones
        var filtrarOpciones = function (opciones, listaPermitida, valorActual) {
            // Agregar valor actual si no está en listaPermitida pero sí en las opciones originales
            if (valorActual && opciones.hasOwnProperty(valorActual) && !listaPermitida.includes(valorActual)) {
                listaPermitida = listaPermitida.concat([valorActual]);
            }

            Object.keys(opciones).forEach(function (key) {
                if (!listaPermitida.includes(key)) {
                    delete opciones[key];
                }
            });
            return opciones; // Retorna el objeto filtrado
        };
        // Función reutilizable para actualizar las opciones y el valor del campo detalle_origen_c
        var actualizarCampoDetalleOrigen = function (opciones_detalle_origen, nuevoValor) {
            // Forzamos la actualización de las opciones en la vista
            var field = this.getField("detalle_origen_c");
            if (field) {
                field.items = opciones_detalle_origen;  // Actualiza la lista de valores del dropdown
                field.render();  // Vuelve a pintar el campo
            }
            // Borramos el valor actual y asignamos el nuevo
            var valorActual = this.model.get('detalle_origen_c');
            // Si el valor actual no está en las opciones permitidas, lo actualizamos
            if (!opciones_detalle_origen.hasOwnProperty(valorActual)) {
                // this.model.unset('detalle_origen_c'); // Eliminamos solo si el valor no es válido
                this.model.set('detalle_origen_c', nuevoValor);
            }
        };
        // Función reutilizable para actualizar las opciones y el valor del campo origen_c
        var actualizarCampoOrigen = function (opciones_origen, nuevoValor) {
            // Forzamos la actualización de las opciones en la vista
            var field = this.getField("origen_c");
            if (field) {
                field.items = opciones_origen;  // Actualiza la lista de valores del dropdown
                field.render();  // Vuelve a pintar el campo
            }
            // Borramos el valor actual y asignamos el nuevo
            var valorActual = this.model.get('origen_c');
            // Si el valor actual no está en las opciones permitidas, lo actualizamos
            if (!opciones_origen.hasOwnProperty(valorActual)) {
                this.model.unset('origen_c'); // Eliminamos solo si el valor no es válido
                this.model.set('origen_c', nuevoValor);
            }
        };
        //Valida si es Origen Alianza
        if (this.model.get('origen_c') === '12') {
            console.log("ORIGEN ALIANZA");
            if (App.user.attributes.define_origen_po_c || App.user.attributes.gestion_utility_trailers_po_c || permisosGestionTeamLeader.includes("^konnect^") || permisosGestionTeamLeader.includes("^vendors^")) {
                //Define opciones de origen
                opciones_origen = filtrarOpciones(opciones_origen, ["12", "20"]); //12:Alianzas - 20:Leasing
                this.model.fields['origen_c'].options = opciones_origen;
                actualizarCampoOrigen.call(this, opciones_origen, '12');
                //Define opciones de detalle origen                
                if (App.user.attributes.define_origen_po_c && App.user.attributes.gestion_utility_trailers_po_c && permisosGestionTeamLeader.includes("^konnect^") && permisosGestionTeamLeader.includes("^vendors^") && this.model.get('origen_c') == '12') {
                    opciones_detalle_origen = filtrarOpciones(opciones_detalle_origen, ["12", "13", "114", "115", "116"]); //12:SOC - 13:Creditaria - 114:Utility Trailers - 115:Konnect - 116:Vendors
                    this.model.fields['detalle_origen_c'].options = opciones_detalle_origen;
                    //Forzamos la actualización de las opciones en la vista
                    actualizarCampoDetalleOrigen.call(this, opciones_detalle_origen, '12');

                } else if (App.user.attributes.define_origen_po_c && App.user.attributes.gestion_utility_trailers_po_c && this.model.get('origen_c') == '12') {
                    opciones_detalle_origen = filtrarOpciones(opciones_detalle_origen, ["12", "13", "114"], valorDetalleActual); //12:SOC - 13:Creditaria - 114:Utility Trailers
                    this.model.fields['detalle_origen_c'].options = opciones_detalle_origen;
                    //Forzamos la actualización de las opciones en la vista
                    actualizarCampoDetalleOrigen.call(this, opciones_detalle_origen, valorDetalleActual || '12');

                } else if (App.user.attributes.gestion_utility_trailers_po_c && permisosGestionTeamLeader.includes("^konnect^") && this.model.get('origen_c') == '12') {
                    opciones_detalle_origen = filtrarOpciones(opciones_detalle_origen, ["114", "115"], valorDetalleActual); //114:Utility Trailers - 115:Konnect
                    this.model.fields['detalle_origen_c'].options = opciones_detalle_origen;
                    //Forzamos la actualización de las opciones en la vista
                    actualizarCampoDetalleOrigen.call(this, opciones_detalle_origen, valorDetalleActual || '114');

                } else if (App.user.attributes.gestion_utility_trailers_po_c && permisosGestionTeamLeader.includes("^vendors^") && this.model.get('origen_c') == '12') {
                    opciones_detalle_origen = filtrarOpciones(opciones_detalle_origen, ["114", "116"], valorDetalleActual); //114:Utility Trailers - 116:Vendors
                    this.model.fields['detalle_origen_c'].options = opciones_detalle_origen;
                    //Forzamos la actualización de las opciones en la vista
                    actualizarCampoDetalleOrigen.call(this, opciones_detalle_origen, valorDetalleActual || '114');

                } else if (App.user.attributes.define_origen_po_c && permisosGestionTeamLeader.includes("^konnect^") && this.model.get('origen_c') == '12') {
                    opciones_detalle_origen = filtrarOpciones(opciones_detalle_origen, ["12", "13", "115"], valorDetalleActual); //12:SOC - 13:Creditaria - 115:Konnect
                    this.model.fields['detalle_origen_c'].options = opciones_detalle_origen;
                    //Forzamos la actualización de las opciones en la vista
                    actualizarCampoDetalleOrigen.call(this, opciones_detalle_origen, valorDetalleActual || '12');

                } else if (App.user.attributes.define_origen_po_c && permisosGestionTeamLeader.includes("^vendors^") && this.model.get('origen_c') == '12') {
                    opciones_detalle_origen = filtrarOpciones(opciones_detalle_origen, ["12", "13", "116"], valorDetalleActual); //12:SOC - 13:Creditaria - 116:Vendors
                    this.model.fields['detalle_origen_c'].options = opciones_detalle_origen;
                    //Forzamos la actualización de las opciones en la vista
                    actualizarCampoDetalleOrigen.call(this, opciones_detalle_origen, valorDetalleActual || '12');

                } else if (permisosGestionTeamLeader.includes("^konnect^") && permisosGestionTeamLeader.includes("^vendors^") && this.model.get('origen_c') == '12') {
                    opciones_detalle_origen = filtrarOpciones(opciones_detalle_origen, ["115", "116"], valorDetalleActual); //115:Konnect - 116:Vendors
                    this.model.fields['detalle_origen_c'].options = opciones_detalle_origen;
                    //Forzamos la actualización de las opciones en la vista
                    actualizarCampoDetalleOrigen.call(this, opciones_detalle_origen, valorDetalleActual || '115');

                } else if (App.user.attributes.define_origen_po_c && this.model.get('origen_c') == '12') {
                    opciones_detalle_origen = filtrarOpciones(opciones_detalle_origen, ["12", "13"], valorDetalleActual); //12:SOC - 13:Creditaria
                    this.model.fields['detalle_origen_c'].options = opciones_detalle_origen;
                    //Forzamos la actualización de las opciones en la vista
                    actualizarCampoDetalleOrigen.call(this, opciones_detalle_origen, valorDetalleActual || '12');

                } else if (App.user.attributes.gestion_utility_trailers_po_c && this.model.get('origen_c') == '12') {
                    opciones_detalle_origen = filtrarOpciones(opciones_detalle_origen, ["114"], valorDetalleActual); //114:Utility Trailers
                    this.model.fields['detalle_origen_c'].options = opciones_detalle_origen;
                    //Forzamos la actualización de las opciones en la vista
                    actualizarCampoDetalleOrigen.call(this, opciones_detalle_origen, valorDetalleActual || '114');

                } else if (permisosGestionTeamLeader.includes("^konnect^") && this.model.get('origen_c') == '12') {
                    opciones_detalle_origen = filtrarOpciones(opciones_detalle_origen, ["115"], valorDetalleActual); //115:Konnect
                    this.model.fields['detalle_origen_c'].options = opciones_detalle_origen;
                    //Forzamos la actualización de las opciones en la vista
                    actualizarCampoDetalleOrigen.call(this, opciones_detalle_origen, valorDetalleActual || '115');

                } else if (permisosGestionTeamLeader.includes("^vendors^") && this.model.get('origen_c') == '12') {
                    opciones_detalle_origen = filtrarOpciones(opciones_detalle_origen, ["116"], valorDetalleActual); //116:Vendors
                    this.model.fields['detalle_origen_c'].options = opciones_detalle_origen;
                    //Forzamos la actualización de las opciones en la vista
                    actualizarCampoDetalleOrigen.call(this, opciones_detalle_origen, valorDetalleActual || '116');
                }
                //Disparar eventos para forzar la actualización
                this.model.trigger("change:detalle_origen_c");
            } else {
                console.log("SIN PERMISOS - ALIANZA");
                //12:SOC - 13:Creditaria - 114:Utility Trailers - 115:Konnect - 116:Vendors
                opciones_detalle_origen = filtrarOpciones(opciones_detalle_origen, ["12", "13", "114", "115", "116"]);
                this.model.fields['detalle_origen_c'].options = opciones_detalle_origen;
                //Forzamos la actualización de las opciones en la vista
                actualizarCampoDetalleOrigen.call(this, opciones_detalle_origen, '12');
            }

        } else if (this.model.get('origen_c') === '1') {
            console.log("ORIGEN MARKETING");
            //Define opciones de detalle origen
            var arrayDetalleOrigen = ['', '3', '9', '5', '11', '70', '71', '72', '73', '74', '75', '76', '77', '78', '79', '80', '81', '82', '83', '84', '85', '104', '105', '106', '107', '108', '109', '110'];
            opciones_detalle_origen = filtrarOpciones(opciones_detalle_origen, arrayDetalleOrigen);
            this.model.fields['detalle_origen_c'].options = opciones_detalle_origen;
            //Forzamos la actualización de las opciones en la vista
            actualizarCampoDetalleOrigen.call(this, opciones_detalle_origen, '80'); //80: Organico

        } else if (this.model.get('origen_c') === '13') {
            console.log("ORIGEN CENTRO DE PROSPECCION");
            //Define opciones de detalle origen
            opciones_detalle_origen = filtrarOpciones(opciones_detalle_origen, ['63', '64']);
            this.model.fields['detalle_origen_c'].options = opciones_detalle_origen;
            //Forzamos la actualización de las opciones en la vista
            actualizarCampoDetalleOrigen.call(this, opciones_detalle_origen, '63'); //80: Prospección Propia

        } else if (this.model.get('origen_c') === '14') {
            console.log("ORIGEN CLOSER");
            //Define opciones de detalle origen
            opciones_detalle_origen = filtrarOpciones(opciones_detalle_origen, ['65']);
            this.model.fields['detalle_origen_c'].options = opciones_detalle_origen;
            //Forzamos la actualización de las opciones en la vista
            actualizarCampoDetalleOrigen.call(this, opciones_detalle_origen, '65'); //65: Closer

        } else if (this.model.get('origen_c') === '15') {
            console.log("ORIGEN GROWTH");
            //Define opciones de detalle origen
            opciones_detalle_origen = filtrarOpciones(opciones_detalle_origen, ['66', '67', '68', '69']);
            this.model.fields['detalle_origen_c'].options = opciones_detalle_origen;
            //Forzamos la actualización de las opciones en la vista
            actualizarCampoDetalleOrigen.call(this, opciones_detalle_origen, '66'); //66: Campañas de Growth

        } else {
            //Define opciones de origen
            opciones_origen = filtrarOpciones(opciones_origen, ["12", "20", "1"]); //12:Alianzas - 20:Leasing - 1:Marketing
            this.model.fields['origen_c'].options = opciones_origen;
            // Define opciones de detalle origen
            opciones_detalle_origen = filtrarOpciones(opciones_detalle_origen, ["113"]);
            this.model.fields['detalle_origen_c'].options = opciones_detalle_origen;
            // SET ORIGEN Y DETALLE ORIGEN LEASING - LEASING
            this.model.set('origen_c', '20');
            actualizarCampoDetalleOrigen.call(this, opciones_detalle_origen, '113');
        }
    },

    editClicked: function () {
        this._super("editClicked");
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
                messages: '<b>El campo no acepta Caracteres Especiales.</b>'
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
                messages: '<b>El campo no acepta caracteres especiales.</b>'
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

    _render: function (options) {
        this._super("_render");
        this.$("[data-panelname='LBL_RECORDVIEW_PANEL3']").hide();
        this.$(".record-cell[data-name='blank_space']").hide();
        $('[data-name="contacto_asociado_c"]').attr('style', 'pointer-events:none');
        //Ocultando campo de control que omite validación de duplicados
        $('[data-name="excluye_campana_c"]').hide();
        //Oculta etiqueta de prospects_direcciones
        this.$("div.record-label[data-name='prospects_direcciones']").attr('style', 'display:none;');
        //Ocultando campo check de homonimo
        $('[data-name="homonimo_c"]').hide();
        //Oculta fecha de bloqueo
        $('[data-name="fecha_bloqueo_origen_c"]').hide();
    },

    convert_Po_to_Lead: function () {
        self = this;
        var filter_arguments = {
            "id": this.model.get('id')
        };
        // alert(this.model.get('id'))
        this.valida_requeridos();
        var btnConvert = this.getField("convert_po_to_Lead");
        btnConvert.hide();
        var editButton = this.getField('edit_button');
        editButton.setDisabled(true);
        app.alert.show('upload', { level: 'process', title: 'LBL_LOADING', autoclose: false });
        app.api.call("create", app.api.buildURL("existsPOLeads", null, null, filter_arguments), null, {
            success: _.bind(function (data) {
                console.log(data);
                app.alert.dismiss('upload');
                app.controller.context.reloadData({});
                editButton.setDisabled(false);
                if (data.idCuenta === "" || data.idCuenta == null) {
                    app.alert.show("Conversión", {
                        level: "error",
                        messages: data.mensaje,
                        autoClose: false
                    });
                } else {
                    app.alert.show("Conversión", {
                        level: "success",
                        messages: data.mensaje,
                        autoClose: false
                    });
                    this._disableActionsSubpanel();
                    var btnConvert = this.getField("convert_po_to_Lead");
                    btnConvert.hide();
                    self.model.set('estatus_po_c', '3');
                    self.model.set('lead_id', data.idCuenta);
                    self.model.save();
                }
                var btnConvert = this.getField("convert_po_to_Lead")

                if (this.model.get('estatus_po_c') == '2') {
                    btnConvert.show();
                } else {
                    btnConvert.hide();
                }
                self.render();

            }, this),
            failure: _.bind(function (data) {
                app.alert.dismiss('upload');

            }, this),
            error: _.bind(function (data) {
                app.alert.dismiss('upload');

            }, this)
        });

    },

    bindDataChange: function () {
        this._super("bindDataChange");
        //Si el registro es Persona Fisica, ya no se podra cambiar a Persona Moral
        this.model.on("change:regimen_fiscal_c", _.bind(function () {

            if (this.model._previousAttributes.regimen_fiscal_c == '1') {
                if (this.model.get('regimen_fiscal_c') == '3') {
                    this.model.set('regimen_fiscal_c', '1');
                }
            }
            if (this.model._previousAttributes.regimen_fiscal_c == '2') {
                if (this.model.get('regimen_fiscal_c') == '3') {
                    this.model.set('regimen_fiscal_c', '2');
                }
            }
            //Si es Persona Moral, ya no se podra cambiar a Persona Fisica
            if (this.model._previousAttributes.regimen_fiscal_c == '3') {
                if (this.model.get('regimen_fiscal_c') == '1' || this.model.get('regimen_fiscal_c') == '2') {
                    this.model.set('regimen_fiscal_c', '3');
                }
            }
            if (this.model._previousAttributes.regimen_fiscal_c != '0') {
                if (this.model.get('regimen_fiscal_c') == '0' || this.model.get('regimen_fiscal_c') == "") {
                    this.model.set('regimen_fiscal_c', this.model._previousAttributes.regimen_fiscal_c);
                }
            }
        }, this));
    },

    llamar_movil: function () {
        var tel_client = this.model.get('phone_mobile');
        this.llamar_vicidial(tel_client);
    },

    llamar_casa: function () {
        var tel_client = this.model.get('phone_home');
        this.llamar_vicidial(tel_client);
    },

    llamar_trabajo: function () {
        var tel_client = this.model.get('phone_work');
        this.llamar_vicidial(tel_client);
    },

    llamar_vicidial: function (tel_client) {
        var tel_usr = app.user.attributes.ext_c;
        var prospectid = this.model.get('id');
        vicidial = app.config.vicidial + '?exten=SIP/' + tel_usr + '&number=' + tel_client;
        _.extend(this, vicidial);
        if (tel_usr != '' || tel_usr != null) {
            if (tel_client != '' || tel_client != null) {
                context = this;
                app.alert.show('do-call', {
                    level: 'confirmation',
                    messages: '¿Realmente quieres realizar la llamada? <br><br><b>NOTA: La marcaci\u00F3n se realizar\u00E1 tal cual el n\u00FAmero est\u00E1 registrado</b>',
                    autoClose: false,
                    onConfirm: function () {
                        context.createcall(context.resultCallback);
                    },
                });
            } else {
                app.alert.show('error_tel_client', {
                    level: 'error',
                    autoClose: true,
                    messages: 'El cliente al que quieres llamar no tiene <b>N\u00FAmero telefonico</b>.'
                });
            }
        } else {
            app.alert.show('error_tel_usr', {
                level: 'error',
                autoClose: true,
                messages: 'El usuario con el que estas logueado no tiene <b>Extensi\u00F3n</b>.'
            });
        }
    },

    createcall: function (callback) {
        self = this;
        var id_call = '';
        var name_client = this.model.get('name');
        var id_client = this.model.get('id');
        var modulo = 'Prospects';
        var posiciones = app.user.attributes.posicion_operativa_c;
        var posicion = '';
        if (posiciones.includes(3)) posicion = 'Ventas';
        if (posiciones.includes(4)) posicion = 'Staff';
        var Params = [id_client, name_client, modulo, posicion];
        app.api.call('create', app.api.buildURL('createcall'), { data: Params }, {
            success: _.bind(function (data) {
                id_call = data;
                console.log('Llamada creada, id: ' + id_call);
                app.alert.show('message-to', {
                    level: 'info',
                    messages: 'Usted está llamando a ' + name_client,
                    autoClose: true
                });
                callback(id_call, self);
            }, this),
        });
    },

    resultCallback: function (id_call, context) {
        self = context;
        vicidial += '&prospectid=' + id_call;
        $.ajax({
            cache: false,
            type: "get",
            url: vicidial,
        });
    },

    siNumero: function () {
        if (!this.model.get('phone_mobile')) $('.llamada_mobile').hide();
        if (!this.model.get('phone_home')) $('.llamada_home').hide();
        if (!this.model.get('phone_work')) $('.llamada_work').hide();
    },

    noLlamar: function () {
        $('.llamada_mobile').hide();
        $('.llamada_home').hide();
        $('.llamada_work').hide();
    },

    _hideBtnReset: function () {
        var btnReset = this.getField("reset_lead");
        var check_resetLead = app.user.attributes.reset_leadcancel_c;
        var motivoCancel = this.model.get('motivo_cancelacion_c');


        if (btnReset) {
            btnReset.listenTo(btnReset, "render", function () {

                if (this.model.get('subtipo_registro_c') == '3' && check_resetLead && (motivoCancel == '3' || motivoCancel == '4')) {
                    btnReset.show();
                } else {
                    btnReset.hide();
                }
            });
        }
    },

    reset_lead: function () {
        reset = this;
        var id = this.model.get('id');
        reset.model.set("subtipo_registro_c", "1");
        reset.model.set("lead_cancelado_c", false);
        reset.model.set("motivo_cancelacion_c", "");
        reset.model.save();
        this._render();

    },

    _checkContactoAsociado: function () {

        if (this.model.get("leads_leads_1_right").id != "" && this.model.get("leads_leads_1_right").id != null) {
            // console.log("Activa check Contacto asociado");
            this.model.set('contacto_asociado_c', true);

        } else {
            // console.log("Desactiva check Contacto asociado");
            this.model.set('contacto_asociado_c', false);
        }
    },

    handleCancel: function () {
        this._super("handleCancel");
        window.cancel = 1;
        //Valores Previos Clasificacion Sectorial - Actividad Economica e INEGI
        clasf_sectorial.ActividadEconomica = app.utils.deepCopy(clasf_sectorial.prevActEconomica);
        clasf_sectorial.ResumenCliente.inegi.inegi_clase = clasf_sectorial.prevActEconomica.inegi_clase;
        clasf_sectorial.ResumenCliente.inegi.inegi_subrama = clasf_sectorial.prevActEconomica.inegi_subrama;
        clasf_sectorial.ResumenCliente.inegi.inegi_rama = clasf_sectorial.prevActEconomica.inegi_rama;
        clasf_sectorial.ResumenCliente.inegi.inegi_subsector = clasf_sectorial.prevActEconomica.inegi_subsector;
        clasf_sectorial.ResumenCliente.inegi.inegi_sector = clasf_sectorial.prevActEconomica.inegi_sector;
        clasf_sectorial.ResumenCliente.inegi.inegi_macro = clasf_sectorial.prevActEconomica.inegi_macro;
        clasf_sectorial.render();
        //Direcciones
        var prospects_direcciones = app.utils.deepCopy(this.prev_oDirecciones.prev_direccion);
        this.model.set('prospects_direcciones', prospects_direcciones);
        this.oDirecciones.direccion = prospects_direcciones;
        prospect_dir.nuevaDireccion = prospect_dir.limpiaNuevaDireccion();
        prospect_dir.render();
    },

    reenvio_correo: function () {
        var campos = "";
        var flagCorreoValido = false;
        /*
          Valida campos requeridos           
        */
        if (this.model.get('phone_mobile').trim() == '' || this.model.get('phone_mobile').trim().length != 10) {
            campos = campos + '<b>' + 'Teléfono celular' + '</b><br>';
        }
        if (this.model.get('email')[0] == undefined || this.model.get('email')[0].email_address == '') {
            campos = campos + '<b>' + 'Correo electrónico' + '</b><br>';
        }
        if (this.model.get('origen_c') == '12' && (this.model.get('detalle_origen_c') == '12' || this.model.get('detalle_origen_c') == '13' || this.model.get('detalle_origen_c') == '114' || this.model.get('detalle_origen_c') == '115')) {
            //VALIDA CAMPOS DE ALIANZA
            if (this.model.get('franquicia_c') === null || this.model.get('franquicia_c') === "") {
                campos = campos + '<b>' + 'Franquicia' + '</b><br>';
            }
            if (this.model.get('asesor_alianza_c') === null || this.model.get('asesor_alianza_c') === "") {
                campos = campos + '<b>' + 'Asesor de la Alianza' + '</b><br>';
            }
            if (this.model.get('telefono_aa_c') === null || this.model.get('telefono_aa_c') === "") {
                campos = campos + '<b>' + 'Teléfono del Asesor de Alianza' + '</b><br>';
            }
            if (this.model.get('email_aa_c') === null || this.model.get('email_aa_c') === "") {
                campos = campos + '<b>' + 'Email del Asesor de Alianza' + '</b><br>';
            } else {
                var inputEAA = this.model.get('email_aa_c'); // Obtenemos el email
                var expresionEAA = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Expresión regular válida para emails

                if (!expresionEAA.test(inputEAA)) {
                    // Si el formato del email no es válido
                    flagCorreoValido = true;
                }
            }
        }

        if (campos || flagCorreoValido) {
            //ALERTAS DE CAMPOS REQUERIDOS
            if (campos) {
                app.alert.show("campos_requeridos_email", {
                    level: "error",
                    messages: "Hace falta completar la siguiente información para <b>Enviar/Reenviar</b> correo: " + campos,
                    autoClose: false
                });
            }
            if (flagCorreoValido) {
                app.alert.show('Error_validar_email_aa', {
                    level: 'error',
                    autoClose: false,
                    messages: 'Se requiere un <b>Email del Asesor de Alianza</b> válido para el PO.'
                });
            }
            return false;
        }

        var id_prospecto = this.model.get('id');
        var buttonReenvio = this.getField('reenvio_correo');
        buttonReenvio.setDisabled(true);

        app.alert.show('envio_correo', {
            level: 'process',
            title: 'Enviando correo',
        });

        app.api.call('GET', app.api.buildURL('SendEmailPO/' + id_prospecto), null, {
            success: _.bind(function (response) {
                var buttonReenvio = this.getField('reenvio_correo');
                buttonReenvio.setDisabled(false);
                app.alert.dismiss('envio_correo');

                app.alert.show('alert_reenvio_correo', {
                    level: 'success',
                    messages: response,
                });

            }, this),
        });
    },

    solicta_cambio_origen: function () {
        /*
          Valida que tenga origen bloqueado
        */
        if (this.model.get("estatus_po_c") == '3') {
            app.alert.show('convertido_po', {
                level: 'error',
                autoClose: false,
                messages: 'No puedes solicitar cambio en un PO convertido'
            });
            return false;
        }

        var permisosGestionTeamLeader = App.user.attributes.gestion_team_leaders_c || ""; //OBTIENE EL PERMISO KONNECT, VENDORS
        if (!App.user.attributes.define_origen_po_c && !permisosGestionTeamLeader.includes("^konnect^") && !permisosGestionTeamLeader.includes("^vendors^")) {
            app.alert.show('not_access', {
                level: 'error',
                autoClose: false,
                messages: 'No cuentas con permiso para solicitar cambiar el origen de un PO'
            });
            return false;
        }

        if (!this.model.get('origen_bloqueado_c') && this.model.get('origen_c') == '') {
            app.alert.show('sin_bloqueo', {
                level: 'error',
                autoClose: false,
                messages: 'Este PO no tiene bloqueada la edición de origen'
            });
            return false;
        }
        if (this.model.get('origen_bloqueado_c') && this.model.get('aprueba_cambio_origen_c') == 'SOLICITAR') {
            app.alert.show('en_proceso', {
                level: 'error',
                autoClose: false,
                messages: 'Ya se ha generado una solicitud de edición'
            });
            return false;
        }

        var id_prospecto = this.model.get('id');
        var botonOrigen = this.getField('cambiar_origen');
        botonOrigen.setDisabled(true);
        //Actualiza bean y guarda
        this.model.set('origen_bloqueado_c', true);
        this.model.set('aprueba_cambio_origen_c', 'Solicitar');
        this.model.save();

        app.alert.show('proceso_solicitud', {
            level: 'process',
            title: 'Enviando correo',
        });

        var args = {
            "id_po": this.model.get('id')
        };
        app.api.call("create", app.api.buildURL("solicitaEdicionOrigenPO", null, null, args), null, {
            success: _.bind(function (response) {
                app.alert.dismiss('proceso_solicitud');
                botonOrigen.setDisabled(true);
                app.alert.show('alert_reenvio_correo', {
                    level: 'success',
                    messages: response['description'],
                });
            }, this),
        });
    },

    vobo_envio_correo: function () {
        app.alert.show("aprueba_envio", {
            level: "confirmation",
            messages: "Está a punto de aprobar la operación, ¿Desea confirmar el envío del correo?",
            autoClose: false,
            onConfirm: function () {
                var id_prospecto = App.controller.context.get('model').id;

                $('a[name="rechaza_envio_correo"]').attr('disabled', "disabled");
                $('a[name="vobo_envio_correo"]').attr('disabled', "disabled");
                $('a[name="rechaza_envio_correo"]').attr('style', "pointer-events:none");
                $('a[name="vobo_envio_correo"]').attr('style', "pointer-events:none");

                app.alert.show('envio_correo_vobo', {
                    level: 'process',
                    title: 'Enviando correo',
                });

                app.api.call('GET', app.api.buildURL('AutorizaEnvioPO/' + id_prospecto), null, {
                    success: _.bind(function (response) {

                        $('a[name="rechaza_envio_correo"]').removeAttr('disabled');
                        $('a[name="vobo_envio_correo"]').removeAttr('disabled');
                        $('a[name="rechaza_envio_correo"]').attr('style', "");
                        $('a[name="vobo_envio_correo"]').attr('style', "");

                        app.alert.dismiss('envio_correo_vobo');

                        app.alert.show('alert_reenvio_correo', {
                            level: 'success',
                            messages: response,
                        });

                        //Recarga modelo para actualizar las banderas reseteadas en el api
                        App.controller.context.attributes.model.fetch();

                        $('[name="rechaza_envio_correo"]').addClass('hidden');
                        $('[name="vobo_envio_correo"]').addClass('hidden');

                    }, this),
                });

            },
            onCancel: function () {
                //alert("Cancelled!");
            }
        });
    },

    rechaza_envio_correo: function () {
        app.alert.show("rechaza_envio", {
            level: "confirmation",
            messages: "Está a punto de cancelar la operación, ¿Desea rechazar el envío del correo?",
            autoClose: false,
            onConfirm: function () {
                var id_prospecto = App.controller.context.get('model').id;

                $('a[name="rechaza_envio_correo"]').attr('disabled', "disabled");
                $('a[name="vobo_envio_correo"]').attr('disabled', "disabled");
                $('a[name="rechaza_envio_correo"]').attr('style', "pointer-events:none");
                $('a[name="vobo_envio_correo"]').attr('style', "pointer-events:none");

                app.alert.show('envio_correo_rechazo', {
                    level: 'process',
                    title: 'Rechazando operación',
                });

                app.api.call('GET', app.api.buildURL('RechazaEnvioPO/' + id_prospecto), null, {
                    success: _.bind(function (response) {

                        $('a[name="rechaza_envio_correo"]').removeAttr('disabled');
                        $('a[name="vobo_envio_correo"]').removeAttr('disabled');
                        $('a[name="rechaza_envio_correo"]').attr('style', "");
                        $('a[name="vobo_envio_correo"]').attr('style', "");

                        app.alert.dismiss('envio_correo_rechazo');

                        app.alert.show('alert_reenvio_correo', {
                            level: 'success',
                            messages: response,
                        });

                        //Recarga modelo para actualizar las banderas reseteadas en el api
                        App.controller.context.attributes.model.fetch();

                        $('[name="rechaza_envio_correo"]').addClass('hidden');
                        $('[name="vobo_envio_correo"]').addClass('hidden');

                    }, this),
                });

            },
            onCancel: function () {
                //alert("Cancelled!");
            }
        });

    },

    get_addresses: function () {

        this.oDirecciones = [];
        this.oDirecciones.direccion = [];
        this.prev_oDirecciones = [];
        this.prev_oDirecciones.prev_direccion = [];

        //Define variables
        var listMapTipo = App.lang.getAppListStrings('tipo_dir_map_list');
        var listTipo = App.lang.getAppListStrings('dir_tipo_unique_list');
        var listMapIndicador = App.lang.getAppListStrings('dir_indicador_map_list');
        var listIndicador = App.lang.getAppListStrings('dir_indicador_unique_list');
        var idProspect = this.model.get('id');

        //Recupera información
        if (!_.isEmpty(idProspect) && idProspect != "") {
            app.api.call('GET', app.api.buildURL('Prospects/' + idProspect + '/link/prospects_dire_direccion_1'), null, {
                success: function (data) {
                    //Itera y agrega direcciones
                    for (var i = 0; i < data.records.length; i++) {
                        //Asignando valores de los campos
                        var tipo = data.records[i].tipodedireccion.toString();
                        var tipoSeleccionados = '^' + listMapIndicador[tipo].replace(/,/gi, "^,^") + '^';
                        var indicador = data.records[i].indicador;
                        var indicadorSeleccionados = '^' + listMapIndicador[indicador].replace(/,/gi, "^,^") + '^';
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
                        var calle = data.records[i].calle;
                        var numExt = data.records[i].numext;
                        var numInt = data.records[i].numint;
                        var principal = (data.records[i].principal == true) ? 1 : 0;
                        var inactivo = (data.records[i].inactivo == true) ? 1 : 0;
                        var secuencia = data.records[i].secuencia;
                        var idDireccion = data.records[i].id;
                        var direccionCompleta = data.records[i].name;
                        var bloqueado = (indicadorSeleccionados.indexOf('2') != -1) ? 1 : 0;
                        // var accesoFiscal = App.user.attributes.tct_alta_clientes_chk_c + App.user.attributes.tct_altaproveedor_chk_c + App.user.attributes.tct_alta_cd_chk_c + App.user.attributes.deudor_factoraje_c;
                        // bloqueado = (self.model.get('tipo_registro_cuenta_c') == 4 || self.model.get('subtipo_registro_cuenta_c') == '') ? 0 : bloqueado;
                        // if (accesoFiscal > 0) bloqueado = 0;

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
                            "bloqueado": bloqueado
                        };

                        //Agregar dirección
                        contexto_prospect.oDirecciones.direccion.push(direccion);

                        //recupera información asociada a CP
                        var strUrl = 'DireccionesCP/' + valCodigoPostal + '/' + i;
                        app.api.call('GET', app.api.buildURL(strUrl), null, {
                            success: _.bind(function (data) {
                                //recupera info
                                var list_paises = data.paises;
                                var list_municipios = data.municipios;
                                var city_list = App.metadata.getCities();
                                var list_ciudades = data.ciudades;
                                var list_estados = data.estados;
                                var list_colonias = data.colonias;
                                //Poarsea valores para listas
                                //País
                                listPais = {};
                                for (var i = 0; i < list_paises.length; i++) {
                                    listPais[list_paises[i].idPais] = list_paises[i].namePais;
                                }
                                contexto_prospect.oDirecciones.direccion[data.indice].listPais = listPais;
                                contexto_prospect.oDirecciones.direccion[data.indice].listPaisFull = listPais;
                                //Municipio
                                listMunicipio = {};
                                for (var i = 0; i < list_municipios.length; i++) {
                                    listMunicipio[list_municipios[i].idMunicipio] = list_municipios[i].nameMunicipio;
                                }
                                contexto_prospect.oDirecciones.direccion[data.indice].listMunicipio = listMunicipio;
                                contexto_prospect.oDirecciones.direccion[data.indice].listMunicipioFull = listMunicipio;
                                //Estado
                                listEstado = {};
                                for (var i = 0; i < list_estados.length; i++) {
                                    listEstado[list_estados[i].idEstado] = list_estados[i].nameEstado;
                                }
                                contexto_prospect.oDirecciones.direccion[data.indice].listEstado = listEstado;
                                contexto_prospect.oDirecciones.direccion[data.indice].listEstadoFull = listEstado;
                                //Colonia
                                listColonia = {};
                                for (var i = 0; i < list_colonias.length; i++) {
                                    listColonia[list_colonias[i].idColonia] = list_colonias[i].nameColonia;
                                }
                                contexto_prospect.oDirecciones.direccion[data.indice].listColonia = listColonia;
                                contexto_prospect.oDirecciones.direccion[data.indice].listColoniaFull = listColonia;
                                //Ciudad
                                listCiudad = {}
                                ciudades = Object.values(city_list);
                                for (var [key, value] of Object.entries(contexto_prospect.oDirecciones.direccion[data.indice].listEstado)) {
                                    for (var i = 0; i < ciudades.length; i++) {
                                        if (ciudades[i].estado_id == key) {
                                            listCiudad[ciudades[i].id] = ciudades[i].name;
                                        }
                                    }
                                }
                                contexto_prospect.oDirecciones.direccion[data.indice].listCiudad = listCiudad;
                                contexto_prospect.oDirecciones.direccion[data.indice].listCiudadFull = listCiudad;

                                //Genera objeto con valores previos para control de cancelar
                                contexto_prospect.prev_oDirecciones.prev_direccion = app.utils.deepCopy(contexto_prospect.oDirecciones.direccion);
                                prospect_dir.oDirecciones = contexto_prospect.oDirecciones;

                                //Aplica render a campo custom
                                prospect_dir.render();

                            }, contexto_prospect)
                        });
                    }
                },
                error: function (e) {
                    throw e;
                }
            });
        }
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
                /*******************Refresca cambios en Direcciones******************/
                this.get_addresses();

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

    setCustomFields: function (fields, errors, callback) {
        if ($.isEmptyObject(errors)) {
            //Direcciones
            this.prev_oDirecciones.prev_direccion = app.utils.deepCopy(this.oDirecciones.direccion);
            this.model.set('prospects_direcciones', this.oDirecciones.direccion);
        }
        //Callback a validation task
        callback(null, fields, errors);
    },

    _direccionDuplicada: function (fields, errors, callback) {
        /* SE VALIDA DIRECTAMENTE DE LOS ELEMENTOS DEL HTML POR LA COMPLEJIDAD DE
         OBETENER LAS DESCRIPCIONES DE LOS COMBOS*/
        // var objDirecciones = $('.control-group.direccion');
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

                // console.log(`Comparando dirección ${keyA} con ${keyB}: duplicado =`, duplicado);

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

    /** Description: On Inline edit disable the TAB Key in order to prevent the field from going to detail mode.*/
    handleKeyDown: function (e, field) {
        if (e.which === 9) {
            if (field.name != this.model.fields.prospects_direcciones.name) {
                e.preventDefault();
                this.nextField(field, e.shiftKey ? 'prevField' : 'nextField');
                this.adjustHeaderpane();
            }
        }
    },

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
                    if (i != j && direccion[i].inactivo == 0 && direccion[j].calle.trim().toLowerCase() + direccion[j].ciudad + direccion[j].colonia + direccion[j].estado + direccion[j].municipio + direccion[j].numext.trim().toLowerCase() + direccion[j].pais + direccion[j].postal + direccion[j].inactivo == direccion[i].calle.trim().toLowerCase() + direccion[i].ciudad + direccion[i].colonia + direccion[i].estado + direccion[i].municipio + direccion[i].numext.trim().toLowerCase() + direccion[i].pais + direccion[i].postal + direccion[i].inactivo) {
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
    valida_usuarios_inactivos: function (fields, errors, callback) {
        var ids_usuarios = '';
        if (this.model.attributes.assigned_user_id) {
            ids_usuarios += this.model.attributes.assigned_user_id;
        }
        console.log("Valor del ID del asignado: ".ids_usuarios);
        ids_usuarios += ',';
        if (ids_usuarios != "") {
            //Generar petición para validación
            app.api.call('GET', app.api.buildURL('GetStatusOfUser/' + ids_usuarios + '/inactivo'), null, {
                success: _.bind(function (data) {
                    if (data.length > 0) {
                        var nombres = '';
                        //Armando lista de usuarios
                        for (var i = 0; i < data.length; i++) {
                            nombres += '<b>' + data[i].nombre_usuario + '</b><br>';
                        }
                        app.alert.show("Usuarios", {
                            level: "error",
                            messages: "No es posible guardar este registro con el siguiente usuario inactivo:<br>" + nombres,
                            autoClose: false
                        });
                        errors['usuariostatus'] = errors['usuariostatus'] || {};
                        errors['usuariostatus'].required = true;
                    }
                    callback(null, fields, errors);
                }, this)
            });
        }
        else {
            callback(null, fields, errors);
        }
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
        /*
        if(this.model.get('assigned_user_id') == idUser ){
            readonly = false;
        }
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

    muestraBotonCorreo: function () {

        var id_prospecto = this.model.get('id');

        app.api.call('GET', app.api.buildURL('GetRelatedMeetingsCallsPO/' + id_prospecto), null, {
            success: _.bind(function (response) {
                if (!response) {
                    //Oculta botón para Reenvío de correo
                    var button = this.getField('reenvio_correo');
                    button.dispose();
                }
            }, this),
        });
    },

    hideShowBtnVoBo: function () {
        var id_user = App.user.id;
        if (this.model.get('envio_correo_po_c') > 1 && this.model.get('id_director_vobo_c') == id_user) {
            $('[name="rechaza_envio_correo"]').removeClass('hidden');
            $('[name="vobo_envio_correo"]').removeClass('hidden');
        }
    },

    muestraBotonConversion: function () {
        //Oculta botón de conversión para todos los usuarios, excepto para roles: Seguros, 	Seguros - Creditaria
        var currentUserRoles = App.user.get('roles');
        var rolesSeguros = ['Seguros', 'Seguros - Creditaria'];
        var includesSeguros = [];

        for (let index = 0; index < currentUserRoles.length; index++) {
            const rol = currentUserRoles[index];

            if (rolesSeguros.includes(rol)) {
                includesSeguros.push("1");
            } else {
                includesSeguros.push("0");
            }
        }

        if (!includesSeguros.includes('1')) {
            var btnConvert = this.getField('convert_po_to_Lead');
            btnConvert.dispose();
        }
    },

    cambios_origen_SOC: function () {
        var idUser = App.user.attributes.id; //Id del usuario,
        var cambio = false;
        var valor = 0;

        if (this.model.get('alianza_soc_chk_c') != undefined) {
            valor = this.model.get('alianza_soc_chk_c');
        }

        Object.entries(App.lang.getAppListStrings('soc_usuario_list')).forEach(([key, value]) => {
            if (value == idUser) {
                cambio = true;
            }
        });

        if (this.model.get('subtipo_registro_c') != undefined && this.model.get('origen_c') != undefined && this.model.get('detalle_origen_c') != undefined) {
            if (this.model.get('subtipo_registro_c') != '4' && this.model.get('origen_c') == '12' && this.model.get('detalle_origen_c') == '12') {
                this.model.set('alianza_soc_chk_c', 1);
            } else {

                if (valor) {
                    this.model.set('alianza_soc_chk_c', valor);
                    this.cmbio_soc += 1;
                } else {
                    this.model.set('alianza_soc_chk_c', 0);
                }

                if ((this.model._previousAttributes.detalle_origen_c == 12 && this.cmbio_soc > 0) ||
                    (this.model._previousAttributes.detalle_origen_c != 12 && this.cmbio_soc > 2)) {
                    this.model.set('alianza_soc_chk_c', 0);
                }

                if ((this.model._previousAttributes.detalle_origen_c != "" &&
                    this.model._previousAttributes.detalle_origen_c != 12 && this.cmbio_soc > 0
                    && this.model.get('alianza_soc_chk_c') == 1)) {
                    this.model.set('alianza_soc_chk_c', 0);
                }

                if (!cambio) {
                    this.model.set('alianza_soc_chk_c', this.model.get('alianza_soc_chk_c'));
                }
            }
        }
    },

    change_estatus: function () {
        var prev_status = this.model.previousAttributes().estatus_po_c;
        var status = this.model.get("estatus_po_c");

        if (event.type == 'mouseup') {
            //Si nuevo valor es Convertido y valor previo es diferente a convertido regresa a estatus previo
            if (status == '3' && prev_status != '3') {
                this.model.set("estatus_po_c", prev_status);
            }
        }
    },

    _estableceMesOperacion: function () {
        // Obtener fecha actual
        var fechaActual = new Date();
        var yyyy = fechaActual.getFullYear();
        var mm = fechaActual.getMonth() + 1;
        if (mm < 10) {
            mm = '0' + mm;
        }
        // Calcular los próximos dos meses en el mismo formato 'YYYYMM'
        var proximosMeses = [];
        for (var i = 0; i < 3; i++) {
            var nuevoMes = new Date(yyyy, mm - 1 + i);
            var nuevoYYYY = nuevoMes.getFullYear();
            var nuevoMM = nuevoMes.getMonth() + 1;
            if (nuevoMM < 10) {
                nuevoMM = '0' + nuevoMM;
            }
            proximosMeses.push(parseInt(`${nuevoYYYY}${nuevoMM}`));
        }
        // Obtener lista de valores y filtrar
        var lista_mes_operacion = app.lang.getAppListStrings('mes_operacion_list');
        var nuevaLista = {};

        Object.keys(lista_mes_operacion).forEach(function (key) {
            var claveNumerica = parseInt(key); // Convertir clave a número para comparación
            if (proximosMeses.includes(claveNumerica)) {
                nuevaLista[key] = lista_mes_operacion[key]; // Conservar solo los permitidos
            }
        });
        // Actualizar opciones del campo
        this.model.fields['mes_operacion_c'].options = nuevaLista;
    },

    _validaActivoInteres: function (type, errors) {
        var activosInteres = this.model.get('activos_interes_c');
        //VALIDA QUE SOLO SEAN 3 ACTIVOS DE INTERES
        if (activosInteres && activosInteres.length > 3) {
            app.alert.show("valida_activo_interes", {
                level: "error",
                messages: "<b>Favor de seleccionar solo 3 activos de interés.</b>",
                autoClose: false
            });
            if (type === 'validateActivoInteres' && errors) {
                errors['activos_interes_c'] = errors['activos_interes_c'] || {};
                errors['activos_interes_c'].required = true;
            }
        }
    },
    _validateTaskActivoInteres: function (fields, errors, callback) {
        this._validaActivoInteres("validateActivoInteres", errors);
        callback(null, fields, errors);
    },

    _validaPotencialCierre: function (type, errors) {
        var potencialCierre = this.model.get('potencial_cierre_c');
        //Valida el potencial de cierre debe ser entre 10 y 100%
        if (potencialCierre !== null && (potencialCierre < 10 || potencialCierre > 100)) {
            app.alert.show("valida_potencial_cierre", {
                level: "error",
                messages: "<b>El potencial de cierre debe ser entre 10 y 100%.</b>",
                autoClose: false
            });
            if (type == 'validatePotencialCierre' && errors) {
                errors['potencial_cierre_c'] = errors['potencial_cierre_c'] || {};
                errors['potencial_cierre_c'].required = true;
            }
        }
    },
    _validateTaskPotencialCierre: function (fields, errors, callback) {
        this._validaPotencialCierre("validatePotencialCierre", errors);
        callback(null, fields, errors);
    },

})
