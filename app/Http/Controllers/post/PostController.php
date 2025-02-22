<?php

namespace App\Http\Controllers\post;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    const PAGE_SIZE = 10;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'show']]);
    }

    /**
     * @OA\Get(
     *     path="/posts",
     *     summary="게시글 목록 조회",
     *     tags={"Post"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="페이지 번호",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="페이지네이션 데이터",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="posts",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="게시글 제목"),
     *                         @OA\Property(property="content", type="string", example="게시글 내용"),
     *                         @OA\Property(property="user_id", type="integer", example=1),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-01T12:34:56Z"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-01T12:34:56Z")
     *                     )
     *                 ),
     *                 @OA\Property(property="first_page_url", type="string", example="http://localhost:8000/api/posts?page=1"),
     *                 @OA\Property(property="from", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="last_page", type="integer", example=1),
     *                 @OA\Property(property="last_page_url", type="string", example="http://localhost:8000/api/posts?page=1"),
     *                 @OA\Property(
     *                     property="links",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="url", type="string", nullable=true, example=null),
     *                         @OA\Property(property="label", type="string", example="&laquo; Previous"),
     *                         @OA\Property(property="active", type="boolean", example=false)
     *                     )
     *                 ),
     *                 @OA\Property(property="next_page_url", type="string", nullable=true, example=null),
     *                 @OA\Property(property="path", type="string", example="http://localhost:8000/api/posts"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *                 @OA\Property(property="to", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="total", type="integer", example=1)
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $posts = Post::whereNull('deleted_at')
            ->latest()
            ->paginate(self::PAGE_SIZE);
        return response()->json(compact('posts'));
    }

    /**
     * @OA\Post(
     *     path="/posts",
     *     summary="게시글 작성",
     *     tags={"Post"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "content"},
     *             @OA\Property(property="title", type="string", example="게시글 제목"),
     *             @OA\Property(property="content", type="string", example="게시글 내용")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="게시글 생성 성공",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="게시글이 작성되었습니다."),
     *             @OA\Property(property="post_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="유효성 검사 실패",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="제목을 입력해주세요.")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ], [
            'title.required' => '제목을 입력해주세요.',
            'title.max' => '제목은 최대 255자까지 입력 가능합니다.',
            'content.required' => '내용을 입력해주세요.',
        ]);
        if ($validated->fails()) {
            $message = $validated->errors()->first();
            return response()->json(compact('message'), 400);
        }
        $currentUserId = auth()->id();
        $post = Post::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'user_id' => $currentUserId,
        ]);

        return response()->json(['message' => '게시글이 작성되었습니다.', 'post_id' => $post->id], 201);
    }

    /**
     * @OA\Get(
     *     path="/posts/{postId}",
     *     summary="게시글 상세 조회",
     *     tags={"Post"},
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         required=true,
     *         description="게시글 ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="게시글 상세 정보",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="post", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="게시글 제목"),
     *                 @OA\Property(property="content", type="string", example="게시글 내용"),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-01T12:34:56Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-01T12:34:56Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="게시글을 찾을 수 없음",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="게시글을 찾을 수 없습니다.")
     *         )
     *     )
     * )
     */
    public function show($postId)
    {
        $post = Post::whereNull('deleted_at')
            ->with('user')
            ->find($postId);
        if (!$post || $post->deleted_at) {
            return response()->json(['message' => '게시글을 찾을 수 없습니다.'], 404);
        }
        return response()->json(compact('post'));
    }

    /**
     * @OA\Patch(
     *     path="/posts/{postId}",
     *     summary="게시글 수정",
     *     tags={"Post"},
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         required=true,
     *         description="게시글 ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "content"},
     *             @OA\Property(property="title", type="string", example="수정된 게시글 제목"),
     *             @OA\Property(property="content", type="string", example="수정된 게시글 내용")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="게시글 수정 성공",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="게시글이 수정되었습니다."),
     *             @OA\Property(
     *                 property="post",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="수정된 게시글 제목"),
     *                 @OA\Property(property="content", type="string", example="수정된 게시글 내용"),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-01T12:34:56Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-01T12:34:56Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="유효성 검사 실패",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="제목을 입력해주세요.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="권한 없음",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="권한이 없습니다.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="게시글을 찾을 수 없음",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="게시글을 찾을 수 없습니다.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $postId)
    {
        $currentUserId = auth()->id();
        $post = Post::where('id', $postId)
            ->whereNull('deleted_at')
            ->first();
        if (!$post) {
            return response()->json(['message' => '잘못된 요청입니다.'], 400);
        }
        if ($post->user_id !== $currentUserId) {
            return response()->json(['message' => '권한이 없습니다.'], 403);
        }
        $validated = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ], [
            'title.required' => '제목을 입력해주세요.',
            'title.max' => '제목은 최대 255자까지 입력 가능합니다.',
            'content.required' => '내용을 입력해주세요.',
        ]);
        if ($validated->fails()) {
            $message = $validated->errors()->first();
            return response()->json(compact('message'), 400);
        }
        $post->update([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
        ]);

        return response()->json([
            'message' => '게시글이 수정되었습니다.',
            'post' => $post
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/posts/{postId}",
     *     summary="게시글 삭제",
     *     tags={"Post"},
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         required=true,
     *         description="게시글 ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="게시글 삭제 성공",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="삭제되었습니다.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="권한 없음",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="권한이 없습니다.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="게시글을 찾을 수 없음",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="게시글을 찾을 수 없습니다.")
     *         )
     *     )
     * )
     */
    public function destroy($postId)
    {
        $currentUserId = auth()->id();
        $post = Post::whereNull('deleted_at')->find($postId);
        if (!$post || $post->deleted_at) {
            return response()->json(['message' => '게시글을 찾을 수 없습니다.'], 404);
        }
        if ($currentUserId !== $post->user_id) {
            return response()->json(['message' => '권한이 없습니다.'], 403);
        }

        $post->delete();
        return response()->json(['message' => '삭제되었습니다.']);
    }
}
