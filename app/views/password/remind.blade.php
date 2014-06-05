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
	<h2>Send password reset request</h2>
    <form class="form-reset" action="{{ action('RemindersController@postRemind') }}" method="POST">
		<input class="form-control" type="email" name="email" placeholder="Email">
		<input class="btn btn-primary btn-block" type="submit" value="Send Reminder">
	</form>
@stop
