@extends('layout')

@section('css')
<style>
	.form-signin {
		max-width: 330px;
		padding: 15px;
		margin: 0 auto;
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
{{ Form::submit('Register', array('class' => 'btn btn-primary btn-block')) }}
{{ Form::close() }}

@stop
