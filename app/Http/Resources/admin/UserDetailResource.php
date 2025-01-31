<?php

namespace App\Http\Resources\admin;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

use function PHPUnit\Framework\isEmpty;

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
        // Convert arrays to collections and filter by 'id'
        $followers_collection = collect($this->followers)->unique('id');
        $followings_collection = collect($this->followings)->unique('id');

        // Filter followers who are not in followings (not following you)
        $followers = $followers_collection->whereNotIn('id', $followings_collection->pluck('id'));

        // Filter followings who are not in followers (you are not following them)
        $followings = $followings_collection->whereNotIn('id', $followers_collection->pluck('id'));

        // Find friends (mutual followings)
        $friends = $followers_collection->whereIn('id', $followings_collection->pluck('id'));

        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'birthday' => Carbon::parse($this->birthday)->format('Y-m-d'),
            'profile_url' => $this->profile_url ? Storage::disk('public')->url($this->profile_url) : '',
            'online' => Cache::has('online-' . $this->id),
            'followers' => UserResource::collection($followers),
            'followings' => UserResource::collection($followings),
            'friends' => UserResource::collection($friends),
            'warnings' => $this->warning,
            'isBanned' => empty($this->bannedUser) ? false : true,
            'ban' => $this->bannedUser,
            'roles' => $this->getRolenames(),
            'permissions' => $this->getPermissionNames(),
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d'),
        ];
    }
}
