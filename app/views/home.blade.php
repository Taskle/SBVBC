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

.btn-add-teammate,
.btn-pay-for-player {
	clear: both;
	margin-top: 10px;
	width: 150px;
	cursor: pointer; /* hack to fix iPhone not registering click */
}

.title-detail {
	margin-left: 10px;
}

.btn-export-players {
	float: right;
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

@if ($tournament && $tournament->schedule_url)
	<p><a target="_blank" href="{{ $tournament->schedule_url }}">View the tournament schedule</a></p>
@endif

@if (Auth::user()->role == 'Admin')

	@if (isset($tournament))
            <a class="btn btn-primary btn-export-players" href="/export-tournament-csv/<?= $tournament->id ?>">Export to Excel</a>

            <h3>{{ $tournament->name }} <span class="title-detail deemphasis">{{ count($tournament->teams) }} teams, {{ count($tournament->getUsers()) }} players</span></h3>
            @foreach ($tournament->divisions()->get() as $division)
                    <div class="panel panel-default">
                            <div class="panel-heading">
                                    <h3 class="panel-title">
                                            <a href="/admin/divisions/{{ $division->id }}">
                                                    {{ $division->name }} <span class="title-detail deemphasis">{{ count($division->teams) }} teams, {{ count($division->users) }} players</span>
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
                                                    @foreach ($division->getUnassignedPlayers($tournament->id) as $user)
                                                            <tr>
                                                                    <td></td>
                                                                    <td><a href="/admin/users/{{ $user->id }}">
                                                                            {{ $user->full_name }}
                                                                    </a></td>
                                                                    <td><a href="mailto:{{ $user->email }}">
                                                                            {{ $user->email }}
                                                                    </a></td>
                                                                    <td><a href="/admin/users/{{ $user->id }}">
                                                                            {{ $user->rating }}
                                                                    </a></td>
                                                                    <td>
                                                                            @if (isset($paymentStatus[$user->email]))
                                                                                    Paid ${{ $paymentStatus[$user->email] }}
                                                                            @endif
                                                                    </td>
                                                            </tr>
                                                    @endforeach
                                                    @foreach ($division->teams->sortBy(function($team) {
                                                            return $team->name;
                                                    }) as $team)
                                                            @foreach ($team->users()->get() as $user)
                                                                    <tr>
                                                                            <td><a href="/admin/teams/{{ $team->id }}">
                                                                                            {{ $team->name }}</a>
                                                                            </td>
                                                                            <td><a href="/admin/users/{{ $user->id }}">
                                                                                    {{ $user->full_name }}
                                                                            </a></td>
                                                                            <td><a href="mailto:{{ $user->email }}">
                                                                                    {{ $user->email }}
                                                                            </a></td>
                                                                            <td><a href="/admin/users/{{ $user->id }}">
                                                                                    {{ $user->rating }}
                                                                            </a></td>
                                                                            <td>
                                                                                    @if (isset($paymentStatus[$user->email]))
                                                                                            Paid ${{ $paymentStatus[$user->email] }}
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
                                                    @foreach ($division->users->sortBy(function($user) {
                                                            return $user->full_name;
                                                    }) as $user)
                                                            <tr>
                                                                    <td><a href="/admin/users/{{ $user->id }}">
                                                                            {{ $user->first_name }}
                                                                    </a></td>
                                                                    <td><a href="/admin/users/{{ $user->id }}">
                                                                            {{ $user->last_name }}
                                                                    </a></td>
                                                                    <td><a href="mailto:{{ $user->email }}">
                                                                            {{ $user->email }}
                                                                    </a></td>
                                                                    <td><a href="/admin/users/{{ $user->id }}">
                                                                            {{ $user->rating }}
                                                                    </a></td>
                                                                    <td>
                                                                            @if (isset($paymentStatus[$user->email]))
                                                                                    Paid ${{ $paymentStatus[$user->email] }}
                                                                            @endif
                                                                    </td>
                                                                    <td>
                                                                            <?php $team = $user->getTeam($tournament->id);
                                                                            if (isset($team) && $team) { ?>
                                                                                    <a href="/admin/teams/{{ $team->id }}">
                                                                                            {{ $team->name }}</a>
                                                                            <?php } ?>
                                                                    </td>
                                                            </tr>
                                                    @endforeach
                                                    </tbody>
                                                    </table>
                                            </div>
                                    </div>
                            </div>
                    </div>
            @endforeach
        @endif

	<h2>My Registration Details</h2>
@endif

@if ($tournament)
	<p>
		@if (Auth::user()->isRegisteredForTournament($tournament))
			You are registered for {{ $tournament->name }} {{ $tournament->year }}. <a href="/tournaments/{{ $tournament->id }}" target="_blank">View tournament details</a>
		@else
			You are not registered for {{ $tournament->name }} {{ $tournament->year }}. <a class="emphasis" href="/tournaments/{{ $tournament->id }}">Click here to register now</a>
		@endif
	</p>
@endif

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">{{ Auth::user()->getFullName() }}</h3>
	</div>
	<div class="panel-body">
		<p>Email: {{ Auth::user()->email }}</p>
		<p>
		@if (Auth::user()->rating)
			Rating: {{ Auth::user()->rating  }}
		@else
			Unrated
		@endif
		</p>
	</div>
</div>

@if ($myTeam)
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">{{ $myTeam->name }} - {{ $myTeam->division->name }} <span class="title-detail deemphasis">{{ count($myTeam->users) - 1 }} teammates</span></h3>
		</div>
		<div class="panel-body">
			<div>
				<ul id="team-members-list">
					@foreach ($myTeam->users()->get() as $user)
						<li data-user-id="{{ $user->id }}">
							@if ($user->id != Auth::user()->id)
								{{ Form::open(array('url' => '/update-teammate', 'class' => 'form-signin')) }}
									{{ Form::text('first_name', $user->first_name, array(
													'class' => 'form-control',
													'placeholder' => 'First name',
										)) }}
									{{ Form::text('last_name', $user->last_name, array(
													'class' => 'form-control',
													'placeholder' => 'Last name',
										)) }}
									{{ Form::text('email', $user->email, array(
													'class' => 'form-control',
													'placeholder' => 'Email',
										)) }}
									{{ Form::hidden('team_id', isset($myTeam) ? $myTeam->id : '') }}
									{{ Form::hidden('user_id', $user->id) }}
									{{ Form::submit('Save', array('class' => 'btn btn-primary btn-block')) }}
								{{ Form::close() }}
							@endif
						</li>
					@endforeach
					@if (count($myTeam->users) < $myTeam->division->min_team_members)
						<button data-team-id="{{ $myTeam->id }}" class="btn-add-teammate btn btn-primary btn-block">Add new teammate</button>
					@endif
				</ul>
			</div>
			<div class="clearfix">
				@if (count($myTeam->users) >= $myTeam->division->max_team_members + 5)
					<p>You've reached the maximum number of players ({{ $myTeam->division->max_team_members }}).</p>
				@elseif (count($myTeam->users) >= $myTeam->division->min_team_members)
					<p>After {{ $myTeam->division->min_team_members }} players, each additional costs ${{ $myTeam->division->formatted_additional_team_member_price }} ({{ $myTeam->division->max_team_members }} players maximum).</p>
					<a href="/register?type=additional&tournament={{ $tournament->id }}&division={{ $myTeam->division->id }}&team={{ $myTeam->id }}&proxy=1" class="btn-pay-for-player btn btn-primary btn-block">Add player for ${{ $myTeam->division->formatted_additional_team_member_price }}</a>
				@endif
			</div>
		</div>
	</div>
@endif

<p>Questions or concerns? Email <a href="mailto:contact@sbvbc.org">contact@sbvbc.org</a>.</p>

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
