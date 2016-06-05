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
            <label for="name" class="control-label col-md-3">Name</label>
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
            <label for="album" class="control-label col-md-3">Album</label>
            <div class="col-md-5">
              {!! Form::select("album_id", $albums, NULL,  ['class' => 'form-control']) !!}
            </div>
          </div>
          <div class="form-group" id="add-new-group" style="display: none;">
            <div class="col-md-offset-3 col-md-8">
              <div class="input-group">
                <input type="text" name="new_group" id="new_group" class="form-control">
                <span class="input-group-btn">
                  <a href="#" id="add-new-btn" class="btn btn-default">
                    <i class="glyphicon glyphicon-ok"></i>
                  </a>
                </span>
              </div>
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




