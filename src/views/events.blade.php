@extends(Config::get('pragmarx/tracker::stats_layout'))

@section('page-contents')
	@include('pragmarx/tracker::_dataTable', array('route' => route('tracker.stats.api.events')))

	<div id='table_div'></div>
@stop
