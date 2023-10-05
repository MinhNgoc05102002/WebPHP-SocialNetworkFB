<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class AuthController extends Controller
{

    protected $account;

    public function __construct(Account $_account) {
        $this->account = $_account;
    }
    public function register(Request $request){
        $validator = Validator::make($request->all(),
        [
         'username'=>'required', 
         'password'=>'required',
         'email'=>'required',
        ]
        );

        return Account::create([
            'username' => $request->input('username'),
            'password' => Hash::make($request->input('password')),
        ]);
    }

    public function login(Request $request)
    {
        try {
             // Kiểm tra xem người dùng đã đăng nhập hay chưa (nó check token trong bảng personal_access_token)
            if (auth('sanctum')->check()) {
                return response()->error("Bạn đã đăng nhập và không thể truy cập API đăng nhập lại.", 403);
            }
            
            // Xác thực người dùng và lấy thông tin người dùng
            if (Auth::attempt($request->only('email', 'password'))) {
                $user = Auth::user();
                // Tạo token Sanctum cho người dùng
                $token = $user->createToken('token-name')->plainTextToken;
                $account = $this->account->getInfoAccount($request->input('email'));

                $result = [
                    'data'=>$account,
                    'authentication' => [
                        'access_token' => $token,
                        'token_type' => 'Bearer',
                    ]
                ];
                return response()->success($result,"Đăng nhập thành công !",200);
            }
            // Xác thực thất bại
            return response()->error('Unauthorized', 401);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function logout(Request $request)
    {
        Auth::user()->tokens->each(function ($token, $key) {
            $token->delete();
        });
    
        return response()->json(['message' => 'Logout successful']);
    }
}
