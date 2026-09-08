<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'currency',
        'timezone',
        'tax_id',
        'status'
    ];
    
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'company_user'
        )->withPivot([
            'status',
            'joined_at',
        ]);
    }
}
