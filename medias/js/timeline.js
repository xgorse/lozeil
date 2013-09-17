window.onload = function () {
	var width = $(".cubism_width li").text();

	var context = cubism.context()
		.serverDelay(0)
		.clientDelay(0)
		.step(1e8)
		.size(width)
		.stop();

	var foo = random("")
	d3.select("#cubismtimeline").call(function(div) {
	  div.datum(foo);
	  
	  div.append("div")
      .attr("class", "axis")
      .call(context.axis().orient("top"));

		div.append("div")
			.attr("class", "horizon")
			.call(context.horizon()
			.height(120)
			.colors(["#bdd7e7","#bae4b3"])
			.extent([-10, 10])
		);

	});

	// On mousemove, reposition the chart values to match the rule.
	context.on("focus", function(i) {
		d3.selectAll(".value").style("right", i == null ? null : context.size() - (i + 90) + "px");
	});

	function random(name) {
	  var value = 0,
		  values = [],
		  i = 0,
		  last;
		  $(".cubism_data li").each(function () {
			  values.push($(this).text());
		  })
		  
	  return context.metric(function(start, stop, step, callback) {
		start = +start, stop = +stop;
		if (isNaN(last)) last = start;
		console.log(last, stop);
		callback(null, values = values.slice((start - stop) / step));
	  }, name);
	}
}