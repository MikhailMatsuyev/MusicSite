@extends('layouts.main')

@section('content')

<div class="panel panel-default">
    <table class="table">

    @foreach ($songs as $song)
        <tr>
            <td class="middle">
                <div class="media">
                    <div class="media-left">
                        <a href="#">       
                            <?php $photo = !is_null($song->photo) ? $song->photo : 'default.png' ?>         
<!--                            {!! Html::image('uploads/' . $photo, $song->name, ['class' => 'media-object', 'width' => 100, 'height' => 100]) !!}-->
                        </a>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading">{{ $song->name }}</h4>
                        <address>
                            <strong>{{ $artists[$song->id] }}</strong><br>
                            <strong>{{ $albums[$song->id] }}</strong><br>   
                        </address>
                    </div>
                </div>
            </td>
            <td width="100" class="middle">
                <div>
                    {!! Form::open(['route' => ['songs.destroy', $song->id], 'method' => 'DELETE']) !!}
                    <a href="{{ route('songs.edit', ['id' => $song->id]) }}" class="btn btn-circle btn-default btn-xs" title="Edit">
                        <i class="glyphicon glyphicon-edit"></i>
                    </a>            
                    <button class="btn btn-circle btn-danger btn-xs" title="Delete" onclick="return confirm('Are You sure ?')">
                        <i class="glyphicon glyphicon-remove"></i>
                    </button>
                    {!! Form::close() !!}
                </div>
            </td>
        </tr>
    @endforeach
    </table>            
</div>

<div class="text-center">
    <nav>
        {!! $songs->appends( Request::query() )->render() !!}
    </nav>
</div>

@endsection
