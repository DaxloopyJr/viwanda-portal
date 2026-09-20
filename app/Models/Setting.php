<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, ?string $default = null): ?string
    {
        return Cache::rememberForever('setting.'.$key, function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default;
        });
    }

    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('setting.'.$key);
    }

    /** Portal display name (used in headers, landing page, titles). */
    public static function siteName(): string
    {
        return static::get('site_name', 'Viwanda na Biashara Portal');
    }

    public static function tagline(): string
    {
        return static::get('site_tagline', 'Ministry of Industry and Trade — Data Collection and Reporting');
    }

    /** Public URL of the uploaded logo, or null to use the default emblem. */
    public static function logoUrl(): ?string
    {
        $logo = static::get('site_logo');

        return $logo ? asset('uploads/'.$logo) : null;
    }
};
