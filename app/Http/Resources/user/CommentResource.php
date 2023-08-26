<?php

namespace App\Http\Resources\user;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'description' => $this->comment,
            'user' => new UserResource($this->user),
            'post_id' => $this->post_id,
            'created_at' => Carbon::parse($this->created_at)->diffForHumans(Carbon::now(), true),
            'updated_at' => Carbon::parse($this->updated_at)->diffForHumans(Carbon::now(), true),
        ];
    }
}
