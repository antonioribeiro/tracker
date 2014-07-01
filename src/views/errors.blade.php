@extends(Config::get('pragmarx/tracker::stats_layout'))

@section('page-contents')
	<div class="table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>HTTP Code</th>
					<th>Session ID</th>
					<th>Message</th>
					<th>Route Path</th>
					<th>When?</th>
				</tr>
			</thead>

			<tbody>
				@foreach($error_log as $row)
					<tr>
						<td>{{ $row->error->code }}</td>
						<td>{{ $row->session->uuid }}</td>
						<td width="20px">{{ $row->error->message }}</td>
						<td>{{ $row->path->path }}</td>
						<td>{{ $row->created_at->diffForHumans() }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
@stop
