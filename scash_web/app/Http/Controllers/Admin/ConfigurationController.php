<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConfigurationWalkthroughVideoRequest;
use App\Models\Configuration;
use App\Traits\DeleteFile;
use Exception;
use Illuminate\Http\Request;
use App\Traits\UploadFile;


class ConfigurationController extends Controller
{

	use UploadFile, DeleteFile;

	protected $configurationService;

	public function __construct(Configuration $configurationService)
	{
		$this->configurationService = $configurationService;
	}

	/**
	 * Display a listing of the resource.
	 */

	public function walkthroughVideo()
	{
		try {
			$detail = $this->configurationService->fetch('walkthrough_video');
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
	public function walkthroughVideoSave(ConfigurationWalkthroughVideoRequest $request)
	{
		try {

			$video = $request->video;
			$detail = $this->configurationService->updateOrCreate(['id'=>$request->id],['config_key' => 'walkthrough_video', 'config_value' => $video]);
			return $this->sendResponse($detail, 'Walkthrough Video uploaded successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function walkthroughVideoDelete(ConfigurationWalkthroughVideoRequest $request)
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
