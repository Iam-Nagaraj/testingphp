<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Api\Cms\{FaqController, PrivacyPolicyController,TermConditionController};
use App\Http\Controllers\Api\{AddressController, BankController, DwollaController, MerchantController, NotificationController, PaymentController, PlaidController, ProfileController, TransactionController, UserController};
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\SecurityController;
use App\Http\Controllers\Common\UploadController;
use App\Http\Controllers\Api\Configuration\{WalkthroughScreenController,WalkthroughVideoController};
use App\Http\Controllers\Merchant\TestDwollaController;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
// 	return $request->user();
// });



Route::prefix('v1')->group(function () {
	Route::prefix('auth')->group(function () {
		Route::controller(LoginController::class)->group(function () {
			Route::post('send-otp', 'sendOtp')->name('api.auth.send-otp');
			Route::post('send-referal-code', 'sendReferalCode')->name('api.auth.send-referal-code');
			Route::post('login', 'login')->name('api.auth.login');
			Route::post('check-merchant-login', 'checkMerchantLogin')->name('api.auth.checkMerchantLogin');
			Route::post('verify-otp', 'verifyOtp')->name('api.auth.verify-otp');
			Route::post('sign-up', 'signUp')->name('api.auth.sign-up');
			Route::post('social-login', 'socialLogin')->name('api.auth.social-login');
		});
		Route::post('send-forgot-email-password', [SecurityController::class, 'sendForgotPassword']);

		Route::middleware('auth:api')->group(function () {
			Route::post('generate-pin', [SecurityController::class, 'generatePin']); 
			Route::post('verify-pin', [SecurityController::class, 'verifyPin']);

			Route::controller(LoginController::class)->group(function () {
				Route::get('logout', 'logout')->name('api.auth.logout');
				Route::prefix('user')->group(function () {
					Route::post('update/{id?}', 'updateProfile')->name('api.auth.user.update');
					Route::get('profile/{id?}', 'getProfile')->name('api.auth.user.profile');
				});
			});
			Route::prefix('address')->controller(AddressController::class)->group(function () {
				Route::post('create', 'save')->name('api.auth.address.create');
			});
			Route::prefix('merchant')->controller(MerchantController::class)->group(function () {
				Route::get('/', 'index')->name('api.auth.merchant');
				Route::get('/{id}', 'detail')->name('api.auth.merchant.detail');
			});

			Route::get('my-offer', [MerchantController::class, 'myOffer'])->name('api.myOffer');
			Route::get('my-referal-list', [TransactionController::class, 'myReferalList'])->name('api.myReferalList');

			Route::post('plaid-link-token', [PlaidController::class, 'createLinkToken'])->name('api.createLinkToken');
			Route::post('plaid-access-token', [PlaidController::class, 'createAccessToken'])->name('api.createAccessToken');
			Route::post('get-bank-account', [PlaidController::class, 'getBankAccount'])->name('api.getBankAccount');
			Route::post('get-plaid-processor-dwolla', [PlaidController::class, 'getPlaidProcessorDwolla'])->name('api.getPlaidProcessorDwolla');

			Route::post('dwolla-access-token', [DwollaController::class, 'createDwollaAccessToken'])->name('api.createDwollaAccessToken');
			Route::post('on-demand-authorizations', [DwollaController::class, 'demandAuthorizations'])->name('api.demandAuthorizations');
			Route::post('add-customer', [BankController::class, 'addCustomer'])->name('api.addCustomer');
			Route::post('plaid-dwolla-funding-create', [DwollaController::class, 'createPlaidDwollaFunding'])->name('api.createPlaidDwollaFunding');
			Route::post('bank-list', [BankController::class, 'getBankList'])->name('api.bankList');
			Route::post('remove-bank', [BankController::class, 'removeBank'])->name('api.removeBank');
			Route::post('verify-micro-deposits', [BankController::class, 'verifyMicroDeposits'])->name('api.verifyMicroDeposits');
			Route::post('fund-transfer', [BankController::class, 'makeFundTransfer'])->name('api.makeFundTransfer');
			Route::post('calculate-cashback', [BankController::class, 'calculateCashback'])->name('api.calculateCashback');
			Route::post('withdraw-fund-transfer', [BankController::class, 'withdrawFundTransfer'])->name('api.withdrawFundTransfer');
			Route::post('deposit-fund-transfer', [BankController::class, 'depositFundTransfer'])->name('api.depositFundTransfer');
			Route::post('user-fund-balance-data', [BankController::class, 'fundBalanceData'])->name('api.QRCodeData');
			Route::post('individual-list', [BankController::class, 'individualList'])->name('api.individualList');
			Route::post('transfer-list', [BankController::class, 'getTransferList'])->name('api.getTransferList');
			Route::post('all-transactions', [BankController::class, 'getTransactionList'])->name('api.getTransactionList');
			Route::post('wallet-transfer-list', [BankController::class, 'getWalletTransferList'])->name('api.getWalletTransferList');
			Route::post('get-latest-transaction', [BankController::class, 'getLatestTransaction'])->name('api.getLatestTransaction');
			Route::post('wallet-balance', [BankController::class, 'getWalletBalance'])->name('api.getBankBalance');
			Route::post('my-wallet-data', [BankController::class, 'MyWalletData'])->name('api.MyWalletData');

			Route::post('cashback-to-wallet', [BankController::class, 'cashbackToWallet'])->name('api.cashbackToWallet');

			Route::post('notification-list', [NotificationController::class, 'list'])->name('api.notificationList');			
			
		});
		Route::prefix('user')->controller(UserController::class)->group(function () {
			Route::get('/', 'index')->name('api.auth.user');
			Route::get('/details', 'detail')->name('api.auth.user.detail');
		});	

	});
	Route::controller(AddressController::class)->group(function () {
		Route::get('state', 'state')->name('api.state');
		Route::get('city', 'city')->name('api.city');

	});
	Route::middleware('auth:api')->group(function () {
		Route::post('payment',[PaymentController::class,'pay']);
		Route::get('transaction-list',[TransactionController::class,'list']);
		Route::get('users/search/{id?}',[ProfileController::class,'search']);
	});

	Route::prefix('file')->controller(UploadController::class)->group(function () {
		Route::post('image/upload', 'uploadImage')->name('api.file.image.upload');
		Route::post('video/upload', 'uploadVideo')->name('api.file.video.upload');
		Route::post('doc/upload', 'uploadDoc')->name('api.file.doc.upload');
		Route::post('delete', 'deleteFile')->name('api.file.delete');
	});

	Route::prefix('configuration')->group(function () {
		Route::prefix('walkthrough-video')->controller(WalkthroughVideoController::class)->group(function () {
			Route::get('/', 'index')->name('api.configuration.walkthrough-video');
		});

		Route::prefix('walkthrough-screen')->controller(WalkthroughScreenController::class)->group(function () {
			Route::get('/', 'index')->name('api.configuration.walkthrough-screen');
		});
	});

	Route::get('privacy-policy', [PrivacyPolicyController::class, 'index'])->name('api.cms.privacy-policy');
	Route::get('faq', [FaqController::class, 'index'])->name('api.cms.faq');
	Route::get('term-condition', [TermConditionController::class, 'index'])->name('api.cms.term-condition');
	Route::get('banner', [BannerController::class, 'banner'])->name('api.cms.banner');

	Route::post('webhooks', [DwollaController::class, 'webhooks'])->name('auth.webhooks');

	Route::post('upload-document', [TestDwollaController::class, 'uploadDocument']);


});
