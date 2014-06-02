SBVBC Registration Confirmation

You are now registered for the {{ $tournament->name }}!

@if ($team)
Team name: {{ $team->name }}

You can edit your team name and members at http://www.sbvbc.org.					
@else
If you registered for a team, you can edit your team name and members at http://www.sbvbc.org.		
@endif

If you registered for a team, you can edit your team name and members at http://www.sbvbc.org.

Email: {{ $email }}
Password: {{ $password }}

If you have any questions or problems, please reply to this email or contact us at contact@sbvbc.org.
