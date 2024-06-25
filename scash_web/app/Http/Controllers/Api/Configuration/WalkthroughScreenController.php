<?php

namespace App\Http\Controllers\Api\Configuration;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConfigurationWalkthroughScreenRequest;
use App\Http\Requests\ConfigurationWalkthroughVideoRequest;
use App\Models\Configuration;
use App\Traits\DeleteFile;
use App\Traits\UploadFile;
use Exception;
use Illuminate\Http\Request;
use App\Http\Resources\ConfigurationWalkthroughScreenResource;

class WalkthroughScreenController extends Controller
{
	use UploadFile, DeleteFile;

	protected Configuration $configurationService;

	public function __construct(Configuration $configurationService)
	{
		$this->configurationService = $configurationService;
	}

	/**
     * @OA\Get(
     ** path="/api/v1/configuration/walkthrough-screen",
     *   tags={"configuration"},
     *   summary="Only for test",
     *   operationId="walkthrough-screen",
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
			$detail = $this->configurationService->fetch([
				'walkthrough_screen_title',
				'walkthrough_screen_sub_title',
				'walkthrough_screen_image'
			]);
			
			return $this->sendResponse(new ConfigurationWalkthroughScreenResource($detail), 'Walkthrough Screen fetched successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	
}
