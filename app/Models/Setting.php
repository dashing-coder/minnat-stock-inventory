<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Throwable;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'type', 'is_public'];

    protected function casts(): array
    {
        return ['is_public' => 'boolean'];
    }

    public static function valueFor(string $key, mixed $default = null): mixed
    {
        try {
            $value = static::query()->where('key', $key)->value('value');
            return $value === null ? $default : $value;
        } catch (Throwable) {
            return $default;
        }
    }

    public static function boolean(string $key, bool $default = false): bool
    {
        $value = static::valueFor($key, $default ? '1' : '0');
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
    }
}
