({
    extendsFrom: 'BaseView',  

    events: {
        'submit #comentarioForm': 'enviarComentario'
    },

    initialize: function(options){
        this._super('initialize', [options]);
        this.resultado = 0;
      
        //Extraer la parte después del #
        var hashParams = window.location.hash.split("?")[1];
        var urlParams = new URLSearchParams(hashParams);

        this.id = urlParams.get('id') || 'No recibido';
        this.accion = urlParams.get('accion') || 'No recibido';

        console.log('ID recibido:', this.id);
        console.log('Acción recibida:', this.accion);
        // Mostrar el parámetro en la consola

        this._render();
        
    },   

    enviarComentario: function(event) {
        event.preventDefault(); // Evita recargar la página

        var comentarios = this.$('#comentarios').val().trim();

        // Validación de longitud de comentario
        if (comentarios.length < 150 || comentarios.length > 500) {
            this.mostrarMensaje("El comentario debe tener entre 150 y 500 caracteres.", "error");
            return;
        }

        var data = {
            id: this.id,
            accion: this.accion,
            comentarios: comentarios
        };

        // Llamada al API
        app.api.call("create", app.api.buildURL("customEndpointAsignacion"), data, {
            success: _.bind(function(response) {
                if (response.status === '200') {
                    var mensaje = (this.accion === 'aceptar') ? 
                        this.aceptacion = 1 :
                        this.rechazo = 1;
                    
                    this.mostrarMensaje(mensaje, "success");
                } else {
                    this.mostrarMensaje("Se ha presentado un error.", "error");
                }
            }, this),
            error: _.bind(function() {
                this.mostrarMensaje("Error en la solicitud.", "error");
            }, this)
        });
    },

    mostrarMensaje: function(texto, tipo) {
        var mensajeDiv = this.$('#mensaje');
        mensajeDiv.removeClass().addClass('message ' + tipo).text(texto);
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
