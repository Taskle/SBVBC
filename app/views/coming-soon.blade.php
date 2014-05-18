@extends('layout')

@section('css')
<style>
	
	body {
		margin:50px;
		text-align:center;
		color: #999;
	}
	
	.welcome {
		width: 300px;
		height: 200px;
		position: absolute;
		left: 50%;
		top: 50%;
		margin-left: -150px;
		margin-top: -100px;
	}

	a, a:visited {
		text-decoration:none;
	}

	h1 {
		font-size: 32px;
		margin: 16px 0 0 0;
	}
</style>
@stop

@section('title')
@stop

@section('content')
    <img src="/images/logos/logo-large.png" />
	<h1>Coming soon...</h1>
@stop
