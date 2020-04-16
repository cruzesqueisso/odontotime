$(document).ready(function(){
	$("#login_form-username").focus();
	$("#login_form").on("submit", function(){
		$.ajax({
			url: BASE_URL + "index.php/restrict/login",
			type: "POST",
			dataType: "json",
			data: $("#login_form").serialize(),
			beforeSend: function(){
				clearErrors();
				$("#login_form-submit_btn").siblings(".form-text").html(loadingImg("Verificando..."));
			},
			success: function(data){
				if(data["status"] == 1){
					clearErrors();
					$("#login_form-submit_btn").siblings(".form-text").html(loadingImg("Logando..."));
					window.location = BASE_URL + "index.php/restrict";
				}else{
					showErrors(data["errorList"]);
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

	$("#form_recovery_password").on("submit", function(){
		$.ajax({
			url: BASE_URL + "index.php/restrict/recoveryPassword",
			type: "POST",
			dataType: "json",
			data: $("#form_recovery_password").serialize(),
			beforeSend: function(){
				clearErrors();
				$("#form_recovery_password-submit_btn").siblings(".form-text").html(loadingImg("Verificando..."));
			},
			success: function(data){
				console.log(data);
				if(data["status"] == 1){
					$("#modal_form_recovery_password").modal("hide");
					swal_success("Uma nova senha foi enviada para o e-mail vinculado ao usuário fornecido.");
				}else{
					showErrors(data["errorList"], "form_recovery_password-");
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

	$("#call_recovery_password").on("click", function(){
		clearErrors();
		$("#form_recovery_password")[0].reset();
		$("#modal_form_recovery_password").modal();
		return false;
	});

	$('#modal_form_recovery_password').on('shown.bs.modal', function (){
		$('#form_recovery_password-username').trigger('focus');
	});
});
