@extends('layout')

@section('css')
<style>
	.form-register {
		max-width: 330px;
		padding: 15px;
		margin: 0 auto;
	}
</style>
@stop

@section('content')

{{ Form::open(array('url' => 'register', 'class' => 'form-register')) }}

@if ($type == 'team')
	<h2 class="form-register-heading">Register Team</h2>
@else
	<h2 class="form-register-heading">Register Individual</h2>
@endif

@if ($division && $tournament)
	<p>{{ $tournament->name }} - {{ $division->name }}</p>
@endif

{{ Form::text('first_name', $user->first_name, array(
				'class' => 'form-control',
				'placeholder' => 'First name',
				'required' => true,
				'autofocus' => true,
	)) }}
{{ Form::text('last_name', $user->last_name, array(
				'class' => 'form-control',
				'placeholder' => 'Last name',
				'required' => true,
	)) }}
@if ($type == 'team')
	{{ Form::text('team_name', $team ? $team->name : '', array(
				'class' => 'form-control',
				'placeholder' => 'Team name',
				'required' => true,
	)) }}
@endif
{{ Form::hidden('type', $type) }}

@if ($division && $tournament)
	{{ Form::hidden('tournament_id', $tournament->id, array(
					'required' => true,
		)) }}
	{{ Form::hidden('division_id', $division->id, array(
					'required' => true,
		)) }}
	<form action="" method="POST">
	  <script
		src="https://checkout.stripe.com/checkout.js" class="stripe-button"
		data-key="{{ Config::get('app.stripe.publishable_key') }}"
		@if ($type == 'team')
			data-amount="{{ $division->team_price * 100.0 }}"
		@else
			data-amount="{{ $division->solo_price * 100.0 }}"
		@endif
		@if ($tournament)
			data-name="{{ $tournament->name }}"
		@else
			data-name="South Bay Volleyball Club"
		@endif
		@if ($user->email)
			data-email="{{ $user->email }}"
		@endif
		data-description="{{ $division->name }}"
		data-image="/images/logos/logo-large-square.png">
	  </script>
	</form>
@else
	{{ Form::text('email', $user->email, array(
					'class' => 'form-control',
					'placeholder' => 'Email address',
					'required' => true,
		)) }}
	{{ Form::password('password', array(
					'placeholder' => 'New Password',
					'class' => 'form-control', 
					'required' => true,
		)) }}
	{{ Form::submit('Register', array('class' => 'btn btn-primary btn-block')) }}
@endif

{{ Form::close() }}



@stop
