<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantDetailResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		$detail = $this->resource->first();
		if (!$detail) {
			return [];
		}
		return [
			'id' => $detail->id,
			'name' => $detail->name,
			'image' => isset($detail->media->first()->file) ? new UploadFileResource($detail->media->first()->file) : "",
			'address' => isset($detail->address) ? new UserAddressResource($detail->address) : (object)[],
			'addditional_cashback' => isset($detail->cashback) ? CashbackResource::collection($detail->cashback) : (object)[],
			'wallet_id' => isset($detail->WalletDetails) ?  $detail->WalletDetails->wallet_id : '',
			'cashback' =>  [
				"id" => 0,
				"cashback" => $detail->cashback_rule ? $detail->cashback_rule['standard_cashback_percentage'] : '0',
				"min_amount" => $detail->cashback_rule ? $detail->cashback_rule['ts_total_amount'] : '0',
				"off" => $detail->cashback_rule ? $detail->cashback_rule['ts_extra_percentage'] : '0',
				"standard_cashback_percentage" => $detail->cashback_rule ? $detail->cashback_rule['standard_cashback_percentage'] : '0',
				"extra_cashback_percentage" => $detail->cashback_rule ? $detail->cashback_rule['ts_extra_percentage'] : '0',
				"bonus_cashback_percentage" => $detail->cashback_rule ? $detail->cashback_rule['rp_extra_percentage'] : '0',
				"platform_fee" => $detail->cashback_rule ? $detail->cashback_rule['platform_fee'] : '0',
			],
		];
	}
}
