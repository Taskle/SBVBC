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

@stop
