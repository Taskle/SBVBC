<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>SBVBC Registration Confirmation</h2>

		<div>
                        @if (isset($tournament) && isset($tournament->name))
                            You are now registered for the {{ $tournament->name }}!<br />
                        @else
                            You are now registered for the South Bay Volleyball Club!<br />
                        @endif
			<br />
			@if (isset($team) && isset($team->name))
				Team name: {{ $team->name }}<br />
				<br />
				You can edit your team name and members at <a href="http://www.sbvbc.org">http://www.sbvbc.org</a>.<br />						
			@else
				If you registered for a team, you can edit your team name and members at <a href="http://www.sbvbc.org">http://www.sbvbc.org</a>.<br />			
			@endif
			<br />
			Email: {{ $email }}<br />
			Password: {{ $password }}<br />
			<br />
			If you have any questions or problems, please reply to this email or contact us at <a href="mailto:contact@sbvbc.org">contact@sbvbc.org</a>.
		</div>
	</body>
</html>
