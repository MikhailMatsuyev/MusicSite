@extends('layouts.main')

@section('content')

<div class="panel panel-default">
  <div class="panel-heading">
    <strong>Add album</strong>
  </div>
{!! Form::open(['route' => 'albums.store', 'files' => true]) !!}  

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
            <label for="name" class="control-label col-md-3">Name album</label>
            <div class="col-md-8">
              {!! Form::text("name", null, ['class' => 'form-control']) !!}
            </div>
          </div>

          <div class="form-group">
            <label for="artist" class="control-label col-md-3">Artist</label>
            <div class="col-md-8">
              <!--{!! Form::text("company", null, ['class' => 'form-control']) !!}-->
              {!! Form::select("artist_id", $artists, null, ['class' => 'form-control']) !!}
            </div>
          </div>

          <div class="form-group">
            <label for="phone" class="control-label col-md-3">Year</label>
            <div class="col-md-8">
              {!! Form::text("year", null, ['class' => 'form-control']) !!}
            </div>
          </div>     
        </div>
        <div class="col-md-4">
          <div class="fileinput fileinput-new" data-provides="fileinput">
            <div class="fileinput-new thumbnail" style="width: 150px; height: 150px;">
              <img src="{{ empty($song->photo) ? 'http://placehold.it/150x150' : "/uploads/{$song->photo}"}}" alt="Photo">
            </div>
            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 150px; max-height: 150px;"></div>
            <div class="text-center">
              <span class="btn btn-default btn-file"><span class="fileinput-new">Choose Photo</span><span class="fileinput-exists">Change</span>{!! Form::file('photo') !!}</span>
              <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
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

