$(document).ready(function() {
	make_drag_and_drop();
	$("body")
		//Edition des enregistrements
		.on("click", ".modify a", function() {
			var row = $(this).parent().parent().parent();
			var id = row.attr("id").substr(6);
			if (row.next().hasClass("table_writings_form_modify")) {
				$("#table_edit_writings").slideUp(400, function() {
					$(".table_writings_form_modify").remove();
				})
			} else {
				$.post(
					"index.php?content=writings.ajax.php",
					{action: "form_edit", id: id},
					function(data) {
						$(".table_writings_form_modify").remove();
						$(data).insertAfter(row);
						$("#table_edit_writings").slideDown();
						$("html, body").animate({ scrollTop: $("#table_"+id).offset().top }, "slow");
					}
				);
			}
			return false;
		})
		.on("click", "#table_edit_writings_cancel", function() {
			$("#table_edit_writings").slideUp(400, function() {
				$(".table_writings_form_modify").remove();
			})
			return false;
		})
		
		.on("submit", "form[name=\"table_edit_writings_form\"]", function() {
			$.post(
				"index.php?content=writings.ajax.php",
				$(this).serialize(),
				function(data) {
					refresh_balance();
					$("#table_edit_writings").slideUp(400, function() {
						$('#table_writings table').html(data);
					})
				}
			);
			return false;
		})
		
		//Duplicate enregistrements
		.on("submit", "form[name=\"table_writings_duplicate\"]", function() {
			$.post(
				"index.php?content=writings.ajax.php",
				$(this).serialize(),
				function(data) {
					refresh_balance();
					$('#table_writings table').html(data);
				}
			);
			return false;
		})
		
		//Forward enregistrements
		.on("submit", "form[name=\"table_writings_forward\"]", function() {
			$.post(
				"index.php?content=writings.ajax.php",
				$(this).serialize(),
				function(data) {
					refresh_balance();
					$('#table_writings table').html(data);
				}
			);
			return false;
		})
		
		//Forward enregistrements
		.on("submit", "form[name=\"table_writings_delete\"]", function() {
			$.post(
				"index.php?content=writings.ajax.php",
				$(this).serialize(),
				function(data) {
					refresh_balance();
					$('#table_writings table').html(data);
				}
			);
			return false;
		})
		
		//Insert enregistrements
		.on("submit", "form[name=\"insert_writings_form\"]", function() {
			$.post(
				"index.php?content=writings.ajax.php",
				$(this).serialize(),
				function(data) {
					refresh_balance();
					reload_insert_form();
					$('#table_writings table').html(data);
				}
			);
			return false;
		})
		//Toggle informations supplémentaires
		.on("click", ".table_writings_comment", function() {
			$(".table_writings_comment_further_information").slideUp();
			var cell = $(this).find(".table_writings_comment_further_information");
			if (cell.css("display") == "none") {
				cell.slideDown();
			}
			return false;
		})
		//Toggle input split & duplicate
		.on("click", "input#table_writings_split_submit, input#table_writings_duplicate_submit, input#table_writings_forward_submit", function() {
			var next = "";
			if ($(this).next().val() == "") {
				event.preventDefault();
				if ($(this).next().attr("type") == "hidden") {
					next = "text";
				} else {
					next = "hidden";
				}
			$("input#table_writings_split_submit, input#table_writings_duplicate_submit, input#table_writings_forward_submit").next().attr("type", "hidden");
					$(this).next().attr("type", next);
			}
		})
		//Sort lines
		.on("click", ".sort", function() {
			var order_col_name = $(this).attr('id');
			$.post(
				"index.php?content=writings.ajax.php",
				{action: "sort", order_col_name: order_col_name},
				function(data) {
					$('#table_writings table').html(data);
				}
			)
		})
		// Split lines
		.on("submit", "form[name=\"table_writings_split\"]", function() {
			var row = $(this).parent().parent().parent();
			var id = row.attr("id").substr(6);
			var amount = $(this).find("#table_writings_split_amount").val();
			$.post(
				"index.php?content=writings.ajax.php",
				{action: "split", table_writings_split_id: id, table_writings_split_amount: amount},
				function(data) {
					$('#table_writings table').html(data);
				}
			)
		return false;
		})
		//Filter input
		.on("change", "input#amount_excl_vat", function() {
			$(this).val($(this).val().replace(",", "."));
			var amount_inc_vat = Math.round($(this).val() * (($("input#vat").val()/100 +1))*1000000)/1000000;
			$("input#amount_inc_vat").val(amount_inc_vat);
		})

		.on("change", "input#amount_inc_vat", function() {
			$(this).val($(this).val().replace(",", "."));
			var amount_excl_vat = Math.round($(this).val() / (($("input#vat").val()/100 +1))*1000000)/1000000;
			$("input#amount_excl_vat").val(amount_excl_vat);
		})

		.on("change", "input#vat", function() {
			$(this).val($(this).val().replace(",", "."));
			var amount_excl_vat = Math.round($("input#amount_inc_vat").val() / (($(this).val()/100 +1))*1000000)/1000000;
			$("input#amount_excl_vat").val(amount_excl_vat);
		})

		.on("keyup", "form[name=\"extra_filter_writings_form\"]", function() {
			$.post(
				"index.php?content=writings.ajax.php",
				$(this).serialize(),
				function(data){
					$('#table_writings table').html(data);
				}
			);
		})
		
		//Affichage des opérations
		.on("mouseenter", "tr", function() {
			$(this).find(".operations > div").css("display", "inline-block");
		})
		
		.on("mouseleave", "tr", function() {
			$(this).find(".operations > div").hide();
		})
		
		//Chargement automatique de la TVA par default
		.on("change", "select[name='categories_id']", function() {
			var form = $(this);
			$.post(
				"index.php?content=categories.ajax.php",
				{ method: "json", action: "filter", value: $(this).val() },
				function(data){
					form.parent().parent().parent().find("#vat").val(data).change();
				}
			);
		})
		
		//Toggle dates pour le filtre
		.on("click", "#extra_filter_writings_toggle", function() {
			$(".extra_filter_writings_days input").each(function() {
				$(this).val('');
				$(this).keyup();
			})
			$(".extra_filter_writings_days").toggle();
		})
});

