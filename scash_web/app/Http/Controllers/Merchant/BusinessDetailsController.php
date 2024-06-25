<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\BusinessCategory;
use App\Models\BusinessDetail;
use App\Models\BusinessSubCategory;
use App\Models\BusinessType;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class BusinessDetailsController extends Controller
{
	use UploadFile;

	protected User $businessDetailsService;

	public function __construct(
		User $businessDetailsService, 
		)
	{
		$this->businessDetailsService = $businessDetailsService;
	}


	public function businessDetails()
	{
		$BusinessCategory = BusinessCategory::select('*')->get();
		$BusinessType = BusinessType::select('*')->get();

		return view('livewire.merchant.businessdetails.form', compact('BusinessCategory','BusinessType'));
	}

	public function businessDetailsSuccess()
	{
		$BusinessDetail = BusinessDetail::where('user_id', Auth::user()->id)->first();
		if(!empty($BusinessDetail)){
			return view('livewire.merchant.businessdetails.success', compact('BusinessDetail'));
		}
	}

	public function businessDetailsSave(Request $request)
	{
		$user_id = Auth::user()->id;
		try {
			DB::beginTransaction();
			
			$businessDetailsData = [
				'user_id' => $user_id,
				'tax_type' => $request->tax_type,
				'registration_type' => $request->registration_type,
				'business_name' => $request->business_name,
				'about_business' => $request->about_business,
				'business_type' => $request->business_type,
				'business_category' => $request->business_category,
				'leagal_name' => $request->leagal_name,
				'business_street_address' => $request->business_street_address,
				'business_city' => $request->business_city,
				'business_state' => $request->business_state,
				'business_zip_code' => $request->business_zip_code,
				'business_ein' => $request->business_ein,
				'business_phone_number' => $request->business_phone_number,
				'business_contact_address' => $request->business_contact_address,
				'contact_city' => $request->contact_city,
				'contact_state' => $request->contact_state,
				'contact_zip_code' => $request->contact_zip_code,
				'dob' => $request->dob,
				'home_address' => $request->home_address,
				'home_city' => $request->home_city,
				'home_state' => $request->home_state,
				'home_zip_code' => $request->home_zip_code,
				'ssn_itin' => Crypt::encryptString($request->ssn_itin),
				'email' => $request->email,
				'Address_city_state' => $request->Address_city_state,
			];
			
			if($request->hasFile('logo')){
				$uploadImage = $this->imageUpload($request->logo);
				$businessDetailsData['logo'] = $uploadImage['url'];
			}
			
			$businessDetails = BusinessDetail::updateOrCreate(
				['user_id' => $user_id],
				$businessDetailsData
			);
			
			if ($businessDetails) {

				DB::commit();

				return redirect(route('merchant.auth.business-details-success'));
			} else {

				DB::rollBack();
				return redirect()->back();
			}
			
		} catch (Exception $ex) {
			DB::rollBack();
			return redirect()->back()->with('error', $ex->getMessage());
		}
	}

	public function getbusinessSubcategory(Request $request)
	{
		$BusinessSubCategory = BusinessSubCategory::where('parent_id', $request->business_category_id)->get();
		return view('merchant.auth.subCategory', compact('BusinessSubCategory'));

	}


}
