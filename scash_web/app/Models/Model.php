<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as MainModel;

class Model extends MainModel
{
	public function sendResponse($result, $message)
	{
		$response = [
			'success' => true,
			'data'    => $result,
			'message' => $message,
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
			'data'    => $result,
			'message' => $message,
		];


		/* if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }*/


		return response()->json($response, $code);
	}
}
