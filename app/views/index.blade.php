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
	
	.panel {
		min-height: 250px;
	}
</style>
@stop

@section('jumbotron')

<div class="jumbotron">
	<div class="jumbotron-background">
		<div class="container">
			<h1>{{ $tournament->name }}</h1>
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
				<p>{{ $division->description }}</p>
				@if ($division->allow_team_registration && $division->allow_solo_registration)
				<span class="emphasis">${{ $division->team_price / 100.0 }}</span> per team of {{ $division->team_size }}, additional players: <span class="emphasis">${{ $division->additional_team_member_price / 100.0 }}</span>
					${{ $division->solo_price }} per person (w/o team)
				@elseif ($division->allow_team_registration)
				<span class="emphasis">${{ $division->team_price / 100.0 }}</span> per team of {{ $division->team_size }}, additional players: <span class="emphasis">${{ $division->additional_team_member_price / 100.0 }}</span>
				@elseif ($division->allow_solo_registration)
					<span class="emphasis">${{ $division->solo_price / 100.0 }}</span> per person
				@endif
			</div>
		</div>
	</div>
@endforeach

@stop
