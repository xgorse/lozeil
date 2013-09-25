$(document)
	.ready(function() {
		$("body").find("tr.modified").delay('6000').queue(function(next){
			$(this).removeClass('modified');
		})
		
		$("#menu_actions_export").hide();
		
		$("#menu_actions_import_label").nextAll().hide();
		
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
		$("#menu_actions_import_label").on("click", function() {
			$(this).nextAll().toggle();
			return false;
		})
	})
	.keyup(function(e) {
	  if (e.keyCode == 27) {
		  if ($(".menu_handle").hasClass("show")) {
				$(".more").addClass("hide").removeClass("show");
				$(".menu_actions").slideUp(400, function() {
					$("#menu_handle_show").hide();
					$("#menu_handle_hide").show();
				});
			}
		$("#table_writings .operations input[type='text']").attr("type", "hidden");
		$(".insert_writings_form, .table_writings_comment_further_information").slideUp();
		$("#insert_writings_hide, #insert_writings_cancel").hide();
		$("#insert_writings_show").show();
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