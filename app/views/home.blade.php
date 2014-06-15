@extends('layout')

@section('css')
<style>
	
#team-members-list {
	padding-left: 0;
}

#team-members-list > li {	
	list-style-type: none;
	clear: both;
}

#team-members-list input {
	width: 150px;
	float: left;
	margin-right: 10px;
}

#team-members-list input[type="submit"] {
	margin: 0 0 10px 0;
}

.team-member-me {
	margin-bottom: 10px;
}

.btn-add-teammate {
	clear: both;
	margin-top: 10px;
	width: 150px;
	cursor: pointer; /* hack to fix iPhone not registering click */
}

</style>
@stop

@section('js')
<script>

$(function() {
		
	$(document).on('click touchstart', '.btn-add-teammate', function() {
		$('.new-teammate-form input[name="team_id"]')
				.val($(this).data('team-id'));
		$(this).before('<li>' + 
				$('.new-teammate-form').html() + '</li>');
	});
});

</script>
@stop

@section('title')
@if (Auth::user()->role == 'Admin')
	Tournament Details
@else
	Registration Details
@endif
@stop

@section('content')

@if (Auth::user()->role == 'Admin')
	@foreach ($tournaments as $tournament)
		<h3>{{ $tournament->name }}</h3>
		@foreach ($tournament->divisions()->get() as $division)
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">
						<a href="/admin/divisions/{{ $division->id }}">
							{{ $division->name }}
						</a>
					</h3>
				</div>
				<div class="panel-body">
					<ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
						<li class="active"><a href="#team-{{ $tournament->id }}-{{ $division->id }}" data-toggle="tab">By Team</a></li>
						<li><a href="#people-{{ $tournament->id }}-{{ $division->id }}" data-toggle="tab">By Person</a></li>
					</ul>
					<div id="my-tab-content" class="tab-content">
						<div class="tab-pane active" id="team-{{ $tournament->id }}-{{ $division->id }}">
							<table class="table table-striped table-bordered table-condensed">
							<thead
								<tr>
								   <th>Team</th>
								   <th>Name</th>
								   <th>Email</th>
								   <th>Rating</th>
								   <th>Payment</th>
								</tr>
							</thead>
							<tbody>
							@foreach ($division->users()->get() as $member)
								@foreach ($member->teams()->get() as $team)
									<tr>
										<td><a href="/admin/teams/{{ $team->id }}">
												{{ $team->name }}</a>
										</td>
										<td><a href="/admin/users/{{ $member->id }}">
											{{ $member->full_name }}
										</a></td>
										<td><a href="mailto:{{ $member->email }}">
											{{ $member->email }}
										</a></td>
										<td><a href="/admin/users/{{ $member->id }}">
											{{ $member->rating }}
										</a></td>
										<td>
											@if (isset($paymentStatus[$member->email]))
												Paid ${{ $paymentStatus[$member->email] }}
											@endif
										</td>
									</tr>
								@endforeach
							@endforeach
							</tbody>
							</table>
						</div>
						<div class="tab-pane" id="people-{{ $tournament->id }}-{{ $division->id }}">
							<table class="table table-striped table-bordered table-condensed">
							<thead
								<tr>
								   <th>First Name</th>
								   <th>Last Name</th>
								   <th>Email</th>
								   <th>Rating</th>
								   <th>Payment</th>
								   <th>Team</th>
								</tr>
							</thead>
							<tbody>
							@foreach ($division->teams()->get() as $team)
								@foreach ($team->users()->get() as $member)
									<tr>
										<td><a href="/admin/users/{{ $member->id }}">
											{{ $member->first_name }}
										</a></td>
										<td><a href="/admin/users/{{ $member->id }}">
											{{ $member->last_name }}
										</a></td>
										<td><a href="mailto:{{ $member->email }}">
											{{ $member->email }}
										</a></td>
										<td><a href="/admin/users/{{ $member->id }}">
											{{ $member->rating }}
										</a></td>
										<td>
											@if (isset($paymentStatus[$member->email]))
												Paid ${{ $paymentStatus[$member->email] }}
											@endif
										</td>
										<td><a href="/admin/teams/{{ $team->id }}">
												{{ $team->name }}</a>
										</td>
									</tr>
								@endforeach
							@endforeach
							</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		@endforeach
	@endforeach

	<h2>My Registration Details</h2>
@endif

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">{{ Auth::user()->getFullName() }}</h3>
	</div>
	<div class="panel-body">
		@if (Auth::user()->rating)
			Rating: {{ Auth::user()->rating  }}
		@else
			Unrated
		@endif
	</div>
</div>

@if (count($myTeams))
	@foreach ($myTeams as $team)
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">{{ $team->name }}</h3>
			</div>
			<div class="panel-body">
				<h4>Members:</h4>
				<ul id="team-members-list">
					@foreach ($team->users()->get() as $member)
					<li data-user-id="{{ $member->id }}">
						@if ($member->id == Auth::user()->id)
							<div class="team-member-me">
								<span class="emphasis">{{ $member->getFullName() }}</span> - {{ $member->email }}
							</div>
						@else
							{{ Form::open(array('url' => '/update-teammate', 'class' => 'form-signin')) }}
								{{ Form::text('first_name', $member->first_name, array(
												'class' => 'form-control',
												'placeholder' => 'First name',
									)) }}
								{{ Form::text('last_name', $member->last_name, array(
												'class' => 'form-control',
												'placeholder' => 'Last name',
									)) }}
								{{ Form::text('email', $member->email, array(
												'class' => 'form-control',
												'placeholder' => 'Email',
									)) }}
								{{ Form::hidden('team_id', isset($team) ? $team->id : '') }}
								{{ Form::hidden('user_id', $member->id) }}
								{{ Form::submit('Save', array('class' => 'btn btn-primary btn-block')) }}
							{{ Form::close() }}
						@endif
					</li>
					@endforeach
					<button data-team-id="{{ $team->id }}" class="btn-add-teammate btn btn-primary btn-block">Add new teammate</button>
				</ul>
			</div>
		</div>
	@endforeach
@endif

<div class="hidden">
	<div class="new-teammate-form">		
		{{ Form::open(array('url' => '/update-teammate',
			'class' => 'form-signin'
		)) }}
			{{ Form::text('first_name', '', array(
							'class' => 'form-control',
							'placeholder' => 'First name',
				)) }}
			{{ Form::text('last_name', '', array(
							'class' => 'form-control',
							'placeholder' => 'Last name',
				)) }}
			{{ Form::text('email', '', array(
							'class' => 'form-control',
							'placeholder' => 'Email',
				)) }}
			{{ Form::hidden('team_id', '') }}
			{{ Form::hidden('user_id') }}
			{{ Form::submit('Save & send email', array('class' => 'btn btn-primary btn-block')) }}
		{{ Form::close() }}
	</div>
</div>

@stop
