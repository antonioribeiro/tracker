<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laravel Stats Tracker</title>

    <!-- Core CSS - Include with every page -->
    <link href="{{ $stats_template_path }}/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ $stats_template_path }}/font-awesome/css/font-awesome.css" rel="stylesheet">

    <!-- Page-Level Plugin CSS - Dashboard -->
    <link href="{{ $stats_template_path }}/css/plugins/morris/morris-0.4.3.min.css" rel="stylesheet">
    <link href="{{ $stats_template_path }}/css/plugins/timeline/timeline.css" rel="stylesheet">

    <!-- SB Admin CSS - Include with every page -->
    <link href="{{ $stats_template_path }}/css/sb-admin.css" rel="stylesheet">

	<link
		rel="stylesheet"
		type="text/css"
		href="https://cloud.github.com/downloads/lafeber/world-flags-sprite/flags16.css"
	/>

</head>

<body>

    <div id="wrapper">

        <nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{route('tracker.stats.index')}}">Laravel Stats Tracker</a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right navbar-nav">
				<li {{ Session::get('tracker.stats.days') == '1' ? 'class="active"' : '' }}>
					<a href="{{route('tracker.stats.index')}}?days=1">1 day</a>
				</li>

				<li {{ Session::get('tracker.stats.days') == '7' ? 'class="active"' : '' }}>
					<a href="{{route('tracker.stats.index')}}?days=7">7 days</a>
				</li>

				<li {{ Session::get('tracker.stats.days') == '30' ? 'class="active"' : '' }}>
					<a href="{{route('tracker.stats.index')}}?days=30">30 days</a>
				</li>

				<li {{ Session::get('tracker.stats.days') == '365' ? 'class="active"' : '' }}>
					<a href="{{route('tracker.stats.index')}}?days=365">1 year</a>
				</li>
            </ul>
            <!-- /.navbar-top-links -->

            <div class="navbar-default navbar-static-side" role="navigation">
                <div class="sidebar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a href="{{route('tracker.stats.index')}}?page=visits" ><i class="fa fa-dashboard fa-fw"></i> Visits <span class="{{ Session::get('tracker.stats.page') =='visits' ? 'fa arrow' : '' }}"></span></a>
                        </li>
                        <li>
                            <a href="{{route('tracker.stats.index')}}?page=summary"><i class="fa fa-bar-chart-o fa-fw"></i> Summary <span class="{{ Session::get('tracker.stats.page') =='summary' ? 'fa arrow' : '' }}"></span></a>
                        </li>
                        <li>
                            <a href="{{route('tracker.stats.index')}}?page=users"><i class="fa fa-user fa-fw"></i> Users <span class="{{ Session::get('tracker.stats.page') =='users' ? 'fa arrow' : '' }}"></span></a>
                        </li>
                        <li>
                            <a href="{{route('tracker.stats.index')}}?page=events"><i class="fa fa-bolt fa-fw"></i> Events <span class="{{ Session::get('tracker.stats.page') =='events' ? 'fa arrow' : '' }}"></span></a>
                        </li>
                        <li>
                            <a href="{{route('tracker.stats.index')}}?page=errors"><i class="fa fa-exclamation fa-fw"></i>Errors <span class="{{ Session::get('tracker.stats.page') =='errors' ? 'fa arrow' : '' }}"></span></a>
                        </li>
                    </ul>
                    <!-- /#side-menu -->
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="page-header">{{$title}}</h1>
                </div>

	            <div class="col-lg-12">
		            @yield('page-contents')
	            </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">
			        @yield('page-secondary-contents')
		        </div>
	        </div>

            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- Core Scripts - Include with every page -->
    <script src="{{ $stats_template_path }}/js/jquery-1.10.2.js"></script>
    <script src="{{ $stats_template_path }}/js/bootstrap.min.js"></script>
    <script src="{{ $stats_template_path }}/js/plugins/metisMenu/jquery.metisMenu.js"></script>

    <!-- Page-Level Plugin Scripts - Main -->
    <script src="{{ $stats_template_path }}/js/plugins/morris/raphael-2.1.0.min.js"></script>
    <script src="{{ $stats_template_path }}/js/plugins/morris/morris.js"></script>

    <!-- Page-Level Plugin Scripts - Flot -->
    <!--[if lte IE 8]><script src="{{ $stats_template_path }}/js/excanvas.min.js"></script><![endif]-->
    <script src="{{ $stats_template_path }}/js/plugins/flot/jquery.flot.js"></script>
    <script src="{{ $stats_template_path }}/js/plugins/flot/jquery.flot.tooltip.min.js"></script>
    <script src="{{ $stats_template_path }}/js/plugins/flot/jquery.flot.resize.js"></script>
    <script src="{{ $stats_template_path }}/js/plugins/flot/jquery.flot.pie.js"></script>

    <!-- SB Admin Scripts - Include with every page -->
    <script src="{{ $stats_template_path }}/js/sb-admin.js"></script>

    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.7.0/moment.min.js"></script>

    <script>
	    @yield('inline-javascript')
    </script>
</body>

</html>
