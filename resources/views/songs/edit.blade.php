@extends('layouts.main')
@section('content')

<div class="panel panel-default">
    <div class="panel-heading">
        <strong>Edit Song</strong>
    </div>

    {!! Form::model($song, ['files' => true, 'method' => 'PATCH', 'route' => ['songs.update', $song->id]]) !!}
        @include('songs.form');
    {!! Form::close() !!}
</div>
@endsection

