<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@lang("tracker::tracker.tracker_title")</title>

	<script src="{{ $stats_template_path }}/bower_components/jquery/dist/jquery.min.js"></script>

	@yield('required-scripts-top')

    <!-- Core CSS - Include with every page -->
    <link href="{{ $stats_template_path }}/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ $stats_template_path }}/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<link href="{{ $stats_template_path }}/bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- Page-Level Plugin CSS - Dashboard -->
    <link href="{{ $stats_template_path }}/bower_components/morrisjs/morris.css" rel="stylesheet">
    <link href="{{ $stats_template_path }}/dist/css/timeline.css" rel="stylesheet">

    <!-- SB Admin CSS - Include with every page -->
    <link href="{{ $stats_template_path }}/dist/css/sb-admin-2.css" rel="stylesheet">

    <link href="//cdn.datatables.net/1.10.2/css/jquery.dataTables.css" rel="stylesheet">

	<link
		rel="stylesheet"
		type="text/css"
		href="https://cloud.github.com/downloads/lafeber/world-flags-sprite/flags16.css"
	/>
</head>

<body>
    @yield('body')

    <!-- Core Scripts - Include with every page -->
    <script src="{{ $stats_template_path }}/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="{{ $stats_template_path }}/bower_components/metisMenu/dist/metisMenu.min.js"></script>

    <script src="{{ $stats_template_path }}/bower_components/raphael/raphael-min.js"></script>
    <script src="{{ $stats_template_path }}/bower_components/morrisjs/morris.min.js"></script>

    <!-- SB Admin Scripts - Include with every page -->
    <script src="{{ $stats_template_path }}/dist/js/sb-admin-2.js"></script>

    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.7.0/moment.min.js"></script>

    <script src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>

	@yield('required-scripts-bottom')

    <script>
	    @yield('inline-javascript')
    </script>
</body>

</html>
