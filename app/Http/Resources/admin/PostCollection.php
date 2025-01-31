<?php

namespace App\Http\Resources\admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PostCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->lastPage() > $this->currentPage()) {
            $next_page = $this->currentPage() + 1;
        } else {
            $next_page = null;
        }
        if (1 < $this->currentPage()) {
            $perv_page = $this->currentPage() - 1;
        } else {
            $perv_page = null;
        }
        return [
            'data' => PostResource::collection($this->collection),
            'first_page' => 1,
            'last_page' => $this->lastPage(),
            'next_page' => $next_page,
            'current_page' => $this->currentPage(),
            'prev_page' => $perv_page,
            'per_page' => $this->perPage(),
            'from' => $this->firstItem(),
            'to' => $this->lastItem(),
            'total' => $this->total(),
        ];
    }
}
