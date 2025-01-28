({
    initialize: function (options) {
        //Inicializa campo custom
        this._super('initialize', [options]);
        options = options || {};
        options.def = options.def || {};
        clasf_sectorial = this;
        this.model.on('sync', this.loadData, this);
        clasf_sectorial.check_uni2 = 0;
		window.actualiza = 0;
        this.ActividadEconomica = {
            'ae': {
                'id': '',
            },
            'sse': {
                'id': '',
            },
            'se': {
                'id': '',
            },
            'ms': {
                'id': '',
            },
            'inegi_clase': '',
            'inegi_subrama': '',
            'inegi_rama': '',
            'inegi_subsector': '',
            'inegi_sector': '',
            'inegi_macro': '',
            'label_subsector': '',
            'label_sector': '',
            'label_macro': '',
            'label_clase': '',
            'label_subrama': '',
            'label_rama': '',
            'label_isubsector': '',
            'label_isector': '',
            'label_imacro': '',
			'label_div': '',
			'label_grp': '',
			'label_cls': ''
        };

        this.prevActEconomica = {
            'ae': {
                'id': '',
            },
            'sse': {
                'id': '',
            },
            'se': {
                'id': '',
            },
            'ms': {
                'id': '',
            },
            'inegi_clase': '',
            'inegi_subrama': '',
            'inegi_rama': '',
            'inegi_subsector': '',
            'inegi_sector': '',
            'inegi_macro': '',
            'label_subsector': '',
            'label_sector': '',
            'label_macro': '',
            'label_clase': '',
            'label_subrama': '',
            'label_rama': '',
            'label_isubsector': '',
            'label_isector': '',
            'label_imacro': '',
			'label_div': '',
			'label_grp': '',
			'label_cls': ''			
        };

        this.ResumenCliente = {
            'inegi': {
                'inegi_clase': '',
                'inegi_subrama': '',
                'inegi_rama': '',
                'inegi_subsector': '',
                'inegi_sector': '',
                'inegi_macro': ''
            },
            'pb': {
                'pb_division': '',
                'pb_grupo': '',
                'pb_clase': ''
            },			
        };
    },

    loadData: function () {
        clasf_sectorial = this;
		//Campos PB
		clasf_sectorial.ActividadEconomica.label_div = app.lang.getAppListStrings('pb_division_list')[this.model.get("pb_division_c")];
		clasf_sectorial.ActividadEconomica.label_grp = app.lang.getAppListStrings('pb_grupo_list')[this.model.get("pb_grupo_c")];
		clasf_sectorial.ActividadEconomica.label_cls = app.lang.getAppListStrings('pb_clase_list')[this.model.get("pb_clase_c")];
		clasf_sectorial.ResumenCliente.pb.pb_division = this.model.get("pb_division_c");
		clasf_sectorial.ResumenCliente.pb.pb_grupo = this.model.get("pb_grupo_c");
		clasf_sectorial.ResumenCliente.pb.pb_clase = this.model.get("pb_clase_c");
        //Campos CNBV
        clasf_sectorial.ActividadEconomica.ae.id = this.model.get("actividad_economica_c");
        // clasf_sectorial.ActividadEconomica.sse.id = this.model.get("subsector_c");
        // clasf_sectorial.ActividadEconomica.se.id = this.model.get("sector_economico_c");
        // clasf_sectorial.ActividadEconomica.ms.id = this.model.get("macrosector_c");
        //Etiquetas de los campos CNBV para Input del HBS en edit
        // clasf_sectorial.ActividadEconomica.label_subsector = app.lang.getAppListStrings('subsector_list')[this.model.get("subsector_c")];
        // clasf_sectorial.ActividadEconomica.label_sector = app.lang.getAppListStrings('sector_list')[this.model.get("sector_economico_c")];
        // clasf_sectorial.ActividadEconomica.label_macro = app.lang.getAppListStrings('macro_cnbv_list')[this.model.get("macrosector_c")];
        //Envia los valores de los campos de INEGI a la vista HBS
        // clasf_sectorial.ResumenCliente.inegi.inegi_clase = this.model.get("inegi_clase_c");
        // clasf_sectorial.ResumenCliente.inegi.inegi_subrama = this.model.get("inegi_subrama_c");
        // clasf_sectorial.ResumenCliente.inegi.inegi_rama = this.model.get("inegi_rama_c");
        // clasf_sectorial.ResumenCliente.inegi.inegi_subsector = this.model.get("inegi_subsector_c");
        // clasf_sectorial.ResumenCliente.inegi.inegi_sector = this.model.get("inegi_sector_c");
        // clasf_sectorial.ResumenCliente.inegi.inegi_macro = this.model.get("inegi_macro_c");
        //Envia los valores al LH para guardarlos en el modulo de Resumen
        // clasf_sectorial.ActividadEconomica.inegi_clase = this.model.get("inegi_clase_c");
        // clasf_sectorial.ActividadEconomica.inegi_subrama = this.model.get("inegi_subrama_c");
        // clasf_sectorial.ActividadEconomica.inegi_rama = this.model.get("inegi_rama_c");
        // clasf_sectorial.ActividadEconomica.inegi_subsector = this.model.get("inegi_subsector_c");
		// clasf_sectorial.ActividadEconomica.inegi_sector = this.model.get("inegi_sector_c");
        // clasf_sectorial.ActividadEconomica.inegi_macro = this.model.get("inegi_macro_c");
        //Etiquetas de los campos INEGI para Input del HBS en edit
        // clasf_sectorial.ActividadEconomica.label_clase = app.lang.getAppListStrings('clase_list')[this.model.get("inegi_clase_c")];
        // clasf_sectorial.ActividadEconomica.label_subrama = app.lang.getAppListStrings('subrama_list')[this.model.get("inegi_subrama_c")];
        // clasf_sectorial.ActividadEconomica.label_rama = app.lang.getAppListStrings('rama_list')[this.model.get("inegi_rama_c")];
        // clasf_sectorial.ActividadEconomica.label_isubsector = app.lang.getAppListStrings('subsector_list')[this.model.get("inegi_subsector_c")];
        // clasf_sectorial.ActividadEconomica.label_isector = app.lang.getAppListStrings('sector_list')[this.model.get("inegi_sector_c")];
        // clasf_sectorial.ActividadEconomica.label_imacro = app.lang.getAppListStrings('macro_list')[this.model.get("inegi_macro_c")];
        _.extend(this, clasf_sectorial.ResumenCliente);
		clasf_sectorial['prevActEconomica'] = app.utils.deepCopy(clasf_sectorial.ActividadEconomica);
		clasf_sectorial.render();
        if ($('.list_ae').select2('val') != "" || clasf_sectorial.ActividadEconomica.ae.id != "") {
            $('.list_ae').trigger('change');
        }
    },

    bindDataChange: function () {
        this.model.on('change:' + this.name, function () {
            if (this.action !== 'edit') {
            }
        }, this);
    },

    _render: function () {
        this._super("_render");
		if($('[data-fieldname="lead_clasf_sectorial"] > span').length >0) $('[data-fieldname="lead_clasf_sectorial"] > span').show();
		//Carga Lista de Actividad Economica
		clasf_sectorial.actividad_list = app.lang.getAppListStrings('actividad_list');
		//Oculta campos
		$('[data-name="pb_division_c"]').hide();
		$('[data-name="pb_grupo_c"]').hide();
		$('[data-name="pb_clase_c"]').hide();
		$('[data-name="actividad_economica_c"]').hide();
		$('[data-name="sector_economico_c"]').hide();
		$('[data-name="subsector_c"]').hide();
		$('[data-name="macrosector_c"]').hide();
		$('[data-name="inegi_clase_c"]').hide();
		$('[data-name="inegi_subrama_c"]').hide();
		$('[data-name="inegi_rama_c"]').hide();
		$('[data-name="inegi_subsector_c"]').hide();
		$('[data-name="inegi_sector_c"]').hide();
		$('[data-name="inegi_macro_c"]').hide();
        //campo custom lead_clasf_sectorial ocualta la Etiqueta
        $("div.record-label[data-name='lead_clasf_sectorial']").attr('style', 'display:none;');
        //Funcion para dar estilo select2 a las listas deplegables.
        var $select = $('select.select2');
        $select.select2();
        if (clasf_sectorial.check_uni2 != 0) {
            //Campos ReadOnly de Actividad Economica dependiendo del check de uni2
            $(".campoAE").attr('style', 'pointer-events:none;');
        }
        //Campos CNBV de Solo Lectura
        $(".campoSSE").attr('style', 'pointer-events:none;');
        $(".campoSE").attr('style', 'pointer-events:none;');
        $(".campoMS").attr('style', 'pointer-events:none;');
        //Campos INEGI de Solo Lectura 
        $(".campoIC").attr('style', 'pointer-events:none;');
        $(".campoISR").attr('style', 'pointer-events:none;');
        $(".campoIR").attr('style', 'pointer-events:none;');
        $(".campoISS").attr('style', 'pointer-events:none;');
        $(".campoIS").attr('style', 'pointer-events:none;');
        $(".campoIMS").attr('style', 'pointer-events:none;');
        //Campos PB de Solo Lectura
        $(".campodiv").attr('style', 'pointer-events:none;');
        $(".campogrp").attr('style', 'pointer-events:none;');
        $(".campocls").attr('style', 'pointer-events:none;');
        //Carga los valores en los campos dependientes de Actividad Económica al momento de hacer el change
        $('.list_ae').change(function (evt) {
            clasf_sectorial.fClasfSectorial(evt);
        });
		if(window.actualiza == 1) {
            this.model.set("actividad_economica_c", clasf_sectorial.ActividadEconomica.ae.id);
			// this.model.set("subsector_c", clasf_sectorial.ActividadEconomica.sse.id);
			// this.model.set("sector_economico_c", clasf_sectorial.ActividadEconomica.se.id);
			// this.model.set("macrosector_c", clasf_sectorial.ActividadEconomica.ms.id);
			// this.model.set("inegi_clase_c", clasf_sectorial.ActividadEconomica.inegi_clase);
			// this.model.set("inegi_subrama_c", clasf_sectorial.ActividadEconomica.inegi_subrama);
			// this.model.set("inegi_rama_c", clasf_sectorial.ActividadEconomica.inegi_rama);
			// this.model.set("inegi_subsector_c", clasf_sectorial.ActividadEconomica.inegi_subsector);
			// this.model.set("inegi_sector_c", clasf_sectorial.ActividadEconomica.inegi_sector);
			// this.model.set("inegi_macro_c", clasf_sectorial.ActividadEconomica.inegi_macro);
			window.actualiza = 0;
        }
    },

    fClasfSectorial: function (evt) {
        if ($(evt.currentTarget).val() == '' || $(evt.currentTarget).val() == null || $(evt.currentTarget).val() == undefined) {
            idActEconomica = clasf_sectorial.ActividadEconomica.ae.id;
        } else {
            idActEconomica = $('.list_ae').select2('val');
        }
        if (idActEconomica != "" && idActEconomica != null && idActEconomica != undefined && idActEconomica != 0) {
            app.api.call('GET', app.api.buildURL('clasificacionSectorialCNVB/' + idActEconomica), null, {
                success: function (data) {
                    dataInegi = data;
                    if (dataInegi != '') {
                        console.log("idActEconomica ", idActEconomica);
                        //Campos CNBV
                        clasf_sectorial.ActividadEconomica.ae.id = idActEconomica;
                        clasf_sectorial.ActividadEconomica.sse.id = dataInegi['id_subsector_economico_cnbv'];
                        clasf_sectorial.ActividadEconomica.se.id = dataInegi['id_sector_economico_cnbv'];
                        clasf_sectorial.ActividadEconomica.ms.id = dataInegi['id_macro_sector_cnbv'];
                        //Etiquetas de los campos CNBV para Input del HBS en edit
                        clasf_sectorial.ActividadEconomica.label_subsector = app.lang.getAppListStrings('subsector_list')[clasf_sectorial.ActividadEconomica.sse.id];
                        clasf_sectorial.ActividadEconomica.label_sector = app.lang.getAppListStrings('sector_list')[clasf_sectorial.ActividadEconomica.se.id];
                        clasf_sectorial.ActividadEconomica.label_macro = app.lang.getAppListStrings('macro_cnbv_list')[clasf_sectorial.ActividadEconomica.ms.id];
                        //Envia los valores de los campos de INEGI a la vista HBS
                        clasf_sectorial.ResumenCliente.inegi.inegi_clase = dataInegi['id_clase_inegi'];
                        clasf_sectorial.ResumenCliente.inegi.inegi_subrama = dataInegi['id_subrama_inegi'];
                        clasf_sectorial.ResumenCliente.inegi.inegi_rama = dataInegi['id_rama_inegi'];
                        clasf_sectorial.ResumenCliente.inegi.inegi_subsector = dataInegi['id_subsector_inegi'];
                        clasf_sectorial.ResumenCliente.inegi.inegi_sector = dataInegi['id_sector_inegi'];
                        clasf_sectorial.ResumenCliente.inegi.inegi_macro = dataInegi['id_macro_inegi'];
                        //Envia los valores al LH para guardarlos en el modulo de Resumen
                        clasf_sectorial.ActividadEconomica.inegi_clase = dataInegi['id_clase_inegi'];
                        clasf_sectorial.ActividadEconomica.inegi_subrama = dataInegi['id_subrama_inegi'];
                        clasf_sectorial.ActividadEconomica.inegi_rama = dataInegi['id_rama_inegi'];
                        clasf_sectorial.ActividadEconomica.inegi_subsector = dataInegi['id_subsector_inegi'];
                        clasf_sectorial.ActividadEconomica.inegi_sector = dataInegi['id_sector_inegi'];
                        clasf_sectorial.ActividadEconomica.inegi_macro = dataInegi['id_macro_inegi'];
                        //Etiquetas de los campos INEGI para Input del HBS en edit
                        clasf_sectorial.ActividadEconomica.label_clase = app.lang.getAppListStrings('clase_list')[clasf_sectorial.ActividadEconomica.inegi_clase];
                        clasf_sectorial.ActividadEconomica.label_subrama = app.lang.getAppListStrings('subrama_list')[clasf_sectorial.ActividadEconomica.inegi_subrama];
                        clasf_sectorial.ActividadEconomica.label_rama = app.lang.getAppListStrings('rama_list')[clasf_sectorial.ActividadEconomica.inegi_rama];
                        clasf_sectorial.ActividadEconomica.label_isubsector = app.lang.getAppListStrings('subsector_list')[clasf_sectorial.ActividadEconomica.inegi_subsector];
                        clasf_sectorial.ActividadEconomica.label_isector = app.lang.getAppListStrings('sector_list')[clasf_sectorial.ActividadEconomica.inegi_sector];
                        clasf_sectorial.ActividadEconomica.label_imacro = app.lang.getAppListStrings('macro_list')[clasf_sectorial.ActividadEconomica.inegi_macro];
						window.actualiza = 1;
                        clasf_sectorial.render();
                    }
                },
                error: function (e) {
                    throw e;
                }
            });
        }
		else { // Limpia campos
            //Campos CNBV
            clasf_sectorial.ActividadEconomica.ae.id = '';
            clasf_sectorial.ActividadEconomica.sse.id = '';
            clasf_sectorial.ActividadEconomica.se.id = '';
            clasf_sectorial.ActividadEconomica.ms.id = '';
            //Etiquetas de los campos CNBV para Input del HBS en edit
            clasf_sectorial.ActividadEconomica.label_subsector = '';
            clasf_sectorial.ActividadEconomica.label_sector = '';
            clasf_sectorial.ActividadEconomica.label_macro = '';
            //Envia los valores de los campos de INEGI a la vista HBS
            clasf_sectorial.ResumenCliente.inegi.inegi_clase = '';
            clasf_sectorial.ResumenCliente.inegi.inegi_subrama = '';
            clasf_sectorial.ResumenCliente.inegi.inegi_rama = '';
            clasf_sectorial.ResumenCliente.inegi.inegi_subsector = '';
            clasf_sectorial.ResumenCliente.inegi.inegi_sector = '';
            clasf_sectorial.ResumenCliente.inegi.inegi_macro = '';
            //Envia los valores al LH para guardarlos en el modulo de Resumen
			clasf_sectorial.ActividadEconomica.inegi_clase = '';
            clasf_sectorial.ActividadEconomica.inegi_subrama = '';
            clasf_sectorial.ActividadEconomica.inegi_rama = '';
            clasf_sectorial.ActividadEconomica.inegi_subsector = '';
            clasf_sectorial.ActividadEconomica.inegi_sector = '';
            clasf_sectorial.ActividadEconomica.inegi_macro = '';
            //Etiquetas de los campos INEGI para Input del HBS en edit
            clasf_sectorial.ActividadEconomica.label_clase = '';
            clasf_sectorial.ActividadEconomica.label_subrama = '';
            clasf_sectorial.ActividadEconomica.label_rama = '';
            clasf_sectorial.ActividadEconomica.label_isubsector = '';
            clasf_sectorial.ActividadEconomica.label_isector = '';
            clasf_sectorial.ActividadEconomica.label_imacro = '';
			window.actualiza = 1;
            clasf_sectorial.render();
		}
    },
})
