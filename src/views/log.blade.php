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
                { "data" : "method",        "title" : "Method", "orderable": true, "searchable": false },
                { "data" : "route_name",    "title" : "Route Name / Action", "orderable": true, "searchable": false },
                { "data" : "route",         "title" : "Route", "orderable": true, "searchable": false },
                { "data" : "query",         "title" : "Query", "orderable": true, "searchable": false },
                { "data" : "is_ajax",       "title" : "Is ajax?", "orderable": true, "searchable": false },
                { "data" : "is_secure",     "title" : "Is secure?", "orderable": true, "searchable": false },
                { "data" : "is_json",       "title" : "Is json?", "orderable": true, "searchable": false },
                { "data" : "wants_json",    "title" : "Wants Json?", "orderable": true, "searchable": false },
                { "data" : "error",         "title" : "Error?", "orderable": true, "searchable": false },
                { "data" : "created_at",    "title" : "Created at", "orderable": true, "searchable": false },
            '
        )
    )
@stop

