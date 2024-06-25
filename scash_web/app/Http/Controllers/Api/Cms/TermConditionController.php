<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\TermConditionRequest;
use App\Http\Resources\TermConditionResource;
use App\Models\Cms;
use Exception;
use Illuminate\Http\Request;

class TermConditionController extends Controller
{
	protected Cms $cmsService;
	public function __construct(Cms $cmsService)
	{
		$this->cmsService = $cmsService;
	}

	/**
     * @OA\Get(
     ** path="/api/v1/term-condition",
     *   tags={"CMS"},
     *   summary="Only for test",
     *   operationId="term-condition",
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
			$detail = $this->cmsService->fetchOne('term_condition_content');

			return $this->sendResponse(new TermConditionResource($detail), 'Term Condition fetched successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}
}
