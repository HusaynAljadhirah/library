<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        if (!empty($this->picture)) {
            $data['picture_url'] = asset('storage/' . ltrim($this->picture, '/'));
        } else {
            $data['picture_url'] = null;
        }
        if ($this->relationLoaded('role') && $this->role) {
            $data['role'] = new RoleResource($this->role);
        }
        return $data;
    }
}
