<?php

namespace App\Http\Controllers;

use App\Jobs\SendVerificationEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $role = Role::where('id', $user->role_id)->first();
            return redirect($role->role_name);
        }

        return view('login');
    }

    public function loginAPI(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email!',
            'email.email' => 'Địa chỉ email không hợp lệ!',
            'email.max' => 'Địa chỉ email không được vượt quá 255 ký tự!',
            'password.required' => 'Vui lòng nhập mật khẩu!',
            'password.string' => 'Mật khẩu phải là chuỗi!',
            'password.min' => 'Mật khẩu phải chứa ít nhất 6 ký tự!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $credentials = $validator->validated();

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if (!$user->is_verified) {
                $user->verification_code = mt_rand(100000, 999999);
                $user->save();

                SendVerificationEmail::dispatch($user);

                Auth::logout();
                return response()->json(['errors' => ['is_verified' => 'Tài khoản chưa được xác thực! Một email chứa mã xác thực đã được gửi đến địa chỉ email của bạn. Vui lòng kiểm tra hộp thư đến (hoặc mục Spam) để hoàn tất quá trình xác thực!']]);
            }
            $role = Role::where('id', $user->role_id)->first();
            $request->session()->regenerate();
            $redirectUrl = redirect()->intended($role->role_name)->getTargetUrl();
            return response()->json(['redirect' => $redirectUrl]);
        }

        return response()->json(['errors' => ['email' => 'Sai email hoặc mật khẩu!']]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
