<?php

namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConfigurationWalkthroughVideoRequest;
use App\Models\Configuration;
use App\Traits\DeleteFile;
use App\Traits\UploadFile;
use Exception;
use Illuminate\Http\Request;

class WalkthroughVideoController extends Controller
{
	use UploadFile, DeleteFile;

	protected Configuration $configurationService;

	public function __construct(Configuration $configurationService)
	{
		$this->configurationService = $configurationService;
	}

	/**
	 * Display a listing of the resource.
	 */

	public function index()
	{
		try {
			$detail = $this->configurationService->fetchOne('walkthrough_video');
			if ($detail) {
				$detail->url = getS3Url($detail->data->walkthrough_video);
			}
			
			return $this->sendResponse($detail, 'Walkthrough Video fetched successfully.');

		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function save(ConfigurationWalkthroughVideoRequest $request)
	{
		try {
			$video = $request->video;

			$detail = $this->configurationService->updateOrCreate(['config_key' => 'walkthrough_video'], ['config_key' => 'walkthrough_video', 'config_value' => $video]);
			return $this->sendResponse($detail, 'Walkthrough Video uploaded successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function delete(ConfigurationWalkthroughVideoRequest $request)
	{
		try {
			$deleteVideo = $this->fileDelete($request->video);
			$detail = $this->configurationService->remove($request->id);
			return $this->sendResponse($detail, 'Walkthrough Video delete successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}
}
