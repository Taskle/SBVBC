@extends('layout')

@section('css')
<style>
	h2 {
		text-align: center;
	}
	.form-reset {
		max-width: 330px;
		padding: 15px;
		margin: 0 auto;
	}
</style>
@stop

@section('content')
	<h2>Reset Your Password</h2>
    <form class="form-reset" action="{{ action('RemindersController@postReset') }}" method="POST">
		<input type="hidden" name="token" value="{{ $token }}">
		<input class="form-control" type="email" name="email" placeholder="Email">
		<input class="form-control" type="password" name="password" placeholder="New password">
		<input class="form-control" type="password" name="password_confirmation" placeholder="Confirm password">
		<input class="btn btn-primary btn-block" type="submit" value="Reset Password">
	</form>
@stop
