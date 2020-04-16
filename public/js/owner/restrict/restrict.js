function loadContent(url){
	$.ajax({
		url: BASE_URL + url,
		type: "GET",
		dataType: "json",
		success: function(data){
			if(data.status == 1){
				$("#dynamic-content").html(data.content);
				$("[role=tablist]").each(function(index, obj){
					$("#"+obj.id+" a:first").trigger("click");
				});
				$("#dynamic-content input:first").trigger("focus");
			}else{
				swal_error(data);
			}
		},
		error: function(data){
			swal_error("Falha ao carregar conteúdo!");
			console.log("ERROR!");
			console.log(data);
		}
	});
}

function current_nav(selector){
	$("#accordionSidebar .nav-item").css("color", "");
	$("#accordionSidebar .nav-item i").css("color", "");
	$(selector).css("color", "rgba(255,255,255)");
	$(selector+" i").css("color", "rgba(255,255,255)");
}

$(document).ready(function(){
	$("#sidebarUser").on("click", function(){
		loadContent("index.php/user");
		current_nav("#sidebarUser");
		return false;
	});
	$("#sidebarPatient").on("click", function(){
		loadContent("index.php/patient");
		current_nav("#sidebarPatient");
		return false;
	});

	$("#profile_user_caller").on("click", function(){
		var user_id = $(this).data('user_id');
		$.ajax({
			type: "POST",
			url: BASE_URL + "index.php/restrict/userProfile",
			dataType: "json",
			data: {
				"user_id": user_id
			},
			success: function(data){
				if(data.status){
					clearErrors();
					$.each(data.profile, function(key, value){
						$("#profile_user_form-"+key).val(value);
					});
					$("#profileUserModal").modal();
				}else{
					swal_error(data);
				}
			},
			error: function(data){
				swal_error("Falha ao carregar dados do perfil de usuário!");
				console.log("ERROR!");
				console.log(data);
			}
		});
	});

	$("#profile_user_form").on("submit", function(){
		$.ajax({
			type: "POST",
			url: BASE_URL + "index.php/restrict/updateUserProfile",
			dataType: "json",
			data: $("#profile_user_form").serialize(),
			beforeSend: function(){
				$("#profile_user_form-submit_btn").siblings(".form-text").html(loadingImg("Verificando..."));
			},
			success: function(data){
				if(data.status){
					$("#profileUserModal").modal("hide");
					$("#userDropdown span").html($("#profile_user_form-name").val());
					swal_success("Ação realizada com sucesso.");
				}else{
					showErrors(data.errorList, "profile_user_form-");
				}
			},
			error: function(data){
				swal_error("Falha ao realizar requisição para o servidor!");
				console.log("ERROR!");
				console.log(data);
			}
		});

		return false;
	});

	$("#alter_current_user_pwd_caller").on("click", function(){
		clearErrors();
		$("#alter_current_user_pwd_form")[0].reset();
		$("#alterCurrentUserPwdModal").modal();
	});

	$("#alter_current_user_pwd_form").on("submit", function(){
		$.ajax({
			url: BASE_URL + 'index.php/restrict/alterUserPassword',
			type: 'POST',
			dataType: 'json',
			data: $("#alter_current_user_pwd_form").serialize(),
			beforeSend: function(){
				$("#alter_current_user_pwd_form-submit_btn").siblings(".form-text").html(loadingImg("Verificando..."));
			},
			success: function(data){
				console.log(data);
				if(data.status){
					$("#alterCurrentUserPwdModal").modal("hide");
					swal_success("Senha alterada com sucesso.");
				}else{
					showErrors(data.errorList, "alter_current_user_pwd_form-");
				}
			},
			error: function(data){
				swal_error("Falha ao realizar requisição para o servidor!");
				console.log("ERROR!");
				console.log(data);
			}
		});

		return false;
	});

	$("#profileUserModal").on("shown.bs.modal", function(){
		$("#profile_user_form.name").trigger("focus");
	});

	$("#alterCurrentUserPwdModal").on("shown.bs.modal", function(){
		$("#alter_current_user_pwd_form.current_password").trigger("focus");
	});
});
