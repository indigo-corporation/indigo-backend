<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\LikeRequest;
use App\Http\Requests\Api\UnlikeRequest;
use App\Http\Resources\Api\CommentAnswerResource;
use App\Http\Resources\Api\CommentEditResource;
use App\Http\Resources\Api\LikeResource;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            'filmId' => 'required|int|exists:films,id',
            'parentId' => 'nullable|exists:comments,id',
            'body' => 'required|string|max:500|min:1'
        ];

        $request->validate($rules);

        $comment = new Comment([
            'user_id' => Auth::user()->getAuthIdentifier(),
            'film_id' => $request->get('filmId'),
            'body' => $request->get('body'),
        ]);

        if ($parentId = $request->get('parentId', false)) {
            $comment->parent_comment_id = $parentId;
            $comment->type = Comment::COMMENT_TYPE_ANSWER;
        } else {
            $comment->type = Comment::COMMENT_TYPE_FILM;
        }

        $comment->save();

        return response()->success(new CommentAnswerResource($comment));
    }

    public function edit(Comment $comment)
    {
        if ($comment->user_id != Auth::user()->getAuthIdentifier()) {
            return response()->error([
                'code' => '403',
                'message' => 'Forbidden'
            ]);
        }

        return response()->success(new CommentEditResource($comment));
    }

    public function update(Request $request, Comment $comment)
    {
        $rules = [
            'body' => 'required|string|max:500|min:1'
        ];

        $request->validate($rules);

        $comment->body = json_encode($request->get('body'));
        $comment->save();

        return response()->success(new CommentEditResource($comment));
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return response()->success('Deleted');
    }

    public function like(LikeRequest $request)
    {
        $like = Auth::user()->like($request->comment_id, $request->is_like);
        $comment = Comment::find($request->comment_id);

        return response()->success([
            'like' => new LikeResource($like),
            'likes_count' => $comment->likes_count,
            'dislikes_count' => $comment->dislikes_count
        ]);
    }

    public function unlike(UnlikeRequest $request)
    {
        Auth::user()->unlike($request->comment_id);
        $comment = Comment::find($request->comment_id);

        return response()->success([
            'like' => null,
            'likes_count' => $comment->likes_count,
            'dislikes_count' => $comment->dislikes_count
        ]);
    }
}
