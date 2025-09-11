/**
 * Created by Levementum on 3/2/2016.
 * User: jgarcia@levementum.com
 */
({
    extendsFrom: 'RecordView',

    initialize: function (options) {
        self = this;
        this._super("initialize", [options]);

        this.getCurrentYearMonth();
        this.model.on("change:anio", _.bind(this.getCurrentYearMonth, this));
        //this.model.on("change:cliente", _.bind(this.getTipoCliente, this));
        this.model.addValidationTask('check_tipo_cliente', _.bind(this._ValidateTipo, this));
        this.model.on("change:porciento_ri", _.bind(this.calcularRI, this));
        this.model.on("change:monto_comprometido", _.bind(this.calcularRI, this));
        this.model.on("change:renta_inicial_comprometida", _.bind(this.calcularPorcientoRI, this));
        this.model.on("change:monto_final_comprometido_c", _.bind(this.setRI, this));
        //this.model.on("change:ri_final_comprometida_c", _.bind(this.setEtapa, this));
        this.model.addValidationTask('igualaMontosFinales', _.bind(this.igualaMontoFinalOpp, this));
        this.model.addValidationTask('camponovacio', _.bind(this.validacampoconversion, this));
        this.model.addValidationTask('valida_requeridos', _.bind(this.valida_requeridos, this));
        this.model.addValidationTask('Valida_edicionBacklog', _.bind(this.mesbacklog, this));

        /************  CAmbiar valores tipo PRoducto LEasing   *****************/
        this.model.addValidationTask('num_tipo_producto', _.bind(this.num_tipo_leasing, this));
        this.model.addValidationTask('tipo_producto_requerido', _.bind(this.tipo_producto_requerido, this));
        /*********** ---- ***********************/

        //Reactivar Backlog
        this.context.on('button:reactivar_backlog:click', this.reactivarBacklog, this);

        // validación de los campos con formato númerico
        this.events['keydown [name=dif_residuales_c]'] = 'checkInVentas';
        this.events['keydown [name=tasa_c]'] = 'checkInVentas';
        this.events['keydown [name=comision_c]'] = 'checkInVentas';
        this.events['keydown [name=monto_comprometido]'] = 'checkInVentas';
        this.events['keydown [name=porciento_ri]'] = 'checkInVentas';
        this.events['keydown [name=renta_inicial_comprometida]'] = 'checkInVentas';
        this.events['keydown [name=tct_conversion_c]'] = 'checkInVentas';


        //Se añade evento para establecer registro como Solo Lectura
        this.model.on('sync', this.setNoEditAllFields, this);
        this.model.on('sync', this.probabilidadreq, this);
        this.model.on('sync', this.refinanciamientoblock, this);

        // Validación monto comprometido
        this.model.addValidationTask('valida_monto_comprometido', _.bind(this.validarMontoComprometido, this));
        /// Validación dependencia declinada
        this.model.addValidationTask('validaDependenciaDeclinada', _.bind(this.validaDependenciaDeclinada, this));
        // Valida permiso de tipificacion riesgo
        this.model.on('sync', this.checkPermisoTipificacion, this);        
        //Boton Reactivación 
        this.context.on('button:reactiva_bkl:click', this.reactiva_bkl, this);
        //ReadOnly Estatus Backlog Declinada
        this.model.on('sync', this._readOnlyEstatusDeclinada, this);
    },

    _render: function () {
        this._super("_render");

        //Se ocultan banderas
        $('[data-name="tct_carga_masiva_chk_c"]').hide();
        $('[data-name="tct_bloqueo_txf_c"]').hide();

        if (this.model.dataFetched) {
            this.$('[data-name=editar]').hide();
            self.model.set("editar", true);
            if (self.$('[data-fieldname=lev_backlog_opportunities_name]').children().children(['data-original-title']).html() != null &&
                self.$('[data-fieldname=lev_backlog_opportunities_name]').children().children(['data-original-title']).html() != "") {
                self.model.set("editar", false);
            }
        }

        var usuario = app.data.createBean('Users', { id: app.user.get('id') });
        usuario.fetch({
            success: _.bind(function (modelo) {

                this.productos = modelo.get('productos_c');
                this.productoUsuario = modelo.get('tipodeproducto_c');
                this.multiProducto = modelo.get('multiproducto_c');


                var op = app.lang.getAppListStrings('tipo_producto_list');
                var op2 = {};
                for (id in this.productos) {
                    op2[this.productos[id]] = op[this.productos[id]];
                }
                var lista = this.getField('producto_c');
                lista.items = op2;
                lista.render();
                if (this.model.get('producto_c') == null) {
                    if (this.productos[0] == "4") {
                        this.model.set('producto_c', '4');
                    } else if (this.productos[0] == "1") {
                        this.model.set('producto_c', '1');
                    } else if (this.productos[0] == "3") {
                        this.model.set('producto_c', '3');
                    } else if (this.productos[0] == "2") {
                        this.model.set('producto_c', '3');
                    }
                    else if (this.productos[0] == "5") {
                        this.model.set('producto_c', '5');
                    }

                }
                //this.model.set("region", modelo.get("region_c"));
            }, this)
        });
        this.$(".record-cell[data-name='blank_space']").hide();
    },

    /**
     * Establecer todo el registro como solo lectura cuando los campos bandera son = true
     * Estos campos bandera son actualizados a través de un Job
     * */
    setNoEditAllFields: function () {

        this.blockRecordNoContactar();

        //Ocultando banderas
        $('.record-cell[data-name="tct_carga_masiva_chk_c"]').addClass('hide');
        $('.record-cell[data-name="tct_bloqueo_txf_c"]').addClass('hide');

        //Estableciendo registro completo como solo lectura
        //Obtener valores para validar bloqueo
        var carga_masiva = this.model.get('tct_carga_masiva_chk_c');
        var bloqueo = this.model.get('tct_bloqueo_txf_c');

        //Validación para establecer como solo lectura todos los campos del registro
        if (carga_masiva && bloqueo == "1") {
            //Se establecen todos los campos como solo lectura
            $('.record-cell').attr("style", "pointer-events:none");
            //Excepto los campos de tipo relacionado para permitir la navegación hacia el registro
            $('.record-cell[data-type="relate"]').removeAttr("style");
            $('.record-cell[data-name="date_entered_by"]').removeAttr("style");
            $('.record-cell[data-name="date_modified_by"]').removeAttr("style");

            //Se oculta botón de edición
            $('[name="edit_button"]').hide();
        }

    },

    blockRecordNoContactar: function () {
        if (!app.user.attributes.tct_no_contactar_chk_c && !app.user.attributes.bloqueo_credito_c && !app.user.attributes.bloqueo_cumple_c) {
            var id_cuenta = this.model.get('account_id_c');
            if (id_cuenta != '' && id_cuenta != undefined) {
                var account = app.data.createBean('Accounts', { id: this.model.get('account_id_c') });
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
                                    //Bloquear el registro completo y mostrar alerta
                                    $('.record').attr('style', 'pointer-events:none');
                                    $('.subpanel').attr('style', 'pointer-events:none');
                                }
                            }, this)
                        });
                    }, this)
                });
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


    getCurrentYearMonth: function () {

        var currentYear = (new Date).getFullYear();
        var currentMonth = (new Date).getMonth();
        var currentDay = (new Date).getDate();

        if (currentDay < 20) {
            currentMonth += 1;
        }
        if (currentDay >= 20) {
            currentMonth += 2;
        }

        var mes = this.model.get("mes");
        var opciones_year = app.lang.getAppListStrings('anio_list');
        Object.keys(opciones_year).forEach(function (key) {
            if (key < currentYear && key != mes) {
                delete opciones_year[key];
            }
        });
        this.model.fields['anio'].options = opciones_year;

        var opciones_mes = app.lang.getAppListStrings('mes_list');
        if (this.model.get("anio") <= currentYear) {
            Object.keys(opciones_mes).forEach(function (key) {
                if (key < currentMonth && key != mes) {
                    delete opciones_mes[key];
                }
            });
        }

        this.model.fields['mes'].options = opciones_mes;
        //this.render();
    },

    getTipoCliente: function () {
        //console.log("getTipoCliente");
        app.api.call("read", app.api.buildURL("Accounts/" + this.model.get('account_id_c'), null, null, {
            fields: name,
        }), null, {
            success: _.bind(function (data) {
                if (data.tipo_registro_cuenta_c == "2") { // 2 - Prospecto
                    this.model.set("tipo_c", "2");
                    this.model.set("etapa_preliminar_c", "3");
                    this.model.set("etapa_c", "3");
                } else if (data.tipo_registro_cuenta_c == "3") { // 3 - Cliente
                    //console.log("Valida lineas de credito autorizadas para leasing");
                    app.api.call("read", app.api.buildURL("Accounts/" + this.model.get('account_id_c') + "/link/opportunities", null, null, {
                        fields: name,
                        "filter": [
                            {
                                "tipo_operacion_c": "2",
                                "tipo_producto_c": "1",
                                "amount": {
                                    "$gt": "0"
                                }
                            }
                        ]
                    }), null, {
                        success: _.bind(function (data) {
                            var disponible = 0;
                            $.each(data.records, function () {
                                disponible += parseFloat(this.amount);
                                //console.log(disponible);
                            });
                            //console.log(data.records.length);
                            if (data.records.length > 0) {
                                this.model.set("tipo_c", "3");
                                this.model.set("etapa_preliminar_c", "Con_linea");
                                this.model.set("etapa_c", "Con_linea");
                                this.model.set("monto_original", disponible);
                            } else {
                                this.model.set("tipo_c", "2");
                                this.model.set("etapa_preliminar_c", "3");
                                this.model.set("etapa_c", "3");
                                this.model.set("monto_original", 0);
                            }
                        }, this)
                    });
                } else {
                    this.model.set("tipo_c", "4");
                    this.model.set("etapa_preliminar_c", "3");
                    this.model.set("etapa_c", "3");
                }
                /*
                 if(data.tipo_registro_c == "Cliente" && data.estatus_c == "Integracion de Expediente"){
                 this.model.set("tipo","Prospecto");
                 this.model.set("etapa_preliminar","Prospecto");
                 this.model.set("etapa","Prospecto");
                 }*/
            }, this)
        });
    },

    _ValidateTipo: function (fields, errors, callback) {
        if (this.model.get("tipo_c") == "4") {

            errors['tipo_c'] = errors['tipo_c'] || {};
            errors['tipo_c'].required = true;

            app.alert.show('tipo de persona', {
                level: 'error',
                messages: 'No se puede crear un Backlog para este tipo',
                autoClose: false
            });
        }
        callback(null, fields, errors);
    },

    calcularRI: function () {
        var ElaborationBacklog = this.getElaborationBacklog();
        var currentDay = (new Date).getDate();
        var currentMonth = (new Date).getMonth() + 1;
        var mesBL = this.model.get("mes") - 2;

        //CVV se cambia la validaci�n para permitir actualizar el BL hasta antes dek d�a 20
        //if (this.model.get("estatus_de_la_operacion") != 'Comprometida') {
        if (this.model.get("mes") >= ElaborationBacklog && this.model.get("estatus_operacion_c") == 'Comprometida') {
            if (currentDay <= 20 || (currentMonth == mesBL && currentDay > 20) || this.model.get("mes") > ElaborationBacklog) {
                if (!_.isEmpty(this.model.get("monto_comprometido")) && !_.isEmpty(this.model.get("porciento_ri"))) {
                    var percent = ((this.model.get("monto_comprometido") * this.model.get("porciento_ri")) / 100).toFixed(2);
                    this.model.set("renta_inicial_comprometida", percent);
                }
            }
        }
    },

    calcularPorcientoRI: function () {
        var ElaborationBacklog = this.getElaborationBacklog();
        var currentDay = (new Date).getDate();
        var currentMonth = (new Date).getMonth() + 1;
        var mesBL = this.model.get("mes") - 2;

        //CVV se cambia la validaci�n para permitir actualizar el BL hasta antes dek d�a 20
        //if (this.model.get("estatus_de_la_operacion") != 'Comprometida'){
        if (this.model.get("mes") >= ElaborationBacklog && this.model.get("estatus_operacion_c") == 'Comprometida') {
            if (currentDay <= 20 || (currentMonth == mesBL && currentDay > 20) || this.model.get("mes") > ElaborationBacklog) {
                if (this.model.get("renta_inicial_comprometida") == 0) {
                    this.model.set("porciento_ri", 0);
                } else {
                    if (!_.isEmpty(this.model.get("monto_comprometido")) && !_.isEmpty(this.model.get("renta_inicial_comprometida"))) {
                        var percent = ((this.model.get("renta_inicial_comprometida") * 100) / this.model.get("monto_comprometido")).toFixed(2);
                        this.model.set("porciento_ri", percent);
                    }
                }
            }
        }
    },

    igualaMontoFinalOpp: function (fields, errors, callback) {
        var ElaborationBacklog = this.getElaborationBacklog();
        var currentDay = (new Date).getDate();
        var currentMonth = (new Date).getMonth() + 1;
        var mesBL = this.model.get("mes") - 2;

        if (this.model.get("mes") >= ElaborationBacklog && this.model.get("estatus_operacion_c") == 'Comprometida') {
            if (currentDay <= 20 || (currentMonth == mesBL && currentDay > 20) || this.model.get("mes") > ElaborationBacklog) {
                this.model.set("monto_comprometido", this.model.get("monto_final_comprometido_c"));
                this.model.set("renta_inicial_comprometida", this.model.get("ri_final_comprometida_c"));
                app.alert.show("Moto Modificado", {
                    level: "info",
                    title: "El Monto de Operacion se igualara al Monto Final ya que el Backlog aun esta en revisión.",
                    autoClose: false
                });
            }
        }
        this.setEtapa();
        callback(null, fields, errors);
    },


    getElaborationBacklog: function () {
        //Obtiene el Backlog en elaboraci�n
        var currentDay = (new Date).getDate();
        var BacklogCorriente = (new Date).getMonth() + 1;

        if (currentDay > 20) { // Si ya cerro el periodo de elaboraci�n de promotor, el Backlog del siguiente mes (natural) se encuentra corriendo
            BacklogCorriente += 2;
        } else {
            BacklogCorriente += 1;
        }

        if (BacklogCorriente > 12) {  //Si resulta mayor a diciembre
            BacklogCorriente = BacklogCorriente - 12;
        }

        return BacklogCorriente;
    },

    setEtapa: function () {
        //Se aplican validaciones únicamente cuando el producto No es de CRÉDITO SIMPLE
        if (this.model.get('producto_c') != "2") {
            //Se recalcula la distribuci�n de montos en cada etapa
            var RI = 0;
            if (parseFloat(this.model.get("monto_final_comprometido_c")) > 0) {
                var RI = (parseFloat(this.model.get("ri_final_comprometida_c")) / parseFloat(this.model.get("monto_final_comprometido_c"))).toFixed(2);
            }
            //El monto en compras no se altera, el resto de lo comprometido se manda a Sin Solicitud
            var SinSolicitud = parseFloat(this.model.get("monto_final_comprometido_c")) - parseFloat(this.model.get("monto_con_solicitud_c"));
            var RISinSolicitud = SinSolicitud * RI;

            //Si el disponible NO alcanza para el resto de la operacion se manda el disponible a SinSolicitud y el resto a PROCESO
            if (parseFloat(this.model.get("monto_original")) < (SinSolicitud - RISinSolicitud)) {
                SinSolicitud = parseFloat(this.model.get("monto_original"));
            }
            if (SinSolicitud < 0) {
                SinSolicitud = 0;
            }
            RISinSolicitud = SinSolicitud * RI;

            //Asignamos montos Sin Solicitud al modelo
            this.model.set("monto_sin_solicitud_c", SinSolicitud);
            this.model.set("ri_sin_solicitud_c", RISinSolicitud);

            //El monto restante de la operacion se reparte en etapas de "Proceso"
            var MontoSolicitudes = parseFloat(this.model.get("monto_final_comprometido_c")) - parseFloat(this.model.get("monto_con_solicitud_c")) - parseFloat(SinSolicitud);
            if (parseFloat(MontoSolicitudes) > 0) {
                var RISolicitudes = parseFloat(MontoSolicitudes) * parseFloat(RI);
                if (parseFloat(this.model.get("monto_prospecto_c")) > 0) {
                    this.model.set("monto_prospecto_c", MontoSolicitudes);
                    this.model.set("ri_prospecto_c", RISolicitudes);
                    this.model.set("monto_credito_c", 0);
                    this.model.set("ri_credito_c", 0);
                    this.model.set("monto_rechazado_c", 0);
                    this.model.set("ri_rechazada_c", 0);
                }
                if (parseFloat(this.model.get("monto_credito_c")) > 0) {
                    this.model.set("monto_credito_c", MontoSolicitudes);
                    this.model.set("ri_credito_c", RISolicitudes);
                    this.model.set("monto_prospecto_c", 0);
                    this.model.set("ri_prospecto_c", 0);
                    this.model.set("monto_rechazado_c", 0);
                    this.model.set("ri_rechazada_c", 0);
                }
                if (parseFloat(this.model.get("monto_rechazado_c"), 0) > 0) {
                    this.model.set("monto_rechazado_c", MontoSolicitudes);
                    this.model.set("ri_rechazada_c", RISolicitudes);
                    this.model.set("monto_prospecto_c", 0);
                    this.model.set("ri_prospecto_c", 0);
                    this.model.set("monto_credito_c", 0);
                    this.model.set("ri_credito_c", 0);
                }
                if (parseFloat(this.model.get("monto_prospecto_c")) == 0 && parseFloat(this.model.get("monto_credito_c")) == 0 && parseFloat(this.model.get("monto_rechazado_c")) == 0) {
                    this.model.set("monto_prospecto_c", MontoSolicitudes);
                    this.model.set("ri_prospecto_c", RISolicitudes);
                    this.model.set("monto_credito_c", 0);
                    this.model.set("ri_credito_c", 0);
                    this.model.set("monto_rechazado_c", 0);
                    this.model.set("ri_rechazada_c", 0);
                }
            } else {
                this.model.set("monto_prospecto_c", 0);
                this.model.set("ri_prospecto_c", 0);
                this.model.set("monto_credito_c", 0);
                this.model.set("ri_credito_c", 0);
                this.model.set("monto_rechazado_c", 0);
                this.model.set("ri_rechazada_c", 0);
            }

        }

        //Calcula la etapa del Backlog
        //Si no tiene solicitud de compra se puede evaluar
        if (this.model.get("progreso") == 2 && this.model.get("etapa_c") == "3" && this.model.get("monto_original") > 0) {
            var MontoOperar = parseFloat(this.model.get("monto_final_comprometido_c")) - parseFloat(this.model.get("ri_final_comprometida_c"));
            if (parseFloat(this.model.get("monto_original")) >= MontoOperar) {
                this.model.set("etapa_c", "1");
                if (this.model.get("estatus_operacion_c") == 'Comprometida') {
                    this.model.set("etapa_preliminar_c", "1");
                }
            } else {
                this.model.set("etapa_c", "3");
                if (this.model.get("estatus_operacion_c") == 'Comprometida') {
                    this.model.set("etapa_preliminar_c", "3");
                }
            }
        }
    },

    setRI: function () {
        if (this.model.get("monto_final_comprometido_c") == 1) {
            this.model.set("ri_final_comprometida_c", 0);
        }
    },

    mesbacklog: function (fields, errors, callback) {
        var mes = this.model.get("mes");
        var mesActual = (new Date).getMonth();
        var anoActual = (new Date).getFullYear();
        var anobacklog = this.model.get('anio');
        var edicion = 0;


        if (anobacklog < anoActual) {
            edicion = 1;
        }
        if (mes < mesActual && anobacklog == anoActual) {
            edicion = 1;
        }
        if (edicion > 0) {
            app.alert.show("Edicion de Backlog", {
                level: "error",
                messages: "No se puede editar un Backlog pasado.",
                autoClose: false
            });
            errors['errorBacklog'] = errors['errorBacklog'] || {};
            errors['errorBacklog'].required = true;

        }
        callback(null, fields, errors);
    },

    valida_requeridos: function (fields, errors, callback) {
        var campos = "";
        _.each(errors, function (value, key) {
            _.each(this.model.fields, function (field) {
                if (_.isEqual(field.name, key)) {
                    if (field.vname) {
                        campos = campos + '<b>' + app.lang.get(field.vname, "lev_Backlog") + '</b><br>';
                    }
                }
            }, this);
        }, this);

        //VALIDA FECHA COMPROMISO
        if (this.model.get('fecha_compromiso_c') == '' || this.model.get('fecha_compromiso_c') == null) {
            campos = campos + '<b>' + 'Fecha compromiso' + '</b><br>';

            errors['fecha_compromiso_c'] = errors['fecha_compromiso_c'] || {};
            errors['fecha_compromiso_c'].required = true;
        }

        if (campos) {
            app.alert.show("Campos Requeridos", {
                level: "error",
                messages: "Hace falta completar la siguiente información en el <b>Backlog:</b><br>" + campos,
                autoClose: false
            });
        }
        callback(null, fields, errors);
    },

    probabilidadreq: function () {
        var mes = this.model.get("mes");
        var mesActual = (new Date).getMonth();
        var anoActual = (new Date).getFullYear();
        var anobacklog = this.model.get('anio');

        if (mes >= mesActual) {
            $('[data-name="tct_conversion_c"]').attr('style', 'pointer-events:block;');
        } else {
            $('[data-name="tct_conversion_c"]').attr('style', 'pointer-events:none;');
        }
        if (anobacklog > anoActual) {
            $('[data-name="tct_conversion_c"]').attr('style', 'pointer-events:block;');
        }
    },

    validacampoconversion: function (fields, errors, callback) {
        if (this.model.get('tct_conversion_c') == "" || this.model.get('tct_conversion_c') == undefined) {
            errors['tct_conversion_c'] = errors['tct_conversion_c'] || {};
            errors['tct_conversion_c'].required = true;
        }
        //Valda valor menor o igual a 0
        if (parseFloat(this.model.get('tct_conversion_c')) <= 0) {

            errors['tct_conversion_c'] = errors['tct_conversion_c'] || {};
            errors['tct_conversion_c'].required = true;

            app.alert.show("Campo con valor cero", {
                level: "error",
                messages: "El campo <b>Probabilidad de Conversión</b> debe ser mayor a cero.",
                autoClose: false
            });

        }
        // Valida valor mayor a 100
        if (parseFloat(this.model.get('tct_conversion_c')) > 100) {

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

    num_tipo_leasing: function (fields, errors, callback) {
        var tiposnum = app.lang.getAppListStrings('num_tipo_op_leasing_list');
        var data1 = this.model.get('tct_tipo_op_leasing_mls_c');
        var producto = this.model.get('producto_c');
        var salida = [];

        if (producto == "2") {
            if (this.model.get('comision_c') == undefined) {
                errors['comision_c'] = errors['comision_c'] || {};
                errors['comision_c'].required = true;
            }
            if (parseFloat(this.model.get('comision_c')) <= 0.0) {
                errors['tct_conversion_c'] = errors['tct_conversion_c'] || {};
                errors['tct_conversion_c'].required = true;
                app.alert.show("comision", {
                    level: "error",
                    messages: "El campo <b>Comisión</b> debe ser mayor a 0.",
                    autoClose: false
                });
            }
        }
        callback(null, fields, errors);
    },

    tipo_producto_requerido: function (fields, errors, callback) {
        var producto = this.model.get('producto_c');
        var salida = [];

        if (producto == "1") {
            if (this.model.get('num_tipo_op_leasing_c') == '' || this.model.get('num_tipo_op_leasing_c') == null) {
                errors['num_tipo_op_leasing_c'] = errors['num_tipo_op_leasing_c'] || {};
                errors['num_tipo_op_leasing_c'].required = true;
            }
        } else if (producto == "2") {
            if (this.model.get('num_tipo_op_credito_c') == '' || this.model.get('num_tipo_op_credito_c') == null) {
                errors['num_tipo_op_credito_c'] = errors['num_tipo_op_credito_c'] || {};
                errors['num_tipo_op_credito_c'].required = true;
            }
        }
        callback(null, fields, errors);
    },

    refinanciamientoblock: function () {
        var existe = 0;
        var usuario = App.user.attributes.id;
        var usuarios_refinanciamiento = app.lang.getAppListStrings('equipo_central_bl_list');
        Object.keys(usuarios_refinanciamiento).forEach(key => {
            if (usuarios_refinanciamiento[key] == usuario) {
                existe++;
            }
        });
        if (existe == 0) {
            $('[data-name="refinanciamiento_c"]').attr('style', 'pointer-events:none');
        }
    },

    /**
     * Valida que monto_comprometido <= monto_con_solicitud_c
     */
    validarMontoComprometido: function (fields, errors, callback) {
        
        var montoComprometido = parseFloat(this.model.get('monto_comprometido')) || 0;
        var montoSolicitud = parseFloat(this.model.get('monto_original')) || 0;
        // console.log("[Validación] monto_comprometido:", montoComprometido, "monto_con_solicitud_c:", montoSolicitud);
        // Si monto_comprometido es mayor que monto_con_solicitud_c
        if (montoComprometido > montoSolicitud) {

            app.alert.show('valida_monto_comprometido', {
                level: 'error',
                messages: 'El <b>Monto Comprometido</b> excede la línea aprobada.',
                autoClose: false
            });

            errors['monto_comprometido'] = errors['monto_comprometido'] || {};
            errors['monto_comprometido'].required = true;
        }
        callback(null, fields, errors);
    },

    /**
     * Valida dependencia estatus backlog
     */
    validaDependenciaDeclinada (fields, errors, callback) {

        var estatus = this.model.get('estatus_backlog_c') || '';
        var motivodecli = this.model.get('motivo_declinacion_c') || '';
        
        if (estatus == '2' && motivodecli=='') {

            app.alert.show('valida_declinacion', {
                level: 'error',
                messages: 'El <b>Motivo de Declinación</b> es requerido.',
                autoClose: false
            });

            errors['motivo_declinacion_c'] = errors['motivo_declinacion_c'] || {};
            errors['motivo_declinacion_c'].required = true;
        }
        callback(null, fields, errors);
    },

    /**
     * Valida permiso si el usuario puede editar el campo tipificacion_riesgo_c
     */
    checkPermisoTipificacion: function () {
        var permisoBacklogTipificacion = app.user.attributes.backlog_tipificacion_c ? 1 : 0;    
        var fieldTipificacionRiesgo = this.getField('tipificacion_riesgo_c');
        //Valida permiso backlog tipificación
        if (permisoBacklogTipificacion === 1) {
            if (fieldTipificacionRiesgo) {
                fieldTipificacionRiesgo.setDisabled(false);
            }            
        } else {
            if (fieldTipificacionRiesgo) {
                fieldTipificacionRiesgo.setDisabled(true);
            }
        }
    },

    reactiva_bkl: function () {
      if (this.model.get('estatus_backlog_c') == 2) {
        app.drawer.open({
          layout: 'reactiva_bkl',
                  context: {
            context: this.context,
                      model: this.model,
                  },
              }, function (context, model, update) {});
          } else {
        app.alert.show('NoDeclinada', {
          level: 'error',
                  messages: 'El Estatus Backlog es diferente a Declinada',
                  autoClose: false
              });
		  }
    },

    _readOnlyEstatusDeclinada: function () {
        //Bloquear el registro completo cuando Estatus Backlog es Declinada
        if (this.model.get('estatus_backlog_c') === '2') {
            $(".record-cell").attr("style", "pointer-events:none");
            $('[name="edit_button"].rowaction').hide();
        }
    },

})