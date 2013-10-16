$(document).ready(function() {
	var timer;
	make_drag_and_drop();
	$('.layout_status').slideDown(400);
	timerstatus = setTimeout(function(){
		$('.layout_status').slideUp(200);
	},3000);
	$(".extra_filter_item input[type=\"checkbox\"]").each(function () {
		if (this.checked) {
			$(this).closest(".extra_filter_item").show();
			$(".input-date").closest(".extra_filter_item").show();
		}
	})
	$("body")
		.on("click", "#insert_writings_show", function() {
			$(".insert_writings_form").slideDown(1, function() {
				$("html, body").animate({ scrollTop: $(document).height() }, "slow");
			});
			$(this).hide();
			$("#insert_writings_hide, #insert_writings_cancel").show();
		})
		
		.on("click", "#insert_writings_hide", function() {
			$(".insert_writings_form").slideUp();
			$(this).hide();
			$("#insert_writings_show").show();
		})
		
		.on("click", "#table_writings .modify a", function() {
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
		
		.on("submit", "form[name=\"delete_writing_attachment\"]", function() {
			$.post(
				"index.php?content=writings.ajax.php",
				$(this).serialize(),
				function(data) {
					var result = jQuery.parseJSON(data);
					$(".manage_writing_attachment").replaceWith(result.link);
					show_status(result.status);
				}
			);
			return false;
		})
		
		.on("submit", "form[name=\"table_edit_writings_form\"]", function() {
			$.post(
				"index.php?content=writings.ajax.php",
				$(this).serialize(),
				function(data) {
					var result = jQuery.parseJSON(data);
					$('#table_writings table').html(result.table);
					refresh_balance();
					make_drag_and_drop();
					show_status(result.status);
				}
			);
			return false;
		})
		
		.on("submit", "form[name=\"table_writings_duplicate\"]", function() {
			$.post(
				"index.php?content=writings.ajax.php",
				$(this).serialize(),
				function(data) {
					refresh_balance();
					var result = jQuery.parseJSON(data);
					$('#table_writings table').html(result.table);
					make_drag_and_drop();
					show_status(result.status);
				}
			);
			return false;
		})
		
		.on("submit", "form[name=\"table_writings_forward\"]", function() {
			$.post(
				"index.php?content=writings.ajax.php",
				$(this).serialize(),
				function(data) {
					refresh_balance();
					var result = jQuery.parseJSON(data);
					$('#table_writings table').html(result.table);
					make_drag_and_drop();
					show_status(result.status);
				}
			);
			return false;
		})
		
		.on("submit", "form[name=\"table_writings_delete\"]", function() {
			$.post(
				"index.php?content=writings.ajax.php",
				$(this).serialize(),
				function(data) {
					refresh_balance();
					var result = jQuery.parseJSON(data);
					$('#table_writings table').html(result.table);
					make_drag_and_drop();
					show_status(result.status);
				}
			);
			return false;
		})
		
		.on("submit", "form[name=\"insert_writings_form\"]", function() {
			$.post(
				"index.php?content=writings.ajax.php",
				$(this).serialize(),
				function(data) {
					refresh_balance();
					reload_insert_form();
					var result = jQuery.parseJSON(data);
					$('#table_writings table').html(result.table);
					make_drag_and_drop();
					show_status(result.status);
				}
			);
			return false;
		})
		
		.on("click", "#table_writings .table_writings_comment", function() {
			$(".table_writings_comment_further_information").slideUp();
			var cell = $(this).find(".table_writings_comment_further_information");
			if (cell.css("display") == "none") {
				cell.slideDown();
			}
			return false;
		})
		
		.on("click", "input#table_writings_split_submit, input#table_writings_duplicate_submit, input#table_writings_forward_submit", function(event) {
			if ($(this).next().val() == "") {
				event.preventDefault();
				if ($(this).next().attr("type") == "hidden") {
					var next = "text";
				} else {
					var next = "hidden";
				}
			$("input#table_writings_split_submit, input#table_writings_duplicate_submit, input#table_writings_forward_submit").next().attr("type", "hidden");
					$(this).next().attr("type", next);
			}
		})
		
		.on("click", "#table_writings .sort", function() {
			var order_col_name = $(this).attr('id');
			$.post(
				"index.php?content=writings.ajax.php",
				{action: "sort", order_col_name: order_col_name},
				function(data) {
					var result = jQuery.parseJSON(data);
					$('#table_writings table').html(result.table);
					make_drag_and_drop();
					show_status(result.status);
				}
			)
		})
		
		.on("submit", "form[name=\"table_writings_split\"]", function() {
			var row = $(this).parent().parent().parent();
			var id = row.attr("id").substr(6);
			var amount = $(this).find("#table_writings_split_amount").val();
			$.post(
				"index.php?content=writings.ajax.php",
				{action: "split", table_writings_split_id: id, table_writings_split_amount: amount},
				function(data) {
					var result = jQuery.parseJSON(data);
					$('#table_writings table').html(result.table);
					make_drag_and_drop();
					show_status(result.status);
				}
			)
		return false;
		})
		
		.on("submit", "form[name=\"extra_cancel_writings_form\"]", function() {
			$.post(
				"index.php?content=writings.ajax.php",
				$(this).serialize(),
				function(data) {
					refresh_balance();
					var result = jQuery.parseJSON(data);
					$('#table_writings table').html(result.table);
					make_drag_and_drop();
					show_status(result.status);
				}
			)
		return false;
		})
		
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

		.on("keyup change", "form[name=\"extra_filter_writings_form\"]", function() {
			var input = $(this);
			clearTimeout(timer);
			timer = setTimeout(function() {
				$.post(
					"index.php?content=writings.ajax.php",
					input.serialize(),
					function(data){
						var result = jQuery.parseJSON(data);
						$('#table_writings table').html(result.table);
						make_drag_and_drop();
						show_status(result.status);
					}
				);
			}, 200);
		})

		.on("submit", "form[name=\"extra_filter_writings_form\"]", function() {
			var tohide = $(this).find(".extra_filter_item input[type=\"text\"]");
			tohide.each( function() {
				if ($(this).val() == "" && $(this).attr("class") != "input-date") {
					$(this).closest(".extra_filter_item").hide();
				}
			})
			$(".extra_filter_item select").each(function () {
				if ($(this).val() == 0) {
					$(this).closest(".extra_filter_item").hide();
				}
			})
			$(".extra_filter_item input[type=\"checkbox\"]").each(function () {
				if (!this.checked) {
					$(this).closest(".extra_filter_item").hide();
				}
			})
			if ($(".extra_filter_item textarea").val() == "") {
				$(".extra_filter_item textarea").closest(".extra_filter_item").hide();
			}
			return false;
		})
		
		.on("mouseenter", "#table_writings tr", function() {
			$(this).find(".operations > div").css("display", "inline-block");
		})
		
		.on("mouseleave", "#table_writings tr", function() {
			$(this).find(".operations > div").hide();
		})
		
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
		
		.on("click", "#extra_filter_writings_toggle", function() {
			$(".extra_filter_writings_days input").each(function() {
				$(this).keyup();
			})
			if ($(".extra_filter_item:first").css("display") == "none") {
				$(".extra_filter_item").slideDown(200);
			} else {
				$(".extra_filter_item").slideUp(200);
			}
		})
		
		.on("click", "#checkbox_all_up, #checkbox_all_down", function() {
			var $checkboxes = $("#table_writings, #select_modify_writings").find(':checkbox');
			if (this.checked) {
				$checkboxes.each(function() {
					$(this)[0].checked = true
				});
			} else {
				$checkboxes.each(function() {
					$(this)[0].checked = false
				});
			}
		})
});

function make_drag_and_drop() {
	$(".dropzone-input").remove();
	$(".droppable").each(function() {
		$(this).dropzone({
			init: function() {
				this.on("success", function(object, data) {
					var result = jQuery.parseJSON(data);
					show_status(result.status);
				})
			},
			url: "index.php?content=writings.ajax.php",
			paramName: $(this).attr('id')
		})
	});
	$("#table_writings tr.draggable").droppable({
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
					var result = jQuery.parseJSON(data);
					$('#table_writings table').html(result.table);
					make_drag_and_drop();
					show_status(result.status);
				}
			);
		}
	});

	var table_header = $(".table_header").html();
	$("#table_writings tr.draggable").draggable({
		cursor: "pointer",
		stack: "tr",
		helper: function(event) {
			var html = $(this).html();
			var id = $(this).attr('id');
			return "<div class=\"dragged\"><table><tr id=\""+id+"\">"+html+"</tr><tr id=\"table_header_dragged\">"+table_header+"</tr></table></div>";
		}
	});
}

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

