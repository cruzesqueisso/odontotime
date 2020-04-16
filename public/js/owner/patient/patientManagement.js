/* Datatable */
$.fn.dataTable.ext.errMode = "none";

var dt_patients = $("#dt_patients").DataTable({
	"oLanguage": DATATABLE_PTBR,
	"autoWidth": false,
	"processing": true,
	"serverSide": true,
	"ajax": function(data, callback, settings){
		$.ajax({
			type: "POST",
			url: BASE_URL + "index.php/patient/actListPatients",
			data: data,
			dataType: "json",
			success: function(data){
				dt_swal_error(data);
				callback(data);
			},
			error: function(data){
				console.log("ERROR!");
				console.log(data);
			}
		});
	},
	"columnDefs":[
		{targets: "no-sort", orderable: false},
		{targets: "dt-center", className: "dt-center"}
	],
	"drawCallback": function(){
		active_patients_btns();
	},
	"initComplete": function(){
		$("#dt_patients_filter").append("<button id='dt_patients_filter_help' class='btn btn-primary ml-3' data-toggle='tooltip' title='Ajuda na procura'><i class='fas fa-question'></i></button>");
		$("#dt_patients_filter_help").on("click", function(){
			$("#modal_dt_patients_help").modal();
		});
	}
});

var dt_app_users = $("#dt_app_users").DataTable({
	"oLanguage": DATATABLE_PTBR,
	"autoWidth": false,
	"processing": true,
	"serverSide": true,
	"ajax": function(data, callback, settings){
		$.ajax({
			type: "POST",
			url: BASE_URL + "index.php/patient/actListAppUsers",
			data: data,
			dataType: "json",
			success: function(data){
				dt_swal_error(data);
				callback(data);
			},
			error: function(data){
				console.log("ERROR!");
				console.log(data);
			}
		});
	},
	"columnDefs":[
		{targets: "no-sort", orderable: false},
		{targets: "dt-center", className: "dt-center"}
	],
	"drawCallback": function(){
		active_patients_btns();
	},
	"initComplete": function(){
		$("#dt_app_users_filter").append("<button id='dt_app_users_filter_help' class='btn btn-primary ml-3' data-toggle='tooltip' title='Ajuda na procura'><i class='fas fa-question'></i></button>");
		$("#dt_app_users_filter_help").on("click", function(){
			$("#modal_dt_app_users_help").modal();
		});
	}
});

/* Active datatable buttons */
function active_patients_btns(){
	$(".btn-alter-app-user-pwd").on("click", function(){
		clearErrors();
		$("#alter_app_user_password_form")[0].reset();
		$("#alterAppUserPasswordModalLabel").html("<strong>Alterar senha do usuário do APP '" + $(this).data("username") + "'</strong>");
		$("#alter_app_user_password_form-id").val($(this).data("id"));
		$("#alterAppUserPasswordModal").modal();
	});

	$(".btn-block-app-user").on("click", function(){
		$.ajax({
			type: "POST",
			url: BASE_URL + "index.php/patient/actBlockUser",
			dataType: "json",
			data: {
				"id": $(this).data("id")
			},
			success: function(data){
				if(data.status){
					swal_success("Usuário do APP bloqueado com sucesso.");
					dt_app_users.ajax.reload();
				}else{
					swal_error(data);
				}
			},
			error: function(data){
				swal_error("Falha ao bloquear usuário do APP!");
				console.log("ERROR!");
				console.log(data);
			}
		});
	});

	$(".btn-unblock-app-user").on("click", function(){
		$.ajax({
			type: "POST",
			url: BASE_URL + "index.php/patient/actUnblockUser",
			dataType: "json",
			data: {
				"id": $(this).data("id")
			},
			success: function(data){
				if(data.status){
					swal_success("Usuário do APP desbloqueado com sucesso.");
					dt_app_users.ajax.reload();
				}else{
					swal_error(data);
				}
			},
			error: function(data){
				swal_error("Falha ao desbloquear usuário do APP!");
				console.log("ERROR!");
				console.log(data);
			}
		});
	});

	$(".btn-edit-patient").on("click", function(){
		var patient_id = $(this).data('patient_id');
		$.ajax({
			type: "POST",
			url: BASE_URL + "index.php/patient/actGetPatientData",
			dataType: "json",
			data: {
				"patient_id": patient_id
			},
			success: function(data){
				if(data.status == 0){
					swal_error(data);
					return false;
				}
				clearErrors();
				$("#form_patient")[0].reset();
				$("#modal_form_patient_title").html("<strong>Edição de paciente:</strong>");
				$("#form_patient-submit_btn").html("Editar");
				$.each(data["patient"], function(id, value){
					$("#form_patient-"+id).val(value);
				});
				$("#form_patient-email_confirm").val(data["patient"]["email"]);
				var selected_state_id = $("#form_patient-state option:selected").data("state_id");
				load_cities(selected_state_id, data["patient"].city);
				$("#modal_form_patient").modal();
			},
			error: function(data){
				swal_error("Falha ao carregar dados do paciente!");
				console.log("ERROR!");
				console.log(data);
			}
		});
	});

	$(".btn-del-patient").on("click", function(){
		swal_confirm_delete({
			confirm_message: "Deseja deletar este paciente?",
			ajax_url: "index.php/patient/actDeletePatient",
			ajax_data: {
				patient_id: $(this).data("patient_id"),
			},
			ajax_success: {
				success_message: "paciente deletado com sucesso.",
				error_message: null,
				datatables: [dt_patients, dt_app_users]
			},
			ajax_error: {
				error_message: "Falha ao deletar paciente!"
			}
		});
	});
}

