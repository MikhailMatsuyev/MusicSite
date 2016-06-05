<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Song;
use App\Album;
use App\Artist;
//use Session;
use Cache;
//use App\Http\Request;

class SongsController extends Controller
{
    private $rules = [
      'name' => ['required', 'min:1'],
      'artist_id' => ['required'],  
      'album_id' => ['required'],
      'photo' => ['mimes:jpg,jpeg,png,gif,bmp']
    ];

    private $upload_dir;
    
    public function __construct()
    {
        $this->upload_dir = base_path() . '/public/uploads';
    }

    public function index(Request $request)
    {
        
        $artists=[];//определить обязательно. Т.к. если не определить, то 
        // будет выскакивать ошибка если создали новую группу контактов и нажали
        // на просмотр.
        //Определяем, выбран ли пункт в меню слева
        //$sql='Contact::where('group_id', $group_id)->orderBy("id", "desc")->paginate(5)';
        
        $albums=[];
        if ( ($album_id = $request->get("album_id") ) ) {
            $songs=Song::where('album_id', $album_id)->orderBy("id", "desc")->paginate(5);
        } // какой либо альбом выбран    
    	
    	elseif ( ($artist_id = $request->get("artist_id") ) ){ 
            $songs = Song::where('artist_id', $artist_id)->orderBy("id", "desc")->paginate(5);
            // какой либо артист выбран    
    	}
        else{
            //$songs=Song::orderBy("id", "desc")->paginate(5);
            $songs = Cache::remember('songs', 60/3600, function()
            {
                return Song::orderBy("id", "desc")->paginate(5);
            });
        }
            // не выбран ни один альбом или группа(т.е. при старте отобразятся все контакты 
            // в обратном порядке их добавления)    
    	      
        foreach ($songs as $song)
        {   
            $artists[$song->id]  =   $this->getArtists($song->artist_id);
            $albums[$song->id]   =   $this->getAlbumsAll($song->group_id);
        }
        
    	return view("songs.index", [
            'songs'     =>  $songs,
            'artists'   =>  $artists,
            'albums'    =>  $albums,
    	]);
    }

    private function getAlbums()
    {
        $albums = [];
        //Album::all() содержит ВСЮ информацию из таблицы Album
        // Чтобы выбрать только названия групп, применяем цикл
        foreach(Album::all() as $album) {
            $albums[$album->id] = $album->name;
        }
        return $albums;
    }
    
    private function getArtists($id_artist)
    {
        $artists = Artist::all();
        foreach(Artist::all() as $artist) {
            if($artist->id===$id_artist){
                return($artist->name);
            }
        }        
        return $artists;
    }
    
    private function getArtistsAll()
    {
        $artists = [];
        foreach(Artist::all() as $artist) 
        {
            $artists[$artist->id] = $artist->name;          
        }
        return($artists);
    }
    
    private function getAlbumsAll($id_album)
    {
        foreach(Album::all() as $album) {
            if($album->id===$id_album){
                return($album->name);
            }
        }
    }

    public function create()
    {
        $albums = $this->getAlbums();
        $artists = $this->getArtistsAll();
        return view("songs.create", compact('albums', 'artists'));
    }

    public function edit($id)
    {
        $albums = $this->getAlbums();
        $artists = $this->getArtistsAll();
        $song = Song::find($id);
        return view("songs.edit", compact('albums', 'song', 'artists'));
    }

    public function store(Request $request)
    { 
        $this->validate($request, $this->rules);
        $data = $this->getRequest($request);
        Song::create($data);
        return redirect("songs")->with("message", "Song Saved!");
    }

    private function getRequest(Request $request)
    {
        $data = $request->all();
        if ($request->hasFile('photo'))
        {
            $photo = $request->file('photo')->getClientOriginalName();
            // move file to server
            $destination = $this->upload_dir;
            $request->file('photo')->move($destination, $photo);
            $data['photo'] = $photo;
        }
        return $data;
    }

    public function update($id, Request $request)
    {
        $this->validate($request, $this->rules);
        $song = Song::find($id);
        $data = $this->getRequest($request);
        $song->update($data);
        Cache::pull('songs');
        return redirect("songs")->with("message", "Song Updated!");   
    }

    public function destroy($id)
    {
        $song = Song::find($id);

        if (!is_null($song->photo)){
            $file_path = $this->upload_dir . '/' . $song->photo;
            if (file_exists($file_path)) {unlink($file_path);}
        }
        $song->delete();
        Cache::forget('songs');
        return redirect("songs")->with("message", "Song Deleted!");   
    }
}

