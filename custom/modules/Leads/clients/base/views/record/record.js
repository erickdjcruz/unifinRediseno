({
    extendsFrom: 'RecordView',

    initialize: function (options) {
        self = this;
        this._super("initialize", [options]);
        /** Valida genero personas fisicas y fisica con actividad empesarial **/
        this.model.addValidationTask('validaGenero', _.bind(this.validaGenero, this));

        this.model.addValidationTask('check_Requeridos', _.bind(this.valida_requeridos_min, this));
        this.model.on('sync', this._readonlyFields, this);
        this.context.on('button:convert_Lead_to_Accounts:click', this.convert_Lead_to_Accounts, this);
        this.context.on('button:cancel_button:click', this.handleCancel, this);
        this.model.on("change:lead_cancelado_c", _.bind(this._subMotivoCancelacion, this));
        //this.model.on('sync', this._hideBtnConvert, this);
        this._readonlyFields();
        this.events['keypress [name=phone_mobile]'] = 'validaSoloNumerosTel';
        this.events['keypress [name=phone_home]'] = 'validaSoloNumerosTel';
        this.events['keypress [name=phone_work]'] = 'validaSoloNumerosTel';
        this.events['keydown [name=phone_mobile]'] = 'validaSoloNumerosTel';
        this.events['keydown [name=phone_home]'] = 'validaSoloNumerosTel';
        this.events['keydown [name=phone_work]'] = 'validaSoloNumerosTel';
        this.model.addValidationTask('check_longDupTel', _.bind(this.validaLongDupTel, this));
        this.model.addValidationTask('check_TextOnly', _.bind(this.checkTextOnly, this));
        this.model.addValidationTask('change:email', _.bind(this.expmail, this));
        this.events['keydown [name=ventas_anuales_c]'] = 'checkInVentas';
        this.context.on('button:llamada_mobile:click', this.llamar_movil, this);
        this.context.on('button:llamada_home:click', this.llamar_casa, this);
        this.context.on('button:llamada_work:click', this.llamar_trabajo, this);
        this.context.on('button:edit_button:click', this.noLlamar, this);
        this.model.on('sync', this.siNumero, this);
        this.context.on('button:reset_lead:click', this.reset_lead, this);
        this.model.on('sync', this._hideBtnReset, this);
        this.model.on("change:leads_leads_1_right", _.bind(this._checkContactoAsociado, this));
        //Direcciones
        contexto_lead = this;
        this.get_addresses();
        this.model.addValidationTask('set_custom_fields', _.bind(this.setCustomFields, this));
        this.model.addValidationTask('checkEmptyFieldsDire', _.bind(this.validadirecc, this));
        this.model.addValidationTask('validate_Direccion_Duplicada', _.bind(this._direccionDuplicada, this));
        this.model.addValidationTask('valida_usuarios_inactivos',_.bind(this.valida_usuarios_inactivos, this));

        this.model.on('sync', this.seteaSubTipoLead, this);
        /****** validaciones SOC  **********/
        this.model.on("change:detalle_origen_c", _.bind(this.cambios_origen_SOC, this));
        this.model.on("change:origen_c", _.bind(this.cambios_origen_SOC, this));
        this.model.on('sync', this.userAlianzaSoc, this);
        this.cmbio_soc = 0;
        this.model.on('sync', this.muestrasubestatus, this);

        this.model.on('sync', this.cargaPipeline, this);
        this.model.on('sync', this.muestraBotonConversionLeads, this);
        //Función para eliminar opciones del campo origen
        this.estableceOpcionesOrigenLeads();
        //Clic solicitar CIEC
        this.context.on('button:solicitar_ciec:click', this.solicitar_ciec_function, this);
    },
    seteaSubTipoLead: function (){
        //realizamos copia del valor previo en subtipo de lead
        this.valorPrevio= contexto_lead.model.attributes.subtipo_registro_c;
    },

    handleEdit: function(e, cell) {
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
        this.deshabilitaOrigen();
    },

    /*
    Se sobreescribe la función de caja para poder evaluar si los campos de origen se deben de bloquear ya que a nivel de dependencoa
    no estaba tomando los diapradores para bloquear dichos campos
    */
    focusFirstInput: function() {
        var self = this;
        $(function() {
            var $element = (app.drawer && (app.drawer.count() > 0)) ?
                app.drawer._components[app.drawer.count() - 1].$el
                : app.$contentEl;
            var $firstInput = $element.find('input[type=text]').first();

            if (($firstInput.length > 0) && $firstInput.is(':visible')) {
                $firstInput.focus();
                self.setCaretToEnd($firstInput);
            }
            self.deshabilitaOrigen();
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
        $("div.record-label[data-name='lead_direcciones']").attr('style', 'display:none;');
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
                messages: 'El campo no acepta caracteres especiales.'
            });
            return false;
        }
    },

    _hideBtnConvert: function () {

        var btnConvert = this.getField("convert_Leads_button");

        if (btnConvert) {
            btnConvert.listenTo(btnConvert, "render", function () {
				var roles = app.user.attributes.roles;
				var creditaria = 0;
				for(var i=0;i<roles.length;i++)
				{
					if(roles[i]==="Seguros - Creditaria")
					{
						creditaria = 1;
					}
				}
                if (this.model.get('subtipo_registro_c') == '2' || creditaria) {
                    btnConvert.show();
                } else {
                    btnConvert.hide();
                }
            });
        }
    },

    _subMotivoCancelacion: function () {
        if(this.valorPrevio!=undefined){

            if (this.model.get('lead_cancelado_c')== true) {

                this.model.set('motivo_cancelacion_c', '');
                this.model.set('subtipo_registro_c', '3');

            }else{
                this.model.set('motivo_cancelacion_c', '');
                this.model.set('subtipo_registro_c',this.valorPrevio);
            }
        }
    },

    /*************Valida Genero *****************/
    validaGenero: function (fields, errors, callback) {
        var genero = this.model.get('genero_c');
        if ((genero == "" || genero == null) && (this.model.get('regimen_fiscal_c') == "1" ||
            this.model.get('regimen_fiscal_c') == "2")) {
            errors['genero_c'] = errors['genero_c'] || {};
            errors['genero_c'].required = true;
            callback(null, fields, errors);
        } else {
            callback(null, fields, errors);
        }
    },

    valida_requeridos_min: function (fields, errors, callback) {
        var campos = "";

        _.each(errors, function (value, key) {
            _.each(this.model.fields, function (field) {
                if (_.isEqual(field.name, key)) {
                    if (field.vname) {
                        campos = campos + '<b>' + app.lang.get(field.vname, "Leads") + '</b><br>';
                    }
                }
            }, this);
        }, this);

        /*****CHECK LEAD CANCELAR*********/
        if (this.model.get('lead_cancelado_c') == '1') {
            //Pide requerido motivo si el usuario no es del CP
            if (App.lang.getAppListStrings('puestos_vicidial_list')[App.user.attributes.puestousuario_c] == undefined && (this.model.get('motivo_cancelacion_c') == '' || this.model.get('motivo_cancelacion_c') == null) ) {

                campos = campos + '<b>' + app.lang.get("LBL_MOTIVO_CANCELACION_C", "Leads") + '</b><br>';
                errors['motivo_cancelacion_c'] = errors['motivo_cancelacion_c'] || {};
                errors['motivo_cancelacion_c'].required = true;
            }
            //Pide requerido subestatys si el usuario es del CP
            if (App.lang.getAppListStrings('puestos_vicidial_list')[App.user.attributes.puestousuario_c] != undefined && (this.model.get('subestatus_ld_c') == '' || this.model.get('subestatus_ld_c') == null) ) {

                campos = campos + '<b>' + app.lang.get("LBL_SUBESTATUS_LD_C", "Leads") + '</b><br>';
                errors['subestatus_ld_c'] = errors['subestatus_ld_c'] || {};
                errors['subestatus_ld_c'].required = true;
            }
        }

        if (campos) {
            app.alert.show("Campos Requeridos", {
                level: "error",
                messages: "Hace falta completar la siguiente información para guardar un <b>Lead: </b><br>" + campos,
                autoClose: false
            });
        }
        callback(null, fields, errors);

    },

    valida_requeridos: function () {
        var campos = "";
        var subTipoLead = this.model.get('subtipo_registro_c');
        var tipoPersona = this.model.get('regimen_fiscal_c');
        var campos_req = ['origen_c'];
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
                        campos = campos + '<b>' + app.lang.get(field.vname, "Leads") + '</b><br>';
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
        /***************************READONLY PARA SUBTIPO DE LEAD CANCELADO**************************/
        if (this.model.get('lead_cancelado_c') == '1' && this.model.get('subtipo_registro_c') == '3') {

            var editButton = self.getField('edit_button');
            editButton.setDisabled(true);

            _.each(this.model.fields, function (field) {

                if (field.name != 'origen_ag_tel_c' && field.name != 'promotor_c' && field.name != 'account_to_lead' && field.name != 'assigned_user_name' && field.name != 'email') {
                    if((field.name!='subestatus_ld_c' && field.name!='detalle_subestatus_ld_c' && App.lang.getAppListStrings('puestos_vicidial_list')[App.user.attributes.puestousuario_c] == undefined)){
                        self.noEditFields.push(field.name);
                        self.$('.record-edit-link-wrapper[data-name=' + field.name + ']').remove();
                        self.$('[data-name=' + field.name + ']').attr('style', 'pointer-events:none;');
                    }
                }
            });
            this._disableActionsSubpanel();
        }
        /***************************READONLY PARA SUBTIPO DE LEAD CONVERTIDO**************************/
        if (this.model.get('subtipo_registro_c') == '4') {
            var editButton = self.getField('edit_button');
            editButton.setDisabled(true);
      			//var btnConvert = self.getField("convert_Leads_button");
      			//btnConvert.hide();
            var noEditCampo = true;
            var pointerEvents = true;
            _.each(this.model.fields, function (field) {
                //Valida si el campo debe evitar bloqueo por pointer-events:none
                if (field.name == 'origen_ag_tel_c' || field.name == 'promotor_c' || field.name == 'account_to_lead' || field.name == 'assigned_user_name' || field.name == 'email' || field.name == 'prospects_leads_1_name') {
                    pointerEvents = false;
                }
                //Valida si el campo debe evitar bloqueo para CP
                if((field.name=='subestatus_ld_c' || field.name=='detalle_subestatus_ld_c') && App.lang.getAppListStrings('puestos_vicidial_list')[App.user.attributes.puestousuario_c] != undefined) {
                    noEditCampo = false;
                    pointerEvents = false;
                }

                //Bloquea campos
                if(noEditCampo){
                    self.noEditFields.push(field.name);
                    self.$('.record-edit-link-wrapper[data-name=' + field.name + ']').remove();
                }
                if(pointerEvents){
                    self.$('[data-name=' + field.name + ']').attr('style', 'pointer-events:none;');
                }
                noEditCampo = true;
                pointerEvents = true;
            });
            this._disableActionsSubpanel();
        }

        //Se omite función para deshabilitar origen, ya que se opta por hacerlo a través de dependencias
        this.deshabilitaOrigen();
    },

    deshabilitaOrigen:function(){
        var today = new Date();
        var yyyy = today.getFullYear();
        var mm = today.getMonth() + 1; // Months start at 0!
        var dd = today.getDate();

        if (dd < 10) dd = '0' + dd;
        if (mm < 10) mm = '0' + mm;

        var hoy = yyyy+'-'+mm+'-'+dd;
        var fecha_actual= new Date(hoy);
        var fecha_bloqueo=new Date(this.model.get("fecha_bloqueo_origen_c"));

        if(fecha_actual<=fecha_bloqueo){
            $('.record-cell[data-name="origen_c"]').find('.normal.index').find('.edit').addClass('disabled');
            $('.record-cell[data-name="origen_c"]').find('.normal.index').find('.select2-container').addClass('select2-container-disabled');
            $('.record-cell[data-name="origen_c"]').find('.normal.index').find('.select2-container').find('.select2-focusser').attr('disabled',"");
            $('.record-cell[data-name="origen_c"]').find('.normal.index').find('input[type="hidden"]').attr('disabled',"");
            $('.record-cell[data-name="origen_c"]').find('.record-edit-link-wrapper').addClass('hide');

            $('.record-cell[data-name="detalle_origen_c"]').find('.normal.index').find('.edit').addClass('disabled');
            $('.record-cell[data-name="detalle_origen_c"]').find('.normal.index').find('.select2-container').addClass('select2-container-disabled');
            $('.record-cell[data-name="detalle_origen_c"]').find('.normal.index').find('.select2-container').find('.select2-focusser').attr('disabled',"");
            $('.record-cell[data-name="detalle_origen_c"]').find('.normal.index').find('input[type="hidden"]').attr('disabled',"");
            $('.record-cell[data-name="detalle_origen_c"]').find('.record-edit-link-wrapper').addClass('hide');

            $('.record-cell[data-name="prospeccion_propia_c"]').find('.normal.index').find('.edit').addClass('disabled');
            $('.record-cell[data-name="prospeccion_propia_c"]').find('.normal.index').find('.select2-container').addClass('select2-container-disabled');
            $('.record-cell[data-name="prospeccion_propia_c"]').find('.normal.index').find('.select2-container').find('.select2-focusser').attr('disabled',"");
            $('.record-cell[data-name="prospeccion_propia_c"]').find('.normal.index').find('input[type="hidden"]').attr('disabled',"");
            $('.record-cell[data-name="prospeccion_propia_c"]').find('.record-edit-link-wrapper').addClass('hide');

            $('.record-cell[data-name="medio_digital_c"]').find('.normal.index').find('.edit').addClass('disabled');
            $('.record-cell[data-name="medio_digital_c"]').find('.normal.index').find('.select2-container').addClass('select2-container-disabled');
            $('.record-cell[data-name="medio_digital_c"]').find('.normal.index').find('.select2-container').find('.select2-focusser').attr('disabled',"");
            $('.record-cell[data-name="medio_digital_c"]').find('.normal.index').find('input[type="hidden"]').attr('disabled',"");
            $('.record-cell[data-name="medio_digital_c"]').find('.record-edit-link-wrapper').addClass('hide');

            $('.record-cell[data-name="punto_contacto_c"]').find('.normal.index').find('.edit').addClass('disabled');
            $('.record-cell[data-name="punto_contacto_c"]').find('.normal.index').find('.select2-container').addClass('select2-container-disabled');
            $('.record-cell[data-name="punto_contacto_c"]').find('.normal.index').find('.select2-container').find('.select2-focusser').attr('disabled',"");
            $('.record-cell[data-name="punto_contacto_c"]').find('.normal.index').find('input[type="hidden"]').attr('disabled',"");
            $('.record-cell[data-name="punto_contacto_c"]').find('.record-edit-link-wrapper').addClass('hide');

            $('[data-name="evento_c"]').css({ "pointer-events":"none"});
            $('[data-name="camara_c"]').css({ "pointer-events":"none"});
            $('[data-name="promotor_c"]').css({ "pointer-events":"none"});
            $('[data-name="codigo_expo_c"]').css({ "pointer-events":"none"});
            $('.record-cell[data-name="codigo_expo_c"]').find('.record-edit-link-wrapper').addClass('hide');
        }
    },

    //Función para eliminar opciones del campo origen
    estableceOpcionesOrigenLeads:function(){
        var opciones_origen = app.lang.getAppListStrings('origen_lead_list');

        if (App.user.attributes.puestousuario_c != '53') { //Si no tiene puesto uniclick, se eliminan las opciones Closer y Growth
            Object.keys(opciones_origen).forEach(function (key) {
                if (key == "14" || key == "15") {
                    delete opciones_origen[key];
                }
            });
        }

        this.model.fields['origen_c'].options = opciones_origen;
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
                messages: 'El campo no acepta Caracteres Especiales.'
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

    _render: function (options) {
        this._super("_render");
        this.$(".record-cell[data-name='blank_space']").hide();
        $('[data-name="contacto_asociado_c"]').attr('style', 'pointer-events:none');
        //Ocultando campo de control que omite validación de duplicados
        $('[data-name="omite_match_c"]').hide();
        //Oculta etiqueta de lead_direcciones
        this.$("div.record-label[data-name='lead_direcciones']").attr('style', 'display:none;');
        //Ocultando campo check de homonimo
        $('[data-name="homonimo_c"]').hide();
        //Oculta etiqueta del pipeline de Analizate
        this.$("div.record-label[data-name='leads_analizate_clientes']").attr('style', 'display:none;');

        //Oculta fecha de bloqueo
        $('[data-name="fecha_bloqueo_origen_c"]').hide();

    },

    convert_Lead_to_Accounts: function () {
        self = this;
        var filter_arguments = {
            "id": this.model.get('id')
        };
        // alert(this.model.get('id'))
        this.valida_requeridos();
		var btnConvert = this.getField("convert_Leads_button");
		btnConvert.hide();
		var editButton = this.getField('edit_button');
        editButton.setDisabled(true);
        app.alert.show('upload', { level: 'process', title: 'LBL_LOADING', autoclose: false });
        app.api.call("create", app.api.buildURL("existsLeadAccounts", null, null, filter_arguments), null, {
            success: _.bind(function (data) {
                console.log(data);
                app.alert.dismiss('upload');
                app.controller.context.reloadData({});
				editButton.setDisabled(false);
                if (data.idCuenta === "") {
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
					var btnConvert = this.getField("convert_Leads_button");
					btnConvert.hide();
                }
                var btnConvert = this.getField("convert_Leads_button")

                if (this.model.get('subtipo_registro_c') == '2') {
                    btnConvert.show();
                } else {
                    btnConvert.hide();
                }
                //app.controller.context.reloadData({});
                //SUGAR.App.controller.context.reloadData({})
                /* Para refrescar solo un campo

                 model.fetch({

                  view: undefined,

                  fields: ['industry']

                });
                 */

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
        var leadid = this.model.get('id');
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
        var modulo = 'Leads';
		var posiciones = app.user.attributes.posicion_operativa_c;
		var posicion = '';
		if(posiciones.includes(3)) posicion = 'Ventas';
		if(posiciones.includes(4)) posicion = 'Staff';
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
        vicidial += '&leadid=' + id_call;
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
        var lead_direcciones = app.utils.deepCopy(this.prev_oDirecciones.prev_direccion);
        this.model.set('lead_direcciones', lead_direcciones);
        this.oDirecciones.direccion = lead_direcciones;
        lead_dir.nuevaDireccion = lead_dir.limpiaNuevaDireccion();
        lead_dir.render();
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
        var idLead = this.model.get('id');

        //Recupera información
        if (!_.isEmpty(idLead) && idLead != "") {
            app.api.call('GET', app.api.buildURL('Leads/' + idLead + '/link/leads_dire_direccion_1'), null, {
                success: function (data) {
                    //Itera y agrega direcciones
                    for (var i = 0; i < data.records.length; i++) {
                        //Asignando valores de los campos
                        var tipo = data.records[i].tipodedireccion.toString();
                        var tipoSeleccionados = '^' + listMapIndicador[tipo].replace(/,/gi, "^,^") + '^';
                        var indicador = data.records[i].indicador;
                        var indicadorSeleccionados = '^' + listMapIndicador[indicador].replace(/,/gi, "^,^") + '^';

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
                        contexto_lead.oDirecciones.direccion.push(direccion);

                        if(valCodigoPostal!=""){
                            //recupera información asociada a CP
                            var strUrl = 'DireccionesCP/' + valCodigoPostal + '/' + i;
                            app.api.call('GET', app.api.buildURL(strUrl), null, {
                                success: _.bind(function (data) {
                                    //recupera info
                                    var list_paises = data.paises;
                                    var list_municipios = data.municipios;
                                    var city_list = App.metadata.getCities();
                                    var list_ciudades=data.ciudades;
                                    var list_estados = data.estados;
                                    var list_colonias = data.colonias;
                                    //Poarsea valores para listas
                                    //País
                                    listPais = {};
                                    for (var i = 0; i < list_paises.length; i++) {
                                        listPais[list_paises[i].idPais] = list_paises[i].namePais;
                                    }
                                    contexto_lead.oDirecciones.direccion[data.indice].listPais = listPais;
                                    contexto_lead.oDirecciones.direccion[data.indice].listPaisFull = listPais;
                                    //Municipio
                                    listMunicipio = {};
                                    for (var i = 0; i < list_municipios.length; i++) {
                                        listMunicipio[list_municipios[i].idMunicipio] = list_municipios[i].nameMunicipio;
                                    }
                                    contexto_lead.oDirecciones.direccion[data.indice].listMunicipio = listMunicipio;
                                    contexto_lead.oDirecciones.direccion[data.indice].listMunicipioFull = listMunicipio;
                                    //Estado
                                    listEstado = {};
                                    for (var i = 0; i < list_estados.length; i++) {
                                        listEstado[list_estados[i].idEstado] = list_estados[i].nameEstado;
                                    }
                                    contexto_lead.oDirecciones.direccion[data.indice].listEstado = listEstado;
                                    contexto_lead.oDirecciones.direccion[data.indice].listEstadoFull = listEstado;
                                    //Colonia
                                    listColonia = {};
                                    for (var i = 0; i < list_colonias.length; i++) {
                                        //listColonia[list_colonias[i].idColonia] = list_colonias[i].nameColonia;
                                        listColonia[i]={};
                                        listColonia[i]['idColonia']=list_colonias[i].idColonia;
                                        listColonia[i]['nameColonia']=list_colonias[i].nameColonia;
                                        listColonia[i]['idCodigoPostal']=list_colonias[i].idCodigoPostal;
                                    }
                                    contexto_lead.oDirecciones.direccion[data.indice].listColonia = listColonia;
                                    contexto_lead.oDirecciones.direccion[data.indice].listColoniaFull = listColonia;
                                    //Ciudad
                                    listCiudad = {};
                                    for (var i = 0; i < list_ciudades.length; i++) {
                                        listCiudad[list_ciudades[i].idCiudad] = list_ciudades[i].nameCiudad;
                                    }
                                    contexto_lead.oDirecciones.direccion[data.indice].listCiudad = listCiudad;
                                    contexto_lead.oDirecciones.direccion[data.indice].listCiudadFull = listCiudad;

                                    //Genera objeto con valores previos para control de cancelar
                                    contexto_lead.prev_oDirecciones.prev_direccion = app.utils.deepCopy(contexto_lead.oDirecciones.direccion);
                                    lead_dir.oDirecciones = contexto_lead.oDirecciones;

                                    //Aplica render a campo custom
                                    lead_dir.render();

                                }, contexto_lead)
                            });
                        }
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
                this.cargaPipeline();

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
            this.model.set('lead_direcciones', this.oDirecciones.direccion);
        }
        //Callback a validation task
        callback(null, fields, errors);
    },

    _direccionDuplicada: function (fields, errors, callback) {

        /* SE VALIDA DIRECTAMENTE DE LOS ELEMENTOS DEL HTML POR LA COMPLEJIDAD DE
         OBETENER LAS DESCRIPCIONES DE LOS COMBOS*/
        var objDirecciones = $('.control-group.direccion');
        var concatDirecciones = [];
        var strDireccionTemp = "";
        for (var i = 0; i < objDirecciones.length - 1; i++) {
            if (objDirecciones.eq(i).find('select.inactivo option:selected') == 0) {
                strDireccionTemp = objDirecciones.eq(i).find('.calleExisting').val() +
                    objDirecciones.eq(i).find('.numExtExisting').val() +
                    objDirecciones.eq(i).find('.numIntExisting').val() +
                    objDirecciones.eq(i).find('select.coloniaExisting option:selected').text() +
                    objDirecciones.eq(i).find('select.municipioExisting option:selected').text() +
                    objDirecciones.eq(i).find('select.estadoExisting option:selected').text() +
                    objDirecciones.eq(i).find('select.ciudadExisting option:selected').text() +
                    objDirecciones.eq(i).find('.postalInputTempExisting').val();

                concatDirecciones.push(strDireccionTemp.replace(/\s/g, "").toUpperCase());
            }
        }

        // validamos  el arreglo generado
        var existe = false;
        for (var j = 0; j < concatDirecciones.length; j++) {
            for (var k = j + 1; k < concatDirecciones.length; k++) {

                if (concatDirecciones[j] == concatDirecciones[k]) {
                    existe = true;
                }
            }
        }

        if (existe) {
            app.alert.show('Direcci\u00F3n', {
                level: 'error',
                autoClose: false,
                messages: 'Existe una o mas direcciones repetidas'
            });
            var messages1 = 'Existe una o mas direcciones repetidas';
            errors['xd'] = errors['xd'] || {};
            // errors['xd'].messages1 = true;
            errors['xd'].required = true;
        }

        callback(null, fields, errors);
    },

    /** Description: On Inline edit disable the TAB Key in order to prevent the field from going to detail mode.*/
    handleKeyDown: function (e, field) {
        if (e.which === 9) {
            if (field.name != this.model.fields.lead_direcciones.name) {
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
    valida_usuarios_inactivos:function (fields, errors, callback) {
        var ids_usuarios='';
            if(this.model.attributes.assigned_user_id) {
              ids_usuarios+=this.model.attributes.assigned_user_id;
            }
            console.log("Valor del ID del asignado: ".ids_usuarios);
            ids_usuarios += ',';
        if(ids_usuarios!="") {
          //Generar petición para validación
          app.api.call('GET', app.api.buildURL('GetStatusOfUser/' + ids_usuarios+'/inactivo'), null, {
              success: _.bind(function(data) {
                  if(data.length>0){
                      var nombres='';
                      //Armando lista de usuarios
                      for(var i=0;i<data.length;i++){
                          nombres+='<b>'+data[i].nombre_usuario+'</b><br>';
                      }
                      app.alert.show("Usuarios", {
                          level: "error",
                          messages: "No es posible guardar este registro con el siguiente usuario inactivo:<br>"+nombres,
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
            if(value == idUser){
                readonly = false;
            }
        });

        if(readonly){
            this.$("[data-name='alianza_soc_chk_c']").attr('style', 'pointer-events:none;');
        }
    },

    cambios_origen_SOC: function () {
        var idUser = App.user.attributes.id; //Id del usuario,
        var cambio = false;
        var valor = 0;

        if (this.model.get('alianza_soc_chk_c') != undefined){
            valor = this.model.get('alianza_soc_chk_c');
        }

        Object.entries(App.lang.getAppListStrings('soc_usuario_list')).forEach(([key, value]) => {
            if(value == idUser){
                cambio = true;
            }
        });

        if(this.model.get('subtipo_registro_c') != undefined && this.model.get('origen_c') != undefined && this.model.get('detalle_origen_c') != undefined){
            if(this.model.get('subtipo_registro_c') != '4' && this.model.get('origen_c') == '12' && this.model.get('detalle_origen_c') == '12' ){
                this.model.set('alianza_soc_chk_c', 1);
            }else{

                if(valor){
                    this.model.set('alianza_soc_chk_c', valor);
                    this.cmbio_soc += 1;
                }else{
                    this.model.set('alianza_soc_chk_c', 0);
                }

                if( (this.model._previousAttributes.detalle_origen_c == 12 && this.cmbio_soc > 0) ||
                    (this.model._previousAttributes.detalle_origen_c != 12 && this.cmbio_soc > 2)) {
                    this.model.set('alianza_soc_chk_c', 0);
                }

                if( (this.model._previousAttributes.detalle_origen_c != ""  &&
                    this.model._previousAttributes.detalle_origen_c != 12 && this.cmbio_soc > 0
                    && this.model.get('alianza_soc_chk_c')==1)) {
                    this.model.set('alianza_soc_chk_c', 0);
                }

                if(!cambio){
                    this.model.set('alianza_soc_chk_c', this.model.get('alianza_soc_chk_c'));
                }
            }
        }
    },

    muestrasubestatus: function (){
        if (App.user.attributes.puestousuario_c!='27' && App.user.attributes.puestousuario_c!='31'){
            $('[data-name="subestatus_ld_c"]').hide();
        }
    },	

    cargaPipeline: function () {
        if(typeof analizate_lead!='undefined'){
            var id =this.model.get('id');
            var requests=[];
            var request={};
            //Obtenemos las peticiones de los campos cstm: Analizate 4
            var requestE = app.utils.deepCopy(request);
            var url = app.api.buildURL('ObtieneFinanciera/' + id);
            requestE.url = url.substring(4);
            requests.push(requestE);
             app.api.call("create", app.api.buildURL("bulk", '', {}, {}), {requests: requests}, {
                        success: _.bind(function (data) {
                            if(data[0].contents!=""){
                                analizate_lead.Analizate=[];
                                analizate_lead.Analizate.Financiera=[];
                                analizate_lead.Analizate.Credit=[];
                                analizate_lead.Analizate.Cliente=[];
                                analizate_lead.Analizate.Financiera = data[0].contents.Financiera;
                                analizate_lead.Analizate.Credit = data[0].contents.Credit;
                                analizate_lead.Analizate.Cliente = data[0].contents.AnalizateCliente;
                                analizate_lead.cargapipeline();
                                analizate_lead.render();
                            }

                        }, this)
            });
        }

    },

    muestraBotonConversionLeads:function(){
        //Oculta botón de conversión para todos los usuarios, excepto para roles: Seguros, 	Seguros - Creditaria
        var currentUserRoles = App.user.get('roles');
        var rolesSeguros = ['Seguros','Seguros - Creditaria'];
        var includesSeguros =[];

        for (let index = 0; index < currentUserRoles.length; index++) {
            const rol = currentUserRoles[index];

            if( rolesSeguros.includes(rol) ){
                includesSeguros.push("1");
            }else{
                includesSeguros.push("0");
            }
        }

        //Se ocula botón para convertir en caso de que no tenga los roles Seguros','Seguros - Creditaria y además no es de origen Seguros
        if( !includesSeguros.includes('1') && this.model.get("origen_c") != "11" ){
            var btnConvert = this.getField('convert_Leads_button');
            btnConvert.dispose();
        }
    },

    solicitar_ciec_function:function(){

        if (this.model.get('subtipo_registro_c') == "3" || this.model.get('subtipo_registro_c') == "4") {
            app.alert.show('No_subtipo', {
                level: 'error',
                messages: 'No se puede solicitar CIEC para Leads cancelados o convertidos.',
                autoClose: false
            });
            return;
        }
        
        if(!_.isEmpty(this.model.get('email'))){
            if (this.model.get('email')[0].email_address == "" || this.model.get('email')[0].email_address == undefined) {
                app.alert.show('No_Envio', {
                    level: 'error',
                    messages: 'El Lead no contiene un correo electrónico.',
                    autoClose: false
                });
                return;
            }

        }

        if(_.isEmpty(this.model.get('email'))){
            app.alert.show('No_Envio', {
                level: 'error',
                messages: 'El Lead no contiene un correo electrónico.',
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

})
