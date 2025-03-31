({
    className: 'asignacion_asesores',

    events: {
        'click #btn_Cuentas': 'buscarCuentas',
        'click #btn_ReAsignar': 'reAsignarCuentas',
        'click .selected': 'seleccionarCuentas',
        'click #btn_STodo': 'seleccionarTodo',
        'click #next_offset': 'nextOffset',
        'click #previous_offset': 'previousOffset',
        'change #filtroCliente': '_setOffset',
		'change .usuarios': 'actualizaUsuarios',
        'click .btnSubir': 'leerCSVReasignar',
    },

    initialize: function(options){
        this._super("initialize", [options]);
        this.cuentas = '';
		this.entra = false;
        this.seleccionados = [];
        this.objEtiquetaID = {};
        this.persistNoSeleccionados=[];
        this.flagSeleccionados=0;
        this.tipo_cuenta = App.lang.getAppListStrings('tipo_registro_cuenta_list');
		delete this.tipo_cuenta[""];
        delete this.tipo_cuenta[1];
        this.model.on('change:users_accounts_1users_ida', this._setOffset, this);
        this.loadView = false;
        var userId = App.user.id;
		asesores = this;
        app.api.call("read", app.api.buildURL("Users/" + userId, null, null, {}), null, {
            success: _.bind(function (data) {
				var strUrl = 'Users?fields=id,nombre_completo_c&order_by=nombre_completo_c:asc&max_num=-1&filter[][status]=Active&filter[][reports_to_id][$equals]='+userId;
				var usuarios_aa = App.lang.getAppListStrings('asignacion_asesores_list');
				this.entra = Object.values(usuarios_aa).includes(userId);
				if(this.entra) strUrl = 'Users?fields=id,nombre_completo_c&order_by=nombre_completo_c:asc&max_num=-1&filter[][status]=Active';
				app.api.call("GET", app.api.buildURL(strUrl), null, {
					success: _.bind(function (data1) {
						if(data1.records.length > 0) {
							var usuario = {};
							usuario[''] = '';
							for(var i = 0; i < data1.records.length; i++) {
								usuario[data1.records[i].id] = data1.records[i].nombre_completo_c;
							}
							usuario[userId] = App.user.attributes.full_name;
							asesores.usuarios_list = usuario;
						}
						var roleReasignacionPromotores = false;
						if(data.posicion_operativa_c.includes("2") || this.entra) roleReasignacionPromotores = true;
						if(roleReasignacionPromotores == true){
							this.obtenerProductosUsuario();
							this.loadView = true;
							this.render();
						}else{
							app.alert.show("asignacion_asesores",{
								level: "error",
								title: "Error",
								messages: "No tiene permisos suficientes para reasignar cuentas",
								autoClose: false
							});					
							var route = app.router.buildRoute(this.module, null, '');
							app.router.navigate(route, {trigger: true});
						}
					}, this)
				});
            }, this)
        });
    },

    _render: function () {
        this._super("_render");
        var tipos_cuenta=[];
         this.$('#tipo_de_cuenta').select2({
             width:'400px',
             closeOnSelect: false,
             containerCssClass: 'select2-choices-pills-close'
         });
        for (var key in this.tipo_cuenta) {
            if (this.tipo_cuenta.hasOwnProperty(key)) {
                tipos_cuenta.push(key);
            }
        }
        this.$("#tipo_de_cuenta").select2('val', tipos_cuenta);
    },

    _setOffset: function (){
        $("#offset_value").attr("from_set", 0);
        $("#crossSeleccionados").val("");
    },

    leerCSVReasignar:function () {
        var fileInput = document.getElementById('csvReasignar');
        var archivo = fileInput.value;
        if(archivo=="" || archivo==undefined){
            app.alert.show('errorAlert', {
                level: 'error',
                messages: 'Favor de elegir un archivo',
                autoClose: true
            });
        }else{
            app.alert.show('reasignandoCSV', {
              level: 'process',
              title: 'Cargando...'
            });
            $('.btnSubir').addClass('disabled');
            $('.btnSubir').attr('style', 'pointer-events:none;margin:10px');
            var file = fileInput.files[0];
            var nombre = file.name;
            var textType = /text.*/;
            self=this;
            var reader = new FileReader();
            reader.onload = function(e) {
                var content = reader.result;
                var archivocsv = {
                    "documento":content,
                    "archivo":nombre,
                    "tipo":'reasignar'
                };
                var seguir=true;
                if(content.trim() ==""){
                    $('.btnSubir').removeClass('disabled');
                    $('.btnSubir').attr('style', 'margin:10px');
                    app.alert.dismiss('reasignandoCSV');
                    app.alert.show('csvVacio', {
                        level: 'error',
                        messages: 'Archivo sin contenido, favor de elegir un archivo v\u00E1lido',
                        autoClose: false
                    });
                    seguir=false;
                }
                if(seguir){
                    var registros=content.split('\n');
                    var flag=self.vaidateEmptyContent(registros);
                    if(!flag){
                      var Url = app.api.buildURL("guardaCSV", '', {}, {});
                      app.api.call("create", Url, {data: archivocsv}, {
                          success: _.bind(function (data) {
                            $('.btnSubir').removeClass('disabled');
                            $('.btnSubir').attr('style', 'margin:10px');
                            app.alert.dismiss('reasignandoCSV');
                            app.alert.show('csvOK', {
                                level: 'success',
                                messages: 'Archivo cargado con éxito. Le llegará un correo con el resultado de la actualización',
                                autoClose: false
                            });
                            self.render();
                          },this),
                          error: function (e) {
                            throw e;
                          }
                      });
                    }else{
                      $('.btnSubir').removeClass('disabled');
                      $('.btnSubir').attr('style', 'margin:10px');
                      app.alert.dismiss('reasignandoCSV');
                      app.alert.show('csvVacio', {
                          level: 'error',
                          messages: 'Archivo sin contenido, favor de elegir un archivo v\u00E1lido',
                          autoClose: false
                      });
                    }
                }
            }
            reader.readAsText(file);
        }
    },

    vaidateEmptyContent:function (registros) {

        var counter=0;

        var flag=false;
        var numero_elementos=registros.length;
        if(numero_elementos!=undefined && numero_elementos>0){
            //Empieza desde el indice 1, pues se asume que el primer renglón es el de las cabeceras
            for(var i=1;i<numero_elementos;i++){
                if(registros[i].trim()==""){
                    counter++;
                }
            }
            if(counter==numero_elementos-1){
                flag=true;
            }
        }

        return flag;
    },

    seleccionarTodo: function(e){

        if(this.persistNoSeleccionados!=undefined && this.persistNoSeleccionados.length>0){

            for(var i=0;i<this.persistNoSeleccionados.length;i++){

                //Añadir el elemento solo si no existe en arreglo full_cuentas
                if(!this.full_cuentas.includes(this.persistNoSeleccionados[i])){
                    this.full_cuentas.push(this.persistNoSeleccionados[i])
                }
            }

            this.persistNoSeleccionados=[];

        }else{

            this.persistNoSeleccionados=[];
        }


        //Control de bandera para saber si se ha oprimido el botón de "Seleccionar Todo" y de esta manera saber si el siguiente "offset" de la tabla
        // debe mostrarse con los check como true
        if(this.flagSeleccionados==0){
            this.flagSeleccionados=1;
        }else {
            this.flagSeleccionados=0;
        }
        var btnState = $(e.target).attr("btnState");
        if(btnState == "Off"){
            $(e.target).attr("btnState", "On");
            btnState='On';
        }else{
            $(e.target).attr("btnState", "Off");
            btnState='Off';
        }

        $('.selected').each(function (index, value) {
            if(btnState == "On"){
                //$(value).attr("checked", true);
                $(value).prop('checked', true);
            }else{
                //$(value).attr("checked", false);
                $(value).prop('checked', false);
            }
        });

        var seleccionarTodo = [];
        var crossSeleccionados = $("#crossSeleccionados").val();
        if(!_.isEmpty(crossSeleccionados)) {
            seleccionarTodo = JSON.parse(crossSeleccionados);
        }

        if($('.selected').prop("checked")) {
            $(this.cuentas).each(function (index, value) {
                seleccionarTodo.push(value.id);
            });
        }else{
            seleccionarTodo = [];
        }

        this.seleccionados = seleccionarTodo;
        $("#crossSeleccionados").val(JSON.stringify(this.seleccionados));
    },

    actualizaUsuarios: function(evt) {
        var inputs = this.$('[data-field="user_id"].updateusr'),
            input = this.$(evt.currentTarget),
            index = inputs.index(input); 
        this.cuentas[index].nuevo = input.val();
    },

    nextOffset: function(){
        var current_set = $("#offset_value").html();
        var from_set = $("#offset_value").attr("from_set");
        var next_from_set = parseInt(from_set) + 20;
        var to_set = $("#offset_value").attr("to_set");
        var next_to_set = parseInt(to_set) + 20;

        if(next_to_set > this.total){
            next_to_set = this.total;

            if(from_set > 0){
                next_from_set = from_set;
            }else{
                next_from_set = next_from_set;
            }
        }

        $("#offset_value").html(current_set);
        $("#offset_value").attr("from_set", next_from_set);
        $("#offset_value").attr("to_set", next_to_set);
        this.buscarCuentas(1);
    },

    previousOffset: function(){
        var current_set = $("#offset_value").html();
        var from_set = $("#offset_value").attr("from_set");
        var next_from_set = parseInt(from_set) - 20;
        var to_set = $("#offset_value").attr("to_set");
        var next_to_set = parseInt(to_set) - 20;

        if(next_from_set < 0){
            next_from_set = 0;
            next_to_set = 20;
        }

        $("#offset_value").html(current_set);
        $("#offset_value").attr("from_set", next_from_set);
        $("#offset_value").attr("to_set", next_to_set);
        this.buscarCuentas(1);
    },

    buscarCuentas: function(flagClean=0){
        //Establece objeto vacio de las cuentas seleccionadas y desmarca los check seleccionados
        if(flagClean != 1){
            this.objEtiquetaID = {};
            $("#offset_value").attr("from_set", 0);
            $("#crossSeleccionados").val("");
        }
		var userId = App.user.id;
		if (this.entra) userId = '';
        var assigneUsr = $("#usuario_actual").val();
        //Condición para controlar la búsqueda cuando no se ha seleccionado Promotor, esto sucede cuando se da click en el icono con el tache
        //dentro del campo Asesor Actual con formato select2
        if(assigneUsr==""){
            assigneUsr=undefined;
        }
        var tipos_seleccionados=this.$(".tipo_cuenta").select2('val');
        if((_.isEmpty(assigneUsr) || _.isUndefined(assigneUsr) || assigneUsr == "") && (tipos_seleccionados.includes('Prospecto') || tipos_seleccionados.includes('Cliente') || tipos_seleccionados.includes('Lead'))) {
            var alertOptions = {
                title: "Por favor, seleccione un Asesor",
                level: "error"
            };
            app.alert.show('validation', alertOptions);
            return;
        }
        var producto_seleccionado = $("#Productos").val();
        var from_set = $("#offset_value").attr("from_set");
        var to_set = $("#offset_value").attr("to_set");
        var current_set = $("#offset_value").html();
        var from_set_num = parseInt(from_set);
        var filtroCliente = $("#filtroCliente").val();
        var crossSeleccionados = $("#crossSeleccionados").val();
        var filtroTipoCuenta=$("#tipo_de_cuenta").select2('val');
        if(_.isEmpty($("#tipo_de_cuenta").select2('val'))){
            var alertOptions = {
                title: "Por favor, seleccionar al menos un Tipo de Cuenta",
                level: "error"
            };
            app.alert.show('validation', alertOptions);
            return;
        }
        if(isNaN(from_set_num)){
            from_set_num = 0;
        }
        assigneUsr += "?PRODUCTO=" + producto_seleccionado + "?" + from_set_num + "?" + filtroCliente + "&tipos_cuenta="+filtroTipoCuenta.toString() + "&Director=" + userId;
        if(!_.isEmpty(assigneUsr) && !_.isUndefined(assigneUsr) && assigneUsr != "") {
            this.seleccionados = [];
            $('#successful').hide();
            $('#processing').show();
            app.api.call("read", app.api.buildURL("AsignacionAsesoresBusqueda/" + assigneUsr, null, null, {}), null, {
                success: _.bind(function (data) {
                    if (data.total <= 0) {
                        var alertOptions = {
                            title: "No se encontraron registros para el usuario seleccionado del producto: " + producto_seleccionado,
                            level: "info"
                        };
                        app.alert.show('validation', alertOptions);
                        $('#processing').hide();
                        return;
                    }
                    $('#processing').hide();
                    this.cuentas = typeof data=="string"?null:data.cuentas;
                    this.total = data.total;
                    this.total_cuentas = data.total_cuentas;
                    //Controlar los checks deseleccionados para que persistan al volver a obtener valores de la petición al api
                    if(this.persistNoSeleccionados!=undefined && this.persistNoSeleccionados.length>0){
                        var tempArray=data.full_cuentas;
                        for(var i=0;i<this.persistNoSeleccionados.length;i++){
                            if(tempArray.includes(this.persistNoSeleccionados[i])){
                                var index = tempArray.indexOf(this.persistNoSeleccionados[i]);
                                if (index > -1) {
                                  tempArray.splice(index, 1);
                                }
                            }
                        }
                        this.full_cuentas=tempArray;
                    }else{
                        this.full_cuentas=data.full_cuentas;
                    }
                    //Se obtiene valor de Tipo de Cuenta, para que persista al aplicar render
                    var valores=$("#tipo_de_cuenta").select2('val');
                    this.render();
                    $("#tipo_de_cuenta").select2('val',valores);
                    if(this.flagSeleccionados==1){
                        $('#btn_STodo').attr('btnstate','On');
                        var context=this;
                        $('.selected').each(function (index, value) {
                            //Validación para persistir valores de los checks en caso de que se haya "Seleccionado Todo"
                            //y se hayan deseleccionado registros individualmente
                            if(context.persistNoSeleccionados!=undefined && context.persistNoSeleccionados.length>0){
                                if(context.persistNoSeleccionados.includes($(this).attr('value'))){
                                    $(value).prop("checked", false)
                                }else{
                                    $(value).prop("checked", true);
                                }
                            }else{

                                $(value).prop("checked", true);

                            }
                        });
                    }
                    $("#Productos").val(producto_seleccionado);
                    if(to_set > this.total){
                        to_set = this.total;
                    }else{
                        to_set = from_set_num + data.total_cuentas;
                    }
                    current_set = (parseInt(from_set) + 1) + " a " + to_set + " de " + this.total;
                    if(_.isEmpty(from_set)){
                        from_set = 0;
                        to_set = 20;
                        if(to_set > this.total){
                            to_set = this.total;
                        }
                        current_set = (parseInt(from_set) + 1) + " a " + to_set + " de " + this.total;
                    }
                    $("#offset_value").html(current_set);
                    $("#offset_value").attr("from_set", from_set);
                    $("#offset_value").attr("to_set", to_set);
                    $("#filtroCliente").val(filtroCliente);
                    if(!_.isEmpty(crossSeleccionados)) {
                        context=this;
                        this.seleccionados = JSON.parse(crossSeleccionados);
                        //Validar que los nuevos checks seleccionados no existen en crossSeleccionados
                        $('.selected').each(function (index, value) {
                            if( $(value).attr("checked")=="checked" && context.flagSeleccionados==1 || $(value).attr("checked")==true && context.flagSeleccionados==1){
                                if(!context.seleccionados.includes(value.value) && value.value!=0){
                                    context.seleccionados.push(value.value)
                                }
                            }
                        });
                        $("#crossSeleccionados").val(JSON.stringify(this.seleccionados));
                        $(this.seleccionados).each(function (index, selected) {
                            $('.selected').each(function (index, value) {
                                if(selected == value.value){
                                    $(value).prop("checked", true);
                                }
                            });
                        });
                    }
                }, this)
            });
        }else{
            var alertOptions = {
                title: "Por favor, seleccione un asesor",
                level: "error"
            };
            app.alert.show('validation', alertOptions);
        }
    },

    seleccionarCuentas: function(e){
        var seleccionarTodo = [];
        var crossSeleccionados = $("#crossSeleccionados").val();
        if(!_.isEmpty(crossSeleccionados)) {
            seleccionarTodo = JSON.parse(crossSeleccionados);
        }
        if($(e.target).is(':checked')){
            seleccionarTodo.push($(e.target).val());
            this.seleccionados = seleccionarTodo;
            this.objEtiquetaID[$(e.target).val()]=$(e.currentTarget).attr('title'); //OBJ DEL ID Y LA ETIQUETA DE REGISTROS SELECCIONADOS
        }else{
            var itemToRemove = $(e.target).val();
            var seleccionadosClone = seleccionarTodo;
            var seleccionadosCleaned = [];
            this.seleccionados = [];
            $(seleccionadosClone).each(function( index,value ) {
                if(value != itemToRemove){
                    seleccionadosCleaned.push(value);
                }
            });
            this.seleccionados = seleccionadosCleaned;
            // console.log(this.seleccionados);
            delete this.objEtiquetaID[itemToRemove]; //ELIMINA LOS ID Y ETIQUETA DE LOS REGISTROS DESMARCADOS
        }
        //Validación para controlar checks seleccionados en caso de que se hayan seleccionado todos los registros
        //(Click en Selecionar Todo)
        if(this.full_cuentas.length >0 && this.full_cuentas != undefined){
            var idAccount=$(e.target).val();
            if(this.full_cuentas.includes(idAccount)){
                //Validar que el check ha sido seleccionado o no, en caso de que no se haya "activado", se elimina del arreglo de full_cuentas
                if($(e.target).prop("checked")==false){
                    var position=this.full_cuentas.indexOf(idAccount)
                    this.full_cuentas.splice(position,1);
                    this.persistNoSeleccionados.push(idAccount);
                }
            }else{
                if($(e.target).prop("checked")){
                    var position=this.full_cuentas.indexOf(idAccount)
                    this.full_cuentas.splice(position,0,idAccount);
                }
            }
        }
        $("#crossSeleccionados").val(JSON.stringify(this.seleccionados));
    },

    reAsignarCuentas: function(){
        //CONTEO DE NUMERO DE PROSPECTOS SELECCIONADOS
        var countProspecto = 0;
        Object.entries(this.objEtiquetaID).forEach(([key, value]) => {
            if (value == 'Prospecto'){
                countProspecto++;
            }
        });
        self=this;
        var reAssignarA = this.model.get('asignar_a_promotor_id');
        var promoActual = this.model.get('users_accounts_1users_ida');
        var radioBl="";
        if($('#optRadioSig:checked').val()=='on'){
            radioBl="siguientes";
        }else{
            radioBl="actualSiguientes";
        }
        if(this.flagSeleccionados==1){
            this.seleccionados=this.full_cuentas;
        }
		var pasa = 0;
		var seleccionadosFull = [];
        $(this.seleccionados).each(function(index,value) {
			arr = self.cuentas.find(cuenta => cuenta.id === value);
            seleccionadosFull.push({modulo:arr.modulo,tipo:arr.tipo,id:arr.id,viejo:arr.idu,nuevo:arr.nuevo});
			if(!_.isEmpty(arr.nuevo)) {
				pasa = 1;
			} else {
				pasa = 0;
			}
        });
        if(pasa) {
            //Comprobando que el asesor seleccionado destino, no tengo más de 20 registros asignados a el
            var id_user=reAssignarA;
            App.alert.show('reasignando', {
                    level: 'process',
                    title: 'Cargando',
            });
            var parametros = seleccionadosFull;
            var producto_seleccionado = $("#Productos").val();
            if(!_.isEmpty(parametros)) {
                var Params = {
                    'optBl':radioBl,
                    'seleccionados': parametros,
                    'reAssignado': reAssignarA,
                    'producto_seleccionado': producto_seleccionado,
                    'promoActual': promoActual,
                };
                var dnbProfileUrl = app.api.buildURL("AsignacionAsesores", '', {}, {});
                app.api.call("create", dnbProfileUrl, {data: Params}, {
                    success: _.bind(function (data) {
                        console.log(typeof data);
                        if(data){
                            app.alert.dismiss('reasignando');
                            self.cuentas = [];
                            self.seleccionados = [];
                            self.render();
                            $('#successful').show();
                            self.model.set("users_accounts_1users_ida","");
                            self.model.set("users_accounts_1_name","");
                            self.model.set("asignar_a_promotor_id","");
                            self.model.set("asignar_a_promotor","");
                        }else{
                            app.alert.dismiss('reasignando');
                            var alertOptions = {
                                title: "El tipo de producto entre el asesor actual y reasignado debe ser el mismo",
                                level: "error"
                            };
                            app.alert.show('validation', alertOptions);
                        }
                    }, this)
                });
            }else{
                var alertOptions = {
                    title: "No hay clientes seleccionados para reasignar",
                    level: "error"
                };
                app.alert.show('validation', alertOptions);
            }
        }else{
            var alertOptions = {
                title: "Por favor, seleccione tanto el Asesor Actual como el Asesor Destino",
                level: "error"
            };
            app.alert.show('validation', alertOptions);
        }
    },

    obtenerProductosUsuario: function (){
		var list_html = '';
        var userId = App.user.id;
        this.productos_list = [];
        var productos_label = app.lang.getAppListStrings('tipo_producto_list');
		list_html += '<option value="LEASING">LEASING</option>';
        this.productos_list = list_html;
    },

})
