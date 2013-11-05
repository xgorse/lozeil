$(document).ready(function() {
	$("body")
		.on("submit", "form[name=\"sources_id\"]", function() {
			$.post(
				"index.php?content=sources.ajax.php",
				$(this).serialize(),
				function(data) {
					var result = jQuery.parseJSON(data);
					$("#edit_sources").replaceWith(result.table);
					show_status(result.status);
				}
			);
			return false;
		})
});