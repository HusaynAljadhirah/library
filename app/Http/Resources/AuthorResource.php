<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        if (!empty($this->photo)) {
            $path = ltrim($this->photo, '/');
            $data['photo_url'] = asset('storage/' . $path);
        } else {
            $data['photo_url'] = null;
        }
        return $data;
    }
}
