<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IgnoredNetwork extends Model {
    protected $primaryKey = 'ssid';
    protected $keyType    = 'string';
    protected $fillable   = ['ssid'];

    private static $cacheFull;
    private static $cacheContains;

    public static function isIgnored(string $ssid): bool {
        if(self::$cacheFull == null)
            self::$cacheFull = IgnoredNetwork::where('contains', 0)->get();
        if(self::$cacheContains == null)
            self::$cacheContains = IgnoredNetwork::where('contains', 1)->get();

        foreach(self::$cacheContains as $con)
            if(str_contains(strtolower($ssid), strtolower($con->ssid)))
                return true;

        return self::$cacheFull->contains($ssid);
    }
}
