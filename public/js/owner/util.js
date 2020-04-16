function clearErrors(){
	$(".is-invalid").removeClass("is-invalid");
	$(".invalid-feedback").html("");
	$(".form-text").html("");
}

function showErrors(errorList, prefix = ""){
	clearErrors();

	$.each(errorList, function(id, message){
		$("#" + prefix + id).addClass("is-invalid");
		$("#" + prefix + id).siblings(".invalid-feedback").html(message);
		$("#" + prefix + id).siblings(".form-text").html(message);
	});
}

function loadingImg(message = ""){
	return '<span class="fas fa-circle-notch fa-spin"></span>&nbsp' + message;
}

function just_numbers(value){
	return value.replace(/[^0-9]/, "");
}

function make_textarea_resizable(id) {
    var o = $("#" + id);
	o.css("overflow-y", "hidden");

    function resize_textarea() {
		o.height("auto");
        o.height(o[0].scrollHeight);
    }

	function set_event_listener(){
		o.on("input", function(){
			resize_textarea();
		});
	}

	var list_obj_events = $._data($("#" + id)[0], "events");
	if(typeof list_obj_events === "undefined"){
		set_event_listener();
		return;
	}

	list_obj_events = list_obj_events.input;

	var event_found = false;
	$.each(list_obj_events, function(index, obj){
		if(obj.type === "input"){
			event_found = true;
			return false;
		}
	});

	if( ! event_found)
		set_event_listener();
}

function swal_success_obj(message){
	return {
		title: "Sucesso!",
		icon: "success",
		text: message,
		timer: SUCCESS_ALERT_TIMER
	};
}

function swal_success(message){
	swal(swal_success_obj(message));
}

function swal_warning_obj(message){
	return {
		title: "Atenção!",
		icon: "warning",
		text: message,
		closeOnClickOutside: false
	};
}

function swal_warning(message){
	swal(swal_warning_obj(message));
}

function swal_error_obj(message){
	return {
		title: "Erro!",
		icon: "error",
		text: message,
		closeOnClickOutside: false,
		buttons: {
			confirm: {
				text: "OK",
				value: true,
				visible: true,
				closeModal: true
			}
		}
	};
}

function swal_expired_session(message){
	swal(
		swal_error_obj(message)
	).then(function(result){
		if(result)
			window.location.replace(BASE_URL + "index.php");
	});
}

function swal_error(json){
	if(typeof json === "string"){
		swal(swal_error_obj(json));
		return;
	}

	if(typeof json.error_message !== "undefined")
		if(typeof json.session_expired !== "undefined"
		&& json.session_expired == 1)
			swal_expired_session(json.error_message);
		else
			swal(swal_error_obj(json.error_message));
}

function dt_swal_error(json){
	if(typeof json.error !== "undefined")
		if(typeof json.session_expired !== "undefined"
		&& json.session_expired == 1)
			swal_expired_session(json.error);
		else
			swal(swal_error(json.error));
}

function swal_confirm_obj(message){
	return {
		title: "Atenção!",
		text: message,
		icon: "warning",
		dangerMode: true,
		buttons: {
			cancel: {
				text: "Cancelar",
				value: 0,
				visible: true,
				closeModal: true,
			},
			confirm: {
				text: "Deletar",
				value: true,
				visible: true,
				closeModal: true
			}
		}
	};
}

/**
 * obj = {
 * 		confirm_message: string
 * 		ajax_url: string
 * 		ajax_data: string
 * 		ajax_success: {
 * 			success_message: string
 * 			error_message: string
 * 			datatable: array[datatable objects]
 * 		}
 * 		ajax_error:
 * 			error_message: string
 * }
 * */
function swal_confirm_delete(obj){
	swal(
		swal_confirm_obj(obj.confirm_message)
	).then(function(result){
		if(result){
			$.ajax({
				type: "POST",
				url: BASE_URL + obj.ajax_url,
				dataType: "json",
				data: obj.ajax_data,
				success: function(data){
					if(data.status){
						if(data.status == 1)
							swal_success(obj.ajax_success.success_message);
						else if(data.status == 2)
							swal_warning(data.warning_msgs.join(" "));
					}else{
						var message = (obj.ajax_success.error_message === null ? data.error_message : obj.ajax_success.error_message);
						swal_error(message);
					}

					$.each(obj.ajax_success.datatables, function(i, dt){
						dt.ajax.reload();
					});
				},
				error: function(data){
					swal_error(obj.ajax_error.error_message);
					console.log("ERROR!");
					console.log(data);
				}
			});
		}
	});
}

function exists(selector){
	return ($(selector).length)? true : false;
}

function get_cookie(cname){
	var name = cname + "=";
	var decodedCookie = decodeURIComponent(document.cookie);
	var ca = decodedCookie.split(";");
	for(var i = 0; i < ca.length; i++){
		var c = ca[i];
		while (c.charAt(0) == " "){
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0){
			return c.substring(name.length, c.length);
		}
	}
	return "";
}