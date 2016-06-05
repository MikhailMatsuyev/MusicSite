<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>My music</title>

        <!-- Bootstrap -->
        <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="/assets/css/jasny-bootstrap.min.css" rel="stylesheet">
        <link href="/assets/css/custom.css" rel="stylesheet">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
      <!-- navbar -->
      <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
          <div style="display: inline-block;float: left !important;" class="navbar-header" >
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>

            <a class="navbar-brand text-uppercase" style="display: inline-block; "  href="{{ url('songs') }}">            
              Musicsite
            </a>

          </div>
          <!-- /.navbar-header -->
          <div class="collapse navbar-collapse" id="navbar-collapse">

              <ul class="nav navbar-nav navbar-right">
                @if (Auth::guest())    
                    <li><a style="padding-left:15px;padding-right:1px;" href="{{ url('login') }}">Login|</a></li>
                    <li><a style="padding-left:0px;" href="{{ url('register') }}">Register</a></li>
                @else
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ url('logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                        </ul>
                    </li>
                @endif    
              </ul>    
  
              <div style="float:right;margin-top:10px;" class="btn-group">
                {!! Form::open(['route' => ['language-chooser'], 'method' => 'POST'] ) !!}

                      <select name="locale" class="form-control input-sm" style="display: inline-block;height:25px; width: 90px;font-size: 10px;" >
                          <option value="en">English</option>
                          <option value="ru" <?= Lang::locale()==='ru' ? 'selected' : '' ?>>Русский</option>
                      </select>
                      <input type="submit" value="Choose" style="float: left; height:25px;" class="btn btn-default btn-xs">

                  {!! Form::close() !!}  
               </div>

                <div class="nav navbar-right navbar-btn" style="margin-right: 20px;">
                  <a href="{{route('songs.create')}}" class="btn btn-default">
                    <i class="glyphicon glyphicon-plus"></i> 
                    <?=trans('test.AddSong')?>
                  </a>
                </div>
                
                @if (  (!Auth::guest()) and   (Auth::user()->isAdmin()))
                  <div class="nav navbar-right navbar-btn" style="margin-right: 20px;">
                    <a href="{{route('albums.create')}}" class="btn btn-default">
                      <i class="glyphicon glyphicon-plus"></i> 
                      <?=trans('test.AddAlbum')?> 
                    </a>
                  </div>
                @endif
                
                @if (!Auth::guest())
                  <div class="nav navbar-right navbar-btn" style="margin-right: 20px;">
                    <a href="{{route('api.playlists.create').'?api_token='.Auth::user()->api_token}}" class="btn btn-default">
                      <i class="glyphicon glyphicon-plus"></i> 
                      <?=trans('test.AddPlaylist')?>
                    </a>
                  </div>
                @endif
          </div>
        </div>
      </nav>

      <!-- content -->
      <div class="container">
          <div class="row">
              <div class="col-md-3">
                  <div class="list-group">
                      <?php $selected_album = Request::get("album_id") ?>
                      <a href="{{ route('songs.index') }}" class="list-group-item 
                         {{ empty($selected_album) ? 'active' : '' }}">All Albums 
                          <span class="badge">{{ App\Song::count() }}</span></a>
                      <!--            Формирование списка альбомов в меню-->
                          @foreach (App\Album::all() as $album)
                              <a href="{{ route('songs.index', ['album_id' => $album->id]) }}"class="list-group-item {{ $selected_album == $album->id ? 'active' : '' }}">
                                   <?php $photo = !is_null($album->photo) ? $album->photo : 'default.png' ?>
                                   {!! Html::image('uploads/' . $photo, $album->name, ['class' => 'media-object','width' => 20, 'height' => 20, 'style'=>'display:inline-block' ]) !!}
                                   {{ $album->name }} 

                                   <span class="badge">
                                        {{$album->songs->count()}}
                                   </span>
                              </a>
                          @endforeach
                  </div>

                  <div class="list-group">
                      <?php $selected_artist = Request::get("artist_id") ?>
                      <a href="{{ route('songs.index') }}" class="list-group-item 
                         {{ empty($selected_artist) ? 'active' : '' }}">All Artists 
                          <span class="badge">{{ App\Song::count() }}</span>
                      </a>

                      <!--            Формирование списка исполнителей в меню-->
                      @foreach (App\Artist::all() as $artist)
                          <a href="{{ route('songs.index', ['artist_id' => $artist->id]) }}"                     
                              class="list-group-item {{ $selected_artist == $artist->id ? 'active' : '' }}">
                              {{ $artist->name }} 
                             <span class="badge">{{$artist->songs->count()}}</span>
                          </a>
                      @endforeach
                  </div> 
                </div><!-- /.col-md-3 -->
              <div class="col-md-9">
              @if(session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
              @endif
              @yield('content')
          </div>
        </div>
      </div>

      <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
      <script src="/assets/js/jquery.min.js"></script>
      <!-- Include all compiled plugins (below), or include individual files as needed -->
      <script src="/assets/js/bootstrap.min.js"></script>
      <script src="/assets/js/jasny-bootstrap.min.js"></script>
      <script src="/assets/jqueryui/jquery-ui.min.js"></script>

      @yield('form-script')
    </body>
</html>

