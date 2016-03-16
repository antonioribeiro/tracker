@extends($stats_layout)

@section('page-contents')
	<table id="table_div" class="display" cellspacing="0" width="100%"></table>
@stop

@section('inline-javascript')
    @include(
        'pragmarx/tracker::_datatables',
        array(
            'datatables_ajax_route' => route('tracker.stats.api.log', array('uuid' => $uuid)),
            'datatables_columns' =>
            '
                { "data" : "method",        "title" : "'.trans('tracker::tracker.method').'", "orderable": true, "searchable": false },
                { "data" : "route_name",    "title" : "'.trans('tracker::tracker.route_name_action').'", "orderable": true, "searchable": false },
                { "data" : "route",         "title" : "'.trans('tracker::tracker.route').'", "orderable": true, "searchable": false },
                { "data" : "query",         "title" : "'.trans('tracker::tracker.query').'", "orderable": true, "searchable": false },
                { "data" : "is_ajax",       "title" : "'.trans('tracker::tracker.is_ajax').'", "orderable": true, "searchable": false },
                { "data" : "is_secure",     "title" : "'.trans('tracker::tracker.is_secure').'", "orderable": true, "searchable": false },
                { "data" : "is_json",       "title" : "'.trans('tracker::tracker.is_json').'", "orderable": true, "searchable": false },
                { "data" : "wants_json",    "title" : "'.trans('tracker::tracker.wants_json').'", "orderable": true, "searchable": false },
                { "data" : "error",         "title" : "'.trans('tracker::tracker.error_q').'", "orderable": true, "searchable": false },
                { "data" : "created_at",    "title" : "'.trans('tracker::tracker.created_at').'", "orderable": true, "searchable": false },
            '
        )
    )
@stop

