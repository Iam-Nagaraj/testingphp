<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConfigurationWalkthroughVideoResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		$detail = $this->resource;
		if ($detail) {
			return [
				'id' => $this->id,
				'video' => getS3Url($detail->data->walkthrough_video),
			];
		} else {
			return [];
		}
	}
}
