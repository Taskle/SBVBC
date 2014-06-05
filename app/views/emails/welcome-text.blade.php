SBVBC Registration Confirmation

@if (isset($tournament) && isset($tournament->name))
You are now registered for the {{ $tournament->name }}!
@else
You are now registered for the South Bay Volleyball Club!
@endif

@if (isset($team) && isset($team->name))
Team name: {{ $team->name }}

You can edit your team name and members at http://www.sbvbc.org.					
@else
If you registered for a team, you can edit your team name and members at http://www.sbvbc.org.		
@endif

If you registered for a team, you can edit your team name and members at http://www.sbvbc.org.

Email: {{ $email }}
Password: {{ $password }}

If you have any questions or problems, please reply to this email or contact us at contact@sbvbc.org.
