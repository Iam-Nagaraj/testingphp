<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\PrivacyPolicyRequest;
use App\Http\Resources\PrivacyPolicyResource;
use App\Models\Cms;
use Exception;
use Illuminate\Http\Request;

class PrivacyPolicyController extends Controller
{
	protected Cms $cmsService;
	public function __construct(Cms $cmsService)
	{
		$this->cmsService = $cmsService;
	}

	/**
     * @OA\Get(
     ** path="/api/v1/privacy-policy",
     *   tags={"CMS"},
     *   summary="Only for test",
     *   operationId="privacy-policy",
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
			$detail = $this->cmsService->fetchOne('privacy_policy_content');

			return $this->sendResponse(new PrivacyPolicyResource($detail), 'Privacy Policy fetched successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}
}