function create_option(id, abbreviation, name){
	return "<option value='" + abbreviation + "' data-state_id='" + id + "'>" + name + "</option>";
}

function load_states(default_state = false, default_city = false){
	$.ajax({
		url: "https://servicodados.ibge.gov.br/api/v1/localidades/estados",
		type: "GET",
		dataType: "json",
		success: function(data){
			var html = "";
			$.each(data, function(index, element){
				html += create_option(element.id, element.sigla, element.nome);
			});
			$("#form_patient-state").html(html);

			if(default_state !== false){
				$("#form_patient-state").val(default_state);
			}

			var selected_state_id = $("#form_patient-state option:selected").data("state_id");
			if(typeof selected_state_id !== 'undefined'){
				if(default_city !== false){
					load_cities(selected_state_id, default_city);
				}else{
					load_cities(selected_state_id);
				}
			}
		},
		error: function(data){
			swal_error("Falha ao carregar dados de estados!");
			console.log("ERROR!");
			console.log(data);
		}
	});
}

function load_cities(state_id, default_city = false){
	var url = "https://servicodados.ibge.gov.br/api/v1/localidades/estados/" + state_id + "/municipios";

	$.ajax({
		url: url,
		type: "GET",
		dataType: "json",
		success: function(data){
			var html = "";
			$.each(data, function(index, element){
				html += create_option(element.id, element.nome, element.nome);
			});
			$("#form_patient-city").html(html);
			if(default_city !== false)
				$("#form_patient-city").val(default_city);
		},
		error: function(data){
			swal_error("Falha ao carregar dados de cidades!");
			console.log("ERROR!");
			console.log(data);
		}
	});
}

$(document).ready(function(){
	/* Load states to client form */
	if(exists("#form_patient-state"))
		load_states("MG", "Patrocínio");

	var options_phone = {
		onKeyPress: function(phone, e, field, options){
			var masks = ["(00) 0000-0000ZZ", "(00) 0 0000-0000"];
			var mask;
			var phone_type = $("#form_patient-phone").data("phone_type");

			if(phone.length <= 14){
				mask = masks[0];
				phone_type = "short";
			}else if(phone.length > 14 && phone_type == "short"){
				mask = masks[1];
				phone_type = "long";
			}else if(phone.length == 15 && phone_type == "long"){
				mask = masks[0];
				phone_type = "short";
			}
			$("#form_patient-phone").mask(mask, options);
			$("#form_patient-phone").data("phone_type", phone_type);
		},
		translation: {
			'Z': {
				pattern: /[0-9]/, optional: true
			}
		}
	};

	$("#form_patient-cpf").mask("000.000.000-00");
	$("#form_patient-phone").mask("(00) 0000-0000ZZ", options_phone);

	$('#form_patient-number').on("input", function() {
		this.value = just_numbers(this.value);
	});

	/* Add client */
	$("#btn_add_patient").on("click", function(){
		clearErrors();
		$("#form_patient")[0].reset();
		$("#modal_form_patient_title").html("<strong>Cadastro de paciente:</strong>");
		$("#form_patient-submit_btn").html("Cadastrar");
		$("#form_patient-state").val("MG");
		var mg_state_id = $("#form_patient-state option:selected").data("state_id");
		load_cities(mg_state_id, "Patrocínio");
		$("#modal_form_patient").modal();
	});

	$("#form_patient").on("submit", function(){
		var url = BASE_URL + "index.php/patient/";

		if($("#form_patient-id").val() == "")
			url += "actCreatePatient";
		else
			url += "actUpdatePatient";

		$.ajax({
			url: url,
			type: "POST",
			dataType: "json",
			data: $("#form_patient").serialize(),
			beforeSend: function(){
				$("#form_patient-submit_btn").siblings(".form-text").html(loadingImg("Verificando..."));
			},
			success: function(data){
				console.log(data);
				if(data.status){
					$("#modal_form_patient").modal("hide");
					swal_success("Ação realizada com sucesso");
					dt_patients.ajax.reload();
					dt_app_users.ajax.reload();
				}else{
					showErrors(data.errorList, "form_patient-");
				}
			},
			error: function(data){
				swal_error("Falha ao realizar requisição ao servidor!");
				console.log("ERROR!");
				console.log(data);
			}
		});

		return false;
	});

	/* Alter APP user password submit */
	$("#alter_app_user_password_form").on("submit", function(){
		$.ajax({
			type: "POST",
			url: BASE_URL + "index.php/patient/actAlterAppUserPassword",
			dataType: "json",
			data: $("#alter_app_user_password_form").serialize(),
			beforeSend: function(){
				$("#alter_app_user_password_form-submit_btn").siblings(".form-text").html(loadingImg("Verificando..."));
			},
			success: function(data){
				if(data.status){
					$("#alterAppUserPasswordModal").modal("hide");
					swal_success("Senha alterada com sucesso.");
				}else{
					showErrors(data.errorList, "alter_app_user_password_form-");
				}
			},
			error: function(data){
				swal_error("Falha ao realizar requisição ao servidor!");
				console.log("ERROR!");
				console.log(data);
			}
		});

		return false;
	});

	$("#form_patient-state").on("change", function(){
		load_cities($("#form_patient-state option:selected").data('state_id'));
	});

	$('#modal_form_patient').on('shown.bs.modal', function (){
		$('#form_patient-name').trigger('focus');
	});
});
