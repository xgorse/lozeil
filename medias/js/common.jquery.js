$(document)
	.ready(function() {
		$("body")
			.on("click", "input.input-ajax-checkbox", function() {
				if ($(this).is(':checked') == false) {
					$(this).parent().hide();
					$(this).parent().parent().parent().children("input:first").show();
				} else {
					$(this).parent().parent().parent().find(".input-ajax-dynamic").hide();
				}
			})
			
			.on("keyup", "input.input-ajax", function(event) {
				if (event.keyCode == 27) {
					$(this).parent().find(".input-ajax-dynamic").hide();
				}
				else {
					input_ajax_input = $(this);
					clearTimeout(input_ajax_timer);
					input_ajax_timer = setTimeout("input_ajax_get()", 200);
				}
			})
			
			.on("click", "*", function () {
				$(this).find(".select-ajax-dynamic").hide();
				$(this).find(".input-ajax-dynamic").hide();
			})
			
			.find("tr.modified").delay('6000').queue(function(next){
				$(this).removeClass('modified');
			})
		
		$("#menu_actions_export").hide();
		
		$(".menu_actions_import_label").nextAll().hide();
		
		$(".menu_handle").on("click", function() {
			if ($(this).hasClass("hide")) {
				$(this).addClass("show").removeClass("hide");
				$(".menu_actions").slideDown(400);
			} else {
				$(this).addClass("hide").removeClass("show");
				$(".menu_actions").slideUp(400);
			}
				$("#menu_handle_hide, #menu_handle_show").toggle();
		})
		
		$("#menu_actions_export_label").on("click", function() {
			$("#menu_actions_export").toggle();
			return false;
		})
		$(".menu_actions_import_label, #menu_actions_other").on("click", function() {
			$(this).nextAll().toggle();
			return false;
		})
		
	var input_ajax_timer;
	var input_ajax_input;

	window.input_ajax_get = function() {
		var input = input_ajax_input;
		var input_dynamic = $(input).next();
		var input_static = $(input).next().next();
		$(input_dynamic).empty();
		$(input_dynamic).show();
		input.addClass("waiting");
		$.getJSON(
			input.attr("data-url"),
			{ method: "json", action: "search", name: input.val(), format: input.attr("data-format") },
			function(data){
				for (i in data) {
					var checkbox = $("<div><input type=\"checkbox\" value=\""+i+"\" name=\""+input.attr("data-name")+"\" class=\"input-ajax-checkbox\" /></div>");
					checkbox.append(data[i]);
					$(input_dynamic).append(checkbox);
					checkbox.click(function() {
						if ($(this).parent().attr("id") == input.attr("id")+'-dynamic') {
							$(this).detach();
							$(this).children("input").attr('checked', "checked");
							$(input_static).html($(this));
							$(input_dynamic).empty();
							input.hide();
						}
					});
				}
				input.removeClass("waiting");
			}
		);
	}
	})
	.keyup(function(e) {
	  if (e.keyCode == 27) {
		  if ($(".menu_handle").hasClass("show")) {
				$(".menu_handle").addClass("hide").removeClass("show");
				$(".menu_actions").slideUp(400, function() {
					$("#menu_handle_show").hide();
					$("#menu_handle_hide").show();
				});
			}
		$("#table_writings .operations input[type='text']").attr("type", "hidden");
		$(".insert_writings_form, .table_writings_comment_further_information").slideUp();
		$("#insert_writings_hide, #insert_writings_cancel").hide();
		$("#insert_writings_show").show();
		$(".extra_filter_writings_days").hide();
		$(".extra_filter_writings_days input").val('');
		$("#extra_filter_writings_value").val('').trigger("keyup");
		$("#table_edit_writings").slideUp(400, function() {
			$(".table_writings_form_modify").remove();
		})
	  }   
	});

function refresh_balance() {
	$.ajax({
		type: "POST",
		url : "index.php?content=timeline.ajax.php"		
	}).done(function (data) {
		$("#heading_timeline").html(data);
	});
	
	$.ajax({
		type: "POST",
		url : "index.php?content=balance.ajax.php"		
	}).done(function (data) {
			$('#menu_header_balance').html(data);
		}
	);
}