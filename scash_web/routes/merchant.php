<?php

use App\Http\Controllers\Admin\Cms\PrivacyPolicyController;
use App\Http\Controllers\Admin\Cms\TermConditionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Merchant\Auth\LoginController;
use App\Http\Controllers\Merchant\{BankController, BusinessDetailsController, CashbackController, CashbackRuleController, DashboardController, NotificationController, ProfileController, StoreController, TestDwollaController, TestPlaidController};
use App\Http\Controllers\Merchant\{PladeController,DwollaController};
use App\Http\Controllers\Merchant\Auth\PasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(['domain' => config('services.url.merchant')], function () {

	Route::get('/privacy-policy', [PrivacyPolicyController::class, 'webView'])->name('auth.privacyPolicy.webView');
    Route::get('/terms-and-condition', [TermConditionController::class, 'webView'])->name('auth.termsAndCondition.wevView');

	Route::group(['prefix' => 'auth', 'as' => 'merchant.'], function () {
		Route::get('login', [LoginController::class, 'merchantLogin'])->name('auth.login');
		Route::get('register', [LoginController::class, 'merchantRegister'])->name('auth.register');
		Route::post('business-subcategory', [BusinessDetailsController::class, 'getbusinessSubcategory'])->name('auth.business-subcategory');
		Route::post('login', [LoginController::class, 'login'])->name('auth.login.store');
		Route::post('logout', [LoginController::class, 'logout'])->name('auth.logout')->middleware('auth:api');
		Route::get('logout', [LoginController::class, 'webLogout'])->name('auth.logout');
		Route::post('register', [LoginController::class, 'register'])->name('auth.register');
		Route::post('web-register', [LoginController::class, 'webRegister'])->name('auth.web-register');
		Route::get('otp-verification', [LoginController::class, 'otpVerification'])->name('auth.otp-verification');
		Route::get('email-otp-verification', [LoginController::class, 'emailOtpVerification'])->name('auth.email-otp-verification');
		Route::post('verifyOtp', [LoginController::class, 'verifyOtp'])->name('auth.verifyOtp');
		Route::get('verify-email', [LoginController::class, 'verifyEmail'])->name('auth.verifyEmail');
		Route::post('checkMailExist', [LoginController::class, 'checkMailExist'])->name('auth.checkMailExist');
		Route::post('checkValidPhoneNumber', [LoginController::class, 'checkValidPhoneNumber'])->name('auth.checkValidPhoneNumber');
		Route::get('forgot-password', [PasswordController::class, 'forgotPassword'])->name('auth.forgotPassword');
		Route::post('send-forgot-email-password', [PasswordController::class, 'sendForgotPassword'])->name('auth.sendForgotPassword');
		Route::get('reset-password/{email}', [PasswordController::class, 'resetPassword'])->name('auth.resetPassword');
		Route::post('update-reset-password', [PasswordController::class, 'updateResetPassword'])->name('auth.updateResetPassword');
		Route::get('password-reset-confirmation', [LoginController::class, 'passwordResetConfirmation'])->name('auth.passwordResetConfirmation');
		Route::get('delete-old-users', [PasswordController::class, 'deleteOldUsers'])->name('auth.deleteOldUsers');
	});

	Route::group(['middleware' => 'auth', 'as' => 'merchant.'], function () {
		Route::get('generate-pin', [PasswordController::class, 'generatePin'])->name('auth.generate-pin');
		Route::post('store-pin', [PasswordController::class, 'storePin'])->name('auth.store-pin');

		Route::get('business-details', [BusinessDetailsController::class, 'businessDetails'])->name('auth.business-details');
		Route::post('business-details-save', [BusinessDetailsController::class, 'businessDetailsSave'])->name('auth.business-details-save');
		Route::get('business-details-success', [BusinessDetailsController::class, 'businessDetailsSuccess'])->name('auth.business-details-success');

		Route::get('/change-password', [PasswordController::class, 'changePassword'])->name('auth.change-password');
		Route::post('/updated-password', [PasswordController::class, 'updatePassword'])->name('auth.update-password');

		Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
		Route::get('/merchant-transaction-table', [DashboardController::class, 'table'])->name('transaction.table');
		Route::get('/dashboard-transaction-barChart', [DashboardController::class, 'barChart'])->name('transaction.barChart');

		Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
		Route::post('/profile-update', [ProfileController::class, 'profileUpdate'])->name('profile-update');
		Route::get('/update-email', [ProfileController::class, 'updateEmail'])->name('updateEmail');
		Route::post('/check-email', [ProfileController::class, 'checkEmail'])->name('checkEmail');
		Route::post('/verify-email', [ProfileController::class, 'verifyOTP'])->name('verifyEmail');
		Route::get('/update-phone', [ProfileController::class, 'updatePhone'])->name('updatePhone');
		Route::post('/check-phone', [ProfileController::class, 'checkPhone'])->name('checkPhone');
		Route::post('/verify-phone', [ProfileController::class, 'verifyOTP'])->name('verifyPhone');

		

		Route::group(['prefix' => 'cashback'], function () {
			Route::get('/', [CashbackController::class, 'index']);
			Route::post('/save', [CashbackController::class, 'save'])->name('cashback.save');
			Route::get('/list', [CashbackController::class, 'list'])->name('cashback');
			Route::get('/table', [CashbackController::class, 'table'])->name('cashback.table');
			Route::get('/view', [CashbackController::class, 'view'])->name('cashback.view');
			Route::get('/add', [CashbackController::class, 'create'])->name('cashback.create');
			Route::post('/store', [CashbackController::class, 'store'])->name('cashback.store');
			Route::post('/status/change', [CashbackController::class, 'userStatusChange'])->name('cashback.status.change');
			Route::delete('/delete/{id}', [CashbackController::class, 'delete'])->name('cashback.delete');
		});

		Route::group(['prefix' => 'store'], function () {
			Route::get('/', [StoreController::class, 'index']);
			Route::get('/list', [StoreController::class, 'list'])->name('store');
			Route::get('/table', [StoreController::class, 'table'])->name('store.table');
			Route::get('/transaction-table/{id}', [StoreController::class, 'transactionTable'])->name('store.transactionTable');
			Route::get('/view', [StoreController::class, 'view'])->name('store.view');
			Route::get('/transaction', [StoreController::class, 'transaction'])->name('store.transaction');
			Route::get('/add', [StoreController::class, 'create'])->name('store.create');
			Route::post('/store', [StoreController::class, 'store'])->name('store.store');
			Route::post('/update', [StoreController::class, 'update'])->name('store.update');
			Route::post('/status/change', [StoreController::class, 'userStatusChange'])->name('store.status.change');
			Route::delete('/delete/{id}', [StoreController::class, 'delete'])->name('store.delete');
		});

		Route::group(['prefix' => 'bank'], function () {
			Route::get('/', [BankController::class, 'index'])->name('bank');
			Route::post('/access-token', [BankController::class, 'getWebAccessToken'])->name('bank.accessToken');
			Route::post('/plaid-dwolla-token', [PladeController::class, 'plaidDwollaToken'])->name('bank.plaidDwollaToken');
			Route::post('/get-banks', [BankController::class, 'getBankList'])->name('bank.getBankList');
			Route::post('/deposit-to-wallet', [BankController::class, 'depositToWallet'])->name('bank.depositToWallet');
			Route::get('/wallet', [BankController::class, 'wallet'])->name('wallet');
			Route::get('/wallet-transaction', [BankController::class, 'walletTransaction'])->name('bank.walletTransaction');
			Route::post('/withdraw-from-wallet', [BankController::class, 'withdrawFromWallet'])->name('bank.withdrawFromWallet');
			Route::post('/pay-to-user', [BankController::class, 'payToUser'])->name('bank.payToUser');

		});

		Route::get('token-data', [PladeController::class, 'tokenData'])->name('auth.token-data');
		Route::get('cashback/rule', [CashbackRuleController::class, 'form'])->name('cashback.rule.form');
		Route::post('cashback/rule', [CashbackRuleController::class, 'save'])->name('cashback.rule.save');

		Route::get('notifications', [NotificationController::class, 'index'])->name('notifications');
		Route::get('notification-table', [NotificationController::class, 'table'])->name('notification.table');

	});

	Route::group(['prefix'=>'merchant','middleware' => 'auth:api', 'as' => 'merchant.'], function () {
			Route::prefix('cashback')->controller(CashbackController::class)->group(function () {
				Route::post('/', [CashbackController::class, 'index'])->name('cashback');
				Route::post('/save', [CashbackController::class, 'save'])->name('cashback.save');
				Route::post('/status/change', [CashbackController::class, 'statusChange'])->name('cashback.status.change');
			});
		Route::get('profile/{id?}', [LoginController::class, 'profile'])->name('auth.profile');
	});
	Route::get('plaid-access-token', [TestPlaidController::class, 'createToken'])->name('auth.create-token');
	Route::get('web-view', [TestPlaidController::class, 'webView'])->name('auth.webView');
	Route::get('public_token/exchange', [TestPlaidController::class, 'publicTokenExchange'])->name('auth.publicTokenExchange');
	Route::get('transactions-get', [TestPlaidController::class, 'transactionsGet'])->name('auth.transactionsGet');
	Route::get('auth-get', [TestPlaidController::class, 'authGet'])->name('auth.authGet');
	Route::get('recipient-create', [TestPlaidController::class, 'recipientCreate'])->name('auth.recipientCreate');
	Route::get('payment-create', [TestPlaidController::class, 'paymentCreate'])->name('auth.paymentCreate');
	Route::get('payments-list', [TestPlaidController::class, 'paymentsList'])->name('auth.paymentsList');
	Route::get('accounts-get', [TestPlaidController::class, 'accountsGet'])->name('auth.accountsGet');
	Route::get('balance-get', [TestPlaidController::class, 'balanceGet'])->name('auth.balanceGet');
	Route::get('item-get', [TestPlaidController::class, 'itemGet'])->name('auth.itemGet');
	Route::get('institutions-get', [TestPlaidController::class, 'institutionsGet'])->name('auth.institutionsGet');

	Route::get('plaid-processor-dwolla', [TestPlaidController::class, 'plaidProcessorDwolla'])->name('auth.plaidProcessorDwolla');
	Route::get('plaid-dwolla-funding-source', [TestDwollaController::class, 'plaidDwollaFundingSource'])->name('auth.plaidDwollaFundingSource');

	Route::get('dwolla-access-token', [TestDwollaController::class, 'createAccessToken'])->name('auth.dwolla-access-token');
	Route::get('on-demand-authorizations', [DwollaController::class, 'onDemandAuthorizations'])->name('auth.on-demand-authorizations');
	Route::get('create-customers', [TestDwollaController::class, 'createCustomers'])->name('auth.create-customers');
	Route::get('create-business-customers', [TestDwollaController::class, 'createBusinessCustomers']);
	Route::get('certify-beneficial-ownership', [TestDwollaController::class, 'certifyBeneficialOwnership']);
	Route::get('document-view', [TestDwollaController::class, 'testUploadDocument']);
	Route::post('upload-document', [TestDwollaController::class, 'uploadDocument'])->name('auth.upload-document');
	Route::get('get-customer', [TestDwollaController::class, 'getCustomersIdByEmail']);
	Route::get('create-bank-source', [DwollaController::class, 'createBankSource'])->name('auth.create-bank-source');
	Route::get('bank-list', [TestDwollaController::class, 'BankList'])->name('auth.bank-list');
	Route::get('business-classifications', [TestDwollaController::class, 'businessClassifications'])->name('auth.bank-list');
	Route::get('fund-transfer', [DwollaController::class, 'fundTransfer'])->name('auth.fund-transfer');
	Route::get('micro-deposits', [DwollaController::class, 'microDeposits'])->name('auth.micro-deposits');
	Route::get('create-virtual-account', [TestDwollaController::class, 'createVirtualAccount'])->name('auth.createVirtualAccount');
	Route::get('send-fcm', [TestDwollaController::class, 'sendFcm'])->name('auth.sendFcm');
	Route::get('transaction-list', [TestDwollaController::class, 'transactionList'])->name('auth.transactionList');
	Route::get('account-transaction-list', [TestDwollaController::class, 'accountTransactionList']);
	Route::get('transaction-detail', [TestDwollaController::class, 'transactionDetail'])->name('auth.transactionDetail');
	Route::get('event-list', [TestDwollaController::class, 'eventList'])->name('auth.eventList');
	Route::get('event-retrieve', [TestDwollaController::class, 'eventRetrieve'])->name('auth.eventRetrieve');
	Route::get('create-webhook-subscriptions', [TestDwollaController::class, 'createWebhookSubscriptions'])->name('auth.createWebhookSubscriptions');
	Route::get('delete-webhook-subscriptions', [TestDwollaController::class, 'deleteWebhookSubscriptions'])->name('auth.deleteWebhookSubscriptions');
	Route::get('webhook-subscriptions', [TestDwollaController::class, 'webhookSubscriptions'])->name('auth.webhookSubscriptions');
	Route::get('remove-bank', [TestDwollaController::class, 'removeBank'])->name('auth.removeBank');
	Route::get('master-account', [TestDwollaController::class, 'masterAccount']);
	Route::get('users-uuid', [DashboardController::class, 'usersUuid']);


});
