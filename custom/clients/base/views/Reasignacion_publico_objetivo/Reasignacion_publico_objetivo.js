/**
 * Created by Jorge on 7/17/2015.
 */
/**
 * Created by Jorge on 6/1/2015.
 */
({

    className: 'Reasignacion_publico_objetivo',

    events: {
        'click #btn_PO': 'buscarPO',
        'click #btn_ReAsignar': 'reAsignarPO',
        'click #btn_STodo': 'seleccionarTodo',
    },

    initialize: function(options){
        this._super("initialize", [options]);

        this.cuentas = '';
        this.seleccionados = [];
        this.objEtiquetaID = {};
        this.persistNoSeleccionados=[];
        this.flagSeleccionados=0;
        this.tipo_cuenta = App.lang.getAppListStrings('tipo_registro_cuenta_list');
        delete this.tipo_cuenta[1];
        this.model.on('change:users_accounts_1users_ida', this._setOffset, this);

        var privilegioReassignPO = App.user.get('reasignacion_po_c');
        var typeuser= App.user.get('type'); //'admin'

        
        if (privilegioReassignPO || typeuser=='admin') {
            this.loadView = true;
        }else{
            var route = app.router.buildRoute(this.module, null, '');
            app.router.navigate(route, {trigger: true});
        }
    },

    _render: function () {
        this._super("_render");
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

        var btnState = $(e.target).attr("btnState");
        if(btnState == "Off"){
            $(e.target).attr("btnState", "On");
            btnState='On';
        }else{
            $(e.target).attr("btnState", "Off");
            btnState='Off';
        }

        $('.checksPO').each(function (index, value) {
            if(btnState == "On"){
                $(value).prop('checked', true);
            }else{
                $(value).prop('checked', false);
            }
        });

    },

    buscarPO: function(flagClean=0){
        //Establece objeto vacio de las cuentas seleccionadas y desmarca los check seleccionados

        if(flagClean != 1){
            this.objEtiquetaID = {};
            $("#offset_value").attr("from_set", 0);
            $("#crossSeleccionados").val("");
        }

        var assigneUsr = this.model.get('users_accounts_1_name');
        var assignedUserId = this.model.get('users_accounts_1users_ida');
        //Condición para controlar la búsqueda cuando no se ha seleccionado Promotor, esto sucede cuando se da click en el icono con el tache
        //dentro del campo Asesor Actual con formato select2
        if(assigneUsr==""){
            assigneUsr=undefined;
        }

        if((_.isEmpty(assigneUsr) || _.isUndefined(assigneUsr) || assigneUsr == "") ) {
            var alertOptions = {
                title: "Error",
                messages: "Por favor, seleccione un Asesor",
                level: "error"
            };
            app.alert.show('validation', alertOptions);
            return;
        }

       
        var from_set = $("#offset_value").attr("from_set");
        var from_set_num = parseInt(from_set);
        var filtroPO = $("#filtroPO").val();
    
        var urlFilter = "";
        //Si no se establece valor en nombre de Prospecto, se buscan todos los registros asignados al usuario seleccionado
        if( filtroPO != "" ){
            urlFilter = 'Prospects?view=list&filter[0][$or][0][first_name][$starts]='+ filtroPO.trim() +'&filter[0][$or][1][last_name][$starts]='+ filtroPO.trim() +'&filter[0][$and][0][assigned_user_id][$in][]='+ assignedUserId +'&fields=name_c,regimen_fiscal_c,nombre_empresa_c,nombre_c,apellido_paterno_c,apellido_materno_c,detalle_origen_c,origen_c,estatus_po_c,subestatus_po_c,assigned_user_name&max_num=-1';
        }else{
            urlFilter = 'Prospects?view=list&filter[0][$and][0][assigned_user_id][$in][]=' + assignedUserId + '&fields=name_c,regimen_fiscal_c,nombre_empresa_c,nombre_c,apellido_paterno_c,apellido_materno_c,detalle_origen_c,origen_c,estatus_po_c,subestatus_po_c,assigned_user_name&max_num=-1';
        }

        if(!_.isEmpty(assigneUsr) && !_.isUndefined(assigneUsr) && assigneUsr != "") {
            this.seleccionados = [];
            $('#successful').hide();
            $('#processing').show();
            app.api.call("read", app.api.buildURL(urlFilter, null, null, {}), null, {
                success: _.bind(function (data) {
                    // console.log(typeof data);
                    // console.log(data);
                    if (data.records.length == 0) {
                        var alertOptions = {
                            title: "Sin registros",
                            messages: "No se encontraron registros para el usuario seleccionado",
                            level: "error"
                        };
                        app.alert.show('validation', alertOptions);
                        $('#processing').hide();
                        return;
                    }else{
                        this.prospects = data.records;
                        this.render();
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

    reAsignarPO: function(){

        self=this;
        var reAssignarA = this.model.get('asignar_a_promotor_id');
        var promoActual = this.model.get('users_accounts_1users_ida');

        var ids_seleccionados = [];

        if(this.flagSeleccionados==1){
            this.seleccionados=this.full_cuentas;
        }
        
        if(!_.isEmpty(reAssignarA) && !_.isEmpty(promoActual)) {
            //Obteniendo ids de las cuentas que tienen el check activo
            ids_seleccionados = this.getProspectsSeleccionados();

        }else{
            var alertOptions = {
                title: "Error",
                level: "error",
                messages:"Por favor, seleccione tanto el Asesor Actual como el Asesor Destino"
            };
            app.alert.show('validation', alertOptions);
        }

        if( ids_seleccionados.length == 0 ){
            var alertOptions = {
                title: "Error",
                level: "error",
                messages: "Al menos seleccione un registro para reasignar"
            };
            app.alert.show('validation', alertOptions);
        }else{
            //Antes de reasignar, validar que el usuario no se encuentre INACTIVO
            App.alert.show('validaUser', {
                level: 'process',
                title: 'Cargando',
            });

            app.api.call('GET', app.api.buildURL('Users/' + reAssignarA), null, {
                success: function (user) {
                    App.alert.dismiss("validaUser");
                    if( user.status == "Inactive" ){
                        var alertOptionsInactive = {
                            title: "Error",
                            level: "error",
                            messages: "No se pueden reasignar registros a " + user.full_name + " ya que se encuentra Inactivo"
                        };
                        app.alert.show('validation', alertOptionsInactive);

                    }else{

                        //Reasignar
                        var paramsReasignProspects = {
                            'prospects': ids_seleccionados,
                            'reasignado': reAssignarA,
                        };

                        app.alert.show('reasignando', {
                            level: 'process',
                            title: 'Cargando...'
                        });
                        var urlProspectsReassign = app.api.buildURL("reasignarPO", '', {}, {});

                        app.api.call("create", urlProspectsReassign, {data: paramsReasignProspects}, {
                            success: _.bind(function (data) {
                                console.log(typeof data);
                                if(data){
                                    app.alert.dismiss('reasignando');
                                    self.prospects = [];
                                    self.seleccionados = [];
                                    self.render();
                                    $('#successful').show();
                                    self.model.set("users_accounts_1users_ida","");
                                    self.model.set("users_accounts_1_name","");
                                    self.model.set("asignar_a_promotor_id","");
                                    self.model.set("asignar_a_promotor","");
                                }
                            }, this)
                        });

                    }
                },
                error: function (e) {
                    throw e;
                }
            });

        }
    },

    getProspectsSeleccionados:function(){
        var ids_seleccionados = [];
        
        $('.checksPO').each(function (index, value) {
                
            if( $(this).is(':checked') ){
                var id_prospect = $(this).val();
                ids_seleccionados.push(id_prospect);
            }
        });

        return ids_seleccionados;
    }

})
