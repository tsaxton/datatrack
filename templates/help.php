<h2>Help Interpreting DataTrack Results</h2>
<h3>Most Recent Data</h3>
<h4>New Records</h4>
<p>New records are defined as being a record since the beginning of the data set. Some data sets may have older data that shows that it isn't a record for all time.</p>

<h4>Percent Increase</h4>
<p>Percent from year A to year B is defined as:<br/>
<pre>
Year B - Year A
---------------
    Year A
</pre></p>
<h3>Long-Term Trends</h3>
<p>In looking at long-term trends, we look at the big picture of how the data is moving. The way that DataTrack does this is through the use of <a target="_blank" href="http://en.wikipedia.org/wiki/Least_squares">least-squares regression modeling.</a> We do two separate sets of least-squares regression modeling to determine long term trends.</p>
<h4>Overall Trend</h4>
<p>In one set of observations, we consider the overall trend. For example, in our <a href="?id=display&dataset=1">CTA Ridership Data</a>, you may get an observation like <em><strong>Total Ridership</strong> has been trending downward at rate of -893,901.05.</em> This indicates an overall downward change, but may not reflect the overall trends if there was a large decrease followed by a smaller increase (which is true of the CTA data)</p>
<p>The rate of -893,901.05 is the slope of the <a target="_blank" href="http://www.stat.wmich.edu/s216/book/node126.html">best-fit line</a> which gives the straight line that most closely models the data.</p>
<p>Based on the trend of the CTA data, of a decrease followed by a smaller increase, we also decided to calculate a two-piece trend. We split the data into two sets of years (like 1989-1993 and 1993-2012) and calculate the regression lines for each data set. This provides us with a more accurate look at the behaviour of the data: <em>After trending downward from 1988-1993, Total Ridership trended upward.</em> We split the time interval dynamically, calculating several different sets of regression lines in order to determine the best change in the trend.</p>
<h3>Streaks</h3>
<p>A <em>streak</em> occurs when something increases or decreases in consecutive years. No change from one year to another does interrupt our streak count.</p>
<h3>Statistics</h3>
Each of the following statistical measures are calculated for various parts of our data.
<h4>Average</h4>
<p>Also known as the <em>mean</em>, this value is the sum of the data point values divided by the number of values.</p>
<h4>Standard Deviation</h4>
<p>Don't think about it too much...</p>
<h4>Median</h4>
<p>The central value in the data, when arranged from smallest to largest. In the case of an even number of data points, DataTrack chooses the left-of-center data point as the median.</p>
<h4>Max</h4>
<p>The largest data value.</p>
<h4>Min</h4>
<p>The smallest data value.</p>
<h4>Average Change</h4>
<p>The average of the change from one year to the next. Calculated as both raw numbers and as percents.</p>
