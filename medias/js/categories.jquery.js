$(document).ready(function() {
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