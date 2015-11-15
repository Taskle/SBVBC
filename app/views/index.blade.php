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
		min-height: 65px;
		margin-bottom: 20px;
	}
</style>
@stop

@section('jumbotron')

@if ($tournament)
    <div class="jumbotron">
            <div class="jumbotron-background">
                    <div class="container">
                            <h1>{{ $tournament->name }}</h1>
                            <h2>{{ (new DateTime($tournament->date))->format('F j, Y') }}</h2>
                            <p>{{ $tournament->description }}</p>
			@if ($tournament->schedule_url)
                                    <p><a target="_blank" href="{{ $tournament->schedule_url }}">View the tournament schedule</a></p>
                            @endif
                    </div>
            </div>
    </div>

    @stop

    @section('content')

    @if ($tournament->isRegistrationOpen())
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
														<span class="emphasis">${{ $division->formatted_team_price }}</span> for up to {{ $division->min_team_members }} people, <span class="emphasis">${{ $division->formatted_additional_team_member_price }}</span> per additional
														<br><span class="emphasis">${{ $division->formatted_solo_price }}</span> per person without team
                                                    @elseif ($division->allow_team_registration)
                                                    <span class="emphasis">${{ $division->formatted_team_price }}</span> for up to {{ $division->min_team_members }} people, <span class="emphasis">${{ $division->formatted_additional_team_member_price }}</span> per additional
                                                    @elseif ($division->allow_solo_registration)
                                                            <span class="emphasis">${{ $division->formatted_solo_price }}</span> per person
                                                    @endif
                                            </div>
                                            @if ($division->allow_solo_registration)
                                                    <a href="/register?type=solo&tournament={{ $tournament->id }}&division={{ $division->id }}" class="btn btn-primary btn-block">Register Individual</a>
                                            @endif
                                            @if ($division->allow_team_registration)
                                                    <a href="/register?type=team&tournament={{ $tournament->id }}&division={{ $division->id }}" class="btn btn-primary btn-block">Register Team</a>
                                            @endif
                                            @if ($division->allow_additional_team_member)
                                                    <a href="/register?type=additional&tournament={{ $tournament->id }}&division={{ $division->id }}" class="btn btn-primary btn-block">Register Additional Player</a>
                                            @endif
                                    </div>
                            </div>
                    </div>
            @endforeach
    @else

    Registration for <?= $tournament->name ?> is now closed. If you would still like to participate, please email <a href="mailto:contact@sbvbc.org">contact@sbvbc.org</a> with the division you wish to join and we will see if it's possible to accommodate you.

    @endif
@else

    <div class="jumbotron">
            <div class="jumbotron-background">
                    <div class="container">
                            <h1>Welcome to the South Bay Volleyball Club</h1>
                            <p>Please visit us on Facebook at <a href="https://www.facebook.com/groups/SouthBayVolleyballClub">https://www.facebook.com/groups/SouthBayVolleyballClub</a> to learn about our upcoming events.</p>
                    </div>
            </div>
    </div>

@endif

@stop
