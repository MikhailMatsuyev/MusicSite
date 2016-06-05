<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;

	class LanguageController extends Controller
	{
	    public function chooser(Request $request) {
	        Session::put('locale',$request->input('locale'));    
	        return redirect()->back();
	    }
	}

