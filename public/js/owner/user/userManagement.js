/* Datatable */
$.fn.dataTable.ext.errMode = "none";

var dt_users = $("#dt_users").DataTable({
	"oLanguage": DATATABLE_PTBR,
	"autoWidth": false,
	"processing": true,
	"serverSide": true,
	"ajax": function(data, callback, settings){
		$.ajax({
			type: "POST",
			url: BASE_URL + "index.php/user/actListUsers",
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
		active_users_btns();
	},
	"initComplete": function(){
		$("#dt_users_filter").append("<button id='dt_users_filter_help' class='btn btn-primary ml-3' data-toggle='tooltip' title='Ajuda na procura'><i class='fas fa-question'></i></button>");
		$("#dt_users_filter_help").on("click", function(){
			$("#modal_dt_users_help").modal();
		});
	}
});

var dt_roles = $("#dt_roles").DataTable({
	"oLanguage": DATATABLE_PTBR,
	"autoWidth": false,
	"processing": true,
	"serverSide": true,
	"ajax": function(data, callback, settings){
		$.ajax({
			type: "POST",
			url: BASE_URL + "index.php/user/actListRoles",
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
		active_roles_btns();
	},
	"initComplete": function(){
		$("#dt_roles_filter").append("<button id='dt_roles_filter_help' class='btn btn-primary ml-3' data-toggle='tooltip' title='Ajuda na procura'><i class='fas fa-question'></i></button>");
		$("#dt_roles_filter_help").on("click", function(){
			$("#modal_dt_roles_help").modal();
		});
	}
});

var dt_roles_permissions = $("#dt_roles_permissions").DataTable({
	"oLanguage": DATATABLE_PTBR,
	"autoWidth": false,
	"processing": true,
	"serverSide": true,
	"ajax": function(data, callback, settings){
		$.ajax({
			type: "POST",
			url: BASE_URL + "index.php/user/actListRolesPermissions",
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
	"initComplete": function(){
		$("#dt_roles_permissions_filter").append("<button id='dt_roles_permissions_filter_help' class='btn btn-primary ml-3' data-toggle='tooltip' title='Ajuda na procura'><i class='fas fa-question'></i></button>");
		$("#dt_roles_permissions_filter_help").on("click", function(){
			$("#modal_dt_roles_permissions_help").modal();
		});
	}
});

/* Added roles counter */
var count_added_roles = 0;
/* Added permissions counter */
var count_added_permissions = 0;
/* Added actions permissoins counter */
var count_added_actions = 0;
// Preload options
var controllers_options = '';
var roles_options = '';

/* Active datatable buttons */
function active_users_btns(){
	$(".btn-alter-user-pwd").on("click", function(){
		var user_id = $(this).data("user_id");
		var username = $(this).data("username");
		clearErrors();
		$("#alter_user_password_form")[0].reset();
		$("#alterUserPasswordModalLabel").html("<strong>Alterar senha do usuário '" + username + "'</strong>");
		$("#alter_user_password_form-user_id").val(user_id);
		$("#alterUserPasswordModal").modal();
	});

	$(".btn-block-user").on("click", function(){
		var user_id = $(this).data("user_id");
		$.ajax({
			type: "POST",
			url: BASE_URL + "index.php/user/actBlockUser",
			dataType: "json",
			data: {
				"user_id": user_id
			},
			success: function(data){
				if(data.status){
					swal_success("Usuário bloqueado com sucesso.");
					dt_users.ajax.reload();
				}else{
					swal_error(data);
				}
			},
			error: function(data){
				swal_error("Falha ao bloquear usuário!");
				console.log("ERROR!");
				console.log(data);
			}
		});
	});

	$(".btn-unblock-user").on("click", function(){
		var user_id = $(this).data("user_id");
		$.ajax({
			type: "POST",
			url: BASE_URL + "index.php/user/actUnblockUser",
			dataType: "json",
			data: {
				"user_id": user_id
			},
			success: function(data){
				if(data.status){
					swal_success("Usuário desbloqueado com sucesso.");
					dt_users.ajax.reload();
				}else{
					swal_error(data);
				}
			},
			error: function(data){
				swal_error("Falha ao desbloquear usuário!");
				console.log("ERROR!");
				console.log(data);
			}
		});
	});

	$(".btn-edit-user").on("click", function(){
		load_roles();
		var user_id = $(this).data("user_id");
		$.ajax({
			type: "POST",
			url: BASE_URL + "index.php/user/actGetUserData",
			dataType: "json",
			data: {
				"user_id": user_id
			},
			success: function(data){
				if(data.status == 0){
					swal_error(data);
					return false;
				}

				var {user} = data;
				var {roles} = user;
				delete user["roles"];

				clearErrors();
				$("#form_user")[0].reset();
				reset_roles();
				$("#modal_form_user_title").html("<strong>Edição de usuário:</strong>");
				$("#form_user-submit_btn").html("Editar");
				$.each(user, function(id, value){
					$("#form_user-"+id).val(value);
				});
				$("#form_user-email_confirm").val(data["user"]["email"]);

				$.each(roles, function(){
					add_role();
					$("#"+get_role_id()+" .select_role").val(this.role_id);
				});

				if($("#form_user-is_admin").val() == 1)
					hide_roles();
				else
					show_roles();
				hide_password();
				$("#modal_form_user").modal();
			},
			error: function(data){
				swal_error("Falha ao carregar dados do usuário!");
				console.log("ERROR!");
				console.log(data);
			}
		});
	});

	$(".btn-del-user").on("click", function(){
		swal_confirm_delete({
			confirm_message: "Deseja deletar este usuário?",
			ajax_url: "index.php/user/actDeleteUser",
			ajax_data: {
				user_id: $(this).data("user_id")
			},
			ajax_success: {
				success_message: "Usuário deletado com sucesso.",
				error_message: null,
				datatables: [dt_users]
			},
			ajax_error: {
				error_message: "Falha ao deletar usuário."
			}
		});
	});
}

function active_roles_btns(){
	$(".btn-edit-role").on("click", function(){
		var role_id = $(this).data("role_id");

		$.ajax({
			type: "POST",
			url: BASE_URL + "index.php/user/actGetRoleData",
			dataType: "json",
			data: {
				"role_id": role_id
			},
			success: function(data){
				if(data.status == 0){
					swal_error(data);
					return false;
				}

				var {role} = data;
				var {actions} = role;
				delete role["actions"];

				clearErrors();
				$("#form_role")[0].reset();
				reset_permissions();
				$("#modal_form_role_title").html("<strong>Edição de papéis:</strong>");
				$("#form_role-submit_btn").html("Editar");
				$.each(role, function(id, value){
					$("#form_role-"+id).val(value);
				});

				var fmt_permissions = {};
				$.each(actions, function(index, value){
					if(typeof fmt_permissions[value["restrict_action.controller_id"]] === "undefined")
						fmt_permissions[value["restrict_action.controller_id"]] = {};

					if(typeof fmt_permissions[value["restrict_action.controller_id"]][value["restrict_action.id"]] === "undefined")
						fmt_permissions[value["restrict_action.controller_id"]][value["restrict_action.id"]] = 1;
				});
				$.each(fmt_permissions, function(controller_id, actions){
					add_permission();
					var permission_id, permission_controller_id, actions_container_id;
					permission_id = get_permission_id();
					permission_controller_id = get_permission_controller_id();
					actions_container_id = get_permission_actions_container_id();

					$("#"+permission_controller_id).val(controller_id);
					var check_data = {
						controller_id: controller_id,
						permission_id: permission_id,
						actions: actions
					};
					load_controller_actions(controller_id, actions_container_id, check_data);
				});

				make_textarea_resizable("form_role-description");
				$("#form_role-description").height("auto");
				$("#modal_form_role").modal();
			},
			error: function(data){
				swal_error("Falha ao carregar dados do papel!");
				console.log("ERROR!");
				console.log(data);
			}
		});
	});

	$(".btn-del-role").on("click", function(){
		swal_confirm_delete({
			confirm_message: "Deseja deletar este papel?",
			ajax_url: "index.php/user/actDeleteRole",
			ajax_data: {
				role_id: $(this).data("role_id")
			},
			ajax_success: {
				success_message: "Papel deletado com sucesso.",
				error_message: null,
				datatables: [dt_users, dt_roles, dt_roles_permissions]
			},
			ajax_error: {
				error_message: "Falha ao deletar papel."
			}
		});
	});
}

function get_permission_id(){
	return "permission" + count_added_permissions;
}

function get_permission_controller_id(){
	return "permission_controller" + count_added_permissions;
}

function get_permission_actions_container_id(){
	return "permission_actions_container" + count_added_permissions;
}

function get_role_id(){
	return "role" + count_added_roles;
}

function get_check_all_actions_id(){
	return "check_all_actions" + count_added_permissions;
}

function add_role(){
	count_added_roles++;
	var role_id = get_role_id();

	$("#container_roles").append('<div id="'+role_id+'" class="mx-0 px-0 row col-md-12" data-current_id="'+count_added_permissions+'"><div class="form-group col-md-10"><label>Papel:</label><select class="form-control select_role" name="role_id[]">'+roles_options+'</select></div><div class="form-group col-md-2"><label>&nbsp;</label><br><button class="float-right btn btn-danger delete-role" data-toggle="tooltip" title="Deletar papel" type="button"><i class="fa fa-times"></i></button></div></div>');
}

function add_permission(){
	count_added_permissions++;
	var permission_id, permission_controller_id, actions_container_id, check_all_actions_id;
	permission_id = get_permission_id();
	permission_controller_id = get_permission_controller_id();
	actions_container_id = get_permission_actions_container_id();
	check_all_actions_id = get_check_all_actions_id();

	$("#container_permissions").append('<div id="'+permission_id+'" class="mx-0 px-0 row col-md-12" data-current_id="'+count_added_permissions+'"><div class="form-group col-md-4"><label>Controlador:</label><select class="form-control" id="'+permission_controller_id+'" name="permission_controller_id[]">'+controllers_options+'</select></div><div class="form-group col-md-7 m-0 p-0"><label>Ações:</label><br><div class="mx-0 px-0 col-md-10" style="display:inline-block;"><input id="'+check_all_actions_id+'" type="checkbox" class="check_all_actions">&nbsp;<label for="'+check_all_actions_id+'">Todas as ações</label><div class="mx-0 px-0 row actions_container" id="'+actions_container_id+'"></div></div></div><div class="form-group col-md-1"><button class="float-right btn btn-danger delete-permission" data-toggle="tooltip" title="Deletar permissão" type="button"><i class="fa fa-times"></i></button></div></div>');

	$("#"+permission_controller_id).on("change", function(){
		var select_id, controller_id;

		select_id = $(this).attr("id");
		controller_id = $("#"+select_id+" option:selected").val();

		load_controller_actions(controller_id, actions_container_id);
	});
}

function load_controller_actions(controller_id, actions_container_id, check_data = {}){
	$.ajax({
		url: BASE_URL + "index.php/user/actGetControllerActions",
		type: "POST",
		dataType: "json",
		data: {
			"controller_id": controller_id
		},
		success: function(data){
			if(data.status){
				var html = "";
				$.each(data.actions, function(i, v){
					count_added_actions++;
					html += create_action(v, count_added_actions);
				});
				$("#"+actions_container_id).html(html);

				$.each(check_data.actions, function(action_id, v){
					var selector = "#"+check_data.permission_id+" input[name='permissions["+check_data.controller_id+"]["+action_id+"][]']";
					$(selector).siblings(".permission_checkbox").prop("checked", true);
					$(selector).val(1);
				});
			}else{
				swal_error(data);
			}
		},
		error: function(data){
			swal_error("Erro ao carregar ação do controlador!");
			console.log("ERROR!");
			console.log(data);
		}
	});
}

function create_action(data, num){
	return '<div class="col-md-12 mx-0 px-0"><input id="action'+num+'" type="checkbox" class="permission_checkbox"><input class="aux_permission_checkbox" type="text" name="permissions['+data["action.controller_id"]+']['+data["action.id"]+'][]" value="0" hidden><label for="action'+num+'">&nbsp;'+data["action.canonical_name"]+'</label></div>';
}

function reset_permissions(){
	count_added_permissions = 0;
	$("#container_permissions").html("");
}

function reset_roles(){
	count_added_roles = 0;
	$("#container_roles").html("");
}

function show_password(){
	$("#form_user-password").parent().show();
	$("#form_user-password_confirm").parent().show();
}

function hide_password(){
	$("#form_user-password").parent().hide();
	$("#form_user-password_confirm").parent().hide();
}

function show_permissions(){
	$("#container_permissions").parent().show();
}

function hide_permissions(){
	$("#container_permissions").parent().hide();
}

function show_roles(){
	$("#container_roles").parent().show();
}

function hide_roles(){
	$("#container_roles").parent().hide();
}

function create_option(id, name){
	return "<option value='" + id + "'>" + name + "</option>";
}

function load_controllers(){
	$.ajax({
		url: BASE_URL + "index.php/user/actGetControllers",
		type: "GET",
		dataType: "json",
		success: function(data){
			if(data.status){
				controllers_options = '';
				$.each(data.controllers, function(index, value){
					controllers_options += create_option(value.id, value.friendly_name);
				});
			}else{
				swal_error(data);
			}
		},
		error: function(data){
			swal_error("Erro ao carregar controladores!");
			console.log("ERROR!");
			console.log(data);
		}
	});
}

function load_roles(){
	$.ajax({
		url: BASE_URL + "index.php/user/actGetRoles",
		type: "GET",
		dataType: "json",
		success: function(data){
			if(data.status){
				roles_options = '';
				$.each(data.roles, function(index, value){
					roles_options += create_option(value.id, value.name);
				});
			}else{
				swal_error(data);
			}
		},
		error: function(data){
			swal_error("Erro ao carregar papéis!");
			console.log("ERROR!");
			console.log(data);
		}
	});
}

$(document).ready(function(){
	/* Load controllers to permissions users */
	if(exists("#add_role"))
		load_roles();

	if(exists("#add_permission"))
		load_controllers();

	/* Add user */
	$("#btn_add_user").on("click", function(){
		load_roles();
		clearErrors();
		$("#form_user")[0].reset();
		reset_roles();
		$("#modal_form_user_title").html("<strong>Cadastro de usuário:</strong>");
		$("#form_user-submit_btn").html("Cadastrar");
		if($("#form_user-is_admin").val() == 1)
			hide_roles();
		else
			show_roles();
		show_password();
		$("#modal_form_user").modal();
	});

	$("#btn_add_role").on("click", function(){
		clearErrors();
		$("#form_role")[0].reset();
		reset_permissions();
		$("#modal_form_role_title").html("<strong>Cadastro de papel de usuário:</strong>");
		$("#form_role-submit_btn").html("Cadastrar");
		make_textarea_resizable("form_role-description");
		$("#form_role-description").height("auto");
		$("#modal_form_role").modal();
	});

	/* Form submit */
	$("#form_user").on("submit", function(){
		var url = BASE_URL + "index.php/user/";

		if($("#form_user-id").val() == "")
			url += "actCreateUser";
		else
			url += "actUpdateUser";

		$.ajax({
			url: url,
			type: "POST",
			dataType: "json",
			data: $("#form_user").serialize(),
			beforeSend: function(){
				$("#form_user-submit_btn").siblings(".form-text").html(loadingImg("Verificando..."));
			},
			success: function(data){
				if(data.status){
					$("#modal_form_user").modal("hide");
					swal_success("Ação realizada com sucesso.");
					dt_users.ajax.reload();
				}else{
					showErrors(data.errorList, "form_user-");
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

	/* Alter user password submit */
	$("#alter_user_password_form").on("submit", function(){
		$.ajax({
			type: "POST",
			url: BASE_URL + "index.php/user/actAlterUserPassword",
			dataType: "json",
			data: $("#alter_user_password_form").serialize(),
			beforeSend: function(){
				$("#alter_user_password_form-submit_btn").siblings(".form-text").html(loadingImg("Verificando..."));
			},
			success: function(data){
				if(data.status){
					$("#alterUserPasswordModal").modal("hide");
					swal_success("Senha alterada com sucesso.");
				}else{
					showErrors(data.errorList, "alter_user_password_form-");
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

	$("#form_role").on("submit", function(){
		var url = BASE_URL + "index.php/user/";

		if($("#form_role-id").val() == "")
			url += "actCreateRole";
		else
			url += "actUpdateRole";

		$.ajax({
			url: url,
			type: "POST",
			dataType: "json",
			data: $("#form_role").serialize(),
			beforeSend: function(){
				$("#form_role-submit_btn").siblings(".form-text").html(loadingImg("Verificando..."));
			},
			success: function(data){
				if(data.status){
					$("#modal_form_role").modal("hide");
					swal_success("Ação realizada com sucesso.");
					dt_users.ajax.reload();
					dt_roles.ajax.reload();
					dt_roles_permissions.ajax.reload();
					load_roles();
				}else if(typeof data.session_expired !== "undefined"){
					swal_error(data);
				}else{
					showErrors(data.error_list);
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

	$("#add_permission").on("click", function(){
		add_permission();
		var permission_controller_id, actions_container_id;
		permission_controller_id = get_permission_controller_id();
		actions_container_id = get_permission_actions_container_id();
		controller_id = $("#"+permission_controller_id+" option:selected").val();
		load_controller_actions(controller_id, actions_container_id);
	});

	$("#add_role").on("click", function(){
		add_role();
	});

	$("#form_user-is_admin").on("change", function(){
		var is_admin = $(this).val();
		if(is_admin == 1){
			hide_roles();
		}else if(is_admin == 0){
			show_roles();
		}
	});

	$("#modal_form_user").on("click", ".delete-role", function(){
		$(this).parent().parent().remove();
		$("#modal_form_user").trigger("focus");
	});

	$("#modal_form_role").on("click", ".delete-permission", function(){
		$(this).parent().parent().remove();
		$("#modal_form_role").trigger("focus");
	});

	$("#modal_form_role").on("change", ".permission_checkbox", function(){
		if($(this).is(":checked")){
			$(this).siblings(".aux_permission_checkbox").val(1);
		}else{
			$(this).parent().parent().siblings(".check_all_actions").prop("checked", false);
			$(this).siblings(".aux_permission_checkbox").val(0);
		}
	});

	$("#modal_form_role").on("change", ".check_all_actions", function(){
		var current = $(this).prop("checked");
		var inputs = $(this).siblings(".actions_container").find("input[type='checkbox']");
		$.each(inputs, function(){
			$(this).prop("checked", current);
			$($(this).siblings(".aux_permission_checkbox")).val((current)?1:0);
		});
	});

	$("#modal_form_user").on("shown.bs.modal", function(){
		$("#form_user-username").trigger("focus");
	});

	$("#modal_form_role").on("shown.bs.modal", function(){
		$("#form_role-name").trigger("focus");
		$("#form_role-description").height($("#form_role-description")[0].scrollHeight);
	});

	$("#alterUserPasswordModal").on("shown.bs.modal", function(){
		$("#alter_user_password_form-password").trigger("focus");
	});

	$('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
		$("#"+$(e.target).attr("aria-controls")+" input:first").trigger("focus");
	});
});
