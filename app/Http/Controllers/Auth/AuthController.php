<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use App\Code;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\CodeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Facebook\Facebook;
use Config;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';
    private $fb;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (!session_id()) {
            session_start();
        }
        $this->fb = new Facebook([
            'app_id' => Config::get('facebookhelper.app_id'), // Replace {app-id} with your app id
            'app_secret' => Config::get('facebookhelper.app_secret'),
            'default_graph_version' => Config::get('facebookhelper.default_graph_version'),
        ]);
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'g-recaptcha-response' => 'required|captcha',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'api_token'=>str_random(60),
        ]);
    }
    
    public function showLoginForm()
    {
        /*if (!session_id()) {
            session_start();
        }/*   
        $fb = new Facebook([
            'app_id' => '1676867382627174', // Replace {app-id} with your app id
            'app_secret' => '4be39d5f776f82e346a6cc62d1cba253',
            'default_graph_version' => 'v2.8',
        ]);*/
        
        $helper = $this->fb->getRedirectLoginHelper();
        $permissions = ['email']; // Optional permissions
        //dd('http://'.$_SERVER['HTTP_HOST'].'/callback');
        $loginUrl = $helper->getLoginUrl('http://'.$_SERVER['HTTP_HOST'].'/callback'/*musicsite.local/callback*/, $permissions);
        
        return view("auth.login")->with('loginUrl', htmlspecialchars($loginUrl));
    }
    
    public function postRegister(Request $request)
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {          
            $this->throwValidationException($request, $validator);
        };
        $user = $this->create($request->all());
        //создаем код и записываем код
        $code = CodeController::generateCode(8);
        Code::create([
            'user_id' => $user->id,
            'code' => $code,
        ]);
        //Генерируем ссылку и отправляем письмо на указанный адрес
        $url = url('/').'/auth/activate?id='.$user->id.'&code='.$code;      
        Mail::send('emails.registration', array('url' => $url), function($message) use ($request)
        {          
            $message->to($request->email)->subject('Registration');
        });

        return 'Регистрация прошла успешно, на Ваш email отправлено письмо со ссылкой для активации аккаунта';
    }
    
    public function activate(Request $request)
{
    $res = Code::where('user_id',$request->id)
        ->where('code',$request->code)
        ->first();
    //dd($request->id, $request->code);
    if($res) {
        //Удаляем использованный код           
        $res->delete();
        //активируем аккаунт пользователя           
        User::find($request->id)
                ->update([                   
                    'activated'=>1,
                ]);
        //редиректим на страницу авторизации с сообщением об активации
        return redirect()->to('/login')->with(['message' => 'ok']);
    }
    return abort(404);
}
    
    
}
