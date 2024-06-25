<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Http\Requests\MerchantStoreRequest;
use App\Models\BusinessDetail;
use App\Models\CashbackRule;
use App\Models\DwollaCustomer;
use App\Models\MerchantStore;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
	protected MerchantStore $merchantStoreService;
	public function __construct(MerchantStore $merchantStoreService)
	{
		$this->merchantStoreService = $merchantStoreService;
	}

	public function index(Request $request)
	{
		try {
			
			return $this->sendResponse([], 'Merchant Store fetched successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function list()
	{
		try {
			return view('merchant.store.index');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function view(Request $request)
	{
		$detail = $this->merchantStoreService->fetchByID($request->id);
		$userModel = User::where('id', $detail->user_id)->first();
		return view('merchant.store.view')->with(['detail' => $detail, 'userModel' => $userModel]);
	}

	public function transaction(Request $request)
	{
		$id = $request->id;
		$storeModel = MerchantStore::where('user_id', $id)->first();
		$userModel = User::where('id', $id)->first();
		return view('merchant.store.transaction', compact('id','storeModel','userModel'));
	}

	public function create()
	{
		try {
			return view('merchant.store.create');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function store(MerchantStoreRequest $request)
	{
		try {
			$current_id = Auth::user()->id;
			
			DB::beginTransaction();
			
			$userModel = $this->user($request);
			$this->userAddress($userModel, $request);
			$wallet = $this->wallet($userModel);
			$this->dwollaCustomer($userModel);
			$this->businessDetail($userModel, $request);
			$this->cashbackRule($userModel);

			$MerchantStore = MerchantStore::updateOrCreate(
				[
					'merchant_id' => $current_id,
					'name' => $request->name
				],
				[
					'merchant_id' => $current_id,
					'wallet_id' => $wallet->id,
					'user_id' => $userModel->id,
					'branch_id' => $request->branch_id,
					'name' => $request->name,
					'email' => $request->email,
					'city' => $request->city,
					'state' => $request->state,
					'phone' => $request->phone,
					'address' => $request->address,
					'latitude' => $request->latitude,
					'longitude' => $request->longitude,
				]
			);

			DB::commit();

			return $this->sendResponse([], 'Merchant store save successfully.');
		} catch (Exception $ex) {
			DB::rollBack();
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function update(MerchantStoreRequest $request)
	{
		try {
			$current_id = Auth::user()->id;
			
			DB::beginTransaction();
			
			$MerchantStore = MerchantStore::updateOrCreate(
				[
					'merchant_id' => $current_id,
					'name' => $request->name
				],
				[
					'merchant_id' => $current_id,
					'branch_id' => $request->branch_id,
					'name' => $request->name,
					'email' => $request->email,
					'city' => $request->city,
					'state' => $request->state,
					'phone' => $request->phone,
					'address' => $request->address,
					'latitude' => $request->latitude,
					'longitude' => $request->longitude,
				]
			);

			DB::commit();

			return $this->sendResponse([], 'Merchant store save successfully.');
		} catch (Exception $ex) {
			DB::rollBack();
			return $this->sendError([], $ex->getMessage());
		}
	}

	protected function cashbackRule($userModel)
	{
		$MerchantCashbackRule = CashbackRule::where('user_id', Auth::user()->id)->first();
		$CashbackRuleModel = new CashbackRule();
		$CashbackRuleModel->user_id = $userModel->id;
		$CashbackRuleModel->standard_cashback_percentage = $MerchantCashbackRule->standard_cashback_percentage;
		$CashbackRuleModel->ts_total_amount = $MerchantCashbackRule->ts_total_amount;
		$CashbackRuleModel->ts_extra_percentage = $MerchantCashbackRule->ts_extra_percentage;
		$CashbackRuleModel->ts_status = $MerchantCashbackRule->ts_status;
		$CashbackRuleModel->rp_total_amount = $MerchantCashbackRule->rp_total_amount;
		$CashbackRuleModel->rp_extra_percentage = $MerchantCashbackRule->rp_extra_percentage;
		$CashbackRuleModel->rp_status = $MerchantCashbackRule->rp_status;
		$CashbackRuleModel->save();

		return $CashbackRuleModel;
	}

	protected function businessDetail($userModel, $request)
	{
		$merchantBusinessDetail = BusinessDetail::where('user_id', Auth::user()->id)->first();
		$BusinessDetailModel = new BusinessDetail();
		$BusinessDetailModel->user_id = $userModel->id;
		$BusinessDetailModel->tax_type = $merchantBusinessDetail->tax_type;
		$BusinessDetailModel->registration_type = $merchantBusinessDetail->registration_type;
		$BusinessDetailModel->business_name = $request->name;
		$BusinessDetailModel->business_category = $merchantBusinessDetail->business_category;
		$BusinessDetailModel->business_street_address = $request->address;
		$BusinessDetailModel->business_city = $request->city;
		$BusinessDetailModel->business_state = $request->state;
		$BusinessDetailModel->business_phone_number = $request->phone;
		$BusinessDetailModel->email = $request->email;
		$BusinessDetailModel->save();

		return $BusinessDetailModel;
	}

	protected function dwollaCustomer($userModel)
	{
		$merchantCustomer = DwollaCustomer::where('user_id', Auth::user()->id)->first();
		$customerModel = new DwollaCustomer();
		$customerModel->user_id = $userModel->id;
		$customerModel->customer_id = $merchantCustomer->customer_id;
		$customerModel->save();

		return $customerModel;
	}

	protected function wallet($userModel)
	{
		$merchantWallet = Wallet::where('user_id', Auth::user()->id)->first();
		$walletModel = new Wallet();
		$walletModel->user_id = $userModel->id;
		$walletModel->wallet_id = $merchantWallet->wallet_id;
		$walletModel->save();

		return $walletModel;
	}

	protected function userAddress($userModel, $request)
	{
		$UserAddressModel = new UserAddress();
		$UserAddressModel->user_id = $userModel->id;
		$UserAddressModel->address = $request->address;
		$UserAddressModel->city = $request->city;
		$UserAddressModel->state = $request->state;
		$UserAddressModel->longitude = $request->longitude;
		$UserAddressModel->latitude = $request->latitude;
		$UserAddressModel->country = $request->country;
		$UserAddressModel->save();

		return $UserAddressModel;
	}

	protected function user($request)
	{
		$uuid = Str::uuid()->toString();
		$userModel = new User();
		$userModel->name = $request->name;
		$userModel->first_name = $request->name;
		$userModel->email = $request->email;
		$userModel->role_id = 5;
		$userModel->country_code = $request->country_code;
		$userModel->phone_number = $request->phone;
		$userModel->uuid = $uuid;
		$userModel->save();

		return $userModel;
	}

	public function table(Request $request)
	{

		$cashbacks = $this->merchantStoreService->where('merchant_id', Auth::user()->id);
		if ($request->has('search') && !empty($request->get('search'))) {
			$searchValue = '%' . $request->get('search')['value'] . '%';
		}
		$cashbacks = $cashbacks->latest('id');
		return DataTables::of($cashbacks)->addColumn('action', function ($row) {
			return view('merchant.store.table-action')->with(
				[
					'id' => $row->id, 
					'view_url' => route('merchant.store.view', ['id' => $row->id]),
					'transaction_url' => route('merchant.store.transaction', ['id' => $row->user_id]),
					'delete_url' => route('merchant.store.delete', ['id' => $row->id])
				]
			);
		})
			->rawColumns(['action'])->make(true);
	}

	public function transactionTable($id)
	{

		$transactions = Transaction::where('wallet_type', Transaction::TYPE_WALLET_TO_WALLET)
		->where('to_user_id', $id)->with(['receiver','sender'])->latest();

		return Datatables::of($transactions)->addColumn('action', function ($row) {
			return view('merchant.dashboard.table-action')->with(
				[
					'id' => $row->id, 
				]
			);
		})
			->rawColumns(['action'])->make(true);
	}

}
