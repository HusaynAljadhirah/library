<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        if (!empty($this->cover_image)) {
            $data['cover_image_url'] = asset('storage/' . ltrim($this->cover_image, '/'));
        } else {
            $data['cover_image_url'] = null;
        }
        return $data;
    }
}
