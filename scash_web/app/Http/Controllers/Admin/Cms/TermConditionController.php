<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\TermConditionRequest;
use App\Models\Cms;
use Exception;
use Illuminate\Http\Request;

class TermConditionController extends Controller
{
    protected Cms $cmsService;
    public function __construct(Cms $cmsService)
    {
        $this->cmsService = $cmsService;
    }
    
    public function index(){
        try {
			$detail = $this->cmsService->fetchOne('term_condition_content');
            if($detail){
                $detail->content = $detail->data->term_condition_content;
            }
			return view('admin.cms.term-condition')->with(['detail'=>$detail]);
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
    }

	public function save(TermConditionRequest $request)
	{
		try {
			$content = $request->content;
			$detail = $this->cmsService->updateOrCreate(['cms_key' => 'term_condition_content'], ['cms_key' => 'term_condition_content', 'cms_value' => $content]);
			return $this->sendResponse($detail, 'Term Condition save successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function webView()
	{
		try {
			$detail = $this->cmsService->fetchOne('term_condition_content');
			if($detail){
				$detail->content = $detail->data->term_condition_content;
			}

			return view('admin.auth.termsAndCondition')->with(['detail'=>$detail]);
			
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}
}
