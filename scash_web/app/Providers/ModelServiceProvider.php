<?php

namespace App\Providers;

use App\Models\Cashback;
use App\Models\Cms;
use App\Models\Configuration;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserKyc;
use App\Models\UserMedia;
use App\Models\Verification;
use App\Models\DeviceToken;
use App\Models\Role;
use App\Models\UserReferalCode;
use App\Models\UserThroughReferalCode;
use Illuminate\Support\ServiceProvider;

class ModelServiceProvider extends ServiceProvider
{
	/**
	 * Register services.
	 */
	public function register(): void
	{
		$this->app->bind(User::class, fn () => new User());
		$this->app->bind(Role::class, fn () => new Role());
		$this->app->bind(UserMedia::class, fn () => new UserMedia());
		$this->app->bind(UserAddress::class, fn () => new UserAddress());
		$this->app->bind(UserKyc::class, fn () => new UserKyc());
		$this->app->bind(Configuration::class, fn () => new Configuration());
		$this->app->bind(Cms::class, fn () => new Cms());
		$this->app->bind(Verification::class, fn () => new Verification());
		$this->app->bind(DeviceToken::class, fn () => new DeviceToken());
		$this->app->bind(UserReferalCode::class, fn () => new UserReferalCode());
		$this->app->bind(UserThroughReferalCode::class, fn () => new UserThroughReferalCode());
		$this->app->bind(Cashback::class, fn () => new Cashback());
	}

	/**
	 * Bootstrap services.
	 */
	public function boot(): void
	{
		//
	}
}
