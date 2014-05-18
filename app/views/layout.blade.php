<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>South Bay Volleyball Club</title>
	<link href='http://fonts.googleapis.com/css?family=Lato|Open+Sans' rel='stylesheet' type='text/css'>
	<style>
		
	body {
		margin: 20px;
		font-family: 'Open Sans', sans-serif;
		color: #333;
	}
	
	header, h1, h2, h3, h4, h5, h6 {
		font-family: 'Lato', sans-serif;
	}
	
	</style>
	
	@yield('css')
</head>
<body>
	<header>
	    <img src="/images/logos/logo-small.png" />
		<h1>
			@section('title')
			South Bay Volleyball Club
			@show
		</h1>
	</header>
	<div class="main">
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
