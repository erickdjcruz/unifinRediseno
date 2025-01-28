({
    tel_tipo_list: null,
    pais_list: null,
    estatus_list: null,
    newWhatsapp: 0,
    newArray: [],

    events: {
        'keydown .Telefonot': 'keyDownNewExtension',
        'keydown .newTelefono': 'keyDownNewExtension',
        'click  .addTelefono': 'addNewTelefono',
        'click  .mcall': 'makecall',
        /*
          * Eventos para actualizacion de valores en modelo Telefonos
        */
        'click .principal': 'updatePrincipal',          //Principal
        'change .Tipotelefonot': 'updateTipotelefonot', //Tipo
        'change .Paist': 'updatePaist',                 //País
        'change .Estatust': 'updateEstatust',           //Estatus
        'change .Extensiont': 'updateExtensiont',       //Extensión
        'change .Telefonot': 'updateTelefonot',         //Teléfono
        'click .Whatsappt': 'updateWhatsapp',           //WhatsApp
        'click .newWhatsapp': 'updateNewWhatsapp',      //Nuevo WhatsApp
        'change .newTipotelefono': 'updateNewTipo',     //Nuevo Tipo
    },

    initialize: function (options) {
        //Inicializa campo custom
        options = options || {};
        options.def = options.def || {};
        cont_tel = this;
        this._super('initialize', [options]);
        this.Listas();
    },

    Listas: function () {
        this.tel_tipo_list = app.lang.getAppListStrings('tel_tipo_list');
        this.pais_list = app.lang.getAppListStrings('paises_list');
        this.estatus_list = app.lang.getAppListStrings('tel_estatus_list');
    },

    /**
     * When data changes, re-render the field only if it is not on edit (see MAR-1617).
     * @inheritdoc
     */
    bindDataChange: function () {
        this.model.on('change:' + this.name, function () {
            if (this.action !== 'edit') {
            }
        }, this);
    },

    _render: function () {
        this._super("_render");

        if($('[data-fieldname="account_telefonos"] > span').length >0){
            $('[data-fieldname="account_telefonos"] > span').show();
        }

        this.$("div.record-label[data-name='account_telefonos']").attr('style', 'display:none;');
        $('#nuevo').hide();
        if (this.action == 'edit' && this.oTelefonos) {
            for (var i = 0; i < this.oTelefonos.telefono.length; i++) {
                if (this.oTelefonos.telefono[i].tipotelefono == 3 || this.oTelefonos.telefono[i].tipotelefono == 4) {
                    document.getElementsByClassName('whatsapp-tel')[i].style.visibility = '';
                } else {
                    document.getElementsByClassName('whatsapp-tel')[i].style.visibility = 'hidden';
                }
                if (this.oTelefonos.telefono[i].reus == 1) {
                    //document.getElementsByClassName('lbl_REUS_phone')[i].style.visibility = 'hidden';
                    $('[data-name="Whatsappt"]').attr('style', 'pointer-events:none');//
                    $('[data-name="whatsapp-tel"]').attr('style', 'pointer-events:none');//
                    $('[data-name="Telefonot"]').attr('style', 'pointer-events:none');//
                    $('[data-name="Extensiont"]').attr('style', 'pointer-events:none');//
                }
            }
        }
        if (this.newArray.length > 0) {
            this.$('.newTipotelefono').select2('val', this.newArray[0]);
            this.$('.newPais').select2('val', this.newArray[1]);
            this.$('.newEstatus').select2('val', this.newArray[2]);
            this.$('.newTelefono').val(this.newArray[3]);
            this.$('.newExtension').val(this.newArray[4]);
            this.newWhatsapp = this.newArray[5];
            $('#nuevo').show();
            this.newArray = [];
        }

    },

    keyDownNewExtension: function (evt) {
        if (!evt) return;
        if (!this.checkNumOnly(evt)) {
            return false;
        }
    },

    //UNI349 Control Telefonos - En el campo teléfono, extensión no se debe permitir caracteres diferentes a numéricos
    checkNumOnly: function (evt) {
        if ($.inArray(evt.keyCode, [110, 188, 190, 45, 33, 36, 46, 35, 34, 8, 9, 20, 16, 17, 37, 40, 39, 38, 16, 49, 50, 51, 52, 53, 54, 55, 56, 57, 48, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105]) < 0) {
            app.alert.show("Caracter Invalido", {
                level: "error",
                title: "Error",
                messages: "Solo n\u00FAmeros son permitidos en este campo.",
                autoClose: true
            });
            return false;
        } else {
            return true;
        }
    },

    validaTamano: function (ValTel) {
        var telefonoTam = ValTel.length;
        var banderTelefono = false;
        var expreg = /^[0-9]{8,13}$/;
        if (telefonoTam >= 8 && telefonoTam <= 13) {
            if (expreg.test(ValTel)) {
                var cont = 0;
                for (var j = 0; j < telefonoTam; j++) {
                    if (ValTel.charAt(0) == ValTel.charAt(j)) {
                        cont++;
                    }
                }
                if (cont != telefonoTam) {
                    banderTelefono = true;
                }
            }
        }
        return banderTelefono;
    },

    addNewTelefono: function (options) {
        if (this.oTelefonos == undefined) {
            this.oTelefonos = contexto_cuenta.oTelefonos;
        }
        //Estableciendo el color de borde original en cada campo en telefonos
        $('.newTipotelefono').find('.select2-choice').css('border-color', '');
        $('.newPais').find('.select2-choice').css('border-color', '');
        $('.newEstatus').find('.select2-choice').css('border-color', '');
        $('.newTipotelefono').find('.select2-choice').css('border-color', '');
        this.$('.newTelefono').css('border-color', '');
        this.$('.newTelefono').css('border-color', '');

        //Obteniendo valores de los campos
        var valor1 = this.$('.newTipotelefono').select2('val');
        var valor2 = this.$('.newPais').select2('val');
        var valor3 = this.$('.newEstatus').select2('val');
        var valor4 = this.$('.newTelefono').val();
        var valor5 = this.$('.newExtension').val();
        var valor6 = this.newWhatsapp;
        var sec = this.oTelefonos.telefono.length + 1;

        var telefono = {
            "name": valor4,
            "tipotelefono": valor1,
            "pais": valor2,
            "estatus": valor3,
            "extension": valor5,
            "telefono": valor4,
            "whatsapp_c": valor6,
            "principal": 1,
            "secuencia": sec,
            "id_cuenta": this.model.get('account_id_c')
        };

        this.newWhatsapp = 0;

        //Valida campos requeridos
        var faltantes = 0;
        if (valor1 == '') {
            $('.newTipotelefono').find('.select2-choice').css('border-color', 'red');
            faltantes++;
        }
        if (valor2 == '') {
            $('.newPais').find('.select2-choice').css('border-color', 'red');
            faltantes++;
        }
        if (valor3 == '') {
            $('.newEstatus').find('.select2-choice').css('border-color', 'red');
            app.alert.show('error_campo_telefono', {
                level: 'error',
                autoClose: true,
                messages: 'Favor de agregar estatus del <b>Tel\u00E9fono</b>'
            });
            faltantes++
        }
        if ((valor4 == '' || valor4.trim() == '')) {

            $('.newTelefono').css('border-color', 'red');
            app.alert.show('error_campo_telefono', {
                level: 'error',
                autoClose: false,
                messages: 'Favor de agregar un <b>Tel\u00E9fono</b>'
            });
            faltantes++;
        }
        if (valor4 != "") {
            //Control de errores
            var msjError = "";
            //Valida númerico
            var valNumerico = /^\d+$/;
            if (!valNumerico.test(valor4)) {
                faltantes++;
                msjError += '<br><b>Solo números son permitidos</b>';
            }
            //Valida longitud
            if (valor4.length !== 10) {
                faltantes++;
                msjError += '<br><b>Debe contener 10 dígitos</b>';
            }

            //Valida números repetidos
            if (valor4.length > 1) {
                var repetido = true;
                for (var iValor4 = 0; iValor4 < valor4.length; iValor4++) {
                    repetido = (valor4[0] != valor4[iValor4]) ? false : repetido;
                }
                if (repetido) {
                    faltantes++;
                    msjError += '<br><b>Caracter repetido</b>';
                }
            }

            //Muestra errores
            if (msjError != "") {
                $('.newTelefono').css('border-color', 'red');
                app.alert.show('phone_add_error', {
                    level: 'error',
                    autoClose: true,
                    messages: 'No se puede agregar el número:' + msjError
                });
            }

        }
        if (faltantes == 0) {
            if (this.oTelefonos.telefono.length >= 1) {
                var duplicados = false;
                this.oTelefonos.telefono.forEach(function (element) {
                    var iteracion = element.telefono;
                    iteracion = iteracion.replace(/\s+/gi, '');
                    var ntelefonico = this.$('.newTelefono').val().trim();
                    if (iteracion == ntelefonico && element.estatus == 'Activo') {
                        duplicados = true;
                    }
                });
                if (duplicados == true) {
                    this.$('.newTelefono').val("");
                    app.alert.show('Numero_duplicado', {
                        level: 'error',
                        autoClose: true,
                        messages: "No se puede agregar el número: <br> <b>Ya ha sido registrado.</b>"
                    });
                }
                else {
                    telefono.principal = 0;
                    this.oTelefonos.telefono.push(telefono);
                    this.render();
                }
            } else {
                //Actualiza estado como activo
                telefono.estatus = 'Activo';
                this.oTelefonos.telefono.push(telefono);
                this.model.set('account_telefonos', this.oTelefonos.telefono);
                this.render();
            }
        }
    },

    makecall: function (evt) {
        if (!evt) return;
        var $input = this.$(evt.currentTarget);
        var tel_client = $input.closest("tr").find("td").eq(1).html();
        var tel_usr = app.user.attributes.ext_c;
        var puesto_usuario = App.user.attributes.puestousuario_c;
        var idUsuarioLogeado = App.user.attributes.id;
        var arrayPuestosComerciales = [];
        var reus = false;
        var productoREUS = false;
        var telREUS = false;
        //LISTA PARA PUESTOS COMERCIALES
        Object.entries(App.lang.getAppListStrings('puestos_comerciales_list')).forEach(([key, value]) => {
            arrayPuestosComerciales.push(key);
        });
        //TELEFONOS QUE SOLO SON REUS
        for (var i = 0; i < this.oTelefonos.telefono.length; i++) {
            if (this.oTelefonos.telefono[i].reus == 1 && this.oTelefonos.telefono[i].telefono == tel_client) {
                telREUS = true;
            }
        }
        /*
        if(self.ResumenProductos == undefined){
            self.ResumenProductos = this.ResumenProductos;
        }*/

        if(self.ResumenProductos!=undefined){
            self1=self;
        }

        if(self.ResumenProductos==undefined){
            self=self1;
        }

        //VALIDACIONES PARA USUARIO LOGEADO CONTRA USUARIO ASIGNADO EN LOS PRODUCTOS Y QUE TIENEN TIPO DE CUENTA CLIENTE
        if (self.ResumenProductos.leasing.tipo_cuenta == "3") {
            productoREUS = true;
            // console.log("LEASING USUARIO LOGEADO & TIPO DE CUENTA CLIENTE");
        }
        if ( self.ResumenProductos.factoring.tipo_cuenta == "3") {
            productoREUS = true;
            // console.log("FACTORAJE USUARIO LOGEADO & TIPO DE CUENTA CLIENTE");
        }
        if (self.ResumenProductos.credito_auto.tipo_cuenta == "3") {
            productoREUS = true;
            // console.log("CREDITO-AUTO USUARIO LOGEADO & TIPO DE CUENTA CLIENTE");
        }
        if (self.ResumenProductos.uniclick.tipo_cuenta == "3") {
            productoREUS = true;
            // console.log("UNICLICK USUARIO LOGEADO & TIPO DE CUENTA CLIENTE");
        }
        if (self.ResumenProductos.fleet.tipo_cuenta == "3") {
            productoREUS = true;
            // console.log("FLEET USUARIO LOGEADO & TIPO DE CUENTA CLIENTE");
        }
        if (self.ResumenProductos.seguros.tipo_cuenta == "3") {
            productoREUS = true;
            // console.log("SEGUROS USUARIO LOGEADO & TIPO DE CUENTA CLIENTE");
        }

        if (telREUS == true) {
            //PUESTOS COMERCIALES AUTORIZADOS CON LA VALIDACION DE USUARIO ASIGNADO EN ALGUN PRODUCTO CON TIPO DE CUENTA-PRODUCTO CLIENTE
            if (arrayPuestosComerciales.includes(puesto_usuario) && productoREUS == true) {
                reus = true;
            }
            //PUESTOS COMERCIALES DIFERENTES A LOS AUTORIZADOS EN LA LISTA CON EL TIPO DE REGISTRO DE LA CUENTA CLIENTE
            if (!arrayPuestosComerciales.includes(puesto_usuario) && this.model.get('tipo_registro_cuenta_c') == '3') {
                reus = true;
            }

        } else {
            //ENTRA PARA LAS LLAMADAS QUE NO SON REUS
            reus = true;
        }
        //Valida REUS
        if (reus == true) {
          //Valida Teléfono y Extensión
          if (tel_usr != '' && tel_usr != null) {
            if (tel_client != '' && tel_client != null) {
              context = this;
              app.alert.show('do-call', {
                level: 'confirmation',
                messages: '¿Realmente quieres realizar la llamada? <br><br><b>NOTA: La marcaci\u00F3n se realizar\u00E1 tal cual el n\u00FAmero est\u00E1 registrado</b>',
                autoClose: false,
                onConfirm: function () {
                  //context.createcall(context.resultCallback);
                  context.createcall(tel_client);
                },
              });
            } else {
              app.alert.show('error_tel_client', {
                level: 'error',
                autoClose: true,
                messages: 'El cliente al que quieres llamar no tiene <b>N\u00FAmero telefónico</b>.'
              });
            }
          } else {
            app.alert.show('error_tel_usr', {
              level: 'error',
              autoClose: true,
              messages: 'El usuario con el que estas logueado no tiene <b>Extensi\u00F3n</b>.'
            });
          }
        } else {
            app.alert.show('message-reus-comercial', {
                level: 'error',
                messages: 'No se puede generar llamada a teléfono registrado en REUS',
                autoClose: false
            });
        }
    },

    createcall: function (tel_client) {
        //Recupera variables para petición
        self = this;
        var posiciones = App.user.attributes.posicion_operativa_c;
        var posicion = '';
        var name_client = this.model.get('name');
        if(posiciones.includes(3)) posicion = 'Ventas';
        if(posiciones.includes(4)) posicion = 'Staff';
        var Params = {
            'id_cliente': this.model.get('id'),
            'nombre_cliente': name_client,
            'numero_cliente': tel_client,
            'modulo': 'Accounts',
            'posicion': posicion,
            'puesto_usuario': App.user.attributes.puestousuario_c,
            'ext_usuario': App.user.attributes.ext_c
        };
        //Ejecuta petición para generar llamada
        app.api.call('create', app.api.buildURL('createcall'), { data: Params }, {
          success: _.bind(function (data) {
            id_call = data;
            console.log('Llamada creada, id: ' + id_call);
            app.alert.show('message-to', {
              level: 'info',
              messages: 'Usted está llamando a ' + name_client,
              autoClose: true
            });
            //callback(id_call, self);
          }, this),
        });
    },

    resultCallback: function (id_call, context) {
        self = context;
        var puesto_usuario = app.user.attributes.puestousuario_c;

        //AGENTE TELEFONICO - COORDINADOR DE CENTRO DE PROSPECCION
        if (puesto_usuario == "27" || puesto_usuario == "31" || puesto_usuario == "61") {
            /*******************VICIDIAL********************/
            vicidial += '&leadid=' + id_call;
            console.log('VICIDIAL_LINK:' + vicidial);
            $.ajax({
                cache: false,
                type: "get",
                url: vicidial,
            });

        } else {
            /*******************ISSABEL********************/
            issabel += '&id_call=' + id_call;
            console.log('ISSABEL_LINK:' + issabel);
            $.ajax({
                cache: false,
                type: "get",
                url: issabel,
            });
        }
    },

    updatePrincipal: function (evt) {
        var inputs = this.$('.principal'),
            input = this.$(evt.currentTarget),
            index = inputs.index(input);
        if (this.oTelefonos.telefono[index].principal == 0) {
            if (this.oTelefonos.telefono[index].estatus == "Activo") {
                for (var i = 0; i < this.oTelefonos.telefono.length; i++) {
                    this.oTelefonos.telefono[i].principal = 0;
                }
                this.oTelefonos.telefono[index].principal = 1;
            } else {
                //No puede marcar como principal teléfono inactivo
                app.alert.show('no_principal', {
                    level: 'warning',
                    messages: 'No se puede tener un teléfono inactivo como principal',
                    autoClose: true
                });
            }
        }
        this.render();
    },

    updateTipotelefonot: function (evt) {
        var inputs = this.$('[data-field="campo1tel"].Tipotelefonot'),
            input = this.$(evt.currentTarget),
            index = inputs.index(input);
        var tipo = input.val();
        this.oTelefonos.telefono[index].tipotelefono = tipo;
        if (tipo == 3 || tipo == 4) {
            document.getElementsByClassName('whatsapp-tel')[index].style.visibility = '';
        } else {
            document.getElementsByClassName('whatsapp-tel')[index].style.visibility = 'hidden';
        }
    },

    updateNewTipo: function (evt) {
        var inputs = this.$('[data-field="campo1tel"].newTipotelefono'),
            input = this.$(evt.currentTarget),
            index = inputs.index(input);
        var tipo = input.val();
        if (tipo == 3 || tipo == 4) {
            document.getElementsByClassName('newwhatsapp-tel')[index].style.visibility = '';
            $('#nuevo').show();
        } else {
            document.getElementsByClassName('newwhatsapp-tel')[index].style.visibility = 'hidden';
        }
    },

    updatePaist: function (evt) {
        var inputs = this.$('[data-field="campo2tel"].Paist'),
            input = this.$(evt.currentTarget),
            index = inputs.index(input);
        var pais = input.val();
        this.oTelefonos.telefono[index].pais = pais;
        //this.render();
    },

    updateEstatust: function (evt) {
        var inputs = this.$('[data-field="campo3tel"].Estatust'),
            input = this.$(evt.currentTarget),
            index = inputs.index(input);
        var estatus = input.val();
        //valida que no sea principal
        if (this.oTelefonos.telefono[index].principal == 1 && estatus == "Inactivo") {
            //No puede marcar como principal teléfono inactivo
            app.alert.show('no_principal_estatus', {
                level: 'warning',
                messages: 'No se puede tener un teléfono inactivo como principal',
                autoClose: true
            });
        } else {
            this.oTelefonos.telefono[index].estatus = estatus;
        }
        this.render();
    },

    updateExtensiont: function (evt) {
        var inputs = this.$('.Extensiont'),
            input = this.$(evt.currentTarget),
            index = inputs.index(input);
        var extension = input.val();
        this.oTelefonos.telefono[index].extension = extension;
        //this.render();
    },

    updateTelefonot: function (evt) {
        var inputs = this.$('.Telefonot'),
            input = this.$(evt.currentTarget),
            index = inputs.index(input);
        var telefono = input.val();
        // if ((this.validaTamano(telefono)==false) || (this.validaTamano(telefono) && telefono=="")) {
        //     this.$('[data-name="Teléfono"]').eq(index).css('border-color', 'red');
        //     app.alert.show('telfono_cuenta_error', {
        //         level: 'error',
        //         autoClose: true,
        //         messages: 'Formato de tel\u00E9fono incorrecto'
        //     });
        //     this.$('.Telefonot').eq(index).find('input').val('');
        // }
        // else {
        //     this.$('[data-name="Teléfono"]').eq(index).css('border-color', '');
        this.oTelefonos.telefono[index].telefono = telefono;
        //}
        //this.render();
    },

    updateWhatsapp: function (evt) {
        var inputs = this.$('.Whatsappt'),
            input = this.$(evt.currentTarget),
            index = inputs.index(input);
        if (this.oTelefonos.telefono[index].whatsapp_c) {
            this.oTelefonos.telefono[index].whatsapp_c = 0;
        } else {
            this.oTelefonos.telefono[index].whatsapp_c = 1;
        }
        this.render();
    },

    updateNewWhatsapp: function (evt) {
        if (this.newWhatsapp) {
            this.newWhatsapp = 0;
        } else {
            this.newWhatsapp = 1;
        }
        this.newArray.push(this.$('.newTipotelefono').select2('val'));
        this.newArray.push(this.$('.newPais').select2('val'));
        this.newArray.push(this.$('.newEstatus').select2('val'));
        this.newArray.push(this.$('.newTelefono').val());
        this.newArray.push(this.$('.newExtension').val());
        this.newArray.push(this.newWhatsapp);
        this.render();
    },
})
