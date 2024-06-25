<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Storage;

trait DeleteFile
{

	public $folderName;
	public function __construct()
	{
		$this->folderName = "file";
	}
	public function fileDelete($file_path)
	{
		try {
			$file_path = $file_path;
			if (isset($file_path) && $file_path) {
				return Storage::disk('s3')->delete($file_path);
				
			}
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}
}
