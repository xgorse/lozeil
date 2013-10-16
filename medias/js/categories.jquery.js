$(document).ready(function() {
	$('.layout_status').slideDown(400);
	timer =	setTimeout(function(){
		$('.layout_status').slideUp(200);
	},3000);
	
	$("body")
		.on("submit", "form[name=\"categories_id\"]", function() {
			$.post(
				"index.php?content=categories.ajax.php",
				$(this).serialize(),
				function(data) {
					var result = jQuery.parseJSON(data);
					$("#edit_categories").replaceWith(result.table);
					show_status(result.status);
				}
			);
			return false;
		})
});

var timer;
function show_status(status) {
	$('.layout_status').slideUp(200, function() {
		$(this).empty().html(status);
	})
	clearTimeout(timer);
	$('.layout_status').slideDown(400);
	timer =	setTimeout(function(){
		$('.layout_status').slideUp(200);
	},3000);
}