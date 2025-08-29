({
    className: 'reactiva_bkl',

    events: {
        'click #btn-cancelar': 'cancelar',
        'click #btn-solicitar-aprobacion': 'enviarAprobacion',
    },

    initialize: function (options) {
        this._super("initialize", [options]);
        var idBkl = options.context.get('model').id;
    },

    cancelar: function(){
        var modal = $('#reactiva_bkl');
        if (modal) {
            modal.hide();
        }
        app.drawer.close();
    },

    enviarAprobacion: function(){
        var mensajeCorreo= $('#motivo-reactivacion').val();
        if( mensajeCorreo.trim() == "" ){
            app.alert.show('mensajeVacio', {
                level: 'error',
                messages: 'Favor de ingresar el motivo de reactivación',
                autoClose: true
            });
        }else{
            var body={
                "idRegistro" : this.model.get('id'),
                "mensaje" : mensajeCorreo.toUpperCase()
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