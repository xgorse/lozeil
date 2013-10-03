$(document).ready(function() {
	$('body')
		.on("click", "tr", function() {
			var id = $(this).attr("id");
			$("#accounting_codes").find("."+id).toggle();
			if($(this).next().css("display") == "none"){
				$("#accounting_codes").find("tr[class^="+id+"]").hide();
			}
		})
})