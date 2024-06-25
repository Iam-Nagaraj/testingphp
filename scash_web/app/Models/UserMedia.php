<?php

namespace App\Models;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UserMedia extends Model
{

	protected $table = "user_medias";

	use HasFactory;

	const TYPE_IMAGE = 1;
	const TYPE_COVER = 2;
	const TYPE_DOCUMENT = 3;

	protected $fillable = ['user_id', 'file', 'type'];

	protected $appends = ['url'];

	public function scopeIsActive($query)
	{
		return $query->where('status', getConfigConstant('STATUS_ACTIVE'));
	}


	public function createUserMedia(User $user, Request $request)
	{
		$mediaArray = [1 => 'image'];

		foreach ($mediaArray as $type => $media) {
			$logo = $request->$media;
			$uploadImage = $this->imageUpload($logo);

			if ($request->$media) {
				self::updateOrCreate(
					['user_id' => $user->id],
					[
						'user_id' => $user->id,
						'file' => $uploadImage['url'],
						'type' => $type,
					]
				);
			}

		}
	}

	public function updateUserMedia(User $user, Request $request)
	{
		$mediaArray = [1 => 'image'];

		foreach ($mediaArray as $type => $media) {
			$existingMedia = self::where('user_id', $user->id)->where('type', $type)->first();

			if ($existingMedia) {
				$existingMedia->delete();
			} 

			$logo = $request->$media;
			$uploadImage = $this->imageUpload($logo);

			if ($request->$media) {
				self::updateOrCreate(
					['user_id' => $user->id],
					[
						'user_id' => $user->id,
						'file' => $uploadImage['url'],
						'type' => $type,
					]
				);
			}
		}
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

	public function remove($id){
		self::where('id',$id)->delete();
	}

	public function getUrlAttribute(){
		return $this->file?getS3Url($this->file):"";
	}
}
