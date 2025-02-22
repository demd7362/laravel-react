<?php

namespace App\Http\Controllers\post;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class CommentController extends Controller
{
    const PAGE_SIZE = 10;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'show']]);
    }
    /**
     * @OA\Get(
     *     path="/posts/{postId}/comments",
     *     summary="게시글의 댓글 조회",
     *     tags={"Post"},
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         required=true,
     *         description="게시글 id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="page",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="페이지네이션 데이터",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="comments",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="content", type="string", example="새 댓글"),
     *                         @OA\Property(property="user_id", type="integer", example=1),
     *                         @OA\Property(property="post_id", type="integer", example=2),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-01T12:34:56Z"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-01T12:34:56Z")
     *                     )
     *                 ),
     *                 @OA\Property(property="first_page_url", type="string", example="http://localhost:8000/api/posts/2/comments?page=1"),
     *                 @OA\Property(property="from", type="integer", nullable=true, example=null),
     *                 @OA\Property(property="last_page", type="integer", example=1),
     *                 @OA\Property(property="last_page_url", type="string", example="http://localhost:8000/api/posts/2/comments?page=1"),
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
     *                 @OA\Property(property="path", type="string", example="http://localhost:8000/api/posts/2/comments"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *                 @OA\Property(property="to", type="integer", nullable=true, example=null),
     *                 @OA\Property(property="total", type="integer", example=0)
     *             )
     *         )
     *     )
     * )
     */
    public function index($postId)
    {
        $comments = Comment::where('post_id', '=', $postId)
            ->whereNull('deleted_at')
            ->with('user')
            ->paginate(self::PAGE_SIZE);
        return response()->json(compact('comments'));
    }
    /**
     * @OA\Post(
     *     path="/posts/{postId}/comments",
     *     summary="댓글 작성",
     *     tags={"Post"},
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"content"},
     *             @OA\Property(property="content", type="string", example="새 댓글")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="댓글 생성 성공",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="댓글이 작성되었습니다."),
     *             @OA\Property(property="post_id", type="integer", example=2),
     *             @OA\Property(property="comment_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="게시글 id 값이 없음",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="잘못된 요청입니다.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="댓글 조회 실패",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="존재하지 않는 게시글입니다.")
     *         )
     *     )
     * )
     */
    public function store(Request $request, $postId)
    {
        if (!$postId) {
            return response()->json(['message' => '잘못된 요청입니다.'], 400);
        }
        $post = Post::where('id', '=', $postId)->whereNull('deleted_at')->first();
        if (!$post) {
            return response()->json(['message' => '존재하지 않는 게시글입니다.'], 404);
        }
        $validated = Validator::make($request->all(), [
            'content' => 'required|string|max:255',
        ], [
            'content.required' => '댓글을 작성해주세요.',
            'content.max' => '최대 255자까지 입력 가능합니다.'
        ]);
        if ($validated->fails()) {
            $message = $validated->errors()->first();
            return response()->json(compact('message'), 400);
        }
        $currentUserId = auth()->id();
        $comment = $post->comments()->create([
            'user_id' => $currentUserId,
            'content' => $request->input('content')
        ]);

        return response()->json(['message' => '댓글이 작성되었습니다.', 'post_id' => $post->id, 'comment_id' => $comment->id, ], 201);
    }
    /**
     * @OA\Patch(
     *     path="/posts/{postId}/comments/{commentId}",
     *     summary="댓글 수정",
     *     tags={"Post"},
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="commentId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"content"},
     *             @OA\Property(property="content", type="string", example="Updated comment content.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="댓글 수정 성공",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="게시글이 수정되었습니다."),
     *             @OA\Property(
     *                 property="comment",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="content", type="string", example="Updated comment content."),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="post_id", type="integer", example=2),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-01T12:34:56Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-01T12:34:56Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="게시글 또는 댓글의 아이디 누락",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="잘못된 요청입니다.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="자신의 댓글이 아님",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="권한이 없습니다.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="이미 삭제된 댓글",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="존재하지 않는 댓글입니다.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $postId, $commentId)
    {
        if (!$postId || !$commentId) {
            return response()->json(['message' => '잘못된 요청입니다.'], 400);
        }
        $comment = Comment::where('id', $commentId)
            ->where('post_id', $postId)
            ->whereNull('deleted_at')
            ->first();
        if (!$comment) {
            return response()->json(['message' => '존재하지 않는 댓글입니다.'], 404);
        }
        $currentUserId = auth()->id();
        if ($currentUserId !== $comment->user_id) {
            return response()->json(['message' => '권한이 없습니다.'], 403);
        }
        $validated = Validator::make($request->all(), [
            'content' => 'required|string|max:255',
        ], [
            'content.required' => '댓글을 작성해주세요.',
            'content.max' => '최대 255자까지 입력 가능합니다.'
        ]);
        if ($validated->fails()) {
            $message = $validated->errors()->first();
            return response()->json(compact('message'), 400);
        }
        $comment->update([
            'content' => $request->input('content'),
        ]);

        return response()->json([
            'message' => '게시글이 수정되었습니다.',
            'comment' => $comment
        ]);
    }
    /**
     * @OA\Delete(
     *     path="/posts/{postId}/comments/{commentId}",
     *     summary="댓글 삭제",
     *     tags={"Post"},
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="commentId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="삭제 성공",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="삭제되었습니다.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="댓글 id값 누락",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="잘못된 요청입니다.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="자신의 댓글이 아님",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="권한이 없습니다.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="이미 삭제된 댓글",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="존재하지 않는 댓글입니다.")
     *         )
     *     )
     * )
     */
    public function destroy($postId, $commentId)
    {
        if (!$postId || !$commentId) {
            return response()->json(['message' => '잘못된 요청입니다.'], 400);
        }
        $comment = Comment::where('id', $commentId)
            ->where('post_id', $postId)
            ->whereNull('deleted_at')
            ->first();
        if (!$comment) {
            return response()->json(['message' => '존재하지 않는 댓글입니다.'], 404);
        }
        $currentUserId = auth()->id();
        if ($currentUserId !== $comment->user_id) {
            return response()->json(['message' => '권한이 없습니다.'], 403);
        }
        $comment->delete();
        return response()->json(['message' => '삭제되었습니다.']);
    }
}

