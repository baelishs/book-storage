<?php

namespace App\Http\Resources\External;

use App\DTO\External\ExternalBookDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchExternalResource extends JsonResource
{
    /** @var ExternalBookDTO */
    public $resource;

    public function toArray(Request $request): array
    {
        return [
            'title' => $this->resource->title,
            'external_id' => $this->resource->externalId,
            'source' => $this->resource->source,
            'description' => $this->resource->description,
            'url' => $this->resource->url,
        ];
    }
}
