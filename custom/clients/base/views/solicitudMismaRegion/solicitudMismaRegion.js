({
    extendsFrom: 'BaseView',  

    events: {        
    },

    initialize: function(options){
        this._super('initialize', [options]);

        //Extraer la parte después del #
        var hashParams = window.location.hash.split("?")[1];
        var urlParams = new URLSearchParams(hashParams);

        var id = urlParams.get('id') || 'No recibido';
        var accion = urlParams.get('accion') || 'No recibido';

        console.log('ID recibido:', id);
        console.log('Acción recibida:', accion);
        // Mostrar el parámetro en la consola

        // Definir aceptación y rechazo como booleanos
        this.aceptacion = accion === 'aceptar';
        this.rechazo = accion === 'rechazar';

        // Guardar en el objeto `this` para que estén disponibles en el HBS
        this.id = id;
        this.accion = accion;
       
        /*app.api.call("read", app.api.buildURL("Users/" + userId, null, null, {}), null, {
            success: _.bind(function (data) {
                var roleReasignacionPromotores = false;
                if(data.posicion_operativa_c.includes("2")) roleReasignacionPromotores = true;
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
        })*/
        this._render();
        
    },   

    _render: function () {
        this._super('_render');

        // Actualizar el contenido en el HTML
        this.$('.id-container').text(this.id);
        this.$('.accion-container').text(this.accion);
        this.$('.aceptacion-container').text(this.aceptacion ? 1 : 0);
        this.$('.rechazo-container').text(this.rechazo ? 1 : 0);
    }
    
})
