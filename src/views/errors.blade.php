@extends(Config::get('pragmarx/tracker::stats_layout'))

@section('page-contents')
	<table id="table_div" class="display" cellspacing="0" width="100%"></table>
@stop

@section('inline-javascript')
    @include(
        'pragmarx/tracker::_datatables',
        array(
            'datatables_ajax_route' => route('tracker.stats.api.errors'),
            'datatables_columns' =>
            '
                { "data" : "error.code",     "title" : "Code", "orderable": true, "searchable": false },
                { "data" : "session.uuid",   "title" : "Session", "orderable": true, "searchable": false },
                { "data" : "error.message",  "title" : "Message", "orderable": true, "searchable": false },
                { "data" : "path.path",      "title" : "Path", "orderable": true, "searchable": false },
                { "data" : "updated_at",     "title" : "When?", "orderable": true, "searchable": false },
            '
        )
    )
@stop
