<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\PrivacyPolicyRequest;
use App\Models\Cms;
use Exception;
use Illuminate\Http\Request;

class PrivacyPolicyController extends Controller
{
	protected Cms $cmsService;
	public function __construct(Cms $cmsService)
	{
		$this->cmsService = $cmsService;
	}

	public function index()
	{
		try {
			$detail = $this->cmsService->fetchOne('privacy_policy_content');
			if($detail){
				$detail->content = $detail->data->privacy_policy_content;
			}
			return view('admin.cms.privacy-policy')->with(['detail'=>$detail]);
			
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function save(PrivacyPolicyRequest $request)
	{
		try {
			$content = $request->content;
			$detail = $this->cmsService->updateOrCreate(['cms_key' => 'privacy_policy_content'], ['cms_key' => 'privacy_policy_content', 'cms_value' => $content]);
			return $this->sendResponse($detail, 'Privacy Policy save successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function webView()
	{
		try {
			$detail = $this->cmsService->fetchOne('privacy_policy_content');
			if($detail){
				$detail->content = $detail->data->privacy_policy_content;
			}

			return view('admin.auth.privacyPolicy')->with(['detail'=>$detail]);
			
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}
}
