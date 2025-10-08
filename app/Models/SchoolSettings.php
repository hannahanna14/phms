<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSettings extends Model
{
    protected $fillable = [
        'school_name',
        'school_address',
        'school_phone',
        'school_email',
        'principal_name',
        'school_logo'
    ];

    /**
     * Get the singleton instance of school settings
     */
    public static function getInstance()
    {
        return static::first() ?? static::create([]);
    }
}
