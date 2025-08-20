/**
 * Created by salvadorlopez salvador.lopez@tactos.com.mx
 */
({
  events: {
    "click #btn_Cuentas": "buscarCuentasNoContactar",
    "click #next_offset": "nextOffset",
    "click #previous_offset": "previousOffset",
    "change .selected": "selectedCheckbox",
    "click #btn_no_contactar": "btnNoContactar",
    "click #btn_read_csv": "procesarCSV",
    "change #razon": "buscaMotivo",
    "change #motivo": "buscaValida",
    "click .ConsultarEstado": "consultarEstado",
    "click #btn-Cancelar-View": "cancelConsultarEstado",
    "click .ConsultarAudit": "consultarAudit",
    "click #btn-close-audit": "cancelConsultarAudit",
  },

  ids_cuentas: [],
  registroBloqueo: [],
  audit_historicos: [],

  initialize: function (options) {
    this._super("initialize", [options]);
    this.tipo_cuenta = app.lang.getAppListStrings("tipo_registro_cuenta_no_contactar_list");
    this.condicion_list = app.lang.getAppListStrings("condicion_cliente_list");
    delete this.tipo_cuenta[1];
    this.loadView = false;
    if (
      app.user.attributes.tct_no_contactar_chk_c == "1" ||
      app.user.attributes.bloqueo_credito_c == "1" ||
      app.user.attributes.bloqueo_cumple_c == "1"
    ) {
      this.loadView = true;
    } else {
      var route = app.router.buildRoute(this.module, null, "");
      app.router.navigate(route, { trigger: true });
    }
    this.selected = "";
    this.ids_cuentas = [];
    this.Parame = {
      condicion: "",
      razon: "",
      motivo: "",
      detalle: "",
      ingesta: "",
      valida: "",
    };
  },

  _render: function () {
    this._super("_render");
    var tipos_cuenta = [];
    this.$("#tipo_de_cuenta").select2({
      width: "450px",
      closeOnSelect: false,
      containerCssClass: "select2-choices-pills-close",
    });
    for (var key in this.tipo_cuenta) {
      if (this.tipo_cuenta.hasOwnProperty(key)) {
        tipos_cuenta.push(key);
      }
    }
    this.$("#tipo_de_cuenta").select2("val", tipos_cuenta);
  },

  condiciones: function () {
    if (
      app.user.attributes.tct_no_contactar_chk_c == "1" ||
      app.user.attributes.bloqueo_credito_c == "1" ||
      app.user.attributes.bloqueo_cumple_c == "1"
    ) {
      if (app.user.attributes.tct_no_contactar_chk_c == "1") {
        this.condicion = 1;
        this.bloqueo_credito_c = 1;
        this.bloqueo_cumple_c = 1;
      }
      if (app.user.attributes.bloqueo_credito_c == "1") {
        this.condicion = 2;
        this.tct_no_contactar_chk_c = 1;
        this.bloqueo_cumple_c = 1;
      }
      if (app.user.attributes.bloqueo_cumple_c == "1") {
        this.condicion = 3;
        this.tct_no_contactar_chk_c = 1;
        this.bloqueo_credito_c = 1;
      }
      //Busca valores
      var strUrl = "tct4_Condiciones?filter[][condicion]=" + this.condicion;
      app.api.call("GET", app.api.buildURL(strUrl), null, {
        success: _.bind(function (data) {
          if (data.records.length > 0) {
            this.data = data;
            document.getElementById("condicion").value = this.condicion;
            var listaRazon = [];
            var indice = 1;
            for (var i = 0; i < data.records.length; i++) {
              listaRazon[data.records[i].razon] =
                App.lang.getAppListStrings("razon_list")[data.records[i].razon];
            }
            if (data.records.length > 1) {
              document.getElementById("razon").options[0] = new Option("", "");
              document.getElementById("razon").value = "";
            }
            Object.entries(listaRazon).forEach(([key, value]) => {
              document.getElementById("razon").options[indice] = new Option(
                value,
                key
              );
              indice++;
            });

            document.getElementById("ingesta").options[0] = new Option(
              app.user.attributes.full_name,
              app.user.attributes.id
            );
            if (data.records.length == 1) this.buscaMotivo();
          }
        }, this),
      });
    }
  },

  buscarCuentasNoContactar: function () {
    //Inicializar arreglo de cuentas cada que se busca por un filtro, para evitar actualizar cuentas que anteriormente se seleccionaron
    this.ids_cuentas = [];
    var assigneUsr = this.model.get("users_accounts_1users_ida");
    //Condición para controlar la búsqueda cuando no se ha seleccionado Promotor, esto sucede cuando se da click en el icono con el tache
    //dentro del campo Asesor Actual con formato select2
    if (assigneUsr == "") {
      assigneUsr = undefined;
    }
    var tipos_seleccionados = this.$(".tipo_cuenta").select2("val");
    if (
      (_.isEmpty(assigneUsr) ||
        _.isUndefined(assigneUsr) ||
        assigneUsr == "") &&
      (tipos_seleccionados.includes("Prospecto") ||
        tipos_seleccionados.includes("Cliente") ||
        tipos_seleccionados.includes("Lead"))
    ) {
      var alertOptions = {
        title: "Por favor, seleccione un Asesor",
        level: "error",
      };
      app.alert.show("validation", alertOptions);
      return;
    }
    var from_set = $("#offset_value").attr("from_set");
    var to_set = $("#offset_value").attr("to_set");
    var current_set = $("#offset_value").html();
    var from_set_num = parseInt(from_set);
    var filtroCliente = $("#filtroCliente").val();
    var filtroTipoCuenta = $("#tipo_de_cuenta").select2("val");
    if (_.isEmpty($("#tipo_de_cuenta").select2("val"))) {
      var alertOptions = {
        title: "Por favor, seleccionar al menos un Tipo de Cuenta",
        level: "error",
      };
      app.alert.show("validation", alertOptions);
      return;
    }
    if (isNaN(from_set_num)) {
      from_set_num = 0;
    }
    assigneUsr +=
      "?from=" +
      from_set_num +
      "&cliente=" +
      filtroCliente +
      "&tipos_cuenta=" +
      filtroTipoCuenta.toString();
    //"c57e811e-b81a-cde4-d6b4-5626c9961772?PRODUCTO=LEASING?0?&tipos_cuenta=Lead,Prospecto,Cliente,Persona,Proveedor"
    if (
      !_.isEmpty(assigneUsr) &&
      !_.isUndefined(assigneUsr) &&
      assigneUsr != ""
    ) {
      this.seleccionados = [];
      $("#successful").hide();
      $("#processing").show();
      $("#btn_Cuentas").attr("disabled", true);
      app.api.call(
        "read",
        app.api.buildURL("CuentasNoContactar/" + assigneUsr, null, null, {}),
        null,
        {
          success: _.bind(function (data) {
            if (data.total <= 0) {
              var nombre_usuario = $('input[name="users_accounts_1_name"]')
                .parent()
                .find("div.ellipsis_inline")
                .attr("title");
              var alertOptions = {
                title:
                  "No se encontraron Cuentas de los tipos seleccionados para el usuario <b>" +
                  nombre_usuario +
                  "</b>",
                level: "error",
              };
              app.alert.show("validation", alertOptions);
              $("#processing").hide();
              $("#btn_Cuentas").attr("disabled", false);
              return;
            }
            $("#processing").hide();
            $("#btn_Cuentas").attr("disabled", false);
            this.cuentas = typeof data == "string" ? null : data.cuentas;
            /*Bloque de código únicamente utilizado para mostrar correctamente el valor de los checkbox en archivo hbs, basado directamente en la consulta a la bd*/
            if (this.cuentas.length > 0) {
              for (var i = 0; i < this.cuentas.length; i++) {
                if (this.cuentas[i].tct_no_contactar_chk_c == 0)
                  this.cuentas[i].tct_no_contactar_chk_c = null;
                if (this.cuentas[i].bloqueo_credito_c == 0)
                  this.cuentas[i].bloqueo_credito_c = null;
                if (this.cuentas[i].bloqueo_cumple_c == 0)
                  this.cuentas[i].bloqueo_cumple_c = null;
              }
            }
            this.total = data.total;
            this.total_cuentas = data.total_cuentas;
            //Se obtiene valor de Tipo de Cuenta, para que persista al aplicar render
            var valores = $("#tipo_de_cuenta").select2("val");
            this.condiciones();
            this.render();
            $("#tipo_de_cuenta").select2("val", valores);
            if (to_set > this.total) {
              to_set = this.total;
            } else {
              to_set = from_set_num + data.total_cuentas;
            }
            current_set =
              parseInt(from_set) + 1 + " a " + to_set + " de " + this.total;
            if (_.isEmpty(from_set)) {
              from_set = 0;
              to_set = 20;
              if (to_set > this.total) {
                to_set = this.total;
              }
              current_set =
                parseInt(from_set) + 1 + " a " + to_set + " de " + this.total;
            }
            $("#offset_value").html(current_set);
            $("#offset_value").attr("from_set", from_set);
            $("#offset_value").attr("to_set", to_set);
            $("#filtroCliente").val(filtroCliente);
            document.getElementById("condicion").disabled = true;
            document.getElementById("ingesta").disabled = true;
          }, this),
        }
      );
    } else {
      var alertOptions = {
        title: "Por favor, seleccione un asesor",
        level: "error",
      };
      app.alert.show("validation", alertOptions);
    }
  },

  nextOffset: function () {
    var current_set = $("#offset_value").html();
    var from_set = $("#offset_value").attr("from_set");
    var next_from_set = parseInt(from_set) + 20;
    var to_set = $("#offset_value").attr("to_set");
    var next_to_set = parseInt(to_set) + 20;
    if (next_to_set > this.total) {
      next_to_set = this.total;
      if (from_set > 0) {
        next_from_set = from_set;
      } else {
        next_from_set = next_from_set;
      }
    }
    $("#offset_value").html(current_set);
    $("#offset_value").attr("from_set", next_from_set);
    $("#offset_value").attr("to_set", next_to_set);
    this.buscarCuentasNoContactar();
  },

  previousOffset: function () {
    var current_set = $("#offset_value").html();
    var from_set = $("#offset_value").attr("from_set");
    var next_from_set = parseInt(from_set) - 20;
    var to_set = $("#offset_value").attr("to_set");
    var next_to_set = parseInt(to_set) - 20;
    if (next_from_set < 0) {
      next_from_set = 0;
      next_to_set = 20;
    }
    $("#offset_value").html(current_set);
    $("#offset_value").attr("from_set", next_from_set);
    $("#offset_value").attr("to_set", next_to_set);
    this.buscarCuentasNoContactar();
  },

  buscaMotivo: function (e) {
    var contador = 0;
    document.getElementById("motivo").options.length = 0;
    for (var i = 0; i < this.data.records.length; i++) {
      if ($("#razon").val() == this.data.records[i].razon) {
        document.getElementById("motivo").options[contador] = new Option(
          app.lang.getAppListStrings("motivo_bloqueo_list")[
            this.data.records[i].motivo
          ],
          this.data.records[i].motivo
        );
        this.detalle = this.data.records[i].detalle;
        contador = contador + 1;
      }
    }
    if (contador > 1) document.getElementById("motivo").value = "";
    document.getElementById("valida").options.length = 0;
    for (var i = 0; i < this.data.records.length; i++) {
      if ($("#razon").val() == this.data.records[i].razon) {
        this.detalle = this.data.records[i].detalle;
        var paramsUser = {
          filter: [
            {
              status: {
                $in: ["Active"],
              },
            },
          ],
        };
        app.api.call(
          "read",
          app.api.buildURL(
            "Teams/" + this.data.records[i].user_id_c + "/link/users",
            null,
            null,
            paramsUser
          ),
          null,
          {
            success: _.bind(function (data1) {
              if (data1.records) {
                for (var j = 0; j < data1.records.length; j++) {
                  //Se agrega una opción vacía para que el campo de Responsable de Validación parezca como que no se llena
                  if (j == 0) {
                    document.getElementById("valida").options[j] = new Option(
                      "",
                      ""
                    );
                  }
                  document.getElementById("valida").options[j + 1] = new Option(
                    data1.records[j].full_name,
                    data1.records[j].id
                  );
                }
              }
            }, this),
          }
        );
      }
    }
  },

  buscaValida: function (e) {
    document.getElementById("valida").options.length = 0;
    for (var i = 0; i < this.data.records.length; i++) {
      if ($("#motivo").val() == this.data.records[i].motivo) {
        this.detalle = this.data.records[i].detalle;
        var paramsUser = {
          filter: [
            {
              status: {
                $in: ["Active"],
              },
            },
          ],
        };
        app.api.call(
          "read",
          app.api.buildURL(
            "Teams/" + this.data.records[i].user_id_c + "/link/users",
            null,
            null,
            paramsUser
          ),
          null,
          {
            success: _.bind(function (data1) {
              if (data1.records) {
                for (var j = 0; j < data1.records.length; j++) {
                  document.getElementById("valida").options[j] = new Option(
                    data1.records[j].full_name,
                    data1.records[j].id
                  );
                }
              }
            }, this),
          }
        );
      }
    }
  },

  selectedCheckbox: function (e) {
    var $input = this.$(e.currentTarget);
    this.selected = $($input).attr("name");
    var id_cuenta = $(e.currentTarget).val();
    var indexFind = this.ids_cuentas.indexOf(id_cuenta);
    //Busca datos de la cuenta seleccionada
    app.api.call(
      "read",
      app.api.buildURL("tct02_Resumen/" + id_cuenta, null, null, {}),
      null,
      {
        success: _.bind(function (data) {
          var limpia = false;
          if (this.selected == "selected1") {
            if (data.condicion_cliente_c) {
              document.getElementById("condicion").value =
                data.condicion_cliente_c;
              document.getElementById("razon").options[0] = new Option(
                app.lang.getAppListStrings("razon_list")[data.razon_c],
                data.razon_c
              );
              document.getElementById("razon").value = data.razon_c;
              document.getElementById("razon").disabled = true;
              document.getElementById("motivo").options[0] = new Option(
                app.lang.getAppListStrings("motivo_bloqueo_list")[
                  data.motivo_c
                ],
                data.motivo_c
              );
              document.getElementById("motivo").value = data.motivo_c;
              document.getElementById("motivo").disabled = true;
              document.getElementById("detalle").value = data.detalle_c;
              document.getElementById("detalle").disabled = true;
              app.api.call(
                "read",
                app.api.buildURL("Users/" + data.user_id_c, null, null, {}),
                null,
                {
                  success: _.bind(function (data1) {
                    if (data1.full_name) {
                      document.getElementById("ingesta").options[0] =
                        new Option(data1.full_name, data1.id);
                      document.getElementById("ingesta").value = data1.id;
                    }
                  }, this),
                }
              );
              var paramsUser = {
                filter: [
                  {
                    status: {
                      $in: ["Active"],
                    },
                  },
                ],
              };
              app.api.call(
                "read",
                app.api.buildURL(
                  "Teams/" + this.data.records[0].user_id_c + "/link/users",
                  null,
                  null,
                  paramsUser
                ),
                null,
                {
                  success: _.bind(function (data1) {
                    if (data1.records) {
                      for (var j = 0; j < data1.records.length; j++) {
                        //Se agrega una opción vacía para que el campo de Responsable de Validación parezca como que no se llena
                        if (j == 0) {
                          document.getElementById("valida").options[j] =
                            new Option("", "");
                        }
                        document.getElementById("valida").options[j + 1] =
                          new Option(
                            data1.records[j].full_name,
                            data1.records[j].id
                          );
                      }
                    }
                  }, this),
                }
              );
            } else {
              limpia = true;
            }
          }
          if (this.selected == "selected2") {
            if (data.condicion2_c) {
              document.getElementById("condicion").value = data.condicion2_c;
              document.getElementById("razon").options[0] = new Option(
                app.lang.getAppListStrings("razon_list")[data.razon2_c],
                data.razon2_c
              );
              document.getElementById("razon").value = data.razon2_c;
              document.getElementById("razon").disabled = true;
              document.getElementById("motivo").options[0] = new Option(
                app.lang.getAppListStrings("motivo_bloqueo_list")[
                  data.motivo2_c
                ],
                data.motivo2_c
              );
              document.getElementById("motivo").value = data.motivo2_c;
              document.getElementById("motivo").disabled = true;
              document.getElementById("detalle").value = data.detalle2_c;
              document.getElementById("detalle").disabled = true;

              var paramsUser = {
                filter: [
                  {
                    status: {
                      $in: ["Active"],
                    },
                  },
                ],
              };
              app.api.call(
                "read",
                app.api.buildURL(
                  "Teams/" + this.data.records[0].user_id_c + "/link/users",
                  null,
                  null,
                  paramsUser
                ),
                null,
                {
                  success: _.bind(function (data1) {
                    if (data1.records) {
                      for (var j = 0; j < data1.records.length; j++) {
                        //Se agrega una opción vacía para que el campo de Responsable de Validación parezca como que no se llena
                        if (j == 0) {
                          document.getElementById("valida").options[j] =
                            new Option("", "");
                        }
                        document.getElementById("valida").options[j + 1] =
                          new Option(
                            data1.records[j].full_name,
                            data1.records[j].id
                          );
                      }
                    }
                  }, this),
                }
              );

              app.api.call(
                "read",
                app.api.buildURL("Users/" + data.user_id2_c, null, null, {}),
                null,
                {
                  success: _.bind(function (data3) {
                    if (data3.full_name) {
                      document.getElementById("ingesta").options[0] =
                        new Option(data3.full_name, data3.id);
                      document.getElementById("ingesta").value = data3.id;
                    }
                  }, this),
                }
              );
            } else {
              limpia = true;
            }
          }
          if (this.selected == "selected3") {
            if (data.condicion3_c) {
              document.getElementById("condicion").value = data.condicion3_c;
              document.getElementById("razon").options[0] = new Option(
                app.lang.getAppListStrings("razon_list")[data.razon3_c],
                data.razon3_c
              );
              document.getElementById("razon").value = data.razon3_c;
              document.getElementById("razon").disabled = true;
              document.getElementById("motivo").options[0] = new Option(
                app.lang.getAppListStrings("motivo_bloqueo_list")[
                  data.motivo3_c
                ],
                data.motivo3_c
              );
              document.getElementById("motivo").value = data.motivo3_c;
              document.getElementById("motivo").disabled = true;
              document.getElementById("detalle").value = data.detalle3_c;
              document.getElementById("detalle").disabled = true;
              app.api.call(
                "read",
                app.api.buildURL("Users/" + data.user_id4_c, null, null, {}),
                null,
                {
                  success: _.bind(function (data5) {
                    if (data5.full_name) {
                      document.getElementById("ingesta").options[0] =
                        new Option(data5.full_name, data5.id);
                      document.getElementById("ingesta").value = data5.id;
                    }
                  }, this),
                }
              );

              var paramsUser = {
                filter: [
                  {
                    status: {
                      $in: ["Active"],
                    },
                  },
                ],
              };
              app.api.call(
                "read",
                app.api.buildURL(
                  "Teams/" + this.data.records[0].user_id_c + "/link/users",
                  null,
                  null,
                  paramsUser
                ),
                null,
                {
                  success: _.bind(function (data1) {
                    if (data1.records) {
                      for (var j = 0; j < data1.records.length; j++) {
                        //Se agrega una opción vacía para que el campo de Responsable de Validación parezca como que no se llena
                        if (j == 0) {
                          document.getElementById("valida").options[j] =
                            new Option("", "");
                        }
                        document.getElementById("valida").options[j + 1] =
                          new Option(
                            data1.records[j].full_name,
                            data1.records[j].id
                          );
                      }
                    }
                  }, this),
                }
              );
            } else {
              limpia = true;
            }
          }
          if (limpia) {
            document.getElementById("condicion").value = this.condicion;
            document.getElementById("razon").options.length = 0;
            var listaRazon = [];
            var indice = 1;
            for (var i = 0; i < this.data.records.length; i++) {
              listaRazon[this.data.records[i].razon] =
                App.lang.getAppListStrings("razon_list")[
                  this.data.records[i].razon
                ];
            }
            if (this.data.records.length > 1) {
              document.getElementById("razon").options[0] = new Option("", "");
              document.getElementById("razon").value = "";
            }
            Object.entries(listaRazon).forEach(([key, value]) => {
              document.getElementById("razon").options[indice] = new Option(
                value,
                key
              );
              indice++;
            });
            document.getElementById("razon").disabled = false;
            document.getElementById("ingesta").options.length = 0;
            document.getElementById("ingesta").options[0] = new Option(
              app.user.attributes.full_name,
              app.user.attributes.id
            );
            document.getElementById("motivo").options.length = 0;
            document.getElementById("valida").options.length = 0;
            if (this.data.records.length == 1) this.buscaMotivo();
            document.getElementById("detalle").value = "";
            document.getElementById("motivo").disabled = false;
            document.getElementById("detalle").disabled = false;
            //document.getElementById("valida").disabled=false;
          }
        }, this),
      }
    );
    //Antes de agregar al arreglo, comprobar que existe, en caso positivo, se elimina
    if (this.ids_cuentas.length > 0 && indexFind != -1) {
      this.ids_cuentas.splice(indexFind, 1);
    } else {
      this.ids_cuentas.push($(e.currentTarget).val());
    }
    if (this.ids_cuentas.length > 0) {
      $("#btn_no_contactar").removeAttr("disabled");
    } else {
      $("#btn_no_contactar").attr("disabled", true);
    }
  },

  btnNoContactar: function () {
    var contador = 0;
    var errorMsg = "";
    if ($("#razon").val() == "") {
      contador++;
      errorMsg += "<br><b>Razón</b>";
      $("#razon").css("border-color", "red");
    } else {
      $("#razon").css("border-color", "");
    }
    /*
        if($("#valida").val() == "" || $("#valida").val() == null) {
			contador++;
            errorMsg += '<br><b>Responsable Validación</b>';
            $('#valida').css('border-color', 'red');
        } else {
            $('#valida').css('border-color', '');
        }
        */
    if ($("#detalle").val() == "" && this.detalle) {
      contador++;
      errorMsg += "<br><b>Detalle</b>";
      $("#detalle").css("border-color", "red");
    } else {
      $("#detalle").css("border-color", "");
    }
    if (contador >= 1) {
      errorMsg =
        "Hace falta completar la siguiente información para poder continuar</b>:" +
        errorMsg;
      app.alert.show("error", {
        level: "error",
        autoClose: false,
        messages: errorMsg,
      });
    } else {
      $("#btn_no_contactar").attr("disabled", true);

      var valida_users = [];
      $("#valida option").each(function () {
        //console.log($(this).val()+" "+$(this).html());
        if ($(this).val() != "") {
          valida_users.push($(this).val());
        }
      });

      this.Parame = {
        condicion: $("#condicion").val(),
        razon: $("#razon").val(),
        motivo: $("#motivo").val(),
        detalle: $("#detalle").val(),
        ingesta: $("#ingesta").val(),
        valida: $("#valida").val(),
        valida_users: valida_users,
      };
      var Params = {
        cuentas: this.ids_cuentas,
        parame: this.Parame,
        selected: this.selected,
      };
      $("#successful").hide();
      $("#processingSave").show();
      var urlNoContactar = app.api.buildURL(
        "ActualizarCuentasNoContactar",
        "",
        {},
        {}
      );
      app.api.call(
        "create",
        urlNoContactar,
        { data: Params },
        {
          success: _.bind(function (data) {
            $("#processingSave").hide();
            this.render();
            $(".cuentasContainer").hide();
            $("#successful").show();
            $("#btn_no_contactar").removeAttr("disabled");
          }, this),
        }
      );
    }
  },

  consultarEstado: function (e) {
    app.alert.show("uni2-disp-estado", {
      level: "process",
      closeable: false,
      messages: app.lang.get("LBL_LOADING"),
    });
    var id_cuenta = e.currentTarget.getAttribute("data-id");
    app.api.call(
      "read",
      app.api.buildURL("Accounts/" + id_cuenta, null, null, {}),
      null,
      {
        success: _.bind(function (account) {
          if (account) {
            app.api.call(
              "read",
              app.api.buildURL("tct02_Resumen/" + id_cuenta, null, null, {}),
              null,
              {
                success: _.bind(function (data) {
                  if (this.disposed) return;
                  //Declara objeto para control de estado de bloqueo
                  this.registroBloqueo = [];
                  var detalleBloqueo = {
                    estado: "Sin bloqueo",
                    condicion: "",
                    razon: "",
                    motivo: "",
                    detalle: "",
                    responsableIngesta: "",
                    responsableValidacion: "",
                  };
                  this.registroBloqueo["cartera"] =
                    App.utils.deepCopy(detalleBloqueo);
                  this.registroBloqueo["credito"] =
                    App.utils.deepCopy(detalleBloqueo);
                  this.registroBloqueo["cumplimiento"] =
                    App.utils.deepCopy(detalleBloqueo);

                  if (data) {
                    //Validaciones para estado
                    if (
                      account.tct_no_contactar_chk_c &&
                      !data.bloqueo_cartera_c
                    )
                      this.registroBloqueo.cartera.estado =
                        "Pendiente de aprobar bloqueo";
                    if (data.bloqueo_credito_c && !data.bloqueo2_c)
                      this.registroBloqueo.credito.estado =
                        "Pendiente de aprobar bloqueo";
                    if (data.bloqueo_cumple_c && !data.bloqueo3_c)
                      this.registroBloqueo.cumplimiento.estado =
                        "Pendiente de aprobar bloqueo";

                    if (
                      account.tct_no_contactar_chk_c &&
                      data.bloqueo_cartera_c
                    )
                      this.registroBloqueo.cartera.estado = "Cuenta bloqueada";
                    if (data.bloqueo_credito_c && data.bloqueo2_c)
                      this.registroBloqueo.credito.estado = "Cuenta bloqueada";
                    if (data.bloqueo_cumple_c && data.bloqueo3_c)
                      this.registroBloqueo.cumplimiento.estado =
                        "Cuenta bloqueada";

                    if (
                      !account.tct_no_contactar_chk_c &&
                      data.bloqueo_cartera_c
                    )
                      this.registroBloqueo.cartera.estado =
                        "Pendiente de aprobar desbloqueo";
                    if (!data.bloqueo_credito_c && data.bloqueo2_c)
                      this.registroBloqueo.credito.estado =
                        "Pendiente de aprobar desbloqueo";
                    if (!data.bloqueo_cumple_c && data.bloqueo3_c)
                      this.registroBloqueo.cumplimiento.estado =
                        "Pendiente de aprobar desbloqueo";
                    //Obtención de variables con detalle
                    this.registroBloqueo["nombreCuenta"] = account.name;
                    //Cartera
                    this.registroBloqueo.cartera.condicion =
                      data.condicion_cliente_c;
                    this.registroBloqueo.cartera.razon = data.razon_c;
                    this.registroBloqueo.cartera.motivo = data.motivo_c;
                    this.registroBloqueo.cartera.detalle = data.detalle_c;
                    this.registroBloqueo.cartera.responsableIngesta =
                      data.ingesta_c;
                    this.registroBloqueo.cartera.responsableValidacion =
                      data.validacion_c;
                    //Crédito
                    this.registroBloqueo.credito.condicion = data.condicion2_c;
                    this.registroBloqueo.credito.razon = data.razon2_c;
                    this.registroBloqueo.credito.motivo = data.motivo2_c;
                    this.registroBloqueo.credito.detalle = data.detalle2_c;
                    this.registroBloqueo.credito.responsableIngesta =
                      data.ingesta2_c;
                    this.registroBloqueo.credito.responsableValidacion =
                      data.validacion2_c;
                    //Cumplimiento
                    this.registroBloqueo.cumplimiento.condicion =
                      data.condicion3_c;
                    this.registroBloqueo.cumplimiento.razon = data.razon3_c;
                    this.registroBloqueo.cumplimiento.motivo = data.motivo3_c;
                    this.registroBloqueo.cumplimiento.detalle = data.detalle3_c;
                    this.registroBloqueo.cumplimiento.responsableIngesta =
                      data.ingesta3_c;
                    this.registroBloqueo.cumplimiento.responsableValidacion =
                      data.validacion3_c;
                  }
                  this.render();
                  app.alert.dismiss("uni2-disp-estado");
                  var modal = $("#consultarEstadoModal");
                  modal.show();
                }, this),
              }
            );
          }
        }, this),
      }
    );
  },

  cancelConsultarEstado: function () {
    var modal = $("#consultarEstadoModal");
    if (modal) {
      modal.hide();
      modal.remove();
    }
    $(".modal").modal("hide");
    $(".modal").remove();
    $(".modal-backdrop").remove();
  },

  consultarAudit: function(e){
    this.audit_historicos=[];
    app.alert.show("cambios_audit", {
      level: "process",
      closeable: false,
      messages: app.lang.get("LBL_LOADING"),
    });
    var id_cuenta = e.currentTarget.getAttribute("data-id");
    var nombre_cuenta_titulo = $(e.currentTarget).parent().parent().parent().parent().find('a').html();
    app.api.call(
      "read",
      app.api.buildURL("Accounts/" + id_cuenta + "/audit", null, null, {}),
      null,
      {
        success: _.bind(function (account) {
          if (account) {
            this.audit_account = account;
            app.api.call(
              "read",
              app.api.buildURL("tct02_Resumen/" + id_cuenta + "/audit",null,null,{}),null,{
                success: _.bind(function (resumen) {
                    if (this.disposed) return;

                    //Evaluando valores de cuenta
                    if( this.audit_account.records.length > 0 ){
                        for (let i = 0; i < this.audit_account.records.length; i++) {
                            
                            if (this.audit_account.records[i].field_name == 'tct_no_contactar_chk_c' && this.audit_account.records[i].after == '1' ) {
                                var objHistorico = {
                                    "area":"Cartera",
                                    "solicito": this.audit_account.records[i].created_by_username,
                                    "fechaSolicitud": this.convertDate(this.audit_account.records[i].date_created),
                                    "aprobo":"",
                                    "fechaAprobacion":""
                                };

                                this.audit_historicos.push(objHistorico);
                            }
                        }
                    }

                    if( resumen.records.length > 0 ){
                        for (let index = 0; index < resumen.records.length; index++) {
                            if (resumen.records[index].field_name == 'bloqueo_cartera_c' && resumen.records[index].after == '1' ) {
                                var objHistorico = {
                                    "area":"Cartera",
                                    "solicito": "",
                                    "fechaSolicitud": "",
                                    "aprobo": this.audit_account.records[index].created_by_username,
                                    "fechaAprobacion": this.convertDate(this.audit_account.records[index].date_created)
                                };

                                this.audit_historicos.push(objHistorico);
                               
                            }

                            //Campo solicitud de bloqueo Crédito
                            if (resumen.records[index].field_name == 'bloqueo_credito_c' && resumen.records[index].after == '1' ) {
                                var objHistoricoCredito = {
                                    "area":"Crédito",
                                    "solicito": resumen.records[index].created_by_username,
                                    "fechaSolicitud": this.convertDate(resumen.records[index].date_created),
                                    "aprobo": "",
                                    "fechaAprobacion": ""
                                };

                                this.audit_historicos.push(objHistoricoCredito);
                            }

                            //Campo aprobaciçon de bloqueo Crédito
                            if (resumen.records[index].field_name == 'bloqueo2_c' && resumen.records[index].after == '1' ) {
                                var objHistoricoCredito = {
                                    "area":"Crédito",
                                    "solicito": "",
                                    "fechaSolicitud": "",
                                    "aprobo": resumen.records[index].created_by_username,
                                    "fechaAprobacion": this.convertDate(resumen.records[index].date_created)
                                };

                                this.audit_historicos.push(objHistoricoCredito);
                            }

                            //Campo solicitud de bloqueo Cumplimiento
                            if (resumen.records[index].field_name == 'bloqueo_cumple_c' && resumen.records[index].after == '1' ) {
                                var objHistoricoCumple = {
                                    "area":"Cumplimiento",
                                    "solicito": resumen.records[index].created_by_username,
                                    "fechaSolicitud": this.convertDate(resumen.records[index].date_created),
                                    "aprobo": "",
                                    "fechaAprobacion": ""
                                };

                                this.audit_historicos.push(objHistoricoCumple);
                            }

                            //Campo aprobaciçon de bloqueo Cumplimiento
                            if (resumen.records[index].field_name == 'bloqueo3_c' && resumen.records[index].after == '1' ) {
                                var objHistoricoCumple = {
                                    "area":"Cumplimiento",
                                    "solicito": "",
                                    "fechaSolicitud": "",
                                    "aprobo": resumen.records[index].created_by_username,
                                    "fechaAprobacion": this.convertDate(resumen.records[index].date_created),
                                };

                                this.audit_historicos.push(objHistoricoCumple);
                            }

                        }

                    }

                    console.log("success_audit");
                    this.render();
                    $("#nombre_cuenta_audit").html(nombre_cuenta_titulo);
                    app.alert.dismiss("cambios_audit");
                    var modal = $("#consultarAuditModal");
                    modal.show();
                }, this),
              }
            );
          }
        }, this),
      }
    );
  },

  convertDate: function ( jsonDate ){
    var tempDate = jsonDate.split('T');
    var fecha = tempDate[0];
    var hora = tempDate[1].split('-');

    return fecha + " " +hora[0];
  },

  cancelConsultarAudit: function (){
    $("#nombre_cuenta_audit").html("");
    
    var modal = $("#consultarAuditModal");
    if (modal) {
      modal.hide();
      //modal.remove();
    }
    $(".modal").modal("hide");
    $(".modal").remove();
    $(".modal-backdrop").remove();

  },

  procesarCSV: function () {
    //Validar que se haya seleccionado un archivo
    var fileInput = document.getElementById("csv_no_contactar");
    var archivo = fileInput.value;
    if (archivo == "" || archivo == undefined) {
      app.alert.show("errorAlert", {
        level: "error",
        messages: "Favor de elegir un archivo",
        autoClose: true,
      });
    } else {
      $("#successful").hide();
      $("#processing").show();
      app.alert.show("reasignandoCSV", {
        level: "process",
        title: "Cargando...",
      });
      $(".btn_read_csv").addClass("disabled");
      $(".btn_read_csv").attr("style", "pointer-events:none;margin:10px");
      var file = fileInput.files[0];
      var nombre = file.name;
      var ext = nombre.toUpperCase();
      if (ext.substr(-3) != "CSV") {
        $(".btn_read_csv").removeClass("disabled");
        $(".btn_read_csv").attr("style", "margin:10px");
        app.alert.dismiss("reasignandoCSV");
        app.alert.show("nocsv", {
          level: "error",
          messages:
            "La extensión del archivo no es correcta. Favor de elegir un archivo .csv",
          autoClose: false,
        });
      } else {
        var textType = /text.*/;
        self = this;
        var reader = new FileReader();
        reader.onload = function (e) {
          var content = reader.result;
          var arr_ids = content.split("\n");
          var Params = {
            documento: content,
            archivo: nombre,
            tipo: "nocontactar",
          };
          if (content.trim() == "") {
            $(".btn_read_csv").removeClass("disabled");
            $(".btn_read_csv").attr("style", "margin:10px");
            app.alert.dismiss("reasignandoCSV");
            app.alert.show("csvVacio", {
              level: "error",
              messages:
                "Archivo sin contenido, favor de elegir un archivo v\u00E1lido",
              autoClose: false,
            });
          } else {
            var Url = app.api.buildURL("guardaCSV", "", {}, {});
            app.api.call(
              "create",
              Url,
              { data: Params },
              {
                success: _.bind(function (data) {
                  app.alert.dismiss("reasignandoCSV");
                  $(".btn_read_csv").removeClass("disabled");
                  $(".btn_read_csv").attr("style", "margin:10px");
                  app.alert.show("csvOK", {
                    level: "success",
                    messages:
                      "Archivo cargado con éxito. Le llegará un correo con el resultado de la actualización",
                    autoClose: false,
                  });
                  self.render();
                }, this),
                error: function (e) {
                  throw e;
                },
              }
            );
          }
        };
        reader.readAsText(file);
      }
    }
  },
});
