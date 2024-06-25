<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantListResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		if ($resourceData = $this->resource) {
			$merchants = [];
			foreach ($resourceData as $resource) {
				$merchants[] = [
					'id' => $resource->id,
					'name' => $resource->name,
					'image' => isset($resource->media->first()->file) ? new UploadFileResource($resource->media->first()->file) : "",
					'wallet_id' => isset($resource->wallet) ? $resource->wallet->wallet_id : null,
					'address' => isset($resource->address) ? new UserAddressResource($resource->address) : (object)[],
					'cashback' =>  [
						"id" => 0,
						"cashback" => $resource->cashback_rule ? $resource->cashback_rule['standard_cashback_percentage'] : '0',
						"min_amount" => $resource->cashback_rule ? $resource->cashback_rule['ts_total_amount'] : '0',
						"off" => $resource->cashback_rule ? $resource->cashback_rule['ts_extra_percentage'] : '0',
						"standard_cashback_percentage" => $resource->cashback_rule ? $resource->cashback_rule['standard_cashback_percentage'] : '0',
						"extra_cashback_percentage" => $resource->cashback_rule ? $resource->cashback_rule['ts_extra_percentage'] : '0',
						"bonus_cashback_percentage" => $resource->cashback_rule ? $resource->cashback_rule['rp_extra_percentage'] : '0',
						"platform_fee" => $resource->cashback_rule ? $resource->cashback_rule['platform_fee'] : '0',
					],
				];
			}

			return $merchants;
		} else {
			return [];
		}
	}
}
