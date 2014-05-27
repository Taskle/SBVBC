@extends('layout')

@section('content')
    @foreach($users as $user)
        <p>{{ $user->getFullName() }}</p>
    @endforeach
@stop
