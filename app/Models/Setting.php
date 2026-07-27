<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value],
        );
    }

    /**
     * Get the current tax rate (PPN percentage).
     * Returns a float, e.g. 11.0 for 11%.
     */
    public static function getTaxRate(): float
    {
        return (float) static::get('tax_rate', 0);
    }
}
