{!! HTML::script('js/plugins/Chart.js') !!}

 <script>
  //$(function () {           
  $(document).ready(function(){      
    /* ChartJS
     * -------
     * Here we will create a few charts using ChartJS
     */
    //--------------
    //- A CHART -
    //--------------
    // Get context with jQuery - using jQuery's .get() method.
    if($( "#areaChart" ).length > 0 ) {  
      var areaChartCanvas = $("#areaChart").get(0).getContext("2d");
      // This will get the first returned node in the jQuery collection.
      var areaChart = new Chart(areaChartCanvas);
      var areaChartData = {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [
          {
            label: "Charges",
            fillColor: "rgba(210, 214, 222, 1)",
            strokeColor: "rgba(210, 214, 222, 1)",
            pointColor: "rgba(210, 214, 222, 1)",
            pointStrokeColor: "#c1c7d1",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(220,220,220,1)",
            data:<?php if(!empty($chargeBar)){echo $chargeBar;} else {echo "0";}?>
          },
          {
            label: "Collections",
            fillColor: "rgba(60,141,188,0.9)",
            strokeColor: "rgba(60,141,188,0.8)",
            pointColor: "#3b8bba",
            pointStrokeColor: "rgba(60,141,188,1)",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(60,141,188,1)",
            data:<?php if(!empty($paymentArray)) {echo $paymentArray;} else {echo "0";}?>
          }
        ]
      };

      var areaChartOptions = {
        //Boolean - If we should show the scale at all
        showScale: true,
        //Boolean - Whether grid lines are shown across the chart
        scaleShowGridLines: false,
        //String - Colour of the grid lines
        scaleGridLineColor: "rgba(0,0,0,.05)",
        //Number - Width of the grid lines
        scaleGridLineWidth: 1,
        //Boolean - Whether to show horizontal lines (except X axis)
        scaleShowHorizontalLines: true,
        //Boolean - Whether to show vertical lines (except Y axis)
        scaleShowVerticalLines: true,
        //Boolean - Whether the line is curved between points
        bezierCurve: true,
        //Number - Tension of the bezier curve between points
        bezierCurveTension: 0.3,
        //Boolean - Whether to show a dot for each point
        pointDot: false,
        //Number - Radius of each point dot in pixels
        pointDotRadius: 4,
        //Number - Pixel width of point dot stroke
        pointDotStrokeWidth: 1,
        //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
        pointHitDetectionRadius: 20,
        //Boolean - Whether to show a stroke for datasets
        datasetStroke: true,
        //Number - Pixel width of dataset stroke
        datasetStrokeWidth: 2,
        //Boolean - Whether to fill the dataset with a color
        datasetFill: true,
        //String - A legend template
        legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
        //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
        maintainAspectRatio: true,
        //Boolean - whether to make the chart responsive to window resizing
        responsive: true
      };

      //Create the line chart
      areaChart.Line(areaChartData, areaChartOptions);      
    }  
    //-------------
    //- PIE CHART -
    //-------------
    // Get context with jQuery - using jQuery's .get() method.
    if($( "#pieChart" ).length > 0 ) {  

      var pieChartCanvas = $("#pieChart").get(0).getContext("2d");        
      var pieChart = new Chart(pieChartCanvas);
      var PieData = [
        {
          value: <?php if(!empty($AgingPiePercentValue0_30)){echo $AgingPiePercentValue0_30;} else{ echo "0";}?>,
          color: "#f4c2f6",
          highlight: "#f4c2f6",
          label: "0 - 30"
        },
        {
          value: <?php if(!empty($AgingPiePercentValue31_60)){echo $AgingPiePercentValue31_60;} else{ echo "0";}?>,
          color: "#9cd3f4",
          highlight: "#9cd3f4",
          label: "31 - 60"
        },
        {
          value: <?php if(!empty($AgingPiePercentValue61_90)){echo $AgingPiePercentValue61_90;} else{ echo "0";}?>,
          color: "#f6a3c2",
          highlight: "#f6a3c2",
          label: "61 - 90"
        },
        {
          value: <?php if(!empty($AgingPiePercentValue91_120)){echo $AgingPiePercentValue91_120;} else{ echo "0";}?>,
          color: "#a3f6d2",
          highlight: "#a3f6d2",
          label: "91 -120"
        },
        {
          value: <?php if(!empty($AgingPiePercentValue121_150)){echo $AgingPiePercentValue121_150;} else{ echo "0";}?>,
          color: "#adbaed",
          highlight: "#adbaed",
          label: "121 - 150"
        },
        {
          value: <?php if(!empty($AgingPiePercentValue151_180)){echo $AgingPiePercentValue151_180;} else{ echo "0";}?>,
          color: "#f8d1ad",
          highlight: "#f8d1ad",
          label: "151 - 180"
        },          
        {
          value: <?php if(!empty($AgingPiePercentValue180_above)){echo $AgingPiePercentValue180_above;} else{ echo "0";}?>,
          color: "#b5b6b7",
          highlight: "#b5b6b7",
          label: "180+"
        }
      ];

      var pieOptions = {
        //Boolean - Whether we should show a stroke on each segment
        segmentShowStroke: true,
        //String - The colour of each segment stroke
        segmentStrokeColor: "#fff",
        //Number - The width of each segment stroke
        segmentStrokeWidth: 2,
        //Number - The percentage of the chart that we cut out of the middle
        percentageInnerCutout: 20, // This is 0 for Pie charts
        //Number - Amount of animation steps
        animationSteps: 100,
        //String - Animation easing effect
        animationEasing: "easeOutBounce",
        //Boolean - Whether we animate the rotation of the Doughnut
        animateRotate: true,
        //Boolean - Whether we animate scaling the Doughnut from the centre
        animateScale: false,
        //Boolean - whether to make the chart responsive to window resizing
        responsive: true,
        // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
        maintainAspectRatio: false,
        //String - A legend template
        legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
      };
      //Create pie or douhnut chart
      // You can switch between pie and douhnut using the method below.
      pieChart.Doughnut(PieData, pieOptions);
    }
    //-------------
    //- BAR CHART -
    //-------------

    if($( "#barChart" ).length > 0 ) {  
      var barChartCanvas = $("#barChart").get(0).getContext("2d");
      var barChart = new Chart(barChartCanvas);
      var barChartData = areaChartData;
      barChartData.datasets[1].fillColor = "#87b6d2";
      barChartData.datasets[1].strokeColor = "#87b6d2";
      barChartData.datasets[1].pointColor = "#87b6d2";
      var barChartOptions = {
        //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
        scaleBeginAtZero: true,
        //Boolean - Whether grid lines are shown across the chart
        scaleShowGridLines: true,
        //String - Colour of the grid lines
        scaleGridLineColor: "rgba(0,0,0,.05)",
        //Number - Width of the grid lines
        scaleGridLineWidth: 1,
        //Boolean - Whether to show horizontal lines (except X axis)
        scaleShowHorizontalLines: true,
        //Boolean - Whether to show vertical lines (except Y axis)
        scaleShowVerticalLines: true,
        //Boolean - If there is a stroke on each bar
        barShowStroke: true,
        //Number - Pixel width of the bar stroke
        barStrokeWidth: 2,
        //Number - Spacing between each of the X value sets
        barValueSpacing: 5,
        //Number - Spacing between data sets within X values
        barDatasetSpacing: 1,
        //String - A legend template
        legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
        //Boolean - whether to make the chart responsive
        responsive: true,
        maintainAspectRatio: true
      };
      barChartOptions.datasetFill = false;
      barChart.Bar(barChartData, barChartOptions);
    }          
  });
</script>