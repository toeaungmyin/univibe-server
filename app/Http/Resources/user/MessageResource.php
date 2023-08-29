<?php

namespace App\Http\Resources\user;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'sender' => User::find($this->sender_id),
            'receiver_id' => $this->receiver_id,
            'content' => $this->content,
            'created_at' => Carbon::parse($this->created_at)->diffForHumans(Carbon::now(), true) . ' ago',
        ];
    }
}
