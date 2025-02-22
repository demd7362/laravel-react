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
     * @OA\Get(
     *     path="/auth/nickname/{nickname}/exists",
     *     summary="닉네임 중복 검사",
     *     tags={"Auth"},
     *     @OA\Parameter(
     *         name="nickname",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="닉네임 중복되지 않음",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="사용 가능한 닉네임입니다.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="닉네임 유효성 검사 실패",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="닉네임을 입력해주세요.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="닉네임 중복",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="이미 존재하는 닉네임입니다.")
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/auth/email/{email}/exists",
     *     summary="이메일 중복 검사",
     *     tags={"Auth"},
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", format="email")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="이메일 중복되지 않음",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="사용 가능한 이메일입니다.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="이메일 유효성 검사 실패",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="이메일을 입력해주세요.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="중복된 이메일",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="이미 존재하는 이메일입니다.")
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/auth/login",
     *     summary="JWT 유저 로그인",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="로그인 성공",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="유효성 검사 실패",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="로그인에 실패했습니다.")
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/auth/register",
     *     summary="회원가입",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nickname", "email", "password", "confirmPassword"},
     *             @OA\Property(property="nickname", type="string", example="user123"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="confirmPassword", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="회원가입 성공",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="회원가입에 성공했습니다.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="유효성 검사 실패",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="닉네임을 입력해주세요.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="닉네임 또는 이메일 중복",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="이미 존재하는 이메일입니다.")
     *         )
     *     )
     * )
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
    /**
     * @OA\Post(
     *     path="/auth/logout",
     *     summary="유저 로그아웃",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="로그아웃 성공",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="로그아웃 되었습니다.")
     *         )
     *     )
     * )
     */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => '로그아웃 되었습니다.']);
    }
}
