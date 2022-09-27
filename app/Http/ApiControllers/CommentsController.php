<?php

namespace App\Http\ApiControllers;

use App\Http\Requests\LikeRequest;
use App\Http\Requests\UnlikeRequest;
use App\Http\Resources\CommentAnswerResource;
use App\Http\Resources\CommentEditResource;
use App\Http\Resources\LikeResource;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param Comment $comment
     * @return Response
     */
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

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Comment $comment
     * @return Response
     */
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

    /**
     * Remove the specified resource from storage.
     *
     * @param Comment $comment
     * @return Response
     */
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
