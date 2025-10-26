<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stevebauman\Location\Facades\Location;

class AdminLoginHistory extends Model
{
    use HasFactory;

    protected $table = 'admin_login_history';
    protected $guarded = [];
    protected $casts = [
        'location' => 'array',
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
    ];
    protected $dates = [
        'login_at',
        'logout_at',
    ];

    /**
     * *
     * @param mixed $email
     * @param mixed $request
     * @return AdminLoginHistory|Model
     */
    public static function logLogin($email, $request)
    {
        $location = Location::get($request['ip_address']);
        //  $location = Location::get('8.8.8.8');
        self::create([
            'admin_email' => $email ?? null,
            'ip_address' => $request['ip_address'] ?? null,
            'user_agent' => $request['user_agent'] ?? null,
            'session_id' => session()->getId() ?? null,
            'location' => [
                'country' => $location->countryName,
                'region' => $location->regionName,
                'city' => $location->cityName,
                'state' => $location->regionName,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
            ],
            'login_at' => now(),
            'status' => 'online',
        ]);
    }

    /**
     * Log the logout time for the current session.
     */
    public static function logLogout($sessionId)
    {
        $record = self::where('session_id', $sessionId)
            ->whereNull('logout_at')
            ->latest()
            ->first();

        if ($record) {
            $record->update([
                'logout_at' => now(),
                'status' => 'offline',
            ]);
        }
    }
    public function getFormattedLocationAttribute()
    {
        if (!is_array($this->location)) {
            return 'غير معروف';
        }

        $city = $this->location['city'] ?? null;
        $country = $this->location['country'] ?? null;
        $regon = $this->location['region'] ?? null;
        return trim(($city ? $city . ', ' : ''). ($regon ? $regon . ', ' : '') . ($country ?? 'غير معروف'), ', ');
    }

    public function getMapsUrlAttribute()
    {
        if (!is_array($this->location)) {
            return null;
        }

        $lat = $this->location['latitude'] ?? null;
        $lng = $this->location['longitude'] ?? null;

        if (!$lat || !$lng) {
            return null;
        }

        return "https://www.google.com/maps?q={$lat},{$lng}";
    }


}
