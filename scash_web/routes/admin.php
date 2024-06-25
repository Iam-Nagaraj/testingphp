<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Cms\{PrivacyPolicyController, TermConditionController};
use App\Http\Controllers\Admin\Configuration\WalkthroughController;
use App\Http\Controllers\Admin\ConfigurationController;
use App\Http\Controllers\Admin\{BankController, BannerController, MerchantController, UserController, StateController, CityController, CashbackController, DashboardController, BusinessCategoryController, BusinessSubCategoryController, BusinessTypeController, FaqController, PromotionalNotificationController};
use App\Http\Controllers\Admin\Auth\PasswordController;
use App\Http\Controllers\Admin\Configuration\ACHFeeController;
use App\Http\Controllers\Admin\Configuration\PlatformFeeController;
use App\Http\Controllers\Admin\Configuration\ReferalController;
use App\Http\Controllers\Admin\Configuration\SupportController;
use App\Http\Controllers\Admin\Configuration\TaxController;
use App\Http\Controllers\Admin\Configuration\TransactionLimitController;
use App\Http\Controllers\Common\UploadController;
use App\Http\Controllers\Merchant\Auth\LoginController as AuthLoginController;
use App\Jobs\SendEmailJob;
use App\Traits\TwilioTrait;

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

Route::group(['domain' => config('services.url.admin')], function () {

	Route::prefix('auth')->controller(LoginController::class)->group(function () {
		Route::get('login', 'login')->middleware('guest')->name('admin.auth.login');
		Route::post('login', 'loginSubmit')->middleware('guest')->name('admin.auth.login.store');

		Route::middleware('auth')->get('logout', 'logout')->name('admin.auth.logout');

		Route::get('test-mail/{email}', function ($email) {
			$responce = dispatch(new SendEmailJob(['details' => 'data'], 'testQueueJob', $email));
			echo 'done';
		});
		Route::get('test-sms/{phone}', [AuthLoginController::class, 'testTwillioSms']);
	});

	Route::middleware('auth')->group(function () {
		Route::get('generate-pin', [PasswordController::class, 'generatePin'])->name('admin.auth.generate-pin');
		Route::post('store-pin', [PasswordController::class, 'storePin'])->name('admin.auth.store-pin');

		Route::get('/profile', [DashboardController::class, 'profile'])->name('admin.profile');
		Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
		Route::get('/old-dashboard', [DashboardController::class, 'oldIndex'])->name('admin.old-dashboard');
		Route::get('/dashboard-transaction-table', [DashboardController::class, 'table'])->name('admin.transaction.table');
		Route::get('/dashboard-transaction-barChart', [DashboardController::class, 'barChart'])->name('admin.transaction.barChart');
		Route::get('/change-password', [PasswordController::class, 'changePassword'])->name('admin.auth.change-password');
		Route::post('/updated-password', [PasswordController::class, 'updatePassword'])->name('admin.auth.update-password');

		Route::get('/transactions', [BankController::class, 'transactions'])->name('admin.transactions');
		Route::get('/bank-transactions', [BankController::class, 'bankTransactions'])->name('admin.bank.transactions');
		Route::get('/wallet', [BankController::class, 'wallet'])->name('admin.wallet');
		Route::post('/get-banks', [BankController::class, 'getBankList'])->name('admin.bank.getBankList');
		Route::post('/withdraw-from-wallet', [BankController::class, 'withdrawFromWallet'])->name('admin.bank.withdrawFromWallet');
		Route::get('/wallet-transaction', [BankController::class, 'walletTransaction'])->name('admin.bank.walletTransaction');
		Route::post('/deposit-to-wallet', [BankController::class, 'depositToWallet'])->name('admin.bank.depositToWallet');
		Route::post('/pay-to-user', [BankController::class, 'payToUser'])->name('admin.bank.payToUser');


		Route::prefix('user')->group(function () {
			Route::get('/', [UserController::class, 'index'])->name('admin.user');
			Route::get('/static', [UserController::class, 'static'])->name('admin.user.static');
			Route::get('/table', [UserController::class, 'table'])->name('admin.user.table');
			Route::get('/view', [UserController::class, 'view'])->name('admin.user.view');
			Route::post('/status/change', [UserController::class, 'userStatusChange'])->name('admin.user.status.change');
			Route::post('/change-status', [UserController::class, 'changeStatus'])->name('admin.user.changeStatus');
		});

		Route::prefix('cashback')->group(function () {
			Route::get('/', [CashbackController::class, 'index']);
			Route::get('/list', [CashbackController::class, 'list'])->name('admin.cashback');
			Route::post('/save', [CashbackController::class, 'save'])->name('admin.cashback.save');
			Route::get('/table', [CashbackController::class, 'table'])->name('admin.cashback.table');
			Route::get('/view', [CashbackController::class, 'view'])->name('admin.cashback.view');
			Route::get('/add', [CashbackController::class, 'create'])->name('admin.cashback.create');
			Route::post('/store', [CashbackController::class, 'store'])->name('admin.cashback.store');
			Route::post('/status/change', [CashbackController::class, 'userStatusChange'])->name('admin.cashback.status.change');
			Route::get('delete/{id}', [CashbackController::class, 'delete'])->name('admin.cashback.delete');
		});


		Route::prefix('state')->group(function () {
			Route::get('/', [StateController::class, 'index'])->name('admin.state');
			Route::get('/table', [StateController::class, 'table'])->name('admin.state.table');
			Route::get('/view', [StateController::class, 'view'])->name('admin.state.view');
			Route::get('/add', [StateController::class, 'create'])->name('admin.state.create');
			Route::post('/store', [StateController::class, 'save'])->name('admin.state.store');
			Route::post('/status/change', [StateController::class, 'userStatusChange'])->name('admin.state.status.change');
			Route::get('/delete/{id}', [StateController::class, 'delete'])->name('admin.state.delete');
		});

		Route::prefix('business-category')->group(function () {
			Route::get('/', [BusinessCategoryController::class, 'index'])->name('admin.businessCategory');
			Route::get('/table', [BusinessCategoryController::class, 'table'])->name('admin.businessCategory.table');
			Route::get('/view', [BusinessCategoryController::class, 'view'])->name('admin.businessCategory.view');
			Route::get('/add', [BusinessCategoryController::class, 'create'])->name('admin.businessCategory.create');
			Route::post('/store', [BusinessCategoryController::class, 'save'])->name('admin.businessCategory.store');
			Route::post('/status/change', [BusinessCategoryController::class, 'userStatusChange'])->name('admin.businessCategory.status.change');
			Route::get('/delete/{id}', [BusinessCategoryController::class, 'delete'])->name('admin.businessCategory.delete');
		});

		Route::prefix('business-subcategory')->group(function () {
			Route::get('/', [BusinessSubCategoryController::class, 'index'])->name('admin.businessSubCategory');
			Route::get('/table', [BusinessSubCategoryController::class, 'table'])->name('admin.businessSubCategory.table');
			Route::get('/view', [BusinessSubCategoryController::class, 'view'])->name('admin.businessSubCategory.view');
			Route::get('/add', [BusinessSubCategoryController::class, 'create'])->name('admin.businessSubCategory.create');
			Route::post('/store', [BusinessSubCategoryController::class, 'save'])->name('admin.businessSubCategory.store');
			Route::post('/status/change', [BusinessSubCategoryController::class, 'userStatusChange'])->name('admin.businessSubCategory.status.change');
			Route::get('/delete/{id}', [BusinessSubCategoryController::class, 'delete'])->name('admin.businessSubCategory.delete');
		});

		Route::prefix('business-type')->controller(BusinessTypeController::class)->group(function () {
			Route::get('/', [BusinessTypeController::class, 'index'])->name('admin.businessType');
			Route::get('/table', [BusinessTypeController::class, 'table'])->name('admin.businessType.table');
			Route::get('/view', [BusinessTypeController::class, 'view'])->name('admin.businessType.view');
			Route::get('/add', [BusinessTypeController::class, 'create'])->name('admin.businessType.create');
			Route::post('/store', [BusinessTypeController::class, 'save'])->name('admin.businessType.store');
			Route::post('/status/change', [BusinessTypeController::class, 'userStatusChange'])->name('admin.businessType.status.change');
			Route::get('/delete/{id}', [BusinessTypeController::class, 'delete'])->name('admin.businessType.delete');
		});

		Route::prefix('city')->group(function () {
			Route::get('/', [CityController::class, 'index'])->name('admin.city');
			Route::get('/table', [CityController::class, 'table'])->name('admin.city.table');
			Route::get('/view', [CityController::class, 'view'])->name('admin.city.view');
			Route::get('/add', [CityController::class, 'create'])->name('admin.city.create');
			Route::post('/store', [CityController::class, 'save'])->name('admin.city.store');
			Route::post('/status/change', [CityController::class, 'userStatusChange'])->name('admin.city.status.change');
			Route::get('/delete/{id}', [CityController::class, 'delete'])->name('admin.city.delete');
		});

		Route::prefix('promotional-notification')->group(function () {
			Route::get('/', [PromotionalNotificationController::class, 'index'])->name('admin.promotionalNotification');
			Route::get('/table', [PromotionalNotificationController::class, 'table'])->name('admin.promotionalNotification.table');
			Route::get('/view', [PromotionalNotificationController::class, 'view'])->name('admin.promotionalNotification.view');
			Route::get('/add', [PromotionalNotificationController::class, 'create'])->name('admin.promotionalNotification.create');
			Route::post('/store', [PromotionalNotificationController::class, 'save'])->name('admin.promotionalNotification.store');
			Route::post('/status/change', [PromotionalNotificationController::class, 'userStatusChange'])->name('admin.promotionalNotification.status.change');
			Route::get('/delete/{id}', [PromotionalNotificationController::class, 'delete'])->name('admin.promotionalNotification.delete');
		});

		Route::prefix('banner-management')->group(function () {
			Route::get('/', [BannerController::class, 'index'])->name('admin.banner');
			Route::get('/table', [BannerController::class, 'table'])->name('admin.banner.table');
			Route::get('/view', [BannerController::class, 'view'])->name('admin.banner.view');
			Route::get('/add', [BannerController::class, 'create'])->name('admin.banner.create');
			Route::post('/store', [BannerController::class, 'save'])->name('admin.banner.store');
			Route::post('/update', [BannerController::class, 'update'])->name('admin.banner.update');
			Route::post('/status/change', [BannerController::class, 'userStatusChange'])->name('admin.banner.status.change');
			Route::get('/delete/{id}', [BannerController::class, 'delete'])->name('admin.banner.delete');
		});

		Route::prefix('merchant')->group(function () {
			Route::get('/', [MerchantController::class, 'index'])->name('admin.merchant');
			Route::get('/table', [MerchantController::class, 'table'])->name('admin.merchant.table');
			Route::get('/view', [MerchantController::class, 'view'])->name('admin.merchant.view');
			Route::get('/business-details/{id}', [MerchantController::class, 'businessDetails'])->name('admin.merchant.business-details');
			Route::get('/add', [MerchantController::class, 'create'])->name('admin.merchant.create');
			Route::post('/store', [MerchantController::class, 'save'])->name('admin.merchant.store');
			Route::post('/updateData', [MerchantController::class, 'updateData'])->name('admin.merchant.updateData');
			Route::post('/add', [MerchantController::class, 'store'])->name('admin.merchant.add');
			Route::post('/update', [MerchantController::class, 'update'])->name('admin.merchant.update');
			Route::get('/delete/{id}', [MerchantController::class, 'delete'])->name('admin.merchant.delete');
			Route::post('/detail', [MerchantController::class, 'detail'])->name('admin.merchant.detail');
			Route::post('/status/change', [MerchantController::class, 'changeStatus'])->name('admin.merchant.status.change');
			Route::post('/certify-merchant', [MerchantController::class, 'certifyMerchant'])->name('admin.merchant.certifyMerchant');
		});

		Route::prefix('configuration')->group(function () {
			Route::prefix('walkthrough')->group(function () {
				Route::get('/', [WalkthroughController::class, 'index'])->name('admin.configuration.walkthrough');


				Route::prefix('video')->group(function () {
					Route::get('/', [WalkthroughController::class, 'video'])->name('admin.configuration.walkthrough.video');
					Route::post('/save', [WalkthroughController::class, 'saveVideo'])->name('admin.configuration.walkthrough.video.save');
					Route::post('/delete', [WalkthroughController::class, 'deleteVideo'])->name('admin.configuration.walkthrough.video.delete');
				});

				Route::prefix('screen')->group(function () {
					Route::get('/', [WalkthroughController::class, 'screen'])->name('admin.configuration.walkthrough.screen');
					Route::post('/save', [WalkthroughController::class, 'saveScreen'])->name('admin.configuration.walkthrough.screen.save');
					Route::post('/delete', [WalkthroughController::class, 'deleteScreen'])->name('admin.configuration.walkthrough.screen.delete');
				});
			});
			Route::prefix('tax')->group(function () {
				Route::get('/', [TaxController::class, 'index'])->name('admin.configuration.tax');
				Route::post('/save', [TaxController::class, 'save'])->name('admin.configuration.tax.save');
			});
			Route::prefix('referral')->group(function () {
				Route::get('/', [ReferalController::class, 'index'])->name('admin.configuration.referral');
				Route::post('/save', [ReferalController::class, 'save'])->name('admin.configuration.referral.save');
			});
			Route::prefix('platform-fee')->group(function () {
				Route::get('/', [PlatformFeeController::class, 'index'])->name('admin.configuration.platformFee');
				Route::post('/save', [PlatformFeeController::class, 'save'])->name('admin.configuration.platformFee.save');
			});
			Route::prefix('ach-fee')->group(function () {
				Route::get('/', [ACHFeeController::class, 'index'])->name('admin.configuration.achFee');
				Route::post('/save', [ACHFeeController::class, 'save'])->name('admin.configuration.achFee.save');
			});
			Route::prefix('support-email')->group(function () {
				Route::get('/', [SupportController::class, 'index'])->name('admin.configuration.supportEmail');
				Route::post('/save', [SupportController::class, 'save'])->name('admin.configuration.supportEmail.save');
			});
			Route::prefix('transaction-limit')->group(function () {
				Route::get('/', [TransactionLimitController::class, 'index'])->name('admin.configuration.transactionLimit');
				Route::post('/save', [TransactionLimitController::class, 'save'])->name('admin.configuration.transactionLimit.save');
			});
		});

		Route::prefix('cms')->group(function () {
			Route::prefix('privacy-policy')->group(function () {
				Route::get('/', [PrivacyPolicyController::class, 'index'])->name('admin.cms.privacy-policy');
				Route::post('/save', [PrivacyPolicyController::class, 'save'])->name('admin.cms.privacy-policy.save');
			});

			Route::prefix('term-condition')->group(function () {
				Route::get('/', [TermConditionController::class, 'index'])->name('admin.cms.term-condition');
				Route::post('/save', [TermConditionController::class, 'save'])->name('admin.cms.term-condition.save');
			});
			Route::prefix('faq')->group(function () {
				Route::get('/', [FaqController::class, 'index'])->name('admin.faq');
				Route::get('/table', [FaqController::class, 'table'])->name('admin.faq.table');
				Route::get('/view', [FaqController::class, 'view'])->name('admin.faq.view');
				Route::get('/add', [FaqController::class, 'create'])->name('admin.faq.create');
				Route::post('/store', [FaqController::class, 'save'])->name('admin.faq.store');
				Route::post('/status/change', [FaqController::class, 'userStatusChange'])->name('admin.faq.status.change');
				Route::get('/delete/{id}', [FaqController::class, 'delete'])->name('admin.faq.delete');
			});
		});
		Route::get('profile/{id?}', [LoginController::class, 'profile'])->name('auth.profile');
	});
});

Route::prefix('file')->group(function () {
	Route::post('/image/upload', [UploadController::class, 'uploadImage'])->name('file.image.upload');
	Route::post('/video/upload', [UploadController::class, 'uploadVideo'])->name('file.video.upload');
	Route::post('/doc/upload', [UploadController::class, 'uploadDoc'])->name('file.doc.upload');
	Route::post('delete', [UploadController::class, 'deleteFile'])->name('file.delete');
});

Route::get('send-notification', [PromotionalNotificationController::class, 'send']);