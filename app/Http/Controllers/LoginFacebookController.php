<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use Facebook\Facebook;
use App\User;
use Config;//Обязательно. Именно так, т.к. В алиасах он добавлен
//use App\Config\facebookhelper;
use Illuminate\Support\Facades\Auth;

class LoginFacebookController extends Controller
{
    public function login() 
    {
        if (!session_id()) {
            session_start();
        }   
        $fb = new Facebook([
            'app_id' => Illuminate\Support\Facades\Config::get('facebookhelper.app_id'), // Replace {app-id} with your app id
            'app_secret' => Config::get('facebookhelper.app_secret'),
            'default_graph_version' => Config::get('facebookhelper.default_graph_version'),
        ]);
        $helper = $fb->getRedirectLoginHelper();
        dd($helper);
        $permissions = ['email']; // Optional permissions
        $loginUrl = $helper->getLoginUrl('http://musicsite.local/', $permissions);
        echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';
    }

    public function callback()
    {
        if (!session_id()) {
            session_start();
        }
        $fb = new Facebook([
            'app_id' => config::get('facebookhelper.app_id'), // Replace {app-id} with your app id
            'app_secret' => Config::get('facebookhelper.app_secret'),
            'default_graph_version' => Config::get('facebookhelper.default_graph_version'),
        ]);

        $helper = $fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        }   catch(Exceptions\FacebookResponseException $e) {
          // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        }   catch(Exceptions\FacebookSDKException $e) {
          // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (! isset($accessToken)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo 'Bad request';
            }
            exit;
        }

        // Logged in
        echo '<h3>Access Token</h3>';
        var_dump($accessToken->getValue());

        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();

        // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);
        echo '<h3>Metadata</h3>';
        var_dump($tokenMetadata);

        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId(Config::get('facebookhelper.app_id')); // Replace {app-id} with your app id
        // If you know the user ID this access token belongs to, you can validate it here
        //$tokenMetadata->validateUserId('123');
        $tokenMetadata->validateExpiration();

        if (! $accessToken->isLongLived()) {
          // Exchanges a short-lived access token for a long-lived one
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (Exceptions\FacebookSDKException $e) {
                echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
                exit;
            }

            echo '<h3>Long-lived</h3>';
            //var_dump($accessToken->getValue());
        }

        $_SESSION['fb_access_token'] = (string) $accessToken;

    // User is logged in with a long-lived access token.
    // You can redirect them to a members-only page.
    //header('Location: https://example.com/members.php');
        
        $fb->setDefaultAccessToken($_SESSION['fb_access_token']);
        $user=$fb->get('/me?fields=name,email')->getGraphUser();
        
        $array = [
            "name" => $user["name"],
            "email" => $user["email"],
            "activated" => 1,
            "password" => bcrypt(1),
            //"password_confirmation" => "nnnnnn",
        ];
        
        if(User::where('email', '=', $array["email"])->count()){
            //dd("yes");
            if (Auth::attempt(['email' => $array["email"],'password' => 1,'activated' => $array["activated"]])){
                return redirect()->intended('/');
        }
        }
        else {
            $this->createsocial($array);
            if (Auth::attempt(['email' => $array["email"],'password' => 1,'activated' => $array["activated"]])){
                return redirect()->intended('contacts');
            }
            echo '<h3>Error</h3>'; 
        }
        $v=$this->createsocial($array);    
    }
    
    protected function createsocial(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'activated' => $data['activated'],
        ]);
    }
}
