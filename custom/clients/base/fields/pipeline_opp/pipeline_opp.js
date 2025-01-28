({

    initialize: function (options) {
        //Inicializa campo custom
        options = options || {};
        options.def = options.def || {};
        pipeopp=this;

        this._super('initialize', [options]);
        this.model.on('sync', this.pipelineopp, this);
    },

    _render: function () {
        this._super("_render");
    },

    pipelineopp: function (){
        //Obtiene Etapa y SubEtapa de la solicitud
        var etapa= this.model.get('tct_etapa_ddw_c');
        var subetapa=this.model.get('estatus_c');
        var producto=this.model.get('tipo_producto_c');
        var negocio = this.model.get('negocio_c');

      //Validaciones para asignar etapas de la solicitud en formado Pipeline SOLICITUD INICIAL
        if (etapa=="SI"){
            //Agrega clase current para sombrear en color actual al pipeline
            $('#SI').addClass('current');
            //Añade sub etapa (solo para Leasing)
            if(typeof banderaExcluye !="undefined"){

                if ((producto==1 || (producto=="2" && negocio!="2" && negocio!="10")) && subetapa==1 && (banderaExcluye.check.length==0 || banderaExcluye.check.includes(0))  ) {

                    $("#SE1").removeClass('ocult');
                    $('#SE1').addClass('alerta');
                    $("#SE1").html("En validación comercial");
                }
            }


            //Valida si tiene subetapa "cancelada"
            if (subetapa=="K"){
                $("#SE1").removeClass('ocult');
                $("#SE1").removeClass('alerta');
                $('#SE1').addClass('error');
                $("#SE1").html("Cancelada");
            }
        }
        if (etapa=="P"){
            //Agrega clase current para sombrear en color actual al pipeline Int de Expediente
            $('#P').addClass('current');
            $('#SI').addClass('done');
            //Valida las subetapas 'En Espera', 'int de Exp','Devuelta por Credito' y 'Cancelada'
            if(subetapa=="PE"){
                $("#SE2").removeClass('ocult');
                $('#SE2').addClass('success');
                $("#SE2").html("En Espera");
            }
            if(subetapa=="P"){
                $("#SE2").removeClass('ocult');
                $('#SE2').addClass('success');
                $("#SE2").html("Int de Expediente");
            }
            if(subetapa=="DP"){
                $("#SE2").removeClass('ocult');
                $('#SE2').addClass('alerta');
                $("#SE2").html("Devuelta por Crédito");
            }
            if(subetapa=="K"){
                $("#SE2").removeClass('ocult');
                $('#SE2').addClass('error');
                $("#SE2").html("Cancelada");
            }
            if(subetapa=="BO"){
                $("#SE2").removeClass('ocult');
                $('#SE2').addClass('success');
                $("#SE2").html("Validación BO Crédito");
            }
            if(subetapa=="DB"){
                $("#SE2").removeClass('ocult');
                $('#SE2').addClass('alerta');
                $("#SE2").html("Devuelta BO Crédito");
            }
            if(subetapa=="VC"){
                $("#SE2").removeClass('ocult');
                $('#SE2').addClass('success');
                $("#SE2").html("Val mesa de Control");
            }
            if(subetapa=="AN"){
                $("#SE2").removeClass('ocult');
                $('#SE2').addClass('alerta');
                $("#SE2").html("Análisis legal");
            }
        }
        
        if (etapa=="PA"){
            //Agrega clase current para sombrear en color actual al pipeline PREAUTORIZADA
            $('#PA').addClass('current');
            $('#SI').addClass('done');
            $('#P').addClass('done');
            //Valida subetapa
            if (subetapa=="CV") {
                $("#SE6").removeClass('ocult');
                $('#SE6').addClass('error');
                $("#SE6").html("Cancelada por vigencia");
            }
            if (subetapa=="BE") {
                $("#SE6").removeClass('ocult');
                $('#SE6').addClass('error');
                $("#SE6").html("Bloq por expediente");
            }
        }
        
        if (etapa=="C"){
            //Agrega clase current para sombrear en color actual al pipeline CREDITO
            $('#C').addClass('current');
            $('#SI').addClass('done');
            $('#P').addClass('done');
            $('#PA').addClass('done');
            //Validaciones subetapas
            if(subetapa=="BC"){
                $("#SE3").removeClass('ocult');
                $('#SE3').addClass('success');
                $("#SE3").html("Buró de Crédito");
            }
            if(subetapa=="CC"){
                $("#SE3").removeClass('ocult');
                $('#SE3').addClass('success');
                $("#SE3").html("Cualitativo/Cuantitativo");
            }
            if(subetapa=="RF"){
                $("#SE3").removeClass('ocult');
                $('#SE3').addClass('success');
                $("#SE3").html("Referencias");
            }
            if(subetapa=="EF"){
                $("#SE3").removeClass('ocult');
                $('#SE3').addClass('success');
                $("#SE3").html("Estados Financieros");
            }
            if(subetapa=="E"){
                $("#SE3").removeClass('ocult');
                $('#SE3').addClass('success');
                $("#SE3").html("Análisis de Crédito");
            }
            if(subetapa=="RM"){
                $("#SE3").removeClass('ocult');
                $('#SE3').addClass('success');
                $("#SE3").html("Análisis Crédito RM");
            }
            if(subetapa=="SC"){
                $("#SE3").removeClass('ocult');
                $('#SE3').addClass('success');
                $("#SE3").html("Scoring");
            }
            if(subetapa=="D"){
                $("#SE3").removeClass('ocult');
                $('#SE3').addClass('success');
                $("#SE3").html("Comité");
            }
            if(subetapa=="CN"){
                $("#SE3").removeClass('ocult');
                $('#SE3').addClass('success');
                $("#SE3").html("Condicionada");
            }
            if(subetapa=="SG"){
                $("#SE3").removeClass('ocult');
                $('#SE3').addClass('success');
                $("#SE3").html("Sugerencias de Crédito");
            }
        }
        if (etapa=="D"){
            //Agrega clase current para sombrear en color actual al pipeline CLIENTE CON LINEA
            $('#D').addClass('current');
            $('#SI').addClass('done');
            $('#P').addClass('done');
            $('#C').addClass('done');
            $('#PA').addClass('done');
            //Valida subetapa
            if (subetapa=="D") {
                $("#SE4").removeClass('ocult');
                $('#SE4').addClass('success');
                $("#SE4").html("Comité");
            }
            if (subetapa=="AC") {
                $("#SE4").removeClass('ocult');
                $('#SE4').addClass('success');
                $("#SE4").html("Autorizador Carátula");
            }
        }
        if (etapa=="CL"){
            //Agrega clase current para sombrear en color actual al pipeline CLIENTE CON LINEA
            $('#CL').addClass('current');
            $('#SI').addClass('done');
            $('#P').addClass('done');
            $('#C').addClass('done');
            $('#D').addClass('done');
            $('#PA').addClass('done');
            //Valida subetapa
            if (subetapa=="N") {
                $("#SE5").removeClass('ocult');
                $('#SE5').addClass('success');
                $("#SE5").html("Autorizada");
            }
            if (subetapa=="VF") {
                $("#SE5").removeClass('ocult');
                $('#SE5').addClass('success');
                $("#SE5").html("Val firmas formatos U");
            }
        }
        if (etapa=="R"){
            //Agrega clase current para sombrear en color actual al pipeline
            $('#CL').addClass('current');
            $('#SI').addClass('done');
            $('#P').addClass('done');
            $('#C').addClass('done');
            $('#D').addClass('done');
            $('#PA').addClass('done');
            //Cambia texto a Rechazado
            $("#CL").html("Rechazado");
            //Valida subetapas
            if(subetapa=="CM"){
                $("#SE5").removeClass('ocult');
                $('#SE5').addClass('error');
                $("#SE5").html("Rechazada Comité");
            }
            if(subetapa=="R"){
                $("#SE5").removeClass('ocult');
                $('#SE5').addClass('error');
                $("#SE5").html("Rechazada Crédito");
            }
            if(subetapa=="RV"){
                $("#SE5").removeClass('ocult');
                $('#SE5').addClass('error');
                $("#SE5").html("Rechazada Visita Ocular");
            }

        }
    },
})
