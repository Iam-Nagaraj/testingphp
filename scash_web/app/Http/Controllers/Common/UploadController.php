<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteFileRequest;
use App\Http\Requests\UploadRequest;
use App\Jobs\UploadDesignToDriveJob;
use Illuminate\Http\Request;
use App\Traits\UploadFile;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Traits\DeleteFile;
use Illuminate\Support\Facades\Redis;

class UploadController extends Controller
{
	use UploadFile, DeleteFile;

	public $folderName;
	public function __construct()
	{
		$this->folderName = "file";
	}

	public function uploadImage(UploadRequest $request)
	{
		try {
			if ($request->has('file')) {
				$file = $request->file;
				$uploadImage = $this->imageUpload($file);
				return $this->sendResponse($uploadImage, 'Image uploaded successfully.');
			}
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function uploadDoc(UploadRequest $request)
	{
		try {
			if ($request->has('file')) {
				$file = $request->file;
				$uploadImage = $this->documentUpload($file);
				return $this->sendResponse($uploadImage, 'Document uploaded successfully.');
			}
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	

	public function uploadVideo(UploadRequest $request)
	{
		try {
			if ($request->has('file')) {
				$file = $request->file;
				$uploadVideo = $this->videoUpload($file);
				return $this->sendResponse($uploadVideo, 'Video uploaded successfully.');
			}
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function deleteFile(DeleteFileRequest $request)
	{
		try {

			$file_path = $request->file_path;
			$deleteVideo = $this->fileDelete($file_path);
			return $this->sendResponse($deleteVideo, 'File deleted successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}
}
