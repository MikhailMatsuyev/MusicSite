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

                        
                        <p><?php $photo = !is_null($song->album->photo) ? $song->album->photo : 'default.png' ?></p>
                        {!! Html::image('uploads/' . $photo, $song->album->id, ['class' => 'media-object','width' => 20, 'height' => 20, 'style'=>'display:inline-block' ]) !!}
                        <h4 class="media-heading">{{ $song->name }}</h4>
                        <address>
                            <strong>{{ $song->artist->name }}</strong><br>
							<strong>{{ $song->album->name }}</strong><br>   
                        </address>
                    </div>
                </div>
            </td>
            <td width="85" class="middle">
                <div>
                    {!! Form::open(['route' => ['songs.destroy', $song->id], 'method' => 'DELETE']) !!}
                    
                    <a href="{{ route('songs.edit', ['id' => $song->id]) }}" class="btn btn-circle btn-default btn-xs" title="Edit">
                        <i class="glyphicon glyphicon-edit"></i>
                    </a>   
                    {{--         
                    <button class="btn btn-circle btn-danger btn-xs" title="Delete" onclick="return confirm('Are You sure ?')">
                        <i class="glyphicon glyphicon-remove"></i>
                    </button>
                    --}}
                    {!! Form::close() !!}
                    <button class="btn btn-circle btn-danger btn-xs bbb" id =  {{"btn_".$song->id}} title="Delete with ajax" value="{{$song->id}}" >
                        <i class="glyphicon glyphicon-flash"></i>
                    </button>
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

@section('form-script')
    
    <script>
        $("document").ready(function(){
            
            $('.bbb').click(function() {
                val=($(this).val());
                parent=$(this).parent().parent().parent();
                

                confirm_var=confirm('Удалить песню с помощью AJAX?');//запрашиваем подтверждение на удаление
                if (!confirm_var) return false;    

                $.ajax({
                    url:'{{ url('songs')  }}'+'/'+val,
                    type: 'DELETE',
                    data:{_token: $("input[name=_token]").val()},
                    success: function(response) {
                        parent.remove();
                        alert ("Песня удалена");
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON;
                        var error = errors;

                        if (error) { 
                            alert(error);           
                        }
                    }            
                })    



            })    
        })
    </script>
@endsection