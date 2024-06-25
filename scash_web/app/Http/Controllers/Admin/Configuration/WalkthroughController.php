<?php

namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConfigurationWalkthroughVideoRequest;
use App\Models\Configuration;
use App\Traits\DeleteFile;
use App\Traits\UploadFile;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Configuration\WalkthroughScreenController;
use App\Http\Controllers\Admin\Configuration\WalkthroughVideoController;
use App\Http\Requests\ConfigurationWalkthroughScreenRequest;

class WalkthroughController extends Controller
{
	use UploadFile, DeleteFile;

	protected Configuration $configurationService;
	protected WalkthroughScreenController $walkthroughScreenController;
	protected WalkthroughVideoController $walkthroughVideoController;

	public function __construct(Configuration $configurationService, WalkthroughScreenController $walkthroughScreenController, WalkthroughVideoController $walkthroughVideoController)
	{
		$this->configurationService = $configurationService;
		$this->walkthroughScreenController = $walkthroughScreenController;
		$this->walkthroughVideoController = $walkthroughVideoController;
	}

	/**
	 * Display a listing of the resource.
	 */

	public function index()
	{
		try {
			$detail = $this->walkthroughVideoController->index()->getData()->data;
			return view('livewire.admin.configuration.walkthrough.index')->with(['render_html' => view('livewire.admin.configuration.walkthrough.video')->with(['detail' => $detail])]);
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}


	public function screen(Request $request)
	{
		try {
			$detail = $this->walkthroughScreenController->index()->getData()->data;

			if (!$request->ajax()) {
				return view('livewire.admin.configuration.walkthrough.index')->with(['render_html' => view('livewire.admin.configuration.walkthrough.screen')->with(['detail' => $detail])]);
			}

			$html = view('livewire.admin.configuration.walkthrough.screen')->with(['detail' => $detail])->render();

			return $this->sendResponse($html, 'Walkthrough Screen fetched successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}


	public function video(Request $request)
	{
		try {
			$detail = $this->walkthroughVideoController->index()->getData()->data;
			$videoDetail = $this->walkthroughScreenController->index()->getData()->data;

			if (!$request->ajax()) {
				return view('admin.configuration.walkthrough.index', compact('detail','videoDetail'));
			}

			$html = view('livewire.admin.configuration.walkthrough.video', compact('detail'))->with(['detail' => $detail])->render();

			return $this->sendResponse($html, 'Walkthrough Video fetched successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}




	/**
	 * Store a newly created resource in storage.
	 */
	public function saveVideo(ConfigurationWalkthroughVideoRequest $request)
	{
		try {
			return $this->walkthroughVideoController->save($request);
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function saveScreen(ConfigurationWalkthroughScreenRequest $request)
	{
		try {
			return $this->walkthroughScreenController->save($request);
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}
}
