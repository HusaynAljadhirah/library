<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        
        $user = $this->resource['user'];
        $token = $this->resource['token'];
       return [
            'status'       => 'success',
            'message'      => 'Logged in successfully',
            'user'         => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ];
    }
}
