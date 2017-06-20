@extends($stats_layout)

@section('page-contents')
	<div id="pageViewsLine" class="chart no-padding"></div>
@stop

@section('page-secondary-contents')
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-sun-o"></i> @lang('tracker::tracker.page_views_by_country')</h3>
				</div>
				<div class="panel-body">
					<div id="pageViewsByCountry" style="height: 450px;"></div>
				</div>
			</div>
		</div>
	</div><!-- /.row -->

	@include('pragmarx/tracker::_summaryPiechart')
@stop

@section('inline-javascript')
	jQuery(function()
    {
		console.log(jQuery('#pageViews'));

		var pageViewsLine = Morris.Line({
            element: 'pageViewsLine',
            parseTime:false,
			grid: true,
			data: [{'date': 0, 'total': 0}],
			xkey: 'date',
			ykeys: ['total'],
			labels: ['Page Views']
		});

		jQuery.ajax({
			type: "GET",
			url: "{{ route('tracker.stats.api.pageviews') }}",
			data: { }
		})
		.done(function( data ) {
		    console.log(data);
			pageViewsLine.setData(formatDates(data));
		});

		var convertToPlottableData = function(data)
		{
			plottable = [];

			jsondata = JSON.parse(data);

            for(key in jsondata)
            {
                plottable[key] = {
					label: jsondata[key].label,
					data: jsondata[key].value
				}
            }

			return plottable;
        };

		var formatDates = function(data)
        {
			data = JSON.parse(data);

            for(key in data)
            {
                if (data[key].date !== 'undefined')
                {
					data[key].date = moment(data[key].date, "YYYY-MM-DD").format('dddd[,] MMM Do');
				}
            }

			return data;
		};
	});
@stop

@section('required-scripts-top')
	<!-- Page-Level Plugin Scripts - Main -->
	<script src="{{ $stats_template_path }}/bower_components/raphael/raphael.min.js"></script>
	<script src="{{ $stats_template_path }}/bower_components/morrisjs/morris.min.js"></script>

	<!-- Page-Level Plugin Scripts - Flot -->
	<!--[if lte IE 8]><script src="{{ $stats_template_path }}/js/excanvas.min.js"></script><![endif]-->
	<script src="{{ $stats_template_path }}/bower_components/flot/jquery.flot.js"></script>
	<script src="{{ $stats_template_path }}/bower_components/flot/jquery.flot.resize.js"></script>
	<script src="{{ $stats_template_path }}/bower_components/flot/jquery.flot.pie.js"></script>
    <script src="{{ $stats_template_path }}/bower_components/flot.tooltip/js/jquery.flot.tooltip.min.js"></script>
@stop