function reload_select_modify_writings() {
	$.ajax({
		type: "POST",
		url : "index.php?content=writings.ajax.php",
		data : {action: "reload_select_modify_writings"}
	}).done(function (data2) {
		$("#select_modify_writings").replaceWith(data2);
	});
}

function confirm_option(text) {
	var select = $("#options_modify_writings");
	
	if (select.val() == 'delete') {
		if(confirm(text)) {
			var ids = get_checked_values();
			$.post(
				"index.php?content=writings.ajax.php",
				{action: "form_options", option : $(select).val(), ids : ids},
				function(data) {
					refresh_balance();
					var result = jQuery.parseJSON(data);
					reload_select_modify_writings();
					$('#table_writings table').html(result.table);
					show_status(result.status);
				}
			);
			return false;
		} else {
			return false;
		}
	}
	
	$.post(
		"index.php?content=writings.ajax.php",
		{action: "form_options", option : $(select).val()},
		function(data) {
			$('#form_modify_writings').html(data);
		}
	);
	return false;
}

function get_checked_values() {
	var $checkboxes = $("#table_writings").find('.table_checkbox:checkbox');
	var ids = [];
	$checkboxes.each(function() {
		if ($(this)[0].checked) {
			ids.push($(this).val());
		}
	});
	return JSON.stringify(ids);
}

function confirm_modify(text) {
	if(confirm(text)) {
		var select = $("#options_modify_writings").val();
		var ids = get_checked_values();
		var serialized = $("form[name=\"writings_modify_form\"]").serialize();
		serialized += "&action=writings_modify&ids=" + ids +"&operation=" + select;
		$.post(
			"index.php?content=writings.ajax.php",
			serialized,
			function(data) {
				reload_select_modify_writings();
				refresh_balance();
				var result = jQuery.parseJSON(data);
				$('#table_writings table').html(result.table);
				make_drag_and_drop();
				show_status(result.status);
			}
		);
	}
	return false;
}

$(document).ajaxStop(function() {
	changeColorLine();
})

var timercolor;
function changeColorLine(){
	clearTimeout(timercolor);
	timercolor = setTimeout(function(){
		$('#table_writings tr.modified').removeClass('modified');
	},6000);
};


var timerstatus;
function show_status(status) {
	$('.layout_status').slideUp(200, function() {
		$(this).empty().html(status);
	})
	clearTimeout(timerstatus);
	$('.layout_status').slideDown(400);
	timerstatus = setTimeout(function(){
		$('.layout_status').slideUp(200);
	},3000);
}