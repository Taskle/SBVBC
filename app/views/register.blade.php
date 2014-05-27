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
<h2 class="form-register-heading">Register</h2>
{{ Form::text('name', $user->name, array(
				'class' => 'form-control',
				'placeholder' => 'Full name',
				'required' => true,
				'autofocus' => true,
	)) }}
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
{{ Form::close() }}

@stop
