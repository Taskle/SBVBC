@extends('layout')

@section('css')

@stop

@section('jumbotron')

<div class="jumbotron">
	<div class="container">
        <h1>{{ $tournament->name }}</h1>
        <p>{{ $tournament->description }}</p>
	</div>
</div>

@stop

@section('content')

@foreach ($tournament->divisions() as $division)
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">{{ $division->title }}</h3>
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
@endforeach

@stop
