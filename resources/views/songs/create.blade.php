@extends('layouts.main')

@section('content')

<div class="panel panel-default">
    <div class="panel-heading">
        <strong><?=trans('test.AddSong')?></strong>
    </div>
    {!! Form::open(['route' => 'songs.store', 'files' => true]) !!}
        @include('songs.form')
    {!! Form::close() !!}

</div>

@endsection