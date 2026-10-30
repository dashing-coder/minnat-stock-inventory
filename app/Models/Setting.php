<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Setting extends Model
{
    protected $fillable=['key','value','group','type','is_public'];
    protected function casts(): array{return ['is_public'=>'boolean'];}
    public static function getValue(string $key, mixed $default=null): mixed
    {
        return static::query()->where('key',$key)->value('value') ?? $default;
    }
}
