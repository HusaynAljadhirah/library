<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BorrowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        if ($this->relationLoaded('user') && $this->user) {
            $data['user'] = new UserResource($this->user);
        }
        if ($this->relationLoaded('book') && $this->book) {
            $data['book'] = new BookResource($this->book);
        }
        return $data;
    }
}
