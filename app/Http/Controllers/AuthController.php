<?php

namespace App\Http\Controllers;
use App\Models\User;
// use App\User;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;

class AuthController extends Controller
{
    /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function regis(Request $request)
    {
        //validate incoming request 
        $this->validate($request, [
            'name' => 'required|string',
            'user_name' => 'required|unique:Users',
              'password' => 'required|min:6',
        ]);
        $user_name = $request->input("user_name");
        $name=$request->input("name");
        $password = $request->input("password");

        $hashPwd = Hash::make($password);

        $data = [
            "user_name" => $user_name,
            "password" => $hashPwd,
            "name" =>$name,
            "created_at"=>date("Y-m-d H:i:s"),
              "updated_at"=>date("Y-m-d H:i:s"),
              "created_by"=>"1",
              "updated_by"=>"1"
        ];

        if (User::create($data)) {


        //if (DB::table('Users')->insert($data)) {
            $out = [
                "message" => "register_success",
                "code"    => 201,
              // "data"    => $data
            ];
        } else {
            $out = [
                "message" => "vailed_regiser",
                "code"   => 404,
            ];
        }

        return response()->json($out, $out['code']);

    }
    public function login(Request $request)
    {
        $validated = $this->validate($request, [
            'user_name' => 'required|exists:Users,user_name',
            'password' => 'required'
        ]);

        $user = User::where('user_name', $validated['user_name'])->first();
        if (!Hash::check($validated['password'], $user->password)) {
            return abort(401, "email or password not valid");
        }
        $payload = [
            'iat' => intval(microtime(true)),
            'exp' => intval(microtime(true)) + (60 * 60 * 1000),
            'uid' => $user->id
        ];
        $token = JWT::encode($payload, env('JWT_SECRET'));
        return response()->json(['access_token' => $token]);
    }

 function decodejwt($key){

        //$key = explode(' ',$request->header('Authorization'));

       $out =json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $key[1])[1]))));
       
       return $out->uid;
      // return response()->json($out->user_id, 200);

     }

}