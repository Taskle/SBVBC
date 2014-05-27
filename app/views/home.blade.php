@extends('layout')

@section('css')

@stop

@section('title')
Registration Details
@stop

@section('content')

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Basic Info</h3>
	</div>
	<div class="panel-body">
		<h4>{{ Auth::user()->getFullName() }}</h4>
		@if (Auth::user()->rating)
			Rating: {{ Auth::user()->rating  }}
		@else
			Unrated
		@endif
	</div>
</div>

@if (count($divisions))
	<h2>Division Registration</h2>

	@foreach ($divisions as $division)
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">{{ $division->name }}</h3>
			</div>
			<div class="panel-body">
				<p>{{ $division->description }}</p>
				@if ($division->allow_team_registration && $division->allow_solo_registration)
				<span class="emphasis">${{ $division->team_price / 100.0 }}</span> for {{ $division->team_size }} people, <span class="emphasis">${{ $division->additional_team_member_price / 100.0 }}</span> per additional
					${{ $division->solo_price }} per person (w/o team)
				@elseif ($division->allow_team_registration)
				<span class="emphasis">${{ $division->team_price / 100.0 }}</span> for {{ $division->team_size }} people, <span class="emphasis">${{ $division->additional_team_member_price / 100.0 }}</span> per additional
				@elseif ($division->allow_solo_registration)
					<span class="emphasis">${{ $division->solo_price / 100.0 }}</span> per person
				@endif
			</div>
		</div>
	@endforeach
@endif

@if (count($teams))
	<h2>Team Registration</h2>

	@foreach ($teams as $team)
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">{{ $team->name }}</h3>
			</div>
			<div class="panel-body">
				<h4>Members:</h4>
				<ul>
					@foreach ($team->users()->get() as $member)
					<li>{{ $member->getFullName() }}</li>
					@endforeach
				</ul>
				<b>Note: please contact jim (at) sbvbc.org to update your team member list.</b>
			</div>
		</div>
	@endforeach
@endif

@stop
