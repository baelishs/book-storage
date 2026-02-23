<?php

namespace App\Http\Resources\Auth;

use App\DTO\Auth\AuthDTO;
use App\DTO\Auth\AuthResultDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /** @var AuthResultDTO */
    public $resource;

    public function toArray(Request $request): array
    {
        return [
            'token' => $this->resource->token,
            'token_type' => $this->resource->token_type,
        ];
    }
}
