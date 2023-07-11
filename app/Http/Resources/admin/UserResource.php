<?php

namespace App\Http\Resources\admin;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'username' => $this->username,
            'email' => $this->email,
            'birthday' => Carbon::parse($this->birthday)->format('F j,Y'),
            'online' => $this->online,
            'profile_url' => $this->profile_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'roles' => $this->getRolenames(),
        ];
    }
}
