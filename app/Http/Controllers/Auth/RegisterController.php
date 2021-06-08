<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Mail\EmailVerification;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;


    protected $redirectTo = RouteServiceProvider::HOME;


    public function __construct()
    {
        $this->middleware('guest');
    }


    protected function validator(array $data)
    {
        return Validator::make($data, [
            'student_number' => ['required', 'string', 'regex:/[a-z][a-z][0-9][0-9][0-9][0-9][0-9]/', 'unique:users'],
        ]);
    }

    /**
     *
     * 仮登録処理・メール送信
     *
     */
    protected function create(array $data)
    {
        $user = User::create([
            'student_number' => $data['student_number'],
            'email_verify_token' => base64_encode($data['student_number']),
        ]);

        $email = new EmailVerification($user);
        $tomail = ($user->student_number).'@'.config('const.DMAIN.TEST');
        Mail::to($tomail)->send($email);

        return $user;
    }

    /**
     *
     * 仮登録確認画面表示
     *
     */
    public function pre_check(Request $request){
        $this->validator($request->all())->validate();
        //flash data
        $request->flashOnly('student_number');

        $bridge_request = $request->all();

        return view('auth.register_check')->with($bridge_request);
    }

    /**
     *
     * 本登録画面表示
     *
     */
    public function showForm($email_token)
    {
        // 使用可能なトークンか
        if ( !User::where('email_verify_token',$email_token)->exists() )
        {
            return view('auth.main.register')->with('message', '無効なトークンです。');
        } else {
            $user = User::where('email_verify_token', $email_token)->first();
            // 本登録済みユーザーか
            if ($user->status == config('const.USER_STATUS.REGISTER')) //REGISTER=1
            {
                logger("status". $user->status );
                return view('auth.main.register')->with('message', 'すでに本登録されています。ログインして利用してください。');
            }
            // ユーザーステータス更新
            $user->status = config('const.USER_STATUS.MAIL_AUTHED');
            $user->email_verified_at = Carbon::now();
            if($user->save()) {
                return view('auth.main.register', compact('email_token'));
            } else{
                return view('auth.main.register')->with('message', 'メール認証に失敗しました。再度、メールからリンクをクリックしてください。');
            }
        }
    }

    /**
     *
     * 本登録確認画面表示
     *
     */
    public function mainCheck(Request $request)
    {
        $request->validate([
            'name' => ['required','string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $bridge_request = $request->all();
        // password マスキング
        $bridge_request['password_mask'] = '******';

        return view('auth.main.register_check')->with($bridge_request);
    }

    /**
     *
     * 本登録処理
     *
     */
    public function mainRegister(Request $request)
    {
        $user = User::where('email_verify_token',$request->email_token)->first();
        $user->status = config('const.USER_STATUS.REGISTER');
        $user->name = $request->name;
        $user->password = Hash::make($request->password);
        $user->save();

        return view('auth.main.registered');
    }
}
