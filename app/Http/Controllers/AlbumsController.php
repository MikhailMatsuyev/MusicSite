<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Album;
use App\Artist;
class AlbumsController extends Controller
{
    private $upload_dir;
    
    public function __construct()
    {
      $this->upload_dir = base_path() . '/public/uploads';
    }
    public function create()
    {
        $songs=[];
        $artists=$this->getArtistsAll();
        $albums=[];
        
        return view("songs.formforaddalbum", [
            'songs' => $songs,
            'artists' => $artists,
            'albums' =>$albums,
    	]);
    }
    
    public function store(Request $request)
    {
    	$this->validate($request, [
            'name' => 'required|max:255',
            'year' => 'required|digits:4',
            'artist_id' => 'required',
    	]);

        $data = $this->get_request($request);
        Album::create($data);
        return back()->with("message", "Album Saved!");
        
    }
    private function get_request(Request $request)
    {
      $data = $request->all();
      
      if ($request->hasFile('photo'))
      {
        // get file name      
        $photo = $request->file('photo')->getClientOriginalName();
        // move file to server
        $destination = $this->upload_dir;
        $request->file('photo')->move($destination, $photo);
        $data['photo'] = $photo;
      }

      return $data;
    }
    private function getArtists($id_company)
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
    {// Получение всех названий компаний из таблицы компаний
    // по именам, т.к Artist::all() возвращает все колонки, не только имя   
        $artists = [];
        foreach(Artist::all() as $artist) 
        {
            $artists[$artist->id] = $artist->name;          
        }
        return($artists);
    }
    
    
    
    
}