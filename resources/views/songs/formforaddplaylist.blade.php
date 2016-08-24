@extends('layouts.main')

@section('content')

<div class="panel panel-default">
  <div class="panel-heading">
    <strong>Add playlist!!!</strong>
  </div>
  {{--{!! Form::open(['route' => 'albums.store', 'files' => true]) !!}--}}
      {!! Form::open(['route' => ['api.playlists.store','api_token'=>Auth::user()->api_token]]) !!}




<div class="panel-body">
    <div class="form-horizontal">
      <div class="row">
        <div class="col-md-8">

          @if( count($errors) )
            <div class="alert alert-danger">
              <ul>
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif


          <div class="form-group">
            <label for="Name" class="control-label col-md-3">Name</label>
            <div class="col-md-8">
              {!! Form::text("name", null, ['class' => 'form-control']) !!}
            </div>
          </div>

          <div class="form-group">
            <label for="Song" class="control-label col-md-3">Song</label>
            <div class="col-md-8">
              {!! Form::select("song_id", $songs, null, array('multiple' => true,'name'=>'song_id_mult[]','class' => 'form-control')) !!}

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="panel-footer">
    <div class="row">
      <div class="col-md-8">
        <div class="row">
          <div class="col-md-offset-3 col-md-6">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ url('/')}}" class="btn btn-default">Cancel</a>
          </div>
        </div>
      </div>
    </div>
  </div>


{!! Form::close() !!}
</div>

@endsection

