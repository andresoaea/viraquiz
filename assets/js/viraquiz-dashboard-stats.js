(function( $ ) {

	"use strict";

	$(function() {

		var daily_stats = VIRAQUIZ_dashboard.daily_stats;
		var stats_generated_results = [];
		var stats_shares = [];
		var stats_dates = [];

		for( var i = 0; i < daily_stats.length; i++ ) {
			stats_generated_results.push( parseInt( daily_stats[i].generated_results ) );
			stats_shares.push( parseInt( daily_stats[i].shares ) );
			stats_dates.push( daily_stats[i].date );
		}

		var gender_stats = [];
		gender_stats.push( VIRAQUIZ_dashboard.genders.male );
		gender_stats.push( VIRAQUIZ_dashboard.genders.female );

		window.chartColors = {
			red: 'rgb(228, 6, 65)',
			orange: 'rgb(255, 159, 64)',
			yellow: 'rgb(255, 205, 86)',
			green: 'rgb(75, 192, 192)',
			blue: 'rgb(54, 162, 235)',
			purple: 'rgb(153, 102, 255)',
			grey: 'rgb(201, 203, 207)'
		};	


		var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
		"Jul", "Aug", "Sept", "Oct", "Nov", "Dec"
		];


		// Quizes stats chart
		function createConfig(position) {
			return {
				type: 'line',
				data: {
					labels: ["January", "February", "March", "April", "May", "June", "July"],
					labels: stats_dates,
					datasets: [{
						label: "Generated Results",
						borderColor: window.chartColors.blue,
						backgroundColor: window.chartColors.blue,
						data: stats_generated_results,
						fill: false,
					}, {
						label: "Facebook Shares",
						borderColor: window.chartColors.red,
						backgroundColor: window.chartColors.red,
						data: stats_shares,
						fill: false,
					}]
				},
				options: {
					responsive: true,
					title:{
						display: true,
						text: 'Quiz stats for the last 15 days'
					},
					tooltips: {
						position: position,
						mode: 'index',
						intersect: false,
					},
				}
			};
		}
		window.onload = function() {
			var container = document.querySelector('.container');
			['average'].forEach(function(position) {
				var div = document.createElement('div');
				div.classList.add('chart-container');
				var canvas = document.createElement('canvas');
				div.appendChild(canvas);
				container.appendChild(div);
				var ctx = canvas.getContext('2d');
				var config = createConfig(position);
				new Chart(ctx, config);
			})
		};


		// Genders chart 
		var randomScalingFactor = function() {
			return Math.round(Math.random() * 100);
		};
		var randomColorFactor = function() {
			return Math.round(Math.random() * 255);
		};
		var randomColor = function(opacity) {
			return 'rgba(' + randomColorFactor() + ',' + randomColorFactor() + ',' + randomColorFactor() + ',' + (opacity || '.3') + ')';
		};

		var config = {
			type: 'doughnut',
			data: {
				datasets: [{
					data: gender_stats,
					backgroundColor: [
					"#1A00FF",
					"#FF0000",
					"#FDB45C",
					"#949FB1",
					"#4D5360",
					],
					label: 'Expenditures'
				}],
				labels: [
				"Male", "Female"
				]
			},
			options: {
				responsive: true,
				legend: {
					position: 'bottom',
				},
				title: {
					display: true,
					text: 'Facebook Users Gender'
				},
				animation: {
					animateScale: true,
					animateRotate: true
				},
				tooltips: {
					callbacks: {
						label: function(tooltipItem, data) {
							var dataset = data.datasets[tooltipItem.datasetIndex];
							var total = dataset.data.reduce(function(previousValue, currentValue, currentIndex, array) {
								return previousValue + currentValue;
							});
							var currentValue = dataset.data[tooltipItem.index];
							var precentage = Math.floor(((currentValue/total) * 100)+0.5);         
							return precentage + "%";
						}
					}
				}
			}
		};


		var ctx = document.getElementById("chart-area").getContext("2d");
		window.myDoughnut = new Chart(ctx, config); {

		}



	});


})(jQuery);