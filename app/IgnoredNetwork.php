<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IgnoredNetwork extends Model {
    protected $primaryKey = 'ssid';
    protected $keyType    = 'string';
    protected $fillable   = ['ssid'];

    private static $cache;

    public static function isIgnored(string $ssid): bool {
        if(self::$cache == null)
            self::$cache = IgnoredNetwork::all();
        return self::$cache->contains($ssid);
    }
}
