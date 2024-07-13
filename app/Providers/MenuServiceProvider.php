<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

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
    // $user = Auth::user();
    // if ($user->hasRole('SuperAdmin')) {
    //   $verticalMenuJson = file_get_contents(base_path('resources/menu/verticalMenu.json'));
    // } elseif ($user->hasRole('Hospital')) {
    //   $verticalMenuJson = file_get_contents(base_path('resources/menu/verticalMenuHospital.json'));
    // } elseif ($user->hasRole('department')) {
    //   $verticalMenuJson = file_get_contents(base_path('resources/menu/verticalMenuDepartment.json'));
    // } elseif ($user->hasRole('doctor')) {
    //   $verticalMenuJson = file_get_contents(base_path('resources/menu/verticalMenuDoctor.json'));
    // }
    $verticalMenuJson = file_get_contents(base_path('resources/menu/verticalMenu.json'));

    $verticalMenuData = json_decode($verticalMenuJson);
    $horizontalMenuJson = file_get_contents(base_path('resources/menu/horizontalMenu.json'));
    $horizontalMenuData = json_decode($horizontalMenuJson);

    // Share all menuData to all the views
    // \View::share('menuData', [$verticalMenuData, $horizontalMenuData]);
  }
}
