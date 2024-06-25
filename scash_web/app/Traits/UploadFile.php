<?php

namespace App\Traits;

use App\Http\Requests\UploadRequest;
use App\Http\Resources\UploadFileResource;
use Exception;
use Illuminate\Support\Facades\Storage;

trait UploadFile
{

	public $folderName;
	public function __construct()
	{
		$this->folderName = "file";
	}
	public function imageUpload($file)
	{
		try {
			$file = $file;
			if (isset($file) && $file) {
				$name = time() . '_' . $file->getClientOriginalName();
				$fileName = $this->folderName . '/image';
				$s3Path = Storage::disk('s3')->put($fileName, $file, 'public');
				$s3Url = getS3Url($s3Path);
				return ['file'=>$s3Path,'url'=>$s3Url];
			}
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function videoUpload($file)
	{
		try {
			$file = $file;
			if (isset($file) && $file) {
				$name = time() . '_' . $file->getClientOriginalName();
				$fileName = $this->folderName . '/video';
				$s3Path =  Storage::disk('s3')->put($fileName, $file, 'public');
				$s3Url = getS3Url($s3Path);
				return ['file'=>$s3Path,'url'=>$s3Url];
			}
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function documentUpload($file)
	{
		try {
			$file = $file;
			if (isset($file) && $file) {
				$name = time() . '_' . $file->getClientOriginalName();
				$fileName = $this->folderName . '/doc';
				$s3Path = Storage::disk('s3')->put($fileName, $file, 'public');
				$s3Url = getS3Url($s3Path);
				return ['file'=>$s3Path,'url'=>$s3Url];
			}
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	
}
