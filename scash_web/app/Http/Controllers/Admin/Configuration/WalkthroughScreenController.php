<?php

namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConfigurationWalkthroughScreenRequest;
use App\Http\Requests\ConfigurationWalkthroughVideoRequest;
use App\Models\Configuration;
use App\Traits\DeleteFile;
use App\Traits\UploadFile;
use Exception;
use Illuminate\Http\Request;

class WalkthroughScreenController extends Controller
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
			$detail = $this->configurationService->fetch([
				'walkthrough_screen_title',
				'walkthrough_screen_sub_title',
				'walkthrough_screen_image'
			]);

			$dataDetail = $detail->first()->data??"";

			$walkthrough_screen_title = $dataDetail->walkthrough_screen_title ?? '';
			$walkthrough_screen_sub_title = $dataDetail->walkthrough_screen_sub_title ?? '';
			$walkthrough_screen_image = $dataDetail->walkthrough_screen_image ?? '';

			$detailArray = (object)[
				'title' => $walkthrough_screen_title,
				'sub_title' => $walkthrough_screen_sub_title,
				'image' => $walkthrough_screen_image,
				'image_url' => getS3Url($walkthrough_screen_image) ?? '',
			];

			return $this->sendResponse($detailArray, 'Walkthrough Screen fetched successfully.');
			
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function save(ConfigurationWalkthroughScreenRequest $request)
	{
		try {
			$logo = $request->image;
			$uploadImage = $this->imageUpload($logo);
			$request->image = $uploadImage['url'];
			
			$requestArray = ['title', 'sub_title', 'image'];
			foreach ($requestArray as $key => $value) {
				$this->configurationService->updateOrCreate(['config_key' => 'walkthrough_screen_' . $value], ['config_key' => 'walkthrough_screen_' . $value, 'config_value' => $request->$value]);
			}
			$detail = $this->configurationService->fetch(['walkthrough_screen_title', 'walkthrough_screen_sub_title', 'walkthrough_screen_image']);

			return $this->sendResponse($detail, 'Walkthrough Screen uploaded successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function delete(ConfigurationWalkthroughScreenRequest $request)
	{
		try {
			$deleteVideo = $this->fileDelete($request->video);
			$detail = $this->configurationService->remove($request->id);
			return $this->sendResponse($detail, 'Walkthrough Screen delete successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}
}
