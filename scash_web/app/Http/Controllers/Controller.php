<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Pagination\Paginator;

/**
 * @OA\Info(
 *    title="Your super  ApplicationAPI",
 *    version="1.0.0",
 * )
 */
class Controller extends BaseController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	public function sendResponse($result, $message, $url = '/')
	{
		$response = [
			'success' => true,
			'data'    => $result,
			'message' => $message,
			'url' => $url,
		];


		return response()->json($response, 200);
	}


	/**
	 * return error response.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function sendError($result, $message, $code = 404)
	{
		$response = [
			'success' => false,
			'errors' => true,
			'error' => true,
			'data'    => $result,
			'message' => $message,
		];


		/* if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }*/


		return response()->json($response, $code);
	}

	public function paginate($items)
	{
	    return new Paginator($items,9);
	}
}
