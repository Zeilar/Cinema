@extends('head')

@section('body')
    <form action="{{ route('create_room') }}" method="post">
        @csrf
        <button type="submit">Create a new room</button>
    </form>
@endsection