@extends('layout')

@section('css')

@stop

@section('jumbotron')

<div class="jumbotron">
	<div class="container">
        <h1>Hello, world!</h1>
        <p>This is a template for a simple marketing or informational website. It includes a large callout called a jumbotron and three supporting pieces of content. Use it as a starting point to create something more unique.</p>
        <p><a class="btn btn-primary btn-lg" role="button">Learn more »</a></p>
	</div>
</div>

@stop

@section('content')

@foreach ($tournament->divisions() as $division)
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">{{ $division-> title }}</h3>
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
