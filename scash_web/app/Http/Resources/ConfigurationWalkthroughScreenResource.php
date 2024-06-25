<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConfigurationWalkthroughScreenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->resource) {
            $dataDetail = $this->resource->first()->data;

            $walkthrough_screen_title = $dataDetail->walkthrough_screen_title ?? '';
            $walkthrough_screen_sub_title = $dataDetail->walkthrough_screen_sub_title ?? '';
            $walkthrough_screen_image = $dataDetail->walkthrough_screen_image ?? '';

            return [
                'title' => $walkthrough_screen_title,
                'sub_title' => $walkthrough_screen_sub_title,
                'image' => getS3Url($walkthrough_screen_image) ?? '',
            ];
        }else{
            return [];
        }
    }
}
