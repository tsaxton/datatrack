<div class="row">
    <div class="span12">
	<h2>CTA Data</h2>
    </div>
</div>
<script>
$(document).ready(function(){
  // Some simple loops to build up data arrays.
  var cosPoints = [];
  for (var i=0; i<2*Math.PI; i+=0.4){
    cosPoints.push([i, Math.cos(i)]);
  }
    
  var sinPoints = [];
  for (var i=0; i<2*Math.PI; i+=0.4){
     sinPoints.push([i, 2*Math.sin(i-.8)]);
  }
    
  var powPoints1 = [];
  for (var i=0; i<2*Math.PI; i+=0.4) {
      powPoints1.push([i, 2.5 + Math.pow(i/4, 2)]);
  }
    
  var powPoints2 = [];
  for (var i=0; i<2*Math.PI; i+=0.4) {
      powPoints2.push([i, -2.5 - Math.pow(i/4, 2)]);
  }
 
  var plot3 = $.jqplot('chart3', [cosPoints, sinPoints, powPoints1, powPoints2],
    {
      title:'Sample Chart',
      // Series options are specified as an array of objects, one object
      // for each series.
      series:[
          {
            // Change our line width and use a diamond shaped marker.
            lineWidth:2,
            markerOptions: { style:'dimaond' }
          },
          {
            // Don't show a line, just show markers.
            // Make the markers 7 pixels with an 'x' style
            showLine:false,
            markerOptions: { size: 7, style:"x" }
          },
          {
            // Use (open) circlular markers.
            markerOptions: { style:"circle" }
          },
          {
            // Use a thicker, 5 pixel line and 10 pixel
            // filled square markers.
            lineWidth:5,
            markerOptions: { style:"filledSquare", size:10 }
          }
      ]
    }
  );
    
});
</script>
<div class="row">
    <div class="span6">
	<h3>Key Observations</h3>
	<ul>
	    <li>The chart on the right will graphically display the data.</li>
	    <li>We also hope to be able to make it customizable, to control timeline and which lines to show.</li>
	    <li>Perhaps make it downloadable also?</li>
	    <li>This section will have all of the key observations that our system finds in the data.</li>
	    <li>Below is the chart of changes over time and other figures.</li>
	</ul>
    </div>

    <div class="span5 offset1 chart" id="chart3">
    </div>
</div>
<div class="row">
    <div class="span12">
	<table width="100%" border="3">
	    <tr>
		<th>Year</th>
		<th>Total</th>
		<th>Bus</th>
		<th>Rail</th>
		<th>1 yr. change</th>
		<th>5 yr. change</th>
		<th>10 yr. change</th>
	    </tr>
	    <tr>
		<td>1992</td>
		<td>1234567</td>
		<td>123456</td>
		<td>123456</td>
		<td>-8.65%</td>
		<td>1.23%</td>
		<td>2.43%</td>
	    </tr>
	    <tr>
		<td>1993</td>
		<td>1234567</td>
		<td>123456</td>
		<td>123456</td>
		<td>-8.65%</td>
		<td>1.23%</td>
		<td>2.43%</td>
	    </tr>
	    <tr>
		<td>1994</td>
		<td>1234567</td>
		<td>123456</td>
		<td>123456</td>
		<td>-8.65%</td>
		<td>1.23%</td>
		<td>2.43%</td>
	    </tr>
	    <tr>
		<td>1995</td>
		<td>1234567</td>
		<td>123456</td>
		<td>123456</td>
		<td>-8.65%</td>
		<td>1.23%</td>
		<td>2.43%</td>
	    </tr>
	</table>
    </div>
</div>
