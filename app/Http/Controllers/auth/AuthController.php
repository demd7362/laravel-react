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

    /**
     * @group Auth
     *
     * 닉네임 중복 검사
     *
     * 주어진 닉네임이 이미 존재하는지 확인합니다.
     *
     * @urlParam nickname string required 닉네임. Example: user123
     *
     * @response 200 {
     *     "message": "사용 가능한 닉네임입니다."
     * }
     * @response 400 {
     *     "message": "닉네임을 입력해주세요."
     * }
     * @response 409 {
     *     "message": "이미 존재하는 닉네임입니다."
     * }
     */
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

    /**
     * @group Auth
     *
     * 이메일 중복 검사
     *
     * 주어진 이메일이 이미 존재하는지 확인합니다.
     *
     * @urlParam email string required 이메일. Example: user@example.com
     *
     * @response 200 {
     *     "message": "사용 가능한 이메일입니다."
     * }
     * @response 400 {
     *     "message": "이메일을 입력해주세요."
     * }
     * @response 409 {
     *     "message": "이미 존재하는 이메일입니다."
     * }
     */
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

    /**
     * @group Auth
     *
     * JWT 유저 로그인
     *
     * 사용자 로그인을 처리하고 JWT 토큰을 반환합니다.
     *
     * @bodyParam email string required 이메일. Example: user@example.com
     * @bodyParam password string required 비밀번호. Example: password123
     *
     * @response 200 {
     *     "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
     *     "token_type": "Bearer",
     *     "user": {
     *         "id": 1,
     *         "nickname": "user123",
     *         "email": "user@example.com"
     *     }
     * }
     * @response 400 {
     *     "message": "로그인에 실패했습니다."
     * }
     */
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

    /**
     * @group Auth
     *
     * 회원가입
     *
     * 새로운 사용자를 등록합니다.
     *
     * @bodyParam nickname string required 닉네임. Example: user123
     * @bodyParam email string required 이메일. Example: user@example.com
     * @bodyParam password string required 비밀번호. Example: password123
     * @bodyParam confirmPassword string required 비밀번호 확인. Example: password123
     *
     * @response 201 {
     *     "message": "회원가입에 성공했습니다."
     * }
     * @response 400 {
     *     "message": "닉네임을 입력해주세요."
     * }
     * @response 409 {
     *     "message": "이미 존재하는 이메일입니다."
     * }
     */
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
