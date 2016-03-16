@extends($stats_layout)

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
                { "data" : "error.code",     "title" : "'.trans('tracker::tracker.code').'", "orderable": true, "searchable": false },
                { "data" : "session.uuid",   "title" : "'.trans('tracker::tracker.session').'", "orderable": true, "searchable": false },
                { "data" : "error.message",  "title" : "'.trans('tracker::tracker.message').'", "orderable": true, "searchable": false },
                { "data" : "path.path",      "title" : "'.trans('tracker::tracker.path').'", "orderable": true, "searchable": false },
                { "data" : "updated_at",     "title" : "'.trans('tracker::tracker.when').'", "orderable": true, "searchable": false },
            '
        )
    )
@stop
