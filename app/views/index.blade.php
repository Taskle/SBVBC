@extends('layout')

@section('css')
<style>
	.jumbotron {
		margin-top: 50px;
		background-image: url(/images/background-grass-volleyball.png);
		webkit-background-size: cover;
		moz-background-size: cover;
		o-background-size: cover;
		background-size: cover;
		background-position-y: 50%;
	}
	
	.emphasis {
		font-weight: bold;
	}
	
	.volleyball-division-details {
		min-height: 130px;
	}
</style>
@stop

@section('jumbotron')

<div class="jumbotron">
	<div class="jumbotron-background">
		<div class="container">
			<h1>{{ $tournament->name }}</h1>
			<h2>{{ (new DateTime($tournament->date))->format('n/d') }} Open Grass Tournament</h2>
			<p>{{ $tournament->description }}</p>
		</div>
	</div>
</div>

@stop

@section('content')

@foreach ($tournament->divisions()->get() as $division)
	<div class="volleyball-division col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">{{ $division->name }}</h3>
			</div>
			<div class="panel-body">
				<div class="volleyball-division-details">
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
				@if ($division->allow_solo_registration)
					<a href="/register?type=solo&tournament={{ $tournament->id }}&division={{ $division->id }}" class="btn btn-primary btn-block">Register Individual</a>
				@endif
				@if ($division->allow_team_registration)
					<a href="/register?type=team&tournament={{ $tournament->id }}&division={{ $division->id }}" class="btn btn-primary btn-block">Register Team</a>
				@endif
			</div>
		</div>
	</div>
@endforeach

@stop
