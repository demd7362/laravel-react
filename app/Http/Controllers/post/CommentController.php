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

    public function index($postId)
    {
        $comments = Comment::where('post_id', '=', $postId)
            ->whereNull('deleted_at')
            ->with('user')
            ->paginate(self::PAGE_SIZE);
        return response()->json(compact('comments'));
    }

    public function store(Request $request, $postId)
    {
        if (!$postId) {
            return response()->json(['message' => '잘못된 요청입니다.'], 400);
        }
        $post = Post::find($postId);
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

        return response()->json(['message' => '댓글이 작성되었습니다.', 'post_id' => $post->id, 'comment_id' => $comment->id], 201);
    }

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

