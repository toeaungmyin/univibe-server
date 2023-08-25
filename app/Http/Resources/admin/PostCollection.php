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
            $nextPage = $this->currentPage() + 1;
        } else {
            $nextPage = null;
        }

        if (1 < $this->currentPage()) {
            $prevPage = $this->currentPage() - 1;
        } else {
            $prevPage = null;
        }
        return [
            'data' => PostResource::collection($this->collection),
            'current_page' => $this->currentPage(),
            'first_page' => 1,
            'last_page' => $this->lastPage(),
            'next_page' => $nextPage,
            'prev_page' => $prevPage,
            'per_page' => $this->perPage(),
            'path' => $this->path(),
            'to' => $this->lastItem(),
            'from' => $this->firstItem(),
            'total' => $this->total(),
        ];
    }
}
