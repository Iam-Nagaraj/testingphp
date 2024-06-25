<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {
    $verticalMenuJson = file_get_contents(base_path('resources/menu/verticalMenu.json'));
    $merchantVerticalMenuJson = file_get_contents(base_path('resources/menu/merchantVerticalMenu.json'));
    $verticalMenuData = json_decode($verticalMenuJson);
    $merchantVerticalMenuData = json_decode($merchantVerticalMenuJson);

    // Share all menuData to all the views
    \View::share('menuData', [$verticalMenuData, $merchantVerticalMenuData]);
  }
}
