<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UploadFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->resource) {
            return [
                'file' => $this->resource,
                'url' => getS3Url($this->resource)
            ];
        } else {
            return [];
        }
    }
}
