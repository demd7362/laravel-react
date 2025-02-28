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

    public function index()
    {
        $posts = Post::whereNull('deleted_at')
            ->latest()
            ->paginate(self::PAGE_SIZE);
        return response()->json(compact('posts'));
    }

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
