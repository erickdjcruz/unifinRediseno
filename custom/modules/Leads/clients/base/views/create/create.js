({

    extendsFrom: 'CreateView',

    total_asignados:null,

    initialize: function (options) {
        self = this;
        this._super("initialize", [options]);
        this.fromProtocolo=options.context.attributes.dataFromProtocolo;

        /** Valida genero personas fisicas y fisica con actividad empesarial **/
        this.model.addValidationTask('validaGenero', _.bind(this.validaGenero, this));

        this.model.addValidationTask('check_Requeridos', _.bind(this.valida_requeridos, this));
        this.model.on('sync', this._readonlyFields, this);
        this.model.on("change:lead_cancelado_c", _.bind(this._subMotivoCancelacion, this));
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
        
        //Validation task que muestra modal sobre duplicados
        this.model.addValidationTask('check_duplicados_modal', _.bind(this.check_duplicados_modal, this));
        this.events['keydown [name=ventas_anuales_c]'] = 'checkInVentas';
        this.on('render', this._hidechkLeadCancelado, this);
        this.model.addValidationTask('setCleanName', _.bind(this.cleanName, this));
        this.model.on("change:regimen_fiscal_c", _.bind(this._cleanRegFiscal, this));
        this.getRegistrosAsignados();
        this.fechaAsignacion();
        this.model.on("change:leads_leads_1_right", _.bind(this._checkContactoAsociado, this));
        this.metodo_asignacion_lm_lead();

        //Direcciones
        contexto_lead = this;
        this.oDirecciones = [];
        this.oDirecciones.direccion = [];
        this.prev_oDirecciones = [];
        this.prev_oDirecciones.prev_direccion = [];
        this.model.addValidationTask('set_custom_fields', _.bind(this.setCustomFields, this));
        this.model.addValidationTask('check_direcciones', _.bind(this.validadireccexisting, this));
        this.model.addValidationTask('validate_Direccion_Duplicada', _.bind(this._direccionDuplicada, this));
        this.model.addValidationTask('valida_usuarios_inactivos',_.bind(this.valida_usuarios_inactivos, this));
        this.model.addValidationTask('valida_telefonos_requeridos',_.bind(this.valida_telefonos_requeridos, this));
    },

    delegateButtonEvents: function() {
        this._super("delegateButtonEvents");
        this.context.on('button:cancel_button:click', this.cancel, this);
    },

    _cleanRegFiscal: function () {

        if (this.model.get('regimen_fiscal_c') == '3') {

            this.model.set('nombre_c', '');
            this.model.set('apellido_paterno_c', '');
            this.model.set('apellido_materno_c', '');

        } else {
            this.model.set('nombre_empresa_c', '');
        }
    },

    cleanName: function (fields, errors, callback) {
        if(_.isEmpty(errors)){
            //Recupera variables
            var postData = {
                'name': this.model.get("name")
            };
            //Consume servicio
            if(this.model.get("name").trim()!='') {
                var serviceURI = app.api.buildURL("getCleanName", '', {}, {});
                App.api.call("create", serviceURI, postData, {
                    success: _.bind(function (data) {
                        if (data['status']=='200') {
                            this.model.set('clean_name_c', data['cleanName']);
                        }else{
                            //Error
                            app.alert.show('error_clean_name', {
                                level: 'error',
                                autoClose: false,
                                messages: data['error']
                            });
                            //Agrega errores
                            errors['clean_name_c'] = errors['clean_name_c']|| {};
                            errors['clean_name_c'].required = true;
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
                errors['clean_name_c'] = errors['clean_name_c'] || {};
                errors['clean_name_c'].required = true;
                callback(null, fields, errors);
            }
        }else{
          callback(null, fields, errors);
        }
    },

    _hidechkLeadCancelado: function () {
        /****Oculta check Lead Cancelado solo al crear Lead****/
        this.$('[data-name=lead_cancelado_c]').hide();
        //this.validaCreacionLeadsSeguros();
    },

    //oculta drawer de creación,únicamente se permite crear Leads a Usuarios con roles: Seguros, 	Seguros - Creditaria y Admins
    validaCreacionLeadsSeguros:function(){
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

        if( App.user.get('type') != 'admin'){
            
            if( !includesSeguros.includes('1') ){
                app.alert.show('noCreaLead', {
                    level: 'error',
                    messages: 'No cuentas con los permisos para crear registro de Lead',
                    autoClose: false
                });
                app.drawer.close();
            }

        }
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

    check_duplicados_modal:function(fields, errors, callback){

        if(Object.keys(errors).length==0 && this.options.context.flagGuardar!="1"){
            var telefonos=[];
            if(this.model.get('phone_mobile')!="" && this.model.get('phone_mobile')!=undefined){
                telefonos.push(this.model.get('phone_mobile'));
            }

            if(this.model.get('phone_home')!="" && this.model.get('phone_home')!=undefined){
                telefonos.push(this.model.get('phone_home'));
            }

            if(this.model.get('phone_work')!="" && this.model.get('phone_work')!=undefined){
                telefonos.push(this.model.get('phone_work'));
            }

            var email="";
            if(this.model.attributes.email !=undefined){
                if(this.model.attributes.email.length>0){
                    email=this.model.attributes.email[0].email_address
                }
            }

            var rfc="";
            if(this.model.get('rfc_c') != undefined && this.model.get('rfc_c') != ""){
                rfc=this.model.get('rfc_c');
            }

            //Parámetros para consumir servicio
            var params = {
                'nombre': this.model.get('name'),
                'correo': email,
                'telefonos': telefonos,
                'rfc': rfc,
            };

            /*
            var params={
                "nombre":"27 MICRAS INTERNACIONAL",
                //"nombre":"GRUASDELVALLESANMARTIN",
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
                                //self.duplicados[property].nivelMatch= self.duplicados[property].nivelMatch[0];
                                //self.duplicados[property].rfc= "LOBS920410HDFPLL06";
                                self.duplicados[property].coincidencia= self.duplicados[property].coincidencia;
                            }
                            errors['modal_duplicados'] = errors['modal_duplicados'] || {};
                            errors['modal_duplicados'].custom_message1 = true;

                            app.alert.show("posibles_coincidencias", {
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
                                    name: 'ValidaDuplicadoModal',
                                    layout: this.layout,
                                    module: 'Leads'
                                });
                                /** add the new view to the components list of the record layout*/
                                this.layout._components.push(quickCreateView);
                                this.layout.$el.append(quickCreateView.$el);
                            }
                            /**triggers an event to show the pop up quick create view*/
                            this.layout.trigger("app:view:ValidaDuplicadoModal");
                        }
                    }
                    callback(null, fields, errors);

                }, this)
            });


        }else{
            callback(null, fields, errors);
        }

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
            if (this.model.get('phone_mobile') == this.model.get('phone_home') && this.model.get('phone_mobile') != undefined && this.model.get('phone_home') != undefined) {
                duplicado = duplicado + 1;
				$('.Telefonom').css('border-color', 'red');
				$('.Telefonoc').css('border-color', 'red');
                errors['phone_mobile'] = errors['phone_mobile'] || {};
                errors['phone_mobile'].required = true;
                errors['phone_home'] = errors['phone_home'] || {};
                errors['phone_home'].required = true;

            }
            if (this.model.get('phone_mobile') == this.model.get('phone_work') && this.model.get('phone_mobile') != undefined && this.model.get('phone_work') != undefined) {
                duplicado = duplicado + 1;
				$('.Telefonom').css('border-color', 'red');
				$('.Telefonot').css('border-color', 'red');
                errors['phone_mobile'] = errors['phone_mobile'] || {};
                errors['phone_mobile'].required = true;
                errors['phone_work'] = errors['phone_work'] || {};
                errors['phone_work'].required = true;

            }
            if (this.model.get('phone_home') == this.model.get('phone_work') && this.model.get('phone_home') != undefined && this.model.get('phone_work') != undefined) {
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

    _subMotivoCancelacion: function () {

        if (!this.model.get('lead_cancelado_c')) {

            this.model.set('motivo_cancelacion_c', '');
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

    valida_requeridos: function (fields, errors, callback) {
        var campos = "";
        var subTipoLead = this.model.get('subtipo_registro_c');
        var tipoPersona = this.model.get('regimen_fiscal_c');
        var campos_req = ['origen_c'];

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
                    campos_req.push('nombre_c', 'apellido_paterno_c', 'apellido_materno_c');
                }
                break;

            default:
                break;
        }

        if (campos_req.length > 0) {

            for (i = 0; i < campos_req.length; i++) {

                var temp_req = campos_req[i];

                if (this.model.get(temp_req) == '' || this.model.get(temp_req) == null) {
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
        /*****CHECK LEAD CANCELAR*********/
        if (this.model.get('lead_cancelado_c') == '1') {
            if (this.model.get('motivo_cancelacion_c') == '' || this.model.get('motivo_cancelacion_c') == null) {

                campos = campos + '<b>' + app.lang.get("LBL_MOTIVO_CANCELACION_C", "Leads") + '</b><br>';
                errors['motivo_cancelacion_c'] = errors['motivo_cancelacion_c'] || {};
                errors['motivo_cancelacion_c'].required = true;
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

    _readonlyFields: function () {
        var self = this;
        /***************************READONLY PARA SUBTIPO DE LEAD CANCELADO**************************/
        if (this.model.get('lead_cancelado_c') == '1' && this.model.get('subtipo_registro_c') == '3') {

            var editButton = self.getField('edit_button');
            editButton.setDisabled(true);

            _.each(this.model.fields, function (field) {

                self.noEditFields.push(field.name);
                self.$('.record-edit-link-wrapper[data-name=' + field.name + ']').remove();
                self.$('[data-name=' + field.name + ']').attr('style', 'pointer-events:none;');
            });
        }
        /***************************READONLY PARA SUBTIPO DE LEAD CONVERTIDO**************************/
        if (this.model.get('subtipo_registro_c') == '4') {

            var editButton = self.getField('edit_button');
            editButton.setDisabled(true);

            _.each(this.model.fields, function (field) {

                self.noEditFields.push(field.name);
                self.$('.record-edit-link-wrapper[data-name=' + field.name + ']').remove();
                self.$('[data-name=' + field.name + ']').attr('style', 'pointer-events:none;');

            });
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
                    //OCULTANDO BOTON DE IGNORAR DUPLICADO CON JQUERY
                    $('[name="duplicate_button"]').hide();
                    $('[data-event="list:dupecheck-list-select-edit:fire"]').addClass("hidden");
                    break;
            }
        }
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

    /*
    Función ejecutada para saber si la información se debe de mostrar
    */
    getRegistrosAsignados:function(){

        var id_user=App.user.attributes.id;
        App.alert.show('obtieneAsignados', {
                    level: 'process',
                    title: 'Cargando',
                });

        app.api.call('GET', app.api.buildURL('GetRegistrosAsignadosForProtocolo/' + id_user), null, {
            success: function (data) {
                App.alert.dismiss('obtieneAsignados');
                self.total_asignados=data.total_asignados;
            },
            error: function (e) {
                throw e;
            }
        });

    },

    cancel: function () {
        //Validación para obligar a registrar Lead a través de Protocolo al asesor firmado
        //necesariamente se agrega 'else' para que en una creación natural, el botón cancel siga con el funcionamiento natural
        if(this.fromProtocolo=='1'){
            app.alert.show("requiredSetLead", {
                level: "warning",
                title: "Es necesario que complete el registro de Lead",
                autoClose: false
            });

            // update the browser URL with the proper
            app.router.navigate('#Home', {trigger: true});

        }else{
            this._super("cancel");
        }

    },

    _render: function (options) {
        this._super("_render");
        this.$(".record-cell[data-name='blank_space']").hide();
        $('[data-name="contacto_asociado_c"]').attr('style', 'pointer-events:none');
        //Ocultando campo de control que omite validación de duplicados
        $('[data-name="omite_match_c"]').hide();
        //Ocultando campo check de homonimo
        $('[data-name="homonimo_c"]').hide();
        //Oculta etiqueta de lead_direcciones
        this.$("div.record-label[data-name='lead_direcciones']").attr('style', 'display:none;');
		//Ocutla telefonos
		$('[data-name="phone_work"]').hide();
		$('[data-name="phone_home"]').hide();
		$('[data-name="phone_mobile"]').hide();
        (document.getElementById("Telefonom")!=null) ? document.getElementById("Telefonom").style.display = '' : '' ;
        (document.getElementById("Telefonot")!=null) ? document.getElementById("Telefonot").style.display = '' : '' ;
        (document.getElementById("Telefonoc")!=null) ? document.getElementById("Telefonoc").style.display = '' : '' ;
        //Oculta subpanel de Analizate
        $('[data-panelname=LBL_RECORDVIEW_PANEL4]').hide();
        $('[data-name="fecha_bloqueo_origen_c"]').hide();
        $('[data-name="c_estatus_telefono_c"]').hide();
        $('[data-name="m_estatus_telefono_c"]').hide();
        $('[data-name="o_estatus_telefono_c"]').hide();
    },

    fechaAsignacion: function () {

        //Asigna fecha de asignacion con los puestos de Asesor Leasing:2 y Director Leasing:5
        var puestoUsuario = App.user.attributes.puestousuario_c;

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

        if (puestoUsuario == '2' || puestoUsuario == '5') {
            this.model.set('fecha_asignacion_c',today);
        }
    },

    _checkContactoAsociado: function() {

        if (this.model.get("leads_leads_1_right").id != "" && this.model.get("leads_leads_1_right").id != undefined) {
            // console.log("Activa check Contacto asociado create");
            this.model.set('contacto_asociado_c', true);

        } else {
            // console.log("Desactiva check Contacto asociado create");
            this.model.set('contacto_asociado_c', false);
        }
    },

    metodo_asignacion_lm_lead: function() {
        if(this.createMode){

            var posicionOperativa = (App.user.attributes.posicion_operativa_c == undefined)? '' : App.user.attributes.posicion_operativa_c ; //Posición Operativa - Asesor

            if (posicionOperativa.includes('3')){
                //METODO DE ASIGNACION LM - CREADO POR ASESOR
                this.model.set('metodo_asignacion_lm_c','2');
            }
        }
    },

    setCustomFields: function (fields, errors, callback) {
        //Direcciones
        this.model.set('lead_direcciones', this.oDirecciones.direccion);

        callback(null, fields, errors);
    },    

    validadireccexisting: function (fields, errors, callback) {
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

    _direccionDuplicada: function (fields, errors, callback) {

        /* SE VALIDA DIRECTAMENTE DE LOS ELEMENTOS DEL HTML POR LA COMPLEJIDAD DE
         OBETENER LAS DESDRIPCIONES DE LOS COMBOS*/
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
                          messages: "No es posible generar un Lead con el siguiente usuario inactivo:<br>"+nombres,
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

    valida_telefonos_requeridos: function (fields, errors, callback){

        if (((this.model.get('phone_mobile') == '' || this.model.get('phone_mobile') == null) &&
            (this.model.get('phone_home') == '' || this.model.get('phone_home') == null) &&
            (this.model.get('phone_work') == '' || this.model.get('phone_work') == null))){

                app.alert.show("phone_required", {
                  level: "error",
                  autoClose: false,
                  messages: "Al menos un teléfono es requerido",
                });

                $('[data-name="phone_work"]').css("border-color", "red");
                $('[data-name="phone_home"]').css("border-color", "red");
                $('[data-name="phone_mobile"]').css("border-color", "red");

                errors["lead_telefonos"] = errors["xd"] || {};
                errors["lead_telefonos"].required = true;

        }

        callback(null, fields, errors);
        
    }
})
