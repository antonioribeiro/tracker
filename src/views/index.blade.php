@extends(Config::get('pragmarx/tracker::stats_layout'))

@section('page-contents')
	<table id="table_div" class="display" cellspacing="0" width="100%"></table>
@stop

@section('inline-javascript')
    @include(
        'pragmarx/tracker::_datatables',
        array(
            'datatables_ajax_route' => route('tracker.stats.api.visits'),
            'datatables_columns' =>
            '
                { "data" : "id",          "title" : "Id", "orderable": true, "searchable": true },
                { "data" : "client_ip",   "title" : "IP Address", "orderable": true, "searchable": true },
                { "data" : "country",     "title" : "Country / City", "orderable": true, "searchable": true },
                { "data" : "user",        "title" : "User", "orderable": true, "searchable": true },
                { "data" : "device",      "title" : "Device", "orderable": true, "searchable": true },
                { "data" : "browser",     "title" : "Browser", "orderable": true, "searchable": true },
                { "data" : "referer",     "title" : "Referer", "orderable": true, "searchable": true },
                { "data" : "pageViews",   "title" : "Page Views", "orderable": true, "searchable": true },
                { "data" : "lastActivity","title" : "Last Activity", "orderable": true, "searchable": true },
            '
        )
    )
@stop
