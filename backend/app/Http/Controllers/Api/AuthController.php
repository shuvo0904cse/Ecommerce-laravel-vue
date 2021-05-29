<?php

namespace App\Http\Controllers\Api;

use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $message;

    public function __construct()
    {
        $this->message = new MessageHelper();
    }

    /**
     * User Login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);
        if ($validator->fails()) return $this->message::validationErrorMessage(null, $validator->errors());

        try {
            //Credential
            $credentials = request(['email', 'password']);
            if(!Auth::attempt($credentials)) return $this->message::unauthorizedMessage();

            //create token
            $token = $request->user()->createToken(config("settings.personal_access_token"))->accessToken;

            return $this->message::successLoginMessage($token);
        } catch (\Exception $ex) {
            return $this->message::errorMessage($ex->getMessage());
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->token()->revoke();
            return $this->message::successMessage(config("messages.logged_out_message"));
        } catch (\Exception $ex) {
            return $this->message::errorMessage($ex->getMessage());
        }
    }

    /**
     * Logged In User
     */
    public function user(Request $request)
    {
        try {
            $user = $request->user();
            return $this->message::successMessage(null, $user);
        } catch (\Exception $ex) {
            return $this->message::errorMessage($ex->getMessage());
        }
    }
}
