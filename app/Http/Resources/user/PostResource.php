<?php

namespace App\Http\Resources\user;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->user),
            'audience' => $this->audience,
            'content' => $this->content,
            'image' => $this->image,
            'reactions' => $this->reactedUsers,
            'comments' => CommentResource::collection($this->comments->sortBy('created_at')),
            'created_at' => Carbon::parse($this->created_at)->diffForHumans(Carbon::now(), true) . ' ago',
        ];
    }
}
