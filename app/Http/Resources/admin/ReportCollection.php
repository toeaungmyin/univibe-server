<?php

namespace App\Http\Resources\admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ReportCollection extends ResourceCollection
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
            $prev_page = $this->currentPage() - 1;
        } else {
            $prev_page = null;
        }
        return [
            'data' => ReportResource::collection($this->collection),
            'first_page' => 1,
            'last_page' => $this->lastPage(),
            'next_page' => $next_page,
            'current_page' => $this->currentPage(),
            'prev_page' => $prev_page,
            'per_page' => $this->perPage(),
            'from' => $this->firstItem(),
            'to' => $this->lastItem(),
            'total' => $this->total(),
        ];
    }
}
