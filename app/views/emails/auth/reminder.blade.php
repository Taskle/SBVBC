<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>SBVBC Password Reset</h2>

		<div>
			<p>To reset your password, go here:<br />
				{{ URL::to('password/reset', array($token)) }}.</p>
		</div>
	</body>
</html>
