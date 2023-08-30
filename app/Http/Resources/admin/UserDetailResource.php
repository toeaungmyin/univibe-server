<?php

namespace App\Http\Resources\admin;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $followers_collection = collect($this->followers);

        $followers = $followers_collection->filter(function ($follower) {
            return !$this->followings->pluck('id')->contains($follower->id);
        });

        $followings_collection = collect($this->followings);

        $followings = $followings_collection->filter(function ($following) {
            return !$this->followers->pluck('id')->contains($following->id);
        });

        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'birthday' => Carbon::parse($this->birthday)->format('Y-m-d'),
            'profile_url' => Storage::disk('public')->url($this->profile_url),
            'online' => $this->online,
            'followers' => UserResource::collection($followers->all()),
            'followings' => UserResource::collection($followings->all()),
            'friends' => UserResource::collection($this->friends),
            'warnings' => $this->warnings,
            'ban' => $this->bannedUser,
            'roles' => $this->getRolenames(),
            'permissions' => $this->getPermissionNames(),
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d'),
        ];
    }
}
