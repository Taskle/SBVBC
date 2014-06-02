<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>South Bay Volleyball Club</title>
	<link href='//fonts.googleapis.com/css?family=Lato|Open+Sans' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="/css/main.css">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
	@yield('css')
	@yield('js')
</head>
<body>
	@section('header')
		<div class="navbar navbar-default">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="/">
						<img class="logo" src="/images/logos/logo-extra-small.png" />
						<span class="logo-name">South Bay Volleyball Club</span>
					</a>
				</div>
				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
						@if (Auth::guest())
							<li><a href="/login">Log in</a></li>
						@else
							<li><a href="/">{{ Auth::user()->name }}</a></li>
							@if (Auth::user()->role == 'Admin')
								<li><a href="/admin">Admin</a></li>
							@endif
							<li><a href="/logout">Log out</a></li>
						@endif
					</ul>
				</div>
			</div>
		</div>
	@show
	@section('jumbotron')
	@show
	<div class="main container">
		<h1>
		@section('title')
		@show
		</h1>
		@if($errors->has())
			@foreach ($errors->all() as $error)
				<div class="alert alert-danger">{{ $error }}</div>
			@endforeach
		@endif
		@yield('content')
	</div>
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		ga('create', 'UA-51090875-1', 'sbvbc.org');
		ga('send', 'pageview');
	</script>
</body>
</html>
