<?php
namespace App\Http\Controllers\Api\Auth;
use App\User;
use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    use AuthenticatesUsers;
    use Helpers;

    public function login(Request $request){

        $user = User::where('email', $request->email)->orWhere('name', $request->name)->first();
        if($user && Hash::check($request->get('password'), $user->password)){
            $token = JWTAuth::fromUser($user);
            return $this->sendLoginResponse($request, $token);
        }

        return $this->sendFailedLoginResponse($request);
    }

    protected function sendLoginResponse(Request $request, $token){
        $this->clearLoginAttempts($request);

        return $this->authenticated($token);
    }

    protected function authenticated($token){
        return $this->response->array([
            'token' => $token,
            'status_code' => 200,
            'message' => 'User Authenticated'
        ]);
    }

    protected function sendFailedLoginResponse(){
        throw new UnauthorizedHttpException("Bad Credentials");
    }

    protected function username()
    {
        return 'name';
    }
    protected function logout(){
        $this->guard()->logout();
    }
}
?>