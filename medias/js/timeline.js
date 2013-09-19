window.onload = function () {
	var width = $("#cubism_width").text();
	var height = $("#cubism_height").text();
	var start_year = $("#cubism_start_year").text();
	var link = [];
	
	 $(".cubism_link").each(function() {
		 link.push($(this).text());
	 });
	var context = cubism.context()
		.serverDelay(Date.now() - new Date(parseInt(start_year) + 1, 0, 1, 0,0 ,0 ,0))
		.clientDelay(0)
		.step(1000*60*60*8)
		.size(width)
		.stop();

	var data = get_data("");
	var scale = d3.scale.linear();
	
	d3.select("#cubismtimeline").call(function(div) {
		div.datum(data);
		div.append("div")
			.attr("class", "axis")
			.call(context.axis()
				.orient("top")
				.tickFormat(d3.time.format("%m/%Y"))
			);
				
		div.append("div")
			.attr("class", "horizon_positive")
			.call(context.horizon()
			.height(height)
			.colors(["#08519c", "#3182bd", "#6baed6", "#bdd7e7", "#bae4b3", "#74c476", "#31a354", "#006d2c"])
		);
			
		div.append("div")
			.attr("class", "rule")
			.call(context.rule());
	});
	
	$("#cubismtimeline g .tick").on("click", function() {
		window.location = link[$(this).index("#cubismtimeline g .tick")];
	});
	
	$("text").each(function() {
		$(this).attr("x", 45)
	})
			
	context.on("focus", function(i) {
		d3.selectAll("#cubismtimeline .value").style("right", i == null ? null : context.size() - i + "px");
	});

	function get_data(name) {
		var values = []
		$(".cubism_data").each(function () {
			var val = $(this).text();
			if (val === "0") {
				val = null;
			}
			values.push(val);
		})
		return context.metric(function(start, stop, step, callback) {
			callback(null, values);
		}, name);
	}
}
