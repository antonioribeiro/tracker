@extends(Config::get('pragmarx/tracker::stats_layout'))

@section('page-contents')
	<div class="table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Name</th>
					<th># of occurrences in the period</th>
				</tr>
			</thead>

			<tbody>
				@foreach($events as $event)
					<tr>
						<td>{{ $event->name }}</td>
						<td>{{ $event->total }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
@stop
