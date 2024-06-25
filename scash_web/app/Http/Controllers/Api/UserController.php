<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected User $userService;

    public function __construct(User $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Get(
     ** path="/api/v1/auth/user",
     *   tags={"user"},
     *   summary="Only for test",
     *   operationId="user",
     *   @OA\Parameter(
     *      name="search",
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
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     * security={{"bearer":{}}}
     *)
    **/
    public function index(Request $request)
    {
        try {

            $list = $this->userService->where('role_id', 4);
            if(!empty($request->search)){
                $list->where('name', 'LIKE', '%'.$request->search.'%');
            }
            $list = $list->get();

            return $this->sendResponse($list, 'User Fetched successfully.');
        } catch (Exception $ex) {
            return $this->sendError([], $ex->getMessage());
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/auth/user/details",
     *   tags={"user"},
     *   summary="state name list",
     *   operationId="userDetails",
     *   @OA\Parameter(
     *      name="user_id",
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
    public function detail(Request $request)
    {
        try {
            $detail = $this->userService->where('id', $request->user_id)->first();
            return $this->sendResponse($detail, 'User detail Fetched successfully.');
        } catch (Exception $ex) {
            return $this->sendError([], $ex->getMessage());
        }
    }
}
