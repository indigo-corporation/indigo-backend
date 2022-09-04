<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'answers' => CommentAnswerResource::collection($this->parent_comments),
            'created_at' => $this->created_at
        ];
    }
}
