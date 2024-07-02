<?php

namespace App\Http\Controllers;

use App\Jobs\SendResetPassword;
use App\Jobs\SendVerificationEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function registerAPI(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            're-enter-password' => 'required|string|min:6|same:password',
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email!',
            'email.email' => 'Địa chỉ email không hợp lệ!',
            'email.max' => 'Địa chỉ email không được vượt quá 255 ký tự!',
            'password.required' => 'Vui lòng nhập mật khẩu!',
            'password.string' => 'Mật khẩu phải là chuỗi!',
            'password.min' => 'Mật khẩu phải chứa ít nhất 6 ký tự!',
            're-enter-password.required' => 'Vui lòng nhập lại mật khẩu!',
            're-enter-password.string' => 'Mật khẩu phải là chuỗi!',
            're-enter-password.min' => 'Mật khẩu phải chứa ít nhất 6 ký tự!',
            're-enter-password.same' => 'Mật khẩu nhập lại không khớp!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $email = $request->input('email');
        $user = User::where('email', $email)->get()->first();

        if (!$user) {
            $user = new User();
            $user->email = $email;
            $user->password = Hash::make($request->input('password'));
            $user->verification_code = mt_rand(100000, 999999);

            if (str_contains($email, '@tlu.edu.vn')) {
                $user->role_id = 4;
            } else if (str_contains($email, '@e.tlu.edu.vn')) {
                $user->role_id = 3;
            } else {
                return response()->json(['errors' => ['email' => "Phải sử dụng Email nhà trường đã cung cấp!"]]);
            }

            $user->save();

            SendVerificationEmail::dispatch($user);

        } else {
            if ($user->is_verified) {
                return response()->json(['errors' => ['email' => "Email đã được sử dụng!"]]);
            } else {
                $user->password = Hash::make($request->input('password'));
                $user->verification_code = mt_rand(100000, 999999);
                $user->save();

                SendVerificationEmail::dispatch($user);
            }
        }

        return response()->json(['success' => 'Một email chứa mã xác thực đã được gửi đến địa chỉ email của bạn. Vui lòng kiểm tra hộp thư đến (hoặc mục Spam) để hoàn tất quá trình xác thực!']);
    }

    public function verificationEmailAPI(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'verification-code' => 'required|string|min:6|max:6',
        ], [
            'verification-code.required' => 'Vui lòng nhập mã xác thực!',
            'verification-code.string' => 'Mã xác thực phải là chuỗi!',
            'verification-code.min' => 'Mã xác thực phải chứa đúng 6 ký tự!',
            'verification-code.max' => 'Mã xác thực phải chứa đúng 6 ký tự!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $user = User::where('verification_code', $request->input('verification-code'))->where('is_verified', false)->get()->first();

        if ($user) {
            $user->is_verified = true;
            $user->verification_code = null;
            $user->save();

            return response()->json(['success' => 'Xác thực email thành công!']);
        } else {
            return response()->json(['errors' => ['verification-code' => 'Mã xác thực không chính xác!']]);
        }
    }

    public function forgotPasswordAPI(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email!',
            'email.email' => 'Địa chỉ email không hợp lệ!',
            'email.max' => 'Địa chỉ email không được vượt quá 255 ký tự!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $email = $request->input('email');

        if (!str_contains($email, '@tlu.edu.vn') && !str_contains($email, '@e.tlu.edu.vn')) {
            return response()->json(['errors' => ['email' => "Phải sử dụng Email nhà trường đã cung cấp!"]]);
        }

        $user = User::where('email', $email)->get()->first();

        if ($user) {
            $user->reset_password_token = Str::random(60);
            $user->save();

            SendResetPassword::dispatch($user);

            return response()->json(['success' => 'Một email đã được gửi đến địa chỉ email của bạn. Vui lòng kiểm tra hộp thư đến (hoặc mục Spam) để hoàn tất quá trình đặt lại mật khẩu!']);
        } else {
            return response()->json(['errors' => ['email' => 'Email không tồn tại!']]);
        }
    }

    public function resetPassword(string $token)
    {
        return view('forgot-password', ['token' => $token]);
    }

    public function resetPasswordAPI(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new-password' => 'required|min:6',
            're-enter-new-password' => 'same:new-password',
        ], [
            'new-password.required' => 'Vui lòng nhập mật khẩu mới!',
            'new-password.min' => 'Mật khẩu phải chứa ít nhất 6 ký tự!',
            're-enter-new-password.same' => 'Mật khẩu nhập lại không khớp!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $user = User::where('reset_password_token', $request->input('token'))->first();

        if ($user) {
            $user->password = Hash::make($request->input('new-password'));
            $user->reset_password_token = null;
            $user->save();

            return response()->json(['success' => 'Đặt lại mật khẩu thành công!', 'redirect' => route('login')]);
        } else {
            return response()->json(['errors' => ['token' => 'Token không hợp lệ!']]);
        }
    }

    public function updatePasswordAPI(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'new-password' => 'required|min:6',
            're-enter-new-password' => 'same:new-password',
        ], [
            'new-password.required' => 'Vui lòng nhập mật khẩu mới!',
            'new-password.min' => 'Mật khẩu phải chứa ít nhất 6 ký tự!',
            're-enter-new-password.same' => 'Mật khẩu nhập lại không khớp!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $user = User::findOrFail($id);
        $user->password = $request->input('new-password');

        $user->save();

        return response()->json(['success' => 'Đổi mật khẩu tài khoản thành công!']);
    }
}
