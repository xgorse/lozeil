window.onload = function () {
	var width = $(".cubism_width li").text();

	var context = cubism.context()
		.serverDelay(0)
		.clientDelay(0)
		.step(1e8)
		.size(width)
		.stop();

	var foo1 = random1("")
	d3.select("#cubismtimeline").call(function(div) {
	  div.datum(foo1);
	  
	  div.append("div")
      .attr("class", "axis")
      .call(context.axis().orient("top"));

		div.append("div")
			.attr("class", "horizon_positive")
			.call(context.horizon()
			.height(50)
			.colors(["#ff0000", "#ff4040", "#ff8080", "#ffbfbf", "#bae4b3", "#74c476", "#31a354", "#006d2c"])
		);
	});

	// On mousemove, reposition the chart values to match the rule.
	context.on("focus", function(i) {
		if($("#cubismtimeline .value").eq(0).text() != "NaN") {
			d3.selectAll("#cubismtimeline .horizon_positive .value").style("display", "block");
			d3.selectAll("#cubismtimeline .horizon_positive .value").style("right", i == null ? null : context.size() - (i + 90) + "px");
			d3.selectAll("#cubismtimeline .horizon_negative .value").style("display", "none");
		} else if ($("#cubismtimeline .value").eq(1).text() != "NaN") {
			d3.selectAll("#cubismtimeline .horizon_negative .value").style("display", "block");
			d3.selectAll("#cubismtimeline .horizon_negative .value").style("right", i == null ? null : context.size() - (i) + "px");
			d3.selectAll("#cubismtimeline .horizon_positive .value").style("display", "none");
		}
		//d3.selectAll("#cubismtimeline .value").style("right", i == null ? null : context.size() - (i + 90) + "px");
	});

	function random1(name) {
	  var positive_values = [],
		  negative_values = [],
		  last;
		  $(".cubism_data li").each(function () {
			positive_values.push($(this).text());
		  })
	  return context.metric(function(start, stop, step, callback) {
		start = +start, stop = +stop;
		if (isNaN(last)) last = start;
		callback(null, positive_values = positive_values.slice((start - stop) / step));
	  }, name);
	}
}