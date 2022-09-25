<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentAnswerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => new UserShortResource($this->user),
            'film_id' => $this->film_id,
            'body' => $this->body,
            'parent_id' => $this->parent_comment_id,
            'like' => $this->getMyLike(),
            'likes_count' => $this->likes_count,
            'dislikes_count' => $this->dislikes_count,
            'created_at' => $this->created_at
        ];
    }
}
