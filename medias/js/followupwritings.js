function make_timeline() {
	var width = $("#cubism_width").text();
	var height = $("#cubism_height").text();
	var start_year = $("#cubism_start_year").text();
	var isleap_year = $("#cubism_isleap_year").text();
	var titles = [],
		link = []
	
	if (isleap_year.length == 0) {
		isleap_year = 1;
	} else {
		isleap_year = 0;
	}
	
	$(".cubism_data_title").each(function() {
		titles.push($(this).text());
	})
	
	 $(".cubism_link").each(function() {
		 link.push($(this).text());
	 });
	var context = cubism.context()
		.serverDelay(Date.now() - new Date(parseInt(start_year) + 1, 0, parseInt(isleap_year), 0,0 ,0 ,0))
		.clientDelay(0)
		.step(1000*60*60*8)
		.size(width)
		.stop();
	
	d3.select("#cubismtimeline").call(function(div) {
		$(".axis").remove();
		$(".horizon").remove();
		div.append("div")
			.attr("class", "axis")
			.call(context.axis()
				.orient("top")
				.tickFormat(d3.time.format("%m/%Y"))
			);
		var i = 0;
		$(".cubism_data").each(function () {
			i++
			var data = get_data("", i);
			div.datum(data);
			div.append("div")
				.attr("class", "horizon")
				.call(context.horizon()
				.height(height)
			.colors(["#B80000", "#D43333", "#F26F6F", "#FABEBE", "#bae4b3", "#74c476", "#31a354", "#006d2c"])
				.format(d3.format("r"))
				.title(titles[i - 1])
			);
		})
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

	function get_data(name, number) {
		var values = new Array();
		var num = 0;
		$(".cubism_data").each(function () {
			num++;
			$(this).find("li.cubism_data_row").each(function () {
				if (typeof values[num] === 'undefined') {
					values[num] = new Array();
				}
				var val = $(this).text();
				if (val === "0") {
					val = null;
				}
				values[num].push(val);
			})
		})
		return context.metric(function(start, stop, step, callback) {
			callback(null, values[number]);
		}, name);
	}
}

window.onload = function() {
	make_timeline();
}

$(document).ajaxStop(function() {
	make_timeline();
})