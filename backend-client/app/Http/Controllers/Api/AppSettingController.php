<?php
namespace App\Http\Controllers\Api;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class AppSettingController extends Controller
{
    // GET /api/app-settings
    public function show()
    {
        $settings = AppSetting::first();
        return response()->json($settings);
    }

    // PUT /api/app-settings
    public function update(Request $request)
    {
        $settings = AppSetting::first();
        $settings->app_name = $request->input('appName', $settings->app_name);
        $settings->logo_url = $request->input('logoUrl', $settings->logo_url);
        $settings->save();
        return response()->json($settings);
    }
}
