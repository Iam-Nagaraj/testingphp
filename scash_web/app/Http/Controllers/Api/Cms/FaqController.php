<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqResource;
use App\Http\Resources\TermConditionResource;
use App\Models\Configuration;
use App\Models\Faq;
use Exception;

class FaqController extends Controller
{

	/**
     * @OA\Get(
     ** path="/api/v1/faq",
     *   tags={"CMS"},
     *   summary="Only for test",
     *   operationId="faq",
     *   @OA\Response(
     *      response=201,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
    **/
	public function index()
	{
		try {
			$details['faq'] = Faq::latest()->get();
            $configModel = Configuration::where('config_key', 'support_email')->first();
			$details['support_email'] = $configModel->config_value??'';

			return $this->sendResponse($details, 'Faq fetched successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}
}
