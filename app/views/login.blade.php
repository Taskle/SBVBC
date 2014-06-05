@extends('layout')

@section('css')
<style>
	.form-signin {
		max-width: 330px;
		padding: 15px;
		margin: 0 auto;
	}
	
	#forgot-password-link {
		margin-top: 10px;
		display: block;
	}
</style>
@stop

@section('content')

{{ Form::open(array('url' => 'login', 'class' => 'form-signin')) }}
<h2 class="form-signin-heading">Login</h2>
{{ Form::text('email', '', array(
				'class' => 'form-control',
				'placeholder' => 'Email address',
				'required' => true,
				'autofocus' => true,
	)) }}
{{ Form::password('password', array(
				'placeholder' => 'Password',
				'class' => 'form-control', 
				'required' => true,
	)) }}
{{ Form::checkbox('rememberme', 'Remember me', array(
				'placeholder' => 'New Password',
	)) }}
	Remember me
{{ Form::submit('Login', array('class' => 'btn btn-primary btn-block')) }}
<a id="forgot-password-link" href="/password/remind">Forgot password?</a>
{{ Form::close() }}

@stop
