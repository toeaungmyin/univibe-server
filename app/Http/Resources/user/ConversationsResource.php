<?php

namespace App\Http\Resources\user;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationsResource extends JsonResource
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
            'user1' => User::select('id', 'profile_url', 'username')->find($this->user1_id),
            'user2' => User::select('id', 'profile_url', 'username')->find($this->user2_id),
            'latest_message' => $this->messages->isEmpty() ? null : $this->messages->last()->content,
            'latest_message_at' => $this->messages->isEmpty() ? null : Carbon::parse($this->messages->last()->created_at)->diffForHumans(Carbon::now(), true) . ' ago',

        ];
    }
}