function make_drag_and_drop() {
	$("tr.draggable").droppable({
		tolerance : "pointer",
		over: function() {
			$(this).removeClass('out').addClass('over');
        },
        out: function() {
			$(this).removeClass('over').addClass('out');
        },
        drop: function() {
        	var writing_from = $(".ui-draggable-dragging tr").attr('id').substr(6);
			var writing_into = $(this).attr('id').substr(6);
			$.post(
				"index.php?content=writings.ajax.php",
				{action: "merge", writing_from: writing_from, writing_into: writing_into},
				function(data) {
					$('#table_writings table tbody').remove();
					refresh_balance();
					$('#table_writings table').html(data);
				}
			);
		}
	});

	var table_header = $(".table_header").html();
	$("tr.draggable").draggable({
		cursor: "pointer",
		stack: "tr",
		helper: function(event) {
			var html = $(this).html();
			var id = $(this).attr('id');
			return "<div class=\"dragged\"><table><tr id=\""+id+"\">"+html+"</tr><tr id=\"table_header_dragged\">"+table_header+"</tr></table></div>";
		}
	});
}

$(document).ajaxStop(function() {
	make_drag_and_drop();
	$(this).find(".modified").delay('6000').queue(function(next){
		$(this).removeClass('modified');
	})
})


function reload_insert_form() {
	$.ajax({
		type: "POST",
		url : "index.php?content=writings.ajax.php",
		data : {action: "reload_insert_form"}
	}).done(function (data) {
		$(".insert_writings_form").slideUp(400, function() {
			$("#insert_writings").html(data);
		})
	});
}