<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'checkEmail', 'checkNickname']]);
    }

    public function checkNickname($nickname)
    {
        $validated = Validator::make(['nickname' => $nickname], [
            'nickname' => ['required', 'min:2', 'max:16'],
        ], [
            'nickname.required' => '닉네임을 입력해주세요.',
            'nickname.min' => '닉네임은 최소 2자리 이상이어야 합니다.',
            'nickname.max' => '닉네임은 최대 16자리 이하여야 합니다.',
        ]);
        if ($validated->fails()) {
            $message = $validated->errors()->first();
            return response()->json(compact('message'), 400);
        }
        $user = User::where('nickname', $nickname)->first();
        if ($user) {
            return response()->json([
                'message' => '이미 존재하는 닉네임입니다.'
            ], 409);
        }

        return response()->json([
            'message' => '사용 가능한 닉네임입니다.'
        ]);
    }

    public function checkEmail($email)
    {
        $validated = Validator::make(['email' => $email], [
            'email' => 'required|email',
        ], [
            'email.required' => '이메일을 입력해주세요.',
            'email.email' => '이메일 형식이 적절하지 않습니다.',
        ]);
        if ($validated->fails()) {
            $message = $validated->errors()->first();
            return response()->json(compact('message'), 400);
        }
        $user = User::where('email', $email)->first();
        if ($user) {
            return response()->json([
                'message' => '이미 존재하는 이메일입니다.'
            ], 409);
        }

        return response()->json([
            'message' => '사용 가능한 이메일입니다.'
        ]);
    }

    public function login(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ], [
            'email.required' => '이메일을 입력해주세요.',
            'email.email' => '이메일 형식이 적절하지 않습니다.',
            'password.required' => '비밀번호를 입력해주세요.',
            'password.min' => '비밀번호는 최소 6자리여야 합니다.',
            'password.max' => '비밀번호는 최대 16자리입니다.',
        ]);
        if ($validated->fails()) {
            $message = $validated->errors()->first();
            return response()->json(compact('message'), 400);
        }
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'message' => '로그인에 실패했습니다.',
            ], 400);
        }
        $user = auth()->user();

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function register(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'nickname' => ['required', 'min:2', 'max:16'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6', 'max:16'],
            'confirmPassword' => ['required', 'same:password'],
        ], [
            'nickname.required' => '닉네임을 입력해주세요.',
            'nickname.min' => '닉네임은 최소 2자리여야 합니다.',
            'nickname.max' => '닉네임은 최대 16자리입니다.',
            'email.required' => '이메일을 입력해주세요.',
            'email.email' => '이메일 형식에 맞지 않습니다.',
            'password.required' => '비밀번호를 입력해주세요.',
            'password.min' => '비밀번호는 최소 6자리여야 합니다.',
            'password.max' => '비밀번호는 최대 16자리입니다.',
            'confirmPassword.required' => '비밀번호 확인을 입력해주세요.',
            'confirmPassword.same' => '비밀번호와 비밀번호 확인이 일치하지 않습니다.',
        ]);
        if ($validated->fails()) {
            $message = $validated->errors()->first();
            return response()->json(compact('message'), 400);
        }
        $user = User::where('email', $request->email)->first();
        if ($user) {
            return response()->json([
                'message' => '이미 존재하는 이메일입니다.'
            ], 409);
        }
        $user = User::where('nickname', $request->nickname)->first();
        if ($user) {
            return response()->json([
                'message' => '이미 존재하는 닉네임입니다.'
            ], 409);
        }
        User::create([
            'nickname' => $request->nickname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => '회원가입에 성공했습니다.'
        ], 201);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => '로그아웃 되었습니다.']);
    }
}
