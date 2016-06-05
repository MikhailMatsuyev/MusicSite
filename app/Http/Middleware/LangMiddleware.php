<?php

namespace App\Http\Middleware;


use Closure;
use Session;
use Lang;

class LangMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //Session::forget('locale');
        $value = $request->header("accept-language");
        
        if ($value != ''){
            $lang=substr($value,0,2);
        }
        else{
            $lang="ru";
        }
        
        if (!Session::has('locale')){
            Session::put('locale','ru');
        }
        Lang::setLocale(Session::get('locale'));
        
        return $next($request);
    }
}
