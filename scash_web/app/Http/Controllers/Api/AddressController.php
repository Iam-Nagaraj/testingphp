<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use App\Http\Requests\AddressRequest;
use App\Http\Resources\CityResource;
use App\Http\Resources\StateResource;
use App\Http\Resources\UserAddressResource;
use App\Models\City;
use App\Models\State;
use Exception;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{


	protected UserAddress $userAddressService;
	protected State $stateService;
	protected City $cityService;


	public function __construct(UserAddress $userAddressService, State $stateService, City $cityService)
	{
		$this->userAddressService = $userAddressService;
		$this->stateService = $stateService;
		$this->cityService = $cityService;
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function save(AddressRequest $request)
	{
		try {
			$user = Auth::user();
			$detail = $this->userAddressService->createUserAddress($user, $request);

			return $this->sendResponse(new UserAddressResource($detail), 'User Address created successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
     * @OA\Get(
     ** path="/api/v1/state",
     *   tags={"location"},
     *   summary="Only for test",
     *   operationId="state",
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
	public function state()
	{
		try {

			$detail = $this->stateService->fetch();

			return $this->sendResponse(StateResource::collection($detail), 'State fetched successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
     * @OA\Get(
     * path="/api/v1/city",
     *   tags={"location"},
     *   summary="state name list",
     *   operationId="city",
     *   @OA\Parameter(
     *      name="state_id",
     *      in="query",
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
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
     *   @OA\Response(
     *       response=403,
     *       description="Forbidden"
     *   )
     *)
    **/
	public function city(Request $request)
	{
		try {
			$state_id = $request->state_id??0;
			$detail = $this->cityService->fetchByState($state_id);

			return $this->sendResponse(CityResource::collection($detail), 'City fetched successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}
}
