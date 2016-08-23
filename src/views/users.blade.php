@extends($stats_layout)

@section('page-contents')
	<table id="table_div" class="display" cellspacing="0" width="100%"></table>
@stop

@section('inline-javascript')
    @include(
        'pragmarx/tracker::_datatables',
        array(
            'datatables_ajax_route' => route('tracker.stats.api.users'),
            'datatables_columns' =>
            '
                { "data" : "user_id",    "title" : "'.trans('tracker::tracker.email').'", "orderable": true, "searchable": false },
                { "data" : "updated_at", "title" : "'.trans('tracker::tracker.last_seen').'", "orderable": true, "searchable": false },
            '
        )
    )
@stop
