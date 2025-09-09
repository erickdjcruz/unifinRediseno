({
    className: 'reactiva_bkl',

    events: {
        'click #btn-cancelar': 'cancelar',
        'click #btn-solicitar-aprobacion': 'enviarAprobacion',
    },

    initialize: function (options) {
        this._super("initialize", [options]);
        var idBkl = options.context.get('model').id;

        if (this.model.get('aprueba_reactivacion_c')=='SOLICITUD'){
            error = true;
            app.alert.show('mensajeVacio', {
                level: 'error',
                messages: 'Solicitud de reactivación enviada previamente.',
                autoClose: true
            });
            var modal = $('#reactiva_bkl');
            if (modal) {
                modal.hide();
            }
            app.drawer.close();
        }        
    },

    cancelar: function(){
        var modal = $('#reactiva_bkl');
        if (modal) {
            modal.hide();
        }
        app.drawer.close();
    },

    enviarAprobacion: function(){
        // Obtener el timestamp actual
        var timestamp = Date.now(); // milisegundos desde 1970-01-01
        // Convertir a objeto Date
        var dateObj = new Date(timestamp);
        // Formato SugarCRM DATETIME (YYYY-MM-DD HH:mm:ss)
        var formattedDateTime = app.date(dateObj).format('YYYY-MM-DD HH:mm:ss');

        var mensajeCorreo= $('#motivo-reactivacion').val();
        var error = false;
        if( mensajeCorreo.trim() == "" ){
            error = true;
            app.alert.show('mensajeVacio', {
                level: 'error',
                messages: 'Favor de ingresar el motivo de reactivación',
                autoClose: true
            });
        }
        if (mensajeCorreo.length > 300 ){
            error = true;
            app.alert.show('mensajeVacio', {
                level: 'error',
                messages: 'El texto de Motivo de reactivación no tiene que ser mayor a 300 caracteres.',
                autoClose: true
            });
        }
        
        if(!error){
            var body={
                "idRegistro" : this.model.get('id'),
                "motivo_reactivacion_c" : mensajeCorreo.toUpperCase(),
                "aprueba_reactivacion_c" : "SOLICITUD",
                "fecha_sol_reactivacion_c" : formattedDateTime
            }
            app.alert.show('setEnviandoCorreoDirector', {
                level: 'process',
                closeable: false,
                messages: app.lang.get('LBL_LOADING'),
            });
            $('#btn-solicitar-aprobacion').attr('disabled', true);
            app.api.call('create', app.api.buildURL("reactiva_bkl"), body, {
                success: _.bind(function (data) {
                    $('#btn-solicitar-aprobacion').removeAttr('disabled');
                    app.alert.dismiss('setEnviandoCorreoDirector');
                    app.alert.show('correoEnviado', {
                        level: 'success',
                        messages: data['msj'],
                        autoClose: false
                    });
                    var modal = $('#reactiva_bkl');
                    if (modal) {
                        modal.hide();
                    }
                    app.drawer.close();
                }, this),
                error: _.bind(function (response) {
                    app.alert.show('error', {
                        level: 'error',
                        messages: response,
                        autoClose: false
                    });
                },this)
            });
        }
    },
})