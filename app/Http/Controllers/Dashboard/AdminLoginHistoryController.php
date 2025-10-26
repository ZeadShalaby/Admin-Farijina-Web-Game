<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Models\AdminLoginHistory;
use App\Http\Controllers\Controller;
use Stevebauman\Location\Facades\Location;

class AdminLoginHistoryController extends Controller
{
    //

    public function index()
    {
        $loginHistory = AdminLoginHistory::all();
        // 1) عدد كل محاولات الدخول
        $totalLogins = AdminLoginHistory::count();

        // 2) آخر 10 محاولات دخول
        $recentLogins = AdminLoginHistory::latest('login_at')->first();

        // 3) كل اللوجينات كـ Collection (لاستخدام map/filter)
        $allLogs = AdminLoginHistory::get();

        // 4) الـ Top Locations (لو location متخزن JSON)
        $locations = $allLogs->pluck('location')->filter();

        $parsedLocations = $locations->map(function ($loc) {
            if (!is_array($loc)) {
                return 'Unknown';
            }
            $city = $loc['city'] ?? null;
            $country = $loc['country'] ?? null;
            $regon = $loc['region'] ?? null;
            return trim(($city ? $city . ', ' : '') . ($regon ? $regon . ', ' : '') . ($country ?? 'Unknown'));
        });
        $location = Location::get($recentLogins->ip_address);
        $mapsUrl = "https://www.google.com/maps?q={$location->latitude},{$location->longitude}";

        $topLocations = $parsedLocations
            ->countBy()
            ->sortDesc()
            ->take(5);

        // 5) عدد المستخدمين الأونلاين
        $onlineUsers = AdminLoginHistory::where('status', 'online')->count();


        return view('dashboard.loginHistory.index', compact(
            'loginHistory',
            'totalLogins',
            'recentLogins',
            'topLocations',
            'onlineUsers',
            'mapsUrl'
        ));
    }


}
