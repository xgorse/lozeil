$(document).ready(function() {	
	$("body")
		.on("submit", "form[name=\"banks_id\"]", function() {
			$.post(
				"index.php?content=banks.ajax.php",
				$(this).serialize(),
				function(data) {
					var result = jQuery.parseJSON(data);
					$("#edit_banks").replaceWith(result.table);
					show_status(result.status);
				}
			);
			return false;
		})
});