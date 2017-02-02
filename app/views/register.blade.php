@extends('layout')

@section('css')
<style>
	.form-register {
		max-width: 330px;
		padding: 15px;
		margin: 0 auto;
	}

	#forgot-password-link {
		margin-top: 10px;
		display: block;
	}

	.below-link {
		max-width: 330px;
		padding: 10px 0 0 0;
		margin: 0 auto;
		display: block;
	}

</style>
@stop

@section('content')

{{ Form::open(array('url' => URL::current(), 'class' => 'form-register')) }}

@if (!Auth::check() && Cookie::get('stripeToken'))
	@if ($type == 'team')
		<h2 class="form-register-heading">Log In & Pay for Team</h2>
	@elseif ($type == 'additional')
		<h2 class="form-register-heading">Log In & Pay for Additional Player</h2>
	@else
		<h2 class="form-register-heading">Log In & Pay</h2>
	@endif
@else
	@if ($type == 'team')
		<h2 class="form-register-heading">Register Team</h2>
	@elseif ($type == 'additional')
		<h2 class="form-register-heading">Additional Player</h2>
	@else
		<h2 class="form-register-heading">Register Individual</h2>
	@endif
@endif

@if ($division && $tournament)
	<p>{{ $tournament->name }} - {{ $division->name }}</p>
@endif

@if ($type == 'additional')
	{{ Form::text('teammate_first_name', $proxy ? null : $user->first_name, array(
					'class' => 'form-control',
					'placeholder' => 'Teammate First name',
					'required' => true,
					'autofocus' => true,
		)) }}
	{{ Form::text('teammate_last_name', $proxy ? null : $user->last_name, array(
					'class' => 'form-control',
					'placeholder' => 'Teammate Last name',
					'required' => true,
		)) }}
	@if ($proxy)
		{{ Form::text('teammate_email', null, array(
					'class' => 'form-control',
					'placeholder' => 'Teammate Email',
					'required' => true,
		)) }}
		{{ Form::hidden('proxy', true, array(
					'required' => true,
		)) }}
	@endif
@elseif (!Auth::check() && !Cookie::get('stripeToken'))
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
@endif

@if ($type == 'team')
	{{ Form::text('team_name', isset($team) ? $team->name : '', array(
				'class' => 'form-control',
				'placeholder' => 'Team name',
				'required' => true,
	)) }}
@elseif ($type == 'additional')
	<p>Select team. Don't see it? <a href="/register?type=team&tournament={{ $tournament->id }}&division={{ $division->id }}">Register your team</a></p>
	{{ Form::select('team_id', Team::where('division_id', $division->id)->lists('name', 'id'), isset($team) ? $team->id : null, array(
				'class' => 'form-control',
				'required' => true,
	)) }}
@endif
{{ Form::hidden('type', $type) }}

@if (!Auth::check() && Cookie::get('stripeToken'))
	{{ Form::text('email', $user->email, array(
					'class' => 'form-control',
					'placeholder' => 'Email address',
					'required' => true,
		)) }}
	{{ Form::password('password', array(
					'placeholder' => 'Password',
					'class' => 'form-control',
					'required' => true,
		)) }}
@endif

@if ($division && $tournament)
	{{ Form::hidden('tournament_id', $tournament->id, array(
					'required' => true,
		)) }}
	{{ Form::hidden('division_id', $division->id, array(
					'required' => true,
		)) }}
	@if (!Auth::check() && Cookie::get('stripeToken'))
		{{ Form::hidden('stripeToken', Cookie::get('stripeToken'), array(
						'required' => true,
			)) }}
		{{ Form::submit('Log In & Submit Payment', array('class' => 'btn btn-primary btn-block')) }}
	@else
		  <script
			src="https://checkout.stripe.com/checkout.js" class="stripe-button"
			data-key="{{ Config::get('app.stripe.publishable_key') }}"
			@if ($type == 'team')
				data-amount="{{ $division->team_price * 100.0 }}"
			@elseif ($type == 'additional')
				data-amount="{{ $division->additional_team_member_price * 100.0 }}"
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
	@endif
@elseif (!Auth::check() && !Cookie::get('stripeToken'))
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

@if (!Auth::check() && Cookie::get('stripeToken'))
	<a id="forgot-password-link" target="_blank" href="/password/remind">Forgot password?</a>
@endif

@if (!Auth::check() && !Cookie::get('stripeToken'))
	<span class="below-link">
		Already have an account? <a id="login-link" href="/login">Log In</a>
	</span>
@endif

{{ Form::close() }}

@stop
