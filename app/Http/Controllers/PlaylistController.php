<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Song;
use App\Playlist;
use Auth;

class PlaylistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(
            Playlist::where('user_id', Auth::guard('api')->id())
                ->get()
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $songs_array=Song::all();
        
        foreach ($songs_array as $song)
        {   
            $songs[$song->id]  =   $song->name;
        }
        return view("songs.formforaddplaylist", compact('songs'));
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
        ]);

        $data['name'] = $request->get("name");

        $data['user_id'] = Auth::guard('api')->id();

        foreach ($_POST['song_id_mult'] as $y){
            $data['song_id'] = $y;
            Playlist::create($data);
        }
        return back()->with("message", "Playlist Saved!");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(
            Playlist::where('id', $id)
                ->where('user_id', Auth::guard('api')->id())
                ->first()
                );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
