@extends($stats_layout)

@section('page-contents')
	<table id="table_div" class="display" cellspacing="0" width="100%"></table>
@stop

@section('inline-javascript')
    @include(
        'pragmarx/tracker::_datatables',
        array(
            'datatables_ajax_route' => route('tracker.stats.api.events'),
            'datatables_columns' =>
            '
                { "data" : "name",  "title" : "'.trans('tracker::tracker.name').'", "orderable": true, "searchable": false },
                { "data" : "total", "title" : "'.trans('tracker::tracker.no_occurrences_in_period').'", "orderable": true, "searchable": false },
            '
        )
    )
@stop
