@extends('layout')

@section('css')
<style>
	
#team-members-list {
	padding-left: 0;
}

#team-members-list > li {	
	list-style-type: none;
	clear: both;
}

#team-members-list input {
	width: 150px;
	float: left;
	margin-right: 10px;
}

#team-members-list input[type="submit"] {
	margin-top: 0;
}

.team-member-me {
	margin-bottom: 10px;
}

#btn-add-teammate {
	clear: both;
	margin-top: 10px;
	width: 150px;
}

</style>
@stop

@section('js')
<script>

$(function() {
	$('#btn-add-teammate').click(function() {
		$('#btn-add-teammate').before('<li>' + 
				$('.new-teammate-form').html() + '</li>');
	});
});

</script>
@stop

@section('title')
Registration Details
@stop

@section('content')

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Basic Info</h3>
	</div>
	<div class="panel-body">
		<h4>{{ Auth::user()->getFullName() }}</h4>
		@if (Auth::user()->rating)
			Rating: {{ Auth::user()->rating  }}
		@else
			Unrated
		@endif
	</div>
</div>

@if (count($divisions))
	<h2>Division Registration</h2>

	@foreach ($divisions as $division)
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">{{ $division->name }}</h3>
			</div>
			<div class="panel-body">
				<p>{{ $division->description }}</p>
				@if ($division->allow_team_registration && $division->allow_solo_registration)
				<span class="emphasis">${{ $division->team_price / 100.0 }}</span> for {{ $division->team_size }} people, <span class="emphasis">${{ $division->additional_team_member_price / 100.0 }}</span> per additional
					${{ $division->solo_price }} per person (w/o team)
				@elseif ($division->allow_team_registration)
				<span class="emphasis">${{ $division->team_price / 100.0 }}</span> for {{ $division->team_size }} people, <span class="emphasis">${{ $division->additional_team_member_price / 100.0 }}</span> per additional
				@elseif ($division->allow_solo_registration)
					<span class="emphasis">${{ $division->solo_price / 100.0 }}</span> per person
				@endif
			</div>
		</div>
	@endforeach
@endif

@if (count($teams))
	<h2>Team Registration</h2>

	@foreach ($teams as $team)
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">{{ $team->name }}</h3>
			</div>
			<div class="panel-body">
				<h4>Members:</h4>
				<ul id="team-members-list">
					@foreach ($team->users()->get() as $member)
					<li data-user-id="{{ $member->id }}">
						@if ($member->id == Auth::user()->id)
							<div class="team-member-me">
								<span class="emphasis">{{ $member->getFullName() }}</span> - {{ $member->email }}
							</div>
						@else
							{{ Form::open(array('url' => '/update-teammate', 'class' => 'form-signin')) }}
								{{ Form::text('first_name', $member->first_name, array(
												'class' => 'form-control',
												'placeholder' => 'First name',
									)) }}
								{{ Form::text('last_name', $member->last_name, array(
												'class' => 'form-control',
												'placeholder' => 'Last name',
									)) }}
								{{ Form::text('email', $member->email, array(
												'class' => 'form-control',
												'placeholder' => 'Email',
									)) }}
								{{ Form::hidden('team_id', $team->id) }}
								{{ Form::hidden('user_id', $member->id) }}
								{{ Form::submit('Save', array('class' => 'btn btn-primary btn-block')) }}
							{{ Form::close() }}
						@endif
					</li>
					@endforeach
					<button id="btn-add-teammate" class="btn btn-primary btn-block">Add new teammate</button>
				</ul>
			</div>
		</div>
	@endforeach
@endif

<div class="hidden">
	<div class="new-teammate-form">		
		{{ Form::open(array('url' => '/update-teammate',
			'class' => 'form-signin'
		)) }}
			{{ Form::text('first_name', '', array(
							'class' => 'form-control',
							'placeholder' => 'First name',
				)) }}
			{{ Form::text('last_name', '', array(
							'class' => 'form-control',
							'placeholder' => 'Last name',
				)) }}
			{{ Form::text('email', '', array(
							'class' => 'form-control',
							'placeholder' => 'Email',
				)) }}
			{{ Form::hidden('team_id', $team->id) }}
			{{ Form::hidden('user_id') }}
			{{ Form::submit('Save & send email', array('class' => 'btn btn-primary btn-block')) }}
		{{ Form::close() }}
	</div>
</div>

@stop
