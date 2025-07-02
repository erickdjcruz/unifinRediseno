({
	className: 'rfc_qr',

	events: {
		'click #btn_Cancelar': 'cancelar',
		'click .btn_rfc_qr': 'btn_rfc_qr',
		'click #validar_QR': 'validarServicioQR',
		'click #validar_CIEC': 'validarServicioCIEC',
		'click #activar_camara': 'activarCamara',
		'click #archivo_qr': 'cargarArchivo',
		'change #btnSubir': 'SubirImagen',
		'click .closeModalRazonSocial': 'closeModalRazonSocial',
		'click .action1': 'modalAction1',
		'click .action2': 'modalAction2',
		'click .action3': 'modalAction3',
	},

	initialize: function (options) {
		this._super("initialize", [options]);
		self.body = null;
		self.picturecam = false;
		this.loadView = true;
		this.context.on('button:btn_rfc:click', this.btn_rfc_qr, this);
		cont_qr = this;
		cambioRazonSocial = [];
		infoUser = [];
		this.getUserInfo();
	},

	render: function () {
		this._super("render");
		$("div.record-label[data-name='rfc_qr']").attr('style', 'pointer-events:none;');
		$("div.record-label[data-name='rfc_qr']").attr('style', 'display:none;');
	},

	tieneSoporteUserMedia: function () {
		return !!(navigator.getUserMedia || (navigator.mozGetUserMedia || navigator.mediaDevices.getUserMedia) || navigator.webkitGetUserMedia || navigator.msGetUserMedia);
	},

	getUserMedia: function () {
		return (navigator.getUserMedia || (navigator.mozGetUserMedia || navigator.mediaDevices.getUserMedia) || navigator.webkitGetUserMedia || navigator.msGetUserMedia).apply(navigator, arguments);
	},

	SubirImagen: function (e) {
		var input = contexto_cuenta.$('input[type=file]');
		var file = input[0].files[0];
		var filePath = input[0].value;
		var ext = $(e.currentTarget).val().split('.').pop();
		if (file == "" || file == undefined) {
			app.alert.show('errorAlert', {
				level: 'error',
				messages: 'Favor de elegir un archivo',
				autoClose: true
			});
		} else if (ext !== 'pdf') {
			app.alert.show('invalidExt', {
				level: 'error',
				messages: 'Tipo de archivo no compatible, favor de elegir únicamente documentos PDF',
				autoClose: true
			});
			$(e.currentTarget).val('');

		} else {
			var FR = new FileReader();
			FR.addEventListener("load", function (e) {
				window.result = e.target.result;
			});
			/*
				setTimeout(function(){
				contexto_cuenta.$('#img').src = window.result;
				contexto_cuenta.$('#img').attr("src",window.result);
				contexto_cuenta.$('#img').show();
			  }, 100);
			  */
			FR.readAsDataURL(input[0].files[0]);
		}
	},

	activarCamara: function () {
		this.$('#carga').hide();
		this.$('#div_video').show();
		var elemento = null;
		var video = this.$('#video')[0];
		canvas = this.$('#canvas')[0];
		snap = this.$('#snap')[0];
		estado = this.$('#estado')[0];
		if (this.$('#activar_camara')[0].checked == true) {
			this.fileupload = false;
			elemento = this.$('#div_video')[0];
			elemento.style.display = 'block';
			elemento = this.$('#carga')[0];
			elemento.style.display = 'none';
			elemento = this.$('#btnSubir')[0];
			elemento.value = '';
			elemento = this.$('#b64')[0];
			elemento.value = "";
			elemento = this.$('#img')[0];
			elemento.src = "";
			elemento.style.display = 'none';
			if (this.tieneSoporteUserMedia()) {
				this.getUserMedia(
					{ video: true, width: 400, height: 200 },
					function (stream) {
						console.log("Permiso concedido");
						video.srcObject = stream;
						video.play();
						//Escuchar el click
						snap.addEventListener("click", function () {
							video.pause();
							//Obtener contexto del canvas y dibujar sobre él
							var contexto = canvas.getContext("2d");
							canvas.width = video.width;
							canvas.height = video.height;
							contexto.drawImage(video, 0, 0, 280, 200);
							var foto = canvas.toDataURL(); //Esta es la foto, en base 64
							//estado.innerHTML = "Foto tomada con exito...";
							canvas.style.display = 'block';
							var body = {
								"file": foto
							}
							self.picturecam = true;
							video.play();
						});
					}, function (error) {
						App.alert.show('no_camara', {
							level: 'error',
							messages: 'No se puede acceder a la cámara o no ha dado permiso.',
							autoClose: true
						});
					});
			} else {
				App.alert.show('no_support', {
					level: 'error',
					messages: 'Parece que tu navegador no soporta esta característica. Intenta actualizarlo.',
					autoClose: true
				});
			}
		} else {
			this.picturecam = false;
			this.body = null;
			var sprite = new Image();
			var contexto = canvas.getContext("2d");
			contexto.drawImage(sprite, 0, 0);
			elemento = this.$('#carga')[0];
			elemento.style.display = 'block';
			elemento = document.getElementById("div_video");
			elemento.style.display = 'none';
			canvas.style.display = 'none';
			video.pause();
			{ video: false }
		}
	},

	formatearFecha: function (fechaISO) {
		var fecha = new Date(fechaISO);

		const anio = fecha.getFullYear();
		const mes = String(fecha.getMonth() + 1).padStart(2, '0');
		const dia = String(fecha.getDate()).padStart(2, '0');

		let hora = fecha.getHours();
		let minutos = fecha.getMinutes();

		// Redondeo de minutos a 00 o 30
		minutos = minutos < 30 ? 0 : 30;

		const horaStr = String(hora).padStart(2, '0');
		const minStr = String(minutos).padStart(2, '0');

		return `${anio}-${mes}-${dia}, ${horaStr}:${minStr}`;
	},

	getClasificacionSectorial: function (idActEconomica) {
		if (idActEconomica != "" && idActEconomica != null && idActEconomica != undefined) {
			console.log("idActEconomica " + idActEconomica);

			app.api.call('GET', app.api.buildURL('clasificacionSectorialCNVB/' + idActEconomica), null, {
				success: function (data) {
					dataInegi = data;
					app.alert.dismiss('obtiene_clasf_sectorial');
					// console.log(data);
					if (dataInegi != '') {
						//Campos CNBV
						clasf_sectorial.ActividadEconomica.ae.id = idActEconomica;
						clasf_sectorial.ActividadEconomica.sse.id = dataInegi['id_subsector_economico_cnbv'];
						clasf_sectorial.ActividadEconomica.se.id = dataInegi['id_sector_economico_cnbv'];
						clasf_sectorial.ActividadEconomica.ms.id = dataInegi['id_macro_sector_cnbv'];

						//Etiquetas de los campos CNBV para Input del HBS en edit
						clasf_sectorial.ActividadEconomica.label_subsector = app.lang.getAppListStrings('subsector_cnbv_list')[clasf_sectorial.ActividadEconomica.sse.id];
						clasf_sectorial.ActividadEconomica.label_sector = app.lang.getAppListStrings('sector_cnbv_list')[clasf_sectorial.ActividadEconomica.se.id];
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

						clasf_sectorial.render();
					}
				},
				error: function (e) {
					throw e;
				}
			});
		}
	},

	validarServicioCIEC: function () {
		var contextol = this;

		if (contexto_cuenta.model.attributes.valid_cambio_razon_social_c) {
			app.alert.show('errorAlertCambio', {
				level: 'error',
				messages: 'Está cuenta se encuentra en validación de cambios y no puede ser modificada',
				autoClose: true
			});
			return;
		}
		app.alert.show('procesando', {
			level: 'process',
			title: 'Cargando...'
		});
		
		this.$("[data-name='validar_CIEC']").attr('style', 'pointer-events:none;');
		var $btnCIEC = this.$('#validar_CIEC');

		if (contexto_cuenta.model.get('rfc_c') != '' && contexto_cuenta.model.get('rfc_c') != undefined) {
			
			$btnCIEC.prop('disabled', true);
			$btnCIEC.text('Procesando...');
			
			var rfc = contexto_cuenta.model.get('rfc_c');
			var body = {
				"rfc": rfc
			}

			app.api.call('create', app.api.buildURL("GetInfoRFCbyCIEC"), body, {
				success: _.bind(function (data) {
					app.alert.dismiss('procesando');

					if (data.success) {
						var indice_indicador = 0;
						var Completo = '';
						var RFC = data["rfc"].toUpperCase();
						var ticket = data["ticket"];
						//var PathQR=data[0]["path_img_qr"];
						var Correo = data["email"];
						var CP = data["address"]["postalCode"];
						var Calle = data["address"]["riched_vialidad"].toUpperCase();
						var Exterior = data["address"]["streetNumber"].toUpperCase();
						var Interior = data["address"]["buildingNumber"].toUpperCase();
						//var Colonia = (data[0]["Colonia"] != undefined && data[0]["Colonia"] !='') ? data[0]["Colonia"] : ' ' ;
						//Se aplica normalize para reemplazar caracteres especiales
						var Ciudad = data["address"]["riched_d_ciudad"].toUpperCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");;
						var IdCiudad = data["address"]["riched_id_ciudad"];
						var Colonia = data["address"]["neighborhood"].toUpperCase();
						var Municipio = data["address"]["municipality"].toUpperCase();
						var Estado = data["address"]["state"].toUpperCase();
						var Regimen = data["taxRegimes"][0]["name"];
						var Pais = "MEXICO";
						var Regimenes = data["taxRegimes"];
						var LugarEmision = data["info"]["place_issued"];
						var FechaEmision = data["info"]["date_issued"];

						//actividades economicas
						// Filtrar las actividades con preponderancia 1
						var filteredActivities = data.economicActivities.filter(
							activity => activity.riched_code_preponderance == 1
						);

						var activity_sat_id = filteredActivities[0].riched_activity_sat_id;
						var activity_sat = filteredActivities[0].riched_activity_sat_desc;
						var code_cnvb = filteredActivities[0].riched_code_cnbv;
						var code_inegi = filteredActivities[0].riched_code_inegi;

						if (RFC != undefined) {
							if (RFC.length == 12) Regimen = "Persona Moral";
							if (Regimen == "Régimen de las Personas Físicas con Actividades Empresariales y Profesionales") Regimen = "Persona Fisica con Actividad Empresarial";
							if (Regimen != "Persona Fisica con Actividad Empresarial" && RFC.length == 13) Regimen = "Persona Fisica";
						}
						if (Estado == "MEXICO") Estado = "ESTADO DE MEXICO";
						if (Regimen == "Persona Moral") {
							//var Denominacion = data[0]["Denominación o Razón Social"]+" "+data[0]["Régimen de capital"];
							//var Constitucion = data[0]["Fecha de constitución"];
							//var Denominacion = data["company"]["legalName"]+" "+ data["company"]["entityType2"];
							var Denominacion = data["company"]["legalName"] + data["company"]["entityType4"];
							var RegimenCapital = data["company"]["entityType4"];
							var Constitucion = data["company"]["constitutionDate"];
							var LegalName = data["company"]["legalName"];
							Completo = Denominacion;
							//Constitucion = Constitucion.substring(6, 10) + "-" + Constitucion.substring(3, 5) + "-" + Constitucion.substring(0, 2);
							Constitucion = Constitucion.split('T')[0];
						} else {
							var Nombre = data["person"]["firstName"];
							var Paterno = data["person"]["middleName"];
							var Materno = data["person"]["lastName"];
							var CURP = data["person"]["curp"];
							var Nacimiento = data["person"]["birthdate"];
							Nacimiento = Nacimiento.split("T")[0];
							Completo = Nombre + " " + Paterno + " " + Materno;
							//Nacimiento = Nacimiento.substring(6, 10) + "-" + Nacimiento.substring(3, 5) + "-" + Nacimiento.substring(0, 2);
							//Nacimiento = Nacimiento.split('T')[0];
						}
						app.api.call("read", app.api.buildURL("Accounts/", null, null, {
							max_num: 5,
							"filter": [{
								"rfc_c": RFC,
							}]
						}), null, {
							success: _.bind(function (data) {
								if (data.records.length > 0 && contexto_cuenta.model.get('id') != data.records[0].id) {
									app.alert.dismiss('procesando');
									app.alert.show('errorAlert', {
										level: 'error',
										messages: "Ya existe la cuenta " + data.records[0].name,
									});
									contexto_cuenta.$('#activar_camara').removeClass('disabled');
									contexto_cuenta.$('#activar_camara').attr('style', '');
									contexto_cuenta.$('#archivo_qr').removeClass('disabled');
									contexto_cuenta.$('#archivo_qr').attr('style', '');
									contexto_cuenta.$('#btnSubir').removeClass('disabled');
									contexto_cuenta.$('#btnSubir').attr('style', 'margin:10px');
									contexto_cuenta.$('#validar_QR').removeClass('disabled');
									contexto_cuenta.$('#validar_QR').attr('style', 'margin:10px');
									contexto_cuenta.$('#btn_Cancelar').removeClass('disabled');
									contexto_cuenta.$('#btn_Cancelar').attr('style', 'margin:10px');
								} else {
									// Valida Regimen
									var verdad = false;
									if (Regimen != contexto_cuenta.model.get('tipodepersona_c') && contexto_cuenta.model.get('id')) {
										if (!Regimen.includes("Persona Fisica") || !contexto_cuenta.model.get('tipodepersona_c').includes("Persona Fisica")) verdad = true;
									}
									if (verdad) {
										app.alert.dismiss('procesando');
										app.alert.show('errorRegimen', {
											level: 'error',
											messages: "El Regimen encontrado con la CSF es diferente al de la cuenta",
										});
										contexto_cuenta.$('#activar_camara').removeClass('disabled');
										contexto_cuenta.$('#activar_camara').attr('style', '');
										contexto_cuenta.$('#archivo_qr').removeClass('disabled');
										contexto_cuenta.$('#archivo_qr').attr('style', '');
										contexto_cuenta.$('#btnSubir').removeClass('disabled');
										contexto_cuenta.$('#btnSubir').attr('style', 'margin:10px');
										contexto_cuenta.$('#validar_QR').removeClass('disabled');
										contexto_cuenta.$('#validar_QR').attr('style', 'margin:10px');
										contexto_cuenta.$('#btn_Cancelar').removeClass('disabled');
										contexto_cuenta.$('#btn_Cancelar').attr('style', 'margin:10px');
									} else {
										app.alert.show('errorAlert2', {
											level:
												'confirmation',
											messages: "La información recuperada con la CSF proporcionada corresponde a: " + Completo + " ¿Desea proceder con estos datos?",
											autoClose: false,
											onCancel: function () {

												$btnCIEC.prop('disabled', false);
												$btnCIEC.text('Actualizar Constancia');

												contexto_cuenta.$('#activar_camara').removeClass('disabled');
												contexto_cuenta.$('#activar_camara').attr('style', '');
												contexto_cuenta.$('#archivo_qr').removeClass('disabled');
												contexto_cuenta.$('#archivo_qr').attr('style', '');
												contexto_cuenta.$('#btnSubir').removeClass('disabled');
												contexto_cuenta.$('#btnSubir').attr('style', 'margin:10px');
												contexto_cuenta.$('#validar_QR').removeClass('disabled');
												contexto_cuenta.$('#validar_QR').attr('style', 'margin:10px');
												contexto_cuenta.$('#btn_Cancelar').removeClass('disabled');
												contexto_cuenta.$('#btn_Cancelar').attr('style', 'margin:10px');
											},
											onConfirm: function () {
												//Comienza integraciones con Alfresco, Quantico y Robina
												//contextol.integraCSF(contexto_cuenta.model.get('id'), RFC, FechaEmision);
												
												//NUEVO SERVICIO PROCESO ROBINA API
												if(RFC != null && ticket != null){
													contextol.registroProcesoRobinaAPI(RFC, FechaEmision, ticket, contexto_cuenta.model.get('id') );
												}
												$btnCIEC.prop('disabled', false);
												$btnCIEC.text('Actualizar Constancia');
												// Actualiza Datos Personales
												contexto_cuenta.model.set('tipodepersona_c', Regimen);
												contexto_cuenta.model.set('rfc_c', RFC);
												contexto_cuenta.model.set("regimenes_fiscal_sat_c", JSON.stringify(Regimenes));

												//clasf_sectorial.ActividadEconomica.ae = code_cnvb;
												//clasf_sectorial.ActividadEconomica.inegi_clase = code_inegi;

												clasf_sectorial.ActividadEconomica.ResumenSAT.aes.id_actividad_economica_sat = activity_sat_id;
												clasf_sectorial.ActividadEconomica.ResumenSAT.aes.actividad_economica_sat = activity_sat;

												contextol.getClasificacionSectorial(code_cnvb);
												clasf_sectorial.render();

												var fechaEmisionFormat = contextol.formatDate(FechaEmision);
												var lugarFechaEmision = LugarEmision + ' a ' + fechaEmisionFormat;
												contexto_cuenta.model.set('emision_csf_c', lugarFechaEmision);

												//contexto_cuenta.model.set('path_img_qr_c', PathQR);
												cambioRazonSocial['cambioCuenta'] = false;
												cambioRazonSocial['Cuenta'] = [];
												cambioRazonSocial['Cuenta']['razonsocial_c'] = contexto_cuenta.model.get('razonsocial_c');
												cambioRazonSocial['Cuenta']['primernombre_c'] = contexto_cuenta.model.get('primernombre_c');
												cambioRazonSocial['Cuenta']['apellidopaterno_c'] = contexto_cuenta.model.get('apellidopaterno_c');
												cambioRazonSocial['Cuenta']['apellidomaterno_c'] = contexto_cuenta.model.get('apellidomaterno_c');
												cambioRazonSocial['Direccion'] = app.utils.deepCopy(contexto_cuenta.prev_oDirecciones.prev_direccion);

												if (Regimen == "Persona Moral") {
													//Valida cambios
													//Para una Persona Moral, los espacios. puntos o comas, no se deben tomar en cuenta como un cambio para notificar
													var name_actual = contexto_cuenta.model.get("razonsocial_c");
													var name_nuevo = Denominacion;
													var regimen_capital = RegimenCapital;
													cambioRazonSocial["cambioCuenta"] = contextol.evaluaCambioRegimenCapital(name_actual, name_nuevo, regimen_capital);
													//cambioRazonSocial['cambioCuenta'] = contexto_cuenta.model.get('razonsocial_c') != Denominacion ? true : cambioRazonSocial['cambioCuenta'];
													contexto_cuenta.model.set('razonsocial_c', Denominacion);
													contexto_cuenta.model.set('denominacion_c', LegalName);
													contexto_cuenta.model.set('nombre_comercial_c', Denominacion);
													contexto_cuenta.model.set('fechaconstitutiva_c', Constitucion);
													contexto_cuenta.model.set('regimen_capital_c', RegimenCapital);
												} else {

													cambioRazonSocial['cambioCuenta'] = contexto_cuenta.model.get('primernombre_c') != Nombre ? true : cambioRazonSocial['cambioCuenta'];
													cambioRazonSocial['cambioCuenta'] = contexto_cuenta.model.get('apellidopaterno_c') != Paterno ? true : cambioRazonSocial['cambioCuenta'];
													cambioRazonSocial['cambioCuenta'] = contexto_cuenta.model.get('apellidomaterno_c') != Materno ? true : cambioRazonSocial['cambioCuenta'];
													contexto_cuenta.model.set('primernombre_c', Nombre);
													contexto_cuenta.model.set('apellidopaterno_c', Paterno);
													contexto_cuenta.model.set('apellidomaterno_c', Materno);
													//contexto_cuenta.model.set('fechadenacimiento_c', Nacimiento);
													contexto_cuenta.model.set('curp_c', CURP);
												}
												//self.model.set('email1', Correo);

												var arrcorreos = [];
												var repetido = 0;
												if (Correo != "") {
													if (contexto_cuenta.model.attributes.email !== undefined) {
														arrcorreos = contexto_cuenta.model.attributes.email;
														if (arrcorreos.length > 0) {
															for (var y = 0; y < arrcorreos.length; y++) {
																if (arrcorreos[y].email_address == Correo) {
																	repetido = 1;
																}
															}
															if (repetido == 0) {
																if (Correo !== null && Correo !== "") {
																	arrcorreos[arrcorreos.length] = { email_address: Correo, primary_address: false };
																	contexto_cuenta.cambio_previo_mail = '2';
																}
															}
														} else {
															arrcorreos = [{ email_address: Correo, primary_address: true }];
															contexto_cuenta.cambio_previo_mail = '1';
														}
													} else {
														arrcorreos = [{ email_address: Correo, primary_address: true }];
														contexto_cuenta.cambio_previo_mail = '1';

													}
													contexto_cuenta.model.set('email', arrcorreos);
													currentValue = contexto_cuenta.model.get('email');
													emailFieldHtml = cont_qr._buildEmailFieldHtml({
														email_address: Correo,
														primary_address: true,
														opt_out: false,
														invalid_email: false
													});
													//self.render();
													$newEmailField = contexto_cuenta.$('.newEmail').closest('.email').before(emailFieldHtml);
												}
												// Valida duplicado
												cont_dir.oDirecciones = contexto_cuenta.oDirecciones;
												cont_tel.oTelefonos = contexto_cuenta.oTelefonos;
												cont_tel.render();
												pipeacc.tipoSubtipo_vista();
												clasf_sectorial.ActividadEconomica = contexto_cuenta.ActividadEconomica;
												clasf_sectorial.ResumenCliente = contexto_cuenta.ResumenCliente;
												clasf_sectorial.render();
												pld.ProductosPLD = contexto_cuenta.ProductosPLD;
												pld.render();
												var nada = 0;
												var secuencia = 0;
												var principal = 0;
												var duplicado = 0;
												var duplicados = 0;
												var cDuplicado = 0;
												var cDireccionFiscal = 0;
												var homologado = 0;
												var direccion = cont_dir.oDirecciones.direccion;
												cambioRazonSocial['cambioDirFiscal'] = false;
												//Itera para validar diferencia en dirección fiscal
												//cambioRazonSocial['cambioCuenta'] = self.model.get('primernombre_c') != Nombre ? true : cambioRazonSocial['cambioCuenta'];
												Object.keys(direccion).forEach(key => {
													//Valida dirección fiscal
													if (direccion[key].indicadorSeleccionados.includes('^2^') && direccion[key].inactivo == 0) {
														cambiaDirFiscal = 0;
														cambiaDirFiscal = (direccion[key].valCodigoPostal != CP) ? cambiaDirFiscal + 1 : cambiaDirFiscal;
														cambiaDirFiscal = (direccion[key].listPais[direccion[key].pais] != Pais) ? cambiaDirFiscal + 1 : cambiaDirFiscal;
														cambiaDirFiscal = (direccion[key].listMunicipio[direccion[key].municipio] != Municipio) ? cambiaDirFiscal + 1 : cambiaDirFiscal;
														cambiaDirFiscal = (direccion[key].listColonia[direccion[key].colonia] != Colonia) ? cambiaDirFiscal + 1 : cambiaDirFiscal;
														cambiaDirFiscal = (contextol._limpiezaDatos(direccion[key].calle) != contextol._limpiezaDatos(Calle)) ? cambiaDirFiscal + 1 : cambiaDirFiscal;
														cambiaDirFiscal = (contextol._limpiezaDatos(direccion[key].numext) != contextol._limpiezaDatos(Exterior)) ? cambiaDirFiscal + 1 : cambiaDirFiscal;
														cambiaDirFiscal = (contextol._limpiezaDatos(direccion[key].numint) != contextol._limpiezaDatos(Interior)) ? cambiaDirFiscal + 1 : cambiaDirFiscal;
														if (cambiaDirFiscal >= 1) {
															cambioRazonSocial['cambioDirFiscal'] = true;
														}
													}
												});
												var auxd = '';
												var auxd1 = '';
												Object.keys(direccion).forEach(key => {
													duplicado = 0;
													secuencia = secuencia + 1;
													if (direccion[key].principal && !direccion[key].inactivo) principal = 1;
													/*
													duplicado = (direccion[key].valCodigoPostal == CP) ? duplicado+1 : duplicado;
													duplicado = (direccion[key].listPais[direccion[key].pais].toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") == Pais.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")) ? duplicado+1 : duplicado;
													duplicado = (direccion[key].listEstado[direccion[key].estado].toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") == Estado.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")) ? duplicado+1 : duplicado;
													duplicado = (direccion[key].listMunicipio[direccion[key].municipio].toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") == Municipio.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")) ? duplicado+1 : duplicado;
													duplicado = (direccion[key].listColonia[direccion[key].colonia].toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") == Colonia.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")) ? duplicado+1 : duplicado;
													*/
													//direccion['sinSepomex'] = true;

													if (_.isEmpty(direccion[key].postal)) {
														duplicado = (direccion[key]?.oldCP == CP) ? duplicado + 1 : duplicado;
														duplicado = ((direccion[key]?.oldPais ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") == (Pais ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")) ? duplicado + 1 : duplicado;
														duplicado = ((direccion[key]?.oldEstado ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") == (Estado ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")) ? duplicado + 1 : duplicado;
														duplicado = ((direccion[key]?.oldMunicipio ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") == (Municipio ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")) ? duplicado + 1 : duplicado;
														duplicado = ((direccion[key]?.oldColonia ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") == (Colonia ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")) ? duplicado + 1 : duplicado;
													} else {
														duplicado = (direccion[key]?.valCodigoPostal == CP) ? duplicado + 1 : duplicado;
														duplicado = ((direccion[key]?.valPais ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") == (Pais ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")) ? duplicado + 1 : duplicado;
														duplicado = ((direccion[key]?.valEstado ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") == (Estado ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")) ? duplicado + 1 : duplicado;
														duplicado = ((direccion[key]?.valMunicipio ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") == (Municipio ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")) ? duplicado + 1 : duplicado;
														duplicado = ((direccion[key]?.valColonia ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") == (Colonia ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")) ? duplicado + 1 : duplicado;
													}

													duplicado = (contextol._limpiezaDatos(direccion[key].calle) == contextol._limpiezaDatos(Calle)) ? duplicado + 1 : duplicado;
													duplicado = (contextol._limpiezaDatos(direccion[key].numext) == contextol._limpiezaDatos(Exterior)) ? duplicado + 1 : duplicado;
													duplicado = (contextol._limpiezaDatos(direccion[key].numint) == contextol._limpiezaDatos(Interior)) ? duplicado + 1 : duplicado;
													duplicado = (direccion[key].inactivo == 0) ? duplicado + 1 : duplicado;
													if (direccion[key].indicadorSeleccionados.includes('^2^') && direccion[key].inactivo == 0) {
														cDireccionFiscal = cDireccionFiscal + 1;
														indice_indicador = key;
													}
													if (duplicado == 9) duplicados = 1;
													if (duplicado == 9 && cDireccionFiscal == 1) nada = 1;
													if (duplicado == 9 && cDireccionFiscal == 0) {
														var bloqueado = 1;
														var accesoFiscal = App.user.attributes.tct_alta_clientes_chk_c + App.user.attributes.tct_altaproveedor_chk_c + App.user.attributes.tct_alta_cd_chk_c + App.user.attributes.deudor_factoraje_c;
														if (accesoFiscal > 0) bloqueado = 0;
														// Indicador
														direccion[key].indicadorSeleccionados = direccion[key].indicadorSeleccionados + ',^2^';
														direccion[key].bloqueado = bloqueado;
														contexto_cuenta.cambio_previo_mail = '3';
														//contexto_cuenta.cambio_previo_mail = '1';
														var indicador = direccion[key].indicadorSeleccionados;
														var dir_indicador_map_list = app.lang.getAppListStrings('dir_indicador_map_list');
														indicador = indicador.substring(1, indicador.length - 1);
														indicador = indicador.split('^,^');
														indicador = indicador.sort((a, b) => a - b);
														for (var key1 in dir_indicador_map_list) {
															var value = app.lang.getAppListStrings('dir_indicador_map_list')[key1];
															if (value == indicador) direccion[key].indicador = key1;
														}
														cont_dir.oDirecciones.direccion = direccion;
														cont_dir.render();
														cDuplicado++;
														contexto_cuenta.$('#activar_camara').removeClass('disabled');
														contexto_cuenta.$('#activar_camara').attr('style', '');
														contexto_cuenta.$('#archivo_qr').removeClass('disabled');
														contexto_cuenta.$('#archivo_qr').attr('style', '');
														contexto_cuenta.$('#btnSubir').removeClass('disabled');
														contexto_cuenta.$('#btnSubir').attr('style', 'margin:10px');
														contexto_cuenta.$('#validar_QR').removeClass('disabled');
														contexto_cuenta.$('#validar_QR').attr('style', 'margin:10px');
														contexto_cuenta.$('#btn_Cancelar').removeClass('disabled');
														contexto_cuenta.$('#btn_Cancelar').attr('style', 'margin:10px');
														contexto_cuenta.$('#rfcModal').hide();
														cont_dir.render();
													}
												});
												// Agrega Dirección
												Colonia = (Colonia == ' ' || Colonia == '') ? '_' : Colonia;
												Ciudad = (Ciudad == '') ? '_' : Ciudad;

												var strUrl = 'DireccionesQR/' + CP + '/0/' + Colonia + '/' + Municipio + '/' + Estado + '/' + Ciudad;
												strUrl = strUrl.replaceAll(' ', '+');
												app.api.call('GET', app.api.buildURL(strUrl), null, {
													success: _.bind(function (data) {
														Colonia = (Colonia == '_') ? ' ' : Colonia;
														if (data.idCP) {
															var list_paises = data.paises;
															var list_municipios = data.municipios;
															//var city_list = App.metadata.getCities();
															var list_ciudades = data.ciudades;
															var list_estados = data.estados;
															var list_colonias = data.colonias;
															//País
															var listPais = {};
															var auxPais = '';
															for (var i = 0; i < list_paises.length; i++) {
																listPais[list_paises[i].idPais] = list_paises[i].namePais;
																auxPais = list_paises[i].idPais;
															}
															//Estado
															var listEstado = {};
															var auxEstado = '';
															for (var i = 0; i < list_estados.length; i++) {
																listEstado[list_estados[i].idEstado] = list_estados[i].nameEstado;
																auxEstado = list_estados[i].idEstado;
															}
															//Municipio
															var listMunicipio = {};
															var auxMunicipio = '';
															for (var i = 0; i < list_municipios.length; i++) {
																listMunicipio[list_municipios[i].idMunicipio] = list_municipios[i].nameMunicipio;
																if (list_municipios[i].nameMunicipio == Municipio) auxMunicipio = list_municipios[i].idMunicipio;
															}
															//Colonia
															var listColonia = {};
															var auxColonia = '';
															for (var i = 0; i < list_colonias.length; i++) {
																//listColonia[list_colonias[i].idColonia] = list_colonias[i].nameColonia;
																listColonia[i] = {};
																listColonia[i]['idColonia'] = list_colonias[i].idColonia;
																listColonia[i]['nameColonia'] = list_colonias[i].nameColonia;
																listColonia[i]['idCodigoPostal'] = list_colonias[i].idCodigoPostal;
																if (list_colonias[i].nameColonia == Colonia) auxColonia = list_colonias[i].idColonia;
															}
															/*if(auxColonia==''){
																listColonia['']="";
															}*/
															//Ciudad
															var listCiudad = {};
															var auxCiudad = '';
															var idSinCiudad = '';
															for (var i = 0; i < list_ciudades.length; i++) {
																listCiudad[list_ciudades[i].idCiudad] = list_ciudades[i].nameCiudad;
																if (list_ciudades[i].nameCiudad.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toUpperCase() == Ciudad.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toUpperCase()) auxCiudad = list_ciudades[i].idCiudad;
																idSinCiudad = (list_ciudades[i].nameCiudad == 'SIN CIUDAD') ? list_ciudades[i].idCiudad : idSinCiudad;
															}

															if (auxPais == "") auxPais = Object.keys(listPais)[0];
															if (auxEstado == "") auxEstado = Object.keys(listEstado)[0];
															if (auxMunicipio == "") auxMunicipio = Object.keys(listMunicipio)[0];
															if (auxColonia == "") auxColonia = listColonia[0]?.idColonia;
															if (auxCiudad == "") auxCiudad = Object.keys(listCiudad)[0];

															if (cDireccionFiscal >= 1) {
																//if(direccion[indice_indicador].indicador == 2) {
																if (direccion[indice_indicador].indicadorSeleccionados.includes('^2^') && duplicados == 1) {
																	direccion[indice_indicador].valCodigoPostal = CP;
																	direccion[indice_indicador].postal = data.idCP;
																	direccion[indice_indicador].calle = Calle.trim();
																	direccion[indice_indicador].numext = Exterior.trim();
																	direccion[indice_indicador].numint = Interior.trim();
																	direccion[indice_indicador].inactivo = 0;
																	//Pais
																	direccion[indice_indicador].pais = auxPais;
																	direccion[indice_indicador].listPais = listPais;
																	direccion[indice_indicador].listPaisFull = listPais;
																	//Estado
																	direccion[indice_indicador].estado = auxEstado;
																	direccion[indice_indicador].listEstado = listEstado;
																	direccion[indice_indicador].listEstadoFull = listEstado;
																	//Municipio
																	direccion[indice_indicador].municipio = auxMunicipio;
																	direccion[indice_indicador].listMunicipio = listMunicipio;
																	direccion[indice_indicador].listMunicipioFull = listMunicipio;
																	//Colonia
																	direccion[indice_indicador].colonia = auxColonia;
																	direccion[indice_indicador].listColonia = listColonia;
																	direccion[indice_indicador].listColoniaFull = listColonia;
																	//Ciudad
																	direccion[indice_indicador].ciudad = auxCiudad;
																	direccion[indice_indicador].listCiudad = listCiudad;
																	direccion[indice_indicador].listCiudadFull = listCiudad;
																} else {
																	if (nada == 0) {
																		var quita = '';
																		if (direccion[indice_indicador].indicadorSeleccionados.includes('^2^,')) {
																			quita = direccion[indice_indicador].indicadorSeleccionados.replace("^2^,", "");
																		}
																		if (direccion[indice_indicador].indicadorSeleccionados.includes(',^2^')) {
																			quita = direccion[indice_indicador].indicadorSeleccionados.replace(",^2^", "");
																		}
																		var indicador = quita;
																		var dir_indicador_map_list = app.lang.getAppListStrings('dir_indicador_map_list');
																		direccion[indice_indicador].indicadorSeleccionados = quita;
																		indicador = indicador.substring(1, indicador.length - 1);
																		indicador = indicador.split('^,^');
																		indicador = indicador.sort((a, b) => a - b);
																		for (var key1 in dir_indicador_map_list) {
																			var value = app.lang.getAppListStrings('dir_indicador_map_list')[key1];
																			if (value == indicador) direccion[indice_indicador].indicador = key1;
																		}
																		direccion[indice_indicador].bloqueado = 0;
																		cont_dir.oDirecciones.direccion = direccion;
																	}
																	if (duplicados == 0) {
																		var nuevaDireccion = {
																			"tipodedireccion": "",
																			"listTipo": App.lang.getAppListStrings('dir_tipo_unique_list'),
																			"tipoSeleccionados": "",
																			"indicador": "",
																			"listIndicador": App.lang.getAppListStrings('dir_indicador_unique_list'),
																			"indicadorSeleccionados": "",
																			"bloqueado": "",
																			"valCodigoPostal": "",
																			"postal": "",
																			"valPais": "",
																			"pais": "",
																			"listPais": {},
																			"listPaisFull": {},
																			"valEstado": "",
																			"estado": "",
																			"listEstado": {},
																			"listEstadoFull": {},
																			"valMunicipio": "",
																			"municipio": "",
																			"listMunicipio": {},
																			"listMunicipioFull": {},
																			"valCiudad": "",
																			"ciudad": "",
																			"listCiudad": {},
																			"listCiudadFull": {},
																			"valColonia": "",
																			"colonia": "",
																			"listColonia": {},
																			"listColoniaFull": {},
																			"calle": "",
																			"numext": "",
																			"numint": "",
																			"principal": "",
																			"inactivo": "",
																			"secuencia": "",
																			"id": "",
																			"direccionCompleta": ""
																		};
																		var bloqueado = 1;
																		var accesoFiscal = App.user.attributes.tct_alta_clientes_chk_c + App.user.attributes.tct_altaproveedor_chk_c + App.user.attributes.tct_alta_cd_chk_c + App.user.attributes.deudor_factoraje_c;
																		if (accesoFiscal > 0) bloqueado = 0;
																		if (!principal) nuevaDireccion.principal = "1";
																		nuevaDireccion.secuencia = secuencia;
																		nuevaDireccion.tipodedireccion = "1";
																		nuevaDireccion.tipoSeleccionados = '^1^';
																		nuevaDireccion.indicador = "2";
																		nuevaDireccion.indicadorSeleccionados = '^2^';
																		nuevaDireccion.bloqueado = bloqueado;
																		nuevaDireccion.valCodigoPostal = CP;
																		nuevaDireccion.postal = data.idCP;
																		nuevaDireccion.calle = Calle;
																		nuevaDireccion.numext = Exterior;
																		nuevaDireccion.numint = Interior;
																		//Pais
																		nuevaDireccion.pais = auxPais;
																		nuevaDireccion.listPais = listPais;
																		nuevaDireccion.listPaisFull = listPais;
																		//Estado
																		nuevaDireccion.estado = auxEstado;
																		nuevaDireccion.listEstado = listEstado;
																		nuevaDireccion.listEstadoFull = listEstado;
																		//Municipio
																		nuevaDireccion.municipio = auxMunicipio;
																		nuevaDireccion.listMunicipio = listMunicipio;
																		nuevaDireccion.listMunicipioFull = listMunicipio;
																		//Colonia
																		nuevaDireccion.colonia = auxColonia;
																		nuevaDireccion.listColonia = listColonia;
																		nuevaDireccion.listColoniaFull = listColonia;
																		//Ciudad
																		nuevaDireccion.ciudad = auxCiudad;
																		nuevaDireccion.listCiudad = listCiudad;
																		nuevaDireccion.listCiudadFull = listCiudad;
																		cont_dir.oDirecciones.direccion.push(nuevaDireccion);
																	}
																}
																cont_dir.render();
																app.alert.dismiss('procesando');
																app.alert.show('multiple_fiscal', {
																	level: 'info',
																	messages: 'Se han actualizado los datos de dirección fiscal'
																});
																contexto_cuenta.$('#activar_camara').removeClass('disabled');
																contexto_cuenta.$('#activar_camara').attr('style', '');
																contexto_cuenta.$('#archivo_qr').removeClass('disabled');
																contexto_cuenta.$('#archivo_qr').attr('style', '');
																contexto_cuenta.$('#btnSubir').removeClass('disabled');
																contexto_cuenta.$('#btnSubir').attr('style', 'margin:10px');
																contexto_cuenta.$('#validar_QR').removeClass('disabled');
																contexto_cuenta.$('#validar_QR').attr('style', 'margin:10px');
																contexto_cuenta.$('#btn_Cancelar').removeClass('disabled');
																contexto_cuenta.$('#btn_Cancelar').attr('style', 'margin:10px');
																contexto_cuenta.$('#rfcModal').hide();
																if (contexto_cuenta.cambio_previo_mail == '1') {
																	contexto_cuenta.cambio_previo_mail = '1';
																} else {
																	contexto_cuenta.cambio_previo_mail = '4';
																}
															} else {
																if (cDuplicado == 0 && duplicados == 0) {
																	var nuevaDireccion = {
																		"tipodedireccion": "",
																		"listTipo": App.lang.getAppListStrings('dir_tipo_unique_list'),
																		"tipoSeleccionados": "",
																		"indicador": "",
																		"listIndicador": App.lang.getAppListStrings('dir_indicador_unique_list'),
																		"indicadorSeleccionados": "",
																		"bloqueado": "",
																		"valCodigoPostal": "",
																		"postal": "",
																		"valPais": "",
																		"pais": "",
																		"listPais": {},
																		"listPaisFull": {},
																		"valEstado": "",
																		"estado": "",
																		"listEstado": {},
																		"listEstadoFull": {},
																		"valMunicipio": "",
																		"municipio": "",
																		"listMunicipio": {},
																		"listMunicipioFull": {},
																		"valCiudad": "",
																		"ciudad": "",
																		"listCiudad": {},
																		"listCiudadFull": {},
																		"valColonia": "",
																		"colonia": "",
																		"listColonia": {},
																		"listColoniaFull": {},
																		"calle": "",
																		"numext": "",
																		"numint": "",
																		"principal": "",
																		"inactivo": "",
																		"secuencia": "",
																		"id": "",
																		"direccionCompleta": ""
																	};
																	var bloqueado = 1;
																	var accesoFiscal = App.user.attributes.tct_alta_clientes_chk_c + App.user.attributes.tct_altaproveedor_chk_c + App.user.attributes.tct_alta_cd_chk_c + App.user.attributes.deudor_factoraje_c;
																	if (accesoFiscal > 0) bloqueado = 0;
																	if (!principal) nuevaDireccion.principal = "1";
																	nuevaDireccion.secuencia = "1";
																	nuevaDireccion.tipodedireccion = "1";
																	nuevaDireccion.tipoSeleccionados = '^1^';
																	nuevaDireccion.indicador = "2";
																	nuevaDireccion.indicadorSeleccionados = '^2^';
																	nuevaDireccion.bloqueado = bloqueado;
																	nuevaDireccion.valCodigoPostal = CP;
																	nuevaDireccion.postal = data.idCP;
																	nuevaDireccion.calle = Calle;
																	nuevaDireccion.numext = Exterior;
																	nuevaDireccion.numint = Interior;
																	//Pais
																	nuevaDireccion.pais = auxPais;
																	nuevaDireccion.listPais = listPais;
																	nuevaDireccion.listPaisFull = listPais;
																	//Estado
																	nuevaDireccion.estado = auxEstado;
																	nuevaDireccion.listEstado = listEstado;
																	nuevaDireccion.listEstadoFull = listEstado;
																	//Municipio
																	nuevaDireccion.municipio = auxMunicipio;
																	nuevaDireccion.listMunicipio = listMunicipio;
																	nuevaDireccion.listMunicipioFull = listMunicipio;
																	//Colonia
																	nuevaDireccion.colonia = auxColonia;
																	nuevaDireccion.listColonia = listColonia;
																	nuevaDireccion.listColoniaFull = listColonia;
																	//Ciudad
																	nuevaDireccion.ciudad = auxCiudad;
																	nuevaDireccion.listCiudad = listCiudad;
																	nuevaDireccion.listCiudadFull = listCiudad;
																	//var listCiudad = {};
																	//var ciudades = Object.values(city_list);
																	//nuevaDireccion.estado = (Object.keys(nuevaDireccion.listEstado)[0] != undefined) ? Object.keys(nuevaDireccion.listEstado)[0] : "";
																	//for (var [key, value] of Object.entries(nuevaDireccion.listEstado)) {
																	//	for (var i = 0; i < ciudades.length; i++) {
																	//	if (ciudades[i].estado_id == key) {
																	//		listCiudad[ciudades[i].id] = ciudades[i].name;
																	//		if(ciudades[i].name == Municipio) nuevaDireccion.ciudad = ciudades[i].id;
																	//	}
																	//	}
																	//}
																	//nuevaDireccion.listCiudad = listCiudad;
																	//nuevaDireccion.listCiudadFull = listCiudad;
																	cont_dir.oDirecciones.direccion.push(nuevaDireccion);
																	cont_dir.render();
																	app.alert.dismiss('procesando');
																	contexto_cuenta.$('#activar_camara').removeClass('disabled');
																	contexto_cuenta.$('#activar_camara').attr('style', '');
																	contexto_cuenta.$('#archivo_qr').removeClass('disabled');
																	contexto_cuenta.$('#archivo_qr').attr('style', '');
																	contexto_cuenta.$('#btnSubir').removeClass('disabled');
																	contexto_cuenta.$('#btnSubir').attr('style', 'margin:10px');
																	contexto_cuenta.$('#validar_QR').removeClass('disabled');
																	contexto_cuenta.$('#validar_QR').attr('style', 'margin:10px');
																	contexto_cuenta.$('#btn_Cancelar').removeClass('disabled');
																	contexto_cuenta.$('#btn_Cancelar').attr('style', 'margin:10px');
																	contexto_cuenta.$('#rfcModal').hide();
																	contexto_cuenta.cambio_previo_mail = '5';
																	//contexto_cuenta.render();
																} else {
																	cont_dir.render();
																	//app.alert.dismiss('procesando');
																	app.alert.show('cp_not_found', {
																		level: 'info',
																		messages: 'Se agrego tipo fiscal a una dirección existente'
																	});
																	contexto_cuenta.$('#activar_camara').removeClass('disabled');
																	contexto_cuenta.$('#activar_camara').attr('style', '');
																	contexto_cuenta.$('#archivo_qr').removeClass('disabled');
																	contexto_cuenta.$('#archivo_qr').attr('style', '');
																	contexto_cuenta.$('#btnSubir').removeClass('disabled');
																	contexto_cuenta.$('#btnSubir').attr('style', 'margin:10px');
																	contexto_cuenta.$('#validar_QR').removeClass('disabled');
																	contexto_cuenta.$('#validar_QR').attr('style', 'margin:10px');
																	contexto_cuenta.$('#btn_Cancelar').removeClass('disabled');
																	contexto_cuenta.$('#btn_Cancelar').attr('style', 'margin:10px');
																	contexto_cuenta.$('#rfcModal').hide();
																	//contexto_cuenta.render();
																}
															}
														}

														//Valida tipo de registro; (Cliente || Proveedor) && Origen != Seguros && Subtipo != Venta activo
														if ((contexto_cuenta.model.get('tipo_registro_cuenta_c') == '3' || contexto_cuenta.model.get('tipo_registro_cuenta_c') == '5') && contexto_cuenta.model.get('origen_cuenta_c') != '11' && contexto_cuenta.model.get('subtipo_registro_cuenta_c') != '11') {
															if (cambioRazonSocial['cambioDirFiscal'] || cambioRazonSocial['cambioCuenta']) {
																//Abre modal para indicar tipo de cambio
																//Restablece valores de custom fieldQR
																contexto_cuenta.$('#activar_camara').removeClass('disabled');
																contexto_cuenta.$('#activar_camara').attr('style', '');
																contexto_cuenta.$('#archivo_qr').removeClass('disabled');
																contexto_cuenta.$('#archivo_qr').attr('style', '');
																contexto_cuenta.$('#btnSubir').removeClass('disabled');
																contexto_cuenta.$('#btnSubir').attr('style', 'margin:10px');
																contexto_cuenta.$('#validar_QR').removeClass('disabled');
																contexto_cuenta.$('#validar_QR').attr('style', 'margin:10px');
																contexto_cuenta.$('#btn_Cancelar').removeClass('disabled');
																contexto_cuenta.$('#btn_Cancelar').attr('style', 'margin:10px');
																//contexto_cuenta.$('#rfcModal').hide();

																//Solo se muestra modal cuando detecta cambios en Razón Social
																if (cambioRazonSocial['cambioCuenta']) {
																	//Muestra modal cambios
																	$('#cambioRazonSocial').show();
																	if (!cambioRazonSocial['cambioDirFiscal'] && cambioRazonSocial['cambioCuenta']) {
																		$('.action1').show();
																		$('.action2').hide();
																		$('.action3').hide();
																	}
																	if (cambioRazonSocial['cambioDirFiscal'] && !cambioRazonSocial['cambioCuenta']) {
																		$('.action1').hide();
																		$('.action2').show();
																		$('.action3').hide();
																	}
																	if (cambioRazonSocial['cambioDirFiscal'] && cambioRazonSocial['cambioCuenta']) {
																		$('.action1').show();
																		$('.action2').show();
																		$('.action3').show();
																	}
																}
															}
														}
													})
												});
											},
										});
									}
								}
							})
						});
					} else {
						app.alert.show('errorCIEC', {
							level: 'warning',
							//messages: 'No se pudo identificar una estructura válida para el documento y/o no cumple con la estructura oficial del SAT.\nPor lo tanto, no se pueden validar los datos del contribuyente con los del SAT',
							messages: 'No se tiene información de CIEC activa, por favor realiza la actualización de datos por medio de carga de CSF.',
							autoClose: false
						});

						contexto_cuenta.$('#activar_camara').removeClass('disabled');
						contexto_cuenta.$('#activar_camara').attr('style', '');
						contexto_cuenta.$('#archivo_qr').removeClass('disabled');
						contexto_cuenta.$('#archivo_qr').attr('style', '');
						contexto_cuenta.$('#btnSubir').removeClass('disabled');
						contexto_cuenta.$('#btnSubir').attr('style', 'margin:10px');
						contexto_cuenta.$('#validar_QR').removeClass('disabled');
						contexto_cuenta.$('#validar_QR').attr('style', 'margin:10px');
						contexto_cuenta.$('#btn_Cancelar').removeClass('disabled');
						contexto_cuenta.$('#btn_Cancelar').attr('style', 'margin:10px');

						$btnCIEC.prop('disabled', false);
						$btnCIEC.text('Actualizar Constancia');

            			contextol.validarServicioQR();
            
					}
				}),
				error: function (error) {
					$btnCIEC.prop('disabled', false);
					$btnCIEC.text('Actualizar Constancia');			
				}
			});
		} else {
			$btnCIEC.prop('disabled', false);
			$btnCIEC.text('Actualizar Constancia');
			
			app.alert.show('errorAlertSinRFC', {
				level: 'error',
				messages: 'Se necesita valor de RFC. para continuar',
				autoClose: true
			});
			return;
		}
	},

	validarServicioQR: function () {
		
		var contextol = this;
		var input = contexto_cuenta.$('input[type=file]');
		var file = input[0].files[0];
		var c = document.createElement("canvas");
		var ctx = c.getContext('2d');
		//var imgn = new Image;
		//var imageData = '';
		if (contexto_cuenta.model.attributes.valid_cambio_razon_social_c) {
			app.alert.show('errorAlertCambio', {
				level: 'error',
				messages: 'Está cuenta se encuentra en validación de cambios y no puede ser modificada',
				autoClose: true
			});
			return;
		}
		if (contexto_cuenta.picturecam == false || self.picturecam == false) {
			var $btnCIEC = this.$('#validar_CIEC');

			if (file == "" || file == undefined) {
				$btnCIEC.prop('disabled', false);
				$btnCIEC.text('Actualizar Constancia');

				app.alert.show('errorAlert', {
					level: 'error',
					messages: 'No se tiene un archivo cargado para realizar la actualización por CSF.',
					autoClose: false
				});
			} else {
				app.alert.dismiss('errorCIEC');
				$btnCIEC.prop('disabled', true);
				$btnCIEC.text('Procesando...');

				//Enviamos petición con el archivo pdf convertido en base64
				if (window.result != "" && window.result != undefined) {
					app.alert.show('procesando', {
						level: 'process',
						title: 'Cargando...'
					});

					contexto_cuenta.$('#activar_camara').addClass('disabled');
					contexto_cuenta.$('#activar_camara').attr('style', 'pointer-events:none;');
					contexto_cuenta.$('#archivo_qr').addClass('disabled');
					contexto_cuenta.$('#archivo_qr').attr('style', 'pointer-events:none;');
					contexto_cuenta.$('#btnSubir').addClass('disabled');
					contexto_cuenta.$('#btnSubir').attr('style', 'pointer-events:none;margin:10px');
					contexto_cuenta.$('#validar_QR').addClass('disabled');
					contexto_cuenta.$('#validar_QR').attr('style', 'pointer-events:none;margin:10px');
					contexto_cuenta.$('#btn_Cancelar').addClass('disabled');
					contexto_cuenta.$('#btn_Cancelar').attr('style', 'pointer-events:none;margin:10px');

					var body = {
						"file": window.result
					}

					/*
					app.api.call('create', app.api.buildURL("GetInfoRFCbyCSF"), body , {
						success: _.bind(function (data) {
							console.log("RESPUESTA SERVICIO");
							app.alert.dismiss('procesando');

						})
					});
					*/
					app.api.call('create', app.api.buildURL("GetInfoRFCbyCSF"), body, {
						success: _.bind(function (data) {
							app.alert.dismiss('procesando');
							if (data != null) {
							if (data.detail == undefined) {
								var indice_indicador = 0;
								var Completo = '';
								var RFC = data["rfc"].toUpperCase();
								var ticket = data["ticket"];
								//var PathQR=data[0]["path_img_qr"];
								var Correo = data["email"];
								var CP = data["address"]["postalCode"];
								var Calle = data["address"]["riched_vialidad"].toUpperCase();
								var Exterior = data["address"]["streetNumber"].toUpperCase();
								var Interior = data["address"]["buildingNumber"].toUpperCase();
								//var Colonia = (data[0]["Colonia"] != undefined && data[0]["Colonia"] !='') ? data[0]["Colonia"] : ' ' ;
								//Se aplica normalize para reemplazar caracteres especiales
								var Ciudad = data["address"]["riched_d_ciudad"].toUpperCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");;
								var IdCiudad = data["address"]["riched_id_ciudad"];
								var Colonia = data["address"]["neighborhood"].toUpperCase();
								var Municipio = data["address"]["municipality"].toUpperCase();
								var Estado = data["address"]["state"].toUpperCase();
								var Regimen = data["taxRegimes"][0]["name"];
								var Pais = "MEXICO";
								var Regimenes = data["taxRegimes"];
								var LugarEmision = data["info"]["place_issued"];
								var FechaEmision = data["info"]["date_issued"];

								//actividades economicas
								// Filtrar las actividades con preponderancia 1
								var filteredActivities = data.economicActivities.filter(
									activity => activity.riched_code_preponderance === 1
								);

								var activity_sat_id = filteredActivities[0].riched_activity_sat_id;
								var activity_sat = filteredActivities[0].riched_activity_sat_desc;
								var code_cnvb = filteredActivities[0].riched_code_cnbv;
								var code_inegi = filteredActivities[0].riched_code_inegi;

								if (RFC != undefined) {
									if (RFC.length == 12) Regimen = "Persona Moral";
									if (Regimen == "Régimen de las Personas Físicas con Actividades Empresariales y Profesionales") Regimen = "Persona Fisica con Actividad Empresarial";
									if (Regimen != "Persona Fisica con Actividad Empresarial" && RFC.length == 13) Regimen = "Persona Fisica";
								}
								if (Estado == "MEXICO") Estado = "ESTADO DE MEXICO";
								if (Regimen == "Persona Moral") {
									//var Denominacion = data[0]["Denominación o Razón Social"]+" "+data[0]["Régimen de capital"];
									//var Constitucion = data[0]["Fecha de constitución"];
									//var Denominacion = data["company"]["legalName"]+" "+ data["company"]["entityType2"];
									var Denominacion = data["company"]["legalName"] + data["company"]["entityType4"];
									var RegimenCapital = data["company"]["entityType4"];
									var Constitucion = data["company"]["constitutionDate"];
									var LegalName = data["company"]["legalName"];
									Completo = Denominacion;
									//Constitucion = Constitucion.substring(6, 10) + "-" + Constitucion.substring(3, 5) + "-" + Constitucion.substring(0, 2);
									Constitucion = Constitucion.split('T')[0];
								} else {
									var Nombre = data["person"]["firstName"];
									var Paterno = data["person"]["middleName"];
									var Materno = data["person"]["lastName"];
									var CURP = data["person"]["curp"];
									var Nacimiento = data["person"]["birthdate"];
									Nacimiento = Nacimiento.split("T")[0];
									Completo = Nombre + " " + Paterno + " " + Materno;
									//Nacimiento = Nacimiento.substring(6, 10) + "-" + Nacimiento.substring(3, 5) + "-" + Nacimiento.substring(0, 2);
									//Nacimiento = Nacimiento.split('T')[0];
								}
								app.api.call("read", app.api.buildURL("Accounts/", null, null, {
									max_num: 5,
									"filter": [{
										"rfc_c": RFC,
									}]
								}), null, {
									success: _.bind(function (data) {
										if (data.records.length > 0 && contexto_cuenta.model.get('id') != data.records[0].id) {
											app.alert.dismiss('procesando');
											app.alert.show('errorAlert', {
												level: 'error',
												messages: "Ya existe la cuenta " + data.records[0].name,
											});
											contexto_cuenta.$('#activar_camara').removeClass('disabled');
											contexto_cuenta.$('#activar_camara').attr('style', '');
											contexto_cuenta.$('#archivo_qr').removeClass('disabled');
											contexto_cuenta.$('#archivo_qr').attr('style', '');
											contexto_cuenta.$('#btnSubir').removeClass('disabled');
											contexto_cuenta.$('#btnSubir').attr('style', 'margin:10px');
											contexto_cuenta.$('#validar_QR').removeClass('disabled');
											contexto_cuenta.$('#validar_QR').attr('style', 'margin:10px');
											contexto_cuenta.$('#btn_Cancelar').removeClass('disabled');
											contexto_cuenta.$('#btn_Cancelar').attr('style', 'margin:10px');
										} else {
											// Valida Regimen
											var verdad = false;
											if (Regimen != contexto_cuenta.model.get('tipodepersona_c') && contexto_cuenta.model.get('id')) {
												if (!Regimen.includes("Persona Fisica") || !contexto_cuenta.model.get('tipodepersona_c').includes("Persona Fisica")) verdad = true;
											}
											if (verdad) {
												app.alert.dismiss('procesando');
												app.alert.show('errorRegimen', {
													level: 'error',
													messages: "El Regimen encontrado con la CSF es diferente al de la cuenta",
												});
												contexto_cuenta.$('#activar_camara').removeClass('disabled');
												contexto_cuenta.$('#activar_camara').attr('style', '');
												contexto_cuenta.$('#archivo_qr').removeClass('disabled');
												contexto_cuenta.$('#archivo_qr').attr('style', '');
												contexto_cuenta.$('#btnSubir').removeClass('disabled');
												contexto_cuenta.$('#btnSubir').attr('style', 'margin:10px');
												contexto_cuenta.$('#validar_QR').removeClass('disabled');
												contexto_cuenta.$('#validar_QR').attr('style', 'margin:10px');
												contexto_cuenta.$('#btn_Cancelar').removeClass('disabled');
												contexto_cuenta.$('#btn_Cancelar').attr('style', 'margin:10px');
												
												$btnCIEC.prop('disabled', false);
												$btnCIEC.text('Actualizar Constancia');

											} else {
												app.alert.show('errorAlert2', {
													level:
														'confirmation',
													messages: "La información recuperada con la CSF proporcionada corresponde a: " + Completo + " ¿Desea proceder con estos datos?",
													autoClose: false,
													onCancel: function () {
														$btnCIEC.prop('disabled', false);
														$btnCIEC.text('Actualizar Constancia');

														contexto_cuenta.$('#activar_camara').removeClass('disabled');
														contexto_cuenta.$('#activar_camara').attr('style', '');
														contexto_cuenta.$('#archivo_qr').removeClass('disabled');
														contexto_cuenta.$('#archivo_qr').attr('style', '');
														contexto_cuenta.$('#btnSubir').removeClass('disabled');
														contexto_cuenta.$('#btnSubir').attr('style', 'margin:10px');
														contexto_cuenta.$('#validar_QR').removeClass('disabled');
														contexto_cuenta.$('#validar_QR').attr('style', 'margin:10px');
														contexto_cuenta.$('#btn_Cancelar').removeClass('disabled');
														contexto_cuenta.$('#btn_Cancelar').attr('style', 'margin:10px');
													},
													onConfirm: function () {
														//Comienza integraciones con Alfresco, Quantico y Robina
														//contextol.FileintegraCSF(contexto_cuenta.model.get('id'), RFC, window.result, FechaEmision);

														//NUEVO SERVICIO PROCESO ROBINA API
														                                //(RFC, fechaEmision , ticket)
														if(RFC != null && ticket != null){
															contextol.registroProcesoRobinaAPI(RFC, FechaEmision, ticket,contexto_cuenta.model.get('id'));
														}

														$btnCIEC.prop('disabled', false);
														$btnCIEC.text('Actualizar Constancia');

														// Actualiza Datos Personales
														contexto_cuenta.model.set('tipodepersona_c', Regimen);
														contexto_cuenta.model.set('rfc_c', RFC);
														contexto_cuenta.model.set("regimenes_fiscal_sat_c", JSON.stringify(Regimenes));
														var fechaEmisionFormat = contextol.formatDate(FechaEmision);
														var lugarFechaEmision = LugarEmision + ' a ' + fechaEmisionFormat;
														contexto_cuenta.model.set('emision_csf_c', lugarFechaEmision);

														//clasf_sectorial.ActividadEconomica.ae = code_cnvb;
														//clasf_sectorial.ActividadEconomica.inegi_clase = code_inegi;

														clasf_sectorial.ActividadEconomica.ResumenSAT.aes.id_actividad_economica_sat = activity_sat_id;
														clasf_sectorial.ActividadEconomica.ResumenSAT.aes.actividad_economica_sat = activity_sat;

														contextol.getClasificacionSectorial(code_cnvb);
														clasf_sectorial.render();

														//contexto_cuenta.model.set('path_img_qr_c', PathQR);
														cambioRazonSocial['cambioCuenta'] = false;
														cambioRazonSocial['Cuenta'] = [];
														cambioRazonSocial['Cuenta']['razonsocial_c'] = contexto_cuenta.model.get('razonsocial_c');
														cambioRazonSocial['Cuenta']['primernombre_c'] = contexto_cuenta.model.get('primernombre_c');
														cambioRazonSocial['Cuenta']['apellidopaterno_c'] = contexto_cuenta.model.get('apellidopaterno_c');
														cambioRazonSocial['Cuenta']['apellidomaterno_c'] = contexto_cuenta.model.get('apellidomaterno_c');
														cambioRazonSocial['Direccion'] = app.utils.deepCopy(contexto_cuenta.prev_oDirecciones.prev_direccion);

														if (Regimen == "Persona Moral") {
															//Valida cambios
															//Para una Persona Moral, los espacios. puntos o comas, no se deben tomar en cuenta como un cambio para notificar
															var name_actual = contexto_cuenta.model.get("razonsocial_c");
															var name_nuevo = Denominacion;
															var regimen_capital = RegimenCapital;
															cambioRazonSocial["cambioCuenta"] = contextol.evaluaCambioRegimenCapital(name_actual, name_nuevo, regimen_capital);
															//cambioRazonSocial['cambioCuenta'] = contexto_cuenta.model.get('razonsocial_c') != Denominacion ? true : cambioRazonSocial['cambioCuenta'];
															contexto_cuenta.model.set('razonsocial_c', Denominacion);
															contexto_cuenta.model.set('denominacion_c', LegalName);
															contexto_cuenta.model.set('nombre_comercial_c', Denominacion);
															contexto_cuenta.model.set('fechaconstitutiva_c', Constitucion);
															contexto_cuenta.model.set('regimen_capital_c', RegimenCapital);
														} else {

															cambioRazonSocial['cambioCuenta'] = contexto_cuenta.model.get('primernombre_c') != Nombre ? true : cambioRazonSocial['cambioCuenta'];
															cambioRazonSocial['cambioCuenta'] = contexto_cuenta.model.get('apellidopaterno_c') != Paterno ? true : cambioRazonSocial['cambioCuenta'];
															cambioRazonSocial['cambioCuenta'] = contexto_cuenta.model.get('apellidomaterno_c') != Materno ? true : cambioRazonSocial['cambioCuenta'];
															contexto_cuenta.model.set('primernombre_c', Nombre);
															contexto_cuenta.model.set('apellidopaterno_c', Paterno);
															contexto_cuenta.model.set('apellidomaterno_c', Materno);
															//contexto_cuenta.model.set('fechadenacimiento_c', Nacimiento);
															contexto_cuenta.model.set('curp_c', CURP);
														}
														//self.model.set('email1', Correo);

														var arrcorreos = [];
														var repetido = 0;
														if (Correo != "") {
															if (contexto_cuenta.model.attributes.email !== undefined) {
																arrcorreos = contexto_cuenta.model.attributes.email;
																if (arrcorreos.length > 0) {
																	for (var y = 0; y < arrcorreos.length; y++) {
																		if (arrcorreos[y].email_address == Correo) {
																			repetido = 1;
																		}
																	}
																	if (repetido == 0) {
																		if (Correo !== null && Correo !== "") {
																			arrcorreos[arrcorreos.length] = { email_address: Correo, primary_address: false };
																			contexto_cuenta.cambio_previo_mail = '2';
																		}
																	}
																} else {
																	arrcorreos = [{ email_address: Correo, primary_address: true }];
																	contexto_cuenta.cambio_previo_mail = '1';
																}
															} else {
																arrcorreos = [{ email_address: Correo, primary_address: true }];
																contexto_cuenta.cambio_previo_mail = '1';

															}
															contexto_cuenta.model.set('email', arrcorreos);
															currentValue = contexto_cuenta.model.get('email');
															emailFieldHtml = cont_qr._buildEmailFieldHtml({
																email_address: Correo,
																primary_address: true,
																opt_out: false,
																invalid_email: false
															});
															//self.render();
															$newEmailField = contexto_cuenta.$('.newEmail').closest('.email').before(emailFieldHtml);
														}
														// Valida duplicado
														cont_dir.oDirecciones = contexto_cuenta.oDirecciones;
														cont_tel.oTelefonos = contexto_cuenta.oTelefonos;
														cont_tel.render();
														pipeacc.tipoSubtipo_vista();
														clasf_sectorial.ActividadEconomica = contexto_cuenta.ActividadEconomica;
														clasf_sectorial.ResumenCliente = contexto_cuenta.ResumenCliente;
														clasf_sectorial.render();
														pld.ProductosPLD = contexto_cuenta.ProductosPLD;
														pld.render();
														var nada = 0;
														var secuencia = 0;
														var principal = 0;
														var duplicado = 0;
														var duplicados = 0;
														var cDuplicado = 0;
														var cDireccionFiscal = 0;
														var homologado = 0;
														var direccion = cont_dir.oDirecciones.direccion;
														cambioRazonSocial['cambioDirFiscal'] = false;
														//Itera para validar diferencia en dirección fiscal
														//cambioRazonSocial['cambioCuenta'] = self.model.get('primernombre_c') != Nombre ? true : cambioRazonSocial['cambioCuenta'];
														Object.keys(direccion).forEach(key => {
															//Valida dirección fiscal
															if (direccion[key].indicadorSeleccionados.includes('^2^') && direccion[key].inactivo == 0) {
																cambiaDirFiscal = 0;
																cambiaDirFiscal = (direccion[key].valCodigoPostal != CP) ? cambiaDirFiscal + 1 : cambiaDirFiscal;
																cambiaDirFiscal = (direccion[key].listPais[direccion[key].pais] != Pais) ? cambiaDirFiscal + 1 : cambiaDirFiscal;
																cambiaDirFiscal = (direccion[key].listMunicipio[direccion[key].municipio] != Municipio) ? cambiaDirFiscal + 1 : cambiaDirFiscal;
																cambiaDirFiscal = (direccion[key].listColonia[direccion[key].colonia] != Colonia) ? cambiaDirFiscal + 1 : cambiaDirFiscal;
																cambiaDirFiscal = (contextol._limpiezaDatos(direccion[key].calle) != contextol._limpiezaDatos(Calle)) ? cambiaDirFiscal + 1 : cambiaDirFiscal;
																cambiaDirFiscal = (contextol._limpiezaDatos(direccion[key].numext) != contextol._limpiezaDatos(Exterior)) ? cambiaDirFiscal + 1 : cambiaDirFiscal;
																cambiaDirFiscal = (contextol._limpiezaDatos(direccion[key].numint) != contextol._limpiezaDatos(Interior)) ? cambiaDirFiscal + 1 : cambiaDirFiscal;
																if (cambiaDirFiscal >= 1) {
																	cambioRazonSocial['cambioDirFiscal'] = true;
																}
															}
														});
														var auxd = '';
														var auxd1 = '';
														Object.keys(direccion).forEach(key => {
															duplicado = 0;
															secuencia = secuencia + 1;
															if (direccion[key].principal && !direccion[key].inactivo) principal = 1;
															/*
															duplicado = (direccion[key].valCodigoPostal == CP) ? duplicado+1 : duplicado;
															duplicado = (direccion[key].listPais[direccion[key].pais].toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") == Pais.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")) ? duplicado+1 : duplicado;
															duplicado = (direccion[key].listEstado[direccion[key].estado].toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") == Estado.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")) ? duplicado+1 : duplicado;
															duplicado = (direccion[key].listMunicipio[direccion[key].municipio].toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") == Municipio.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")) ? duplicado+1 : duplicado;
															duplicado = (direccion[key].listColonia[direccion[key].colonia].toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") == Colonia.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")) ? duplicado+1 : duplicado;
															*/
															//direccion['sinSepomex'] = true;

															if (_.isEmpty(direccion[key].postal)) {
																duplicado = (direccion[key]?.oldCP == CP) ? duplicado + 1 : duplicado;
																duplicado = ((direccion[key]?.oldPais ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") == (Pais ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")) ? duplicado + 1 : duplicado;
																duplicado = ((direccion[key]?.oldEstado ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") == (Estado ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")) ? duplicado + 1 : duplicado;
																duplicado = ((direccion[key]?.oldMunicipio ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") == (Municipio ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")) ? duplicado + 1 : duplicado;
																duplicado = ((direccion[key]?.oldColonia ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") == (Colonia ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")) ? duplicado + 1 : duplicado;
															} else {
																duplicado = (direccion[key]?.valCodigoPostal == CP) ? duplicado + 1 : duplicado;
																duplicado = ((direccion[key]?.valPais ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") == (Pais ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")) ? duplicado + 1 : duplicado;
																duplicado = ((direccion[key]?.valEstado ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") == (Estado ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")) ? duplicado + 1 : duplicado;
																duplicado = ((direccion[key]?.valMunicipio ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") == (Municipio ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")) ? duplicado + 1 : duplicado;
																duplicado = ((direccion[key]?.valColonia ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") == (Colonia ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")) ? duplicado + 1 : duplicado;
															}

															duplicado = (contextol._limpiezaDatos(direccion[key].calle) == contextol._limpiezaDatos(Calle)) ? duplicado + 1 : duplicado;
															duplicado = (contextol._limpiezaDatos(direccion[key].numext) == contextol._limpiezaDatos(Exterior)) ? duplicado + 1 : duplicado;
															duplicado = (contextol._limpiezaDatos(direccion[key].numint) == contextol._limpiezaDatos(Interior)) ? duplicado + 1 : duplicado;
															duplicado = (direccion[key].inactivo == 0) ? duplicado + 1 : duplicado;
															if (direccion[key].indicadorSeleccionados.includes('^2^') && direccion[key].inactivo == 0) {
																cDireccionFiscal = cDireccionFiscal + 1;
																indice_indicador = key;
															}
															if (duplicado == 9) duplicados = 1;
															if (duplicado == 9 && cDireccionFiscal == 1) nada = 1;
															if (duplicado == 9 && cDireccionFiscal == 0) {
																var bloqueado = 1;
																var accesoFiscal = App.user.attributes.tct_alta_clientes_chk_c + App.user.attributes.tct_altaproveedor_chk_c + App.user.attributes.tct_alta_cd_chk_c + App.user.attributes.deudor_factoraje_c;
																if (accesoFiscal > 0) bloqueado = 0;
																// Indicador
																direccion[key].indicadorSeleccionados = direccion[key].indicadorSeleccionados + ',^2^';
																direccion[key].bloqueado = bloqueado;
																contexto_cuenta.cambio_previo_mail = '3';
																//contexto_cuenta.cambio_previo_mail = '1';
																var indicador = direccion[key].indicadorSeleccionados;
																var dir_indicador_map_list = app.lang.getAppListStrings('dir_indicador_map_list');
																indicador = indicador.substring(1, indicador.length - 1);
																indicador = indicador.split('^,^');
																indicador = indicador.sort((a, b) => a - b);
																for (var key1 in dir_indicador_map_list) {
																	var value = app.lang.getAppListStrings('dir_indicador_map_list')[key1];
																	if (value == indicador) direccion[key].indicador = key1;
																}
																cont_dir.oDirecciones.direccion = direccion;
																cont_dir.render();
																cDuplicado++;
																contexto_cuenta.$('#activar_camara').removeClass('disabled');
																contexto_cuenta.$('#activar_camara').attr('style', '');
																contexto_cuenta.$('#archivo_qr').removeClass('disabled');
																contexto_cuenta.$('#archivo_qr').attr('style', '');
																contexto_cuenta.$('#btnSubir').removeClass('disabled');
																contexto_cuenta.$('#btnSubir').attr('style', 'margin:10px');
																contexto_cuenta.$('#validar_QR').removeClass('disabled');
																contexto_cuenta.$('#validar_QR').attr('style', 'margin:10px');
																contexto_cuenta.$('#btn_Cancelar').removeClass('disabled');
																contexto_cuenta.$('#btn_Cancelar').attr('style', 'margin:10px');
																contexto_cuenta.$('#rfcModal').hide();
																cont_dir.render();
															}
														});
														// Agrega Dirección
														Colonia = (Colonia == ' ' || Colonia == '') ? '_' : Colonia;
														Ciudad = (Ciudad == '') ? '_' : Ciudad;

														var strUrl = 'DireccionesQR/' + CP + '/0/' + Colonia + '/' + Municipio + '/' + Estado + '/' + Ciudad;
														strUrl = strUrl.replaceAll(' ', '+');
														app.api.call('GET', app.api.buildURL(strUrl), null, {
															success: _.bind(function (data) {
																Colonia = (Colonia == '_') ? ' ' : Colonia;
																if (data.idCP) {
																	var list_paises = data.paises;
																	var list_municipios = data.municipios;
																	//var city_list = App.metadata.getCities();
																	var list_ciudades = data.ciudades;
																	var list_estados = data.estados;
																	var list_colonias = data.colonias;
																	//País
																	var listPais = {};
																	var auxPais = '';
																	for (var i = 0; i < list_paises.length; i++) {
																		listPais[list_paises[i].idPais] = list_paises[i].namePais;
																		auxPais = list_paises[i].idPais;
																	}
																	//Estado
																	var listEstado = {};
																	var auxEstado = '';
																	for (var i = 0; i < list_estados.length; i++) {
																		listEstado[list_estados[i].idEstado] = list_estados[i].nameEstado;
																		auxEstado = list_estados[i].idEstado;
																	}
																	//Municipio
																	var listMunicipio = {};
																	var auxMunicipio = '';
																	for (var i = 0; i < list_municipios.length; i++) {
																		listMunicipio[list_municipios[i].idMunicipio] = list_municipios[i].nameMunicipio;
																		if (list_municipios[i].nameMunicipio == Municipio) auxMunicipio = list_municipios[i].idMunicipio;
																	}
																	//Colonia
																	var listColonia = {};
																	var auxColonia = '';
																	for (var i = 0; i < list_colonias.length; i++) {
																		//listColonia[list_colonias[i].idColonia] = list_colonias[i].nameColonia;
																		listColonia[i] = {};
																		listColonia[i]['idColonia'] = list_colonias[i].idColonia;
																		listColonia[i]['nameColonia'] = list_colonias[i].nameColonia;
																		listColonia[i]['idCodigoPostal'] = list_colonias[i].idCodigoPostal;
																		if (list_colonias[i].nameColonia == Colonia) auxColonia = list_colonias[i].idColonia;
																	}
																	/*if(auxColonia==''){
																		listColonia['']="";
																	}*/
																	//Ciudad
																	var listCiudad = {};
																	var auxCiudad = '';
																	var idSinCiudad = '';
																	for (var i = 0; i < list_ciudades.length; i++) {
																		listCiudad[list_ciudades[i].idCiudad] = list_ciudades[i].nameCiudad;
																		if (list_ciudades[i].nameCiudad.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toUpperCase() == Ciudad.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toUpperCase()) auxCiudad = list_ciudades[i].idCiudad;
																		idSinCiudad = (list_ciudades[i].nameCiudad == 'SIN CIUDAD') ? list_ciudades[i].idCiudad : idSinCiudad;
																	}

																	if (auxPais == "") auxPais = Object.keys(listPais)[0];
																	if (auxEstado == "") auxEstado = Object.keys(listEstado)[0];
																	if (auxMunicipio == "") auxMunicipio = Object.keys(listMunicipio)[0];
																	if (auxColonia == "") auxColonia = listColonia[0]?.idColonia;
																	if (auxCiudad == "") auxCiudad = Object.keys(listCiudad)[0];

																	if (cDireccionFiscal >= 1) {
																		//if(direccion[indice_indicador].indicador == 2) {
																		if (direccion[indice_indicador].indicadorSeleccionados.includes('^2^') && duplicados == 1) {
																			direccion[indice_indicador].valCodigoPostal = CP;
																			direccion[indice_indicador].postal = data.idCP;
																			direccion[indice_indicador].calle = Calle.trim();
																			direccion[indice_indicador].numext = Exterior.trim();
																			direccion[indice_indicador].numint = Interior.trim();
																			direccion[indice_indicador].inactivo = 0;
																			//Pais
																			direccion[indice_indicador].pais = auxPais;
																			direccion[indice_indicador].listPais = listPais;
																			direccion[indice_indicador].listPaisFull = listPais;
																			//Estado
																			direccion[indice_indicador].estado = auxEstado;
																			direccion[indice_indicador].listEstado = listEstado;
																			direccion[indice_indicador].listEstadoFull = listEstado;
																			//Municipio
																			direccion[indice_indicador].municipio = auxMunicipio;
																			direccion[indice_indicador].listMunicipio = listMunicipio;
																			direccion[indice_indicador].listMunicipioFull = listMunicipio;
																			//Colonia
																			direccion[indice_indicador].colonia = auxColonia;
																			direccion[indice_indicador].listColonia = listColonia;
																			direccion[indice_indicador].listColoniaFull = listColonia;
																			//Ciudad
																			direccion[indice_indicador].ciudad = auxCiudad;
																			direccion[indice_indicador].listCiudad = listCiudad;
																			direccion[indice_indicador].listCiudadFull = listCiudad;
																		} else {
																			if (nada == 0) {
																				var quita = '';
																				if (direccion[indice_indicador].indicadorSeleccionados.includes('^2^,')) {
																					quita = direccion[indice_indicador].indicadorSeleccionados.replace("^2^,", "");
																				}
																				if (direccion[indice_indicador].indicadorSeleccionados.includes(',^2^')) {
																					quita = direccion[indice_indicador].indicadorSeleccionados.replace(",^2^", "");
																				}
																				var indicador = quita;
																				var dir_indicador_map_list = app.lang.getAppListStrings('dir_indicador_map_list');
																				direccion[indice_indicador].indicadorSeleccionados = quita;
																				indicador = indicador.substring(1, indicador.length - 1);
																				indicador = indicador.split('^,^');
																				indicador = indicador.sort((a, b) => a - b);
																				for (var key1 in dir_indicador_map_list) {
																					var value = app.lang.getAppListStrings('dir_indicador_map_list')[key1];
																					if (value == indicador) direccion[indice_indicador].indicador = key1;
																				}
																				direccion[indice_indicador].bloqueado = 0;
																				cont_dir.oDirecciones.direccion = direccion;
																			}
																			if (duplicados == 0) {
																				var nuevaDireccion = {
																					"tipodedireccion": "",
																					"listTipo": App.lang.getAppListStrings('dir_tipo_unique_list'),
																					"tipoSeleccionados": "",
																					"indicador": "",
																					"listIndicador": App.lang.getAppListStrings('dir_indicador_unique_list'),
																					"indicadorSeleccionados": "",
																					"bloqueado": "",
																					"valCodigoPostal": "",
																					"postal": "",
																					"valPais": "",
																					"pais": "",
																					"listPais": {},
																					"listPaisFull": {},
																					"valEstado": "",
																					"estado": "",
																					"listEstado": {},
																					"listEstadoFull": {},
																					"valMunicipio": "",
																					"municipio": "",
																					"listMunicipio": {},
																					"listMunicipioFull": {},
																					"valCiudad": "",
																					"ciudad": "",
																					"listCiudad": {},
																					"listCiudadFull": {},
																					"valColonia": "",
																					"colonia": "",
																					"listColonia": {},
																					"listColoniaFull": {},
																					"calle": "",
																					"numext": "",
																					"numint": "",
																					"principal": "",
																					"inactivo": "",
																					"secuencia": "",
																					"id": "",
																					"direccionCompleta": ""
																				};
																				var bloqueado = 1;
																				var accesoFiscal = App.user.attributes.tct_alta_clientes_chk_c + App.user.attributes.tct_altaproveedor_chk_c + App.user.attributes.tct_alta_cd_chk_c + App.user.attributes.deudor_factoraje_c;
																				if (accesoFiscal > 0) bloqueado = 0;
																				if (!principal) nuevaDireccion.principal = "1";
																				nuevaDireccion.secuencia = secuencia;
																				nuevaDireccion.tipodedireccion = "1";
																				nuevaDireccion.tipoSeleccionados = '^1^';
																				nuevaDireccion.indicador = "2";
																				nuevaDireccion.indicadorSeleccionados = '^2^';
																				nuevaDireccion.bloqueado = bloqueado;
																				nuevaDireccion.valCodigoPostal = CP;
																				nuevaDireccion.postal = data.idCP;
																				nuevaDireccion.calle = Calle;
																				nuevaDireccion.numext = Exterior;
																				nuevaDireccion.numint = Interior;
																				//Pais
																				nuevaDireccion.pais = auxPais;
																				nuevaDireccion.listPais = listPais;
																				nuevaDireccion.listPaisFull = listPais;
																				//Estado
																				nuevaDireccion.estado = auxEstado;
																				nuevaDireccion.listEstado = listEstado;
																				nuevaDireccion.listEstadoFull = listEstado;
																				//Municipio
																				nuevaDireccion.municipio = auxMunicipio;
																				nuevaDireccion.listMunicipio = listMunicipio;
																				nuevaDireccion.listMunicipioFull = listMunicipio;
																				//Colonia
																				nuevaDireccion.colonia = auxColonia;
																				nuevaDireccion.listColonia = listColonia;
																				nuevaDireccion.listColoniaFull = listColonia;
																				//Ciudad
																				nuevaDireccion.ciudad = auxCiudad;
																				nuevaDireccion.listCiudad = listCiudad;
																				nuevaDireccion.listCiudadFull = listCiudad;
																				cont_dir.oDirecciones.direccion.push(nuevaDireccion);
																			}
																		}
																		cont_dir.render();
																		app.alert.dismiss('procesando');
																		app.alert.show('multiple_fiscal', {
																			level: 'info',
																			messages: 'Se han actualizado los datos de dirección fiscal'
																		});
																		contexto_cuenta.$('#activar_camara').removeClass('disabled');
																		contexto_cuenta.$('#activar_camara').attr('style', '');
																		contexto_cuenta.$('#archivo_qr').removeClass('disabled');
																		contexto_cuenta.$('#archivo_qr').attr('style', '');
																		contexto_cuenta.$('#btnSubir').removeClass('disabled');
																		contexto_cuenta.$('#btnSubir').attr('style', 'margin:10px');
																		contexto_cuenta.$('#validar_QR').removeClass('disabled');
																		contexto_cuenta.$('#validar_QR').attr('style', 'margin:10px');
																		contexto_cuenta.$('#btn_Cancelar').removeClass('disabled');
																		contexto_cuenta.$('#btn_Cancelar').attr('style', 'margin:10px');
																		contexto_cuenta.$('#rfcModal').hide();
																		if (contexto_cuenta.cambio_previo_mail == '1') {
																			contexto_cuenta.cambio_previo_mail = '1';
																		} else {
																			contexto_cuenta.cambio_previo_mail = '4';
																		}
																	} else {
																		if (cDuplicado == 0 && duplicados == 0) {
																			var nuevaDireccion = {
																				"tipodedireccion": "",
																				"listTipo": App.lang.getAppListStrings('dir_tipo_unique_list'),
																				"tipoSeleccionados": "",
																				"indicador": "",
																				"listIndicador": App.lang.getAppListStrings('dir_indicador_unique_list'),
																				"indicadorSeleccionados": "",
																				"bloqueado": "",
																				"valCodigoPostal": "",
																				"postal": "",
																				"valPais": "",
																				"pais": "",
																				"listPais": {},
																				"listPaisFull": {},
																				"valEstado": "",
																				"estado": "",
																				"listEstado": {},
																				"listEstadoFull": {},
																				"valMunicipio": "",
																				"municipio": "",
																				"listMunicipio": {},
																				"listMunicipioFull": {},
																				"valCiudad": "",
																				"ciudad": "",
																				"listCiudad": {},
																				"listCiudadFull": {},
																				"valColonia": "",
																				"colonia": "",
																				"listColonia": {},
																				"listColoniaFull": {},
																				"calle": "",
																				"numext": "",
																				"numint": "",
																				"principal": "",
																				"inactivo": "",
																				"secuencia": "",
																				"id": "",
																				"direccionCompleta": ""
																			};
																			var bloqueado = 1;
																			var accesoFiscal = App.user.attributes.tct_alta_clientes_chk_c + App.user.attributes.tct_altaproveedor_chk_c + App.user.attributes.tct_alta_cd_chk_c + App.user.attributes.deudor_factoraje_c;
																			if (accesoFiscal > 0) bloqueado = 0;
																			if (!principal) nuevaDireccion.principal = "1";
																			nuevaDireccion.secuencia = "1";
																			nuevaDireccion.tipodedireccion = "1";
																			nuevaDireccion.tipoSeleccionados = '^1^';
																			nuevaDireccion.indicador = "2";
																			nuevaDireccion.indicadorSeleccionados = '^2^';
																			nuevaDireccion.bloqueado = bloqueado;
																			nuevaDireccion.valCodigoPostal = CP;
																			nuevaDireccion.postal = data.idCP;
																			nuevaDireccion.calle = Calle;
																			nuevaDireccion.numext = Exterior;
																			nuevaDireccion.numint = Interior;
																			//Pais
																			nuevaDireccion.pais = auxPais;
																			nuevaDireccion.listPais = listPais;
																			nuevaDireccion.listPaisFull = listPais;
																			//Estado
																			nuevaDireccion.estado = auxEstado;
																			nuevaDireccion.listEstado = listEstado;
																			nuevaDireccion.listEstadoFull = listEstado;
																			//Municipio
																			nuevaDireccion.municipio = auxMunicipio;
																			nuevaDireccion.listMunicipio = listMunicipio;
																			nuevaDireccion.listMunicipioFull = listMunicipio;
																			//Colonia
																			nuevaDireccion.colonia = auxColonia;
																			nuevaDireccion.listColonia = listColonia;
																			nuevaDireccion.listColoniaFull = listColonia;
																			//Ciudad
																			nuevaDireccion.ciudad = auxCiudad;
																			nuevaDireccion.listCiudad = listCiudad;
																			nuevaDireccion.listCiudadFull = listCiudad;
																			//var listCiudad = {};
																			//var ciudades = Object.values(city_list);
																			//nuevaDireccion.estado = (Object.keys(nuevaDireccion.listEstado)[0] != undefined) ? Object.keys(nuevaDireccion.listEstado)[0] : "";
																			//for (var [key, value] of Object.entries(nuevaDireccion.listEstado)) {
																			//	for (var i = 0; i < ciudades.length; i++) {
																			//	if (ciudades[i].estado_id == key) {
																			//		listCiudad[ciudades[i].id] = ciudades[i].name;
																			//		if(ciudades[i].name == Municipio) nuevaDireccion.ciudad = ciudades[i].id;
																			//	}
																			//	}
																			//}
																			//nuevaDireccion.listCiudad = listCiudad;
																			//nuevaDireccion.listCiudadFull = listCiudad;
																			cont_dir.oDirecciones.direccion.push(nuevaDireccion);
																			cont_dir.render();
																			app.alert.dismiss('procesando');
																			contexto_cuenta.$('#activar_camara').removeClass('disabled');
																			contexto_cuenta.$('#activar_camara').attr('style', '');
																			contexto_cuenta.$('#archivo_qr').removeClass('disabled');
																			contexto_cuenta.$('#archivo_qr').attr('style', '');
																			contexto_cuenta.$('#btnSubir').removeClass('disabled');
																			contexto_cuenta.$('#btnSubir').attr('style', 'margin:10px');
																			contexto_cuenta.$('#validar_QR').removeClass('disabled');
																			contexto_cuenta.$('#validar_QR').attr('style', 'margin:10px');
																			contexto_cuenta.$('#btn_Cancelar').removeClass('disabled');
																			contexto_cuenta.$('#btn_Cancelar').attr('style', 'margin:10px');
																			contexto_cuenta.$('#rfcModal').hide();
																			contexto_cuenta.cambio_previo_mail = '5';
																			//contexto_cuenta.render();
																		} else {
																			cont_dir.render();
																			//app.alert.dismiss('procesando');
																			app.alert.show('cp_not_found', {
																				level: 'info',
																				messages: 'Se agrego tipo fiscal a una dirección existente'
																			});
																			contexto_cuenta.$('#activar_camara').removeClass('disabled');
																			contexto_cuenta.$('#activar_camara').attr('style', '');
																			contexto_cuenta.$('#archivo_qr').removeClass('disabled');
																			contexto_cuenta.$('#archivo_qr').attr('style', '');
																			contexto_cuenta.$('#btnSubir').removeClass('disabled');
																			contexto_cuenta.$('#btnSubir').attr('style', 'margin:10px');
																			contexto_cuenta.$('#validar_QR').removeClass('disabled');
																			contexto_cuenta.$('#validar_QR').attr('style', 'margin:10px');
																			contexto_cuenta.$('#btn_Cancelar').removeClass('disabled');
																			contexto_cuenta.$('#btn_Cancelar').attr('style', 'margin:10px');
																			contexto_cuenta.$('#rfcModal').hide();
																			//contexto_cuenta.render();
																		}
																	}
																}

																//Valida tipo de registro; (Cliente || Proveedor) && Origen != Seguros && Subtipo != Venta activo
																if ((contexto_cuenta.model.get('tipo_registro_cuenta_c') == '3' || contexto_cuenta.model.get('tipo_registro_cuenta_c') == '5') && contexto_cuenta.model.get('origen_cuenta_c') != '11' && contexto_cuenta.model.get('subtipo_registro_cuenta_c') != '11') {
																	if (cambioRazonSocial['cambioDirFiscal'] || cambioRazonSocial['cambioCuenta']) {
																		//Abre modal para indicar tipo de cambio
																		//Restablece valores de custom fieldQR
																		contexto_cuenta.$('#activar_camara').removeClass('disabled');
																		contexto_cuenta.$('#activar_camara').attr('style', '');
																		contexto_cuenta.$('#archivo_qr').removeClass('disabled');
																		contexto_cuenta.$('#archivo_qr').attr('style', '');
																		contexto_cuenta.$('#btnSubir').removeClass('disabled');
																		contexto_cuenta.$('#btnSubir').attr('style', 'margin:10px');
																		contexto_cuenta.$('#validar_QR').removeClass('disabled');
																		contexto_cuenta.$('#validar_QR').attr('style', 'margin:10px');
																		contexto_cuenta.$('#btn_Cancelar').removeClass('disabled');
																		contexto_cuenta.$('#btn_Cancelar').attr('style', 'margin:10px');
																		//contexto_cuenta.$('#rfcModal').hide();

																		//Solo se muestra modal cuando detecta cambios en Razón Social
																		if (cambioRazonSocial['cambioCuenta']) {
																			//Muestra modal cambios
																			$('#cambioRazonSocial').show();
																			if (!cambioRazonSocial['cambioDirFiscal'] && cambioRazonSocial['cambioCuenta']) {
																				$('.action1').show();
																				$('.action2').hide();
																				$('.action3').hide();
																			}
																			if (cambioRazonSocial['cambioDirFiscal'] && !cambioRazonSocial['cambioCuenta']) {
																				$('.action1').hide();
																				$('.action2').show();
																				$('.action3').hide();
																			}
																			if (cambioRazonSocial['cambioDirFiscal'] && cambioRazonSocial['cambioCuenta']) {
																				$('.action1').show();
																				$('.action2').show();
																				$('.action3').show();
																			}
																		}

																	}
																}
															})
														});
													},
												});
											}
										}
									})
								});
							} else {
								app.alert.show('errorCSF', {
									level: 'error',
									messages: 'No se pudo identificar una estructura válida para el documento y/o no cumple con la estructura oficial del SAT.\nPor lo tanto, no se pueden validar los datos del contribuyente con los del SAT',
									autoClose: true
								});

								$btnCIEC.prop('disabled', false);
								$btnCIEC.text('Actualizar Constancia');

								contexto_cuenta.$('#activar_camara').removeClass('disabled');
								contexto_cuenta.$('#activar_camara').attr('style', '');
								contexto_cuenta.$('#archivo_qr').removeClass('disabled');
								contexto_cuenta.$('#archivo_qr').attr('style', '');
								contexto_cuenta.$('#btnSubir').removeClass('disabled');
								contexto_cuenta.$('#btnSubir').attr('style', 'margin:10px');
								contexto_cuenta.$('#validar_QR').removeClass('disabled');
								contexto_cuenta.$('#validar_QR').attr('style', 'margin:10px');
								contexto_cuenta.$('#btn_Cancelar').removeClass('disabled');
								contexto_cuenta.$('#btn_Cancelar').attr('style', 'margin:10px');

							}
							} else {
								$btnCIEC.prop('disabled', false);
								$btnCIEC.text('Actualizar Constancia');

								app.alert.show('errorCSF', {
									level: 'error',
									messages: 'No se pudo identificar una estructura válida para el documento y/o no cumple con la estructura oficial del SAT.\nPor lo tanto, no se pueden validar los datos del contribuyente con los del SAT',
									autoClose: true
								});

								contexto_cuenta.$('#activar_camara').removeClass('disabled');
								contexto_cuenta.$('#activar_camara').attr('style', '');
								contexto_cuenta.$('#archivo_qr').removeClass('disabled');
								contexto_cuenta.$('#archivo_qr').attr('style', '');
								contexto_cuenta.$('#btnSubir').removeClass('disabled');
								contexto_cuenta.$('#btnSubir').attr('style', 'margin:10px');
								contexto_cuenta.$('#validar_QR').removeClass('disabled');
								contexto_cuenta.$('#validar_QR').attr('style', 'margin:10px');
								contexto_cuenta.$('#btn_Cancelar').removeClass('disabled');
								contexto_cuenta.$('#btn_Cancelar').attr('style', 'margin:10px');

							}
						}),
						error: function (error) {
							$btnCIEC.prop('disabled', false);
							$btnCIEC.text('Actualizar Constancia');			
						}
					});
				}

			}
		}
		/*
		else{
			var elemento = this.$('#canvas')[0];
			imgn.src = elemento.toDataURL();
		}
		*/

		//imgn.onload
	},

	FileintegraCSF: function (idRegistro, RFC, base64, fechaEmision) {
		app.alert.show('sendCSF', {
			level: 'process',
			title: 'Enviando Constancia de Situación Fiscal...',
		});

		var b64 = base64.split(";base64,");
		var params = {
			"idCliente": idRegistro,
			//"idCliente": '88cfd57e-2277-11ea-98a4-00155d96730d',
			"rfc": RFC,
			"base64": b64[1],
			"vigencia": fechaEmision
		};

		var url = app.api.buildURL('FileIntegracionesCSF', null, null,);
		app.api.call('create', url, params, {
			success: function (response) {
				app.alert.dismiss('sendCSF');
				app.alert.show('info_csf', {
					level: 'info',
					autoClose: false,
					messages: '<br>-' + response.robina + '<br><br>-' + response.quantico_validator + '<br><br>-' + response.alfresco
				});

			}
		});

	},

	integraCSF: function (idRegistro, RFC, fechaEmision) {
		app.alert.show('sendCSF', {
			level: 'process',
			title: 'Enviando Constancia de Situación Fiscal...',
		});

		var params = {
			"idCliente": idRegistro,
			//"idCliente": '88cfd57e-2277-11ea-98a4-00155d96730d',
			"rfc": RFC,
			"base64": "",
			"vigencia": fechaEmision
		};

		var url = app.api.buildURL('IntegracionesCSF', null, null,);
		app.api.call('create', url, params, {
			success: function (response) {
				app.alert.dismiss('sendCSF');
				app.alert.show('info_csf', {
					level: 'info',
					autoClose: false,
					messages: '<br>-' + response.robina + '<br><br>-' + response.quantico_validator + '<br><br>-' + response.alfresco
				});

			}
		});

	},

	evaluaCambioRegimenCapital: function (name_actual, name_nuevo, regimen_capital) {
		name_nuevo = name_nuevo.replace(regimen_capital, "");
		var cambio_nombre = false;
		if (name_actual.startsWith(name_nuevo)) {
			var regimen_capital_actual = name_actual.replace(name_nuevo, "");
			//Elimina los espacios, puntos y comas
			var regimen_capital_actual_clean = regimen_capital_actual.replace(/[ ,.]/g, "");
			var regimen_capital_nuevo_clean = regimen_capital.replace(/[ ,.]/g, "");

			if (regimen_capital_actual_clean != regimen_capital_nuevo_clean) {
				cambio_nombre = true;
			} else {
				cambio_nombre = false;
			}
		} else {
			cambio_nombre = true;
		}

		return cambio_nombre;

	},

	formatDate: function (fecha) {

		var fecha_formateada = "";

		if (fecha !== null) {
			var fecha_parts = fecha.split('T');
			var fecha_unformat = fecha_parts[0].split('-');

			fecha_formateada = fecha_unformat[2] + "/" + fecha_unformat[1] + "/" + fecha_unformat[0];
		}

		return fecha_formateada;

	},

	cargarArchivo: function () {
		contexto_cuenta.$('#carga').show();
		contexto_cuenta.$('#div_video').hide();
		contexto_cuenta.picturecam = false;
	},

	btn_rfc_qr: function () {
		contexto_cuenta.$('#rfcModal').show();
	},

	cancelar: function () {
		contexto_cuenta.$('#rfcModal').hide();
		contexto_cuenta.picturecam = false;
		contexto_cuenta.$('#rfcModal').hide();
	},

	_limpiezaDatos: function (cadena) {

		cadena = cadena.trim().toLowerCase();
		cadena = cadena.split(" ").join("");
		cadena = cadena.replace(".", "");
		cadena = cadena.replace("-", "");
		cadena = cadena.replace("_", "");
		cadena = cadena.replace(",", "");
		cadena = cadena.replace(";", "");
		cadena = cadena.replace(":", "");
		cadena = cadena.replace("#", "");
		cadena = cadena.replace("$", "");
		cadena = cadena.replace("%", "");
		cadena = cadena.replace("&", "");
		cadena = cadena.replace("\d", "");
		cadena = cadena.replace("\r", "");
		cadena = cadena.replace("\t", "");
		cadena = cadena.replace("\n", "");
		return cadena;
	},

	bindDataChange: function () {
		this.model.on('change:' + this.name, function () {
			if (this.action !== 'edit') {
				this.render();
			}
		}, this);
	},
	_buildEmailFieldHtml: function (email) {
		var editEmailFieldTemplate = app.template.getField('email', 'edit-email-field'), emails = this.model.get('email'), index = _.indexOf(emails, email);
		return editEmailFieldTemplate({
			max_length: this.def.len,
			index: index === -1 ? emails.length - 1 : index,
			email_address: email.email_address,
			primary_address: email.primary_address,
			opt_out: email.opt_out,
			invalid_email: email.invalid_email
		});
	},

	closeModalRazonSocial: function () {
		//Restablece valores de custom fieldQR
		contexto_cuenta.$('#activar_camara').removeClass('disabled');
		contexto_cuenta.$('#activar_camara').attr('style', '');
		contexto_cuenta.$('#archivo_qr').removeClass('disabled');
		contexto_cuenta.$('#archivo_qr').attr('style', '');
		contexto_cuenta.$('#btnSubir').removeClass('disabled');
		contexto_cuenta.$('#btnSubir').attr('style', 'margin:10px');
		contexto_cuenta.$('#validar_QR').removeClass('disabled');
		contexto_cuenta.$('#validar_QR').attr('style', 'margin:10px');
		contexto_cuenta.$('#btn_Cancelar').removeClass('disabled');
		contexto_cuenta.$('#btn_Cancelar').attr('style', 'margin:10px');
		//Cierra modal
		$('#cambioRazonSocial').hide();
		//Aplica rollback
		if (cambioRazonSocial['cambioCuenta']) {
			if (contexto_cuenta.model.get('tipodepersona_c') == "Persona Moral") {
				contexto_cuenta.model.set('razonsocial_c', cambioRazonSocial['Cuenta']['razonsocial_c']);
				contexto_cuenta.model.set('nombre_comercial_c', cambioRazonSocial['Cuenta']['razonsocial_c']);
			} else {
				contexto_cuenta.model.set('primernombre_c', cambioRazonSocial['Cuenta']['primernombre_c']);
				contexto_cuenta.model.set('apellidopaterno_c', cambioRazonSocial['Cuenta']['apellidopaterno_c']);
				contexto_cuenta.model.set('apellidomaterno_c', cambioRazonSocial['Cuenta']['apellidomaterno_c']);
			}
		}
		cont_dir.oDirecciones.direccion = cambioRazonSocial['Direccion'];
		cont_dir.render();
		App.alert.show('closemodalRazonSocial', {
			level: 'info',
			messages: 'Se ha descartado la solicitud de cambios',
			autoClose: true
		});

	},

	modalAction1: function () {
		//Cierra modal
		$('#cambioRazonSocial').hide();
		$('#rfcModal').hide();
		//cont_dir.oDirecciones.direccion = cambioRazonSocial['Direccion'];
		//cont_dir.render();
		var model = App.data.createBean('Cases');
		model.set('account_id', contexto_cuenta.model.get('id'));
		model.set('account_name', contexto_cuenta.model.get('name'));
		model.set('producto_c', 'SC6'); //Seguimiento comercial
		model.set('type', '15'); //Cambio nombre
		model.set('area_interna_c', 'Credito');  //Crédito
		//Fix: Establece bandera para que asignación de casos únicamente se aplique cuando hay un cambio de dirección fiscal o razón social
		model.set('valida_cambio_fiscal_c', 1);
		if (App.user.attributes.cac_c) {
			var asignadoId = infoUser['id'] != '' ? infoUser['id'] : App.user.id;
			var asignadoName = infoUser['name'] != '' ? infoUser['name'] : App.user.attributes.full_name;
			model.set('assigned_user_id', asignadoId);
			model.set('assigned_user_name', asignadoName);
			model.set('area_interna_c', '');
		}
		app.drawer.open({
			layout: 'create',
			context: {
				create: true,
				module: 'Cases',
				model: model
			},
		},
			function (variable) {
				if (variable.attributes.model.id == undefined) {
					//Aplica rollback
					if (cambioRazonSocial['cambioCuenta']) {
						if (contexto_cuenta.model.get('tipodepersona_c') == "Persona Moral") {
							contexto_cuenta.model.set('razonsocial_c', cambioRazonSocial['Cuenta']['razonsocial_c']);
							contexto_cuenta.model.set('nombre_comercial_c', cambioRazonSocial['Cuenta']['razonsocial_c']);
						} else {
							contexto_cuenta.model.set('primernombre_c', cambioRazonSocial['Cuenta']['primernombre_c']);
							contexto_cuenta.model.set('apellidopaterno_c', cambioRazonSocial['Cuenta']['apellidopaterno_c']);
							contexto_cuenta.model.set('apellidomaterno_c', cambioRazonSocial['Cuenta']['apellidomaterno_c']);
						}
						cont_dir.oDirecciones.direccion = cambioRazonSocial['Direccion'];
						cont_dir.render();
					}
					App.alert.show('cancelCase1', {
						level: 'info',
						messages: 'Se ha descartado la solicitud de cambios',
						autoClose: true
					});
				} else {
					contexto_cuenta.model.set('omitir_caso', 1);
					App.alert.show('saveaction1', {
						level: 'info',
						messages: 'Guarde la cuenta para continuar con el proceso de solicitud de cambios',
						autoClose: false
					});
				}
			}
		);
	},

	modalAction2: function () {
		//Cierra modal
		$('#cambioRazonSocial').hide();
		$('#rfcModal').hide();
		if (contexto_cuenta.model.get('tipodepersona_c') == "Persona Moral") {
			contexto_cuenta.model.set('razonsocial_c', cambioRazonSocial['Cuenta']['razonsocial_c']);
			contexto_cuenta.model.set('nombre_comercial_c', cambioRazonSocial['Cuenta']['razonsocial_c']);
		} else {
			contexto_cuenta.model.set('primernombre_c', cambioRazonSocial['Cuenta']['primernombre_c']);
			contexto_cuenta.model.set('apellidopaterno_c', cambioRazonSocial['Cuenta']['apellidopaterno_c']);
			contexto_cuenta.model.set('apellidomaterno_c', cambioRazonSocial['Cuenta']['apellidomaterno_c']);
		}
		var model = App.data.createBean('Cases');
		model.set('account_id', contexto_cuenta.model.get('id'));
		model.set('account_name', contexto_cuenta.model.get('name'));
		model.set('producto_c', 'SC6'); //Seguimiento comercial
		model.set('type', '16'); //Cambio dirección
		model.set('area_interna_c', 'Credito');  //Crédito
		//Fix: Establece bandera para que asignación de casos únicamente se aplique cuando hay un cambio de dirección fiscal o razón social
		model.set('valida_cambio_fiscal_c', 1);
		if (App.user.attributes.cac_c) {
			var asignadoId = infoUser['id'] != '' ? infoUser['id'] : App.user.id;
			var asignadoName = infoUser['name'] != '' ? infoUser['name'] : App.user.attributes.full_name;
			model.set('assigned_user_id', asignadoId);
			model.set('assigned_user_name', asignadoName);
			model.set('area_interna_c', '');
		}
		app.drawer.open({
			layout: 'create',
			context: {
				create: true,
				module: 'Cases',
				model: model
			},
		},
			function (variable) {
				if (variable.attributes.model.id == undefined) {
					//Aplica rollback
					if (cambioRazonSocial['cambioDirFiscal']) {
						cont_dir.oDirecciones.direccion = cambioRazonSocial['Direccion'];
						cont_dir.render();
					}
					App.alert.show('cancelCase2', {
						level: 'info',
						messages: 'Se ha descartado la solicitud de cambios',
						autoClose: true
					});
				} else {
					contexto_cuenta.model.set('omitir_caso', 1);
					App.alert.show('saveaction2', {
						level: 'info',
						messages: 'Guarde la cuenta para continuar con el proceso de solicitud de cambios',
						autoClose: false
					});
				}
			}
		);
	},

	modalAction3: function () {
		//Cierra modal
		$('#cambioRazonSocial').hide();
		$('#rfcModal').hide();
		var model = App.data.createBean('Cases');
		model.set('account_id', contexto_cuenta.model.get('id'));
		model.set('account_name', contexto_cuenta.model.get('name'));
		model.set('producto_c', 'SC6'); //Seguimiento comercial
		model.set('type', '17'); //Cambio nombre y dirección
		model.set('area_interna_c', 'Credito');  //Crédito
		//Fix: Establece bandera para que asignación de casos únicamente se aplique cuando hay un cambio de dirección fiscal o razón social
		model.set('valida_cambio_fiscal_c', 1);
		if (App.user.attributes.cac_c) {
			var asignadoId = infoUser['id'] != '' ? infoUser['id'] : App.user.id;
			var asignadoName = infoUser['name'] != '' ? infoUser['name'] : App.user.attributes.full_name;
			model.set('assigned_user_id', asignadoId);
			model.set('assigned_user_name', asignadoName);
			model.set('area_interna_c', '');
		}
		app.drawer.open({
			layout: 'create',
			context: {
				create: true,
				module: 'Cases',
				model: model
			},
		},
			function (variable) {
				if (variable.attributes.model.id == undefined) {
					//Aplica rollback
					if (cambioRazonSocial['cambioCuenta']) {
						if (contexto_cuenta.model.get('tipodepersona_c') == "Persona Moral") {
							contexto_cuenta.model.set('razonsocial_c', cambioRazonSocial['Cuenta']['razonsocial_c']);
							contexto_cuenta.model.set('nombre_comercial_c', cambioRazonSocial['Cuenta']['razonsocial_c']);
						} else {
							contexto_cuenta.model.set('primernombre_c', cambioRazonSocial['Cuenta']['primernombre_c']);
							contexto_cuenta.model.set('apellidopaterno_c', cambioRazonSocial['Cuenta']['apellidopaterno_c']);
							contexto_cuenta.model.set('apellidomaterno_c', cambioRazonSocial['Cuenta']['apellidomaterno_c']);
						}
					}
					cont_dir.oDirecciones.direccion = cambioRazonSocial['Direccion'];
					cont_dir.render();
					App.alert.show('cancelCase3', {
						level: 'info',
						messages: 'Se ha descartado la solicitud de cambios',
						autoClose: true
					});
				} else {
					contexto_cuenta.model.set('omitir_caso', 1);
					App.alert.show('saveaction3', {
						level: 'info',
						messages: 'Guarde la cuenta para continuar con el proceso de solicitud de cambios',
						autoClose: false
					});
				}
			}
		);
	},

	getUserInfo: function () {
		if (App.user.attributes.cac_c) {
			try {
				app.api.call("read", app.api.buildURL("getAsignadoCaso/" + contexto_cuenta.model.attributes.id), null, {
					success: _.bind(function (data) {
						if (data) {
							infoUser['id'] = data['id'];
							infoUser['name'] = data['name'];
						}
					}, this),
					error: _.bind(function (error) {
						//Muestra error
						infoUser['id'] = '';
						infoUser['name'] = '';
					}, this),
				});
			} catch (e) {
				infoUser['id'] = '';
				infoUser['name'] = '';
			}
		}
	},

	registroProcesoRobinaAPI: function(RFC, fechaEmision , ticket , id_cuenta) {

		app.alert.show('registro_pr', {
			level: 'process',
			title: 'Registrando Referencia Robina...',
		});

		var params = {
			"id_cuenta": id_cuenta,
			"rfc": RFC,
			"ticket": ticket,
			"estatus_procesado": "Sin procesar",
			"fecha_emision": fechaEmision
		};

		var url = app.api.buildURL('procesoRobinaApi', null, null,);
		app.api.call('create', url, params, {
			success: function (response) {
				app.alert.dismiss('registro_pr');
				app.alert.show('info_pr', {
					level: 'info',
					autoClose: false,
					messages: 'Se esta procesando la descarga de los documentos SAT, por favor espera unos minutos para poder descargarlos'
				});
			},
			error: function (error) {
				app.alert.dismiss('registro_pr');
				app.alert.show('error_pr', {
					level: 'error',
					autoClose: false,
					messages: 'Ocurrió un error al registrar el proceso robina.'
				});
			}
		});
	},
})
