<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model['code'] = uniqid();
        });
    }

    public function country() {
        return $this->belongsTo(Country::class,'country_id');
    }

    public function cities() {
        return $this->hasMany(City::class);
    }
}
