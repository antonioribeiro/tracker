@extends(Config::get('pragmarx/tracker::stats_layout'))

@section('page-contents')
	<div class="table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Email</th>
					<th>Last seen</th>
				</tr>
			</thead>

			<tbody>
				@foreach($users as $user)
					<tr>
						<td>{{ $user->user->email }}</td>
						<td>{{ $user->updated_at->diffForHumans() }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
@stop
