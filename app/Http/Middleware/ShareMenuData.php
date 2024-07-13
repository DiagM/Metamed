<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class ShareMenuData
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    $user = Auth::user();

    $verticalMenuJson = '';
    if ($user) {
      if ($user->hasRole('SuperAdmin')) {
        $verticalMenuJson = file_get_contents(base_path('resources/menu/verticalMenu.json'));
      } elseif ($user->hasRole('hospital')) {
        $verticalMenuJson = file_get_contents(base_path('resources/menu/verticalMenuHospital.json'));
      } elseif ($user->hasRole('department')) {
        $verticalMenuJson = file_get_contents(base_path('resources/menu/verticalMenuDepartment.json'));
      } elseif ($user->hasRole('doctor')) {
        $verticalMenuJson = file_get_contents(base_path('resources/menu/verticalMenuDoctor.json'));
      }
    }

    $verticalMenuData = !empty($verticalMenuJson) ? json_decode($verticalMenuJson) : [];

    $horizontalMenuJson = file_get_contents(base_path('resources/menu/horizontalMenu.json'));
    $horizontalMenuData = json_decode($horizontalMenuJson);

    // Share all menuData to all the views
    View::share('menuData', [$verticalMenuData, $horizontalMenuData]);

    return $next($request);
  }
}
