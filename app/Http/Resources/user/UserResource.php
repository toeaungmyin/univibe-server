<?php

namespace App\Http\user\Resources;

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
            'birthday' => $this->birthday,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'roles' => $this->getRolenames(),
        ];
    }
}
