<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CashbackResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		$detail = $this->resource;
		if (!$detail) {
			return [];
		}
		return [
			'id' => $detail->id,
			'cashback' => $detail->cashback,
			'min_amount' => $detail->min_amount,
			'off' => $detail->cashback ?? 0
		];
	}
}
